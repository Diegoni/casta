<?php
class M_Group extends MY_Model
{
	function __construct()
	{
		
		$data_model = array(
			'reduction'			=> array(), 
		);
		
		parent::__construct(
					'ps_group', 
					'id_group', 
					'reduction', 
					'id_group',  
					$data_model
		);
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */