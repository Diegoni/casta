<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	generico
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Secciones
 *
 */
class M_Seccion extends MY_Model
{
	/**
	 * Costructor
	 * @return M_Seccion
	 */
	function __construct()
	{
		$data_model = array(
			'nIdSeccionPadre'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search')),
			'cNombre'			=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'bBloqueada'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bWeb' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'cCodigo'			=> array(),
			'nHijos' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);

		parent::__construct('Cat_Secciones', 'nIdSeccion', 'cNombre', 'cNombre', $data_model, TRUE);

		$this->_cache = TRUE;
	}

	/**
	 * Devuelve las secciones de un padre
	 * @param $id ID del pader
	 * @return array
	 */
	function get_by_padre($id = null)
	{
		if ($id)
		{
			return $this->get(null, null, 'cNombre', null, 'nIdSeccionPadre = ' . $id);
		}
		else
		{
			return $this->get(null, null, 'cNombre', null, 'nIdSeccionPadre IS NULL ');
		}
	}

	/**
	 * Devuelve los libros de una seccion
	 * @param $id Id de la sección
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $where Condiciones de la consulta
	 * @return unknown_type
	 */
	function get_libros($id, $start = null, $limit = null, $sort = null, $dir = null, $where = null)
	{
		if (is_numeric($id))
		{
			//Contador
			$this->db->start_cache();
			$this->db->select('l.cTitulo, l.cAutores, l.cISBN, sl.nStockFirme + sl.nStockDeposito nStock, l.nIdLibro')
			->from($this->_tablename. ' s')
			->join('Cat_Secciones_Libros sl', 'sl.nIdSeccion = s.nIdSeccion')
			->join('Cat_Fondo l', 'sl.nIdLibro = l.nIdLibro')
			->where('sl.nIdSeccion', $id);
			if (isset($where)) $this->db->where($where);
			$this->_count = $this->db->count_all_results();

			//Consulta
			$this->_limits_sort($start, $limit, $sort, $dir);
			$query = $this->db->get();
			$this->db->flush_cache();

			return $this->_get_results($query, $start, $limit);
		}
	}

	/**
	 * Mueve los libros de una sección a otra y mantiene el histórico
	 *
	 * @param mixed $ids array de IDs o id individual del libro
	 * @param int $idorigen Sección origen
	 * @param int $iddestino Sección destino
	 */
	function move_libros($ids, $idorigen, $iddestino)
	{
		if (is_numeric($idorigen) && is_numeric($iddestino) && (is_numeric($ids) || is_array($ids)))
		{
			$sql = 'EXEC spMoverLibroSeccionHistorico @IdOrigen = ?, @IdDestino = ?, @IdLibro = ?';
			$count = 0;
			if (is_array($ids))
			{
				foreach($ids as $id)
				{
					if (is_numeric($id))
					{
						$this->db->query($sql, array((int) $idorigen, (int) $iddestino, (int) $id));
						$count++;
					}
				}
			}
			else
			{
				$this->db->query($sql, array((int) $idorigen, (int) $iddestino, (int) $ids));
				$count++;
			}
			return $count;
		}
		return 0;
	}

	/**
	 * Elimina los libros de una sección
	 *
	 * @param mixed $ids array de IDs o id individual del libro
	 * @param int $idorigen Sección origen
	 */
	function del_libros($ids, $idorigen)
	{
		if (is_numeric($idorigen) && (is_numeric($ids) || is_array($ids)))
		{
			$count = 0;
			if (is_array($ids))
			{
				foreach($ids as $id)
				{
					if (is_numeric($id))
					{
						$this->db->where('nIdLibro', (int)$id);
						$this->db->where('nIdSeccion', (int)$idorigen);
						$this->db->delete('Cat_Secciones_Libros');
						$count++;
					}
				}
			}
			else
			{
				$this->db->where('nIdLibro', (int)$id);
				$this->db->where('nIdSeccion', (int)$idorigen);
				$this->db->delete('Cat_Secciones_Libros');
				$count++;
			}
			return $count;
		}
		return 0;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert($data)
	 */
	protected function onAfterInsert($id, &$data)
	{
		if (parent::onAfterInsert($id, $data))
		{
			return $this->_check_code($id, $data);
		}
		return TRUE;
	}

	/**
	 * General el código de la sección
	 * @param int $id ID de la sección
	 * @param array $data Datos del registro
	 */
	private function _check_code($id, &$data)
	{
		if (!isset($data['cCodigo']))
		{
			if (isset($data['nIdSeccionPadre']) && ($data['nIdSeccionPadre']>0))
			{
				$this->db->start_cache();

				$sec = $this->load($data['nIdSeccionPadre']);
				$codigo = "{$sec['cCodigo']}.{$id}";
			}
			else
			{
				$codigo = $id;
			}
			return $this->update($id, array('cCodigo' => $codigo));
		}
		return TRUE;
	}

	/**
	 * Devuelve el stock actual de las secciones
	 * @return array
	 */
	function stocks()
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Secciones.nIdSeccion, Cat_Secciones.cNombre')
		->select_sum('nStockFirme', 'nStockFirme')
		->select_sum('nStockDeposito', 'nStockDeposito')
		->from('Cat_Secciones')
		->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdSeccion=Cat_Secciones.nIdSeccion', 'left')
		->group_by('Cat_Secciones.nIdSeccion, Cat_Secciones.cNombre')
		->order_by('Cat_Secciones.cNombre');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($data)
	 */
	protected function onAfterUpdate($id, &$data)
	{
		if (parent::onAfterUpdate($id, $data))
		{
			return $this->_check_code($id, $data);
		}
		return TRUE;
	}
}
/* End of file M_seccion.php */
/* Location: ./system/application/models/generico/M_seccion.php */
