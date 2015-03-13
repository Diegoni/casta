<?php
class Customer extends MY_Controller
{
	protected $model		= 'customer/m_customer';
	protected $check_loged	= FALSE; 
	protected $title		= 'Clientes'; 
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
	function crud_customer_faster()
	{
		$db['grupos']			= $this->m_group_lang->getSelect();
		
		if($this->input->post('b_codigo'))
		{
			$db['b_clientes']	= $this->m_customer->getID($this->input->post('b_codigo'), TRUE);
			if($db['b_clientes']==null)//Aseguramos que el codigo exista
			{
				unset($db['b_clientes']);
				$db['mensaje']	= $this->m_customer->getMensaje('Busqueda', 'error', $this->input->post('b_codigo'));
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
		
		if($this->input->post('guardar'))
		{
			if($this->input->post('id_customer'))//update
			{
				$id = $this->input->post('id_customer');
				$this->m_customer->update($id);
				$db['mensaje']	= $this->m_customer->getMensaje('Modificacion', 'ok', $id);
			}
			else
			{
				$id	= $this->m_customer->insert();
				if($id>0){
					$db['mensaje']	= $this->m_customer->getMensaje('Alta', 'ok', $id);
				}
				else
				{
					$db['mensaje']	= $this->m_customer->getMensaje('Alta', 'error', $id);
				}	
			}
		}
		
		$db['clientes']			= $this->m_customer->getRegistros();
		$db['clientes_model']	= $this->m_customer->getData_model();
		
		$this->load->view('head', $db);
		$this->load->view('menu');
		$this->load->view('customer/crud_customer');
		$this->load->view('footer');		
	}


/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de clientes
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	function crud_customer()
	{
		
		$crud = new grocery_CRUD();

		$crud->where(array('ps_customer.active' => 1));
							
		$crud->set_table('ps_customer');
		
		$crud->set_subject($this->title);
				
		$crud->columns('id_customer', 'firstname', 'lastname', 'email', 'id_gender');
				
		$crud->display_as('id_customer', 'ID')
			 ->display_as('date_add',	$this->lang->line("fecha")." ".$this->lang->line("alta"))
			 ->display_as('firstname',	$this->lang->line("nombre"))
			 ->display_as('lastname',	$this->lang->line("apellido"))
			 ->display_as('email',		$this->lang->line("emails"))
			 ->display_as('id_gender',	$this->lang->line("genero"))
			 ->display_as('id_default_group',	$this->lang->line("grupo"))
			 ->display_as('id_lang',	$this->lang->line("idioma"))
			 ->display_as('id_risk',	$this->lang->line("riesgo"))
			 ->display_as('company',	$this->lang->line("empresa"))
			 ->display_as('last_passwd_gen',	$this->lang->line("ultimo")." ".$this->lang->line("cambio")." ".$this->lang->line("pass"))
			 ->display_as('birthday',	$this->lang->line("cumpleaños"))
			 ->display_as('newsletter',	$this->lang->line("boletin"))
			 ->display_as('newsletter_date_add',	$this->lang->line("boletin")." ".$this->lang->line("alta"))
			 ->display_as('outstanding_allow_amount',	$this->lang->line("saldo")." ".$this->lang->line("pendiente"))
			 ->display_as('max_payment_days',	$this->lang->line("maximo")." ".$this->lang->line("dias")." ".$this->lang->line("pago"))
			 ->display_as('note',		$this->lang->line("nota"))
			 ->display_as('optin',		$this->lang->line("anuncios"))
			 ->display_as('date_upd',	$this->lang->line("fecha")." ".$this->lang->line("modificacion"));
		
		$crud->field_type('date_add', 'readonly');
		$crud->field_type('date_upd', 'readonly');
		$crud->field_type('last_passwd_gen', 'readonly');
		$crud->field_type('newsletter_date_add', 'readonly');
		$crud->field_type('outstanding_allow_amount', 'readonly');
				
		$crud->field_type('active',		'hidden');
		$crud->field_type('id_lang',	'hidden');
		$crud->field_type('id_shop_group', 'hidden');
		$crud->field_type('id_shop', 'hidden');
		$crud->field_type('siret', 'hidden');
		$crud->field_type('ape', 'hidden');
		$crud->field_type('passwd', 'hidden');
		$crud->field_type('ip_registration_newsletter', 'hidden');
		$crud->field_type('website', 'hidden');
		$crud->field_type('show_public_prices', 'hidden');
		$crud->field_type('secure_key', 'hidden');		
		$crud->field_type('is_guest', 'hidden');
		$crud->field_type('deleted', 'hidden');
								
		$crud->set_relation('id_gender', 'ps_gender', 'descripcion');
		$crud->set_relation('id_risk', 'ps_risk', 'descripcion');
		$crud->set_relation('id_default_group', 'ps_group', 'descripcion');
		
		$crud->callback_delete(array($this,'delete_reg'));
		
		$output = $crud->render();
		
		$this->_crud_output($output);

	}
}

/* End of file cliente.php */
/* Location: ./system/application/controllers/clientes/cliente.php */