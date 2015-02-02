<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	suscripciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Entrada de mercancía suscripciones
 *
 */
class Entradamercancia extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Entradamercancia
	 */
	function __construct()
	{
		parent::__construct('suscripciones.entradamercancia', 'suscripciones/m_entradamercancia', TRUE, 'suscripciones/entradamercancia.js', 'Entrada de mercancía');
	}
	
	/**
	 * Busca los pedidos pedientes de recibir por artículo, suscripción o título
	 * Si no se indica nada devuelve todos.
	 * @param string $query Palabra de búsqueda
	 */
	function pedidos($query = null)
	{
		$this->userauth->roleCheck(($this->auth .'.search'));
		$query	= isset($query)?$query:$this->input->get_post('query');
		
		$data = $this->reg->pedidos($query);
		#var_dump($data);
		$this->out->data($data, $this->reg->get_count());
	}
	
	/**
	 * Devuelve la información de la suscripcion indicada para entrar la mercancia
	 * @param int $id Id de la suscripción
	 * @return DATA
	 */
	function get_datos($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.search'));
		$id	= isset($id)?$id:$this->input->get_post('id');
		
		$data = $this->reg->get_data($id);
		if (isset($data))
		{
			# Artículo
			$this->load->model('catalogo/m_articulo');
			$data['articulo'] = $this->m_articulo->load($data['nIdLibro']);
			# Proveedor
			$this->load->model('proveedores/m_proveedor');
			$idp = $this->m_articulo->get_proveedor_habitual($data['articulo']);
			if ($idp > 0) $data['proveedor'] = $this->m_proveedor->load($idp);			
			$this->load->model('perfiles/m_perfil');
			$data['direccion'] = $this->m_proveedor->get_direccion($idp, PERFIL_SUSCRIPCIONES);
			# Cliente
			$this->load->model('clientes/m_cliente');
			$data['cliente'] = $this->m_cliente->load($data['nIdCliente'], array('descuentos', 'tarifas'));
			$data['tarifa'] = $data['cliente']['nIdTipoTarifa'];
			for($i=0; $i<count($data['cliente']['tarifas']); $i++)
			{
				if ($data['cliente']['tarifas'][$i]['nIdTipo'] == $data['articulo']['nIdTipo'])
				{
					$data['tarifa'] = $data['cliente']['tarifas'][$i]['nIdTipoTarifa'];
					break;
				}
			}
			#var_dump($data['tarifa']); die();
			if (($data['nFacturas']  > $data['nEntradas'])||($data['bNoFacturable'])) $data['bNoFacturable'] = TRUE;		
			# Histórico de compras y ventas
			$this->load->model('suscripciones/m_suscripcion');
			$data['facturas'] = $this->m_suscripcion->get_facturas($data['nIdSuscripcion']);
			$data['pedidosproveedor'] = $this->m_suscripcion->get_pedidosproveedor($data['nIdSuscripcion']);
			$data['presupuesto'] = $this->m_articulo->get_presupuestos($data['nIdLibro'], null, null, $data['nIdCliente'], TRUE);
			if (count($data['presupuesto']) > 0)
			{
				$data['presupuesto'] = array_pop(($data['presupuesto']));
				$data['presupuesto'] = array_merge($data['presupuesto'], format_calculate_importes($data['presupuesto']));
			}

			$message = $this->load->view('suscripciones/datos_entrada', $data, TRUE);
			$res = array(
				'success' 	=> TRUE,
				'message' 	=> $message,
				'data'		=> $data
			);
			
			$this->out->send($res);
		}		
	}

	/**
	 * Obtiene los posibles precios de una suscripción
	 * @param float $precio Precio de compra (sin IVA)
	 * @param float $dto Descuento del proveedor sobre el artículo
	 * @param int $divisa Id de la divisa
	 * @param float $cambio Valor del cambio de la divisa
	 * @param int $pais Id del país del proveedor
	 * @param float $iva IVA del artículo
	 * @param int $cantidad Unidades
	 * @param float $gastos Gastos totales del albarán de entrada
	 * @param int $tipo Tipo de artículo
	 * @return DATA
	 */
	function get_precios($precio = null, $dto = null, $divisa = null, $cambio = null, $pais = null, $iva = null, $cantidad = null, $gastos = null, $tipo = null)
	{
		$this->userauth->roleCheck(($this->auth .'.search'));
		
		$precio	= isset($precio)?$precio:$this->input->get_post('precio');
		$iva	= isset($iva)?$iva:$this->input->get_post('iva');
		$dto	= isset($dto)?$dto:$this->input->get_post('dto');
		$divisa	= isset($divisa)?$divisa:$this->input->get_post('divisa');
		$cambio	= isset($cambio)?$cambio:$this->input->get_post('cambio');
		$pais	= isset($pais)?$pais:$this->input->get_post('pais');
		$gastos	= isset($gastos)?$gastos:$this->input->get_post('gastos');
		$cantidad = isset($cantidad)?$cantidad:$this->input->get_post('cantidad');
		$tipo	= isset($tipo)?$tipo:$this->input->get_post('tipo');

		if ($cantidad < 1) $cantidad = 1;
		// Cambio divisa y margen
		$divisa_default = $this->config->item('bp.divisa.default');
		if (!is_numeric($divisa))
			$divisa = $divisa_default;
		if (empty($cambio))
		{
			$this->load->model('generico/m_divisa');
			$d = $this->m_divisa->load($divisa);
			$cambio = $d['fCompra'];
		}
		
		$margendivisa = ($divisa != $divisa_default) ? $this->config->item('bp.divisa.margenmoneda') : 0;

		// Carga la tarifa por defecto
		$tarifa = $this->config->item('ventas.tarifas.defecto');
		$this->load->model('ventas/m_tipotarifa');
		$tarifa = $this->m_tipotarifa->load($tarifa);
		#$margentarifa = $tarifa['fMargen'];
		$tf = $this->m_tipotarifa->get();
		foreach ($tf as $t)
		{
			$tipostarifa[$t['nIdTipoTarifa']] = $t;
		}
		$tarifa_defecto = $this->config->item('ventas.tarifas.defecto');

		// Carga los ivas de los países de origen
		$tipopais = array();
		$paislocal = $this->config->item('bp.address.pais');
		if (is_numeric($pais) && ($pais != $paislocal))
		{
			$this->load->model('compras/m_tipopais');
			$ivas = $this->m_tipopais->get(null, null, null, null, 'nIdPais=' . $pais);
			if (count($ivas) > 0)
			{
				foreach ($ivas as $iv)
				{
					$tipopais[$iv['nIdTipo']] = $iv['fIVA'];
				}
			}
		}
		// Diferencia mínima con el original
		$margenoriginal = $this->config->item('bp.ventas.margenoriginal');

		// Precios iniciales
		#var_dump($precio);
		#var_dump($dto);
		$coste = $precio * (1 - $dto / 100);
		$coste = format_decimals($coste / $cambio);
		$original = format_decimals($precio / $cambio);
		$gastos_ar = preg_split('/;/', $gastos);
		$gastos = 0;
		foreach($gastos_ar as $g)
		{
			if (is_numeric($g)) $gastos += $g;
		}
		$gastos = $gastos / $cantidad;
		$gastos = $gastos * (1 + $margendivisa / 100);
		$gastos = format_decimals($gastos/$cambio);
		if (!is_numeric($gastos)) $gastos = 0;
		#var_dump($gastos);
		#$margendivisa = (1 + $margendivisa / 100);
		$coste = format_decimals((($coste * (1 + $margendivisa / 100)) /*/ (1 - $margentarifa / 100)*/));
		#$base = format_decimals(($precio) * (1 - $tipostarifa[$tarifa_defecto]['fMargen'] / 100));
		$tarifas = array();
		foreach ($tipostarifa as $t => $v)
		{
			$pr2 = format_decimals(($coste / (1 - $v['fMargen'] / 100)) + $gastos);
			$pr2 = format_add_iva($pr2, $iva);
			// Redondear a 0.5
			$pr2 = format_redondear05($pr2);
			$pr2 = format_quitar_iva($pr2, $iva);
			if (isset($tipopais[$tipo]))
			{
				$original = $coste * (1 + $margenoriginal / 100);
				$pr2 = max($pr2, format_add_iva($original, $tipopais[$tipo]));
			}
			$tarifas[] = array(
					'nIdTipoTarifa' => $t,
					'cDescripcion' => $v['cDescripcion'],
					'fMargen' => $v['fMargen'],
					'fMargenDivisa' => $margendivisa,
					'fOriginal' => $original,
					'fMargenOriginal' => $margenoriginal,
					'fCoste' => $coste,
					'fGastos' => $gastos,
					'fIVA' => $iva,
					'fPrecio' => $pr2,
					'fPVP' =>  format_add_iva($pr2, $iva),
			);
		}
		#var_dump($tarifas);die();
		$this->out->data($tarifas);		
	}

	/**
	 * Ejecuta la entrada de mercancía creando el albarán de entrada, asignándolo al pedido del proveedor y 
	 * creando el albarán de salida.
	 * @param  [type] $precio    [description]
	 * @param  [type] $dto       [description]
	 * @param  [type] $divisa    [description]
	 * @param  [type] $cambio    [description]
	 * @param  [type] $iva       [description]
	 * @param  [type] $cantidad  [description]
	 * @param  [type] $gastos    [description]
	 * @param  [type] $proveedor [description]
	 * @param  [type] $sus       [description]
	 * @param  [type] $numero    [description]
	 * @param  [type] $volumen   [description]
	 * @param  [type] $fecha     [description]
	 * @param  [type] $pvp       [description]
	 * @param  [type] $dtocl     [description]
	 * @param  [type] $pedido    [description]
	 * @param  [type] $factura   [description]
	 * @param  [type] $direccion [description]
	 * @param  [type] $pais      [description]
	 * @param  [type] $camara    [description]
	 * @return [type]            [description]
	 */
	function crear($precio = null, $dto = null, $divisa = null, $cambio = null, $iva = null, $cantidad = null, $gastos = null, 
		$proveedor = null, $sus = null, $numero = null, $volumen = null, $fecha = null, $pvp = null, $dtocl = null, 
		$pedido = null, $factura = null, $direccion = null, $pais = null, $camara = null)
	{
		$this->userauth->roleCheck(($this->auth .'.search'));
		
		$precio	= isset($precio)?$precio:$this->input->get_post('precio');
		$dto	= isset($dto)?$dto:$this->input->get_post('dto');
		$divisa	= isset($divisa)?$divisa:$this->input->get_post('divisa');
		$cambio	= isset($cambio)?$cambio:$this->input->get_post('cambio');
		$iva	= isset($iva)?$iva:$this->input->get_post('iva');
		$gastos	= isset($gastos)?$gastos:$this->input->get_post('gastos');
		$cantidad = isset($cantidad)?$cantidad:$this->input->get_post('cantidad');
		$proveedor	= isset($proveedor)?$proveedor:$this->input->get_post('proveedor');
		$sus	= isset($sus)?$sus:$this->input->get_post('sus');
		$numero	= isset($numero)?$numero:$this->input->get_post('numero');
		$volumen	= isset($volumen)?$volumen:$this->input->get_post('volumen');
		$fecha	= isset($fecha)?$fecha:$this->input->get_post('fecha');
		$pvp	= isset($pvp)?$pvvp:$this->input->get_post('pvp');
		$dtocl	= isset($dtocl)?$dtocl:$this->input->get_post('dtocl');
		$pedido	= isset($pedido)?$pedido:$this->input->get_post('pedido');
		$factura	= isset($factura)?$factura:$this->input->get_post('factura');
		$direccion	= isset($direccion)?$direccion:$this->input->get_post('direccion');
		$art	= isset($art)?$art:$this->input->get_post('art');
		$pais	= isset($pais)?$pais:$this->input->get_post('pais');
		$camara	= isset($camara)?$camara:$this->input->get_post('camara');

		// Lee la suscripción
		$this->load->model('suscripciones/m_suscripcion');
		$suscripcion = $this->m_suscripcion->load($sus);
		# si es un anticipo, el albarán es no facturable
		$facturas = $suscripcion['nFacturas'];
		$entradas = $suscripcion['nEntradas']; 
		if (($facturas  > $entradas)||($suscripcion['bNoFacturable'])) $factura = FALSE;		
		 
		// Crea el albarán de entrada
		# Gastos
		$gastos_ar = preg_split('/;/', $gastos);
		$gastos2 = array();
		$totalgastos = 0;
		foreach($gastos_ar as $g)
		{
			if (!empty($g))
			{
				$ar2 = preg_split('/_/', $g);
				if (isset($ar2[0]) && isset($ar2[1]))
				{
					$gastos2[] = array('nIdTipoCargo' => $ar2[0], 'fImporte' => $ar2[1]);
					$totalgastos +=	$ar2[1];
				}
			}
		}
		$peso = $this->config->item('bp.entradamercancia.peso');
		$tipo = $this->config->item('bp.entradamercancia.tipomercancia');
		$albaran = array(
			'nIdProveedor' => $proveedor,
			'nIdDireccion' => $direccion,
			'fPrecioCambio' => $cambio,
			'nIdDivisa' => $divisa,
			'dFecha' => $fecha,
			'cNumeroAlbaran' => $numero,
			'bPrecioLibre' => TRUE,
			'bSuscripciones' => TRUE,
			'nIdPais' => $pais,
			'nPeso' => $peso, 
			'nIdTipoMercancia' => $tipo,
			'cRefProveedor' => $suscripcion['cRefProveedor'],
			'cRefInterna' => $suscripcion['cRefCliente'],
		);
		
		$albaran['cargos'] = $gastos2;
		$albaran['lineas'][] = array(
			'nIdLibro' => $art,
			'fPrecio' => $precio,
			'nCantidad' => $cantidad,
			'fPrecioVenta' => $pvp,
			'fDescuento' => $dto,
			'fIVA' => $iva,
			'cRefProveedor' => $suscripcion['cRefProveedor'],
			'cRefInterna' => $volumen,
		);

		$this->load->model('compras/m_albaranentrada');
		$this->db->trans_begin();
		$idal = $this->m_albaranentrada->insert($albaran);
		if ($idal < 0 )
		{
			$this->db->trans_rollback();
			$this->out->error($this->m_albaranentrada->error_message());
		}
		if (!$this->m_albaranentrada->cerrar($idal))
		{
			$this->db->trans_rollback();
			$this->out->error($this->m_albaranentrada->error_message());			
		}
		$albaran = $this->m_albaranentrada->load($idal, 'lineas');

		#Importe cámara
		if (!$this->m_albaranentrada->update($idal, array('fImporteCamara' => $camara)))
		{
			$this->db->trans_rollback();
			$this->out->error($this->m_albaranentrada->error_message());
		}

		# Asigna el albarán al pedido de proveedor
		$this->load->model('compras/m_pedidoproveedor');
		$pd = $this->m_pedidoproveedor->load($pedido, 'lineas');
		$ct = $cantidad;
		$asignar = array();
		$seccion = null;
		if (count($pd['lineas']) > 0)
		{
			foreach($pd['lineas'] as $linea)
			{
				if ($linea['nIdLibro'] == $art && ($linea['nIdEstado'] == 2 || $linea['nIdEstado'] == 4))
				{
					$ct2 = min($linea['nCantidad'] - $linea['nRecibidas'], $ct);
					$seccion = $linea['nIdSeccion'];
					$asig[] = array($albaran['lineas'][0]['nIdLibro'], $linea['nIdLinea'], $ct2, $linea['cSeccion'], $pedido, $linea['nIdSeccion']);
					$ct -= $ct2;
				}
				if ($ct == 0) break;
			}
		}
		if ($ct > 0)
		{
			$this->db->trans_rollback();
			$this->out->error(sprintf($this->lang->line('entradamercancia-no-cantidad'), $pedido));
		}

		$res = $this->m_albaranentrada->asignar($idal, $asig);
		if ($res === FALSE)
		{
			$this->db->trans_rollback();
			$this->out->error($this->m_albaranentrada->error_message());			
		}

		// Crea el albarán de salida
		$salida = array(
			'nIdDireccion' => $suscripcion['nIdDireccionEnvio'],
			'cRefCliente' => $suscripcion['cRefCliente'],
			'cRefInterna' => $volumen,		
			'bNoFacturable' => !format_tobool($factura),	
			'nIdCliente' => $suscripcion['nIdCliente'],
		);
		$salida['albaransalidasuscripcion'][] = array('nIdSuscripcion' => $sus);
		$salida['lineas'][] = array(
			'nIdLibro' => $art,
			'fPrecio' => format_quitar_iva($pvp, $iva),
			'nIdSeccion' => $seccion,
			'nCantidad' => $cantidad,
			'cRefInterna' => $volumen,		
			'cRefCliente' => $suscripcion['cRefCliente'],
			'fDescuento' => $dtocl,
			'fCoste' => $albaran['lineas'][0]['fCoste'] + $albaran['lineas'][0]['fGastos'],
			'fIVA' => $iva
		);

		$this->load->model('ventas/m_albaransalida');
		$idsa = $this->m_albaransalida->insert($salida);
		if ($idsa < 0 )
		{
			$this->db->trans_rollback();
			$this->out->error($this->m_albaransalida->error_message());
		}
		if (!$this->m_albaransalida->cerrar($idsa))
		{
			$this->db->trans_rollback();
			$this->out->error($this->m_albaransalida->error_message());			
		}

		# Actualiza las entradas de la suscripción y el precio de coste
		$upd['nEntradas'] = $suscripcion['nEntradas'] + $cantidad;
		$upd['nIdUltimaEntrada'] = $idal;
		$upd['fPrecioCompra'] = $albaran['lineas'][0]['fCoste'] + $albaran['lineas'][0]['fGastos'];
		if (!$this->m_suscripcion->update($sus, $upd))
		{
			$this->_set_error_message($obj->m_suscripcion->error_message());
			return FALSE;
		}
		
		# Final
		$this->db->trans_commit();
		
		# Respuesta
		$link_ae = format_enlace_cmd($idal, site_url('compras/albaranentrada/index/' . $idal));
		$link_as = format_enlace_cmd($idsa, site_url('ventas/albaransalida/index/' . $idsa));
		$link_sus = format_enlace_cmd($sus, site_url('suscripciones/suscripcion/index/' . $sus));
		$res = array (
			'entrada' => $idal, 
			'salida' => $idsa, 
			'suscripcion' => $sus,
			'success' => TRUE,
			'dialog' => sprintf($this->lang->line('entradamercancia-ok'), $link_sus, $link_ae, $link_as));
		$this->out->send($res);		
		#$this->db->trans_rollback();
	}

	/**
	 * Deshace una entrada de mercancía, abonando el albarán de salida y eliminando el albarán de entrada
	 * @param int $entrada Id del albarán de entrada
	 * @param int $salida Id del albarán de salida
	 * @return MSG
	 */
	function deshacer($entrada = null, $salida = null)
	{
		$this->userauth->roleCheck(($this->auth .'.search'));
		
		$entrada	= isset($entrada)?$entrada:$this->input->get_post('entrada');
		$salida		= isset($salida)?$salida:$this->input->get_post('salida');
		
		$this->load->model('compras/m_albaranentrada');
		
		$this->db->trans_begin();
		#Desasigna el albarán
		if (!$this->m_albaranentrada->desasignar($entrada))
		{
			$this->db->trans_rollback();
			$this->out->error($this->m_albaranentrada->error_message());
		}
		# Cantidad
		$ae = $this->m_albaranentrada->load($entrada, 'lineas');
		$cantidad = 0;
		foreach ($ae['lineas'] as $l) $cantidad += $l['nCantidad'];
		#Lo abre
		if (!$this->m_albaranentrada->abrir($entrada))
		{
			$this->db->trans_rollback();
			$this->out->error($this->m_albaranentrada->error_message());
		}
		#Comprueba albarán de salida esté cerrado y no esté facturado
		$this->load->model('ventas/m_albaransalida');		
		$as = $this->m_albaransalida->load($salida, 'albaransalidasuscripcion');
		if (empty($as))
		{
			$this->db->trans_rollback();
			$this->out->error($this->lang->line('registro_no_encontrado'));
			return FALSE;
		}
		$sus = $as['albaransalidasuscripcion'][0]['nIdSuscripcion'];
		#var_dump($sus); die();
		if ($as['nIdEstado'] != 2)
		{
			$this->db->trans_rollback();
			$this->out->error(sprintf($this->lang->line('entradamercancia-deshacer-no-estado'), $salida));			
		}
		if (is_numeric($as['nIdFactura']))
		{
			$this->db->trans_rollback();
			$this->out->error(sprintf($this->lang->line('entradamercancia-deshacer-factura'), $salida));			
		}
		#Lo abona
		$id_n = $this->m_albaransalida->abonar($salida);
		if ($id_n < 1)
		{
			$this->db->trans_rollback();
			$this->out->error($this->m_albaransalida->error_message());
		}
		#Lo cierra
		$this->m_albaransalida->cerrar($id_n);
		
		$this->load->model('suscripciones/m_suscripcion');
		$suscripcion = $this->m_suscripcion->load($sus);
		# Actualiza las entradas de la suscripción		
		$upd['nEntradas'] = $suscripcion['nEntradas'] - $cantidad;
		$last = $this->m_suscripcion->get_pedidosproveedor($sus, TRUE, TRUE);
		$last = (isset($last[0]['nIdAlbaran']))?$last[0]['nIdAlbaran']:null;
		$upd['nIdUltimaEntrada'] = $last;
		if (!$this->m_suscripcion->update($sus, $upd))
		{
			$this->_set_error_message($obj->m_suscripcion->error_message());
			return FALSE;
		}
		# Elimina el albarán de entrada
		if (!$this->m_albaranentrada->delete($entrada))
		{
			$this->db->trans_rollback();
			$this->out->error($this->m_albaranentrada->error_message());
		}
		
		$this->db->trans_commit();
		#Mensaje de éxito
		$link_as1 = format_enlace_cmd($salida, site_url('ventas/albaransalida/index/' . $salida));
		$link_as2 = format_enlace_cmd($id_n, site_url('ventas/albaransalida/index/' . $id_n));
		$this->out->success(sprintf($this->lang->line('entradamercancia-deshacer-ok'), $entrada, $link_as1, $link_as2));		
	}

	/**
	 * Devuelve los albaranes que están preparados para ser facturados
	 * @param string $sort Campo de orden
	 * @param string $dir ASC, DESC 
	 * @return DATOS
	 */
	function albaranes($sort = null, $dir = null)
	{
		$this->userauth->roleCheck(($this->auth .'.facturar'));
		
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		
		$this->load->model('suscripciones/m_albaransuscripcion');
		$data = $this->m_albaransuscripcion->get(null, null, $sort, $dir, 'bNoFacturable=0 AND nIdFactura IS NULL AND nIdEstado=2');
		$this->out->data($data);
	}

	/**
	 * Crea las factura de suscripciones. Crea una factura por cliente y por dirección de facturación
	 * @param string/array $ids Ids de los albaranes a factura
	 * @param id $serie Id de la serie de la factura
	 * @param date $fecha Fecha de la factura
	 * @return DATA array[] -> <li>id: id de la factura</li>
	 * <li>numero: Número de factura</li>
	 * <li>total: Importe de la factura</li>
	 * <li>cliente: Cliente</li> 
	 */
	function facturar($ids = null, $serie = null, $fecha = null)
	{
		$ids = isset($ids)?$ids:$this->input->get_post('ids');
		$serie = isset($serie)?$serie:$this->input->get_post('serie');
		$fecha = isset($fecha)?$fecha:$this->input->get_post('fecha');
		if (!empty($ids) && !empty($serie))
		{
			if (empty($fecha)) $fecha = time();
			if (is_string($ids))
			{
				$ids = preg_split('/\;/', $ids);
				$ids = array_unique($ids);
			}
			if (count($ids) > 0)
			{
				set_time_limit(0);
				# Lee los albaranes
				$this->load->model('ventas/m_albaransalida');
				$this->load->model('suscripciones/m_suscripcion');
				$this->load->model('clientes/m_cliente');
				$this->load->model('perfiles/m_perfil');
				$albaranes = array();
				foreach ($ids as $id)
				{
					$al = $this->m_albaransalida->load($id, 'albaransalidasuscripcion');
					if ($al)
					{
						$sus = $al['albaransalidasuscripcion'][0]['nIdSuscripcion'];
						$sus = $this->m_suscripcion->load($sus, 'cliente');
						if (!$sus['cliente']['bCredito'] || !is_numeric($sus['cliente']['nIdCuenta']))
						{
							$this->out->error(sprintf($this->lang->line('sus-error-no-cuenta'), $sus['cCliente'], $id));
						}
						$al['cCliente'] = $sus['cCliente'];
						$dir = $sus['nIdDireccionFactura'];
						if (!isset($dir))
						{
							$dir = $this->m_cliente->get_direccion($sus['nIdCliente'], array(PERFIL_FACTURACIONSUSCRIPCIONES, PERFIL_FACTURACION));
						}
						$albaranes[$al['nIdCliente']][$dir][] = $al;
					}
				}
				$this->load->model('ventas/m_factura');
				$this->load->model('ventas/m_albaransalida');
				$this->load->model('suscripciones/m_suscripcion');
				$caja = $this->config->item('bp.suscripciones.caja');
				$modopago = $this->config->item('bp.suscripciones.modopago');
				$facturas = array();
				$this->db->trans_begin();
				foreach($albaranes as $k => $albs)
				{
					foreach ($albs as $dir => $alb)
					{
						#Crea una factura
						$datos = array(
							'nIdCliente' => $k,
							'nIdDireccion' => $dir,
							'nIdSerie' => $serie,
							'nIdCaja' => $caja,
							'dFecha' => $fecha
						);
						$total = 0;
						foreach($alb as $al)
						{
							$albaran = $this->m_albaransalida->load($al['nIdAlbaran'], array('lineas'));
							$total2 = 0;
					 		foreach($albaran['lineas'] as $reg)
							{
								if ($reg['nCantidad'] != 0)
								{
									$linea = format_calculate_importes($reg);
									$total2 += $linea['fTotal2'];
								}						
							}
							$total += $total2;
						}
						$datos['modospago'][] = array(
							'nIdModoPago' => $modopago,
							'nIdCaja' => $caja,
							'dFecha' => $fecha, 
							'fImporte' => $total);
						$idf = $this->m_factura->insert($datos);
						if ($idf < 0)
						{
							$this->db->trans_rollback();
							$this->out->error($this->m_factura->error_message());
						}
						$facturas[] = array('id' => $idf, 'cliente' => $alb[0]['cCliente'], 'total' => $total);
						foreach($alb as $al)
						{
							if (!$this->m_albaransalida->update($al['nIdAlbaran'], array('nIdFactura' => $idf)))
							{
								$this->db->trans_rollback();
								$this->out->error($this->m_albaransalida->error_message());								
							}
						}
					}					
				}
				# Cerrar las facturas
				#var_dump($facturas);
				foreach ($facturas as $k => $ft)
				{
					$res = $this->m_factura->cerrar($ft['id']);
					if ($res===FALSE)
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_factura->error_message());														
					}
					$facturas[$k]['numero'] = format_numerofactura($res['numero'], $res['serie']);
					#var_dump($res);
				}
				$this->db->trans_commit();
				#var_dump($facturas);
				#var_dump($albaranes); die();
				$this->out->data($facturas);
			}
			else
			{
				$this->out->error($this->lang->line('mensaje_faltan_datos'));					
			}
		}
		else if ((!empty($ids) && empty($serie)) || (empty($ids) && !empty($serie))) 
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
		
		$this->_show_form('facturar', 'suscripciones/facturar.js', $this->lang->line('Facturar albaranes'));		
	}
	
	/**
	 * Elimina la factura de los albaranes de suscripciones. Solo si no está contabilizado.
	 * @param int $id Id de la factura
	 * @return MSG
	 */
	function desfacturar($id = null)
	{
		$id = isset($id)?$id:$this->input->get_post('id');
		if (!empty($id))
		{
			$this->load->model('ventas/m_factura');
			#Carga			
			$ft = $this->m_factura->load($id);
			if ($ft===FALSE)
			{
				$this->out->error($this->lang->line('registro_no_encontrado'));
			}
			#¿Se puede eliminar?
			if ($ft['nIdEstado'] == FACTURA_STATUS_CONTABILIZADA)
			{
				$this->out->error($this->lang->line('factura-contabilizada-error'));
			}

			#Fuerza el cambio de estado
			$upd = array(
				'nIdEstado' => FACTURA_STATUS_EN_PROCESO,
			);
			if (!$this->m_factura->update($id, $upd))
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_factura->error_message());														
			}
			if (!$this->m_factura->delete($id))
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_factura->error_message());														
			}
			$this->out->success(sprintf($this->lang->line('factura-eliminar-ok'), $ft['cNumero'], $ft['cCliente']));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
		
		
	}
}

/* End of file entradamercancia.php */
/* Location: ./system/application/controllers/suscripciones/entradamercancia.php */
