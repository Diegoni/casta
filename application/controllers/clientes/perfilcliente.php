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
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Perfiles de un cliente
 *
 */
class PerfilCliente extends MY_Controller
{

	/**
	 * Configuración de los perfiles
	 * @var array
	 */
	protected $_config;

	/**
	 * Nombre del campo de Id de cruce de los registros
	 * @var string
	 */
	protected $_idref;

	/**
	 * Constructor
	 *
	 * @return PerfilCliente
	 */
	function __construct()
	{
		parent::__construct('clientes.perfilcliente', null, TRUE, null, null);

		$this->_idref = 'nIdCliente';

		$this->_config['E'] = array('clientes/M_email');
		$this->_config['C'] = array('clientes/M_contacto');
		$this->_config['T'] = array('clientes/M_telefono');
		$this->_config['D'] = array('clientes/M_direccioncliente');
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#get_list($start, $limit, $sort, $dir, $where)
	 */
	function get_list($id = null, $tipo = null, $long = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$tipo	= isset($tipo)?$tipo:$this->input->get_post('tipo');
		$long	= isset($long)?$long:$this->input->get_post('long');

		$data = null;
		if (!empty($id) && is_numeric($id))
		{
			$data = array();
			$btipo = (isset($tipo) && ($tipo != ''));
			foreach($this->_config as $k => $v)
			{
				if (($btipo && $k == $tipo) || !$btipo)
				{
					$this->load->model($v[0], $k);
					$d = $this->$k->get_list($id, $long);
					if (isset($d))
					{
						$data = array_merge($data, $d);
					}
				}
			}
		}
		$res = array(
			'success' 		=> TRUE,
			'value_data' 	=> $data
		);

		echo $this->out->send($res);
	}

	/**
	 * Obtiene los datos de un perfil
	 * @param int $id Id del perfil
	 * @param string $tipo Tipo de perfil
	 */
	function get($id = null, $tipo = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$tipo	= isset($tipo)?$tipo:$this->input->get_post('tipo');

		if (isset($id) && ($id != ''))
		{
			$v = $this->_config[$tipo];
			if (isset($v))
			{
				$this->load->model($v[0], 'r1');
				$data = $this->r1->load($id);
			}

			$res = array(
				'success' 		=> TRUE,
				'value_data' 	=> $data
			);
		}
		else
		{
			$res = array(
				'success' 		=> FALSE,
				'message' 		=> sprintf($this->lang->line('registro_no_encontrado'), $id)
			);
		}
		// Respuesta
		echo $this->out->send($res);
	}

	/**
	 * Añade un nuevo perfil
	 */
	function add()
	{
		$this->userauth->roleCheck(($this->auth .'.add'));

		$id_c		= isset($id_c)?$id_c:$this->input->get_post('id_c');
		$id			= isset($id)?$id:$this->input->get_post('id');
		$tipo		= isset($tipo)?$tipo:$this->input->get_post('tipo');

		$res = TRUE;
		if ($id_c && $tipo)
		{
			$v = $this->_config[$tipo];
			if (isset($v))
			{
				$upd = is_numeric($id);

				$this->load->model($v[0], 'r2');

				$this->db->trans_begin();

				if ($upd)
				{
					list($res, $id_new) = $this->_add_reg($this->r2, $id);
				}
				else
				{
					list($res, $id_new) = $this->_add_reg($this->r2, null, array($this->_idref => $id_c));
				}
			}
		}
		else
		{
			$res = sprintf($this->lang->line('mensaje_faltan_datos'));
		}

		if ($res === TRUE)
		{
			$this->db->trans_commit();
			$ajax_res = array(
				'success' 	=> true,
				'message'	=> sprintf($this->lang->line(($upd?'registro_actualizado':'registro_generado')), $id_new),
				'id'		=> (int) $id_new
			);
			$this->out->send($ajax_res);
		}
		else
		{
			$this->db->trans_rollback();

			$this->out->error($res);
		}


	}

	/**
	 * Añade un nuevo perfil (uso interno)
	 * @param string $reg Modelo de datos de cruce
	 * @param string $reg2 Modelo de datos final
	 * @param string $id_name Nombre del campo de enlace en la tabla final
	 * @param string $id Id del registro (si se actualiza)
	 * @param string $id_c Id del propietario del perfil (cliente, contacto, etc)
	 * @return JSON
	 */
	protected function _add_datos($reg, $reg2, $id_name, $id = null, $id_c = null)
	{
		$this->userauth->roleCheck(($this->auth .'.add'));

		$id	= isset($id)?$id:$this->input->get_post('id');
		$id_c	= isset($id_c)?$id_c:$this->input->get_post('id_c');

	}

	/**
	 * Elimina un perfil
	 * @param int $id Id del perfil
	 * @param string $tipo Tipo de perfil
	 */
	function del($id = null, $tipo = null)
	{
		$this->userauth->roleCheck(($this->auth .'.del'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$tipo	= isset($tipo)?$tipo:$this->input->get_post('tipo');

		$res = TRUE;
		if ($id && $tipo)
		{
			$v = $this->_config[$tipo];
			if (isset($v))
			{
				$this->load->model($v[0], 'r1');

				$this->db->trans_begin();
				if (!$this->r1->delete($id))
				{
					$res = $this->db->_error_message();
				}
			}
			else
			{
				$res = sprintf($this->lang->line('mensaje_faltan_datos'));
			}
		}
		else
		{
			$res = sprintf($this->lang->line('mensaje_faltan_datos'));
		}

		// Respuesta
		if ($res === TRUE)
		{
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('registro_eliminado'), $id));
		}
		else
		{
			$this->db->trans_rollback();
			$this->out->error($res);
		}
	}

	/**
	 * Genera el envío del paquete
	 * @param int $id Id de la dirección
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
		
		if (!empty($id))
		{
			$id = explode('_', $id);
			$idc = $id[0];
			$id = $id[1];
			#$this->out->success('OK COLEGA');
			$this->load->library('ASM');

			$this->load->model('clientes/m_direccioncliente');
			$this->load->model('clientes/m_cliente');
			$this->load->model('clientes/m_email');
			$this->load->model('clientes/m_telefono');

			$dir = $this->m_direccioncliente->load($id);

			$emails = $this->m_email->get_list($idc);
			$em = $this->utils->get_profile($emails, PERFIL_ENVIO);
			$tels = $this->m_telefono->get_list($idc);
			$tf = $this->utils->get_profile($tels, PERFIL_ENVIO);

			$cl = $this->m_cliente->load($idc);
			//$cliente = format_name($cl['cEmpresa'], $cl['cNombre'], $cl['cApellido']);
		
			$ref = $id . substr(time(), 7);
			
			$resultado = '';
			if (!$idetq = $this->asm->enviar($ref, $dir, $cl, $em['text'], $tf['text'], $dia, ($reembolso?$importe:null), $obs, $bultos, $resultado))
			{
				$this->out->error('<pre>' . $this->asm->get_error() . '</pre>');
			}

			//$this->reg->update($id, array('cIdShipping' => $idetq));
			
			$res = $this->asm->etiqueta($idetq);
			$this->load->library('HtmlFile');
			$url = $this->htmlfile->url($res);
			$text = format_enlace_cmd($idetq, site_url('sys/codebar/etiqueta/' . $idetq));

			$msg = ($reembolso)?sprintf($this->lang->line('pedidocliente-courier-reembolso-ok'), $bultos, format_price($importe), $text, $resultado):
				sprintf($this->lang->line('pedidocliente-courier-ok'), $bultos, $text, $resultado);

			$this->_add_nota(null, $idc, NOTA_INTERNA, $msg, $this->m_cliente->get_tablename());

			$this->out->url($url, $this->lang->line('Enviar por courier'), 'iconoCourierTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Unificador
	 * @param int $id1 Id de destino
	 * @param string $id2 Ids repetidos, separados por ;
	 * @return JSON
	 */
	function unificar_direccion($id1 = null, $id2 = null)
	{
		$this->userauth->roleCheck(($this->auth.'.unificar'));

		$id1	= isset($id1)?$id1:$this->input->get_post('id1');
		$id2	= isset($id2)?$id2:$this->input->get_post('id2');
		if ($id1 && $id2)
		{
			$ids = preg_split('/\;/', $id2);
			$t = '';
			$this->load->library('Logger');
			$this->load->model('clientes/m_direccioncliente');
			if (!$this->m_direccioncliente->unificar($id1, $ids))
			{
				$str = $this->m_direccioncliente->error_message();
				$this->out->error($str);
			}
			$this->logger->log('Dirección cliente unificada ' . implode(',', $ids) . ' con ' .$id1, 'unificador');
			$this->out->success($this->lang->line('direcciones-unificadas-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}
}

/* End of file perfilcliente.php */
/* Location: ./system/application/controllers/clientes/perfilcliente.php */