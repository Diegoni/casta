<?php
class M_Tax extends MY_Model
{
	function __construct()
	{
		$data_model = array(
		);
		
		parent::__construct(
					'ps_tax', 
					'id_tax', 
					'descripcion', 
					'descripcion', 
					$data_model
				);
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */