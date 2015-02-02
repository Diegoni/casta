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
 * Tipos de Notas
 *
 */
class TipoNota extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return TipoNota
	 */
	function __construct()
	{
		parent::__construct('generico.tiponota', 'generico/M_tiponota', true, null, 'Tipos de Notas');
	}
}
/* End of file nota.php */
/* Location: ./system/application/controllers/generico/nota.php */