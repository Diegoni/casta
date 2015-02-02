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
 * @filesource
 */

/**
 * Materias
 *
 */
class M_Materia extends MY_Model
{
	/**
	 * Constructor
	 * @return M_materia
	 */
	function __construct()
	{
		$data_model = array(
				'nIdMateriaPadre' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/materia/search')),
				'cNombre' 			=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
				'cCodMateria' 		=> array(),
				'nHijos' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
				'nLibrosLocal'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
				'nLibrosTotal' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);

		parent::__construct('Cat_Materias', 'nIdMateria', 'cNombre', 'cNombre', $data_model, TRUE);
		$this->_cache = TRUE;
		$this->_deleted = TRUE;
	}

	/**
	 * Devuelve las materias de un padre
	 * @param $id ID del padre
	 * @return array
	 */
	function get_by_padre($id = null)
	{
		if ($id)
		{
			return $this->get(null, null, 'cNombre', null, 'nIdMateriaPadre = ' . $id);
		}
		else
		{
			return $this->get(null, null, 'cNombre', null, 'nIdMateriaPadre IS NULL ');
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert($data)
	 */
	protected function onAfterInsert($id, &$data)
	{
		if (parent::onAfterInsert($id, $data))
		{
			$data2['cCodMateria'] = $this->_Codigo($data['nIdMateriaPadre']) . $id;
			$this->db->where('nIdMateria=' . $id);
			$this->db->update($this->_tablename, $data2);
		}
		return TRUE;
	}

	private function _codigo($id)
	{
		if ($id > 0)
		{
			$this->db->select('cCodMateria')->from($this->_tablename)->where('nIdMateria=' . $id);
			$r = $this->db->get();
			$data = $this->_get_results($r);
			return $data[0]['cCodMateria'] . '.';
		}
		return '';
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			if (isset($data['nIdMateriaPadre']))
			{
				$data['cCodMateria'] = $this->_Codigo($data['nIdMateriaPadre']) . $id;
				// Cambia el código de todos los hijos
				$this->_update_codigo($id, $data['cCodMateria']);
			}
		}
		return TRUE;
	}

	private function _update_codigo($id, $codigo)
	{
		$hijos = $this->get_by_padre($id);
		if (count($hijos) > 0)
		{
			foreach ($hijos as $hijo)
			{
				$this->db->where('nIdMateria=' . $hijo['nIdMateria']);
				$subcodigo = $codigo . '.' . $hijo['nIdMateria'];
				$this->db->update($this->_tablename, array('cCodMateria' => $subcodigo));
				$this->_update_codigo($hijo['nIdMateria'], $subcodigo);
			}
		}
	}
}

/* End of file M_materia.php */
/* Location: ./system/application/models/catalogo/M_materia.php */
