<?php
class Ajax extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('userauth');
		$this->load->library('out');
		$this->load->model('clientes/m_cliente');
		
	}

	function index()
	{
		$this->load->view('ajax');
	}
	
	public function respuesta()
    {
        $id=$this->input->post("id");
		$db['datos']	= $this->m_cliente->getID($id);
        $this->load->view("respuesta", $db);
    }
}
