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
 * Etiquetas de direcciones
 * @author alexl
 *
 */
class Etiquetas {
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
	 * @return Etiquetas
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		log_message('debug', 'Etiquetas Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Reemplaza las variables de la plantilla de la reclamación con los valores indicados
	 * @param string $texto Plantilla
	 * @param array $data Variables - valores
	 */
	private function replaces(&$texto, &$data)
	{
		foreach($data as $nombre => $valor)
		{
			if (!is_object($valor)) 
			{
				if (is_array($valor))
				{
					$this->replaces($texto, $valor);
				}
				else
				{
					$texto = str_replace('%' . $nombre . '%', $valor, $texto);
				}
			}
		}
	}

	/**
	 * Genera la etiqueta 
	 * @param array $dir Dirección
	 * @return string texto formateado
	 */
	function etiqueta($dir, $tipo)
	{
		$this->obj->load->model('etiquetas/m_etiquetaformato');
		$formato = $this->obj->m_etiquetaformato->load($tipo);
		$texto = $formato['tFormato'];
		$this->replaces($texto, $dir);
		return $texto;
	}

	/**
	 * Encola una etiqueta
	 * @param array $dir Dirección
	 * @return string texto formateado
	 */
	function encolar($dir, $grupo)
	{
		$data = array(
			'cDescripcion' 	=> $grupo,
			'cEtiqueta' 	=> serialize($dir),
			);
		$this->obj->load->model('etiquetas/m_etiqueta');
		if (!$this->obj->m_etiqueta->insert($data))
		{
			$this->_error = $this->obj->m_etiqueta->error_message();
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Genera una hoja de etiquetas
	 * @param mixed $etq Texto a imprimir o array de textos
	 * @param int $id Id del formato de la hoja
	 * @param int $row Fila inicial
	 * @param int $column Columna inicial
	 * @return string URL del PDF generado
	 */
	function paper($etq, $id, $row, $column)
	{
		$this->obj->load->model('etiquetas/m_etiquetatipo');
		$formato = $this->obj->m_etiquetatipo->load($id);
		(is_array($etq))?$data['etiquetas']=$etq:$data['etiquetas'][] = $etq;
		$data['config'] = $formato;
		$data['config']['fPaddingLeft'] = 0;
		$data['config']['fPaddingTop'] = 0;
		$data['row'] = $row;
		$data['column'] = $column;
		$body = $this->obj->load->view('etiquetas/labels', $data, TRUE);
		#echo $body; die();
		$this->obj->load->library('HtmlFile');
		if (($formato['nColumns'] * ($formato['fWidth'] + $formato['fHorizontal']) + $formato['fLeft']) >
			($formato['nRows'] * ($formato['fHeight'] + $formato['fVertical']) + $formato['fTop']))
		{
			$body = $this->obj->htmlfile->orientation(ORIENTATION_LANDSCAPE) . $body;
		}
		$filename = $this->obj->htmlfile->create($body, 'Labels');
		$this->obj->load->library('PdfLib');
		$pdf = $this->obj->pdflib->create($this->obj->htmlfile->pathfile($filename), null, null, null, FALSE, FALSE);
		$url = $this->obj->htmlfile->url($pdf);
		return $url;
	}

	/**
	 * Imprime una etiqueta directa a la impresora de etiquetas
	 * @param  string $html  Texto
	 * @param  string $title Título
	 * @return string Mensaje del servidor de impresión 
	 */
	function print_direct($html, $title = 'direct')
	{
		$this->obj->load->library('Configurator');
		$host = $this->obj->configurator->user('bp.teixellserver.host');
		$port = $this->obj->configurator->user('bp.teixellserver.port');
		$html = utf8_encode(urlencode(($html)));
		$title = utf8_encode(urlencode($title));
		$url = "http://{$host}:{$port}?cmd=label&text={$html}%title={$title}";
		#var_dump($url);
		$err = error_reporting();
		error_reporting(E_ERROR);
		$result = file_get_contents($url);
		error_reporting($err);
		return $result;
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
/* End of file etiquetas.php */
/* Location: ./system/libraries/etiquetas.php */