<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	libraries
 * @category	core
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Grestión de multidominios
 * @author alexl
 *
 */
class Domains {

	/**
	 * Servidores
	 * @var string
	 */
	private $servers;

	/**
	 * Dominio base
	 * @var string
	 */
	private $base;

	/**
	 * Apuntado de la posicion del URL actual
	 * @var int
	 */
	private $position;

	/**
	 * Constructor
	 * @return Out
	 */
	function __construct()
	{
		$CI =& get_instance();
		$this->servers = $CI->config->item('bp.servers');
		$this->base = $CI->config->item('base_url');
		$this->position = time() % count($this->servers);
		log_message('debug', 'Domains Class Initialised via '.get_class($CI));
	}

	private function next()
	{
		$domain = $this->servers[$this->position] . '/';
		$this->position = ($this->position+1) % count($this->servers);
		return $domain;
	}

	function url($url)
	{
		if (count($this->servers) > 1)
		{
			$domain = $this->next();
			return str_replace($this->base, $domain, $url);
		}
		return $url;
	}

	function domains()
	{
		return (is_array($this->servers))?implode(';', $this->servers):'';
	}
}

/* End of file domains.php */
/* Location: ./system/libraries/domains.php */