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

define('OFERTAS_DEFAULT_TIPO', 2);
/**
 * Ofertas
 *
 */
class M_oferta extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_oferta
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'fValor'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
			#'bPrecioFijo'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'tDescripcion'	=> array(),
			'nIdTipoOferta' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO,'catalogo/tipooferta/search')),
			'bVerWeb'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
		);

		parent::__construct('Gen_Ofertas', 'nIdOferta', 'cDescripcion', 'cDescripcion', $data_model, TRUE);	
		$this->_cache = TRUE;
	}
}

/* End of file M_oferta.php */
/* Location: ./system/application/models/catalogo/M_oferta.php */