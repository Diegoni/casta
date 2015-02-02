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
 * Tipos de autor
 *
 */
class Tipoautor extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('catalogo.tipoautor', 'catalogo/M_tipoautor', TRUE, null, 'Tipos Autor');
	}

}

/* End of file Tipoautor.php */
/* Location: ./system/application/controllers/catalogo/Tipoautor.php */
