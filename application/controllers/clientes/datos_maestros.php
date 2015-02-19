<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	clientes
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador de clientes
 *
 */
class Datos_maestros extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Cliente
	 */
	function __construct()
	{
		//parent::__construct('clientes.cliente', 'clientes/M_cliente', TRUE, 'clientes/cliente.js', 'Clientes');
		parent::__construct();
		
		$this->load->library('userauth');
		$this->load->library('grocery_CRUD');
		$this->load->library('out');
		
		$this->load->helpers('vistas');
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
		$this->load->view('clientes/crud.php',$output);
		$this->load->view('footer');
	}	

	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de tipos clientes
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	public function crud_cli_tiposcliente()
	{
		$crud = new grocery_CRUD();

		$crud->set_table('cli_tiposcliente');
		$crud->set_subject('Tipos');
		$crud->columns('nIdTipoCliente','cDescripcion','nIdTipoTarifa');
		
		$crud->display_as('nIdTipoCliente','ID')
			 ->display_as('cDescripcion','Descripción')
			 ->display_as('bProtegido','Protegido')
			 ->display_as('nCuenta','Nro Cuenta')
			 ->display_as('nIdModoCobro','Modo corbro')
			 ->display_as('nIdTipoTarifa','Tipo Tarifa');
		
		$crud->field_type('delete', 'hidden');
			
		$output = $crud->render();

		$this->_crud_output($output);
	}

	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de estado cliente
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	public function crud_cli_estadoscliente()
	{
		$crud = new grocery_CRUD();

		$crud->set_table('cli_estadoscliente');
		$crud->set_subject('Estados');
		$crud->columns('nIdEstado','cDescripcion','bProtegido');
		
		$crud->display_as('nIdEstado','ID')
			 ->display_as('cDescripcion','Descripción')
			 ->display_as('bProtegido','Protegido');
		
		$crud->field_type('delete', 'hidden');
			
		$output = $crud->render();

		$this->_crud_output($output);
	}

	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de grupos clientes
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	public function crud_cli_gruposcliente()
	{
		$crud = new grocery_CRUD();

		$crud->set_table('cli_gruposcliente');
		$crud->set_subject('Grupos');
		$crud->columns('nIdGrupoCliente','cDescripcion');
		
		$crud->display_as('nIdGrupoCliente','ID')
			 ->display_as('cDescripcion','Descripción');
		
		$crud->field_type('delete', 'hidden');
			
		$output = $crud->render();

		$this->_crud_output($output);
	}	

	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Crud de grupos clientes
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/


	public function crud_gen_tratamientos()
	{
		$crud = new grocery_CRUD();

		$crud->set_table('gen_tratamientos');
		$crud->set_subject('Tratamientos');
		$crud->columns('nIdTratamiento','cDescripcion');
		
		$crud->display_as('nIdTratamiento','ID')
			 ->display_as('cDescripcion','Descripción');
		
		//$crud->field_type('delete', 'hidden');
			
		$output = $crud->render();

		$this->_crud_output($output);
	}
}

/* End of file cliente.php */
/* Location: ./system/application/controllers/clientes/cliente.php */