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

define('DEFAULT_CLIENTE_STATUS', 	1);
define('DEFAULT_CLIENTE_TARIFA', 	1);
define('DEFAULT_CLIENTE_GRUPOIVA', 	1);

define('STATUS_CLIENTE_ACTIVADO', 	1);
define('STATUS_CLIENTE_BLOQUEADO', 	2);
define('STATUS_CLIENTE_BAJA', 		3);

/**
 * Clientes
 *
 */
class M_Cliente extends MY_Model
{
	/**
	 * Añadir el email a las búsquedas
	 * @var bool
	 */
	public $_addemail = FALSE;

	/**
	 * Constructor
	 * @return M_cliente
	 */
	function __construct()
	{
		/*
		$data_model = array(
			'cNombre' 			=> array(), 
			'cApellido'			=> array(),
			'cEmpresa'			=> array(DATA_MODEL_DEFAULT => TRUE),
			'cReferencia'		=> array(DATA_MODEL_DEFAULT => TRUE),
			'cCuil'				=> array(DUPLICADO			=> TRUE),
			'bCredito'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'nIdCuenta' 		=> array(/*DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT*//*),
		/* 
			'cPass' 			=> array(),
			'bRecargo'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bExamen'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN), 
			'cNIF' 				=> array(),
			'nIdTipoCliente' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/tipocliente/search')), 
			'nIdTratamiento' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/tratamiento/search')),
			'nIdGrupoCliente' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/grupocliente/search')),
			'bNoEmail'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bNoCarta'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN), 
			'bExentoIVA'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN), 
			'cRandom' 			=> array(),
			'nIdEstado' 		=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_CLIENTE_STATUS, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/estadocliente/search')), 
			'nIdTipoTarifa' 	=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_CLIENTE_TARIFA, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/tipotarifa/search')),
			'nIdGrupoIva' 		=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_CLIENTE_GRUPOIVA, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/grupoiva/search')),
			'nIdIdioma' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/idioma/search')), 
			'fImporte1'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'fImporte2'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),

			'tNotas'			=> array(),
			'cIdioma'			=> array(),
			'nIdWeb'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);
		 */ 
		 
		 $data_model = array(
			'cNombre' 			=> array(), 
			'cApellido'			=> array(DATA_MODEL_REQUIRED => TRUE),
			'cEmpresa'			=> array(DATA_MODEL_DEFAULT => TRUE),
			'cReferencia'		=> array(DATA_MODEL_DEFAULT => TRUE),
			'cCuil'				=> array(DATA_MODEL_DUPLICATE => TRUE),
			'bCredito'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'nIdCuenta' 		=> array(/*DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT*/),
			'cPass' 			=> array(),
			'bRecargo'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bExamen'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN), 
			'cNIF' 				=> array(),
			'nIdTipoCliente' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/tipocliente/search')), 
			'nIdTratamiento' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/tratamiento/search')),
			'nIdGrupoCliente' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/grupocliente/search')),
			'bNoEmail'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bNoCarta'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN), 
			'bExentoIVA'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN), 
			'cRandom' 			=> array(),
			'nIdEstado' 		=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_CLIENTE_STATUS, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/estadocliente/search')), 
			'nIdTipoTarifa' 	=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_CLIENTE_TARIFA, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/tipotarifa/search')),
			'nIdGrupoIva' 		=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_CLIENTE_GRUPOIVA, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/grupoiva/search')),
			'nIdIdioma' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/idioma/search')), 
			'fImporte1'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'fImporte2'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),

			'tNotas'			=> array(),
			'cIdioma'			=> array(),
			'nIdWeb'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);

		parent::__construct(
					'Cli_Clientes', 
					'nIdCliente', 
					'cEmpresa, cNombre, cApellido', 
					array('cNombre', 'cApellido', 'cEmpresa'), 
					$data_model
				);
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */