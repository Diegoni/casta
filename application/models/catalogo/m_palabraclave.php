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
 * Palabras Clave
 *
 */
class M_palabraclave extends MY_Model
{
	/**
	 * Costructor
	 * @return M_palabraclave
	 */
	function __construct()
	{
		$data_model = array(
			'cPalabraClave'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		);

		parent::__construct('Cat_PalabrasClave', 'nIdPalabraClave', 'cPalabraClave', 'cPalabraClave', $data_model, TRUE);
		$this->_cache = TRUE;
	}
	
	/**
	 * Unificador 
	 * @param int $id1 Id del destino
	 * @param int $id2 Id del repetido
	 * @return bool, TRUE: correcto, FALSE: incorrecto
	 */
	function unificar($id1, $id2)
	{
		$this->load->helper('unificar');
		// TRANS
		$this->db->trans_begin();

		// Palabras Clave
		if (!unificar_nn($this, 'Cat_PalabrasClave_Libros', 'nIdPalabraClave', 'nIdPalabraClave', $id1, $id2))
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Borrado
		$res = $this->db->flush_cache();
		$this->db->where("nIdPalabraClave={$id2}")
		->delete($this->_tablename);
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		// Limpieza de caches
		$ci = get_instance();

		$ci->load->model('catalogo/m_articulopalabraclave');
		$ci->m_articulopalabraclave->clear_cache();

		$this->clear_cache();

		// COMMIT
		$this->db->trans_commit();
		return TRUE;
	}	
}

/* End of file M_palabraclave.php */
/* Location: ./system/application/models/catalogo/M_palabraclave.php */