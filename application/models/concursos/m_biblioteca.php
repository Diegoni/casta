<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Bibliotecas  Concurso
 *
 */
class M_biblioteca extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_biblioteca
	 */
	function __construct()
	{
		$data_model = array(
			'nIdConcurso'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/concurso/search', 'cConcurso')),
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'fImporte'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_MONEY),
			'nIdCliente'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdDireccion'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);
		
		parent::__construct('Ext_Bibliotecas', 'nIdBiblioteca', 'cDescripcion', 'cDescripcion', $data_model, TRUE);	
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Ext_Concursos.cDescripcion cConcurso');
			$this->db->join('Ext_Concursos', "Ext_Concursos.nIdConcurso = {$this->_tablename}.nIdConcurso", 'left');
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_biblioteca.php */
/* Location: ./system/application/models/concursos/M_biblioteca.php */