<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	stocks
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tipos de regulación de Stock
 *
 */
class Tiporegulacion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Tiporegulacion
	 */
	function __construct()
	{
		parent::__construct('stocks.tiporegulacion', 'stocks/M_tiporegulacion', TRUE, null, 'Tipos regulación');
	}

	/**
	 * Motivos de salida
	 * @return DATA
	 */
	function salida()
	{
		$this->out->data($this->search(null, null, null, null, null, 'bSigno=0'));
	}

	/**
	 * Motivos de Entrada
	 * @return DATA
	 */
	function entrada()
	{
		$this->out->data($this->search(null, null, null, null, null, 'bSigno=1'));
	}
}

/* End of file tiporegulacion.php */
/* Location: ./system/application/controllers/stocks/tiporegulacion.php */