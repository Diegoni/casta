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
 * @todo  Documentar correctamente los parámetros y crear documentación Markdown de uso
 */

/**
 * Envio de datos
 * @author alexl
 *
 */
class Out {
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Formato de salida
	 * @var string
	 */
	private $format;

	/**
	 * Constructor
	 * @return Out
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		$format = $this->obj->input->get_post('format');
		$this->format = (isset($format) && ($format != ''))?$format:$this->obj->config->item('bp.data.format');

		log_message('debug', 'Out Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Especifica formato de salida
	 * @param string $format JSON, XML, HTML
	 */
	function set_format($format)
	{
		$this->format = $format;
	}

	/**
	 * Obtiene el formato de salida actual
	 * @return JSON, XML, HTML
	 */
	function get_format()
	{
		return $this->format;
	}

	/**
	 * Escribe un nodo XML
	 * @param XMLWriter $xml
	 * @param mixed $data
	 */
	protected function _write(XMLWriter $xml, $data)
	{
		foreach($data as $key => $value)
		{
			if(is_array($value))
			{
				if (!isset($key)||($key=='')||(is_numeric($key))) $key = 'data';
				$xml->startElement($key);
				$this->_write($xml, $value);
				$xml->endElement();
				continue;
			}
			$xml->writeElement($key, $value);
		}
	}

	/* The main function for converting to an XML document.
	 * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
	 *
	 * http://snipplr.com/view/3491/convert-php-array-to-xml-or-simple-xml-object-if-you-wish/
	 * @param array $data
	 * @param string $rootNodeName - what you want the root node to be - defaultsto data.
	 * @param SimpleXMLElement $xml - should only be used recursively
	 * @return string XML
	 */
	function toXML($data, $rootNodeName = 'data', $xml=null)
	{
		// turn off compatibility mode as simple xml throws a wobbly if you don't.
		if (ini_get('zend.ze1_compatibility_mode') == 1)
		{
			ini_set ('zend.ze1_compatibility_mode', 0);
		}

		if ($xml == null)
		{
			$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
		}

		// loop through the data passed in.
		foreach($data as $key => $value)
		{
			// no numeric keys in our xml please!
			if (is_numeric($key))
			{
				// make string key...
				$key = "unknownNode_". (string) $key;
			}

			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z]/i', '', $key);

			// if there is another array found recrusively call this function
			if (is_array($value))
			{
				$node = $xml->addChild($key);
				// recrusive call.
				$this->toXML($value, $rootNodeName, $node);
			}
			else
			{
				// add single node.
				$value = htmlentities($value);
				$xml->addChild($key,$value);
			}

		}
		// pass back as string. or simple xml object if you want!
		return $xml->asXML();
	}

	/**
	 * Conversor de array a HTML
	 * @param array $data
	 * @return string
	 */
	protected function _array_to_html($data)
	{
		return $this->obj->utils->array_to_html($data);
		
		$text = '<table style="border: 1px solid black; text-align: left">' . "\n";
		foreach ($data as $field => $value)
		{
			$text .= "<tr>\n";
			$text .= "<th>{$field}</th>\n";
			$text .= "<td>\n";
			$text .= ((is_array($value))?$this->_array_to_html($value):$value) . "\n";
			$text .= "</td>\n";
			$text .= "</tr>\n";
		}
		$text .= "</table>\n";

		return $text;
	}

	/**
	 * Convierte un array a HTML
	 *
	 * @param array $data Datos
	 * @return string Fuente HTML
	 */
	function toHTML($data)
	{

		$text = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			</head>
		<body>';
		$text .= $this->_array_to_html($data);//'<pre>' . print_r($data, TRUE) .'</pre>';
		$text .= '</body></html>';
		return $text;
	}

	/**
	 * Convierte un array a XML
	 *
	 * @param array $data Datos
	 * @param bool $headers TRUE: devuelve una página al cliente, FALSE: devuelve el texto en la función
	 * @return string Fuente XML
	 */
	protected function _to_xml($data, $headers)
	{
		/*$xml = new XmlWriter();
		 $xml->openMemory();
		 $xml->startDocument('1.0', 'UTF-8');
		 $xml->startElement('root');

		 $this->_write($xml, $data);

		 $xml->endElement();
		 $text = $xml->outputMemory(true);;*/
		$text = $this->toXml($data);
		if ($headers)
		{
			header("Content-length: ". strlen($text));
			header('Content-type: text/xml');
			echo $text;
			exit;
		}
		return $text;
	}

	/**
	 * Convierte un array a HTML
	 *
	 * @param array $data Datos
	 * @param bool $headers TRUE: devuelve una página al cliente, FALSE: devuelve el texto en la función
	 * @return string Fuente HTML
	 */
	protected function _to_html($data, $headers)
	{

		$text = $this->toHTML($data);
		if ($headers)
		{
			header("Content-length: ". strlen($text));
			header('Content-type: text/html');
			echo $text;
			exit;
		}

		return $text;
	}

	/**
	 * Convierte un array a PHP serializado
	 *
	 * @param array $data Datos
	 * @param bool $headers TRUE: devuelve una página al cliente, FALSE: devuelve el texto en la función
	 * @return string Fuente PHPS
	 */
	protected function _to_phps($data, $headers)
	{
		$text = serialize($data);
		if ($headers)
		{
			header("Content-length: ". strlen($text));
			header('Content-type: text/plain');
			echo $text;
			exit;
		}

		return $text;
	}

	/**
	 * Formatea los datos en un formato de salida
	 *
	 * @param mixed $data Datos
	 * @param string $format Formato (JSON, XML)
	 * @return string Datos formateados
	 */
	function send($data, $headers = TRUE)
	{
		header('Access-Control-Allow-Origin: *');
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado

		switch (strtoupper($this->format))
		{
			case 'JSON':
				return $this->obj->jsonci->sendJSON($data, $headers);
				break;
			case 'XML':
				return $this->_to_xml($data, $headers);
				break;
			case 'HTML':
				return $this->_to_html($data, $headers);
				break;
			case 'PHPS':
				return $this->_to_phps($data, $headers);
				break;
			default:
				return $this->obj->jsonci->sendJSON($data, $headers);
				break;
		}
	}

	/**
	 * Envía un texto HTML al cliente en formato JSON reconido por el sistema
	 * @param string $html Código HTML
	 * @param string $format formato de salida (JSON, XML, etc)
	 * @return JSON
	 */
	function html($html, $title = null, $icon = null, $complete = FALSE, $headers = TRUE)
	{
		$this->obj->load->helper('asset');
		if (!$complete)	$html = $this->obj->load->view('main/html', array('html' => $html), TRUE);
		$data = array(
			'success' 	=> TRUE,
			'title'		=> $title,
			'icon'		=> $icon,
			'html'		=> $html
		);
		$data['title'] =(isset($title))?$title:$this->obj->config->item('bp.application.name');
		if (isset($icon)) $data['icon'] = $icon;

		return $this->send($data, $headers);
	}

	/**
	 * Envía un texto fichero HTML al cliente en formato JSON reconido por el sistema
	 * @param string $html Código HTML
	 * @param string $format formato de salida (JSON, XML, etc)
	 * @return JSON
	 */
	function html_file($html, $title = null, $icon = null, $css = null, $complete = FALSE, $headers = TRUE)
	{
		$this->obj->load->library('HtmlFile');

		$filename = $this->obj->htmlfile->create($html, $title, $css, $complete);
		$url = $this->obj->htmlfile->url($filename);
		$data = array(
			'success' 	=> TRUE,
			'title'		=> $title,
			'icon'		=> $icon,
			'html_file'	=> $url
		);
		if (isset($title)) $data['title'] = $title;
		if (isset($icon)) $data['icon'] = $icon;

		return $this->send($data, $headers);
	}

	/**
	 * Envía un Window ExtJS al cliente en formato JSON reconido por el sistema
	 * @param string $win Código ExtJS
	 * @param string $format formato de salida (JSON, XML, etc)
	 * @return JSON
	 */
	function window($win, $title = null, $icon = null, $id = null, $headers = TRUE)
	{
		if (!$this->obj->config->item('js.debug'))
		{
			$this->obj->load->plugin('jsmin');
			$win = JSMin::minify($win);
		}

		$data = array(
			'success' 	=> TRUE,
			'win'		=> $win
		);
		if (isset($title)) $data['title'] = $title;
		if (isset($icon)) $data['icon'] = $icon;
		if (isset($id)) $data['id'] = $id;

		return $this->send($data, $headers);
	}

	/**
	 * Envía una URL al cliente en formato JSON reconido por el sistema
	 * @param string $win Código ExtJS
	 * @param string $format formato de salida (JSON, XML, etc)
	 * @return JSON
	 */
	function url($url, $title = null, $icon = null, $id = null, $headers = TRUE)
	{
		$data = array(
			'success' 	=> TRUE,
			'url'		=> $url
		);
		if (isset($title)) $data['title'] = $title;
		if (isset($icon)) $data['icon'] = $icon;
		if (isset($id)) $data['id'] = $id;

		return $this->send($data, $headers);
	}

	/**
	 * Envía una orden de redirección al cliente en formato JSON reconido por el sistema
	 * @param string $win Código ExtJS
	 * @param string $format formato de salida (JSON, XML, etc)
	 * @return JSON
	 */
	function redirect($url, $title = null, $icon = null, $id = null, $headers = TRUE)
	{
		$data = array(
			'success' 	=> TRUE,
			'redirect'		=> $url
		);

		return $this->send($data, $headers);
	}

	/**
	 * Envía una orden de redirección al cliente en formato JSON reconido por el sistema
	 * @param string $win Código ExtJS
	 * @param string $format formato de salida (JSON, XML, etc)
	 * @return JSON
	 */
	function download($url, $title = null, $icon = null, $id = null, $headers = TRUE)
	{
		$data = array(
			'success' 	=> TRUE,
			'download'	=> $url
		);

		return $this->send($data, $headers);
	}

	/**
	 * Envía un código JS al cliente en formato JSON reconido por el sistema
	 * @param string $html Código JS
	 * @param string $format formato de salida (JSON, XML, etc)
	 * @return JSON
	 */
	function js($js, $headers = TRUE)
	{
		if (!$this->obj->config->item('js.debug'))
		{
			$this->obj->load->plugin('jsmin');
			$js = JSMin::minify($js);
		}
		$data = array(
			'success' 	=> TRUE,
			'js'		=> $js
		);
		return $this->send($data, $headers);
	}

	/**
	 * Envía un código mensaje al cliente en formato JSON reconido por el sistema
	 * @param string $html Código JS
	 * @param string $format formato de salida (JSON, XML, etc)
	 * @return JSON
	 */
	function message($success, $message, $headers = TRUE)
	{
		$data = array(
			'success' 	=> $success,
			'message'	=> $message
		);
		return $this->send($data, $headers);
	}

	/**
	 * Envía un código mensaje al cliente en formato JSON reconido por el sistema
	 * @param string $html Código JS
	 * @param string $format formato de salida (JSON, XML, etc)
	 * @return JSON
	 */
	function lightbox($message, $headers = TRUE)
	{
		$data = array(
			'success' 	=> TRUE,
			'lightbox'	=> $message
		);
		return $this->send($data, $headers);
	}

	/**
	 * Envía un código mensaje al cliente en formato JSON reconido por el sistema.
	 * Obliga a diálogo
	 * @param  bool  $success TRUE: Ok, FALSE: Error
	 * @param  [type]  $message Mensaje a mostrar
	 * @param bool $headers TRUE: Enviar los headers
	 * @return JSON
	 */
	function dialog($success, $message, $headers = TRUE)
	{
		$data = array(
			'success' 	=> $success,
			'dialog'	=> $message
		);
		return $this->send($data, $headers);
	}

	/**
	 * Envía un mensaje de error
	 * @param string $message Mensaje de error
	 * @param bool $headers TRUE: Enviar los headers
	 * @return JSON
	 */
	function success($message = null, $headers = TRUE)
	{
		return $this->message(TRUE, $message, $headers);
	}

	/**
	 * Envía un mensaje de error
	 * @param string $message Mensaje de error
	 * @param bool $headers TRUE: Enviar los headers
	 * @return JSON
	 */
	function error($message = null, $headers = TRUE)
	{
		return $this->message(FALSE, $message, $headers);
	}

	/**
	 * Formatea los datos en un formato de salida
	 *
	 * @param mixed $data Datos
	 * @param string $format Formato (JSON, XML)
	 * @return string Datos formateados
	 */
	function data($data = null, $count = null, $headers = TRUE)
	{
		if (isset($data))
		{
			$res = array(
				'total_data' 	=> (isset($count)?$count:count($data)),
				'value_data' 	=> $data,
				'success' 		=> true
			);
		}
		else
		{
			$res = array(
				'success' 		=> false
			);
		}

		// Respuesta
		echo $this->send($res, $headers);
	}

	/**
	 * Envía un código JS al cliente en formato JSON reconido por el sistema
	 * @param string $html Código JS
	 * @param string $format formato de salida (JSON, XML, etc)
	 * @return JSON
	 */
	function cmd($cmd, $headers = TRUE)
	{
		$data = array(
			'success' 	=> TRUE,
			'cmd'		=> $cmd
		);
		return $this->send($data, $headers);
	}

	/**
	 * Fuerza que no se cache la información
	 * @link http://blog.unijimpe.net/evitar-cache-con-php/
	 * @return null
	 */
	function noCache() 
	{
		header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
}

/* End of file out.php */
/* Location: ./system/libraries/out.php */