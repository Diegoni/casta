<?php
class M_Product_lang extends MY_Model
{
	function __construct()
	{
		
		$data_model = array(
			  'description'			=> array(),
			  'description_short'	=> array(),
			  'link_rewrite'		=> array(),
			  'meta_description'	=> array(),
			  'meta_keywords'		=> array(),
			  'meta_title'			=> array(),
			  'name'				=> array(),
			  'available_now'		=> array(),
			  'available_later'		=> array(),
		);
		
		parent::__construct(
					'ps_product_lang', 
					'id_product', 
					'id_product', 
					'name', 
					$data_model
				);
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */