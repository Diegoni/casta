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

/**
 * Controlador de materias
 *
 */
class Materia extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('catalogo.materia', 'catalogo/M_materia', TRUE, 'catalogo/materias.js', 'Materias');
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
			$n['qtip'] = $node['nIdMateria'] . '-' . $node['cNombre'];
			$n['id'] = $node['nIdMateria'];
			$n['uiProvider'] = 'col';
			$children = $this->_get_tree($node['nIdMateria'], $is_tree, $level + 1);
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
				$n['_id'] = $node['nIdMateria'];
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
	 * Devuelve el árbol de las materias
	 *
	 * @param int $id Id del padre
	 * @return JSON_DATA
	 */
	function get_tree($id = null)
	{
		$id	= isset($id)?$id:$this->input->get_post('id');

		echo $this->out->send($this->_get_tree($id));
	}

	/**
	 * Devuelve el árbol de las materias
	 *
	 * @param int $id Id del padre
	 * @return JSON_DATA
	 */
	function mover($id = null, $destino = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');
		$id	= isset($id)?$id:$this->input->get_post('id');
		$destino	= isset($destino)?$destino:$this->input->get_post('destino');
		
		if (is_numeric($id) && is_numeric($destino))
		{
			if ($id == $destino)
				$this->out->error($this->lang->line('materia-mover-id-error'));				
			$mat = $this->reg->load($destino);
			#var_dump($id);
			#var_dump((strpos($mat['cCodMateria'], '.' . $id) !== FALSE || strpos($mat['cCodMateria'], $id . '.') === 0));			
			#var_dump($mat); die();
			if (strpos($mat['cCodMateria'], '.' . $id) !== FALSE || strpos($mat['cCodMateria'], $id . '.') === 0)
				$this->out->error($this->lang->line('materia-mover-hijo-error'));
			if (!$this->reg->update($id, array('nIdMateriaPadre' => $destino)))
				$this->out->error($this->reg->error_message());
			$this->out->success($this->lang->line('materia-mover-ok'));			
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Regenera los códigos de materia
	 * @param int $id  Id de la materia padre
	 * @param string $base Código base
	 * @return TXT
	 */
	function generar_codigos($id = null, $base = '')
	{

		$where =(isset($id))?('nIdMateriaPadre='.$id):'nIdMateriaPadre IS NULL OR nIdMateriaPadre=0';
		$mats = $this->reg->get(null, null, null, null, $where);
		if (!isset($id)) echo '<pre>';
		foreach ($mats as $value) 
		{
			if ($value['cCodMateria'] != ($base . $value['nIdMateria']))
			{
				echo "{$value['cNombre']} -> ACT: {$value['cCodMateria']} -> TIENE QUE SER {$base}{$value['nIdMateria']}\n";
				$this->reg->update($value['nIdMateria'], array('cCodMateria' => $base . $value['nIdMateria']));
			}
			$this->generar_codigos($value['nIdMateria'], $base . $value['nIdMateria'] .'.');
		}
		if (!isset($id)) echo '</pre>';
	}
}

/* End of file materia.php */
/* Location: ./system/application/controllers/catalogo/materia.php */
