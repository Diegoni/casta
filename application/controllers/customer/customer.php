<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	clientes
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador de clientes
 *
 */
class Customer extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Cliente
	 */
	function __construct()
	{
		//parent::__construct('clientes.cliente', 'clientes/M_cliente', TRUE, 'clientes/cliente.js', 'Clientes');
		parent::__construct();
		
		$this->load->library('userauth');
		$this->load->library('out');
		
		$this->load->helpers('vistas');
		
		$this->load->model('customer/m_customer');
		$this->load->model('group/m_group');
		$this->load->model('group/m_group_lang');
	}
	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de clientes
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/
	function crud_customer()
	{
		$db['texto']			= $this->m_idiomas->getIdioma(1);
		$db['grupos']			= $this->m_group_lang->getSelect();
		
		if($this->input->post('b_codigo'))
		{
			$db['b_clientes']	= $this->m_customer->getID($this->input->post('b_codigo'), TRUE);
			if($db['b_clientes']==null)//Aseguramos que el codigo exista
			{
				unset($db['b_clientes']);
				$db['mensaje']	= $this->m_customer->getMensaje('Busqueda', 'error', $this->input->post('b_codigo'));
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
		
		if($this->input->post('guardar'))
		{
			if($this->input->post('id_customer'))//update
			{
				$id = $this->input->post('id_customer');
				$this->m_customer->update($id);
				$db['mensaje']	= $this->m_customer->getMensaje('Modificacion', 'ok', $id);
			}
			else
			{
				$id	= $this->m_customer->insert();
				if($id>0){
					$db['mensaje']	= $this->m_customer->getMensaje('Alta', 'ok', $id);
				}
				else
				{
					$db['mensaje']	= $this->m_customer->getMensaje('Alta', 'error', $id);
				}	
			}
		}
		
		$db['clientes']			= $this->m_customer->getRegistros();
		$db['clientes_model']	= $this->m_customer->getData_model();
		
		$this->load->view('head', $db);
		$this->load->view('menu');
		$this->load->view('customer/crud_customer');
		$this->load->view('footer');		
	}
	
}

/* End of file cliente.php */
/* Location: ./system/application/controllers/clientes/cliente.php */