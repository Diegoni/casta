<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Estados
 *
 */
class Concurso extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Concurso
	 */
	function __construct()
	{
		parent::__construct('concursos.concurso', 'concursos/M_concurso', TRUE, null, 'Concursos');
	}

	/**
	 * Muestra el informe del resultado de la acción crear pedidos
	 * @return HTML
	 */
	private function _show_result()
	{
		$body = $this->messages->out($this->lang->line('Crear pedidos proveedor'));
		$this->out->html_file($body, $this->lang->line('Crear pedidos proveedor'), 'iconoConcursosPedirProveedorTab');
	}

	/**
	 * Pide los artículos del concurso a los proveedores. Crea pedidos nuevos, no reutiliza abiertos.
	 * @param  int $concurso Id del concurso
	 * @param  int $seccion  Id de la sección a pedir
	 * @param  int $direccion [description]
	 * @param  string $ref  Referencia del pedido
	 * @param  string $nota  Nota adicional
	 * @param  string $id Ids de la línea del concurso a pedir, separadas por ;
	 * @param string $cmpid Id del componente que realiza la tarea
	 * @return MSG
	 */
	function pedirproveedor($concurso = null, $seccion = null, $direccion = null, $ref = null, $nota = null, $id = null, $cmpid = null)
	{
		$this->userauth->roleCheck($this->auth.'.pedirproveedor');

		$concurso = isset($concurso)?$concurso:$this->input->get_post('concurso');
		$seccion = isset($seccion) ? $seccion : $this->input->get_post('seccion');
		$direccion = isset($direccion) ? $direccion : $this->input->get_post('direccion');
		$nota = isset($nota) ? $nota : $this->input->get_post('nota');
		$ref = isset($ref) ? $ref : $this->input->get_post('ref');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');

		if (!empty($seccion) && !empty($concurso) && !empty($direccion))
		{
			set_time_limit(0);
			$this->load->library('Messages');
			$bl = $this->reg->load($concurso);

			$this->messages->info(sprintf($this->lang->line('concursos_pedidosproveedor'), $bl['cDescripcion']));

			$this->load->model('concursos/m_pedidoconcursolinea');
			$this->load->model('concursos/m_estadolineaconcurso');
			$this->load->model('catalogo/m_articulo');
			$this->load->model('proveedores/m_proveedor');
			$this->load->model('compras/m_pedidoproveedor');			
			$this->load->model('compras/m_pedidoproveedorlinea');
			$this->load->model('concursos/m_pedidoconcursolineaaccion');

			$where = "Ext_Concursos.nIdConcurso={$concurso} AND nIdLineaPedidoProveedor IS NULL AND nIdEstado=" . CONCURSOS_ESTADO_LINEA_EN_PROCESO;
			$where .= ' AND nIdLineaPedidoConcurso NOT IN (SELECT Ext_LineasPedidoConcursoAcciones.nIdLineaPedidoConcurso FROM Ext_LineasPedidoConcursoAcciones WHERE nIdTipo=' . ACCION_ANTIGUO_VISTO . ')';

			if (!empty($id))
			{
				$ids = is_string($id)?preg_split('/\;/', $id):$id;
				$ids = array_unique($ids);
				foreach ($ids as $key => $value) 
				{
					if (!is_numeric($value)) unset($ids[$key]);
				}
				if (!empty($ids))
					$where .= ' AND nIdLineaPedidoConcurso IN (' . implode(',', $ids) . ')';
			}
			#var_dump($where); die();

			$apedir = $this->m_pedidoconcursolinea->get(null, null, null, null, $where);
			#echo array_pop($this->db->queries); die();
			#$this->db->trans_begin();
			$proveedores[] = array();			
			foreach ($apedir as $reg) 
			{
				if (isset($reg['nIdLibro']))
				{
					$art = $this->m_articulo->load($reg['nIdLibro']);
					$idpv = $this->m_articulo->get_proveedor_habitual($art);
					if (!isset($idpv))
					{
						# No hay proveedor
						$link = format_enlace_cmd($art['cTitulo'], site_url('catalogo/articulo/index/' . $art['nIdLibro']));
						$this->messages->error(sprintf($this->lang->line('concursos_pedirproveedor_sin_proveedor'), $link));
					}
					else
					{
						if (isset($proveedores[$idpv]))
						{
							# Caché de pedidos
							$idpd = $proveedores[$idpv]['idpd'];							
							$pv = $proveedores[$idpv]['pv'];
							$pvname = $proveedores[$idpv]['pvname'];
						}
						else
						{
							$pv = $this->m_proveedor->load($idpv);
							$pvname = format_name($pv['cNombre'], $pv['cApellido'], $pv['cEmpresa']);
							# Busca un pedido abierto del proveedor, de la biblioteca, y de la sección
							$pds = $this->m_pedidoproveedor->get(0, 1, 'dCreacion', 'ASC', 
								"nIdEstado=".PEDIDO_PROVEEDOR_STATUS_EN_CREACION ." AND nIdProveedor={$idpv} AND nIdSeccion={$seccion} AND nIdConcurso={$concurso}");
							if (count($pds) == 1)
							{
								$idpd = $pds[0]['nIdPedido'];							
							}
							else
							{
								# Crea un pedido nuevo
								$pedido = array(
									'nIdProveedor' 	=> $idpv,
									'nIdSeccion'	=> $seccion,
									'nIdConcurso' 	=> $concurso,
									'nIdEntrega'	=> $direccion,
									'cRefProveedor'	=> !empty($ref)?$ref:null,
									'tNotasExternas'=> !empty($nota)?$nota:null,
								);
								$idpd = $this->m_pedidoproveedor->insert($pedido);
								if ($idpd < 1)
								{
									#$this->db->trans_rollback();
									$this->messages->error(sprintf($this->lang->line('concursos_pedirproveedor_error_crear_pedido'), $pvname, $this->m_pedidoproveedor->error_message()));
									$this->_show_result();
								}
								$this->_add_nota(null, $idpd, NOTA_INTERNA, $this->lang->line('concursos_pedirproveedor_pedido_creado_nota'), $this->m_pedidoproveedor->get_tablename());
								$link = format_enlace_cmd($idpd, site_url('compras/pedidoproveedor/index/' . $idpd));
								$this->messages->info(sprintf($this->lang->line('concursos_pedirproveedor_pedido_creado'), $pvname, $link));
							}
						}
						# Ya tenemos pedido, añadimos línea
						$l2 = array(
							'nCantidad' 	=> 1,
							'nIdSeccion' 	=> $seccion,
							'nIdLibro' 		=> $art['nIdLibro'],
							'fPrecio' 		=> $art['fPrecio'],
							'fIVA' 			=> $art['fIVA'],
							'fRecargo' 		=> ($pv['bRecargo']==1)?$art['fRecargo']:0,
							'fDescuento' 	=> $this->m_articulo->get_descuento($art['nIdLibro'], $idpv),
							'nIdPedido'	 	=> $idpd,
							'cRefInterna'	=> $reg['cBiblioteca']
							);
						#var_dump($l2); die();
						$link_p = format_enlace_cmd($idpd, site_url('compras/pedidoproveedor/index/' . $idpd));
						$link_a = format_enlace_cmd($art['cTitulo'], site_url('catalogo/articulo/index/' . $art['nIdLibro']));
						if (($idln = $this->m_pedidoproveedorlinea->insert($l2)) <= 0)
						{
							#$this->db->trans_rollback();
							$this->messages->error(sprintf($this->lang->line('concursos_pedirproveedor_error_crear_linea_pedido'), $link_a, $pvname, $link_p, $this->m_pedidoproveedorlinea->error_message()));
							$this->_show_result();
						}
						$this->messages->info(sprintf($this->lang->line('concursos_pedirproveedor_crear_linea_pedido'), $link_a, $pvname, $link_p));
						#echo sprintf($this->lang->line('concursos_pedirproveedor_crear_linea_pedido'), $link_a, $pvname, $link_p); die();
						# Caché de pedidos
						$proveedores[$idpv]['idpd'] = $idpd;
						$proveedores[$idpv]['pv'] = $pv;
						$proveedores[$idpv]['pvname'] = $pvname;
						# Actualiza la línea de pedido del concurso
						# 
						if (!$this->m_pedidoconcursolinea->update($reg['nIdLineaPedidoConcurso'], 
							array(
								'nIdLineaPedidoProveedor' => $idln,
								'nIdEstado' => CONCURSOS_ESTADO_LINEA_A_PEDIR
								)))
						{
							#$this->db->trans_rollback();
							$this->messages->error($this->m_pedidoconcursolinea->error_message());
							$this->_show_result();							
						}
					}
				}
				else
				{
					$this->messages->error(sprintf($this->lang->line('concursos_pedirproveedor_sin_articulo'), $reg['cTitulo']));
				}
			}
			#$this->db->trans_commit();
			#$this->db->trans_rollback();
			$this->_show_result();
		}
		else
		{
			$data = array('cmpid' => $cmpid);
			if (!empty($id)) $data['id'] = $id;
			if (!empty($concurso)) $data['concurso'] = $concurso;
			$this->_show_js('pedirproveedor', 'concursos/pedir.js', $data);
		}
	}

	/**
	 * Estado del concurso
	 * @param  int $concurso Id del concurso
	 * @return HTML_FILE
	 */
	function estado($concurso = null)
	{
		$this->userauth->roleCheck($this->auth . '.estado');
		$concurso = isset($concurso) ? $concurso : $this->input->get_post('concurso');
		if (is_numeric($concurso))
		{
			$estado = $this->reg->estado($concurso);
			$datos = array();
			$biblioteca = array();
			$cuenta = array();
			foreach ($estado as $st)
			{
				$bibliotecas[$st['cBiblioteca']]['dto'] = $st['fDescuento'];
				$bibliotecas[$st['cBiblioteca']]['importe'] = $st['fImporte'];
				$bibliotecas[$st['cBiblioteca']]['coste'] = 
				$bibliotecas[$st['cBiblioteca']]['venta'] = 
				$bibliotecas[$st['cBiblioteca']]['cuenta'] = 
				$bibliotecas[$st['cBiblioteca']]['venta2'] = 
				$bibliotecas[$st['cBiblioteca']]['unidades'] = 0;

				$cuenta[$st['cEstado']] = $st['bSuma'];

				$datos[$st['cBiblioteca']][$st['cEstado']]['fCoste'] = $st['fCoste'];
				$datos[$st['cBiblioteca']][$st['cEstado']]['fVentaSinIVA'] = $st['fVentaSinIVA'];
				$datos[$st['cBiblioteca']][$st['cEstado']]['fVentaConIVA'] = $st['fVentaConIVA'];
				$datos[$st['cBiblioteca']][$st['cEstado']]['fCuenta'] = $st['fVentaConIVA'];
				$datos[$st['cBiblioteca']][$st['cEstado']]['nUnidades'] = $st['nUnidades'];
			}
			$data = array('bibliotecas' => $bibliotecas, 'datos' => $datos, 'cuenta' => $cuenta);
			#var_dump($datos, $data);die();
			$message = $this->load->view('concursos/estado2', $data, TRUE);
			#echo $message; die();
			$this->out->html_file($message, $this->lang->line('Estado del pedido'), 'iconoReportTab');
		}
		$this->_show_js('estado', 'concursos/concurso.js', array('url' => site_url('concursos/concurso/estado')));
	}

	/**
	 * Busca las editoriales que se parecen por nombre y las compara con asignaciones anteriores
	 * 
	 * @param  int $id Id del concurso
	 * @return HTML
	 */
	function editoriales($concurso = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$concurso = isset($concurso) ? $id : $this->input->get_post('concurso');
		//if (is_numeric($id))
		//{
		$data = $this->reg->editoriales();
		$data2 = array();
		foreach ($data as $reg)
		{
			if (isset($data2[$reg['cEditorial']]) && $data2[$reg['cEditorial']]['nContador'] > $reg['nContador'])
				continue;
			$data2[$reg['cEditorial']] = $reg;
		}
		$data3 = $this->reg->sin_editorial();

		$esta = array();
		foreach ($data3 as $k => $reg)
		{
			if (isset($data2[$reg['cEditorial']]))
			{
				unset($data3[$k]);
				$esta[] = $data2[$reg['cEditorial']];
			}
			//$data2[$reg['cEditorial']] = $reg;
		}
		if (count($esta) == 0)
			$this->out->success($this->lang->line('concurso-no-editoriales'));
		#var_dump($esta); die();
		sksort($esta, 'cEditorial');
		$data['editoriales'] = $esta;
		$message = $this->load->view('concursos/editoriales', $data, TRUE);
		$this->out->html_file($message, $this->lang->line('Incidencias editoriales') . ' ' . $concurso, 'icon-page-warning');
		//}
		//$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Asigna una editorial y proveedor a una editorial del concurso
	 * @param  string $malo Nombre de la editorial del concurso
	 * @param  int $ed   Id de la editorial correcta
	 * @param  int $pv   Id del proveedor de la editorial
	 * @return MSG
	 */
	function asignar_editorial($malo = null, $ed = null, $pv = null)
	{
		$this->userauth->roleCheck($this->auth . '.upd');
		$malo = isset($malo) ? $malo : $this->input->get_post('malo');
		$pv = isset($pv) ? $pv : $this->input->get_post('pv');
		$ed = isset($ed) ? $ed : $this->input->get_post('ed');
		#var_dump($malo, $ed, $pv); die();
		#$this->out->success($malo);
		if (!empty($malo))
		{
			$this->load->model('catalogo/m_articulo');
			$this->load->model('catalogo/m_editorial');
			$count = 0;

			$malo = utf8_decode(urldecode($malo));
			$data = $this->reg->sin_proveedor($malo);
			$this->db->trans_begin();
			foreach ($data as $libro)
			{
				if (is_numeric($ed))
				{
					if (!$this->m_articulo->update($libro['nIdLibro'], array('nIdEditorial' => $ed)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_articulo->error_message());
					}
					if (is_numeric($pv))
					{
						$reg = $this->m_editorial->load($ed);
						if (!isset($reg['nIdProveedor']))
						{
							if (!$this->m_articulo->update($libro['nIdLibro'], array('nIdProveedor' => $pv)))
							{
								$this->db->trans_rollback();
								$this->out->error($this->m_articulo->error_message());
							}
						}
					}
					++$count;
				}				
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('concurso-editoriales-asignadas'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra un listado de las editoriales que no se pueden pedir para asignar a las correctas
	 * @param  int $concurso Id del concursos
	 * @return HTML
	 */
	function asignar_editoriales($concurso = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$concurso = isset($concurso) ? $concurso : $this->input->get_post('concurso');
		//if (is_numeric($id))
		//{
		$data = $this->reg->sin_editorial();

		if (count($data) == 0)
			$this->out->success($this->lang->line('concurso-no-editoriales'));
		sksort($data, 'cEditorial');
		$data['editoriales'] = $data;
		$message = $this->load->view('concursos/editoriales2', $data, TRUE);
		$this->out->html_file($message, $this->lang->line('Asignar editoriales') . ' ' . $concurso, 'icon-page-warning');
		//}
		//$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Busca una editorial por texto
	 * @param  string $term Texto
	 * @return JSON
	 */
	function search_editorial($term = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$term = isset($term) ? $term : $this->input->get_post('term');
		$this->load->model('catalogo/m_editorial');
		$data = $this->m_editorial->get(0, 10, 'cNombre', 'ASC', null, null, $term);
		#var_dump($data); die();
		#echo '<pre>'; print_r($this->db->queries); echo '</pre>';
		//[{"id":"Upupa epops","label":"Eurasian Hoopoe","value":"Eurasian Hoopoe"}
		$res = array();
		foreach ($data as $reg)
		{
			$res[] = array(
					'id' 		=> $reg['id'], 
					'label' 	=> $reg['cNombre'], 
					'value' 	=> $reg['cNombre'],
					'proveedor'	=> $reg['nIdProveedor'],
					'name'		=> $reg['cProveedor']
				);
		}
		$this->out->send($res);
	}

	/**
	 * Incidencias de un albarán antes de ser asignado
	 * @param int $id Id del documento (separados por ; si hay varios)
	 * @param in $concurso Id del concurso
	 * @return MSG
	 */
	function unificar()
	{
		$this->userauth->roleCheck($this->auth . '.unificar');
		$this->load->model('catalogo/m_aunificar');
		$data = $this->m_aunificar->get();
		if (count($data) > 0)
		{
			$message = $this->load->view('concursos/unificar', array('libros' => $data), TRUE);
			$this->out->html_file($message, $this->lang->line('Unificar'), 'iconoUnficarArticuloTab');
		}
		$this->out->success($this->lang->line('concurso-no-unificar'));
	}

	/**
	 * Mueve libros de una sección al concurso
	 * @return FORM
	 */
	function mover()
	{
		#$this->_show_js('asignar', 'concursos/mover.js');
		$this->_show_form('asignar', 'concursos/mover.js', $this->lang->line('Mover Libros'));
	}

	/**
	 * Asigna alternativas a libros en proceso
	 * @return HTML
	 */
	function alternativas()
	{
		$this->userauth->roleCheck($this->auth.'.alternativas');

		$this->load->model('concursos/m_estadolineaconcurso');
		$this->load->model('concursos/m_pedidoconcursolinea');
		$data = $this->m_pedidoconcursolinea->get(null, null, null, null, 'nIdEstado=' . CONCURSOS_ESTADO_LINEA_EN_PROCESO);
		$st = array(CONCURSOS_ESTADO_LINEA_RECIBIDO_PROVEEDOR);
		$st = '(' . implode(',', $st) . ')';
		#var_dump($data); die();
		$sin = 0;
		$sin_cdu = 0;
		$con = array();
		$errores = array();
		foreach ($data as $k => $reg)
		{
			if (isset($reg['cCDU']) && trim($reg['cCDU']) != '')
			{
				$cdu = $this->db->escape($reg['cCDU']);
				$alt = $this->m_pedidoconcursolinea->get(0, 1, 'Cat_Fondo.fPrecio', 'DESC',
					"nIdEstado IN {$st} AND nIdBiblioteca <> {$reg['nIdBiblioteca']} AND cCDU = {$cdu} " . 
					" AND nIdLibro NOT IN (SELECT nIdLibro FROM Ext_LineasPedidoConcurso WHERE nIdBiblioteca = {$reg['nIdBiblioteca']})");
				if (count($alt) == 1)
				{
					$new = $this->m_pedidoconcursolinea->load($alt[0]['nIdLineaPedidoConcurso']);

					unset($new['nIdLineaPedidoConcurso']);
					unset($new['nIdLineaPedidoProveedor']);
					unset($new['nIdLineaAlbaranEntrada']);
					unset($new['nIdLineaPedidoCliente']);
					unset($new['nIdLineaAlbaranSalida']);
					unset($new['nIdLineaDevolucion']);
					unset($new['dCreacion']);
					unset($new['cCUser']);
					unset($new['dAct']);
					unset($new['cAUser']);
					unset($new['cConcurso']);
					unset($new['cProveedor']);

					$new['nIdBiblioteca'] = $reg['nIdBiblioteca'];
					$new['nIdEstado'] = CONCURSOS_ESTADO_LINEA_EN_PROCESO;
					$new['bNuevo'] = TRUE;

					$id_n = $this->m_pedidoconcursolinea->insert($new);
					if ($id_n > 0)
					{
						$this->m_pedidoconcursolinea->update($reg['nIdLineaPedidoConcurso'], array(
								'nIdEstado' 		=> CONCURSOS_ESTADO_LINEA_CAMBIADO_POR_OTRO,
								'nIdCambioLibro' 	=> $id_n							
							));		
						$reg['alt']	= $alt[0];
						$con[] = $reg;
					}
					else
					{
						$reg['error'] = $this->m_pedidoconcursolinea->error_message();
						$errores[] = $reg;
					}
				}
				else
				{
					++$sin;
				}
			}
			else
			{
				++$sin_cdu;
			}
		}
		$message = $this->load->view('concursos/alternativas', array('errores' => $errores, 'libros' => $con, 'sin' => $sin, 'sin_cdu' => $sin_cdu), TRUE);
		#echo $message; die();
		$this->out->html_file($message, $this->lang->line('Alternativas'), 'iconoAlternativasTab');
		#var_dump($data); die();
	}

	/**
	 * Indica libros como NO DISPONIBLES hasta ajustar el importe indicado
	 * @param  int $biblioteca Id de la biblioteca
	 * @param  float $importe    Imoporte a ajustar
	 * @return MSG/FORM
	 */
	function ajustar($biblioteca = null, $importe = null)
	{
		$this->userauth->roleCheck($this->auth.'.ajustar');

		$biblioteca = isset($biblioteca)?$biblioteca:$this->input->get_post('biblioteca');
		$importe = isset($importe) ? $importe : $this->input->get_post('importe');

		if (is_numeric($biblioteca) && is_numeric($importe))
		{
			$this->load->model('concursos/m_estadolineaconcurso');
			$this->load->model('concursos/m_pedidoconcursolinea');
			$importe = (float) $importe;
			$data = $this->m_pedidoconcursolinea->get(null, null, 'ISNULL(bNuevo, 0) ASC, Cat_Fondo.fPrecio', 'DESC', 'nIdEstado=' . CONCURSOS_ESTADO_LINEA_EN_PROCESO . 
				" AND Ext_Bibliotecas.nIdBiblioteca={$biblioteca}");			
			foreach ($data as $k => $reg)
			{
				#var_dump($reg); die();
				if ($importe <= 0) break;
				$importe -= ($reg['fPrecio2'] * 1.04) * 0.85;
				$this->m_pedidoconcursolinea->update($reg['nIdLineaPedidoConcurso'], array(
						'nIdEstado' 		=> CONCURSOS_ESTADO_LINEA_NO_DISPONIBLE
					));
			}
			$this->out->dialog($this->lang->line('Ajustar'), sprintf($this->lang->line('ajustar-res'), format_price($importe)));
		}
		$this->_show_js('ajustar', 'concursos/ajustar.js');
	}

	/**
	 * Muestra un formulario para crear los teixells
	 * @return FORM
	 */
	function teixells()
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$this->_show_form('get_list', 'concursos/teixells2.js', $this->lang->line('Teixells'));
	}

	/**
	 * Añade un teixell a la cola del usuario
	 * @param string $code Texto
	 * @param int $cantidad Unidades a imprimir
	 * @param int Tipo de etiqueta
	 * @return  MSG
	 */
	function add_teixell($code = null, $cantidad = null, $tipo = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');

		$code = isset($code) ? $code : $this->input->get_post('code');
		$cantidad = isset($cantidad) ? $cantidad : $this->input->get_post('cantidad');
		$tipo = isset($tipo) ? $tipo : $this->input->get_post('tipo');

		if (trim($code) != '') 
		{
			$this->load->model('concursos/m_teixell');
			if (!is_numeric($cantidad) || $cantidad < 1) $cantidad = 1;
			do {
				if (!$this->m_teixell->insert(array('cTexto' => $code, 'nTipo' => $tipo)))
					$this->out->error($this->m_teixell->error_message());
				--$cantidad;
			} while ($cantidad > 0);
		}
		$this->out->success();
	}

	/**
	 * Separar un texto de Teixell en líneas según las normas de GENE
	 * @param  string $value Texto
	 * @param  int $count Número de líneas
	 * @param int $caracteres Número de caracteres por línea
	 * @return array
	 */
	private function separar($value, $count, $caracteres)
	{
		$value = str_replace('"-', '"%%%', $value);
		$value = str_replace('.(', '(', $value);
		$value = preg_replace('/(\(.+\))/', ' $1 ', $value);
		$value = preg_replace('/(\".+\")/', ' $1 ', $value);
		$value = str_replace('.', ' .', $value);
		$value = str_replace('-', ' -', $value);
		$value = str_replace('%%%', '-', $value);
		$lineas = explode(' ', $value);
		$l = array();
		foreach ($lineas as $k => $v)
		{
			if (!empty($v)) 
			{
				$l[] = substr(trim($v), 0, $caracteres);
				--$count;
			}
		}
		if ($count > 0 && (strtoupper(substr($l[0], 0, 1)) == 'I' || strtoupper(substr($l[0], 0, 2)) == 'JN'))
		{
			$l = array_merge(array_fill(0, $count, ' '), $l);
			$count = 0;
		}
		elseif (floor($count / 2) > 0)
		{
			$l = array_merge(array_fill(0, floor($count / 2), ' '), $l);
			$count -= floor($count / 2);
		}

		while ($count > 0)
		{
			$l[] = ' ';
			--$count;
		}
		#var_dump($l); die();

		return $l;
	}

	/**
	 * Imprime los teixells
	 * @param  string $username Usuario
	 * @return MSG
	 */
	function imprimir_teixells($username = null, $row = null, $column = null)
	{

		$this->userauth->roleCheck($this->auth . '.get_list');

		$row = isset($row) ? $row : $this->input->get_post('row');
		$column = isset($column) ? $column : $this->input->get_post('column');
		$username = isset($username) ? $username : $this->input->get_post('username');

		if (empty($row) || empty($column))
		{
			$this->_show_js('get_list', 'concursos/print.js');
		}

		if (empty($username)) 
			$username = $this->userauth->get_username();

		if (!empty($username)) 
		{
			$this->load->model('concursos/m_teixell');
			$this->load->library('Etiquetas');

			$filas = $this->config->item('bp.concurso.filas');
			$columnas = $this->config->item('bp.concurso.columnas');
			$lineas = $this->config->item('bp.concurso.lineas');
			$caracteres = $this->config->item('bp.concurso.caracteres');

			$caracteres2 = $this->config->item('bp.concurso.caracteres2');
			$caracteres3 = $this->config->item('bp.concurso.caracteres3');

			$username = $this->db->escape($username);

			$count = 0;
			$etq = array();
			if (empty($row)) $row = 1;
			if (empty($column)) $column = 1;
			--$row;
			--$column;
			$row_orig = $row;
			$column_orig = $column;
			$row = $row % $filas;
			$column = $column % $columnas;
			$paginas = 0;

			# Formato vertical. General
			$data = $this->m_teixell->get(null, null, 'dCreacion', 'ASC', "cCUser={$username} AND (nTipo IS NULL OR nTipo IN (1, 8))");
			foreach ($data as $value) 
			{
				$etq[$paginas][$row][$column] = array($value['nTipo'], $this->separar($value['cTexto'], $lineas, $caracteres));
				++$count;
				++$column;
				if ($column >= $columnas) 
				{
					++$row;
					$column = 0;
				}
				if ($row >= $filas)
				{
					++$paginas;
					$row = 0;
					$column = 0;
				}
			}
			$total = ($row) * $columnas + $column + 1;
			++$paginas;

			# Rellena la cuadrilla
			require(DIR_CONTRIB_PATH . 'fpdf17/cellpdf.php');

			$pdf = new CellPDF('L','mm','A4');
			$w = 25.4;
			$h = 48.5;
			$left = 8;
			$top = 8;
			$pdf->SetMargins(0, 0);
			$pdf->SetAutoPageBreak(FALSE);
			$pdf->SetTitle($this->lang->line('Etiquetas') . ' ' . $username);
			$pdf->SetAuthor($this->config->item('bp.application.name'));
			$pdf->SetFont('Arial', 'B', 16);
			$p = 0;
			if (count($etq) > 0)
			{
				while ($paginas > 0)
				{
					$pdf->AddPage();
					for($i = 0; $i < $filas; $i++)
					{
						for($j = 0; $j < $columnas; $j++)
						{
							if (isset($etq[$p][$i][$j]))
							{
								$var = isset($etq[$p][$i][$j][1])?implode("\n", $etq[$p][$i][$j][1]):' ';
								if (in_array($etq[$p][$i][$j][0], array(8)))
								{
									$pdf->SetFont('Arial', 'B', 20);
								}
								else
									$pdf->SetFont('Arial', 'B', 16);
								$this->cell($pdf, $i, $j, $left, $top, $w, $h, $var);
							}
						}
					}
					++$p;
					--$paginas;
				}
			}

			#Formato vertical
			$data = $this->m_teixell->get(null, null, 'dCreacion', 'ASC', "cCUser={$username} AND NOT (nTipo IS NULL OR nTipo IN (1, 8))");
			$etq = array();
			$paginas = 0;
			if ($p > 0)
			{
				$row = $column = 0;
			}
			else
			{
				$row = $row_orig % $columnas;
				$column = $column_orig % $filas;
			}
			foreach ($data as $value) 
			{
				$etq[$paginas][$row][$column] = array($value['nTipo'], $value['cTexto']); #$this->separar($value['cTexto'], $lineas2, $caracteres2);
				++$count;
				++$column;
				if ($column >= $filas) 
				{
					++$row;
					$column = 0;
				}
				if ($row >= $columnas)
				{
					++$paginas;
					$row = 0;
					$column = 0;
				}
			}
			$total = ($column) * $filas + $row + 1;
			++$paginas;

			$h = 25.4;
			$w = 48.5;
			$left = 10;
			$top = 8;
			$p = 0;
			$align = 'L';
			#var_dump($etq); die();
			while ($paginas > 0)
			{
				$pdf->AddPage('P');
				for($i = 0; $i < $columnas; $i++)
				{
					for($j = 0; $j < $filas; $j++)
					{
						if (isset($etq[$p][$i][$j]))
						{
							/*if (in_array($etq[$p][$i][$j][0], array(8)))
							{
								$pdf->SetFont('Arial', 'B', 20);
								$align = 'L';
							}
							else*/if (in_array($etq[$p][$i][$j][0], array(4, 6)) || strlen($etq[$p][$i][$j][1]) > $caracteres3)
							{
								$pdf->SetFont('Arial', 'B', 12);
								$align = 'L';
							}
							elseif (in_array($etq[$p][$i][$j][0], array(5, 3, 7)) || strlen($etq[$p][$i][$j][1]) > $caracteres2)
							{
								$pdf->SetFont('Arial', 'B', 14);
								$align = 'L';
								if ($etq[$p][$i][$j][0] == 7)
								{
									$etq[$p][$i][$j][1] = "\n\n\n\n" . $etq[$p][$i][$j][1];
									$align = 'C';
								}
							}
							else
							{
								$pdf->SetFont('Arial', 'B', 16);
								$align = 'L';
							}
							$this->cell($pdf, $i, $j, $left, $top, $w, $h, $etq[$p][$i][$j][1], $align);
						}
					}
				}
				++$p;
				--$paginas;
			}

			$this->load->library('HtmlFile');
			$filename = time() . '.pdf';
			$pdffile = $this->obj->htmlfile->pathfile($filename);
			$pdf->Output($pdffile, 'F');
			$url = $this->htmlfile->url($filename);
			$this->out->url($url, $this->lang->line('Etiquetas'), 'iconoReportTab');
		}

		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Genera una Celda para la hoja PDF
	 * @param  CellPDF $pdf    Objeto PDF
	 * @param  int $row    Fila (empezando por 0)
	 * @param  int $column Columna (empezando por 0)
	 * @param  int $left   mm de margen a la izquierda
	 * @param  int $top    mm de margen superior
	 * @param  int $w      mm ancho
	 * @param  int $h      mm alto
	 * @param  int $text   Texto para la celda
	 * @return null
	 */
	private function cell($pdf, $row, $column, $left, $top, $w, $h, $text, $align = 'C')
	{
		$pdf->SetY($top + $row  * $h);
		$pdf->SetX($left + $column * $w);
		$pdf->Cell($w, $h, utf8_decode($text), 0, 0, $align);
	}

	/**
	 * Imprime los teixells
	 * @param  string $username Usuario
	 * @return MSG
	 */
	function clear_teixells($username = null)
	{

		$this->userauth->roleCheck($this->auth . '.get_list');
		$username = isset($username) ? $username : $this->input->get_post('username');


		if (empty($username)) 
			$username = $this->userauth->get_username();

		if (!empty($username)) 
		{
			$username = $this->db->escape($username);

			$this->load->model('concursos/m_teixell');
			$data = $this->m_teixell->get(null, null, 'dCreacion', 'ASC', "cCUser={$username}");
			$count = 0;
			$this->db->trans_begin();
			foreach ($data as $reg) 
			{
				if (!$this->m_teixell->delete($reg['nIdTeixell']))
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_teixell->error_message());
				}
				--$count;
			}
			#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('teixell-borrado'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Imprime los teixells
	 * @param  string $username Usuario
	 * @return MSG
	 */
	function get_teixells($username = null)
	{

		$this->userauth->roleCheck($this->auth . '.get_list');
		$username = isset($username) ? $username : $this->input->get_post('username');


		if (empty($username)) 
			$username = $this->userauth->get_username();

		if (!empty($username)) 
		{
			$username = $this->db->escape($username);

			$this->load->model('concursos/m_teixell');
			$data = $this->m_teixell->get(null, null, 'dCreacion', 'ASC', "cCUser={$username}");
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra un formulario para consultar el precio e indicar que está catalogado
	 * @return FORM
	 */
	function precios()
	{
		$this->_show_form('get_list', 'concursos/precios2.js', $this->lang->line('Consulta de precios'));
	}

	/**
	 * Obtiene el artículo del pedido según el código
	 * @param  string $code  ISBN/EAN/Título
	 * @param  int $biblioteca Id de la biblioteca
	 * @param  int  $sala       Id de la sala
	 * @param  boolean $catalogar  Indica que se ha de catalogar
	 * @param  int $estado     Id del estado en el que tiene que estar el artículo
	 * @return  array
	 */
	private function _get_code($code, $biblioteca, $sala = null, $catalogar = FALSE, $estado = null)
	{
		$this->load->library('ISBNEAN');
		$this->load->model('concursos/m_estadolineaconcurso');
		$this->load->model('concursos/m_pedidoconcursolinea');
		$ean = $this->isbnean->to_ean($code);
		$isbn = $this->isbnean->to_isbn($code);

		if ($ean || $isbn)
		{
			$isbn_e = $this->db->escape($isbn);
			$ean_e = $this->db->escape($ean);
			if ($isbn)
			{
				$where[] = "cISBN={$isbn_e} OR Cat_Fondo.cISBN={$isbn_e}";
			}
			if ($ean)
			{
				$where[] = "cEAN={$ean} OR cISBN={$ean_e} OR Cat_Fondo.nEAN={$ean} OR Cat_Fondo.cISBN={$ean_e}";
			}
			$where = '(' . implode(' OR ', $where) . ')';
		}
		elseif (is_numeric($code))
		{
			$where = "(nIdLibro={$code} OR nIdLineaPedidoConcurso={$code})";
		}
		else
		{
			$this->load->helper('parsersearch');
			$value = $this->db->escape_str($code);
			$where = '( ' . boolean_sql_where($value, 'cTitulo') . 
				 ' OR ' . boolean_sql_where($value, 'Cat_Fondo.cTitulo') . ')';
		}
		if (is_numeric($sala))
			$where .= " AND nIdSala={$sala}";
		if (is_numeric($biblioteca))
			$where .= " AND nIdBiblioteca={$biblioteca}";

		if ($catalogar)
			$where .= ' AND nIdEstado IN (' . CONCURSOS_ESTADO_LINEA_RECIBIDO_PROVEEDOR . ')';

		if ($estado)
			$where .= ' AND nIdEstado=' . $estado;

		#var_dump($where); die();
		#$res = $this->m_pedidoconcursolinea->get(null, null, null, null, $where);
		#echo '<pre>'; echo array_pop($this->db->queries); echo '</pre>'; die();
		#var_dump($res); die();
		return $this->m_pedidoconcursolinea->get(null, null, null, null, $where);
	}

	/**
	 * Lee el precio y cambia el estado a CATALOGADO de una línea de pedido de una biblioteca dada
	 * @param  string $code  ISBN/EAN/Título
	 * @param  int $biblioteca Id de la biblioteca
	 * @return DATA
	 */
	function check_precio($code = null, $biblioteca = null, $catalogar = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');

		$code = isset($code) ? $code : $this->input->get_post('code');
		$biblioteca = isset($biblioteca) ? $biblioteca : $this->input->get_post('biblioteca');
		$catalogar = isset($catalogar) ? $catalogar : $this->input->get_post('catalogar');
		$catalogar = format_tobool($catalogar);
		#var_dump($catalogar); die();

		$data['success'] = TRUE;
		if (trim($code) != '') 
		{
			$l = $this->_get_code($code, $biblioteca, null, $catalogar);
			if (count($l) > 0)
			{
				if (count($l) > 1)
				{
					$data['cTitulo'] = $this->lang->line('---VARIOS---');
					foreach ($l as $value) 
					{
						$data['cTitulo'] .=  '<br/>(' . $value['nIdLineaPedidoConcurso'] . ')  (' . $value['nIdLibro'] . ')' . $value['cTitulo2'] . ' [' . $value['cEstado'] . ']';
					}
				}
				else
				{
					$data = $l[0];
					#var_dump($data); die();
					$data['fPrecio'] = $l[0]['fPVP2'];
					$data['success'] = TRUE;
					if (in_array($data['nIdEstado'], array(CONCURSOS_ESTADO_LINEA_RECIBIDO_PROVEEDOR)) && $catalogar)
					{
	 					$this->m_pedidoconcursolinea->update($data['nIdLineaPedidoConcurso'], array(
							'nIdEstado' => CONCURSOS_ESTADO_LINEA_CATALOGADO
						));
	 					$data['cTitulo'] = $this->lang->line('---CATALOGADO---') . '<br/>' . $data['cTitulo2'];
	 				}
	 				elseif ($catalogar)
	 				{
	 					$data['cTitulo'] = $this->lang->line('---NO SE CATALOGA---') . '<br/>' . $data['cTitulo2'] . ' [' . $data['cEstado'] . ']';
	 				}
	 				else
	 				{
 						$data['cTitulo'] = $data['cTitulo2'] . ' [' . $data['cEstado'] . ']';
	 				}
				}
			}
			else 
			{
				$data['success'] = TRUE;	
				$data['cTitulo'] = $this->lang->line('---NO ENCONTRADO---');
			}				
		}
		$this->out->send($data);
	}

	/**
	 * Ajuste de los precios de las líneas de pedido para llegar al importe necesario
	 * @param  int $origen  Id Biblioteca de donde sacar los títulos
	 * @param  int $destino Id Biblioteca destino de los títulos
	 * @param  float $importe Importe a cuadrar
	 * @return TEXT
	 */
	private function ajustar3($origen, $destino, $importe)
	{
		echo "ORIGEN: {$origen} DESTINO {$destino} IMPORTE: {$importe}\n";
		$this->load->model('concursos/m_pedidoconcursolinea');
		$this->load->model('concursos/m_estadolineaconcurso');
		$where = "nIdBiblioteca={$origen} AND nIdEstado=" . CONCURSOS_ESTADO_LINEA_RECIBIDO_PROVEEDOR .
		 " AND bNuevo = 1
		  AND nIdLibro NOT IN (SELECT nIdLibro FROM Ext_LineasPedidoConcurso WHERE nIdBiblioteca={$destino})";
		$data = $this->m_pedidoconcursolinea->get(null, null, 'Cat_Fondo.fPrecio', 'DESC', $where);
		foreach ($data as $reg) 
		{
			#var_dump($reg['nIdLineaPedidoConcurso']); 
			if (($reg['fPVP'] * 0.85) < $importe)
			{
				$this->m_pedidoconcursolinea->update($reg['nIdLineaPedidoConcurso'], array(
					'nIdBiblioteca' => $destino,
					'cBibid'		=> $origen
					));
				$importe -= ($reg['fPVP'] * 0.85);
				echo ($importe) . "\n";
			}
			if ($importe <= 0) break;
		}
		echo ($importe) . "\n\n";
	}

	/**
	 * Ajusta los precios del concurso para cuadrar el importe indicado. De uso interno
	 * @return TEXT
	 */
	function ajustar2()
	{
		return;
		echo '<pre>';
		$this->ajustar3(7, 3, 6.94);
		$this->ajustar3(10, 4, 516.98);
		$this->ajustar3(6, 4, 213.05);
		echo '</pre>';
	}

	/**
	 * Muestra la ventana para crear albaranes de salida
	 * @return FORM
	 */
	function albaransalida()
	{
		$this->_show_form('albaransalida', 'concursos/albaransalida.js', $this->lang->line('Albaran de Salida'));
	}

	/**
	 * Busca un albarán de salida abierto de la biblioteca y salas indicadas
	 * @param  int $biblioteca Id de la Biblioteca 
	 * @param  int $sala       Id de la Sala
	 * @return int             Id del aĺbarán encontrado
	 */
	private function _get_albaransalida($biblioteca, $sala = null)
	{
		$this->load->model('ventas/m_albaransalida');
		$where = 'nIdEstado=' . DEFAULT_ALBARAN_SALIDA_STATUS . ' AND nIdBiblioteca=' . $biblioteca;
		$where .=  (is_numeric($sala))?(' AND nIdSala=' . $sala):' AND nIdSala IS NULL';
		$al = $this->m_albaransalida->get(0, 1, null, null, $where);
		if (count($al) > 0)
		{
			return $al[0]['nIdAlbaran'];
		}
		return null;
	}

	/**
	 * Obtiene todas las líneas de albarán en proceso de la biblioteca y sala indicadas
	 * @param  int $biblioteca Id de la Biblioteca 
	 * @param  int $sala       Id de la Sala
	 * @return DATA
	 */
	function get_lineasalbaransalida($biblioteca = null, $sala = null)
	{
		$this->userauth->roleCheck($this->auth.'.albaransalida');

		$biblioteca = isset($biblioteca)?$biblioteca:$this->input->get_post('biblioteca');
		$sala = isset($sala)?$sala:$this->input->get_post('sala');

		if (is_numeric($biblioteca) /*&& is_numeric($sala)*/)
		{
			# Busca un albaran de salida abierto de la sala y la biblioteca
			$this->load->model('ventas/m_albaransalida');
			$al = $this->_get_albaransalida($biblioteca, $sala);
			if (isset($al))
			{
				$al = $this->m_albaransalida->load($al, 'lineas');
				$this->out->data($al['lineas']);
			}
			# Si no hay ninguno, manda la lista vacía
			$this->out->data(array());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Añade un nuevo artículo al albarán de salida de la biblioteca y serie indicados. Busca uno que ya existe
	 * o creo uno nuevo sino había
	 * @param  int $biblioteca Id de la Biblioteca 
	 * @param  int $sala       Id de la Sala
	 * @param int $seccion    Id de la sección 
	 * @param string $code       Código de artículo (Id artículo, Id línea pedido, ISBN, EAN o título)
	 * @return MSG
	 */
	function add_lineaalbaransalida($biblioteca = null, $sala = null, $seccion = null, $code = null)
	{
		$this->userauth->roleCheck($this->auth.'.albaransalida');

		$biblioteca = isset($biblioteca)?$biblioteca:$this->input->get_post('biblioteca');
		$sala = isset($sala)?$sala:$this->input->get_post('sala');
		$code = isset($code)?$code:$this->input->get_post('code');
		$seccion = isset($seccion)?$seccion:$this->input->get_post('seccion');

		if (is_numeric($biblioteca) /*&& is_numeric($sala)*/ && is_numeric($seccion) && !empty($code))
		{
			$this->load->model('concursos/m_estadolineaconcurso');
			$l = $this->_get_code($code, $biblioteca, $sala, null, CONCURSOS_ESTADO_LINEA_CATALOGADO);
			#var_dump($l); die();
			if (count($l) > 0)
			{
				$data = $l[0];
				#var_dump($data); die();
				$al = $this->_get_albaransalida($biblioteca, $sala);
				#var_dump($al); die();

				$this->db->trans_begin();

				# Crea un nuevo albarán si no existe otro 
				if (!isset($al))
				{
					# Crea el albaran de salida
					$this->load->model('concursos/m_biblioteca');
					$bl = $this->m_biblioteca->load($biblioteca);
					if (!$bl || !isset($bl['nIdCliente']))
						$this->out->error($this->lang->line('concurso-no-cliente-biblioteca'));

					$alb = array(
						'nIdCliente'	=> $bl['nIdCliente'],
						'nIdDireccion'	=> $bl['nIdDireccion'],
						'nIdBiblioteca'	=> $biblioteca,
						'nIdSala'		=> $sala
						);

					$al = $this->m_albaransalida->insert($alb);
					if ($al < 0)
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_albaransalida->error_message());
					}
				}

				# Comprueba stock
				$this->load->model('catalogo/m_articuloseccion');
				$sc = $this->m_articuloseccion->get(null, null, null, null, "nIdLibro={$data['nIdLibro']} AND nIdSeccion={$seccion}");
				if (count($sc) > 0)
				{
					/*if ($sc[0]['nStockFirme'] + $sc[0]['nStockDeposito'] < 1)
						$this->out->error($this->lang->line('no-stock-seccion'));*/
					if ($sc[0]['nStockFirme'] > 0)
					{
						$firme = 1;
						$deposito = 0;
					}
					else
					{
						$firme = 0;
						$deposito = 1;							
					}
					if ($firme == 0 && $deposito == 0)
						$firme = 1;

					# El albarán existe, se añade la línea
					$linea = array(
						'nIdLibro' 		=> $data['nIdLibro'],
						'nIdSeccion'	=> $seccion,
						'fDescuento'	=> $data['fDescuento'],
						'fIVA'			=> $data['fIVA'],
						'nCantidad'		=> 1,
						'fPrecio'		=> $data['fPrecio2'],
						'nEnFirme'		=> $firme,
						'nEnDeposito'	=> $deposito,
						'fCoste'		=> $data['fCoste'],
						'nIdAlbaran'	=> $al
						);
					#var_dump($linea); die();
					$this->load->model('ventas/m_albaransalidalinea');
					$id = $this->m_albaransalidalinea->insert($linea);
					if ($id < 0)
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_albaransalidalinea->error_message());
					}
					# Añade la referencia del albarán de salida a la línea del concurso
					if (!$this->m_pedidoconcursolinea->update($data['nIdLineaPedidoConcurso'], array(
						'nIdEstado' 			=> CONCURSOS_ESTADO_LINEA_EN_ALBARAN,
						'nIdLineaAlbaranSalida' => $id
						)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_pedidoconcursolinea->error_message());
					}

					$this->db->trans_commit();
					$this->out->success();
				}
				else
				{
					$this->out->error($this->lang->line('no-seccion-libro'));
				}
			}
			else 
			{
				$this->out->error($this->lang->line('---NO ENCONTRADO---'));
			}
			$this->out->success();			
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cierra el albarán
	 * @param  int $biblioteca Id de la Biblioteca 
	 * @param  int $sala       Id de la Sala
	 * @param  bool $nofacturable Indica que el albarán no es facturable
	 * @return MSG
	 */
	function cerrar_albaransalida($biblioteca = null, $sala = null, $nofacturable = null)
	{
		$this->userauth->roleCheck($this->auth.'.albaransalida');

		$biblioteca = isset($biblioteca)?$biblioteca:$this->input->get_post('biblioteca');
		$sala = isset($sala)?$sala:$this->input->get_post('sala');
		$nofacturable = isset($nofacturable)?$nofacturable:$this->input->get_post('nofacturable');

		$nofacturable = (empty($nofacturable))?FALSE:format_tobool($nofacturable);

		if (is_numeric($biblioteca) /*&& is_numeric($sala)*/)
		{
			$al = $this->_get_albaransalida($biblioteca, $sala);
			if (!isset($al))
				$this->out->error($this->lang->line('concursos-albaransalida-no-items'));
			$this->db->trans_begin();
			# No facturable?
			if (!$this->m_albaransalida->update($al, array('bNoFacturable' => $nofacturable)))
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_albaransalida->error_message());
			}
			#Cierre
			if (!$this->m_albaransalida->cerrar($al))
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_albaransalida->error_message());
			}
			$this->db->trans_commit();
			#$this->db->trans_rollback();
			#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();
			$link = format_enlace_cmd($al, site_url('ventas/albaransalida/index/' . $al));
			$this->out->dialog($this->lang->line('Cerrar albarán'), sprintf($this->lang->line('concursos-albaransalida-cerrado'), $link));
		}

		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra las posibles obras
	 * @param  int $concurso Id del concurso
	 * @return WEBAPP
	 */
	function obras($concurso = null)
	{
		$this->userauth->roleCheck($this->auth.'.obras');
		$concurso = isset($concurso) ? $concurso : $this->input->get_post('concurso');
		$this->load->model('concursos/m_pedidoconcursolinea');
		if (is_numeric($concurso))
		{
			$datos = $this->reg->obras($concurso);
			if (count($datos) > 0)
			{
				foreach ($datos as $key => $value) 
				{
					$datos[$key] = $this->m_pedidoconcursolinea->load($value);
				}
				$concurso = $this->reg->load($concurso);
				$data = array('concurso' => $concurso, 'datos' => $datos);
				#var_dump($datos); die();
				#var_dump($datos, $data);die();
				$message = $this->load->view('concursos/obras', $data, TRUE);
				#echo $message; die();
				$this->out->html_file($message, $this->lang->line('Obras'), 'iconoReportTab');
			}
			$this->out->dialog($this->lang->line('Obras'), $this->lang->line('no-hay-obras'));
		}
		$this->_show_js('obras', 'concursos/concurso.js', array('url' => site_url('concursos/concurso/obras')));
	}

	/**
	 * Marca una obra como vista
	 * @param  int $id Id de la línea del pedido
	 * @return MSG
	 */
	function obra_vista($id=null)
	{
		$this->userauth->roleCheck($this->auth.'.obras');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$this->load->model('concursos/m_pedidoconcursolineaaccion');
		if (is_numeric($id))
		{
			$add = array(
				'nIdLineaPedidoConcurso'	=> $id,
				'nIdTipo'					=> ACCION_OBRA_VISTA
				);
			if (!$this->m_pedidoconcursolineaaccion->insert($add))
			{
				$this->out->error($this->m_pedidoconcursolineaaccion->error_message());
			}
			$this->out->success($this->lang->line('obra-vista-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra los posibles descatalogados según el sistema
	 * @param  int $concurso Id del concurso
	 * @return WEBAPP
	 */
	function descatalogados($concurso = null)
	{
		#$this->out->dialog('Descatalogados', 'TODAVÍA NO');
		$this->userauth->roleCheck($this->auth.'.descatalogados');
		$concurso = isset($concurso) ? $concurso : $this->input->get_post('concurso');
		$this->load->model('concursos/m_pedidoconcursolinea');
		if (is_numeric($concurso))
		{
			$datos = $this->reg->descatalogados($concurso);
			if (count($datos) > 0)
			{
				foreach ($datos as $key => $value) 
				{
					$datos[$key] = array_merge($value, $this->m_pedidoconcursolinea->load($value['nIdLineaPedidoConcurso']));
				}
				$concurso = $this->reg->load($concurso);
				$data = array('concurso' => $concurso, 'datos' => $datos);
				#var_dump($datos); die();
				#var_dump($datos, $data);die();
				$message = $this->load->view('concursos/descatalogados', $data, TRUE);
				#echo $message; die();
				$this->out->html_file($message, $this->lang->line('Descatalogados'), 'iconoReportTab');
			}
			$this->out->dialog($this->lang->line('Obras'), $this->lang->line('no-hay-descatalogados'));
		}
		$this->_show_js('descatalogados', 'concursos/concurso.js', array('url' => site_url('concursos/concurso/descatalogados')));
	}
	/**
	 * Marca una obra como vista
	 * @param  int $id Id de la línea del pedido
	 * @return MSG
	 */
	function estado_visto($id=null)
	{
		$this->userauth->roleCheck($this->auth.'.obras');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$this->load->model('concursos/m_pedidoconcursolineaaccion');
		if (is_numeric($id))
		{
			$add = array(
				'nIdLineaPedidoConcurso'	=> $id,
				'nIdTipo'					=> ACCION_DESCATALOGADO_VISTO 
				);
			if (!$this->m_pedidoconcursolineaaccion->insert($add))
			{
				$this->out->error($this->m_pedidoconcursolineaaccion->error_message());
			}
			$this->out->success($this->lang->line('descatalogado-visto-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Libros con 30 sin venta que tiene pedido en el concurso
	 * @param  int $concurso Id concurso
	 * @param  int $dias     Número de días sin ventas
	 * @return HTML
	 */
	function antiguos($concurso = null)
	{
		$this->userauth->roleCheck($this->auth.'.get_list');
		$concurso = isset($concurso) ? $concurso : $this->input->get_post('concurso');
		if (is_numeric($concurso))
		{
			$dias = $this->config->item('bp.concurso.dias');
			$data = $this->reg->antiguos($concurso, $dias);
			$datos = array();
			$this->load->model('catalogo/m_articulo');
			$arts = array();
			foreach ($data as $reg)
			{
				if (!isset($arts[$reg['nIdLibro']]))
				{
					$art = $this->m_articulo->load($reg['nIdLibro'], array('materias', 'ubicaciones'));
					$ubicaciones = array();
					$materias = array();
					#var_dump($art['ubicaciones'], $art['materias']); die();
					foreach($art['ubicaciones'] as $ub)
						$ubicaciones[]  =$ub['cDescripcion'];
					foreach($art['materias'] as $ub)
						$materias[]  = $ub['cNombre'];
					$arts[$reg['nIdLibro']] = array(
						'ubicaciones' 	=> implode(' | ' , $ubicaciones), 
						'materias' 		=> implode(' | ' , $materias)
						);
				}

				$datos[$reg['cNombre']][] = array_merge($reg, $arts[$reg['nIdLibro']]);
			}
			#var_dump($datos); die();
			$concurso = $this->reg->load($concurso);
			$data = array('concurso' => $concurso, 'datos' => $datos);
			#var_dump($datos, $data);die();
			$message = $this->load->view('concursos/antiguos', $data, TRUE);
			#echo $message; die();
			$this->out->html_file($message, $this->lang->line('Libros a recoger de tienda'), 'iconoReportTab');
		}
		$this->_show_js('get_list', 'concursos/concurso.js', array('url' => site_url('concursos/concurso/antiguos')));
	}

	/**
	 * Libros con 30 sin venta que tiene pedido en el concurso
	 * @param  int $concurso Id concurso
	 * @param  int $dias     Número de días sin ventas
	 * @return HTML
	 */
	function en_stock($concurso = null)
	{
		$this->userauth->roleCheck($this->auth.'.get_list');
		$concurso = isset($concurso) ? $concurso : $this->input->get_post('concurso');
		if (is_numeric($concurso))
		{
			$dias = $this->config->item('bp.concurso.dias');
			$this->load->model('concursos/m_estadolineaconcurso');
			$estado = array(
				CONCURSOS_ESTADO_LINEA_EN_PROCESO, 
				CONCURSOS_ESTADO_LINEA_A_PEDIR, 
				CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR,
				CONCURSOS_ESTADO_LINEA_AGOTADOS,
				CONCURSOS_ESTADO_LINEA_DESCATALOGADO
				);
			$data = $this->reg->antiguos($concurso, 0, implode(',', $estado));
			#echo array_pop($this->db->queries); die();
			$datos = array();
			$this->load->model('catalogo/m_articulo');
			$arts = array();
			foreach ($data as $reg)
			{
				if (!isset($arts[$reg['nIdLibro']]))
				{
					$art = $this->m_articulo->load($reg['nIdLibro'], array('materias', 'ubicaciones'));
					$ubicaciones = array();
					$materias = array();
					#var_dump($art['ubicaciones'], $art['materias']); die();
					foreach($art['ubicaciones'] as $ub)
						$ubicaciones[]  =$ub['cDescripcion'];
					foreach($art['materias'] as $ub)
						$materias[]  = $ub['cNombre'];
					$arts[$reg['nIdLibro']] = array(
						'ubicaciones' 	=> implode(' | ' , $ubicaciones), 
						'materias' 		=> implode(' | ' , $materias)
						);
				}

				$datos[$reg['cNombre']][] = array_merge($reg, $arts[$reg['nIdLibro']]);
			}
			#var_dump($datos); die();
			$concurso = $this->reg->load($concurso);
			$data = array('concurso' => $concurso, 'datos' => $datos);
			#var_dump($datos, $data);die();
			$message = $this->load->view('concursos/antiguos', $data, TRUE);
			#echo $message; die();
			$this->out->html_file($message, $this->lang->line('Libros a recoger de tienda'), 'iconoReportTab');
		}
		$this->_show_js('get_list', 'concursos/concurso.js', array('url' => site_url('concursos/concurso/en_stock')));
	}

	/**
	 * Marca los Libros con >$dias sin venta que tiene pedido en el concurso para que no salgan en los pedidos de proveedor
	 * @param  int $concurso Id concurso
	 * @return MSG
	 */
	function set_antiguos($concurso = null)
	{
		$this->userauth->roleCheck($this->auth.'.get_list');
		$concurso = isset($concurso) ? $concurso : $this->input->get_post('concurso');
		if (is_numeric($concurso))
		{
			$dias = $this->config->item('bp.concurso.dias');
			$data = $this->reg->antiguos($concurso, $dias);
			$this->load->model('concursos/m_pedidoconcursolineaaccion');
				
			foreach ($data as $reg)
			{
				$add = array(
					'nIdLineaPedidoConcurso'	=> $reg['nIdLineaPedidoConcurso'],
					'nIdTipo'					=> ACCION_ANTIGUO_VISTO 
					);
				if (!$this->m_pedidoconcursolineaaccion->insert($add))
				{
					$this->out->error($this->m_pedidoconcursolineaaccion->error_message());
				}
			}
			$this->out->success(sprintf($this->lang->line('antiguos-visto-ok'), count($data)));
		}
		$this->_show_js('get_list', 'concursos/concurso.js', array('url' => site_url('concursos/concurso/set_antiguos')));
	}

	/**
	 * Marca los Libros con >$dias sin venta que tiene pedido en el concurso para que no salgan en los pedidos de proveedor
	 * @param  int $concurso Id concurso
	 * @return MSG
	 */
	function reset_antiguos($concurso = null)
	{
		$this->userauth->roleCheck($this->auth.'.get_list');
		$concurso = isset($concurso) ? $concurso : $this->input->get_post('concurso');
		if (is_numeric($concurso))
		{
			$dias = $this->config->item('bp.concurso.dias');
			$data = $this->reg->antiguos($concurso, $dias);
			$this->load->model('concursos/m_pedidoconcursolineaaccion');
				
			foreach ($data as $reg)
			{
				$add = array(
					'nIdLineaPedidoConcurso'	=> $reg['nIdLineaPedidoConcurso'],
					'nIdTipo'					=> ACCION_ANTIGUO_VISTO 
					);
				if (!$this->m_pedidoconcursolineaaccion->delete_by('nIdTipo=' .ACCION_ANTIGUO_VISTO)) 
				{
					$this->out->error($this->m_pedidoconcursolineaaccion->error_message());
				}
			}
			$this->out->success(sprintf($this->lang->line('antiguos-reset-visto-ok'), $this->db->affected_rows()));
		}
		$this->_show_js('get_list', 'concursos/concurso.js', array('url' => site_url('concursos/concurso/reset_antiguos')));
	}

}

/* End of file concurso.php */
/* Location: ./system/application/controllers/concursos/concurso.php */