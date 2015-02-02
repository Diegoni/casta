<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	web
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Suscritos a newsletters
 *
 */
class M_newsletter extends MY_Model
{
	/**
	 * Cosntructor
	 * 
	 * @return MY_Model
	 */
	function __construct()
	{
		$data_model = array(
			'cEmail'		=> array(DATA_MODEL_REQUIRED => TRUE),		
			'nIdTema'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'mailing/tema/search')),		
		);
		parent::__construct('Web_NewsLetter', 'nIdNewsletter', 'cEmail', 'cEmail', $data_model, TRUE);
		#$this->_cache = TRUE;
	}

	/**
	 * Devuelve todos los temas a los que está suscrito el email 
	 * @param string $email Email 
	 * @return array
	 */
	function temas_email($email)
	{
		$email = $this->db->escape(trim($email));

		// Clientes
		$this->db->select('nIdTema')
		->from('Sus_Clientes_Temas')
		->where("nIdCliente IN (SELECT Cli_EMails.nIdCliente FROM Cli_EMails WHERE Cli_EMails.cEmail = {$email})");
		$r = $this->db->get();
		$data = $this->_get_results($r);

		// Web Newsletter
		$this->db->select('nIdTema')
		->from('Web_NewsLetter')
		->where("cEmail = {$email}");
		$r = $this->db->get();
		$data = array_merge($this->_get_results($r), $data);
		
		// Contactos
		$this->db->select('nIdTema')
		->from('Mailing_TemasContacto')
		->where("nIdContacto IN (
			SELECT nIdContacto FROM Mailing_EMailsContacto WHERE nIdEmail IN (
				SELECT nIdEmail FROM Gen_EMails WHERE cEMail = {$email}
				)
			)");
		$r = $this->db->get();
		$data = array_merge($this->_get_results($r), $data);
		$data2 = array();
		foreach($data as $v)
		{
			$data2[$v['nIdTema']]  = $v['nIdTema'];
		}

		return $data2;
	}
	
	/**
	 * Elimina el email de todos los lugares que se pueden eliminar
	 * @param strint $email Email
	 * @return int Número de referencias eliminadas
	 */
	function del_general($email)
	{
		$count = 0;
		$email = $this->db->escape($email);

		// Clientes
		$this->db->where("nIdCliente IN (SELECT Cli_EMails.nIdCliente FROM Cli_EMails WHERE Cli_EMails.cEmail = {$email})");
		$this->db->delete('Sus_Clientes_Temas');
		$count += $this->db->affected_rows();

		// Web Newsletter
		$this->db->where("cEmail = {$email}");
		$this->db->delete('Web_NewsLetter');
		$count += $this->db->affected_rows();
		
		// Contactos
		$this->db->where("nIdContacto IN (
			SELECT nIdContacto FROM Mailing_EMailsContacto WHERE nIdEmail IN (
				SELECT nIdEmail FROM Gen_EMails WHERE cEMail = {$email}
				)
			)");
		$this->db->from('Mailing_TemasContacto');
		$count += $this->db->affected_rows();		

		return $count;
	}
		
}

/* End of file M_newsletter.php */
/* Location: ./system/application/models/web/M_newsletter.php */