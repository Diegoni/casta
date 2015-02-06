<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	clientes
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
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

		parent::__construct('Cli_GruposCliente', 'nIdGrupoCliente', 'cDescripcion', 'cDescripcion', $data_model, TRUE);	
		$this->_cache = TRUE;
	}
}

/* End of file M_grupocliente.php */
/* Location: ./system/application/models/clientes/M_grupocliente.php */