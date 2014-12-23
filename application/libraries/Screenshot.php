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
 * Generación de Screenshots de páginas Web
 * @author alexl
 *
 */
class Screenshot {
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;
	/**
	 * Último error
	 * @var string
	 */
	private $_error;
	/**
	 * Comando para generar el screenshot
	 * @var string
	 */
	private $command;
	/**
	 * Convert de PDF a PNG
	 * @var string
	 */
	private $convert;

	/**
	 * Timeout para generar imágenes (en ms)
	 * @var int
	 */
	private $timeout;
	/**
	 * Constructor
	 * @return Screenshot
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		$this->command = $this->obj->config->item('bp.screenshot.url2png');
		$this->convert = $this->obj->config->item('bp.screenshot.pdf2png');
		$this->timeout = $this->obj->config->item('bp.screenshot.timeout');
		
		log_message('debug', 'Screenshot Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Devuelve la captura de una Página Web 
	 * @param string $url URL de la página para generar la captura 
	 * @return string URL de la imagen 
	 */
	function url($url)
	{
		$this->obj->load->library('HtmlFile');
		$name = md5($url) . '.png';
		$fout = $this->obj->htmlfile->pathfile($name);
		$cmd = str_replace(array('{input}', '{output}'), array($url, $fout), $this->command);
		set_time_limit(0);
		if (file_exists($fout)) 
			unlink($fout);
		#echo($cmd); die();
		$this->obj->utils->exec_timeout($cmd, $this->timeout);
		if (!file_exists($fout))
			return FALSE;
		#exec($cmd);		
		// La corta
		list($width, $height) = getimagesize($fout);
		$src = imagecreatefrompng($fout);					
		$dest = imagecreatetruecolor($width, min($width, $height));
		// Copy
		imagecopy($dest, $src, 0, 0, 0, 0, $width, min($width, $height));                     
		if (!imagepng($dest, $fout)) 
			return FALSE;
		return $this->obj->htmlfile->url($name); 		
	}
	
	/**
	 * Devuelve la captura de un documento PDF 
	 * @param string $url URL del documento 
	 * @return string URL de la imagen 
	 */
	function pdf($url)
	{
		 #convert -monitor -density 300 documento.pdf documento.png
		 #Descarga
		$name = time();
		$dir = DIR_TEMP_PATH . $name . '/' ;		
		$origen = $dir . $name . '.pdf';
		$destino = $dir . $name . '.png';
		
		$src = $dir . $name . '-0.png';
		$page = md5($url) . '.png';
		
		mkdir($dir, 0777);
		
		$this->obj->load->library('Utils');
		$this->obj->load->library('HtmlFile');
		$data = $this->obj->utils->get_url($url);
		file_put_contents($origen, $data['response']);
		$cmd = str_replace(array('{input}', '{output}'), array($origen, $destino), $this->convert);
		set_time_limit(0);
		$res = passthru($cmd, $r);
		$fout = $this->obj->htmlfile->pathfile($page);
		copy($src, $fout);
		$this->obj->utils->recursiveDelete($dir);
		return $this->obj->htmlfile->url($page); 
	}

	/**
	 * Último error generado
	 * @return string
	 */
	function get_error()
	{
		return $this->_error;
	}
}
/* End of file Screenshot.php */
/* Location: ./system/libraries/Screenshot.php */