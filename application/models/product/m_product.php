<?php
class M_Product extends MY_Model
{
	function __construct()
	{
		
		$data_model = array(
			'id_category_default'		=> array(), 
			'id_shop_default'			=> array(),
			'minimal_quantity'			=> array(),
			'price'						=> array(),
			'reference'					=> array(),
			'out_of_stock'				=> array(),
			'show_price'				=> array(),
			'active'					=> array(),
		);
		
		parent::__construct(
					'ps_product', 
					'id_product', 
					'id_product', 
					'reference', 
					$data_model
				);
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */