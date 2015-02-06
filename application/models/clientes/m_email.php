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
 * Emails
 *
 */
class M_email extends MY_Model
{
	/**
	 * Costructor
	 * @return M_email
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE), 
			'cEMail'		=> array(DATA_MODEL_REQUIRED => TRUE), 
			'nIdTipo'		=> array(DATA_MODEL_DEFAULT_VALUE => 1, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'perfiles/tipoperfil/search')), 
			'nIdCliente'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
		);

		parent::__construct('Cli_EMails', 'nIdEmail', 'cDescripcion', 'cDescripcion', $data_model);
		#$this->_cache = TRUE;
	}
	/**
	 * Obtiene un listado de emails
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
					'tipo' 			=> 'E', 
					'id_perfil'		=> $d['nIdTipo'],
					'cPerfil' 		=> $d['cPerfil'],
					'cDescripcion' 	=> $d['cDescripcion'],
					'text' 			=> $d['cEMail'], 
					'id_u'			=> 'E' . $d['nIdEMail'],			
					'id' 			=> $d['nIdEMail']);
		}

		return $perfiles;
	}
}

/* End of file M_email.php */
/* Location: ./system/application/models/clientes/M_email.php */