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
class Cliente extends MY_Controller
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
		$this->load->model('clientes/m_cliente');
		$this->load->model('clientes/m_tipocliente');		
		$this->load->model('clientes/m_grupocliente');
		$this->load->model('clientes/m_clientetarifa');
		$this->load->model('clientes/m_estadocliente');
	}

	/**
	 * Gestión Cliente
	 * @return FORM
	 */
	function abm_clientes()
	{
		$db['texto']	= $this->m_idiomas->getIdioma(1);
		
		//carga de select
		$db['tipos']	= $this->m_tipocliente->getSelect();
		$db['grupos']	= $this->m_grupocliente->getSelect();
		$db['tarifas']	= $this->m_clientetarifa->getSelect();
		$db['idiomas']	= $this->m_idiomas->getSelect();
		$db['estados']	= $this->m_estadocliente->getSelect();
		
		if($this->input->post('b_codigo'))
		{
			$db['b_clientes']	= $this->m_cliente->getID($this->input->post('b_codigo'));
			if($db['b_clientes']==null)//Aseguramos que el codigo exista
			{
				unset($db['b_clientes']);
				$db['mensaje']	= $this->m_cliente->getMensaje('Busqueda', 'error', $this->input->post('b_codigo'));
			}
		}
		
		if($this->input->post('guardar'))
		{
			if($this->input->post('nIdCliente'))//update
			{
				$id = $this->input->post('nIdCliente');
				$this->m_cliente->update($id);
				$db['mensaje']	= $this->m_cliente->getMensaje('Modificacion', 'ok', $id);
			}
			else
			{
				$id	= $this->m_cliente->insert();
				if($id>0){
					$db['mensaje']	= $this->m_cliente->getMensaje('Alta', 'ok', $id);
				}
				else
				{
					$db['mensaje']	= $this->m_cliente->getMensaje('Alta', 'error', $id);
				}	
			}
		}
		
		// para helper_abm_clientes
		$db['clientes']	= $this->m_cliente->getRegistros();
		$db['clientes_model'] = $this->m_cliente->getData_model();
		
		$this->load->view('head', $db);
		$this->load->view('menu');
		$this->load->view('clientes/abm_clientes');
		$this->load->view('footer');
	}
}

/* End of file cliente.php */
/* Location: ./system/application/controllers/clientes/cliente.php */