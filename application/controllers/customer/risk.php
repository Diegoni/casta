<?php
class Risk extends MY_Controller
{
	protected $model		= 'customer/m_risk';
	protected $check_loged	= FALSE; 
	protected $title		= 'Clientes - Riesgo'; 
	protected $view			= 'customer';
	
	function __construct()
	{
		parent::__construct(
			$model			= $this->model, 
			$check_loged	= $this->check_loged, 
			$title			= $this->title, 
			$view			= $this->view
		);
		
		$this->load->library('userauth');
		$this->load->library('out');
		$this->load->library('grocery_CRUD');
		
		$this->load->helpers('vistas');
	}


/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de riesgo
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function crud_risk()
	{
		
		$crud = new grocery_CRUD();
							
		$crud->set_table('ps_risk');
		
		$crud->set_subject($this->title);
				
		$crud->columns('id_risk', 'descripcion');
		
		$output = $crud->render();
		
		$this->_crud_output($output);

	}
}

/* End of file cliente.php */
/* Location: ./system/application/controllers/clientes/cliente.php */