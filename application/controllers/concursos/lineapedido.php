<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * LineasPedidos
 *
 */
class LineaPedido extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return LineaPedido
	 */
	function __construct()
	{
		parent::__construct('concursos.lineapedido', 'concursos/M_lineapedido', TRUE, null, 'Líneas de pedido');
	}
}

/* End of file LineaPedido.php */
/* Location: ./system/application/controllers/concursos/LineaPedido.php */
