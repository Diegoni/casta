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

define('DEFAULT_REPORT', 	'default');
define('PATH_REPORTS', 		'reports');

/**
 * Envio de datos
 * @author alexl
 *
 */
class Reports {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Directorio donde se guardan los reports
	 * @var string
	 */
	private $path;

	/**
	 * Constructor
	 * @return Reports
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		$obj =& get_instance();

		$this->path = APPPATH . 'views' . DS . PATH_REPORTS . DS ;

		log_message('debug', 'Report Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Devuelve el nombre del fichero XML que contiene la definición de los reports. Si existe.
	 * @param array $parts Controlador
	 * @return FALSE: no hay fichero, string: nombre del fichero
	 */
	protected function file_reports($parts)
	{
		$file = $this->path . $parts[0] . DS . $parts[1] . '.xml';
		return (file_exists($file))?$file:FALSE;
	}

	/**
	 * Devuelve el nombre del report si se ha indicado en las vistas
	 * @param array $parts path del report
	 * @return FALSE: no hay vista, string: nombre de la vista
	 */
	protected function file_single($parts)
	{
		$file = $this->path . $parts[0] . '_' . $parts[1] . '.php';
		return (file_exists($file))? PATH_REPORTS . '/' . $parts[0] . '_' . $parts[1]:FALSE;
	}

	/**
	 * Path de las vistas de los reports
	 * @param array $parts Controlador
	 * @return string
	 */
	protected function view_path($parts)
	{
		return PATH_REPORTS .'/' . $parts[0] . '/';
	}

	/**
	 * Crea la definición de un report para el listado
	 * @param string $text Descripción
	 * @param string $file Vista
	 * @param string $order Orden por defecto
	 * @param string $id Identificador del report
	 */
	protected function get_report($text, $file, $order, $id)
	{
		return array(
				'text' 	=> $text,
				'order'	=> $order,
			 	'file'	=> $file,
				'id'	=> $id
		);
	}

	/**
	 * Vista por defecto
	 * @return string
	 */
	function default_view()
	{
		return PATH_REPORTS . '/' . DEFAULT_REPORT;
	}

	/**
	 * Devuelve el listado de informes del tipo indicado
	 * @param string $type Grupo de informes
	 * @param string $id Nombre del report para
	 * @return array: Listado si no se indica Id, string: Nombre del fichero si se indica id
	 */
	function get_list($type, $id = null)
	{
		$parts = preg_split('/\./', $type);
		//Hay un directorio?
		$file = $this->file_reports($parts);
		if ($file === FALSE)
		{
			// Hay un archivo?
			$file = $this->file_single($parts);
			if ($file === FALSE)
			{
				return FALSE;
			}
			return $file;
		}

		$xml = new SimpleXMLElement($file, null, TRUE);

		if (!isset($xml->report)) return null;

		$path = $this->view_path($parts);

		$reports = array();
		$default = FALSE;
		foreach ($xml->report as $report)
		{
			//var_dump($report);
			$data = $this->get_report($this->obj->lang->line((string)$report['name']),
			$path .(string)$report['file'],	(string)$report['order'], (string)$report['name']);

			if (isset($id) && ($id == (string)$report['name'] ))
			{
				// Parámetros
				if (isset($report->param))
				{
					$params = array();
					foreach ($report->param as $param)
					{
						$params[(string)$param['name']] = (string)$param['value'];
					}
					$data['params'] = $params;
				}
				if (isset($report['default']))
				{
					$data['default'] = TRUE;
					$default = TRUE;
				}

				return $data;
			}
			$reports[] = $data;
		}
		if (!$default && count($reports) > 0) $reports[0]['default'] = TRUE;
		return $reports;
	}

	/**
	 * Devuelve el nombre del archivo de la vista
	 * @param string $type Grupo de informes
	 * @param string $id Nombre del report para
	 * @return unknown_type
	 */
	function get($type, $id)
	{
		return $this->get_list($type, $id);
	}
}

/* End of file Reports.php */
/* Location: ./system/libraries/reports.php */