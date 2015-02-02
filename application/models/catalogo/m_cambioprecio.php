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
class M_Cambioprecio extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Cambioprecio
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array('nIdLibro' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT,
			DATA_MODEL_REQUIRED => TRUE,
			DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO,
				'catalogo/articulo/search')),
			'fPrecioAntiguo' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
			'fPrecioNuevo' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
			'dCambio' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
			'cCUser' => array());

		parent::__construct('Cat_CambiosPrecio', 'nIdCambioPrecio', 'nIdCambioPrecio', 'dCambio', $data_model, TRUE);
		$this->_cache = TRUE;
	}

}

/* End of file M_Cambioprecio.php */
/* Location: ./system/application/models/catalogo/M_Cambioprecio.php */
