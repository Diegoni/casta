<?php
class M_Bank extends MY_Model
{
	function __construct()
	{
		$data_model = array(
		
		);
		
		parent::__construct(
					'tms_bank', 
					'id_bank', 
					'name', 
					'name', 
					$data_model
				);
	}
}