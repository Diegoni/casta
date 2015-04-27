<?php
class Datos_maestros extends MY_Controller
{
	protected $model		= 'product/m_feature'; 
	protected $check_loged	= FALSE;
	protected $title		= 'Productos - Características'; 
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
	}


/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de Forma pago
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function forma_pago()
	{	
		$crud = new grocery_CRUD();
		
		$crud->set_table('tms_formas_pago');
		$crud->set_subject($this->lang->line('forma_pago'));
		
		$crud->columns('id_forma_pago', 'forma_pago');
		
		$crud->display_as('id_forma_pago','ID')
			 ->display_as('forma_pago',	$this->lang->line('forma_pago'));
		 
		$crud->order_by('forma_pago');
			
		$output = $crud->render();

		$this->_crud_output($output);
	}	
	

/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de Condición pago
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function condicion_pago()
	{	
		$crud = new grocery_CRUD();
		
		$crud->set_table('tms_condiciones_pago');
		$crud->set_subject($this->lang->line('condicion_pago'));
		
		$crud->columns('id_condicion_pago', 'condicion_pago');
		
		$crud->display_as('id_condicion_pago','ID')
			 ->display_as('condicion_pago',	$this->lang->line('condicion_pago'));
		 
		$crud->order_by('condicion_pago');
			
		$output = $crud->render();

		$this->_crud_output($output);
	}	
		

/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de Impuestos
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function tax()
	{	
		$crud = new grocery_CRUD();
		
		$crud->set_table('ps_tax');
		$crud->set_subject($this->lang->line('impuesto'));
		
		$crud->columns('id_tax', 'descripcion', 'rate');
		$crud->fields('descripcion', 'rate');
		
		$crud->display_as('id_tax','ID')
			 ->display_as('descripcion',	$this->lang->line('impuesto'))
			 ->display_as('rate',	$this->lang->line('valor'));
		 
		$crud->order_by('descripcion');
			
		$output = $crud->render();

		$this->_crud_output($output);
	}
	
	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de Impuestos
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function currencys()
	{	
		$crud = new grocery_CRUD();
		
		$crud->set_table('ps_currency');
		$crud->set_subject($this->lang->line('moneda'));
		
		$crud->columns('id_currency', 'name', 'sign', 'format', 'decimals', 'conversion_rate');
		$crud->fields('name', 'sign', 'format', 'decimals', 'conversion_rate');
		
		$crud->display_as('id_currency','ID')
			 ->display_as('name',	$this->lang->line('moneda'))
			 ->display_as('sign',	$this->lang->line('signo'))
			 ->display_as('format',	$this->lang->line('formato'))
			 ->display_as('decimals',	$this->lang->line('decimales'))
			 ->display_as('conversion_rate',	$this->lang->line('rango_conversion'));
		 
		$crud->order_by('name');
			
		$output = $crud->render();

		$this->_crud_output($output);
	}	

}