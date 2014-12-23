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

 define('SINLI_TIPOCARGO_GASTOS', 1);
/**
 * Gestor de sinli
 *
 */
class Sinli extends MY_Controller
{
	
	/**
	 * Relación de los estados de SINLI con la información del proveedor de Bibliopola
	 * @var array, Id SINLI => Id BP, null si no hay relación 
	 */
	private $estadosenvio = array (
		0 => null,	#'Sin clasificar',
		1 => null, 	#'Disponible',
		2 => 4, 	#'Descatalogado',
		3 => 1, 	#'Agotado',
		4 => 5, 	#'En reimpresión inmediata, hasta 1 mes',
		5 => 6, 	#'En reimpresión, sin fecha conocida de servicio',
		6 => 3, 	#'Sin existencia',
		7 => 2, 	#'No pertenece a nuestro fondo',
		8 => 7, 	#'Novedad. Próxima aparición',
		9 => null, 	#'Últimas novedades'
		);
		
	/**
	 * Relación de los estados de SINLI con el estado del artículo en BP
	 * @var array, Id SINLI => Id BP, null si no hay relación 
	 */
	private $estadosarticulo = array (
		0 => 3,		#'Sin clasificar',
		1 => 3, 	#'Disponible',
		2 => 4, 	#'Descatalogado',
		3 => 6, 	#'Agotado',
		4 => 8, 	#'En reimpresión inmediata, hasta 1 mes',
		5 => 8, 	#'En reimpresión, sin fecha conocida de servicio',
		6 => 3, 	#'Sin existencia',
		7 => null, 	#'No pertenece a nuestro fondo',
		8 => 3, 	#'Novedad. Próxima aparición',
		9 => 3, 	#'Últimas novedades'
		);
		
	/**
	 * Constructor
	 *
	 * @return Sinli
	 */
	function __construct()
	{
		parent::__construct('sys.sinli', 'sys/m_sinli', TRUE, null, 'SINLI', 'sys/submenusinli.js');
	}
	
	/**
	 * Listado de proveedores que tienen documentos SINLI
	 * @return DATA
	 */
	function proveedores($tipo = NULL)
	{
		$this->userauth->roleCheck(($this->auth . '.get_list'));

		$tipo = isset($tipo)?$tipo:$this->input->get_post('tipo');

		$data = $this->reg->proveedores(strtoupper($tipo));
		foreach ($data as $k => $reg)
		{
			$data[$k]['text'] = format_name($reg['cNombre'], $reg['cApellido'], $reg['cEmpresa']);
		}
		sksort($data,'text');
		$this->out->data($data);		
	}
	
	/**
	 * Documentos SINLI recibidos del tipo ENVIOS
	 * @param int $id Id del proveedor
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @return JSON_DATA
	 */
	function envios($id = null, $start = null, $limit = null, $sort = null, $dir = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id = isset($id) ? $id : $this->input->get_post('id');
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		
		if (!empty($id)) 
		{
			$this->load->model('proveedores/m_proveedor');
			$pv = $this->m_proveedor->load($id);
			$data = $this->reg->get($start, $limit, $sort, $dir, 'nIdDocumento IS NULL AND cTipo=\'ENVIO\' AND cOrigen=' .$this->db->escape($pv['cSINLI']), array('cFichero', 'nIdFichero', $this->reg->_date_field('dFecha', 'dFecha')));
			foreach ($data as $k => $reg)
			{
				#echo '<pre>'; print_r($reg); echo '</pre>';
				$error = error_reporting();
				error_reporting(E_ERROR);
				$fichero = unserialize(utf8_decode($reg['cFichero']));
				error_reporting($error);
				#var_dump($fichero); die();
				
				$data[$k]['cProveedor'] = format_name($pv['cNombre'], $pv['cApellido'], $pv['cEmpresa']);
				$data[$k]['cAlbaran'] = $fichero['C'][0]['numero'];
				$data[$k]['dFechaAlbaran'] = isset($fichero['C'][0]['fecha'])?$fichero['C'][0]['fecha']:(isset($fichero['C'][0]['Fecha'])?$fichero['C'][0]['Fecha']:null);
				$data[$k]['fImporte'] = $fichero['T'][0]['neto'];
				$data[$k]['nCantidad'] = $fichero['T'][0]['unidades'];
				$data[$k]['id'] = $data[$k]['nIdFichero']; 
				$data[$k]['fGastos'] = $fichero['C'][0]['gastos'];
				unset($data[$k]['cFichero']);
			}
			#var_dump($data);
			$this->out->data($data, $this->reg->get_count());		
		}
		
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Busca el artículo por EAN
	 * @param string $ean EAN
	 * @return mixed, int Id del artículo, null no encontrado
	 */
	private function articulo($ean)
	{
		$this->load->library('ISBNEAN');
		$this->load->model('catalogo/m_articulo');
		$ean = $this->isbnean->to_ean($ean);
		if ($ean)
		{
			// Busca el artículo
			$find = $this->m_articulo->search($ean, 0, 1);
			#echo array_pop($this->db->queries); die();
			#var_dump($this->db->queries); die();
			if (count($find) > 0)
			{
				$find[0]['nIdLibro'] = $find[0]['id'];
				return $find[0];
			}
		}
		return null;		
	}

	/**
	 * Importa los envios de SINLI
	 * @param int $id Ids de los documentos de pedido separados por ;
	 *  
	 */
	function importarenvio($ids = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$ids = isset($ids)?$ids:$this->input->get_post('ids');

		if ($ids)
		{
			$ids = is_string($ids)?preg_split('/\;/', $ids):$ids;
			$res = array();
			$this->db->trans_begin();
			$this->load->model('proveedores/m_proveedor');
			$this->load->model('compras/m_albaranentrada');
			$this->load->library('Messages');
			foreach($ids as $k => $id)
			{
				if (is_numeric($id))
				{
					# Importa el envio
					$data = $this->reg->load($id);
					$pv = $this->m_proveedor->get(0, 0, 0, 0, 'cSINLI=' . $this->db->escape($data['cOrigen']));
					if (!isset($pv[0]))
					{
						$res[$id] = array('error' => sprintf($this->lang->line('sinli-error-no-proveedor'), $data['cOrigen']));
					}
					$fichero = unserialize(utf8_decode($data['cFichero']));

					$this->load->model('proveedores/m_direccion');
					$dirs = $this->m_direccion->get_list($pv[0]['nIdProveedor']);
					$dir = $this->utils->get_profile($dirs, PERFIL_PEDIDO);
					$idd = ($dir)?$dir['id']:null;

					# Cabecera					
					$albaran = array(
						'nIdProveedor' => $pv[0]['nIdProveedor'],
						'cNumeroAlbaran' => $fichero['C'][0]['numero'],
						'bExtranjero' => FALSE,
						'nIdDireccion' => $idd,
						'dFecha' => isset($fichero['C'][0]['fecha'])?$fichero['C'][0]['fecha']:(isset($fichero['C'][0]['Fecha'])?$fichero['C'][0]['Fecha']:null),
						'cRefInterna' => sprintf($this->lang->line('sinli-ref-albaranentrada'), $data['cOrigen'], $id, format_datetime($data['dFecha']))
					);					
					 
					# Depósito?
					if (($fichero['C'][0]['tipoenvio'] == 'D')||($fichero['C'][0]['tipoenvio'] == 'C'))
					{
						$albaran['bDeposito'] = TRUE;
					}
					
					# Líneas
					$fijo = array();
					$libre = array();
					$notfound = array();
					foreach($fichero['D'] as $linea)
					{
						if ($linea['cantidad'] > 0)
						{
							$idl = $this->articulo(substr($linea['ean'], 0, 13));
							if (isset($idl))
							{
								$idl = $idl['nIdLibro'];
								$l = array(
									'nCantidad' => $linea['cantidad'],
									'fDescuento' => $linea['descuento'],
									'fPrecio' => $linea['precio'],
									'fIVA' => $linea['iva'],
									'fPrecioVenta' => $linea['pvp'],
									'nIdLibro' => $idl,
									'cRefProveedor' => $linea['referencia']
								);
								if ($linea['tipoprecio'] == 'L')
								{ 
									unset($l['fPrecioVenta']);
									$libre[]  = $l;
								}
								else
									$fijo[] = $l;
							}
							else
							{
								# No existe la línea
								$notfound[] = $linea;
							}
						}
					}
					
					#Gastos
					if ($fichero['C'][0]['gastos'] != 0)
					{
						$albaran['cargos'][] = array ('nIdTipoCargo' => SINLI_TIPOCARGO_GASTOS, 'fImporte' => $fichero['C'][0]['gastos']);
					}
					
					# Si hay fijo y libre, crea dos albaranes
					if (count($libre) > 0)
					{
						$albaran['bPrecioLibre'] = TRUE;
						$albaran['lineas'] = $libre;
					}
					else
					{
						$albaran['lineas'] = $fijo;						
					}
					#echo '<pre>'; var_dump($albaran); echo '</pre>';

					# Crea el albarán
					$idn = $this->m_albaranentrada->insert($albaran);
					if ($idn < 0)
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_albaranentrada->error_message());
					}
					$this->_add_nota(null, $idn, NOTA_INTERNA, $albaran['cRefInterna'], $this->m_albaranentrada->get_tablename());
					
					$idn2 = null;
					if (count($libre) > 0 && count($fijo) > 0)
					{
						$albaran['bPrecioLibre'] = FALSE;
						$albaran['lineas'] = $fijo;
						# Crea el albarán 2
						$idn2 = $this->m_albaranentrada->insert($albaran);
						if ($idn2 < 0)
						{
							$this->db->trans_rollback();
							$this->out->error($this->m_albaranentrada->error_message());
						}						
						$this->_add_nota(null, $idn2, NOTA_INTERNA, $albaran['cRefInterna'], $this->m_albaranentrada->get_tablename());
					}

					if (isset($idn2))
					{
						$res[$id] = array('libre'=> $idn, 'fijo' => $idn2, 'notfound' => $notfound);
					}
					elseif (count($libre) > 0)
					{ 
						$res[$id] = array('libre' => $idn, 'notfound' => $notfound);
					}
					else
					{
						$res[$id] = array('fijo' => $idn, 'notfound' => $notfound);
					}
					if (!$this->reg->update($id, array('nIdDocumento' => $idn)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->reg->error_message());
					}						
				}
			}
			$this->db->trans_commit();
			foreach ($res as $id => $data)
			{
				if (isset($data['libre']))
				{
					$link = format_enlace_cmd($data['libre'], site_url('compras/albaranentrada/index/' . $data['libre']));
					$this->messages->info(sprintf($this->lang->line('sinli-albaranentrada-creado-libre'), $link));
				}
				if (isset($data['fijo']))
				{
					$link = format_enlace_cmd($data['fijo'], site_url('compras/albaranentrada/index/' . $data['fijo']));
					$this->messages->info(sprintf($this->lang->line('sinli-albaranentrada-creado-fijo'), $link));
				}
				if (count($data['notfound'])> 0)
				{
					$this->messages->info($this->lang->line('sinli-albaranentrada-titulos-noencontrados'), 1);
					foreach ($data['notfound'] as $l)
					{
						$this->messages->info(sprintf($this->lang->line('sinli-albaranentrada-no-encontrado'), $l['cantidad'], substr($l['ean'], 0, 13), utf8_encode($l['titulo']), format_price($l['pvp'])), 2);
					}
				}				
			}
			$body = $this->messages->out('Importar SINLI');

			$this->out->html_file($body, $this->lang->line('Importar SINLI'), 'iconoReportTab');			
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Procesa los mensajes del buzón de SINLI
	 * @param int $count Número de mensajes a procesar
	 * @return DIALOG
	 */
	function process($count = null, $out = null)
	{
		#$this->userauth->roleCheck($this->auth .'.add');

		$count = isset($count)?$count:$this->input->get_post('count');
		$out = isset($out)?$out:$this->input->get_post('out');
		$out = empty($out)?FALSE:format_tobool($out);
		
		$this->load->library('SinliLib');
		
		if (!is_numeric($count)) $count = 1000;
		
		# Lee los mensajes 
		$res = $this->sinlilib->check($count);
		
		if ($res===FALSE)
			$this->out->error($this->sinlilib->get_error());
		
		# Procesa los mensajes
		$data = $this->reg->get(null, null, 'dFecha', 'ASC', 'ISNULL(bProcesado,0)=0 AND cTipo=\'ENVIO\'', array('cFichero', 'nIdFichero', 'cTipo', $this->reg->_date_field('dFecha', 'dFecha')));
		$message = "<pre>Procesando ENVIO...\n--------------------------\n";
		foreach ($data as $k => $reg)
		{
			$message .= "  +{$reg['cTipo']} - {$reg['nIdFichero']}..";
			$res2 = $this->procesar($reg['nIdFichero'], FALSE);
			$message .= (($res2===TRUE)?'OK': 'ERROR ' . $res2) . "\n"; 
		}
		$message .= '</pre>';

		if ($out)
			$this->out->html_file(sprintf($this->lang->line('sinli-docs-procesados'), $res['emails'], $res['count'], count($res['files'])) . '<pre>' . print_r($res['files'], TRUE) . '</pre>' . $message, $this->lang->line('SINLI'), 'iconoReportTab');
		else
			$this->out->dialog($this->lang->line('SINLI'), sprintf($this->lang->line('sinli-docs-procesados'), $res['emails'], $res['count'], count($res['files']))/* . '<pre>' . print_r($res['files'], TRUE) . '</pre>'. $message*/);
	}

	/**
	 * Procesa los mensajes del buzón de SINLI
	 * @param int $count Número de mensajes a procesar
	 * @return DIALOG
	 */
	function process2($count = null)
	{
		#$this->userauth->roleCheck($this->auth .'.add');
		$data = $this->reg->get(null, null, 'dFecha', 'ASC', 'ISNULL(bProcesado,0)=0 AND cTipo=\'ENVIO\'', array('cFichero', 'nIdFichero', 'cTipo', $this->reg->_date_field('dFecha', 'dFecha')));
		$message = "<pre>Procesando ENVIO...\n--------------------------\n";
		foreach ($data as $k => $reg)
		{
			$message .= "  +{$reg['cTipo']} - {$reg['nIdFichero']}..";
			$res = $this->procesar($reg['nIdFichero'], FALSE);
			$message .= (($res===TRUE)?'OK': 'ERROR ' . $res) . "\n"; 
		}
		$message .= '</pre>';
		$this->out->html_file($message, $this->lang->line('SINLI'), 'iconoReportTab');		
	}

	/**
	 * Muestra el contenido del documento sinli
	 * @param int $id Id del documento
	 * @return HTML_FILE
	 */
	function ver($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id = isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{
			$data = $this->reg->load($id);
			$data['cFichero'] = unserialize(utf8_decode($data['cFichero']));
			$html = '<pre>' . $this->utils->print_r_tree($data) . '</pre>';
			$this->out->html_file($html, $this->lang->line('SINLI'), 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}
	
	/**
	 * Procesa el contenido del documento sinli
	 * @param int $id Id del documento
	 * @return HTML_FILE
	 */
	function procesar($id = null, $out = TRUE)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id = isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{
			$data = $this->reg->load($id);
			$method = strtolower($data['cTipo']);
			if (method_exists($this, $method))
			{
				$this->load->model('proveedores/m_proveedor');
				$pv = $this->m_proveedor->get(0, 0, 0, 0, 'cSINLI=' . $this->db->escape($data['cOrigen']));
				if (!isset($pv[0]))
				{
					$error = sprintf($this->lang->line('sinli-error-no-proveedor'), $data['cOrigen']); 
					if ($out) $this->out->error($error);
					return $error;
				}
				return $this->$method($id, $data, $pv[0], $out);
			}
			$error = $this->lang->line('sinli-error-formato-nosportado');
			if ($out) $this->out->error($error);
			return $error;
		}
		$error = $this->lang->line('mensaje_faltan_datos');
		if ($out) $this->out->error($error);
		return $error;		
	}

	/**
	 * Ejecuta el archivo de estados de un ESTADO
	 * @param int $id Id del documento
	 * @param array $data Registro del documento leído
	 * @param array $pv Datos del proveedor
	 * @return HTML_FILE
	 */
	function estado($id, $data, $pv)
	{
		return $this->envio($id, $data, $pv);
	}

	/**
	 * Ejecuta el archivo de estados de un ENVIO
	 * @param int $id Id del documento
	 * @param array $data Registro del documento leído
	 * @param array $pv Datos del proveedor
	 * @param bool $out Debe generar una salida HTML
	 * @return HTML_FILE
	 */
	function envio($id, $data, $pv, $out = TRUE)
	{
		$this->load->library('SinliLib');
		if ($out) $this->load->library('Messages');
		$this->load->model('catalogo/m_articulo');
		$this->load->model('compras/m_pedidoproveedorlinea');
		$this->load->model('compras/m_pedidoproveedor');
		
		$fichero = unserialize(utf8_decode($data['cFichero']));
		$fecha = $data['dFechaAlbaran'] = isset($fichero['C'][0]['Fecha'])?$fichero['C'][0]['Fecha']:$data['dFecha'];
		#var_dump($fichero);
		
		if ($out) $this->messages->info(sprintf($this->lang->line('sinli-procesando-estado'), 
			format_name($pv['cNombre'], $pv['cApellido'], $pv['cEmpresa']),
			isset($fichero['C'][0]['numero'])?$fichero['C'][0]['numero']:$data['nIdFichero'], format_date($fecha)));
			#$link = format_enlace_cmd($data['libre'], site_url('compras/albaranentrada/index/' . $data['libre']));
			#$this->messages->info(sprintf($this->lang->line('sinli-albaranentrada-creado-libre'), $link));
		if (isset($fichero['E']))
		{
			foreach ($fichero['E'] as $estado)
			{
				# Error importación anterior al 28/03/2012
				if (is_float($estado['estado'])) $estado['estado'] = (int) ($estado['estado'] * 100);
				$est = $this->estadosenvio[$estado['estado']];
				if (isset($est))
				{
					$ean = $estado['ean'];
					$idl = $this->articulo(substr($estado['ean'], 0, 13));
					if (isset($idl))
					{
						$link_l = format_enlace_cmd($idl['nIdLibro'], site_url('catalogo/articulo/index/' . $idl['nIdLibro']));
						if ($out) $this->messages->info(sprintf($this->lang->line('sinli-envio-cambio-estado'), $link_l, $idl['cTitulo'], $this->sinlilib->estadosenvio[$estado['estado']]), 1);
						# Buscamos los pedidos de proveedor pendientes
						$ped = $this->m_articulo->get_pedidos_proveedor($idl['nIdLibro'], null, null, TRUE);
						if (count($ped) > 0)
						{
							foreach($ped as $p)
							{
								if ($p['nIdPv'] == $pv['nIdProveedor'])
								{
									$res = $this->m_pedidoproveedorlinea->update($p['nIdLinea'], array('nIdInformacion' => $est, 'dFechaInformacion' => time()));
									if (!$res) $this->out->error($this->m_pedidoproveedorlinea->error_message());
									$message = sprintf($this->lang->line('estado-pedido-proveedor-titulo'), $this->lang->line('SINLI') .': '.$this->sinlilib->estadosenvio[$estado['estado']], $link_l, $idl['cTitulo']);
									$this->_add_nota(null, $p['id'], NOTA_INTERNA, $message, $this->m_pedidoproveedor->get_tablename());
									$link_p = format_enlace_cmd($p['id'], site_url('compras/pedidoproveedor/index/' . $p['id']));
									if ($out) $this->messages->info(sprintf($this->lang->line('sinli-envio-cambio-estado-pedido'), $link_p, $this->sinlilib->estadosenvio[$estado['estado']]), 2);	
								}
							}
						}
						# Actualizamos el estado del artículo			
						$est = $this->estadosarticulo[$estado['estado']];
						if (isset($est))						
						{
							if ($out) $this->messages->info(sprintf($this->lang->line('sinli-envio-cambio-estado-articulo'), $link_l, $idl['cTitulo'], $this->sinlilib->estadosenvio[$estado['estado']]), 1);
							$res = $this->m_articulo->update($idl['nIdLibro'], array('nIdEstado' => $est));
							if (!$res) $this->out->error($this->m_articulo->error_message());
							$message = sprintf($this->lang->line('estado-pedido-proveedor-titulo'), $this->lang->line('SINLI') .': '.$this->sinlilib->estadosenvio[$estado['estado']], $link_l, $idl['cTitulo']);
							$this->_add_nota(null, $idl['nIdLibro'], NOTA_INTERNA, $message, $this->m_articulo->get_tablename());							
						}
					}
				}
				#var_dump($estado);
				#echo $this->sinlilib->estadosenvio[$estado['estado']]; 
			}
		}
		else 
		{
			if ($out) $this->messages->warning($this->lang->line('sinli-envio-no-estado'));			
		}
		if (!$this->reg->update($id, array('bProcesado' => TRUE)))
		{
			$this->db->trans_rollback();
			if ($out) $this->out->error($this->reg->error_message());
			return $this->reg->error_message();
		}						
		#die();
		if ($out)
		{
			$body = $this->messages->out('Importar SINLI');
			$this->out->html_file($body, $this->lang->line('Importar SINLI'), 'icon-sinli');
		}
		return TRUE;
	}

	/**
	 * Limpieza de los ficheros antiguos
	 */
	function clean()
	{		
		#$data = $this->reg->get(null, null, null, null, 'nIdDocumento IS NULL AND cTipo=\'ENVIO\'', array('cFichero', 'nIdFichero', $this->reg->_date_field('dFecha', 'dFecha')));
		$data = $this->reg->get(null, null, null, null, 'nIdDocumento IS NULL AND cTipo=\'ESTADO\'', array('cFichero', 'nIdFichero', $this->reg->_date_field('dFecha', 'dFecha')));
		
		$message = '<pre>';
		$message .= "Borrando ESTADO...\n--------------------------\n";
		foreach ($data as $k => $reg)
		{
			#echo '<pre>'; print_r($reg); echo '</pre>';
			$fichero = unserialize(utf8_decode($reg['cFichero']));
			
			if (!isset($fichero['E']))
			{
				$message .= "  -BORRADO {$reg['nIdFichero']}\n";				
				$this->reg->delete($reg['nIdFichero']);
			}
			else 
			{
				$message .= "  +NO {$reg['nIdFichero']}\n";				
			}
			
			/*$data[$k]['cAlbaran'] = $fichero['C'][0]['numero'];
			$data[$k]['dFechaAlbaran'] = $fichero['C'][0]['Fecha'];
			$data[$k]['fImporte'] = $fichero['T'][0]['neto'];
			$data[$k]['nCantidad'] = $fichero['T'][0]['unidades'];
			$data[$k]['id'] = $data[$k]['nIdFichero']; 
			$data[$k]['fGastos'] = $fichero['C'][0]['gastos'];
			unset($data[$k]['cFichero']);
			var_dump($data[$k]);*/
		}		
		$data = $this->reg->get(null, null, null, null, 'nIdDocumento IS NULL AND cTipo=\'ENVIO\'', array('cFichero', 'nIdFichero', $this->reg->_date_field('dFecha', 'dFecha')));
		$message .= "Borrando ENVIO...\n--------------------------\n";
		foreach ($data as $k => $reg)
		{
			$fichero = unserialize(utf8_decode($reg['cFichero']));
			if (isset($fichero['C']))
			{
				$fecha = isset($fichero['C'][0]['fecha'])?$fichero['C'][0]['fecha']:(isset($fichero['C'][0]['Fecha'])?$fichero['C'][0]['Fecha']:null);
				if ($fecha < mktime(0, 0, 0, 3,26, 2012))
				{
					$message .= "  -BORRADO {$reg['nIdFichero']}\n";				
					$this->reg->delete($reg['nIdFichero']);
				}
				else 
				{
					$message .= "  +NO {$reg['nIdFichero']}\n";
				}
			}
			else 
			{
				$message .= "  +NO {$reg['nIdFichero']}\n";				
			}
			
		}
		$message .= '</pre>';
		$this->out->html_file($message, $this->lang->line('Importar SINLI'), 'icon-sinli');
	}

	/**
	 * Importa un fichero EXCEL como pedido del cliente
	 * @param int $proveedor Id del proveedor
	 * @param string $file Fichero EXCEL de <upload> a importar
	 */
	function fichero($file = null)
	{
		$this->userauth->roleCheck(($this->auth.'.add'));
		
		$file = isset($file) ? $file : $this->input->get_post('file');
		
		if (empty($file))
		{
			$this->_show_js('add', 'sys/file.js', array('url' => 'sys/sinli/fichero'));
		}

		$files = preg_split('/;/', $file);
		$files = array_unique($files);
		$count = 0;
		$msg = array();
		foreach ($files as $k => $file)
		{
			if (!empty($file))
			{
				$this->load->library('UploadLib');
				$file = urldecode($file);
				$name = $file;
				$file = $this->uploadlib->get_pathfile($file);
				set_time_limit(0);
				#var_dump($file); die();
				$this->load->library('SinliLib');
				$id = $this->sinlilib->process(array('filename' => $file, 'subject' => $name));
				$this->load->model('sys/m_sinli');
				$this->load->model('proveedores/m_proveedor');
				$sinli = $this->m_sinli->load($id);
				#Proveedor
				$pv = $this->m_proveedor->get(null, null, null, null, 'cSINLI=' .$this->db->escape($sinli['cOrigen']));
				if (count($pv) > 0)
				{
					$pv = $pv[0];
					$pv = format_enlace_cmd(format_name($pv['cEmpresa'], $pv['cNombre'], $pv['cApellido']), site_url('proveedores/proveedor/index/' . $pv['nIdProveedor']));
				}
				else
				{
					$pv = $this->lang->line('NO PROVEEDOR SINLI');
				}
				$id = format_enlace_cmd($id,  site_url('sys/sinli/ver/' . $id));
				$msg[] = sprintf($this->lang->line('sinli-importar-fichero-ok'), $id, $sinli['cTipo'], $pv);
			}
		}
		$this->out->dialog($this->lang->line('Importar Fichero'), implode('<br/>', $msg));
	}

}
/* End of file sinli.php */
/* Location: ./system/application/controllers/sys/sinli.php */