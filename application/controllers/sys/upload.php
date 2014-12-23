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
 * Gestor de Upload
 * Basado en el código de Andrew Rymarczyk @ref
 * http://jsjoy.com/blog/ext-js-extension-awesome-uploader
 *
 * @author alexl
 *
 */
class Upload extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Upload
	 */
	function __construct()
	{
		parent::__construct(null, null, FALSE);
	}

	/**
	 * Upload de un archivo
	 * @param int $rewrite 0: No sobreescribe si existe, 1: sobreescribe si existe
	 * @return DATA nombre del archivo subido
	 */
	function file($rewrite =null)
	{
		$rewrite = isset($rewrite) ? $id : $this->input->get_post('rewrite');
		if($rewrite === FALSE)
			$rewrite = 1;
		$this->load->library('UploadLib');
		#$fp = fopen(__DIR__ . '/upload.txt', 'w+');
		#fwrite($fp, format_datetime(time()). " - Leyendo");
		$res = $this->uploadlib->file($rewrite, 'file');
		#fwrite($fp, print_r($res, TRUE));
		#fclose($fp);
		if($res['success'])
		{
			$this->out->message(TRUE, $res['name']);
		}
		else
		{
			$this->out->error($res['error']);
		}
	}
}

/* End of file upload.php */
/* Location: ./system/application/controllers/sys/upload.php */
