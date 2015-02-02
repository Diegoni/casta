<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Estado de las líneas de pedido por defecto
 * @var int
 */
define('DEFAULT_ESTADO_ARTICULO_CONCURSO', 1);
/**
 * Artículos Concurso
 *
 */
class M_articuloconcurso extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Constructor
	 * @return M_Pedido
	 */
	function __construct()
	{
		$data_model = array(
			'cISBN' 				=> array(),
			'cISBNBase10' 			=> array(),
			'cEAN' 					=> array(),
			'cTitulo' 				=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_DEFAULT => TRUE),
			'cAutores' 				=> array(),
			'nIdEditorial' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concurso/editorial/search')), 
			'cEdicion' 				=> array(),
			'fPrecio' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
			'nIdPedido' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/pedido/search')),
			'nIdEditorial' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/editorial/search')),
			'nIdProveedor' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/proveedor/search')),
			'nIdEstado' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => DEFAULT_ESTADO_ARTICULO_CONCURSO, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/estado/search')),
		);

		//CI
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.concursos.database');
		parent::__construct($this->prefix . 'Diba_LineasPedido', 'nIdLibro', 'nIdLibro', 'cTitulo', $data_model);
	}
}

/* End of file M_articuloconcurso.php */
/* Location: ./system/application/models/concursos/M_articuloconcurso.php */