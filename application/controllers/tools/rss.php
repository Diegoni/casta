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
 * Lector de RSS
 * @author alexl
 *
 */
class Rss extends MY_Controller
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
	function feed($feed = null)
	{
		$feed = isset($feed)?$feed:$this->input->get_post('feed');

		if($feed != '')
		{
			if (!(strpos($feed, 'http') === 0))
			{
				$feed = base_url() . $feed;
			}
			#echo $feed; die();
			header('Content-Type: text/xml');
			$xml = file_get_contents($feed);
			$xml = str_replace('<content:encoded>', '<content>', $xml);
			$xml = str_replace('</content:encoded>', '</content>', $xml);
			$xml = str_replace('</dc:creator>', '</author>', $xml);
			echo str_replace('<dc:creator', '<author', $xml);
			return;
		}
	}
}
/* End of file rss.php */
/* Location: ./system/application/controllers/tools/rss.php */

