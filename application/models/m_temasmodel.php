<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	models
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Temas 
 *
 */
class M_temasmodel extends MY_Model
{
	/**
	 * Nombre del campo ID del contacto de la tabla de cruce 
	 * @var string
	 */
	protected $_idrel;
	/**
	 * Costructor
	 * @return M_temasmodel
	 */
	function __construct($tablename, $id, $idrel)
	{
		$data_model = array(
			'nIdTema'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			$idrel		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
		);
		$this->_idrel = $idrel;

		parent::__construct($tablename, $id, 'd.cDescripcion', $id, $data_model);
		$this->_cache = TRUE;
	}

	/**
	 * Obtiene el listado de temas de un contacto
	 * @param int $id Id contacto
	 * @return array
	 */
	function get_list($id)
	{
		$this->db->select("d2.{$this->_id} id, d.nIdTema, d.cDescripcion")
		->from('Sus_Temas d')
		->join("{$this->_tablename} d2", "d.nIdTema = d2.nIdTema AND d2.{$this->_idrel} = {$id}", 'left')
		->order_by('d.cDescripcion');

		$r = $this->db->get();
		$temas = $this->_get_results($r);

		return $temas;
	}

	/**
	 * Añade/elimina un tema a un contacto
	 * @param int $id ID del contacto
	 * @param int $idtema ID del tema
	 * @param string $value Añadir o quitar
	 * @return unknown_type
	 */
	function add($id, $idtema, $value)
	{
		$this->db->trans_begin();
		// Borra el anterior
		$this->db->where("{$this->_idrel} = {$id} AND nIdTema = {$idtema}");
		$this->db->delete($this->_tablename);

		// Añade el nuevo
		$value = $this->_tobool($value);
		if ($value === FALSE)
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		else
		{
			if ($value == 1)
			{
				$datos = array (
					$this->_idrel 	=> $id, 
					'nIdTema'		=> $idtema
				);
				$this->insert($datos);
			}
		}
		$this->db->trans_commit();
		return TRUE;
	}
}

/* End of file M_temasmodel.php */
/* Location: ./system/application/models/M_temasmodel.php */