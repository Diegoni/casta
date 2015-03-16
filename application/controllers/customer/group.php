<?php
class Group extends MY_Controller
{
	protected $model		= 'customer/m_group';
	protected $check_loged	= FALSE; 
	protected $title		= 'Clientes - Grupo'; 
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
		
		$this->load->model('customer/m_customer');
	}


/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de grupo
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function crud_group()
	{
		
		$crud = new grocery_CRUD();
							
		$crud->set_table('ps_group');
		
		$crud->set_subject($this->title);
				
		$crud->columns('id_group', 'descripcion');
		
		$crud->field_type('date_add', 'readonly');
		$crud->field_type('date_upd', 'readonly');
		
		$output = $crud->render();
		
		$this->_crud_output($output);

	}
}

/* End of file cliente.php */
/* Location: ./system/application/controllers/clientes/cliente.php */