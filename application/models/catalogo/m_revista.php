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
 * Datos extra revista
 *
 */
class M_revista extends MY_Model
{
	/**
	 * Costructor
	 * @return M_revista
	 */
	function __construct()
	{
		$data_model = array(
			'nIdTipoPeriodoRevista'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/periodorevista/search', 'cTipoPeriodoRevista')),
			'nIdPeriodo'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/periodosucripcion/search', 'cPeriodoSuscripcion')),
			'nIdTipoSuscripcion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/tiposuscripcionrevista/search', 'cTipoSuscripcionRevista')),
			'nEjemplares'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'bRenovable' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'dUltimoCambioPrecio' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
		);

		parent::__construct('Cat_Revistas', 'nIdLibro', 'nIdLibro', 'nIdLibro', $data_model, TRUE);
		$this->_cache = TRUE;
	}
	/**
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_PeriodosSuscripcion.cDescripcion cPeriodoSuscripcion');
			$this->db->join('Cat_PeriodosSuscripcion', "Cat_PeriodosSuscripcion.nIdPeriodo = {$this->_tablename}.nIdPeriodo", 'left');
			$this->db->select('Cat_TiposSuscripcionRevista.cDescripcion cTipoSuscripcionRevista');
			$this->db->join('Cat_TiposSuscripcionRevista', "Cat_TiposSuscripcionRevista.nIdTipoSuscripcion = {$this->_tablename}.nIdTipoSuscripcion", 'left');
			$this->db->select('Cat_TiposPeriodoRevista.cDescripcion cTipoPeriodoRevista');
			$this->db->join('Cat_TiposPeriodoRevista', "Cat_TiposPeriodoRevista.nIdTipoPeriodoRevista = {$this->_tablename}.nIdTipoPeriodoRevista", 'left');
				
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_revista.php */
/* Location: ./system/application/models/catalogo/M_revista.php */