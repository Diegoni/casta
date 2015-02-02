<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	clientes
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador de clientes
 *
 */
class Cliente extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Cliente
	 */
	function __construct()
	{
		parent::__construct('clientes.cliente', 'clientes/M_cliente', TRUE, 'clientes/cliente.js', 'Clientes');
	}

	/**
	 * Busca una cuenta libre para el tipo de cliente indicado
	 * @param int $tipo Id del tipo de cliente
	 * @return JSON
	 */
	function index() {
		echo "entro";
	} 
	 
	function cuenta($tipo = null)
	{
		$this->userauth->roleCheck(($this->auth.'.cuenta'));

		$tipo	= isset($tipo)?$tipo:$this->input->get_post('tipo');

		if ($tipo)
		{

			$this->load->model('clientes/m_tipocliente');
			$tipo = $this->m_tipocliente->load($tipo);
			$base = $tipo['nCuenta'];
			$digitos = $this->config->item('bp.clientes.digitoscuenta');
			$min = (float)($base . str_repeat('0', $digitos - strlen($base)));
			$max = (float)($base . str_repeat('9', $digitos - strlen($base)));
			$cuenta = $this->reg->next_cuenta($min, $max);
			$this->out->success($cuenta);
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}

	/**
	 * Unificador de clientes
	 * @param int $id1 Id de la cliente destino
	 * @param string $id2 Ids de las clientes repetidos, separados por ;
	 * @return JSON
	 */
	function unificar($id1 = null, $id2 = null)
	{
		$this->userauth->roleCheck(($this->auth.'.unificar'));

		$id1	= isset($id1)?$id1:$this->input->get_post('id1');
		$id2	= isset($id2)?$id2:$this->input->get_post('id2');

		if ($id1 && $id2)
		{
			$ids = preg_split('/\;/', $id2);
			$t = '';
			$this->load->library('Logger');
			if (!$this->reg->unificar($id1, $ids))
			{
				$str = $this->reg->error_message();
				$this->out->error($str);
			}
			$this->logger->log('Cliente unificado ' . implode(',', $ids) . ' con ' .$id1, 'unificador');
			$this->out->success($this->lang->line('clientes-unificados-ok'));
		}
		else
		{
			$data['title'] = $this->lang->line('Unificar cliente');
			$data['icon'] = 'iconoUnficarClienteTab';
			$data['url_search'] = site_url('clientes/cliente/search');
			$data['url'] = site_url('clientes/cliente/unificar');
			$this->_show_form('unificar', 'catalogo/unificador.js', $this->lang->line('Unificar cliente'), null, null, null, $data);
		}
	}

	/**
	 * Devuelve la información del cliente para mostrar en los documentos
	 * @param int $id Id del cliente
	 */
	function info($id = null)
	{
		$this->userauth->roleCheck(($this->auth.'.index'));

		$id	= isset($id)?$id:$this->input->get_post('id');

		$cliente = null;
			
		if ($id)
		{
			$cliente = $this->reg->load($id, TRUE);
			#print '<pre>'; print_r($cliente); print '</pre>';
			$info = array();
			if ($cliente['bCredito']) $info[] = sprintf($this->lang->line('cliente-tiene-cuenta'), $cliente['nIdCuenta']);
			if (isset($cliente['cGrupoCliente'])) $info[] = sprintf($this->lang->line('cliente-grupo'), $cliente['cGrupoCliente']);
			if (isset($cliente['descuentos']))
			{
				foreach($cliente['descuentos'] as $descuento)
				{
					$info[] = sprintf($this->lang->line('cliente-descuento'), $descuento['cDescripcion'], format_percent($descuento['fValor']));
				}
			}
			if (isset($cliente['descuentosgrupo']))
			{
				foreach($cliente['descuentosgrupo'] as $descuento)
				{
					$info[] = sprintf($this->lang->line('cliente-descuento-grupo'), $descuento['cDescripcion'], format_percent($descuento['fValor']));
				}
			}
			if (isset($cliente['cTipoTarifa']))
			{
					$info[] = sprintf($this->lang->line('cliente-tarifageneral'), $cliente['cTipoTarifa']);
			}
			if (isset($cliente['tarifas']))
			{
				foreach($cliente['tarifas'] as $tarifa)
				{
					$info[] = sprintf($this->lang->line('cliente-tarifa'), $tarifa['cTipo'], $tarifa['cTipoTarifa']);
				}
			}
			if (isset($cliente['tNotas']) && (trim($cliente['tNotas']) != '')) $info[] = str_replace("\n", '<br/>', $cliente['tNotas']);
			$text = (count($info) > 0)?implode($info, '<br/>'):null;
			$cliente['info'] = $text;
		}
		$this->out->data($cliente);
	}

	/**
	 * Alta rápida del cliente
	 * @return FORM
	 */
	function alta()
	{
		$this->userauth->roleCheck(($this->auth.'.add'));
		$data = get_post_all();
		foreach($data as $k => $v)
		{
			if (trim($v) == ''){
				unset($data[$k]);
			}
			elseif (is_string($v))
			{
				$data[$k] = urldecode($v);
			}
		}
		if (isset($data['cEmpresa']) || isset($data['cNombre']) || isset($data['cApellido']))
		{
			//Preparamos los datos
			$this->load->model('clientes/m_cliente');
			$this->load->model('clientes/m_telefono');
			$this->load->model('clientes/m_direccioncliente');
			$this->load->model('clientes/m_email');
			$this->db->trans_begin();
			$id_cliente = $this->m_cliente->insert($data);
			if ($id_cliente < 1)
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_cliente->error_message());
			}
			if (isset($data['cEmail']))
			{
				$id = $this->m_email->insert(array('nIdCliente' => $id_cliente, 'cEMail' => $data['cEmail']));
				if ($id < 1)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_email->error_message());
				}
			}
			if (isset($data['cTelefono']))
			{
				$id = $this->m_telefono->insert(array('nIdCliente' => $id_cliente, 'cTelefono' => $data['cTelefono']));
				if ($id < 1)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_telefono->error_message());
				}
			}
			if (isset($data['cCalle']))
			{
				$data['nIdCliente'] =  $id_cliente;
				$id = $this->m_direccioncliente->insert($data);
				if ($id < 1)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_direccioncliente->error_message());
				}
			}
			$this->db->trans_commit();

			$res = array (
				'success'	=> TRUE,
				'message'	=> sprintf($this->lang->line('registro_generado'), $id_cliente),
				'id'		=> $id_cliente
			);
			$this->out->send($res);
		}
		else
		{
			$this->_show_js('add', 'clientes/altarapida.js');
		}
	}

	/**
	 * Cesta de la compra del cliente
	 * @param int $id Id del cliente
	 * @return HTML_FILE
	 */
	function cesta($id = null)
	{
		$this->userauth->roleCheck(($this->auth.'.index'));

		$id	= isset($id)?$id:$this->input->get_post('id');

		$cliente = null;
			
		if (is_numeric($id))
		{
			$cl = $this->reg->load($id);
			if (!isset($cl['nIdWeb']))
				$this->out->error($this->lang->line('cliente-sin-internet'));

			$this->load->library('Webshop');
			#$this->webshop->debug = 3;
			$res = $this->webshop->login();
			if (!$res)
				$this->out->error($this->lang->line('webshop-error-login'));

			$res = $this->webshop->action('api/customer/cart', array('id' => $cl['nIdWeb']));
			if ($res['success'])
			{
				$list = unserialize($res['data']);
				$this->load->model('catalogo/m_articulo');
				foreach ($list as $key => $value) 
				{
					$lb = $this->m_articulo->load($key);
					$list[$key] = array(
						'nIdLibro'	=> $key,
						'nCantidad'	=> $value,
						'cTitulo'	=> $lb['cTitulo'],
						'fPVP'		=> $lb['fPVP']
						);
				}
				if (count($list) > 0 )
				{
					$message = $this->load->view('clientes/listadolibros', array ('libros' => $list), TRUE);
					$this->out->html_file($message, $this->lang->line('Cesta cliente'). ' ' . $id, 'iconoReportTab');
				}
			}
			$this->out->success($this->lang->line('no-hay-documentos'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Favoritos del cliente
	 * @param int $id Id del cliente
	 * @return HTML_FILE
	 */
	function favoritos($id = null)
	{
		$this->userauth->roleCheck(($this->auth.'.index'));

		$id	= isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{
			$cl = $this->reg->load($id);
			if (!isset($cl['nIdWeb']))
				$this->out->error($this->lang->line('cliente-sin-internet'));

			$this->load->library('Webshop');
			#$this->webshop->debug = 3;
			$res = $this->webshop->login();
			if (!$res)
				$this->out->error($this->lang->line('webshop-error-login'));

			$res = $this->webshop->action('api/customer/wishlist', array('id' => $cl['nIdWeb']));
			if ($res['success'])
			{
				$list = unserialize($res['data']);
				#var_dump($list); die();
				$this->load->model('catalogo/m_articulo');
				foreach ($list as $key => $value) 
				{
					$lb = $this->m_articulo->load($value);
					$list[$key] = array(
						'nIdLibro'	=> $value,
						'nCantidad'	=> 1,
						'cTitulo'	=> $lb['cTitulo'],
						'fPVP'		=> $lb['fPVP']
						);
				}
				if (count($list) > 0)
				{
					$message = $this->load->view('clientes/listadolibros', array ('libros' => $list), TRUE);
					$this->out->html_file($message, $this->lang->line('Favoritos cliente'). ' ' . $id, 'iconoReportTab');
				}
			}
			$this->out->success($this->lang->line('no-hay-documentos'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
	
	/**
	 * Documentos desglosado por titulos de un cliente
	 * @param int $idc ID del cliente
	 * @param date $fecha1 Fecha desde
	 * @param date $fecha2 Fecha hasta
	 * @param string $tipo Tipo de documento a miostrar, separado por ;
	 * @return HTML_FILE
	 */
	function documentos_articulos($idc = null, $fecha1 = null, $fecha2 = null, $tipo = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$idc		= isset($idc)?$idc:$this->input->get_post('idc');
		$fecha1 	= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2 	= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');
		$tipo 		= isset($tipo)?$tipo:$this->input->get_post('tipo');
		if ($tipo !== FALSE) $tipo = preg_split("/[,\|\s]/", $tipo);
		if ($tipo === FALSE) $tipo = TRUE;

		if (!empty($idc) && !empty($fecha1) && !empty($fecha2))
		{
			set_time_limit(0);
			$cl = $this->reg->load($idc);
			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);
			$this->load->model('catalogo/m_articulo');
			$docs = $this->m_articulo->get_documentos(null, $fecha1, $fecha2, null, $tipo, $idc);
			if (count($docs)>0)
			{
				sksort($docs, 'dFecha');
				$data['docs'] = $docs;
				$data['fecha1'] = $fecha1;
				$data['fecha2'] = $fecha2;
				$data['titulo'] = $this->lang->line('Documentos cliente');
				$data['item'] = format_name($cl['cNombre'], $cl['cApellido'], $cl['cEmpresa']);
				$data['id'] = $cl['id'];
				$data['clpv'] = FALSE;
				$message = $this->load->view('catalogo/documentos', $data, TRUE);
				$this->out->html_file($message, sprintf($this->lang->line('Documentos cliente artículos'), $idc), 'iconoReportTab');
			}
			$this->out->success($this->lang->line('no-hay-documentos'));
		}
		elseif (is_numeric($idc))
		{
			$data['url'] = site_url('clientes/cliente/documentos_articulos');
			$data['title'] = sprintf($this->lang->line('Documentos cliente artículos'), '');
			$data['idl'] = $idc;
			$data['name'] = 'idc';

			$this->_show_js('get_list', 'catalogo/documentosasticulo.js', $data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Envía la contraña a la página Web
	 * @param int $idc ID del cliente
	 * @return MSG
	 */
	function passwordweb($id = null)
	{
		$this->userauth->roleCheck($this->auth.'.upd');

		$id	= isset($id)?$id:$this->input->get_post('id');

		$this->load->library('Webshop');
		$res = $this->webshop->login();
		if (!$res)
			$this->out->error($this->lang->line('webshop-error-login'));

		if (is_numeric($id))
		{
			$cl = $this->reg->load($id);
			if (!isset($cl['cPass']) || $cl['cPass'] == '')
				$this->out->error($this->lang->line('cliente-sin-password'));
			if (!isset($cl['nIdWeb']))
				$this->out->error($this->lang->line('cliente-sin-internet'));


			$res = $this->webshop->action('api/customer/upd_pwd', array('id' => $cl['nIdWeb'], 'pwd' => $cl['cPass']));
			if ($res['success'])
			{
				$this->out->success(sprintf($this->lang->line('cliente-password-web-ok'), $id));
			}
			$this->out->error($this->webshop->get_error($res));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra los mailings que se han enviado al cliente
	 * @param int $idc ID del cliente
	 * @return HTML
	 */
	function mailings($id = null)
	{
		$this->userauth->roleCheck($this->auth.'.get_list');

		$id	= isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{
			$this->load->model('clientes/m_email');
			$this->load->model('mailing/m_mailing');

			$emails = $this->m_email->get(null, null, null, null, 'nIdCliente=' .$id );
			$em = array();
			foreach ($emails as $value) 
			{
				$em[] = $value['cEMail'];
			}
			$data = $this->m_mailing->sended_email($em);
			#echo array_pop($this->db->queries); 
			#var_dump($data); die();
			if (count($data) > 0)
			{
				$message = $this->load->view('clientes/mailing', array('mailing' => $data), TRUE);
				$this->out->html_file($message, $this->lang->line('Boletines enviados') . ' ' . $id, 'iconoReportTab');
			}
			else
			{
				$this->out->dialog($this->lang->line('Boletines enviados') . ' ' . $id, $this->lang->line('no-mailings'));
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

}

/* End of file cliente.php */
/* Location: ./system/application/controllers/clientes/cliente.php */