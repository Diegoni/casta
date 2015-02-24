<?php
class M_Supplier extends MY_Model
{
	function __construct()
	{
		
		$data_model = array(
					'name'			=> array(),
 					'date_add'		=> array(),
  					'date_upd'		=> array(),
  					'active'		=> array(),
		);
		
		parent::__construct(
					'ps_supplier', 
					'id_supplier', 
					'id_supplier', 
					'name', 
					$data_model
				);
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */