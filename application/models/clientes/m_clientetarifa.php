<?php
/**
 * Casta
 *
 * Gestión de librerías
 *
 * @package		1.1
 * @subpackage	Models
 * @category	clientes
 * @author		Diego Nieto
 * @copyright	Copyright (c) 2015
 * @link		https://github.com/Diegoni/casta
 * @since		Version 1.1
 * @version		$Rev:  $
 * @filesource
 */

/**
 * Tarifas de un cliente
 *
 */
class M_clientetarifa extends MY_Model
{
	/**
	 * Constructor
	 * @return M_clientetarifa
	 */
	function __construct()
	{
		$data_model = array(
			'nIdCliente'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE),
			'nIdTipoLibro'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/tipolibro/search', 'cTipo')),
			'nIdTipoTarifa'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/tipotarifa/search', 'cTipoTarifa')),
		);

		parent::__construct(
					'Cli_Clientes_Tarifas', 
					'nIdClienteTarifa', 
					'nIdClienteTarifa', 
					'nIdClienteTarifa', 
					$data_model
				);
	}
			
}

/* End of file M_clientetarifa.php */
/* Location: ./system/application/models/clientes/M_clientetarifa.php */
