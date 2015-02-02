<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	suscripciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Suscripciones
 *
 */
class M_suscripcion extends MY_Model
{
	/**
	 * Costructor
	 * @return M_suscripcion
	 */
	function __construct()
	{
		$data_model = array(
			'nIdCliente'			=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'clientes/cliente/search', 'cCliente')),
			'nIdRevista'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/articulo/revista', 'cTitulo')),
			'cRevista' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_ALIAS, DATA_MODEL_NO_GRID => FALSE, DATA_MODEL_SEARCH => TRUE),
			'cCliente' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_ALIAS, DATA_MODEL_NO_GRID => FALSE, DATA_MODEL_SEARCH => TRUE),
			'cProveedor' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_ALIAS, DATA_MODEL_NO_GRID => FALSE, DATA_MODEL_SEARCH => TRUE),
			'dInicio'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE, DATA_MODEL_REQUIRED => TRUE),
			'dRenovacion'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'nIdTipoEnvio'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'suscripciones/tipoenvio/search')),
			'nIdDireccionEnvio'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_NO_GRID => TRUE),
			'nIdDireccionFactura'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_NO_GRID => TRUE),
			'nDuracion'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 1),
			'cRefCliente' 			=> array(), 
			'cRefProveedor'			=> array(),
			'nEjemplares'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 1),
			'fDescuento'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'fPrecio' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fPrecioCompra' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'nIdUltimaFactura'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdUltimaEntrada'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nEntradas'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),
			'nFacturas'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),
			'bNoFacturable' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
			'nIdPedidoProveedor'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'bActiva' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'nIdLineaPedidoProveedor' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'dPrimerInicio'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),		
			'cDirEnv' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_ALIAS, DATA_MODEL_SEARCH => TRUE),
			'cDirFac' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_ALIAS, DATA_MODEL_SEARCH => TRUE),
			'cIdShipping'			=> array(),
		);
		
		$this->_relations['revista'] = array (
			'ref'	=> 'catalogo/m_revista',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdRevista');
			
		$this->_relations['articulo'] = array (
			'ref'	=> 'catalogo/m_articulo',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdRevista');

		$this->_relations['cliente'] = array (
			'ref'	=> 'clientes/m_cliente',
			'fk'	=> 'nIdCliente');

		$this->_relations['direccionenvio'] = array (
			'ref'	=> 'clientes/m_direccioncliente',
			'fk'	=> 'nIdDireccionEnvio');
							
		$this->_relations['direccionfactura'] = array (
			'ref'	=> 'clientes/m_direccioncliente',
			'fk'	=> 'nIdDireccionFactura');				

		parent::__construct('Sus_Suscripciones', 'nIdSuscripcion', 'nIdSuscripcion', 'nIdSuscripcion', $data_model, TRUE);

		$this->_alias = array(
				'cRevista' 		=> array('Cat_Fondo.cTitulo', DATA_MODEL_TYPE_STRING),
				'cCliente' 		=> array($this->db->concat(array('Cli_Clientes.cEmpresa', 'Cli_Clientes.cNombre', 'Cli_Clientes.cApellido'))),
				'cProveedor' 	=> array($this->db->concat(array('Prv_Proveedores.cEmpresa', 'Prv_Proveedores.cNombre', 'Prv_Proveedores.cApellido'))),
		);
			
		//$this->_cache = TRUE;
	}

	/**
	 * Informe del estado de las suscripciones
	 * @param bool $obras TRUE: Mostrar solo obras, FALSE: mostrar todo
	 * @param bool $activas TRUE: Mostrar solo suscricpiones activas, FALSE: Mostrar todo
	 * @return array
	 */
	function estado($obras = null, $activas = null)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.cTitulo, Cat_Fondo.nIdLibro, Sus_Suscripciones.dRenovacion')
		->select('Sus_Suscripciones.nIdSuscripcion, Sus_Suscripciones.fPrecio, Sus_Suscripciones.fPrecioCompra fCoste')
		->select('Sus_Suscripciones.bActiva, Cat_Revistas.nIdTipoSuscripcion')
		->select('Cat_TiposSuscripcionRevista.cDescripcion cTipoSuscripcion')
		->select('Cli_Clientes.*')
		->from('Sus_Suscripciones')
		->join('Cat_Fondo', 'Sus_Suscripciones.nIdRevista = Cat_Fondo.nIdLibro')
		->join('Cli_Clientes', 'Cli_Clientes.nIdCliente = Sus_Suscripciones.nIdCliente')
		->join('Cat_Revistas', 'Cat_Revistas.nIdLibro = Cat_Fondo.nIdLibro', 'left')
		->join('Cat_TiposSuscripcionRevista', 'Cat_Revistas.nIdTipoSuscripcion = Cat_TiposSuscripcionRevista.nIdTipoSuscripcion', 'left');
		if ($obras) $this->db->where('ISNULL(Cat_Revistas.nIdTipoSuscripcion,0) <> 5');
		if ($activas) $this->db->where('Sus_Suscripciones.bActiva = 1');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Informe de las suscripciones anticipadas
	 * @param bool $obras TRUE: Mostrar solo obras, FALSE: mostrar todo
	 * @param bool $activas TRUE: Mostrar solo suscricpiones activas, FALSE: Mostrar todo
	 * @return array
	 */
	function anticipadas($obras = null, $activas = null)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.cTitulo, Cat_Fondo.nIdLibro')
		->select($this->_date_field('Sus_Suscripciones.dRenovacion', 'dRenovacion'))
		->select('Sus_Suscripciones.nIdSuscripcion, Sus_Suscripciones.fPrecio, Sus_Suscripciones.fPrecioCompra fCoste')
		->select('Sus_Suscripciones.bActiva, Cat_Revistas.nIdTipoSuscripcion')
		->select('Cat_TiposSuscripcionRevista.cDescripcion cTipoSuscripcion')
		->select('Cli_Clientes.*')
		->select('Sus_Suscripciones.nEntradas, Sus_Suscripciones.nFacturas')
		->select('Sus_Suscripciones.nIdUltimaFactura')
		->select('Sus_Suscripciones.nIdUltimaEntrada')
		->select($this->_date_field('Doc_Facturas.dFecha', 'dFechaFactura'))
		->select($this->_date_field('Doc_AlbaranesEntrada.dCierre', 'dFechaAlbaranEntrada'))
		->from('Sus_Suscripciones')
		->join('Cat_Fondo', 'Sus_Suscripciones.nIdRevista = Cat_Fondo.nIdLibro')
		->join('Cli_Clientes', 'Cli_Clientes.nIdCliente = Sus_Suscripciones.nIdCliente')
		->join('Cat_Revistas', 'Cat_Revistas.nIdLibro = Cat_Fondo.nIdLibro', 'left')
		->join('Cat_TiposSuscripcionRevista', 'Cat_Revistas.nIdTipoSuscripcion = Cat_TiposSuscripcionRevista.nIdTipoSuscripcion', 'left')
		->join('Doc_AlbaranesEntrada', 'Sus_Suscripciones.nIdUltimaEntrada=Doc_AlbaranesEntrada.nIdAlbaran', 'left')
		->join('Doc_Facturas', 'Sus_Suscripciones.nIdUltimaFactura=Doc_Facturas.nIdFactura', 'left')
		->where('Sus_Suscripciones.nEntradas <> Sus_Suscripciones.nFacturas')
		->where('Sus_Suscripciones.bNoFacturable <> 1')
		->order_by('Cli_Clientes.cEmpresa, Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cat_Fondo.cTitulo');
		
		if ($obras) $this->db->where('ISNULL(Cat_Revistas.nIdTipoSuscripcion,0) <> 5');
		if ($activas) $this->db->where('Sus_Suscripciones.bActiva = 1');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Coste de una suscripción según la última compra
	 * @param int $id Id de la suscripción
	 * @return float, NULL si no hay última compra
	 */
	function coste($id)
	{
		$this->db->flush_cache();

		$this->db->select('Doc_LineasAlbaranesEntrada.fPrecio * ( 1 - Doc_LineasAlbaranesEntrada.fDescuento / 100.0) fCoste')
		->from('Sus_PedidosSuscripcion')
		->join('Doc_PedidosProveedor', 'Sus_PedidosSuscripcion.nIdPedido = Doc_PedidosProveedor.nIdPedido')
		->join('Doc_LineasPedidoProveedor', 'Doc_LineasPedidoProveedor.nIdPedido = Doc_PedidosProveedor.nIdPedido')
		->join('Doc_LineasPedidosRecibidas', 'Doc_LineasPedidosRecibidas.nIdLineaPedido = Doc_LineasPedidoProveedor.nIdLinea')
		->join('Doc_LineasAlbaranesEntrada', 'Doc_LineasPedidosRecibidas.nIdLineaAlbaran = Doc_LineasAlbaranesEntrada.nIdLinea')
		->where("Sus_PedidosSuscripcion.nIdSuscripcion = {$id}")
		->where('Doc_LineasAlbaranesEntrada.nCantidad > 0')
		->order_by("Doc_LineasAlbaranesEntrada.dCreacion", "ASC")
		->limit(1);
		#echo 'coste' ;die();
		$query = $this->db->get();
		$data = $this->_get_results($query);
		if (count($data) > 0)
		{
			return $data[0]['fCoste'];
		}
		return null;
	}

	/**
	 * Renueva la suscripción con la referencia indicada
	 * @param int $id Id de la suscripción
	 * @param string $ref Referencia
	 * @return bool
	 */
	function renovar($id, $ref)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_PeriodosSuscripcion.nMeses')
		->select($this->_date_field('Sus_Suscripciones.dRenovacion', 'dRenovacion'))
		->from('Sus_Suscripciones')
		->join('Sus_AvisosRenovacion', 'Sus_AvisosRenovacion.nIdSuscripcion = Sus_Suscripciones.nIdSuscripcion')
		->join('Cat_Revistas', 'Cat_Revistas.nIdLibro = Sus_Suscripciones.nIdRevista', 'left')
		->join('Cat_PeriodosSuscripcion', 'Cat_PeriodosSuscripcion.nIdPeriodo = Cat_Revistas.nIdPeriodo', 'left')
		->where("Sus_Suscripciones.nIdSuscripcion={$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$meses = (count($data) > 0 && isset($data[0]['nMeses']))?$meses = $data[0]['nMeses']:12;
		$renovacion = isset($data[0]['dRenovacion'])?$data[0]['dRenovacion']:time();
		#var_dump($renovacion, dateadd($renovacion, 0, $meses), $meses); die();
		$upd = array(
			'dRenovacion' 	=> dateadd($renovacion, 0, $meses),
			'cRefCliente'	=> $ref!=''?$this->db->escape($ref):null,
			'dInicio' 		=> $renovacion
		);

		return $this->update($id, $upd);
	}

	/**
	 * Cancela la suscripción
	 * @param int $id Id de la suscripción
	 * @return bool
	 */
	function cancelar($id)
	{
		return $this->update($id, array('bActiva' => FALSE));		
	}

	/**
	 * Activa la suscripción
	 * @param int $id Id de la suscripción
	 * @return bool
	 */
	function activar($id)
	{
		return $this->update($id, array('bActiva' => TRUE));		
	}

	/**
	 * Obtiene los pedidos de proveedor y albaranes de entrada de una suscripción
	 * @param int $id Id de la suscripción
	 * @param bool $last TRUE: Devuelve la última
	 * @param bool $entrada TRUE: Solo los pedidos que tienen entrada de mercancía
	 * @param bool $pendientes TRUE: Solo los pedidos que están pendientes
	 * @return array
	 */
	function get_pedidosproveedor($id, $last = FALSE, $entrada = FALSE, $pendientes = FALSE)
	{
		$this->db->flush_cache();
		$this->db->select('p.nIdPedido,
		ep.cDescripcion cEstadoPedido,
		elp.cDescripcion cEstadoLinea,
		lp.nIdLinea,
		lp.nIdEstado,
		lp.fPrecio,
		ae.cNumeroAlbaran cNumeroAlbaran,
		ae.nIdAlbaran,
		lae.cRefInterna,
		ISNULL(lae.nCantidad, lp.nCantidad) nCantidad,
		ISNULL(lae.fPrecio, lp.fPrecio) fPrecio,
		ISNULL(lae.fIVA, lp.fIVA) fIVA,
		ISNULL(lae.fDescuento, lp.fDescuento) fDescuento')
		->select($this->_date_field('p.dCreacion' , 'dCreacion'))
		->select($this->_date_field('p.dFechaEntrega' , 'dFechaEntrega'))
		->select($this->_date_field('ae.dFecha' , 'dFecha'))
		->select($this->_date_field('ae.dCreacion' , 'dCreacionAlbaran'))		
		->from('Sus_PedidosSuscripcion s')
		->join('Doc_PedidosProveedor p', 'p.nIdPedido = s.nIdPedido')
		->join('Sus_Suscripciones s2', 's2.nIdSuscripcion = s.nIdSuscripcion')
		->join('Doc_LineasPedidoProveedor lp', 's.nIdPedido = lp.nIdPedido AND lp.nIdLibro = s2.nIdRevista')
		->join('Doc_EstadosPedidoProveedor ep', 'p.nIdEstado = ep.nIdEstado')
		->join('Doc_EstadosLineaPedidoProveedor elp', 'lp.nIdEstado = elp.nIdEstado')
		->join('Doc_LineasPedidosRecibidas lpr', 'lpr.nIdLineaPedido = lp.nIdLinea', ($entrada)?null:'left')
		->join('Doc_LineasAlbaranesEntrada lae', 'lae.nIdLinea = lpr.nIdLineaAlbaran', ($entrada)?null:'left')
		->join('Doc_AlbaranesEntrada ae', 'ae.nIdAlbaran = lae.nIdAlbaran', ($entrada)?null:'left')
		->where("s.nIdSuscripcion = {$id}")
		->order_by("ISNULL(p.dFechaEntrega,p.dCreacion) DESC, p.dCreacion DESC");
		if ($last)
			$this->db->limit(1);

		if ($pendientes)
			$this->db->where('lp.nIdEstado IN (1, 4, 2)');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		if (count($data) > 0)
		{
			$this->obj->load->model('compras/m_albaranentrada');
			foreach ($data as $k => $v)
			{
				$data[$k]['Cargos'] = (isset($v['nIdAlbaran']))?$this->obj->m_albaranentrada->get_cargos($v['nIdAlbaran']):null;
				$data[$k] = array_merge(format_calculate_importes($data[$k]), $data[$k]);
			}
		}
		return $data;
	}
	
	/**
	 * Obtiene las facturas de una suscripción
	 * @param int $id Id de la suscripción
	 * @param bool $last TRUE: Devuelve la última
	 * @param bool $factura TRUE: Solo los albaranes que tienen factura
	 * @return array
	 */
	function get_facturas($id, $last = FALSE, $factura = FALSE)
	{
		$this->db->flush_cache();
		$this->db->select('a.nIdAlbaran,
		a.nIdFactura,
		f.nNumero cFactura, 
		sr.nNumero cNumeroSerie,
		al.cRefInterna,
		al.nCantidad,
		al.fPrecio,
		al.fIVA,
		al.fDescuento')
		->select($this->_date_field('a.dCreacion' , 'dCreacion'))
		->select($this->_date_field('f.dFecha' , 'dFecha'))
		->from('Sus_SuscripcionesAlbaranes s')
		->join('Sus_Suscripciones s2', 's.nIdSuscripcion = s2.nIdSuscripcion')
		->join('Doc_AlbaranesSalida a', 'a.nIdAlbaran = s.nIdAlbaran')
		->join('Doc_LineasAlbaranesSalida al', 'a.nIdAlbaran = al.nIdAlbaran')
		->join('Doc_Facturas f', 'f.nIdFactura = a.nIdFactura', ($factura)?null:'left')
		#->join('Doc_AlbaranesSalida a2', 'f.nIdFactura = a2.nIdFactura', 'left')
		#->join('Doc_LineasAlbaranesSalida al', 'a2.nIdAlbaran = al.nIdAlbaran', 'left')
		->join('Doc_Series sr', 'sr.nIdSerie = f.nIdSerie', ($factura)?null:'left')
		->where("a.nIdEstado=2  AND s.nIdSuscripcion = {$id}")
		#->where('(al.nIdLibro = s2.nIdRevista AND a.nIdFactura IS NOT NULL OR a.nIdFactura IS NULL)')
		->where('(al.nIdLibro = s2.nIdRevista)')
		->order_by("ISNULL(dFecha, a.dCreacion) DESC");
		if ($last)
			$this->db->limit(1);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		#echo '<pre>'; echo array_pop($this->db->queries); die();
		foreach ($data as $k => $v)
		{
			$data[$k] = array_merge(format_calculate_importes($data[$k]), $data[$k]);
		}

		return $data;
	}

	/**
	 * Obtiene el último albarán sin facturar
	 * @param int $id Id de la suscripción
	 * @return array
	 */
	function get_albaran_sin_facturar($id)
	{
		$this->db->flush_cache();
		$this->db->select('a.nIdAlbaran,
		al.cRefInterna,
		al.nCantidad,
		al.fCoste,
		al.fPrecio,
		al.fDescuento,
		al.Total')
		->select($this->_date_field('a.dCreacion' , 'dCreacion'))
		->from('Sus_SuscripcionesAlbaranes s')
		->join('Sus_Suscripciones s2', 's.nIdSuscripcion = s2.nIdSuscripcion')
		->join('Doc_AlbaranesSalida a', 'a.nIdAlbaran = s.nIdAlbaran')
		->join('Doc_LineasAlbaranesSalida al', 'a.nIdAlbaran = al.nIdAlbaran AND al.nIdLibro = s2.nIdRevista')
		->where('a.nIdEstado=2')
		->where("s.nIdSuscripcion = {$id}")
		->where('a.nIdFactura IS NULL')
		->where('a.bNoFacturable <> 1')
		->order_by("a.dCreacion DESC")
		->limit(1);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return isset($data[0])?$data[0]:null;
	}
	
	/**
	 * Obtiene las reclamaciones de una suscripción
	 * @param int $id Id de la suscripción
	 * @return array
	 */
	function get_reclamaciones($id)
	{
		$this->db->flush_cache();
		$this->db->select('a.nIdAlbaran,
		a.nIdFactura,
		f.nNumero cFactura, 
		sr.nNumero cNumeroSerie,
		AL.cRefInterna,
		al.nCantidad,
		al.fPrecio,
		al.fDescuento,
		al.Total')
		->select($this->_date_field('a.dCreacion' , 'dCreacion'))
		->select($this->_date_field('f.dFecha' , 'dFecha'))
		->from('Sus_SuscripcionesAlbaranes s')
		->join('Sus_Suscripciones s2', 's.nIdSuscripcion = s2.nIdSuscripcion')
		->join('Doc_AlbaranesSalida a', 'a.nIdAlbaran = s.nIdAlbaran')
		->join('Doc_LineasAlbaranesSalida al', 'a.nIdAlbaran = al.nIdAlbaran AND al.nIdLibro = s2.nIdRevista')
		->join('Doc_Facturas f', 'f.nIdFactura = a.nIdFactura', 'left')
		->join('Doc_Series sr', 'sr.nIdSerie = f.nIdSerie', 'left')
		->where("a.nIdEstado = 2 AND s.nIdSuscripcion = {$id}")
		->group_by('')
		->order_by("ISNULL(dFecha, a.dCreacion) DESC");
		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}

	/**
	 * Obtiene los avisos de renovación de una suscripción
	 * @param int $id Id de la suscripción
	 * @param bool $last Solo la última
	 * @param bool $pendientes Solo las pendientes
	 * @return array
	 */
	function get_avisosrenovacion($id, $last = FALSE, $pendientes = FALSE)
	{
		$this->db->flush_cache();
		$this->db->select('Sus_GruposAvisos.cDescripcion cCampana')
		->select('Gen_MediosRenovacion.cDescripcion cMedio')
		->select('Sus_AvisosRenovacion.bAceptada, Sus_AvisosRenovacion.cPersona, Sus_AvisosRenovacion.nIdAvisoRenovacion')
		->select($this->_date_field('Sus_AvisosRenovacion.dEnviada' , 'dEnviada'))
		->select($this->_date_field('Sus_AvisosRenovacion.dGestionada' , 'dGestionada'))
		->select($this->_date_field('Sus_AvisosRenovacion.dFecha' , 'dFecha'))
		->from('Sus_GruposAvisos')
		->join('Sus_AvisosRenovacion', 'Sus_GruposAvisos.nIdGrupoAviso = Sus_AvisosRenovacion.nIdGrupoAviso AND Sus_AvisosRenovacion.nIdSuscripcion=' . $id, 'left')
		->join('Gen_MediosRenovacion', 'Gen_MediosRenovacion.nIdMedioRenovacion = Sus_AvisosRenovacion.nIdMedioRenovacion', 'left')
		->order_by("Sus_GruposAvisos.cDescripcion DESC");

		if ($last)
			$this->db->limit(1);

		if ($pendientes)
			$this->db->where('Sus_AvisosRenovacion.bAceptada=0');

		
		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}

	/**
	 * Realiza las búsqueda de suscripiones según el filtro indicado
	 * @param int $revista Id de la revista
	 * @param int $cliente Id del cliente
	 * @param int $proveedor Id del proveedor
	 * @param bool $obras TRUE: Muestra solo las obras
	 * @param bool $activas TRUE: Muestra solo la activas
	 * 
	 * @return array
	 */
	function buscar($revista = null, $cliente = null, $proveedor = null, $obras = null, $activas = null, $facturable = null)
	{
		$this->db->flush_cache();
		$this->db->select('Sus_Suscripciones.nIdSuscripcion')
		->select('Cat_TiposSuscripcionRevista.cDescripcion cTipoSuscripcion')
		->from('Sus_Suscripciones')
		->join('Cat_Fondo', 'Sus_Suscripciones.nIdRevista = Cat_Fondo.nIdLibro')
		->join('Cat_Editoriales' ,'Cat_Editoriales.nIdEditorial = Cat_Fondo.nIdEditorial', 'left')
		->join('Cat_Revistas', 'Cat_Revistas.nIdLibro = Cat_Fondo.nIdLibro', 'left')
		->join('Cat_TiposSuscripcionRevista', 'Cat_Revistas.nIdTipoSuscripcion = Cat_TiposSuscripcionRevista.nIdTipoSuscripcion', 'left')
		->order_by('Sus_Suscripciones.nIdSuscripcion');
		if ($obras) $this->db->where('ISNULL(Cat_Revistas.nIdTipoSuscripcion,0) <> 5');
		if ($activas) $this->db->where('Sus_Suscripciones.bActiva = 1');
		if (!empty($cliente)) $this->db->where('Sus_Suscripciones.nIdCliente = ' . $cliente);
		if (!empty($revista)) $this->db->where('Sus_Suscripciones.nIdRevista = ' . $revista);
		if (!empty($proveedor)) $this->db->where("(Cat_Fondo.nIdProveedor = {$proveedor} OR Cat_Fondo.nIdProveedor IS NULL AND Cat_Editoriales.nIdProveedor={$proveedor})");
		if ($facturable) $this->db->where('ISNULL(Sus_Suscripciones.bNoFacturable, 0) = 0');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Precios de una suscripción
	 * @param int $id Id de la suscripción
	 * @return array
	 */
	function get_precios($id)
	{
		$this->db->flush_cache();
		$this->db->select('fPrecioAntiguo, fPrecioNuevo, cCUser')
		->select($this->_date_field('dCambio', 'dCambio'))
		->from('Sus_CambiosPrecio')
		->where("nIdSuscripcion = {$id}")
		->order_by('dCambio DESC');
		$query = $this->db->get();
		return $this->_get_results($query);
	}

	/**
	 * clientes de una suscripción
	 * @param int $id Id de la suscripción
	 * @return array
	 */
	function get_clientes($id)
	{
		$this->db->flush_cache();
		$this->db->select('nIdClienteAntiguo, nIdClienteNuevo, Sus_CambiosCliente.cCUser')
		->select($this->_date_field('dCambio', 'dCambio'))
		->select('c1.cNombre cNombre1, c1.cApellido cApellido1, c1.cEmpresa cEmpresa1')
		->select('c2.cNombre cNombre2, c2.cApellido cApellido2, c2.cEmpresa cEmpresa2')
		->from('Sus_CambiosCliente')
		->join('Cli_Clientes c1', 'c1.nIdCliente=nIdClienteAntiguo')
		->join('Cli_Clientes c2', 'c2.nIdCliente=nIdClienteNuevo')
		->where("nIdSuscripcion = {$id}")
		->order_by('dCambio DESC');
		$query = $this->db->get();
		return $this->_get_results($query);
	}
	

	/**
	 * Crea un nuevo pedido a proveedor de una suscripción
	 * @param int @Id Id de la suscripción
	 * @return FALSE: error, int Id del nuevo pedido
	 */
	function crear_pedido($id)
	{
		$sus = $this->load($id);

		$this->obj->load->model('catalogo/m_articulo');
		$art = $this->obj->m_articulo->load($sus['nIdRevista']);

		# Crea el pedido
		$pedido['nIdProveedor'] = $this->obj->m_articulo->get_proveedor_habitual($art);
		$pedido['cRefProveedor'] = $sus['cRefProveedor'];
		$pedido['cRefInterna'] = $sus['cRefCliente'];
		$pedido['bRevistas'] = TRUE;
		$pedido['lineas'][] = array(
			'nIdSeccion' 	=> $this->config->item('bp.suscripciones.seccion'),
			'nIdLibro' 		=> $sus['nIdRevista'],
			'fPrecio' 		=> $art['fPrecio'],
			'fIVA' 			=> $art['fIVA'],
			'fDescuento' 	=> $this->obj->m_articulo->get_descuento($sus['nIdRevista'], $pedido['nIdProveedor']),
			'nCantidad' 	=> $sus['nEjemplares'],
		);

		$this->obj->load->model('compras/m_pedidoproveedor');

		$this->db->trans_begin();

		$id_n = $this->obj->m_pedidoproveedor->insert($pedido);
		if ($id_n < 0)
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->obj->m_pedidoproveedor->error_message());				
			return FALSE;
		}
		$ped = $this->obj->m_pedidoproveedor->load($id_n, 'lineas');
		$id_l = $ped['lineas'][0]['nIdLinea'];
		
		# Vincula el pedido a la suscripción
		$this->load->model('suscripciones/m_pedidosuscripcion');
		$data['nIdSuscripcion'] = $id;
		$data['nIdPedido'] = $id_n;
		$data['nIdLineaPedido'] = $id_l;
		if ($this->obj->m_pedidosuscripcion->insert($data) < 0)
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->obj->m_pedidosuscripcion->error_message());								
			return FALSE;
		}
		
		#Actualiza la suscripción
		$data = array(
			'nIdLineaPedidoProveedor' => $id_l,
			'nIdPedidoProveedor' => $id_n);
		if (!$this->update($id, $data))
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		#OK
		$this->db->trans_commit();
		return $id_n;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect())
		{
			$fields = 'Cat_Editoriales.cNombre cEditorial';
			$fields .= ',Cat_Tipos.fIVA, Cat_Tipos.fRecargo, Cat_EstadosLibro.cDescripcion cEstado, Cat_Editoriales.cNombre cEditorial';
			$fields .= ',Cat_TiposEnvio.cDescripcion cTipoEnvio';
			$fields .= ',Cat_Editoriales.nIdProveedor nIdProveedor2';
			$fields .= ',Cat_Fondo.nIdProveedor, Cat_Fondo.cISBN';
			$fields .= ',Cat_Fondo.fPrecio fPrecioRevista, Cat_Fondo.cTitulo, Cat_Fondo.cTitulo cRevista';

			$this->db->select($fields)
			->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Sus_Suscripciones.nIdRevista', 'left')
			->join('Cat_TiposEnvio', 'Cat_TiposEnvio.nIdTipoEnvio = Sus_Suscripciones.nIdTipoEnvio', 'left')
			->join('Cat_Tipos', 'Cat_Tipos.nIdTipo = Cat_Fondo.nIdTipo', 'left')
			->join('Cat_EstadosLibro', 'Cat_EstadosLibro.nIdEstado = Cat_Fondo.nIdEstado', 'left')
			->join('Cat_Editoriales' ,'Cat_Editoriales.nIdEditorial = Cat_Fondo.nIdEditorial', 'left');

			$this->db->select('Cli_Clientes.cNombre cNombre2, Cli_Clientes.cApellido cApellido2, Cli_Clientes.cEmpresa cEmpresa2')
			->join('Cli_Clientes', 'Sus_Suscripciones.nIdCliente = Cli_Clientes.nIdCliente');

			$this->db->select('Prv_Proveedores.cNombre cNombre3, Prv_Proveedores.cApellido cApellido3, Prv_Proveedores.cEmpresa cEmpresa3')
			->join('Prv_Proveedores', 'Prv_Proveedores.nIdProveedor = ' . $this->db->isnull('Cat_Fondo.nIdProveedor', 'Cat_Editoriales.nIdProveedor'), 'left');

			$this->db->select('d1.cTitular,d1.cCalle,d1.cCP, d1.cPoblacion,r1.nIdPais, r1.cNombre cRegion, p1.cNombre cPais')
			->select('d2.cTitular cTitular2,d2.cCalle cCalle2,d2.cCP cCP2, d2.cPoblacion cPoblacion2,r2.nIdPais nIdPais2, r2.cNombre cRegion2, p2.cNombre cPais2')
			->join('Cli_Direcciones d1', 'Sus_Suscripciones.nIdDireccionEnvio = d1.nIdDireccion', 'left')
			->join('Cli_Direcciones d2', 'Sus_Suscripciones.nIdDireccionFactura = d2.nIdDireccion', 'left')
			->join('Gen_Regiones r1', 'd1.nIdRegion = r1.nIdRegion', 'left')
			->join('Gen_Paises p1', 'r1.nIdPais = p1.nIdPais', 'left')
			->join('Gen_Regiones r2', 'd2.nIdRegion = r2.nIdRegion', 'left')
			->join('Gen_Paises p2', 'r2.nIdPais = p2.nIdPais', 'left');

			$envio = $this->db->concat(array('d1.cTitular', 'd1.cCalle', 'd1.cCP', 'd1.cPoblacion', 'r1.cNombre', 'r1.cNombre'));
			$factura = $this->db->concat(array('d2.cTitular', 'd2.cCalle', 'd2.cCP', 'd2.cPoblacion', 'r2.cNombre', 'r2.cNombre'));
			$where = str_replace(array('Sus_Suscripciones.cDirEnv', 'Sus_Suscripciones.cDirFac'), array($envio, $factura), $where);

			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSearch($where, $fields)
	 */
	protected function onBeforeSearch($query, &$where, &$fields)
	{
		if (parent::onBeforeSearch($query, $where, $fields))
		{
			//Si es un ISBN lo añade a la búsqueda
			$this->load->library('ISBNEAN');
			if ($this->isbnean->is_isbn($query) || $this->isbnean->is_isbn($query, TRUE))
			{
				$isbn = $this->isbnean->to_isbn($query);
				$where = "{$this->_tablename}.cISBNBase = " . $this->db->escape($this->isbnean->clean_code($isbn));
				if (is_array($fields))
				{
					$fields[] = $this->_tablename . '.cISBN';
				}
				else
				{
					$fields .= ($fields != '')?',':'' . $this->_tablename . '.cISBN';
				}
			}

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
			if (isset($data['fPrecio']) && isset($data['fIVA']))
			{
				$data['fPVP'] = format_add_iva($data['fPrecio'], $data['fIVA']);
			}
			if (isset($data['fPrecioRevista']) && isset($data['fIVA']))
			{
				$data['fPVPRevista'] = format_add_iva($data['fPrecioRevista'], $data['fIVA']);
			}
			if (isset($data['dCreacion']))
			{
				$data['nYears'] = date('Y', time()) - date('Y', $data['dCreacion']) + 1;
			}
			$data['cCliente'] = format_name($data['cNombre2'], $data['cApellido2'], $data['cEmpresa2']);
			$data['cProveedor'] = format_name($data['cNombre3'], $data['cApellido3'], $data['cEmpresa3']);

			if (isset($data['dRenovacion']) && isset($data['nDuracion']))
			{
				$data['dDesde'] = dateadd($data['dRenovacion'], 0, 0, -$data['nDuracion']);
				$data['dHasta'] = dateadd($data['dRenovacion'], -1);
			}

			if (isset($data['cCalle']))
			{
				$data['cDirEnv'] = format_address_print(array(
					'cTitular' => $data['cTitular'], 
					'cCalle' => $data['cCalle'], 
					'cCP' => $data['cCP'], 
					'cPoblacion' => $data['cPoblacion'], 
					'cRegion' => $data['cRegion'], 
					'cPais' => $data['cPais']
					));
				$data['cDirFac'] = format_address_print(array(
					'cTitular' => $data['cTitular2'], 
					'cCalle' => $data['cCalle2'], 
					'cCP' => $data['cCP2'], 
					'cPoblacion' => $data['cPoblacion2'], 
					'cRegion' => $data['cRegion2'], 
					'cPais' => $data['cPais2'] 
					));
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert($data)
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			# Cambio de duración
			if (isset($data['nDuracion']))
			{
				#$duracion = dateadd($old['dRenovacion'], 0, 0, -$old['nDuracion']);
				if (!isset($data['dInicio'])) $data['dInicio'] = time();
				#var_dump($data['dInicio']); die();
				$data['dRenovacion'] = dateadd($data['dInicio'], 0, 0, $data['nDuracion']);				
			}			
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
			# Cambios de precio de venta
			$old = null;
			if (isset($data['fPrecio']))
			{
				if (!isset($old)) $old = $this->load($id);
				if ($old['fPrecio'] != $data['fPrecio'])
				{
					$ins = array(
						'nIdSuscripcion' => (int)$id,
						'fPrecioAntiguo' =>  $this->_tofloat($old['fPrecio']),
						'fPrecioNuevo' =>  $this->_tofloat($data['fPrecio']),
						'cCUser' => $this->userauth->get_username(),
						'dCambio' => $this->_todate(time())
					);
					if (!$this->db->insert('Sus_CambiosPrecio', $ins))
					{
						$this->_set_error_message($this->db->_error_message());
						return FALSE;
					}
				}	
			}		
			# Cambios de cliente			
			$old = null;
			if (isset($data['nIdCliente']))
			{
				if (!isset($old)) $old = $this->load($id);
				if ($old['nIdCliente'] != $data['nIdCliente'])
				{
					$ins = array(
						'nIdSuscripcion' => (int)$id,
						'nIdClienteAntiguo' =>  $old['nIdCliente'],
						'nIdClienteNuevo' =>  $data['nIdCliente'],
						'cCUser' => $this->userauth->get_username(),
						'dCambio' => $this->_todate(time())
					);
					if (!$this->db->insert('Sus_CambiosCliente', $ins))
					{
						$this->_set_error_message($this->db->_error_message());
						return FALSE;
					}
				}	
			}					
			# Cambio de duración
			if (isset($data['nDuracion']) || isset($data['dRenovacion']))
			{
				if (!isset($old)) $old = $this->load($id);
				#$duracion = dateadd($old['dRenovacion'], 0, 0, -$old['nDuracion']);
				$inicio = isset($data['dInicio'])?$data['dInicio']:$old['dInicio'];
				$duracion = isset($data['nDuracion'])?$data['nDuracion']:$old['nDuracion'];
				$data['dRenovacion'] = dateadd($inicio, 0, 0, $duracion);				
			}			
		}
		return TRUE;
	}

	/**
	 * @see system/application/libraries/MY_Model#onParseWhere
	 */
	protected function onParseWhere(&$where)
	{
		parent::onParseWhere($where);
		if (isset($where['cProveedor'])) 
		{
			$value = $this->db->escape_str($where['cProveedor']);
			$w = boolean_sql_where($value, $this->_complete_field('Cli_Cliente.cliente'), $this->_get_type_parser('Cli_Cliente.cliente'));
			$w = str_replace('Cli_Cliente.cliente', $this->db->concat(array('Prv_Proveedores.cEmpresa', 'Prv_Proveedores.cNombre', 'Prv_Proveedores.cApellido')), $w);
			$where[count($where)] = $w;
			unset($where['cProveedor']);
		}
		if (isset($where['cCliente'])) 
		{
			$value = $this->db->escape_str($where['cCliente']);
			$w = boolean_sql_where($value, $this->_complete_field('Cli_Cliente.cliente'), $this->_get_type_parser('Cli_Cliente.cliente'));
			$w = str_replace('Cli_Cliente.cliente', $this->db->concat(array('Cli_Clientes.cEmpresa', 'Cli_Clientes.cNombre', 'Cli_Clientes.cApellido')), $w);
			$where[count($where)] = $w;
			unset($where['cCliente']);
		}
		if (isset($where['cRevista'])) 
		{
			$where['Cat_Fondo.cTitulo'] = $where['cRevista'];
			unset($where['cRevista']);
		}
		return TRUE;	
	}

	/**
	 * Regenera los precios de coste de los albaranes de entrada y de las suscripciones
	 * @return TEXT
	 */
	function costes()
	{
		# Obtiene todos los albaranes de compra de una suscripción
		$this->db->flush_cache();
		$this->db->select('Sus_PedidosSuscripcion.nIdSuscripcion, 
			Doc_LineasAlbaranesEntrada.nIdAlbaran, 
			Doc_LineasAlbaranesEntrada.nCantidad,
			Doc_LineasAlbaranesEntrada.fPrecio,
			Doc_LineasAlbaranesEntrada.cRefInterna,
			Doc_LineasAlbaranesEntrada.fDescuento,
			Doc_LineasAlbaranesEntrada.fGastos,
			Doc_LineasAlbaranesEntrada.fCoste')
		->select($this->db->date_field('Doc_AlbaranesEntrada.dCierre', 'dCierre'))
		->from('Doc_LineasAlbaranesEntrada')
		->join('Doc_AlbaranesEntrada', 'Doc_AlbaranesEntrada.nIdAlbaran=Doc_LineasAlbaranesEntrada.nIdAlbaran')
		->join('Doc_LineasPedidosRecibidas', "Doc_LineasAlbaranesEntrada.nIdLinea = Doc_LineasPedidosRecibidas.nIdLineaAlbaran")
		->join('Doc_LineasPedidoProveedor', 'Doc_LineasPedidoProveedor.nIdLinea = Doc_LineasPedidosRecibidas.nIdLineaPedido')
		->join('Sus_PedidosSuscripcion', 'Doc_LineasPedidoProveedor.nIdPedido = Sus_PedidosSuscripcion.nIdPedido')
		#->where('Sus_PedidosSuscripcion.nIdSuscripcion=20362');
		->where('YEAR(Doc_LineasAlbaranesEntrada.dCreacion) >= 2010');
		$query = $this->db->get();
		$datos = $this->_get_results($query);

		$sus = array();
		foreach ($datos as $reg)
		{
			$sus[$reg['nIdSuscripcion']]['compras'][] = $reg;
		}

		#Obtiene los albaranes de salida
		$this->db->select('Sus_SuscripcionesAlbaranes.nIdSuscripcion,
			Doc_LineasAlbaranesSalida.nIdAlbaran, 
			Doc_LineasAlbaranesSalida.nIdLineaAlbaran,
			Doc_LineasAlbaranesSalida.nCantidad,
			Doc_LineasAlbaranesSalida.fPrecio,
			Doc_LineasAlbaranesSalida.cRefInterna,
			Doc_LineasAlbaranesSalida.fDescuento,
			Doc_LineasAlbaranesSalida.fCoste')
		->select($this->db->date_field('Doc_AlbaranesSalida.dCreacion', 'dCreacion'))
		->from('Sus_SuscripcionesAlbaranes')
		->join('Doc_AlbaranesSalida', 'Doc_AlbaranesSalida.nIdAlbaran = Sus_SuscripcionesAlbaranes.nIdAlbaran')
		->join('Doc_LineasAlbaranesSalida', 'Doc_LineasAlbaranesSalida.nIdAlbaran = Doc_AlbaranesSalida.nIdAlbaran');
		#->where('Sus_SuscripcionesAlbaranes.nIdSuscripcion=20362');
		#->where($this->db->isnull('Doc_LineasAlbaranesSalida.fCoste', '0') . ' = 0');
		#->where('YEAR(Doc_LineasAlbaranesSalida.dCreacion) >= 2010');
		
		$query = $this->db->get();
		$datos = $this->_get_results($query);

		#echo '<pre>'; print_r($this->db->queries); echo '</pre>';

		foreach ($datos as $reg)
		{
			$sus[$reg['nIdSuscripcion']]['ventas'][] = $reg;
		}

		$this->obj->load->model('ventas/m_albaransalidalinea');
		echo '<pre>';
		foreach ($sus as $id => $datos)
		{
			if (isset($datos['compras']) && isset($datos['ventas']))
			{
				echo "Suscripción {$id}\n-------------------------\n";
				sksort($datos['compras'], 'cRefInterna');
				sksort($datos['ventas'], 'cRefInterna');
				/*$compra = array_pop($datos['compras']);
				$venta = array_pop($datos['ventas']);
				#var_dump($compra, $venta);
				var_dump(date('Y-m-d G:i:s', $compra['dCierre']), $compra, date('Y-m-d G:i:s', $venta['dCreacion']), $venta); die();
				$coste = $compra['fCoste'];
				while ($compra['dCierre'] > $venta['dCreacion'])
				{
					$venta = array_pop($datos['ventas']);
				}
				var_dump(date('Y-m-d G:i:s', $compra['dCreacion']), $compra, date('Y-m-d G:i:s', $venta['dCreacion']), $venta);
				*/
				foreach ($datos['ventas'] as $key => $venta) 
				{
					if (/*$venta['fCoste'] == 0 &&*/ !empty($venta['cRefInterna']))
					{
						foreach($datos['compras'] as $compra)
						{
							if ($venta['cRefInterna'] == $compra['cRefInterna'])
							{
								#$this->obj->m_albaransalidalinea->update($venta['nIdLineaAlbaran'], array('fCoste' => $compra['fCoste'] + $compra['fGastos']));
								echo "Albarán {$venta['nIdAlbaran']} ({$venta['cRefInterna']}) = {$compra['nIdAlbaran']} - {$compra['fCoste']} +  {$compra['fGastos']}\n";
								$this->update($id, array('fPrecioCompra' => $compra['fCoste'] + $compra['fGastos']));
								break;
							}

							elseif ($venta['cRefInterna'] < $compra['cRefInterna']) 
							{
								echo "NO Albarán {$venta['nIdAlbaran']} ({$venta['cRefInterna']})\n";
								break;
							}
						}
					}
				}
				#var_dump($datos['compras'], $datos['ventas']); die();
			}
		}
		echo '</pre>';
		#var_dump($sus);
	}
}

/* End of file M_suscripcion.php */
/* Location: ./system/application/models/suscripciones/M_suscripcion.php */