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

class Template extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Template
	 */
	function Template()
	{
		parent::__construct('generico.template', 'generico/M_template', true, null, 'Plantillas');
	}
}

/* End of file template.php */
/* Location: ./system/application/controllers/generico/template.php */