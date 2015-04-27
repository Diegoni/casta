<?php
class M_Forma_pago extends MY_Model
{
	function __construct()
	{
		$data_model = array(
		
		);
		
		parent::__construct(
					'tms_formas_pago', 
					'id_forma_pago', 
					'forma_pago', 
					'forma_pago', 
					$data_model
				);
	}
}