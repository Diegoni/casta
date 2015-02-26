<?php
class MY_Controller extends CI_Controller
{
	
	protected $model; 
	protected $check_loged; 
	protected $index_view; 
	protected $title; 
	protected $submenu;
	
	public function __construct($model = null,$check_loged = FALSE,	$title = null,$submenu = null)
	{
		parent::__construct();
		$this->submenu = $submenu;
		$this->load->model($model,'reg');
    }
	
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
					$name = $row->name;
				}
				
			}	
		}
		
		return $name;
	}
  
}
