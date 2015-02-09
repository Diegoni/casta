<?php 
/**
 * Casta
 *
 * Gestión de librerías
 *
 * @package		1.1
 * @subpackage	Models
 * @category	clientes
 * @author		Diego Nieto
 * @copyright	Copyright (c) 2015
 * @link		https://github.com/Diegoni/casta
 * @since		Version 1.1
 * @version		$Rev:  $
 * @filesource
 */

/**
 * Idiomas
 *
 */
class M_Idiomas extends MY_Model {
	/**
	 * Costructor 
	 * @return M_Contacto
	 */
	
	function __construct()
	{
		$data_model = array(
			
		);

		parent::__construct(
					'gen_idiomas', 
					'nIdIdioma', 
					'cNombre', 
					'cNombre', 
					$data_model);	
	}
	
	function getIdioma($url){
		/*	
		$id		= $this->config->item('idioma');
		
		$query	= $this->db->query("SELECT * FROM idiomas WHERE idiomas.url='$url'");
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
		}else{
			return FALSE;
		}
		
		foreach ($data as $idioma) {
			include_once('idiomas/'.$idioma->archivo);	
		}
		 
		*/
		include_once('idiomas/spanish.php');
		
		return $texto;
	}
}
