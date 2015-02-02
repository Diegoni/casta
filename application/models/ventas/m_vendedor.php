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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Vendedores
 *
 */
class M_Vendedor extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Vendedor
	 */
	function __construct()
	{
		$data_model = array(
			'cNombre' 		=> array(), 
			'cApellido'		=> array(),
			'nIdEstado'		=> array(DATA_MODEL_DEFAULT_VALUE => 1),
		);

		parent::__construct('Ven_Vendedores', 'nIdVendedor', 'cNombre, cApellido', array('cNombre', 'cApellido'), $data_model, TRUE);
		$this->_cache = TRUE;
	}
}
/* End of file M_Vendedor.php */
/* Location: ./system/application/models/Vendedor/M_Vendedor.php */