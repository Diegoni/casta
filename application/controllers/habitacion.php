<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Habitacion extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('hoteles_model');
		$this->load->model('habitaciones_model');
		$this->load->model('huespedes_model');
		$this->load->model('reservas_model');
		$this->load->model('notas_model');
		$this->load->model('ayudas_model');
		$this->load->model('configs_model');
		$this->load->model('habitacion_servicio_model');
		$this->load->model('tipos_habitacion_model');
		$this->load->model('provincias_model');
		$this->load->model('imagenes_habitacion_model');
		$this->load->model('modulos_idioma_model');
		$this->load->model('monedas_model');
		$this->load->helper('main');
		$this->load->helper('form');
      	$this->load->helper('url');
	}
	
	
	public function view($id=NULL, $id_hotel=NULL){
			
		if($id_hotel==NULL){
			if($this->uri->segment(1)==""){
				redirect(base_url().'','refresh');
			}else{
				redirect(base_url().'/index.php/'.$this->uri->segment(1).'/','refresh');	
			}
		}else{
			$_COOKIE['id_hotel']=$id_hotel;
		}


		if($id==NULL){
			$id=$this->input->post('id');
		}
		$db['texto']		= $this->idiomas_model->getIdioma($this->uri->segment(1));
		$db['idiomas']		= $this->idiomas_model->getIdiomas();
		$db['hoteles']		= $this->hoteles_model->getHoteles($id_hotel);
		$db['hoteles_menu']	= $this->hoteles_model->getHotelesAll();
		$db['habitaciones']	= $this->habitaciones_model->getHabitacion($id);
		$db['traducciones']	= $this->modulos_idioma_model->getTraducciones($db['habitaciones'], 1);
		$db['servicios']	= $this->habitacion_servicio_model->getServicios($id);
		$db['t_servicios']	= $this->modulos_idioma_model->getTraducciones($db['servicios'], 5);
		$db['provincias']	= $this->provincias_model->getProvincias('032');
		$db['configs']		= $this->configs_model->getConfigs();
		$db['emails_hotel']	= $this->hoteles_email_model->getEmails($id_hotel);
								
		$this->load->view('frontend/head', $db);
		$this->load->view('frontend/menu');
		//$this->load->view('frontend/formulario_consulta');
		$this->load->view('frontend/habitacion/view');
		$this->load->view('frontend/footer');
		
	}
	
	public function galeria($id=NULL, $id_hotel=NULL){
			
		if($id_hotel==NULL){
			if($this->uri->segment(1)==""){
				redirect(base_url().'','refresh');
			}else{
				redirect(base_url().'/index.php/'.$this->uri->segment(1).'/','refresh');	
			}
		}else{
			$_COOKIE['id_hotel']=$id_hotel;
		}
		
		if($id==NULL){
			$id=$this->input->post('id');
		}
		
		$db['texto']		= $this->idiomas_model->getIdioma($this->uri->segment(1));
		$db['idiomas']		= $this->idiomas_model->getIdiomas();
		$db['hoteles']		= $this->hoteles_model->getHoteles($id_hotel);
		$db['hoteles_menu']	= $this->hoteles_model->getHotelesAll();
		$db['habitaciones']	= $this->habitaciones_model->getHabitacion($id);
		$db['servicios']	= $this->habitacion_servicio_model->getServicios($id);
		$db['configs']		= $this->configs_model->getConfigs();
		$db['emails_hotel']	= $this->hoteles_email_model->getEmails($id_hotel);
								
		$this->load->view('frontend/head', $db);
		$this->load->view('frontend/menu');
		//$this->load->view('frontend/formulario_consulta');
		$this->load->view('frontend/habitacion/galeria');
		$this->load->view('frontend/footer');
		
	}
	
	
	
	
}
