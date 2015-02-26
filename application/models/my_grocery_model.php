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
  
}