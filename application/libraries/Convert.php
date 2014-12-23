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
 * Conversor de varios formatos
 * ODT, RTF, DOCX
 * @author alexl
 *
 */
class Convert {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return Convert
	 */
	function __construct()
	{
		$this->obj =& get_instance();

		log_message('debug', 'Convert Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Convierte un fichero HTML a ODT, DOCX, RTF usando PANDOC
	 * @param string $fin Fichero HTML de entrada
	 * @param string $ext Extensión del fichero
	 * @param string $format Formato de salida
	 * @param bool $stream Envia el fichero como stream
	 * @param bool $attached TRUE: se envía como fichero adjunto, FALSE: se envía como datos
	 * @return file
	 */
	private function _convert($fin, $ext, $format, $stream = TRUE, $attached = TRUE)
	{
		$this->obj->load->library('HtmlFile');

		$name = pathinfo($fin);
		$name = $name['filename'] . '.' . $ext;
		$fout = $this->obj->htmlfile->pathfile($name);

		if (!file_exists($fout))
		{
			// Obtiene la orientación y page-size del fichero
			$html = file_get_contents($fin);
			#$o = $this->obj->htmlfile->get_orientation($html);
			#$ps = $this->obj->htmlfile->get_page_size($html);

			// Parámetros por defecto
			$execpath = $this->obj->config->item('convert.path');
			$default = $this->obj->config->item('convert.parameters');
			#$papersize = isset($ps)?$ps:(isset($papersize)?$papersize:$this->obj->config->item('pdf.papersize'));
			#$orientation = isset($o)?$o:(isset($orientation)?$orientation:$this->obj->config->item('pdf.orientation'));

			// Aplica los replaces
			$replaces = $this->obj->config->item('pdf.replaces');
			if (isset($replaces))
			{
				foreach ($replaces as $k => $v)
				{
					$html = str_replace($k, $v, $html);
				}
				$name2 = pathinfo($fin);
				$name2 = $name2['filename'] . '.rep.html';
				$fin = $this->obj->htmlfile->pathfile($name2);
				file_put_contents($fin, $html);
			}

			// Comando a ejecutar
			$params = str_replace(array('%in%', '%out%', '%format%'), array($fin, $fout, $ext), $default);
			$exec = $execpath . ' ' . $params;
			#var_dump($exec); die();

			// Sin límite de tiempo
			set_time_limit(0);
			$result = null;
			#$this->obj->out->dialog($ext, $exec);
			#echo $exec; die();
			//$r = exec($exec, $result);
			#$xfc_dir = $this->obj->config->item('convert.xfcdir');
				
			#putenv("XFC_DIR={$xfc_dir}");
			$r = exec($exec, $result);
			#echo '<pre>' . $exec . '</pre>';
			#echo '<pre>' . print_r($result) . '</pre>';
			#echo '<pre>' . $r . '</pre>';
			#die();
		}
		// Devuelve el archivo como un un adjunto si así se indica o simplemente deja el archivo creado
		if ($stream)
		{
			if ($attached)
			{
				header("Content-type: application/{$ext}; charset=UTF-8");
				header("Content-Disposition: attachment; filename={$name}");
				header("Pragma: no-cache");
				header("Expires: 0");
				readfile($fout);
			}
			else
			{
				redirect($this->obj->htmlfile->pathfile($name));
			}
				
		}
		return $fout;
	}

	/**
	 * Convierte un fichero HTML a ODT, DOCX, RTF usando XFC
	 * @param string $fin Fichero HTML de entrada
	 * @param string $ext Extensión del fichero
	 * @param string $format Formato de salida
	 * @param bool $stream Envia el fichero como stream
	 * @param bool $attached TRUE: se envía como fichero adjunto, FALSE: se envía como datos
	 * @return file
	 */
	private function _convert2($fin, $ext, $format, $stream = TRUE, $attached = TRUE)
	{
		$this->obj->load->library('HtmlFile');

		$name = pathinfo($fin);
		$name = $name['filename'] . '.' . $ext;
		$fout = $this->obj->htmlfile->pathfile($name);

		if (!file_exists($fout))
		{
			// Obtiene la orientación y page-size del fichero
			$html = file_get_contents($fin);
			#$o = $this->obj->htmlfile->get_orientation($html);
			#$ps = $this->obj->htmlfile->get_page_size($html);

			// Parámetros por defecto
			$execpath = $this->obj->config->item('convert2.path');
			$default = $this->obj->config->item('convert2.parameters');
			#$papersize = isset($ps)?$ps:(isset($papersize)?$papersize:$this->obj->config->item('pdf.papersize'));
			#$orientation = isset($o)?$o:(isset($orientation)?$orientation:$this->obj->config->item('pdf.orientation'));

			// Aplica los replaces
			$replaces = $this->obj->config->item('pdf.replaces');
			if (isset($replaces))
			{
				foreach ($replaces as $k => $v)
				{
					$html = str_replace($k, $v, $html);
				}
				$name2 = pathinfo($fin);
				$name2 = $name2['filename'] . '.rep.html';
				$fin = $this->obj->htmlfile->pathfile($name2);
				file_put_contents($fin, $html);
			}
			// Comando a ejecutar
			$params = str_replace(array('%in%', '%out%', '%format%'), array($fin, $fout, $format), $default);
			$exec = $execpath . ' ' . $params;
			#die($exec);

			// Sin límite de tiempo
			set_time_limit(0);
			$result = null;
			#echo $exec; die();
			#$r = exec($exec, $result);
			$xfc_dir = $this->obj->config->item('convert.xfcdir');
				
			putenv("XFC_DIR={$xfc_dir}");
			$r = exec($exec, $result);
			#echo '<pre>' . $exec . '</pre>';
			#echo '<pre>' . print_r($result) . '</pre>';
			#echo '<pre>' . $r . '</pre>';
			#die();
		}
		// Devuelve el archivo como un un adjunto si así se indica o simplemente deja el archivo creado
		if ($stream)
		{
			if ($attached)
			{
				header("Content-type: application/{$ext}; charset=UTF-8");
				header("Content-Disposition: attachment; filename={$name}");
				header("Pragma: no-cache");
				header("Expires: 0");
				readfile($fout);
			}
			else
			{
				redirect($this->obj->htmlfile->pathfile($name));
			}
				
		}
		return $fout;
	}

	/**
	 * Convierte un fichero HTML a ODT
	 * @param string $fin Fichero HTML de entrada
	 * @param bool $attached TRUE: se envía como fichero adjunto, FALSE: se envía como datos
	 * @return file
	 */
	function odt($fin, $attached = TRUE)
	{
		return $this->_convert($fin, 'odt', 'odt-xfc', $attached);
	}

	/**
	 * Convierte un fichero HTML a RTF
	 * @param string $fin Fichero HTML de entrada
	 * @param bool $attached TRUE: se envía como fichero adjunto, FALSE: se envía como datos
	 * @return file
	 */
	function rtf($fin, $attached = TRUE)
	{
		return $this->_convert($fin, 'rtf', 'rtf-xfc', $attached);
	}

	/**
	 * Convierte un fichero HTML a Word OpenXML
	 * @param string $fin Fichero HTML de entrada
	 * @param bool $attached TRUE: se envía como fichero adjunto, FALSE: se envía como datos
	 * @return file
	 */
	function docx($fin, $attached = TRUE)
	{
		return $this->_convert($fin, 'docx', 'ooxml-xfc', $attached);
	}
}

/* End of file pdflib.php */
/* Location: ./system/libraries/pdflib.php */