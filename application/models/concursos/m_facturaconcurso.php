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
 * Facturas  Concurso
 *
 */
class M_facturaconcurso extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Costructor
	 * @return M_factura
	 */
	function __construct()
	{
		$data_model = array(
			'nNumero'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'nSerie'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdFacturaBibliopola'  => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'dCreacionReal'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
		);

		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.concursos.database');

		parent::__construct($this->prefix . 'Diba_Facturas', 'nIdFactura', 'nNumero', 'nNumero', $data_model);
		$this->_relations['albaranes'] = array (
			'ref'	=> 'concursos/m_albaran',
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdFactura');

		$this->_relations['albaranesagrupados'] = array (
			'ref'	=> 'concursos/m_albaranagrupado',
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdFactura');

		$this->_relations['cliente'] = array (
			'ref'	=> 'clientes/m_cliente',
			'fk'	=> 'nIdCliente');				
	}
}

/* End of file M_facturaconcurso.php */
/* Location: ./system/application/models/concursos/M_facturaconcurso.php */