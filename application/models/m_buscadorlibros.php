<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Buscador de libros
 *
 */
class M_Buscadorlibros extends MY_Model
{

	private $dir_robots;

	function M_Buscadorlibros()
	{
		parent::__construct('', '');

		$this->load->plugin('SearchRobot');
		//$this->load->plugin('Books');
		//$this->load->library('Cache');

		$this->dir_robots = BASEPATH .'plugins/SearchRobots';
		//CACHE
		$id_c = 'Robots2';
		$r = apc_fetch($id_c);
		if ($r === FALSE)
		{
			$r = SearchRobot::loadAll($this->dir_robots);
			
			apc_store($id_c, new ArrayObject($r), CACHE_DAY);
		}
		else
		{
			$r = $r->getArrayCopy();
		}
		$this->robots = $r;
	}

	function get_robots()
	{
		foreach($this->robots as $r)
		{
			$rb['name'] = $r->getNombre();
			$rb['tags'] = $r->getTags();
			$rb['version'] = $r->getVersion();
			$results[] = $rb;
		}

		return $results;
	}

	function search($code = null)
	{
		set_time_limit(0);
		$code = trim($code);
		if ($code != '')
		{
			//Prepara los códigos para el buscador
			$codes_a['code'] 	= $code;
			$isbn = Books::to_isbn($code, true);
			$codes_a['isbn13'] 	= $isbn['isbn13'];
			$codes_a['isbn13-'] = str_replace('-', '', $isbn['isbn13']);
			$codes_a['isbn10'] 	= $isbn['isbn10'];
			$codes_a['isbn10-'] = str_replace('-', '', $isbn['isbn10']);
			$codes_a['ean'] 	= str_replace('-', '', $isbn['isbn13']);

			$codes_a['1'] = $codes_a['isbn13'];
			$codes_a['2'] = $codes_a['ean'];
			$codes_a['3'] = $codes_a['code'];
			$r = SearchRobot::search($codes_a, $this->robots);
			return $r;
		}
		return null;
	}
}

/* End of file M_buscadorlibros.php */
/* Location: ./system/application/models/M_buscadorlibros.php */