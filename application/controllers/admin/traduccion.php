<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Traduccion extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('menu');
		$this->load->model('reserva_habitacion_model');
		$this->load->model('modulos_model');
		$this->load->model('idiomas_model');
		$this->load->model('modulos_idioma_model');
		$this->load->model('estados_traduccion_model');
		$this->load->library('grocery_CRUD');
		//$this->load->library('image_CRUD');
		
	}


	public function index()
	{
		if($this->session->userdata('logged_in')){
			$reservas=buscarReservas();
			$mensajes=buscarMensajes();
						
			$db=array_merge($reservas, $mensajes);
			$db['modulos']	=	$this->modulos_model->getModulos();
			$db['idiomas']	=	$this->idiomas_model->getIdiomas();
			$db['estados_traduccion']	=	$this->estados_traduccion_model->getEstados();
			
			if($this->input->post('enviar')){
					
				if($this->input->post('estado')){
					$estado	= $this->input->post('estado');
				}else{
					$estado	= 0;
				}
				
				$datos=array(
							'modulo'	=> $this->input->post('modulo'),
							'idioma'	=> $this->input->post('idioma'),
							'estado'	=> $estado);
							
				$db['registros'] = $this->modulos_idioma_model->getModulo($datos);
			}else if($this->input->post('traducir')){
				$datos2=array(
							'modulo'	=> $this->input->post('modulo'),
							'idioma'	=> $this->input->post('idioma'),
							'estado'	=> $this->input->post('traducir'));
							
				$registros	= $this->modulos_idioma_model->getModulo($datos2);
				
				
				foreach ($registros as $registro) {
					if($this->input->post('titulo_idioma'.$registro->id_tabla)){
						
						$datos=array(
									'id_modulo_idioma'	=> $this->input->post('id_modulo_idioma'),
									'titulo'			=> $this->input->post('titulo_idioma'.$registro->id_tabla),
									'descripcion'		=> $this->input->post('descripcion_idioma'.$registro->id_tabla),
									'id_modulo'			=> $this->input->post('modulo'),
									'id_tabla'			=> $registro->id_tabla,
									'id_idioma'			=> $this->input->post('idioma'),
									'id_estado_traduccion'=> $this->input->post('traducir'),
									'delete'			=> 0
									);
						$mensaje = $this->modulos_idioma_model->updateModulo($datos);
					}else{
						//echo "no entro ".$registro->id_tabla."<br>";
					}
				}
				
				$db['registros'] = $this->modulos_idioma_model->getModulo($datos2);
			}
						
			$this->load->view('backend/head.php', $db);
			$this->load->view('backend/menu.php');	
			$this->load->view('backend/modal.php');
			$this->load->view('backend/traducciones.php');
			$this->load->view('backend/footer.php');
		}else{
			redirect('/admin/home/logout/','refresh');
		}
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