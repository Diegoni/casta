<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Log extends CI_Controller {

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
			$this->load->view('backend/logs.php');
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
 * 				Logs de artículos
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function logs_articulos_abm(){
			$crud = new grocery_CRUD();
			
			$crud->set_table('logs_articulos');
			
			$crud->columns(	'id_log_articulo',
							'tabla',
							'id_tabla',
							'id_accion',
							'fecha', 
							'id_usuario');
			
			$crud->display_as('id_log_articulo','ID')
				 ->display_as('tabla','Tabla')
				 ->display_as('id_tabla','Registro')
				 ->display_as('id_accion','Acción')
				 ->display_as('fecha','Fecha')
				 ->display_as('id_usuario','Usuario');
			
			$crud->set_subject('artículo');
			
			$crud->unset_add();
            $crud->unset_edit();
			$crud->unset_delete();
			$crud->unset_read();
			
			$crud->set_relation('id_accion','accion','accion');
			$crud->set_relation('id_usuario','usuarios','usuario');
			
			$crud->add_action('View', '', '','icon-chevron-right', array($this,'view_articulos'));
						
			$output = $crud->render();

			$this->_example_output($output);
	}
	
	function view_articulos($id){
		$query = $this->db->query("SELECT * FROM logs_articulos WHERE id_log_articulo='$id' ");
		foreach ($query->result_array() as $row){
   			$id		= $row['id_tabla'];
			$tabla	= $row['tabla'];
	  
		}
			
		return site_url('/'.$this->uri->segment(1).'/admin/articulo/'.$tabla.'_abm/read').'/'.$id;	
	}
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Logs de habitaciones
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function logs_habitaciones_abm(){
			$crud = new grocery_CRUD();
			
			$crud->set_table('logs_habitaciones');
			
			$crud->columns(	'id_log_habitacion',
							'tabla',
							'id_accion',
							'fecha', 
							'id_usuario');
			
			$crud->display_as('id_log_habitacion','ID')
				 ->display_as('tabla','Tabla')
				 ->display_as('id_accion','Acción')
				 ->display_as('fecha','Fecha')
				 ->display_as('id_usuario','Usuario');
			
			$crud->set_subject('habitación');
			
			$crud->unset_add();
            $crud->unset_edit();
			$crud->unset_delete();
			$crud->unset_read();
			
			$crud->set_relation('id_accion','accion','accion');
			$crud->set_relation('id_usuario','usuarios','usuario');

			$crud->add_action('View', '', '','icon-chevron-right', array($this,'view_habitaciones'));
			
			$output = $crud->render();

			$this->_example_output($output);
	}
	
	function view_habitaciones($id){
		$query = $this->db->query("SELECT * FROM logs_habitaciones WHERE id_log_habitacion='$id' ");
		foreach ($query->result_array() as $row){
   			$id		= $row['id_tabla'];
			$tabla	= $row['tabla'];
	  
		}
			
		return site_url('/'.$this->uri->segment(1).'/admin/habitacion/'.$tabla.'_abm/read').'/'.$id;	
	}

/**********************************************************************************
 **********************************************************************************
 * 
 * 				Logs de hoteles
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function logs_hoteles_abm(){
			$crud = new grocery_CRUD();
			
			$crud->set_table('logs_hoteles');
			
			$crud->columns(	'id_log_hotel',
							'tabla',
							'id_accion',
							'fecha', 
							'id_usuario');
			
			$crud->display_as('id_log_hotel','ID')
				 ->display_as('tabla','Tabla')
				 ->display_as('id_accion','Acción')
				 ->display_as('fecha','Fecha')
				 ->display_as('id_usuario','Usuario');
			
			$crud->set_subject('hotel');
			
			$crud->unset_add();
            $crud->unset_edit();
			$crud->unset_delete();
			$crud->unset_read();
			
			$crud->set_relation('id_accion','accion','accion');
			$crud->set_relation('id_usuario','usuarios','usuario');
			
			$crud->add_action('View', '', '','icon-chevron-right', array($this,'view_hoteles'));
						
			$output = $crud->render();

			$this->_example_output($output);
	}
	
	function view_hoteles($id){
		$query = $this->db->query("SELECT * FROM logs_hoteles WHERE id_log_hotel='$id' ");
		foreach ($query->result_array() as $row){
   			$id		= $row['id_tabla'];
			$tabla	= $row['tabla'];
	  
		}
			
		return site_url('/'.$this->uri->segment(1).'/admin/hotel/'.$tabla.'_abm/read').'/'.$id;	
	}
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Logs de huespedes
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function logs_huespedes_abm(){
			$crud = new grocery_CRUD();
			
			$crud->set_table('logs_huespedes');
			
			$crud->columns(	'id_log_huesped',
							'tabla',
							'id_accion',
							'fecha', 
							'id_usuario');
			
			$crud->display_as('id_log_huesped','ID')
				 ->display_as('tabla','Tabla')
				 ->display_as('id_accion','Acción')
				 ->display_as('fecha','Fecha')
				 ->display_as('id_usuario','Usuario');
			
			$crud->set_subject('hotel');
			
			$crud->unset_add();
            $crud->unset_edit();
			$crud->unset_delete();
			$crud->unset_read();
			
			$crud->set_relation('id_accion','accion','accion');
			$crud->set_relation('id_usuario','usuarios','usuario');
			
			$crud->add_action('View', '', '','icon-chevron-right', array($this,'view_huespedes'));
						
			$output = $crud->render();

			$this->_example_output($output);
	}
	
	function view_huespedes($id){
		$query = $this->db->query("SELECT * FROM logs_huespedes WHERE id_log_huesped='$id' ");
		foreach ($query->result_array() as $row){
   			$id		= $row['id_tabla'];
			$tabla	= $row['tabla'];
	  
		}
			
		return site_url('/'.$this->uri->segment(1).'/admin/huesped/'.$tabla.'_abm/edit').'/'.$id;	
	}
	
	
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Logs de mensajes
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function logs_mensajes_abm(){
			$crud = new grocery_CRUD();
			
			$crud->set_table('logs_mensajes');
			
			$crud->columns(	'id_log_mensaje',
							'tabla',
							'id_accion',
							'fecha', 
							'id_usuario');
			
			$crud->display_as('id_log_mensaje','ID')
				 ->display_as('tabla','Tabla')
				 ->display_as('id_accion','Acción')
				 ->display_as('fecha','Fecha')
				 ->display_as('id_usuario','Usuario');
			
			$crud->set_subject('mensaje');
			
			$crud->unset_add();
            $crud->unset_edit();
			$crud->unset_delete();
			$crud->unset_read();
			
			$crud->set_relation('id_accion','accion','accion');
			$crud->set_relation('id_usuario','usuarios','usuario');
			
			$crud->add_action('View', '', '','icon-chevron-right', array($this,'view_mensajes'));
						
			$output = $crud->render();

			$this->_example_output($output);
	}
	
	function view_mensajes($id){
		$query = $this->db->query("SELECT * FROM logs_mensajes WHERE id_log_mensaje='$id' ");
		foreach ($query->result_array() as $row){
   			$id		= $row['id_tabla'];
			$tabla	= $row['tabla'];
	  
		}
			
		return site_url('/'.$this->uri->segment(1).'/admin/mensaje/'.$tabla.'_abm/read').'/'.$id;	
	}
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Logs de otros
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function logs_otros_abm(){
			$crud = new grocery_CRUD();
			
			$crud->set_table('logs_otros');
			
			$crud->columns(	'id_log_otro',
							'tabla',
							'id_accion',
							'fecha', 
							'id_usuario');
			
			$crud->display_as('id_log_otro','ID')
				 ->display_as('tabla','Tabla')
				 ->display_as('id_accion','Acción')
				 ->display_as('fecha','Fecha')
				 ->display_as('id_usuario','Usuario');
			
			$crud->set_subject('otro');
			
			$crud->unset_add();
            $crud->unset_edit();
			$crud->unset_delete();
			$crud->unset_read();
			
			$crud->set_relation('id_accion','accion','accion');
			$crud->set_relation('id_usuario','usuarios','usuario');
			
			$crud->add_action('View', '', '','icon-chevron-right', array($this,'view_otros'));
						
			$output = $crud->render();

			$this->_example_output($output);
	}
	
	function view_otros($id){
		$query = $this->db->query("SELECT * FROM logs_otros WHERE id_log_otro='$id' ");
		foreach ($query->result_array() as $row){
   			$id		= $row['id_tabla'];
			$tabla	= $row['tabla'];
	  
		}
			
		return site_url('/'.$this->uri->segment(1).'/admin/otro/'.$tabla.'_abm/edit').'/'.$id;	
	}
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Logs de reservas
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function logs_reservas_abm(){
			$crud = new grocery_CRUD();
			
			$crud->set_table('logs_reservas');
			
			$crud->columns(	'id_log_reserva',
							'tabla',
							'id_accion',
							'fecha', 
							'id_usuario');
			
			$crud->display_as('id_log_reserva','ID')
				 ->display_as('tabla','Tabla')
				 ->display_as('id_accion','Acción')
				 ->display_as('fecha','Fecha')
				 ->display_as('id_usuario','Usuario');
			
			$crud->set_subject('reserva');
			
			$crud->unset_add();
            $crud->unset_edit();
			$crud->unset_delete();
			$crud->unset_read();
			
			$crud->set_relation('id_accion','accion','accion');
			$crud->set_relation('id_usuario','usuarios','usuario');
			
			$crud->add_action('View', '', '','icon-chevron-right', array($this,'view_reservas'));
						
			$output = $crud->render();

			$this->_example_output($output);
	}
	
	function view_reservas($id){
		$query = $this->db->query("SELECT * FROM logs_reservas WHERE id_log_reserva='$id' ");
		foreach ($query->result_array() as $row){
   			$id		= $row['id_tabla'];
			$tabla	= $row['tabla'];
	  
		}
			
		return site_url('/'.$this->uri->segment(1).'/admin/reserva/'.$tabla.'_abm/read').'/'.$id;	
	}
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Logs de usuarios
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function logs_usuarios_abm(){
			$crud = new grocery_CRUD();
			
			$crud->set_table('logs_usuarios');
			
			$crud->columns(	'id_log_usuario',
							'tabla',
							'id_accion',
							'fecha', 
							'id_usuario');
			
			$crud->display_as('id_log_usuario','ID')
				 ->display_as('tabla','Tabla')
				 ->display_as('id_accion','Acción')
				 ->display_as('fecha','Fecha')
				 ->display_as('id_usuario','Usuario');
			
			$crud->set_subject('usuario');
			
			$crud->unset_add();
            $crud->unset_edit();
			$crud->unset_delete();
			$crud->unset_read();
			
			$crud->set_relation('id_accion','accion','accion');
			$crud->set_relation('id_usuario','usuarios','usuario');
			
			$crud->add_action('View', '', '','icon-chevron-right', array($this,'view_usuarios'));
						
			$output = $crud->render();

			$this->_example_output($output);
	}
	
	function view_usuarios($id){
		$query = $this->db->query("SELECT * FROM logs_usuarios WHERE id_log_usuario='$id' ");
		foreach ($query->result_array() as $row){
   			$id		= $row['id_tabla'];
			$tabla	= $row['tabla'];
	  
		}
			
		return site_url('/'.$this->uri->segment(1).'/admin/usuario/'.$tabla.'_abm/read').'/'.$id;	
	}

}