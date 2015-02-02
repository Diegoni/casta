<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	suscripciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Grupos de Avisos 
 *
 */
class GrupoAviso extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return GrupoAviso
	 */
	function __construct()
	{
		parent::__construct('suscripciones.grupoaviso', 'suscripciones/M_grupoaviso', TRUE, null, 'Campañas');
	}
}	

/* End of file GrupoAviso.php */
/* Location: ./system/application/controllers/suscripciones/GrupoAviso.php */