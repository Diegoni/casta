<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Artíulos a unificar
 *
 */
class M_aunificar extends MY_Model
{
	/**
	 * Costructor
	 * @return M_aunificar
	 */
	function __construct()
	{
		$data_model = array(
			'nIdBueno'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE),
			'nIdMalo'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE),
		);
		 
		parent::__construct('Ext_AUnificar', 'nIdUnificar', 'nIdUnificar', 'nIdUnificar', $data_model, TRUE);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Fondo.cTitulo cTituloBueno, Cat_Fondo.cAutores cAutoresBueno')
			->join('Cat_Fondo', "Cat_Fondo.nIdLibro = {$this->_tablename}.nIdBueno");

			$this->db->select('Cat_Fondo2.cTitulo cTituloMalo, Cat_Fondo2.cAutores cAutoresMalo')
			->join('Cat_Fondo Cat_Fondo2', "Cat_Fondo2.nIdLibro = {$this->_tablename}.nIdMalo");

			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_aunificar.php */
/* Location: ./system/application/models/ventas/M_aunificar.php */