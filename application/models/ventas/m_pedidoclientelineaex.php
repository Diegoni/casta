<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'ventas' . DIRECTORY_SEPARATOR . 'm_pedidoclientelinea.php');

/**
 * Líneas de pedido cliente extendida
 *
 */
class M_pedidoclientelineaex extends M_pedidoclientelinea
{
	/**
	 * Constructor
	 * @return M_pedidoclientelineaex
	 */
	function __construct()
	{
		parent::__construct();
		$this->_alias = array(
			'cTitulo'			=> array('cTitulo'), 
			'cISBN'				=> array('cISBN'), 
			'cCliente'			=> array('Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cli_Clientes.cEmpresa', DATA_MODEL_TYPE_INT),
			'nServir'			=> array('Cat_Secciones_Libros.nStockServir'),
			'nRecibir'			=> array('Cat_Secciones_Libros.nStockRecibir'),
			'nPendientes'		=> array('nCantidad - nCantidadServida'),
			'nStockDisponible'	=> array('Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito - Cat_Secciones_Libros.nStockReservado - Cat_Secciones_Libros.nStockADevolver'),
			'nDias' 			=> array('dCreacion'),
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->_fix_sort($sort);

			$this->db->select('Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito nStock')
			->select('Cat_Secciones_Libros.nStockServir nServir')
			->select('Cat_Secciones_Libros.nStockRecibir nRecibir') 
			->select('Cat_Secciones_Libros.nStockReservado, Cat_Secciones_Libros.nStockAPedir, Cat_Secciones_Libros.nStockServir, Cat_Secciones_Libros.nStockADevolver')
			->select('Cat_Secciones_Libros.nIdSeccion')
			->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdLibro = Doc_LineasPedidoCliente.nIdLibro AND Cat_Secciones_Libros.nIdSeccion = Doc_LineasPedidoCliente.nIdSeccion', 'left');

			$this->db->select('Cli_Clientes.nIdCliente, Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cli_Clientes.cEmpresa')
			->select('Doc_PedidosCliente.nIdPedido')
			->join('Doc_PedidosCliente', "Doc_PedidosCliente.nIdPedido = {$this->_tablename}.nIdPedido", 'left')
			->join('Cli_Clientes', "Cli_Clientes.nIdCliente = Doc_PedidosCliente.nIdCliente", 'left');

			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		if (parent::onAfterSelect($data, $id))
		{
			$disponible = isset($data['nStock'])?$data['nStock']:0;
			$disponible -= isset($data['nStockReservado'])?$data['nStockReservado']:0;
			$disponible -= isset($data['nStockADevolver'])?$data['nStockADevolver']:0;
			$data['nStockDisponible'] = $disponible;
			$data['nPendientes'] = $data['nCantidad'] - $data['nCantidadServida'];
			
			$data['cCliente'] = format_name($data['cNombre'], $data['cApellido'], $data['cEmpresa']);
			
			if (isset($data['dCreacion']))
			{
				$data['nDias'] = daysDifference($data['dCreacion'], time());
			}
						
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_articulosearch.php */
/* Location: ./system/application/models/catalogo/M_articulosearch.php */