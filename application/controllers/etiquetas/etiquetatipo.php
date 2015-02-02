<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	etiquetas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tipos de etiqueta
 *
 */
class Etiquetatipo  extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Etiquetatipo
	 */
	function __construct()
	{
		parent::__construct('etiquetas.etiquetatipo', 'etiquetas/M_etiquetatipo', TRUE, null, 'Tipos de Etiquetas');
	}
}

/* End of file etiquetatipo.php */
/* Location: ./system/application/controllers/etiquetas/etiquetatipo.php */