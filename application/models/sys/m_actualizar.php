<?php
class M_Actualizar extends CI_Model
{
	function __construct()
	{	
		parent::__construct();
	}
	
	function actualizar($tables)
	{
		$suma = 0;
		
		foreach ($tables as $key => $value) {
			$table		= 'ps_'.$value;
			$table_lang = $table.'_lang';
			$id_table	= 'id_'.$value;
			
			$query = $this->db->query("SELECT $id_table as id_table, descripcion FROM $table");
				
			if($query->num_rows() > 0){	
				foreach ($query->result() as $row){
					if($row->descripcion=="")
					{
						$query_lang = $this->db->query("SELECT * FROM $table_lang WHERE $id_table = $row->id_table AND id_lang = 1");
						if($query_lang->num_rows() > 0){
							foreach ($query_lang->result() as $row_lang){
								if(isset($row_lang->name))
								{
									$descripcion = $row_lang->name;	
								}
								else
								{
									$descripcion = $row_lang->value."<br>";
								}	
							}
							
							$datos = array('descripcion' => $descripcion);
							$this->db->update($table, $datos, $id_table." = ".$row->id_table);
						}
						
						$suma = $suma + 1;
					}
				}
			}
		}
		
		return $suma;
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */