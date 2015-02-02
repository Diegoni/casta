<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Líneas de devolucion
 *
 */
class Devolucionlinea extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Devolucionlinea
	 */
	function __construct()
	{
		parent::__construct('compras.devolucionlinea', 'compras/m_devolucionlinea', TRUE, null, 'Líneas devolución');
	}

	/**
	 * Líneas de devolución que se pueden rechazar
	 * @param $id Id del pedido
	 * @return DATA
	 */
	function rechazables($id = null)
	{
		$id	= isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			// Lee las líneas de devolución rechazables
			$data = $this->reg->get(null, null, 'cTitulo', null, "nIdDevolucion ={$id} AND (nCantidad - ISNULL(nRechazadas,0) > 0)");
			foreach ($data as $k => $v)
			{
				$data[$k]['nCantidad'] -= $data[$k]['nRechazadas']; 
			}

			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

}

/* End of file devolucionlinea.php */
/* Location: ./system/application/controllers/compras/devolucionlinea.php */