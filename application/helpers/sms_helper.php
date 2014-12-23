<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Helpers
 * @category	Heleprs
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

require('../sms_status.php');


if ( ! function_exists('sms_send'))
{
	/**
	 * Envía un SMS
	 * http://www.enviarmensajessms.es/pdf/envio_sms_http_post.pdf
	 * @param string $to Móvil destino
	 * @param string $msg Mensaje (utf8)
	 * @param string $id Id identificador del mensaje
	 * @param date $date Fecha de envío (opcional)
	 * @return bool
	 */
	function sms_send($to, $msg, $id, $date = null)
	{
		//parámetros
		$obj =& get_instance();
		
		$obj->load->helper('formatters');
		
		// Configuración
		$url 		= $obj->config->item('bp.sms.url', null);
		$username 	= $obj->config->item('bp.sms.username', null);
		$password 	= $obj->config->item('bp.sms.password', null);
		$type 		= $obj->config->item('bp.sms.type', null);
		$quality 	= $obj->config->item('bp.sms.quality', null);
		$from 		= $obj->config->item('bp.sms.from', null);

		if (!isset($url) || !isset($username) || !isset($password) || !isset($type) || !isset($quality) || !isset($from))
		{
			return $obj->lang->line('sms_no_configurado');
		}
		
		if (!$to || !$msg)
		{
			return $obj->lang->line('sms_faltan_datos');
		}

		$to = preg_replace('/[\s\.]/', '', $to);
		//Envia el mensaje
		$data = array(
			'login' 		=> $username,
			'password'		=> $password,
			'extid'			=> $id,
			'mobile'		=> $to,
			'messageQty' 	=> $quality,
			'messageType'	=> $type,
			'tpoa'			=> $from,
			'message'		=> string_encode($msg)
		);
		
		if (isset($date)) $data['dateforsend'] = date('d/m/Y H:i:s');

		list($header, $content) = PostRequest($url, $obj->config->site_url(), $data);

		if (strpos($content,'OK') !== false) return true;

		return $content;
	}
}

if ( ! function_exists('PostRequest'))
{
	/**
	 * Envía una petición POST a una dirección
	 * http://www.jonasjohn.de/snippets/php/post-request.htm
	 *
	 * @param string $url Dirección URI
	 * @param string $referer Refererer
	 * @param array $_data datos POST
	 * @return array
	 */
	function PostRequest($url, $referer, $_data)
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
/* End of file sms_helper.php */
/* Location: ./system/application/helpers/sms_helper.php */