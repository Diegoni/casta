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

/**
 * Coleccion
 *
 */
class M_coleccion extends MY_Model
{
	/**
	 * Costructor
	 * @return M_coleccion
	 */
	function __construct()
	{
		$data_model = array(
			'cNombre'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		//'cCodigo'		=> array(),
			'nIdEditorial'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/editorial/search', 'cEditorial')),
		);

		parent::__construct('Cat_Colecciones', 'nIdColeccion', 'cNombre', 'cNombre', $data_model, TRUE);
		$this->_cache = TRUE;
	}
	
	/**
	 * Unificador 
	 * @param int $id1 Id del destino
	 * @param int $id2 Id del repetido
	 * @return bool, TRUE: correcto, FALSE: incorrecto
	 */
	function unificar($id1, $id2)
	{
		// TRANS
		$this->db->trans_begin();

		// Colección Artículo 1
		$update = array('nIdColeccion' => (int)$id1);
		$this->db->flush_cache();
		$this->db->where("nIdColeccion = {$id2}")
		->update('Cat_Fondo', $update);
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		// Colección Artículo 2
		$update = array('nIdColeccion2' => (int)$id1);
		$this->db->flush_cache();
		$this->db->where("nIdColeccion2 = {$id2}")
		->update('Cat_Fondo', $update);
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		// Colección Artículo 3
		$update = array('nIdColeccion3' => (int)$id1);
		$this->db->flush_cache();
		$this->db->where("nIdColeccion3 = {$id2}")
		->update('Cat_Fondo', $update);
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		
		// Borrado
		$res = $this->db->flush_cache();
		$this->db->where("nIdColeccion={$id2}")
		->delete('Cat_Colecciones');
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		// Limpieza de caches
		$ci = get_instance();

		$ci->load->model('catalogo/m_articuloautor');
		$ci->m_articuloautor->clear_cache();

		$this->clear_cache();

		// COMMIT
		$this->db->trans_commit();
		return TRUE;
	}
	
	/**
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			#echo '<pre>'; var_dump($where); echo '</pre>'; die();
			$this->db->select('Cat_Editoriales.cNombre cEditorial, Cat_Editoriales.cNombreCorto');
			$this->db->join('Cat_Editoriales', "Cat_Editoriales.nIdEditorial = {$this->_tablename}.nIdEditorial", 'left');
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
			if (!isset($data['cEditorial']))
			{
				$data['cEditorial'] = isset($data['cNombreCorte'])?$data['cNombreCorte']:null;
			}
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_coleccion.php */
/* Location: ./system/application/models/catalogo/M_coleccion.php */