<?php
class Product extends MY_Controller
{
	function __construct()
	{
		parent::__construct(
			$model = 'product/m_product', 
			$check_loged = FALSE, 
			$title = 'productos', 
			$submenu = 'productos'
		);
		
		$this->load->library('userauth');
		$this->load->library('out');
		$this->load->library('grocery_CRUD');
		
		$this->load->helpers('vistas');
		
		$this->load->model('product/m_product');
		$this->load->model('product/m_product_lang');
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
			Salida del Crud 
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/
	
	public function _crud_output($output = null)
	{
		$db['texto']	= $this->m_idiomas->getIdioma(1);
		
		$this->load->view('head', $db);
		$this->load->view('menu');
		$this->load->view('product/crud.php',$output);
		$this->load->view('footer');
	}	


/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de productos traduccion
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function crud_product_lang()
	{
		
		$crud = new grocery_CRUD();

		$crud->set_table('ps_product_lang');
		$crud->set_subject('Productos');
		
		$crud->columns('id_product','name', 'description_short', 'id_lang');
		
		$crud->display_as('id_product','ID')
			 ->display_as('name','Nombre')
			 ->display_as('id_lang','Lenguaje')
			 ->display_as('description_short','Descripción');
			 
		$crud->field_type('id_shop', 'hidden');
		
		$crud->set_relation('id_lang','ps_lang', 'name');
		
		$crud->order_by('id_product','desc');
		
		
			
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

		$crud->set_table('ps_product');
		$crud->set_subject('Productos');
		$crud->set_model('MY_grocery_Model');
		
		$crud->columns('id_product','reference', 'id_supplier', 'producto', 'price');
		$crud->callback_column('producto',array($this,'_lang'));
		
		$crud->display_as('id_product','ID')
			 ->display_as('id_supplier','Proveedor')
			 ->display_as('manufacturer','Fabricante')
			 ->display_as('id_lang','Lenguaje')
			 ->display_as('id_category_default', 'Categoría')
  			 ->display_as('id_tax_rules_group', 'Reglas del grupo')
  			 ->display_as('on_sale', 'En venta')
  			 ->display_as('online_only', 'Sólo online (no se vende en ningún establecimiento físico)')
			 ->display_as('quantity', 'Cantidad')//Ver de donde saca
			 ->display_as('minimal_quantity', 'Cantidad mínima')
			 ->display_as('price', 'Precio')
			 ->display_as('wholesale_price', 'Precio al por mayor')
			 ->display_as('additional_shipping_cost', 'Costo de envío adicional')
			 ->display_as('unity', 'Unidad')
			 ->display_as('reference', 'Referencia') 
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
		$crud->set_relation('id_manufacturer','ps_manufacturer', 'name');
		$crud->set_relation('id_category_default','ps_category', 'level_depth', 'level_depth');
		
		//$crud->order_by('id_product','desc');
			
		$output = $crud->render();

		$this->_crud_output($output);

	}
	
}