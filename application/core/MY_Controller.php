<?php
class MY_Controller extends CI_Controller
{
	
	protected $model; 
	protected $check_loged; 
	protected $index_view; 
	protected $title; 
	protected $view;
	
	public function __construct($model = null,$check_loged = FALSE,	$title = null, $view = null)
	{
		parent::__construct();
		$this->load->model($model,'reg');
    }


/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Busca la correspondiente traduccion
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/

	
	function _lang($value, $row) 
	{
		$table		= $this->reg->getTable();
		$table		= $table.'_lang';
		$id_table	= $this->reg->getId_table();
		$id			= $row->$id_table;
		
		if ($this->db->table_exists($table))
		{
			$condicion = $this->reg->addCondicion(NULL, $table);
			$query = $this->db->query("SELECT * FROM $table $condicion AND $table.$id_table = $id");
			
			if($query->num_rows() > 0){	
				foreach ($query->result() as $row){
					if(isset($row->name))
					{
						$name = $row->name;	
					}	
					if(isset($row->value))
					{
						$name = $row->value;	
					}
				}
				
			}	
		}
		
		return $name;
	}
	

/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Devuelve la vista de la traducción
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/
	
	
	function vista_traduccion($primary_key , $row)
	{
		$table		= $this->reg->getTable();
		$table		= substr($table, 3);
	    return site_url($this->view.'/'.$table.'/crud_'.$table.'_lang/'.$primary_key);
	}


/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Devuelve la vista de la traducción
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/
	

	function insert_lang($post_array)
	{
		$name = $post_array['name'];
		unset($post_array['name']);
		
		if ($this->db->field_exists('date_add', $this->reg->getTable()))
		{
			$this->load->helper('date');
			$post_array['date_add'] = date('Y-m-d H:i:s',now());
		}
		
		$this->db->insert($this->reg->getTable(), $post_array);
		
		$id = $this->db->insert_id();
		
		$table_lang = $this->reg->getTable().'_lang';
		
		$query = $this->db->query("SELECT * FROM ps_lang");
							

		if ($this->db->field_exists('name', $table_lang))
		{
			$table_name = 'name';
		}
		else
		{
			$table_name = 'value';
		}
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $row){
					
				$data = array(
			        $this->reg->getId_table() => $id,
			        "id_lang"		=> $row->id_lang,
			        $table_name		=> $name
		    	);
				
				$this->db->insert($table_lang, $data);
			}
		}
				
		return TRUE;
	}		


/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Cambia la forma de delete de Grocery Crud
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/
	
	
	function delete_reg($primary_key_value)
	{
		$table		= $this->reg->getTable();
		$id_table	= $this->reg->getId_table();
		
		$delete_array['active']	= '0';
		
		if ($this->db->field_exists('date_upd', $table))
		{
			$this->load->helper('date');
			$delete_array['date_upd'] = date('Y-m-d H:i:s',now());
		}
		
    	return $this->db->update($table, $delete_array, array($id_table => $primary_key_value));
	}
			
	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Salida del Crud 
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/
	
	
	function _crud_output($output = null, $titulo = NULL)
	{
		if($titulo != NULL)
		{
			$db['titulo']	= $this->title." : ".$titulo;	
		}
		else
		{
			$db['titulo']	= $this->title;
		}
		
		$this->load->view('head', $db);
		$this->load->view('menu');
		$this->load->view($this->view.'/crud',$output);
		$this->load->view('footer');
	}	

	
/*----------------------------------------------------------------------------------
------------------------------------------------------------------------------------
			Salida del Crud 
------------------------------------------------------------------------------------
----------------------------------------------------------------------------------*/
	

	function buscar_select($datos)
	{
		$datos['table'];
		$query = $this->db->query("SELECT *, $datos[id] as id_table FROM $datos[table] WHERE $datos[table].id_lang=1");
		
		$input ="<SELECT id='' name='".$datos['name']."' class='chosen-select form-control'>";
			
		if($query->num_rows() > 0){	
			foreach ($query->result() as $row){
				if($row->id_table==$datos['value'])
				{
					$option = 'selected';	
				}
				else 
				{
					$option = '';
				}
				
				$input .= "<option ".$option." value='".$row->id_table	."'>".$row->name."</option>";
			}
			
		}			
		
		$input .="</SELECT>";
		
		return $input;
	}
}