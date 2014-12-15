<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Articulo extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('menu');
		$this->load->model('reserva_habitacion_model');
		$this->load->library('grocery_CRUD');
		//$this->load->library('image_CRUD');
		
	}


	public function _example_output($output = null)
	{
		if($this->session->userdata('logged_in')){
			$reservas=buscarReservas();
			$mensajes=buscarMensajes();
			
			$db=array_merge($reservas, $mensajes);
						
			$this->load->view('backend/head.php',$output);
			$this->load->view('backend/menu.php', $db);	
			$this->load->view('backend/modal.php');
			$this->load->view('backend/articulos.php');
			$this->load->view('backend/footer.php');
		}else{
			redirect('/admin/home/logout/','refresh');
		}
	}
	

	public function index()
	{
		$this->_example_output2((object)array('output' => '' , 'js_files' => array() , 'css_files' => array()));
	
	}


/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de artículos
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function articulos_abm(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->where('articulos.delete', 0);
			$crud->set_table('articulos');
			
			$crud->columns(	'id_articulo',
							'titulo',
							'id_hotel',
							'id_categoria',
							'orden',
							'id_estado_articulo');
			
			$crud->display_as('id_articulo','ID')
				 ->display_as('articulo','Artículo')
				 ->display_as('titulo','Título')
				 ->display_as('subtitulo','Sub-título')
				 ->display_as('id_hotel','Hotel')
				 ->display_as('id_categoria','Categoría')
				 ->display_as('id_autor','Autor')
				 ->display_as('id_estado_articulo','Estado')
				 ->display_as('id_idioma','Idioma')
				 ->display_as('archivo_url','Archivo')
				 ->display_as('pagina_principal','Página Principal')
				 ->display_as('fecha_publicacion','Fecha publicación')
				 ->display_as('fecha_despublicacion','Fecha despublicación')
				 ->display_as('id_tipo','Tipo')
				 ->display_as('id_tarifa_temporal','Tarifa temporal');
			
			$crud->set_subject('artículo');
			
			$crud->field_type('pagina_principal', 'true_false');
			
			$crud->fields(	'titulo',
							//'subtitulo',
							'articulo',
							'fecha_publicacion', 
							'fecha_despublicacion',
							'pagina_principal', 
							'id_hotel',
							'id_autor',
							'archivo_url',
							'orden',
							'id_categoria',
							'id_estado_articulo',
							'id_idioma',
							'id_tipo', 
							'id_tarifa_temporal');
			
			$crud->set_relation('id_hotel','hoteles','hotel', 'delete = 0');
			$crud->set_relation('id_categoria','categorias','categoria', 'delete = 0');
			$crud->set_relation('id_autor','usuarios','usuario', 'delete = 0');
			$crud->set_relation('id_estado_articulo','estados_articulo','estado_articulo');
			$crud->set_relation('id_tarifa_temporal','tarifas_temporales','tarifa_temporal');
			$crud->set_relation('id_idioma','idiomas','idioma');
			$crud->set_relation('id_tipo','tipos_articulo','tipo_articulo');
					
			$crud->required_fields('titulo', 'articulo','id_hotel','fecha_publicacion', 'id_categoria', 'id_estado_articulo', 'id_tipo');
			
			$crud->set_field_upload('archivo_url','assets/uploads/articulos');
			
			chmod("assets/uploads/articulos", 755);
			
			$_COOKIE['tabla']='articulos';
			$_COOKIE['id']='id_articulo';	
			
			$crud->callback_after_insert(array($this, 'insert_log'));
			$crud->callback_after_update(array($this, 'update_log'));
			$crud->callback_delete(array($this,'delete_log'));	
			
			$output = $crud->render();

			$this->_example_output($output);
	}
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de categorías
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function categorias_abm(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->where('categorias.delete', 0);
			$crud->set_table('categorias');
			
			$crud->columns(	'id_categoria',
							'categoria');
			
			$crud->display_as('id_categoria','ID')
				 ->display_as('categoria','Categoría');
			
			$crud->set_subject('categoría');
			
			$crud->fields('categoria');
					
			$crud->required_fields('categoria');
			
			$_COOKIE['tabla']='categorias';
			$_COOKIE['id']='id_categoria';	
			
			$crud->callback_after_insert(array($this, 'insert_log'));
			$crud->callback_after_update(array($this, 'update_log'));
			$crud->callback_delete(array($this,'delete_log'));	
			
			$output = $crud->render();

			$this->_example_output($output);
	}
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de Estados Articulo
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function estados_articulo(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->set_table('estados_articulo');
			
			$crud->columns(	'id_estado_articulo',
							'estado_articulo');
			
			$crud->display_as('id_estado_articulo','ID')
				 ->display_as('estado_articulo','Estado');
			
			$crud->set_subject('estado');
			$crud->unset_delete();
			$crud->unset_export();
			$crud->unset_add();
			$crud->unset_read();				
						
			$crud->required_fields('estado_articulo');
			
			$_COOKIE['tabla']='estados_articulo';
			$_COOKIE['id']='id_estado_articulo';	
						
			$crud->callback_after_update(array($this, 'update_log'));
				
			
			$output = $crud->render();

			$this->_example_output($output);
	}
	

 
 /**********************************************************************************
 **********************************************************************************
 * 
 * 				Configuración de los articulos
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function config_articulos(){
			$crud = new grocery_CRUD();

			
			$crud->set_table('config_articulos');
			
			$crud->set_subject('config artículos');
			
			$crud->field_type('usar_limite', 'true_false');
			
			$crud->unset_back_to_list();
			
			$_COOKIE['tabla']='config_correo';
			$_COOKIE['id']='id_config_correo';	
						
			$crud->callback_after_insert(array($this, 'insert_log'));	
			$crud->callback_after_update(array($this, 'update_log'));
			$crud->callback_delete(array($this,'delete_log'));
			
			
			$output = $crud->render();

			$this->_example_output($output);
	}
	
	
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Funciones logs
 * 
 * ********************************************************************************
 **********************************************************************************/

	
	function insert_control_fechas($datos, $id){
		if($datos['entrada']>$datos['salida']){
			return false;
		}else{
			return true;	
		} 
	}
	

	function insert_log($datos, $id){
		$session_data = $this->session->userdata('logged_in');
		
	    $registro = array(
	        "tabla"		=> $_COOKIE['tabla'],
	        "id_tabla"	=> $id,
	        "id_accion"	=> 1,
	        "fecha"		=> date('Y-m-d H:i:s'),
	        "id_usuario"=> $session_data['id_usuario']
	    );
	 
	    $this->db->insert('logs_articulos',$registro);
	 
	    return true;
	}
	
	
	function update_log($datos, $id){
		$session_data = $this->session->userdata('logged_in');
		
    	$registro = array(
	        "tabla"		=> $_COOKIE['tabla'],
	        "id_tabla"	=> $id,
	        "id_accion"	=> 2,
	        "fecha"		=> date('Y-m-d H:i:s'),
	        "id_usuario"=> $session_data['id_usuario']
	    );
 
    	$this->db->insert('logs_articulos',$registro);
 
    	return true;
	}
	
	
	public function delete_log($id){
    	$session_data = $this->session->userdata('logged_in');
		
		$registro = array(
	        "tabla"		=> $_COOKIE['tabla'],
	        "id_tabla"	=> $id,
	        "id_accion"	=> 3,
	        "fecha"		=> date('Y-m-d H:i:s'),
	        "id_usuario"=> $session_data['id_usuario']
	    );
 
    	$this->db->insert('logs_articulos',$registro);
			
    	return $this->db->update($_COOKIE['tabla'], array('delete' => 1), array($_COOKIE['id'] => $id));
	}


}