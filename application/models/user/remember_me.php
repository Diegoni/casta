<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	user
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

class Remember_me extends Model {

	var  $db_table = 'ci_remember_me_login';
	
	function __construct()
	{
		parent::Model();
		$this->obj =& get_instance();
		$this->obj->load->helper( array('cookie', 'date', 'security', 'string') );
	}

	// Test if latest schema
	function valid_db() 
	{
		return $this->db->table_exists($this->db_table);
	}

	function addRememberMe ($username)
	{
		// start by removing any current cookie before the re-issue
		$this->removeRememberMe();
		
		$random_string = random_string('alnum', 128);
		$remember_me_info = array(
			   'username' => $username,
			   'usernamehash' => dohash($username),
			   'random_string' => $random_string,
			   'origin_time' => now()
			);
			$this->db->insert($this->db_table, $remember_me_info);
			set_cookie("ci_userhash", dohash($username), $this->config->item('remember_me_life'));
			set_cookie("ci_randomstring", $random_string, $this->config->item('remember_me_life'));
	}

	function removeRememberMe ($username = false)
	{
		if(!$username)
		{
			$this->db->where('usernamehash', $this->input->cookie('ci_userhash', TRUE));
			$this->db->where('random_string', $this->input->cookie('ci_randomstring', TRUE));
			/*
			 * it is possible, although incredibly unprobable that the same user will have persistent
			 * cookies on more then 1 machine with the same randomly generated hash, so this simply
			 * ensures that only 1 is taken out.  If this ever happens, buy a lottery ticket ;)
			 */
			//$this->db->limit(1); 

			set_cookie("ci_userhash");
			set_cookie("ci_randomstring");
		}
		// removes all entries for a given username (deleted user!)
		else $this->db->where('username', $username);

		$this->db->delete($this->db_table); 
	}
	function removeOldRememberMe ()
	{
		// $this->db->use_table($this->db_table);
		// not done yet
	}
	function checkRememberMe ()
	{
		//input->cookie returns FALSE if item does not exist
		$userhash = $this->input->cookie('ci_userhash', TRUE);
		
		$random_string = $this->input->cookie('ci_randomstring', TRUE);
		
		if (!empty($userhash) && !empty($random_string)) {
		
			log_message('debug','Has Remember Me Cookie');
			// test if mini-app db schema installed
			
			if ( ! $this->valid_db() ) { return FALSE; }
			
			
			
			$this->db->where ('usernamehash', $userhash);
			
			$this->db->where ('random_string', $random_string);
			
			$result = $this->db->get($this->db_table);
			
			
			
			if ($result != FALSE && $result->num_rows() > 0) {
			
				$result = $result->row();
				
				$expire_time = time() - $this->obj->config->item('remember_me_life');
				
				if ( $result->origin_time <=  $expire_time )
				{
					$this->removeRememberMe();
					return FALSE;
				}
				else
				{
					return $result->username;
				}
				
				} else { return FALSE; }
		
		
		
		} else { return FALSE; }
	
	}
}

?>