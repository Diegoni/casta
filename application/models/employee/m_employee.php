<?php
class M_Employee extends MY_Model
{
	function __construct()
	{
		
		$data_model = array(
			'id_profile'		=> array(), 
			'id_lang'			=> array(),
			'lastname'			=> array(),
			'firstname'			=> array(),
			'email'				=> array(),
		);
		
		parent::__construct(
					'ps_employee', 
					'id_employee', 
					'firstname, lastname', 
					array('firstname', 'lastname'), 
					$data_model
				);
	}
	
	function login($username, $password)
	{
		$this->db->select('id_employee, firstname, passwd');
		$this->db->from('ps_employee');
		$this->db->where('firstname', $username);
		$this->db->where('passwd', MD5(_COOKIE_KEY_.$password));
		$this->db->limit(1);

		$query=$this->db->get();

		if($query -> num_rows() == 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */