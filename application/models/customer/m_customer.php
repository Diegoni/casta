<?php
class M_Customer extends MY_Model
{
	function __construct()
	{
		
		$data_model = array(
			'firstname'			=> array(), 
			'lastname'			=> array(),
			'company'			=> array(),
			'cuil'				=> array(),
			'id_default_group'	=> array(),
			'active'			=> array(DATA_MODEL_TYPE_BOOLEAN => TRUE),
			'newsletter'		=> array(),
			'optin'				=> array(),
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