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
 * Encuadernaciones
 *
 */
class Encuadernacion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Encuadernacion
	 */
	function __construct()
	{
		parent::__construct('catalogo.encuadernacion', 'catalogo/M_encuadernacion', TRUE, null, 'Encuadernaciones');
	}

}

/* End of file Encuadernacion.php */
/* Location: ./system/application/controllers/catalogo/Encuadernacion.php */
