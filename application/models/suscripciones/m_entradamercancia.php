<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	suscripciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Entrada de mercancía 
 *
 */
class M_entradamercancia extends MY_Model
{
	/**
	 * Busca los pedidos pedientes de recibir por artículo, suscripción o título
	 * Si no se indica nada devuelve todos.
	 * @param string $query Palabra de búsqueda
	 * @return array
	 */
	function pedidos($query = null)
	{
		$this->db->flush_cache();
		// Revista o ID del pedido
		$this->db->select('Doc_PedidosProveedor.nIdPedido, Doc_PedidosProveedor.nIdProveedor')
		->select('Sus_PedidosSuscripcion.nIdSuscripcion')
		->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo')
		->from('Sus_PedidosSuscripcion')
		->join('Doc_LineasPedidoProveedor', 'Sus_PedidosSuscripcion.nIdLineaPedido = Doc_LineasPedidoProveedor.nIdLinea')
		->join('Doc_PedidosProveedor', 'Doc_PedidosProveedor.nIdPedido = Doc_LineasPedidoProveedor.nIdPedido')
		->join('Sus_Suscripciones', 'Sus_Suscripciones.nIdSuscripcion = Sus_PedidosSuscripcion.nIdSuscripcion')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Sus_Suscripciones.nIdRevista')
		->where('Doc_LineasPedidoProveedor.nIdEstado IN (2, 4)');

		if (is_numeric($query))
		{
			$this->db->where("(Sus_PedidosSuscripcion.nIdSuscripcion={$query} OR Doc_PedidosProveedor.nIdPedido ={$query} OR Cat_Fondo.nIdLibro={$query})");			
		}
		else
		{
			$this->db->where($this->_create_like($query, 'Cat_Fondo.cTitulo'));	
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		
		for($i = 0; $i < count($data); $i++)
		{
			$data[$i]['id'] = $data[$i]['nIdPedido'];
			$data[$i]['text'] = sprintf($this->lang->line('entradamecancia-format'), $data[$i]['nIdPedido'], $data[$i]['nIdSuscripcion'], $data[$i]['cTitulo'], $data[$i]['nIdLibro']);
		}
		return $data;
	}	

	/**
	 * Devuelve la información de la suscripcion indicada para entrar la mercancia
	 * @param int $id Id de la suscripción
	 * @return array
	 */
	function get_data($id)
	{
		$this->db->flush_cache();
		$this->db->select('Doc_PedidosProveedor.nIdPedido, Doc_PedidosProveedor.nIdProveedor')
		->select('Sus_Suscripciones.nEntradas, Sus_Suscripciones.nFacturas, Sus_Suscripciones.bNoFacturable')
		->select('Sus_Suscripciones.nIdCliente, Sus_Suscripciones.nIdUltimaFactura')
		->select('Sus_PedidosSuscripcion.nIdSuscripcion')
		->select('Doc_LineasPedidoProveedor.nCantidad')
		->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo')
		->from('Sus_PedidosSuscripcion')
		->join('Doc_LineasPedidoProveedor', 'Sus_PedidosSuscripcion.nIdLineaPedido = Doc_LineasPedidoProveedor.nIdLinea')
		->join('Doc_PedidosProveedor', 'Doc_PedidosProveedor.nIdPedido = Doc_LineasPedidoProveedor.nIdPedido')
		->join('Sus_Suscripciones', 'Sus_Suscripciones.nIdSuscripcion = Sus_PedidosSuscripcion.nIdSuscripcion')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Sus_Suscripciones.nIdRevista')
		->join('Prv_Proveedores pv', 'pv.nIdProveedor = Doc_PedidosProveedor.nIdProveedor')
		->join('Cli_Clientes', 'Cli_Clientes.nIdCliente = Sus_Suscripciones.nIdCliente')
		->where('Doc_PedidosProveedor.nIdPedido=' . $id);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return (count($data) == 1)?$data[0]:null;
	}	
}

/* End of file M_entradamercancia.php */
/* Location: ./system/application/models/suscripciones/M_entradamercancia.php */
