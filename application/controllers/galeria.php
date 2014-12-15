<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Galeria extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		/* Standard Libraries */
		$this->load->database();
		/* ------------------ */
		$this->load->model('reservas_model');
		$this->load->helper('url'); //Just for the examples, this is not required thought for the library
		$this->load->helper('menu');
		
		$this->load->library('image_CRUD');
	}
	
	function _example_output($output = null)
	{
		$db['reservas']=$this->reservas_model->getCantNuevas();
		$reservas=buscarReservas();
		$mensajes=buscarMensajes();
		
		$db=array_merge($reservas, $mensajes);
			
		$this->load->view('backend/head.php',$output);
		$this->load->view('backend/menu.php',$db);	
		$this->load->view('backend/hoteles.php');
		$this->load->view('backend/footer.php');
	}
	
	function _habitacion_output($output = null)
	{
		$db['reservas']=$this->reservas_model->getCantNuevas();
		$reservas=buscarReservas();
		$mensajes=buscarMensajes();
		
		$db=array_merge($reservas, $mensajes);
			
		$this->load->view('backend/head.php',$output);
		$this->load->view('backend/menu.php',$db);	
		$this->load->view('backend/habitaciones.php');
		$this->load->view('backend/footer.php');
	}
	
	function _example_articulos($output = null)
	{
		$db['reservas']=$this->reservas_model->getCantNuevas();
		$reservas=buscarReservas();
		$mensajes=buscarMensajes();
		
		$db=array_merge($reservas, $mensajes);
			
		$this->load->view('backend/head.php',$output);
		$this->load->view('backend/menu.php',$db);	
		$this->load->view('backend/articulos.php');
		$this->load->view('backend/footer.php');
	}
	
	

/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de imagenes galeria
 * 
 * ********************************************************************************
 **********************************************************************************/
	
	function imagenes_habitacion($id=NULL)
	{
		$image_crud = new image_CRUD();
		
		$image_crud->set_primary_key_field('id_imagen');
		$image_crud->set_url_field('imagen');
		$image_crud->set_title_field('descripcion');

		
		$image_crud->set_table('imagenes_habitacion')
				   ->set_relation_field('id_habitacion')
				   ->set_ordering_field('orden')
				   ->set_image_path('assets/uploads/habitaciones');
			
		$output = $image_crud->render();
	
		$this->_habitacion_output($output);
	}
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de imagenes carrusel
 * 
 * ********************************************************************************
 **********************************************************************************/
	
	function imagenes_carrusel($id=NULL)
	{
		$image_crud = new image_CRUD();
	
		$image_crud->set_primary_key_field('id_imagen');
		$image_crud->set_url_field('imagen');
		$image_crud->set_title_field('descripcion');
		
		$image_crud->set_table('imagenes_carrusel')
				   ->set_relation_field('id_hotel')
				   ->set_ordering_field('orden')
				   ->set_image_path('assets/uploads/carrusel');
			
		$output = $image_crud->render();
	
		$this->_example_output($output);
	}	
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de imagenes carrusel
 * 
 * ********************************************************************************
 **********************************************************************************/
	
	function imagenes_hoteles($id=NULL)
	{
		$image_crud = new image_CRUD();
	
		$image_crud->set_primary_key_field('id_imagen');
		$image_crud->set_url_field('imagen');
		$image_crud->set_title_field('descripcion');
		
		$image_crud->set_table('imagenes_hoteles')
				   ->set_relation_field('id_hotel')
				   ->set_ordering_field('orden')
				   ->set_image_path('assets/uploads/hoteles');
			
		$output = $image_crud->render();
	
		$this->_example_output($output);
	}	
	
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Alta, baja y modificación de imagenes carrusel
 * 
 * ********************************************************************************
 **********************************************************************************/
	
	function imagenes_articulos()
	{
		$image_crud = new image_CRUD();
	
		$image_crud->set_primary_key_field('id_imagen');
		$image_crud->set_url_field('imagen');
		$image_crud->set_title_field('descripcion');
		$image_crud->set_title_description();
		
		$image_crud->set_table('imagenes_articulo')
				   //->set_relation_field('id_hotel')
				   ->set_ordering_field('orden')
				   ->set_image_path('assets/uploads/articulos');
			
		$output = $image_crud->render();
	
		$this->_example_articulos($output);
	}	
	
	

}