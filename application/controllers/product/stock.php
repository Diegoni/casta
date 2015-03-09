<?php
class Stock extends MY_Controller
{
	protected $model		= 'product/m_product'; 
	protected $check_loged	= FALSE;
	protected $title		= 'Stock'; 
	protected $view			= 'product';
		
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
		
		$this->load->model('product/m_product');
		$this->load->model('product/m_product_lang');
		$this->load->model('product/m_stock');
	}

	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Movientos de Stock
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function stock()
	{
		$this->m_stock->activarNuevos();
		
		$crud = new grocery_CRUD();
				
		$crud->set_table('tms_stock');
		$crud->set_subject('Stock');
		
		$crud->columns('producto','firme', 'pendiente', 'asignado');
		
		$crud->callback_column('producto',array($this,'_lang'));
		
		$crud->display_as('id_product','ID')
			 ->display_as('name','Nombre')
			 ->display_as('id_lang','Lenguaje')
			 ->display_as('description_short','Descripción');
		
		//$crud->set_relation('id_product','ps_product_lang', 'name', 'id_lang = 1');
		
		$crud->order_by('id_product','desc');
		
		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_read();
		$crud->unset_edit();
			
		$output = $crud->render();

		$this->_crud_output($output);
	}
	
}