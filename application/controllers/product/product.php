<?php
class Product extends MY_Controller
{
	protected $model		= 'product/m_product'; 
	protected $check_loged	= FALSE;
	protected $title		= 'Productos'; 
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

	function stock()
	{
		echo "test";
	}
	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud rapido de productos
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function crud_product_faster()
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


/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de productos traduccion
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function crud_product_lang($id=NULL)
	{	
		$crud = new grocery_CRUD();
		
		if($id != NULL)
		{
			$crud->where('ps_product_lang.id_product = '.$id);
		}
		
		$crud->set_table('ps_product_lang');
		$crud->set_subject('Productos');
		
		$crud->columns('id_product','name', 'description_short', 'id_lang');
		
		$crud->display_as('id_product','ID')
			 ->display_as('name','Nombre')
			 ->display_as('id_lang','Lenguaje')
			 ->display_as('description_short','Descripción');
			 
		$crud->field_type('id_shop', 'hidden');
		$crud->field_type('id_lang', 'readonly');
		$crud->field_type('id_product', 'readonly');
		
		$crud->set_relation('id_lang','ps_lang', 'name');
		
		$crud->order_by('id_product','desc');
		
		$crud->unset_add();
		$crud->unset_delete();
			
		$output = $crud->render();

		$this->_crud_output($output);
	}
	

/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de productos
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function crud_product()
	{
		
		$crud = new grocery_CRUD();
		
		$crud->where('ps_product.active = 1');
		$crud->set_table('ps_product');
		$crud->set_subject('Productos');
		$crud->set_model('MY_grocery_Model');
		
		$crud->columns('id_product', 'descripcion', 'reference', 'id_supplier',  'price');
	
		//$crud->callback_column('producto',array($this,'_lang'));
		$crud->add_action('Traducción', '', '','fa fa-globe',array($this,'vista_traduccion'));
		$crud->callback_delete(array($this,'delete_reg'));
		
		$crud->display_as('id_product', 		'ID')
			 ->display_as('id_supplier',		$this->lang->line("proveedor"))
			 ->display_as('id_manufacturer',	$this->lang->line("fabricante"))
			 ->display_as('id_lang',			$this->lang->line("lenguaje"))
			 ->display_as('id_category_default',$this->lang->line("categoria"))
  			 ->display_as('id_tax_rules_group',	$this->lang->line("reglas")." ".$this->lang->line("grupo"))
  			 ->display_as('on_sale',			$this->lang->line("en")." ".$this->lang->line("venta"))
  			 ->display_as('online_only', 		$this->lang->line("solo")." ".$this->lang->line("online"))
			 ->display_as('quantity', 			$this->lang->line("cantidad"))//Ver de donde saca
			 ->display_as('minimal_quantity',	$this->lang->line("cantidad")." ".$this->lang->line("minima"))
			 ->display_as('price',				$this->lang->line("precio"))
			 ->display_as('wholesale_price',	$this->lang->line("precio")." ".$this->lang->line("mayor"))
			 ->display_as('additional_shipping_cost', $this->lang->line("costo")." ".$this->lang->line("envio"))
			 ->display_as('unity', 				$this->lang->line("unidad"))
			 ->display_as('reference',			$this->lang->line("referencia"))
			 ;
  			 
		$crud->field_type('upc', 'hidden');
		$crud->field_type('ecotax', 'hidden');
		
		$crud->field_type('unit_price_ratio', 'hidden');
		
		$crud->field_type('supplier_reference', 'hidden');
		$crud->field_type('location', 'hidden');
		$crud->field_type('width', 'hidden');
		$crud->field_type('height', 'hidden');
		$crud->field_type('depth', 'hidden');
		$crud->field_type('weight', 'hidden');
		$crud->field_type('out_of_stock', 'hidden');
		$crud->field_type('quantity_discount', 'hidden');
		$crud->field_type('customizable', 'hidden');
		$crud->field_type('uploadable_files', 'hidden');
		$crud->field_type('text_fields', 'hidden');
		$crud->field_type('active', 'hidden');
		$crud->field_type('redirect_type', 'hidden');
		$crud->field_type('id_product_redirected', 'hidden');
		$crud->field_type('available_for_order', 'hidden');
		$crud->field_type('available_date', 'hidden');
		$crud->field_type('condition', 'hidden');
		$crud->field_type('show_price', 'hidden');
		$crud->field_type('indexed', 'hidden');
		$crud->field_type('visibility', 'hidden');
		$crud->field_type('cache_is_pack', 'hidden');
		$crud->field_type('cache_has_attachments', 'hidden');
		$crud->field_type('is_virtual', 'hidden');
		$crud->field_type('cache_default_attribute', 'hidden');
		$crud->field_type('date_add', 'hidden');
		$crud->field_type('date_upd', 'hidden');
		$crud->field_type('advanced_stock_management', 'hidden');
		$crud->field_type('pack_stock_type', 'hidden');

		$crud->field_type('id_shop', 'hidden');
		$crud->field_type('ean13', 'hidden');
		$crud->field_type('id_shop_default', 'hidden');
				
		$crud->set_relation('id_supplier','ps_supplier', 'name');
		$crud->set_relation('id_manufacturer', 'ps_manufacturer', 'name');
		$crud->set_relation('id_category_default','ps_category', 'descripcion');
		
		//$crud->order_by('id_product','desc');
			
		$output = $crud->render();

		$this->_crud_output($output);

	}
	
}