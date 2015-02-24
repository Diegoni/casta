<?php
class Product extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('userauth');
		$this->load->library('out');
		
		$this->load->helpers('vistas');
		
		$this->load->model('product/m_product');
		$this->load->model('product/m_product_lang');
	}
	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de productos
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/
	function crud_product()
	{
		$db['texto']			= $this->m_idiomas->getIdioma(1);
		
		if($this->input->post('b_codigo'))
		{
			$db['b_registros']	= $this->m_product->getID($this->input->post('b_codigo'), TRUE);
			$db['b_registros_lang']	= $this->m_product_lang->getID($this->input->post('b_codigo'), TRUE);
			if($db['b_registros']==null)//Aseguramos que el codigo exista
			{
				unset($db['b_registros']);
				unset($db['b_registros_lang']);
				$db['mensaje']	= $this->m_product->getMensaje('Busqueda', 'error', $this->input->post('b_codigo'));
			}
			else
			{
				/*
				$where = 'nIdCliente = '.$this->input->post('b_codigo');
				
				$db['telefonos']	= $this->m_telefono->getRegistros($where);
				$db['direcciones']	= $this->m_direccioncliente->getRegistros($where);
				$db['emails']		= $this->m_email->getRegistros($where);
				$db['contactos']	= $this->m_contacto->getRegistros($where);
				 * */
			}
		}
		
		$db['productos']			= $this->m_product_lang->getRegistros();
		$db['registro_model']		= $this->m_product->getData_model();
		$db['lang_model']			= $this->m_product_lang->getData_model();
		
		$this->load->view('head', $db);
		$this->load->view('menu');
		$this->load->view('product/crud_product');
		$this->load->view('footer');		
	}
	
}