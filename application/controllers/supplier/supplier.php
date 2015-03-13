	<?php
class Supplier extends MY_Controller
{
	protected $model		= 'supplier/m_supplier';
	protected $check_loged	= FALSE; 
	protected $title		= 'Proveedores'; 
	protected $view			= 'supplier';
	
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
		
		$this->load->model('supplier/m_supplier');	
		$this->load->model('supplier/m_supplier_lang');
		$this->load->model('product/m_product');	
		$this->load->model('product/m_product_lang');
	}
	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de proveedores
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/

	function crud_supplier_faster()
	{
		$db['texto']			= $this->m_idiomas->getIdioma(1);
		
		if($this->input->post('b_codigo'))
		{
			$db['b_registros']	= $this->m_supplier->getID($this->input->post('b_codigo'), TRUE);
			$db['b_registros_lang']	= $this->m_supplier_lang->getID($this->input->post('b_codigo'), TRUE);
			if($db['b_registros']==null)//Aseguramos que el codigo exista
			{
				unset($db['b_registros']);
				unset($db['b_registros_lang']);
				$db['mensaje']	= $this->m_supplier->getMensaje('Busqueda', 'error', $this->input->post('b_codigo'));
			}
			else
			{

			}
		}
		
		$db['proveedores']			= $this->m_supplier_lang->getRegistros();
		$db['registro_model']		= $this->m_supplier->getData_model();
		$db['lang_model']			= $this->m_supplier_lang->getData_model();
		
				
		$this->load->view('head', $db);
		$this->load->view('menu');
		$this->load->view('supplier/crud_supplier');
		$this->load->view('footer');		
	}


/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de productos
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function crud_supplier()
	{
		
		$crud = new grocery_CRUD();
		
		$crud->where('ps_supplier.active = 1');
		$crud->set_table('ps_supplier');
		
		$crud->set_subject($this->title);
				
		$crud->columns('name');
				
		$crud->display_as('date_add',	$this->lang->line("fecha")." ".$this->lang->line("alta"))
			 ->display_as('name',		$this->lang->line("nombre"))
			 ->display_as('date_upd',	$this->lang->line("fecha")." ".$this->lang->line("modificacion"));
		
		$crud->field_type('date_add', 'readonly');
		$crud->field_type('date_upd', 'readonly');
		$crud->field_type('active', 'hidden');
		
		$crud->callback_delete(array($this,'delete_reg'));
		
		$output = $crud->render();
		
		$this->_crud_output($output);

	}


/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Pedidos
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function pedidos()
	{
		if($this->input->post('guardar'))
		{

		}
		
		$db['products_name']	= $this->m_product->getSelect();
		$db['products_upc']		= $this->m_product->getSelect('upc');
		$db['supplier']			= $this->m_supplier->getSelect();
		
		$this->load->view('head', $db);	
		$this->load->view('menu');
		$this->load->view($this->view.'/pedidos');
		$this->load->view('footer');
	}
	
	function buscar(){
		if(!$this->input->is_ajax_request())
		{
			redirect('404');
		}
		else
		{
			$id = $this->input->post('name');
			
			$product	= $this->m_product->getID($id);
			
			foreach ($product as $row) {
				$upc	= $row->upc;
				$name	= $row->descripcion;
			}
			
			$cadena =  "<div class='row' id='div-$id'>";
			$cadena .= "<div class='col-md-1'>".$id."</div>";
			$cadena .= "<div class='col-md-2'>".$upc."</div>";
			$cadena .= "<div class='col-md-5'>".$name."</div>";
			$cadena .= "<div class='col-md-1'><input class='form-control input-sm' name='product-$id' id='product-$id' type='number' value='".$this->input->post('cantidad')."'></div>";
			$cadena .= "<div class='col-md-1'><input class='form-control input-sm' name='price-$id' id='price-$id' type='number' value='".$this->input->post('precio')."'></div>";
			$cadena .= "<div class='col-md-1'><input class='form-control input-sm' name='price-$id' id='price-$id' type='number' value='".$this->input->post('precio')*$this->input->post('cantidad')."' readonly></div>";
			$cadena .= "<div class='col-md-1'><button id='button-$id' class='btn btn-danger btn-xs'>Eliminar</button></div>";
			$cadena .= "</div>";
			$cadena .= "<script type='text/javascript'>
						$(document).ready(function() {
							$('#button-$id').click(function(event) {
								$('#div-$id').remove();
							});
						});
						</script>";
			echo $cadena;
		}
	}

	function buscar_precio($id){
		if(!$this->input->is_ajax_request())
		{
			redirect('404');
		}
		else
		{
			$product	= $this->m_product->getRegistros('id_product = '.$id);
			
			foreach ($product as $row) {
				echo round($row->price, 2);
			}
		}
	}

	
	
}

