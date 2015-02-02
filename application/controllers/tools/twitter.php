<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	tools
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Lector de Twitter
 * @author alexl
 *
 */
class Twitter extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Pedidos
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Arregla de los precios de coste
	 */
	function follow($tweet = null, $count = null)
	{
		$tweet = isset($tweet)?$tweet:$this->input->get_post('tweet');
		$count = isset($count)?$count:$this->input->get_post('count');
		
		if($tweet != '')
		{
			$datos['tweet'] = $tweet;
			$datos['count'] = isset($count)?$count:10;
			$this->load->view('tools/tw_follow', $datos);			
			return;
		}
	}
}
/* End of file twitter.php */
/* Location: ./system/application/controllers/tools/twitter.php */

