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
 * Formatos de etiqueta
 *
 */
class Etiquetaformato  extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Etiquetaformato
	 */
	function __construct()
	{
		parent::__construct('etiquetas.etiquetaformato', 'etiquetas/M_etiquetaformato', TRUE, 'etiquetas/formatos.js', 'Formatos de Etiquetas');
	}
}

/* End of file Etiquetaformato.php */
/* Location: ./system/application/controllers/etiquetas/Etiquetaformato.php */