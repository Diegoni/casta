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
 * Grupos de cliente
 *
 */
class M_grupocliente extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_gruposcliente
	 */
	
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		);
		parent::__construct(
					'Cli_GruposCliente', 
					'nIdGrupoCliente', 
					'cDescripcion', 
					'cDescripcion',
					$data_model 
				);	
	}
}

/* End of file M_grupocliente.php */
/* Location: ./system/application/models/clientes/M_grupocliente.php */