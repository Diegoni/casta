<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	perfiles
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Emails
 *
 */
class Email extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('perfiles.email', 'perfiles/M_Email', TRUE, null, 'Emails');
	}
}

/* End of file email.php */
/* Location: ./system/application/controllers/perfiles/email.php */