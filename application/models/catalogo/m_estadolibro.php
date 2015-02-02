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

define('ESTADO_ARTICULO_A_LA_VENTA', 3);
define('ESTADO_ARTICULO_AGOTADO_EN_PROVEEDOR', 6);
define('ESTADO_ARTICULO_AGOTADO_EN_TIENDA', 5);
define('ESTADO_ARTICULO_CAMBIO_EDICION', 11);
define('ESTADO_ARTICULO_DESCATALOGADO', 4);
define('ESTADO_ARTICULO_EN_ALMACEN', 2);
define('ESTADO_ARTICULO_EN_REEDICION', 7);
define('ESTADO_ARTICULO_EN_REIMPRESION', 8);
define('ESTADO_ARTICULO_GENERICO', 16);
define('ESTADO_ARTICULO_NO_LOCALIZADO', 13);
define('ESTADO_ARTICULO_NO_PUBLICADO', 14);
define('ESTADO_ARTICULO_NO_VENAL', 12);
define('ESTADO_ARTICULO_OBSEQUIO', 15);
define('ESTADO_ARTICULO_PEDIDO_AL_PROVEEDOR', 1);
define('ESTADO_ARTICULO_POD', 17);
define('ESTADO_ARTICULO_RETIRADO STOCK', 9);
define('ESTADO_ARTICULO_TEMPORAL', 10);

/**
 * Estados de un libro
 *
 */
class M_estadolibro extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_estadolibro
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'bProtegido'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN)
		);

		parent::__construct('Cat_EstadosLibro', 'nIdEstado', 'cDescripcion', 'cDescripcion', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_estadolibro.php */
/* Location: ./system/application/models/catalogo/M_estadolibro.php */