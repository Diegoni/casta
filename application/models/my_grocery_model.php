<?php
class MY_grocery_Model  extends grocery_CRUD_Model{
  
	function db_update($post_array, $primary_key_value)
	{
		if ($this->field_exists('date_upd'))
		{
			$this->load->helper('date');
			$post_array['date_upd'] = date('Y-m-d H:i:s',now());
		}
	
		return parent::db_update($post_array, $primary_key_value);
	}  
  
	function db_insert($post_array)
	{
		if ($this->field_exists('date_upd') && $this->field_exists('date_add'))
		{
			$this->load->helper('date');
			$post_array['date_add'] = date('Y-m-d H:i:s',now());
			$post_array['date_upd'] = date('Y-m-d H:i:s',now());
		}
		return parent::db_insert($post_array);
	}
	
	//The function get_list is just a copy-paste from grocery_CRUD_Model
	function get_list()
	{
	 if($this->table_name === null)
	  return false;
	
	 $select = "{$this->table_name}.*";
	
  // ADD YOUR SELECT FROM JOIN HERE, for example: <------------------------------------------------------
  // $select .= ", user_log.created_date, user_log.update_date";

	 if(!empty($this->relation))
	  foreach($this->relation as $relation)
	  {
	   list($field_name , $related_table , $related_field_title) = $relation;
	   $unique_join_name = $this->_unique_join_name($field_name);
	   $unique_field_name = $this->_unique_field_name($field_name);
	  
	if(strstr($related_field_title,'{'))
		$select .= ", CONCAT('".str_replace(array('{','}'),array("',COALESCE({$unique_join_name}.",", ''),'"),str_replace("'","\\'",$related_field_title))."') as $unique_field_name";
	   else	  
		$select .= ", $unique_join_name.$related_field_title as $unique_field_name";
	  
	   if($this->field_exists($related_field_title))
		$select .= ", {$this->table_name}.$related_field_title as '{$this->table_name}.$related_field_title'";
	  }
	
	 $this->db->select($select, false);
	// $this->db->where('id_lang', 1); 
	
  // ADD YOUR JOIN HERE for example: <------------------------------------------------------
  // $this->db->join('user_log','user_log.user_id = users.id');

	 $results = $this->db->get($this->table_name)->result();
	
	 return $results;
	}
  
}