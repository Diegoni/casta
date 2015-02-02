<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	etiquetas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Etiquetas
 *
 */
class M_etiqueta extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_etiqueta
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_REQUIRED => TRUE), 
			'cEtiqueta'		=> array(DATA_MODEL_NO_GRID => TRUE),
			'cEtiquetaTexto' => array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_ALIAS, DATA_MODEL_READONLY => TRUE)
		);

		parent::__construct('Ext_Etiquetas', 'nIdEtiqueta', 'nIdEtiqueta DESC', 'cDescripcion', $data_model, TRUE);
		
		$this->_cache = TRUE;		
	}

	/**
	 * Devuelve los grupos de etiquetas
	 * @return array
	 */
	function grupos()
	{
		$this->db->flush_cache();
		$this->db->select('cDescripcion text, cDescripcion id')
		->from($this->_tablename)
		->group_by('cDescripcion');
		return $this->_get_results($this->db->get());
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert($data)
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			if (isset($data['cEtiqueta']) && is_array($data['cEtiqueta']))
			{
				$data['cEtiqueta'] = serialize($data['cEtiqueta']);
			}
		}
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		if (isset($data['cEtiqueta']))
			$data['cEtiquetaTexto'] = $this->utils->array_to_html(unserialize($data['cEtiqueta']));
		return parent::onAfterSelect($data, $id);
	}

}

/* End of file M_etiqueta.php */
/* Location: ./system/application/models/etiquetas/M_etiqueta.php */