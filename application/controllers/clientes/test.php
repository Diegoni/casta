<?php
class Test extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Cliente
	 */
	function __construct()
	{
		parent::__construct(
		'clientes.cliente', 
		'clientes/M_cliente', 
		FALSE, 
		'clientes/cliente.js', 
		'Clientes');
		
	}
	
	function index()
	{
		
		$this->load->view('clientes/test');
	}

	function alta()
	{
		//$this->userauth->roleCheck(($this->auth.'.add'));
		$data = get_post_all();
		foreach($data as $k => $v)
		{	
			if (trim($v) == ''){
				unset($data[$k]);
			}
			elseif (is_string($v))
			{
				$data[$k] = urldecode($v);
			}
		}
		if (isset($data['cEmpresa']) || isset($data['cNombre']) || isset($data['cApellido']))
		{
			//Preparamos los datos
			$this->load->model('clientes/m_cliente');
			$this->load->model('clientes/m_telefono');
			$this->load->model('clientes/m_direccioncliente');
			$this->load->model('clientes/m_email');
			$this->db->trans_begin();
			$id_cliente = $this->m_cliente->insert($data);
			if ($id_cliente < 1)
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_cliente->error_message());
			}
			if (isset($data['cEmail']))
			{
				$id = $this->m_email->insert(array('nIdCliente' => $id_cliente, 'cEMail' => $data['cEmail']));
				if ($id < 1)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_email->error_message());
				}
			}
			if (isset($data['cTelefono']))
			{
				$id = $this->m_telefono->insert(array('nIdCliente' => $id_cliente, 'cTelefono' => $data['cTelefono']));
				if ($id < 1)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_telefono->error_message());
				}
			}
			if (isset($data['cCalle']))
			{
				$data['nIdCliente'] =  $id_cliente;
				$id = $this->m_direccioncliente->insert($data);
				if ($id < 1)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_direccioncliente->error_message());
				}
			}
			$this->db->trans_commit();

			$res = array (
				'success'	=> TRUE,
				'message'	=> sprintf($this->lang->line('registro_generado'), $id_cliente),
				'id'		=> $id_cliente
			);
			$this->out->send($res);
		}
		else
		{
			$this->_show_js('add', 'clientes/altarapida.js');
		}
	}


}
