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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tarifas de un cliente
 *
 */
class M_clientetarifa extends MY_Model
{
	/**
	 * Constructor
	 * @return M_clientetarifa
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'nIdCliente'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE),
			'nIdTipoLibro'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/tipolibro/search', 'cTipo')),
			'nIdTipoTarifa'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/tipotarifa/search', 'cTipoTarifa')),
		);

		parent::__construct('Cli_Clientes_Tarifas', 'nIdClienteTarifa', 'nIdClienteTarifa', 'nIdClienteTarifa', $data_model);
	}
			
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Tipos.cDescripcion cTipo, Cat_TiposTarifa.cDescripcion cTipoTarifa');
			$this->db->join('Cat_TiposTarifa', 'Cat_TiposTarifa.nIdTipoTarifa = Cli_Clientes_Tarifas.nIdTipoTarifa');
			$this->db->join('Cat_Tipos', 'Cat_Tipos.nIdTipo = Cli_Clientes_Tarifas.nIdTipoLibro');
			
			return TRUE;
		}
		return FALSE;
	}	
}

/* End of file M_clientetarifa.php */
/* Location: ./system/application/models/clientes/M_clientetarifa.php */
