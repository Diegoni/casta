<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	calendario
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Emails de los trabajadores
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
		parent::__construct('calendario.email', 'calendario/M_Email', true, null, 'Emails');
	}
}

/* End of file email.php */
/* Location: ./system/application/controllers/calendario/email.php */