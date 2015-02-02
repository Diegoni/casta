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
 * Direcciones
 *
 */
class M_perfildireccionmodel extends MY_Model
{
	/**
	 * Campo FK en la trabla de cruce que enlaza a las direcciones
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
	 * @param string $tablename Tabla de cruce entre el registro principal y las direcciones
	 * @param string $idname Campo Id en la trabla de cruce del registro principal
	 * @param string $idcruceid Campo FK en la trabla de cruce que enlaza a las direcciones
	 * @param string $idcruce Campo FK en la trabla de cruce que enlaza al registro principal
	 * @return M_perfildireccionmodel
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
	 * Obtiene un listado de direcciones
	 * @param int $id Id de cruce
	 * @return array
	 */
	function get_list($id, $long = FALSE)
	{
		$this->db->select('d.*, p.cDescripcion cPerfil, r.cNombre cRegion, ps.cNombre cPais')
		->from('Gen_Direcciones d')
		->join("{$this->_tablename} d2", "d.nIdDireccion = d2.{$this->_id1}" )
		->join("Gen_TiposPerfil p", "d.nIdTipo = p.nIdTipo", 'left')
		->join("Gen_Regiones r", "d.nIdRegion = r.nIdregion", 'left')
		->join("Gen_Paises ps", "ps.nIdPais = r.nIdPais", 'left')
		->where("d2.{$this->_id2} = {$id}");

		$r = $this->db->get();
		$dir = $this->_get_results($r);

		$perfiles = array();
		foreach($dir as $d)
		{
			$perfiles[] = format_address($d, TRUE);
		}

		return $perfiles;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('d.*, r.cNombre cRegion, ps.cNombre cPais')
			->join("Gen_Direcciones d", "d.nIdDireccion = {$this->_tablename}.{$this->_id1}" )
			->join("Gen_Regiones r", "d.nIdRegion = r.nIdregion", 'left')
			->join("Gen_Paises ps", "ps.nIdPais = r.nIdPais", 'left');
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_perfildireccionmodel.php */
/* Location: ./system/application/models/M_perfildireccionmodel.php */