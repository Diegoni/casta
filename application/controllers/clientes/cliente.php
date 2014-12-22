<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cliente extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model(V_CLIENTES.'gruposcliente_model');
		$this->load->model(V_CLIENTES.'tiposcliente_model');
		$this->load->model(V_CLIENTES.'estadoscliente_model');
		$this->load->model(V_CLIENTES.'tarifascliente_model');
		
	}
	
	
	public function index(){
		$db['texto']			= $this->idiomas_model->getIdioma('es');
		$db['gruposcliente']	= $this->gruposcliente_model->getGrupos();
		$db['tiposcliente']		= $this->tiposcliente_model->getTipos();
		$db['estadoscliente']	= $this->estadoscliente_model->getEstados();
		$db['tarifascliente']	= $this->tarifascliente_model->getTarifas();
		
				
		$this->load->view('head', $db);
		$this->load->view(V_CLIENTES.'acciones');
		$this->load->view(V_CLIENTES.'clientes');
		$this->load->view('footer');
	}
	
}

