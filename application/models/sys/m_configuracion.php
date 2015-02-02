<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Configuración del sistema/terminal/usuario
 *
 */
class M_Configuracion extends MY_Model
{
	/**
	 * Constructoir
	 * @return unknown_type
	 */
	function __construct()
	{
		$data_model = array(
			'cEntrada'		=> array(DATA_MODEL_REQUIRED => TRUE),
			'tValor'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_DEFAULT => TRUE),
		);

		parent::__construct('App_Configuracion', 'nIdConfiguracion', 'cEntrada', 'cEntrada', $data_model, TRUE);
		$this->_cache = TRUE;
	}
}

/* End of file M_Configuracion.php */
/* Location: ./system/application/models/sys/M_Configuracion.php */
