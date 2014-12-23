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

define('CACHE_TIMEOUT_AMAZON', 1000 * 60 * 24 * 30);
define('CACHE_TIMEOUT_COMEIN', 1000 * 60 * 24 * 30);
define('CACHE_TIMEOUT_LAIE', 1000 * 60 * 24 * 30);
define('CACHE_TIMEOUT_LACENTRAL', 1000 * 60 * 24 * 30);
define('CACHE_TIMEOUT_CASADELLIBRO', 1000 * 60 * 24 * 30);
define('CACHE_TIMEOUT_JAIMES', 1000 * 60 * 24 * 30);

/**
 * Muestra por defecto para las búsquedas
 */
define('CONST_MUESTRA_DEFECTO', 100);

/**
 * Búsqueda de artículos en Internet
 * @author alexl
 *
 */
class SearchInternet
{
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Path para el programa de descargas
	 * @var string
	 */
	private $download_path = null;
	/**
	 * Timeout para el programa de descargas
	 * @var int
	 */
	private $download_timeout = null;
	/**
	 * Número de hilos para el programa de descargas
	 * @var int
	 */
	private $download_threads = null;

	/**
	 * Constructor
	 * @return SearchInternet
	 */
	function __construct()
	{
		$this->obj = &get_instance();
		$path = $this->obj->config->item('bp.path.download');
		$this->download_threads = $this->obj->config->item('bp.path.download.threads');
		$this->download_path = $this->obj->config->item('bp.path.download.path');
		$this->download_timeout = $this->obj->config->item('bp.path.download.timeout');

		log_message('debug', 'SearchInternet Class Initialised via ' . get_class($this->obj));
	}

	/**
	 * Imagenes de un archivo HTML
	 *
	 * Examina un archivo HTML en busca de sus imagenes para
	 * luego devolver su correspondiente direccion relativa.
	 *
	 * @author  fran86       <fran86@myrealbox.com>
	 * @param   string       $archivo      Path correspondiente al HTML a examinar.
	 * @param   bool         $norepetidos  Opcional para no repetir las imagenes.
	 * @return  array|false  Array con los paths relativos de las imagenes
	 *
	 */
	private function imagenesHTML($archivo, $norepetidos = TRUE)
	{
		if (filter_var($archivo, FILTER_VALIDATE_URL) === FALSE)
		{
			return $this->google($archivo);
		}

		$contenido = $this->obj->utils->get_url($archivo);
		if (isset($contenido['headers']['content_type']))
		{
			if (strpos($contenido['headers']['content_type'], 'image') !== FALSE)
			{
				return array($archivo);
			}
		}
		$domain = $this->get_domain($archivo);

		$contenido = $contenido['response'];

		if (preg_match_all('/<img([^<>]+)>/i', $contenido, $match))
		{
			foreach ($match[1] as $atributos)
			{
				if (preg_match('/src="([^"]+)"/i', $atributos, $matchpaths))
				{
					$pathimgs[] = $this->add_domain($domain, $matchpaths[1]);
				}
				elseif (preg_match('/src=([^ ]+)/i', $atributos, $matchpaths))
				{
					$pathimgs[] = $this->add_domain($domain, $matchpaths[1]);
				}
				unset($matchpaths);
			}
		}
		if (!empty($pathimgs))
		{
			if ($norepetidos)
			{
				return array_unique($pathimgs);
			}
			else
			{
				return $pathimgs;
			}
		}
		return array();
	}

	/**
	 * Búsqueda en Google Images
	 * @param string $text EAN/ISBN/Título a buscar
	 * @return array
	 */
	private function google($text)
	{
		$text = urlencode(trim($text));
		$url = "http://www.google.es/images?hl=es&source=imghp&biw=1600&bih=681&gbv=2&aq=f&aqi=&oq=&q={$text}&tbs=isch:1";
		$contenido = file_get_contents($url);
		$pathimgs = array();
		
		if (preg_match_all('/\"\/imgres\?(.*?)\\x26/i', $contenido, $match))
		{
			foreach ($match[1] as $atributos)
			{
				$pathimgs[] = str_replace(array(
						'imgurl=',
						'\x3d',
						'\x26'
				), '', $atributos);
			}
		}
		return $pathimgs;
	}

	/**
	 * Búsqueda en Google normal
	 * @param string $text EAN/ISBN/Título a buscar
	 * @return array
	 */
	private function google2($text)
	{
		$text = urlencode(trim($text));
		$pathimgs = array();
		// Busca dentro de los resultados
		$url = "http://www.google.es/search?hl=es&q={$text}";
		$contenido = file_get_contents($url);
		if (preg_match_all('/<a.href=\"([^"]*?)\".onmouse/i', $contenido, $match))
		{
			$count = 0;
			foreach ($match[1] as $link)
			{
				$images = $this->imagenesHTML($link);
				$pathimgs = array_merge($pathimgs, $images);
				++$count;
				if ($count == 4)
					break;
			}
		}
		return $pathimgs;
	}
	
	/**
	 * Descarga todas las URLs indicadas
	 * @param  array $datos 'url' => 'code'
	 * @return array 'code' => 'html'
	 */
	private function _download($datos)
	{
		#var_dump($datos);
		$info = "timeout={$this->download_timeout}\nthreads={$this->download_threads}\n" . 
			implode("\n", array_keys($datos));
		#print $info;
		#echo "Llamando a EXEC...\n";
		$res = $this->obj->utils->exec_timeout($this->download_path, 30, $info); 
		#var_dump($res);
		#echo "Finalizado EXEC\n";
		$final = array();
		if (!empty($res['buffer']))
		{
			$r = explode('###URL###', $res['buffer']);
			foreach($r as $info)
			{
				$pos = strpos($info, '###HTML###');
				$url = trim(substr($info, 0, $pos - 13));
				$html = trim(substr($info, $pos + 25));
				if (!empty($url))
					$final[$datos[$url]] = $html;
			}
		}
		return $final;
	}


	private function _amazon_patterns()
	{
		return array(
				'title' 		=> '"title"\:"(.*?)"',
				'pages' 		=> '<\/b>(.*?)p.ginas<',
				'lang' 			=> '<li><b>Idioma:<\/b>\s([^<]*)<\/li>',
				'publisher' 	=> '<b>Editor:<\/b>(.*?)\;',
				'edition' 		=> 'Edici.n:(.*?)\<\/li>',
				'date'			=> 'Edici.n:.*\((.*?)\)',
				'description'	=> '/<div.class\="productDescriptionWrapper">(.*?)<div.class\="emptyClear/isU',
				'price' 		=> 'class="listprice">EUR (.*?)<\/span>',
				'category'		=> '/href=".*ref=dp_brlad_entry.*">(.*)<\/a>/isU',
				'cover' 		=> 'prefetchURL.\=."(.*?)"',
				'currency' 		=> 'class="listprice">(.*?)\s',
				'author'		=> 'field-author\=(.*?)\&amp\;',
				'format'		=> '<b>(.*?)\:<\/b>.*?p.ginas',
				'colection' 	=> 'Colecci.n:<\/b>(.*?)\<\/li>',
			);		
	}

	/**
	 * Descarga la página HTML de Amazon con la información de un título
	 * @param  mixed $code Array/string : códigos a buscar
	 * @param bool $cache Usa la cache
	 * @return array ('code' => HTML)
	 */
	function amazon($code, $cache = TRUE)
	{
		$patterns = $this->_amazon_patterns();

		$this->obj->load->library('ISBNEAN');
		$this->obj->load->library('WebSave');
		if (!is_array($code))
			$code = array($code);
		$datos = array();
		$en_cache = array();
		foreach ($code as $c)
		{
			$isbn10 = $this->obj->isbnean->to_isbn($c, TRUE);			
			$isbn10 = $this->obj->isbnean->clean_code($isbn10['isbn10']);
			if ($cache)
			{
				$ean = $this->obj->isbnean->to_ean($c);
				$html = $this->obj->websave->get($ean, 'amazon', CACHE_TIMEOUT_AMAZON);
				if ($html)
				{
					$en_cache[$isbn10] = $html;
				}
				else
				{
					$datos['http://www.amazon.es/gp/product/' . $isbn10] = $c;					
				}
			}
			else
			{
				$datos['http://www.amazon.es/gp/product/' . $isbn10] = $c;
			}
		}
		#var_dump($en_cache); die();
		if (count($datos) > 0 )
		{
			#echo "Leyendo..." . count($datos) ."\n";
			$regs = $this->_download($datos);
			#echo "Leído\n";
			if ($cache)
			{
				foreach($regs as $code => $html)
				{
					$ean = $this->obj->isbnean->to_ean($code);
					$this->obj->websave->put($ean, 'amazon', $html);
				}
			}
			$regs = array_replace($regs, $en_cache);
		}
		else
			$regs = $en_cache;
		#var_dump(count($en_cache), count($datos), count($regs)); die();
		$final = array();
		#echo "Procesando...\n";
		foreach($regs as $code => $html)
		{
			$res = $this->_apply_patterns($html, $patterns, $hay, TRUE);
			if (isset($res['category']) && !is_array($res['category']))
				$res['category'] = array($res['category']);
			if (isset($res['author']))
			{
				if (is_array($res['author']))
				{
					foreach ($res['author'] as $k => $v)
					{
						$res['author'][$k] = urldecode($v);
					}				
				}
				else
				{
					$res['author'] = urldecode($res['author']);
				}
			}
			if (!empty($res['description']))
			{
				$pos = strpos($res['description'], '<div class="emptyClear">');
				if ($pos > 0)
					$res['description'] = substr($res['description'], 0, $pos-1);				
			}
			if ($hay > 0)
				$final[$code] = $res;
		}
		#echo "Procesado\n";
		return $final;
	}

	/**
	 * Búsqueda de artículos
	 * @param string $text EAN/ISBN/Título a buscar
	 * @param string $method auto: el defecto, google, google2,
	 * @return array
	 */
	function search($text, $method = null)
	{
		$data = array();
		if (isset($text) && $text != '')
		{
			if (!isset($method))
				$method = 'auto';

			if ($method == 'auto')
			{
				$images = $this->imagenesHTML($text);
			}
			elseif ($method == 'google')
			{
				$images = $this->google($text);
			}
			elseif ($method == 'google2')
			{
				$images = $this->google2($text);
			}
			if (count($images) > 0)
			{
				foreach ($images as $img)
				{
					$data[] = array(
							'name' => $img,
							'url' => urldecode($img)
					);
				}
			}
		}
		#echo '<pre>'; print_r(parse_url($text)); echo '</pre>';
		#echo '<pre>'; print_r($data); echo '</pre>';

		return $data;
	}

	/**
	 * Aplica las expresiones regulares al texto recibido
	 * @param  string  $info     Texto
	 * @param  array  $patterns  Patrones 'key' => 'patron'
	 * @param  integer $hay      Número de patrones encontrados con valor
	 * @return array 'key' => 'valor'
	 */
	private function _apply_patterns(&$info, &$patterns, &$hay = 0, $multi = FALSE)
	{
		$data = null;
		foreach ($patterns as $key => $value) 
		{
			#echo "Aplicando {$key} => {$value}\n";
			if (substr($value,0,1) != '/')
				$value = '/' . $value . '/';
			preg_match_all($value, $info, $matches);
			#var_dump($key, $matches[1]);
			if (count($matches[1]) <= 1 || (!$multi && count($matches[1]) > 1))
			{
				$data[$key] = (empty($matches[1][0]))?null:trim($matches[1][0]);
				++$hay;
			}
			else
			{
				$data[$key] = $matches[1];
				++$hay;
			}
		}
		return $data;
	}

	/**
	 * Busca datos de títulos por EAN en la Web de Come In
	 * @param  string $ean EAN del producto
	 * @param array @config Patrones
	 * @param string @html HTML donde buscar, opcional. Si está vacío devuelve el leído
	 * @return array (place, price, url, title)
	 */
	private function _data_generic($ean, $config, $group = null, $timeout = null, &$html = null)
	{
		if (empty($html))
		{
			$en_cache = FALSE;
			if (isset($group))
			{
				$html = $this->obj->websave->get($ean, $group, $timeout);
				$en_cache = !empty(trim($html));
			}
			if (!$en_cache)
			{
				$url = str_replace('%ean%', $ean, $config['url']);
				$post = null;
				if (isset($config['post']))
				{
					foreach ($config['post'] as $key => $value) 
					{
						$post[$key] = str_replace('%ean%', $ean, $value);
					}
				}		
				$res = $this->obj->utils->get_url($url, FALSE, $post);
				$html = trim($res['response']);
			}
		}

		$data = $this->_apply_patterns($html, $config['patterns'], $hay);
		$data['place'] = $config['place'];
		$data['icon'] = $config['icon'];
		if ($hay>0)
		{
			if (isset($group) && !$en_cache && !empty($html))
			{
				$this->obj->websave->put($ean, $group, $html);
			}
			return $data;
		}
		return FALSE;
	}

	/**
	 * Busca datos de títulos por EAN en la Web de Come In
	 * @param  string $ean EAN del producto
	 * @return array (place, price, url, title)
	 */
	function comein($ean, $html = null)
	{
		$config = array(
			'url'	=> 'http://www.libreriainglesa.com/libros/fault/%ean%/',
			'place'	=> 'COMEIN',
			'icon'	=>  'http://www.libreriainglesa.com/skin/frontend/default/default/favicon.ico',
			'patterns'	=> array(
				'price'	=> '/<div.class="datosExtra">.*<\/span>(.*)€.*\<\/div/isU',
				'title'	=> '/<div.class="titBlock">.*<h1>(.*?)<\/h1>/isU',
				'publisher' => 'Publisher\:<\/strong>.<a.href=".*">(.*?)<\/a>',
				'cover' => '<a href="(.*?)".class="zoom">',
				'description' => '/Book summary<\/span><\/h2>.*(.*?)<\/p><\/p>[\s\n]*<\!--.separador.-->/isU'
				)
			);

		$data = $this->_data_generic($ean, $config, 'comein', CACHE_TIMEOUT_COMEIN, $html);
		$data['url'] = 'http://www.libreriainglesa.com/libros/fault/' . $ean .'/';
		if (!empty($data['description']))
		{
			$data['description'] = utf8_decode(str_replace(array('<p>', '</p>'), '', $data['description']));
		}
		#echo $html; die();
		if (!empty($data['cover'])) $data['cover'] = 'http://www.libreriainglesa.com' .$data['cover'];
		if (!empty($data['price'])) $data['price'] = str_replace(',', '.', $data['price']);
		return $data;
	}

	function comein_url($ean)
	{
		return 'http://www.libreriainglesa.com/catalogsearch/advanced/result/?isbn=' . $ean;
	}

	/**
	 * Busca datos de títulos por EAN en la Web de Jaimes
	 * @param  string $ean EAN del producto
	 * @return array (place, price, url, title)
	 */
	function jaimes($ean, $html = null)
	{		
		$config = array(
			'url'	=> 'http://www.jaimes.cat/producto/listadobuscar?buscar=%ean%',
			'place'	=> 'JAIMES',
			'icon'	=> 'http://www.jaimes.cat/favicon.ico',
			'patterns'	=> array(
				'price'	=> 'itemprop\=\"price\">([^€]*)€',
				'url'	=> '<h4.class\=\"titulo\"><a.href="(.*?)"',
				'title'	=> 'itemprop="name">(.*?)<\/a><\/h4>',
				'cover' => '<img.alt=".*".itemprop="image".src="(.*?)\&amp.*"',           
				)
			);
		$data = $this->_data_generic($ean, $config, 'jaimes_fast', CACHE_TIMEOUT_JAIMES, $html);
		if (!empty($data['url'])) $data['url'] = 'http://www.jaimes.cat' . $data['url'];
		if (!empty($data['cover'])) $data['cover'] = 'http://www.jaimes.cat' . $data['cover'];
		if (!empty($data['price'])) $data['price'] = str_replace(',', '.', $data['price']);
		if (!isset($data['price'])||($data['price'] == 0)) return FALSE;
		return $data;
	}

	function jaimes_url($ean)
	{
		return 'http://www.jaimes.cat/producto/listadobuscar?buscar='.$ean;
	}

	/**
	 * Busca datos de títulos por EAN en la Web de Casa del Libro
	 * @param  string $ean EAN del producto
	 * @return array (place, price, url, title)
	 */
	function casadellibro($ean, $html = null)
	{
		$config = array(
			'url'	=> 'http://www.casadellibro.com/busqueda-libros?busqueda=%ean%&nivel=5',
			'place'	=> 'Casa del Libro',
			'icon'	=> 'http://www.casadellibro.com/favicon.ico',
			'patterns'	=> array(
				'price'	=> 'class="price"[^\>]*>(.*)<\/span>',
				'url'	=> 'title-link.searchResult".href="([^\"]*)"',
				'title'	=> 'addOri\(\\\'br\\\'\)\"\>([^\<]*)<',
				'date' => 'Fecha.Lanzamiento\:.(.*?)\n',
				'cover'	=> '<img.src\="(.*?)".*class="img\-shadow"',
				'description' => '/<p.class\=\"smaller.pb15\">(.*)<\/p>/isU'
				)
			);
		$data = $this->_data_generic($ean, $config, 'casadellibro', CACHE_TIMEOUT_CASADELLIBRO, $html);

		if (!empty($data['description']))
		{
			$data['description'] = utf8_decode($data['description']);
			$pos = strpos($data['description'], '<span class="text-ellipsis">');
			if ($pos)
			{
				$data['description'] = substr($data['description'], 0, $pos);
			}		
			$pos = strpos($data['description'], '<span class="text-ellipsis">');
			$data['description'] = str_replace(" 
                                    
                                        <span id=\"sinopsis0\" class=\"expand-content expand-hide\">", '' , $data['description']);
		}
		if (!empty($data['cover']))
		{
			$data['cover'] = str_replace('http://image.casadellibro.com/libros/3', 'http://image.casadellibro.com/libros/0', $data['cover']);
		}
		#echo /*htmlspecialchars*/($data['description']); die();
		if (!empty($data['url'])) $data['url'] = 'http://www.casadellibro.com' . $data['url'];
		if (!empty($data['price'])) $data['price'] = str_replace(array('&euro;', '<span>',' '), '', $data['price']);
		if (!empty($data['price'])) $data['price'] = str_replace(',', '.', $data['price']);
		return $data;
	}

	function casadellibro_url($ean)
	{
		return 'http://www.casadellibro.com/busqueda-libros?busqueda=' . $ean . '&nivel=5';
	}

	/**
	 * Busca datos de títulos por EAN en la Web de La Central
	 * @param  string $ean EAN del producto
	 * @return array (place, price, url, title)
	 */
	function lacentral($ean, $html = null)
	{
		$config = array(
			'url'	=> 'http://www.lacentral.com/web/book/?id=%ean%',
			'place'	=> 'La Central',
			'icon'	=> 'http://www.lacentral.com/favicon.ico',
			'patterns'	=> array(
				'price'	=> '<li>P.*\:(.*?)\&euro\;<\/li>',
				'publisher'	=> '<li>Editorial:.*<a.href=\".*\">(.*?)<\/a><\/li>',
				'pages'	=> '<li>.*ginas.*\:(.*?)<\/li>',
				'title'	=> '<h4>(.*?)<\/h4>[\n\s\r]*<ul.class\="datosFicha"',
				'cover' => 'class="imgFicha"><img.src=\'(.*)\'.style',
				'description' => '/<div.id="sinopsis".style="">(.*)<\/div>/isU',
				)
			);
		$data = $this->_data_generic($ean, $config, 'lacentral', CACHE_TIMEOUT_LACENTRAL, $html);
		#echo($html);
		#echo htmlspecialchars($html); die();
		if (!empty($data['description']))
		{
			$data['description'] = utf8_decode(str_replace(array('<p>', '</p>'), '', $data['description']));
		}
		$data['url'] = 'http://www.lacentral.com/web/book/?id=' . $ean;
		if (isset($data['cover'])) 
		{
			if ($data['cover'] == '/imgs/imgMuestra4.png')
				unset($data['cover']);
			else
				$data['cover'] = 'http://www.lacentral.com' . $data['cover'];
		}
		#if (!empty($data['price'])) $data['price'] = str_replace(',', '.', $data['price']);
		return $data;
	}

	function lacentral_url($ean)
	{
		return 'http://www.lacentral.com/web/book/?id=' . $ean;
	}
	
	/**
	 * Busca datos de títulos por EAN en la Web de Amazon
	 * @param  string $ean EAN del producto
	 * @return array (place, price, url, title)
	 */
	function amazon_data($ean, $html = null)
	{
		$isbn = $this->obj->isbnean->to_isbn($ean, TRUE);
		$cachear = FALSE;
		#var_dump($isbn); die();
		$isbn = str_replace('-', '', $isbn['isbn10']);
		$url = 'http://www.amazon.es/tit/dp/' . $isbn;
		$config = array(
			'url'	=> $url,
			'place'	=> 'Amazon ES',
			'icon'	=> 'http://www.amazon.es/favicon.ico',
			'patterns'	=> $this->_amazon_patterns()
			);
	
		$data = $this->_data_generic($ean, $config, 'amazon', CACHE_TIMEOUT_AMAZON, $html);
		$data['url'] = $url;
		#if (!empty($data['url'])) $data['url'] = 'http://www.casadellibro.com' . $data['url'];
		if (!empty($data['price'])) $data['price'] = str_replace(array('&euro;', '<span>',' '), '', $data['price']);
		if (!empty($data['price'])) $data['price'] = str_replace(',', '.', $data['price']);

		return $data;
	}

	function amazon_data_url($ean)
	{
		return 'http://www.amazon.es/s/?field-keywords=' . $ean;
	}

	/**
	 * Busca datos de títulos por EAN en la Web de Laie
	 * @param  string $ean EAN del producto
	 * @return array (place, price, url, title)
	 */
	function laie($ean, $html = null)
	{
		$config = array(
			'url'	=> 'http://www.laie.es/busqueda/listaLibros.php?tipoArticulo=L0&isbn=%ean%',
			'place'	=> 'Laie',
			'icon'	=> 'http://www.laie.es/favicon.ico',
			'patterns'	=> array(
				'price'	=> '<dd.class\=\"precio\">(.*)&euro;',
				'url'	=> 'sombra\"\>[\n\s\r]*<a.href="([^"]*)"',
				'title'	=> '\"meta\">[\n\s\r]*<h3><a.*>(.*)<\/a>', 
				#'cover' => '<img.class="foto".src="(.*?)"',
				)
			);

		$data = $this->_data_generic($ean, $config, 'laie_fast', CACHE_TIMEOUT_LAIE, $html);
		if (!empty($data['url'])) $data['cover'] ='http://www.laie.es/foto/muestraPortada.php?id=' . $ean;
		if (!empty($data['url'])) $data['url'] = 'http://www.laie.es' . $data['url'];
		if (!empty($data['price'])) $data['price'] = str_replace(',', '.', $data['price']);
		return $data;
	}

	function laie_url($ean)
	{
		return 'http://www.laie.es/busqueda/listaLibros.php?tipoArticulo=L0&isbn=' .$ean;
	}

	/**
	 * Busca los precios del ISBN/EAN indicado en las distintas fuentes
	 * @param  string $code ISBN/EAN
	 * @return array
	 */
	function precios($code, $motor = null)
	{
		$this->obj->load->library('ISBNEAN');
		$this->obj->load->library('WebSave');
		$ean = $this->obj->isbnean->to_ean($code);
		#var_dump($this->casadellibro($ean)); die();
		$motores = array('casadellibro', 'amazon_data', 'lacentral', 'jaimes', 'comein', 'laie');
		if (isset($motor) && in_array($motor, $motores))
		{
			$motores = array($motor);
		}
		foreach ($motores as $value) 
		{
			$timer_global = microtime(true);
			$data = $this->$value($ean);
			if ($data)
			{
				$data['time'] = microtime(true)-$timer_global;
				$res[] = $data;
			}
		}
		return $res;

		$urls = array();
		foreach ($motores as $value) 
		{
			$call=$value.'_url';
			$urls[$this->$call($ean)] = $value;
		}
		$regs = $this->_download($urls);
		#var_dump($regs); die();

		$res = array();
		foreach ($motores as $value) 
		{
			if (isset($regs[$value]))
			{
				$data = $this->$value($ean, $regs[$value]);
				if ($data)
					$res[] = $data;
			}
		}
		return $res;
	}

	/**
	 * Busca los artículos en DILVE
	 * @param  array  $data     Registros a buscar 
	 * @param  boolean $covers   Guarda las portadas
	 * @param  boolean $sinopsis Guarda reseñas
	 * @param  integer $muestra  Cuantos busca de golpe
	 * @return array Los registros que no ha encontrado
	 */
	function buscar_dilve($data, $covers = FALSE, $sinopsis = FALSE, $muestra = CONST_MUESTRA_DEFECTO)
	{
		$this->obj->load->library('Dilve');
		$this->obj->load->library('ISBNEAN');
		$this->obj->load->library('WebSave');
		$this->obj->load->model('catalogo/m_articulo');

		$articulos = array_chunk($data, $muestra);
		$count = 0;
		$count2 = 0;
		$total = count($data);
		$creados = 0;
		$portadas = 0;
		$sinop = 0;
		#$this->color2->info('Buscando en DILVE %_' . count($data));
		foreach ($articulos as $bloque)
		{
			++$count;
			#$this->color2->line("Leyendo bloque %_{$count}/" . count($articulos));
			$codes = array();
			$ids = array();

			foreach ($bloque as $art)
			{
				++$count2;
				$ean = $this->obj->isbnean->to_ean($art['cISBN']);
				if (empty($ean)) $ean = $art['nEAN'];
				$codes[$ean] = $art;
			}
			#var_dump($codes); die();
			#$this->color2->line('Llamando DILVE...');
			$res = $this->obj->dilve->get(array_keys($codes));
			if ($res && count($res) > 0)
			{
				foreach($res as $p)
				{
	 				$isbn = $this->obj->dilve->get_isbn($p);
					$ean = $this->obj->isbnean->to_ean($isbn);
					if (isset($p['MediaFile']['MediaFileTypeCode'])&&($p['MediaFile']['MediaFileTypeCode']=='04'))
					{
						if ($p['MediaFile']['MediaFileLinkTypeCode'] == '06')
						{
							$file = DIR_TEMP_PATH . $p['MediaFile']['MediaFileLink'];
							file_put_contents($file, $this->obj->dilve->media($isbn, $p['MediaFile']['MediaFileLink']));
							$p['MediaFile']['MediaFileLink'] = $file;
						}
						$f = $this->obj->websave->set_cover($ean, $p['MediaFile']['MediaFileLink']);
						if ($p['MediaFile']['MediaFileLinkTypeCode'] == '06')
						{
							#var_dump($file, $f); die();
							unlink($p['MediaFile']['MediaFileLink']);
						}
						if (!empty($f))
						{
			 				#$this->color2->line("Artículo {$isbn} -> DILVE COVER -> %_{$f}");
			 				if ($covers)
			 				{
			 					++$portadas;
								$this->obj->m_articulo->set_portada($codes[$isbn]['nIdLibro'], $f);
								#$this->color2->line("({$portadas} - {$count2}/{$total}):[{$codes[$isbn]['nIdLibro']}] - %gDILVE%_ - %_{$f}");
								unset($codes[$isbn]);
			 				}
			 			}
					}
					$text = null;
					if (!empty($p['Contributor']['BiographicalNote']) && !empty($p['Contributor']['PersonNameInverted']))
					{
						$text[] = $p['Contributor']['PersonNameInverted'];
						$text[] = $p['Contributor']['BiographicalNote'];
					}
					if (isset($p['OtherText']['Text']))
					{
						$text[] = $p['OtherText']['Text'];
						#var_dump($p['OtherText']['Text']); die();
					}
					if (isset($text))
					{
						$f = $this->obj->websave->set_description($ean, implode("\n", $text));
		 				#$this->color2->line("Artículo {$isbn} -> DILVE SINOPSIS -> %_{$f}");
		 				if ($sinopsis)
		 				{
		 					++$sinop;
		 					$text = implode("<br/>", $text);
							$this->obj->m_articulo->set_sinopsis($codes[$isbn]['nIdLibro'], $text);
							#$this->color2->line("({$sinop} - {$count2}/{$total}):[{$codes[$isbn]['nIdLibro']}] - %gDILVE%_ - %_{$text}");
							unset($codes[$isbn]);
		 				}
					}
				}
			}
			/*else
			{
				$this->color2->error('No hay artículos en DILVE');
			}*/
		}
		return $codes;
	}

	/**
	 * Busca los artículos en AMAZON
	 * @param  array  $data     Registros a buscar 
	 * @param  boolean $covers   Guarda las portadas
	 * @param  boolean $sinopsis Guarda reseñas
	 * @param  integer $muestra  Cuantos busca de golpe
	 * @return array Los registros que no ha encontrado
	 */
	function buscar_amazon($data, $covers=FALSE, $sinopsis=FALSE, $muestra=CONST_MUESTRA_DEFECTO)
	{
		$this->obj->load->library('Dilve');
		$this->obj->load->library('ISBNEAN');
		$this->obj->load->library('WebSave');
		$this->obj->load->model('catalogo/m_articulo');

		$articulos = array_chunk($data, $muestra);
		$count = 0;
		$count2 = 0;
		$total = count($data);
		$creados = 0;
		$portadas = 0;
		$sinop = 0;
		#$this->color2->info('Buscando en AMAZON %_' . count($data));
		foreach ($articulos as $bloque)
		{
			++$count;
			#$this->color2->line("Leyendo bloque %_{$count}/" . count($articulos));
			$codes = array();
			$ids = array();

			foreach ($bloque as $art)
			{
				++$count2;
				$ean = $this->obj->isbnean->to_ean($art['cISBN']);
				$codes[$ean] = $art;
			}
			#$this->color2->line('Llamando a AMAZON...');						
			$res = $this->amazon(array_keys($codes));
			if ($res && count($res) > 0)
			{
				foreach($res as $isbn => $p)
				{
					$ean = $this->obj->isbnean->to_ean($isbn);
					if (isset($p['cover']))
					{
						$f = $this->obj->websave->set_cover($ean, $p['cover']);							
		 				#$this->color2->line("Artículo {$ean} -> AMAZON COVER -> %_{$f}");
		 				if ($covers)
		 				{
		 					++$portadas;
							$this->obj->m_articulo->set_portada($codes[$ean]['nIdLibro'], $f);
							#$this->color2->line("({$portadas} - {$count2}/{$total}):[{$codes[$ean]['nIdLibro']}] - %gDILVE%_ - %_{$f}");
							unset($codes[$isbn]);
		 				}
					}
					if (isset($p['description']))
					{
						$f = $this->obj->websave->set_description($ean, $p['description']);
		 				#$this->color2->line("Artículo {$ean} -> AMAZON SINOPSIS -> %_{$f}");
		 				if ($sinopsis)
		 				{
		 					++$sinop;
							$this->obj->m_articulo->set_sinopsis($codes[$ean]['nIdLibro'], $p['description']);
							#$this->color2->line("({$sinop} - {$count2}/{$total}):[{$codes[$ean]['nIdLibro']}] - %gAMAZON%_ - %_{$p['description']}");
							unset($codes[$isbn]);
		 				}
					}
				}
			}
			/*else
			{
				$this->color2->error('No hay artículos en AMAZON');
			}*/
		}
		return $codes;
	}

}

/* End of file searchinternet.php */
/* Location: ./system/libraries/searchinternet.php */
