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
 * @version		$Rev: 435 $
 * @filesource
 */

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'catalogo' . DIRECTORY_SEPARATOR . 'm_articulo.php');

/**
 * Búsqueda de Artículos
 *
 */
class M_articulosearch extends M_articulo
{
	/**
	 * Constructor
	 * @return M_articulosearch
	 */
	function __construct()
	{
		parent::__construct();
		$this->_alias = array(
				'Dpt' 		=> array('Cat_Secciones_Libros.nStockDeposito', DATA_MODEL_TYPE_INT),
				'Rsd'		=> array('Cat_Secciones_Libros.nStockReservado', DATA_MODEL_TYPE_INT),
				'Rcbir'		=> array('Cat_Secciones_Libros.nStockRecibir', DATA_MODEL_TYPE_INT),
				'APdr'		=> array('Cat_Secciones_Libros.nStockAPedir', DATA_MODEL_TYPE_INT),
				'Srv'		=> array('Cat_Secciones_Libros.nStockServir', DATA_MODEL_TYPE_INT),
				'ADvr'		=> array('Cat_Secciones_Libros.nStockADevolver', DATA_MODEL_TYPE_INT),
				'AExm'		=> array('Cat_Secciones_Libros.nStockAExamen', DATA_MODEL_TYPE_INT),
				'Frm'		=> array('Cat_Secciones_Libros.nStockFirme', DATA_MODEL_TYPE_INT),		
				'Stk'		=> array('(Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito)', DATA_MODEL_TYPE_INT),		
				'Idm'		=> array('Cat_Libros_Materias.nIdMateria', DATA_MODEL_TYPE_INT),
				'Scn'		=> array('Cat_Secciones.nIdSeccion', DATA_MODEL_TYPE_INT),
				'nStockDisponible'		=> array('(Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito - Cat_Secciones_Libros.nStockReservado - Cat_Secciones_Libros.nStockADevolver)', DATA_MODEL_TYPE_INT),
				'nStockADevolver'		=> array('nStockADevolver', DATA_MODEL_TYPE_INT),
				'fPVP'		=> array('fPrecio', DATA_MODEL_TYPE_FLOAT),
				'portada'		=> array('Fotos.nIdRegistro', DATA_MODEL_TYPE_INT),
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id, &$sort = null, &$dir = null, &$where = null)
	{
		#echo '<pre>'; print_r($where); echo '</pre>';
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->_fix_sort($sort);

			$this->db->select('Cat_Secciones.cNombre cSeccion, Cat_Secciones_Libros.nStockFirme, Cat_Secciones_Libros.nStockDeposito')
			->select('Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito Stk')
			->select('Cat_Secciones_Libros.nStockReservado, Cat_Secciones_Libros.nStockRecibir, Cat_Secciones_Libros.nStockAPedir, Cat_Secciones_Libros.nStockServir, Cat_Secciones_Libros.nStockADevolver, Cat_Secciones_Libros.nStockAExamen')
			->select('Cat_Secciones_Libros.nIdSeccion')
			->select($this->db->concat(array('ISNULL(Cat_Secciones_Libros.nIdSeccion, 0)', "'_'", 'Cat_Fondo.nIdLibro'), 'id2'))
			->select('(Cat_Fondo.fPrecio * (1 + Cat_Tipos.fIVA / 100)) fPVP2')
			->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdLibro = Cat_Fondo.nIdLibro', 'left')
			->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion = Cat_Secciones_Libros.nIdSeccion', 'left');
			
			if (strpos($where, 'Cat_Libros_Materias.') !== FALSE)
			{
				$this->db->join('Cat_Libros_Materias', 'Cat_Libros_Materias.nIdLibro = Cat_Fondo.nIdLibro', 'left');
			}
			if (strpos($where, 'Cat_Materias.') !== FALSE)
			{
				$this->db->join('Cat_Libros_Materias', 'Cat_Libros_Materias.nIdLibro = Cat_Fondo.nIdLibro', 'left');
				$this->db->join('Cat_Materias', 'Cat_Libros_Materias.nIdMateria = Cat_Materias.nIdMateria', 'left');
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		if (parent::onAfterSelect($data, $id))
		{
			$disponible = isset($data['nStockFirme'])?$data['nStockFirme']:0;
			$disponible += isset($data['nStockDeposito'])?$data['nStockDeposito']:0;
			$disponible -= isset($data['nStockReservado'])?$data['nStockReservado']:0;
			$disponible -= isset($data['nStockADevolver'])?$data['nStockADevolver']:0;
			$data['nStockDisponible'] = $disponible;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @see system/application/libraries/MY_Model#onParseWhere
	 */
	protected function onParseWhere(&$where)
	{
		parent::onParseWhere($where);
		if (isset($where['portada']))
		{
			if ($where['portada'] == $this->lang->line('bool_si'))
			{
				$where[] = 'Cat_Fondo.nIdLibro IN (SELECT Fotos.nIdRegistro FROM Fotos)';
			}
			elseif ($where['portada'] == $this->lang->line('bool_no'))
			{
				$where[] = 'Cat_Fondo.nIdLibro NOT IN (SELECT Fotos.nIdRegistro FROM Fotos)';
			}
			unset($where['portada']);
		}
		if (isset($where['sinopsis']))
		{
			if ($where['sinopsis'] == $this->lang->line('bool_si'))
			{
				$where[] = 'Cat_Fondo.nIdLibro IN (SELECT Cat_Sinopsis.nIdLibro FROM Cat_Sinopsis)';
			}
			elseif ($where['sinopsis'] == $this->lang->line('bool_no'))
			{
				$where[] = 'Cat_Fondo.nIdLibro NOT IN (SELECT Cat_Sinopsis.nIdLibro FROM Cat_Sinopsis)';
			}
			unset($where['sinopsis']);
		}
		#print '<pre>'; print_r($where); print '</pre>'; die();
		return TRUE;
	}

}

/* End of file M_articulosearch.php */
/* Location: ./system/application/models/catalogo/M_articulosearch.php */
