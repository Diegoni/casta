<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	models
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Contactos contacto
 *
 */
class m_perfilcontactomodel extends MY_Model
{
	/**
	 * Campo FK en la trabla de cruce que enlaza a los contactos
	 * @var string
	 */
	protected $_id1;
	/**
	 * Campo FK en la trabla de cruce que enlaza al registro principal
	 * @var string
	 */
	protected $_id2;

	/**
	 * Costructor
	 * @param string $tablename Tabla de cruce entre el registro principal y los contactos
	 * @param string $idname Campo Id en la trabla de cruce del registro principal
	 * @param string $idcruceid Campo FK en la trabla de cruce que enlaza a los contactos
	 * @param string $idcruce Campo FK en la trabla de cruce que enlaza al registro principal
	 * @return m_perfilcontactomodel
	 */
	function __construct($tablename, $idname, $idcruceid, $idcruce)
	{
		$data_model = array(
		$idcruceid 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		$idcruce	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);

		$this->_id1 = $idcruceid;
		$this->_id2 = $idcruce;

		parent::__construct($tablename, $idname, $idname, $idname, $data_model);
	}

	/**
	 * Obtiene un listado de contactos
	 * @param int $id Id de cruce
	 * @return array
	 */
	function get_list($id, $long = FALSE)
	{
		
		$this->db->select('d.*, p.cDescripcion cPerfil')
		->from('Gen_Contactos d')
		->join("{$this->_tablename} d2", "d.nIdContacto = d2.{$this->_id1}" )
		->join("Gen_TiposPerfil p", "d.nIdTipo = p.nIdTipo")
		->where("d2.{$this->_id2} = {$id}");

		$r = $this->db->get();
		$dir = $this->_get_results($r);

		$perfiles = array();
		foreach($dir as $d)
		{
			$perfiles[] = array(
					'tipo' 			=> 'C', 
					'cPerfil' 		=> $d['cPerfil'],
					'cDescripcion' 	=> $d['cDescripcion'],
					'text' 		=> trim($d['cNombre'] . ' '. $d['cApellido']), 
					'id_u'			=> 'C' . $d['nIdContacto'],			
					'id' 			=> $d['nIdContacto']);
		}

		return $perfiles;
	}
}

/* End of file m_perfilcontactomodel.php */
/* Location: ./system/application/models/m_perfilcontactomodel.php */