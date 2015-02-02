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

require_once(APPPATH . 'models' . DS . 'ventas' .DS . 'm_pedidoclientelinea.php');

define('PEDIDO_CLIENTE_ESTADOS_PENDIENTE', '1, 2, 3, 6');
/**
 * Líneas de pedido de cliente pendientes
 *
 */
class m_pedidoclientelineapendiente extends m_pedidoclientelinea
{
	/**
	 * Constructor
	 * @return m_pedidoclientelineapendiente
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Asignar los pedidos de cliente a un albarán
	 * @param int $albaran Id del albarán a asignar
	 * @param int $cliente Id del cliente
	 * @param int $seccion Id de la sección
	 * @param int $articulo Id del artículo
	 * @param int $cantidad Número de ejemplares
	 * @param int @idpedido Id de la línea de pedido asignada en la ventana
	 * @return bool, TRUE: asignación correcta, FALSE: algún error
	 */
	function asignar_albaran($albaran, $cliente, $seccion, $articulo, $cantidad, $idpedido = null)
	{
		// Obtiene todas las líneas que se pueden servir
		$this->db->flush_cache();
		$this->db->select("{$this->_tablename}.nIdLinea, {$this->_tablename}.nIdSeccion, {$this->_tablename}.nIdLibro, {$this->_tablename}.nIdPedido")
		->select("{$this->_tablename}.fPrecio, {$this->_tablename}.fIVA, {$this->_tablename}.fRecargo, {$this->_tablename}.fDescuento, {$this->_tablename}.nCantidad, {$this->_tablename}.nCantidadServida")
		->select("{$this->_tablename}.cRefInterna, {$this->_tablename}.cRefCliente")
		->from("{$this->_tablename}")
		->join('Doc_PedidosCliente', "Doc_PedidosCliente.nIdPedido = {$this->_tablename}.nIdPedido");
	
		// Si se ha indicado un pedido, se preselecciona
		if (isset($idpedido))
		{
			$this->db->where("{$this->_tablename}.nIdLinea = {$idpedido}");			
		}
		else 
		{		
			$this->db->where("Doc_PedidosCliente.nIdCliente = {$cliente}")
			->where("{$this->_tablename}.nIdLibro = {$articulo}")
			->where("{$this->_tablename}.nIdSeccion = {$seccion}")
			->where("{$this->_tablename}.nIdEstado IN (" . PEDIDO_CLIENTE_ESTADOS_PENDIENTE . ')');			
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		#var_dump($cantidad);
		#var_dump($idpedido);
		#echo '<pre>'; var_dump($data); echo '</pre>';

		// Procesa las líneas
		foreach($data as $reg)
		{
			$ct = min($reg['nCantidad'], $cantidad);
			#var_dump($ct);
			if ($ct > 0)
			{
				if ($ct < $reg['nCantidad'])
				{
					#echo 'Creando nueva línea<br/>';
					// No se sirve todo, por lo tanto creamos una nueva línea
					$reg2['nIdSeccion'] = $reg['nIdSeccion'];
					$reg2['nIdLibro'] = $reg['nIdLibro'];
					$reg2['nIdPedido'] = $reg['nIdPedido'];
					$reg2['fPrecio'] = $reg['fPrecio'];
					$reg2['fIVA'] = $reg['fIVA'];
					$reg2['fRecargo'] = $reg['fRecargo'];
					$reg2['fDescuento'] = $reg['fDescuento'];
					$reg2['cRefCliente'] = $reg['cRefCliente'];
					$reg2['cRefInterna'] = $reg['cRefInterna'];
					$reg2['nCantidad'] = $reg['nCantidad'] - $ct;
					$reg2['nCantidadServida'] = 0;
					if (!$this->insert($reg2))
					{
						return FALSE;
					}
				}
				$reg['nCantidad'] = $ct;
				$reg['nCantidadServida'] = $ct;
				$reg['nIdEstado'] = 4;
				$reg['nIdAlbaranSal'] = $albaran;
				#echo "Actualizando {$ct} cantidad<br/>";
				if (!$this->update($reg['nIdLinea'], $reg))
				{
					return FALSE;
				}
			}
			$cantidad -= $ct;
			#var_dump($cantidad);
			if ($cantidad == 0) break;
		}
		// Si había un pedido preseleccionado, pero la cantidad no es suficiente,
		// asigna otros pedidos
		if ($cantidad > 0 && isset($idpedido))
		{
			#echo "llamando sin id<br/>";
			return $this->asignar_albaran($albaran, $cliente, $seccion, $articulo, $cantidad);
		}
		#die();

		return TRUE;
	}

	/**
	 * Líneas de pedido pendientes
	 * @param $id Id del pedido
	 * @return array Líneas del pedido
	 */
	function pendientes_pedido($id)
	{
		return $this->get(null, null, null, null, "Doc_LineasPedidoCliente.nIdPedido = {$id} AND Doc_LineasPedidoCliente.nIdEstado IN (" . PEDIDO_CLIENTE_ESTADOS_PENDIENTE . ')');
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Fondo.fPrecioCompra fCoste, Cat_Fondo.fPrecio fPrecio2');
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
			if (isset($data['fPrecio2']) && $data['fIVA'])
			{
				$data['fPVP2'] = format_add_iva($data['fPrecio2'], $data['fIVA']);
			}
			return TRUE;
		}
		return FALSE;
	}

}
