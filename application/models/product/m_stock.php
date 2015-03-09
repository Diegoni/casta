<?php
class M_Stock extends MY_Model
{
	function __construct()
	{		
		$data_model = array(
			'firme'				=> array(), 
			'pendiente'			=> array(),
			'asignado'			=> array(),
		);
		
		parent::__construct(
					'tms_stock', 
					'id_stock', 
					'id_product', 
					'id_product', 
					$data_model
				);
	}
	
	function activarNuevos()
	{
		$query = $this->db->query("SELECT 
				*
				FROM `ps_product` 
				WHERE active = 1 ");
				
		$query_stock = $this->db->query("SELECT 
				id_product 
				FROM tms_stock ");
		
		$array_fila = array();
				
		foreach ($query_stock->result() as $fila){
			$array_fila[] = $fila->id_product;
		}		
					
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				if(! in_array($fila->id_product, $array_fila))
				{
					$data =  array(
						'id_product'	=> $fila->id_product,
						'firme'			=> 0,
						'pendiente'		=> 0,
						'asignado'		=> 0,
					);
					
					$this->db->insert('tms_stock', $data);
				}
			}
		}
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */