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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Albarán de salida
 *
 */
class Albaransalida extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Albaransalida
	 */
	function __construct()
	{
		parent::__construct('ventas.albaransalida', 'ventas/m_albaransalida', TRUE, 'ventas/albaransalida.js', 'Albarán de Salida');
	}

	/**
	 * Cierra el albarán de salida
	 * @param int $id Id del albarán
	 * @return MSG
	 */
	function cerrar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.cerrar');
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$res = $this->reg->cerrar($id);
			if ($res === FALSE) $this->out->error($this->reg->error_message());
			$res = array(
				'success'	=> TRUE,
				'message'	=> sprintf($this->lang->line('albaransalida-cerrada-ok'), $id),
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

		if ($cliente && $seccion/* && $caja*/)
		{
			set_time_limit(0);
			$this->db->trans_begin();
			$albaran['nIdCliente'] = $cliente;

			$this->load->model('catalogo/m_articulosearch');
			$libros = $this->m_articulosearch->get(null, null, null, null,
					"Scn={$seccion} AND Stk <> 0");
			#$this->load->model('ventas/m_albaransalida');
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

			$id_albaran = $this->reg->insert($albaran);
			if ($id_albaran < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}

			$this->db->trans_commit();
			$res = array(
				'success'	=> TRUE,
				'message'	=> sprintf($this->lang->line('albaransalida-liquidar-ok'), $id_albaran),
				'id'		=> $id_albaran
			);

			$this->out->send($res);
		}
		else
		{
			$this->_show_js('liquidarstock', 'ventas/liquidarstock.js', array('tpv' => FALSE));
		}
	}

	/**
	 * Abona un albarán de salida
	 * @param int $id Id del albarán
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
				'message'	=> sprintf($this->lang->line('albaransalida-contra-ok'), $id, $id_n),
				'id'		=> $id_n
			);
			$this->out->send($res);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
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
			$this->out->success(sprintf($this->lang->line('albaransalida-copiar-refs-ok'), $id, $count));
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
			$this->out->success(sprintf($this->lang->line('albaransalida-copiar-refs-ok'), $id, $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Hook para las llamadas después de leer los datos
	 * @param int $id Id del registro
	 * @param mixed $relations Relaciones
	 * @param array $data Datos leídos
	 */
	protected function _post_get($id, $relations, &$data, $cmpid)
	{
		parent::_post_get($id, $relations, $data, $cmpid = null);
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
	 * Información para el envío de los documentos
	 * @param int $id Id del documento
	 * @return array, información para el envío
	 */
	protected function _get_profile_sender($id)
	{
		$this->load->model('clientes/m_email');
		$this->load->model('clientes/m_telefono');
		$pd = $this->reg->load($id, TRUE);
		$subject = $this->lang->line('albaransalida-subject-email');
		$subject = str_replace('%id%', $id, $subject);
		return array(
			'perfil' 		=> array(PERFIL_ENVIOFACTURACION, PERFIL_FACTURACION),
			'emails'		=> $this->m_email,
			'faxes'			=> $this->m_telefono,
			'report_email' 	=> $this->config->item('sender.albaransalida'),
			'report_normal' => $this->_get_report_default(),
			'report_lang'	=> (isset($pd['cliente']['cIdioma']) && trim($pd['cliente']['cIdioma'])!='')?$pd['cliente']['cIdioma']:(isset($pd['direccion'])?$pd['direccion']['cIdioma']:null),
			'subject'		=> $subject,
			'data'			=> $pd,
			'css'			=> $this->config->item('bp.documentos.css'),
			'id'			=> $pd['nIdCliente']		
		);
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

			$this->load->model('ventas/m_albaransalida');
			$this->load->model('clientes/m_direccioncliente');
			$this->load->model('clientes/m_email');
			$this->load->model('clientes/m_telefono');
			$pd = $this->m_albaransalida->load($id, 'cliente');
			$idd = $pd['nIdDireccion'];
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
	 * Albaranes pendientes de facturar
	 * @return HTML
	 */
	function sinfacturar()
	{
		$this->userauth->roleCheck($this->auth .'.get_list');
		$data = $this->reg->sinfacturar();
		if (count($data) > 0)
		{
			$body = $this->load->view('ventas/sinfacturar', array('albaranes' => $data), TRUE);

			$datos['title'] = $this->lang->line('Albaranes sin facturar');
			$datos['body'] = $body;
			$r = $this->load->view('main/bootstrap', $datos, TRUE);
			$this->out->html_file($r, $this->lang->line('Albaranes sin facturar'), 'iconoReportTab', null, TRUE);
		}
		$this->out->success($this->lang->line('no-hay-sin-facturar'));
	}
	
	/**
	 * Marca un albarán como facturable o no facturable
	 * @return MSG
	 */
	function nofacturable($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.upd');

		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$al = $this->reg->load($id);
			if (!$this->reg->update($id, array('bNoFacturable' => !$al['bNoFacturable'])))
			{
				$this->out->error($this->reg->error_message());		
			}
			$res = array(
				'success'	=> TRUE,
				'message'	=> sprintf($this->lang->line(!$al['bNoFacturable']?'albaran-marcado-sin-facturar':'albaran-marcado-facturable'), $id),
				'facturable' => $al['bNoFacturable']
				);
			$this->out->send($res);
		}
		$this->out->success($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve el listado de pedidos que sirven un albarán
	 * @param int $id Id del albarán
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
	 * Calcula el coste del albarán
	 * @param int $id Id del albarán
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

	/**
	 * Genera la antigüedad de las ventas aún sin generar
	 * @return MSG
	 */
	function antiguedad($debug = FALSE)
	{
		$this->userauth->roleCheck($this->auth .'.upd');
		# Ventas sin antiguedad
		set_time_limit(0);
		$data = $this->reg->antiguedad();
		$volcado = null;
		$ant = null;
		$prefix = $this->config->item('bp.oltp.database');
		$this->load->model('stocks/m_antiguedadstock');
		$this->load->model('ventas/m_albaransalidalinea');
		$total = count($data);
		$msg = array();
		if ($debug) $msg[] = "{$total} Líneas a procesar";
		$count = 0;
		foreach ($data as $reg) 
		{
			# Comprueba si hemos leído la antiguedad correcta
			$fecha = format_mssql_date($reg['dCreacion']);
			$this->db->flush_cache();
			$this->db->select_max('nIdVolcado')
			->from("{$prefix}Ext_AntiguedadStockVolcados")
			->where("dCreacion < " . $this->db->dateadd('d', 1, $fecha));
			$query = $this->db->get();
			if ($query)
			{
				$temp = $query->row_array();
				$new = $temp['nIdVolcado'];
				if (!isset($volcado) || ($volcado != $new))
				{
					if ($debug) $msg[] = "Leyendo volcado {$new}";
					$data = $this->m_antiguedadstock->get_volcado($new);
					if ($debug) $msg[] = "Leídos " . count($data);
					foreach ($data as $r)
					{
						$ant[$r['nIdLibro']] = $r;
					}
					$volcado = $new;
				}
			}
			$cnt = $reg['nEnFirme'];

			if (isset($ant[$reg['nIdLibro']]))
			{
				$esta = $ant[$reg['nIdLibro']];
				$final['nFirme4'] = min($cnt, $esta['nFirme4']);
				$cnt -= $final['nFirme4'];
				$final['nFirme3'] = min($cnt, $esta['nFirme3']);
				$cnt -= $final['nFirme3'];
				$final['nFirme2'] = min($cnt, $esta['nFirme2']);
				$cnt -= $final['nFirme2'];
				$final['nFirme1'] = $cnt;

				$esta['nFirme4'] -= $final['nFirme4'];
				$esta['nFirme3'] -= $final['nFirme3'];
				$esta['nFirme2'] -= $final['nFirme2'];
				$esta['nFirme1'] -= $final['nFirme1'];

				$ant[$reg['nIdLibro']] = $esta;
			}
			else
			{
				if ($debug) $msg[] = "Volcado {$volcado} - Id {$reg['nIdLibro']} NO ESTA";
				$final['nFirme2'] = $final['nFirme3'] = $final['nFirme4'] = 0;
				$final['nFirme1'] = $cnt;
			}
			$this->m_albaransalidalinea->update($reg['nIdLineaAlbaran'], $final);
			++$count;
			if (($count % 100) == 0)
			{
				if ($debug) $msg[] = "{$count}/{$total} Líneas procesadas\n";
			}
		}
		$this->out->success(sprintf($this->lang->line('albaransalida-antoguedad-ok'), $count));
	}

	function antiguedad_salida()
	{
		$this->userauth->roleCheck($this->auth .'.get_list');
		$data = $this->reg->antiguedad_salida();
		$final = array();
		$this->load->model('generico/m_seccion');
		foreach ($data as $value) 
		{
			$cod = explode('.', $value['cCodigo']);
			if (!isset($final[$cod[0]]))
			{
				$s = $this->m_seccion->load($cod[0]);
				$final[$cod[0]]['nombre'] = $s['cNombre'];
			}
			$final[$cod[0]]['lineas'][] = $value;
		}
		sksort($final, 'nombre');
		$body = $this->load->view('ventas/antiguedadsalida', array('datos' => $final), TRUE);
		$datos['title'] = $this->lang->line('Antigüedad Ventas');
		$datos['body'] = $body;
		$r = $this->load->view('oltp/reports', $datos, TRUE);
		$this->out->html_file($r, $this->lang->line('Antigüedad Ventas'), 'iconoReportTab', null, TRUE);
		#echo $message; die();
			
		$this->out->html_file($message, $this->lang->line('Antigüedad Ventas'), 'iconoReportTab');
	}

}

/* End of file Albaransalida.php */
/* Location: ./system/application/controllers/ventas/Albaransalida.php */
