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

define('DILVE_CACHE_TIMEOUT', 1000 * 60 * 24 * 30);

/**
 * Acceso a Dilve
 * @author alexl
 *
 */
class Dilve {
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
	 * Usuario DILVE
	 * @var string
	 */
	private $_username;
	/**
	 * Contraseña DILVE
	 * @var string
	 */
	private $_password;
	/**
	 * URL de llamada a los sevicios DILVE
	 * @var string
	 */
	private $_url;

	/**
	 * Constructor
	 * @return Dilve
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		$this->_username = $this->obj->config->item('bp.dilve.username');
		$this->_password = $this->obj->config->item('bp.dilve.password');
		$this->_url = $this->obj->config->item('bp.dilve.url');

		log_message('debug', 'Dilve Class Initialised via '.get_class($this->obj));
	}

	/**
	 * xml2array() will convert the given XML text to an array in the XML structure.
	 * Link: http://www.bin-co.com/php/scripts/xml2array/
	 * Arguments : $contents - The XML text
	 *                $get_attributes - 1 or 0. If this is 1 the function will get
	 * the attributes as well as the tag values - this results in a different array
	 * structure in the return value.
	 *                $priority - Can be 'tag' or 'attribute'. This will change the
	 * way the resulting array sturcture. For 'tag', the tags are given more
	 * importance.
	 * Return: The parsed XML in an array form. Use print_r() to see the resulting
	 * array structure.
	 * Examples: $array =  xml2array(file_get_contents('feed.xml'));
	 *              $array =  xml2array(file_get_contents('feed.xml', 1,
	 * 'attribute'));
	 */
	function xml2array($contents, $get_attributes = 1, $priority = 'tag')
	{
		if (!$contents)
			return array();

		if (!function_exists('xml_parser_create'))
		{
			//print "'xml_parser_create()' function not found!";
			return array();
		}

		//Get the XML parser of PHP - PHP must have this module for the parser to work
		$parser = xml_parser_create('');
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		# http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($contents), $xml_values);
		xml_parser_free($parser);

		if (!$xml_values)
			return;
		//Hmm...

		//Initializations
		$xml_array = array();
		$parents = array();
		$opened_tags = array();
		$arr = array();

		$current = &$xml_array;
		//Refference

		//Go through the tags.
		$repeated_tag_index = array();
		//Multiple tags with same name will be turned into an array
		foreach ($xml_values as $data)
		{
			unset($attributes, $value);
			//Remove existing values, or there will be trouble

			//This command will extract these variables into the foreach scope
			// tag(string), type(string), level(int), attributes(array).
			extract($data);
			//We could use the array by itself, but this cooler.

			$result = array();
			$attributes_data = array();

			if (isset($value))
			{
				if ($priority == 'tag')
					$result = $value;
				else
					$result['value'] = $value;
				//Put the value in a assoc array if we are in the 'Attribute' mode
			}

			//Set the attributes too.
			if (isset($attributes) and $get_attributes)
			{
				foreach ($attributes as $attr => $val)
				{
					if ($priority == 'tag')
						$attributes_data[$attr] = $val;
					else
						$result['attr'][$attr] = $val;
					//Set all the attributes in a array called 'attr'
				}
			}

			//See tag status and do the needed.
			if ($type == "open")
			{
				//The starting of the tag '<tag>'
				$parent[$level - 1] = &$current;
				if (!is_array($current) or (!in_array($tag, array_keys($current))))
				{
					//Insert New tag
					$current[$tag] = $result;
					if ($attributes_data)
						$current[$tag . '_attr'] = $attributes_data;
					$repeated_tag_index[$tag . '_' . $level] = 1;

					$current = &$current[$tag];

				}
				else
				{
					//There was another element with the same tag name

					if (isset($current[$tag][0]))
					{
						//If there is a 0th element it is already an array
						$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
						$repeated_tag_index[$tag . '_' . $level]++;
					}
					else
					{
						//This section will make the value an array if multiple tags with the same name
						// appear together
						$current[$tag] = array(
								$current[$tag],
								$result
						);
						//This will combine the existing item and the new item together to make an array
						$repeated_tag_index[$tag . '_' . $level] = 2;

						if (isset($current[$tag . '_attr']))
						{
							//The attribute of the last(0th) tag must be moved as well
							$current[$tag]['0_attr'] = $current[$tag . '_attr'];
							unset($current[$tag . '_attr']);
						}

					}
					$last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
					$current = &$current[$tag][$last_item_index];
				}

			}
			elseif ($type == "complete")
			{
				//Tags that ends in 1 line '<tag />'
				//See if the key is already taken.
				if (!isset($current[$tag]))
				{
					//New Key
					$current[$tag] = $result;
					$repeated_tag_index[$tag . '_' . $level] = 1;
					if ($priority == 'tag' and $attributes_data)
						$current[$tag . '_attr'] = $attributes_data;

				}
				else
				{
					//If taken, put all things inside a list(array)
					if (isset($current[$tag][0]) and is_array($current[$tag]))
					{
						//If it is already an array...

						// ...push the new element into that array.
						$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

						if ($priority == 'tag' and $get_attributes and $attributes_data)
						{
							$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
						}
						$repeated_tag_index[$tag . '_' . $level]++;

					}
					else
					{
						//If it is not an array...
						$current[$tag] = array(
								$current[$tag],
								$result
						);
						//...Make it an array using using the existing value and the new value
						$repeated_tag_index[$tag . '_' . $level] = 1;
						if ($priority == 'tag' and $get_attributes)
						{
							if (isset($current[$tag . '_attr']))
							{
								//The attribute of the last(0th) tag must be moved as well

								$current[$tag]['0_attr'] = $current[$tag . '_attr'];
								unset($current[$tag . '_attr']);
							}

							if ($attributes_data)
							{
								$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
							}
						}
						$repeated_tag_index[$tag . '_' . $level]++;
						//0 and 1 index is already taken
					}
				}

			}
			elseif ($type == 'close')
			{
				//End of tag '</tag>'
				$current = &$parent[$level - 1];
			}
		}

		return ($xml_array);
	}

	/**
 	 * Llamada a DILVE
	 * @param string $action Comando
	 * @param array $params Parámetros de la llamada
	 * @return mixed, FALSE = Ha habido error, array: resultado 
 	 */
	private function _call($action, $params = null, $binary = FALSE)
	{
		$params= (isset($params))? ('&' . http_build_query($params)):'';
		$url = str_replace(array('%username%', '%password%', '%action%', '%params%'), array($this->_username, $this->_password, $action, $params), $this->_url);
		$error = error_reporting();
		error_reporting(0);
		$res = file_get_contents($url);
		error_reporting($error);
		if ($binary)
			return $res;
		$res = $this->obj->utils->xml2array($res);
		#$res = $this->xml2array($res);
		#var_dump($res); die();
		if (!isset($res['getRecordsXResponse']))
		{
			$this->_error = $this->obj->lang->line('dilve-articulo-error-notfound');
			return FALSE;
		}
		if (isset($res['getRecordsXResponse']['error']) && isset($res['getRecordsXResponse']['error']['text']))
		{
			#var_dump($res['getRecordsXResponse']['error']); die();
			$this->_error = "[{$res['getRecordsXResponse']['error']['code']}] - {$res['getRecordsXResponse']['error']['text']}";
			return FALSE;
		}
		return $res['getRecordsXResponse'];
	}
	
	/**
	 * Obtiene el ISBN de los datos ONIX
	 * @param  array $data Datos ONIX
	 * @return string
	 */
	function get_isbn($data)
	{
		$isbn = null;
		if (isset($data['ProductIdentifier']))
		{
			if (isset($data['ProductIdentifier']['ProductIDType']))
			{
				if ($data['ProductIdentifier']['ProductIDType'] == '03')
				{
					$isbn = $data['ProductIdentifier']['IDValue']; 
				}
			}				
			else
			{
				foreach($data['ProductIdentifier'] as $id)
				{
					if ($id['ProductIDType'] == '03')
					{
						$isbn = $id['IDValue'];
						break;
					}
				}
			}
		}
		return $isbn;
	}

	/**
	 * Obtiene una referencia de DILVE
	 * @param string $isbn ISBN del producto a buscar
	 * @param bool $cache Usa la cache
	 * @return array
	 */
	function get($isbn, $cache = TRUE)
	{
		$this->obj->load->library('ISBNEAN');
		$this->obj->load->library('WebSave');

		$en_cache = null;
		if (!is_array($isbn)) $isbn = array($isbn);
		if ($cache)
		{
			foreach($isbn as $k => $code)
			{
				$ean = $this->obj->isbnean->to_ean($code);
				$html = $this->obj->websave->get($ean, 'dilve', DILVE_CACHE_TIMEOUT);
				if ($html)
				{
					$en_cache[$code] = unserialize($html);
					unset($isbn[$k]);
				}
			}
		}

		$isbn = implode('|', $isbn);

		$res = FALSE;

		if (!empty($isbn))
		{
			$res = $this->_call('getRecordsX', array('identifier' => $isbn));
			if (isset($res['ONIXMessage']['Product']['RecordReference']))
				$res['ONIXMessage']['Product'] = array($res['ONIXMessage']['Product']);
		}

		#CACHEA
		if ($res && isset($res['ONIXMessage']['Product']) && $cache)
		{
			foreach ($res['ONIXMessage']['Product'] as $key => $value) 
			{
				$isbn = $this->get_isbn($value);
				$ean = $this->obj->isbnean->to_ean($isbn);
				$this->obj->websave->put($ean, 'dilve', serialize($value));
			}
		}
		if ($res && isset($res['ONIXMessage']['Product']) && isset($en_cache))
			return array_merge($res['ONIXMessage']['Product'], $en_cache);
		if ($res && isset($res['ONIXMessage']['Product']))
			return $res['ONIXMessage']['Product'];
		if (isset($en_cache))
			return $en_cache;
		return FALSE;
	} 

	/**
	 * Descarga un recurso desde DILVE
	 * @param  string $isbn  Código del artículo
	 * @param  string $media Nombre del recurso
	 * @return BINARY
	 */
	function media($isbn, $media)
	{
		return $this->_call('getResourceX', array('identifier' => $isbn, 'resource' => $media), TRUE);
	}

	/**
	 * Comprueba si la imagen se tiene que descargar y se descarga
	 * @param  array $p Datos DILVE
	 * @return string
	 */
	function check_cover(&$p)
	{
		if (isset($p['MediaFile']['MediaFileTypeCode'])&&($p['MediaFile']['MediaFileTypeCode']=='04'))
		{
			if ($p['MediaFile']['MediaFileLinkTypeCode'] == '06')
			{
				$file = DIR_TEMP_PATH . $p['MediaFile']['MediaFileLink'];
				file_put_contents($file, $this->media($this->get_isbn($p), $p['MediaFile']['MediaFileLink']));
				$p['MediaFile']['MediaFileLink'] = $file;
			}
		}
	}


	/**
	 * Último error generado
	 * @return string
	 */
	function get_error()
	{
		return $this->_error;
	}

}
/* End of file Scribd.php */
/* Location: ./system/libraries/scribd.php */