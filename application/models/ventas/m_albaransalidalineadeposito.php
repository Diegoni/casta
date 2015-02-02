<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'ventas' . DIRECTORY_SEPARATOR . 'm_albaransalidalinea.php');

/**
 * Líneas de albarán de salida temporales
 *
 */
class M_albaransalidalineadeposito extends M_albaransalidalinea
{
	/**
	 * Constructor
	 * @return M_albaransalidalineadeposito
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Doc_LineasLiquidacionDeposito.fPrecio fPrecioA,Doc_LineasLiquidacionDeposito.fDescuento fDescuentoA, Doc_LineasLiquidacionDeposito.nCantidad nCantidadA')
			->select('Doc_LineasAlbaranesEntrada.nIdAlbaran nIdAlbaranEntrada')
			->select('Doc_LineasAlbaranesEntrada.nIdLinea nIdLineaAlbaranEntrada')
			->select('Doc_LineasAlbaranesEntrada.nCantidadLiquidada')
			->select('Doc_AlbaranesEntrada.cNumeroAlbaran')
			->select($this->_date_field('dFecha', 'dFechaAlbaran'))
			->join('Doc_LineasLiquidacionDeposito', 'Doc_LineasLiquidacionDeposito.nIdLineaSalida = Doc_LineasAlbaranesSalida.nIdLineaAlbaran', 'left')
			->join('Doc_LineasAlbaranesEntrada', 'Doc_LineasLiquidacionDeposito.nIdLineaEntrada = Doc_LineasAlbaranesEntrada.nIdLinea', 'left')
			->join('Doc_AlbaranesEntrada', 'Doc_AlbaranesEntrada.nIdAlbaran = Doc_LineasAlbaranesEntrada.nIdAlbaran', 'left');
			
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id =null)
	{
		if (isset($data['fPrecioA']))
		{
			$data['fPrecio'] = $data['fPrecioA'];
			unset($data['fPrecioA']);
		}
		if (isset($data['fDescuentoA']))
		{
			$data['fDescuento'] = $data['fDescuentoA'];
			unset($data['fDescuentoA']);
		}
		if (isset($data['nCantidadA']))
		{
			$data['nEnDeposito'] = $data['nCantidadA'];
			unset($data['nCantidadA']);
		}
		return parent::onAfterSelect($data, $id);
	}
}

/* End of file M_albaransalidalineadeposito.php */
/* Location: ./system/application/models/compras/M_albaransalidalineadeposito.php */
