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
 * Tipos de periodo revista
 *
 */
class Periodorevista extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Periodorevista
	 */
	function __construct()
	{
		parent::__construct('catalogo.periodorevista', 'catalogo/M_periodorevista', TRUE, null, 'Tipos de periodo revista');
	}
}

/* End of file Periodorevista.php */
/* Location: ./system/application/controllers/catalogo/Periodorevista.php */
