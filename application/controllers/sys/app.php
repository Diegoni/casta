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

/**
 * Controlador principal de la aplicación
 *
 */
class App extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		/*
		parent::__construct(null, null, FALSE);
		$this->load->library('userauth');
		*/
		parent::__construct();
		$this->load->library('userauth');
	}

	/**
	 * Ventana principal de la aplicación
	 *
	 */
	function index()
	{
		$this->userauth->check_login(null, null, null, 'sys/app/login');

		$this->load->helper('asset');
		$this->load->helper('extjs');
		$this->load->view(isset($_GET['beta'])?'sys/main2':'sys/main');
	}

	/**
	 * Rutas URL
	 * @return JS
	 */
	function routes()
	{
		$this->js_help('routes');
	}

	/**
	 * Constantes
	 * @return JS
	 */
	function constants()
	{
		$this->load->helper('asset');
		$this->load->library('Configurator');
		$js = $this->load->view('sys/constants.js', null, TRUE);
		$this->load->plugin('jsmin');
		echo $this->config->item('js.debug')?$js:JSMin::minify($js);
	}

	/**
	 * Librería de soporte
	 * @return JS
	 */
	function lib()
	{
		$this->js_help('ExtLib');
	}
	/**
	 * JS de soporte para la aplicación, pasado como vista
	 *
	 * @param string $file Fichero
	 * @param string $id Datos stamp adicionales
	 * @return JS
	 */
	function js_help($file, $id = null)
	{
		if ($this->config->item('js.debug'))
		{
			$this->load->helper('asset');
			$this->load->helper('extjs');
			$this->load->view('sys/' . $file . '.js');
		}
		else
		{
			$source 	= $this->load->_ci_view_path . 'sys/' . $file . '.js';
			$stamp 		= DIR_CACHE_PATH . 'js.' . $file . '.stamp';
			$min 		= DIR_CACHE_PATH . 'js.' . $file . '.min.js';

			$time = filemtime($source);
			$create = TRUE;
			if (file_exists($stamp))
			{
				$actual = file_get_contents($stamp);
				if ($actual == $id.$time) $create = FALSE;
			}

			if (!$create)
			{
				$create = !file_exists($min);
			}

			if ($create)
			{
				$this->load->helper('asset');
				$this->load->helper('extjs');
				$text = $this->load->view('sys/' . $file . '.js', null, TRUE);

				$this->load->plugin('jsmin');
				$textmin = JSMin::minify($text);

				file_put_contents($min, $textmin);
				file_put_contents($stamp, $id.$time);

				echo $textmin;
			}
			else
			{
				echo file_get_contents($min);
			}
		}
	}

	/**
	 * JS con los textos
	 *
	 * @param string $lang Idioma
	 * @return JS
	 */
	function lang($lang = null, $debug = null)
	{
		if (!isset($lang)) $lang = 'spanish';
		$source 	= BASEPATH . 'application' . DS . 'language' . DS . $lang . DS . 'bibliopola_lang.php';
		$stamp 		= BASEPATH . 'application' . DS . 'language' . DS . $lang . DS . 'bibliopola_lang.stamp';
		$destiny 	= BASEPATH . 'application' . DS . 'language' . DS . $lang . DS . 'bibliopola_lang.js';
		$destinymin = BASEPATH . 'application' . DS . 'language' . DS . $lang . DS . 'bibliopola_lang.min.js';

		$time = filemtime($source);
		$create = TRUE;
		if (file_exists($stamp))
		{
			$actual = file_get_contents($stamp);
			if ($actual == $time) $create = FALSE;
		}
		if (!$create)
		{
			$create = !file_exists(($this->config->item('js.debug')?$destiny:$destinymin));
		}

		if ($create)
		{
			$data['lang'] = $this->lang->get_texts();
			$text = $this->load->view('sys/lang.js', $data, TRUE);
			file_put_contents($destiny, $text);

			$this->load->plugin('jsmin');
			$textmin = JSMin::minify($text);
			file_put_contents($destinymin, $textmin);

			file_put_contents($stamp, $time);

			echo $this->config->item('js.debug')?$text:$textmin;
		}
		else
		{
			echo file_get_contents($this->config->item('js.debug')?$destiny:$destinymin);
		}
	}

	/**
	 * JS con los textos
	 *
	 * @param string $lang Idioma
	 * @return JS
	 */
	function js_search()
	{
		return $this->js_help('search');
	}

	/**
	 * Menú de la aplicación
	 * @return JSON
	 */
	function menu()
	{
		$this->userauth->check_login();
		//@todo uso de caché para no generar siempre el menú
		$this->load->helper('extjs');
		$filename = __DIR__ . '/../../config/' . $this->config->item('bp.application.menu');
		$menu = extjs_load_tree_xml($filename);
		echo $this->out->send($menu);
	}

	/**
	 * Rutinas JS para la creación del menú de la aplicación
	 */
	function js_menu()
	{
		if ($this->userauth->loggedin())
		{
			//@todo uso de caché para no generar siempre el menú
			$this->load->helper('extjs');
			$filename = __DIR__ . '/../../config/' . $this->config->item('bp.application.menu');
			$menu = extjs_load_tree_xml($filename);
			$datos['menu'] = $menu;
			$js = $this->load->view('main/menu', $datos);
			$this->load->plugin('jsmin');
			echo $this->config->item('js.debug')?$js:JSMin::minify($js);
		}
	}

	/**
	 * Página de bienvenida
	 * @return HTML
	 */
	function welcome()
	{
		$this->load->view('sys/welcome');
	}

	/**
	 * Página de ayuda
	 * @return HTML
	 */
	function help($topic = null)
	{
		$this->userauth->check_login();
		if ($topic)
		{
			$url = base_url() . sprintf($this->config->item('bp.application.help.topic'), $topic);
		}
		else
		{
			$url = base_url() . $this->config->item('bp.application.help');
		}
		$this->out->url($url, $this->lang->line('Ayuda'), 'help');
	}

	/**
	 * Version
	 * @return HTML
	 */
	function version()
	{
		$this->userauth->check_login();
		$data = array('versions' => $this->config->item('bp.versions'));
		$message = $this->load->view('sys/version', $data, TRUE);
		echo $this->out->html_file($message, $this->lang->line('Version'), 'iconoConfiguracionTab');
	}

	/**
	 * Version
	 * @return HTML
	 */
	function cfg_bp()
	{
		$this->userauth->roleCheck('app.configure');
		$this->load->helper('asset');
		$data['json'] = json_encode($this->config->config);
		$message = $this->load->view('sys/JSONEditor', $data, TRUE);
		echo $this->out->html_file($message, $this->lang->line('Configuración'), 'iconoConfiguracionTab', null, TRUE);
	}

	/**
	 * Devuelve el estado al cliente
	 * @return JSON
	 */
	function get_status($config = null)
	{
		//$this->userauth->roleCheck(('app.stayalive'));
		$config = isset($config)?$config:$this->input->get_post('config');
		if (isset($config))
		{
			$config = preg_split('/\;/', $config);
			$vars = array();
			if (count($config) > 0)
			{
				foreach($config as $c)
				{
					if (trim($c) != '')
					{
						$c = preg_split('/\=/', $c);
						$vars[$c[0]] = $c[1];
					}
				}
			}
		}

		$line = $this->config->item('bp.plugins.status');
		$plugins = preg_split('/;/', $line);
		foreach ($plugins as $p)
		{
			$this->load->plugin("status/{$p}");
			$i = new $p;
			$data = $i->run($vars);
			if (isset($data))
			{
				$res[$i->get_label()] = $data;
			}
		}
		// Respuesta
		echo $this->out->message(TRUE, $res);
	}

	/**
	 * Genera los procedimientos JS para intepretar el status
	 * @return JSON
	 */
	function js_status()
	{
		$line = $this->config->item('bp.plugins.status');
		$plugins = preg_split('/;/', $line);
		if ( count($plugins) > 0 )
		{
			$params = array();
			$js = array();
			foreach ($plugins as $p)
			{
				if ($p != '')
				{
					$this->load->plugin("status/{$p}");
					$i = new $p;
					$code = $i->js();
					if (isset($code)) $js[$i->get_label()] = $code;
					$code = $i->params();
					if (isset($code)) $params[$i->get_label()] = $code;
				}
			}
			// Respuesta
			$this->load->view('sys/status.js', array('js' => $js , 'params' => $params));
		}
	}

	/**
	 * Muestra un iframe los créditos de la aplicación
	 * @return HTML_FILE
	 */
	function credits()
	{
		$this->userauth->roleCheck('app.credits');

		$message = $this->load->view('sys/credits', null, TRUE);
		echo $this->out->html_file($message, $this->lang->line('Créditos'), 'iconoCreditosTab');
	}

	/**
	 * Muestra el iframe las cosas pendientes de la aplicación
	 * @return JSON
	 */
	function todo()
	{
		$this->userauth->roleCheck('app.todo');

		$message = $this->load->view('sys/todo', null, TRUE);
		echo $this->out->html_file($message, $this->lang->line('TODO'), 'iconoTODOTab');
	}

	/**
	 * Muestra el iframe las cosas pendientes de la aplicación
	 * @return JSON
	 */
	function cfg()
	{
		$this->userauth->roleCheck('app.configure');

		ob_start();
		phpinfo(-1);
		$message = ob_get_clean();
		echo $this->out->html_file($message, $this->lang->line('Configuración'), 'iconoConfiguracionTab', null, TRUE);
	}

	/**
	 * Muestra el iframe las cosas pendientes de la aplicación
	 * @return JSON
	 */
	function help_cmd()
	{
		$this->help('Comandos directos');
		$message = $this->load->view('sys/help_cmd', null, TRUE);
		echo $this->out->html_file($message, $this->lang->line('Comandos'), 'iconoHelpCmdTab');
	}

	/**
	 * Muestra la ventana de login
	 *
	 */
	function login()
	{
		$this->session->keep_flashdata('uri');
		$url = site_url($this->session->flashdata('uri'));
		
		// Formulario
		$this->load->helper('asset');
		$this->load->helper('extjs');

		$datos['title'] = $this->config->item('bp.application.name');
		$datos['url'] = $url . (isset($_GET['beta'])?'?beta':'');
		
		$this->load->view(isset($_GET['beta'])?'main/login3':'main/login', $datos);
		
	}

	/**
	 * Muestra la ventana de login
	 *
	 */
	function login2()
	{
		$this->session->keep_flashdata('uri');
		$url = site_url($this->session->flashdata('uri'));

		// Formulario
		$this->load->helper('asset');
		$this->load->helper('extjs');
		$datos['title'] = $this->config->item('bp.application.name');// . ' - ' .$this->lang->line('Login');

		$datos['url'] = $url;
		$datos['script'] = $this->load->view('sys/login.js', $datos, true);
		$this->load->view('main/main', $datos);
	}
	/**
	 * Muestra una página en blanco
	 * @return HTML
	 */
	function blank()
	{
		$this->load->helper('asset');
		$this->load->view('main/blank');
	}

	/**
	 * Realiza una limpieza del sistema
	 * @param string $type 'all': todo; 'temp': elimina los ficheros temporales
	 * @return JSON
	 */
	function clear($type = null)
	{
		$this->userauth->roleCheck(('app.clear'),'', TRUE);
		$type	= isset($type)?$type:$this->input->get_post('type');

		if(isset($type) || ($type == ''))
		{
			$type = 'all';
		}

		//@todo falta una rutina de limpieza de la caché
		$message = '';
		if ($type == 'all' || $type == 'temp')
		{
			//Limpia el temporal
			$dir= DIR_TEMP_PATH;
			$this->_destroy_dir(DIR_TEMP_PATH, false);
			$message .= $this->lang->line('clear_tempo_ok') . "\n";
		}
		$res = array(
			'success' 	=> true,
			'message'	=> $message
		);

		// Respuesta
		echo $this->out->send($res, $format);
	}

	/**
	 * Actualiza la aplicación a la última versión del repositorio.
	 * @return HTML
	 */
	function update()
	{
		$this->userauth->roleCheck('sys.update');

		$svn = $this->config->item('svn.cmd');
		$src = $this->config->item('svn.src');
		var_dump($svn, $src);

		chdir($src);
		$file = DIR_BIN_PATH . 'updatesrc.sh';
		$this->cmd_exec("/bin/bash {$file}", $out, $err);
		var_dump($out, $err);
		die();
		ob_start();
		set_time_limit(0);
		passthru($svn, $res);
		$var = ob_get_contents();
		ob_end_clean();
		var_dump($var);
		die();

		$this->load->view('sys/update', array('result' => $var));
	}

	/**
	 * Muestra el iframe las cosas pendientes de la aplicación
	 *
	 */
	function update2()
	{
		/*
		 $this->userauth->roleCheck(('app.update'),'', TRUE);
		 $this->load->helper('extjs');
		 $this->_iframe(site_url('sys/app/update2'), $this->lang->line('Actualizar aplicación'));
		 */
		// Encolar llamada y enviar mensaje de vuelta
		$this->_show_message('Actualizar', 'Se ha encolado la petición');
	}

	/**
	 * Muestra los skins de la aplicación
	 */
	function temas()
	{
		$message = $this->load->view('sys/temas', null, TRUE);
		echo $this->out->html_file($message, $this->lang->line('Temas'), 'iconoSelectTemasTab', null, TRUE);
	}

	/**
	 * Muestra el estado del servidor
	 * http://www.cyberciti.biz/tips/top-linux-monitoring-tools.html
	 * http://www.cyberciti.biz/tips/how-do-i-find-out-linux-cpu-utilization.html
	 * @return HTML_FILE
	 */
	function status()
	{
		$process[] = array('cat /etc/lsb-release', 
			$this->lang->line('Server software'));
		$process[] = array('uname -a', 
			$this->lang->line('Server Type'));
		$process[] = array('mpstat -P ALL', 
			$this->lang->line('Utilization of each CPU individually'));
		#$process[] = array('sar', 'Report CPU utilization using sar command');
		#$process[] = array('sar -u 2 5', 'Comparison of CPU utilization');

		$process[] = array('ps -eo pcpu,pid,user,args | sort -k 1 -r | head -10', 
			$this->lang->line('Task: Find out who is monopolizing or eating the CPUs'));
		$process[] = array('ps -eo pcpu,pid,user,args | sort -r -k1 | less', 
			$this->lang->line('Task: Find out who is monopolizing or eating the CPUs'));
		$process[] = array('iostat', 
			$this->lang->line('Average CPU Load, Disk Activity'));
		#$process[] = array('vmstat -m', $this->lang->line('Memory Utilization Slabinfo'));
		$process[] = array('ps aux | awk \'{print $4"\t"$11}\' | sort | uniq -c | awk \'{print $2" "$1" "$3}\' | sort -nr', 
			$this->lang->line('Consumo de memoria'));
		$process[] = array('vmstat -a', 
			$this->lang->line('Information About Active / Inactive Memory Pages'));
		$process[] = array('free', 
			$this->lang->line('Memory Usage'));
		$process[] = array('df -h', 
			$this->lang->line('Espacio de disco'));
		$process[] = array('mpstat -P ALL', 
			$this->lang->line('Multiprocessor Usage'));
		$process[] = array('/etc/init.d/bpsched status', 
			$this->lang->line('Bibliopola Scheduler'));
		$process[] = array('/etc/init.d/bpscan status', 
			$this->lang->line('Bibliopola Code Scan'));

		foreach($process as $k => $p)
		{
			$result = null;
			exec($p[0], $result);
			$process[$k][2] = $result;
		}
		$message = $this->load->view('sys/status', array('process' => $process), TRUE);
		$this->out->html_file($message, $this->lang->line('Estado servidor'), 'iconoStatusTab');
	}

	/**
	 * Estado de la caché
	 * @return HTML_FILE
	 */
	function apc()
	{
		//$message = $this->load->view('sys/apc', null, TRUE);
		$this->out->url(base_url() . '/tools/apc.php', $this->lang->line('APC'), 'iconoStatusTab');
	}
	/**
	 * Elimina el contenido de un directorio
	 * @param string $dir Path del directorio a borrar
	 * @param bool $recurse TRUE: elimina también los subdirectorios, FALSE: solo los archivos
	 * @return void
	 */
	private function _destroy_dir($dir, $recurse = true)
	{
		$mydir = opendir($dir);
		while(false !== ($file = readdir($mydir)))
		{
			if($file != "." && $file != "..")
			{
				chmod($dir.$file, 0777);
				if(is_dir($dir.$file) && ($recurse))
				{
					chdir('.');
					$this->_destroy_dir($dir.$file.'/', $recurse);
					//echo $dir.$file . '<BR/>';
					rmdir($dir.$file) or DIE("couldn't delete $dir$file<br />");
				}
				else if (!is_dir($dir.$file))
				{
					//echo $dir.$file . '<BR/>';
					unlink($dir.$file) or DIE("couldn't delete $dir$file<br />");
				}
			}
		}
		closedir($mydir);
	}

	/**
	 * Información de PHP
	 * @return HTML
	 */
	function phpinfo()
	{
		ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_end_clean();
		$this->out->html_file($phpinfo, $this->lang->line('PHP'), 'iconoPHPTab', null, TRUE);
	}

	/**
	 * Explorador de archivos
	 * @return HTML
	 */
	function explorer()
	{
		$this->out->url(base_url() . 'explorer', $this->lang->line('Explorador'), 'iconoExplorerTab');
	}
	/**
	 * Administrador de base de datos
	 * @return HTML
	 */
	function database()
	{
		$this->out->url(base_url() . 'system/contrib/phpMyAdmin', $this->lang->line('Base de datos'), 'iconoDatabaseTab');
	}

	/**
	 * Shell de sistema
	 * @return HTML_FILE
	 */
	function shell()
	{
		$this->userauth->roleCheck('app.shell');

		$message = $this->load->view('sys/shell', null, TRUE);
		$this->out->html_file($message, $this->lang->line('Shell'), 'iconoComandosTab', null, TRUE);
	}
	
	/**
	 * Limpia la memoria caché
	 * @return [type] [description]
	 */
	function clear_storage()
	{
		$this->_show_js(null, 'sys/clearstorage.js');	
	}

	/**
	 * Obtiene una vista JS para BP 6.0
	 * @param  [type] $tpl [description]
	 * @return [type]      [description]
	 */
	function tpl($tpl = null)
	{
		$tpl	= isset($tpl)?$tpl:$this->input->get_post('tpl');
		if (!empty($tpl))
			$this->load->view(str_replace('::', DS, $tpl));
	}

	/**
	 * Ejecuta un script SQL
	 * @param  string $name Nombre del script
	 * @return MSG
	 */
	function sql($name = null)
	{
		$name = isset($name)?$name:$this->input->get_post('name');

		if (!empty($name))
		{
			$filename = $name . '.' . $this->db->dbdriver . '.sql';
			$file = __DIR__ . '/sql/' . $filename;
			if (file_exists($file))
			{
				$sql = file_get_contents($file);
				if ($this->db->multi_query($sql))
					$this->out->success(sprintf($this->lang->line('sql-exec-ok'), $name));
				else
					$this->out->error($this->db->_error_message());
			}
			$this->out->error(sprintf($this->lang->line('sql-exec-no-file'), $filename));
		}
	}

	/**
	 * Ejecutar un comando y obtener el resultado
	 * @param  string $cmd   Comando a ejecutar
	 * @param  array $stdout Salida del programa
	 * @param  array $stderr Error del programa
	 * @return int
	 */
	private function cmd_exec($cmd, &$stdout, &$stderr)
	{
	    $outfile = tempnam(".", "cmd");
	    $errfile = tempnam(".", "cmd");
	    $descriptorspec = array(
	        0 => array("pipe", "r"),
	        1 => array("file", $outfile, "w"),
	        2 => array("file", $errfile, "w")
	    );
	    $proc = proc_open($cmd, $descriptorspec, $pipes);
	    
	    if (!is_resource($proc)) return 255;

	    fclose($pipes[0]);    //Don't really want to give any input

	    $exit = proc_close($proc);
	    $stdout = file($outfile);
	    $stderr = file($errfile);

	    unlink($outfile);
	    unlink($errfile);
	    return $exit;
	}

	/**
	 * Función interna de reinicio de servicios
	 * @param  string $service Nombre del servicio
	 * @return MSG
	 */
	private function _restart($service)
	{
		$err = '';
		$out = '';
		$file = DIR_BIN_PATH . 'restart' . $service . '.sh';
		$this->cmd_exec("/bin/bash {$file}", $out, $err);
		$res = implode('<br/>', array_merge($out, $err));
		$this->out->dialog($service, $res);
	}

	/**
	 * Reinicia el controlador de tareas
	 * @return MSG
	 */
	function restartsched()
	{
		$this->_restart('bpsched');
	}

	/**
	 * Reinicia el lector de precios
	 * @return MSG
	 */
	function restartscan()
	{
		$this->_restart('bpscan');
	}

	/**
	 * Configurar FAST TPV
	 * @return null
	 */
	function configure()
    {
		print "Importando base...\n";
		file_get_contents("http://localhost/app/sys/import/base?username=eoid&password=eoid&confirm=1");
		print "Importado\n";

		print "Importando EOIS...\n";
		file_get_contents("http://localhost/app/sys/import/eoi?username=eoid&password=eoid&confirm=1");
		print "Importado\n";

        $this->load->model('eoi/m_escuela');
        $eois = $this->m_escuela->get();
        $count = 1;
        foreach ($eois as $eoi)
        {
        	$message[] = "  {$count} - {$eoi['cDescripcion']}";
        	++$count;
        }
        $prompt = "Indica escuela:\n" . implode("\n", $message) . "\n>>";
        $res = $this->utils->prompt(($prompt));
        if ($res<1 || $res > count($eois))
        {
        	echo "Cancelado\n";
        	exit(1);
        }
        $eoi = $eois[$res-1];
        echo "Caja: {$eoi['nIdCaja']}\nSerie: {$eoi['nIdSerie']}\nSección: {$eoi['nIdSeccion']}\nDto: {$eoi['fDescuento']}\n";
        echo "Importando catálogo\n";
        if (is_numeric($eoi['nIdSeccion']))
        {
        	file_get_contents("http://localhost/app/sys/import/catalogo?username=eoid&password=eoid&seccion=".$eoi['nIdSeccion']);
        }
        echo "Importando verntas\n";
        if (is_numeric($eoi['nIdCaja']))
        {
        	file_get_contents("http://localhost/app/sys/ventas/catalogo?username=eoid&password=eoid&caja=".$eoi['nIdCaja']);
        }
        echo "Ejecute el programa y configure el terminal, recordando el ID de la sección {$eoi['nIdSeccion']} y el descuento {$eoi['fDescuento']}\n";
        /*$file = APPPATH . 'config/bibliopola.php';
        $data = file_get_contents($file);
        $this->replace($data, 'bp.tpv.caja', $eoi['nIdCaja']);
        $this->replace($data, 'bp.tpv.serie', $eoi['nIdSerie']);
        $this->replace($data, 'bp.factura.secciones.defecto', "'{$eoi['nIdSeccion']}'");*/

        //echo $data;
    }

    /*private function replace($data, $var, $value)
    {
    	$regex = "/\$config\[\'{$value}\'\](.*);/";
    	preg_match_all($regex, $data, $matches);
    	var_dump($matches);
    }*/
}

/* End of file app.php */
/* Location: ./system/application/controllers/sys/app/.php */