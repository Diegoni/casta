<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	stocks
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Stock contado
 *
 */
class Stockcontado extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Stockcontado
	 */
	function __construct()
	{
		parent::__construct('stocks.stockcontado', 'stocks/M_stockcontado', TRUE, null, 'Stock contado');
	}

	/**
	 * Ventana para realizar el inventario de artículos
	 * @return FORM
	 */
	function inventariar()
	{
		$this->userauth->roleCheck($this->auth . '.index');
		$this->_show_form('index', 'stocks/inventario.js', 'Inventariar');
	}

	/**
	 * Analiza el stock de un artículo
	 * @param int $id Id del artículo
	 * @return HTML_FILE
	 */
	function analisis($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.analisis');

		$id 	= isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{
			// Modelos
			$this->load->model('catalogo/m_articulo');
			$this->load->model('stocks/m_arreglostock');
			$this->load->model('stocks/m_antiguedadstock');
				
			// Configuración
			$idajustemas = $this->config->item('bp.stocks.idajustemas');
			$idajustemenos = $this->config->item('bp.stocks.idajustemenos');
			$idregemas = $this->config->item('bp.stocks.idregulacionmas');
			$idregmenos = $this->config->item('bp.stocks.idregulacionmenos');
			$fechainventario = $this->config->item('bp.stocks.fechainventario');
			$fecharetroceso = $this->config->item('bp.oltp.fechadpr');

			// Artículo
			$articulo = $this->m_articulo->load($id, array('ubicaciones', 'secciones'));
			// Stock contado
			$contado = $this->m_articulo->get_stockcontado($id);

			// Stock ajustado
			$desde = format_mssql_date(to_date(($fechainventario)));
			$where[0] = "dCreacion >= {$desde}";
			$where[1] = "nIdLibro = {$id}";
			$where[2] = "nIdMotivo = {$idajustemas}";
			$where2 = implode(' AND ', $where);
			$ajustesmas = $this->m_arreglostock->get(null, null, 'dCreacion', null, $where2);
			$where[2] = "nIdMotivo = {$idajustemenos}";
			$where2 = implode(' AND ', $where);
			$ajustesmenos = $this->m_arreglostock->get(null, null, 'dCreacion', null, $where2);

			// Stock retrocedido
			$retrocedido = $this->m_antiguedadstock->load($id);
			// Documentos de retroceso
			$docs_retroceso = $this->m_articulo->get_documentos($id, to_date($fecharetroceso), to_date($fechainventario), null, TRUE);
			// Documentos hasta el inventario
			$docs = $this->m_articulo->get_documentos($id, to_date($fechainventario), time(), null, TRUE);
				
			$data['articulo'] = $articulo;
			$data['contado'] = $contado;
			$data['ajustesmas'] = $ajustesmas;
			$data['ajustesmenos'] = $ajustesmenos;
			$data['fechainventario'] = format_date(to_date($fechainventario));
			$data['fecharetroceso'] = format_date(to_date($fecharetroceso));
			$data['ahora'] = format_date(time());
			$data['retrocedido'] = $retrocedido;
			$data['docs_retroceso'] = $docs_retroceso;
			$data['docs'] = $docs;
			#echo '<pre>'; print_r($data); echo '</pre>'; die();
			$message = $this->load->view('stocks/analisis', $data, TRUE);
			$this->out->html_file($message, $this->lang->line('Análisis de stock inventario'), 'iconoReportTab', $this->config->item('bp.data.css'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Indica los ajustes de Stock en el inventario
	 * @return HTML
	 */
	function ajustes()
	{
		$this->userauth->roleCheck($this->auth.'.get_list');

		$this->load->model('stocks/m_arreglostock');
		$this->load->model('generico/m_seccion');

		$idajustemas = $this->config->item('bp.stocks.idajustemas');
		$idajustemenos = $this->config->item('bp.stocks.idajustemenos');
		$fechainventario = $this->config->item('bp.stocks.fechainventario');

		// Stock ajustado
		$desde = format_mssql_date(to_date(($fechainventario)));
		$where[0] = "dCreacion >= {$desde}";
		$where[2] = "nIdMotivo = {$idajustemas}";
		$where2 = implode(' AND ', $where);
		$ajustesmas = $this->m_arreglostock->get(null, null, 'dCreacion', null, $where2);
		$where[2] = "nIdMotivo = {$idajustemenos}";
		$where2 = implode(' AND ', $where);
		$ajustesmenos = $this->m_arreglostock->get(null, null, 'dCreacion', null, $where2);

		$secciones = $this->m_seccion->get(null, null, 'cNombre');
		$data = array();
		foreach ($secciones as $sec)
		{
			if (!isset($data[$sec['nIdSeccion']]))
			{
				$data[$sec['nIdSeccion']] = array(
					'cNombre' 	=> $sec['cNombre'],
					'fm-'		=> 0,
					'dp-'		=> 0,
					'fm+'		=> 0,
					'dp+'		=> 0,
					);
			}
		}
		foreach ($ajustesmas as $reg)
		{
			if ($reg['nCantidadFirme'] > 0)
				$data[$reg['nIdSeccion']]['fm+'] += $reg['nCantidadFirme'];
			if ($reg['nCantidadDeposito'] > 0)
				$data[$reg['nIdSeccion']]['dp+'] += $reg['nCantidadDeposito'];
		}
		foreach ($ajustesmenos as $reg)
		{
			if ($reg['nCantidadFirme'] > 0)
				$data[$reg['nIdSeccion']]['fm-'] += $reg['nCantidadFirme'];
			if ($reg['nCantidadDeposito'] > 0)
				$data[$reg['nIdSeccion']]['dp-'] += $reg['nCantidadDeposito'];
		}

		#var_dump($data);			
		$res['stocks'] = $data;
		$body = $this->load->view('catalogo/stockajustes', $res, TRUE);
		$this->out->html_file($body, $this->lang->line('Ajustes de stock contado'), 'iconoReportTab');		
	}
	
	/**
	 * Elimina las ubicaciones temporales
	 * @return MSG
	 */
	function resetubicaciones()
	{
		$this->userauth->roleCheck($this->auth . '.reset');
		$this->load->model('stocks/m_ubucaciontemporal');
		if (!$this->m_ubucaciontemporal->delete())
			$this->out->error($this->m_ubucaciontemporal->error_message());
		$this->out->success($this->lang->line('reset-ubicaciones-ok'));	
	}

	/**
	 * Asigna las ubicaciones temporales
	 * @return MSG
	 */
	function asignarubicaciones($task = null)
	{
		$this->userauth->roleCheck($this->auth . '.asignar');
		
		$task = isset($task)?$task:$this->input->get_post('task');
		if ($task === FALSE) $task = 1;
		
		if ($task == 1)
		{
			$this->load->library('tasks');
			$cmd = site_url("stocks/stockcontado/asignarubicaciones/0");
			$this->tasks->add2($this->lang->line('Asignar ubicaciones temporales'), $cmd);
		}
		else
		{
			set_time_limit(0);
			$this->load->model('stocks/m_ubucaciontemporal');
			$this->load->model('catalogo/m_articuloubicacion');
			$data = $this->m_ubucaciontemporal->get();
			$count = 0;
			if (count($data) == 0)
				$this->out->error($this->lang->line('ubicaciones-asignadas-no'));
			foreach($data as $r)
			{
				$this->db->trans_begin();
				$res = $this->m_articuloubicacion->get(null, null, null, null, "nIdLibro={$r['nIdLibro']} AND nIdUbicacion={$r['nIdUbicacion']}");
				if (count($res) == 0)
				{
					if ($this->m_articuloubicacion->insert($r) < 0)
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_articuloubicacion->error_message());
					}
				}
				else
				{
					if (!$this->m_articuloubicacion->update($res[0]['nIdUbicacionLibro'], array('dCreacion' => time())))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_articuloubicacion->error_message());
					}					
				}
				if (!$this->m_ubucaciontemporal->delete($r['nIdLibroUbicacion']))
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_ubucaciontemporal->error_message());
				}					
				$this->db->trans_commit();
				++$count;			
			}
			$this->out->success(sprintf($this->lang->line('ubicaciones-asignadas-ok'), $count));	
		}
	}
	
	/**
	 * Realiza un backup del stock contado
	 * @param string $name Nombre del backup
	 * @return MSG/FORM 
	 */
	function reset($name = null)
	{
		$this->userauth->roleCheck($this->auth . '.reset');
		$name 	= isset($name)?$name:$this->input->get_post('name');
		if (!empty($name))
		{
			if (!($res = $this->reg->reset($name)))
				$this->out->error($this->reg->error_message());
			$this->out->dialog($this->lang->line('Eliminar stock contado'), sprintf($this->lang->line('reset-stockcontado-ok'), $res));
		}	
		$this->_show_js('reset', 'stocks/reset.js');
	}
	
	/**
	 * Añade una entrada de stock contado y la ubicación
	 * @param int $idl Id del artículo 
	 * @param int $ids Id de la sección
	 * @param int $idt Id del tipo de stock
	 * @param int $ct Unidades contadas
	 * @return mixed TRUE: Ok, string: error
	 */
	private function _add2($idl, $ids, $idt, $ct)
	{
		// Añade el contado
		$res = $this->reg->get(null, null, null, null, "nIdLibro={$idl} AND nIdSeccion={$ids} AND nIdTipoStock={$idt}");
		if (count($res) == 0)
		{
			// Es nuevo
			$data['nIdLibro'] = $idl;
			$data['nIdSeccion'] = $ids;
			$data['nIdTipoStock'] = $idt;
			$data['nCantidad']  = $ct;
			$data['dCreacion'] = time();
			$idn = $this->reg->insert($data);
			if ($idn < 1)
			{
				return $this->reg->error_message();	
			}
		}
		else
		{
			$data['nCantidad']  = $res[0]['nCantidad'] + $ct;
			if (!$this->reg->update($res[0]['nIdRegulacionStock'], $data))
			{
				return $this->reg->error_message();
			}
		}
		return TRUE;
	}

	/**
	 * Añade una entrada de stock contado y la ubicación
	 * @param int $idl Id del artículo 
	 * @param int $ids Id de la sección
	 * @param int $idt Id del tipo de stock
	 * @param int $idu Id de la ubicación
	 * @param int $ct Unidades contadas
	 * @return MSG
	 */
	function add2($idl = null, $ids = null, $idt = null, $idu = null, $ct = null)
	{
		$this->userauth->roleCheck($this->auth . '.add');

		$idl 	= isset($idl)?$idl:$this->input->get_post('idl');
		$ids 	= isset($ids)?$ids:$this->input->get_post('ids');
		$idt 	= isset($idt)?$idt:$this->input->get_post('idt');
		$idu 	= isset($idu)?$idu:$this->input->get_post('idu');
		$ct 	= isset($ct)?$ct:$this->input->get_post('ct');
		
		if (empty($ct)) $ct = 1;
		if (empty($idt) || $idt < 1) $idt = $this->config->item('bp.contarstocks.firme'); 
		if (is_numeric($idl) && is_numeric($ids))
		{
			if (is_numeric($idu))
			{
				// Añade la ubicación
				$this->load->model('stocks/m_ubucaciontemporal');
				$this->m_ubucaciontemporal->insert(array('nIdLibro' => $idl, 'nIdUbicacion' => $idu));
			}
			// Añade el contado
			
			$res = $this->_add2($idl, $ids, $idt, $ct);
			if ($res !== TRUE)
			{
				$this->out->error($res);	
			}
			$this->out->success();	
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));				
	}

	/**
	 * Devuelve el stock contado de las secciones
	 * @return HTML_FILE
	 */
	function stocks()
	{
		$this->userauth->roleCheck($this->auth.'.get_list');
		$data['stocks'] = $this->reg->stocks();
		$body = $this->load->view('catalogo/stocksecciones', $data, TRUE);
		$this->out->html_file($body, $this->lang->line('Stock contado por secciones'), 'iconoReportTab');		
	}

	/**
	 * Asigna el stock contado
	 * @param int $idmas Id del motivo de regulación para cuando hay más unidades
	 * @param int $idmenos Id del motivo de regulación para cuando hay menos unidades
	 * @return MSG/FORM 
	 */
	function asignar($idmas = null, $idmenos = null)
	{
		$this->userauth->roleCheck($this->auth . '.asignar');
		
		$idmas 	= isset($idmas)?$idmas:$this->input->get_post('idmas');
		$idmenos 	= isset($idmenos)?$idmenos:$this->input->get_post('idmenos');
		
		if (is_numeric($idmas) && is_numeric($idmenos))
		{
			set_time_limit(0);
			if (!$this->reg->asignar($idmas, $idmenos))
				$this->out->error($this->reg->error_message());
			return $this->diferencias();
		}	
		$this->_show_js('asignar', 'stocks/asignarcontado.js');
	}

	/**
	 * Lista las diferencias de stock
	 * @return MSG/FORM 
	 */
	function diferencias()
	{
		$this->userauth->roleCheck($this->auth . '.asignar');
	
		$cambios = $this->reg->diferencias();
		$data = array();
		$sec = array();
		$this->load->model('generico/m_seccion');
		foreach($cambios as $reg)
		{
			$cod = $reg['cCodigo'];
			$cod = explode('.', $reg['cCodigo']);
			$padre = $cod[0];
			if (!isset($sec[$padre]))
			{	
				$sec[$padre] = $this->m_seccion->load($padre);
			}
			$data[$sec[$padre]['cNombre']][$reg['cNombre']][] = $reg;
		}
		$data['cambios'] = $data;
		$body = $this->load->view('stocks/cambiostock', $data, TRUE);
		#echo $body; die();
		$this->out->html_file($body, $this->lang->line('Diferencias de stock'), 'icon-accept');		
	}

	/**
	 * Cuenta las devoluciones cerradas como STOCK
	 * @return MSG
	 */
	function devoluciones()
	{
		set_time_limit(0);
		$this->load->model('compras/m_devolucion');
		$devs = $this->m_devolucion->get(null, null, null, null, 'nIdEstado=' . DEVOLUCION_STATUS_CERRADA);
		$count = 0;
		$count2 = 0;
		$this->db->trans_commit();
		foreach($devs as $dev)
		{
			$dev = $this->m_devolucion->load($dev['nIdDevolucion'], 'lineas');
			$idt = $this->config->item($dev['bDeposito']?'bp.contarstocks.deposito':'bp.contarstocks.firme');
			foreach($dev['lineas'] as $linea)
			{
				$res = $this->_add2($linea['nIdLibro'], $linea['nIdSeccion'], $idt, $linea['nCantidad']);
				if ($res !== TRUE)
				{
					$this->db->trans_rollback();
					$this->out->error($res);	
				}
				++$count;
				$count2 += $linea['nCantidad'];
			}
		}
		$this->db->trans_commit();
		$this->out->dialog($this->lang->line('Contar devoluciones'), sprintf($this->lang->line('devoluciones-contadas'), count($devs), $count, $count2)); 
	}

	/**
	 * Cuenta un albarán de salida Factura como STOCK
	 * @return MSG
	 */
	function albaransalida($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.asignar');
		$id 	= isset($id)?$id:$this->input->get_post('id');
		set_time_limit(0);
		if (is_numeric($id))
		{
			$this->load->model('ventas/m_albaransalida');
			$count = 0;
			$count2 = 0;
			$this->db->trans_commit();
			$alb = $this->m_albaransalida->load($id, 'lineas');
			$idf = $this->config->item('bp.contarstocks.firme');
			$idd = $this->config->item('bp.contarstocks.deposito');
			#var_dump($alb['lineas']);
			foreach($alb['lineas'] as $linea)
			{
				if (!isset($linea['nEnFirme'])) $linea['nEnFirme'] = 0;
				if (!isset($linea['nEnDeposito'])) $linea['nEnDeposito'] = 0;
				if ($linea['nEnFirme']!=0)
				{
					$res = $this->_add2($linea['nIdLibro'], $linea['nIdSeccion'], $idf, $linea['nEnFirme']);
					if ($res !== TRUE)
					{
						$this->db->trans_rollback();
						$this->out->error($res);	
					}
					$count2 += $linea['nEnFirme'];
				}
				if ($linea['nEnDeposito']!=0)
				{
					$res = $this->_add2($linea['nIdLibro'], $linea['nIdSeccion'], $idd, $linea['nEnDeposito']);
					if ($res !== TRUE)
					{
						$this->db->trans_rollback();
						$this->out->error($res);	
					}
					$count2 += $linea['nEnDeposito'];
				}
				if ($linea['nEnFirme']==0 && $linea['nEnDeposito']==0 && $linea['nCantidad']!=0)
				{
					$res = $this->_add2($linea['nIdLibro'], $linea['nIdSeccion'], $idf, $linea['nCantidad']);
					if ($res !== TRUE)
					{
						$this->db->trans_rollback();
						$this->out->error($res);	
					}
					$count2 += $linea['nCantidad'];
				}
				++$count;
			}
			$this->db->trans_commit();
			$this->out->dialog($this->lang->line('Contar albarán salida'), sprintf($this->lang->line('albaransalida-contado'), $id, $count, $count2));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));				
	}

	/**
	 * Cuenta un albarán de salida de TPV como STOCK
	 * @return MSG
	 */
	function albaransalida2($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.asignar');
		$id 	= isset($id)?$id:$this->input->get_post('id');
		set_time_limit(0);
		if (is_numeric($id))
		{
			$this->load->model('ventas/m_albaransalida2');
			$count = 0;
			$count2 = 0;
			$this->db->trans_commit();
			$alb = $this->m_albaransalida2->load($id, 'lineas');
			$idf = $this->config->item('bp.contarstocks.firme');
			$idd = $this->config->item('bp.contarstocks.deposito');
			#var_dump($alb['lineas']);
			foreach($alb['lineas'] as $linea)
			{
				if (!isset($linea['nEnFirme'])) $linea['nEnFirme'] = 0;
				if (!isset($linea['nEnDeposito'])) $linea['nEnDeposito'] = 0;
				if ($linea['nEnFirme']!=0)
				{
					$res = $this->_add2($linea['nIdLibro'], $linea['nIdSeccion'], $idf, $linea['nEnFirme']);
					if ($res !== TRUE)
					{
						$this->db->trans_rollback();
						$this->out->error($res);	
					}
					$count2 += $linea['nEnFirme'];
				}
				if ($linea['nEnDeposito']!=0)
				{
					$res = $this->_add2($linea['nIdLibro'], $linea['nIdSeccion'], $idd, $linea['nEnDeposito']);
					if ($res !== TRUE)
					{
						$this->db->trans_rollback();
						$this->out->error($res);	
					}
					$count2 += $linea['nEnDeposito'];
				}
				if ($linea['nEnFirme']==0 && $linea['nEnDeposito']==0 && $linea['nCantidad']!=0)
				{
					$res = $this->_add2($linea['nIdLibro'], $linea['nIdSeccion'], $idf, $linea['nCantidad']);
					if ($res !== TRUE)
					{
						$this->db->trans_rollback();
						$this->out->error($res);	
					}
					$count2 += $linea['nCantidad'];
				}
				++$count;
			}
			$this->db->trans_commit();
			$this->out->dialog($this->lang->line('Contar albarán salida'), sprintf($this->lang->line('albaransalida-contado'), $id, $count, $count2));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));				
	}

	/**
	 * Imprime etiquetas con cantidades
	 * @return MSG
	 */
	function cantidades()
	{
		$this->load->library('Etiquetas');
		$etiquetas = array(
			'1', '2', '3', '4', '5', '6', '7', '8', '9', '10',
			'15', '20', '25', '30', '35', '40', '45', '50',
			'100', '150', '200');
		foreach($etiquetas as $etq)
		{
			$html = $this->load->view('stocks/cantidades2', array('etiquetas' => array($etq)), TRUE);
			$this->etiquetas->print_direct($html);
		}
		$this->out->success($this->lang->line('Etiquetas cantidad impresas'));
		#$this->out->html_file($message, $this->lang->line('Hoja de cantidades'), 'iconoReportTab', $this->config->item('bp.data.css'));
	}

	/**
	 * Convierte los contados en firme a depósito según la cantidad en stock actualmente
	 * @return MSG
	 */
	function depositos()
	{
		$this->userauth->roleCheck($this->auth . '.asignar');

		$this->load->model('catalogo/m_articuloseccion');
		$deps = $this->m_articuloseccion->get(null, null, null, null, 'nStockDeposito > 0');
		$iddp = $this->config->item('bp.contarstocks.deposito');
		$idfm = $this->config->item('bp.contarstocks.firme');
		#echo '<pre>';
		$count = 0;
		$this->db->trans_begin();
		foreach ($deps as $reg)
		{
			$fm = null;
			$dp = null;
			$cnt = $this->reg->get(null, null, null, null, "nIdLibro={$reg['nIdLibro']} AND nIdSeccion={$reg['nIdSeccion']}");
			foreach ($cnt as $r)
			{
				if ($r['nIdTipoStock'] == $iddp)
				{
					$dp = $r;
				}
				elseif ($r['nIdTipoStock'] == $idfm)
				{
					$fm = $r;
				}
			}
			$act = FALSE;
			if (isset($dp) && ($dp['nCantidad'] < $reg['nStockDeposito']) && isset($fm))
			{
				#echo "+DP {$reg['nStockDeposito']} CONTADO {$dp['nCantidad']} : nIdLibro={$reg['nIdLibro']} AND nIdSeccion={$reg['nIdSeccion']}\n";
				$ct = $reg['nStockDeposito'] - $dp['nCantidad'];
				$ct2 = min($fm['nCantidad'], $ct);
				#echo "Hay que ajustar mover {$ct2} FM a DP\n";
				if ($ct >0)
				{
					$dp['nCantidad'] += $ct2;
					$fm['nCantidad'] -= $ct2;
					$act = TRUE;
				}
			}
			elseif (isset($fm) && !isset($dp))
			{
				#echo "+DP {$reg['nStockDeposito']} NO CONTADO nIdLibro={$reg['nIdLibro']} AND nIdSeccion={$reg['nIdSeccion']}\n";
				$ct = min($reg['nStockDeposito'], $fm['nCantidad']);
				$ct2 = $fm['nCantidad'] - $ct;
				if ($ct > 0)
				{
					#echo "Hay que ajustar {$ct} a DP y {$ct2} a FM\n";
					$dp['nCantidad'] = $ct;
					$fm['nCantidad'] -= $ct;
					$act = TRUE;
				}
			}
			if ($act)
			{
				if (isset($dp))
				{
					if (isset($dp['nIdRegulacionStock']))
					{
						if (!$this->reg->update($dp['nIdRegulacionStock'], array('nCantidad' => $dp['nCantidad'])))
						{
							$this->db->trans_rollback();
							$this->out->error($this->reg->error_message());
						}
						++$count;
					}
					else
					{
						$dp['nIdTipoStock'] = $iddp;
						$dp['dCreacion'] = time();
						$dp['nIdSeccion'] = $reg['nIdSeccion'];
						$dp['nIdLibro'] = $reg['nIdLibro'];
						if ($this->reg->insert($dp) < 1)
						{
							$this->db->trans_rollback();
							$this->out->error($this->reg->error_message());
						}
						++$count;					
					}
				}
				if (isset($fm))
				{
					if ($fm['nCantidad'] == 0)
					{
						if (!$this->reg->delete($fm['nIdRegulacionStock']))
						{
							$this->db->trans_rollback();
							$this->out->error($this->reg->error_message());
						}
						++$count;
					}
					else
					{
						if (!$this->reg->update($fm['nIdRegulacionStock'], array('nCantidad' => $fm['nCantidad'])))
						{
							$this->db->trans_rollback();
							$this->out->error($this->reg->error_message());
						}
						++$count;						
					}
				}				
			}
		}
		$this->db->trans_commit();
		$this->out->success(sprintf($this->lang->line('stockcontado-depositos-ok'), $count));
	}

	/**
	 * Arregla los stocks contados de una sección madre y lo asigna a los hijos. Error creado por CECILIO al forzar.
	 * 
	 * @return TXT
	 */
	private function cecilio()
	{
		$this->load->model('catalogo/m_articuloseccion');
		$data = $this->reg->get(null, null, null, null, "nIdSeccion=856");
		var_dump(count($data));
		echo '<pre>';
		#$this->db->trans_begin();

		foreach ($data as $value) 
		{
			echo "MATA A CECILIO: {$value['nIdLibro']} -> {$value['nIdSeccion']}\n";
			$r = $this->reg->get(null, null, null, null, "nIdSeccion IN (102, 103, 710) AND nIdLibro={$value['nIdLibro']}");
			if (count($r) > 0)
			{
				echo "   +Se ADD AL YA CONTADO cantidad en contado\n";
				$r = $r[0];
				if (!$this->reg->update($r['nIdRegulacionStock'], array('nCantidad' => $r['nCantidad'] + $value['nCantidad'])))
				{
					$this->db->trans_rollback();
					$this->out->error($this->reg->error_message());
				}
				echo "   Se elimina el otro\n";
				if (!$this->reg->delete($value['nIdRegulacionStock']))
				{
					$this->db->trans_rollback();
					$this->out->error($this->reg->error_message());
				}
			}
			else
			{
				#Se busca la sección
				$sec = $this->m_articuloseccion->get(null, null, null, null, "nIdSeccion IN (102, 103, 710) AND nIdLibro={$value['nIdLibro']}");
				$esta = null;
				foreach ($sec as $s)
				{
					if ($s['nStockDeposito'] > 0 || $s['nStockFirme'] > 0)
					{
						$esta = $s;
					}
				}

				if (!$esta && count($sec) > 0)
					$esta = $sec[0];
				elseif (!$esta)
					$esta = array('nIdSeccion' => 103);

				echo "   -Se CAMBIA SECCION {$esta['nIdSeccion']}\n";
				
				if (!$this->reg->update($value['nIdRegulacionStock'], array('nIdSeccion' => $esta['nIdSeccion'])))
				{
					$this->db->trans_rollback();
					$this->out->error($this->reg->error_message());
				}
			}			
		}
		$this->db->trans_commit();
	}
}

/* End of file Stockcontado.php */
/* Location: ./system/application/controllers/stocks/Stockcontado.php */
