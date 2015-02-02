<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	eoi
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Departamentos EOI
 *
 */
class M_Departamento extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Departamento
	 */
	function __construct()
	{
		$data_model = array(
			'nIdEOI'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'eoi/escuela/search', 'cEscuela')), 			
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE),
			'nIdCliente'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'clientes/cliente/search', 'nIdCliente')),		
		);
		
		parent::__construct('Ext_EOISDepartamentos', 'nIdDepartamento', 'cDescripcion', 'cDescripcion', $data_model);
		$this->load->library('cache');
		$this->_cache = TRUE;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cli_Clientes.cEmpresa');
			$this->db->join('Cli_Clientes', "Cli_Clientes.nIdCliente = {$this->_tablename}.nIdCliente", 'left');
			$this->db->select('Ext_EOIS.cDescripcion cEscuela');
			$this->db->join('Ext_EOIS', "Ext_EOIS.nIdEOI = {$this->_tablename}.nIdEOI", 'left');
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
			$data['cCliente'] = format_name($data['cNombre'], $data['cApellido'], $data['cEmpresa']);
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_Departamento.php */
/* Location: ./system/application/models/eoi/M_Departamento.php */