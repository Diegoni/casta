<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	libraries
 * @category	core
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * PATH de las variables de sistema
 * @var string
 */
define('CONFIGURATOR_SYSTEM', 'SYSTEM');
define('CONFIGURATOR_CONFIG', DIR_CONFIG_PATH . 'config.xml');

/**
 * Configuración del usuario, del terminal, de la aplicación y del sistema
 * USER ->
 * 		TERMINAL ->
 * 			SISTEMA ->
 * 				APLICACION
 * @author alexl
 *
 */
class Configurator {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return Configurator
	 */
	function __construct()
	{
		$this->obj =& get_instance();

		log_message('debug', 'Configurator Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Añade una variable al sistema
	 * @param string $type Tipo
	 * @param string $field Campo
	 * @param string $value Valor
	 */
	protected function set($type, $field, $value = null)
	{
		$this->obj->load->model('sys/m_configuracion');
		$field = $type . '.' . $field;
		$this->obj->m_configuracion->delete_by(array('cEntrada' => $field));
		if (isset($value))
		{
			$data = array (
				'cEntrada'	=> $field,
				'tValor'	=> $value
			);
			#echo "$field = $value";
			return $this->obj->m_configuracion->insert($data);
		}
		return TRUE;
	}

	/**
	 * Obtiene una variable del sistema
	 * @param string $type Tipo
	 * @param string $field Campo. Si no se indica nada se devuelven todas
	 * @param string $value Valor
	 */
	protected function get($type, $field = null, $item = null)
	{
		$this->obj->load->model('sys/m_configuracion');
		if (!isset($field))
		{
			$entrada = $this->obj->db->escape($type . '.%' , TRUE);
			$data = $this->obj->m_configuracion->get(null, null, null, null, "cEntrada LIKE {$entrada}");
			if (count($data)>0)
			{
				$pos = strlen($type) + 1;
				$data2 = array();
				foreach($data as $v)
				{
					$k = substr($v['cEntrada'], $pos);
					$data2[$k] = $v['tValor'];
				}
				return $data2;
			}
			return null;
		}

		$entrada = $this->obj->db->escape($type . '.' . $field, TRUE);

		$data = $this->obj->m_configuracion->get(null, null, null, null, "cEntrada = {$entrada}");
		if (count($data) == 0)
		{
			return null;
		}
		return $data[0]['tValor'];
	}

	/**
	 * http://www.maheshchari.com/real-ip-address/
	 */
	function get_terminal_name()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif (isset($_SERVER['HTTP_X_REAL_IP']))
		{
			return $_SERVER['HTTP_X_REAL_IP'];
		}
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * Lee una variable de usuario
	 * @param string $item Campo
	 * @return string
	 */
	function user($item = null, $user = null, $recursive = TRUE)
	{
		$this->obj->load->library('Userauth');
		if (!isset($item))
		{
			return $this->get(isset($user)?$user:$this->obj->userauth->get_username());
		}
		$v = $this->get(isset($user)?$user:$this->obj->userauth->get_username(), $item, $item);
		if (!isset($v))	return ($recursive)?$this->terminal($item):null;
		return $v;
	}

	/**
	 * Añade una variable al sistema
	 * @param string $item Campo
	 * @param string $value Valor
	 */
	function set_user($item, $value = null, $user = null)
	{
		$this->obj->load->library('Userauth');
		$this->set(isset($user)?$user:$this->obj->userauth->get_username(), $item, $value);
	}

	/**
	 * Lee una variable de terminal
	 * @param string $item Campo
	 * @return string
	 */
	function terminal($item = null, $terminal = null, $recursive = TRUE)
	{
		if (!isset($item))
		{
			return $this->get(isset($terminal)?$terminal:$this->get_terminal_name());
		}
		$v = $this->get(isset($terminal)?$terminal:$this->get_terminal_name(), $item, $item);
				if (!isset($v))	return ($recursive)?$this->system($item):null;
		
		return $v;
	}

	/**
	 * Añade una variable al sistema
	 * @param string $item Campo
	 * @param string $value Valor
	 */
	function set_terminal($item, $value = null, $terminal = null)
	{
		$this->set(isset($terminal)?$terminal:$this->get_terminal_name(), $item, $value);
	}

	/**
	 * Lee una variable de sistema
	 * @param string $item Campo
	 * @return string
	 */
	function system($item = null, $recursive = TRUE)
	{
		if (!isset($item))
		{
			return $this->get(CONFIGURATOR_SYSTEM);
		}
		$v = $this->get(CONFIGURATOR_SYSTEM, $item, $item);
		if (!isset($v))	return ($recursive)?$this->application($item):null;
		return $v;
	}

	/**
	 * Añade una variable al sistema
	 * @param string $item Campo
	 * @param string $value Valor
	 */
	function set_system($item, $value = null)
	{
		$this->set(CONFIGURATOR_SYSTEM, $item, $value);
	}

	/**
	 * Lee una variable de aplicación
	 * @param string $item Campo
	 * @return string
	 */
	function application($item)
	{
		return $this->obj->config->item($item);
	}

	/**
	 * Devuelve las variables del tipo indicado
	 * @param string $profile app, user, terminal
	 * @param bool $agrupar TRUE: Devuelve los grupos de variables, FALSE: Solo las variables
	 * @return array. Si $agrupar = TRUE, array('vars', 'groups'), sino array con las variables
	 */
	function get_variables($profile = 'system', $agrupar = FALSE)
	{
		$xml = new SimpleXMLElement(CONFIGURATOR_CONFIG, null, TRUE);
		$vars = null;
		$grupos = null;
		if (isset($xml->group)) 
		{
			foreach($xml->group as $group)
			{
				foreach ($group->var as $var)
				{
					if (($profile == 'system') || (isset($var->profile) && strpos($var->profile, $profile) !== FALSE))
					{
						$vars[(string)$var->name] = array(
								'value' 	=> $this->obj->config->item((string)$var->name), 
								'type' 		=> (string) $var->type,
								'values' 	=> 	isset($var->values)?((string)$var->values):null,
							);
						if ($agrupar) 
						{
							$grupos[(string) $group->name]['vars'][] = (string)$var->name;
							$grupos[(string) $group->name]['icon'] = (string)$group->icon;
						}
					}
				}
			}
		}
		if ($agrupar) 
			return array('vars' => $vars, 'groups' => $grupos);
		return $vars;
	}
}

/* End of file Configurator.php */
/* Location: ./system/libraries/configurator.php */