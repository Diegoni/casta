<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	generico
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Idiomas
 *
 */
class Idioma extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Idioma
	 */
	function __construct()
	{
		parent::__construct('generico.idioma', 'generico/M_idioma', true, null, 'Idiomas');
	}

}
/* End of file idioma.php */
/* Location: ./system/application/controllers/generico/idioma.php */