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
 * Formatos de etiqueta
 *
 */
class M_etiquetaformato extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_etiquetaformato
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'tFormato'		=> array(), 
		);

		parent::__construct('Ext_EtiquetasFormatos', 'nIdFormato', 'cDescripcion', 'cDescripcion', $data_model, TRUE);
		
		$this->_cache = TRUE;		
	}
}

/* End of file M_etiquetaformato.php */
/* Location: ./system/application/models/etiquetas/M_etiquetaformato.php */