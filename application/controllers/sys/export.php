<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Exportador
 *
 */
class Export extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Export
	 */
	function __construct()
	{
		parent::__construct(null, null, FALSE);
		$this->load->library('userauth');
	}

	/**
	 * Exporta un contenido HTML
	 * @param string $html Código HTML
	 * @param string $type Tipo de fichero
	 * @return JSON
	 */
	function html($html = null, $type = null)
	{
		$this->load->library('HtmlFile');

		$html = isset($html)?$html:$this->input->get_post('html');
		$type = isset($type)?$type:$this->input->get_post('type');

		$html = htmlentities($html, ENT_NOQUOTES, 'UTF-8'); // Convertir caracteres especiales a entidades
		$html = htmlspecialchars_decode($html, ENT_NOQUOTES); // Dejar <, & y > como estaban
		$html = $this->htmlfile->orientation(ORIENTATION_LANDSCAPE) . $html;

		// Fichero
		$filename = time() . '.html';
		$file = $this->htmlfile->pathfile($filename);
		file_put_contents($file, $html);

		$url = site_url('sys/export/file/' . $filename . '/'.$type);

		$res = array(
			'success' 	=> TRUE,
			'message'	=> $this->lang->line('export-ok'),
			'file'		=> $filename,
			'src'		=> $url
		);

		// Respuesta
		echo $this->out->send($res);
	}

	/**
	 * Exporta una dirección URL
	 * @param string $url Código HTML
	 * @param string $type Tipo de fichero
	 * @return JSON
	 */
	function url($url = null, $type = null)
	{
		$url = isset($url)?$url:$this->input->get_post('url', FALSE);
		$type = isset($type)?$type:$this->input->get_post('type', FALSE);

		$this->out->message('No implementado');
		// Fichero
		/*$filename = time() . '.html';
		 $file = DIR_TEMP_PATH . $filename;
		 file_put_contents($file, $html);*/

		$url = site_url('sys/export/file/' . $filename . '/'.$type);

		$res = array(
			'success' 	=> true,
			'message'	=> $this->lang->line('export-ok'),
			'file'		=> $filename,
			'src'		=> $url
		);

		// Respuesta
		echo $this->out->send($res);
	}

	/**
	 * Exporta un fichero
	 * @param string $file fichero a exportar
	 * @param string $type Tipo de fichero
	 * @return HTTP
	 */
	function file($file = null, $type = null)
	{
		$file= isset($file)?$file:$this->input->get_post('file', FALSE);
		$type = isset($type)?$type:$this->input->get_post('type', FALSE);
		$this->load->library('HtmlFile');

		$pathfile = $this->htmlfile->pathfile($file);
		if (file_exists($pathfile))
		{
			$ext = strtoupper($type);
			$d = pathinfo($pathfile);
			$datos['name'] = $d['filename'] . '.' . strtolower($ext);
			$datos['file'] = $pathfile;
			#die($pathfile);
			if (in_array($ext, $this->config->item('bp.export.extensions')))
			{
				$this->load->view('export/' . $ext, $datos);
			}
			else
			{
				show_error($this->lang->line('export-no-format'));
			}
		}
		else
		{
			show_404($file);
		}
	}

	/**
	 * Obtiene un fichero
	 * @param string $file fichero a exportar
	 * @return HTTP
	 */
	function get($file = null)
	{
		$file = isset($file)?$file:$this->input->get_post('file', null);
		$this->load->library('HtmlFile');
		$pathfile = $this->htmlfile->pathfile($file);

		if (file_exists($pathfile))
		{
			$datos = pathinfo($pathfile);
			$ext = strtoupper($datos['extension']);
			$datos['file'] = $pathfile;
			$datos['name'] = $file;
			if (in_array($ext, $this->config->item('bp.export.extensions')))
			{
				$this->load->view('export/' . $ext, $datos);
			}
			else
			{
				show_error($this->lang->line('export-no-format'));
			}
		}
		else
		{
			show_404($file);
		}
	}

	/**
	 * Vuelca a disco una tabla 
	 * @param string $table Nombre de la tabla
	 * @param string $filter Filtro a aplicar
	 * @param string $output Nombre del directorio donde volcar
	 * @param int $number Número de secuencia para ordenar la tabla
	 * @return MSG
	 */
	function table($table = null, $filter = null, $output = null, $number = null, $fields = null)
	{
		$this->userauth->roleCheck('sys.export');

		$table = isset($table)?$table:$this->input->get_post('table');
		$filter = isset($filter)?$filter:$this->input->get_post('filter');
		$output = isset($output)?$output:$this->input->get_post('output');
		$number = (int) (isset($number)?$number:$this->input->get_post('number'));
		$fields = (isset($fields)?$fields:$this->input->get_post('fields'));

		$this->load->library('ExportTables');
		$this->exporttables->setNumber($number);

		if (!$this->exporttables->table($table, $output, $filter, $fields, $this->db->dbdriver))
		{
			$this->out->error($this->exporttables->get_last_error());
		}
		$this->out->success();
	}

	/**
	 * Exporta un grupo de tabla
	 * @param string $tables Nombre de las tablas separadas por saltos de líneas. El filtro se indica separado por |
	 * @param bool $cache Usar caché
	 * @return DATA, 'src' => URL del fichero ZIP generado
	 */
	function data($tables = null, $cache = null)
	{
		$this->userauth->roleCheck('sys.export');

		$tables = isset($tables)?$tables:$this->input->get_post('tables');
		$cache = isset($cache)?$cache:$this->input->get_post('cache');
		$type = isset($type)?$type:$this->input->get_post('type');

		$cache = !empty($cache)?format_tobool($cache):FALSE;

		if (trim($tables) != '')
		{
			$res = array();
			$call = $this->_data($tables, $cache, $res);
			if ($call !== TRUE)
				$this->out->error($call);
			$this->out->noCache();
			$this->out->send($res);
		}

		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Exporta un grupo de tabla
	 * @param string $tables Nombre de las tablas separadas por saltos de líneas. El filtro se indica separado por |
	 * @param bool $cache Usar caché
	 * @param array $res Resultado
	 * @param string $type Fichero SQL adicional
	 * @return mixed, string: error, bool: TRUE: correcto
	 */
	private function _data($tables, $cache, &$res, $type = null)
	{

		$this->load->library('ExportTables');
		$this->load->library('HtmlFile');

		# Uso de caché?
		$filename = null;
		$cache_filename = DIR_CACHE_PATH . md5($tables) .'.zip';
		if ($cache)
		{
			if (file_exists($cache_filename))
			{
				$filename = time() . '.zip';
				$zipfile = $this->htmlfile->pathfile($filename);
				copy($cache_filename, $zipfile);
			}
		}

		if (!isset($filename))
		{
			$tables = preg_split('/\n/', $tables);
			# Se conecta a si mismo
			require_once(__DIR__ . '/../../../../api/BibliopolaAPI.php');
			$bp = new BibliopolaAPI($this->userauth->get_username(), $this->userauth->get_password());
			#var_dump($bp); die();
			$res = $bp->connect(site_url());

			if (!$bp->is_ok($res))
			{
				return ($bp->get_last_error());
			}

			$output = time();
			$number = 1;

			# Lee tabla a tabla
			foreach ($tables as $table)
			{
				$t = explode('|', $table);
				if (!empty($t[0]))
				{
					$data = array(
						'table' 	=> $t[0],
						'filter'	=> !empty($t[1])?$t[1]:null,
						'fields'	=> !empty($t[2])?$t[2]:null,
						'output' 	=> $output,
						'success'	=> TRUE,
						'number' 	=> $number
						);
					#$this->out->send($data);
					#$bp->debug = 3;
					$res = $bp->action('sys/export/table', $data);
					#die();
					if (!$bp->is_ok($res))
					{
						$this->utils->rrmdir($this->exporttables->get_directory($output));
						return ($bp->get_last_error());
					}
					++$number;
				}
			}
			#die();

			# Genera el ZIP
			$path = $this->exporttables->get_directory($output) . '/';
			# Hay algún UPDATE para esas tablas?
			$updfile = __DIR__ . "/export.{$type}.update.php";

			if (!empty($type) && file_exists($updfile))
			{
				copy($updfile, $path . '000_Update.php');
			}
			$this->load->library('Zip2');
			$filename = $output . '.zip';
			$zipfile = $this->htmlfile->pathfile($filename);
			if ($this->zip2->open($zipfile, ZIPARCHIVE::OVERWRITE) === TRUE)
			{
				$this->zip2->addDirectory($path, $path);
				$this->zip2->close();
			}
			else
			{				
				$this->utils->rrmdir($this->exporttables->get_directory($output));
				return sprintf($this->lang->line('zip-file-error'), $zipfile);
			}
			$this->utils->rrmdir($this->exporttables->get_directory($output));

			# Lo guarda siempre en caché, por si acaso
			copy($zipfile, $cache_filename);
		}

		$res = array(
			'success' 	=> TRUE,
			'message'	=> $this->lang->line('export-ok'),
			'file'		=> $filename,
			'src'		=> $this->htmlfile->url($filename)
		);
		return TRUE;
	}

	private function _procesar_ventas(&$ventas)
	{
		# Procesar las facturas de TPV, para no volver a enviarlas
		foreach($ventas['lista2'] as $factura)
		{
			$this->m_factura2->cerrar2($factura['nIdFactura']);					
		}

		# Procesa las de FACTURACION 
		$lista = $this->m_factura->get(null, null, null, null, 'nIdEstado=' . FACTURA_STATUS_A_PROCESAR);
		foreach($ventas['lista'] as $factura)
		{
			$this->m_factura->cerrar2($factura['nIdFactura']);
		}

		# Marca los albaranes y los clientes
		$this->configurator->set_system('bp.import.last.cliente', $ventas['maxcliente']);
		$this->configurator->set_system('bp.import.last.albaran', $ventas['maxalbaran']);

		return TRUE;
	}

	/**
	 * Envía las ventas a la central
	 * @param int $confirm Si es 1 actualiza los datos, sino pregunta si está seguro
	 * @return MSG/FORM
	 */
	function sendventas($confirm = null)
	{
		$this->userauth->roleCheck('sys.export');

		if ($this->config->item('bp.export.database.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-export-catalogo-allow'));
		}

		$confirm = isset($confirm)?$confirm:$this->input->get_post('confirm');

		if ($confirm == 1)
		{
			set_time_limit(0);
			
			# Las envía al remoto
			$username = $this->config->item('bp.import.remote.username');
			$server = $this->config->item('bp.import.remote.server');
			$password = $this->config->item('bp.import.remote.password');
			require_once(__DIR__ . '/../../../../api/BibliopolaAPI.php');
			$bp = new BibliopolaAPI($username, $password);
			#$bp->debug = 3;

			$res = $bp->connect($server);		
			if (!$bp->is_ok($res))
			{
				$this->out->error(sprintf($this->lang->line('no-import-remote-error'), $bp->get_last_error()));
			}

			# Envía las facturas
			$ventas = $this->_ventas();
			$data['data'] =  $ventas['data'];
			$res = $bp->action('sys/import/ventas_remoto', $data);

			#var_dump($res); die();
			if ($bp->is_ok($res))
			{
				$this->_procesar_ventas($ventas);

				$this->out->dialog($this->lang->line('Enviar Ventas'), sprintf($this->lang->line('import-remote-ok'), $count, $res['message']));
			}
			$this->out->error(sprintf($this->lang->line('no-import-remote-error'), $bp->get_last_error()));
		}
		$data = array(
			'icon' => 'iconoExportarTab',
			'title' => $this->lang->line('Enviar Ventas'),
			'text' => $this->lang->line('export-ventas-query'),
			'url' => site_url('sys/export/sendventas/1')
			);

		$this->_show_js('sys.export', 'sys/basequery.js', $data);
	}

	/**
	 * Exporta el catálogo para el inventario
	 * @return URL
	 */
	function catalogoinventario()
	{
		$this->userauth->roleCheck('sys.export');

		if ($this->config->item('bp.export.general.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-export-catalogo-allow'));
		}
		$cache = FALSE;

		$tables =  file_get_contents(__DIR__ . '/export.catalogo.inventario.txt');
		$res = array();
		$call = $this->_data($tables, $cache, $res);
		if ($call !== TRUE) $this->out->error($call);
		$old = $this->htmlfile->pathfile($res['file']);
		$new = $this->lang->line('catalogoinventario') . '-' . $res['file'];
		rename($old, $this->htmlfile->pathfile($new));
		$this->out->redirect($this->htmlfile->url($new));
	}

	/**
	 * Exporta desde el servidor remoto el catálogo perteneciente a la sección indicada. Usando la caché
	 * Si hay más de una sección se debe separar por comas (,)
	 * Si se quieren todas hay que indicar la sección -2
	 * @param string $seccion Secciones
	 * @return FILE
	 */
	function catalogo($seccion = null)
	{
		$this->userauth->roleCheck('sys.export');

		if ($this->config->item('bp.export.general.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-export-catalogo-allow'));
		}

		$cache = FALSE;

		$seccion = isset($seccion)?$seccion:$this->input->get_post('seccion');

		if (!empty($seccion))
		{
			$tables =  file_get_contents(__DIR__ . (($seccion == -2)?'/export.catalogo.todo.txt':'/export.catalogo.txt'));
			$tables =  str_replace('%id%', $seccion, $tables);
			
			$res = array();
			$call = $this->_data($tables, $cache, $res, 'catalogo');
			if ($call !== TRUE) $this->out->error($call);
			$old = $this->htmlfile->pathfile($res['file']);
			$name = ($seccion == -2)?'':(str_replace(',', '-', $seccion) . '-');
			$new = $this->lang->line('catalogo') . '-' . $name . $res['file'];
			rename($old, $this->htmlfile->pathfile($new));
			$this->out->redirect($this->htmlfile->url($new));
		}
		$data['cache'] = $cache;
		$data['url'] = site_url('sys/export/catalogo');
		$data['title'] = $this->lang->line('Exportar Catálogo');
		$this->_show_js('sys.export', 'sys/seccion.js', $data);
	}

	/**
	 * Exporta desde el servidor remoto las ventas perteneciente a la serie indicada. 
	 * Si hay más de una serie se debe separar por comas (,)
	 * @param bool $cache Usar caché
	 * @param string $serie Series
	 * @return FILE
	 */
	function ventas($seccion = null)
	{
		$this->userauth->roleCheck('sys.export');

		if ($this->config->item('bp.export.general.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-export-catalogo-allow'));
		}

		$cache = FALSE;

		$serie = isset($serie)?$serie:$this->input->get_post('serie');

		if (!empty($serie))
		{
			# Genera la importación
			$tables =  file_get_contents(__DIR__ . '/export.ventas.txt');
			$tables =  str_replace('%id%', $serie, $tables);
			
			$res = array();
			$call = $this->_data($tables, $cache, $res, 'ventas');
			if ($call !== TRUE) $this->out->error($call);
			$old = $this->htmlfile->pathfile($res['file']);
			$name = str_replace(',', '-', $serie) . '-';
			$new = $this->lang->line('ventas') . '-' . $name . $res['file'];
			rename($old, $this->htmlfile->pathfile($new));
			$this->out->redirect($this->htmlfile->url($new));
		}
		$data['cache'] = $cache;
		$data['url'] = site_url('sys/export/ventas');
		$data['title'] = $this->lang->line('Exportar Ventas');
		$this->_show_js('sys.export', 'sys/serie.js', $data);
	}

	/**
	 * Exporta desde el servidor remoto la base de datos base del sistema
	 * Si hay más de una sección se debe separar por  comas (,)
	 * @param int $confirm Si es 1 actualiza los datos, sino pregunta si está seguro
	 * @return MSG/FORM
	 */
	function base($confirm = null)
	{
		$this->userauth->roleCheck('sys.export');

		if ($this->config->item('bp.export.general.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-export-catalogo-allow'));
		}

		$cache = FALSE;

		$confirm = isset($confirm)?$confirm:$this->input->get_post('confirm');

		if ($confirm == 1)
		{
			# Genera la importación
			$tables =  file_get_contents(__DIR__ . '/export.base.txt');
			
			$res = array();
			$call = $this->_data($tables, $cache, $res, 'base');
			if ($call !== TRUE) $this->out->error($call);
			$old = $this->htmlfile->pathfile($res['file']);
			$new = $this->lang->line('base') . '-' . $res['file'];
			rename($old, $this->htmlfile->pathfile($new));
			$this->out->redirect($this->htmlfile->url($new));
		}

		$data = array(
			'icon' => 'iconoImportarTab',
			'title' => $this->lang->line('Exportar Base'),
			'text' => $this->lang->line('exportar-base-query'),
			'url' => site_url('sys/export/base/1'),
			'params' => '{cache: ' .($cache?1:0) . '}'
			);

		$this->_show_js('sys.export', 'sys/basequery.js', $data);
	}

	/**
	 * Envía las ventas a la central
	 * @param int $confirm Si es 1 actualiza los datos, sino pregunta si está seguro
	 * @return MSG/FORM
	 */
	function ventasfile($confirm = null)
	{
		$this->userauth->roleCheck('sys.export');

		if ($this->config->item('bp.export.database.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-export-catalogo-allow'));
		}

		$confirm = isset($confirm)?$confirm:$this->input->get_post('confirm');

		if ($confirm == 1)
		{
			set_time_limit(0);

			$ventas = $this->_ventas();
			$this->_procesar_ventas($ventas);
			$this->load->library('HtmlFile');
			$filename = $this->lang->line('Ventas') . '-' . str_replace('/', '-', format_date(time())) . '.dat';
			$file = $this->htmlfile->pathfile($filename);
			file_put_contents($file, $ventas['data']);
			$this->out->redirect($this->htmlfile->url($filename));
		}

		$data = array(
			'icon' => 'iconoExportarFicheroTab',
			'title' => $this->lang->line('Ventas a archivo'),
			'text' => $this->lang->line('export-ventas-file-query'),
			'url' => site_url('sys/export/ventasfile/1')
			);

		$this->_show_js('sys.export', 'sys/basequery.js', $data);
	}

	/**
	 * Envía las ventas a la central
	 * @param int $confirm Si es 1 actualiza los datos, sino pregunta si está seguro
	 * @return MSG/FORM
	 */
	private function _ventas()
	{
		set_time_limit(0);
		
		# Lee FACTURAS
		$this->load->model('ventas/m_factura');
		$this->load->model('ventas/m_factura2');
		$lista2 = $this->m_factura2->get(null, null, null, null, 'nIdEstado=' . FACTURA_STATUS_A_PROCESAR);
		$lista = $this->m_factura->get(null, null, null, null, 'nIdEstado=' . FACTURA_STATUS_A_PROCESAR);
		$count = 0;
		$facturas = array();
		foreach($lista as $factura)
		{
			$facturas[] = $this->m_factura->load($factura['nIdFactura'], array('lineas', 'modospago'));
			++$count;
		}
		foreach($lista2 as $factura)
		{
			$facturas[] = $this->m_factura2->load($factura['nIdFactura'], array('lineas', 'modospago'));
			++$count;
		}
		if ($count == 0)
			$this->out->error($this->lang->line('export-no-ventas'));

		# Lee ALBARANES SALIDA
		$this->load->model('ventas/m_albaransalida');
		$this->load->library('Configurator');
		$lastid = $this->configurator->system('bp.import.last.albaran');
		$maxalbaran = 0;
		if (is_numeric($lastid))
		{
			$lista3 = $this->m_albaransalida->get(null, null, null, null, 'nIdFactura IS NULL AND nIdEstado = 2 AND nIdAlbaran > ' . $lastid);
			$albaranes = array();

			foreach ($lista3 as $k => $value)
			{
				$albaranes[] = $this->m_albaransalida->load($value['nIdAlbaran'], 'lineas');
				$maxalbaran = max($maxalbaran, $value['nIdAlbaran']);
			}
		}

		# Lee CLIENTES
		$this->load->model('clientes/m_cliente');
		$this->load->model('clientes/m_direccioncliente');
		$this->load->model('clientes/m_email');
		$this->load->model('clientes/m_telefono');
		$lastid = $this->configurator->system('bp.import.last.cliente');
		$maxcliente = 0;
		#var_dump($lastid);
		if (is_numeric($lastid))
		{
			$lista4 = $this->m_cliente->get(null, null, null, null, 'nIdCliente > ' . $lastid);
			$clientes = array();

			foreach ($lista4 as $k => $value)
			{
				$cliente = $this->m_cliente->load($value['nIdCliente'], TRUE);
				$cliente['direcciones'] = $this->m_direccioncliente->get(null, null, null, null, 'nIdCliente='. $value['nIdCliente']);
				$cliente['emails'] = $this->m_email->get(null, null, null, null, 'nIdCliente='. $value['nIdCliente']);
				$cliente['telefonos'] = $this->m_telefono->get(null, null, null, null, 'nIdCliente='. $value['nIdCliente']);
				$clientes[] = $cliente;
				$maxcliente = max($maxcliente, $value['nIdCliente']);
			}
		}
		$datos['facturas'] = $facturas;
		$datos['clientes'] = $clientes;
		$datos['albaranes'] = $albaranes;
		#var_dump($datos); die();

		return array(
			'data' 			=> base64_encode(gzcompress(serialize($datos),9)),
			'lista' 		=> $lista,
			'lista2'		=> $lista2,
			'maxalbaran'	=> $maxalbaran,
			'maxcliente'	=> $maxcliente
		);
	}
}

/* End of file export.php */
/* Location: ./system/application/controllers/sys/export.php */