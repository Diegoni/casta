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
 * Autores de un artículo
 *
 */
class M_Articuloautor extends MY_Model
{
	/**
	 * Datos a borrar
	 * @var arrray 
	 */
	private $delete_data = null;

	/**
	 * Constructor
	 * @return M_Articuloautor
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'nIdAutor'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/autor/search', 'cAutor')),
			'nIdLibro'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
			'nIdTipoAutor'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/tipoautor/search', 'cTipoAutor')),		
		);

		parent::__construct('Cat_Autores_Libros', 'nIdAutorLibro', 'nIdAutorLibro', 'nIdAutorLibro', $data_model, TRUE);
		$this->_deleted = TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		if (parent::onAfterSelect($data, $id))
		{
			$data['cAutor'] = format_autor($data['cNombre'], $data['cApellido']);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, $where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select("Cat_Autores.cNombre, Cat_Autores.cApellido, Cat_TiposAutor.cDescripcion cTipoAutor");
			$this->db->join('Cat_Autores', 'Cat_Autores.nIdAutor = Cat_Autores_Libros.nIdAutor');
			$this->db->join('Cat_TiposAutor', 'Cat_TiposAutor.nIdTipoAutor = Cat_Autores_Libros.nIdTipoAutor', 'left');
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Crea los autores de un artículo
	 * @param int $id Id del artículo
	 * @return mixed Si TRUE: ok, sino texto: error
	 */
	protected function _crear_autores($id)
	{
		$this->obj->load->model('catalogo/m_articulo');		
		return $this->obj->m_articulo->crear_autores($id);;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterInsert($data)
	 */
	protected function onAfterInsert($id, &$data)
	{
		if (parent::onAfterInsert($id, $data))
		{
			return $this->_crear_autores($data['nIdLibro']);
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterUpdate($data)
	 */
	protected function onAfterUpdate($id, &$data)
	{
		if (parent::onAfterUpdate($id, $data))
		{
			if (isset($data['nIdLibro']) || isset($data['nIdAutor']))
			{
				$old = $this->load($id);
				if (isset($data['nIdLibro']) && ($data['nIdLibro'] != $old['nIdLibro']))
				{
					if (!$this->_crear_autores($old['nIdLibro'])) return FALSE;					
				}
			}
			return $this->_crear_autores($id);
		}
		return FALSE;
	}


	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterDelete($id)
	 */
	protected function onAfterDelete($id) 
	{
		if (parent::onAfterDelete($id))
		{
			return $this->_crear_autores($this->delete_data['nIdLibro']);
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 */
	protected function onBeforeDelete($id) 
	{
		if(parent::onBeforeDelete($id))
		{
			$this->delete_data = $this->load($id);
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_Articuloautor.php */
/* Location: ./system/application/models/catalogo/M_Articuloautor.php */
