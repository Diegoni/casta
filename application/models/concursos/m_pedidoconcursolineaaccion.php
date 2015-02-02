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
define('ACCION_OBRA_VISTA', 			1);
define('ACCION_DESCATALOGADO_VISTO',	2);
define('ACCION_ANTIGUO_VISTO',			3);

/**
 * Acciones y notas sobre cada línea de pedido
 *
 */
class M_pedidoconcursolineaaccion extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_pedidoconcursolineaaccion
	 */
	function __construct()
	{
		$data_model = array(
			'nIdLineaPedidoConcurso'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdTipo' 					=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'cDescripcion'				=> array(), 
		);
		
		parent::__construct('Ext_LineasPedidoConcursoAcciones', 'nIdAccion', 'nIdAccion', 'nIdAccion', $data_model, TRUE);
	}
}

/* End of file M_sala.php */
/* Location: ./system/application/models/concursos/M_sala.php */