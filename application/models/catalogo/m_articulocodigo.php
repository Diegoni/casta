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
 * Codugos de un artículo
 *
 */
class M_articulocodigo extends MY_Model
{
	/**
	 * Constructor
	 * @return M_articulocodigo
	 */
	function __construct()
	{
		$data_model = array(
			'nIdLibro'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),	
			'nCodigo' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);

		parent::__construct('Cat_Codigos_Fondo', 'nIdCodigo', 'nCodigo', 'nCodigo', $data_model, TRUE);
	}		
}

/* End of file M_articulocodigo.php */
/* Location: ./system/application/models/catalogo/M_articulocodigo.php */
