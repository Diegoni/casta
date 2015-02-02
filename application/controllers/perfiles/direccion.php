<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	perfiles
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Direcciones
 *
 */
class Direccion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('perfiles.direccion', 'perfiles/M_Direccion', TRUE, null, 'Direcciones');
	}

	/**
	 * Muestra la dirección en un mapa de Google.
	 * @param  string $direccion Dirección a mostrar
	 * @return HTML
	 */
	function mapa($direccion = null)
	{
		$direccion 	= isset($direccion)?$direccion:$this->input->get_post('direccion');
		//$url = $this->config->item('bp.map.url');
		if ($direccion)
		{
			$direccion = preg_replace('/\[.*?\]/', '', $direccion);
			$data['direccion'] = str_replace('-', ',', $direccion);
			$message = $this->load->view('perfiles/mapa', $data, TRUE);
			// Respuesta
			$this->out->html_file($message, $this->lang->line('Mapa'), 'iconoMapaTab');
		}
		$this->out->error($this->lang->line('mapa-no-configurado'));
	}
}

/* End of file direccion.php */
/* Location: ./system/application/controllers/perfiles/direccion.php */