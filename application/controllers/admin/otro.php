<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Otro extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->helper('menu');
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
			$this->load->view('backend/otros.php');
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
 * 				Alta, baja y modificación de departamentos
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function departamentos_abm(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->set_table('departamentos');
			
			$crud->columns(	'id_departamento',
							'departamento',
							'id_provincia');
			
			$crud->display_as('id_departamento','ID')
				 ->display_as('departamento','Departamento')
				 ->display_as('id_provincia','Provincia');
			
			$crud->set_subject('departamento');
			
			$crud->set_relation('id_provincia','provincias','provincia');
			
			$crud->required_fields(	'id_departamento',
									'departamento', 
									'id_provincia');
			
			$output = $crud->render();

			$this->_example_output($output);
	}



/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de provincias
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function provincias_abm(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->set_table('provincias');
			
			$crud->columns(	'id_provincia',
							'provincia',
							'id_pais');
			
			$crud->display_as('id_provincia','ID')
				 ->display_as('provincia','Provincia')
				 ->display_as('id_pais','País');
			
			$crud->set_subject('provincia');
			
			$crud->set_relation('id_pais','paises','pais');
			
			$crud->required_fields(	'id_provincia',
									'provincia', 
									'id_pais');
			
			$output = $crud->render();

			$this->_example_output($output);
	}


/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de paises
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function paises_abm(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->set_table('paises');
			
			$crud->columns(	'id_pais',
							'pais');
			
			$crud->display_as('id_pais','ID')
				 ->display_as('pais','País');
			
			$crud->set_subject('país');
			
			
			$crud->required_fields(	'pais');
			
			$output = $crud->render();

			$this->_example_output($output);
	}
	
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de tipos 
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function tipos_abm(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->set_table('tipos');
			
			$crud->columns(	'id_tipo',
							'tipo');
			
			$crud->display_as('id_tipo','ID')
				 ->display_as('tipo','Tipo');
			
			$crud->set_subject('tipo');
							
			$crud->required_fields(	'tipo');
			
			$output = $crud->render();

			$this->_example_output($output);
	}

/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de aerolineas 
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function aerolineas_abm(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->set_table('aerolineas');
			
			$crud->columns(	'id_aerolinea',
							'aerolinea');
			
			$crud->display_as('id_aerolinea','ID')
				 ->display_as('aerolinea','Aerolinea');
				 
			$crud->fields('aerolinea');
			
			$crud->set_subject('aerolinea');
							
			$crud->required_fields(	'aerolinea');
			
			$output = $crud->render();

			$this->_example_output($output);
	}
	
	
	
	

/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de términos
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function terminos_abm(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->set_table('terminos');
			
			$crud->columns(	'id_termino',
							'termino');
			
			$crud->display_as('id_termino','ID')
				 ->display_as('termino','Término');
			
			$crud->set_subject('término');
			$crud->unset_delete();
			$crud->unset_export();
			$crud->unset_delete();
			$crud->unset_export();
			$crud->unset_add();
			$crud->unset_read();
						
			$crud->required_fields(	'termino');
			
			$_COOKIE['tabla']='terminos';
			$_COOKIE['id']='id_termino';	
			
			$crud->callback_after_update(array($this, 'update_log'));
			
			
			$output = $crud->render();

			$this->_example_output($output);
	}
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de monedas
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function idiomas_abm(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->where('idiomas.delete', 0);
			$crud->set_table('idiomas');
			
			$crud->columns(	'id_idioma',
							'idioma',
							'imagen');
			
			$crud->display_as('id_idioma','ID')
				 ->display_as('idioma','Idioma')
				 ->display_as('imagen','Imágen');
			
			$crud->fields('idioma', 'imagen');
			
			$crud->set_subject('idioma');
						
			$crud->required_fields('idioma', 'imagen');
			
			$crud->set_field_upload('imagen','assets/uploads/idiomas');
			
			$crud->unset_add();
			$crud->unset_delete();
			
			$_COOKIE['tabla']='idiomas';
			$_COOKIE['id']='id_idioma';
			
			$crud->callback_after_update(array($this, 'update_log'));
						
			$output = $crud->render();

			$this->_example_output($output);
	}

	

	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de ubicación
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function ayudas_abm(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->set_table('ayudas');
			
			$crud->columns(	'id_ayuda',
							'titulo',
							'ayuda',
							'id_idioma');
			
			$crud->display_as('id_ayuda','ID')
				 ->display_as('titulo','Título')
				 ->display_as('ayuda','Ayuda')
				 ->display_as('id_idioma','Idioma');
			
			$crud->edit_fields('titulo', 'ayuda', 'sector', 'id_idioma');
			
			$crud->set_subject('ayuda');
			
			$crud->set_relation('id_idioma','idiomas','idioma');
						
			$crud->required_fields('titulo', 'ayuda', 'sector', 'id_idioma');
			
			$crud->field_type('sector','dropdown',
                               array( "datos"  => "datos", "pagos" => "pagos"));
			
			//$crud->unset_delete();
			//$crud->unset_add();
			
			$_COOKIE['tabla']='ayudas';
			$_COOKIE['id']='id_ayuda';	
						
			$crud->callback_after_insert(array($this, 'insert_log'));	
			$crud->callback_after_update(array($this, 'update_log'));
			$crud->callback_delete(array($this,'delete_log'));
			
			
			$output = $crud->render();

			$this->_example_output($output);
	}
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de ubicación
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
	public function config_correo(){
			$crud = new grocery_CRUD();

			
			$crud->set_table('config_correo');
			
			$crud->set_subject('config correo');
			
			$crud->field_type('autenticacion_smtp', 'true_false');
			
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
	 
	    $this->db->insert('logs_otros',$registro);
	 
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
 
    	$this->db->insert('logs_otros',$registro);
 
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
 
    	$this->db->insert('logs_otros',$registro);
			
    	return $this->db->update($_COOKIE['tabla'], array('delete' => 1), array($_COOKIE['id'] => $id));
	}
	
}