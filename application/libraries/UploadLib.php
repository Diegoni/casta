<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	libraries
 * @category	core
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Soporte para Upload
 * @author alexl
 *
 */
class UploadLib
{
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return UploadLib
	 */
	function __construct()
	{
		$this->obj = &get_instance();
		log_message('debug', 'Upload Class Initialised via ' . get_class($this->obj));
		$this->messages = array();
	}

	/**
	 * Obtiene el fichero subido por Upload del navegador
	 * @param string $field Nombre del campo en el formulario de subida
	 */
	function get_file($field)
	{
		if (isset($_FILES[$field]))
		{
			$destino = $this->obj->config->item('bp_upload_path');
			$file = $destino . '/' . $_FILES[$field]['name'];
			$name = $_FILES[$field]['name'];
			move_uploaded_file($_FILES[$field]['tmp_name'], $file);
			return array(
					'file' => $file,
					'name' => $name
			);
		}
		return NULL;
	}

	/**
	 * Obtiene el fichero subido por Upload del navegador
	 * @param int $rewrite 0: No sobreescribe si existe, 1: sobreescribe si existe
	 * @param string $field Nombre del campo en el formulario de subida
	 * @return array, 'success' => TRUE/FALSE, 'error' => mensaje de error, 'name' =>
	 * Nombre del archivo, 'file' => path completo del archivo
	 */
	function file($rewrite = TRUE, $field = 'file')
	{
		$save_path = $this->obj->config->item('bp_upload_path');
		// Characters allowed in the file name (in a Regular Expression format)
		$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';

		$MAX_FILENAME_LENGTH = $this->obj->config->item('bp.upload.maxnamelength');
		$max_file_size_in_bytes = $this->obj->config->item('bp.upload.max_file_size_in_bytes');

		//Header 'X-File-Name' has the dashes converted to underscores by PHP:
		if (!isset($_SERVER['HTTP_X_FILE_NAME']) && !isset($_FILES[$field]))
		{
			return array(
					'success' => FALSE,
					'error' => $this->obj->lang->line('Missing file name')
			);
		}

		// Comprueba si es un POST estandar de FILE
		if (isset($_FILES[$field]))
		{
			// Comprueba tamaño máximo del sistema
			$POST_MAX_SIZE = ini_get('post_max_size');
			$unit = strtoupper(substr($POST_MAX_SIZE, -1));
			$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));

			if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier * (int)$POST_MAX_SIZE && $POST_MAX_SIZE)
			{
				return array(
						'success' => FALSE,
						'error' => $this->obj->lang->line('POST exceeded maximum allowed size')
				);
			}

			$uploadErrors = array(
					0 => 'There is no error, the file uploaded with success',
					1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
					2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
					3 => 'The uploaded file was only partially uploaded',
					4 => 'No file was uploaded',
					6 => 'Missing a temporary folder'
			);

			if (isset($_FILES[$field]["error"]) && $_FILES[$field]["error"] != 0)
			{
				return array(
						'success' => FALSE,
						'error' => $this->obj->lang->line($uploadErrors[$_FILES[$field]["error"]])
				);
			}
			else
			if (!isset($_FILES[$field]["tmp_name"]) || !@is_uploaded_file($_FILES[$field]["tmp_name"]))
			{
				return array(
						'success' => FALSE,
						'error' => $this->obj->lang->line('Upload failed is_uploaded_file test')
				);
			}
			else
			if (!isset($_FILES[$field]['name']))
			{
				return array(
						'success' => FALSE,
						'error' => $this->obj->lang->line('File has no name')
				);
			}

			# Validate the file size Warning: the largest files supported by this code is 2GB
			$file_size = filesize($_FILES[$field]["tmp_name"]);
			if (!$file_size || $file_size > $max_file_size_in_bytes)
			{
				return array(
						'success' => FALSE,
						'error' => $this->obj->lang->line('File exceeds the maximum allowed size')
				);
			}

			if ($file_size <= 0)
			{
				return array(
						'success' => FALSE,
						'error' => $this->obj->lang->line('File size outside allowed lower bound')
				);
			}

			$filename = basename($_FILES[$field]['name']);
			$normal = TRUE;
		}
		else
		{
			$filename = $_SERVER['HTTP_X_FILE_NAME'];
			$normal = FALSE;
		}

		$file_name = preg_replace('/[^' . $valid_chars_regex . ']|\.+$/i', '', $filename);
		if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH)
		{
			return array(
					'success' => FALSE,
					'error' => $this->obj->lang->line('Invalid file name')
			);
		}

		$destino = $save_path . DS . $file_name;
		if (($rewrite == 0) && file_exists($destino))
		{
			return array(
					'success' => FALSE,
					'error' => $this->obj->lang->line('A file with this name already exists')
			);
		}

		if ($normal)
		{
			if (!move_uploaded_file($_FILES[$field]["tmp_name"], $destino))
			{
				return array(
						'success' => FALSE,
						'error' => $this->obj->lang->line('File could not be saved')
				);
			}
		}
		else
		{
			$file = file_get_contents('php://input');
			file_put_contents($destino, $file);
		}
		return array(
				'success' => TRUE,
				'name' => $file_name,
				'file' => $destino
		);
	}

	/**
	 * Devuelve el path completo a un archivo Upload
	 * @param string $filename Nomnbre del fichero
	 */
	function get_pathfile($filename)
	{
		$save_path = $this->obj->config->item('bp_upload_path');
		return $save_path . DS . $filename;
	}

}

/* End of file uploadlib.php */
/* Location: ./system/libraries/uploadlib.php */
