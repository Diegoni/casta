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

/**
 * Controlador de secciones
 *
 */
class Seccion extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Seccion
	 */
	function Seccion()
	{
		parent::__construct('generico.seccion', 'generico/M_Seccion', true, 'generico/secciones.js', 'Secciones');
	}

	/**
	 * Formulario de mover libros y el histórico
	 *
	 */
	function mover_libros()
	{
		$this->_show_form($this->auth . '.mover_libros_historia', 'catalogo/moverlibros.js', $this->lang->line('Mover Libros'));
	}

	/**
	 * Devuelve el árbol de secciones
	 *
	 * @param int $id Id del padre
	 */
	function get_tree($id = null)
	{
		$id	= isset($id)?$id:$this->input->get_post('id');

		echo $this->out->send($this->_get_tree($id));
	}

	/**
	 * Devuelve las secciones en forma de listado
	 *
	 * @param int $id Id del padre
	 */
	function get_list($id = null)
	{
		$id	= isset($id)?$id:$this->input->get_post('id');

		$data = $this->_get_tree($id, false);
		$res = array(
			'total_data' => count($data),
			'value_data' => $data,
			'success' => true
		);
		// Respuesta
		echo $this->out->send($res);
	}

	/**
	 * Lee el árbol de secciones, de uso interno
	 *
	 * @param int $id Id del padre
	 * @param bool $is_tree true: en forma de árbol, false: en listado
	 * @param int $level Nivel de profundidad (para el formato listado)
	 * @return array
	 */
	private function _get_tree($id = null, $is_tree = true, $level = 1)
	{
		$nodes = $this->reg->get_by_padre($id);
		$tree = array();
		foreach($nodes as $node)
		{
			$n['text'] = $node['cNombre'];
			$n['qtip'] = $node['nIdSeccion'] . '-' . $node['cNombre'];
			$n['id'] = $node['nIdSeccion'];
			$n['uiProvider'] = 'col';
			$children = $this->_get_tree($node['nIdSeccion'], $is_tree, $level + 1);
			$n['iconCls'] = (count($children)>0)?'icon-seccion-folder':'icon-seccion';
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
				$n['_id'] = $node['nIdSeccion'];
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
	 * Devuelve un listado de libros de una sección
	 *
	 * @param int $id Id de la sección
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 */
	function get_libros($id=null, $start = null, $limit = null, $sort = null, $dir = null)
	{
		$id		= isset($id)?$id:$this->input->get_post('id');
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($order)?$order:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');

		if (isset($id))
		{
			if ($sort == 'nIdLibro') $sort = 'l.nIdLibro';
			if ($sort == 'nStock') $sort = 'sl.nStockFirme + sl.nStockDeposito';

			$data = $this->reg->get_libros($id, $start, $limit, $sort, $dir);

			$res = array(
				'total_data' => $this->reg->get_count(),
				'value_data' => $data
			);
			// Respuesta
			echo $this->out->send($res);
		}
	}

	/**
	 * Mueve los libros de una sección a otra y mantiene el histórico
	 *
	 * @param string $ids IDs separados por ;
	 * @param int $idorigen Sección origen
	 * @param int $iddestino Sección destino
	 */
	function move_books_ids($ids = null, $idorigen = null, $iddestino = null)
	{
		$this->userauth->roleCheck(('seccion.mover_libros_historia'));

		$ids 	= isset($ids)?$ids:$this->input->get_post('ids');
		$idorigen 	= isset($idorigen)?$idorigen:$this->input->get_post('idorigen');
		$iddestino 	= isset($iddestino)?$iddestino:$this->input->get_post('iddestino');

		if ($ids && $idorigen && $iddestino)
		{
			if (is_string($ids)) $ids = preg_split('/\;/', $ids);
			//Puede tardar
			set_time_limit(0);
			$contador = $this->reg->move_libros($ids, $idorigen, $iddestino);
			$res = TRUE;
		}
		else
		{
			$res = sprintf($this->lang->line('mensaje_faltan_datos'));
		}
		if ($res === TRUE)
		{
			$ajax_res = array(
				'success' 	=> true,
				'message'	=> sprintf($this->lang->line('libros-movidos'), $contador)
			);
		}
		else
		{
			$ajax_res = array(
				'success' 	=> false,
				'message'	=> $res
			);
		}
		// Respuesta
		echo $this->out->send($ajax_res);
	}

	/**
	 * Elimina los libros de una sección
	 *
	 * @param string $ids IDs separados por ;
	 * @param int $idorigen Sección origen
	 */
	function del_books_ids($ids = null, $idorigen = null)
	{
		$this->userauth->roleCheck(('seccion.del_libros'));

		$ids 	= isset($ids)?$ids:$this->input->get_post('ids');
		$idorigen 	= isset($idorigen)?$idorigen:$this->input->get_post('idorigen');

		if ($ids && $idorigen)
		{
			if (is_string($ids)) $ids = preg_split('/\;/', $ids);
			//Puede tardar
			set_time_limit(0);
			$contador = $this->reg->del_libros($ids, $idorigen);
			$res = TRUE;
		}
		else
		{
			$res = sprintf($this->lang->line('mensaje_faltan_datos'));
		}
		if ($res === TRUE)
		{
			$ajax_res = array(
				'success' 	=> true,
				'message'	=> sprintf($this->lang->line('libros-borrados'), $contador)
			);
		}
		else
		{
			$ajax_res = array(
				'success' 	=> false,
				'message'	=> $res
			);
		}
		// Respuesta
		echo $this->out->send($ajax_res);
	}
	
	/**
	 * Devuelve el stock actual de las secciones
	 * @return HTML_FILE
	 */
	function stocks()
	{
		$this->userauth->roleCheck($this->auth.'.get_list');
		$data['stocks'] = $this->reg->stocks();
		$body = $this->load->view('catalogo/stocksecciones', $data, TRUE);
		$this->out->html_file($body, $this->lang->line('Stocks en las secciones'), 'iconoReportTab');		
	}

	/**
	 * Regenera los códigos de sección
	 * @param int $id  Id de la sección padre
	 * @param string $base Código base
	 * @return TXT
	 */
	function generar_codigos($id = null, $base = '')
	{

		$where =(isset($id))?('nIdSeccionPadre='.$id):'nIdSeccionPadre IS NULL OR nIdSeccionPadre=0';
		$mats = $this->reg->get(null, null, null, null, $where);
		if (!isset($id)) echo '<pre>';
		foreach ($mats as $value) 
		{
			if ($value['cCodigo'] != ($base . $value['nIdSeccion']))
			{
				echo "{$value['cNombre']} -> ACT: {$value['cCodigo']} -> TIENE QUE SER {$base}{$value['nIdSeccion']}\n";
				$this->reg->update($value['nIdSeccion'], array('cCodigo' => $base . $value['nIdSeccion']));
			}
			$this->generar_codigos($value['nIdSeccion'], $base . $value['nIdSeccion'] .'.');
		}
		if (!isset($id)) echo '</pre>';
	}

}

/* End of file seccion.php */
/* Location: ./system/application/controllers/generico/seccion.php */
