<?php
class M_Currency extends MY_Model
{
	function __construct()
	{
		$data_model = array(
		
		);
		
		parent::__construct(
					'ps_currency', 
					'id_currency', 
					'name', 
					'name', 
					$data_model
				);
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */