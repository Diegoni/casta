<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	pedidos cliente
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * EOI - Escuelas
 *
 */
class Escuela extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Escuela
	 */
	function __construct()
	{
		parent::__construct('eoi.escuela', 'eoi/M_Escuela', true, 'eoi/escuela.js', 'Escuelas');
	}
	
	/**
	 * Devuelve los departamentos de la escuela
	 * @param int $id Id de la escuela
	 * @return DATA
	 */
	function departamentos($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if (is_numeric($id)) 
		{
			$this->load->model('eoi/m_departamento');				
			$data = $this->m_departamento->get(NULL, NULL, NULL, NULL, "nIdEOI={$id}");
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));						
	}	

	/**
	 * Devuelve los departamentos de la escuela
	 * @param int $id Id de la escuela
	 * @return DATA
	 */
	function wiki($url = null)
	{
		$url = isset($url) ? $url : $this->input->get_post('url');
		if (!empty($url))
		{
			$data = file_get_contents(sprintf($this->config->item('bp.eoi.wiki.url'), $url));
			$data = str_replace($this->config->item('bp.eoi.wiki.replace'), site_url('eoi/escuela/wiki') . '/', $data);
			echo $data;
			return;
		}
		$data = file_get_contents($this->config->item('bp.eoi.wiki'));
		$data = str_replace($this->config->item('bp.eoi.wiki.replace'), site_url('eoi/escuela/wiki') . '/', $data);
		$this->out->html_file($data, $this->lang->line('Wiki EOI'), 'iconoWikiEOITab', null, TRUE);
	}

	/**
	 * Añade un departmanto
	 * @param int $id1 Id de la EOI
	 * @param int $id2 Id del departamento
	 * @return MSG 
	 */
	function add_departamento($id1 = null, $id2 = null, $descripcion = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id1 = isset($id1) ? $id1 : $this->input->get_post('id1');
		$id2 = isset($id2) ? $id2 : $this->input->get_post('id2');
		$descripcion = isset($descripcion) ? $descripcion : $this->input->get_post('descripcion');

		if (is_numeric($id1) && is_numeric($id2)) 
		{
			$this->load->model('eoi/m_departamento');
			$data = $this->m_departamento->get(NULL, NULL, NULL, NULL, "nIdCliente={$id1}");
			if (count($data) > 0) $this->out->error($this->lang->line('departamento-ya-existente'));
			$data =array(
				'nIdEOI' 		=> $id1, 
				'nIdCliente' 	=> $id2, 
				'cDescripcion'	=> $descripcion
			);
			if ($this->m_departamento->insert($data) > 0)
			{
				$this->out->success($this->lang->line('departamento-ok'));
			}
			
			$this->out->error($this->m_departamento->error_message());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));					
	}
	
	/**
	 * Elimina un artículo relacionado
	 * @param int $id Lista de Ids de relación separadas por ; 
	 * @return MSG 
	 */
	function del_departamento($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.del'));
		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$count = 0;
			$ids= preg_split('/[\;\s\n\r\;]/', $id);
			$this->load->model('eoi/m_departamento');
			$this->db->trans_begin();
			foreach ($ids as $id)
			{
				if (trim($id) != '')
				{
					if (!$this->m_departamento->delete($id))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_departamento->error_message());
					}			
					++$count;
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('departamento-delete-ok'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));							
	}

	/**
	 * Devuelve los importes de la escuela
	 * @param int $id Id de la escuela
	 * @return DATA
	 */
	function importes($id = null, $start = null, $limit = null, $sort = null, $dir = null, $where = null, $query = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$start = isset($start) ? $start : $this->input->get_post('start');
		$limit = isset($limit) ? $limit : $this->input->get_post('limit');
		$sort = isset($sort) ? $sort : $this->input->get_post('sort');
		$dir = isset($dir) ? $dir : $this->input->get_post('dir');
		$where = isset($where) ? $where : $this->input->get_post('where');
		$query = isset($query) ? $query : $this->input->get_post('query');

		if (is_numeric($id)) 
		{
			$this->load->model('eoi/m_importe');
			if ($sort == 'fEntrada') $sort = 'fImporte';				
			if ($sort == 'fSalida') $sort = 'fImporte';
			$data = $this->m_importe->get($start, $limit, $sort, $dir, "nIdEOI={$id}");
			foreach($data as $k => $v)
			{
				if ($v['fImporte']>0)
				{ 
					$v['fEntrada'] = $v['fImporte'];
					$v['fSalida'] = 0;
				}
				else
				{ 
					$v['fEntrada'] = 0;
					$v['fSalida'] = $v['fImporte'];
				}
				$data[$k] = $v;			
			}
			$this->out->data($data, $this->m_importe->get_count());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));						
	}

	/**
	 * Elimina un artículo relacionado
	 * @param int $id Lista de Ids de relación separadas por ; 
	 * @return MSG 
	 */
	function del_importe($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.del'));
		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$count = 0;
			$ids= preg_split('/[\;\s\n\r\;]/', $id);
			$this->load->model('eoi/m_importe');
			$this->db->trans_begin();
			foreach ($ids as $id)
			{
				if (trim($id) != '')
				{
					if (!$this->m_importe->delete($id))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_importe->error_message());
					}			
					++$count;
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('importe-delete-ok'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));							
	}

	/**
	 * Muestra el estado de una cuenta de una escuela
	 * @param int $id Id de la escuela
	 * @param date $fecha Fecha límite para mostrar los datos
	 * @param bool $web Solo lista los albaranes que se deben ver en la Web 
	 * @return DATA
	 */
	private function _estadocuenta($id = null, $fecha = null)
	{
		set_time_limit(0);
		$fecha = to_date($fecha);
		// Importes
		$this->load->model('eoi/m_importe');
		$importes = $this->m_importe->get(null, null, null, null, "nIdEOI={$id}");
		foreach($importes as $k => $v)
		{
			$v['fImporte'] = -$v['fImporte'];
			$importes[$k] = $v;			
		}
		// Albaranes de salida
		$albaranes = $this->reg->albaranes($id);
		$data = array_merge($albaranes, $importes);
		sksort($data, 'dFecha');
		$pre = 0;
		$post = 0;
		foreach($data as $k => $d)
		{
			if ($d['dFecha'] < $fecha)
			{
				$pre += $d['fImporte'];
				unset($data[$k]);
			}
			else
			{
				$post += $d['fImporte'];
			}
		}
		$post += $pre;
		sksort($data, 'dFecha', FALSE);

		$datos['escuela'] = $this->reg->load($id);
		$datos['Fecha'] = format_date($fecha);
		$datos['pre'] = $pre;
		$datos['post'] = $post;
		$datos['valores'] = $data;
		return $datos;
	}

	/**
	 * Muestra el estado de una cuenta de una escuela
	 * @param int $id Id de la escuela
	 * @param date $fecha Fecha límite para mostrar los datos
	 * @return HTML_FILE
	 */
	function estadocuenta($id = null, $fecha = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));
		$id 	= isset($id)?$id:$this->input->get_post('id');
		$fecha	= isset($fecha)?$fecha:$this->input->get_post('fecha');

		if (is_numeric($id) && !empty($fecha))
		{
				$datos = $this->_estadocuenta($id, $fecha);
				$body = $this->load->view('eoi/estadocuenta', $datos, TRUE);
				$this->out->html_file($body, $this->lang->line('estado-cuenta') . ' - ' . $id, 'iconoReportTab');			
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));							
	}

	/**
	 * Muestra el estado de una cuenta de una escuela
	 * @param int $id Id de la escuela
	 * @param date $fecha Fecha límite para mostrar los datos
	 * @return DATA
	 */
	function estadocuenta2($id = null, $fecha = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));
		$id 	= isset($id)?$id:$this->input->get_post('id');
		$fecha	= isset($fecha)?$fecha:$this->input->get_post('fecha');

		if (is_numeric($id) && !empty($fecha))
		{
			$datos = $this->_estadocuenta($id, $fecha);
			$this->out->data($datos);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Estado de la cuenta de una escuela
	 * @param int $id Lista de Ids de relación separadas por ; 
	 * @return MSG 
	 */
	function estado($fecha = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));
		$fecha	= isset($fecha)?$fecha:$this->input->get_post('fecha');
		if (!empty($fecha))
		{
			set_time_limit(0);
			$fecha = to_date($fecha);
			// Importes
			$this->load->model('eoi/m_importe');
			$importes = $this->m_importe->totales($fecha);
			#var_dump($importes);
			// Albaranes de salida
			$albaranes = $this->reg->totales($fecha);
			#echo '<pre>'; print_r($this->db->queries); echo '</pre>';
			#var_dump($albaranes); die();

			$final = array();
			foreach($importes as $i)
			{
				$final[$i['cDescripcion']] = $i['fImporte'];
			}
			foreach($albaranes as $i)
			{
				$final[$i['cDescripcion']] = (isset($final[$i['cDescripcion']])?$final[$i['cDescripcion']]:0) - $i['fImporte'];
			}
			#$data = array_merge($albaranes, $importes);
			#sksort($data, 'dFecha');
			$datos['fecha'] = format_date($fecha);
			$datos['valores'] = $final;
			$body = $this->load->view('eoi/estadotodo', $datos, TRUE);
			#echo $body; die();
			$this->out->html_file($body, $this->lang->line('estado-cuenta'), 'iconoReportTab');
		}
		$data['title'] = $this->lang->line('Estado de las cuentas');
		$data['icon'] = 'iconoReportTab';
		$data['url'] = site_url('eoi/escuela/estado');
		$this->_show_js('get_list', 'main/date.js', $data);									
	}

	/**
	 * Estado de la cuenta de una escuela
	 * @param int $id Id de la escuela
	 * @return MSG 
	 */
	function comisiones($mes = null, $year = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));
		$mes = isset($mes)?$mes:$this->input->get_post('mes');
		$year = isset($year)?$year:$this->input->get_post('year');
		if (!empty($mes) && !empty($year))
		{
			set_time_limit(0);
			// Importes
			$comisiones = $this->reg->comisiones($mes, $year);
			/*$final = array();
			foreach($comisiones as $i)
			{
				$final[$i['cDescripcion']][] = $i;
			}*/
			$datos['mes'] = $mes;
			$datos['year'] = $year;
			$datos['valores'] = $comisiones;
			$body = $this->load->view('eoi/comisiones', $datos, TRUE);
			#echo $body; die();
			$this->out->html_file($body, $this->lang->line('estado-cuenta'), 'iconoReportTab');
		}
		$this->_show_js('get_list', 'eoi/comisiones.js');									
	}

	/**
	 * Estado de la cuenta de una escuela serparados por idiiomas
	 * @param int $id Id de la escuela
	 * @return MSG 
	 */
	function comisiones2($mes = null, $year = null, $escuela = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));
		$mes = isset($mes)?$mes:$this->input->get_post('mes');
		$year = isset($year)?$year:$this->input->get_post('year');
		$escuela = isset($escuela)?$escuela:$this->input->get_post('escuela');

		if (!empty($mes) && !empty($year))
		{
			set_time_limit(0);
			// Importes
			$comisiones = $this->reg->comisiones2($mes, $year, $escuela);
			$datos['mes'] = $mes;
			$datos['year'] = $year;
			$datos['valores'] = $comisiones;
			$body = $this->load->view('eoi/comisiones2', $datos, TRUE);

			$this->out->html_file($body, $this->lang->line('estado-cuenta'), 'iconoReportTab');
		}

		$this->_show_js('get_list', 'eoi/comisiones2.js');
	}

	/**
	 * Estado de la cuenta de una escuela
	 * @param int $id Lista de Ids de relación separadas por ; 
	 * @return MSG 
	 */
	function sin_idioma($mes = null, $year = null, $escuela = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));
		$mes = isset($mes)?$mes:$this->input->get_post('mes');
		$year = isset($year)?$year:$this->input->get_post('year');
		$escuela = isset($escuela)?$escuela:$this->input->get_post('escuela');

		if (!empty($mes) && !empty($year))
		{
			set_time_limit(0);
			// Importes
			$valores = $this->reg->sin_idioma($mes, $year, $escuela);
			#var_dump($datos);die();
			$datos['mes'] = $mes;
			$datos['year'] = $year;
			$datos['valores'] = $valores;
			$body = $this->load->view('eoi/sinidioma', $datos, TRUE);
			#echo $body; die();

			$this->out->html_file($body, $this->lang->line('estado-cuenta'), 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}


	/**
	 * Otiene los libros de una escuela
	 * @param int $id Id de la escuela
	 * @return MSG 
	 */
	function get_books($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));
		$id = isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{
			$data = $this->reg->load($id);
			$this->load->model('catalogo/m_articulo');

			$lineas = explode("\n", $data['cLibros']);
			$idioma = null;
			$nivel = null;
			$resultado = null;
			$texto = null;
			$ids = array();
			foreach ($lineas as $value) 
			{
				$value = trim($value);
				$libro = null;
				if (strpos($value, '==') === 0)
				{
					$nivel = str_replace('=', '', $value);
					$texto = null;
				}
				elseif (strpos($value, '=') === 0) 
				{
					$idioma = str_replace('=', '', $value);
					$nivel = null;
					$texto = null;
				}
				else
				{
					if (strpos($value, '<libro') !== FALSE)
					{
						$pos1 = strpos($value, '"');
						$pos2 = strpos($value, '"', $pos1 + 1);
						$datos[0] = substr($value, $pos1 + 1, $pos2 - $pos1 - 1);
					}
					else
					{
						$datos = explode(';', $value);						
					}
					if (is_numeric($datos[0]))
					{
						#var_dump($datos);
						if (!isset($datos[1]) && !empty($texto)) $datos[1] = $texto;
						$libro = $this->m_articulo->load($datos[0]);
						if ($libro) 
						{
							$reg = array(
								'id' 		=> $libro['nIdLibro'],
								'pvp' 		=> $libro['fPVP'],
								'precio'	=> $libro['fPrecio'],
								'ean'		=> $libro['nEAN'],
								'isbn'		=> $libro['cISBN'],
								'iva'		=> $libro['fIVA'],
								'titulo'	=> $libro['cTitulo'],
								'coste'		=> $libro['fPrecioCompra'],
								'img'		=> site_url("catalogo/articulo/cover/{$libro['nIdLibro']}"),
								'alias'		=> isset($datos[1])?$datos[1]:null
								);
							$ids[$datos[0]] = $datos[0];
							if (isset($idioma))
							{
								if (!isset($nivel))
								{
									$nivel = $this->lang->line('Varios');
								}
								$resultado[$idioma][$nivel][$datos[0]] = $reg;
							}
						}
					}
					elseif (!empty($datos[0]))
					{
						$texto = $datos[0];
					}
				}
			}
			# Busca el resto
			$this->load->model('generico/m_idioma');
			$regs = $this->m_idioma->get();
			$idm = array();
			foreach ($regs as $value) 
			{
				$idm[$value['nIdIdioma']] = $value['cNombre'];
			}

			$this->load->model('catalogo/m_articuloseccion');
			$regs = $this->m_articuloseccion->get(null, null, null, null, '(nStockFirme + nStockDeposito > 0) AND nIdSeccion = ' . $data['nIdSeccion']);
			foreach($regs as $reg)
			{
				if (!isset($ids[$reg['nIdLibro']]))
				{

					$libro = $this->m_articulo->load($reg['nIdLibro']);
					if ($libro) 
					{
						$reg = array(
							'id' 		=> $libro['nIdLibro'],
							'pvp' 		=> $libro['fPVP'],
							'precio'	=> $libro['fPrecio'],
							'ean'		=> $libro['nEAN'],
							'isbn'		=> $libro['cISBN'],
							'iva'		=> $libro['fIVA'],
							'titulo'	=> $libro['cTitulo'],
							'coste'		=> $libro['fPrecioCompra'],
							'img'		=> site_url("catalogo/articulo/cover/{$libro['nIdLibro']}"),
							'alias'		=> null
							);
						$ids[$datos[0]] = $datos[0];
						$idioma = isset($idm[$libro['nIdIdioma']])?$idm[$libro['nIdIdioma']]:'General';
						$resultado['Resto_eu'][$idioma][$libro['nIdLibro']] = $reg;						
					}
				}
			}
			$this->out->data($resultado);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));							
	}

}

/* End of file escuela.php */
/* Location: ./system/application/controllers/eoi/escuela.php */
