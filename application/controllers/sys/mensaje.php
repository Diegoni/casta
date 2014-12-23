<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Mensajes del sistema
 *
 */
class Mensaje extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Mensaje
	 */
	function __construct()
	{
		parent::__construct('sys.mensaje', 'sys/m_mensaje', TRUE, 'sys/chat.js', 'Mensajes');
	}

	/*function index()
	{
		$this->userauth->roleCheck(($this->auth . '.index'));

		#$year 	= isset($year)?$year:$this->input->get_post('year');
		#$id		= isset($id)?$id:$this->input->get_post('id');

		#if ($id && $year)
		#{
		$this->obj->load->library('Userauth');
		$username = $this->obj->userauth->get_username();
		$username = $this->db->escape((string)$username);
			
		$data['mensajes'] = $this->reg->get(0, $this->config->item('bp.mensajes.limit'), 'dCreacion', 'DESC', "cDestino={$username}");
		$data['username'] = $this->obj->userauth->get_username();
		$message = $this->load->view('sys/mensajes', $data, TRUE);
		// Respuesta
		$this->out->html_file($message, $this->lang->line('Mensajes'), 'iconoMensajesTab');
		}
		 else
		 {
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
			}
	}*/

	private function _check_group($idg, $idu, $msg)
	{
		$this->load->model('sys/m_mensajegrupousuario');
		$esta = $this->m_mensajegrupousuario->get(null, null, null, null, "nIdGrupo={$idg} AND nIdUsuario={$idu}");
		if (count($esta) == 0 && $msg != 'delete')
		{
			$this->m_mensajegrupousuario->insert(array('nIdGrupo' => $idg, 'nIdUsuario' => $idu));
			$this->load->library('Mensajes');
			$this->mensajes->grupo($idg, sprintf($this->lang->line('mensaje-add-grupo'), htmlentities($this->obj->userauth->get_name())));
		}
		else
		{
			if (count($esta) > 0 && $msg == 'delete')
			{
				$esta = $this->m_mensajegrupousuario->delete($esta[0]['nIdMensajeGrupoUsuario']);
				$this->load->library('Mensajes');
				$this->mensajes->grupo($idg, sprintf($this->lang->line('mensaje-del-grupo'), htmlentities($this->obj->userauth->get_name())));
			}
		}
	}

	/**
	 * Comunicación del chat
	 * @param int $last_id Último mensaje leído
	 * @param string $to Nombre del usario al que se envía el mensaje o Id de grupo
	 * @param string $msg Mensaje a enviar
	 * @param int $idu Id del usuario con el que se abre un chat privado
	 * @param int $idg Id del grupo con el que se abre un chat privado
	 * @param bool $group TRUE: El $to es un Id de grupo, FALSE: El $to es un nombre de usuario
	 * @return array 'messages' => Los mensajes,
	 * 		'last_id'	=> El último Id del mensaje,
	 * 		'users'		=> Los usuarios,
	 * 		'grupos'	=> Los grupos
	 */
	function chat($last_id = null, $to = null, $msg = null, $idu = null, $idg = null, $first_id = null)
	{
		$this->userauth->roleCheck(($this->auth . '.get_list'));
		$last_id = isset($last_id)?$last_id:$this->input->get_post('last_id');
		$msg = isset($msg)?$msg:$this->input->get_post('msg');
		$to = isset($to)?$to:$this->input->get_post('to');
		$idu = isset($idu)?$idu:$this->input->get_post('idu');
		$idg = isset($idg)?$idg:$this->input->get_post('idg');
		$group = isset($group)?$group:$this->input->get_post('group');
		$first_id = isset($first_id)?$first_id:$this->input->get_post('first_id');

		#Lee los mensajes a partir de una ID
		$new_last_id = $last_id;
		$this->obj->load->library('Userauth');
		$username = $this->obj->userauth->get_username();
		$id = $this->obj->userauth->get_id();
		$username = $this->db->escape((string)$username);

		# Si está en un grupo se añade
		$group = format_tobool($group);
		$group_checked = FALSE;
		if ($group) 
		{
			$this->_check_group($to, $id, $msg);
			$group_checked = TRUE;
		}

		# Si se ha indicado mensaje, lo envía
		if (!empty($msg) && $msg != 'delete')
		{
			//$msg = htmlentities($msg);
			#$msg = utf8_encode($msg);
			$this->load->library('Mensajes');
			//var_dump($group); die();
			if ($group)
			{
				$this->mensajes->grupo($to, $msg);
			}		
			else {
				(empty($to))?$this->mensajes->todos($msg):$this->mensajes->usuario($to, $msg);
			}
		}

		$filter = "(cDestino={$username} OR (cDestino IS NULL AND nIdGrupo IS NULL) OR cOrigen={$username}
		OR (nIdGrupo IS NOT NULL AND nIdGrupo IN (SELECT nIdGrupo FROM Ext_MensajesGruposUsuarios WHERE nIdUsuario={$id})))";
		if ($last_id > 0)
			$filter .= " AND nIdMensaje > {$last_id}";

		$mensajes = $this->reg->get(0, $this->config->item('bp.mensajes.limit'), 'nIdMensaje', 'DESC', $filter);
		# Si se ha indicado un ID de usuario, busca los últimos mensajes enviados/recibidos
		if (!empty($idu))
		{
			$idu = $this->db->escape((string)$idu);
			$filter = "((cDestino={$idu} AND cOrigen={$username}) OR (cOrigen={$idu} AND cDestino={$username}))";
			$mensajes2 = $this->reg->get(0, $this->config->item('bp.mensajes.limit'), 'nIdMensaje', 'DESC', $filter);
			$mensajes = array_merge($mensajes, $mensajes2);
		}

		# Si se ha indicado un ID de grupo, busca los últimos mensajes enviados/recibidos
		if (!empty($idg))
		{
			if (!$group_checked)
				$this->_check_group($idg, $id, $msg);
			$filter = "nIdGrupo={$idg}";
			$mensajes2 = $this->reg->get(0, $this->config->item('bp.mensajes.limit'), 'nIdMensaje', 'DESC', $filter);
			$mensajes = array_merge($mensajes, $mensajes2);
		}
		sksort($mensajes, 'nIdMensaje', TRUE);

		if ($first_id > 0)
		{
			$filter = "nIdMensaje < {$first_id}";
			$mensajes2 = $this->reg->get(0, $this->config->item('bp.mensajes.limit'), 'nIdMensaje', 'DESC', $filter);
			sksort($mensajes2, 'nIdMensaje', FALSE);
			#var_dump($mensajes2); die();
			$mensajes = array_merge($mensajes, $mensajes2);
		}

		#var_dump($filter, $mensajes); die();
		$messages = array();
		$i = count($mensajes) - 1;
		$ids = array();
		$vistos = array();
		while($i >= 0)
		{
			$value = $mensajes[$i];
			if (!in_array($value['nIdMensaje'], $ids))
			{
				$messages[] = array(
					'message'	=> $value['tMensaje'],
					'origen' 	=> $value['cOrigen'],
					'destino' 	=> $value['cDestino'],
					'id'		=> $value['nIdMensaje'],
					'time'		=> $value['dCreacion'],
					'grupo'		=> $value['nIdGrupo'],
					'gruponombre' => $value['cGrupo'],
					'origennombre' => $value['cOrigenNombre'],
					'destinonombre' => $value['cDestinoNombre'],
					'new' 		=> ($value['bVisto'] != TRUE)
					);
				if ($value['bVisto'] != TRUE && ($value['cDestino']==$username)) 
					$vistos[] = $value['nIdMensaje'];
				$new_last_id = max($new_last_id, $value['nIdMensaje']);
				$ids[] = $value['nIdMensaje'];
			}
			--$i;
		}

		# Marca como vistos los nuevos
		if (count($vistos) > 0) $this->reg->vistos($vistos);

		#Usuarios
		$users = array();
		$cache_id = 'chat.users';
		if (!($users = $this->cache->fetch($this->reg->get_tablename(), $cache_id, CACHE_MEMORY)))
		{
			#echo 'users';
			$this->load->model('user/m_usuario');
			$list = $this->m_usuario->get(null, null, 'cUsername', 'ASC', 'bEnabled=1');
			foreach ($list as $key => $value) 
			{
				$users[] = array('user' => $value['cUsername'], 'name' => $value['cNombre']);
			}
		}

		#Grupos
		$grupos = array();
		$cache_id = 'chat.grupos';
		$this->load->model('sys/m_mensajegrupo');
		if (!($grupos = $this->cache->fetch($this->m_mensajegrupo->get_tablename(), $cache_id, CACHE_MEMORY)))
		{
			$list = $this->m_mensajegrupo->get(null, null, 'cDescripcion', 'ASC');
			foreach ($list as $key => $value) 
			{
				$grupos[] = array('id' => $value['nIdGrupo'], 'name' => $value['cDescripcion']);
			}
		}

		$res = array(
			'success' 	=> TRUE,
			'last_id'	=> $new_last_id,
			'messages' 	=> $messages,
			'users'		=> $users,
			'grupos'	=> $grupos,
		);

		$this->out->send($res);
	}

	/**
	 * Marca un mensaje como visto
	 * @param int $id Id del mensaje
	 * @return JSON
	 */
	function visto( $id = null )
	{
		$this->userauth->roleCheck(($this->auth . '.visto'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$this->load->library('Mensajes');
			$this->mensajes->visto($id);
			$this->out->success();
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}

	/**
	 * Envia un mensaje
	 * @param string $usuario
	 * @param string $msg mensaje
	 * @return JSON
	 */
	function send($usuario = null, $msg = null)
	{
		$this->userauth->roleCheck($this->auth . '.send');
		$usuario = isset($usuario)?$usuario:$this->input->get_post('usuario');
		$msg = isset($msg)?$msg:$this->input->get_post('msg');
		if ($usuario && $msg)
		{
			$this->load->library('Mensajes');
			$this->mensajes->usuario($usuario, $msg);
			$this->out->success($this->lang->line('mensaje_enviado'));
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}
}

/* End of file mensaje.php */
/* Location: ./system/application/controllers/sys/mensaje.php */