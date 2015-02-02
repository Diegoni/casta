<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-20100, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

require_once('factura.php');
/**
 * TPV
 *
 */
class Tpv extends Factura
{
	/**
	 * Constructor
	 *
	 * @return Tpv
	 */
	function __construct()
	{
		parent::__construct('ventas/M_factura2');
	}

	/**
	 * Ventana de TPV
	 * @param int $open_id ID de la factura a abrir
	 * @return TAB
	 */
	function index($open_id = null)
	{
		$this->userauth->roleCheck($this->auth .'.tpv');
		$open_id = isset($open_id)?$open_id:$this->input->get_post('open_id');
		$this->load->library('Configurator');
		$data['tpv'] = TRUE;
		#$data['descuento'] = ($this->configurator->user('ventas.tpv.aplicardescuento'))?$this->configurator->user('ventas.tpv.descuento'):0;
		
		$this->_show_form('tpv', 'ventas/tpv.js', $this->lang->line('TPV'), null, null, $open_id, $data);
	}
}

/* End of file Factura.php */
/* Location: ./system/application/controllers/ventas/factura.php */