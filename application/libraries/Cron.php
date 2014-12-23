<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	libraries
 * @category	core
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

 /**
  * Índices de array de tiempo
  * @var int
  */ 
define("PC_MINUTE",		1);
define("PC_HOUR",		2);
define("PC_DOM",		3);
define("PC_MONTH",		4);
define("PC_DOW",		5);
define("PC_CMD",		7);
define("PC_COMMENT",	8);
define("PC_CRONLINE", 	20);

/**
 * Fichero de tareas
 * @var string
 */
define('CRON_JOB_FILE', 		'crontab.txt');
/**
 * Directorio de trabajo
 * @var string
 */
define('CRON_JOB_DIRECTORY', 	'cronjobs');

/**
 * pseudo-cron v1.3
 * (c) 2003,2004 Kai Blankenhorn
 * www.bitfolge.de/pseudocron
 * kaib@bitfolge.de
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details. You should have received a copy of the
 * GNU General Public License along with this program; if not, write to the Free Software Foundation,
 * Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * Modifica por alexl para adaptarlo al Sheduler de Bibliopola
 */
class Cron {
	/**
	 * The file that contains the job descriptions.
	 * For a description of the format, see http://www.unixgeeks.org/security/newbie/unix/cron-1.html
	 * and http://www.bitfolge.de/pseudocron
	 * @var unknown_type
	 */
	private $cronTab;

	/**
	 * The directory where the script can store information on completed jobs and its log file.
	 * @var unknown_type
	 */
	private $writeDir;

	// Control logging, true=use log file, false=don't use log file
	#var $useLog = true;

	// Where to send cron results.
	//$sendLogToEmail = "youraddess@mail.domain";
	#var $sendLogToEmail = "";

	// Maximum number of jobs run during one call of pseudocron.
	// Set to a low value if your jobs take longer than a few seconds and if you scheduled them
	// very close to each other. Set to 0 to run any number of jobs.
	#var $maxJobs = 1;

	/**
	 * DEBUG: 0, No, 1: trabajos, 2: procesos
	 * @var int
	 */
	var $debug = 0;

	#var $resultsSummary = "";

	/**
	 * Texto de DEBUG
	 * @var string
	 */
	private $_debug_text = null;

	/**
	 * Controlador CodeIgniter
	 * @var CI
	 */
	private $CI;

	/**
	 * Fecha del fichero crontab leído
	 * @var unknown_type
	 */
	private $timestamp = null;

	/**
	 * Trabajos leídos en el crontab
	 * @var unknown_type
	 */
	private $jobs = null;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->cronTab = APPPATH . 'config' . DIRECTORY_SEPARATOR . CRON_JOB_FILE;

		// The directory where the script can store information on completed jobs and its log file.
		// include trailing slash
		$this->writeDir = DIR_CACHE_PATH . DIRECTORY_SEPARATOR . CRON_JOB_DIRECTORY . DIRECTORY_SEPARATOR;
		$this->CI = get_instance();
		$this->CI->load->library('Logger');
		if (!is_dir($this->writeDir))
		{
			mkdir($this->writeDir);
			$this->CI->logger->Log("Directorio de trabajo {$this->writeDir} cerado", 'cron');
		}
	}

	/**
	 * Devuelve el texto de debug
	 * @return string
	 */
	function getTextDebug()
	{
		return $this->_debug_text;
	}

	/**
	 * Mensaje de log
	 * @param string $msg Mensaje
	 */
	private function logMessage($msg)
	{
		if ($this->debug > 0) $this->_debug_text .= $msg . "\n";
		echo $msg . "\n";
		$this->CI->logger->Log($msg, 'cron');
	}

	/**
	 * Quita los ceros a la izquierda
	 * @param string $number
	 * @return string
	 */
	private function lTrimZeros($number)
	{
		while ($number[0] == '0')
		{
			$number = substr($number,1);
		}
		return $number;
	}

	/**
	 * Ordena un array
	 * @param array $array Array a ordenar
	 * @param string $sortby Campo de orden
	 * @param string $order Dirección
	 * @return array
	 */
	private function multisort(&$array, $sortby, $order='asc')
	{
		foreach($array as $val)
		{
			$sortarray[] = $val[$sortby];
		}
		$c = $array;
		$const = $order == 'asc' ? SORT_ASC : SORT_DESC;
		$s = array_multisort($sortarray, $const, $c, $const);
		$array = $c;
		return $s;
	}

	/**
	 * Obtiene los campos de tiempo de un trabajo
	 * @param string $element Línea de texto a parsear
	 * @param array $targetArray Array donde almacenar el resultado
	 * @param int $numberOfElements Número de elementos a parsear
	 */
	private function parseElement($element, &$targetArray, $numberOfElements)
	{
		$subelements = explode(",",$element);
		for ($i=0;$i<$numberOfElements;$i++) {
			$targetArray[$i] = $subelements[0]=="*";
		}

		for ($i=0;$i<count($subelements);$i++)
		{
			if (preg_match("~^(\\*|([0-9]{1,2})(-([0-9]{1,2}))?)(/([0-9]{1,2}))?$~", $subelements[$i], $matches))
			{
				if ($matches[1] == '*')
				{
					$matches[2] = 0;		// from
					$matches[4] = $numberOfElements;		//to
				}
				elseif (!isset($matches[4]))
				{
					$matches[4] = $matches[2];
				}
				if (isset($matches[5][0]) && $matches[5][0]!="/")
				{
					$matches[6] = 1;		// step
				}
				#print_r($matches);
				if (!isset($matches[6])) $matches[6] = 1;
				for ($j=$this->lTrimZeros($matches[2]);$j<=$this->lTrimZeros($matches[4]);$j+=$this->lTrimZeros($matches[6]))
				{
					$targetArray[$j] = TRUE;
				}
			}
		}
	}

	/**
	 * Incrementa una unidad de tiempo
	 * @param array $dateArr Array con la fecha
	 * @param int $amount Unidades a incrementar
	 * @param string $unit Parte de la fecha
	 */
	private function incDate(&$dateArr, $amount, $unit)
	{
		if ($unit=="mday")
		{
			$dateArr["hours"] 	= 0;
			$dateArr["minutes"] = 0;
			$dateArr["seconds"] = 0;
			$dateArr["mday"] 	+= $amount;
			$dateArr["wday"] 	+= $amount % 7;
			if ($dateArr["wday"] > 6)
			{
				$dateArr["wday"] -= 7;
			}

			$months28 = Array(2);
			$months30 = Array(4,6,9,11);
			$months31 = Array(1,3,5,7,8,10,12);

			$bisiesto = checkdate(02,29,$dateArr["year"]);
			if ((in_array($dateArr["mon"], $months28) && $dateArr["mday"]==29 && !$bisiesto) ||
			(in_array($dateArr["mon"], $months28) && $dateArr["mday"]==30 && $bisiesto) ||
			(in_array($dateArr["mon"], $months30) && $dateArr["mday"]==31) ||
			(in_array($dateArr["mon"], $months31) && $dateArr["mday"]==32)
			)
			{
				$dateArr["mon"]++;
				if ($dateArr["mon"]==13)
				{
					$dateArr["mon"] = 1;
					++$dateArr['year'];
				}
				$dateArr["mday"] = 1;
			}

		}
		elseif ($unit == "hour")
		{
			if ($dateArr["hours"]==23)
			{
				$this->incDate($dateArr, 1, "mday");
			}
			else
			{
				$dateArr["minutes"] = 0;
				$dateArr["seconds"] = 0;
				$dateArr["hours"]++;
			}
		}
		elseif ($unit == "minute")
		{
			if ($dateArr["minutes"]==59)
			{
				$this->incDate($dateArr, 1, "hour");
			}
			else
			{
				$dateArr["seconds"] = 0;
				$dateArr["minutes"]++;
			}
		}
	}

	/**
	 * Calcula la próxima ejecución de un proceso
	 * @param array $job Proceso
	 * @return date con la próxima ejecución
	 */
	private function getLastScheduledRunTime($job)
	{

		$extjob = Array();

		$this->parseElement($job[PC_MINUTE], $extjob[PC_MINUTE], 60);
		$this->parseElement($job[PC_HOUR], $extjob[PC_HOUR], 24);
		$this->parseElement($job[PC_DOM], $extjob[PC_DOM], 31);
		$this->parseElement($job[PC_MONTH], $extjob[PC_MONTH], 12);
		$this->parseElement($job[PC_DOW], $extjob[PC_DOW], 7);

		if ($this->debug > 2) $this->_debug_text .= "A ejecutar cada...\n" . print_r($extjob, TRUE);
		$last = $this->getLastActualRunTime($job[PC_CMD]);
		if (!isset($last)) $last = time();
		$dateArr = getdate($last);
		if ($dateArr["mon"]==13) 
		{
			$dateArr["mon"]=1;
			++$dateArr["year"];
		}
		if ($this->debug > 1) $this->_debug_text .= "Última ejecución\n" . print_r($dateArr, TRUE);
		$minutesAhead = 0;
		$change = FALSE;
		while (
			($minutesAhead < 525600) &&
			(!$extjob[PC_MINUTE][$dateArr["minutes"]] ||
			!$extjob[PC_HOUR][$dateArr["hours"]] ||
			!$extjob[PC_DOM][$dateArr["mday"]] ||
			!$extjob[PC_DOW][$dateArr["wday"]] ||
			!$extjob[PC_MONTH][$dateArr["mon"]])
			)
		{
			if (!$extjob[PC_DOM][$dateArr["mday"]] || !$extjob[PC_DOW][$dateArr["wday"]])
			{
				$this->incDate($dateArr, 1, "mday");
				$minutesAhead += 1440;
				$change = TRUE;
				continue;
			}
			if (!$extjob[PC_HOUR][$dateArr["hours"]])
			{
				$this->incDate($dateArr, 1, "hour");
				$minutesAhead += 60;
				$change = TRUE;
				continue;
			}
			if (!$extjob[PC_MINUTE][$dateArr["minutes"]])
			{
				$this->incDate($dateArr, 1, "minute");
				++$minutesAhead;
				$change = TRUE;
				continue;
			}
			if ($dateArr["mon"]==13) 
			{
				$dateArr["mon"]=1;
				++$dateArr["year"];
			}
		}
		// Si se ejecuta cada minuto, que no se repita
		if (!$change) $this->incDate($dateArr, 1, "minute");

		if ($this->debug > 1) $this->_debug_text .= "Próxima ejecución\n" . print_r($dateArr, TRUE);

		return mktime($dateArr["hours"],$dateArr["minutes"],0,$dateArr["mon"],$dateArr["mday"],$dateArr["year"]);
	}

	/**
	 * Crea el nombre dode se almacena el estado del proceso
	 * @param string $jobname Nombre del trabajo
	 * @return string
	 */
	private function getJobFileName($jobname)
	{
		$jobfile = $this->writeDir . urlencode($jobname).".job";
		return $jobfile;
	}

	/**
	 * Devuelve al última ejecución de un proceso
	 * @param string $jobname Nombre del proceso
	 */
	private function getLastActualRunTime($jobname)
	{
		$jobfile = $this->getJobFileName($jobname);
		if (file_exists($jobfile))
		{
			return filemtime($jobfile);
		}
		return null;
	}

	/**
	 * Indica la última ejecución del proceso
	 * @param string $jobname Nombre del proceso
	 * @param date $lastRun última ejecución
	 */
	private function markLastRun($jobname, $lastRun)
	{
		$jobfile = $this->getJobFileName($jobname);
		touch($jobfile);
	}

	/**
	 * Ejecuta un trabajo
	 * @param array $job Trabajo
	 */
	private function runJob($job, $run = TRUE)
	{
		#$this->resultsSummary = "";

		$lastActual = $job["lastActual"];
		$lastScheduled = $job["lastScheduled"];
		if ($lastScheduled < time() || empty($lastScheduled) || !isset($lastActual) /* && $lastScheduled < $lastActual*/)
		{
			$this->logMessage("Running " . $job[PC_CMD]);
			$this->logMessage("  Line 	          " . $job[PC_CRONLINE]);
			$this->logMessage("  Last run:        " . format_datetime($lastActual));
			$this->logMessage("  Last scheduled:  " . format_datetime($lastScheduled));
				
			$this->markLastRun($job[PC_CMD], $lastScheduled);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Lee el archivo de trabajos programados
	 * @return array
	 */
	private function parseCronFile()
	{
		$time = filemtime($this->cronTab);
		if ($time != $this->timestamp)
		{
			$file = file($this->cronTab);
			$job = Array();
			$jobs = Array();
			for ($i=0;$i<count($file);$i++)
			{
				if ($file[$i][0] != '#')
				{
					if (preg_match("~^([-0-9,/*]+)\\s+([-0-9,/*]+)\\s+([-0-9,/*]+)\\s+([-0-9,/*]+)\\s+([-0-7,/*]+|(-|/|Sun|Mon|Tue|Wed|Thu|Fri|Sat)+)\\s+([^#]*)\\s*(#.*)?$~i", $file[$i], $job))
					{
						$jobNumber = count($jobs);
						$jobs[$jobNumber] = $job;
						if ($jobs[$jobNumber][PC_DOW][0] != '*' && !is_numeric($jobs[$jobNumber][PC_DOW]))
						{
							$jobs[$jobNumber][PC_DOW] = str_replace(
							Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'),
							Array(0, 1, 2, 3, 4, 5, 6),
							$jobs[$jobNumber][PC_DOW]);
						}
						$jobs[$jobNumber][PC_CMD] 		= trim($job[PC_CMD]);
						$jobs[$jobNumber][PC_COMMENT] 	= trim(substr($job[PC_COMMENT],1));
						$jobs[$jobNumber][PC_CRONLINE] 	= trim($file[$i]);
					}
					$jobfile = $this->getJobFileName($jobs[$jobNumber][PC_CMD]);

					$jobs[$jobNumber]['lastActual'] = $this->getLastActualRunTime($jobs[$jobNumber][PC_CMD]);
					$jobs[$jobNumber]['lastScheduled'] = $this->getLastScheduledRunTime($jobs[$jobNumber]);
				}
			}

			$this->multisort($jobs, 'lastScheduled');
			$this->timestamp = $time;
			$this->jobs = $jobs;

			if ($this->debug > 1) $this->_debug_text .= "parseCronFile\n" . var_export($jobs, TRUE);
			return $jobs;
		}
		else
		{
			foreach ($this->jobs as $k => $job)
			{
				$this->jobs[$k]['lastActual'] = $this->getLastActualRunTime($this->jobs[$k][PC_CMD]);
				$this->jobs[$k]['lastScheduled'] = $this->getLastScheduledRunTime($this->jobs[$k]);
			}
			return $this->jobs;
		}
	}

	/**
	 * Marca el trabajo con ejecutado
	 * @param array $job Información del trabajo
	 * @return void
	 */
	function running($job)
	{
		$this->markLastRun($job[PC_CMD], time());
	}
	
	/**
	 * Guarda el último resultado del trabajo
	 * @param string $job Nombre del trabajo
	 * @param string $res
	 */
	function set_result($job, $res)
	{
		$filename = $this->getJobFileName($job) . '.res';
		file_put_contents($filename, $res);		
	}
	
	/**
	 * Devuelve el resultado de la última ejecución de un proceso
	 * @param string $job Nombre del trabajo
	 * @return string
	 */
	function get_result($job)
	{
		$filename = $this->getJobFileName($job) . '.res';
		return (file_exists($filename)?file_get_contents($filename):null);		
	}

	/**
	 * Decide los procesos que se deben ejecutar
	 * @return array Lista de los procesos a ejecutar
	 */
	function get_to_run()
	{
		if ($this->debug > 0) $this->_debug_text = '';

		$jobs = $this->parseCronFile();
		$jobsRun = 0;
		$torun = array();
		for ($i=0; $i<count($jobs); $i++)
		{
			if ($this->runJob($jobs[$i]))
			{
				$torun[] = array('job' => $jobs[$i][PC_CMD], 'title' => $jobs[$i][PC_COMMENT]);
				#$jobsRun++;
			}
		}
		return $torun;
	}

	/**
	 * Devuelve la información de los trabajos
	 * @return array Las tareas del fichero
	 */
	function get_info()
	{
		$jobs = $this->parseCronFile();
		foreach ($jobs as $k => $job)
		{
			$jobs[$k]['result'] = $this->get_result($job[PC_CMD]);
		}
		return $jobs;
	}
}

/* End of file Cron.php */
/* Location: ./system/libraries/cron.php */
