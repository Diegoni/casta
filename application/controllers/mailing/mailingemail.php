<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	mailing
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Emails de un mailing
 * @author alexl
 *
 */
class Mailingemail extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()	
	{
		parent::__construct('mailing.mailingemail', 'mailing/M_mailingemail', true, null, 'Emails');
	}
}

/* End of file Mailingemail.php */
/* Location: ./system/application/controllers/mailing/Mailingemail.php */
