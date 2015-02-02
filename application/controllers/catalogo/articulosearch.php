<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

require_once(APPPATH . 'controllers' . DIRECTORY_SEPARATOR . 'catalogo' . DIRECTORY_SEPARATOR . 'articulo.php');

/**
 * Búsqueda Artículos
 *
 */
class ArticuloSearch extends Articulo
{
	/**
	 * Constructor
	 *
	 * @return ArticuloSearch
	 */
	function __construct()
	{
		parent::__construct('catalogo/M_articulosearch');
	}

	private function sort($sort)
	{
		$alias = array(
			'cTitulo' 	=> 'cTitulo', 
			'cAutores' 	=> 'cAutores', 
			'cISBN' 	=> 'cISBNBase', 
			'cISBNBase' => 'cISBNBase',
			'nIdLibro'	=> 'id',
			'id'		=> 'id');

		return  (isset($alias[$sort])?$alias[$sort]:null);
	}

	function query($start = null, $limit = null, $sort = null, $dir = null, $where = null, $query = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		$query 	= isset($query)?$query:$this->input->get_post('query');
		$where 	= isset($where)?$where:$this->input->get_post('where');

		if (!isset($query) || $query == '')
		{
			parse_str($where, $fields);
			if (isset($fields) && isset($fields['query'])) $query = $fields['query'];
		}
		if (isset($query) || $query != '')
		{
			$this->load->library('Sphinx');
			$data = $this->sphinx->search($query, $start, $limit, $this->sort($sort), $dir);
			if ($data === FALSE)
			{
				$this->out->error($this->sphinx->get_error());
			}
			#echo '<pre>'; print_r($data); echo '</pre>';
			$ids = array();
			$articulos = array();
			$articulos2 = array();
			if (isset($data['matches']))
			{
				foreach($data['matches'] as $id => $v)
				{
					$ids[] = $id;
				}

				if (count($ids) > 0)
				{
					$where = 'Cat_Fondo.nIdLibro IN ( '. implode(',', $ids) . ')';
					$this->load->model('catalogo/m_articulosearch');
					$articulos = $this->m_articulosearch->get(null, null, null, null, $where);
					foreach ($articulos as $art)
					{
						$k = array_search($art['nIdLibro'], $ids);
						$ids[$k] = $art;
					}
					#echo '<pre>'; print_r($articulos); echo '</pre>';
				}
			}
			$this->out->data($ids, $data['total']);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
}

/* End of file articulosearch.php */
/* Location: ./system/application/controllers/catalogo/articulosearch.php */
