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
 * Librería para almacenar datos descargados de Internet
 * @author alexl
 *
 */
class WebSave 
{
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;	

	/**
	 * Constructor
	 * @return WebSave
	 */
	function __construct()
	{
		$this->obj =& get_instance();

		log_message('debug', 'WebSave Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Añade la firma de stampo al fichero
	 * @param string $file Fichero
	 */
	private function _set_stamp($file)
	{
		$filename_stamp =  $file . '.stamp';
		$fs = fopen($filename_stamp, 'w');
		fwrite($fs, time());
		fclose($fs);
	}

	/**
	 * Devuelve la firma de stamp al fichero
	 * @param string $file Fichero
	 * @return int Valor del stamp
	 */
	private function _get_stamp($file)
	{
		$filename_stamp =  $file . '.stamp';
		if (file_exists($filename_stamp))
		{
			$fs = fopen($filename_stamp, 'r');
			$s = fgets($fs);
			fclose($fs);
			return $s;
		}
		return null;
	}

	/**
	 * Indica si un fichero ha superado el timeout
	 * @param string $file Fichero
	 * @return int $timeout Tiempo de espera, en ms
	 */
	private function _timeout($file, $timeout = null)
	{
		if (!isset($timeout))
			return FALSE;
		$s = $this->_get_stamp($file);
		return (time() - $s) > $timeout;
	}

	/**
	 * Genera el nombre del fichero
	 * @param  string $code    Id del fichero
	 * @param  string $group   Grupo de cache
	 * @return string Nombre del fochero completo con el path
	 */
	private function _file_name($code, $group, $ext = 'html')
	{
		$code = trim($code);
		$md5 = md5($code);
		$a = $md5[0];
		$b = $md5[1];
		$path = DIR_CACHE_PATH . 'webfiles' . DS . $group . DS . $a . DS . $b . DS;
		if (!is_dir($path))
			mkdir($path, 0777, TRUE);
		return $path . $code . '.' .$ext;
	}

	/**
	 * Obtiene todos los archivos asociados a un código en un grupo
	 * @param  string $code    Id del fichero
	 * @param  string $group   Grupo de cache
	 * @return array Listado de archivos
	 */
	private function _get_files($code, $group)
	{
		$code = trim($code);
		$md5 = md5($code);
		$a = $md5[0];
		$b = $md5[1];
		$path = DIR_CACHE_PATH . 'webfiles' . DS . $group . DS . $a . DS . $b . DS;
		return glob($path . $code . '*');
	}

	/**
	 * Guarda un archivo en la cache
	 * @param  string $code    Id del fichero
	 * @param  string $group   Grupo de cache
	 * @param  mixed $html 	Daros a archivar
	 * @return string Nombre del fichero
	 */
	function put($code, $group, $html)
	{
		$file = $this->_file_name($code, $group);
		file_put_contents($file, $html);
		$this->_set_stamp($file, time());

		return $file;
	}

	/**
	 * Lee un fichero de la cache
	 * @param  string $code    Id del fichero
	 * @param  string $group   Grupo de cache
	 * @param  int $timeout Timeout en ms
	 * @return string -> contenido del archivo, null -> no hay caché o venció
	 */
	function get($code, $group, $timeout = null)
	{
		$file = $this->_file_name($code, $group);
		if (file_exists($file))
		{
			if ($this->_timeout($file, $timeout))
				return null;
			return file_get_contents($file);
		}
		return null;
	}

	/**
	 * Añade una portada a la Cache
	 * @param string $code Código asociado
	 * @param string $url  URI del archivo
	 */
	function set_cover($code, $url)
	{
		$this->obj->load->library('SearchImages');
		if (is_file($url))
		{
			$parts = pathinfo($url);
			$res['file'] = $url;
			$res['ext'] = $parts['extension']; 
		}
		else
		{
			$res = $this->obj->searchimages->download($url);
		}

		if ($res)
		{
			$file = $this->_file_name($code, 'covers', $res['ext']);
			copy($res['file'], $file);
			if (!is_file($url))
			{
				unlink($res['file']);
			}
			return $file;
		}
		return null;
	}

	/**
	 * Añade una reseña a la Cache
	 * @param string $code Código asociado
	 * @param string $description  Texto
	 */
	function set_description($code, $description)
	{
		$file = $this->_file_name($code, 'description', 'txt');
		file_put_contents($file, $description);
		$this->_set_stamp($file, time());

		return $file;
	}

	/**
	 * Devuele la URL de un archivo de cache
	 * @param  string $file Nombre del fichero local
	 * @return string URL del archivo
	 */
	function _get_http($file)
	{
		return site_url(str_replace(DIR_CACHE_PATH, URL_CACHE_PATH, $file));
	}

	/**
	 * Obtiene una portada a la Cache
	 * @param string $code Código asociado
	 * @param  int $timeout Timeout en ms
	 * @return string $url  URI del archivo
	 */
	function get_cover($code, $http=TRUE, $timeout = null)
	{
		if (empty($code)) return null;
		$this->obj->load->library('SearchImages');
		$files = $this->_get_files($code, 'covers');
		foreach ($files as $value) 
		{
			if (filesize($value) == 0)
			{
				unlink($value);
			}
			else
			{
				$parts = pathinfo($value);
				$ext = $parts['extension']; 
				if (strpos($this->obj->searchimages->get_mime($ext),'image') !== FALSE)
				{
					return $http?$this->_get_http($value):$value;
				}
			}
		}
		return null;
	}

	/**
	 * Añade una reseña a la Cache
	 * @param string $code Código asociado
	 * @param string $description  Texto
	 */
	function get_description($code, $timeout = null)
	{
		$file = $this->_file_name($code, 'description', 'txt');
		if (!file_exists($file) || $this->_timeout($file, $timeout))
			return null;
		return file_get_contents($file);
	}
}
/* End of file WebSave.php */
/* Location: ./system/libraries/WebSave.php */