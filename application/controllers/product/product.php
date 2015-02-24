<?php
class Product extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('userauth');
		$this->load->library('out');
		
		$this->load->helpers('vistas');
	}
	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de productos
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/
	function crud_product()
	{
		$db['texto']			= $this->m_idiomas->getIdioma(1);
		
		$this->load->view('head', $db);
		$this->load->view('menu');
		$this->load->view('product/crud_product');
		$this->load->view('footer');		
	}
	
}