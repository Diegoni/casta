<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Limpieza y preparacion del pedido del concurso
 *
 */
class M_limpieza extends MY_Model
{
	/**
	 * Costructor
	 * @return M_limpieza
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Arregla los ISBNS
	 * @param string $db Catálogo en la bbdd
	 * @return array
	 */
	function isbns($db)
	{
		$this->db->flush_cache();
		$this->db->select('nIdLibro, cISBN')
		->from("{$db}..Diba_LineasPedido")
		->where("nIdPedido > 40");

		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}

	/**
	 * Arregla las editoriales
	 * @param string $db Catálogo en la bbdd
	 * @return array
	 */
	function editoriales($db)
	{
		$this->db->flush_cache();
		$this->db->select('nIdLibro, cISBN')
		->from("{$db}..Diba_LineasPedido")
		->where('nIdEditorial IS NULL');

		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}

	function editoriales2($db)
	{
		$this->db->flush_cache();
		$this->db->select('nIdLibro, cISBN')
		->from("{$db}..Diba_LineasPedido")
		->where('nIdProveedor = 100040');

		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}

	function descuentos($db)
	{
		$this->db->flush_cache();
		$this->db->select('nIdLibro, cISBN')
		->from("{$db}..Diba_LineasPedido")
		->where('ISNULL(fDescuento, 0) <= 0');

		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
		
	}
}

/* End of file M_limpieza.php */
/* Location: ./system/application/models/concursos/M_limpieza.php */