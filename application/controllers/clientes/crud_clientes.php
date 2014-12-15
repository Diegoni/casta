<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crud_clientes extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('grocery_CRUD');
	}


	public function _vista_salida($output = NULL){
		if($this->session->userdata('logged_in')){
			$this->load->view(ADMIN_CLIENTES.'clientes.php', $output);
		}else{
			redirect(ADMIN_LOGIN.'logout/','refresh');
		}
	}
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de clientes
 * 
 * ********************************************************************************
 **********************************************************************************/


	public function index(){
			$crud = new grocery_CRUD();

			//$crud->set_theme('datatables');
			$crud->where('clientes.delete', 0);
			$crud->set_table('clientes');
			
			$crud->columns(	'cNombre',
							'cApellido',
							'cEmpresa',
							'cNombreFiscal');
			
			$crud->display_as('cNombre','Nombre')
				 ->display_as('cApellido','Apellido')
				 ->display_as('cEmpresa','Empresa')
				 ->display_as('cNombreFiscal','Nombre Fiscal');				 
			
			$crud->set_subject('clientes');
			
			//$crud->field_type('pagina_principal', 'true_false');
			
			$crud->fields(	'cNombre',
							'cApellido',
							'cEmpresa', 
							'cNombreFiscal');
			
			//$crud->set_relation('id_hotel','hoteles','hotel', 'delete = 0');
					
			$crud->required_fields('cNombre', 'cApellido','cEmpresa','cNombreFiscal');
			
			//$crud->set_field_upload('archivo_url','assets/uploads/articulos');
			
			$_COOKIE['tabla']	= 'clientes';
			$_COOKIE['id']		= 'nIdCliente';	
			
			//$crud->callback_after_insert(array($this, 'insert_log'));
			//$crud->callback_after_update(array($this, 'update_log'));
			//$crud->callback_delete(array($this,'delete_log'));	
			
			$output = $crud->render();

			$this->_vista_salida($output);
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