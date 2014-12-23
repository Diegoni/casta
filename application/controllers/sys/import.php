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
 * Importador de la base de datos para sincronizar Servidores de ventas
 *
 */
class Import extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Import
	 */
	function __construct()
	{
		parent::__construct(null, null, FALSE);
		$this->load->library('userauth');
	}

	/**
	 * Importa desde el servidor remoto el catálogo perteneciente a la sección indicada. 
	 * Si hay más de una sección se debe separar por ,
	 * @param string $seccion Secciones
	 * @param bool $cache Usar caché
	 * @return MSG
	 */
	private function _import($tables, $cache = FALSE, $type = null)
	{
		$username = $this->config->item('bp.import.remote.username');
		$server = $this->config->item('bp.import.remote.server');
		$password = $this->config->item('bp.import.remote.password');

		if (empty($username) || empty($server) || empty($password))
		{
			$this->out->error($this->lang->line('no-import-remote-configure'));		
		}

		# Puede ocupar tiempo
		set_time_limit(0);

		require_once(__DIR__ . '/../../../../api/BibliopolaAPI.php');
		$bp = new BibliopolaAPI($username, $password);
		#$bp->debug = 3;
		$res = $bp->connect($server);

		if (!$bp->is_ok($res))
		{
			$this->out->error(sprintf($this->lang->line('no-import-remote-error'), $bp->get_last_error()));
		}
		$data['tables'] = $tables;
		$data['cache'] = $cache;
		$data['type'] = $type;
		$res = $bp->action('sys/export/data', $data);
		if ($bp->is_ok($res))
		{
			#var_dump($res); die();
			# Descarga el ZIP
			$this->load->library('HtmlFile');
			$output = time();
			$filename = $output . '.zip';
			$zipfile = $this->htmlfile->pathfile($filename);
			file_put_contents($zipfile, file_get_contents($res['src']));
			#var_dump($zipfile); die();

			# Lo importa
			$this->load->library('ExportTables');
			if (!$this->exporttables->import_zip($zipfile))
			{
				unlink($zipfile);
				$this->out->error($this->exporttables->get_last_error());
			}
			unlink($zipfile);
			return TRUE;
		}
		else
		{
			$this->out->error(sprintf($this->lang->line('no-import-remote-error'), $bp->get_last_error()));
		}
	}

	/**
	 * Importa desde el servidor remoto el catálogo perteneciente a la sección indicada. 
	 * Si hay más de una sección se debe separar por comas (,)
	 * Si se quieren todas hay que indicar la sección -2
	 * @param bool $cache Usar caché
	 * @param string $seccion Secciones
	 * @return MSG
	 */
	function catalogo($cache = null, $seccion = null)
	{
		$this->userauth->roleCheck('sys.import');

		if ($this->config->item('bp.import.database.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-import-catalogo-allow'));
		}

		$seccion = isset($seccion)?$seccion:$this->input->get_post('seccion');
		$cache = isset($cache)?$cache:$this->input->get_post('cache');
		$cache = !empty($cache)?format_tobool($cache):FALSE;
		if (!empty($seccion))
		{
			# Genera la importación
			$tables =  file_get_contents(__DIR__ . (($seccion == -2)?'/export.catalogo.todo.txt':'/export.catalogo.txt'));
			$tables =  str_replace('%id%', $seccion, $tables);
			
			$this->_import($tables, $cache, 'catalogo');

			$this->out->dialog($this->lang->line('Importar Catalogo'), $this->lang->line('import-catalogo-ok'));
		}
		$data['cache'] = $cache;
		$this->_show_js('sys.import', 'sys/seccion.js', $data);
	}

	/**
	 * Importa desde el servidor remoto las ventas perteneciente a la serie indicada. 
	 * Si hay más de una serie se debe separar por comas (,)
	 * @param bool $cache Usar caché
	 * @param string $serie Series
	 * @return MSG
	 */
	function ventas($cache = null, $serie = null)
	{
		$this->userauth->roleCheck('sys.import');

		if ($this->config->item('bp.import.database.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-import-catalogo-allow'));
		}

		$this->load->model('ventas/m_factura');
		$this->load->model('ventas/m_factura2');
		$lista2 = $this->m_factura2->get(null, 1, null, null, 'nIdEstado=' . FACTURA_STATUS_A_PROCESAR);
		$lista = $this->m_factura->get(null, 1, null, null, 'nIdEstado=' . FACTURA_STATUS_A_PROCESAR);

		if (count($lista) + count($lista2) > 0)
		{
			$this->out->error($this->lang->line('no-import-ventas-exist'));
		}

		$serie = isset($serie)?$serie:$this->input->get_post('serie');
		$cache = isset($cache)?$cache:$this->input->get_post('cache');
		$cache = !empty($cache)?format_tobool($cache):FALSE;

		if (!empty($serie))
		{
			# Genera la importación
			$tables =  file_get_contents(__DIR__ . '/export.ventas.txt');
			$tables =  str_replace('%id%', $serie, $tables);

			$this->_import($tables, $cache, 'ventas');
			# Debe almacenar el último número de cliente, para saber cuales son los nuevos
			$this->load->model('clientes/m_cliente');
			$this->load->library('Configurator');
			$this->load->model('ventas/m_albaransalida');
			$this->configurator->set_system('bp.import.last.cliente', $this->m_cliente->get_last());
			$this->configurator->set_system('bp.import.last.albaran', $this->m_albaransalida->get_last());
			$this->load->model('ventas/m_serie');
			$num = $this->m_serie->set_last($serie);
			if ($num === FALSE)
				$this->out->error($this->m_serie->error_message());

			$this->out->dialog($this->lang->line('Importar Ventas'), sprintf($this->lang->line('import-ventas-ok'), $num));
		}
		$this->_show_js('sys.import', 'sys/serie.js');
	}

	/**
	 * Importa desde el servidor remoto la base de datos base del sistema
	 * Si hay más de una sección se debe separar por  comas (,)
	 * @param bool $cache Usar caché
	 * @param int $confirm Si es 1 actualiza los datos, sino pregunta si está seguro
	 * @return MSG/FORM
	 */
	function base($confirm = null, $cache = null)
	{
		$this->userauth->roleCheck('sys.import');

		if ($this->config->item('bp.import.database.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-import-catalogo-allow'));
		}

		$confirm = isset($confirm)?$confirm:$this->input->get_post('confirm');
		$cache = isset($cache)?$cache:$this->input->get_post('cache');
		$cache = !empty($cache)?format_tobool($cache):FALSE;

		if ($confirm == 1)
		{
			# Genera la importación
			$tables =  file_get_contents(__DIR__ . '/export.base.txt');

			$this->_import($tables, $cache, 'base');
			# Debe almacenar el último número de factura, cliente y dirección, por si se añaden,
			# Para hacer el traspaso de las ventas

			$this->out->dialog($this->lang->line('Importar Base'), $this->lang->line('import-base-ok'));
		}
		$data = array(
			'icon' => 'iconoImportarTab',
			'title' => $this->lang->line('Importar Base'),
			'text' => $this->lang->line('importar-base-query'),
			'url' => site_url('sys/import/base/1'),
			'params' => '{cache: ' .($cache?1:0) . '}'
			);

		$this->_show_js('sys.import', 'sys/basequery.js', $data);
	}

	/**
	 * Importa desde el servidor remoto la base de datos de escuelas
	 * Si hay más de una sección se debe separar por  comas (,)
	 * @param bool $cache Usar caché
	 * @param int $confirm Si es 1 actualiza los datos, sino pregunta si está seguro
	 * @return MSG/FORM
	 */
	function eoi($confirm = null, $cache = null)
	{
		$this->userauth->roleCheck('sys.import');

		if ($this->config->item('bp.import.database.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-import-catalogo-allow'));
		}

		$confirm = isset($confirm)?$confirm:$this->input->get_post('confirm');
		$cache = isset($cache)?$cache:$this->input->get_post('cache');
		$cache = !empty($cache)?format_tobool($cache):FALSE;

		if ($confirm == 1)
		{
			# Genera la importación
			$tables =  file_get_contents(__DIR__ . '/export.eoi.txt');

			$this->_import($tables, $cache, 'eoi');
			# Debe almacenar el último número de factura, cliente y dirección, por si se añaden,
			# Para hacer el traspaso de las ventas

			$this->out->dialog($this->lang->line('Importar Escuelas'), $this->lang->line('import-eoi-ok'));
		}
		$data = array(
			'icon' => 'iconoImportarTab',
			'title' => $this->lang->line('Importar Escuelas'),
			'text' => $this->lang->line('importar-eoi-query'),
			'url' => site_url('sys/import/eoi/1'),
			'params' => '{cache: ' .($cache?1:0) . '}'
			);

		$this->_show_js('sys.import', 'sys/basequery.js', $data);
	}

	/**
	 * Importa desde el servidor remoto el catálogo perteneciente a la sección indicada. 
	 * Si hay más de una sección se debe separar por comas (,)
	 * Si se quieren todas hay que indicar la sección -2
	 * @param bool $cache Usar caché
	 * @param string $seccion Secciones
	 * @return MSG
	 */
	function portadas($seccion = null)
	{
		$this->userauth->roleCheck('sys.import');

		if ($this->config->item('bp.import.database.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-import-catalogo-allow'));
		}

		$username = $this->config->item('bp.import.remote.username');
		$server = $this->config->item('bp.import.remote.server');
		$password = $this->config->item('bp.import.remote.password');

		if (empty($username) || empty($server) || empty($password))
		{
			$this->out->error($this->lang->line('no-import-remote-configure'));		
		}

		$seccion = isset($seccion)?$seccion:$this->input->get_post('seccion');
		$cache = isset($cache)?$cache:$this->input->get_post('cache');
		$cache = !empty($cache)?format_tobool($cache):FALSE;
		if (!empty($seccion))
		{

			# Puede ocupar tiempo
			set_time_limit(0);

			require_once(__DIR__ . '/../../../../api/BibliopolaAPI.php');
			$bp = new BibliopolaAPI($username, $password);
			#$bp->debug = 3;
			$res = $bp->connect($server);

			if (!$bp->is_ok($res))
			{
				$this->out->error(sprintf($this->lang->line('no-import-remote-error'), $bp->get_last_error()));
			}
			$data['id'] = $seccion;
			$res = $bp->action('catalogo/articulo/portadas', $data);
			if ($bp->is_ok($res))
			{
				$this->load->model('catalogo/m_articulo');
				$count = 0;
				foreach($res['value_data'] as $reg)
				{
					$this->db->flush_cache();
					$fecha1 = format_mssql_date($reg['dAct']);
					$this->db->select('nIdFoto')
					->from('Fotos')
					->where('nIdRegistro='. $reg['nIdRegistro'])
					->where($this->db->date_field('dAct') . '=' . $reg['dAct']);
					$query = $this->db->get();
					$data = $query->row_array();
					if (count($data) == 0)
					{
						$url = $bp->get_url('catalogo/articulo/cover/' . $reg['nIdRegistro']);
						if ($this->m_articulo->set_portada($reg['nIdRegistro'], $url, null, $reg['dAct']))
							++$count;
					}
				}
				$this->out->dialog($this->lang->line('Importar Portadas'), sprintf($this->lang->line('import-portadas-ok'), $count));
			}
			else
			{
				$this->out->error(sprintf($this->lang->line('no-import-remote-error'), $bp->get_last_error()));
			}


			$this->out->dialog($this->lang->line('Importar Catalogo'), $this->lang->line('import-catalogo-ok'));
		}
		$data['cache'] = $cache;
		$data['url'] = 'sys/import/portadas';
		$this->_show_js('sys.import', 'sys/seccion.js', $data);
	}


	/**
	 * Importa las ventas desde una aplicación remota
	 * @param string $data El array de las ventas, albaranes y clientes serializado y comprimido
	 * @return MSG
	 */
	function ventas_remoto($data = null)
	{
		$this->userauth->roleCheck('sys.import');

		if ($this->config->item('bp.import.remoto.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-import-remoto-allow'));
		}

		$data = isset($data)?$data:$this->input->get_post('data');
		$data = unserialize(gzuncompress(base64_decode($data)));

		$facturas = $data['facturas'];
		$clientes = $data['clientes'];
		$albaranes2 = $data['albaranes'];

		$albaranes = array();
		$this->db->trans_begin();

		# Copia los clientes
		$this->load->model('clientes/m_cliente');
		$this->load->model('clientes/m_direccioncliente');
		$count_c = 0;
		$alias_c = array();
		$alias_d = array();
		$direcciones = null;
		foreach ($clientes as $cliente) 
		{
			$id_fk = $cliente['nIdCliente'];
			$alias_c[$id_fk] = $this->config->item('bp.tpv.cliente');
			/*
			$direcciones = $cliente['direcciones'];
			unset($cliente['direcciones']);
			unset($cliente['nIdCliente']);
			unset($cliente['descuentosgrupo']);
			unset($cliente['tarifas']);
			foreach ($cliente['descuentos'] as $key => $value) 
			{
				unset($cliente['descuentos'][$key]['nIdDescuento']);
			}
			foreach ($cliente['emails'] as $key => $value) 
			{
				if (trim($value['cEMail'])=='')
					unset($cliente['emails'][$key]);
				else					
					unset($cliente['emails'][$key]['nIdEmail']);
			}
			foreach ($cliente['telefonos'] as $key => $value) 
			{
				if (trim($value['cTelefono'])=='')
					unset($cliente['telefonos'][$key]);
				else					
					unset($cliente['telefonos'][$key]['nIdTelefono']);
			}
			$id_n = $this->m_cliente->insert($cliente);
			if ($id_n < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_cliente->error_message());
				return FALSE;
			}
			$alias_c[$id_fk] = $id_n;
			foreach ($direcciones as $value) 
			{
				$id_fk = $value['nIdDireccion'];
				if (trim($value['cCalle']) != '')
				{
					unset($value['nIdDireccion']);
					$value['nIdCliente'] = $id_n;
					$id_n = $this->m_direccioncliente->insert($value);
					if ($id_n < 0)
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_direccioncliente->error_message());
						return FALSE;
					}
				}
				else
				{
					$id_n = null;
				}
				$alias_d[$id_fk] = $id_n;
			}
			++$count_c;
			 */
		}

		# Copia los albaranes salida sin factura
		$this->load->model('ventas/m_albaransalida');
		$count_a = 0;
		#var_dump($albaranes2);
		foreach ($albaranes2 as $albaran) 
		{
			$id_fk = $albaran['nIdAlbaran'];
			unset($albaran['nIdAlbaran']);

			# Nuevas direcciones y nuevos clientes
			if (isset($alias_c[$albaran['nIdCliente']])) $albaran['nIdCliente'] = $alias_c[$albaran['nIdCliente']];
			#if (isset($alias_d[$albaran['nIdDireccion']])) $albaran['nIdDireccion'] = $alias_d[$albaran['nIdDireccion']];
			$albaran['nIdDireccion'] = null;

			foreach ($albaran['lineas'] as $k => $lineas)
			{
				#$albaranes[$lineas['nIdAlbaran']] = $lineas['nIdAlbaran'];
				unset($lineas['nIdLineaAlbaran']);
				unset($lineas['nIdAlbaran']);
				$albaran['lineas'][$k] = $lineas;
			}

			$albaran['nIdEstado'] = DEFAULT_ALBARAN_SALIDA_STATUS;
			$albaran['cRefInterna'] = sprintf($this->lang->line('albaran-importado-de'), $id_fk);

			$id_n = $this->m_albaransalida->insert($albaran);
			if ($id_n < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_albaransalida->error_message());
				return FALSE;
			}

			# Cierra el albarán		
			if (!$this->m_albaransalida->cerrar($id_n))
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_albaransalida->error_message());
				return FALSE;
			}

			++$count_a;
		}

		// Actualiza las facturas
		$count = 0;
		$this->load->model('ventas/m_factura');
		foreach ($facturas as $factura)
		{
			// Copia la factura
			unset($factura['nIdFactura']);
			unset($factura['nIdEstado']);

			# Nuevas direcciones y nuevos clientes
			if (isset($alias_c[$factura['nIdCliente']])) $factura['nIdCliente'] = $alias_c[$factura['nIdCliente']];
			#if (isset($alias_d[$factura['nIdDireccion']])) $factura['nIdDireccion'] = $alias_d[$factura['nIdDireccion']];
			$factura['nIdDireccion'] = null;

			// Copia las líneas
			foreach ($factura['lineas'] as $k => $lineas)
			{
				$albaranes[$lineas['nIdAlbaran']] = $lineas['nIdAlbaran'];
				unset($lineas['nIdLineaAlbaran']);
				unset($lineas['nIdAlbaran']);
				$factura['lineas'][$k] = $lineas;
			}
			// Copia los modos de pago
			foreach ($factura['modospago'] as $k => $modopago)
			{
				unset($modopago['nIdFacturaModoPago']);
				unset($modopago['nIdFactura']);
				$factura['modospago'][$k] = $modopago;
			}

			// Crea la factura nueva
			$factura['nIdEstado'] = FACTURA_STATUS_A_PROCESAR;
			$id_n = $this->m_factura->insert($factura);
			if ($id_n < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_factura->error_message());
				return FALSE;
			}
			++$count;
		}
		#Actualiza los números de factura
		$this->m_factura->numeros();
		
		$this->db->trans_commit();

		$this->out->dialog(TRUE, sprintf($this->lang->line('import-ventas-remoto-ok'), $count, $count_a, $count_c));
	}

	/**
	 * Importa las tablas de un fichero ZIP
	 * @param  string $file Fichero
	 * @return MSG/FORM
	 */
	function file($file = null)
	{
		$this->userauth->roleCheck('sys.import');

		if ($this->config->item('bp.import.database.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-import-catalogo-allow'));
		}

		$file = isset($file) ? $file : $this->input->get_post('file');

		if (!empty($file))
		{
			set_time_limit(0);
			$files = preg_split('/;/', $file);
			$files = array_unique($files);
			$file = array_pop($files);
			$url = site_url('sys/import/file2/' . $file);
			$this->out->url($url, $this->lang->line('Importar fichero'), 'iconoImportarTab');
		}
		else
		{
			$this->_show_js(null, 'sys/file.js');
		}
	}

	/**
	 * Importa las tablas de un fichero ZIP
	 * @param  string $file Fichero
	 * @return MSG/FORM
	 */
	function file2($file = null)
	{
		$this->userauth->roleCheck('sys.import');

		if ($this->config->item('bp.import.database.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-import-catalogo-allow'));
		}

		$file = isset($file) ? $file : $this->input->get_post('file');

		if (!empty($file))
		{
			set_time_limit(0);
			$files = preg_split('/;/', $file);
			$files = array_unique($files);
			$this->load->library('UploadLib');
			$this->load->library('ExportTables');
			$this->load->library('Progress');
			$this->progress->start();
			foreach ($files as $k => $file)
			{
				set_time_limit(0);
				if (!empty($file))
				{
					# Lo importa
					$file = urldecode($file);
					$name = $file;
					$zipfile = $this->uploadlib->get_pathfile($file);
					if (!$this->exporttables->import_zip($zipfile, $this->progress))
					{
						unlink($zipfile);
						$this->progress->text('ERROR: ' . $this->exporttables->get_last_error());
						$this->progress->end();
						exit;

						#$this->out->error($this->exporttables->get_last_error());
					}
					unlink($zipfile);
				}				
			}
			$this->progress->end();
			#$this->out->success($this->lang->line('import-ok'));
		}
		else
		{
			$this->_show_js(null, 'sys/file.js');
		}
	}

	/**
	 * Importa las tablas de un fichero ZIP
	 * @param  string $file Fichero
	 * @return MSG/FORM
	 */
	function ventasfile($file = null)
	{
		$this->userauth->roleCheck('sys.import');

		if ($this->config->item('bp.import.remoto.allow') !== TRUE)
		{
			$this->out->error($this->lang->line('no-import-remoto-allow'));
		}

		$file = isset($file) ? $file : $this->input->get_post('file');

		if (!empty($file))
		{
			set_time_limit(0);
			$files = preg_split('/;/', $file);
			$files = array_unique($files);
			$this->load->library('UploadLib');
			foreach ($files as $k => $file)
			{
				set_time_limit(0);
				if (!empty($file))
				{
					# Lo importa
					$file = urldecode($file);
					$name = $file;
					$datafile = $this->uploadlib->get_pathfile($file);
					$data = file_get_contents($datafile);
					$this->ventas_remoto($data);
				}				
			}
		}
		else
		{
			$data['url'] = 'sys/import/ventasfile';
			$data['ext'] = '*.dat';
			$data['desc'] = 'Ficheros de ventas';
			$this->_show_js(null, 'sys/file.js', $data);
		}
	}

}

/* End of file import.php */
/* Location: ./system/application/controllers/sys/import.php */