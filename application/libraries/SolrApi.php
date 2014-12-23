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

define('SORL_XML_INIT', "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<add>\n");
define('SORL_XML_DONE', "</add>\n");

/**
 * Clase de acceso al servidor SOLR
 * @author alexl
 *
 */
class SolrApi {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;
	/**
	 * URL del query
	 * @var string
	 */
	private $url_query;
	/**
	 * URL del Update
	 * @var string
	 */
	private $url_update;

	/**
	 * Constructor
	 * @return SolrApi
	 */
	function __construct()
	{
		$this->obj =& get_instance();

		$this->url_query = $this->obj->config->item('bp.solr.query');
		$this->url_update = $this->obj->config->item('bp.solr.update');
	}

	/**
	 * Realiza una llamada al motor de búsqueda de texto
	 * @param string $query Consulta en formato lucene query (http://http://lucene.apache.org/java/2_4_0/queryparsersyntax.html)
	 * @param string $sort Orden del resultado
	 * @param string $qop AND/OR en las búsquedas
	 * @param int $start Primer registro
	 * @param int $rows Número de registro
	 * @return array
	 */
	function query($query, $sort = null, $qop = 'AND', $start = null, $rows = null)
	{
		$q = '&q=' . urlencode($query) . "&q.op={$qop}";

		if (isset($start))
		{
			$q .= "&start={$start}";
		}
		if (isset($rows))
		{
			$q .="&rows={$rows}";
		}
		if (isset($sort))
		{
			$q .= '&sort=' . urlencode($sort);
		}

		$url = $this->url_query . $q;

		/*if (!file_exists($url))
		 {
			throw new Exception('Servicio de búsqueda parado');
			}*/
		//print "SOLRAPI: QUERY: {$url}\n";
		$serializedResult = file_get_contents($url);
		$result = unserialize($serializedResult);
		return $result;
	}
	
	/**
	 * Envía un documento al servidor
	 * @param string $xml Documento en formato XML
	 * @param bool $file TRUE: Indica si $xml es fichero, FALSE: es una fuente XML
	 * @return string
	 */
	function update($xml, $file = FALSE)
	{		
		if ($file) 	$xml = file_get_contents($xml);
		
		$header = array("Content-type:text/xml; charset=utf-8");

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->url_update);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

		//print "Enviando datos a SOLR en {$url}\n";
		$data = curl_exec($ch);

		if (curl_errno($ch))
		{
			return "curl_error:" . curl_error($ch);
		}
		else
		{
			curl_close($ch);
			$xml = new SimpleXMLElement($data);
			if (isset($xml->lst))
			{
				$result = '';
				foreach($xml->lst->int as $d)
				{
					$result .= (string)$d['name'] . ' = ' . $d[0] . "\n";
				}
				unset($xml);
				return $result;
			}
			unset($xml);
		}
		
		return $data;
	}

	/**
	 * Realiza un commit de los últimos cambios
	 */
	function commit()
	{
		$data = '<commit/>';
		return $this->update($data);
	}

	/**
	 * Limpieza de los datos para el XML
	 * @param string $input Cadena a limpiar
	 * @return string
	 */
	private function _clean($input)
	{
		if (!isset($input)) return null;
		$output = '';
		$input = htmlspecialchars($input);
		for ($i = 0; $i < strlen($input); $i++)
		{
			if (ord($input[$i])>= 32 && ord($input[$i]) <= 255)
			{
				$output .= $input[$i];
			}
		}
		return $output;
	}


	/**
	 * Conviert de array a XML
	 * @param array $data Datos
	 * @return XML
	 */
	private function _data_to_xml($data)
	{
		$xmldoc ="<doc>\n";
		foreach($data as $k => $v)
		{
			// Es un array? Crea varios
			if (is_array($v))
			{
				foreach($v as $v2)
				{
					$v2 = $this->_clean($v2);
					if (isset($v2) && ($v2 != ''))
					{
						$xmldoc .= "<field name=\"{$k}\">{$v2}</field>\n";
					}
				}
			}
			// No es un array
			else
			{
				$v = $this->_clean($v);
				if (isset($v) && ($v != ''))
				{
					$xmldoc .= "<field name=\"{$k}\">{$v}</field>\n";
				}
			}
		}
		$xmldoc .= "</doc>\n";
		return $xmldoc;
	}

	/**
	 * Crea un documento en el formato SOLR
	 * @param array $data Datos del documento
	 * @return XML
	 */
	function create_document($data)
	{
		if (count($data)==0) return 0;

		$xmldoc = SORL_XML_INIT;

		foreach ($data as $ar)
		{
			$xmldoc .= $this->_data_to_xml($ar);
		}

		$xmldoc .= SORL_XML_DONE;

		return $xmldoc;
	}


	/**
	 * Creación de documentos de autores
	 */
	function autores()
	{
		$name = $this->obj->getVar('name', null);
		if (!tep_not_null($name)) $name = 'aut';

		// Datos
		$sql = 'SELECT
			nIdAutor id,
			nIdAutor nIdLibro,
			cNombre cTitulo,
			cApellido cISBN
		FROM Cat_Autores (NOLOCK)';

		$this->_create_docs($name, 'A', $sql);
	}

}

/* End of file solrapi.php */
/* Location: ./system/libraries/solrapi.php */
