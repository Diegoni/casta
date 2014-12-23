<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
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
define('WEBSHOPAPI_DEFAULT_TIMEOUT', 30);
/**
 * Conexión a la Web
 * @author alexl
 *
 */
class Webshop
{
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;
	/**
	 * Último error
	 * @var string
	 */
	private $_error;

	/**
	 * URL de la API de la página Web
	 * @var string
	 */
	private $host;

	/**
	 * Token de autentificación
	 * @var string
	 */
	private $token;

	/**
	 * COOKIE de las llamadas
	 * @var string
	 */
	private $cookie;
	/**
	 * Códigos HTTP de respuesta del servidor
	 *
	 * @link
	 * http://www.askapache.com/htaccess/apache-status-code-headers-errordocument.html
	 * @var array
	 */
	public $debug = FALSE;

	var $http_codes = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',

			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',

			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',

			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			426 => 'Upgrade Required',

			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			510 => 'Not Extended'
	);

	/**
	 * Constructor
	 * @return Sphinx
	 */
	function __construct()
	{
		$this->obj = &get_instance();

		log_message('debug', 'Webshop Class Initialised via ' . get_class($this->obj));
	}

	/**
	 * Devuelve la URL de la llamada
	 * @param string $cmd Comando
	 * @return string
	 */
	function get_url($cmd)
	{
		return $this->host . '/index.php?route=' . $cmd;
	}

	/**
	 * Función interna para construir la llamada al Bibliopola
	 * @param string $cmd Nombre del procedimiento
	 * @param array $post Parámetros pasados al procedimiento
	 * @return string, JSON del resultado de la llamada
	 */
	private function call($cmd, $post = null)
	{
		$url = $this->get_url($cmd);
		if ($this->token)
		{			
			$url .= (strpos($url, '?')===FALSE?'?':'&') .'token=' . $this->token;
		}
		if ($this->debug) { var_dump($url); var_dump($post); }
		$curly = curl_init();


		#var_dump(file_get_contents(DIR_TEMP_PATH . "cookie.txt")); die(); 
		curl_setopt($curly, CURLOPT_URL, $url);
	    #curl_setopt($curly, CURLOPT_COOKIEJAR, DIR_TEMP_PATH . "cookie.txt");
    	curl_setopt($curly, CURLOPT_COOKIEFILE, DIR_TEMP_PATH . "cookie.txt");
		curl_setopt($curly, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curly, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curly, CURLOPT_TIMEOUT, WEBSHOPAPI_DEFAULT_TIMEOUT);

		// post?
		if (!empty($post))
		{
			$post_string = '';
			foreach ($post as $k => $v)
			{
				$post_string .= $k . '=' . urlencode($v) . '&';
			}
			curl_setopt($curly, CURLOPT_POST, 1);
			curl_setopt($curly, CURLOPT_POSTFIELDS, $post_string);
		}

		$res = curl_exec($curly);
		$info = curl_getinfo($curly);
		if ($this->debug) { var_dump($res); var_dump($info); }
		$code = (string)$info['http_code'];
		if ($code[0] == 4 || $code[0] == 5)
		{
			$this->set_error($this->http_codes[$code] . "\n" . $res);
			return FALSE;
		}

		return array(
				'info' => $info,
				'res' => $res
		);
	}

	/**
	 * Se logea en la página web
	 * @param string $server Dirección de la Web
	 * @param string $username Usuario
	 * @param string $password Contraseña
	 * @return bool, TRUE: Se ha identificado correctamente
	 */
	function login($server = null, $username = null, $password = null)
	{
		if (!isset($server)) $server = $this->obj->config->item('bp.webshop.server');
		if (!isset($username)) $username = $this->obj->config->item('bp.webshop.username');
		if (!isset($pasword)) $pasword = $this->obj->config->item('bp.webshop.password');

		$this->host = $server;
		#var_dump($server); die();
		return TRUE;
		$url = $this->get_url('common/login');
		#var_dump($url);
		
		$fp = fopen("cookie.txt", "w");
        fclose($fp);
		
		$curly = curl_init();

		curl_setopt($curly, CURLOPT_URL, $url);
	    curl_setopt($curly, CURLOPT_COOKIEJAR, DIR_TEMP_PATH . "cookie.txt");
    	#curl_setopt($curly, CURLOPT_COOKIEFILE, DIR_TEMP_PATH . "cookie.txt");
    	curl_setopt($curly, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($curly, CURLOPT_HEADER, 1);
		curl_setopt($curly, CURLOPT_NOBODY, 1);
		curl_setopt($curly, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curly, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curly, CURLOPT_TIMEOUT, WEBSHOPAPI_DEFAULT_TIMEOUT);

		// post?
		$post = array(
				'username' => $username,
				'password' => $password
		);
		$post_string = '';
		foreach ($post as $k => $v)
		{
			$post_string .= $k . '=' . urlencode($v) . '&';
		}
		curl_setopt($curly, CURLOPT_POST, 1);
		curl_setopt($curly, CURLOPT_POSTFIELDS, $post_string);

		$res = curl_exec($curly);
		$info = curl_getinfo($curly);
		$code = (string)$info['http_code'];
		if ($code[0] == 4 || $code[0] == 5)
		{
			$this->set_error($this->http_codes[$code] . "\n" . $res);
			return FALSE;
		}

		$res = array(
				'info' => $info,
				'res' => $res
		);

		if (!isset($res['info']['url']))
			return FALSE;
		$url = $res['info']['url'];
		$params = preg_split('/\?/', $url);
		if (!isset($params[1]))
			return FALSE;
		$params = preg_split('/\&/', $params[1]);
		$token = null;
		foreach ($params as $p)
		{
			$p = preg_split('/\=/', $p);
			if ($p[0] == 'token')
				$token = $p[1];
		}
		if (!isset($token))
			return FALSE;

		#var_dump($res); die();
	#var_dump($token); 
	#var_dump(file_get_contents(DIR_TEMP_PATH . "cookie.txt")); die();
		$this->token = $token;
		return TRUE;
	}

	/**
	 * Llama a un procedimiento de Bibliopola
	 * @param string $action Nombre del procedimiento
	 * @param array $params Parámetros pasados al procedimiento
	 * @return array: resultado de la llamada
	 */
	function action($action, $params = null)
	{
		/*if (!isset($this->token))
			return FALSE;*/

		$res = $this->call($action, $params);
		if ($res === FALSE)
			return FALSE;
		#var_dump($res);
		$res = json_decode($res['res'], TRUE);

		if (!$res['success'])
			$this->set_error($res['message']);
		return $res;
	}

	/**
	 * Función interna para asignar un error
	 * @param string $msg Error
	 */
	private function set_error($msg = null)
	{
		$this->_error = $msg;
	}

	/**
	 * Devuelve el último error
	 */
	function get_error()
	{
		return $this->_error;
	}

}

/* End of file Sphinx.php */
/* Location: ./system/libraries/sphinx.php */
