<?php
class Feature extends MY_Controller
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
			Crud de productos traduccion
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function crud_feature()
	{	
		$crud = new grocery_CRUD();
		
		$crud->set_table('ps_feature');
		$crud->set_subject($this->lang->line('caracteristicas'));
		
		$crud->columns('id_feature', 'feature');
		
		$crud->display_as('id_feature','ID')
			 ->display_as('feature',	$this->lang->line('caracteristicas'))
			 ->display_as('name',		$this->lang->line('nombre'))
			 ->display_as('position',	$this->lang->line('posicion'));
		
		$crud->add_fields('position', 'name');
		
		$crud->add_action($this->lang->line('traduccion'), '', '','fa fa-globe',array($this,'vista_traduccion'));
		$crud->add_action($this->lang->line('valor'), '', '','fa fa-chevron-circle-right',array($this,'vista_valor'));
		$crud->callback_column('feature',array($this,'_lang'));
		$crud->callback_insert(array($this,'insert_lang'));
		
		$crud->display_as('id_feature','ID')
			 ->display_as('caracteristica', $this->lang->line('caracteristicas'));
			 
		$crud->order_by('id_feature','desc');
			
		$output = $crud->render();

		$this->_crud_output($output);
	}	

		
	function vista_valor($primary_key , $row)
	{
		$table		= $this->reg->getTable();
		$table		= substr($table, 3);
	    return site_url($this->view.'/feature_value/crud_feature_value/'.$primary_key);
	}



/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de productos traduccion
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function crud_feature_lang($id=NULL)
	{	
		$crud = new grocery_CRUD();
		
		if($id != NULL)
		{
			$crud->where('ps_feature_lang.id_feature = '.$id);
		}
		
		$crud->set_table('ps_feature_lang');
		$crud->set_subject($this->lang->line('caracteristicas'));
		
		$crud->columns('id_feature', 'name', 'id_lang');
		
		$crud->display_as('id_feature','ID')
			 ->display_as('name',	$this->lang->line('nombre'))
			 ->display_as('id_lang',$this->lang->line('lenguaje'));
			 
		$crud->field_type('id_lang', 'readonly');
		$crud->field_type('id_feature', 'readonly');
		
		$crud->set_relation('id_lang','ps_lang', 'name');
		
		$crud->order_by('id_feature','desc');
		
		$crud->unset_add();
		$crud->unset_delete();
			
		$output = $crud->render();

		$this->_crud_output($output);
	}
	
}