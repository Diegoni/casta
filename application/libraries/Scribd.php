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

require_once(DIR_CONTRIB_PATH. 'scribd' . DS . 'scribd.php');

/**
 * Acceso a Scribd
 * @author alexl
 *
 */
class Scribd {
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;
	/**
	 * Objecto Scribd para comunicarse con el servidor
	 * @var ScribdServer
	 */
	private $scribd;
	/**
	 * Último error
	 * @var string
	 */
	private $_error;

	/**
	 * Constructor
	 * @return Scribd
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		$scribd_api_key = $this->obj->config->item('bp.scribd.api_key');
		$scribd_secret = $this->obj->config->item('bp.scribd.secret');

		$this->scribd = new ScribdServer($scribd_api_key, $scribd_secret);

		log_message('debug', 'Scribd Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Uploa de un documento
	 * @param string $file Fichero a subir
	 * @param string $doc_type Tipo de documento. (optional) Identify the document type with the extension for this file. If the extension is not included, Scribd will try to infer it from the file, but you will see better results if you include an explicit extension. The following extensions are currently supported: pdf, txt, ps, rtf, epub, odt, odp, ods, odg, odf, sxw, sxc, sxi, sxd, doc, ppt, pps, xls, docx, pptx, ppsx, xlsx, tif, tiff
	 * @param string $access Acceso. ["public", "private"]	 (optional) Default: "private".
	 * @param int $rev_id Id de la revisión
	 * @return mixed, FALSE: ha habido error, Si ha ido bien Array ( [doc_id] => 1026598 [access_key] => key-23nvikunhtextwmdjm2i )
	 */	
	function upload($file, $doc_type = null, $access = 'public', $rev_id = null)
	{
		$data = $this->scribd->upload($file, $doc_type, $access, $rev_id); 
		if (!isset($data['doc_id'])) 
		{
			$this->_error = $this->scribd->get_error();
			return FALSE;
		}
		return $data;
	}
	/**
	 * Devuelve la URL de accesso al documento
	 * @param string $doc_id Id del documento Scribd
	 * @param string $access_key Key de acceso
	 * @param string $secret_password Password para acceder al documento
	 * @param bool $fullscreen TRUE: URL de fullscreen, FALSE: Normal
	 */
	function url($doc_id, $access_key, $secret_password = null, $fullscreen = TRUE)
	{
		return $fullscreen?
			"http://es.scribd.com/fullscreen/{$doc_id}?access_key={$access_key}":
			"http://es.scribd.com/doc/{$doc_id}?secret_password={$secret_password}";
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
/* End of file Scribd.php */
/* Location: ./system/libraries/scribd.php */