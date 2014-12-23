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
 * Gestor de Logs de la aplicación
 * @author alexl
 *
 */
class Logger {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return Logger
	 */
	function __construct()
	{
		$this->obj =& get_instance();

		$file = DIR_CONTRIB_PATH. 'Log' . DS . 'Log.php';
		$file2 = DIR_CONTRIB_PATH. 'LogParser' . DS . 'class-logparse.php';

		if (!file_exists($file)||!file_exists($file2))
		{
			die('No se ha encontrado las librerías PEAR Log');
		}
		require_once($file);
		require_once($file2);
		log_message('debug', 'Logger Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Añade un mensaje de Log
	 * @param string $message Mensaje
	 * @param string $group Grupo de log
	 */
	function log($message, $group = null)
	{
		// Crea grupo
		$enabled = $this->obj->config->item('bp.logger.groups');
		$name = !isset($group)?'default':$group;
		if ($this->obj->config->item('bp.logger.enabled') && (isset($enabled[$name]) && $enabled[$name] == TRUE))
		{
			if (isset($group))
			{
				$dir = DIR_LOG_PATH . $group;
				if (!file_exists($dir))
				{
					mkdir($dir, 0777);
				}
				$dir .= DS;
			}
			else
			{
				$dir = DIR_LOG_PATH;
			}
			$name = 'log-' . date('Y-m-d') . '.txt';
			$filename = $dir . $name;
			$conf = array('mode' => 0777, 'timeFormat' => '%x %X');
			$title = $this->obj->config->item('bp.application.name');
			$file = &Log::singleton('file', $filename, $title, $conf);
			
			$this->obj->load->library('Userauth');		
			$username = $this->obj->userauth->get_username();
			$file->log('(' . $username .') ' . $message);
		}
	}

	/**
	 * Devuelve el listado de logs de un grupo
	 * @param string $group Grupo de log
	 * @return array
	 */
	function get_list($group = null)
	{
		$dir = DIR_LOG_PATH . ((isset($group))?$group:'');
			
		if (!file_exists($dir))
		{
			return null;
		}
		$list = null;
		if ($dh = opendir($dir))
		{
			while (($file = readdir($dh)) !== false)
			{
				if (preg_match("/^log-(.*)\.txt/", $file, $name))
				{
					$list[] = $name;
				}
			}
			closedir($dh);
		}
		sort($list);
		return $list;
	}

	/**
	 * Devuelve el listado de grupos de logs
	 * @return array
	 */
	function get_groups()
	{
		$dir = DIR_LOG_PATH;
			
		if (!file_exists($dir))
		{
			return null;
		}
		$list = null;
		if ($dh = opendir($dir))
		{
			while (($file = readdir($dh)) !== false)
			{
				if($file != "." && $file != "..")
				{
					if(is_dir($dir.$file)) $list[] = $file;
				}
			}
			closedir($dh);
		}
		sort($list);
		return $list;
	}

	/**
	 * Lee el archivo de Log indicado
	 * @param $log
	 * @param $group
	 */
	function output($log, $group = null)
	{
		$name = 'log-' . $log . '.txt';
		$file = DIR_LOG_PATH . ((isset($group))?$group . DS :'') . $name;
		if (file_exists($file))
		{
			return file_get_contents($file);
		}
		return null;
		/*
		 try
		 {
		 $instance = new LogParser($file);

		 // get a log line
		 while ($LogString = $instance->GetLine())
		 {
		 return $instance->Parse($LogString); // parse/format the line
		 }
		 }
		 catch(Exception $e)
		 {
		 return $e->getMessage();
		 }
		 */
		$logparse = new LogParse($file);
		var_dump($logparse->parseFile());
		///$logparse->addFilter("prio", $_POST["prio"]);
		return $logparse->Output();
	}
}

/* End of file logger.php */
/* Location: ./system/libraries/logger.php */