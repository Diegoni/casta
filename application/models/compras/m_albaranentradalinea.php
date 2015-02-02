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

define('ALBARAN_ENTRADA_LINEA_STATUS_EN_PROCESO', 1);
define('ALBARAN_ENTRADA_LINEA_STATUS_CERRADO', 2);

define('DEFAULT_LINEA_ALBARAN_ENTRADA_STATUS', ALBARAN_ENTRADA_LINEA_STATUS_EN_PROCESO);

/**
 * Líneas de albarán de entrada
 *
 */
class M_albaranentradalinea extends MY_Model
{
	/**
	 * Constructor
	 * @return M_albaranentradalinea
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'nIdAlbaran'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/devolucion/search')),
			'nIdLibro'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
			'nCantidad' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE), 
			'nCantidadReal'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'fPrecio' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'fIVA' 					=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'fRecargo' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'fDescuento' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'nIdEstado'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => DEFAULT_LINEA_ALBARAN_ENTRADA_STATUS, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/estadopedidoproveedorlinea/search')),		
			'cRefProveedor' 		=> array(), 
			'cRefInterna'			=> array(),
			'fCoste'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'fGastos'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'fPrecioDivisa'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'fPrecioVenta'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'nCantidadDevuelta' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'nCantidadAsignada' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'nIdEstado'				=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_LINEA_ALBARAN_ENTRADA_STATUS, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/estadoalbaranentradalinea/search')),
		);

		parent::__construct('Doc_LineasAlbaranesEntrada', 'nIdLinea', 'nIdLinea', 'nIdLinea', $data_model, TRUE);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.cISBN, Cat_Fondo.nIdEditorial, Cat_Editoriales.cNombre cEditorial, Cat_Fondo.fPrecio fPrecioArticulo, Cat_Fondo.nIdTipo, Cat_Tipos.fIVA fIVAArticulo')
			->select('Cat_Fondo.cCUser cCUserArticulo')
			->select($this->_date_field('Cat_Fondo.dCreacion', 'dCreacionArticulo'))
			->select('Doc_EstadosLineaAlbaranEntrada.cDescripcion cEstado')
			->join('Cat_Fondo', "Cat_Fondo.nIdLibro = {$this->_tablename}.nIdLibro")
			->join('Doc_AlbaranesEntrada' , 'Doc_AlbaranesEntrada.nIdAlbaran=Doc_LineasAlbaranesEntrada.nIdAlbaran')
			->join('Cat_Tipos', "Cat_Fondo.nIdTipo = Cat_Tipos.nIdTipo")
			->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial', 'left')
			->join('Doc_EstadosLineaAlbaranEntrada', "Doc_EstadosLineaAlbaranEntrada.nIdEstado = {$this->_tablename}.nIdEstado", 'left');

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
			$importes = format_calculate_importes($data);
			$data = array_merge($data, $importes);
			$data['fPVPArticulo'] = format_add_iva($data['fPrecioArticulo'], $data['fIVAArticulo']);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Trigger llamado antes de actualizar los datos
	 * @param int $id Id del registro actualizado
	 * @param array $data Registro
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			if (isset($data['nCantidad'])) $data['nCantidadReal']  = $data['nCantidad'];
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Trigger llamado Antes de insertar los datos
	 * @param array $data Registro a insertar
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			if (isset($data['nCantidad'])) $data['nCantidadReal']  = $data['nCantidad'];
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 * @todo Actualizar el estado del pedido cuando se modifican las líneas
	 */
	protected function onBeforeDelete($id)
	{
		$sql = "DELETE FROM Prv_Proveedores_Fondo_Compras 
		WHERE nIdLinea={$id}";
		if (!$this->db->query($sql))
			return FALSE;
		return parent::onBeforeDelete($id);
	}
}

/* End of file M_albaranentradalinea.php */
/* Location: ./system/application/models/compras/M_albaranentradalinea.php */