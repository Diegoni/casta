<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Albarán de entrada
 *
 */
class AlbaranEntrada extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return AlbaranEntrada
	 */
	function __construct()
	{
		parent::__construct('compras.albaranentrada', 'compras/m_albaranentrada', TRUE, 'compras/albaranentrada.js', 'Albarán de Entrada');
	}

	/**
	 * Cambia los IVAs del 18 y el 8 a los antiguos 16 y 7
	 * Esta herramienta debe desaparecer
	 * @param int $idd ID del albarán
	 * @return MSG/JS
	 */
	function iva16($idd = null)
	{
		$this->userauth->roleCheck($this->auth . '.iva16');

		$idd = isset($idd) ? $idd : $this->input->get_post('idd');

		if ($idd)
		{

			$this->load->model('compras/m_albaranentrada');
			$data = $this->m_albaranentrada->load($idd, 'lineas');
			$update = array();
			foreach ($data['lineas'] as $linea)
			{
				if (($linea['fIVA'] == 21) || ($linea['fIVA'] == 10))
				{
					$iva = ($linea['fIVA'] == 21) ? 18 : 8;
					$up['nIdLinea'] = $linea['nIdLinea'];
					$up['fIVA'] = $iva;
					$up['fPrecioVenta'] = $linea['fPrecio'] * (1 + $linea['fIVA'] / 100);
					$update[] = $up;
				}
			}
			if (count($update) == 0)
			{
				$this->out->success(sprintf($this->lang->line('albaranentrada-no-hay-iva16'), $idd));
			}
			$upd['lineas'] = $update;
			if (!$this->m_albaranentrada->update($idd, $upd))
			{
				$this->out->error($this->reg->error_message());
			}
			$this->out->message(sprintf($this->lang->line('albaranentrada-iva16-ok'), $idd));
		}
		else
		{
			$data['title'] = $this->lang->line('Cambiar IVA a 18% en Albarán de Entrada');
			$data['label'] = $this->lang->line('Albarán de Entrada');
			$data['url_action'] = site_url('compras/albaranentrada/iva16');
			$data['url_search'] = site_url('compras/albaranentrada/search');
			$this->_show_js('iva16', 'compras/selectdoc.js', $data);
		}
	}

	/**
	 * Cierra el documento
	 * @param int $id Id del documento (separados por ; si hay varios)
	 * @return MSG
	 */
	function cerrar($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.cerrar');
		$id = isset($id) ? $id : $this->input->get_post('id');
		if ($id)
		{
			$ids = is_string($id) ? preg_split('/\;/', $id) : $id;
			$count = 0;
			foreach ($ids as $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->cerrar($id);
					if ($res === FALSE)
						$this->out->error($this->reg->error_message());
					$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('albaranentrada-cerrado-history'));
					++$count;
				}
			}
			$this->out->success(sprintf($this->lang->line('albaranentrada-cerrada-ok'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Incidencias de un albarán antes de ser asignado
	 * @param int $id Id del documento (separados por ; si hay varios)
	 * @param in $concurso Id del concurso
	 * @return MSG
	 */
	function incidencias($id = null, $concurso = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$concurso = isset($concurso) ? $id : $this->input->get_post('concurso');
		if (is_numeric($id))
		{
			$data = $this->_get_asignacion($id);
			if (!is_array($data))
				$this->out->error($data);
			$errores = array();
			$exceso = array();
			$ok = array();
			sksort($data, 'cTitulo');
			foreach ($data as $value) 
			{
				if (!isset($value['nIdPedido']))
				{
					$errores[$value['nIdLibro']][] = $value;
				}
				else
				{
					$ok[$value['nIdLibro']] = $value['nIdLibro'];
				}
			}
			foreach ($ok as $key => $value) 
			{
				if (isset($errores[$value]))
				{
					$exceso[] = $errores[$value][0];
					unset($errores[$value]);
				}
			}
			#var_dump($exceso); die();
			$ae = $this->reg->load($id);
			$alt = $this->reg->lineasconcurso($concurso, $ae['nIdProveedor']);
			if (count($errores) > 0 || count($exceso))
			{
				$data['errores'] = $errores;
				$data['exceso'] = $exceso;
				$data['alt'] = $alt;
				$data['id'] = $id;
				$message = $this->load->view('compras/incidencias', $data, TRUE);
				$this->out->html_file($message, $this->lang->line('Incidencias concurso') . ' ' . $id, 'icon-page-warning');
			}
			$this->out->success($this->lang->line('albaranentrada-no-incidencias'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Parte el documento en varios según el nhttp://localhost/app/compras/albaranentrada/precio?actual=13.6&coste=8.08&gastos=10&id=199465&iva=4&precio=&stock=11&tipo=1&venta=13.6úmero máximo por albarán
	 * @param int $id Id del documento (separados por ; si hay varios)
	 * @param int $count Número de libros por albarán
	 * @return MSG
	 */
	function dividir($id = null, $count = null)
	{
		$this->userauth->roleCheck($this->auth . '.add');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$count = isset($count) ? $count : $this->input->get_post('count');
		
		if (is_numeric($id) && is_numeric($count))
		{
			set_time_limit(0);
			$data = $this->reg->load($id, 'lineas');
			$lineas = $data['lineas'];
			unset($data['lineas']);
			$this->db->trans_begin();
			$total = $count;	
			$supertotal = 0;
			$superlibros = 0;
			$ids = array();
			
			$this->load->model('compras/m_albaranentradalinea');
			
			while($total < count($lineas))
			{
				$local = 0;
				$alb = $data;
				unset($alb['nIdAlbaran']);
				
				$sublineas = array();				
				$total = 0;
				$libros = 0;
				$base = 0;
				while($total < count($lineas) && $local < $count)
				{
					$linea = $lineas[$total];
					$sublineas[] = $linea;
					$totales = format_calculate_importes($linea);
					$supertotal += $totales['fTotal2'];
					$total += $totales['fTotal2'];
					$superlibros += $linea['nCantidad'];
					$libros += $linea['nCantidad'];
					
					++$local;
					++$total;
				}
				if ($local > 0)
				{
					$alb['fTotal'] = $total;
					$alb['nLibros'] = $libros;
					
					$id_n = $this->reg->insert($alb);
					if ($id_n < 0)
					{
						$this->db->trans_rollback();
						$this->out->error($this->reg->error_message());
					}
					foreach($sublineas as $l)
					{
						$l['nIdAlbaran'] = $id_n;
						
						if (!$this->m_albaranentradalinea->update($l['nIdLinea'], $l))
						{
							$this->db->trans_rollback();
							$this->out->error($this->m_albaranentradalinea->error_message());
						}						
					}
					$link_l = format_enlace_cmd($id_n, site_url('compras/albaranentrada/index/' . $id_n));
					
					$ids[] = $link_l;
				}
			}
			$upd['fTotal'] = $data['fTotal'] - $supertotal;
			$upd['nLibros'] = $data['nLibros'] - $superlibros;
			
			if (!$this->reg->update($data['nIdAlbaran'], $upd))
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}

			$this->db->trans_commit();
			$this->out->dialog(TRUE, sprintf($this->lang->line('albaranentrada-dividir-ok'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Abrir el documento
	 * @param int $id Id del documento (separados por ; si hay varios)
	 * @return MSG
	 */
	function abrir($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.cerrar');
		$id = isset($id) ? $id : $this->input->get_post('id');
		if ($id)
		{
			$ids = is_string($id) ? preg_split('/\;/', $id) : $id;
			$count = 0;
			foreach ($ids as $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->abrir($id);
					if ($res === FALSE)
						$this->out->error($this->reg->error_message());
					$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('albaranentrada-abrir-history'));
					++$count;
				}
			}
			$this->out->success(sprintf($this->lang->line('albaranentrada-abrir-ok'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Obtiene las líneas de albarán a los pedidos pendientes e indica las líneas que
	 * se han de autopedir
	 * @param int $id Id del albarán
	 * @return mixed: string: error, array asignación
	 */
	private function _get_asignacion($id = null)
	{
		$data = $this->reg->get_lineas($id);
		$doc = $data['doc'];
		$lineas = $data['lineas'];

		//Lee los pedidos pendientes de cada línea
		$data = array();
		foreach ($lineas as $k => $linea)
		{
			$ct = $linea['nCantidad'] - $linea['nCantidadAsignada'];
			foreach ($linea['pendientes'] as $l2)
			{
				$asignar = min($ct, $l2['nPendientes']);
				$data[] = array(
						'id' => $linea['nIdLibro'] . '_' . $l2['nIdLinea'],
						'nIdLibro' => $linea['nIdLibro'],
						'dCreacion' => $linea['dCreacionArticulo'],
						'cCUser' => $linea['cCUserArticulo'],
						'cTitulo' => $linea['cTitulo'],
						'cISBN' => $linea['cISBN'],
						'cAutores' => $linea['cAutores'],
						'cEditorial' => $linea['cEditorial'],
						'nIdEditorial' => $linea['nIdEditorial'],
						'text' => "{$linea['cTitulo']} - Pend: ({$linea['nCantidad']}) Asig: ({$linea['nCantidadAsignada']}) - [{$linea['nIdLibro']}]",
						'nCantidad' => $linea['nCantidad'],
						'nCantidadAsignada' => $linea['nCantidadAsignada'],
						'nIdSeccion' => $l2['nIdSeccion'],
						'cSeccion' => $l2['cSeccion'] . (isset($l2['cConcurso'])?(' -> ' . $l2['cConcurso'].': '):'') . (isset($l2['cBiblioteca'])?($l2['cBiblioteca']):''),
						'nIdLineaPedido' => $l2['nIdLinea'],
						'nPedidoPendientes' => $l2['nPendientes'],
						'nIdPedido' => $l2['nIdPedido'],
						'dFechaEntrega' => $l2['dFechaEntrega'],
						'nDias' => $l2['nDias'],
						'nAsignar' => $asignar,
						'cConcurso' => isset($l2['cConcurso'])?$l2['cConcurso']:null,
						'cBiblioteca' => isset($l2['cBiblioteca'])?$l2['cBiblioteca']:null,
						'nIdLineaPedidoConcurso' => isset($l2['nIdLineaPedidoConcurso'])?$l2['nIdLineaPedidoConcurso']:null,
				);
				$ct -= $asignar;
			}
			if ($ct > 0)
			{
				$this->load->model('catalogo/m_articuloseccion');
				$secs = $this->m_articuloseccion->get(null, null, null, null, "nIdLibro={$linea['nIdLibro']}");
				$link_l = format_enlace_cmd($linea['cTitulo'], site_url('catalogo/articulo/index/' . $linea['nIdLibro']));
				if (count($secs) == 0)
				{
					return sprintf($this->lang->line('asignacion-no-secciones'), $link_l);
				}
					
				foreach ($secs as $sec)
				{
					$data[] = array(
							'id' => $linea['nIdLibro'] . '_s_' . $sec['nIdSeccion'],
							'nIdLibro' => $linea['nIdLibro'],
							'dCreacion' => $linea['dCreacionArticulo'],
							'cCUser' => $linea['cCUserArticulo'],
							'cTitulo' => $linea['cTitulo'],
							'cISBN' => $linea['cISBN'],
							'cAutores' => $linea['cAutores'],
							'cEditorial' => $linea['cEditorial'],
							'nIdEditorial' => $linea['nIdEditorial'],
							'text' => "{$linea['cTitulo']} - Pend: ({$linea['nCantidad']}) Asig: ({$linea['nCantidadAsignada']})",
							'nCantidad' => $linea['nCantidad'],
							'nCantidadAsignada' => $linea['nCantidadAsignada'],
							'nIdSeccion' => $sec['nIdSeccion'],
							'cSeccion' => $sec['cNombre'],
							'nIdLineaPedido' => null,
							'nPedidoPendientes' => null,
							'nIdPedido' => null,
							'dFechaEntrega' => null,
							'nDias' => null,
							'nAsignar' => $ct,
							'cConcurso' => null,
							'cBiblioteca' => null,
							'nIdLineaPedidoConcurso' => null,
					);
					$ct = 0;
				}
			}
		}
		sksort($data, 'cTitulo');
		return $data;
	}

	/**
	 * Asigna las líenas de albarán a los pedidos pendientes e indica las líneas que
	 * se han de autopedir
	 * @param int $id Id del albarán
	 * @return JSON
	 */
	function get_asignacion($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.asignar');
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (is_numeric($id))
		{
			$data = $this->_get_asignacion($id);
			if (!is_array($data))
				$this->out->error($data);
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Calcula un precio de venta
	 * @param int $id Id del albarán
	 * @param float $coste Coste del artículo
	 * @param float $gastos Gastos asociados al artículo
	 * @param float $iva % de IVA
	 * @param int $stock Stock actual
	 * @param int $tipo Tipo de artículo
	 * @param float $precio Precio original
	 * @param float $actual Precio actual 
	 * @param float $venta Precio actual albarán
	 * @return JSON array (fPrecioRecomendado, fPrecioAsignado)
	 */
	function precio($id = null, $coste = null, $gastos = null, $iva = null, $stock = null, $tipo = null, $precio = null, $actual = null, $venta = null)
	{
		$this->userauth->roleCheck($this->auth . '.precios');

		$id = isset($id) ? $id : $this->input->get_post('id');
		$coste = isset($coste) ? $coste : $this->input->get_post('coste');
		$gastos = isset($gastos) ? $gastos : $this->input->get_post('gastos');
		$iva = isset($iva) ? $iva : $this->input->get_post('iva');
		$stock = isset($stock) ? $stock : $this->input->get_post('stock');
		$tipo = isset($tipo) ? $tipo : $this->input->get_post('tipo');
		$precio = isset($precio) ? $precio : $this->input->get_post('precio');
		$venta = isset($venta) ? $venta : $this->input->get_post('venta');
		$actual = isset($actual) ? $actual : $this->input->get_post('actual');
		$original = $precio;

		$alb = $this->reg->load($id);
		// Es precio libre?
		if ($alb['bPrecioLibre'])
		{
			// Cambio divisa y margen
			$divisa_default = $this->config->item('bp.divisa.default');
			if (!isset($alb['nIdDivisa']))
				$alb['nIdDivisa'] = $divisa_default;
			if (!isset($alb['fPrecioCambio']))
			{
				$this->load->model('generico/m_divisa');
				$alb['fPrecioCambio'] = $d['fCompra'];
			}
			$margendivisa = ($alb['nIdDivisa'] != $divisa_default) ? $this->config->item('bp.divisa.margenmoneda') : 0;
		}
		else
		{
			$margendivisa = 0;
		}
		$tarifa = $this->config->item('ventas.tarifas.defecto');
		$this->load->model('ventas/m_tipotarifa');
		$tarifa = $this->m_tipotarifa->load($tarifa);
		$margentarifa = $tarifa['fMargen'];
		// Calcula el precio de venta según el margen de tienda
		// Carga el % de la tarifa por defecto
		$precio = (($coste * (1 + $margendivisa / 100)) / (1 - $margentarifa / 100)) + (($gastos) * (1 + $margendivisa / 100));
		// Aplicar IVA
		$precio = format_add_iva($precio, $iva);

		$pais = $this->config->item('bp.address.pais');
		$tipopais = array();
		if (is_numeric($alb['nIdPais']) && ($alb['nIdPais'] != $pais))
		{
			$this->load->model('compras/m_tipopais');
			$ivas = $this->m_tipopais->get(null, null, null, null, 'nIdPais=' . $alb['nIdPais']);
			if (count($ivas) > 0)
			{
				foreach ($ivas as $iva)
				{
					$tipopais[$iva['nIdTipo']] = $iva['fIVA'];
				}
			}
		}

		// Variación aceptable de la divisa
		$variacion = $this->config->item('compras.precio.variacion');
		$mantenerpreciosuperior = $this->config->item('bp.ventas.mantenerpreciosuperior');
		$margenoriginal = $this->config->item('bp.ventas.margenoriginal');

		// Aplicar el IVA del origen y si el precio final es mayor, aplicarlo
		if (isset($tipopais[$tipo]))
		{
			$original = $original * (1 + $margenoriginal / 100);
			$precio = max($precio, format_add_iva($original, $tipopais[$tipo]));
		}
		else
		{
			$precio = max($precio, $original);
		}
		// Redondear a 0.5
		$precio = format_redondear05($precio);

		// Recomendado
		// Si la variación es pequeña, recomendar el anterior (si hay stock)
		// Si no hay stock, y el precio anterior es superior, recomendar el anterior
		// Si hay precio asignado a mano en el albarán, recomendar el indicado
		$var = ($actual > 0) ? abs((($actual - $precio) / $actual) * 100) : 100;
		if ($venta > 0)
		{
			$pr['fPrecioRecomendado'] = format_decimals($precio);
			$pr['fPrecioAsignado'] = format_decimals($venta);
		}
		elseif ((($stock > 0) && ($var < $variacion)) || ((($precio < $actual) && $mantenerpreciosuperior)))
		{
			$pr['fPrecioRecomendado'] = format_decimals($precio);
			$pr['fPrecioAsignado'] = format_decimals($actual);
		}
		else
		{
			$pr['fPrecioRecomendado'] = format_decimals($precio);
			$pr['fPrecioAsignado'] = format_decimals($precio);
		}
		$pr['success'] = TRUE;
		$this->out->send($pr);
	}

	/**
	 * @param int $id Id del albarán
	 * @return JSON
	 */
	function get_precios($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.precios');
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (is_numeric($id))
		{
			$alb = $this->reg->load($id, 'lineas');
			$precios = array();
			// Es precio libre?
			if ($alb['bPrecioLibre'])
			{
				// Cambio divisa y margen
				$divisa_default = $this->config->item('bp.divisa.default');
				if (!isset($alb['nIdDivisa']))
					$alb['nIdDivisa'] = $divisa_default;
				if (!isset($alb['fPrecioCambio']))
				{
					$this->load->model('generico/m_divisa');
					$alb['fPrecioCambio'] = $d['fCompra'];
				}
				$margendivisa = ($alb['nIdDivisa'] != $divisa_default) ? $this->config->item('bp.divisa.margenmoneda') : 0;
			}
			else
			{
				$margendivisa = 0;
			}
			$this->load->model('catalogo/m_articuloseccion');
			// Una entrada por título, si está mas de una vez se unen
			foreach ($alb['lineas'] as $linea)
			{
				$idl = $linea['nIdLibro'];
				if (!isset($precios[$idl]))
				{
					// Stock en secciones
					$sec = $this->m_articuloseccion->get(null, null, null, null, "nIdLibro = {$idl} AND (nStockFirme > 0 OR nStockDeposito > 0 OR nStockServir > 0)");
					$stk = 0;
					$ped = 0;
					if (count($sec) > 0)
					{
						foreach ($sec as $s)
						{
							$stk += $s['nStockFirme'] + $s['nStockDeposito'];
							$ped += $s['nStockServir'];
						}
					}
					$precios[$idl] = array(
							'nCantidad' 	=> 0,
							'nStock' 		=> $stk,
							'nPedidos'		=> $ped,
							'fPrecioActual' => $linea['fPVPArticulo'],
							'fIVA' 			=> $linea['fIVAArticulo'],
							'nIdTipo' 		=> $linea['nIdTipo'],
							'cTitulo' 		=> $linea['cTitulo'],
							'nIdLibro' 		=> $linea['nIdLibro'],
							'fPrecio' 		=> $linea['fPrecio'],
							'fCoste' 		=> 0,
							'fGastos'	 	=> 0
					);
				}
				else
				{
					$precios[$idl]['fPrecio'] = max($precios[$idl]['fPrecio'], $linea['fPrecio']);
				}
				if ($alb['nIdEstado'] == ALBARAN_ENTRADA_STATUS_ASIGNADO)
					$precios[$idl]['nStock'] -= $linea['nCantidad'];
				$precios[$idl]['fPrecioVenta'] = format_decimals($linea['fPrecioVenta']);
				$precios[$idl]['nCantidad'] += $linea['nCantidad'];
				$precios[$idl]['fCoste'] += ($linea['nCantidad'] * $linea['fCoste']);
				$precios[$idl]['fGastos'] += ($linea['nCantidad'] * $linea['fGastos']);
			}
			$data = array();
			if ($alb['bPrecioLibre'])
			{
				// Carga la tarifa por defecto
				$tarifa = $this->config->item('ventas.tarifas.defecto');
				$this->load->model('ventas/m_tipotarifa');
				$tarifa = $this->m_tipotarifa->load($tarifa);
				$margentarifa = $tarifa['fMargen'];

				// Carga los ivas de los países de origen
				$tipopais = array();
				$pais = $this->config->item('bp.address.pais');
				if (is_numeric($alb['nIdPais']) && ($alb['nIdPais'] != $pais))
				{
					$this->load->model('compras/m_tipopais');
					$ivas = $this->m_tipopais->get(null, null, null, null, 'nIdPais=' . $alb['nIdPais']);
					if (count($ivas) > 0)
					{
						foreach ($ivas as $iva)
						{
							$tipopais[$iva['nIdTipo']] = $iva['fIVA'];
						}
					}
				}

				// Variación aceptable de la divisa
				$variacion = $this->config->item('compras.precio.variacion');
			}
			$mantenerpreciosuperior = $this->config->item('bp.ventas.mantenerpreciosuperior');
			$margenoriginal = $this->config->item('bp.ventas.margenoriginal');
			foreach ($precios as $pr)
			{
				$pr['fCoste'] = format_decimals($pr['fCoste'] / $pr['nCantidad']);
				$pr['fGastos'] = format_decimals($pr['fGastos'] / $pr['nCantidad']);
				$pr['fCoste'] = format_decimals($pr['fCoste']);
				$pr['fGastos'] = format_decimals($pr['fGastos']);
				$pr['fMagenDivisa'] = (1 + $margendivisa / 100);
				if ($alb['bPrecioLibre'])
				{
					// Calcula el precio de venta según el margen de tienda
					// Carga el % de la tarifa por defecto
					$precio = (($pr['fCoste'] * (1 + $margendivisa / 100)) / (1 - $margentarifa / 100)) + (($pr['fGastos']) * (1 + $margendivisa / 100));
					// Aplicar IVA
					$precio = format_add_iva($precio, $pr['fIVA']);
					// Aplicar el IVA del origen y si el precio final es mayor, aplicarlo
					if (isset($tipopais[$pr['nIdTipo']]))
					{
						$original = $pr['fPrecio'] * (1 + $margenoriginal / 100);
						$precio = max($precio, format_add_iva($original, $tipopais[$pr['nIdTipo']]));
					}
					else
					{
						$precio = max($precio, $pr['fPrecio']);
					}
					// Redondear a 0.5
					$precio = format_redondear05($precio);

					// Recomendado
					// Si la variación es pequeña, recomendar el anterior (si hay stock)
					// Si no hay stock, y el precio anterior es superior, recomendar el anterior
					// Si hay precio asignado a mano en el albarán, recomendar el indicado
					$var = ($pr['fPrecioActual'] > 0) ? abs((($pr['fPrecioActual'] - $precio) / $pr['fPrecioActual']) * 100) : 100;
					if ($pr['fPrecioVenta'] > 0)
					{
						$pr['fPrecioRecomendado'] = format_decimals($precio);
						$pr['fPrecioAsignado'] = format_decimals($pr['fPrecioVenta']);

					}
					elseif ((($pr['nStock'] > 0) && ($var < $variacion)) || ((($precio < $pr['fPrecioActual']) && $mantenerpreciosuperior)))
					{
						$pr['fPrecioRecomendado'] = format_decimals($precio);
						$pr['fPrecioAsignado'] = format_decimals($pr['fPrecioActual']);
					}
					else
					{
						$pr['fPrecioRecomendado'] = format_decimals($precio);
						$pr['fPrecioAsignado'] = format_decimals($precio);
					}
				}
				else
				{
					$pr['fPrecioRecomendado'] = format_decimals($pr['fPrecioVenta']);
					$pr['fPrecioAsignado'] = format_decimals($pr['fPrecioVenta']);
				}
				if ($pr['fPrecioAsignado']==0) $pr['fPrecioAsignado'] = $pr['fPrecioActual'];

				$data[] = $pr;
			}
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Asigna las líneas de albarán a la líneas de los pedidos.
	 * Primero crea los autopedidos de las unidadas que no se habían pedido y luego
	 * las asigna.
	 * @param int $id Id del albarán
	 * @param string $asig Relación de asignaciones separadas por ; en el formato
	 * IdLibro##IdLíneaPedido##Cantidad##NombreSeccion
	 * @param string $auto Relación de líneas autopediro separadas por ; en el
	 * formato IdLibro##IdSección##Cantidad##NombreSeccion
	 * @param string $cmpid Id del componente que realiza la tarea
	 * @return HTML_FILE
	 */
	function asignar($id = null, $asig = null, $auto = null, $cmpid = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$asig = isset($asig) ? $asig : $this->input->get_post('asig');
		$auto = isset($auto) ? $auto : $this->input->get_post('auto');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');

		if (is_numeric($id))
		{
			if ($asig !== FALSE || $auto != FALSE)
			{
				// Líneas asignadas
				if ($asig !== FALSE)
				{
					$asig = preg_split('/;/', $asig);
					foreach ($asig as $k => $a)
					{
						if (trim($a) != '')
						{
							$a = preg_split('/\#\#/', $a);
							if (count($a) == 7)
							{
								$asig[$k] = $a;
							}
							else
							{
								$this->out->error($this->lang->line('mensaje_faltan_datos'));
							}
						}
						else
						{
							unset($asig[$k]);
						}
					}
				}

				$this->load->library('Messages');
				$this->db->trans_begin();

				// Autopedido
				if ($auto !== FALSE)
				{
					$this->load->model('compras/m_pedidoproveedor');
					$this->load->model('compras/m_pedidoproveedorlinea');

					$data = $this->reg->get_lineas($id);
					$doc = $data['doc'];
					$lineas = $data['lineas'];

					$auto = preg_split('/;/', $auto);
					$pedido = array(
						'nIdProveedor' => $doc['nIdProveedor'],
						'nIdDireccion' => $doc['nIdDireccion'],
						'bDeposito' => $doc['bDeposito'],
						'cRefInterna' => sprintf($this->lang->line('asignacion-pedido-ref'), $id)
					);

					$idp = null;
					foreach ($auto as $a)
					{
						if (trim($a) != '')
						{
							$a = preg_split('/\#\#/', $a);
							if (count($a) == 4)
							{
								if (!isset($idp))
								{
									$idp = $this->m_pedidoproveedor->insert($pedido);
									if ($idp < 0)
									{
										$this->db->trans_rollback();
										$this->out->error($this->m_pedidoproveedor->error_message());
									}
									$link = format_enlace_cmd($idp, site_url('compras/pedidoproveedor/index/' . $idp));
									$this->messages->info(sprintf($this->lang->line('asignacion-creando-pedido'), $link));
								}
								$idl = $a[0];
								$ids = $a[1];
								$ct = $a[2];
								$sec = $a[3];
								$pedido_linea = array(
										'nIdPedido' => $idp,
										'nIdLibro' => $idl,
										'nIdSeccion' => $ids,
										'fDescuento' => $lineas[$idl]['lineas'][0]['fDescuento'],
										'fPrecio' => $lineas[$idl]['lineas'][0]['fPrecio'],
										'nCantidad' => $ct,
										'fIVA' => $lineas[$idl]['lineas'][0]['fIVA'],
										'fRecargo' => $lineas[$idl]['lineas'][0]['fRecargo'],
								);
								$idln = $this->m_pedidoproveedorlinea->insert($pedido_linea);
								if ($idln < 0)
								{
									$this->db->trans_rollback();
									$this->out->error($this->m_pedidoproveedorlinea->error_message());
								}
								$a[0] = $idl;
								$a[1] = $idln;
								$a[2] = $ct;
								$a[3] = $sec;
								$a[4] = $idp;
								$a[5] = $ids;
								$asig[] = $a;
								$link_l = format_enlace_cmd($idl, site_url('catalogo/articulo/index/' . $idl));
								$this->messages->info(sprintf($this->lang->line('asignacion-creando-linea'), $link_l, $lineas[$idl]['cTitulo'], $sec, $ct), 1);
							}
							else
							{
								$this->db->trans_rollback();
								$this->out->error($this->lang->line('mensaje_faltan_datos'));
							}
						}
					}
					// Cerrar el pedido
					if (isset($idp))
					{
						if (!$this->m_pedidoproveedor->cerrar($idp))
						{
							$this->db->trans_rollback();
							$this->out->error($this->m_pedidoproveedor->error_message());
						}
					}
				}

				// Asignación
				if (count($asig) > 0)
				{
					$res = $this->reg->asignar($id, $asig);
					if ($res === FALSE)
					{
						$this->db->trans_rollback();
						$this->out->error($this->reg->error_message());				
					}
					$messages = array();
					foreach ($res as $r)
					{
						$link_l = format_enlace_cmd($r['linea'], site_url('catalogo/articulo/index/' . $r['linea']));
						$link_pd = format_enlace_cmd($r['pedido'], site_url('compras/pedidoproveedor/index/' . $r['pedido']));
						$extra = isset($r['cBiblioteca'])?sprintf($this->lang->line('asignacion-asignando-concurso'), $r['cConcurso'], $r['cBiblioteca']):'';

						$message = sprintf($this->lang->line('asignacion-asignando'), $link_l, $r['titulo'], $link_pd, $r['seccion'], $r['cantidad'], $extra);
						$messages[$r['seccion']][] = $message;
						$this->messages->info($message);
						$this->_add_nota(null, $id, NOTA_INTERNA, $message);
					}
					$this->messages->info('<hr/>');
					foreach ($messages as $key => $value) 
					{
						$this->messages->info('<strong>' . $key . '</strong>');
						foreach($value as $message)
						{
							$this->messages->info($message, 1);
						}				
					}
				}

				$this->db->trans_commit();
				$body = $this->messages->out($this->lang->line('Asignación de albarán') . ' ' . $id);
				$this->load->library('HtmlFile');

				$filename = $this->htmlfile->create($body, $this->lang->line('Asignación de albarán') . ' ' . $id);
				$url = $this->htmlfile->url($filename);

				$message = sprintf($this->lang->line('albaran-asignado-ok'), $url);

				$this->out->dialog(TRUE, $message);
			}
			else
			{
				// Formulario
				$this->_show_js('asignar', 'compras/asignar.js', array(
						'id' => $id,
						'cmpid' => $cmpid
				));
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Quita la asignación de las líneas de un albarán a un pedido de proveedor
	 * @param int $id Id del albarán de entrada
	 * @return MSG
	 */
	function desasignar($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (is_numeric($id))
		{
			// Actualiza el estado del albarán de entrada
			if (!$this->reg->desasignar($id))
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}

			$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('albaranentrada-desasignado-nota'));
			$this->out->success($this->lang->line('albaranentrada-desasignado-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra un formulario para comprobar las secciones de la asignación de un albarán de entrada
	 * @param  int $id Id del albarán
	 * @return FORM
	 */
	function check_asignacion($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (is_numeric($id))
		{
			// Formulario
			$this->_show_js('asignar', 'compras/check_asignar.js', array('id' => $id));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Comprueba la asignación de un albarán dado un código
	 * @param  int $id   Id del albarán
	 * @param  string $code Código a comprobar
	 * @return DATA
	 */
	function check($id = null, $code = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$code = isset($code) ? $code : $this->input->get_post('code');
		$data['success'] = TRUE;
		$data['text'] = $this->lang->line('dilve-articulo-error-notfound');
		$data['titulo'] = null;
		if (is_numeric($id) && !empty($code))
		{
			if (trim($code) != '') 
			{
				$this->load->library('ISBNEAN');
				$this->load->model('catalogo/m_articulocodigo');
				$this->load->model('catalogo/m_articulo');
				$isbn = $this->isbnean->to_ean($code);
				$idl = null;
				$titulo = null;
				if ($isbn)
				{
					$l = $this->m_articulocodigo->get(null, null, null, null, 'nCodigo=' . $isbn);
					#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();
					#var_dump($l); die();
					if (count($l)>0)
					{
						$l = $this->m_articulo->load($l[0]['nIdLibro']);
						$titulo = $l['cTitulo'];
						$idl = $l['nIdLibro'];
					}
				}
				else
				{
					if (is_numeric($code))
					{
						$l = $this->m_articulo->load($code);
						if ($l)
						{
							$idl = $code;
							$titulo = $l['cTitulo'];
						}
					}			
				}
				if (isset($idl))
				{
					$res = $this->reg->get_asignacion($id, $idl);
					if ($res)
					{
						$textos = array();
						foreach($res as $l2)
						{
							#var_dump($l2); die();
							$textos[] = $l2['cSeccion'] . (isset($l2['cConcurso'])?('-> <span style="color: orange;">' . $l2['cConcurso'].'</span> '):'') 
							. (isset($l2['cBiblioteca'])?('- <span style="color: red;">'.$l2['cBiblioteca'].'</span>'):'')
							. (isset($l2['cSala'])?(' - <span style="color: blue;">' . $l2['cSala'].'</span> '):'');
						}
						sort($textos);
						$data['text'] = implode('<br/>', $textos);
						$this->load->model('compras/m_albaranentradalineavisto');
						$res = $this->m_albaranentradalineavisto->get(null, null, null, null, "nIdLibro={$idl} AND nIdAlbaran={$id}");
						if (count($res) == 0)
						{
							$this->m_albaranentradalineavisto->insert(array('nIdLibro' => $idl, 'nIdAlbaran' => $id));
						}
					}
				}
				$data['titulo'] = $titulo;
			}
		}
		$this->out->send($data);
	}

	/**
	 * Muestra los títulos consultados desde la asignación de un albarán de entrada
	 * @param int $id Id del albarán de entrada
	 * @return HTML_FILE
	 */
	function consultados($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (is_numeric($id))
		{
			$data['titulos'] = $this->reg->consultados($id);
			$data['id'] = $id;

			$body = $this->load->view('compras/consultados', $data, TRUE);
			$this->out->html_file($body, $this->lang->line('Artículos no consultados'), 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra asignación de las líneas de un albarán a un pedido de proveedor
	 * @param int $id Id del albarán de entrada
	 * @return HTML_FILE
	 */
	function asignacion($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (is_numeric($id))
		{
			// Carga los modelos y datos
			$this->load->library('Messages');
			$this->load->model('compras/m_pedidoproveedorlinearecibida');
			#$this->load->model('compras/m_albaranentradalinea');
			$this->load->model('compras/m_pedidoproveedorlinea');
			#$this->load->model('catalogo/m_articuloseccion');
			$alb = $this->reg->load($id, 'lineas');

			// Para cada línea
			$asig = array();
			$new = array();
			$concursos = array();
			foreach ($alb['lineas'] as $linea)
			{
				$idl = $linea['nIdLibro'];
				$link_l = format_enlace_cmd($idl, site_url('catalogo/articulo/index/' . $idl));
				$data = $this->m_pedidoproveedorlinearecibida->get(null, null, null, null, "nIdLineaAlbaran={$linea['nIdLinea']}");
				foreach ($data as $l)
				{
					$extra = isset($l['cBiblioteca'])?sprintf($this->lang->line('asignacion-asignando-concurso'), $l['cConcurso'], $l['cBiblioteca']):'';
					$lnpd = $this->m_pedidoproveedorlinea->load($l['nIdLineaPedido']);
					$link_pd = format_enlace_cmd($lnpd['nIdPedido'], site_url('compras/pedidoproveedor/index/' . $lnpd['nIdPedido']));
					$message = sprintf($this->lang->line('asignacion-asignando'), $link_l, $linea['cTitulo'], $link_pd, $lnpd['cSeccion'], $l['nCantidad'], $extra);
					$this->messages->info($message);
					$new[$lnpd['cSeccion']][] = $message;
					if (isset($l['cBiblioteca']))
						$concursos[$l['cConcurso'] . ': ' . $l['cBiblioteca']][] = $message;
				}
			}
			$this->messages->info('<hr/>');
			foreach ($new as $key => $value) 
			{
				$this->messages->info('<strong>' . $key . '</strong>');
				foreach($value as $message)
				{
					$this->messages->info($message, 1);
				}				
			}
			if (count($concursos) > 0)
			{
				$this->messages->info('<hr/>');
				foreach ($concursos as $key => $value) 
				{
					$this->messages->info('<strong>' . $key . '</strong>');
					foreach($value as $message)
					{
						$this->messages->info($message, 1);
					}				
				}
			}
			$message = $this->messages->out($this->lang->line('Asignación de albarán') . ' ' . $id);
			#print $message; die();
			$this->out->html_file($message, $this->lang->line('Asignación de albarán') . ' ' . $id, 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Asigna los precios
	 * @param int $id Id del albarán
	 * @param string $precios Precios a asignar
	 * @param bool $etq Generar etiquetas
	 */
	function precios($id = null, $precios = null, $etq = null, $cmpid = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$precios = isset($precios) ? $precios : $this->input->get_post('precios');
		$etq = isset($etq) ? $etq : $this->input->get_post('etq');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');

		if (is_numeric($id))
		{
			if ($precios === FALSE)
			{
				// Formulario
				$this->_show_js('asignar', 'compras/precios.js', array(
						'id' => $id,
						'cmpid' => $cmpid
				));
			}
			else
			{
				// Limpiamos entrada
				$asig = preg_split('/;/', $precios);
				$precios = array();
				foreach ($asig as $k => $a)
				{
					if (trim($a) != '')
					{
						$a = preg_split('/\#\#/', $a);
						if (count($a) == 3)
						{
							$precios[$a[0]] = array(
									'fPVP' => $a[1],
									'fGastos' => $a[2]
							);
							$asig[$k] = $a;
						}
						else
						{
							$this->out->error($this->lang->line('mensaje_faltan_datos'));
						}
					}
					else
					{
						unset($asig[$k]);
					}
				}
				#var_dump($precios); die();
				$etq = format_tobool($etq);
				$this->load->model('catalogo/m_articulo');
				$etiquetas = array();
				$alb = $this->reg->load($id, 'lineas');
				// Lee el stock
				$count = 0;
				foreach ($alb['lineas'] as $linea)
				{
					$idl = $linea['nIdLibro'];
					if (isset($precios[$idl]))
					{
						if (!isset($etiquetas[$idl]))
						{
							// Stock en secciones y precio
							$libro = $this->m_articulo->load($idl, array('secciones'));
							#$this->out->error('<pre>' . print_r($libro, TRUE). '</pre>');
							if ($libro['fPVP'] == $precios[$idl]['fPVP'] && $libro['bPrecioLibre'] == FALSE)
							{
								// No cambia de precio
								$etiquetas[$idl] = FALSE;
							}
							else
							{
								++$count;
								// Cambia de precio, comprueba stock
								$etiquetas[$idl]['fPVP'] = $precios[$idl]['fPVP'];
								$etiquetas[$idl]['fGastos'] = $precios[$idl]['fGastos'];
								$etiquetas[$idl]['fIVA'] = $libro['fIVA'];
								if (isset($libro['secciones']) > 0)
								{
									foreach ($libro['secciones'] as $s)
									{
										$etiquetas[$idl]['secciones'][$s['nIdSeccion']] = array(
												'cNombre' => $s['cNombre'],
												'nStockFirme' => $s['nStockFirme'],
												'nStockDeposito' => $s['nStockDeposito'],
										);
									}
								}
							}
						}
					}
				}

				if ($count > 0)
				{
					// Quita el asignado por el albarán
					if ($alb['nIdEstado'] == ALBARAN_ENTRADA_STATUS_ASIGNADO)
					{
						// Lee la asignación
						$asig = $this->reg->get_asignacion($id);
						#print '<pre>'; print_r($etiquetas); print_r($asig);
						foreach ($asig as $a)
						{
							$idl = $a['nIdLibro'];
							if (isset($etiquetas[$idl]['secciones'][$a['nIdSeccion']]))
							{
								if ($alb['bDeposito'])
								{
									$etiquetas[$idl]['secciones'][$a['nIdSeccion']]['nStockDeposito'] -= $a['nCantidad'];
								}
								else
								{
									$etiquetas[$idl]['secciones'][$a['nIdSeccion']]['nStockFirme'] -= $a['nCantidad'];
								}
							}
						}
						#print print_r($etiquetas); print '</pre>'; die();
					}

					// Crea la cola para imprimir las etiquetas
					if ($etq)
					{
						$this->load->model('catalogo/m_grupoetiqueta');
						$lineas = array();
						foreach ($etiquetas as $idl => $l)
						{
							if ($l !== FALSE)
							{
								foreach ($l['secciones'] as $ids => $sec)
								{
									if ($sec['nStockDeposito'] > 0)
									{
										$lineas[] = array(
												'nIdLibro' => $idl,
												'nIdSeccion' => $ids,
												'nCantidad' => $sec['nStockDeposito'],
												'cSimbolo' => $this->lang->line('simbolo-deposito'),
												'fPVP' => $l['fPVP']
										);
									}
									if ($sec['nStockFirme'] > 0)
									{
										$lineas[] = array(
												'nIdLibro' => $idl,
												'nIdSeccion' => $ids,
												'nCantidad' => $sec['nStockFirme'],
												'cSimbolo' => $this->lang->line('simbolo-firme'),
												'fPVP' => $l['fPVP']
										);
									}
								}
							}
						}
						if (count($lineas) > 0)
						{
							$ide = $this->m_grupoetiqueta->insert(array(
									'cDescripcion' => sprintf($this->lang->line('descripcion-etiquetas-albaran'), $id),
									'lineas' => $lineas
							));
							if ($ide < 0)
								$this->out->error($this->m_grupoetiqueta->error_message);
						}
					}

					// Asignamos precios
					$this->load->model('catalogo/m_articulo');
					$this->load->model('catalogo/m_articulotarifa');
					$this->load->model('ventas/m_tipotarifa');
					$this->db->trans_begin();
					$count = 0;
					#echo '<pre>'; print_r($etiquetas); echo '</pre>'; $this->db->trans_rollback();
					#die();
					// Carga los tipos de tarifa
					if ($alb['bPrecioLibre'])
					{
						$tf = $this->m_tipotarifa->get();
						foreach ($tf as $t)
						{
							$tipostarifa[$t['nIdTipoTarifa']] = $t;
						}
						$tarifa_defecto = $this->config->item('ventas.tarifas.defecto');
					}
					// Actualiza los precios de los artículos
					foreach ($etiquetas as $k => $a)
					{
						if ($a != FALSE)
						{
							$precio = format_quitar_iva($a['fPVP'], $a['fIVA']);
							#var_dump($precio); die();
							$upd = array(
								'fPrecio' => $precio,
								'bPrecioLibre' => $alb['bPrecioLibre']
							);

							// Actualiza las tarifas
							// Elimina las anteriores
							if (!$this->m_articulotarifa->delete_by('nIdLibro=' . $k))
							{
								$this->db->trans_rollback();
								$this->out->error($this->m_articulotarifa->error_message());
							}
							// Crea las nuevas
							if ($alb['bPrecioLibre'])
							{
								$coste = format_decimals(($precio - $a['fGastos']) * (1 - $tipostarifa[$tarifa_defecto]['fMargen'] / 100));
								#echo "PR: {$precio} GT: {$a['fGastos']} MG: {$tipostarifa[$tarifa_defecto]['fMargen']} CT: {$coste}";
								$tarifas = array();
								foreach ($tipostarifa as $t => $v)
								{
									if ($t == $tarifa_defecto)
									{
										$pr2 = $precio;
									}
									else
									{
										$pr2 = format_decimals(($coste / (1 - $v['fMargen'] / 100)) + $a['fGastos']);
										#echo "PR: {$precio} GT: {$a['fGastos']} MG:
										# {$tipostarifa[$tarifa_defecto]['fMargen']} CT: {$coste} PR:{$pr2}<br/>";
										$pr2 = format_add_iva($pr2, $a['fIVA']);
										// Redondear a 0.5
										$pr2 = format_redondear05($pr2);
										$pr2 = format_quitar_iva($pr2, $a['fIVA']);										
									}
									$tarifas[] = array(
											'nIdTipoTarifa' => $t,
										#'nIdLibro' 			=> $k,
											'fPrecio' => $pr2
									);
								}
								$upd['tarifas'] = $tarifas;

								#echo '<pre>'; print_r($tarifas); echo '</pre>'; $this->db->trans_rollback();
								#die();
							}
							if (!$this->m_articulo->update($k, $upd))
							{
								$this->db->trans_rollback();
								$this->out->error($this->m_articulo->error_message());
							}
							++$count;
						}
					}
					// Actualiza los PVP en las líneas de albarán
					#print '<pre>'; print_r($alb['lineas']); print '</pre>'; die();

					if ($alb['bPrecioLibre'])
					{
						foreach ($alb['lineas'] as $linea)
						{
							$idl = $linea['nIdLibro'];
							$pvp = (isset($etiquetas[$idl]) && $etiquetas[$idl] !== FALSE) ? $etiquetas[$idl]['fPVP'] : $linea['fPVPArticulo'];
							if (!$this->m_albaranentradalinea->update($linea['nIdLinea'], array('fPrecioVenta' => $pvp)))
							{
								$this->db->trans_rollback();
								$this->out->error($this->m_albaranentradalinea->error_message());
							}
						}
					}
					#$this->db->trans_rollback(); die();
					$this->db->trans_commit();

					// Respuesta
					$message[] = sprintf($this->lang->line('albaran-actualizar-precios-ok'), $count);
					if ($etq)
					{
						if (isset($ide))
						{
							$link = format_enlace_cmd($ide, site_url('catalogo/grupoetiqueta/imprimir/' . $ide));
							$message[] = sprintf($this->lang->line('albaran-cambios-precios-secciones'), $link);
						}
						else
						{
							$message[] = $this->lang->line('albaran-cambios-precios-secciones-no');
						}
					}
					$this->out->dialog(TRUE, implode('<br/>', $message));
				}
				else
				{
					if ($alb['bPrecioLibre'])
					{
						$this->db->trans_begin();
						// Actualiza los PVP en las líneas de albarán
						#print '<pre>'; print_r($alb['lineas']); print '</pre>';
						foreach ($alb['lineas'] as $linea)
						{
							if (!$this->m_albaranentradalinea->update($linea['nIdLinea'], array('fPrecioVenta' => $linea['fPVPArticulo'])))
							{
								$this->db->trans_rollback();
								$this->out->error($this->m_albaranentradalinea->error_message());
							}
						}
						$this->db->trans_commit();
					}
					$this->out->success($this->lang->line('albaran-actualizar-precios-nocambios'));
				}
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Procedimiento de impresión de etiquetas agrupadas
	 * @param int $id Id del grupo de etiquetas
	 * @param int $ids Id de la sección (-1/null todas)
	 * @param int $ida Id de la etiqueta
	 * @param string $report Formato de impresión por defecto
	 * @return MSG, FORM
	 */
	function etiquetas($id = null, $report = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$report = isset($report) ? $report : $this->input->get_post('report');

		if (is_numeric($id))
		{
			$this->_show_js('get_list', 'compras/etiquetas.js', array('id' => $id));
			if ($report === FALSE || $report = '')
			{
				$this->load->library('Configurator');
				$report = $this->configurator->user('compras.etiquetas.formato');
			}
			$html = $this->show_report(null, array('etiquetas' => $data), $report, null, FALSE, null, FALSE, FALSE);
			$this->out->success($html);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Genera un árbol con las etiquetas
	 * @param int $id Id de la sección
	 * @return array
	 */
	private function _get_tree($id = null, $list = FALSE)
	{
		$nodos = array();
		$data = $this->reg->get_asignacion($id);
		if (count($data) == 0 || $list)
		{
			// No hay asignación, imprimos todas
			$data = $this->reg->load($id, 'lineas');
			if (count($data['lineas']) == 0)
				$this->out->error($this->lang->line('no-etiquetas'));
			$simbolo = $this->lang->line(($data['bDeposito']) ? 'D' : 'F');
			sksort($data['lineas'], 'dCreacion', FALSE);
			foreach ($data['lineas'] as $l)
			{
				$l['text'] = $l['dCreacion'] . '-<b>' . $l['cTitulo'] . '</b>';
				$l['qtip'] = $l['nIdLibro'] . '-' . $l['cTitulo'];
				$l['id'] = $l['nIdLinea'];
				$l['uiProvider'] = 'col';
				$l['iconCls'] = 'icon-seccion';
				$l['leaf'] = TRUE;
				$l['cSimbolo'] = $simbolo;
				$l['fPVP'] = isset($l['fPrecioVenta']) ? $l['fPrecioVenta'] : $l['fPVPArticulo'];
				$nodos[] = $l;
			}
			return $nodos;
		}
		$alb = $this->reg->load($id);
		$simbolo = $this->lang->line(($alb['bDeposito']) ? 'D' : 'F');
		// Construye el árbol
		foreach ($data as $linea)
		{
			$linea['cSimbolo'] = $simbolo;
			$nodos[$linea['nIdSeccion']][] = $linea;
		}

		return $this->_imprimir_tree($nodos);
	}

	/**
	 * Genera un árbol con las etiquetas por imprimir (uso interno)
	 * @param array $nodos Array de [seccion] => líneas
	 * @param int $id Id de la sección a analizar
	 * @return array
	 */
	private function _imprimir_tree(&$nodos, $id = null)
	{
		$this->load->model('generico/m_seccion');
		$tree = $this->m_seccion->get_by_padre($id);

		$tree2 = array();
		foreach ($tree as $k => $t)
		{
			$children = $this->_imprimir_tree($nodos, $t['nIdSeccion']);
			if (count($children) > 0 || isset($nodos[$t['nIdSeccion']]))
			{
				$n['text'] = $t['cNombre'];
				$n['qtip'] = $t['nIdSeccion'] . '-' . $t['cNombre'];
				$n['id'] = $t['nIdSeccion'];
				$n['uiProvider'] = 'col';
				$n['iconCls'] = 'icon-seccion-folder';
				$n['leaf'] = FALSE;
				$n['children'] = $children;
				$n['cSimbolo'] = '-';
				$n['fPVP'] = '-';
				$n['dCreacion'] = $t['cNombre'];

				$cantidad = 0;
				if (count($children) > 0)
				{
					foreach ($children as $c)
					{
						$cantidad += $c['nCantidad'];
					}
				}
				if (isset($nodos[$t['nIdSeccion']]))
				{
					foreach ($nodos[$t['nIdSeccion']] as $l)
					{
						$l['text'] = $l['dCreacion'] . '-<b>' . $l['cTitulo'] . '</b>';
						$l['qtip'] = $l['nIdLibro'] . '-' . $l['cTitulo'];
						$l['id'] = $l['nIdLinea'];
						$l['uiProvider'] = 'col';
						$l['iconCls'] = 'icon-seccion';
						$l['leaf'] = TRUE;
						$n['children'][] = $l;
						$cantidad += $l['nCantidad'];
					}
				}
				$n['nCantidad'] = $cantidad;
				$tree2[] = $n;
			}
		}
		return $tree2;
	}

	/**
	 * Genera un árbol con las etiquetas del grupo
	 * @param int $id Id de la sección
	 * @return DATA
	 */
	function get_tree($id = null, $list = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$list = isset($list) ? $list : $this->input->get_post('list');
		if ($list !== FALSE)
			$list = format_tobool($list);
		if (is_numeric($id))
		{
			$tree = $this->_get_tree($id, $list);
			$this->out->send($tree);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Calcula cuando se compró la última vez a un proveedor
	 * @return MSG
	 */
	function ultimacompra()
	{
		$this->load->library('Configurator');
		$fecha = (int)$this->configurator->system('bp.albaranentrada.ultimacompra');
		$last = time();
		if ($this->reg->ultimacompra($fecha))
		{
			$this->configurator->set_system('bp.albaranentrada.ultimacompra', (string)$last);
			$this->out->success($this->lang->line('utlimacompra-act-ok'));
		}
		$this->out->error($this->reg->error_message());
	}

	/**
	 * Información para el envío de los pedidos
	 * @param int $id Id del pedido
	 * @return array, información para el envío
	 */
	protected function _get_profile_sender($id)
	{
		$this->load->model('proveedores/m_email');
		$this->load->model('proveedores/m_telefono');
		$pd = $this->reg->load($id, TRUE);
		$pd['suscripcion'] = $this->reg->get_suscripcion($id);
		$subject = $this->lang->line('albaranentrada-subject-email');
		$subject = str_replace('%id%', $id, $subject);
		return array(
				'perfil' => PERFIL_PEDIDO,
				'emails' => $this->m_email,
				'faxes' => $this->m_telefono,
				'report_email' => $this->config->item('sender.albaranentrada'),
				'report_normal' => $this->_get_report_default(),
				'report_lang' => (isset($pd['proveedor']['cIdioma']) && trim($pd['proveedor']['cIdioma']) != '') ? $pd['proveedor']['cIdioma'] : (isset($pd['direccion']) ? $pd['direccion']['cIdioma'] : null),
				'subject' => $subject,
				'data' => $pd,
				'css' => $this->config->item('bp.documentos.css'),
				'id' => $pd['nIdProveedor']
		);
	}

	/**
	 * Importa un fichero EXCEL como pedido del cliente
	 * @param int $proveedor Id del proveedor
	 * @param string $file Fichero EXCEL de <upload> a importar
	 * @param string $filtro Ramgo EXCEL a tratar
	 * @param bool $crear TRUE: Crear el pedido, FALSE: solo analiza
	 * @param string $ref Referencia interna del cliente
	 * @param float $dto Descuento a aplicar
	 * @param int $seccion Sección de las líneas de pedido
	 */
	function importar($proveedor = null, $file = null, $rango = null, $crear = null, $ref = null, $dto = null, $seccion = null, $crear_libros = null)
	{

		$this->userauth->roleCheck(($this->auth.'.add'));
		
		$file = isset($file) ? $file : $this->input->get_post('file');
		$proveedor 	= isset($proveedor)?is_null_str($proveedor):$this->input->get_post('proveedor');
		$rango 		= isset($rango)?is_null_str($rango):$this->input->get_post('rango');
		$crear 		= isset($crear)?is_null_str($crear):$this->input->get_post('crear');
		$ref		= isset($ref)?is_null_str($ref):$this->input->get_post('ref');
		$dto		= isset($dto)?is_null_str($dto):$this->input->get_post('dto');
		$seccion	= isset($seccion)?is_null_str($seccion):$this->input->get_post('seccion');
		$crear_libros = isset($crear_libros)?is_null_str($crear_libros):$this->input->get_post('crear_libros');
		
		if (empty($file))
		{
			$this->_show_js('excel', 'concursos/excel.js', array('prv' => TRUE, 'seccion' => FALSE, 'url' => 'compras/albaranentrada/importar'));
		}
		
		$files = preg_split('/;/', $file);
		$files = array_unique($files);
		$count = 0;
		if (isset($ref)) $ref = urldecode($ref);

		foreach ($files as $k => $file)
		{
			if (!empty($file))
			{
				$this->load->library('UploadLib');
				$file = urldecode($file);
				$name = $file;
				$file = $this->uploadlib->get_pathfile($file);
				set_time_limit(0);
	
				$this->load->library('Messages');
				$this->load->library('Importador');
	
				$crear = format_tobool($crear);
				$crear_libros = format_tobool($crear_libros);
	
				$this->db->trans_begin();
				$data = $this->importador->excel_generic($file, $rango, $crear_libros, FALSE, FALSE, $this->messages, $this->lang);
				// Crea el pedido
				$error = ($data === FALSE);
				#var_dump($creados); die();
				if ($data !== FALSE && is_numeric($proveedor) && $crear)
				{
					if ((count($data['libros']) == 0))
					{
						$this->messages->error($this->lang->line('concurso_creando_pedido_nolibros'));
					}
					else
					{
						$this->load->model('proveedores/m_proveedor');
						$c = $this->m_proveedor->load($proveedor);
						$this->messages->info(sprintf($this->lang->line('concurso_creando_pedido'), format_name($c['cNombre'], $c['cApellido'], $c['cEmpresa'])));

						$dto = isset($dto)?$dto:0;
						$this->messages->info(sprintf($this->lang->line('concurso_usando_datos'), $dto, $ref));
							
						foreach($data['libros'] as $k => $l)
						{
							$data['libros'][$k]['nCantidad'] = isset($l['original']['cantidad'])?$l['original']['cantidad']:1;
							$data['libros'][$k]['fDescuento'] = isset($l['original']['descuento'])?$l['original']['descuento']:$dto;
							$data['libros'][$k]['fPrecio'] = isset($l['original']['precio'])?$l['original']['precio']:0;
							$data['libros'][$k]['fPrecioVenta'] = isset($l['original']['pvp'])?$l['original']['pvp']:null;
						}
						$this->load->model('compras/m_albaranentrada');
						$id = $this->importador->crear_documento_generico(FALSE, $proveedor, $data['libros'], $this->m_albaranentrada, FALSE, $ref, $ref);
						if ($id === FALSE)
						{
							$this->messages->error($this->importador->get_error_message());
							$error = TRUE;
						}
						else
						{
							$link = format_enlace_cmd($id, site_url('compras/albaranentrada/index/' . $id));
							$this->messages->info(sprintf($this->lang->line('concurso_albaran_creado'), $link));
						}
					}
				}
				else
				{
					$this->messages->warning($this->lang->line('concurso_no_crear_albaran'));
				}
				#var_dump($error); die();

				($error)?$this->db->trans_rollback():$this->db->trans_commit();
			}
		}
		$body = $this->messages->out($this->lang->line('Importar EXCEL'));
		$this->out->html_file($body, $this->lang->line('Importar EXCEL'), 'iconoConcursosImportarEXCELTab');
	}

	/**
	 * Hook para las llamadas después de leer los datos
	 * @param int $id Id del registro
	 * @param mixed $relations Relaciones
	 * @param array $data Datos leídos
	 * @return bool
	 */
	protected function _post_get($id, $relations, &$data, $cmpid = null)
	{
		parent::_post_get($id, $relations, $data, $cmpid);
		$data['suscripcion'] = $this->reg->get_suscripcion($id);
		return TRUE;
	}

	/**
	 * Función interna llamada antes de imprimir
	 * @param int $id Id del registro
	 * @param array $data Registro
	 * @param string $css Fichero CSS a aplicar
	 * @return bool
	 */
	protected function _pre_printer($id, &$data, &$css)
	{
		$data['suscripcion'] = $this->reg->get_suscripcion($id);
		return parent::_pre_printer($id, $data, $css);
	}
	
	/**
	 * Importar albaranes desde SINLI
	 * @return FORM
	 */
	function sinli()
	{
		// Formulario
		$this->_show_form('add', 'compras/sinlialbaranentrada.js', $this->lang->line('Importar SINLI'));
	}

	/**
	 * Devuelve las líneas de pedido que esperan libros del albarán de entrada
	 * @param int $id Id del registro
	 * @return HTML_FILE
	 * 	 */
	function pedidoscliente($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (is_numeric($id))
		{
			$reg = $this->reg->pedidoscliente($id);
			if (count($reg) > 0)
			{
				foreach ($reg as $k => $v)
				{
					$reg[$k]['cCliente'] = format_name($v['cNombre'], $v['cApellido'], $v['cEmpresa']);
				}
				$data['nIdAlbaran'] = $id;
				$data['pedidos'] = $reg;
				$message = $this->load->view('compras/pedidoscliente', $data, TRUE);
	
				$this->out->html_file($message, $this->lang->line('Pedidos cliente') . ' ' . $id, 'iconoReportTab');
			}
			else
			{
				$this->out->message($this->lang->line('Pedidos cliente'), $this->lang->line('no-hay-pedidos-cliente'));		
			}
			#echo '<pre>'; print_r($data); echo '</pre>'; die();
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Obtiene las alternativa para los albaranes de entrada de una devolución
	 * @param  int $idl Id de la línea
	 * @return DATA
	 */
	function get_albaranesentrada($idl = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$idln = isset($idln) ? $idln : $this->input->get_post('idln');
		if (is_numeric($idln))
		{
			$this->load->model('compras/m_albaranentradalinea');
			$l = $this->reg->load($idln);
			if ($l)
			{
				$data = $this->m_albaranentradalinea->get(0, 10, 'dCreacion', 'DESC', "nIdLibro={$l['nIdLibro']} AND nCantidadDevuelta + {$l['nCantidad']} <= nCantidadReal AND nIdLinea <> {$l['nIdLineaAlbaran']} AND nIdEstado=2");
				$this->out->data($data);
			}
			$this->out->data(array());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra las líneas de depósito liquidadas
	 * @param int $id Id del albarán de entrada
	 * @return HTML_FILE
	 */
	function liquidacion($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (is_numeric($id))
		{
			// Carga los modelos y datos
			$this->load->library('Messages');
			$data = $this->reg->liquidacion($id);
			#var_dump($data); die();

			$data = array(
				'lineas'	=> $data,
				'id'		=> $id
				);

			$message = $this->load->view('compras/liquidacion', $data, TRUE);
			$this->out->html_file($message, $this->lang->line('Liquidación depósitos') . ' ' . $id, 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

}

/* End of file albaranentrada.php */
/* Location: ./system/application/controllers/compras/albaranentrada.php */
