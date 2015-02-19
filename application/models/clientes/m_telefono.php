<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	clientes
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Teléfonos
 *
 */
class M_telefono extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_tema
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE), 
			'cTelefono'		=> array(DATA_MODEL_REQUIRED => TRUE), 
			'bFax'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN), 
			'nIdTipo'		=> array(DATA_MODEL_DEFAULT_VALUE => 1, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'perfiles/tipoperfil/search')), 
			'nIdCliente'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
		);

		parent::__construct(
					'Cli_Telefonos', 
					'nIdTelefono', 
					'cDescripcion', 
					'cDescripcion', 
					$data_model
				);	
		#$this->_cache = TRUE;
	}
	
	/**
	 * Obtiene un listado de telefonos
	 * @param int $id Id de cruce
	 * @return array
	 */
	function get_list($id, $long = FALSE)
	{
		$this->db->flush_cache();
		$this->db->select('d.*, p.cDescripcion cPerfil')
		->from("{$this->_tablename} d")
		->join("Gen_TiposPerfil p", "d.nIdTipo = p.nIdTipo")
		->where("d.nIdCliente = {$id}");

		$r = $this->db->get();
		$dir = $this->_get_results($r);

		$perfiles = array();
		foreach($dir as $d)
		{
			$perfiles[] = array(
					'tipo' 			=> 'T', 
					'id_perfil'		=> $d['nIdTipo'],
					'cPerfil' 		=> $d['cPerfil'],
					'cDescripcion' 	=> ($d['bFax']?$this->lang->line('fax-descr'):'') . $d['cDescripcion'],
					'text' 			=> $d['cTelefono'], 
					'id_u'			=> 'T' . $d['nIdTelefono'],
					'id' 			=> $d['nIdTelefono']);
		}

		return $perfiles;
	}
}

/* End of file M_telefono.php */
/* Location: ./system/application/models/clientes/M_telefono.php */