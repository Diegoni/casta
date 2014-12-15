<?php
Class User extends CI_Model
{
 function login($username, $password)
 {
   $this -> db -> select('id_usuario, usuario, pass');
   $this -> db -> from('usuarios');
   $this -> db -> where('usuario', $username);
   $this -> db -> where('pass', MD5($password));
   $this -> db -> limit(1);

   $query = $this -> db -> get();

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
?>
