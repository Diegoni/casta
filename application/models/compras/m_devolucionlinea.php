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

define('DEVOLUCION_LINEA_STATUS_EN_PROCESO', 1);
define('DEVOLUCION_LINEA_STATUS_CERRADA', 2);
define('DEVOLUCION_LINEA_STATUS_ENTREGADA', 3);

define('DEFAULT_DEVOLUCION_LINEA_STATUS', DEVOLUCION_LINEA_STATUS_EN_PROCESO);

/**
 * Líneas de devolución
 *
 */
class M_DevolucionLinea extends MY_Model
{
	/**
	 * Constructor
	 * @return M_DevolucionLinea
	 */
	function __construct()
	{
		$data_model = array(
			'nIdDevolucion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/devolucion/search')),
			'nIdSeccion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search')),		
			'nIdLibro'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
			'nCantidad' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE), 
			'fPrecio' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fPrecioDivisa' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fIVA' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fRecargo' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fDescuento' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'nIdEstado'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => DEFAULT_DEVOLUCION_LINEA_STATUS, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/estadodevolucionlinea/search')),		
			'cRefProveedor' => array(), 
			'cRefInterna'	=> array(),
			'nRechazadas' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 		
			'fCoste' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'nIdLineaDevolucion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/devolucion/search')),		
			'nIdAlbaran'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/albaranentrada/search')),		
			'nIdLineaAlbaran'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/albaranentrada/search')),		
		);

		parent::__construct('Doc_LineasDevolucion', 'nIdLinea', 'nIdLinea', 'nIdLinea', $data_model);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.cISBN, Cat_Secciones.cNombre cSeccion, Cat_Editoriales.cNombre cEditorial')
			->select('Doc_EstadosLineaDevolucion.cDescripcion cEstado')
			->select('Doc_AlbaranesEntrada.cNumeroAlbaran cAlbaranProveedor, Doc_AlbaranesEntrada.nIdAlbaran')
			->select($this->_date_field('Doc_AlbaranesEntrada.dFecha', 'dFecha'))
			->select('d2.nIdDevolucion nIdDevolucionRechazada')
			->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Doc_LineasDevolucion.nIdLibro')
			->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion = Doc_LineasDevolucion.nIdSeccion')
			->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial', 'left')
			->join('Doc_EstadosLineaDevolucion', 'Doc_EstadosLineaDevolucion.nIdEstado = Doc_LineasDevolucion.nIdEstado', 'left')
			->join('Doc_LineasAlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdLinea = Doc_LineasDevolucion.nIdLineaAlbaran', 'left')
			->join('Doc_AlbaranesEntrada', 'Doc_AlbaranesEntrada.nIdAlbaran = Doc_LineasAlbaranesEntrada.nIdAlbaran', 'left')
			->join('Doc_LineasDevolucion d2', 'Doc_LineasDevolucion.nIdLineaDevolucion = d2.nIdLinea', 'left');
			
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
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 */
	protected function onBeforeDelete($id) 
	{
		$old = $this->load($id);
		if ($old['nIdEstado'] != DEVOLUCION_LINEA_STATUS_EN_PROCESO)
		{
			$this->_set_error_message($this->lang->line('devolucion-delete-error-state'));
			return FALSE;
		}

		return parent::onBeforeDelete($id);
	}

}

/* End of file M_DevolucionLinea.php */
/* Location: ./system/application/models/compras/M_DevolucionLinea.php */
