<?php
class M_Supplier_lang extends MY_Model
{
	function __construct()
	{
		
		$data_model = array(
					'description'		=> array(),
  					'meta_title'		=> array(),
  					'meta_keywords'		=> array(),
  					'meta_description'	=> array(),
		);
		
		parent::__construct(
					'ps_supplier_lang', 
					'id_supplier', 
					'id_supplier', 
					'description', 
					$data_model
				);
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */