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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Configuración del sistema/terminal/usuario
 *
 */
class Configuracion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Configuracion
	 */
	function __construct()
	{
		parent::__construct('sys.configuracion', 'sys/M_configuracion', TRUE, null, 'Configuración');
	}

	private function get_value($v, $type)
	{
		if ($type == 'bool') return ($v == 1 || $v == '1' || $v == 'true');
		if ($type == 'list' || $type == 'combo' || $type == 'int') return (int) $v;
		if ($type == 'float') return (float) $v;
		return $v;
	}

	/**
	 * Cambia la configuración del usuario
	 * @return MSG / FORM
	 */
	function user()
	{
		$set = get_post_names();
		unset($set['id']);
		unset($set['icon']);
		unset($set['title']);

		$this->userauth->check_login();

		$this->obj->load->library('Configurator');
		if (count($set) == 0)
		{
			# Variables
			$vars = $this->configurator->get_variables('user', TRUE);
			$user = $vars['vars'];
			$groups = $vars['groups'];
			# Valores
			$user2 = $this->configurator->user();
			# Asigna valores
			foreach ($user as $k => $v)
			{
				if (isset($user2[$k])) 
				{
					$user[$k]['value'] = $this->get_value($user2[$k], $user[$k]['type']);
				}
				$user[$k]['default'] = $this->configurator->user($k);
			}

			$data['title'] = $this->lang->line('Configuración usuario');
			$data['url'] = 'sys/configuracion/user';
			$data['items'] = $user;
			$data['groups'] = $groups;
			$data['terminal'] = FALSE;
			$data['system'] = FALSE;

			$this->_show_form('user', 'sys/configure2.js', $this->lang->line('Configuración usuario'), null, null, null, $data);
		}
		else
		{
			$values = get_post_all();
			foreach($values as $k => $v)
			{
				$k = str_replace('_', '.', $k);
				if ($k == $this->lang->line('valor-defecto'))
				{
					$this->obj->configurator->set_user($k);
				}
				else
				{
					$this->obj->configurator->set_user($k, (trim($v)=='')?null:($v=='true'?'1':(($v=='false')?'0':$v)));
				}					
			}
			$this->out->success($this->lang->line('app-config-user-set'));
		}
	}

	/**
	 * Cambia la configuración del terminal
	 * @return MSG / FORM
	 */
	function terminal()
	{
		$set = get_post_names();
		unset($set['id']);
		unset($set['icon']);
		unset($set['title']);

		$this->userauth->check_login();

		$this->obj->load->library('Configurator');
		if (count($set) == 0)
		{
			# Variables
			$vars = $this->configurator->get_variables('terminal', TRUE);
			$user = $vars['vars'];
			$groups = $vars['groups'];

			$data['title'] = $this->lang->line('Configuración Terminal');
			$data['url'] = 'sys/configuracion/user';
			$data['items'] = $user;
			$data['groups'] = $groups;
			$data['terminal'] = TRUE;
			$data['system'] = FALSE;

			$this->_show_form('user', 'sys/configure2.js', $this->lang->line('Configuración Terminal'), null, null, null, $data);
		}
		else
		{
			$values = get_post_all();
			foreach($values as $k => $v)
			{
				$k = str_replace('_', '.', $k);
				if ($k == $this->lang->line('valor-defecto'))
				{
					$this->obj->configurator->set_user($k);
				}
				else
				{
					$this->obj->configurator->set_user($k, (trim($v)=='')?null:($v=='true'?'1':(($v=='false')?'0':$v)));
				}					
			}
			$this->out->success($this->lang->line('app-config-user-set'));
		}
	}

	/**
	 * Muestra la configuración
	 * @return HTML_FILE
	 */
	function config()
	{
		$this->userauth->check_login();

		$this->load->library('Configurator');

		$user2 = $this->configurator->user();
		$user = $this->configurator->get_variables('user');
		#var_dump($user2, $user); die();
		foreach ($user as $k => $v)
		{
			$user[$k] = (isset($user2[$k]))?$this->get_value($user2[$k], $user[$k]['type']):null;
		}
		
		$system = $this->configurator->get_variables();
		foreach ($system as $k => $v)
		{
			$system[$k] = $system[$k]['value'];
		}

		$res = array(
			'success' 	=> TRUE,
			'user'		=> $user,
			'system'	=> $system,
		);

		$this->out->send($res);
	}

	/**
	 * Ventana de configuración de la aplicación
	 *
	 */
	function configure($type = null, $param = null)
	{
		$param = isset($param)?$param:$this->input->get_post('param');
		$user = ($type == 'user')?$param: null;
		$terminal = ($type == 'terminal')?$param:null;
		if (isset($user)&&($user!=''))
		{
			$this->userauth->roleCheck($this->auth .'.usuario');
		}
		elseif (isset($terminal)&&($terminal!=''))
		{
			$this->userauth->roleCheck($this->auth .'.terminal');
		}
		else
		{
			$this->userauth->check_login();
			$user = null;
		}
		$this->load->library('Configurator');
		$this->obj->load->library('Userauth');
		$datos['user_name'] = isset($user)?$user:$this->userauth->get_username();
		$datos['terminal_name'] = isset($terminal)?$terminal:$this->configurator->get_terminal_name();
		$datos['user'] = $this->configurator->user(null, $user);
		$datos['system'] = $this->configurator->system();
		$datos['terminal'] = $this->configurator->terminal(null, $terminal);
		$message = $this->load->view('sys/config', $datos, TRUE);
		$this->out->html_file($message, $this->lang->line('Configuración'), 'iconoConfiguracionTab');
	}

	/**
	 * Asigna una variable
	 * @param string $var Variable
	 * @param string $value Valor
	 * @param string $type Tipo
	 */
	function set($var = null, $value = null, $type = null, $param = null)
	{
		$var = isset($var)?$var:$this->input->get_post('var');
		$value = isset($value)?$value:$this->input->get_post('value');
		$type = isset($type)?$type:$this->input->get_post('type');
		$param = isset($param)?$param:$this->input->get_post('param');
		if ($value=='' || $value === FALSE) $value = null;
		if ($param == '') $param = null;
		if ($var)
		{
			$var = str_replace(' ', '.', $var);
			$this->load->library('Configurator');
			switch($type)
			{
				case 'system':
					$this->configurator->set_system($var, $value);
					break;
				case 'terminal':
					$this->configurator->set_terminal($var, $value, $param);
					break;
				case 'user':
				default:
					$this->configurator->set_user($var, $value, $param);
					break;
			}
			$this->out->success(sprintf($this->lang->line('app-config-set'), $var, $value));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

}

/* End of file configuracion.php */
/* Location: ./system/application/controllers/sys/configuracion.php */