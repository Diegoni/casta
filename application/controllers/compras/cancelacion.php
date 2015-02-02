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
 * Cancelación Pedido Proveedor
 *
 */
class Cancelacion extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Cancelacion
	 */
	function __construct()
	{
		parent::__construct('compras.cancelacion', 'compras/m_cancelacion', TRUE, 'compras/cancelacion.js', 'Cancelación pedido proveedor');
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
		$message = $this->load->view('compras/cancelacion', $data, TRUE);
		$this->load->library('HtmlFile');
		$filename = $this->obj->htmlfile->create($message, $this->lang->line('Cancelación pedido proveedor') . ' ' . $id, $this->config->item('bp.documentos.css'));
		$url = $this->obj->htmlfile->url($filename);
		$data['info'] = $url;

		return TRUE;
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
		$subject = $this->lang->line('cancelacionpedidoproveedor-subject-email');
		$subject = str_replace('%id%', $id, $subject);
		return array(
			'perfil' 		=> array(PERFIL_RECLAMACIONES, PERFIL_PEDIDO),
			'emails'		=> $this->m_email,
			'faxes'			=> $this->m_telefono,
			'report_email' 	=> $this->config->item('sender.cancelacionpedidoproveedor'),
			'report_normal' => $this->_get_report_default(),
			'report_lang'	=> (isset($pd['proveedor']['cIdioma']) && trim($pd['proveedor']['cIdioma'])!='')?$pd['proveedor']['cIdioma']:(isset($pd['direccion'])?$pd['direccion']['cIdioma']:null),
			'subject'		=> $subject,
			'data'			=> $pd,
			'css'			=> $this->config->item('bp.documentos.css'),
			'id'			=> $pd['nIdProveedor']		
		);
	}

	/**
	 * Cancela un pedido entero
	 * @param int $id Id del pedido
	 * @return HTML_FILE
	 */
	function pedido($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			// Lee el pedido
			$this->load->model('compras/m_pedidoproveedor');
			$pedido = $this->m_pedidoproveedor->load($id, 'lineas');
			$ids = array();
			foreach($pedido['lineas'] as $linea)
			{
				if ($linea['nPendientes'] > 0) $ids[] = $linea['nIdLinea'];
			}
			$this->crear($ids);
		}
	}
	/**
	 * Crea una cancelación para envíar
	 * @param mixed $id int: Id de la línea, string: Ids separados por , array: Ids
	 * @return HTML_FILE
	 */
	function crear($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			// Modelos de datos
			$this->load->model('compras/m_pedidoproveedorlineaex');
			$this->load->model('proveedores/m_proveedor');
			$this->load->model('compras/m_pedidoproveedor');
			$this->load->model('proveedores/m_direccion');

			// Librerías
			$this->load->library('Messages');
			$this->load->library('Sender');

			// Id de las líneas a reclamar
			$ids = is_string($id)?preg_split('/\;/', $id):$id;

			// Agrupa los datos
			$pedidos = array();
			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					$linea = $this->m_pedidoproveedorlineaex->load($id);
					if ($linea['nPendientes'] > 0 &&
					(in_array($linea['nIdEstado'], array(LINEA_PEDIDO_PROVEEDOR_STATUS_PENDIENTE_DE_RECIBIR,
					LINEA_PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO))))
					{
						$pedidos[$linea['nIdProveedor']]['lineas'][] = array(
							'nIdLineaPedido'	=> $linea['nIdLinea'],
							'nCantidad'			=> $linea['nPendientes'],
							'nIdPedido'			=> $linea['nIdPedido'],
							'nIdLibro'			=> $linea['nIdLibro'],
							'cTitulo'			=> $linea['cTitulo']
						);
					}
				}
			}
			if (count($pedidos) == 0) $this->out->error($this->lang->line('no-hay-lineas-por-cancelar'));

			// Crea las reclamaciones
			foreach($pedidos as $k => $v)
			{
				unset($reclamacion);
				$dirs = $this->m_direccion->get_list($k);
				$direccion = $this->utils->get_profile($dirs, array(PERFIL_RECLAMACIONES, PERFIL_PEDIDO));
				if (isset($direccion['id'])) $reclamacion['nIdDireccion'] = $direccion['id'];
				$reclamacion['nIdProveedor'] = $k;
				$reclamacion['lineas'] = $v['lineas'];
				$id_r = $this->reg->insert($reclamacion);
				if ($id_r < 0)
				{
					$this->messages->error(sprintf($this->lang->line('cancelacion-error'), $k, $this->reg->error_message()));
				}
				else
				{
					$profile = $this->_get_profile_sender($id_r);
					$profile['controller'] = $this;

					$res = $this->sender->send($id_r, $profile, TRUE, TRUE);
					if ($res['success'])
					{
						$link_r = format_enlace_cmd($id_r, site_url('compras/cancelacion/index/' . $id_r));
						$message = sprintf($this->lang->line('sender-documento-enviado'), $link_r, $res['media'], $res['dest'], $this->lang->line($profile['report_email']). ' (' . $profile['report_lang'] . ')');
						$this->messages->info($message);
						$message2 = sprintf($this->lang->line('sender-documento-enviado'), $id_r, $res['media'], $res['dest'], $this->lang->line($profile['report_email']). ' (' . $profile['report_lang'] . ')');
						$this->_add_nota(null, $id_r, NOTA_INTERNA, $message2);
						
						foreach($v['lineas'] as $l)
						{
							// Cancela las líneas
							$this->m_pedidoproveedorlineaex->cancelar($l['nIdLineaPedido']);

							// Añade las notas a los pedidos de proveedor
							$link_l = format_enlace_cmd($l['nIdLibro'], site_url('catalogo/articulo/index/' . $l['nIdLibro']));
							$link_r = format_enlace_cmd($id_r, site_url('compras/cancelacion/index/' . $id_r));
							$message2 = sprintf($this->lang->line('cancelacion-titulo'), $l['nCantidad'], $link_l, $l['cTitulo'], $message);
							$this->messages->info($message2, 1);
							$this->_add_nota(null, $l['nIdPedido'], NOTA_INTERNA, $message2, $this->m_pedidoproveedor->get_tablename());
						}
					}
					else
					{
						$this->messages->error(sprintf($this->lang->line('sender-documento-enviado-error'), $id_r, $res['media'], $res['message']));
					}
				}
			}
			$body = $this->messages->out($this->lang->line('Cancelación pedidos proveedor'));
			$this->out->html_file($body, $this->lang->line('Cancelación pedidos proveedor'), 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
}

/* End of file cancelacion.php */
/* Location: ./system/application/controllers/compras/cancelacion.php */