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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tarifas de un artículo
 *
 */
class M_Articulotarifa extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Articulotarifa
	 */
	function __construct()
	{
		$data_model = array(
			'nIdTipoTarifa'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/tipotarifa/search')),
			'nIdLibro'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
			'fPrecio' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
		);

		parent::__construct('Cat_Libros_Tarifas', 'nIdTarifaLibro', 'nIdTarifaLibro', 'nIdTarifaLibro', $data_model, TRUE);
	}
		
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_TiposTarifa.cDescripcion');
			$this->db->join('Cat_TiposTarifa', 'Cat_TiposTarifa.nIdTipoTarifa = Cat_Libros_Tarifas.nIdTipoTarifa');
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
			if (isset($data['nIdTipoTarifa']) && $data['nIdTipoTarifa'] == $this->config->item('ventas.tarifas.defecto'))
			{
				$this->obj->load->model('catalogo/m_articulo');
				# Evita ciclo de actualizaciones
				$this->obj->m_articulo->triggers_disable();
				if (!$this->obj->m_articulo->update($data['nIdLibro'], array('fPrecio' => $data['fPrecio'])))
				{
					$this->obj->m_articulo->triggers_enable();
					$this->_set_error_message($this->obj->m_articulo->error_message());
					return FALSE;
				}
				$this->obj->m_articulo->triggers_enable();
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
			#Cambios de tarifa por defecto
			if ($data['fPrecio'])
			{
				$tmp = $data;
				if (isset($tmp['nIdTipoTarifa']) || isset($tmp['nIdLibro']))
				{	
					$tar = $this->load($id);
					$tmp['nIdTipoTarifa'] = isset($tmp['nIdTipoTarifa'])?$tmp['nIdTipoTarifa']:$tar['nIdTipoTarifa'];
					$tmp['nIdLibro'] = isset($tmp['nIdLibro'])?$tmp['nIdLibro']:$tar['nIdLibro'];
				}
				
				if ($tmp['nIdTipoTarifa'] == $this->config->item('ventas.tarifas.defecto'))
				{
					$this->obj->load->model('catalogo/m_articulo');
					# Evita ciclo de actualizaciones
					$this->obj->m_articulo->triggers_disable();
					if (!$this->obj->m_articulo->update($tmp['nIdLibro'], array('fPrecio' => $data['fPrecio'])))
					{
						$this->obj->m_articulo->triggers_enable();
						$this->_set_error_message($this->obj->m_articulo->error_message());
						return FALSE;
					}
					$this->obj->m_articulo->triggers_enable();
				}
			}
		}
		return TRUE;
	}
}

/* End of file M_Articulotarifa.php */
/* Location: ./system/application/models/catalogo/M_Articulotarifa.php */
