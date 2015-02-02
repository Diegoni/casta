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
 * Notas
 *
 */
class Nota  extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Nota
	 */
	function __construct()
	{
		parent::__construct('generico.nota', 'generico/M_nota', true, null, 'Notas');
	}

}
/* End of file nota.php */
/* Location: ./system/application/controllers/generico/nota.php */