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
 * Cambios Estados Líneas  Concurso
 *
 */
class M_cambioestado extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_cambioestado
	 */
	function __construct()
	{
		$data_model = array(
			'nIdLineaPedidoConcurso'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdEstado' 				=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);
		
		parent::__construct('Ext_CambiosEstadoLineaConcurso', 'nIdCambio', 'nIdCambio', 'nIdCambio', $data_model, TRUE);
	}
}

/* End of file M_cambioestado.php */
/* Location: ./system/application/models/concursos/M_cambioestado.php */