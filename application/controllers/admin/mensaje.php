<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mensaje extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('menu');
		//$this->load->helper('logs');
		$this->load->model('reserva_habitacion_model');
		$this->load->model('mensajes_model');
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
			$this->load->view('backend/mensajes.php');
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
 * 				Alta, baja y modificación de mensajes
 * 
 **********************************************************************************
 **********************************************************************************/


	public function mensajes_abm(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->where('mensajes.delete', 0);
			$crud->set_table('mensajes');
			
			$crud->columns(	'id_mensaje',
							'mensaje',
							'emisor',
							'fecha_envio',
							'id_estado_mensaje', 
							'id_hotel');
			
			$crud->display_as('id_mensaje','ID')
				 ->display_as('titulo','Título')
				 ->display_as('mensaje','Mensaje')
				 ->display_as('emisor','Email')
				 ->display_as('fecha_envio','Fecha')
				 ->display_as('id_tipo_mensaje','Tipo')
				 ->display_as('id_estado_mensaje','Mensaje')
				 ->display_as('id_hotel','Hotel')
				 ->display_as('comentario','Comentario');
				 
			$crud->fields('titulo', 'mensaje', 'emisor', 'nombre', 'apellido', 'telefono', 'fecha_envio', 'id_tipo_mensaje', 'id_estado_mensaje', 'id_hotel', 'comentario');
			
			$crud->set_subject('mensaje');
			
			$crud->set_relation('id_tipo_mensaje','tipos_mensaje','tipo_mensaje');
			$crud->set_relation('id_estado_mensaje','estados_mensaje','estado_mensaje');
			$crud->set_relation('id_hotel','hoteles','hotel');
			
			$crud->field_type('titulo', 'readonly');
			$crud->field_type('mensaje', 'readonly');
			$crud->field_type('emisor', 'readonly');
			$crud->field_type('nombre', 'readonly');
			$crud->field_type('apellido', 'readonly');
			$crud->field_type('telefono', 'readonly');
			$crud->field_type('fecha_envio', 'readonly');
			$crud->field_type('id_tipo_mensaje', 'readonly');
			$crud->field_type('id_hotel', 'readonly');
					
			$crud->unset_add();
			//$crud->required_fields('titulo', 'mensaje');
			
			$_COOKIE['tabla']='mensajes';
			$_COOKIE['id']='id_mensaje';	
			
			$crud->callback_after_update(array($this, 'update_log'));
			$crud->callback_delete(array($this,'delete_log'));	
			
			$output = $crud->render();

			$this->_example_output($output);
	}



/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de tipos de mensaje
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function tipos_mensaje(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->set_table('tipos_mensaje');
			
			$crud->columns(	'id_tipo_mensaje',
							'tipo_mensaje');
			
			$crud->display_as('id_tipo_mensaje','ID')
				 ->display_as('tipo_mensaje','Tipo mensaje');
			
			$crud->set_subject('tipo mensaje');
			$crud->unset_delete();
			$crud->unset_export();
			$crud->unset_add();
			$crud->unset_read();	
							
			$crud->required_fields(	'tipo_mensaje');
			
			$output = $crud->render();

			$this->_example_output($output);
	}


/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de Estados los mensajes
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function estados_mensaje(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->set_table('estados_mensaje');
			
			$crud->columns(	'id_estado_mensaje',
							'estado_mensaje');
			
			$crud->display_as('id_estado_mensaje','ID')
				 ->display_as('estado_mensaje','Estado');
			
			$crud->set_subject('estado');
			$crud->unset_delete();
			$crud->unset_export();
			$crud->unset_add();
			$crud->unset_read();			
						
			$crud->required_fields('estado_mensaje');
			
			$_COOKIE['tabla']='estados_mensaje';
			$_COOKIE['id']='id_estado_mensaje';	
						
			$crud->callback_after_update(array($this, 'update_log'));
			
			$output = $crud->render();

			$this->_example_output($output);
	}



/**********************************************************************************
 **********************************************************************************
 * 
 * 				Actulizar nuevos
 * 
 * ********************************************************************************
 **********************************************************************************/
	
	
	function actualizar_nuevos(){
		$cant_mensajes=$this->mensajes_model->getCantNuevos();	
		
		if($cant_mensajes>0){
			$mensajes=$this->mensajes_model->getNuevos();
		
			foreach ($mensajes as $mensaje) {
				$id=$mensaje->id_mensaje;
				if($id>0){
					$mensaje = array(
        				"id_mensaje" => $this->input->post('id_mensaje'.$id),
        				"id_estado_mensaje" => $this->input->post('estado'.$id)
    				);	
				}else{
					//echo "cero";
				}
				
 				
				$this->db->update('mensajes', $mensaje, array('id_mensaje' => $id));
				$_COOKIE['tabla']='mensajes';
				$_COOKIE['id']='id_mensaje';
				$this->update_log($mensaje, $id);
			}
				
		}
		redirect('admin/mensaje/mensajes_abm/success', 'refresh');		
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
	 
	    $this->db->insert('logs_mensajes',$registro);
	 
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
 
    	$this->db->insert('logs_mensajes',$registro);
 
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
 
    	$this->db->insert('logs_mensajes',$registro);
			
    	return $this->db->update($_COOKIE['tabla'], array('delete' => 1), array($_COOKIE['id'] => $id));
	}
	



	


}