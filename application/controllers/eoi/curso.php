<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	pedidos cliente
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * EOI - Cursos de venta por internet
 *
 */
class Curso extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Curso
	 */
	function __construct()
	{
		parent::__construct('eoi.curso', 'eoi/M_curso', TRUE, 'eoi/curso.js', 'Cursos Internet');
	}


	/**
	 * Devuelve los títulos de un curso
	 *
	 * @param int $id Id del curso
	 * @return DATA
	 */
	function get_titulos($id = null)
	{
		$this->userauth->roleCheck(($this->auth.'.get_list'));

		$id	= isset($id)?$id:$this->input->get_post('id');		

		if (is_numeric($id))
		{
			$this->out->send($this->_get_titulo($id));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve los modos de entrega de un curso
	 *
	 * @param int $id Id del curso
	 * @return DATA
	 */
	function get_entregas($id = null)
	{
		$this->userauth->roleCheck(($this->auth.'.get_list'));

		$id	= isset($id)?$id:$this->input->get_post('id');	
		if (is_numeric($id))
		{
			$this->load->model('eoi/m_entrega');
			$data = $this->m_entrega->get(null, null, null, null, 'nIdCurso=' . $id);
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Lee el árbol del curso, de uso interno
	 *
	 * @param int $id Id del curso
	 * @param int $padre Id del pader
	 * @param bool $is_tree true: en forma de árbol, false: en listado
	 * @param int $level Nivel de profundidad (para el formato listado)
	 * @return array
	 */
	private function _get_titulo($id = null, $padre = null, $is_tree = true, $level = 1)
	{
		$this->load->model('eoi/m_titulo');
		$nodes = $this->m_titulo->get_by_padre($id, $padre);
		$tree = array();
		$tipos = array( 1 => 'icon-curso',
				2 => 'icon-nivel',
				3 => 'icon-obligatorio',
				4 => 'icon-lectura',
				5 => 'icon-opcional');

		foreach($nodes as $node)
		{
			if (empty($node['cDescripcion']) || (trim($node['cDescripcion']) == '')) $node['cDescripcion']= $node['cTitulo'];

			$n['text'] = $node['cDescripcion'];
			$n['qtip'] = $node['nIdTitulo'] . '-' . $node['cDescripcion'];
			$n['id'] = $node['nIdTitulo'];
			$n['uiProvider'] = 'col';
			$children = $this->_get_titulo($id, $node['nIdTitulo'], $is_tree, $level + 1);
			$n['iconCls'] = $tipos[$node['nTipo']];
			$n['leaf'] = (count($children) == 0);

			foreach($node as $k => $v) 
			{
				$n[$k]=$v;
			}
			
			$n['dCreacion'] = format_datetime($n['dCreacion']);
			$n['dAct'] = format_datetime($n['dAct']);
			
			if ($is_tree)
			{
				$n['children'] = $children;
			}
			else
			{
				$n['_id'] = $node['nIdTitulo'];
				$n['_level'] = $level;
				$n['_parent'] = isset($id)?$id:null;
				$n['_is_leaf'] = $n['leaf'];
			}
			$tree[] = $n;
			if (!$is_tree)
			{
				$tree = array_merge($tree, $children);
			}
		}
		return $tree;
	}

	/**
	 * Añade los títulos de un curso desde un texto wiki
	 * @param int $id Id del curso
	 * @param int $padre Id del nivel
	 * @param string $wiki Texto de la wiki
	 */
	function add_wiki($id = null, $padre = null, $wiki = null)
	{
		$this->userauth->roleCheck(($this->auth.'.add'));
		$id		= isset($id)?$id:$this->input->get_post('id');
		$padre	= isset($padre)?$padre:$this->input->get_post('padre');
		$wiki	= isset($wiki)?$wiki:$this->input->get_post('wiki');

		if (is_numeric($id) && is_numeric($padre))
		{
			#$wiki = file_get_contents(__DIR__ . '/test.txt');
			$lines = explode("\n", $wiki);
			$title = null;
			$titulos = array();
			# Busca títulos
			foreach ($lines as $l)
			{
				preg_match_all('/==(.+)==/', $l, $res);
				if (isset($res[1][0]))
				{
					$title = $res[1][0];
				}
				preg_match_all('/<libro id="(.+)"[^\/]*\/>/', $l, $res);
				if (isset($res[1][0]))
				{
					$libro = $res[1][0];
					if (isset($title))
					{
						$titulos[$title][] = $libro;
					}
				}				
			}
			#Añade cursos
			$this->load->model('eoi/m_titulo');
			$n['nIdCurso'] = $id;
			$n['nIdTituloPadre'] = $padre;
			$this->db->trans_begin();
			$niveles = $count = 0;
			foreach ($titulos as $nivel => $libros)
			{
				$n['cDescripcion'] = $nivel;
				$n['nTipo'] = 2;
				$id_n = $this->m_titulo->insert($n);
				if ($id_n < 0)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_titulo->error_message());
				}
				++$niveles;
				# Añade los títulos
				$n2['nIdCurso'] = $id;
				$n2['nTipo'] = 3;
				$n2['nIdTituloPadre'] = $id_n;
				$n2['cDescripcion'] = '';
				foreach ($libros as $value) 
				{
					$n2['nIdRegistro'] = $value;
					if ($this->m_titulo->insert($n2) < 0)
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_titulo->error_message());
					}		
					++$count;			
				}				
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('eoi-curso-add-wiki-ok'), $niveles, $count));
			#Mensaje final
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}	

	/**
	 * Devuelve las escuelas activas con títulos
	 * @return DATA
	 */
	function get_escuelas()
	{
		$this->userauth->roleCheck(($this->auth.'.get_list'));
		$data = $this->reg->get_escuelas();
		$this->out->data($data);
	}

	/**
	 * Devuelve los idiomas de la escuela indicada
	 * @param int $id Id del curso
	 * @return DATA
	 */
	function get_idiomas($id = null)
	{
		$this->userauth->roleCheck(($this->auth.'.get_list'));
		$id		= isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$this->load->model('eoi/m_titulo');
			$data = $this->m_titulo->get(null, null, null, null, "nIdCurso={$id} AND nTipo=1");
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve los niveles del idioma indicado
	 * @param int $id Id del idioma
	 * @return DATA
	 */
	function get_niveles($id = null)
	{
		$this->userauth->roleCheck(($this->auth.'.get_list'));
		$id		= isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$this->load->model('eoi/m_titulo');
			$data = $this->m_titulo->get(null, null, null, null, "nIdTituloPadre={$id}");
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
}

/* End of file curso.php */
/* Location: ./system/application/controllers/eoi/curso.php */
