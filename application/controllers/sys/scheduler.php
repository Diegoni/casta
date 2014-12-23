<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

define('MY_SCHEDULER_REV', '$Rev: 607 $');

/**
 * Gestor de trabajos pendientes
 * @author alexl
 *
 */
class Scheduler extends MY_Controller
{

	/**
	 * Número máximo de tareas a ejecutar
	 * @var int
	 */
	private $max_tasks;
	private $wait;
	private $mode;
	private $mh;
	private $procs;
	private $tasks_id;
	private $runners;
	private $responses;
	private $username;
	private $password;
	private $debug;
	private $cron_running;
	private $cron_id;

	/**
	 * Constructor
	 *
	 * @return Scheduler
	 */
	function __construct()
	{
		parent::__construct(null, null, FALSE);

		$this->max_tasks 	= $this->config->item('bp.runner.max');
		$this->wait 		= $this->config->item('bp.runner.wait');
		$this->mode 		= $this->config->item('bp.runner.mode');
		$this->debug		= $this->config->item('bp.runner.echo');
		$this->username 	= $this->config->item('bp.runner.username');
		$this->password 	= $this->config->item('bp.runner.password');

	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#index()
	 */
	function index()
	{
		echo $this->config->item('bp.application.name') . "\n";
		echo "Scheduler : Gestión de trabajos\n";
	}

	/**
	 * Uso de la memoria
	 */
	private function _echo_memory_usage()
	{
		$mem_usage = memory_get_usage(true);

		return ($mem_usage < 1024)?$mem_usage." bytes":(($mem_usage < 1048576)?round($mem_usage/1024,2)." K":round($mem_usage/1048576,2)." M");
	}

	/**
	 * Envía un mensaje a la pantalla
	 * @param string $message Mensaje
	 */
	protected function _print($message)
	{
		$msg = format_datetime(time()) . ' - ' . '#'. $this->_echo_memory_usage() . "# - {$message}";
		if ($this->debug) print $msg . "\n";
		$this->logger->log($msg, 'scheduler');
	}

	/**
	 * Ejecuta la siguiente tarea
	 */
	protected function _run_next()
	{
		$exec = FALSE;
		//Ejecuta los trabajos de cron
		$jobs = $this->cron->get_to_run();
		#echo "Buscando JOBS\n";
		#var_dump($jobs);
		if (count($jobs) > 0)
		{
			foreach($jobs as $job)
			{
				// Comprueba si la tarea se está ejecutando
				if (!isset($this->cron_running[$job['job']]))
				{
					$this->_print(sprintf($this->lang->line('job-read'), $job['job'], $job['title']));
					#$username = $this->username;
					$data = array(
						'url' 		=> site_url($job['job']),
						'post'		=> array (
							'username'	=> $this->username,
							'password'	=> $this->password
						)
					);
					if ($this->cron->debug > 0) 
						var_dump($data);
					$id = $this->_add_proc($data);
					$this->tasks_id[$id] = $job['job'];
					$this->cron_running[$job['job']] = $id;
					$this->cron_id[$id] = $job['job'];
					$this->runners[$id] = $this->username;
					$this->cron->running($job['job']);
					$this->cron->set_result($this->cron_id[$id], '<pre>' . print_r($data, TRUE) . '</pre>');
					#echo "CRON añadido\n";
				}
				else
				{
					#echo "{$job['job']} - Esta en ejecución!!!\n";
				}
				#echo "CRON LIST\n";
				#print_r($this->cron_running);
			}
			#$this->tasks->running($tarea['nIdTarea']);
			$exec = TRUE;
		}
		if ($this->cron->debug > 0)
		{
			$text = $this->cron->getTextDebug();
			if ($text != '')  echo $text . "\n";
		};
		$tarea = $this->tasks->next();
		if ($tarea)
		{
			$this->_print(sprintf($this->lang->line('task-read'), $tarea['nIdTarea'], $tarea['cDescripcion']));
			$url = $tarea['cComando'];
			$username = $tarea['cCUser'];
			$data = array(
				'url' 	=> $url,
				'post'	=> array (
					'username'	=> $username,
					'password'	=> $this->userauth->get_password($username),
					'task'		=> $tarea['nIdTarea'])
			);
			$id = $this->_add_proc($data);
			$this->tasks_id[$id] = $tarea['nIdTarea'];
			$this->runners[$id] = $username;
			$this->tasks->running($tarea['nIdTarea']);
			$exec = TRUE;
		}
		return $exec;
	}

	/**
	 * Final de una tarea
	 * @param int $id Id de la tarea
	 * @param string $result Resultado de la tarea
	 */
	protected function _done($id, $result = null)
	{
		if (is_numeric($this->tasks_id[$id]))
		{
			$this->tasks->finish($this->tasks_id[$id], $result);
			$this->_print(sprintf($this->lang->line('task-runner-task-ended'), $this->tasks_id[$id]));
		}
		else
		{
			$this->_print(sprintf($this->lang->line('task-runner-job-ended'), $this->tasks_id[$id]));
		}

		log_message('debug', "Scheduler: Ending Task {$id}");

		if (isset($result))
		{
			$this->load->library('Comandos');
			$this->comandos->add($this->runners[$id], $result, $this->tasks_id[$id]);
		}

		if ($this->debug && isset($result) && ($result != ''))
		{
			print "-------------------------------------\n";
			print $result . "\n";
			print "-------------------------------------\n";
		}
		// Si era una tarea cron, la quita de la cola
		if (isset($this->cron_id[$id]))
		{
			#echo "{$this->cron_id[$id]} - CRON Finalizado\n";
			$this->cron->set_result($this->cron_id[$id], $result);
			unset($this->cron_running[$this->cron_id[$id]]);
			unset($this->cron_id[$id]);
			#echo "CRON LIST\n";
			#print_r($this->cron_running);				
		}
		// Quita la tarea de los procesos
		unset($this->procs[$id]);
		unset($this->tasks_id[$id]);
		unset($this->runners[$id]);
	}

	/**
	 * Añade un nuevo proceso a la cola
	 * @param array $data Datos del proceso
	 * @param array $options Opciones del proceso
	 * @return string Id del preceso
	 */
	protected function _add_proc($data, $options = array())
	{
		// loop through $data and create curl handles
		// then add them to the multi-handle
		$d = $data;
		$curly = curl_init();

		$url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
		curl_setopt($curly, CURLOPT_URL,            $url);
		curl_setopt($curly, CURLOPT_HEADER,         0);
		curl_setopt($curly, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curly, CURLOPT_TIMEOUT, 		60 * 60 * 24);


		// post?
		if (is_array($d)) {
			if (!empty($d['post'])) {
				#print_r($d['post']);
				$post = '';
				foreach($d['post'] as $k => $v)
				{
					$post .= $k . '=' . urlencode($v) . '&';
					$this->_print('[' . $k . '] = ' . $v);
				}
				curl_setopt($curly, CURLOPT_POST,       1);
				curl_setopt($curly, CURLOPT_POSTFIELDS, $post);
			}
		}

		// extra options?
		if (!empty($options)) {
			curl_setopt_array($curly, $options);
		}
		#print "Exec {$url}\n";
		#print_r($data);
		$this->_print($url);
		#curl_exec($curly);

		curl_multi_add_handle($this->mh, $curly);

		$key = (string) $curly;
		$this->procs[$key] = $curly;
		$this->_print(sprintf($this->lang->line('task-runner-task-started'), $key));
		log_message('debug', "Scheduler: Starting Task {$key}");

		return $key;
	}

	/**
	 * Daemon de tareas en paralelo
	 * http://www.ibm.com/developerworks/web/library/os-php-multitask/
	 * http://www.rustyrazorblade.com/2008/02/curl_multi_exec/
	 * http://www.phpied.com/simultaneuos-http-requests-in-php-with-curl/
	 * http://www.jaisenmathai.com/blog/2008/05/29/asynchronous-parallel-http-requests-using-php-multi_curl/
	 * http://kementeus.wordpress.com/2006/12/24/escribiendo-daemons-en-php-parte-i/
	 */
	function run()
	{
		if (isset($_SERVER['REMOTE_ADDR'])) {
			die($this->lang->line('task-runner-cmd-only'));
		}

		error_reporting(E_ALL);
		set_time_limit(0);

		// Primero creamos un proceso hijo
		if (function_exists('pcntl_fork'))
		{
			#$this->_print('Lanzado Deamon como hilo');
			$pid = pcntl_fork();
			if($pid == -1){
				#$this->logger->log("Algo pasó con el forking del proceso!", 'scheduler');
				die("Algo pasó con el forking del proceso!");
			}

			// Preguntamos si somos el proceso padre o el hijo recien construido
			if($pid) {
				// Ejecutamos otros hijos
				// Carga los procesos extra
				/*$process = $this->config->item('bp.runner.extraprocess');
				if (count($process)>0)
				{
					foreach ($process as $key => $value) 
					{
						$this->logger->log(sprintf($this->lang->line('task-runner-run-process'), $key));
						require_once($value);
					}
				}*/
				// Soy el padre por lo tanto necesito morir
				#$this->_print("Proceso padre terminado...");
				return;
			}

			// De aqui en adelante solo se ejecuta si soy el hijo y futuro daemon

			// Lo siguiente que hacemos es soltarnos de la terminal de control
			if (!posix_setsid()) {
				#$this->logger->log("No pude soltarme de la terminal", 'scheduler');
				die ("No pude soltarme de la terminal");
			}

			// De este punto en adelante debemos cambiarnos de directorio y
			// hacemos las recomendaciones de Wikipedia para un daemon
			chdir("/");
			umask(0);
			#$this->_print("Daemon en marcha...");
		}

		$this->load->library('Logger');
		$this->load->library('Userauth');

		$this->logger->log('Scheduler - ' . $this->config->item('bp.application.name'), 'scheduler');
		$this->logger->log(sprintf($this->lang->line('task-runner-started'), $this->mode, $this->max_tasks), 'scheduler');
		log_message('debug', "Scheduler: Starting mode {$this->mode}");

		#print "Funciona\n";
		//Carga librerías
		$this->load->library('Tasks');
		$this->load->library('Cron');
		$this->cron->debug = $this->config->item('bp.runner.cron.debug');

		// Login
		$this->load->library('Userauth');

		$this->logger->log(sprintf($this->lang->line('task-runner-login'), $this->username), 'scheduler');

		#print "Login... {$this->username} -> {$this->password}\n";
		#var_dump($this->db);
		if (!$this->userauth->login($this->username, $this->password))
		{
			#print "No se logea {$this->username} -> {$this->password}\n";
			#print $this->db->_error_message();
			$this->logger->log($this->lang->line('task-runner-login-error'), 'scheduler');
			return;
		}
		#print "Logeado\n";
		#$this->db->db_connect();
		// data to be returned
		$result = array();

		// multi handle
		$this->mh = curl_multi_init();

		// Ejecuta los procesos
		$running = null;
		do {
			while (count($this->procs) < $this->max_tasks)
			{
				if (!$this->_run_next()) break;
			}

			curl_multi_exec($this->mh, $running);
			$msgs_in_queue = 0;
			$data = curl_multi_info_read ($this->mh, $msgs_in_queue);

			// Tarea finalizada?
			if (isset($data['handle']))
			{
				$c = $data['handle'];
				$id = (string) $c;

				$result = curl_multi_getcontent($c);
				curl_multi_remove_handle($this->mh, $c);
				$this->_done($id, $result);
			}

			// Residente?
			if (count($this->procs) == 0)
			{
				if ($this->mode != 'resident')
				{
					break;
				}
			}
			else
			{
				//$this->_print(sprintf($this->lang->line('task-runner-waiting'), $this->wait, count($this->procs)));
			}
			sleep($this->wait);
		} while (TRUE);

		// all done
		curl_multi_close($this->mh);

		//print_r($result);

		$this->logger->log($this->lang->line('task-runner-ended'), 'scheduler');
		#print "CHAO\n";
		return;
	}

	/**
	 * Información de las tareas del scheduler
	 */
	function info()
	{
		$this->load->library('Cron');
		$info = $this->cron->get_info();
		$message = $this->load->view('sys/cron', array('tareas' => $info), TRUE);
		$this->out->html_file($message, $this->lang->line('Tareas Programadas'), 'iconoReportTab');
	}
}

/* End of file scheduler.php */
/* Location: ./system/application/controllers/sys/scheduler.php */