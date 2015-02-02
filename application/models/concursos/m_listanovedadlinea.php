<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	mailing
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Articulos en una lista de novedad
 *
 */
class M_Listanovedadlinea extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Listanovedadlinea
	 */
	function __construct()
	{
		$data_model = array(
			'nIdLista'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concurso/listanovedad/search')),
			'nIdLibro'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
			'nIdSeccion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search')),		
		);

		parent::__construct('Diba_LibrosNovedades', 'nIdLinea', 'nIdLinea DESC', 'nIdLinea', $data_model, TRUE);
		$this->_alias = array(
			'fPVP' 		=> array('Cat_Fondo.fPrecio', DATA_MODEL_TYPE_INT),
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.cISBN, Cat_Editoriales.cNombre cEditorial');
			$this->db->select('Cat_Fondo.fPrecio, Cat_Tipos.fIVA');
			$this->db->select('Cat_Secciones.cNombre cSeccion');			
			$this->db->join('Cat_Fondo', "Cat_Fondo.nIdLibro = {$this->_tablename}.nIdLibro");
			$this->db->join('Cat_Tipos', "Cat_Tipos.nIdTipo = Cat_Fondo.nIdTipo");
			$this->db->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial', 'left');
			$this->db->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = {$this->_tablename}.nIdSeccion", 'left');
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
			if (isset($data['nIdLibro']))
			{
				$data['fPVP'] = format_add_iva($data['fPrecio'], $data['fIVA']);
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert($data)
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			if (isset($data['nIdSeccion']) && isset($data['nIdLibro']))
			{
				# Mueve el libro
				$this->obj->load->model('catalogo/m_movimiento');
				$id_n = $this->obj->m_movimiento->mover($data['nIdLibro'], 
					$data['nIdSeccion'], 
					$this->config->item('bp.servnov.idseccion'), 
					1);
				if ($id_n < 0)
				{
					$this->_set_error_message($this->obj->m_movimiento->error_message());
					return FALSE;
				}
			}
		}
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			if (isset($data['nIdSeccion']) || isset($data['nIdLibro']))
			{
				$old = $this->load($id);
				# Devuelve el libro
				$this->obj->load->model('catalogo/m_movimiento');
				$id_n = $this->obj->m_movimiento->mover($old['nIdLibro'], 
					$this->config->item('bp.servnov.idseccion'), 
					$old['nIdSeccion'], 
					1);
				if ($id_n < 0)
				{
					$this->_set_error_message($this->obj->m_movimiento->error_message());
					return FALSE;
				}

				# Mueve lo nuevo
				$id_n = $this->obj->m_movimiento->mover(
					isset($data['nIdLibro'])?$data['nIdLibro']:$old['nIdLibro'], 
					isset($data['nIdSeccion'])?$data['nIdSeccion']:$old['nIdSeccion'], 
					$this->config->item('bp.servnov.idseccion'), 
					1);
				if ($id_n < 0)
				{
					$this->_set_error_message($this->obj->m_movimiento->error_message());
					return FALSE;
				}
			}
		}
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 */
	protected function onBeforeDelete($id) 
	{
		$old = $this->load($id);
		# Devuelve el libro
		if (isset($old['nIdSeccion']))
		{
			$this->obj->load->model('catalogo/m_movimiento');
			$id_n = $this->obj->m_movimiento->mover($old['nIdLibro'], 
				$this->config->item('bp.servnov.idseccion'), 
				$old['nIdSeccion'], 
				1);
			if ($id_n < 0)
			{
				$this->_set_error_message($this->obj->m_movimiento->error_message());
				return FALSE;
			}
		}
		return parent::onBeforeDelete($id);
	}
}

/* End of file M_listanovedadlinea.php */
/* Location: ./system/application/models/mailing/M_listanovedadlinea.php */
