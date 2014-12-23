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
 * Envio de datos
 * @author alexl
 *
 */
class SmsServer {

	var $url;
	var $username;
	var $password;
	var $type;
	var $quality;
	var $from;

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return Out
	 */
	function __construct()
	{
		$this->obj =& get_instance();

		$this->url 		= $this->obj->config->item('bp.sms.url', null);
		$this->username = $this->obj->config->item('bp.sms.username', null);
		$this->password = $this->obj->config->item('bp.sms.password', null);
		$this->type 	= $this->obj->config->item('bp.sms.type', null);
		$this->quality 	= $this->obj->config->item('bp.sms.quality', null);
		$this->from 	= $this->obj->config->item('bp.sms.from', null);

		log_message('debug', 'Out Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Envía un SMS
	 * http://www.enviarmensajessms.es/pdf/envio_sms_http_post.pdf
	 * @param string $to Móvil destino
	 * @param string $msg Mensaje (utf8)
	 * @param string $id Id identificador del mensaje
	 * @param date $date Fecha de envío (opcional)
	 * @return bool
	 */
	function send($to, $msg, $id, $date = null)
	{
		$file = DIR_CONTRIB_PATH. 'SMStrendSDK' . DS . 'sendsms.php';
		require_once($file);

		$this->obj->load->helper('formatters');

		// Configuración

		if (!isset($this->url) || !isset($this->username) || !isset($this->password) || !isset($this->type) || !isset($this->quality) || !isset($this->from))
		{
			return $this->obj->lang->line('sms_no_configurado');
		}

		if (!$to || !$msg)
		{
			return $this->obj->lang->line('sms_faltan_datos');
		}

		$to = preg_replace('/[\s\.]/', '', $to);

		/*
		 $sms = new SMStrend_SMS($username, $password);
		 $sms->sms_type = $type;
		 //$sms->add_recipient('+393479057982');
		 $sms->add_recipient($to);
		 $sms->message = $msg;;
		 $sms->sender = $from;
		 $sms->set_immediate(); // or sms->set_scheduled_delivery($unix_timestamp)
		 $sms->order_id = $id;
		 if ($sms->validate())
		 {
			$res = $sms->send();
			if ($res['ok']) return TRUE;;
			}
			return $sms->problem();
			*/


		//Envia el mensaje
		$data = array(
			'login' 		=> $this->username,
			'password'		=> $this->password,
			'extid'			=> $id,
			'mobile'		=> $to,
			'messageQty' 	=> $this->quality,
			'messageType'	=> $this->type,
			'tpoa'			=> $this->from,
			'message'		=> string_encode($msg)
		);

		if (isset($date)) $data['dateforsend'] = date('d/m/Y H:i:s');

		list($header, $content) = $this->PostRequest($this->url, $this->obj->config->site_url(), $data);

		if (strpos($content,'OK') !== false) return true;

		return $content;
	}

	/**
	 * Comprueba el estado de un mensaje
	 * @param int $id Id del mensaje
	 * @return array 'ok' => bool, 'status' => mensaje de estado
	 */
	function status($id)
	{
		$file = DIR_CONTRIB_PATH. 'SMStrendSDK' . DS . 'sms_status.php';
		require_once($file);

		$status = smstrend_get_message_status($id, $this->username, $this->password);
		#echo '<pre>'; var_dump($status); echo '</pre>';
		if ($status['ok'])
		{
			for ($i=0;$i<$status['count'];$i++)
			{
				return array('ok' => TRUE, 'status' => $status[$i]);
			}
			return array('ok' => TRUE, 'status' => $status);
		}
		/*
		 * array(4) { ["ok"]=> bool(false) ["errcode"]=> int(1) ["errmsg"]=> string(28) "Invalid username or password" ["count"]=> int(0) }
		 */
		return array('ok' => FALSE, 'status' => $status);
	}

	/**
	 * Envía una petición POST a una dirección
	 * http://www.jonasjohn.de/snippets/php/post-request.htm
	 *
	 * @param string $url Dirección URI
	 * @param string $referer Refererer
	 * @param array $_data datos POST
	 * @return array
	 */
	protected function PostRequest($url, $referer, $_data)
	{
		// convert variables array to string:
		$data = array();
		while(list($n,$v) = each($_data))
		{
			$data[] = "$n=$v";
		}
		$data = implode('&', $data);
		// format --> test1=a&test2=b etc.

		// parse the given URL
		$url = parse_url($url);

		// extract host and path:
		$host = $url['host'];
		$path = $url['path'];
		$protocol = $url['scheme'];
		$protocol = isset($url['scheme'])?$url['scheme']:'http';
		$port = (isset($url['port'])?$url['port']:($protocol=='http'?80:443));

		$fp = fsockopen((($protocol == 'http')?'':"ssl://").$host, $port);

		// send the request headers:
		fputs($fp, "POST $path HTTP/1.1\r\n");
		fputs($fp, "Host: $host\r\n");
		fputs($fp, "Referer: $referer\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ". strlen($data) ."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $data);
		$result = '';
		$safe=0;
		while(!feof($fp)&&$safe<1000)
		{
			// receive the results of the request
			$result .= fgets($fp, 128);
			$safe++;
		}

		// close the socket connection:
		fclose($fp);

		// split the result header from the content
		$result = explode("\r\n\r\n", $result, 2);

		$header = isset($result[0]) ? $result[0] : '';
		$content = isset($result[1]) ? $result[1] : '';

		// return as array:
		return array($header, $content);
	}

}

/* End of file out.php */
/* Location: ./system/libraries/out.php */