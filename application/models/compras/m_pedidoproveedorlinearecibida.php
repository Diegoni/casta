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
 * Estados de una devolución
 *
 */
class M_pedidoproveedorlinearecibida extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_pedidoproveedorlinearecibida
	 */
	function __construct()
	{
		$data_model = array(
			'nIdLineaAlbaran'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE),		
			'nIdLineaPedido'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE),		
			'nCantidad' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE), 			
		);

		parent::__construct('Doc_LineasPedidosRecibidas', 'nIdLinea', 'nIdLinea', 'nIdLinea', $data_model);	
		#$this->_cache = TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Doc_LineasPedidoProveedor.nIdPedido nIdPedidoProveedor')
			->select('Ext_Concursos.cDescripcion cConcurso, Ext_Concursos.nIdConcurso')
			->select('Ext_Bibliotecas.cDescripcion cBiblioteca')
			->select('Ext_LineasPedidoConcurso.nIdLineaPedidoConcurso')
			->join('Doc_LineasPedidoProveedor', "Doc_LineasPedidoProveedor.nIdLinea = {$this->_tablename}.nIdLineaPedido", 'left')
			->join('Ext_LineasPedidoConcurso', 'Ext_LineasPedidoConcurso.nIdLineaPedidoProveedor=Doc_LineasPedidoProveedor.nIdLinea', 'left')
			->join('Ext_Bibliotecas', "Ext_Bibliotecas.nIdBiblioteca = Ext_LineasPedidoConcurso.nIdBiblioteca", 'left')
			->join('Ext_Concursos', "Ext_Concursos.nIdConcurso = Ext_Bibliotecas.nIdConcurso", 'left');

			return TRUE;
		}
		return FALSE;
	}

}

/* End of file M_pedidoproveedorlinearecibida.php */
/* Location: ./system/application/models/compras/M_pedidoproveedorlinearecibida.php */