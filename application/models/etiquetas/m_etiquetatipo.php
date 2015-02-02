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
 * Tipos de etiqueta
 *
 */
class M_etiquetatipo extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_etiquetatipo
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'fTop'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fLeft'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'fWidth'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fHeight'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'nRows'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'nColumns'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'fHorizontal'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fVertical'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
		);

		parent::__construct('Ext_EtiquetasTipos', 'nIdTipo', 'cDescripcion', 'cDescripcion', $data_model, TRUE);
		
		$this->_cache = TRUE;		
	}
}

/* End of file M_etiquetatipo.php */
/* Location: ./system/application/models/etiquetas/M_etiquetatipo.php */