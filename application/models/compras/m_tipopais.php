<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tipos de artículo por países
 *
 */
class M_tipopais extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_tipopais
	 */
	function __construct()
	{
		$data_model = array(
			'nIdTipo'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/tipolibro/search')),
			'nIdPais'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'perfiles/pais/search')),
			'fIVA'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
			'fRecargo'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
		
		);

		parent::__construct('Cat_TipoPais', 'nIdTipoPais', 'nIdPais', 'nIdPais', $data_model, TRUE);	
		$this->_cache = TRUE;
	}
}

/* End of file M_tipopais.php */
/* Location: ./system/application/models/compras/M_tipopais.php */