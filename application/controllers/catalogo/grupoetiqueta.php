<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Grupos de etiquetas
 *
 */
class Grupoetiqueta extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Grupoetiqueta
	 */
	function __construct()
	{
		parent::__construct('catalogo.grupoetiqueta', 'catalogo/M_grupoetiqueta', TRUE, 'catalogo/grupoetiquetas.js', 'Etiquetas');
	}

	/**
	 * Genera un árbol con las etiquetas por imprimir (uso interno)
	 * @param array $nodos Array de [seccion] => líneas
	 * @param int $id Id de la sección a analizar
	 * @return array
	 */
	private function _imprimir_tree(&$nodos, $id = null)
	{
		$tree = $this->m_seccion->get_by_padre($id);

		$tree2 = array();
		foreach($tree as $k => $t)
		{
			$children = $this->_imprimir_tree($nodos, $t['nIdSeccion']);
			if(count($children) > 0 || isset($nodos[$t['nIdSeccion']]))
			{
				$n['text'] = $t['cNombre'];
				$n['qtip'] = $t['nIdSeccion'] . '-' . $t['cNombre'];
				$n['id'] = $t['nIdSeccion'];
				$n['uiProvider'] = 'col';
				$n['iconCls'] = 'icon-seccion-folder';
				$n['leaf'] = FALSE;
				$n['children'] = $children;
				$cantidad = 0;
				if(count($children) > 0)
				{
					foreach($children as $c)
					{
						$cantidad += $c['nCantidad'];
					}
				}
				if(isset($nodos[$t['nIdSeccion']]))
				{
					foreach($nodos[$t['nIdSeccion']] as $l)
					{
						$l['text'] = $l['cTitulo'];
						$l['qtip'] = $l['nIdLibro'] . '-' . $l['cTitulo'];
						$l['id'] = $l['nIdAcumulado'];
						$l['uiProvider'] = 'col';
						$l['iconCls'] = 'icon-seccion';
						$l['leaf'] = TRUE;
						$n['children'][] = $l;
						$cantidad += $l['nCantidad'];
					}
				}
				$n['nCantidad'] = $cantidad;
				$tree2[] = $n;
			}
		}
		return $tree2;
	}

	/**
	 * Procedimiento de impresión de etiquetas agrupadas
	 * @param int $id Id del grupo de etiquetas
	 * @param int $ids Id de la sección (-1/null todas)
	 * @param int $ida Id de la etiqueta
	 * @param string $report Formato de impresión por defecto
	 * @return MSG, FORM
	 */
	function imprimir($id =null, $ids =null, $ida =null, $report =null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$ids = isset($ids) ? $ids : $this->input->get_post('ids');
		$ida = isset($ida) ? $ida : $this->input->get_post('ida');
		$report = isset($report) ? $report : $this->input->get_post('report');

		if(is_numeric($id))
		{
			if(is_numeric($ida))
			{
				$this->load->model('catalogo/m_grupoetiquetalinea');
				$data[] = $this->m_grupoetiquetalinea->load($ida);
				#$this->out->error('Imprimir:<pre>' . print_r($data, TRUE) . '</pre>');
			}
			elseif(is_numeric($ids))
			{
				if($ids == -1)
					$ids = null;
				$data = $this->reg->get_seccion($id, $ids);
				#$this->out->error('Imprimir:<pre>' . print_r($data, TRUE) . '</pre>');
			}
			else
			{
				$this->_show_js('get_list', 'catalogo/etiquetas.js', array('id' => $id));
			}
			if(empty($report) || $report == 'null')
			{
				$this->load->library('Configurator');
				$report = $this->configurator->user('compras.etiquetas.formato');
			}
			$html = $this->show_report(null, array('etiquetas' => $data), $report, null, FALSE, null, FALSE, FALSE);
			$this->out->success($html);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Procedimiento de impresión de etiquetas agrupadas
	 * @param int $id Id del grupo de etiquetas
	 * @param int $ids Id de la sección (-1/null todas)
	 * @param int $ida Id de la etiqueta
	 * @param string $report Formato de impresión por defecto
	 * @return MSG, FORM
	 */
	function imprimir2($id =null, $ids =null, $ida =null, $report =null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$ids = isset($ids) ? $ids : $this->input->get_post('ids');
		$ida = isset($ida) ? $ida : $this->input->get_post('ida');
		$report = isset($report) ? $report : $this->input->get_post('report');

		if(is_numeric($id))
		{
			$url = site_url('catalogo/grupoetiqueta/get_tree/' . $id . '/1');
			$this->_show_js('get_list', 'compras/etiquetas.js', array('id' => $id, 'url' => $url));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Imprime una etiqueta de artículo
	 * @param int $id Id del artículo
	 * @param string $simbolo Símbolo a mostrar en la etiqueta
	 * @param int $qty Cantidad de etiquetas
	 * @param string $report Formato de impresión a utilizar
	 * @return FORM, MSG
	 */
	function imprimir_una($id =null, $simbolo =null, $qty =null, $report =null, $idg = null)
	{
		$report = isset($report) ? $report : $this->input->get_post('report');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$simbolo = isset($simbolo) ? $simbolo : $this->input->get_post('simbolo');
		$qty = isset($qty) ? $qty : $this->input->get_post('qty');
		$idg = isset($idg) ? $idg : $this->input->get_post('idg');

		if(is_numeric($id))
		{
			$this->load->model('catalogo/m_articulo');
			$art = $this->m_articulo->load($id);
			if (is_numeric($idg) && ($idg > 0))
			{
				$this->load->model('catalogo/m_grupoetiquetalinea');
				$data = array(
					'nIdPaquete'	=> $idg,
					'nIdLibro'		=> $id,
		            'nIdSeccion'	=> $this->config->item('bp.etiquetas.idseccion'),
					'nCantidad' 	=> $qty, 
					'fPVP' 			=> $art['fPVP'],
					'cSimbolo'		=> $simbolo
					);
				if ($this->m_grupoetiquetalinea->insert($data) < 1)
				{
					$this->out->error($this->m_grupoetiquetalinea->error_message());
				}
				$this->out->success($this->lang->line('etq_add_ok'));
			}
			else
			{
				$art['cSimbolo'] = $simbolo;
				$art['nCantidad'] = $qty;
				$art['nIdAcumulado'] = -1;
				$data[] = $art;
				if(empty($report) || $report == 'null' )
				{
					$this->load->library('Configurator');
					$report = $this->configurator->user('compras.etiquetas.formato');
				}				
				$html = $this->show_report(null, array('etiquetas' => $data), $report, null, FALSE, null, FALSE, FALSE);
				$this->out->success($html);
			}
		}
		$this->_show_js('get_list', 'catalogo/etiquetauna.js', array('id' => $id));
	}

	/**
	 * Imprime las etiquetas usando el driver
	 * @param string $src URL que genera las etiquetas
	 * @param string $title Título a mostrar en el trabajo de impresión
	 * @return MSG
	 */
	function labels($src =null, $title =null)
	{
		$src = isset($src) ? $src : $this->input->get_post('src');
		$title = isset($title) ? $title : $this->input->get_post('title');
		if($src != FALSE)
		{
			/*if($title == FALSE)
				$title = $this->lang->line('Etiquetas');
			$this->load->library('Etiquetas');
			$this->etiquetas->print_direct($src, $title);
			$this->out->success($this->lang->line('Etiquetas imprimidas'));*/
			if($title == FALSE)
				$title = $this->lang->line('Etiquetas');
			$old = $src;
			$src = urlencode($src);
			$title = urlencode($title);
			$this->load->library('Configurator');
			$host = $this->configurator->user('bp.labelserver.host');
			$port = $this->configurator->user('bp.labelserver.port');
			$url = "http://{$host}:{$port}?cmd=label&src={$src}&title={$title}";
			$err = error_reporting();
			error_reporting(E_ERROR);
			$result = file_get_contents($url);
			error_reporting($err);
			$this->out->success($this->lang->line('Etiquetas imprimidas'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Imprime un grupo de etiquetas
	 * @param string $etq Etiquetas separadas por ;
	 * @param string $report Formato de impresión a utilizar
	 * @return MSG
	 */
	function imprimir_grupo($etq =null, $report =null)
	{
		$etq = isset($etq) ? $etq : $this->input->get_post('etq');
		$report = isset($report) ? $report : $this->input->get_post('report');

		if($etq !== FALSE)
		{
			$etq = preg_split('/;/', $etq);
			$art = null;
			$data = null;
			if(count($etq) > 0)
			{
				$this->load->model('catalogo/m_articulo');
				foreach($etq as $e)
				{
					if($e != '')
					{
						$e = preg_split('/_/', $e);
						if ($e[1]>0)
						{
							if(!isset($art[$e[0]]))
							{
								$art[$e[0]] = $this->m_articulo->load($e[0]);
							}
							$reg['nIdLibro'] = $e[0];
							$reg['cTitulo'] = $art[$e[0]]['cTitulo'];
							$reg['cAutores'] = $art[$e[0]]['cAutores'];
							$reg['cISBN'] = $art[$e[0]]['cISBN'];
							#$reg['cSeccion'] 	= $e[4];
							#$reg['nIdSeccion'] 	= $e[5];
							$reg['cSimbolo'] = $e[2];
							$reg['fPVP'] = $e[3];
							$reg['nCantidad'] = $e[1];
							$reg['nIdAcumulado'] = -1;
							$data[] = $reg;
						}
					}
				}
			}
			if(empty($report) || $report == 'null')
			{
				$this->load->library('Configurator');
				$report = $this->configurator->user('compras.etiquetas.formato');
			}
			$html = $this->show_report(null, array('etiquetas' => $data), $report, null, FALSE, null, FALSE, FALSE);
			$this->out->success($html);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Borra etiquetas del grupo
	 * @param int $id Id del grupo de etiquetas
	 * @param int $ids Id de la sección (-1/null todas)
	 * @param int $ida Id de la etiqueta
	 * @return MSG
	 */
	function del_etq($id =null, $ids =null, $ida =null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$ids = isset($ids) ? $ids : $this->input->get_post('ids');
		$ida = isset($ida) ? $ida : $this->input->get_post('ida');
		if(is_numeric($id))
		{
			if(is_numeric($ida))
			{
				$this->load->model('catalogo/m_grupoetiquetalinea');
				if(!$data = $this->m_grupoetiquetalinea->delete($ida))
					$this->out->error($this->m_grupoetiquetalinea->error_message());
				$this->out->success($this->lang->line('etiqueta-delete-ok'));
			}
			elseif(is_numeric($ids))
			{
				if(!$this->reg->del_seccion($id, $ids))
					$this->out->error($this->m_grupoetiquetalinea->error_message());
				$this->out->success($this->lang->line('etiqueta-delete-grupo-ok'));
			}
			else
			{
				$this->del($id);
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Genera un árbol con las etiquetas
	 * @param int $id Id de la sección
	 * @return array
	 */
	private function _get_tree($id = null, $list = null)
	{
		$data = $this->reg->load($id, TRUE);
		if (count($data) == 0 || $list)
		{
			if (count($data['lineas']) == 0)
				$this->out->error($this->lang->line('no-etiquetas'));

			sksort($data['lineas'], 'dCreacion', FALSE);
			foreach ($data['lineas'] as $l)
			{
				$l['text'] = $l['dCreacion'] . '-<b>' . $l['cTitulo'] . '</b>';
				$l['qtip'] = $l['nIdLibro'] . '-' . $l['cTitulo'];
				$l['id'] = $l['nIdAcumulado'];
				$l['uiProvider'] = 'col';
				$l['iconCls'] = 'icon-seccion';
				$l['leaf'] = TRUE;
				#$l['cSimbolo'] = $simbolo;
				#$l['fPVP'] = $l['fPVP'];
				$nodos[] = $l;
			}
			#var_dump($nodos); die();
			return $nodos;
		}

		$tree = array();
		$nodos = array();
		// Construye el árbol
		foreach($data['lineas'] as $linea)
		{
			$nodos[$linea['nIdSeccion']][] = $linea;
		}
		$this->load->model('generico/m_seccion');

		return $this->_imprimir_tree($nodos);
	}

	/**
	 * Genera un árbol con las etiquetas del grupo
	 * @param int $id Id de la sección
	 * @return DATA
	 */
	function get_tree($id =null, $list = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$list = isset($list) ? $list : $this->input->get_post('list');
		if ($list !== FALSE)
			$list = format_tobool($list);
		if(is_numeric($id))
		{
			$tree = $this->_get_tree($id, $list);
			$this->out->send($tree);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Imprime una etiqueta de artículo del albarán
	 * @param int $id Id del artículo
	 * @param int $alb id del albarán
	 * @param string $report Formato de impresión por defecto
	 * @return MSG
	 */
	function albaran($code =null, $alb =null, $report = null)
	{
		$code = isset($code) ? $code : $this->input->get_post('code');
		$alb = isset($alb) ? $alb : $this->input->get_post('alb');
		$report = isset($report) ? $report : $this->input->get_post('report');

		if(!empty($code) && !empty($alb))
		{
			$art = null;
			$this->load->model('catalogo/m_articulo');
			# Busca datos etiqueta
			$art = $this->m_articulo->load($code);
			$this->load->model('compras/m_albaranentrada');
			$extra = $this->m_albaranentrada->datos_etiqueta($alb, $art['nIdLibro']);
			if (isset($extra))
			{
				$art['cSimbolo'] = $simbolo = $this->lang->line(($extra['bDeposito']) ? 'D' : 'F');;
				$art['nCantidad'] = $extra['nCantidad'];
				$art['fPVP'] = $extra['fPrecioVenta'];
				$data[] = $art;
				$this->load->library('Configurator');
				if(empty($report) || $report == 'null')
				{
					$this->load->library('Configurator');
					$report = $this->configurator->user('compras.etiquetas.formato');
				}
				$html = $this->show_report(null, array('etiquetas' => $data), $report, null, FALSE, null, FALSE, FALSE);
				$this->out->success($html);
			}
			$this->out->error(sprintf($this->lang->line('registro_no_encontrado'), $code));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

}

/* End of file Grupoetiqueta.php */
/* Location: ./system/application/controllers/catalogo/Grupoetiqueta.php */
