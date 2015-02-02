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
 * Códigos de una editorial
 *
 */
class Editorialcodigo extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Editorialcodigo
	 */
	function __construct()
	{
		parent::__construct('catalogo.editorialcodigo', 'catalogo/M_editorialcodigo', TRUE, null, 'Estados Libro');
	}

}

/* End of file Editorialcodigo.php */
/* Location: ./system/application/controllers/catalogo/Editorialcodigo.php */
