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

define('CONCURSOS_ESTADO_LINEA_EN_PROCESO', 		1);
define('CONCURSOS_ESTADO_LINEA_A_PEDIR', 			22);
define('CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR', 5);
define('CONCURSOS_ESTADO_LINEA_RECIBIDO_PROVEEDOR', 2);
define('CONCURSOS_ESTADO_LINEA_AGOTADOS', 			11);
define('CONCURSOS_ESTADO_LINEA_DESCATALOGADO', 		6);
define('CONCURSOS_ESTADO_LINEA_EN_REIMPRESION', 	23);
define('CONCURSOS_ESTADO_LINEA_ALTERNATIVA', 		10);
define('CONCURSOS_ESTADO_LINEA_CAMBIADO_POR_OTRO', 	8);
define('CONCURSOS_ESTADO_LINEA_CAMBIO_DE_EDICION', 	9);
define('CONCURSOS_ESTADO_LINEA_CATALOGADO', 		17);
define('CONCURSOS_ESTADO_LINEA_DESCARTADO', 		12);
define('CONCURSOS_ESTADO_LINEA_DEVUELTO_PROVEEDOR', 15);
define('CONCURSOS_ESTADO_LINEA_ELIMINADO', 			14);
define('CONCURSOS_ESTADO_LINEA_EN_ALBARAN', 		3);
define('CONCURSOS_ESTADO_LINEA_EN_ALBARAN_SIN_CATALOGAR', 19);
define('CONCURSOS_ESTADO_LINEA_EN_DEVOLUCION', 		18);
define('CONCURSOS_ESTADO_LINEA_EN_FACTURA', 		4);
define('CONCURSOS_ESTADO_LINEA_EN_FACTURA_SIN_CATALOGAR', 20);
define('CONCURSOS_ESTADO_LINEA_FUERA_DEL_PRESUPUESTO', 21);
define('CONCURSOS_ESTADO_LINEA_NO_DISPONIBLE', 		7);
define('CONCURSOS_ESTADO_LINEA_NO_VENAL', 			16);
define('CONCURSOS_ESTADO_LINEA_REPETIDO', 			13);

/**
 * Estados  Concurso
 *
 */
class M_estadolineaconcurso extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_estadolineaconcurso
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'nIdGrupoEstado' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/grupoestadolineaconcurso/search')),
			'bSuma' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
		);
		
		parent::__construct('Ext_EstadosConcurso', 'nIdEstado', 'cDescripcion', 'cDescripcion', $data_model);	
	}
}

/* End of file M_estadolineaconcurso.php */
/* Location: ./system/application/models/concursos/M_estadolineaconcurso.php */