<?php
class M_Condicion_pago extends MY_Model
{
	function __construct()
	{
		$data_model = array(
		
		);
		
		parent::__construct(
					'tms_condiciones_pago', 
					'id_condicion_pago', 
					'condicion_pago', 
					'condicion_pago', 
					$data_model
				);
	}
}