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
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Pedido Proveedor
 *
 */
class PedidoProveedor extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return PedidoProveedor
	 */
	function __construct()
	{
		parent::__construct('compras.pedidoproveedor', 'compras/m_pedidoproveedor', TRUE, 'compras/pedidoproveedor.js', 'Pedido Proveedor');
	}

	/**
	 * Pedidos abiertos
	 * @param int $proveedor ID del proveedor
	 * @param int $deposito 0:Firme, 1: Depósito
	 * @param int $todos 0: no mostrar bloqueados, 1: mostrar todos
	 * @param int $seccion 
	 * @return DATA
	 */
	function abiertos($proveedor = null, $deposito = null, $todos = null, $seccion = null)
	{
		$this->userauth->roleCheck($this->auth .'.index');
		$proveedor = isset($proveedor)?$proveedor:$this->input->get_post('proveedor');
		$deposito = isset($deposito)?$deposito:$this->input->get_post('deposito');
		$todos = isset($todos)?$todos:$this->input->get_post('todos');
		$seccion = isset($seccion)?$seccion:$this->input->get_post('seccion');
		$todos = ($todos === FALSE)? FALSE : $todos;

		if (is_numeric($proveedor))
		{
			$where = "nIdProveedor = {$proveedor} AND bDeposito = {$deposito} AND bRevistas = 0 AND nIdEstado=1";
			if (!$todos) $where .= " AND ISNULL(bBloqueado, 0) = 0";
			if (is_numeric($seccion)) $where .= " AND (nIdSeccion={$seccion} OR nIdSeccion IS NULL)";
			$data = $this->reg->search(null, null, null, 'cSeccion DESC, dCreacion DESC', null, $where);
			#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();
			/*foreach ($data as $key => $value) 
			{
				$data[$key]['text'] .= '(' .$value['id'] . ')';
			}*/
			$this->out->data($data, $this->reg->get_count());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));

	}

	/**
	 * Cierra el pedido a proveedor
	 * @param int $id Id del pedido del proveedor
	 * @param  bool $force Fuerza el cierra de la carta a pesar de  no cumplir el mínimo
	 * @return MSG
	 */
	function cerrar($id = null, $force = null)
	{
		$this->userauth->roleCheck($this->auth .'.cerrar');
		$id = isset($id)?$id:$this->input->get_post('id');
		$force = isset($force)?$force:$this->input->get_post('force');
		if (empty($force)) $force = FALSE;
		$force = format_tobool($force);
		if ($id)
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$count = 0;
			$pvs = array();
			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->cerrar($id, $force);
					if ($res === FALSE) $this->out->error($this->reg->error_message());
					$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('pedidoproveedor-cerrado-history'));
					++$count;
				}
			}
			$this->out->success(sprintf($this->lang->line('pedidoproveedor-cerrada-ok'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cancela un pedido de proveedor
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function cancelar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.cancelar');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$count = 0;
			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->cancelar($id);
					if (!$res) $this->out->error($this->reg->error_message());
					$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('pedidoproveedor-cancelado-history'));
					++$count;
				}
			}
			$this->out->success(sprintf($this->lang->line('pedido-proveedor-cancelado'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Abre el pedido del proveedor
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function abrir($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.abrir');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			if ($this->reg->abrir($id))
			{
				$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('pedidoproveedor-abierto-history'));
				$this->out->success(sprintf($this->lang->line('pedidoproveedor-abierto-ok'), $id));
			}
			$this->out->error($this->reg->error_message());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
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
		$subject = $this->lang->line('pedidoproveedor-subject-email');
		$subject = str_replace('%id%', $id, $subject);
		if ($pd['bDeposito'])
			$sinli = isset($pd['proveedor']['bEnviarSINLIDep'])?$pd['proveedor']['bEnviarSINLIDep']:FALSE;
		else
			$sinli = isset($pd['proveedor']['bEnviarSINLI'])?$pd['proveedor']['bEnviarSINLI']:FALSE;

		if (!isset($pd['direccion']))
		{
			$this->load->model('proveedores/m_direccion');
			$dirs = $this->m_direccion->get_list($pd['nIdProveedor']);
			$pd['direccion'] = $this->utils->get_profile($dirs, PERFIL_PEDIDO);
		}
		
		return array(
			'perfil' 		=> PERFIL_PEDIDO,
			'emails'		=> $this->m_email,
			'faxes'			=> $this->m_telefono,
			'report_email' 	=> $this->config->item('sender.pedidoproveedor'),
			'report_normal' => $this->_get_report_default(),
			'report_lang'	=> (isset($pd['proveedor']['cIdioma']) && trim($pd['proveedor']['cIdioma'])!='')?
				$pd['proveedor']['cIdioma']:
				((isset($pd['direccion']) && isset($pd['direccion']['cIdioma']))?$pd['direccion']['cIdioma']:null),
			'subject'		=> $subject,
			'data'			=> $pd,
			'css'			=> $this->config->item('bp.documentos.css'),
			'id'			=> $pd['nIdProveedor'],
			'sinli'			=> $sinli?(isset($pd['proveedor']['cSINLI'])?$pd['proveedor']['cSINLI']:null):null,		
			'sinliemail'	=> $sinli?(isset($pd['proveedor']['cSINLIBuzon'])?$pd['proveedor']['cSINLIBuzon']:null):null,
			'sinlitipo'		=> 'PEDIDO'		
		);
	}

	/**
	 * Muestra la ventana de pendientes de recibir
	 * @return FORM
	 */
	function pendienterecibir()
	{
		$this->_show_form('pendienterecibir', 'compras/pendienterecibir.js', $this->lang->line('Pedidos pendientes de recibir'));
	}

	/**
	 * Líneas de pedido de proveedor pendiente de recibir
	 *
	 * @param int $ids Id de la sección
	 * @param int $idpv Id del proveedor
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $query Palabra clave de búsqueda
	 * @return JSON_DATA
	 */
	function get_pendienterecibir($ids = null, $idl = null, $idpv = null, $pp = null, $start = null, $limit = null, $sort = null, $dir = null, $query = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$ids 	= isset($ids)?$ids:$this->input->get_post('ids');
		$idl 	= isset($idl)?$idl:$this->input->get_post('idl');
		$idpv 	= isset($idpv)?$idpv:$this->input->get_post('idpv');
		$pp 	= isset($pp)?$pp:$this->input->get_post('pp');
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		$query 	= isset($query)?$query:$this->input->get_post('query');
		if (trim($query) == '') $query = null;

		$this->load->model('compras/m_pedidoproveedorlineaex');
		$where = 'nIdEstado IN (' . LINEA_PEDIDO_PROVEEDOR_STATUS_PENDIENTE_DE_RECIBIR . ', ' . LINEA_PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO . ')';

		if ($ids) $where .= " AND (Cat_Secciones.cCodigo LIKE '{$ids}.%' OR Cat_Secciones.cCodigo LIKE '%.{$ids}.%' OR nIdSeccion={$ids})";
		if ($idpv) $where .= " AND (Prv_Proveedores.nIdProveedor={$idpv})";
		if ($idl) $where .= " AND (nIdLibro={$idl})";
		if ($pp == '1') $where .= " AND (Cat_Secciones_Libros.nStockServir > 0)";
		$data = $this->m_pedidoproveedorlineaex->get($start, $limit, $sort, $dir, $where, null, $query);
		$this->out->data($data, $this->m_pedidoproveedorlineaex->get_count());
	}

	/**
	 * Líneas de pedido de proveedor pendiente de cerrar por línea
	 *
	 * @param int $ids Id de la sección
	 * @param int $idpv Id del proveedor
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $query Palabra clave de búsqueda
	 * @return JSON_DATA
	 */
	function get_pendientecerrarlinea($ids = null, $idl = null, $idpv = null, $pp = null, $start = null, $limit = null, $sort = null, $dir = null, $query = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$ids 	= isset($ids)?$ids:$this->input->get_post('ids');
		$idl 	= isset($idl)?$idl:$this->input->get_post('idl');
		$idpv 	= isset($idpv)?$idpv:$this->input->get_post('idpv');
		$pp 	= isset($pp)?$pp:$this->input->get_post('pp');
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		$query 	= isset($query)?$query:$this->input->get_post('query');
		if (trim($query) == '') $query = null;

		$this->load->model('compras/m_pedidoproveedorlineaex');
		$where = 'nIdEstado IN (' . LINEA_PEDIDO_PROVEEDOR_STATUS_EN_PROCESO . ')';

		if ($ids) $where .= " AND (Cat_Secciones.cCodigo LIKE '{$ids}.%' OR Cat_Secciones.cCodigo LIKE '%.{$ids}.%' OR nIdSeccion={$ids})";
		if ($idpv) $where .= " AND (Prv_Proveedores.nIdProveedor={$idpv})";
		if ($idl) $where .= " AND (nIdLibro={$idl})";
		if ($pp == '1') $where .= " AND (Cat_Secciones_Libros.nStockServir > 0)";
		$data = $this->m_pedidoproveedorlineaex->get($start, $limit, $sort, $dir, $where, null, $query);
		#echo '<pre>'; echo array_pop($this->db->queries); die();
		$this->out->data($data, $this->m_pedidoproveedorlineaex->get_count());
	}
	
	/**
	 * Precalcula los totales y unidades para pedidos abiertos
	 * @return MSG
	 */
	function calcular_totales()
	{
		$data = $this->reg->get(null, null, null, null, 'nIdEstado = ' .PEDIDO_PROVEEDOR_STATUS_EN_CREACION);
		set_time_limit(0);
		#var_dump($data); die();
		foreach ($data as  $value) 
		{
			if (!$this->reg->totales($value['nIdPedido']))
			{
				$this->out->error($this->reg->error_message());
			}
		}
		$this->out->success(sprintf($this->lang->line('calcular-totales-ok'), count($data)));
	}

	/**
	 * Muestra la ventana de pendientes de cerrar
	 * @return FORM
	 */
	function pendientecerrar()
	{
		$this->_show_form('pendientecerrar', 'compras/pendientecerrar.js', $this->lang->line('Pedidos pendientes de cerrar'));
	}

	/**
	 * Muestra la ventana de pendientes de cerrar por línea
	 * @return FORM
	 */
	function pendientecerrarlinea()
	{
		$this->_show_form('pendientecerrar', 'compras/pendientecerrarlinea.js', $this->lang->line('Pedidos pendientes de cerrar por línea'));
	}
	
	/**
	 * Pedido de proveedor pendiente de cerrar
	 *
	 * @param int $idp Id del proveedor
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $query Palabra clave de búsqueda
	 * @return JSON_DATA
	 */
	function get_pendientecerrar($idp = null, $start = null, $limit = null, $sort = null, $dir = null, $query = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$idp 	= isset($idp)?$idp:$this->input->get_post('idp');
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		$query 	= isset($query)?$query:$this->input->get_post('query');
		if (trim($query) == '') $query = null;

		$this->load->model('compras/m_pedidoproveedor');
		$where = 'nIdEstado IN (' . PEDIDO_PROVEEDOR_STATUS_EN_CREACION . ')';

		if ($idp) $where .= " AND nIdProveedor = {$idp}";
		$data = $this->m_pedidoproveedor->get($start, $limit, $sort, $dir, $where, null, $query);
		$this->out->data($data, $this->m_pedidoproveedor->get_count());
	}
	
	/**
	 * Actualiza el estado de los pedido de proveedor
	 * @param int $id Id del pedido a procesar
	 * @return MSG
	 */
	function actualizar_estado($idp = null)
	{
		$idp = isset($idp) ? $idp: $this->input->get_post('idp');
		
		$this->load->library('Configurator');
		$last = (int)$this->configurator->system('pedidoproveedor.estado.last');			
		$res = $this->reg->actualizar_estado($last, $idp);	
		$this->configurator->set_system('pedidoproveedor.estado.last', (string)$res['last']);
		$this->out->dialog(TRUE, sprintf($this->lang->line('actualizar-estado-pedido.ok'), $res['count']));
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
			$this->load->model('compras/m_albaranentradalinea');
			$this->load->model('compras/m_pedidoproveedorlinea');
			$this->load->model('catalogo/m_articuloseccion');
			$alb = $this->reg->load($id, 'lineas');
			$this->db->trans_begin();

			// Para cada línea
			$asig = array();
			$new = array();
			foreach ($alb['lineas'] as $linea)
			{
				$idl = $linea['nIdLibro'];
				$link_l = format_enlace_cmd($idl, site_url('catalogo/articulo/index/' . $idl));
				$data = $this->m_pedidoproveedorlinearecibida->get(null, null, null, null, "nIdLineaPedido={$linea['nIdLinea']}");
				foreach ($data as $l)
				{
					$lnpd2 = $this->m_pedidoproveedorlinea->load($l['nIdLineaPedido']);
					$lnpd = $this->m_albaranentradalinea->load($l['nIdLineaAlbaran']);
					$link_pd = format_enlace_cmd($lnpd['nIdAlbaran'], site_url('compras/albaranentrada/index/' . $lnpd['nIdAlbaran']));
					$message = sprintf($this->lang->line('asignacion-asignando-alb'), $link_l, $linea['cTitulo'], $link_pd, $lnpd2['cSeccion'], $l['nCantidad']);
					$this->messages->info($message);
					$new[$lnpd2['cSeccion']][] = $message;
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
			$message = $this->messages->out($this->lang->line('Asignación de albarán') . ' ' . $id);
			$this->out->html_file($message, $this->lang->line('Asignación de albarán') . ' ' . $id, 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Genera un fichero EXCEL con las líneas del pedido proveedor
	 * @param  int $id Id del pedido al proveedor
	 * @return FILE
	 */
	function exportar_excel($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');
		$id = (int) (isset($id)?$id:$this->input->get_post('id'));
		if (is_numeric($id))
		{
			# Genera el REPORT
			$report = $this->config->item('bp.pedidoproveedor.excel.report');
			if (empty($report))
			{
				$this->out->error(sprintf($this->lang->line('error-no-config-report'), 'bp.pedidoproveedor.excel.report'));
			}
			$profile = $this->_get_profile_sender($id);
			#var_dump($profile)
			$html = $this->show_report($profile['subject'], $profile['data'], $report, null, FALSE, $profile['report_lang'], FALSE, FALSE);

			// Fichero
			$this->load->library('HtmlFile');
			$filename = time() . '.html';
			$file = $this->htmlfile->pathfile($filename);
			file_put_contents($file, $html);

			$url = site_url('sys/export/file/' . $filename . '/XLSX');

			$res = array(
				'success' 	=> TRUE,
				'message'	=> $this->lang->line('export-ok'),
				'file'		=> $filename,
				'src'		=> $url
			);

			// Respuesta
			$this->out->send($res);
			#var_dump($html); die();
			#$this->out->success(sprintf($this->lang->line('pedidoproveedor-cerrada-ok'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Unificador de proveedores
	 * @param int $destino Id de la proveedor destino
	 * @param string $origen Ids de las proveedores repetidos, separados por ;
	 * @return JSON
	 */
	function unificar($origen = null, $destino = null)
	{
		$this->userauth->roleCheck(($this->auth.'.unificar'));

		$origen	= isset($origen)?$origen:$this->input->get_post('origen');
		$destino	= isset($destino)?$destino:$this->input->get_post('destino');

		if (is_numeric($destino) && !empty($origen))
		{
			$ids = preg_split('/\;/', $origen);
			$this->load->library('Logger');
			if (!$this->reg->unificar($destino, $ids))
			{
				$str = $this->reg->error_message();
				$this->out->error($str);
			}
			$this->logger->log('pedido proveedor unificado ' . implode(',', $ids) . ' con ' .$destino, 'unificador');
			$this->out->success($this->lang->line('pedido proveedores-unificados-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Limpiar los pedidos a proveedor sin líneas
	 * @return MSG
	 */
	function limpiar()
	{
		$this->userauth->roleCheck(($this->auth.'.del'));
		$count = $this->reg->limpiar();
		$this->out->success(sprintf($this->lang->line('pedidoproveedor-limpiar-ok'), $count));
	}
}

/* End of file PedidoProveedor.php */
/* Location: ./system/application/controllers/compras/PedidoProveedor.php */