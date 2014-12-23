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
 * Trabajo con los ficheros HTML en temporal
 * @author alexl
 *
 */
class HtmlFile {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return Out
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		log_message('debug', 'HtmlFile Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Crea el documento final
	 * @param string $html Código HTML
	 * @param string $title Título 
	 * @param string $css Fichero CSS a utilizar
	 * @param bool $complete TRUE: Indica que el HTML contiene el HEAD y el BODY, FALSE: Solo es el body
	 */
	function create($html, $title = null, $css = null, $complete = FALSE )
	{
		$this->obj->load->helper('asset');
		$title =(isset($title))?$title:$this->obj->config->item('bp.application.name');
		if (!$complete)
		{
			if (!isset($css)) $css = $this->obj->config->item('bp.report.style');
			$data = array(
				'title'		=> $title,
				'html'		=> $html,
				'css'		=> $css
			);

			$html = $this->obj->load->view('main/html', $data, TRUE);
		}

		do {
			$filename = time() . '.html';
			$file = $this->pathfile($filename);
		} while (file_exists($file));

		$html = str_replace('<head>', '<head><!-- FILE: ' . $filename . "-->\n", $html);
		file_put_contents($file, $html);

		return $filename;
	}

	/**
	 * Añade la marca de orientación a un archivo HTML
	 * @param string $orientation Orientación
	 * @return string
	 */
	function orientation($orientation)
	{
		return "<!-- ORIENTATION: {$orientation} -->";
	}

	/**
	 * Añade la marca de tipo de página a un HTML
	 * @param string $orientation Orientación
	 * @return string
	 */
	function page_size($pagesize)
	{
		return "<!-- PAGE-SIZE: {$pagesize} -->";
	}

	/**
	 * Añade los márgenes
	 * @param float $top Top
	 * @param float $left Left
	 * @param float $bottom Bottom
	 * @param float $right Right
	 * @return string
	 */
	function margins($top, $left, $bottom, $right)
	{
		return "<!-- PAGE-MARGINS: {$top} {$left} {$bottom} {$right} -->";
	}

	/**
	 * Lee la orientación dentro del un archiovo HTML
	 * @param strimg $html Contenido HTML
	 * @return string null si no hay nada
	 */
	function get_orientation($html)
	{

		$regex = '/ORIENTATION\:.(.*?)-/m';
		preg_match($regex, $html, $result);
		if (!isset($result[1])) return null;
		$res = trim($result[1]);
		if (isset($res) && $res!='')
		{
			return $res;
		}
		return null;
	}

	/**
	 * Lee los máreenes
	 * @param strimg $html Contenido HTML
	 * @return string null si no hay nada
	 */
	function get_margins($html)
	{

		$regex = '/PAGE-MARGINS\:.(.*?)-/m';
		preg_match($regex, $html, $result);
		if (!isset($result[1])) return null;
		$res = trim($result[1]);
		if (isset($res) && $res!='')
		{			
			return explode(' ', $res);
		}
		return null;
	}

	/**
	 * Lee el tamaño de la página dentro del un archiovo HTML
	 * @param strimg $html Contenido HTML
	 * @return string null si no hay nada
	 */
	function get_page_size($html)
	{

		$regex = '/PAGE-SIZE\:.(.*?)-/m';
		preg_match($regex, $html, $result);
		if (!isset($result[1])) return null;
		$res = trim($result[1]);
		if (isset($res) && $res!='')
		{
			return $res;
		}
		return null;
	}

	/**
	 * Crea un documento html para ser enviado al cliente, guarda un fichero temporal
	 *
	 * @param mixed $html Código HTML
	 * @return URL
	 */
	function pathfile($filename)
	{
		return DIR_TEMP_PATH . $filename;
	}

	/**
	 * Crea un documento html para ser enviado al cliente, guarda un fichero temporal
	 *
	 * @param mixed $html Código HTML
	 * @return URL
	 */
	function url($filename)
	{
		return base_url() . URL_TEMP_PATH . $filename;
	}

}

/* End of file htmlfile.php */
/* Location: ./system/libraries/htmlfile.php */