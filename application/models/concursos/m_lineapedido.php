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
 * Lineas Pedido  Concurso
 *
 */
class M_lineapedido extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Costructor
	 * @return M_lineapedido
	 */
	function __construct()
	{
		$data_model = array(
			'nRefCli' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'cTitulo' 				=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_DEFAULT => TRUE),
			'cAutores' 				=> array(),
			'fPrecio' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),		
			'nIdPedido' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/pedido/search')),
			'cISBN' 				=> array(),
			'cEAN' 					=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
			'cISBNBase' 			=> array(),
		
			'nIdEditorial' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/editorialconcurso/search')),
			'nIdEstado' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/estado/search')),

			'nIdAlbaran' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/albaran/search')),
			'nIdFactura' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/factura/search')),

			'nIdCambioLibro' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),

			'nIdProveedor' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/proveedorconcurso/search')),
		
			'nCaja' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'bNuevo' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'nAlternativas' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'fDescuento' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),		
			'cEdicion' 				=> array(),		
			'nIdInterno' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),				
		);

		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.concursos.database');

		parent::__construct($this->prefix . 'Diba_LineasPedido', 'nIdLibro', 'nIdLibro', 'nIdLibro', $data_model, TRUE);
	}
	
		/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert($data)
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			$this->_check_isbn($data);
		}
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			$this->_check_isbn($data);
		}
		return TRUE;
	}

	/**
	 * Comprueba el ISBN y completa los datos
	 * @param array $data Registro a comprobar
	 */
	private function _check_isbn(&$data)
	{
		$isbn = null;
		if (isset($data['cISBN']))
		{
			$this->load->library('ISBNEAN');
			$isbn = $this->isbnean->to_isbn($data['cISBN'], TRUE);
		}
		elseif (isset($data['cEAN']))
		{
			$this->load->library('ISBNEAN');
			$isbn = $this->isbnean->to_isbn($data['cEAN'], TRUE);
		}
		if (isset($isbn))
		{
			$data['cISBN'] = $isbn['isbn13'];
			$data['cISBNBase'] = $this->isbnean->clean_code($isbn['isbn13']);
			$data['cEAN'] = $this->isbnean->to_ean($isbn['isbn13']);
		}
		//print '<pre>'; print_r($data); print '</pre>';
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select("{$this->prefix}Diba_Pedidos.nBiblioteca, {$this->prefix}Diba_Pedidos.cPedido")
			->select("{$this->prefix}Diba_Salas.cSala")
			->select("{$this->prefix}Diba_Bibliotecas.cBiblioteca")
			->select("{$this->prefix}Diba_Estados.cEstado")
			->join($this->prefix . 'Diba_Pedidos', "{$this->prefix}Diba_Pedidos.nIdPedido = {$this->_tablename}.nIdPedido")
			->join($this->prefix . 'Diba_Estados', "{$this->prefix}Diba_Estados.nIdEstado = {$this->_tablename}.nIdEstado")
			->join($this->prefix . 'Diba_Salas', "{$this->prefix}Diba_Pedidos.nSala = {$this->prefix}Diba_Salas.nIdSala", 'left')
			->join($this->prefix . 'Diba_Bibliotecas', "{$this->prefix}Diba_Pedidos.nBiblioteca = {$this->prefix}Diba_Bibliotecas.nIdBiblioteca", 'left')
			;

			return TRUE;
		}
		return FALSE;
	}

	
}

/* End of file M_lineapedido.php */
/* Location: ./system/application/models/concursos/M_lineapedido.php */