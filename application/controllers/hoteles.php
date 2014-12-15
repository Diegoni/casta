<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hoteles extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model('articulos_model');
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
		$this->load->model('imagenes_hotel_model');
		$this->load->model('monedas_model');
		$this->load->model('modulos_idioma_model');
		$this->load->helper('main');
		$this->load->helper('form');
      	$this->load->helper('url');
	}
	
	
	public function galeria($id_hotel=NULL){
		if($id_hotel==NULL){
			if($this->uri->segment(1)==""){
				redirect(base_url().'','refresh');
			}else{
				redirect(base_url().'/index.php/'.$this->uri->segment(1).'/','refresh');	
			}
		}else{
			$_COOKIE['id_hotel']=$id_hotel;
		}
		
		$datos=array(	
			'id_tipo'		=> 3
		);
		
		$db['banner']		= $this->articulos_model->getBanner($datos);
		$db['texto']		= $this->idiomas_model->getIdioma($this->uri->segment(1));
		$db['idiomas']		= $this->idiomas_model->getIdiomas();
		$db['hoteles']		= $this->hoteles_model->getHoteles($_COOKIE['id_hotel']);
		$db['hoteles_menu']	= $this->hoteles_model->getHotelesAll();
		$db['habitaciones']	= $this->hoteles_model->getHotel($_COOKIE['id_hotel']);
		$db['configs']		= $this->configs_model->getConfigs();
		$db['emails_hotel']	= $this->hoteles_email_model->getEmails($_COOKIE['id_hotel']);
								
		$this->load->view('frontend/head', $db);
		$this->load->view('frontend/menu');
		//$this->load->view('frontend/formulario_reserva');
		$this->load->view('frontend/hoteles/galeria');
		$this->load->view('frontend/footer');
		
	}
	
	public function habitaciones($id_hotel=NULL){
		if($id_hotel==NULL){
			if($this->uri->segment(1)==""){
				redirect(base_url().'','refresh');
			}else{
				redirect(base_url().'/index.php/'.$this->uri->segment(1).'/','refresh');	
			}
		}else{
			$_COOKIE['id_hotel']=$id_hotel;
		}
		
		
		
		$db['texto']		= $this->idiomas_model->getIdioma($this->uri->segment(1));
		
		$consulta=array('id_hotel'	=> $id_hotel);
		
		$datos=array(	
			'id_tipo'		=> 2
		);
		
		$db['banner']		= $this->articulos_model->getBanner($datos);
		$db['hoteles']		= $this->hoteles_model->getHoteles($_COOKIE['id_hotel']);
		$db['hotel']		= $this->hoteles_model->getHotel($_COOKIE['id_hotel']);
		$db['hoteles_menu']	= $this->hoteles_model->getHotelesAll();
		$db['habitaciones']	= $this->habitaciones_model->getHabitaciones($consulta);
		$db['traducciones']	= $this->modulos_idioma_model->getTraducciones($db['habitaciones'], 1);
		$db['idiomas']		= $this->idiomas_model->getIdiomas();
		$db['emails_hotel']	= $this->hoteles_email_model->getEmails($id_hotel);
		
		if(!(isset($_COOKIE['moneda']))){
			$_COOKIE['moneda']=1;
		}
		
		$db['cambios']			= $this->monedas_model->getMoneda($_COOKIE['moneda']);
		$db['configs']			= $this->configs_model->getConfigs();
		$db['tipos_habitacion']	= $this->tipos_habitacion_model->getTipos();
		$db['tipo_habitacion']	= $this->tipos_habitacion_model->getTipo($this->input->post('tipo'));
		
				
		$this->load->view('frontend/head', $db);
		$this->load->view('frontend/menu');
		//$this->load->view('frontend/formulario_reserva');
		$this->load->view('frontend/hoteles/habitacion');
		$this->load->view('frontend/footer');
		
	}


	public function como_llegar($id_hotel=NULL){
		if($id_hotel==NULL){
			if($this->uri->segment(1)==""){
				redirect(base_url().'','refresh');
			}else{
				redirect(base_url().'/index.php/'.$this->uri->segment(1).'/','refresh');	
			}
		}else{
			$_COOKIE['id_hotel']=$id_hotel;
		}
		
		$datos=array(	
			'id_tipo'		=> 4
		);
		
		$db['banner']		= $this->articulos_model->getBanner($datos);
		$db['texto']		= $this->idiomas_model->getIdioma($this->uri->segment(1));
		$db['idiomas']		= $this->idiomas_model->getIdiomas();
		$db['hoteles']		= $this->hoteles_model->getHoteles($_COOKIE['id_hotel']);
		$db['hoteles_menu']	= $this->hoteles_model->getHotelesAll();
		$db['configs']		= $this->configs_model->getConfigs();
		$db['emails_hotel']	= $this->hoteles_email_model->getEmails($_COOKIE['id_hotel']);
								
		$this->load->view('frontend/head', $db);
		$this->load->view('frontend/menu');
		//$this->load->view('frontend/formulario_reserva');
		$this->load->view('frontend/hoteles/como_llegar');
		$this->load->view('frontend/footer');
		
	}
	
	
	
}
