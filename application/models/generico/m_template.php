<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	generico
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Plantillas de mensajes
 *
 */
class M_Template extends MY_Model
{
	function M_Template()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'cTipo'			=> array(DATA_MODEL_REQUIRED => TRUE),
			'bIsHTML'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'tTexto'		=> array(DATA_MODEL_REQUIRED => TRUE),
		);
		
		parent::__construct('Ext_Plantillas', 'nIdPlantilla', 'cDescripcion', 'cDescripcion', $data_model, TRUE);
		$this->_cache = TRUE;
	}	
}

/* End of file M_template.php */
/* Location: ./system/application/models/generico/M_template.php */
