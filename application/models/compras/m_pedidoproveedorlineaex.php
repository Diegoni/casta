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

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'compras' . DIRECTORY_SEPARATOR . 'm_pedidoproveedorlinea.php');

/**
 * Líneas de pedido proveedor extendida
 *
 */
class M_pedidoproveedorlineaex extends M_pedidoproveedorlinea
{
	/**
	 * Constructor
	 * @return M_pedidoproveedorlineaex
	 */
	function __construct()
	{
		parent::__construct();
		$this->_alias = array(
			'cTitulo'			=> array('cTitulo'), 
			'cISBN'				=> array('cISBN'), 
			'cProveedor'		=> array('Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa', DATA_MODEL_TYPE_INT),
			'nServir'			=> array('Cat_Secciones_Libros.nStockServir'),
			'nDias2' 			=> array('dCreacion'),
			'nDias' 			=> array('Doc_PedidosProveedor.dFechaEntrega'),
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
			->select('Cat_Secciones_Libros.nStockReservado, Cat_Secciones_Libros.nStockRecibir, Cat_Secciones_Libros.nStockAPedir, Cat_Secciones_Libros.nStockServir, Cat_Secciones_Libros.nStockADevolver')
			->select('Cat_Secciones_Libros.nIdSeccion')
			->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdLibro = Doc_LineasPedidoProveedor.nIdLibro AND Cat_Secciones_Libros.nIdSeccion = Doc_LineasPedidoProveedor.nIdSeccion', 'left');

			$this->db->select('Prv_Proveedores.nIdProveedor, Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa')
			->select('Doc_PedidosProveedor.nIdPedido')
			->select($this->_date_field('Doc_PedidosProveedor.dFechaEntrega', 'dFechaEntrega'))
			->join('Doc_PedidosProveedor', "Doc_PedidosProveedor.nIdPedido = {$this->_tablename}.nIdPedido", 'left')
			->join('Prv_Proveedores', "Prv_Proveedores.nIdProveedor = Doc_PedidosProveedor.nIdProveedor", 'left');

			$this->db->select('Doc_ReclamacionesPedidoProveedor.nIdReclamacion')
			->select($this->_date_field('Doc_ReclamacionesPedidoProveedor.dCreacion', 'dReclamacion'))
			->join('(SELECT MAX(nIdReclamacion) nIdReclamacion, nIdLineaPedido
				FROM Doc_LineasReclamacionPedidoProveedor (NOLOCK)
				GROUP BY nIdLineaPedido) a', "a.nIdLineaPedido = {$this->_tablename}.nIdLinea", 'left')
			->join('Doc_ReclamacionesPedidoProveedor', 'Doc_ReclamacionesPedidoProveedor.nIdReclamacion = a.nIdReclamacion', 'left');

			$this->db->select('Cli_Clientes.nIdCliente, Cli_Clientes.cNombre cNombre2, Cli_Clientes.cApellido cApellido2, Cli_Clientes.cEmpresa cEmpresa2')
			->select('Sus_Suscripciones.nIdSuscripcion')
			->join('Sus_PedidosSuscripcion', 'Doc_PedidosProveedor.nIdPedido = Sus_PedidosSuscripcion.nIdPedido', 'left')
			->join('Sus_Suscripciones', 'Sus_Suscripciones.nIdSuscripcion = Sus_PedidosSuscripcion.nIdSuscripcion AND Doc_LineasPedidoProveedor.nIdLibro = Sus_Suscripciones.nIdRevista', 'left')
			->join('Cli_Clientes', 'Sus_Suscripciones.nIdCliente = Cli_Clientes.nIdCliente', 'left');
			#->where('Doc_LineasPedidoProveedor.nIdPedido = Doc_PedidosProveedor.nIdPedido AND ');
			
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
			$disponible = isset($data['nStockFirme'])?$data['nStockFirme']:0;
			$disponible += isset($data['nStockDeposito'])?$data['nStockDeposito']:0;
			$disponible -= isset($data['nStockReservado'])?$data['nStockReservado']:0;
			$disponible -= isset($data['nStockADevolver'])?$data['nStockADevolver']:0;
			$data['nStockDisponible'] = $disponible;
			
			$data['cProveedor'] = format_name($data['cNombre'], $data['cApellido'], $data['cEmpresa']);
			
			if (isset($data['dFechaEntrega']))
			{
				$data['nDias'] = daysDifference($data['dFechaEntrega'], time());
			}
			if (isset($data['dCreacion']))
			{
				$data['nDias2'] = daysDifference($data['dCreacion'], time());
			}
			$data['cCliente'] = format_name($data['cNombre2'], $data['cApellido2'], $data['cEmpresa2']);
						
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_articulosearch.php */
/* Location: ./system/application/models/catalogo/M_articulosearch.php */
