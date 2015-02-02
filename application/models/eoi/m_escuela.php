<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	eoi
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Escuelas EOI
 *
 */
class M_Escuela extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Escuela
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'cPin'			=> array(DATA_MODEL_REQUIRED => TRUE),
			'cUsuario'		=> array(DATA_MODEL_REQUIRED => TRUE),		
			'nIdCaja'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/caja/search', 'cCaja')),
			'nIdSeccion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search', 'cSeccion')),
			'nIdSerie'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/serie/search', 'cSerie')),
			'nIdCliente'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'clientes/cliente/search', 'cCliente')),
			'fComision'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),		
			'fDescuento'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),		
			'cLibros'		=> array(),
		);

		parent::__construct('Ext_EOIS', 'nIdEOI', 'cDescripcion', 'cDescripcion', $data_model, TRUE);
		$this->_cache = TRUE;
	}
	
	/**
	 * Lista de albaranes que se restan de la cuenta de la escuela
	 * @param int $id Id de la escuela
	 * @return array 
	 */
	function albaranes($id = null)
	{
		$this->db->flush_cache();
		$this->db->select('Ext_EOISDepartamentos.cDescripcion')
		->select('Sus_SuscripcionesAlbaranes.nIdSuscripcion')
		->select('Cat_Fondo.cTitulo, Cat_Fondo.nIdLibro')
		->select($this->_date_field('Doc_AlbaranesSalida.dCreacion', 'dFecha'))
		->select('Doc_AlbaranesSalida.bMostrarWeb, Doc_AlbaranesSalida.nLibros, Doc_AlbaranesSalida.fTotal fImporte, Doc_AlbaranesSalida.nIdAlbaran')
		->from('Doc_AlbaranesSalida')
		->join('Ext_EOISDepartamentos', 'Doc_AlbaranesSalida.nIdCliente = Ext_EOISDepartamentos.nIdCliente')
		->join('Ext_EOIS', 'Ext_EOIS.nIdEOI = Ext_EOISDepartamentos.nIdEOI')
		->join('Sus_SuscripcionesAlbaranes', 'Sus_SuscripcionesAlbaranes.nIdAlbaran = Doc_AlbaranesSalida.nIdAlbaran', 'left')
		->join('Sus_Suscripciones', 'Sus_Suscripciones.nIdSuscripcion = Sus_SuscripcionesAlbaranes.nIdSuscripcion', 'left')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Sus_Suscripciones.nIdRevista', 'left')
		->join('Doc_Facturas', 'Doc_Facturas.nIdFactura = Doc_AlbaranesSalida.nIdFactura', 'left')
		->where('(Doc_AlbaranesSalida.nIdFactura IS NULL OR (Doc_AlbaranesSalida.nIdFactura IS NOT NULL AND Doc_Facturas.nNumero IS NULL))')
		->where('ISNULL(Doc_AlbaranesSalida.bMostrarWeb, 0) = 1')		
		->where('Doc_AlbaranesSalida.nIdEstado IN (2,3)');
		if (isset($id)) $this->db->where("Ext_EOIS.nIdEOI = {$id}");
		
		$query = $this->db->get(); 
		$data = $this->_get_results($query);
		
		return $data;		
	}

	/**
	 * Totales de los albaranes por escuelas a una fecha dada
	 * @param date $fecha Fecha límite de los albaranes
	 * @return array 
	 */
	function totales($fecha = null)
	{
		$this->db->flush_cache();
		$this->db->select_sum('Doc_AlbaranesSalida.fTotal', 'fImporte')
		->select('Ext_EOIS.nIdEOI, Ext_EOIS.cDescripcion')
		->from('Doc_AlbaranesSalida')
		->join('Ext_EOISDepartamentos', 'Doc_AlbaranesSalida.nIdCliente = Ext_EOISDepartamentos.nIdCliente')
		->join('Ext_EOIS', 'Ext_EOIS.nIdEOI = Ext_EOISDepartamentos.nIdEOI')
		->join('Doc_Facturas', 'Doc_Facturas.nIdFactura=Doc_AlbaranesSalida.nIdFactura', 'left')
		->where('(Doc_AlbaranesSalida.nIdFactura IS NULL OR (Doc_AlbaranesSalida.nIdFactura IS NOT NULL AND Doc_Facturas.nNumero IS NULL))')
		->where('Doc_AlbaranesSalida.nIdEstado IN (2,3)')
		->where('ISNULL(Doc_AlbaranesSalida.bMostrarWeb, 0) = 1')		
		->group_by('Ext_EOIS.nIdEOI, Ext_EOIS.cDescripcion')
		->order_by('Ext_EOIS.cDescripcion');

		if(isset($fecha) && $fecha != '')
		{
			$fecha = format_mssql_date($fecha);
			$this->db->where("Doc_AlbaranesSalida.dCreacion < " . $this->db->dateadd('d', 1, $fecha));
		}
		
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;		
		
	}
	
	/**
	 * Cálculo de las comisiones en un mes y año de todas las escuelas
	 * @param int $mes Mes
	 * @param int $year Año
	 * @return array 
	 */
	function comisiones($mes, $year)
	{
		$this->db->flush_cache();
		$this->db->select('Ext_EOIS.cDescripcion, Ext_EOIS.fComision')
		->select('Doc_LineasAlbaranesSalida.fDescuento')
		->select_sum('Doc_LineasAlbaranesSalida.nCantidad * ' .
			$this->db->numeric('Doc_LineasAlbaranesSalida.fPrecio * (100 - Doc_LineasAlbaranesSalida.fDescuento) / 100.0'),	 
			'fVenta')
		->select_sum('Doc_LineasAlbaranesSalida.nCantidad * ' .
			$this->db->numeric('Doc_LineasAlbaranesSalida.fPrecio * (Ext_EOIS.fComision - Doc_LineasAlbaranesSalida.fDescuento) / 100.0'),
			'fImporte')
		->from('Doc_Facturas')
		->join('Ext_EOIS', 'Ext_EOIS.nIdCaja = Doc_Facturas.nIdCaja')
		->join('Doc_AlbaranesSalida', 'Doc_AlbaranesSalida.nIdFactura = Doc_Facturas.nIdFactura')
		->join('Doc_LineasAlbaranesSalida', 'Doc_LineasAlbaranesSalida.nIdAlbaran = Doc_AlbaranesSalida.nIdAlbaran')
		->where('Doc_Facturas.nIdEstado IN (2,3)')
		->where("YEAR(Doc_Facturas.dFecha) = {$year}")
		->where("MONTH(Doc_Facturas.dFecha) = {$mes}")
		->group_by('Ext_EOIS.cDescripcion, Ext_EOIS.fComision')
		->group_by('Doc_LineasAlbaranesSalida.fDescuento')
		->order_by('Ext_EOIS.cDescripcion');
		
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;				
	}	

	/**
	 * Cálculo de las comisiones en un mes y año de todas las escuelas separado por idiomas
	 * @param int $mes Mes
	 * @param int $year Año
	 * @param int  $escuela Id de la escuela
	 * @return array 
	 */
	function comisiones2($mes, $year, $escuela)
	{
		$this->db->flush_cache();
		$this->db->select('Ext_EOIS.cDescripcion, Ext_EOIS.fComision')
		->select('Doc_LineasAlbaranesSalida.fDescuento')
		->select_sum('Doc_LineasAlbaranesSalida.nCantidad * ' .
			$this->db->numeric('Doc_LineasAlbaranesSalida.fPrecio * (100 - Doc_LineasAlbaranesSalida.fDescuento) / 100.0'),	 
			'fVenta')
		->select_sum('Doc_LineasAlbaranesSalida.nCantidad * ' .
			$this->db->numeric('Doc_LineasAlbaranesSalida.fPrecio * (Ext_EOIS.fComision - Doc_LineasAlbaranesSalida.fDescuento) / 100.0'),
			'fImporte')
		->select('Gen_Idiomas.cNombre, Ext_EOIS.nIdEOI')
		->from('Doc_Facturas')
		->join('Ext_EOIS', 'Ext_EOIS.nIdCaja = Doc_Facturas.nIdCaja')
		->join('Doc_AlbaranesSalida', 'Doc_AlbaranesSalida.nIdFactura = Doc_Facturas.nIdFactura')
		->join('Doc_LineasAlbaranesSalida', 'Doc_LineasAlbaranesSalida.nIdAlbaran = Doc_AlbaranesSalida.nIdAlbaran')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesSalida.nIdLibro')
		->join('Gen_Idiomas', 'Gen_Idiomas.nIdIdioma=Cat_Fondo.nIdIdioma', 'left')
		->where('Doc_Facturas.nIdEstado IN (2,3)')
		->where("YEAR(Doc_Facturas.dFecha) = {$year}")
		->where("MONTH(Doc_Facturas.dFecha) = {$mes}")
		->group_by('Ext_EOIS.cDescripcion, Ext_EOIS.fComision')
		->group_by('Doc_LineasAlbaranesSalida.fDescuento')
		->group_by('Gen_Idiomas.cNombre, Ext_EOIS.nIdEOI')
		->order_by('Ext_EOIS.cDescripcion, Gen_Idiomas.cNombre');

		if (is_numeric($escuela)) $this->db->where('Ext_EOIS.nIdEOI=' . $escuela);
		
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;				
	}	

	/**
	 * Cálculo de las comisiones en un mes y año de todas las escuelas separado por idiomas
	 * @param int $mes Mes
	 * @param int $year Año
	 * @param int  $escuela Id de la escuela
	 * @return array 
	 */
	function sin_idioma($mes, $year, $escuela)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo')
		->from('Doc_Facturas')
		->join('Ext_EOIS', 'Ext_EOIS.nIdCaja = Doc_Facturas.nIdCaja')
		->join('Doc_AlbaranesSalida', 'Doc_AlbaranesSalida.nIdFactura = Doc_Facturas.nIdFactura')
		->join('Doc_LineasAlbaranesSalida', 'Doc_LineasAlbaranesSalida.nIdAlbaran = Doc_AlbaranesSalida.nIdAlbaran')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesSalida.nIdLibro')
		->join('Gen_Idiomas', 'Gen_Idiomas.nIdIdioma=Cat_Fondo.nIdIdioma', 'left')
		->where('Doc_Facturas.nIdEstado IN (2,3)')
		->where("YEAR(Doc_Facturas.dFecha) = {$year}")
		->where("MONTH(Doc_Facturas.dFecha) = {$mes}")
		->where("Cat_Fondo.nIdIdioma IS NULL")
		->group_by('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo');

		if (is_numeric($escuela)) $this->db->where('Ext_EOIS.nIdEOI=' . $escuela);
		
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;				
	}	
}

/* End of file M_escuela.php */
/* Location: ./system/application/models/eoi/M_escuela.php */