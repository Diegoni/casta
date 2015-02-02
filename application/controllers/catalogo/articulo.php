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
 * ID del tipo de autor por defecto
 * @var int
 */
define('DEFAULT_TIPO_AUTOR', 1);

/**
 * Margen de IDs a buscar
 * @var int
 */
define('MARGEN_ID_SEARCH', 1000);

/**
 * Artículos
 *
 */
class Articulo extends MY_Controller
{
	/**
	 * Enlace a la página web
	 * @var string
	 */
	var $_webpage = null;

	/**
	 * Constructor
	 *
	 * @return Articulo
	 */
	function __construct($model ='catalogo/M_articulo')
	{
		parent::__construct('catalogo.articulo', $model, FALSE, 'catalogo/articulo.js', 'Artículos');
		$this->_webpage = $this->config->item('catalogo.webpage.url');
	}

	/**
	 * Hook para las llamadas después de leer los datos
	 * @param int $id Id del registro
	 * @param mixed $relations Relaciones
	 * @param array $data Datos leídos
	 * @return bool
	 */
	protected function _post_get($id, $relations, &$data, $cmpid = null)
	{
		parent::_post_get($id, $relations, $data, $cmpid);

		$data['proveedores_all'] = $this->reg->get_proveedores($id);
		$data['notas'] = $this->get_notas($id);
		$this->load->model('compras/m_reposicion');
		//Ventas totales
		$data['t_semana'] = $this->m_reposicion->get_ventas($id, 7, 'd');
		$data['t_mes'] = $this->m_reposicion->get_ventas($id, 1, 'm');
		$data['t_mes3'] = $this->m_reposicion->get_ventas($id, 3, 'm');
		$data['t_mes6'] = $this->m_reposicion->get_ventas($id, 6, 'm');
		$data['t_mes12'] = $this->m_reposicion->get_ventas($id, 12, 'm');
		$data['t_mes24'] = $this->m_reposicion->get_ventas($id, 24, 'm');
		$data['ult_docs_general'] = $this->reg->get_last_docs($id);
		$data['pedidos_cliente'] = $this->reg->get_pedidos_cliente($id, null, null, TRUE);
		$data['pedidos_proveedor'] = $this->reg->get_pedidos_proveedor($id, null, null, TRUE);
		$data['presupuestos'] = $this->reg->get_presupuestos($id);
		
		$this->load->model('catalogo/m_promocion');
		$data['promociones'] = $this->m_promocion->get(null, null, null, null, "nIdLibro={$id} AND (dFinal IS NULL OR dFinal >= GETDATE())");

		if (isset($data['sinopsis']['tSinopsis']))
			$data['sinopsis']['tSinopsis'] = str_replace("\n", '<br/>', $data['sinopsis']['tSinopsis']);
		if (json_encode($data['sinopsis']['tSinopsis']) == null)
			$data['sinopsis']['tSinopsis'] = null;
		
		if(isset($data['nIdOferta']))
		{
			$this->load->model('catalogo/m_oferta');
			$data['oferta'] = $this->m_oferta->load($data['nIdOferta']);
		}
		$cod = null;
		if(isset($data['codigos']))
		{
			$ar = array($id, $data['nEAN'], $data['cISBN'], $data['cISBNBase']);
			foreach ($data['codigos'] as $k => $value) 
			{
				$data['codigos'][$k]['bDelete'] = (!in_array($value['nCodigo'], $ar));
			}
		}
		#var_dump($data['nIdProveedor']); die();
		$message = $this->load->view('catalogo/articulo', $data, TRUE);
		$this->load->library('HtmlFile');
		$css = array($this->config->item('bp.data.css'), array('style.css', 'main'), array('icons.css', 'main'));
		$filename = $this->obj->htmlfile->create($message, $this->lang->line('Artículo') . ' ' . $id, $css);
		$url = $this->obj->htmlfile->url($filename);
		$data['info'] = $url;

		if($this->_webpage)
		{
			$data['cURL'] = str_replace('%id%', $id, $this->_webpage);
		}
		#var_dump($data);
		return TRUE;
	}

	/**
	 * Obtiene los datos de un registro en una versión simple
	 * @param int $id Id del registro
	 * @param string $relation Relación individual a cargar
	 * @return JSON
	 */
	function get2($id = null, $relation = null)
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.get_list'));
		}

		$id	= isset($id)?$id:$this->input->get_post('id');
		$relation	= isset($relation)?$relation:$this->input->get_post('relation');

		if (isset($id) && ($id != ''))
		{
			if ($relation)
			{
				if ($relation == 'true')
				{
					$relation = TRUE;
				}
				elseif (is_string($relation))
				{
					$relation = preg_split('/\;/', $relation);
				}
				//$data = $this->reg->get_relation($id, $relation);
				$data = $this->reg->load($id, $relation);
			}
			else
			{
				$data = $this->reg->load($id);
			}
			if ($data!==FALSE)
			{

				#$this->_post_get($id, $relation, $data);
				$this->out->data($data);
			}
			else
			{
				$this->out->error(sprintf($this->lang->line('registro_no_encontrado'), $id));
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Obtiene los datos de un registro en una versión simple
	 * @param int $id Id del registro
	 * @param string $relation Relación individual a cargar
	 * @return JSON
	 */
	function get3($id = null, $relation = null)
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.get_list'));
		}

		$id	= isset($id)?$id:$this->input->get_post('id');
		$relation	= isset($relation)?$relation:$this->input->get_post('relation');

		if (isset($id) && ($id != ''))
		{
			if ($relation)
			{
				if ($relation == 'true')
				{
					$relation = TRUE;
				}
				elseif (is_string($relation))
				{
					$relation = preg_split('/\;/', $relation);
				}
				//$data = $this->reg->get_relation($id, $relation);
				$data = $this->reg->load($id, $relation);
			}
			else
			{
				$data = $this->reg->load($id);
			}
			if ($data!==FALSE)
			{

				#$this->_post_get($id, $relation, $data);
				$data['pedidos_cliente'] = $this->reg->get_pedidos_cliente($id, null, null, TRUE);
				$data['pedidos_proveedor'] = $this->reg->get_pedidos_proveedor($id, null, null, TRUE);
				$this->out->data($data);
			}
			else
			{
				$this->out->error(sprintf($this->lang->line('registro_no_encontrado'), $id));
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Alta rápida del artículo
	 * @return FORM
	 */
	function alta($text = null)
	{
		$this->userauth->roleCheck(($this->auth . '.add'));

		$text = isset($text) ? $id : $this->input->get_post('text');

		$data = get_post_all();
		foreach ($data as $k => $v) {
			if (trim($v) == '') {
				unset($data[$k]);
			} elseif (is_string($v)) {
				$data[$k] = urldecode($v);
			}
		}
		set_time_limit(0);
		if (isset($data['cTitulo']) && (is_numeric($data['fPVP']) || is_numeric($data['fPrecio'])) && is_numeric($data['nIdTipo'])) {
			$this->load->model('catalogo/m_tipolibro');
			$tipo = $this->m_tipolibro->load($data['nIdTipo']);
			if (!isset($data['fPrecio']))
			$data['fPrecio'] = format_quitar_iva($data['fPVP'], $tipo['fIVA']);
			if (isset($data['autores'])) {
				$aut = preg_split('/;/', $data['autores']);
				$data['autores'] = array();
				foreach ($aut as $k => $a) {
					$a2 = preg_split('/\_/', $a);
					if (is_numeric($a2[0])) {
						$data['autores'][] = array('nIdAutor' => $a2[0], 'nIdTipoAutor' => (isset($a2[1]) ? $a2[1] : DEFAULT_TIPO_AUTOR));
					}
				}
			}
			if (isset($data['materias'])) {
				$mat = preg_split('/;/', $data['materias']);
				$data['materias'] = array();
				foreach ($mat as $k => $a) {
					if (is_numeric($a)) {
						$data['materias'][] = array('nIdMateria' => $a);
					}
				}
			}
			if (isset($data['nIdSeccion'])) {
				$data['secciones'][] = array('nIdSeccion' => $data['nIdSeccion']);
				$this->load->library('Configurator');
				$this->configurator->set_user('bp.catalogo.idsecciondefault', $data['nIdSeccion']);
			}
			$this->db->trans_begin();
			$id_libro = $this->reg->insert($data);
			if ($id_libro < 0) {
				$this->error = $this->reg->error_message();
				$this->db->trans_rollback();
				return FALSE;
			}

			if (isset($data['urlPortada']) && ($data['urlPortada'] != '')) {
				if (!$this->reg->set_portada($id_libro, $data['urlPortada'])) {
					$this->error = $this->reg->error_message();
					$this->db->trans_rollback();
					return FALSE;
				}
			}
			$this->db->trans_commit();

			$id = format_enlace_cmd($id_libro, site_url('catalogo/articulo/index/' . $id_libro));

			$res = array(
                'success' => TRUE,
                'message' => sprintf($this->lang->line('articulo_creado'), $id),
                'id' => $id_libro
			);
			$this->out->send($res);
		} else {
			$this->load->library('Configurator');
			$data['text'] = $text;
			$this->_show_js('add', 'catalogo/altarapida.js', $data);
		}
	}

	/**
	 * Muestra la ficha del libro
	 * @param int $open_id Id del libro a mostrar
	 * @return HTML_FILE
	 */
	function index2($idl = null)
	{
		$this->userauth->roleCheck($this->auth . '.index');

		$idl = isset($idl) ? $idl : $this->input->get_post('idl');

		if ($idl)
		{
			$id = $idl;

			$data = $this->reg->load($id, TRUE);
			if ($data === FALSE)
			$this->out->error($this->lang->line('registro_no_encontrado'));
			$data['proveedores_all'] = $this->reg->get_proveedores($id);
			$data['notas'] = $this->reg->get_notas($id);
			#print_r($data['notas']);
			$this->load->model('compras/m_reposicion');
			//Ventas totales
			$data['t_semana'] = $this->m_reposicion->get_ventas($id, 7, 'd');
			$data['t_mes'] = $this->m_reposicion->get_ventas($id, 1, 'm');
			$data['t_mes3'] = $this->m_reposicion->get_ventas($id, 3, 'm');
			$data['t_mes6'] = $this->m_reposicion->get_ventas($id, 6, 'm');
			$data['t_mes12'] = $this->m_reposicion->get_ventas($id, 12, 'm');
			$data['t_mes24'] = $this->m_reposicion->get_ventas($id, 24, 'm');
			$data['ult_docs_general'] = $this->reg->get_last_docs($id);
			$data['pedidos_cliente'] = $this->reg->get_pedidos_cliente($id, null, null, TRUE);
			$data['pedidos_proveedor'] = $this->reg->get_pedidos_proveedor($id, null, null, TRUE);
			if (isset($data['nIdOferta'])) {
				$this->load->model('catalogo/m_oferta');
				$data['oferta'] = $this->m_oferta->load($data['nIdOferta']);
			}

			$message = $this->load->view('catalogo/articulo', $data, TRUE);

			$this->out->html_file($message, $this->lang->line('Artículo') . ' ' . $idl, 'iconoArticulosTab', $this->config->item('bp.data.css'));
		} 
		else 
		{
			$data['url'] = site_url('catalogo/articulo/index');
			$data['title'] = $this->lang->line('Artículo');
			$data['fechas'] = FALSE;
			$this->_show_js('get_list', 'catalogo/documentosasticulo.js', $data);
		}
	}

	/**
	 * Muestra la ficha del libro
	 * @param int $open_id Id del libro a mostrar
	 * @return HTML_FILE
	 */
	function index3($open_id = null)
	{
		$this->userauth->roleCheck($this->auth . '.index');

		$open_id = isset($open_id) ? $open_id : $this->input->get_post('open_id');

		if ($open_id)
		{
			$id = $open_id;
			$data = $this->reg->load($id, TRUE);
			if ($data == FALSE)
			$this->out->error($this->lang->line('registro_no_encontrado'));
			$data['proveedores_all'] = $this->reg->get_proveedores($id);

			$this->load->model('compras/m_reposicion');
			//Ventas totales
			$data['t_semana'] = $this->m_reposicion->get_ventas($id, 7, 'd');
			$data['t_mes'] = $this->m_reposicion->get_ventas($id, 1, 'm');
			$data['t_mes3'] = $this->m_reposicion->get_ventas($id, 3, 'm');
			$data['t_mes6'] = $this->m_reposicion->get_ventas($id, 6, 'm');
			$data['t_mes12'] = $this->m_reposicion->get_ventas($id, 12, 'm');
			$data['ult_docs_general'] = $this->reg->get_last_docs($id);
			$data['pedidos_cliente'] = $this->reg->get_pedidos_cliente($id, null, null, TRUE);
			$data['pedidos_proveedor'] = $this->reg->get_pedidos_proveedor($id, null, null, TRUE);

			$this->obj->load->helper('asset');
			$message = $this->load->view('catalogo/articulo', $data, TRUE);

			$this->out->html($message, $this->lang->line('Artículo') . ' ' . $open_id, 'iconoArticulosTab', $this->config->item('bp.data.css'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Formulario de búsqueda
	 * @return FORM
	 */
	function buscar($query = null) 
	{
		$this->userauth->roleCheck($this->auth . '.search');
		$query = isset($query) ? $query : $this->input->get_post('query');
		$data['query'] = trim($query);
		$this->_show_form('search', 'catalogo/buscarlibros.js', $this->lang->line('Búsqueda Artículos'), null, null, null, $data);
	}

	/**
	 * Formulario de búsqueda rápido
	 * @return FORM
	 */
	function query() 
	{
		$this->userauth->roleCheck($this->auth . '.search');
		$data['fast_query'] = TRUE;
		$this->_show_form('search', 'catalogo/buscarlibros.js', $this->lang->line('Búsqueda Artículos'), null, null, null, $data);
	}

	/**
	 * Documentos de un artículo
	 * @param int $id Id del artículo
	 * @param date $fecha1 Fecha desde
	 * @param date $fecha2 Fecha hasta
	 * @param int $ids Id de la sección
	 * @param mixed $tipo array: lista de listados a mostrar, TRUE: todos, string: uno solo
	 * @return JSON
	 */
	function documentos($idl = null, $fecha1 = null, $fecha2 = null, $ids = null, $tipo = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');

		$idl = isset($idl) ? $idl : $this->input->get_post('idl');
		$fecha1 = isset($fecha1) ? $fecha1 : $this->input->get_post('fecha1');
		$fecha2 = isset($fecha2) ? $fecha2 : $this->input->get_post('fecha2');
		$ids = isset($ids) ? $ids : $this->input->get_post('ids');
		$tipo = isset($tipo) ? $tipo : $this->input->get_post('tipo');
		if (!empty($tipo)) 
		{
			$tipo_or = $tipo;
			$tipo = preg_split("/[,\|\s|;]/", $tipo);
		}
		if ($tipo === FALSE)
		$tipo = TRUE;

		if (!empty($idl) && !empty($fecha1) && !empty($fecha2)) 
		{
			$art = $this->reg->load($idl);
			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);
			//print "{$fecha1} - {$fecha2}\n"; die();
			//var_dump($tipo); die();
			$docs = $this->reg->get_documentos($idl, $fecha1, $fecha2, $ids, $tipo);
			if (count($docs) > 0) 
			{
				#sksort($docs, 'dDia');
				$data['docs'] = $docs;
				$data['fecha1'] = $fecha1;
				$data['fecha2'] = $fecha2;
				$data['titulo'] = sprintf($this->lang->line('documentos_articulo'), $art['cTitulo']);
				$data['item'] = $art['cTitulo'];
				$data['id'] = $art['id'];
				$data['clpv'] = TRUE;

				$message = $this->load->view('catalogo/documentos', $data, TRUE);
				$this->out->html_file($message, sprintf($this->lang->line('documentos_articulo'), $idl), 'iconoReportTab');
			}
			$this->out->success($this->lang->line('no-hay-documentos'));
		} 
		else 
		{
			$data['url'] = site_url('catalogo/articulo/documentos');
			$data['title'] = $this->lang->line('documentos_articulo_form');
			if (isset($tipo_or))
			$data['tipo'] = $tipo_or;
			if (!empty($idl))
			$data['idl'] = $idl;

			$this->_show_js('get_list', 'catalogo/documentosasticulo.js', $data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Pedidos de cliente de un artículo pendientes
	 * @param int $id Id del artículo
	 * @param date $fecha1 Fecha desde
	 * @param date $fecha2 Fecha hasta
	 * @return JSON
	 */
	function pedidos_cliente_pendiente($idl = null, $fecha1 = null, $fecha2 = null) 
	{
		$this->_pedidos('get_pedidos_cliente',
                'pedidos_cliente_pendientes_articulo',
                'pedidos_cliente_articulo',
                'catalogo/articulo/pedidos_cliente_pendiente',
                'catalogo/articulo/pedidos_cliente', $idl, $fecha1, $fecha2, TRUE);
	}

	/**
	 * Pedidos de cliente de un artículo
	 * @param int $id Id del artículo
	 * @param date $fecha1 Fecha desde
	 * @param date $fecha2 Fecha hasta
	 * @param bool $pendientes TRUE: Muestra solo los pendientes, FALSE: todos
	 * @return JSON
	 */
	function pedidos_cliente($idl = null, $fecha1 = null, $fecha2 = null, $pendientes = null) 
	{
		$this->_pedidos('get_pedidos_cliente',
                'pedidos_cliente_pendientes_articulo',
                'pedidos_cliente_articulo',
                'catalogo/articulo/pedidos_cliente_pendiente',
                'catalogo/articulo/pedidos_cliente', $idl, $fecha1, $fecha2, $pendientes);
	}

	/**
	 * Pedidos de proveedor de un artículo pendientes
	 * @param int $id Id del artículo
	 * @param date $fecha1 Fecha desde
	 * @param date $fecha2 Fecha hasta
	 * @return JSON
	 */
	function pedidos_proveedor_pendiente($idl = null, $fecha1 = null, $fecha2 = null) 
	{
		$this->_pedidos('get_pedidos_proveedor',
                'pedidos_proveedor_pendientes_articulo',
                'pedidos_proveedor_articulo',
                'catalogo/articulo/pedidos_proveedor_pendiente',
                'catalogo/articulo/pedidos_proveedor', $idl, $fecha1, $fecha2, TRUE);
	}

	/**
	 * Pedidos de cliente de un artículo
	 * @param int $id Id del artículo
	 * @param date $fecha1 Fecha desde
	 * @param date $fecha2 Fecha hasta
	 * @param bool $pendientes TRUE: Muestra solo los pendientes, FALSE: todos
	 * @return JSON
	 */
	function pedidos_proveedor($idl = null, $fecha1 = null, $fecha2 = null, $pendientes = null) 
	{
		$this->_pedidos('get_pedidos_proveedor',
                'pedidos_proveedor_pendientes_articulo',
                'pedidos_proveedor_articulo',
                'catalogo/articulo/pedidos_proveedor_pendiente',
                'catalogo/articulo/pedidos_proveedor', $idl, $fecha1, $fecha2, $pendientes);
	}

	/**
	 * Listado de Pedidos
	 * @param string $func Función de m_articulo
	 * @param string $title1 Título para pendientes
	 * @param string $title2 Título para todos
	 * @param string $url1 URL para pendientes
	 * @param string $url2 URL para todos
	 * @param int $id Id del artículo
	 * @param date $fecha1 Fecha desde
	 * @param date $fecha2 Fecha hasta
	 * @param bool $pendientes TRUE: Muestra solo los pendientes, FALSE: todos
	 * @return JSON
	 */
	private function _pedidos($func, $title1, $title2, $url1, $url2, $idl = null, $fecha1 = null, $fecha2 = null, $pendientes = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');

		$idl = isset($idl) ? $idl : $this->input->get_post('idl');
		$fecha1 = isset($fecha1) ? $fecha1 : $this->input->get_post('fecha1');
		$fecha2 = isset($fecha2) ? $fecha2 : $this->input->get_post('fecha2');
		$pendientes = isset($pendientes) ? $pendientes : $this->input->get_post('pendientes');
		$pendientes = (!empty($pendientes) ? $pendientes : FALSE);

		if (!empty($idl) && ((!$pendientes && !empty($fecha1) && !empty($fecha2) || ($pendientes)))) 
		{
			$art = $this->reg->load($idl);
			if (!empty($fecha1))
			$fecha1 = to_date($fecha1);
			if (!empty($fecha2))
			$fecha2 = to_date($fecha2);
			$docs = $this->reg->$func($idl, $fecha1, $fecha2, $pendientes);
			if (count($docs) > 0)
			sksort($docs, 'dFecha');
			$data['articulo'] = $art;
			$data['docs'] = $docs;
			$data['fecha1'] = $fecha1;
			$data['fecha2'] = $fecha2;
			$data['title'] = $this->lang->line((isset($pendientes) && $pendientes) ? $title1 : $title2);
			$message = $this->load->view('catalogo/pedidos', $data, TRUE);
			$this->out->html_file($message, $this->lang->line($pendientes ? $title1 : $title2) . ' ' . $idl, 'iconoReportTab');
			return;
		}
		else 
		{
			$data['url'] = site_url($pendientes ? $url1 : $url2);
			$data['title'] = $this->lang->line($pendientes ? $title1 : $title2);
			if (!empty($idl))
			$data['idl'] = $idl;
			$this->_show_js('get_list', 'catalogo/documentosasticulo.js', $data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Convierte un codigo EAN/ISBN a EAN/ISBN 10/ISBN 13
	 * @param string $code Código EAN/ISBN
	 * @return HTML_FILE
	 */
	private function _isbnean($code) 
	{
		$this->load->library('ISBNEAN');
		$this->load->model('catalogo/m_editorial');
		$this->load->model('proveedores/m_proveedor');
		$data['code'] = $code;
		$isbn = $this->isbnean->to_isbn($code, TRUE);
		$data['isbn10'] = isset($isbn['isbn10']) ? $isbn['isbn10'] : '';
		$data['isbn13'] = isset($isbn['isbn13']) ? $isbn['isbn13'] : '';
		$data['ean'] = $this->isbnean->to_ean($code);
		$data['ean1'] = $this->isbnean->to_ean($data['isbn10']);
		$data['ean2'] = $this->isbnean->to_ean($data['isbn13']);
		$data['is_ean'] = $this->isbnean->is_ean($code) ? $this->lang->line('SI') : $this->lang->line('NO');
		$data['is_isbn10'] = $this->isbnean->is_isbn($code, TRUE) ? $this->lang->line('SI') : $this->lang->line('NO');
		$data['is_isbn13'] = $this->isbnean->is_isbn($code) ? $this->lang->line('SI') : $this->lang->line('NO');

		$data['parts'] = $this->isbnean->isbnparts($data['isbn13']);
		if (isset($data['parts'])) 
		{
			$editorial = $this->m_editorial->search($data['parts']['publisher_id'], 0, 1);
			if (count($editorial) > 0) 
			{
				$ed = $this->m_editorial->load($editorial[0]['id']);
				$data['editorial'] = $editorial[0]['text'];
				$data['nIdEditorial'] = $editorial[0]['id'];
				if (isset($ed['nIdProveedor'])) 
				{
					$pv = $this->m_proveedor->load($ed['nIdProveedor']);
					$data['proveedor'] = format_name($pv['cNombre'], $pv['cApellido'], $pv['cEmpresa']);
					$data['nIdProveedor'] = $ed['nIdProveedor'];
				}
			}
		}
		return $data;
	}

	/**
	 * Convierte un codigo EAN/ISBN a EAN/ISBN 10/ISBN 13
	 * @param string $code Código EAN/ISBN
	 * @return HTML_FILE
	 */
	function isbnean($code = null) 
	{
		$this->userauth->roleCheck($this->auth . '.index');
		$code = isset($code) ? $code : $this->input->get_post('code');

		if ($code) 
		{
			$data = $this->_isbnean($code);

			$message = $this->load->view('catalogo/isbnean', $data, TRUE);
			// Respuesta
			echo $this->out->html_file($message, $this->lang->line('ISBN/EAN') . " {$code}", 'iconoReportTab');
		} 
		else 
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}

	/**
	 * Comprueba si un código es un ISBN
	 * @param string $code Código
	 * @return DATA
	 */
	function isbn($code = null) 
	{
		$this->userauth->roleCheck($this->auth . '.index');
		$code = isset($code) ? $code : $this->input->get_post('code');

		if (trim($code) != '') 
		{
			$data = $this->_isbnean($code);
			$data['success'] = TRUE;
			$this->out->send($data);
		}
	}

	/**
	 * Devuelve el precio y los datos de un código de artículo
	 * @param string $code Código
	 * @return DATA
	 */
	function precio($code = null) 
	{
		#$this->userauth->roleCheck($this->auth . '.get_list');
		
		$code = isset($code) ? $code : $this->input->get_post('code');

		$data['success'] = TRUE;
		if (trim($code) != '') 
		{
			$this->load->library('ISBNEAN');
			$isbn = $this->isbnean->to_ean($code);
			if ($isbn)
			{
				$l = $this->reg->get(null, null, null, null, 'nEAN=' . $isbn);
				if (count($l)>0)
				{
					$data = $l[0];
					$data['fPVP'] = format_price($l[0]['fPVP'], FALSE);
					$data['success'] = TRUE;
				}
				else 
				{
					$data['success'] = FALSE;	
				}				
			}
			else
			{
				if (is_numeric($code))
				{
					$l = $this->reg->load($code);
					$data = $l;
					$data['fPVP'] = format_price($l['fPVP'], FALSE);
					$data['success'] = TRUE;					
				}
				else 
				{
					$data['success'] = FALSE;
				}			
			}
		}
		$this->out->send($data);
	}

	/**
	 * Devoluciones por entregar de un artículo
	 * @param int $id Id del artículo
	 * @return HTML_FILE
	 */
	function devoluciones($id = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if ($id) 
		{
			$docs = $this->reg->get_documentos($id, null, null, null, array('saldevall', 'entdevall'));
			#var_dump($docs);
			if (count($docs) > 0) 
			{
				foreach ($docs as $k => $v) 
				{
					if (isset($v['dEntrega']))
					unset($docs[$k]);
				}
			}
			if (count($docs) > 0) 
			{
				$art = $this->reg->load($id);
				sksort($docs, 'dFecha');
				$data['articulo'] = $art;
				$data['titulo'] = $this->lang->line('devoluciones_sin_entregar');
				$data['docs'] = $docs;
				$data['fecha1'] = null;
				$data['fecha2'] = null;
				$data['item'] = $art['cTitulo'];
				$data['id'] = $art['id'];
				$data['clpv'] = FALSE;
				$message = $this->load->view('catalogo/documentos', $data, TRUE);
				$this->out->html_file($message, $this->lang->line('devoluciones_sin_entregar') . ' ' . $id, 'iconoReportTab');
			}
			$this->out->success($this->lang->line('no-devoluciones-sin-entregar'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Descuentos de los proveedores del artículo
	 * @param int $id Id del artículo
	 * @return JSON
	 */
	function descuentos($id = null) 
	{

		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if ($id) {
			$data = $this->reg->get_proveedores($id);
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Antigüedad del libros
	 * @param int $id Id del artículo
	 * @return HTML_FILE
	 */
	function antiguedad($id = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if ($id) 
		{
			$docs = $this->reg->get_antiguedad($id);
			if (count($docs) > 0) 
			{
				$art = $this->reg->load($id);
				$data['articulo'] = $art;
				$data['docs'] = $docs;
				$message = $this->load->view('catalogo/antiguedad', $data, TRUE);
				$this->out->html_file($message, $this->lang->line('antiguedad_articulo') . ' ' . $id, 'iconAntiguoTab');
			}
			$this->out->success($this->lang->line('no-antiguedad_articulo'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra el histórico de precios
	 * @param int $id Id del artículo
	 * @return HTML_FILE
	 */
	function historicoprecios($id = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if ($id) 
		{
			$docs = $this->reg->get_precios($id);
			if (count($docs) > 0) 
			{
				$art = $this->reg->load($id);
				$art['precios'] = $docs;
				$message = $this->load->view('catalogo/precios', $art, TRUE);
				$this->out->html_file($message, $this->lang->line('precios_articulo') . ' ' . $id, 'iconoReportTab');
			}
			$this->out->success($this->lang->line('no-precios_articulo'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra los clientes a los que hay que avisar de un artículo
	 * @param int $id Id del artículo
	 * @return FORM
	 */
	function avisar($id = null, $ids = null, $cmpid = null, $texto_rs = null, $texto_rc = null, $texto_srs = null, $texto_src = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$ids= isset($ids) ? $ids: $this->input->get_post('ids');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');

		$texto_rc = isset($texto_rc) ? $texto_rc: urldecode($this->input->get_post('texto_rc'));
		$texto_rs = isset($texto_rs) ? $texto_rs: urldecode($this->input->get_post('texto_rs'));
		$texto_src = isset($texto_src) ? $texto_src: urldecode($this->input->get_post('texto_src'));
		$texto_srs = isset($texto_srs) ? $texto_srs: urldecode($this->input->get_post('texto_srs'));

		if (is_numeric($id)) 
		{
			if (!empty($ids))
			{
				$asig = preg_split('/;/', $ids);
				$precios = array();
				$this->load->model('ventas/m_pedidoclientelinea');
				$this->load->model('ventas/m_pedidocliente');
				$this->load->model('comunicaciones/m_sms');
				$this->obj->load->library('Emails');
				$this->load->library('SmsServer');				
				set_time_limit(0);
				$result = array();
				foreach ($asig as $k => $a)
				{
					if (trim($a) != '')
					{
						$a = preg_split('/\#\#/', $a);
						$linea = $a[0];
						$modo = $a[1];
						$contacto = $a[2];
						$reservar = $a[3] == 'true';
						// Lee la línea
						$ln = $this->m_pedidoclientelinea->load($linea);
						if ($modo == $this->lang->line('EMAIL'))
						{
							if($this->_webpage)
							{
								$url = str_replace('%id%', $ln['nIdLibro'], $this->_webpage);
								$text = "<a href='{$url}'>{$ln['cTitulo']}</a>";
							}
							else 
							{
								$text = $ln['cTitulo'];
							}
							
							$data['texto_email'] = str_replace('%t', $text,($reservar || $ln['nIdEstado']==ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA)?$texto_rs:$texto_rc);
							$message = $this->load->view('main/email', $data, TRUE);
							$res = $this->obj->emails->send($this->lang->line('aviso-recibido-email-subject'), $message, array($contacto), null, null, null, $this->config->item('bp.documentos.css'));
							#$res = TRUE;
						}
						elseif ($modo == $this->lang->line('SMS'))
						{							
							$msg = str_replace('%t', format_title($ln['cTitulo'], 100), ($reservar || $ln['nIdEstado']==ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA)?$texto_srs:$texto_src);
							$sms = array(
								'cMensaje'	=> $msg,
								'cTo'		=> $contacto
							);
							$id = $data = $this->m_sms->insert($sms);
							
							$res = $this->smsserver->send($contacto, $msg, $id);
						}
						else {
							$res = '';
						}
						if ($res === TRUE)
						{
							$link_l = format_enlace_cmd($ln['cTitulo'], site_url('catalogo/articulo/index/' . $ln['nIdLibro']));
							$nota = sprintf($this->lang->line(($reservar || $ln['nIdEstado']==ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA)?'aviso-reservado-email-nota':'aviso-recibido-email-nota'), $link_l, $modo, $contacto);
							$aviso = TRUE;
							$this->m_pedidoclientelinea->avisado($linea, $aviso);
							if ($reservar) $this->m_pedidoclientelinea->update($linea, array('nCantidadServida' => $ln['nCantidad']));							
							$this->_add_nota(null, $ln['nIdPedido'], NOTA_INTERNA, $nota, $this->m_pedidocliente->get_tablename());
							$result[] = $modo . ' -> <b>' . $contacto . '</b> : <font color="green">' . $this->lang->line('OK') . '</font>' 
							.($reservar?' -> <font color="blue">' . $this->lang->line('RESERVADO') . '</font>':'');
						}
						else
						{
							$result[] = $modo . ' -> <b>' . $contacto . '</b> : <font color="red">' . $this->lang->line('ERROR'). '</font> : ' . $res;
						}						
					}
				}
				$message = $this->lang->line('avisos-enviados') . '<br>' . implode('<br/>', $result); 
				$this->out->dialog(TRUE, $message);							
			}
			// Formulario
			$data['id']  = $id;
			$data['cmpid'] = $cmpid;
			$data['texto_rs'] = empty($texto_rs)?$this->lang->line('aviso-reservado-email'):$texto_rs;
			$data['texto_rc'] = empty($texto_rc)?$this->lang->line('aviso-recibido-email'):$texto_rc;
			$data['texto_srs'] = empty($texto_srs)?$this->lang->line('aviso-reservado-sms'):$texto_srs;
			$data['texto_src'] = empty($texto_src)?$this->lang->line('aviso-recibido-sms'):$texto_src;

			$data['url'] = site_url('catalogo/articulo/get_avisos/' . $id);
			$this->_show_js('get_list', 'catalogo/avisararticulo.js', $data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra el histórico de precios
	 * @param int $id Id del artículo
	 * @return HTML_FILE
	 */
	function get_avisos($id = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if ($id) 
		{
			$data = $this->reg->get_avisos($id);
			$this->load->model('clientes/m_email');
			$this->load->model('clientes/m_telefono');
			$this->load->library('Sender');
			
			foreach($data as $k => $v)
			{
				$data[$k]['cCliente'] = format_name($v['cNombre'], $v['cApellido'], $v['cEmpresa']);
				// Móvil o SMS?
				$emails = $this->m_email->get_list($v['nIdCliente']);
				#var_dump($emails);
				$em = $this->utils->get_profile($emails, PERFIL_GENERAL);
				#$em = null;
				if (isset($em))
				{
					$modo = $this->lang->line('EMAIL');
					$contacto = $em['text'];
				}
				else
				{
					$tel = $this->m_telefono->get_list($v['nIdCliente']);
					#var_dump($tel);
					$num = $this->sender->get_mobile($tel, PERFIL_GENERAL);
					if (isset($num))
					{
					 	$modo = $this->lang->line('SMS');
						$contacto = $num['text'];						
					}
					else
					{
						$modo = $this->lang->line('NO SE PUEDE AVISAR');
						$contacto = '';
							
					}
				}
				$data[$k]['cModo'] = $modo;
				$data[$k]['cContacto'] = $contacto;
				$data[$k]['bReservado'] = FALSE;
			}
			
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Stock contado
	 * @param int $id Id del artículo
	 * @return HTML_FILE
	 */
	function stockcontado($id = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if ($id) 
		{
			$data = $this->reg->get_stockcontado($id);
			if (count($data) > 0) 
			{
				$art = $this->reg->load($id);
				$data['docs'] = $data;
				$data['articulo'] = $art;
				$message = $this->load->view('catalogo/stockcontado', $data, TRUE);
				$this->out->html_file($message, $this->lang->line('stock_contado_articulo') . ' ' . $id, 'iconoReportTab');
			}
			$this->out->success($this->lang->line('no-stockcontado'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Asigna la ubicación de un artículo
	 * @param int $idu ID de la ubicación
	 * @param int $idl ID del artículo
	 * return FORM
	 */
	function ubicar($idu = null, $idl = null) 
	{
		$this->userauth->roleCheck(($this->auth . '.ubicar'));

		$idu = isset($idu) ? $idu : $this->input->get_post('idu');
		$idl = isset($idl) ? $idl : $this->input->get_post('idl');

		if (is_numeric($idl) && is_numeric($idu)) 
		{
			$this->load->model('catalogo/m_articuloubicacion');
			$data = $this->m_articuloubicacion->get(null, null, null, null, "nIdLibro = {$idl} AND nIdUbicacion = {$idu}");
			if (count($data) == 0) {
				$id = $this->m_articuloubicacion->insert(array('nIdLibro' => $idl, 'nIdUbicacion' => $idu, 'dCreacion' => time()));
				if ($id <= 0) {
					$this->out->error($this->m_articuloubicacion->error_message());
				}
			} else {
				if (!$this->m_articuloubicacion->update($data[0]['nIdUbicacionLibro'], array('dCreacion' => time()))) {
					$this->out->error($this->m_articuloubicacion->error_message());
				}
			}
			$this->out->success($this->lang->line('articulo-ubicacion-ok'));
		} else {
			$this->_show_form('ubicar', 'catalogo/ubicar.js', $this->lang->line('Ubicar artículos'));
		}
	}

	/**
	 * Asigna la materia de un artículo
	 * @param int $idm ID de la materia
	 * @param int $idl ID del artículo
	 * return FORM
	 */
	function materias($idm = null, $idl = null) 
	{
		$this->userauth->roleCheck(($this->auth . '.upd'));

		$idm = isset($idm) ? $idm : $this->input->get_post('idm');
		$idl = isset($idl) ? $idl : $this->input->get_post('idl');

		if (is_numeric($idl) && is_numeric($idm)) 
		{
			$this->load->model('catalogo/m_articulomateria');
			$data = $this->m_articulomateria->get(null, null, null, null, "nIdLibro = {$idl} AND nIdMateria = {$idm}");
			if (count($data) == 0) 
			{
				$id = $this->m_articulomateria->insert(array('nIdLibro' => $idl, 'nIdMateria' => $idm));
				if ($id <= 0) 
				{
					$this->out->error($this->m_articulomateria->error_message());
				}
			} 
			$this->out->success($this->lang->line('articulo-materia-ok'));
		} 
		else 
		{
			$this->_show_form('upd', 'catalogo/materiasfast.js', $this->lang->line('Materías-artículos'));
		}
	}

	/**
	 * Buscador de portadas
	 * @param string $text Texto de búsqueda
	 * @param string $method Método
	 * @return JSON
	 */
	function search_portada($text = null, $method = null) 
	{
		//$this->userauth->roleCheck(($this->auth.'.ubicar'));

		$text = isset($text) ? $text : $this->input->get_post('text');
		$method = isset($method) ? $method : $this->input->get_post('method');

		$data = array();
		if ($text !== FALSE && trim($text) != '') 
		{
			$this->load->library('SearchImages');
			$this->load->library('ISBNEAN');
			$data = $this->searchimages->search($text, $method);
			$ean = $this->isbnean->to_ean($text);
			if ($ean)
			{
				$this->load->library('WebSave');
				$file = $this->websave->get_cover($ean);
				if (!empty($file))
					$data = array_merge(array(array('name' => $ean, 'url' => $file)), $data);
			}
		}
		$o = array('images' => $data);
		echo json_encode($o);
		return;
	}

	/**
	 * Actualiza la portada de un artículo
	 * @param int $id ID del artículo
	 * @param string $url URI de la imagen
	 * @param string $file Imagen en formato BLOB
	 * @return MSG
	 */
	function set_cover($id = null, $url = null) 
	{
		$this->userauth->roleCheck(($this->auth . '.upd'));

		$url = isset($url) ? $url : $this->input->get_post('url');
		$id = isset($id) ? $id : $this->input->get_post('id');
		if ($url === FALSE)	$url = null;
		$file = null;
		if (is_numeric($id)) 
		{
			$this->load->library('UploadLib');
			$data = $this->uploadlib->get_file('file');
			if (isset($data))
			{ 
				$file = $data['file'];
				$url = NULL;
			}
			if ($this->reg->set_portada($id, $url, $file)) 
			{
				$this->out->success($this->lang->line('portada-actualizada'));
			}
			$this->out->error($this->reg->error_message());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve la portada del artículo
	 * @param int $id Id del artículo
	 * @param int $size Tamaño de la imagen (null para tamaño real)
	 * @return IMG
	 */
	function cover($id = null, $size = null) 
	{
		#$this->userauth->roleCheck(($this->auth.'.get_list'));

		$id = isset($id) ? $id : $this->input->get_post('id');
		$size = isset($size) ? $size : $this->input->get_post('size');
		if (!is_numeric($size))
		$size = FALSE;
		if ($size === 0)
		$size = TRUE;
		if ($size === TRUE)
		$size = $this->obj->config->item('bp.catalogo.cover.thumb');

		if (is_numeric($id)) {
			$res = $this->reg->get_portada($id, $size);
			$this->load->library('SearchImages');
			$mime = $this->searchimages->get_mime($res['ext']);
			$res['mime'] = $mime;
			Header("Content-type: {$mime}\n");
			Header("Content-Transfer-Encoding: binary\n");
			readfile($res['file']);
			die();
		}
		die();
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#_pre_printer($id, $data, $css)
	 */
	protected function _pre_printer($id, &$data, &$css) {
		parent::_pre_printer($id, $data, $css);
		$data['proveedores_all'] = $this->reg->get_proveedores($id);
		$data['notas'] = $this->reg->get_notas($id);
		#print_r($data['notas']);
		$this->load->model('compras/m_reposicion');
		//Ventas totales
		$data['t_semana'] = $this->m_reposicion->get_ventas($id, 7, 'd');
		$data['t_mes'] = $this->m_reposicion->get_ventas($id, 1, 'm');
		$data['t_mes3'] = $this->m_reposicion->get_ventas($id, 3, 'm');
		$data['t_mes6'] = $this->m_reposicion->get_ventas($id, 6, 'm');
		$data['t_mes12'] = $this->m_reposicion->get_ventas($id, 12, 'm');
		$data['t_mes24'] = $this->m_reposicion->get_ventas($id, 24, 'm');
		$data['ult_docs_general'] = $this->reg->get_last_docs($id);
		$data['pedidos_cliente'] = $this->reg->get_pedidos_cliente($id, null, null, TRUE);
		$data['pedidos_proveedor'] = $this->reg->get_pedidos_proveedor($id, null, null, TRUE);
		if (isset($data['nIdOferta'])) {
			$this->load->model('catalogo/m_oferta');
			$data['oferta'] = $this->m_oferta->load($data['nIdOferta']);
		}

		$css = $this->config->item('bp.data.css');
		return TRUE;
	}

	/**
	 * Movimiento de artículos entre secciones
	 * @return FORM
	 */
	function mover()
	{
		$this->_show_form('upd', 'catalogo/moverseccion.js', $this->lang->line('Mover libros secciones'));
	}

	/**
	 * Comprueba si existen los ISBNs en artículos en la base de datos
	 * @param string @isbns ISBNS separadoas por punto y coma (;), comas (,), espacios o saltos de línea
	 * @param int $stock Unidades mínimas a buscar
	 * @param int $task 1, se añade como tarea, 0 se ejecuta
	 * @return HTML
	 */
	function check($isbns = null, $stock = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');
		$isbns = isset($isbns)?$isbns:$this->input->get_post('isbns');
		$stock = isset($stock)?$stock:$this->input->get_post('stock');
		if (!is_numeric($stock)) $stock = 0;
		if ($isbns)
		{
			$this->load->library('ISBNEAN');
			set_time_limit(0);
			$isbns = preg_split('/[\;\s\n\r\;]/', $isbns);
			$data = array();
			$no = array();
			foreach ($isbns as $e)
			{
				if (trim($e) != '')
				{
					$isbn = $this->isbnean->to_ean($e);
					if (isset($isbn))
					{
						$reg = $this->reg->get(null, null, null, null, "nEAN={$isbn}");
						if (count($reg) == 0)
						{
							$no[] = $e;
						}
						else
						{
							foreach($reg as $r)
							{
								$data[] = $this->reg->load($r['nIdLibro'], array('ubicaciones', 'secciones'));
							}
						}
					}
				}
			}
			$data = array(
				'title'		=> $this->lang->line('Comprobación artículos'),
				'titulos' 	=> $data, 
				'stock' 	=> $stock, 
				'no' 		=> $no
			); 
			
			$body = $this->load->view('catalogo/check', $data, TRUE);

			$this->out->html_file($body, $this->lang->line('Comprobación artículos'), 'iconoCheckArticulosTab');
		}
		else
		{
			$data = array(
				'title' => $this->lang->line('Comprobación artículos'),
				'icon' 	=> 'iconoCheckArticulosTab',
				'url'	=> 'catalogo/articulo/check',
				'stock' => 'true'
			);
			$this->_show_js('get_list', 'catalogo/check.js', $data);
		}
	}

	/**
	 * Comprueba los precios de los artículos en la competencia
	 * @param  string $isbns ISBNS separados por espacio, punto y coma, saltos de línea o tabulador
	 * @return HTML
	 */
	function check_precios($isbns = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');
		$isbns = isset($isbns)?$isbns:$this->input->get_post('isbns');
		if ($isbns)
		{
			$this->load->library('ISBNEAN');
			$this->load->library('SearchInternet');
			set_time_limit(0);
			$isbns = preg_split('/[\;\s\n\r\;]/', $isbns);
			$data = array();
			$no = array();
			$motores = array();
			foreach ($isbns as $e)
			{
				if (trim($e) != '')
				{
					$isbn = $this->isbnean->to_ean($e);
					if (isset($isbn))
					{
						$reg = $this->reg->get(null, null, null, null, "nEAN={$isbn}");

						if (count($reg) == 0)
						{
							$data[$isbn]['libro'] = null;
						}
						else
						{
							$data[$isbn]['libro'] = $this->reg->load($reg[0]['nIdLibro']);
						}
						$mot = $this->searchinternet->precios($isbn);
						foreach ($mot as $value) 
						{
							$data[$isbn]['precios'][$value['place']] = $value;
							$motores[$value['place']] = $value['icon'];
							if (!isset($data[$isbn]['libro']) && isset($value['title']))
							{
								$data[$isbn]['libro']=array(
									'cTitulo' 	=> $value['title'],
									'fPVP'		=> null,
									'cAutores'	=> null,
									'cISBN'		=> null
									);
							}
						}
					}
				}
			}
			$params = array(
				'title'		=> $this->lang->line('Comprobación precios'),
				'titulos' 	=> $data, 
				'motores' 	=> $motores, 
			); 
			#var_dump($data);die();
			$body = $this->load->view((count($data)==1)?'catalogo/check_precios_uno':'catalogo/check_precios', $params, TRUE);
			#echo $body; die();

			$this->out->html_file($body, $this->lang->line('Comprobación precios'), 'iconoConsultaPreciosTab');
		}
		else
		{
			$data = array(
				'title' => $this->lang->line('Comprobación precios'),
				'icon' 	=> 'iconoConsultaPreciosTab',
				'url'	=> 'catalogo/articulo/check_precios',
				'stock' => 'false'
			);
			$this->_show_js('get_list', 'catalogo/check.js', $data);
		}
	}

	/**
	 * Comprueba si existen los ISBNs en artículos en la base de datos
	 * @param string @isbns ISBNS separadoas por punto y coma (;), comas (,), espacios o saltos de línea
	 * @return HTML
	 */
	function descatalogar($isbns = null)
	{
		$this->userauth->roleCheck($this->auth .'.upd');
		$isbns = isset($isbns)?$isbns:$this->input->get_post('isbns');
		if ($isbns)
		{
			$this->load->library('ISBNEAN');
			set_time_limit(0);
			$isbns = preg_split('/[\;\s\n\r\;]/', $isbns);
			$data = array();
			$no = array();
			foreach ($isbns as $e)
			{
				if (trim($e) != '')
				{
					$isbn = $this->isbnean->to_ean($e);
					if (isset($isbn))
					{
						$reg = $this->reg->get(null, null, null, null, "nEAN={$isbn}");
						if (count($reg) == 0)
						{
							$no[] = $e;
						}
						else
						{
							foreach($reg as $r)
							{
								$data[] = $this->reg->load($r['nIdLibro'], array('ubicaciones', 'secciones'));
								$this->reg->update($r['nIdLibro'], array('nIdEstado' => 4));
							}
						}
					}
				}
			}
			$data = array(
				'title'		=> $this->lang->line('Descatalogar artículos'),
				'titulos' 	=> $data, 
				'no' 		=> $no
			); 
			
			$body = $this->load->view('catalogo/check', $data, TRUE);

			$this->out->html_file($body, $this->lang->line('Descatalogar artículos'), 'iconoDescatalogarArticulosTab');
		}
		else
		{
			$data = array(
				'title' => $this->lang->line('Descatalogar artículos'),
				'icon' 	=> 'iconoDescatalogarArticulosTab',
				'url'	=> 'catalogo/articulo/descatalogar',
				'stock' => 'false' 
			);
			$this->_show_js('get_list', 'catalogo/check.js', $data);
		}
	}
	
	/**
	 * Unificador
	 * @param int $id1 Id de destino
	 * @param string $id2 Ids repetidos, separados por ;
	 * @return JSON
	 */
	function unificar($id1 = null, $id2 = null)
	{
		$this->userauth->roleCheck(($this->auth.'.unificar'));

		$id1	= isset($id1)?$id1:$this->input->get_post('id1');
		$id2	= isset($id2)?$id2:$this->input->get_post('id2');

		if ($id1 && $id2)
		{
			$ids = preg_split('/\;/', $id2);
			$t = '';
			$this->load->library('Logger');
			if (!$this->reg->unificar($id1, $ids))
			{
				$str = $this->reg->error_message();
				$this->out->error($str);
			}
			$this->logger->log('Artículo unificado ' . implode(',', $ids) . ' con ' .$id1, 'unificador');
			$this->out->success($this->lang->line('articulos-unificados-ok'));
		}
		else
		{
			$data['title'] = $this->lang->line('Unificar artículo');
			$data['icon'] = 'iconoUnficarArticuloTab';
			$data['url_search'] = site_url('catalogo/articulo/search');
			$data['url'] = site_url('catalogo/articulo/unificar');
			$this->_show_form('unificar', 'catalogo/unificador.js', $this->lang->line('Unificar artículo'), null, null, null, $data);
		}
	}
	
	/**
	 * Muestra el stock de un artículo
	 * @param int $id Id del artículo
	 * @return HTML
	 */
	function stock($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if ($id) 
		{		
			$data = $this->reg->load($id, 'secciones');
			#$message = '<pre>' . print_r($data, TRUE) . '</pre>';
			$message = $this->load->view('catalogo/stock', $data, TRUE);
			$this->out->lightbox($message);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Muestra el stock de un artículo
	 * @param int $id Id del artículo
	 * @return HTML
	 */
	function ventas($id = null, $desde = null, $hasta = null, $ids = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		
		$desde	= isset($desde)?$desde:$this->input->get_post('desde', null);
		$hasta	= isset($hasta)?$hasta:$this->input->get_post('hasta', null);
		$id		= isset($id)?$id:$this->input->get_post('id', null);
		$ids 	= isset($ids) ? $ids : $this->input->get_post('ids');

		if (is_numeric($id)) 
		{
			$this->load->model('compras/m_reposicion');
			//Ventas totales
			$desde = to_date($desde);
			$hasta = to_date($hasta);
			
			$ventas = $this->m_reposicion->get_ventasperiodo($id, $desde, $hasta, $ids);
			$this->out->data(array('ventas' => $ventas));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}
	
	/**
	 * Actualizar precios
	 * @param string $file Nombre de los ficheros subido separados por punto y coma (;)
	 * @return FORM
	 */
	function precios($file = null, $fila1 = null, $isbn = null, $precio = null, $pvp = null, $next = null) 
	{
		$this->userauth->roleCheck($this->auth . '.upd');
		
		$file = isset($file) ? $file : $this->input->get_post('file');
		#$fila1 = isset($fila1) ? $fila1 : $this->input->get_post('fila1');
		$isbn = isset($isbn) ? $isbn: $this->input->get_post('isbn');
		$precio = isset($precio) ? $precio: $this->input->get_post('precio');
		$pvp = isset($pvp) ? $pvp : $this->input->get_post('pvp');
		$next = isset($next) ? $next: $this->input->get_post('next');
		
		if (!empty($file))
		{
			if (is_numeric($isbn))
			{
				# Hay que procesar un archivo
				set_time_limit(0);
				$this->load->library('ISBNEAN');
				$this->load->library('Importador');
				$this->load->library('UploadLib');
				$path = $this->uploadlib->get_pathfile($file);
				$columns['isbn'] = array('column' => $isbn);
				$columns['pvp'] = array('column' => $pvp);
				$columns['precio'] = array('column' => $precio);
				#$data = $this->importador->read($path);
				#Lee archivo
				$data = $this->importador->excel($path, null, $columns);
				#var_dump($data); die();
				
				#Actualiza los precios
				#$this->db->trans_begin();
				$count = 0;
				$lineas = array();
				foreach($data['libros'] as $libro)
				{					
					if ((isset($libro['original']['pvp']) && $libro['fPVP'] != $libro['original']['pvp'] && !isset($libro['nIdOferta'])) ||
					(isset($libro['original']['precio']) && $libro['fPrecio'] != $libro['original']['precio'] && !isset($libro['nIdOferta'])) ||
					(isset($libro['original']['precio']) && $libro['fPrecioOriginal'] != $libro['original']['precio'] && isset($libro['nIdOferta']))
					)
					{
						$idl = $libro['nIdLibro'];
						$a = $this->reg->load($idl, 'secciones');
						if (isset($libro['original']['pvp']))
						{
							$pvp = format_decimals($libro['original']['pvp']);
							$precio = format_quitar_iva($pvp, $a['fIVA']);
						}							
						elseif (isset($libro['original']['precio']))
						{
							$precio = $libro['original']['precio'];
							$pvp = format_add_iva($precio, $a['fIVA']);
						}
						else
						{
							$pvp = $precio = null;
						}
						#echo '<pre>';var_dump($a, $libro['original'], $precio, $pvp); echo '</pre>'; die();
						if (isset($pvp) && $pvp != $a['fPVP'])
						{								
							foreach ($a['secciones'] as $sec)
							{							
								if ($sec['nStockDeposito'] > 0)
								{
									$lineas[] = array(
										'nIdLibro' => $idl,
										'nIdSeccion' => $sec['nIdSeccion'],
										'nCantidad' => $sec['nStockDeposito'],
										'cSimbolo' => $this->lang->line('simbolo-deposito'),
										'fPVP' => $pvp
									);
								}
								if ($sec['nStockFirme'] > 0)
								{
									$lineas[] = array(
										'nIdLibro' => $idl,
										'nIdSeccion' => $sec['nIdSeccion'],
										'nCantidad' => $sec['nStockFirme'],
										'cSimbolo' => $this->lang->line('simbolo-firme'),
										'fPVP' => $pvp
									);
								}								
							}
							if (!$this->reg->update($idl, array('fPrecio' => $precio)))
							{
								#$this->db->trans_rollback();
								$this->out->error($this->reg->error_message());								
							}
							++$count;
						}						
					}
				}
				# Crea las etiquetas
				$this->load->model('catalogo/m_grupoetiqueta');
				if (count($lineas) > 0)
				{
					$ide = $this->m_grupoetiqueta->insert(array(
						'cDescripcion' => sprintf($this->lang->line('descripcion-etiquetas-fichero'), $file),
						'lineas' => $lineas
					));
					if ($ide < 0)
					{
						#$this->db->trans_rollback();
						$this->out->error($this->m_grupoetiqueta->error_message);
					}
				}
				#$this->db->trans_commit();
				#var_dump($lineas);
				if (isset($ide))
				{
					$link = format_enlace_cmd($ide, site_url('catalogo/grupoetiqueta/imprimir/' . $ide));
					$message[] = sprintf($this->lang->line('fichero-cambios-precios-etiquetas'), $count, $link);
				}
				else
				{
					$message[] = sprintf($this->lang->line('fichero-cambios-precios-no-etiquetas'), $count);
				}
				$this->out->dialog(TRUE, implode('<br/>', $message));				
			}
			else
			{
				set_time_limit(0);
				$files = preg_split('/;/', $file);
				$files = array_unique($files);
				
				$count = 0;
				foreach ($files as $k => $file)
				{
					$this->load->library('UploadLib');
					$this->load->library('Importador');
					//$this->load->library('ExcelData');
					if (!empty($file))
					{
						unset($files[$k]);
						# Abre el archivo EXCEL									
						$path = $this->uploadlib->get_pathfile($file);
						$data = $this->importador->read($path);
						if (is_array($data))
						{
							$preview = $this->load->view('catalogo/excelpreview', array('file' => $file, 'data' => $data), TRUE);
							$preview = str_replace(array('"', "\n", "\r"), array('\\"', '', ''), $preview);
							$params = array(
									'preview' 	=> $preview,
									'file'		=> $file,
									'next' 		=> implode(';', $files)
								);
							$this->_show_js('upd', 'catalogo/excelpreview.js', $params);
							return;												
						}
					}
				}
				if ($count > 0)
				{
					$this->out->success($file);
				}
				$this->out->error($this->lang->line('mensaje_faltan_datos'));		
			}
		}
		else
		{
			$this->_show_js('upd', 'catalogo/preciofile.js');
		}	
	}

	/**
	 * Realiza una búsqueda por palabra clave
	 *
	 * @param string $query Palabra de búsqueda
	 * @param int $start Registr inicio
	 * @param int $limit Contador de registros
	 * @param string $order Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param string $where Campos WHERE
	 */
	function revista($query = null, $start = null, $limit = null, $sort = null, $dir = null, $where = null)
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.search'));
		}
		$query	= isset($query)?$query:$this->input->get_post('query');
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		$where 	= isset($where)?$where:$this->input->get_post('where');
		
		$where = $this->reg->parse_where($where);
		$where .= (empty($where)?'':' AND ') . 'nIdTipo IN (6, 14)';
		$query = trim($query);
		$data = $this->reg->search($query, $start, $limit, $sort, $dir, $where);
		$this->out->data($data, $this->reg->get_count());
	}

	/**
	 * Devuelve los artículos relaciondos con el Id indicado
	 * @param int $id Id del artículo
	 * @return DATA
	 */
	function relacionados($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if (is_numeric($id)) 
		{
			$this->load->model('catalogo/m_relacionados');				
			$data = $this->m_relacionados->get(NULL, NULL, NULL, NULL, "nIdLibro1={$id} OR nIdLibro2={$id}");
			$data2 = array();
			foreach($data as $d)
			{
				$data2[] = ($d['nIdLibro1'] == $id)?array(
					'id'		=> $d['id'],
					'nIdLibro' 	=> $d['nIdLibro2'], 
					'cAutores' 	=> $d['cAutores2'],
					'cTitulo' 	=> $d['cTitulo2'],
					'fPrecio' 	=> $d['fPrecio2'],
					'nIdTipo' 	=> $d['nIdTipo2'],
					'fIVA'		=> $d['fIVA2'],
					'cISBN' 	=> $d['cISBN2']
					):
					array(
					'id'		=> $d['id'],
					'nIdLibro' 	=> $d['nIdLibro1'], 
					'cAutores' 	=> $d['cAutores1'],
					'cTitulo' 	=> $d['cTitulo1'],
					'fPrecio' 	=> $d['fPrecio1'],
					'nIdTipo' 	=> $d['nIdTipo1'],
					'fIVA'		=> $d['fIVA1'],
					'cISBN' 	=> $d['cISBN1']
					);
			}
			$this->out->data($data2);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));				
	}

	/**
	 * Relaciona 2 artículos
	 * @param int $id1 Id del articulo 
	 * @param int $id2 Id del segundo articulo
	 * @return MSG 
	 */
	function add_relacionado($id1 = null, $id2 = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id1 = isset($id1) ? $id1 : $this->input->get_post('id1');
		$id2 = isset($id2) ? $id2 : $this->input->get_post('id2');

		if (is_numeric($id1) && is_numeric($id2)) 
		{
			if ($id1 == $id2)
			{
				$this->out->error($this->lang->line('relacionados-mismo-titulo'));
			}
			$id_1 = min($id1, $id2);				
			$id_2 = max($id1, $id2);				
			$this->load->model('catalogo/m_relacionados');
			$data = $this->m_relacionados->get(NULL, NULL, NULL, NULL, "nIdLibro1={$id_1} AND nIdLibro2={$id_2}");
			if (count($data) > 0) $this->out->error($this->lang->line('relacionados-ya-relacionados'));
			if ($this->m_relacionados->insert(array('nIdLibro1' => $id_1, 'nIdLibro2' => $id_2)) > 0)
			{
				$this->out->success($this->lang->line('relacionados-ok'));
			}
			
			$this->out->error($this->m_relacionados->error_message());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));					
	}
	
	/**
	 * Elimina un artículo relacionado
	 * @param int $id Lista de Ids de relación separadas por ; 
	 * @return MSG 
	 */
	function del_relacionado($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.del'));
		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$count = 0;
			$ids= preg_split('/[\;\s\n\r\;]/', $id);
			$this->load->model('catalogo/m_relacionados');
			$this->db->trans_begin();
			foreach ($ids as $id)
			{
				if (trim($id) != '')
				{
					if (!$this->m_relacionados->delete($id))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_relacionados->error_message());
					}			
					++$count;
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('relacionados-delete-ok'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));							
	}

	/**
	 * Devuelve los artículos multimedia del artículo indicado
	 * @param int $id Id del artículo
	 * @return DATA
	 */
	function media($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if (is_numeric($id)) 
		{
			$this->load->model('catalogo/m_media');				
			$data = $this->m_media->get(NULL, NULL, NULL, NULL, "nIdLibro={$id}");
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));				
	}
	
	/**
	 * Procesa los documentos multimedia
	 * @return bool
	 */
	function media_process()
	{
		$this->load->model('catalogo/m_media');
		
		# Genera los documentos
		$data = $this->m_media->get(null, null, null, null, "cTipo='doc-file'");
		$count = 0;
		if (count($data) > 0)
		{
			$this->load->library('Scribd');
			$this->load->library('UploadLib');
			foreach ($data as $media)
			{
				$path = $this->uploadlib->get_pathfile($media['cUrl']);
				# Si el documento es doc, lo sube a scrib
				$res = $this->scribd->upload($path);
				#$res['doc_id'] = '81153678';
  				#$res['access_key'] =  'key-m1nopf8pww8qf9t8aqx';
  				#$res['secret_password'] = '2n4f9ry0iv1g24rhlei4';
				if ($res===FALSE)
				{
					$this->out->error($this->scribd->get_error());	
				}
				$url = $this->scribd->url($res['doc_id'], $res['access_key']);
				$datos = array(
					'cTitulo' => null,
					'cDescripcion' => null,
					'cTipo' => 'pdf',
					'cUrl' => $url
				);
				if (!$this->m_media->update($media['id'], $datos))
				{
					$this->out->error($this->m_media->error_message());
				}
				++$count;				
			}
		}
		
		# Genera los screenshots
		$data = $this->m_media->get(null, null, null, null, "cImagen IS NULL OR cImagen=''");
		$count = 0;
		if (count($data) > 0)
		{
			$this->load->library('Screenshot');
			$this->load->helper('asset');
			foreach ($data as $media)
			{
				$img = null;
				#var_dump($media);
				if ($media['cTipo'] == 'url')
				{
					$img = $this->screenshot->url($media['cUrl']);
					if (!$img)
						$img = image_asset_path('white500x500.png');
					#var_dump($img); die();
				}
				elseif ($media['cTipo'] == 'pdf')
				{
					if (strpos($media['cUrl'], 'scribd.com') !== FALSE)
					{
						# Es SCRIB
						$url = str_replace('fullscreen', 'doc', $media['cUrl']);
						#echo $url;						
						$images = $this->utils->get_images_url($url);
						#echo '<pre>'; print_r($images); echo '</pre>';
						if (isset($images[0])) $img = $images[0];
					}
					else 
					{
						$res = $this->utils->get_url($media['cUrl'], TRUE);
						// ¿Es un PDF?						
						if (strpos($res['headers']['content_type'], 'pdf') !== FALSE)
						{
							$img = $this->screenshot->pdf($media['cUrl']);
						}										
					}
				}
				if (isset($img))
				{
					$img = $this->utils->translate_url($img);
					$datos = array(
						'cImagen' => $img
					);
					if (!$this->m_media->update($media['id'], $datos))
					{
						$this->out->error($this->m_media->error_message());
					}
					++$count;
				}				
			}
		}
		
		$this->out->success(sprintf($this->lang->line('process-media-file-ok'), $count));		
	}
	
	/**
	 * Añade ficheros a un artículo
	 * @param int $id Id del artículo
	 * @param string $file Ficheros a añadir (separados por ;)
	 */
	function add_media_file($id = null, $file = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$file = isset($file) ? $file : $this->input->get_post('file');
		if (!empty($id) && !empty($file))
		{
			$files = preg_split('/;/', $file);
			$files = array_unique($files);
			
			$count = 0;
			$this->load->model('catalogo/m_media');
			$this->db->trans_begin();
			foreach ($files as $k => $file)
			{
				$this->load->library('UploadLib');
				if (!empty($file))
				{
					unset($files[$k]);
					# Añade el archivo a los media, para ser procesado
					$datos = array(
						'nIdLibro' => $id,
						'cTitulo' => $this->lang->line('procesando-doc-file'),
						'cDescripcion' => null,
						'cImagen' => null,
						'cTipo' => 'doc-file',
						'cUrl' => $file
					);
					if ($this->m_media->insert($datos) < 0)
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_media->error_message());
					}
					++$count;	
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('add-media-file-ok'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));				
	}

	/**
	 * Añade referencias multimedia a un artículo
	 * @param string $text Texto con la información (ISBN URL)
	 * @return JSON
	 */
	function add_media_text($isbns = null)
	{
		$this->userauth->roleCheck($this->auth . '.add');
		$text = isset($isbns) ? $isbns : $this->input->get_post('isbns');
		if (!empty($text))
		{
			$no = array();
			$elm = array();
			$error = array();
			$lineas = explode("\n", urldecode($text));
			$this->load->library('ISBNEAN');
			foreach ($lineas as $linea) 
			{
				$data = preg_split('/[\t|\s]/', $linea);
				if (count($data) > 1)
				{					
					$isbn = $this->isbnean->to_ean($data[0]);
					if (isset($isbn))
					{
						$reg = $this->reg->get(null, null, null, null, "nEAN={$isbn}");
						if (count($reg) == 0)
						{
							$no[] = $data[0];
						}
						else
						{
							foreach($reg as $r)
							{
								#Añade el elemento multimedia $r['nIdLibro']
								$i = 1;
								while ($i < count($data))
								{
									$link = format_enlace_cmd($r['cTitulo'], site_url('catalogo/articulo/index/' . $r['nIdLibro']));
									$res = $this->_add_media($r['nIdLibro'], $data[$i]);
									if (!$res)
										$error[] = sprintf($this->lang->line('add-media-text-elm-error'), $link, $data[$i], $res);
									else
										$elm[] = sprintf($this->lang->line('add-media-text-elm'), $link, $data[$i]);
									++$i;
								}
							}
						}
					}
				}
			}
			$data = array(
				'no'		=> $no,
				'error' 	=> $error, 
				'elm' 		=> $elm, 
			); 
			
			$body = $this->load->view('catalogo/multimedia', $data, TRUE);

			$this->out->html_file($body, $this->lang->line('Añadir elementos multimedia'), 'iconoReportTab');
		}
		$data = array(
			'title' => $this->lang->line('Añadir elementos multimedia'),
			'icon' 	=> 'icon-multimedia',
			'url'	=> 'catalogo/articulo/add_media_text',
			'stock' => 'false'
		);
		$this->_show_js('add', 'catalogo/check.js', $data);
	}

	/**
	 * Añade un elemento multimedia a un artículo
	 * @param int $id Id del articulo 
	 * @param string $url URL del elemento
	 * @return MSG 
	 */
	function add_media($id = null, $url = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$url = isset($url) ? $url : $this->input->get_post('url');

		if (is_numeric($id) && !empty($url)) 
		{
			$res = $this->_add_media($id, $url);
			if (!$res)
				$this->out->error($res);
			$this->out->success($this->lang->line('media-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));					
	}

	/**
	 * Añade un elemento multimedia a un artículo
	 * @param int $id Id del articulo 
	 * @param string $url URL del elemento
	 * @return MSG 
	 */
	function _add_media($id, $url)
	{
		// Lee los datos de la URL
		#echo $url; die();
		$contenido = $this->utils->get_url($url, TRUE);
		if (!isset($contenido['headers']['http_code']) || ($contenido['headers']['http_code'])!=200)
		{
			return $this->lang->line('url_devuelve_error');
		}			
		#var_dump($contenido); echo $contenido['response'];die();
		# Imagen
		if (strpos($contenido['headers']['content_type'], 'image') !== FALSE)
		{
			$title = '';
			$description = '';
			$image = $url;
			$type = 'image';
		}
		elseif (strpos($contenido['headers']['content_type'], 'pdf') !== FALSE)
		{
			$title = '';
			$description = '';
			$image = null;
			$type = 'pdf';
		}
		else
		{
			$contenido = $this->utils->get_url($url);
			$src = $contenido['response'];
			#$src = file_get_contents($url);	
			#echo $src; die();
           	preg_match_all( 
				"/<meta[^>]+(http\-equiv|name|property)=\"([^\"]*)\"[^>]" . "+content=\"([^\"]*)\"[^>]*>/i", 
				$src, $metas, PREG_PATTERN_ORDER);

			$description = null;
			$image = null;
			$type = 'url';
			$title = null;
			foreach ($metas[0] as $k => $v)
			{
				$metas[2][$k] = strtolower($metas[2][$k]);
				if ($metas[2][$k] == 'title' || $metas[2][$k] == 'og:title') $title = $metas[3][$k];
				if ($metas[2][$k] == 'type' || $metas[2][$k] == 'og:type') $type = $metas[3][$k];
				if ($metas[2][$k] == 'image' || $metas[2][$k] == 'og:image') $image = $metas[3][$k];
				if ($metas[2][$k] == 'description' || $metas[2][$k] == 'og:description') $description = $metas[3][$k];
				if ($metas[2][$k] == 'og:video') $type = 'video';				
				if ($metas[2][$k] == 'og:url') $url = $metas[3][$k];				
			}				

			// fetch description
			if (!isset($description))
			{
				$title_regex = "/<body[^>]*>(.*?)<\/body>/isU";
				preg_match_all($title_regex, $src, $descr, PREG_PATTERN_ORDER);
				if (isset($descr[1][0]))
				{
					$text = trim(strip_tags_attributes($descr[1][0]));
					$description = substr($text, 0, 255);
					if (strlen($text)> 255) $description .= '...';
				}			
			}
			// fetch images
			/*if (!isset($image))
			{ 				 
				$image_regex = '/<img[^>]*'.'src=[\"|\'](.*)[\"|\']/Ui';
				preg_match_all($image_regex, $src, $img, PREG_PATTERN_ORDER);
				if (isset($img[1][0]))
				{ 
					$image = $img[1][0];
					$this->load->library('SearchImages');
					$domain = $this->searchimages->get_domain($url);
					$image = $this->searchimages->add_domain($domain, $image);						
				}			
			}*/
			// fecth title
			if (!isset($title))
			{
				$title_regex = "/<title\b[^>]*>(.+)<\/title>/isUm";
				preg_match_all($title_regex, $src, $title, PREG_PATTERN_ORDER);
				if (isset($title[1][0])) $title = $title[1][0];
			}
		} 
		if (is_array($title)) $title = implode(' ', $title);
		$this->load->model('catalogo/m_media');
		$datos = array(
			'nIdLibro' => $id,
			'cTitulo' => $title,
			'cDescripcion' => html_entity_decode($description),
			'cImagen' => $image,
			'cTipo' => $type,
			'cUrl' => $url
			);
		#var_dump($datos); die();
		if ($this->m_media->insert($datos) > 0)
		{
			return TRUE; 
		}
		
		return $this->m_media->error_message();
	}
	
	/**
	 * Elimina un elemento multimedia de un artículo
	 * @param int $id Lista de Ids de elementos separadas por ; 
	 * @return MSG 
	 */
	function del_media($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.del'));
		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$count = 0;
			$ids= preg_split('/[\;\s\n\r\;]/', $id);
			$this->load->model('catalogo/m_media');
			$this->db->trans_begin();
			foreach ($ids as $id)
			{
				if (trim($id) != '')
				{
					if (!$this->m_media->delete($id))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_media->error_message());
					}			
					++$count;
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('media-delete-ok'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));							
	}

	/**
	 * Elimina la imagen de un elemento multimedia de un artículo
	 * @param int $id Lista de Ids de elementos separadas por ; 
	 * @return MSG 
	 */
	function del_media_image($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.del'));
		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$this->load->helper('asset');
			$img = image_asset_url('white500x500.png');
			$img = $this->utils->translate_url($img);
			$count = 0;
			$ids= preg_split('/[\;\s\n\r\;]/', $id);
			$this->load->model('catalogo/m_media');
			$this->db->trans_begin();
			foreach ($ids as $id)
			{
				if (trim($id) != '')
				{
					if (!$this->m_media->update($id, array('cImagen' => $img)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_media->error_message());
					}			
					++$count;
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('media-image-delete-ok'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));				
	}
	

	/**
	 * Act
	 * @param int $id Lista de Ids de elementos separadas por ; 
	 * @return MSG 
	 */
	/**
	 * Actualiza el texto decriptivo de un elemento multimedia
	 * @param  int $id   Id del elemento
	 * @param  string $text Nuevo texto descriptivo
	 * @return MSG
	 */
	function upd_media_text($id = null, $text = null)
	{
		$this->userauth->roleCheck(($this->auth .'.del'));
		$id = isset($id)?$id:$this->input->get_post('id');
		$text = isset($text)?$text:$this->input->get_post('text');

		if (is_numeric($id))
		{
			$this->load->model('catalogo/m_media');
			if (!$this->m_media->update($id, array('cDescripcion' => $text)))
			{
				$this->out->error($this->m_media->error_message());
			}			
			$this->out->success($this->lang->line('media-text-update-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));				
	}
	/**
	 * Importación de artículos desde DILVE
	 * @param string $isbn ISBN/EAN a buscar
	 * @param int $seccion Id de la sección a vincular
	 * @param int $materia Id de la materia
	 * @param bool $autor Crear autores si no existen
	 * @param bool $coleccion Crear colección si no existe 
	 * @param int $test 1: no crea, solo busca y devuelve dialog, 2: no crea, solo busca y devuelve TEXT
	 * @return MSG
	 */
	function dilve($isbn = null, $seccion = null, $materia = null, $autor = null, $coleccion = null, $test = null)
	{
		$this->userauth->roleCheck(($this->auth .'.add'));
		$isbn = isset($id)?$id:$this->input->get_post('isbn');
		$seccion = isset($seccion)?$seccion:$this->input->get_post('seccion');
		$materia = isset($materia)?$materia:$this->input->get_post('materia');
		$autor = isset($autor)?$autor:$this->input->get_post('Autor');
		$coleccion = isset($coleccion)?$coleccion:$this->input->get_post('coleccion');
		$test = isset($test)?$test:$this->input->get_post('test');
		if (!empty($isbn))
		{
			$this->load->library('ISBNEAN');
			$this->load->library('Importador');
			$isbn = $this->isbnean->to_isbn($isbn);
			$id_old = null;
			if ($isbn)
			{
				# Existe el libro?
				if ($test != 1)
				{
					$ean = $this->isbnean->to_ean($isbn);
					$l = $this->reg->get(null, null, null, null, 'nEAN=' . $ean);
					if (count($l) > 0)
					{
						$id_old = $l[0]['nIdLibro'];
						$title = $l[0]['cTitulo'];
						//$link = format_enlace_cmd($id, site_url('catalogo/articulo/index/' . $id));
						//$this->out->success(sprintf($this->lang->line('dilve-articulo-existe'), $link, $title));		
					}
				}
				$this->load->library('Dilve');
				$data = $this->dilve->get($isbn);
				#var_dump($data); die();
				if ($data)
				{
					$data = array_pop($data);
					#Descarga portada
					$this->dilve->check_cover($data);
					$res = $this->importador->onix($data);
					#var_dump(string_decode($res['titulo']), $res); die();
					if ($test > 0)
					{
						$message = '<h1>' . $isbn . '</h1><pre>' . print_r($data, TRUE) . print_r($res, TRUE) . '</pre>';
						if ($test == 1)	$this->out->html($message, $isbn);
						else 
						{
							 echo $message; exit;
						}
					}
					if (isset($id_old)) $res['id'] = $id_old;
					$autor = format_tobool($autor);
					$coleccion = format_tobool($coleccion);
					#var_dump($autor, $coleccion); die();
					$res = $this->importador->crear_libros(array($res), $autor, $coleccion);
					if ($res != FALSE)
					{
						if (isset($id_old))
						{
							$id = $id_old;
							$msg = $this->lang->line('dilve-articulo-existe');							
						} 
						else 
						{						
							$id = $res['libros'][0]['id'];
							$title = /*utf8_encode*/($res['libros'][0]['cTitulo']);
							$msg = $this->lang->line('dilve-articulo-creado');
						}
						if (is_numeric($seccion))
						{
							$this->load->model('catalogo/m_articuloseccion');
							$sc = $this->m_articuloseccion->get(0,0,0,0, "nIdLibro={$id} AND nIdSeccion={$seccion}");
							if (count($sc) == 0)
							{
								$this->m_articuloseccion->insert(array('nIdLibro' => $id, 'nIdSeccion' => $seccion));
							}
						}						
						if (is_numeric($materia))
						{
							$this->load->model('catalogo/m_articulomateria');
							$sc = $this->m_articulomateria->get(0,0,0,0, "nIdLibro={$id} AND nIdMateria={$materia}");
							if (count($sc) == 0)
							{
								$this->m_articulomateria->insert(array('nIdLibro' => $id, 'nIdMateria' => $materia));
							}
						}						
						$link = format_enlace_cmd($id, site_url('catalogo/articulo/index/' . $id));
						$msg = sprintf($msg, $link, $title);
						$this->out->success($msg);
					}
					$this->out->success($this->lang->line('dilve-articulo-no-creado'));		
				}
				else
				{
					$link = format_enlace_cmd($this->lang->line('crear-dilve'), site_url('catalogo/articulo/alta'));
					$msg = sprintf($this->lang->line('dilve-articulo-error-crear'), $this->dilve->get_error(), $link);
					$this->out->success($msg);
				}
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Alta rápida del artículo con datos de Internet
	 * @return FORM
	 */
	function internet()
	{
		$this->_show_form('add', 'catalogo/importadorinternet.js', $this->lang->line('Alta desde Internet'));
	}

	/**
	 * Quita la oferta a todos los títulos que no tienen stock
	 * @return MSG
	 */
	function quitar_oferta()
	{
		$count = $this->reg->quitar_oferta();

		$this->out->success(sprintf($this->lang->line('quitar_oferta-ok'), $count));
	}

	/**
	 * Actualiza los proveedores a los que se compra por artículo
	 * @return  DIALOG
	 */
	function compras_proveedores()
	{
		$this->load->library('Configurator');
		$last = (int)$this->configurator->system('catalogo.ultima_compra_proveedor');			
		$res = $this->reg->compras_proveedores($last);
		$this->configurator->set_system('catalogo.ultima_compra_proveedor', (string)$res['last']);
		$this->out->dialog(TRUE, sprintf($this->lang->line('actualizar-compras-proveedor-ok'), $res['count']));
	}

	/**
	 * Muestra un listado de las editoriales que no se pueden pedir para asignar a las correctas
	 * @param  int $concurso Id del concursos
	 * @return HTML
	 */
	function asignar_materias()
	{
		$this->userauth->roleCheck($this->auth . '.upd');

		$body = $this->load->view('catalogo/sinmateria', null, TRUE);
		$datos['title'] = $this->lang->line('Albaranes sin facturar');
		$datos['body'] = $body;
		$r = $this->load->view('main/bootstrap', $datos, TRUE);
		$this->out->html_file($r, $this->lang->line('Asignar materias'), 'iconoMateriasTab', null, TRUE);
	}

	/**
	 * Busca los artículos sin materia de la sección indicada
	 * @param  int $id Id de la sección
	 * @param int $start Registr inicio
	 * @param int $limit Contador de registros
	 * @param string $order Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @return DATA
	 */
	function sinmateria($id = null, $start = null, $limit = null, $sort = null, $dir = null, $conventas = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id = isset($id)?$id:$this->input->get_post('id');
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');

		$conventas 	= isset($conventas)?$conventas:$this->input->get_post('conventas');
		if (!isset($conventas))
			$conventas = TRUE;
		$conventas = format_tobool($conventas);

		if (is_numeric($id))
		{
			if (empty($sort)) $sort = 'Cat_Fondo.dCreacion';
			if (empty($dir)) $dir = 'DESC';
			if (empty($start)) $start = 0;
			if (empty($limit)) $limit = 10;

			$data = $this->reg->sinmateria($id, $start, $limit, $sort, $dir, $conventas);
			#var_dump($data); die();
			$this->out->data($data['data'], $data['count']);
		}
		$this->out->success($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Asigna la materia a los artículos indicados
	 * @param  int $mat Id de la materia
	 * @param  int $ids Id de los artículos separados por ;
	 * @return MSG
	 */
	function asignar_materia($mat = null, $ids = null)
	{
		$ids = isset($ids) ? $ids : $this->input->get_post('ids');
		$mat = isset($mat) ? $mat : $this->input->get_post('mat');		
		$ids = preg_split("/[,\|\s|;]/", $ids);
		if (is_numeric($mat) && count($ids) > 0)
		{
			$count = 0;
			$this->load->model('catalogo/m_articulomateria');
			$this->load->model('catalogo/m_materia');
			$upd['nIdMateria'] = $mat;
			$mat = $this->m_materia->load($mat);
			$this->db->trans_begin();
			foreach ($ids as $art)
			{
				if (is_numeric($art))
				{
					$upd['nIdLibro'] = $art;
					if (!$this->m_articulomateria->insert($upd))
					{
						$this->trans_rollback();
						$this->out->error($this->m_articulomateria->error_message());
					}
					++$count;
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('asignar-materia-ok'), $count, $mat['cNombre']));
		}
		$this->out->success($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Obtiene los IDs de las portadas de una sección dada
	 * @param  int $id Id de la sección
	 * @return DATOS
	 */
	function portadas($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (is_numeric($id))
		{
			$portadas = $this->reg->portadas($id);
			$this->out->data($portadas);
		}
		$this->out->success($this->lang->line('mensaje_faltan_datos'));

	}

	/**
	 * Análisis de las ventas de un artículo
	 * @param int $id Id del artículo
	 * @return HTML_FILE
	 */
	function analisis($id = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if (is_numeric($id) )
		{
			$ventas = $this->reg->ventas($id);
			for($i=1; $i < 13; $i++)
			{
				$base[$i] = 0;
			}
			$secciones = array();
			$totales = array();
			$ventas2 = array();
			$anos = array();
			foreach($ventas as $v)
			{
				if (!isset($ventas2[$v['cNombre']][$v['year']]))
					$ventas2[$v['cNombre']][$v['year']] = $base;
				$ventas2[$v['cNombre']][$v['year']][$v['month']] += $v['nCantidad'];
				if (!isset($totales[$v['year']]))
					$totales[$v['year']] = $base;
				$totales[$v['year']][$v['month']] += $v['nCantidad'];
				$anos[$v['year']] = $v['year'];
			}

			$data = array(
				'articulo' 		=> $this->reg->load($id),
				'secciones'		=> $ventas2,
				'totales'		=> $totales,
				'anos'			=> $anos
				);

			$message = $this->load->view('catalogo/ventas', $data, TRUE);
			#echo $message; die();
			$this->out->html_file($message, $this->lang->line('Análisis de ventas') . ' ' . $id, 'iconoReportTab', $this->config->item('bp.data.css'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Busca los artículos con estado DESCATALOGADO que han entrado libros desde el número de días indicaod
	 * @param int $dias Número de días
	 * @return array
	 */
	function revision_estado()
	{
		$this->userauth->roleCheck($this->auth . '.upd');
		$dias = $this->config->item('bp.catalogo.estado.descatalogados.dias');
		$res = $this->reg->revision_estado($dias);
		#var_dump($this->db->queries); die();
		if (count($res) > 0)
		{
			$this->load->model('catalogo/m_estadolibro');
			foreach ($res as $value) 
			{
				$this->reg->update($value['nIdLibro'], array('nIdEstado' => ESTADO_ARTICULO_A_LA_VENTA));
			}
			$this->out->success(sprintf($this->lang->line('revision_estado-ok'), count($res)));
		}
		$this->out->success($this->lang->line('revision_estado-ok-no'));
	}

	/**
	 * Gestiona el stock en depósito
	 * @param  int $id Id de la sección
	 * @return DATA, si Id<>NULL o HTML sino
	 */
	function depositos($id = null)
	{
		#$this->out->error('Programa en cuanrentena hasta nuevo aviso!!!');
		$this->userauth->roleCheck($this->auth .'.upd');

		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$this->load->model('catalogo/m_articuloseccion');
			$this->load->model('generico/m_seccion');
			$this->load->model('compras/m_albaranentradalinea');
			$this->load->model('compras/m_albaranentrada');
			$sec = $this->m_seccion->load($id);
			if (!$sec)
				$this->out->error($this->lang->line('mensaje_faltan_datos'));
			$data = $this->m_articuloseccion->get(null, null, null, null, "nStockDeposito>0 AND (Cat_Secciones.cCodigo LIKE '{$sec['cCodigo']}.%' OR nIdSeccion = {$id})");
			$res = array();
			foreach ($data as $value) 
			{
				$art = $this->reg->load($value['nIdLibro']);
				$alb = $this->m_albaranentradalinea->get(0, 1, 'dCreacion', 'DESC', "nIdLibro={$value['nIdLibro']} AND bDeposito=1");
				$q = 0;
				if (count($alb) > 0) 
				{
					$q = $alb[0]['nCantidad'];
					$alb = $this->m_albaranentrada->load($alb[0]['nIdAlbaran']);
				}
				$res[] = array(
					'nIdLibro'		=> $value['nIdLibro'],
					'nIdSeccion'	=> $value['nIdSeccion'],
					'cTitulo'		=> $art['cTitulo'],
					'cISBN'			=> $art['cISBN'],
					'cNombre'		=> $value['cNombre'],
					'nStockDeposito'=> $value['nStockDeposito'],
					'cProveedor'	=> isset($alb['cProveedor'])?$alb['cProveedor']:null,
					'nIdAlbaran'	=> isset($alb['nIdAlbaran'])?$alb['nIdAlbaran']:null,
					'dVencimiento'	=> isset($alb['dVencimiento'])?format_date($alb['dVencimiento']):'',
					'dFecha'		=> isset($alb['dFecha'])?format_date($alb['dFecha']):'',
					'nCantidad'		=> $q
					);
				//$data[$key] = $value;
			}
			$this->out->data($res);
		}
		$body = $this->load->view('catalogo/depositos', null, TRUE);

		$datos['title'] = $this->lang->line('Depósitos');
		$datos['body'] = $body;
		$r = $this->load->view('main/bootstrap', $datos, TRUE);
		#echo $r; die();
		$this->out->html_file($r, $this->lang->line('Depósitos'), 'iconoDepositosTab', null, TRUE);
	}

	/**
	 * Convierte el firme a depósito
	 * @param  int $id  Id del artículo
	 * @param  int $ids Id de la sección
	 * @return MSG
	 */
	function firme($id = null, $ids = null)
	{
		#$this->out->error('Programa en cuanrentena hasta nuevo aviso!!!');
		$this->userauth->roleCheck($this->auth .'.upd');

		$id = isset($id)?$id:$this->input->get_post('id');
		$ids = isset($ids)?$ids:$this->input->get_post('ids');
		if (is_numeric($id))
		{
			$this->load->model('catalogo/m_articuloseccion');
			$data = $this->m_articuloseccion->get(null, null, null, null, "nIdLibro={$id} AND nIdSeccion = {$ids}");
			if (count($data) > 0)
			{
				$stk = $data[0]['nStockDeposito'];
				if ($stk < 1)
					$this->out->error($this->lang->line('no-hay-depositos'));
				$this->load->model('stocks/m_arreglostock');
				$motivomas = $this->config->item('bp.depositos.motivomas');
				$motivomenos = $this->config->item('bp.depositos.motivomenos');
				$stk += $data[0]['nStockFirme'];
				if (!$this->m_arreglostock->arreglar($data[0]['nIdSeccionLibro'], $stk, 0, $motivomas, $motivomenos))
				{
					$this->out->error($this->m_arreglostock->error_message());
				}
				$this->out->success(sprintf($this->lang->line('depositos-a-firme'), $stk));
			}
			$this->out->error($this->lang->line('no-hay-depositos'));		
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Arreglo de los stocks a pasar a deposito [OBSOLETO]
	 * @return MSG
	 */
	function check_firme()
	{
		/*$data = $this->reg->check_error();
		$i = 0;
		foreach ($data as $l)
		{
			$docs = $this->reg->get_documentos($l['nIdLibro'],  mktime(0,0, 0, 12, 27, 2013), mktime(0,0, 0, 1, 1, 2014));
			var_dump($l, $docs); 
			++$i;
			if ($i == 10) die();
		}
		return;*/

		return;

		$data = $this->reg->check_firme();
		$res = array();
		foreach ($data as $reg)
		{
			$id = $reg['nIdSeccion'] . '_' . $reg['nIdLibro'];
			if (!isset($res[$id]))
				$res[$id] = 0;
			if ($reg['nIdMotivo'] == 22 || $reg['nIdMotivo'] == 24)
			{
				$res[$id] += $reg['nCantidadFirme'];
				$res[$id] += $reg['nCantidadDeposito'];
			}
			else
			{
				$res[$id] -= $reg['nCantidadFirme'];
				$res[$id] -= $reg['nCantidadDeposito'];				
			}
		}
		echo '<pre>';
		$this->load->model('catalogo/m_articuloseccion');
		$this->load->model('stocks/m_arreglostock');
		$motivomas = $this->config->item('bp.depositos.motivomas');
		$motivomenos = $this->config->item('bp.depositos.motivomenos');
		$this->db->trans_begin();
		foreach ($res as $id => $reg)
		{
			if ($reg != 0)
			{
				$id = explode('_', $id);
				$data = $this->m_articuloseccion->get(null, null, null, null, "nIdLibro={$id[1]} AND nIdSeccion = {$id[0]}");
				$stk1 = $data[0]['nStockDeposito'];
				$stk2 = $data[0]['nStockFirme'];
				$stk = $reg + $stk2;
				echo "{$id[1]}-{$id[0]} -> {$reg} -> FM: {$stk} DP: {$stk1}\n";
				if (!$this->m_arreglostock->arreglar($data[0]['nIdSeccionLibro'], $stk, $stk1, $motivomas, $motivomenos))
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_arreglostock->error_message());
				}
			}
		}
		$this->db->trans_commit();
		echo '</pre>';
		return;

		$data = $this->reg->check_firme();
		$res = array();
		foreach ($data as $reg)
		{
			$id = $reg['nIdSeccion'] . '_' . $reg['nIdLibro'];
			if (!isset($res[$id]))
				$res[$id] = 0;
			$res[$id] += $reg['nCantidadFirme'];
			$res[$id] -= $reg['nCantidadDeposito'];
		}
		echo '<pre>';
		foreach ($res as $id => $reg)
		{
			if ($reg != 0)
			{
				echo "{$id} -> {$reg}\n";
			}
		}
		echo '</pre>';
	}

	/**
	 * Regenera las portadas
	 * @param  string  $ext   Extensión
	 * @param  integer $limit Límite
	 */
	function check_portadas($ext = 'gif', $limit = 100)
	{
		$this->load->library('SearchImages');
		
		$result = $this->reg->portadas_ext($ext, $limit);
		echo count($result) . " Portadas con la extensión {$ext}\n";
		foreach ($result as $reg)
		{
			$cover = $this->reg->get_portada($reg['nIdRegistro']);
			echo "{$reg['nIdRegistro']} --> {$cover['file']}\n";
			#die();
			$this->reg->set_portada($reg['nIdRegistro'], null, $cover['file']);
			echo "OK\n";
		}
	} 

	/**
	 * Busca las portadas de todos los artículos entre dos Ids
	 * @param  int $id1    Id inicial
	 * @param  int $id2    Id final
	 * @param  bool $amazon Busca en AMAZON
	 * @param  bool $dilve  Busca en DILVE
	 * @param  int $muestra Número de items a leer de golpe
	 */
	function search_portadas($id1 = null, $id2 = null, $amazon = null, $dilve = null, $muestra = null)
	{
		$id1 = isset($id1)?$id1:$this->input->get_post('id1');
		$id2 = isset($id2)?$id2:$this->input->get_post('id2');		
		$amazon = isset($amazon)?$amazon:$this->input->get_post('amazon');
		$dilve = isset($dilve)?$dilve:$this->input->get_post('dilve');
		$muestra = isset($meustra)?$muestra:$this->input->get_post('muestra');

		$this->load->library('ISBNEAN');
		$this->load->library('SearchInternet');
		$this->load->library('WebSave');
		$this->load->library('Configurator');

		if (empty($amazon)) $amazon = TRUE;
		if (empty($dilve)) $dilve = TRUE;
		if (empty($muestra)) $muestra = CONST_MUESTRA_DEFECTO;

		error_reporting(E_ALL);
		$this->load->library('Dilve');
		$this->load->library('Color2');

		$timer_global = microtime(true);
		#$this->color2->head();
		#$this->color2->title('Portadas');
		#$this->color2->head();

		set_time_limit(0);
		$last = (int)$this->configurator->system('catalogo.ultimo_id_portada');
		$last = max($last - MARGEN_ID_SEARCH, 0);		
		if (!is_numeric($id1)) $id1 = $last;

		$id1_text = isset($id1)?$id1:'INICIO';
		$id2_text = isset($id2)?$id2:'FINAL';
		#$this->color2->info("Leyendo artículos de {$id1_text} a {$id2_text}");
		$data = $this->reg->sinportada($id1, $id2);
		#$data = $this->m_articulo->portadamala();
		$total = count($data);
		#$this->color2->info('Leidos %_' . $total);
		$portadas = 0;
		$count = 0;
		$no_esta = array();
		# Busca lo que ya tenemos
		foreach ($data as $reg)
		{
			++$count;
			$local = $this->websave->get_cover($reg['nEAN'], FALSE);
			if (isset($local))
			{
				++$portadas;
				#$this->color2->line("({$portadas} - {$count}/{$total}):[{$reg['nIdLibro']}] - %gLOCAL%_ - %_{$local}");		
				$this->reg->set_portada($reg['nIdLibro'], $local);
			}
			else
			{
				$no_esta[] = $reg;
			}
			$last = $reg['nIdLibro'];
		}
		#Lo que no está lo busca en DILVE
		if ($dilve && (count($no_esta) > 0))
		{
			$portadas += count($no_esta);
			$no_esta = $this->searchinternet->buscar_dilve($no_esta, TRUE, FALSE, $muestra);
			$portadas -= count($no_esta);
		}
		#Lo que no está lo busca en AMAZON
		if ($amazon && (count($no_esta) > 0))
		{
			$portadas += count($no_esta);
			$no_esta = $this->searchinternet->buscar_amazon($no_esta, TRUE, FALSE, $muestra);
			$portadas -= count($no_esta);
		}
		$this->configurator->set_system('catalogo.ultimo_id_portada', (string)$last);
		$this->out->success(sprintf($this->lang->line('search-portadas-result'), $portadas, $count, microtime(true)-$timer_global));
	}

	/**
	 * Busca las sinospsis de los artículos
	 * @param  int $id1    Id inicial
	 * @param  int $id2    Id final
	 * @param  bool $amazon Busca en AMAZON
	 * @param  bool $dilve  Busca en DILVE
	 * @param  int $muestra Número de items a leer de golpe
	 */
	function search_sinopsis($id1 = null, $id2 = null, $amazon = null, $dilve = null, $muestra = null)
	{
		$id1 = isset($id1)?$id1:$this->input->get_post('id1');
		$id2 = isset($id2)?$id2:$this->input->get_post('id2');
		$amazon = isset($amazon)?$amazon:$this->input->get_post('amazon');
		$dilve = isset($dilve)?$dilve:$this->input->get_post('dilve');
		$muestra = isset($meustra)?$muestra:$this->input->get_post('muestra');

		$this->load->library('ISBNEAN');
		$this->load->library('SearchInternet');
		$this->load->library('WebSave');
		$this->load->library('Configurator');

		if (empty($amazon)) $amazon = TRUE;
		if (empty($dilve)) $dilve = TRUE;
		if (empty($muestra)) $muestra = CONST_MUESTRA_DEFECTO;

		error_reporting(E_ALL);
		$timer_global = microtime(true);
		#$this->color2->head();
		#$this->color2->title('Sinopsis');
		#$this->color2->head();

		set_time_limit(0);
		$last = (int)$this->configurator->system('catalogo.ultimo_id_sinopsis');
		$last = max($last - MARGEN_ID_SEARCH, 0);		
		if (!is_numeric($id1)) $id1 = $last;

		$id1_text = isset($id1)?$id1:'INICIO';
		$id2_text = isset($id2)?$id2:'FINAL';
		#$this->color2->info("Leyendo artículos de {$id1_text} a {$id2_text}");
		$data = $this->reg->sinsinopsis($id1, $id2);
		$total = count($data);
		#$this->color2->info('Leidos %_' . $total);
		$portadas = 0;
		$count = 0;
		$no_esta = array();
		# Busca lo que ya tenemos
		foreach ($data as $reg)
		{
			++$count;
			$local = $this->obj->websave->get_description($reg['nEAN']);
			if (!empty($local))
			{
				$upd['sinopsis']['tSinopsis'] = $local;
				++$portadas;
				#$this->color2->line("({$portadas} - {$count}/{$total}):[{$reg['nIdLibro']}] - %gLOCAL%_ - %_{$local}");
				$this->reg->set_sinopsis($reg['nIdLibro'], str_replace("\n", "<br/>", $local));
			}
			else
			{
				$no_esta[] = $reg;
			}			
			$last = $reg['nIdLibro'];
		}
		#Lo que no está lo busca en DILVE
		if ($dilve && (count($no_esta) > 0))
		{
			$portadas += count($no_esta);
			$no_esta = $this->searchinternet->buscar_dilve($no_esta, FALSE, TRUE, $muestra);
			$portadas -= count($no_esta);
		}
		#Lo que no está lo busca en AMAZON
		if ($amazon && (count($no_esta) > 0))
		{
			$portadas += count($no_esta);
			$no_esta = $this->searchinternet->buscar_amazon($no_esta, FALSE, TRUE, $muestra);
			$portadas -= count($no_esta);
		}
		$this->configurator->set_system('catalogo.ultimo_id_sinopsis', (string)$last);
		$this->out->success(sprintf($this->lang->line('search-sinopsis-result'), $portadas, $count, microtime(true)-$timer_global));
		
		#$this->color2->line("%ySe han encotrado %_{$portadas}/{$count}%n%y sinopsis ".sprintf("en %%_%fs%%n", microtime(true)-$timer_global));
	}

	/**
	 * Asigna editoriales según código a artículos con ISBN
	 * @param  integer $fix   0: solo muestra, 1: corrige
	 * @param  integer $count Número de artículos máximo a corregir
	 * @param  integer $id    Id del artículo inicial
	 * @return HTML
	 */
	function fixeditoriales($fix = 0, $count = 100, $id = 0)
	{
		/*if ($task == 1)
		{
			$cmd = site_url("tools/catalogo/fixeditoriales/{$fix}/$count");

			$this->load->library('tasks');
			$this->tasks->add2($this->lang->line('Corregir editoriales artículo') , $cmd);
		}*/

		set_time_limit(0);
		$this->load->library('Logger');
		$this->load->library('ISBNEAN');
		$this->load->model('catalogo/m_editorial');
		$this->load->library('Messages');
		$ok = 0;
		$nok = 0;
		$nocodes = array();
		$data = $this->reg->get(0, $count, 'Cat_Fondo.nIdLibro', 'ASC', 'nIdEditorial IS NULL AND cISBNBase IS NOT NULL AND nIdLibro >= '.$id);
		foreach($data as $reg)
		{
			$id = format_enlace_cmd($reg['nIdLibro'], site_url('catalogo/articulo/index/' . $reg['nIdLibro']));
			if (isset($reg['cISBNBase']))
			{
				$isbn = $this->isbnean->to_isbn($reg['cISBNBase']);
				if ($isbn!='')
				{
					$parts = $this->isbnean->isbnparts($isbn);
					if (isset($parts))
					{
						$editorial = $this->m_editorial->search($parts['publisher_id'], 0, 1);
						if (count($editorial) > 0)
						{
							$ed = $this->m_editorial->load($editorial[0]['id']);
							$this->messages->info("{$id} - <strong>{$reg['cTitulo']}</strong> ({$reg['cISBNBase']}) -> {$editorial[0]['text']} -> {$ed['nIdProveedor']}");
							if ($fix == 1)
							{
								$this->logger->Log("{$reg['nIdLibro']} - {$reg['cTitulo']} ({$reg['cISBNBase']}) -> {$editorial[0]['text']} -> {$ed['nIdProveedor']}", 'catalogo');
								$this->reg->update($reg['nIdLibro'], array(
									'nIdEditorial' => $editorial[0]['id'],
									'nIdProveedor' => $ed['nIdProveedor']
								));
								++$ok;
							}
						}
						else
						{
							if (!isset($nocodes[$parts['publisher_id']]))
								$nocodes[$parts['publisher_id']] = 0;
							++$nocodes[$parts['publisher_id']];
							++$nok;
							$this->messages->warning("{$id} - <strong>{$reg['cTitulo']}</strong> ({$reg['cISBNBase']}) -> NO EDITORIAL ({$parts['publisher_id']})");
						}
					}
					else
					{
						++$nok;
						$this->messages->warning("{$id} - <strong>{$reg['cTitulo']}</strong> ({$reg['cISBNBase']}) -> ISBN NO CORRECTO");
					}
				}
			}
			else
			{
				++$nok;
				$this->messages->warning("{$id} - <strong>{$reg['cTitulo']}</strong> -> NO ISBN");
			}
		}
		#var_dump($ok, $nok);
		#die();
		$this->messages->info("NO Se han corregido {$nok} artículos");
		$this->messages->info("Se han corregido {$ok} artículos");
		$this->messages->info("Se han corregido {$ok} artículos");
		foreach ($nocodes as $key => $value) 
		{
			$this->messages->info(sprintf("[%s] = %3d", $key, $value));
		}

		$body = $this->messages->out($this->lang->line('Corregir editoriales artículo'));
		$this->out->html_file($body, $this->lang->line('Corregir editoriales artículo'), 'iconoReportTab');
	}

	/**
	 * Arregla ISBNS 
	 * @param  integer $do Genera informe
	 * @return HTML
	 */
	function fixisbn($do = 0)
	{
		$this->load->model('catalogo/m_articulo');
		$this->load->library('ISBNEAN');
		$data = $this->m_articulo->get(null, null, null, null, 'LEN(cISBNBase) = 12');
		$count = 0;
		$message = '<pre>';
		foreach ($data as $l)
		{
			$isbn = $this->isbnean->to_isbn($l['cISBNBase'], TRUE);
			$message .= "{$l['cISBNBase']} -> {$isbn['isbn13']} -> {$isbn['isbn10']}\n";
			if ($do == 1) $this->m_articulo->update($l['nIdLibro'], array('cISBN' => $isbn['isbn13'], 'cISBN10' => $isbn['isbn10']));
			++$count;
		}
		$message .= "Se han corregido {$count} artículos";
		$message .= '</pre>';
		if ($do == 1)
		{
			$this->out->success("Se han corregido {$count} artículos");
		}
		else
		{
			$this->out->html($message);
		}
	}


}
/* End of file articulo.php */
/* Location: ./system/application/controllers/catalogo/articulo.php */
