<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-20100, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Facturas
 *
 */
class Factura extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Factura
	 */
	function __construct($model = null)
	{
		if (!isset($model)) $model = 'ventas/M_factura';

		parent::__construct('ventas.factura', $model, TRUE, null, 'Factura');
	}

	/**
	 * Ventana de facturación
	 * @param int $open_id ID de la factura a abrir
	 * @return TAB
	 */
	function index($open_id = null)
	{
		$this->userauth->roleCheck($this->auth .'.tpv');
		$open_id = isset($open_id)?$open_id:$this->input->get_post('open_id');
		$data['allsecciones'] = TRUE;
		$data['Cerrar'] = FALSE;
		$data['tpv'] = FALSE;
		#$data['descuento'] = ($this->config->item('ventas.tpv.aplicardescuento'))?$this->config->item('ventas.tpv.descuento'):0;
		$this->load->library('Configurator');
		$this->_show_form('tpv', 'ventas/tpv.js', $this->lang->line('Facturación'), null, null, $open_id, $data, 'iconoFacturacionTab');
	}

	/**
	 * Abre el cajón portamonedas
	 * @return JS
	 */
	function openbox()
	{
		$this->userauth->roleCheck($this->auth .'.tpv');
		$this->_show_js('tpv', 'ventas/openbox.js');
	}

	/**
	 * Cierra la factura
	 * @param int $id Id de la factura
	 * @return MSG
	 */
	function cerrar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.cerrar');
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			//if ()
			$numero = $this->reg->cerrar($id);
			if ($numero === FALSE) $this->out->error($this->reg->error_message());
			$factura = format_numerofactura($numero['numero'], $numero['serie']);

			$data = $this->reg->load($id);
			$res = array(
				'success'	=> TRUE,
				'message'	=> sprintf($this->lang->line('factura-cerrada-ok'), $factura),
				'numero'	=> $factura,
				'data'		=> $data,
				'abonos'	=> $numero['abonos']
			);
			$this->out->send($res);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Abre la factura
	 * @param int $id Id de la factura
	 * @return MSG
	 */
	function abrir($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.abrir');
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			if ($this->reg->abrir($id))
				$this->out->success(sprintf($this->lang->line('factura-abierta-ok'), $id));
			else
				$this->out->error($this->reg->error_message());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Procesa la factura
	 * @param int $id Id de la factura
	 * @return MSG
	 */
	function cerrar2($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.cerrar');
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$ok = $this->reg->cerrar2($id);
			if ($ok === FALSE) 
				return $this->reg->error_message();
			return TRUE;
		}
		return $this->lang->line('mensaje_faltan_datos');
	}

	/**
	 * Procesa las facturas que aún no está procesadas
	 * @param int $id Id de la factura a procesar
	 * @return MSG
	 */
	function procesar($silent = null, $id = null)
	{
		$this->userauth->roleCheck($this->auth .'.procesar');

		if ($this->config->item('bp.ventas.procesar.allow') === FALSE)
		{
			$this->out->error($this->lang->line('no-ventas-procesar-allow'));
		}

		$id = isset($id)?$id:$this->input->get_post('id');
		$silent = isset($silent)?$silent:$this->input->get_post('silent');
		if ($silent === FALSE) $silent = '1';
		if (is_numeric($id))
		{
			$lista[] = array('nIdFactura' => $id);
		}
		else
		{
			$lista = $this->reg->get(null, null, null, null, 'nIdEstado=' . FACTURA_STATUS_A_PROCESAR);
		}
		$errores = array();
		$count = 0;
		foreach($lista as $factura)
		{
			$this->userauth->set_username($factura['cCUser']);
			$res = $this->cerrar2($factura['nIdFactura']);
			if ($res!==TRUE)
			{
				$errores[] = sprintf($this->lang->line('error-procesar-factura'), $factura['nIdFactura'], $res);
			}
			else
				++$count;
		}
		$errores[] = sprintf($this->lang->line('facturas-a-procesar'), $count);
		$this->out->success(implode('<br/>', $errores));
	}

	/**
	 * Devuelve el ticket de una venta
	 * @param int $id Id del ticket
	 * @return JSON
	 */
	function ticket($id = null, $report = null)
	{
		$this->userauth->roleCheck($this->auth .'.tpv');
		$id = isset($id)?$id:$this->input->get_post('id');
		$report = isset($report)?$report:$this->input->get_post('report');
		if ($id)
		{
			$this->load->library('Configurator');
			if (empty($report))
				$report = $this->configurator->user('bp.factura.ticket');
			$text = $this->printer($id, $report, $this->lang->line('TPV'), FALSE);
			$this->out->success($text);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve las suscripciones de una factura
	 * @param int $id Id de la factura
	 * @return DATA
	 */
	function suscripciones($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$data = $this->reg->get_suscripciones($id); 
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Abona una factura
	 * @param int $id Id de la factura
	 * @return JSON
	 */
	function abonar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.abonar');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$id_n = $this->reg->abonar($id);
			if ($id_n < 1)
			{
				$this->out->error($this->reg->error_message());
			}
			$res = array(
				'success'	=> TRUE,
				'message'	=> sprintf($this->lang->line('factura-abono-ok'), $id, $id_n),
				'id'		=> $id_n
			);
			$this->out->send($res);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Liquida el stock de una sección
	 * @param int $seccion Id de la sección
	 * @param int $cliente Id del cliente
	 * @param float $dto Descuento a aplicar
	 * @return JSON
	 */
	function liquidarstock($seccion = null, $cliente = null, $dto = null)
	{
		$this->userauth->roleCheck($this->auth .'.liquidarstock');

		$cliente 	= isset($cliente)?$cliente:$this->input->get_post('cliente');
		$seccion 	= isset($seccion)?$seccion:$this->input->get_post('seccion');
		#$caja 		= isset($caja)?$caja:$this->input->get_post('caja');
		$dto 		= isset($dto)?$dto:$this->input->get_post('dto');

		if ($cliente && $seccion)
		{
			set_time_limit(0);
			$this->load->library('Configurator');
			$this->db->trans_begin();
			$albaran['nIdCliente'] 	= $cliente;
			$albaran['nIdCaja'] 	= $this->configurator->user('bp.tpv.caja');
			$albaran['nIdSerie'] 	= $this->configurator->user('bp.tpv.serie');

			$id_n = $this->reg->insert($albaran);

			if ($id_n < 1)
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}

			$albaran['nIdFactura'] = $id_n;

			$this->load->model('catalogo/m_articulosearch');
			$libros = $this->m_articulosearch->get(null, null, null, null,
					"Scn={$seccion} AND Stk <> 0");
			$this->load->model('ventas/m_albaransalida');
			$albaran['nIdCliente'] = $cliente;
			foreach($libros as $libro)
			{
				if (($libro['nStockFirme'] + $libro['nStockDeposito'] - $libro['nStockADevolver']) != 0)
				{
					$libro['nCantidad'] = $libro['nStockFirme'] + $libro['nStockDeposito'] - $libro['nStockADevolver'];
					$libro['fRecargo'] = 0;
					$libro['fCoste'] = $libro['fPrecioCompra'];
					$libro['fDescuento'] = (isset($dto) && ($dto > 0))?$dto:0;
					$albaran['lineas'][] = $libro;
				}
			}

			$id_albaran = $this->m_albaransalida->insert($albaran);
			if ($id_albaran < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}

			$this->db->trans_commit();
			$res = array(
				'success'	=> TRUE,
				'message'	=> sprintf($this->lang->line('factura-liquidar-ok'), $id_n),
				'id'		=> $id_n
			);

			$this->out->send($res);
		}
		else
		{
			$this->_show_js('liquidarstock', 'ventas/liquidarstock.js', array('tpv' => FALSE));
		}
	}

	/**
	 * Últimas facturas
	 */
	function get_last()
	{
		$data = $this->reg->get_last_factura();
		$this->out->data($data, $this->reg->get_count());
	}

	/**
	 * Copiar la referencia del cliente
	 * @param int $id Id del albarán
	 * @return JSON
	 */
	function copiarrefcliente($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.upd');
		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$count = 0;
			$upd = array();
			$alb = $this->reg->load($id, 'lineas');
			foreach($alb['lineas'] as $linea)
			{
				$upd[] = array('nIdLineaAlbaran' => $linea['nIdLineaAlbaran'], 
					'cRefCliente' => $alb['cRefCliente']);
				++$count;
			}
			
			if (!$this->reg->update($id, array('lineas' => $upd)))
			{
				$this->out->error($this->reg->error_message());
			}
			$this->out->success(sprintf($this->lang->line('factura-copiar-refs-ok'), $id, $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Copiar la referencia interna
	 * @param int $id Id del albarán
	 * @return JSON
	 */
	function copiarrefinterna($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.upd');
		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$count = 0;
			$upd = array();
			$alb = $this->reg->load($id, 'lineas');
			foreach($alb['lineas'] as $linea)
			{
				$upd[] = array('nIdLineaAlbaran' => $linea['nIdLineaAlbaran'], 
					'cRefInterna' => $alb['cRefInterna']);
				++$count;
			}
			
			if (!$this->reg->update($id, array('lineas' => $upd)))
			{
				$this->out->error($this->reg->error_message());
			}
			$this->out->success(sprintf($this->lang->line('factura-copiar-refs-ok'), $id, $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Genera el envío del paquete
	 * @param int $id Id del pedido
	 * @param int $dia Fecha del envío. Por defecto hoy
	 * @param bool $reembolso Es un reembolso
	 * @param float $importe Valor del reembolso
	 * @param string $obs Observaciones sobre el envío
	 * @param int $bultos Número de bultos
	 * @return MSG
	 */
	function courier($id = null, $dia = null, $reembolso = null, $importe = null, $obs = null, $bultos = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id = isset($id)?$id:$this->input->get_post('id');
		$reembolso = isset($reembolso)?$reembolso:$this->input->get_post('reembolso');
		$reembolso = format_tobool($reembolso);
		$importe = isset($importe)?$importe:$this->input->get_post('importe');
		$dia = isset($dia)?$dia:$this->input->get_post('dia');
		$obs = isset($obs)?$obs:$this->input->get_post('obs');
		$bultos = isset($bultos)?$bultos:$this->input->get_post('bultos');
		
		if (is_numeric($id))
		{
			#$this->out->success('OK COLEGA');
			$this->load->library('ASM');

			$this->load->model($this->model, 'ft');
			$this->load->model('clientes/m_direccioncliente');
			$this->load->model('clientes/m_email');
			$this->load->model('clientes/m_telefono');
			$pd = $this->ft->load($id, 'cliente');
			$idd = isset($pd['nIdDireccionEnvio'])?$pd['nIdDireccionEnvio']:$pd['nIdDireccion'];
			$dir = $this->m_direccioncliente->load($idd);
			if (!$dir)
				$this->out->error($this->lang->line('courier-no-hay-direccion'));

			$emails = $this->m_email->get_list($pd['nIdCliente']);
			$em = $this->utils->get_profile($emails, PERFIL_ENVIO);
			$tels = $this->m_telefono->get_list($pd['nIdCliente']);
			$tf = $this->utils->get_profile($tels, PERFIL_ENVIO);
		
			$ref = $id . substr(time(), 7);

			$resultado = '';
			if (!$idetq = $this->asm->enviar($ref, $dir, $pd['cliente'], $em['text'], $tf['text'], $dia, ($reembolso?$importe:null), $obs, $bultos, $resultado))
			{
				$this->out->error($this->asm->get_error());
			}

			$this->reg->update($id, array('cIdShipping' => $idetq));
			
			$res = $this->asm->etiqueta($idetq);
			$this->load->library('HtmlFile');
			$url = $this->htmlfile->url($res);
			$text = format_enlace_cmd($idetq, site_url('sys/codebar/etiqueta/' . $idetq));

			$msg = ($reembolso)?sprintf($this->lang->line('pedidocliente-courier-reembolso-ok'), $bultos, format_price($importe), $text, $resultado):
				sprintf($this->lang->line('pedidocliente-courier-ok'), $bultos, $text, $resultado);

			$this->_add_nota(null, $id, NOTA_INTERNA, $msg);

			$this->out->url($url, $this->lang->line('Enviar por courier'), 'iconoCourierTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambiar el modo de pago
	 * @param int $id Id del modo de pago
	 * @param int $mp Id del modo de pago a actualizar
	 * @param bool $cuenta Puede comprar a cuenta
	 * @return MSG
	 */
	function modopago($id = null, $mp = null, $cuenta = null)
	{
		$this->userauth->roleCheck($this->auth .'.administrar');
		$id = isset($id)?$id:$this->input->get_post('id');
		$mp = isset($mp)?$mp:$this->input->get_post('mp');
		$cuenta = isset($cuenta)?$cuenta:$this->input->get_post('cuenta');
		$cuenta = format_tobool($cuenta);
		#die();

		if (is_numeric($id) && is_numeric($mp))
		{
			$this->load->model($this->reg->_modospago, 'mp');
			$old = $this->mp->load($id);
			# Contabilizado?
			if ($old['bContabilizado'])
			{
				$this->out->error('modopago-error-contabilizado');
			}
			# Abonos no
			if ($old['nIdModoPago'] == 4 || $mp == 4)
			{
				$this->out->error($this->lang->line('modopago-error-abonos'));
			}
			if (!$this->mp->update($id, array('nIdModoPago' => $mp)))
			{
				$this->out->error($this->mp->error_message());
			}
			# A cuenta?
			if ($mp == 6 && !$cuenta)
			{
				$this->out->error($this->lang->line('modopago-error-nocuenta'));
			}
			$this->out->success($this->lang->line('modopago-cambio-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Calcula el próximo número de factura
	 * @return bool
	 */
	function numeros()
	{
		$this->reg->numeros();
		$this->out->success($this->lang->line('facturas-numeros-ok'));
	}

	/**
	 * Ajusta la factura para cuadrar con el pago
	 * @param  int $id Id de la factura
	 * @return MSG
	 */
	function ajustepago($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.ajustepago');
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			if (!($diff = $this->reg->ajustepago($id)))
			{
				$this->out->error($this->reg->error_message());
			}
			$this->_add_nota(null, $id, NOTA_INTERNA, sprintf($this->lang->line('ajuste-factura-mensaje'), format_price($diff)));
			$this->out->success(sprintf($this->lang->line('ajuste-factura-ok'), $id, format_price($diff)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Marca una factura como contabilizada
	 * @param  int $id Id de la factura
	 * @return MSG
	 */
	function contabilizar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.contabilizar');
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			if (!$this->reg->contabilizar($id))
			{
				$this->out->error($this->reg->error_message());
			}

			$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('factura-marcada-contabilizada'));
			$this->out->success($this->lang->line('factura-contabilizada-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Quita la marca de contabilizada
	 * @param  int $id Id de la factura
	 * @return MSG
	 */
	function descontabilizar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.contabilizar');
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			if (!$this->reg->descontabilizar($id))
			{
				$this->out->error($this->reg->error_message());
			}
			$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('factura-marcada-no-contabilizada'));
			$this->out->success($this->lang->line('factura-descontabilizada-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Información para el envío del documento
	 * @param int $id Id del documento
	 * @return array, información para el envío
	 */
	protected function _get_profile_sender($id)
	{
		$this->load->model('clientes/m_email');
		$this->load->model('clientes/m_telefono');
		$pd = $this->reg->load($id, TRUE);
		$subject = $this->lang->line('factura-subject-email');
		$subject = str_replace('%id%', $id, $subject);
		return array(
			'perfil' 		=> array(PERFIL_ENVIOFACTURACION, PERFIL_FACTURACION),
			'emails'		=> $this->m_email,
			'faxes'			=> $this->m_telefono,
			'report_email' 	=> $this->config->item('sender.factura'),
			'report_normal' => $this->_get_report_default(),
			'report_lang'	=> (isset($pd['cliente']['cIdioma']) && trim($pd['cliente']['cIdioma'])!='')?$pd['cliente']['cIdioma']:(isset($pd['direccion'])?$pd['direccion']['cIdioma']:null),
			'subject'		=> $subject,
			'data'			=> $pd,
			'css'			=> $this->config->item('bp.documentos.css'),
			'id'			=> $pd['nIdCliente']		
		);
	}

	/**
	 * Hook para las llamadas después de leer los datos
	 * @param int $id Id del registro
	 * @param mixed $relations Relaciones
	 * @param array $data Datos leídos
	 */
	protected function _post_get($id, $relations, &$data, $cmpid = null)
	{
		parent::_post_get($id, $relations, $data, $cmpid);
		check_portes($data);
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#_pre_printer($id, $data, $css)
	 */
	protected function _pre_printer($id, &$data, &$css)
	{
		parent::_pre_printer($id, $data, $css);
		check_portes($data);
		$css = $this->config->item('bp.documentos.css');
		return TRUE;
	}

	/**
	 * Copia el nombre del cliente como referencia de cada albaran
	 * @param  int $id Id de la factura
	 * @return MSG
	 */
	function ref($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.upd');
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$this->reg->ref($id)?$this->out->success(sprintf($this->lang->line('ref-factura-ok'), $id)):
			$this->out->error($this->db->error_message());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Devuelve el listado de pedidos que sirven una factura
	 * @param int $id Id de la factura
	 * @return JSON
	 */
	function pedidos($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id = isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{
			$pedidos = $this->reg->pedidos($id);
			#var_dump($pedidos); die();
			$data['pedidos'] = $pedidos;
			$data['id'] = $id;
			$message = $this->load->view('ventas/pedidoalbaranes', $data, TRUE);
			#echo $message; die();
			
			$this->out->html_file($message, $this->lang->line('Pedidos cliente') . ' ' . $id, 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Calcula el coste de la factura
	 * @param int $id Id de la factura
	 * @return JSON
	 */
	function coste($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id = isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{
			$ft = $this->reg->load($id, 'lineas');
			#var_dump($ft['lineas']); die();
			$t = $c = 0;
			foreach ($ft['lineas'] as $l)
			{
				$c += $l['fCoste'] * $l['nCantidad'];
				$t += $l['fBase'];
			}

			$data = array (
				'coste'	=> $c,
				'base'	=> $t,
				'id'	=> $id
				);
			$message = $this->load->view('ventas/costefactura', $data, TRUE);
			#echo $message; die();
			
			$this->out->html_file($message, $this->lang->line('Coste') . ' ' . $id, 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}
}

/* End of file Factura.php */
/* Location: ./system/application/controllers/ventas/factura.php */