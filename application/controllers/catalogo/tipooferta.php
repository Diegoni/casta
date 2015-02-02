<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tipos de oferta
 *
 */
class Tipooferta extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Tipooferta
	 */
	function __construct()
	{
		parent::__construct('catalogo.tipooferta', 'catalogo/M_tipooferta', TRUE, null, 'Tipos de oferta');
	}
}

/* End of file Tipooferta.php */
/* Location: ./system/application/controllers/catalogo/Tipooferta.php */
