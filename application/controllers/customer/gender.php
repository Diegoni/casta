<?php
class Gender extends MY_Controller
{
	protected $model		= 'customer/m_gender';
	protected $check_loged	= FALSE; 
	protected $title		= 'Clientes - Género'; 
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
		$this->load->model('group/m_group');
		$this->load->model('group/m_group_lang');
	}


/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de clientes
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function crud_gender()
	{
		
		$crud = new grocery_CRUD();
							
		$crud->set_table('ps_gender');
		
		$crud->set_subject($this->title);
				
		$crud->columns('id_gender', 'descripcion');
		
		$output = $crud->render();
		
		$this->_crud_output($output);

	}
}

/* End of file cliente.php */
/* Location: ./system/application/controllers/clientes/cliente.php */