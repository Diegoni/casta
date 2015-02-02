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

/**
 * Autores
 *
 */
class M_autor extends MY_Model
{
	/**
	 * Costructor
	 * @return M_autor
	 */
	function __construct()
	{
		$data_model = array(
			'cNombre'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'cApellido'		=> array(),
			'bTipo'			=> array( DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOL),
		);

		parent::__construct('Cat_Autores', 'nIdAutor', 'cNombre', array('cNombre', 'cApellido'), $data_model, TRUE);
		#$this->_cache = TRUE;
		$this->_deleted = TRUE;
	}

	/**
	 * Unificador de autores
	 * @param int $id1 Id del autor destino
	 * @param int $id2 Id del autor repetido
	 * @return bool, TRUE: correcto, FALSE: incorrecto
	 */
	function unificar($id1, $id2)
	{
		// TRANS
		$this->db->trans_begin();

		// Artículos - Autor
		$update = array('nIdAutor' => (int)$id1);
		$this->db->flush_cache();
		$this->db->where("nIdAutor = {$id2}")
		->update('Cat_Autores_Libros', $update);
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		// Borrado
		$res = $this->db->flush_cache();
		$this->db->where("nIdAutor={$id2}")
		->delete('Cat_Autores');
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		// Limpieza de caches
		$ci = get_instance();

		$ci->load->model('catalogo/m_articuloautor');
		$ci->m_articuloautor->clear_cache();

		$this->clear_cache();

		// COMMIT
		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterUpdate($data)
	 */
	protected function onAfterUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			if (isset($data['cApellido']) || isset($data['cNombre']))
			{
				$this->obj->db->flush_cache();
				$this->obj->db->select('nIdLibro')
				->from('Cat_Autores_Libros')
				->where('Cat_Autores_Libros.nIdAutor=' . $id);
				$query = $this->obj->db->get();
				$data = $this->_get_results($query);
				$this->obj->load->model('catalogo/m_articulo');		
				foreach ($data as $art) 
				{
					if (!$this->obj->m_articulo->crear_autores($art['nIdLibro']))
					{
						$this->_set_error_message($this->obj->m_articulo->error_message());
						return FALSE;
					}
				}
			}
			return TRUE;
		}
		return FALSE;
	}

}

/* End of file M_autor.php */
/* Location: ./system/application/models/catalogo/M_autor.php */