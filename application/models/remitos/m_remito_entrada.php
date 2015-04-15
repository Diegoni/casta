<?php
class M_Remito_entrada extends MY_Model
{
	function __construct()
	{
		
		$data_model = array(
		);
		
		parent::__construct(
					'ps_customer', 
					'id_customer', 
					'firstname, lastname', 
					array('firstname', 'lastname'), 
					$data_model
				);
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */