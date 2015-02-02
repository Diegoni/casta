<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	web
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador de las estadísticas web
 *
 */
class Webpage extends MY_Controller
{
	/**
	 * Id del último cliente del que se han leído las direcciones
	 * @var int 
	 */
	private $idlast = null;
	/**
	 * Las últimas direcciones leídas
	 * @var array
	 */
	private $direcciones = null;

	/**
	 * Último error generado
	 * @var string
	 */
	private $_error = null;
	
	/**
	 * Constructor
	 *
	 * @return Webpage
	 */
	function __construct()
	{
		parent::__construct('web.webpage', 'web/M_webpage', TRUE);
	}

	/**
	 * Pedidos realizados en Internet
	 * Muestra el formulario
	 *
	 * @return HTML
	 */
	function pedidos_realizados()
	{
		$this->userauth->roleCheck($this->auth . '.pedidos_realizados');

		set_time_limit(0);

		$data['pedidos'] = $this->reg->pedidos_realizados();

		$this->load->helper('asset');
		$message = $this->load->view('webpage/pedidosrealizados', $data, true);

		// Respuesta
		$this->out->html_file($message, $this->lang->line('Pedidos en Internet'), 'iconoReportTab');
	}

	/**
	 * Pedidos de Internet y series de factura
	 * Crea el formulario
	 *
	 * @param int $year Año a mostrar
	 * @param int $month Mes a mostrar
	 * @param int $task 0: Directo, 1: Como tareas
	 * @return HTML_FILE
	 */
	function pedidos_series($year = null, $month = null, $task = null)
	{
		$this->userauth->roleCheck($this->auth . '.pedidos_series');

		$year = isset($year) ? $year : $this->input->get_post('year', null);
		$month = isset($month) ? $month : $this->input->get_post('month', null);
		$task = isset($task) ? $task : $this->input->get_post('task');

		if ($task === FALSE)
			$task = 1;

		if ($task == 1)
		{
			$this->load->library('tasks');
			if (!is_numeric($year))
				$year = 'null';
			if (!is_numeric($month))
				$month = 'null';
			$cmd = site_url("web/webpage/pedidos_series/{$year}/{$month}/0");
			$this->tasks->add2($this->lang->line('Pedidos Series'), $cmd);
		}
		else
		{
			set_time_limit(0);
			if ($year == 'null')
				$year = null;
			if ($month == 'null')
				$month = null;

			$data['pedidos'] = $this->reg->pedidos_series($year, $month);

			$message = $this->load->view('webpage/pedidosseries', $data, true);
			$this->out->html_file($message, $this->lang->line('Pedidos Series'), 'iconoReportTab');
		}
	}

	/**
	 * Actualiza los más vendidos
	 * @return MSG
	 */
	function bestsellers()
	{
		set_time_limit(0);
		($this->reg->bestsellers()) ? $this->out->success($this->lang->line('webpage-bestsellers-ok')) : $this->out->error($this->reg->error_message());
	}

	/**
	 * Stock para mostrar en la Web
	 * @return DATA
	 */
	function stock($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		set_time_limit(0);
		$data = $this->reg->stock($id);
		$this->out->data($data);
	}

	/**
	 * Estado para mostrar en la Web
	 * @return DATA
	 */
	function status()
	{
		set_time_limit(0);
		$data = $this->reg->status();
		$this->out->data($data);
	}

	/**
	 * Disponibilidad para mostrar en la Web
	 * @return DATA
	 */
	function disponibilidad($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		set_time_limit(0);
		$data = $this->reg->disponibilidad(90, 24, $id);
		$this->out->data($data);
	}

	/**
	 * Promociones Web
	 * @return DATA
	 */
	function promociones()
	{
		set_time_limit(0);
		$promos = array();

		// Las promociones de verdad
		$this->load->model('catalogo/m_promocion');
		$inicio = format_mssql_date(time());
		$dias = $this->config->item('bp.articulo.diaspromocion');
		$filter = "nIdTipoPromocion IN (8, 12) 
		AND (dInicio <= {$inicio} OR dInicio IS NULL) 
		AND (dFinal <= " . $this->db->dateadd('d', $dias, $inicio) . " OR dFinal IS NULL)";
		$data = $this->m_promocion->get(null, null, null, null, $filter);
		foreach ($data as $v)
		{
			$promos[] = array(
					'nIdLibro' => $v['nIdLibro'],
					'dInicio' => $v['dInicio'],
					'dFinal' => $v['dFinal']
			);
		}
		// Las promociones de boletines
		$data = $this->reg->promociones($dias);
		foreach ($data as $v)
		{
			$promos[] = array(
					'nIdLibro' => $v['nIdLibro'],
					'dInicio' => $v['dInicio'],
					'dFinal' => $v['dFinal']
			);
		}
		$this->out->data($promos);
	}

	/**
	 * Bestsellers
	 * @return DATA
	 */
	function get_bestsellers()
	{
		set_time_limit(0);
		$data = $this->reg->get_bestsellers();
		$this->out->data($data);
	}

	/**
	 * Libros relacionados
	 * @return DATA
	 */
	function relacionados()
	{
		set_time_limit(0);
		$this->load->model('catalogo/m_relacionados');
		$data = $this->m_relacionados->get();
		// Agrupa por ID
		foreach ($data as $d)
		{
			$data2[$d['nIdLibro1']][] = $d['nIdLibro2'];
			$data2[$d['nIdLibro2']][] = $d['nIdLibro1'];
			$data[] = array(
					'nIdLibro1' => $d['nIdLibro2'],
					'nIdLibro2' => $d['nIdLibro1']
			);
		}
		// Asocia a primer nivel
		foreach ($data as $d)
		{
			if (isset($data2[$d['nIdLibro2']]))
			{
				foreach ($data2[$d['nIdLibro2']] as $d2)
				{
					if (!in_array($d2, $data2[$d['nIdLibro1']]) && ($d2 != $d['nIdLibro1']))
					{
						$data[] = array(
								'nIdLibro1' => $d['nIdLibro1'],
								'nIdLibro2' => $d2
						);
						$data2[$d['nIdLibro1']] = $d2;
					}
				}
			}
		}
		$this->out->data($data);
	}

	/**
	 * Elementos multimedia de los artículos
	 * @return DATA
	 */
	function multimedia()
	{
		set_time_limit(0);
		$this->load->model('catalogo/m_media');
		$data = $this->m_media->get(null, null, null, null, "cTipo<>'doc-file'");
		$this->out->data($data);
	}

	/**
	 * Se loguea en la Web
	 */
	private function login_web()
	{
		$this->load->library('Webshop');
		$res = $this->webshop->login();
		if (!$res)
		{
			$this->out->error($this->lang->line('webshop-error-login'));
		}
	}

	/**
	 * Lista los pedidos de la web
	 *
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $where Condiciones de la consulta
	 * @param string $query Palabra clave de búsqueda
	 * @return DATA
	 */
	function get_pedidos($start = null, $limit = null, $sort = null, $dir = null, $where = null, $query = null)
	{
		$this->userauth->roleCheck($this->auth . '.pedidos');

		$start = isset($start) ? $start : $this->input->get_post('start');
		$limit = isset($limit) ? $limit : $this->input->get_post('limit');
		$sort = isset($sort) ? $sort : $this->input->get_post('sort');
		$dir = isset($dir) ? $dir : $this->input->get_post('dir');
		$where = isset($where) ? $where : $this->input->get_post('where');
		$query = isset($query) ? $query : $this->input->get_post('query');

		$this->login_web();

		$filter = array();
		if (!empty($start))
			$filter['start'] = $start;
		if (!empty($limit))
			$filter['limit'] = $limit;
		if (!empty($sort))
			$filter['sort'] = $sort;
		if (!empty($dir))
			$filter['dir'] = $dir;

		#$this->webshop->debug = 1;
		$orders = $this->webshop->action('api/order/get_list', $filter);
		#var_dump($orders); die();

		if ($orders['success'])
		{
			foreach ($orders['data']['data'] as $k => $v)
			{
				$orders['data']['data'][$k]['customer'] = format_name($v['firstname'], $v['lastname'], null);
				$orders['data']['data'][$k]['date_added'] = strtotime($v['date_added']);
				$orders['data']['data'][$k]['date_modified'] = strtotime($v['date_modified']);
			}
			$this->out->data($orders['data']['data'], $orders['data']['total']);
		}
		$this->out->error($this->webshop->get_error());
	}

	/**
	 * Muestra la ventana de pedidos Web
	 * @return FORM
	 */
	function pedidos_web()
	{
		$this->_show_form('pedidos', 'webpage/pedidos.js', $this->lang->line('Pedidos Web'));

	}

	/**
	 * Importa un pedido de cliente de la Web al sistema
	 * @param int $id Id del pedido de Internet
	 * @param int $seccion Id de la sección a la que se vincula el pedido
	 * @return MSG
	 */
	function importar_pedido($id = null, $seccion = null)
	{
		$this->userauth->roleCheck($this->auth . '.crear_pedidos');

		$id = isset($id) ? $id : $this->input->get_post('id');
		$seccion = isset($seccion) ? $seccion : $this->input->get_post('seccion');
		if (is_numeric($id) && is_numeric($seccion))
		{
			# Lee el pedido Web	
			$this->login_web();
			$filter['id'] = $id;
			#$this->webshop->debug = 3;
			$res = $this->webshop->action('api/order/get', $filter);
			$order = $res['data'];
			#var_dump($order); die();

			$this->db->trans_begin();
			// Importa el cliente

			$this->load->model('clientes/m_cliente');
			if (count($order['customer']) == 0)
			{
				$order['customer'] = array_merge($order, array('password' => null, 'newsletter' => 0));
				$idcliente = null;
			}
			else
			{
				$idcliente = (int)$order['customer']['foreign_id'];
				$cl = $this->m_cliente->load($idcliente);
				if (!$cl) $idcliente = null;
			}
			if (!isset($idcliente) || $idcliente < 1)
			{	
				$order['customer']['company'] = $order['payment_company'];
				$idcliente = $this->crear_cliente($order['customer']);
				#var_dump($idcliente); die();
				if (!isset($idcliente))
				{
					$this->db->trans_rollback();
					$this->out->error($this->_error);
				}
				#var_dump($idcliente); die();
				if (($order['customer']['customer_id']) > 0)
				{
					$data['id'] = $order['customer']['customer_id'];
					$data['foreign_id'] = $idcliente;
					#$this->webshop->debug = 3;
					$res = $this->webshop->action('api/customer/upd', $data);
					if (!$res['success'])
					{
						$this->db->trans_rollback();
						$this->out->error(sprintf($this->lang->line('error-llamada-web'), $res['message']));
					}
				}
			}
			#echo 'cliente '; var_dump($idcliente); die();
			
			// Importa las dirección envío
			$dir['cTitular'] = format_name($order['shipping_firstname'], $order['shipping_lastname'], $order['shipping_company']);
			$dir['cCalle'] = $order['shipping_address_1'];
			$dir['cCP'] = $order['shipping_postcode'];
			$dir['cPoblacion'] = $order['shipping_city'];
			$dir['nIdRegion'] = $order['shipping_zone_id'];
			$idshipping = $this->crear_direccion($idcliente, $dir, PERFIL_ENVIO);
			if (!isset($idshipping))
			{
				$this->db->trans_rollback();
				$this->out->error($this->_error);
			}
			#echo 'shipping '; var_dump($idshipping);

			// Importa dirección de facturación
			$dir['cTitular'] = format_name($order['payment_firstname'], $order['payment_lastname'], $order['payment_company']);
			$dir['cCalle'] = $order['payment_address_1'];
			$dir['cCP'] = $order['payment_postcode'];
			$dir['cPoblacion'] = $order['payment_city'];
			$dir['nIdRegion'] = $order['payment_zone_id'];
			$idpayment = $this->crear_direccion($idcliente, $dir, PERFIL_GENERAL);
			if (!isset($idpayment))
			{
				$this->db->trans_rollback();
				$this->out->error($this->_error);
			}
			#echo 'payment'; var_dump($idpayment);			
			$this->load->model('catalogo/m_articulo');
			$this->load->model('catalogo/m_articulocodigo');
			// Importa el pedido
			$lineas = array();
			foreach($order['lineas'] as $linea)
			{
				$art = $this->m_articulo->load($linea['product_id']);				
				if (!$art)
				{
					# Se ha unificado
					$d = $this->m_articulocodigo->get(null, null, null, null, 'nCodigo=' . $linea['product_id']);
					if (count($d) == 0)
					{
						# Buscamos por ISBN
						$d = $this->m_articulocodigo->get(null, null, null, null, 'nCodigo=' . $this->db->escape($linea['model']));
					}
					if (count($d) > 0)
					{
						$art = $this->m_articulo->load($d[0]['nIdLibro']);
					}
					if (!$art)
					{
						$this->db->trans_rollback();
						$this->out->error(sprintf($this->lang->line('id-articulo-error-web'), $linea['product_id']));
					}
				}
				$lineas[] = array(
					'nIdLibro' => $art['nIdLibro'],
					'nCantidad' => $linea['quantity'],
					'fCoste' => (float)$art['fPrecioCompra'],
					'fPrecio' => $linea['price'],
					'fIVA' => $linea['tax'],
					'nIdSeccion' => $seccion
				);
			}
			#$portes = null;
			$totals = array();

			foreach ($order['totals'] as $total)
			{
				$totals[] = sprintf($this->lang->line('webpage-notas-total'), $total['title'], format_price($total['value']));
				if ($total['code'] == 'shipping' && ($total['value'] > 0))
				{
					$lineas[] = array(
						'nIdLibro' 		=> $this->config->item('bp.idportes'),
						'fPrecio' 		=> format_quitar_iva($total['value'], $this->config->item('bp.ivaportes')),
						'nIdSeccion' 	=> $seccion,
						'cRefInterna' 	=> $total['title'],
						'fIVA'			=> $this->config->item('bp.ivaportes'),
						'nCantidad' 	=> 1
						);
				}
			}
			$pedido['nIdCliente'] = $idcliente;
			$pedido['nIdDirEnv'] = $idshipping;
			$pedido['nIdDirFac'] = $idpayment;
			$pedido['nIdWeb'] = $id;
			$pedido['nIdTipoOrigen'] = $this->config->item('bp.webpage.origenpedido');
			$pedido['tNotasExternas'] = $order['comment'];
			$shipping_address = implode(' / ', array(
				$order['shipping_firstname'],
				$order['shipping_lastname'],
				$order['shipping_address_1'],
				$order['shipping_address_2'],
				$order['shipping_postcode'],
				$order['shipping_city'],
				$order['shipping_zone'],
				$order['shipping_country'],
				));
			$payment_address = implode(' / ', array(
				$order['payment_firstname'],
				$order['payment_lastname'],
				$order['payment_address_1'],
				$order['payment_address_2'],
				$order['payment_postcode'],
				$order['payment_city'],
				$order['payment_zone'],
				$order['payment_country'],
				));

			$pedido['tNotasInternas'] = sprintf($this->lang->line('webpage-notas-pedido'), 
					$order['shipping_method'] . '<br/>'. $shipping_address, 
					$order['payment_method'] . '<br/>'. $payment_address, 
					implode('', $totals), 
					$order['email'],
					$order['telephone']);
			#var_dump($payment_address, $shipping_address, $pedido['tNotasInternas']); die();
			$pedido['lineas'] = $lineas;
			//webpage-notas-pedido
			$this->load->model('ventas/m_pedidocliente');

			$id_n = $this->m_pedidocliente->insert($pedido);
			if ($id_n < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_pedidocliente->error_message());
			}
			#echo 'pedido'; var_dump($id_n);

			# Histórico
			$this->load->model('generico/m_nota');

			$data = array (
				'nIdTipoObservacion' 	=> $this->config->item('bp.webpage.tiponota'),
				'cTabla'				=> $this->m_pedidocliente->get_tablename(),
				'nIdRegistro'			=> (int)$id_n
			);
				
			foreach ($order['history'] as $v)
			{
				if (trim($v['comment']) != '')
				{
					$data['tObservacion'] = str_replace("\n", '<br/>', $v['comment']);
					if (($this->m_nota->insert($data)) < 0)
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_nota->error_message());
					}
				}
			}

			# Actualiza el pedido de Internet
			$data = array(
				'id' 			=> $order['order_id'],
				'foreign_id' 	=> $id_n
			);

			#$this->webshop->debug = 3;
			$res = $this->webshop->action('api/order/upd', $data);
			if (!$res['success'])
			{
				$this->db->trans_rollback();
				$this->out->error(sprintf($this->lang->line('error-llamada-web'), $res['message']));
			}

			#var_dump($pedido);
			#$this->db->trans_rollback();
			$this->db->trans_commit();
			$nota = sprintf($this->lang->line('pedidocliente_importado_internet'), $id);
			$this->_add_nota(null, $id_n, NOTA_INTERNA, $nota, $this->m_pedidocliente->get_tablename());
			$link_pd = format_enlace_cmd($id_n, site_url('ventas/pedidocliente/index/' . $id_n));
			$res = array(
				'success' 	=> TRUE,
				'message' 	=> sprintf($this->lang->line('pedidocliente_add_pedido_nuevo'), $link_pd),
				'id'		=> $id_n
				);
			$this->out->send($res);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Indica que un pedido de Internet ha sido enviado
	 * @param int $id Id del pedido de Internet
	 * @return MSG
	 */
	function enviado($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.crear_pedidos');

		$id = isset($id) ? $id : $this->input->get_post('id');
		if (is_numeric($id))
		{
			$this->load->model('ventas/m_pedidocliente');
			$ped = $this->m_pedidocliente->load($id);
			if (!isset($ped['nIdWeb']))
			{
				$this->out->error(sprintf($this->lang->line('pedido-cliente-no-web'), $id));
			}
			$this->login_web();
			$data['id'] = $ped['nIdWeb'];
			#$this->webshop->debug = 3;
			$res = $this->webshop->action('api/order/enviado', $data);
			if (!$res['success'])
			{
				$this->out->error(sprintf($this->lang->line('error-llamada-web'), $res['message']));
			}

			$nota = $this->lang->line('pedidocliente_enviado_internet');
			$this->_add_nota(null, $id, NOTA_INTERNA, $nota, $this->m_pedidocliente->get_tablename());
			$this->out->success($nota);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Publica el cliente en la Web para que pueda realizar compras
	 * @param  int $id Id del cliente
	 * @return MSG
	 */
	function publicarcliente($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.crear_pedidos');

		$id = isset($id) ? $id : $this->input->get_post('id');
		if (is_numeric($id))
		{
			$this->load->model('clientes/m_cliente');
			$this->load->model('clientes/m_email');
			$this->load->model('clientes/m_telefono');
			$this->load->model('clientes/m_direccioncliente');
			$cl = $this->m_cliente->load($id,TRUE);
			if (isset($cl['nIdWeb']))
			{
				$this->out->error(sprintf($this->lang->line('cliente-en-web'), $id));
			}
			$emails = $this->m_email->get_list($id);
			$em = $this->utils->get_profile($emails, PERFIL_GENERAL);
			$tels = $this->m_telefono->get_list($id);
			$tf = $this->utils->get_profile($tels, PERFIL_GENERAL);
			$dirs = $this->m_direccioncliente->get_list($id);
			$dir = $this->utils->get_profile($dirs, PERFIL_GENERAL, TRUE);
			if (isset($dir))
			{
				$dir = $this->m_direccioncliente->load($dir['id']);
			}

			$clweb = array(
				'firstname' 	=> (trim($cl['cNombre'])!='')?trim($cl['cNombre']):((trim($cl['cEmpresa'])!='')?trim($cl['cEmpresa']):null),
				'lastname' 		=> (trim($cl['cApellido'])!='')?trim($cl['cApellido']):null,
				'company' 		=> (trim($cl['cEmpresa'])!='')?trim($cl['cEmpresa']):null,
				'email'			=> ($em['text'])?$em['text']:null,
				'telephone'		=> ($tf['text'])?$tf['text']:null,
				'nIdCliente'	=> $id,
				'cPass'			=> $cl['cPass'],
				'address_1'		=> (trim($dir['cTitular'])!='')?trim($dir['cTitular']):null,
				'address_2'		=> !empty($dir['cCalle'])?$dir['cCalle']:null,
				'city'			=> !empty($dir['cPoblacion'])?$dir['cPoblacion']:null,
				'postcode'		=> !empty($dir['cCP'])?$dir['cCP']:null,
				'country_id'	=> !empty($dir['nIdPais'])?$dir['nIdPais']:null,
				'zone_id'		=> !empty($dir['nIdRegion'])?$dir['nIdRegion']:null,
				);
			$data['cliente'] = serialize($clweb);
			$this->login_web();
			#$this->webshop->debug = 3;
			$res = $this->webshop->action('api/customer/add', $data);
			if (!$res['success'])
			{
				$this->out->error(sprintf($this->lang->line('error-llamada-web'), $res['message']));
			}
			#var_dump($res); die();
			$this->m_cliente->update($id, array('nIdWeb' => $res['id']));

			$nota = $res['new']?sprintf($this->lang->line('cliente_enviado_internet'), $res['id']):
				sprintf($this->lang->line('cliente_enviado_internet_vinculado'), $res['id']);
			$this->_add_nota(null, $id, NOTA_INTERNA, $nota, $this->m_cliente->get_tablename());
			$this->out->success($nota);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Busca una coincidencia de cliente
	 * @param string $text Texto a buscar
	 * @param array $data Datos del pedido de Internet
	 * @return int Id del cliente encontrado, null si no lo encuentra 
	 */
	private function buscar_cliente($text, $data)
	{		
		$cl = $this->m_cliente->search($text);
		#var_dump($cl); die();
		if (count($cl) > 0)
		{
			$n1 = trim(str_replace(array(' ', '<br/>', "\n", "\r", '.', '-'), '', $data['firstname'] . $data['lastname']));
			foreach($cl as $c)
			{
				$cl2 = $this->m_cliente->load($c['id']);
				if (trim(str_replace(' ', '', $cl2['cNombre'] . $cl2['cApellido'])) == $n1)
					return $c['id'];
				if (trim(str_replace(' ', '', $cl2['cEmpresa'])) == $n1)
					return $c['id'];
			}
		}
		#die();
		return null;		
	}

	/**
	 * Busca una coincidencia de cliente
	 * @param string $text Texto a buscar
	 * @param array $data Datos del pedido de Internet
	 * @return int Id del cliente encontrado, null si no lo encuentra 
	 */
	private function buscar_email($email)
	{		
		$cl = $this->m_email->get(0, 2, null, null, 'cEmail=' . $this->db->escape($email));
		#var_dump($cl);
		if (count($cl) == 1)
		{
			return $cl[0]['nIdCliente'];
		}
		return null;		
	}

	/**
	 * Busca una coincidencia de cliente. Si no existe la crea
	 * @param array $data Datos del pedido de Internet
	 * @return int Id del cliente encontrado 
	 */
	private function crear_cliente($data)
	{
		$this->load->model('clientes/m_telefono');
		$this->load->model('clientes/m_email');
		#var_dump($data);				
		#Comprueba que no existe el mismo con el email y el nombre
		$cl = $this->buscar_email($data['email']);
		#var_dump($cl); die();
		if (isset($cl)) return $cl;
		# Busca por nombre
		#$cl = $this->buscar_cliente($data['firstname'] . ' ' . $data['lastname'], $data);
		#var_dump($cl);
		#if (isset($cl)) return $cl;
		# Lo crea
		//Preparamos los datos
		$new = array(
			'cNombre' => $data['firstname'],
			'cApellido' => $data['lastname'],
			'cEmpresa' => $data['company'],
			'cPass' => $data['password'],
			'bNoEmail' => ($data['newsletter'] == 0),
			'cDescripcion' => $this->lang->line('direccion-internet')
			);

		$id_cliente = $this->m_cliente->insert($new);
		if ($id_cliente < 1)
		{
			$this->_error = $this->m_cliente->error_message();
			return null;
		}
		if (isset($data['email']))
		{
			$id = $this->m_email->insert(array('nIdCliente' => $id_cliente, 'cEMail' => $data['email']));
			if ($id < 1)
			{
				$this->_error = $this->m_email->error_message();
				return null;
			}
		}
		if (isset($data['telephone']))
		{
			$id = $this->m_telefono->insert(array('nIdCliente' => $id_cliente, 'cTelefono' => $data['telephone']));
			if ($id < 1)
			{
				$this->_error = $this->m_telefono->error_message();
				return null;
			}
		}
		return $id_cliente;
	}

	/**
	 * Busca una coincidencia de dirección de cliente. Si no existe la crea
	 * @param int $id Id del cliente
	 * @param array $data Datos del pedido de Internet
	 * @param int Id del tipo de dirección
	 * @return int Id del cliente encontrado 
	 */
	private function crear_direccion($id, $data, $idtipo = PERFIL_GENERAL)
	{
		$this->load->model('clientes/m_direccioncliente');
		$direcciones = $this->m_direccioncliente->get(null, null, null, null, 'nIdCliente=' . $id );
		#Comprueba que no existe la misma dirección
		foreach ($direcciones as $dir) 
		{
			if (($data['cCalle'] == $dir['cCalle']) && ($data['nIdRegion'] == $dir['nIdRegion']))
				return $dir['nIdDireccion'];
		}
		# La crea
		$new = array(
			'nIdCliente' => $id,
			'cCalle' => $data['cCalle'],
			'cCP' => $data['cCP'],
			'nIdRegion' => $data['nIdRegion'],
			'cPoblacion' => $data['cPoblacion'],
			'cDescripcion' => $this->lang->line('direccion-internet')
			);
		$id_n = $this->m_direccioncliente->insert($new);
		if ($new < 0)
		{
			$this->_error = $this->m_direccioncliente->error_message();
			return null;
		}
		return $id_n;	
	}

	/**
	 * Elementos multimedia de los artículos
	 * @return DATA
	 */
	function ofertas()
	{
		set_time_limit(0);
		$this->load->model('catalogo/m_articulo');
		$data2 = array();
		$data = $this->reg->get_ofertas();
		foreach($data as $reg)
		{
			$art = $this->m_articulo->load($reg['nIdLibro']);
			$data2[] = array(
				'nIdLibro'	=> $reg['nIdLibro'],
				'fPrecio'	=> $art['fPrecio']
				);
		}
		$this->out->data($data2);
	}

	/**
	 * ID de todos los artículos del sistema
	 * @return DATA
	 */
	function articulos()
	{
		set_time_limit(0);
		$data = $this->reg->articulos();
		$data2 = array();
		foreach($data as $reg)
			$data2[] = $reg['nIdLibro'];
		$this->out->data($data2);
	}

	/**
	 * ID de todos los clientes de Bibliopola que tienen cliente Web
	 * @return DATA
	 */
	function clientes()
	{
		set_time_limit(0);
		$data = $this->reg->clientes();
		$this->out->data($data);
	}

	/**
	 * Sincroniza los pedidos de la Web con el estado de los pedidos del sistema
	 * @param int $id ID del pedido (si solo se quiere sincronizar 1)
	 * @param int $cliente ID del cliente (si solo se quiere sincronizar los pedidos del cliente)
	 * @return DIALOG
	 */
	function syncro_pedidos($id = null, $cliente = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$cliente = isset($cliente) ? $cliente : $this->input->get_post('cliente');
		set_time_limit(0);
		#die();

		$this->load->model('ventas/m_pedidocliente');
		$this->load->model('clientes/m_cliente');
		$this->load->model('clientes/m_email');
		$this->load->model('clientes/m_telefono');
		$this->load->model('clientes/m_direccioncliente');

		$idportes = $this->config->item('bp.idportes');

		$this->load->library('Configurator');		
		$last = (int)$this->configurator->system('webpage.pedidocliente.last');

		$ok = array();
		$error = array();

		$estados = array(
			2	=> 5,
			1	=> 2,
			7	=> 3,
			6	=> 15,
			3	=> 2			
			);
		$filter = (is_numeric($id))?"Doc_PedidosCliente.nIdPedido={$id}":null;
		$filter = (is_numeric($cliente))?"Doc_PedidosCliente.nIdCliente={$cliente}":$filter;
		if (is_numeric($id)) $last = null;
		$data = $this->reg->get_pedidos_web($last, $filter);
		$last = time();
		#var_dump(count($data)); die();
		#var_dump($data); die();
		foreach ($data as $reg)
		{
			$ped = $this->m_pedidocliente->load($reg['nIdPedido'], array('lineas', 'cliente', 'direccion', 'direccionfactura'));
			#$last = $reg['dAct'];

			#Dirección de envío y de factura
			$dirs = $this->m_direccioncliente->get_list($ped['nIdCliente']);
			if (empty($ped['direccion']))
				$ped['direccion'] = $this->utils->get_profile($dirs, PERFIL_ENVIO);
			if (empty($ped['direccionfactura']))
				$ped['direccionfactura'] = $this->utils->get_profile($dirs, PERFIL_FACTURACION);

			# Datos de contacto
			$emails = $this->m_email->get_list($ped['nIdCliente']);
			$em = $this->utils->get_profile($emails, PERFIL_GENERAL);
			$tels = $this->m_telefono->get_list($ped['nIdCliente']);
			$tf = $this->utils->get_profile($tels, PERFIL_GENERAL);

			$dir = $ped['direccion'];
			$cl = $ped['cliente'];

			$pedweb = array(
					'order_id'				=> $ped['nIdWeb'],
					'foreign_id'			=> $reg['nIdPedido'],
					'order_status_id'		=> $estados[$ped['nIdEstado']],
					'date_added'			=> format_mssql_date($ped['dCreacion']),
					'date_modified'			=> format_mssql_date($ped['dAct']),
					'ref'					=> $ped['cRefCliente'],

					'customer_id'			=> $ped['cliente']['nIdWeb'],
					'firstname' 			=> (trim($cl['cNombre'])!='')?trim($cl['cNombre']):((trim($cl['cEmpresa'])!='')?trim($cl['cEmpresa']):null),
					'lastname' 				=> (trim($cl['cApellido'])!='')?trim($cl['cApellido']):null,
					'company' 				=> (trim($cl['cEmpresa'])!='')?trim($cl['cEmpresa']):null,
					'email'					=> ($em['text'])?$em['text']:null,
					'telephone'				=> ($tf['text'])?$tf['text']:null,

					'shipping_firstname' 	=> (trim($cl['cNombre'])!='')?trim($cl['cNombre']):((trim($cl['cEmpresa'])!='')?trim($cl['cEmpresa']):null),
					'shipping_lastname' 	=> (trim($cl['cApellido'])!='')?trim($cl['cApellido']):null,
					'shipping_company'		=> (trim($cl['cEmpresa'])!='')?trim($cl['cEmpresa']):null,
					'shipping_address_1'	=> (trim($dir['cTitular'])!='')?trim($dir['cTitular']):null,
					'shipping_address_2'	=> !empty($dir['cCalle'])?$dir['cCalle']:null,
					'shipping_city'			=> !empty($dir['cPoblacion'])?$dir['cPoblacion']:null,
					'shipping_postcode'		=> !empty($dir['cCP'])?$dir['cCP']:null,
					'shipping_country_id'	=> !empty($dir['nIdPais'])?$dir['nIdPais']:null,
					'shipping_zone_id'		=> !empty($dir['nIdRegion'])?$dir['nIdRegion']:null,					
					/*'shipping_address_format'
					'shipping_method'*/
					);

			$dir = $ped['direccionfactura'];

			$pedweb = array_merge($pedweb, 
				array(
					'payment_firstname' 	=> (trim($cl['cNombre'])!='')?trim($cl['cNombre']):((trim($cl['cEmpresa'])!='')?trim($cl['cEmpresa']):null),
					'payment_lastname' 		=> (trim($cl['cApellido'])!='')?trim($cl['cApellido']):null,
					'payment_company'		=> (trim($cl['cEmpresa'])!='')?trim($cl['cEmpresa']):null,
					'payment_address_1'		=> (trim($dir['cTitular'])!='')?trim($dir['cTitular']):null,
					'payment_address_2'		=> !empty($dir['cCalle'])?$dir['cCalle']:null,
					'payment_city'			=> !empty($dir['cPoblacion'])?$dir['cPoblacion']:null,
					'payment_postcode'		=> !empty($dir['cCP'])?$dir['cCP']:null,
					'payment_country_id'	=> !empty($dir['nIdPais'])?$dir['nIdPais']:null,
					'payment_zone_id'		=> !empty($dir['nIdRegion'])?$dir['nIdRegion']:null,
					)
				);

			$total = 0;
			$pedweb['lineas'] = array();
			foreach ($ped['lineas'] as $linea) 
			{
				$no = in_array($linea['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADO,
					ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADO_Y_CATALOGADO,
					ESTADO_LINEA_PEDIDO_CLIENTE_NO_SE_PUEDE_SERVIR,
					ESTADO_LINEA_PEDIDO_CLIENTE_RECHAZADO,
					ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA,
					ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA_Y_CATALOGADA
					));

				$l = array(
					'product_id' 	=> $linea['nIdLibro'],
					'name' 			=> $linea['cTitulo'],
					'model' 		=> $linea['cISBN'],
					'quantity' 		=> $linea['nCantidad'],
					'price' 		=> ($no)?0:$linea['fPrecio'],
					'total' 		=> ($no)?0:$linea['fPrecio'] * $linea['nCantidad'],
					'tax' 			=> $linea['fIVA'],
					'fIVAImporte'	=> ($no)?0:$linea['fIVAImporte'],
					'discount' 		=> ($no)?0:$linea['fDescuento'],
					'status' 		=> ($linea['nIdLibro'] == $idportes)?0:$linea['nIdEstado'],
					'ref'			=> $linea['cRefCliente'],
					'option' 		=> array(),
					'download' 		=> array()
					);

				$pedweb['lineas'][] = $l;
				$total += $linea['fTotal'];
			}
			$pedweb['total'] = $total;
			$pedweb['comment'] = $ped['tNotasExternas'];

			$params['pedido'] = serialize($pedweb);
			$this->login_web();
			#$this->webshop->debug = 3;
			$res = $this->webshop->action('api/order/sincro', $params);

			if (!$res['success'])
			{
				$error[] = array(
					'id'	=> $reg['nIdPedido'],
					'idweb'	=> $ped['nIdWeb'],
					'error'	=> $res['message']
					);
			}
			else 
			{
				if ($res['id'] && empty($ped['nIdWeb']))
				{
					if (!$this->m_pedidocliente->update($reg['nIdPedido'], array('nIdWeb' => $res['id'])))
					{
						$error[] = array(
							'id'	=> $reg['nIdPedido'],
							'idweb'	=> $ped['nIdWeb'],
							'error'	=> $this->m_pedidocliente->error_message()
							);
					}
					continue;
				}
				$ok[] = array(
					'id'		=> $reg['nIdPedido'],
					'idweb'		=> $res['id'],
					'create'	=> empty($ped['nIdWeb'])
					);
			}
		}
		if (!is_numeric($id))
			$this->configurator->set_system('webpage.pedidocliente.last', (string)$last);

		$message[] = sprintf($this->lang->line('webpage-pedidocliente-syncro-result'), count($ok));
		foreach ($error as $e)
		{
			$message[] = sprintf($this->lang->line('webpage-pedidocliente-syncro-error'), $e['id'], $e['idweb'], $e['error']);
		}
		foreach ($ok as $e)
		{
			$message[] = sprintf($this->lang->line('webpage-pedidocliente-syncro-ok'), $e['id'], $e['idweb']);
		}
		$this->out->dialog($this->lang->line('Sincronizar pedidos'), implode('<br/>', $message));
	}

	/**
	 * Sincroniza los clientes de la Web con los de la tienda, según el email de la Web
	 * @return DIALOG
	 */
	function syncro_clientes()
	{
		$this->userauth->roleCheck($this->auth . '.crear_pedidos');

		$this->login_web();
		#$this->webshop->debug = 3;
		$res = $this->webshop->action('api/customer/syncro');
		if (!$res['success'])
		{
			$this->out->error(sprintf($this->lang->line('error-llamada-web'), $res['message']));
		}

		$this->load->model('clientes/m_cliente');
		$this->load->model('clientes/m_email');

		$count = 0;
		$error = $ok = array();
		foreach($res['customers'] as $cl)
		{
			$id = $this->buscar_email($cl['email']);
			if ($id)
			{
				$data['id'] = $cl['customer_id'];
				$data['foreign_id'] = $id;
				#$this->webshop->debug = 3;
				$res = $this->webshop->action('api/customer/upd', $data);
				if (!$res['success'])
				{
					$error[] = array(
						'id'	=> $id,
						'idweb'	=> $cl['customer_id'],
						'error'	=> $res['message']
						);
				}
				else
				{
					# Lo vincula y añade una nota
					$this->m_cliente->update($id, array('nIdWeb' => $cl['customer_id']));
					$nota = sprintf($this->lang->line('cliente_enviado_internet_vinculado'), $cl['customer_id'], $cl['firstname'] . ' ' . $cl['lastname']);
					$this->_add_nota(null, $id, NOTA_INTERNA, $nota, $this->m_cliente->get_tablename());
					$ok[] = array(
						'id'	=> $id,
						'idweb'	=> $cl['customer_id']
						);
				}
			}
		}
		$message[] = sprintf($this->lang->line('webpage-cliente-syncro-result'), count($ok));
		foreach ($error as $e)
		{
			$message[] = sprintf($this->lang->line('webpage-cliente-syncro-error'), $e['id'], $e['idweb'], $e['error']);
		}
		foreach ($ok as $e)
		{
			$message[] = sprintf($this->lang->line('webpage-cliente-syncro-ok'), $e['id'], $e['idweb']);
		}
		$this->out->dialog($this->lang->line('Sincronizar clientes'), implode('<br/>', $message));
	}
}

/* End of file webpage.php */
/* Location: ./system/application/controllers/web/webpage.php */
