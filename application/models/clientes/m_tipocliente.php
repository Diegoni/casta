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
 * Tipos de cliente
 *
 */
class M_Tipocliente extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_Contacto
	 */
	
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'nCuenta'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdModoCobro' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/modocobro/search')), 
			'nIdTipoTarifa' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/tipotarifa/search')),
			'bProtegido'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN)
		);

		parent::__construct(
					'Cli_TiposCliente', 
					'nIdTipoCliente', 
					'cDescripcion', 
					'cDescripcion', 
					$data_model
				);	
	}
	
}

/* End of file M_tipocliente.php */
/* Location: ./system/application/models/clientes/M_tipocliente.php */