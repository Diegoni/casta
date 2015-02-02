<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	stocks
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Muestra de stock
 *
 */
class M_muestra extends MY_Model
{
	/**
	 * Costructor
	 * @return M_muestra
	 */
	function __construct()
	{
		parent::__construct();
	}

	private function _base($id, $unidades)
	{
		$this->db->flush_cache();
		$this->db->select("Cat_Fondo.nIdLibro, Cat_Fondo.cISBN, Cat_Fondo.cTitulo, Cat_Fondo.fPrecio")
		->select('Cat_Secciones_Libros.nStockFirme, Cat_Secciones_Libros.nStockDeposito')
		->from('Cat_Secciones_Libros')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Cat_Secciones_Libros.nIdLibro')
		->where("Cat_Secciones_Libros.nIdSeccion = {$id}")
		->where('Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito > 0')
		->limit($unidades);
	}

	/**
	 * Muestra de stocks ordenadas por cantidades
	 * @param int $id Id de la sección
	 * @param int $task 0: Directo, 1: Como tareas
	 * @return array
	 */
	function cantidades($id, $unidades)
	{
		$this->_base($id, $unidades);
		$this->db->order_by('Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito', 'DESC');
		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}

	/**
	 * Muestra de stocks ordenadas por cantidades
	 * @param int $id Id de la sección
	 * @param int $task 0: Directo, 1: Como tareas
	 * @return array
	 */
	function precio($id, $unidades)
	{
		$this->_base($id, $unidades);
		$this->db->order_by('fPrecio * (Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito)', 'DESC');
		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}
}

/* End of file M_muestra */
/* Location: ./system/application/models/stocks/M_muestra.php */