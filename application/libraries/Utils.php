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

define('PERFIL_GENERAL', 1);
define('PERFIL_ENVIO', 2);
define('PERFIL_FACTURACION', 3);
define('PERFIL_PEDIDO', 4);
define('PERFIL_DEVOLUCION', 5);
define('PERFIL_CONTABILIDAD', 6);
define('PERFIL_FISCAL', 7);
define('PERFIL_RECLAMACIONES', 8);
define('PERFIL_SUSCRIPCIONES', 9);
define('PERFIL_RECLAMACIONESSUSCRIPCIONES', 10);
define('PERFIL_FACTURACIONSUSCRIPCIONES', 11);
define('PERFIL_ENVIOFACTURACION', 12);
define('PERFIL_DIRIGIDO', 13);

/**
 * Utilidades generales
 * @author alexl
 *
 */
class Utils
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
	 * Constructor
	 * @return Utils
	 */
	function __construct()
	{
		$this->obj = &get_instance();
		log_message('debug', 'Utils Class Initialised via ' . get_class($this->obj));
	}

	/**
	 * Devuelve el contenido y las cabeceras de respuesta de la url indicada
	 * @param string $url URI a descargar
	 * @param bool $onlyheaders Solo las cabeceras
	 * @param array $post Parámetros post
	 * @param bool $old_browser usar Identificación browerantiguo
	 * @param int $timeout Timeout de la llamada (0 => sin límite)
	 * @return array 'response' => contenido, 'headers' => array de cabeceras
	 */
	function get_url($url, $onlyheaders = FALSE, $post = null, $old_browser = FALSE, $timeout=0)
	{
		// http://moises-soft.blogspot.com.es/2011/06/descargar-url-con-php-curl-emulando.html
		// browsers keep this blank.
		$referers = array("google.com", "yahoo.com", "msn.com", "ask.com", "live.com");
		$choice = array_rand($referers);
		$referer = "http://" . $referers[$choice] . "";
	
		if ($old_browser)
		{
			$browser = 'Mozilla/4.0 (compatible; MSIE 5.5b1; Mac_PowerPC)';
		}
		else
		{
			$browsers = array("Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.3) Gecko/2008092510 Ubuntu/8.04 (hardy) Firefox/3.0.3", "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20060918 Firefox/2.0", "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3", "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506)");
			$choice2 = array_rand($browsers);
			$browser = $browsers[$choice2];		//initiate curl transfer
		}
		// cabeceras enviadas por firefox
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: en-us,en;q=0.5";
		$header[] = "Pragma: ";
		
		$ch = curl_init();
		
		//set the URL to connect to
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, $browser);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_REFERER, $referer);		
		curl_setopt($ch, CURLOPT_FILETIME, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		#curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		if ($onlyheaders)
			curl_setopt($ch, CURLOPT_NOBODY, true);
		//register a callback function which will process the headers
		//this assumes your code is into a class method, and uses $this->readHeader as
		// the callback //function
		//Tell curl to write the response to a variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if (count($post) > 0)
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));	
		}

		//Execute request
		$response = curl_exec($ch);

		//get the default response headers
		$headers = curl_getinfo($ch);

		//close connection
		curl_close($ch);

		return array(
				'response' 	=> $response,
				'headers' 	=> $headers
			);
	}

	/**
	 * Obtiene todas las imágenes de un texto HTML
	 * @param string $src Fuente HTML
	 * @param string $url URL de origen
	 */
	function get_images_text($src, $url)
	{
		$image_regex = '/<img[^>]*' . 'src=[\"|\'](.*)[\"|\']/Ui';
		preg_match_all($image_regex, $src, $img, PREG_PATTERN_ORDER);
		#var_dump($img);
		if (isset($img[1]))
		{
			$this->obj->load->library('SearchImages');
			$domain = $this->obj->searchimages->get_domain($url);
			foreach ($img[1] as $k => $v)
			{
				$img[1][$k] = $this->obj->searchimages->add_domain($domain, $v);
			}
			return $img[1];
		}
		return null;
	}

	/**
	 * Obtiene todas las imágenes de un texto HTML
	 * @param string $src Fuente HTML
	 * @param string $url URL de origen
	 */
	function get_images_url($url)
	{
		$res = $this->get_url($url);
		#var_dump($res);

		return $this->get_images_text($res['response'], $url);
	}

	/**
	 * Delete a file or recursively delete a directory
	 *
	 * http://www.php.net/manual/en/function.unlink.php
	 * @param string $str Path to file or directory
	 */
	function recursiveDelete($str)
	{
		if (is_file($str))
		{
			return @unlink($str);
		}
		elseif (is_dir($str))
		{
			$scan = glob(rtrim($str, '/') . '/*');
			foreach ($scan as $index => $path)
			{
				$this->recursiveDelete($path);
			}
			return @rmdir($str);
		}
	}

	/**
	 * Devuelve el perfil del tipo indciado del modelo del tipo de perfil indicado.
	 * Si no existe el tipo indicado se devuelve uno general.
	 * Si no existe el general se devuelve el primero que encuentra.
	 * @param int $id ID del registro
	 * @param string $idname Nombre del campo Id del resgistro
	 * @param string $model Modelo de datos
	 * @param int $profile Tipo de perfil
	 */
	function get_profile_model($id, $idname, $model, $profile = null)
	{
		$this->obj->load->model("{$model}", $model);
		$datos = $this->obj->$model->get(null, null, null, null, "{$idname}={$id}");
		$general = null;
		$act = null;
		foreach ($datos as $dato)
		{
			if (isset($profile) && ($dato['nIdTipo'] == $profile))
			{
				$act = $dato;
				break;
			}
			if ($dato['nIdTipo'] == 1)
			{
				$general = $dato;
			}
		}
		return isset($act) ? $act : (isset($general) ? $general : isset($datos[0]) ? $datos[0] : null);
	}

	/**
	 * Busca el perfil del tipo indicado. Si no encuentra el tipo, devuelve un
	 * general, y si no hay general
	 * el primero de ellos
	 * @param array $profiles Perfiles
	 * @param mixed $type int: Id del perfil, array: perfiles posibles por orden de
	 * preferencia
	 * @return array, registro de perfil
	 */
	function get_profile($profiles, $types = PERFIL_GENERAL)
	{
		$general = null;
		if (!is_array($types))
			$types = array($types);
		if (count($profiles) > 0)
		{
			foreach ($types as $type)
			{
				foreach ($profiles as $perfil)
				{
					if ($perfil['id_perfil'] == $type)
						return $perfil;
					if (!isset($general) && $perfil['id_perfil'] == PERFIL_GENERAL)
						$general = $perfil;
				}
			}
			if (isset($general))
				return $general;
			return $profiles[0];
		}
		return null;
	}

	/**
	 * Aplica los alias de las URL
	 * @param string $url URL a revisar
	 * @return string, URL modificada
	 */
	function translate_url($url)
	{
		$alias = $this->obj->config->item('bp.runner.alias');
		if (isset($alias))
		{
			foreach ($alias as $k => $v)
			{
				$url = str_replace($k, $v, $url);
			}
		}
		return $url;

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
	 * Devuelve la fecha de un año atrás teniendo en cuenta si es un año bisiesto, que en tal caso devuelve el 28/02 
	 * @param int $fecha Fecha
	 * @return int Fecha año anterior
	 */
	function yearbefore($fecha)
	{
		$fecha2 = strtotime ( '-1 year' , $fecha ) ;
		if (date('m', $fecha2) > date('m', $fecha))
		{
			$fecha2 = strtotime ( '-1 day' , $fecha2 ) ;
		}
		
		return $fecha2;
	}

	/**
	 * Elimina un directorio recursivamente
	 * @param string $dir Directorio a borrar
	 * @return null
	 */
	function rrmdir($dir) 
	{
		if (is_dir($dir)) 
		{
			$objects = scandir($dir);
			foreach ($objects as $object) 
			{
				if ($object != "." && $object != "..") 
				{
					if (filetype($dir."/".$object) == "dir") 
						$this->rrmdir($dir."/".$object); 
					else 
						unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}

	/**
	 * Conversor de array a HTML
	 * @param array $data
	 * @return string
	 */
	function array_to_html($data)
	{
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
	 * Limpiar Código Postal
	 * @param string $cp Código postal
	 * @return string
	 */
	function cleanCP($cp)
	{
		return str_replace('.', '', $cp);
	}

	/**
	 * Divide el texto de un teixell en líneas
	 * @param  string $value      Texto a dividir
	 * @param  int $caracteres Tamaño de la línea
	 * @return array
	 */
	function partirTeixells($value, $caracteres)
	{
		$value = str_replace('"-', '"%%%', $value);
		$value = str_replace(' ', '_', $value);
		$value = str_replace('.(', '(', $value);
		$value = preg_replace('/(\(.*?\))/', ' $1 ', $value);
		$value = preg_replace('/(\".*?\")/', ' $1 ', $value);
		$value = str_replace('.', ' .', $value);
		$value = str_replace('-', ' -', $value);
		$value = str_replace('%%%', '-', $value);
		$lineas = array_filter(explode(' ', $value));

		$l = array();
		$text = '';
		foreach ($lineas as $k => $v)
		{
			if (strlen($text . $v) <= $caracteres)
			{
				$text .= $v;
			}
			elseif (strlen($v)>$caracteres)
			{
				$l[] = trim(str_replace('_', ' ', $text));
				$l[] = trim(str_replace('_', ' ', $v));
				$text = '';
			}
			else
			{
				$l[] = trim(str_replace('_', ' ', $text));
				$text = $v;
			}
		}
		if (!empty($text))
			$l[] = trim(str_replace('_', ' ', $text));

		return $l;
	}

	/**
	 * Parte un array en n partes
	 * @param  array $list Array a partir
	 * @param  int $p  Partes
	 * @return array  Array de arrays
	 */
	function partition( $list, $p ) 
	{
	    $listlen = count( $list );
	    $partlen = floor( $listlen / $p );
	    $partrem = $listlen % $p;
	    $partition = array();
	    $mark = 0;
	    for ($px = 0; $px < $p; $px++) {
	        $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
	        $partition[$px] = array_slice( $list, $mark, $incr );
	        $mark += $incr;
	    }
	    return $partition;
	}

	/**
	 * Execute a command and return it's output. Either wait until the command exits or the timeout has expired.
	 * http://blog.dubbelboer.com/2012/08/24/execute-with-timeout.html
	 * @param string $cmd     Command to execute.
	 * @param number $timeout Timeout in seconds.
	 * @return array 'errors', 'result', 'buffer' Output of the command.
	 */
	function exec_timeout($cmd, $timeout = null, $stdin = null) 
	{
		// File descriptors passed to the process.
		$descriptors = array(
			0 => array('pipe', 'r'),  // stdin
			1 => array('pipe', 'w'),  // stdout
			2 => array('pipe', 'w')   // stderr
		);

		// Start the process.
		$process = proc_open('exec ' . $cmd, $descriptors, $pipes);

		if (!is_resource($process)) 
		{
			return FALSE;
		}
		# Envía stdin
		if (isset($stdin))
		{
 			fwrite($pipes[0], $stdin);
    		fclose($pipes[0]);
    	}

		// Set the stdout stream to none-blocking.
		stream_set_blocking($pipes[1], 0);

		// Turn the timeout into microseconds.
		if (isset($timeout))
			$timeout = $timeout * 1000000;

		// Output buffer.
		$buffer = '';

		#echo "Timeout: {$timeout}\n";

		// While we have time to wait.
		while (!isset($timeout) || $timeout > 0) 
		{
			$start = microtime(true);
			#echo "Start: {$start}\n";

			// Wait until we have output or the timer expired.
			$read  = array($pipes[1]);
			$other = array();
			stream_select($read, $other, $other, 0, $timeout);

			// Get the status of the process.
			// Do this before we read from the stream,
			// this way we can't lose the last bit of output if the process dies between these functions.
			$status = proc_get_status($process);

			// Read the contents from the buffer.
			// This function will always return immediately as the stream is none-blocking.
			$buffer .= stream_get_contents($pipes[1]);

			if (!$status['running']) 
			{
				// Break from this loop if the process exited before the timeout.
				break;
			}

			// Subtract the number of microseconds that we waited.
			if (isset($timeout))
				$timeout -= (microtime(true) - $start) * 1000000;
			#echo "Timeout: {$timeout}\n";
		}
		#echo "END Timeout: {$timeout}\n";

		// Check if there were any errors.
		if ($timeout > 0)
		{
			#echo "Leyendo error..\n";
			$errors = stream_get_contents($pipes[2]);
			#echo "END leyendo error\n";
			$result['errors'] = $errors;
		}

		// Kill the process in case the timeout expired and it's still running.
		// If the process already exited this won't do anything.
		#echo "KILL\n";
		proc_terminate($process, 9);

		// Close all streams.
		#echo "PIPES\n";
		if (!isset($stdin)) fclose($pipes[0]);
		fclose($pipes[1]);
		fclose($pipes[2]);

		#echo "RESULT\n";
		$result['return'] = proc_close($process);
		$result['buffer'] = $buffer;

		#echo "Finalizado exec\n";

		return $result;
	}

	/**
	 * Pass it a multidimensional array or object and each sub-array/object will be hidden and replaced by a html link that will toggle its display on and off.
	 * Its quick and dirty, but great for debugging the contents of large arrays and objects.
	 * Note: You'll want to surround the output with <pre></pre>
	 * http://es1.php.net/print_r
	 * @param  array $data Array a impromir
	 * @return string HTML
	 */
	function print_r_tree($data)
	{
	    // capture the output of print_r
	    $out = print_r($data, true);

	    // replace something like '[element] => <newline> (' with <a href="javascript:toggleDisplay('...');">...</a><div id="..." style="display: none;">
	    error_reporting(E_ERROR);
	    $out = preg_replace('/([ \t]*)(\[[^\]]+\][ \t]*\=\>[ \t]*[a-z0-9 \t_]+)\n[ \t]*\(/iUe',
	    	"'\\1<a href=\"javascript:toggleDisplay(\''.(\$id = substr(md5(rand().'\\0'), 0, 7)).'\');\">\\2</a><div id=\"'.\$id.'\" style=\"display: none;\">'", 
	    	$out);

	    // replace ')' on its own on a new line (surrounded by whitespace is ok) with '</div>
	    $out = preg_replace('/^\s*\)\s*$/m', '</div>', $out);

	    // print the javascript function toggleDisplay() and then the transformed output
	    return '<script language="Javascript">function toggleDisplay(id) { document.getElementById(id).style.display = (document.getElementById(id).style.display == "block") ? "none" : "block"; }</script>'."\n$out";
	}

	/**
	 * Completa un array para que sean resultados por meses
	 *
	 * @param array $datos Datos
	 * @return array
	 */
	function meses_datos($datos, $field ='importe')
	{
		$res = array();
		for($i = 0; $i < 12; $i++)
		{
			$res[$i] = 0;
		}
		foreach($datos as $mes)
		{
			$res[$mes['mes'] - 1] = $mes[$field];
		}
		return $res;
	}

/**
	 * Completa un array para que sean resultados por años y meses
	 *
	 * @param array $datos Datos
	 * @return array
	 */
	function year_meses_datos($datos, $field ='importe')
	{
		$res = array();
		#$total = 0;
		foreach($datos as $mes)
		{
			$res[$mes['y']][$mes['mes'] - 1] = $mes[$field];
		}
		foreach ($res as $key => $value) 
		{
			for($i = 0; $i < 12; $i++)
			{
				if (!isset($value[$i]))
					$value[$i] = 0;
				#$total += $value[$i];
			}
			ksort($value);
			$res[$key] = $value;
		}
		#$res['total'] = $total;

		return $res;
	}


	/**
	 * Último error generado
	 * @return string
	 */
	function get_error()
	{
		return $this->_error;
	}


    public function UTF8entities($content="") { 
        $contents = $this->unicode_string_to_array($content);
        $swap = "";
        $iCount = count($contents);
        for ($o=0;$o<$iCount;$o++) {
            $contents[$o] = $this->unicode_entity_replace($contents[$o]);
            $swap .= $contents[$o];
        }
        return mb_convert_encoding($swap,"UTF-8"); //not really necessary, but why not.
    }

    public function unicode_string_to_array( $string ) { //adjwilli
        $strlen = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr( $string, 0, 1, "UTF-8" );
            $string = mb_substr( $string, 1, $strlen, "UTF-8" );
            $strlen = mb_strlen( $string );
        }
        return $array;
    }

    public function unicode_entity_replace($c) { //m. perez 
        $h = ord($c{0});    
        if ($h <= 0x7F) { 
            return $c;
        } else if ($h < 0xC2) { 
            return $c;
        }
        
        if ($h <= 0xDF) {
            $h = ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
            $h = "&#" . $h . ";";
            return $h; 
        } else if ($h <= 0xEF) {
            $h = ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6 | (ord($c{2}) & 0x3F);
            $h = "&#" . $h . ";";
            return $h;
        } else if ($h <= 0xF4 && isset($c{2})) {
            $h = ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12 | (ord($c{2}) & 0x3F) << 6 | (ord($c{3}) & 0x3F);
            $h = "&#" . $h . ";";
            return $h;
        }
    }

    /**
     * Leer del terminal
     * @param  string  $message Mensaje
     * @param  boolean $hidden  Ocultar respuesta
     * @return string
     */
    function prompt($message = 'prompt: ', $hidden = false) 
    {
        if (PHP_SAPI !== 'cli') 
        {
            return false;
        }
        echo $message;
        $ret = 
            $hidden
            ? exec(
                PHP_OS === 'WINNT' || PHP_OS === 'WIN32'
                ? __DIR__ . '\prompt_win.bat'
                : 'read -s PW; echo $PW'
            )
            : rtrim(fgets(STDIN), PHP_EOL)
        ;
        if (PHP_OS !== 'WINNT' && PHP_OS !== 'WIN32') {
            echo PHP_EOL;
        }
        return $ret;
    }

}

/* End of file Utils.php */
/* Location: ./system/libraries/utils.php */
