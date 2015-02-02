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
 * Revistas
 *
 */
class Revista extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Revista
	 */
	function __construct()
	{
		parent::__construct('catalogo.revista', 'catalogo/M_revista', TRUE, null, 'Revistas');
	}
}

/* End of file revista.php */
/* Location: ./system/application/controllers/catalogo/revista.php */
