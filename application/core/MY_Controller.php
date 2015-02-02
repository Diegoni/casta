<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	core
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

 /**
  * @page my_controller_page Controlador
  * 
  * @section my_controller_page_description Descripción
  *
  * Todos los controladores deben heredar de MY_Controller, que a su vez es una clase heredera de Controller.
  * 
  * Un controlador por lo general está vinculada un modelo de datos de la clase MY_Model y ofrece todo la API de acceso
  * CRUD a estos datos y una vista genérica para poder ver, añadir, editar y eliminar registros.
  * 
  * También ofrece métodos para la seguridad y el control de errores.   
  *
  *  @section my_controller_page_vista_defecto Vista genérica
  * 
  * Es un grid con todos los campos definidos en el modelo de datos vinculado. Se puede indicar un fichero JS 
  * en la llamada al constructor MY_Controller::__construct
  * adicional que contendrá el menú contextual que se abrirá sobre cada línea del registro. 
 */

/**
 * Tipos de nota para el histórico
 * @var int
 */
define('NOTA_INTERNA', 	3);
define('NOTA_NORMAL', 	1);

/**
 * Controlador própio con funciones de uso general
 *
 */
class MY_Controller extends CI_Controller
{
	/**
	 * Nombre de la entrada en la autentificación del fichero 'auth.php'
	 *
	 * @var string
	 */
	protected $auth;
	/**
	 * Modelo de datos
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * Vista Index
	 * @var string
	 */
	protected $index_view = null;
	
	/**
	 * Fichero js con el menú contextual para el index por defecto
	 * @var string
	 */
	protected $submenu = null;

	/**
	 * Report de impresión por defecto
	 * @var string
	 */
	protected $report = null;

	/**
	 * Título de los formularios
	 * @var string
	 */
	protected $title = null;

	/**
	 * Objeto CI
	 * @var CI_Base
	 */
	protected $obj = null;
	/**
	 * Constructor. Comprueba usuario logeado
	 *
	 * @param string $auth Variable de confuguración de la seguridad
	 * @param string $model Nombre del modelo
	 * @param bool $check_loged TRUE: El usuari debe estar logueado, FALSE: No es necesario
	 * @param string $index_view Fichero JS que contiene la vista del modelo de datos
	 * @param string $title Título del modelo de datos
	 * @param string $submenu Fichero JS con el menú contextual 
	 * @return MY_Controller
	 */
	function __construct($auth = null, $model = null, $check_loged = FALSE, $index_view = null, $title = null, $submenu = null)
	{
		parent::__construct();
		$this->obj =& get_instance();

		if ($check_loged)
		{
			$this->obj->load->library('Userauth');
			$this->userauth->check_login(null, null, null);
		}
		$this->auth = $auth;
		$this->model = $model;
		$this->submenu = $submenu;
		$this->load->helper('formatters');
		$this->index_view = $index_view;
		$this->title = $this->obj->lang->line($title);
		if (isset($model)) $this->load->model($model,'reg');
	}

	/**
	 * Función interna que devuelve el formulario por defecto.
	 * Se debe sobreescribir para crear un formulario distinto o ampliar el que hay.
	 * @param int $id ID del formulario
	 * @param string $icon Icono del formulario
	 * @return string
	 */
	protected function _index_form($id, $icon, $open_id)
	{
		$modelo = $this->reg->get_data_model();
		return extjs_creategrid($modelo, $id, $this->lang->line($this->title), $icon, $this->auth, $this->reg->get_id(), null, TRUE, null, null, $this->submenu, TRUE);
	}

	/**
	 * Formulario principal
	 * @return HTML
	 */
	function index($open_id = null, $data = null)
	{
		$open_id = isset($open_id)?$open_id:$this->input->get_post('open_id');
		if (isset($this->index_view))
		{
			$this->_show_form('index', $this->index_view, $this->title, null, null, $open_id, $data);
		}
		else
		{
			$this->crud($open_id);
		}
	}

	/**
	 * Formulario CRUD
	 * @return HTML
	 */
	function crud($open_id = null)
	{
		$open_id = isset($open_id)?$open_id:$this->input->get_post('open_id');
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.index'), '', TRUE);
		}

		$id 	= isset($id)?$id:$this->input->get_post('id');
		$icon 	= isset($icon)?$icon:$this->input->get_post('icon');

		$this->load->helper('extjs');
		$this->load->helper('asset');

		$form = $this->_index_form($id, $icon, $open_id);

		if (isset($id) && ($id != ''))
		{
			//echo $form;
			$this->out->window($form);
		}
		else
		{
			unset($datos);
			$datos['form'] = $form;
			$datos['title'] = $this->lang->line($this->title);
			$datos['script'] = $this->load->view('main/main_app.js', $datos, true);
			if (isset($js_files)) $datos['js_include'] = $js_files;
			if (isset($css_files)) $datos['css_include'] = $css_files;
			$this->load->view('main/main', $datos);
		}
	}

	/**
	 * Crea un registro
	 * @param MY_Model Modelo de datos
	 * @param int $id Id del registro si se tiene que actualizar
	 * @param array $extra Campos y valores extras
	 */
	protected function _add_reg($reg, $id = null, $extra = null)
	{
		$upd = is_numeric($id);
		// Autorización
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth . ($upd?'.upd':'.add')));
		}
		// Modelo de datos?
		$data = $reg->get_data_model();
		$data2 = array();

		// Obtiene los datos
		foreach($data as $field => $values)
		{
			// Valor
			$value = ($this->input->get_post($values[0]));
			if ($value === FALSE)
			{
				// se puede enviar con _ del inicio de la variable.
				// Se tiene que hacer para los combos en los formularios, ya que hiddenname no puede ser
				// iguales
				$value = ($this->input->get_post('_' . $values[0]));
			}

			if (isset($values[DATA_MODEL_READONLY]) && $values[DATA_MODEL_READONLY] === TRUE)
			{
				$value = FALSE;
			}

			// Valor por defecto
			if ($value === FALSE && !$upd)
			{
				if (isset($values[DATA_MODEL_DEFAULT_VALUE]))
				{
					$value = $values[DATA_MODEL_DEFAULT_VALUE];
				}
			}

			// Comprueba que estén los valores solo si es un ADD
			if ($value !== FALSE /*&& $value != ''*/)
			{
				#echo $value . ' => ' . urldecode($value) . '<br/>';
				$data2[$field] = /*urldecode*/($value);
			}
		}

		// Obtiene los posible valores de relaciones
		$rels = $reg->get_relations();
		foreach($rels as $rel)
		{
			$set = $this->input->get_post($rel);
			if (isset($set) && ($set!='') && (count($set) > 0))
			{
				$data2[$rel] = $set;
			}
		}

		// Valores extras
		if (isset($extra)) $data2 = array_merge($data2, $extra);
		#var_dump($data2); die();
		// Crea/actualiza el registro
		$res = TRUE;
		if ($id)
		{
			// UPD
			if (!$reg->update($id, $data2))
			{
				$res = $reg->error_message();
			}
		}
		else
		{
			// ADD
			$id = $reg->insert($data2);
			if (!isset($id) || $id < 0 )
			{
				$res = $reg->error_message();
			}
		}

		return array($res, $id);
	}

	/**
	 * Crea un registro
	 *
	 * @param int $id Id del registro si se tiene que actualizar
	 * @param array $extra Campos y valores extras
	 */
	protected function _add($id = null, $extra = null)
	{
		$upd = is_numeric($id);

		list($res, $id) = $this->_add_reg($this->reg, $id, $extra);


		// Respuesta
		if ($res === TRUE)
		{
			$ajax_res = array(
					'success' 	=> TRUE,
					'message'	=> sprintf($this->lang->line(($upd?'registro_actualizado':'registro_generado')), $id),
					'id'		=> (int) $id
			);
			$this->out->send($ajax_res);
		}
		else
		{
			$this->out->error(($res == '')?$this->lang->line('registro_no_creado'):$res);
		}
		$this->out->send($ajax_res);
	}

	/**
	 * Lista los registros
	 *
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $where Condiciones de la consulta
	 * @param string $query Palabra clave de búsqueda
	 * @return DATA
	 */
	function get_list($start = null, $limit = null, $sort = null, $dir = null, $where = null, $query = null)
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.get_list'));
		}
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		$where 	= isset($where)?$where:$this->input->get_post('where');
		$query 	= isset($query)?$query:$this->input->get_post('query');
		if (trim($query) == '') $query = null;
		// El where tiene el formato <field>=<valor>&....
		#var_dump($where);
		$where = $this->reg->parse_where($where);
		#var_dump($where); die();
		$data = $this->reg->get($start, $limit, $sort, $dir, $where, null, $query);
		#echo array_pop($this->db->queries); die();

		$this->out->data($data, $this->reg->get_count());
	}

	/**
	 * Obtiene los datos de un registro
	 * @param int $id Id del registro
	 * @param string $relation Relación individual a cargar
	 * @param string $cmpid Id del componente que solicita los datos
	 * @return JSON
	 */
	function get($id = null, $relation = null, $cmpid = null)
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.get_list'));
		}

		$id	= isset($id)?$id:$this->input->get_post('id');
		$relation	= isset($relation)?$relation:$this->input->get_post('relation');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');

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

				$this->_post_get($id, $relation, $data, $cmpid);
				#var_dump($data); die();
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
	 * Actualiza un registro
	 *
	 * @return JSON
	 */
	function upd()
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.upd'));
		}
		$id = $this->input->get_post('id');
		$this->_add($id);
	}

	/**
	 * Actualiza un registro
	 *
	 * @return JSON
	 */
	function clear_cache()
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.upd'));
		}
		$this->reg->clear_cache();
		$this->out->success($this->lang->line('clear-cache-ok'));
	}

	/**
	 * Añade un registro
	 *
	 * @return JSON
	 */
	function add()
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.add'));
		}
		$this->_add();
	}

	/**
	 * Función interna llamada antes de imprimir
	 * @param int $id Id del registro
	 * @param array $data Registro
	 * @param string $css Fichero CSS a aplicar
	 * @return bool
	 */
	protected function _pre_printer($id, &$data, &$css)
	{
		$css = $this->config->item('bp.documentos.css');
		return TRUE;
	}

	/**
	 * Hook para las llamadas después de leer los datos
	 * @param int $id Id del registro
	 * @param mixed $relations Relaciones
	 * @param array $data Datos leídos
	 */
	protected function _post_get($id, $relations, &$data, $cmpid = null)
	{
		return TRUE;
	}

	/**
	 * Obtiene el listado de reports de un controlador
	 */
	protected function _get_reports()
	{
		$this->load->library('Reports');
		$reports = $this->reports->get_list($this->auth);
		return $reports;
	}

	/**
	 * Obtiene nombre del report por defecto
	 */
	protected function _get_report_default()
	{
		$this->load->library('Reports');
		$reports = $this->reports->get_list($this->auth);
		if (is_array($reports))
		{
			foreach ($reports as $report)
			{
				if (isset($report['default']) && $report['default'])
				{
					return $report['id'];
				}
			}
		}
		return $reports;
	}

	/**
	 * Función de impresión del controlador
	 * @param int $id Id del registro a imprimir
	 * @return JSON
	 */
	function printer($id = null, $report = null, $title = null, $out = null, $print = null, $list = null, $preview = null, $create = null, $lang = null)
	{
		$this->userauth->roleCheck(($this->auth .'.index'));

		$id 	= isset($id)?$id:$this->input->get_post('id');
		$report = urldecode(isset($report)?$report:$this->input->get_post('report'));
		$title 	= urldecode(isset($title)?$title:$this->input->get_post('title'));
		$out 	= isset($out)?$out:format_tobool($this->input->get_post('out'));
		$print 	= isset($print)?$print:$this->input->get_post('print');
		$list 	= isset($list)?$list:format_tobool($this->input->get_post('list'));
		$preview= isset($preview)?$preview:format_tobool($this->input->get_post('preview'));
		$lang 	= isset($lang)?$lang:$this->input->get_post('lang');
		
		if (empty($title)) $title = $this->title . ' - ' . $id;

		if (!isset($lang) || trim($lang) == '' )
		{
			$lang = $this->config->item('reports.language');
			$lang = preg_split('/;/', $lang);
			$lang = $lang[0];
		}

		if ($out === 0) $out = TRUE;
		if ($list === 0) $list = FALSE;
		if ($preview == 0) $preview = FALSE;

		$print 	= (bool)format_tobool($print);
		#$preview= (bool)format_tobool($print);

		// Se pide el listado?
		if ($list)
		{
			$reports = $this->_get_reports();
			if (!is_array($reports))
			{
				$this->out->success();
			}
			$this->out->data($reports);
		}
		if ($id)
		{
			//Hay un report por defecto?
			if (!$report)
			{
				$report = $this->_get_report_default();
			}
			$data = $this->reg->load($id, TRUE);
			$css = null;
			$this->_pre_printer($id, $data, $css);
			$text = $this->show_report($title, $data, $report, $css, $out, $lang, $preview, $create);
			if ($print===TRUE) echo $text;
			return $text;
		}
		else
		{
			$this->out->message(FALSE, $this->lang->line('mensaje_faltan_datos'));
		}

		$this->out->message($this->lang->line('Imprimir'), $this->lang->line('no-soportado'));
	}

	/**
	 * Listado de informes del controlador
	 * @return JSON
	 */
	function report_list()
	{
		$this->load->library('Reports');
		$data = $this->reports->get_list($this->auth);
		$this->out->data($data);
	}

	/**
	 * Elimina un registro
	 *
	 * @param int $id Identificador
	 */
	function del($id = null)
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.del'));
		}

		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$this->_delete($id, $this->reg);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Elimina un registro
	 *
	 * @param int $id Identificador
	 * @param MY_Model $reg Modelo de datos
	 * @return MSG
	 */
	protected function _delete($id, $reg = null)
	{
		if (!isset($reg)) $reg = $this->reg;

		$res = TRUE;
		if (is_string($id))
		{
			$ids = preg_split('/\;/', $id);
			$ids = array_unique($ids);
		}
		else
		{
			$ids[] = $id;
		}
		$this->db->trans_begin();
		$count = 0;
		foreach($ids as $i)
		{
			if (is_numeric($i))
			{
				if (!$reg->delete($i))
				{
					$res = $reg->error_message();
					break;
				}
				$count++;
			}
		}
		if ($res === TRUE)
		{
			$this->db->trans_commit();
		}
		else
		{
			$this->db->trans_rollback();
		}
		if ($res === TRUE)
		{
			if (count($ids) == 1)
			{
				$this->out->success(sprintf($this->lang->line('registro_eliminado'), $ids[0]));
			}
			else
			{
				$this->out->success(sprintf($this->lang->line('registro_eliminado_varios'), $count));
			}
		}
		$this->out->error($res);
	}
	/**
	 * Crea una ventana EXTJS con un IFRAME
	 * @param $url URL del contenido
	 * @param $title Título
	 * @return HTML
	 */
	protected function _iframe($url, $title)
	{
		//echo '<iframe style="height: 100%; width: 100%; border: 0" frameborder="0" src="' . $url . '"></iframe>';
		$id 	= isset($id)?$id:$this->input->get_post('id');
		$icon 	= isset($icon)?$icon:$this->input->get_post('icon');

		$this->load->helper('extjs');
		$this->load->helper('asset');

		$datos['title'] = $title;
		$datos['id'] = $id;
		$datos['icon'] = $icon;
		$datos['url'] = $url;
		$form = $this->load->view('main/iframe.js', $datos ,true);

		if (isset($id) && ($id != ''))
		{
			$this->out->window($form, $title, $icon, $id);
		}
		else
		{
			unset($datos);
			$datos['form'] = $form;
			$datos['script'] = $this->load->view('main/main_app.js', $datos, true);
			$this->load->view('main/main', $datos);
		}
	}

	/**
	 * Devuelve al cliente un mensaje JS
	 * @param string $title Título
	 * @param string $message Mensaje
	 * @param string $headers Enviar direcatemente
	 */
	protected function _show_message($title, $message, $headers = TRUE)
	{
		$this->load->helper('extjs');
		$this->load->helper('asset');
		$datos['message'] = $message;
		$datos['title'] = $title;
		$js = $this->load->view('sys/message.js', $datos ,true);
		// Respuesta
		return $this->out->js($js, $headers);
	}

	/**
	 * Muestra un formulario teniendo en cuenta si es tab o aplicación independientes
	 * y la seguridad.
	 * Origen un string.
	 *
	 * @param string $auth Seguridad
	 * @param string $view Vista
	 * @param string $title Título
	 * @param array $js_files Ficheros JS a incluir
	 * @param array $css_files Ficheros CSS incluir
	 * @return HTML
	 */
	protected function _show_form_window($auth, $form, $title, $js_files = null, $css_files = null)
	{

		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.'.$auth), '', TRUE);
		}
		$id 	= isset($id)?$id:$this->input->get_post('id');
		$icon 	= isset($icon)?$icon:$this->input->get_post('icon');

		//Si no hay ID se inventa uno

		$this->load->helper('extjs');
		$this->load->helper('asset');

		if (isset($id) && ($id != ''))
		{
			$this->out->window($form, $title, $icon, $id);
		}
		else
		{
			$datos['title'] = $title;
			$datos['form'] = $form;
			$datos['script'] = $this->load->view('main/main_app.js', $datos, true);
			if (isset($js_files)) $datos['js_include'] = $js_files;
			if (isset($css_files)) $datos['css_include'] = $css_files;
			$this->load->view('main/main', $datos);
		}
	}

	/**
	 * Carga una vista con un código JS y lo devuelve al cliente
	 * @param string $auth Permiso
	 * @param string $view Vista
	 * @param array $data Datos para la vista
	 * @param bool $headers Enviar directamente
	 */
	protected function _show_js($auth, $view, $data = null, $headers = TRUE)
	{

		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.'.$auth), '', TRUE);
		}
		$this->load->helper('extjs');
		$this->load->helper('asset');
		$form = $this->load->view($view, $data, TRUE);
		return $this->out->js($form, $headers);
	}

	/**
	 * Muestra un formulario teniendo en cuenta si es tab o aplicación independientes
	 * y la seguridad.
	 * Origen una vista.
	 *
	 * @param string $auth Seguridad
	 * @param string $view Vista
	 * @param string $title Título
	 * @param array $js_files Ficheros JS a incluir
	 * @param array $css_files Ficheros CSSS incluir
	 * @return HTML
	 */
	protected function _show_form($auth, $view, $title, $js_files = null, $css_files = null, $open_id = null, $data = null, $icon = null)
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck($this->auth .'.'.$auth, '', TRUE);
		}

		$id 	= isset($id)?$id:$this->input->get_post('id');
		$icon 	= isset($icon)?$icon:$this->input->get_post('icon');
		$open_id = isset($open_id)?$open_id:$this->input->get_post('open_id');

		//Si no hay ID se inventa uno

		$this->load->helper('extjs');
		$this->load->helper('asset');

		$datos = $data;
		$datos['title'] = $title;
		$datos['id'] = isset($id) && ($id != '')?$id:'f_'.time();
		$datos['icon'] = $icon;
		$datos['open_id'] = $open_id;
		if (isset($js_files)) $datos['js_include'] = $js_files;
		if (isset($css_files)) $datos['css_include'] = $css_files;

		#var_dump($datos);
		$form = $this->load->view($view, $datos, TRUE);

		$this->_show_form_window($auth, $form, $title, $js_files, $css_files);
	}

	/**
	 * Realiza una búsqueda por palabra clave
	 *
	 * @param string $query Palabra de búsqueda
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $order Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param string $where Campos WHERE
	 */
	function search($query = null, $start = null, $limit = null, $sort = null, $dir = null, $where = null)
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
		#print '<pre>'; var_dump($where); print '</pre>';
		$where = $this->reg->parse_where($where);
		#print '<pre>'; var_dump($where); print '</pre>';
		#print '<pre>'; var_dump($query); print '</pre>';
		$query = trim($query);
		$data = $this->reg->search($query, $start, $limit, $sort, $dir, $where);
		$this->out->data($data, $this->reg->get_count());
	}

	/**
	 * Ejecuta un report con los datos indicados
	 * @param string $title Título
	 * @param array $data Datos del report
	 * @param string $report Report a ejecutar
	 * @param string $css Estilos CSS a aplicar
	 * @param bool $out TRUE: Devuelve un HTML al cliente, FALSE: devuelve el código HTML de la función
	 * @return string
	 */
	function show_report($title, $data, $report, $css = null, $out = TRUE, $lang = null, $preview = TRUE, $create = FALSE)
	{
		// Idioma del report
		if (!isset($lang) || $lang == '' )
		{
			$lang = $this->config->item('reports.language');
			$lang = preg_split('/;/', $lang);
			$lang = $lang[0];
		}
		$this->load->language("report.{$lang}");

		$this->load->library('Reports');
		$view = $this->reports->get($this->auth, $report);
		#var_dump($this->auth, $view);die();
		// Orden?
		if (is_array($view))
		{
			if (isset($view['order']) && $view['order'] != '')
			{
				$sort = preg_split('/\./', $view['order']);
				if (count($sort) > 1)
				{
					sksort($data[$sort[0]], $sort[1]);
				}
				else
				{
					sksort($data, $data[$view['order']]);
				}
			}

			// Parámetros del report?
			if (isset($view['params']))
			{
				$data = array_merge($data, $view['params']);
			}
			$message = $this->load->view($view['file'], $data, TRUE);
		}
		else
		{
			$message = $this->load->view($view, $data, TRUE);
		}
		// Vista
		if ($out == TRUE)
		{
			if ($preview)
			{
				$this->out->html_file($message, $title, 'iconoReportTab', $css);
			}
			else
			{
				$this->load->library('PdfLib');
				$this->load->library('HtmlFile');
				$filename = $this->htmlfile->create($message, $title, $css);
				$pdf = $this->pdflib->create($this->htmlfile->pathfile($filename), null, null, null, FALSE, FALSE);
				$url = $this->htmlfile->url($pdf);
				$this->out->url($url, $title, 'iconoReportTab');
			}
		}
		else
		{
			if ($create == TRUE)
			{

				$this->load->library('HtmlFile');
				$filename = $this->htmlfile->create($message, $title, $css);
				return $filename;
			}

			return $message;
		}
	}

	/**
	 * Comprueba si el resultado de algo es TRUE y sino devuelve el error codificado
	 * @param bool $res TRUE: no error, FALSE: error
	 * @return JSON
	 */
	protected function _checkdberror($res)
	{
		if ($res !== TRUE)
		{
			$res = $this->reg->error_message();
			$ajax_res = array(
				'success' 	=> false,
				'message'	=> $res
			);
			echo $this->out->send($ajax_res);
			exit;
		}
	}

	/**
	 * Hook al que se llama cuando se ha realizado un envio positivo
	 * @param int $id Id del documento
	 * @param string $message Mensaje de resultado
	 */
	protected function _post_send($id, $message)
	{
	}

	/**
	 * Envía el documento. Si se indica un report y/o idioma no utiliza SINLI aunque así se indique en el documento.
	 * @param int $id Id del documento
	 * @param bool $email Usar email
	 * @param bool $fax Usar Fax
	 * @param bool $sinli Usar SINLI
	 * @return MSG
	 */
	function send($id = null, $email = null, $fax = null, $sinli = null, $lang = null, $report = null)
	{
		$this->userauth->roleCheck($this->auth .'.index');

		$id 	= isset($id)?$id:$this->input->get_post('id');
		$email 	= isset($email)?$email:format_tobool($this->input->get_post('email'));
		$fax	= isset($fax)?$fax:format_tobool($this->input->get_post('fax'));
		$sinli	= isset($sinli)?$sinli:format_tobool($this->input->get_post('sinli'));
		$report = urldecode(isset($report)?$report:$this->input->get_post('report'));
		$lang 	= isset($lang)?$lang:$this->input->get_post('lang');

		if ($email === 0) $email = TRUE;
		if ($fax === 0) $fax = TRUE;
		if ($sinli === 0) $sinli = TRUE;
		if ($id)
		{
			$this->load->library('Sender');
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$count = 0;
			$finalmsg = array();
			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					$profile = $this->_get_profile_sender($id);
					if (!empty($lang)) 
					{
						$profile['report_lang'] = $lang;
						unset($profile['sinli']);
					}
					if (!empty($report)) 
					{
						$profile['report_email'] = $report;
						unset($profile['sinli']);
					}
					$profile['controller'] = $this;

					$res = $this->sender->send($id, $profile, $email, $fax, $sinli);
					if ($res['success'])
					{
						$message = sprintf($this->lang->line('sender-documento-enviado'), 
							$id, $res['media'], $res['dest'], 
							$this->lang->line($profile['report_email']) . ' (' . $profile['report_lang'] . ')');
						$this->_add_nota(null, $id, NOTA_INTERNA, $message);
						$this->_post_send($id, $message);
					}
					else
					{
						$message = sprintf($this->lang->line('sender-documento-enviado-error'), $id, $res['media'], $res['message']);
					}
					$finalmsg[] = $message;
					++$count;
				}
			}
			$this->out->dialog(TRUE, implode('<br/>', $finalmsg));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Notas del documento
	 *
	 * @param int $id Id del registro
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $query Palabra clave de búsqueda
	 * @return array
	 */
	protected function get_notas($id = null, $start = null, $limit = null, $sort = null, $dir = null, $query = null)
	{
		/*$this->load->model('generico/m_nota');
		$tabla = $this->db->escape_str($this->reg->get_tablename());
		$where = "nIdRegistro={$id} AND cTabla ='{$tabla}'";*/
		return $this->reg->get_notas($id, $start, $limit, $sort, $dir, $query);
	} 

	/**
	 * Notas del documento
	 *
	 * @param int $id Id del registro
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $query Palabra clave de búsqueda
	 * @return JSON_DATA
	 */
	function notas($id = null, $start = null, $limit = null, $sort = null, $dir = null, $query = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id 	= isset($id)?$id:$this->input->get_post('id');
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		$query 	= isset($query)?$query:$this->input->get_post('query');
		if (trim($query) == '') $query = null;

		if ($id)
		{
			$data = $this->get_notas($id, $start, $limit, $sort, $dir, $query);
			$this->out->data($data, $this->reg->get_count());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Lee los datos de una nota
	 * @param int $id Id de la nota
	 * @return DATA
	 */
	function get_nota($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id 	= isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$this->load->model('generico/m_nota');
			$data = $this->m_nota->load($id);
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Añade una nota
	 * @param int $id Id de la nota (si se actualiza)
	 * @param int $id_r Id del registro
	 * @param int $tipo Id del tipo de nota
	 * @param string $texto Texto de la nota
	 * @return MSG
	 */
	function add_nota($id = null, $id_r = null, $tipo = null, $texto = null )
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id 	= isset($id)?$id:$this->input->get_post('id');
		$id_r 	= isset($id_r)?$id_r:$this->input->get_post('id_r');
		$tipo 	= isset($tipo)?$tipo:urldecode($this->input->get_post('tipo'));
		$texto 	= isset($texto)?$texto:urldecode($this->input->get_post('Texto'));
		if (is_numeric($id_r) && $texto)
		{
			$res = $this->_add_nota($id, $id_r, $tipo, $texto);
			if ($res['success'])
			{
				$ajax_res = array(
					'success' 	=> TRUE,
					'message'	=> $res['message'],
					'id'		=> $res['id']
				);
				$this->out->send($ajax_res);
			}
			else
			{
				$this->out->error($res['message']);
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Añade una nota
	 * @param int $id Id de la nota (si se actualiza)
	 * @param int $id_r Id del registro
	 * @param int $tipo Id del tipo de nota
	 * @param string $texto Texto de la nota
	 * @param string $tablename Nombre de la tabla
	 * @return MSG
	 */
	protected function _add_nota($id, $id_r, $tipo, $texto, $tablename = null)
	{
		$this->load->model('generico/m_nota');
		$data = array (
				'tObservacion'			=> (string) $texto,
				'nIdTipoObservacion' 	=> $tipo,
				'cTabla'				=> (isset($tablename)?$tablename:$this->reg->get_tablename()),
				'nIdRegistro'			=> (int)$id_r
		);
		$upd = is_numeric($id);
		if ($upd)
		{
			//update
			if (!$this->m_nota->update($id, $data))
			{
				return array('success' => FALSE, 'message' => $this->m_nota->error_message());
			}
		}
		else
		{
			if (($id = $this->m_nota->insert($data)) <= 0)
			{
				return array('success' => FALSE, 'message' => $this->m_nota->error_message());
			}
		}
		return array(
			'success' 	=> TRUE, 
			'message' 	=> sprintf($this->lang->line(($upd?'registro_actualizado':'registro_generado')), $id), 
			'id' 		=> $id
		);			
	}

	/**
	 * Elimina una nota
	 * @param int $id Id de la nota
	 * @return MSG
	 */
	function del_nota($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$this->load->model('generico/m_nota');
			$this->_delete($id, $this->m_nota);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Lee los registros eliminados
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param string $query Palabra clave de búsqueda
	 * @return DATA
	 */
	function get_deleted($start = null, $limit = null, $sort = null, $dir = null, $query = null)
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.get_list'));
		}
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		$query 	= isset($query)?$query:$this->input->get_post('query');

		$data = $this->reg->get_deleted($start, $limit, $sort, $dir, $query);
		#var_dump($this->db->queries); die();
		$this->out->data($data);
	}
}

/* End of file MY_Controller.php */
/* Location: ./system/libraries/MY_Controller.php */
