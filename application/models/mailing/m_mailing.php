<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	mailing
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Mailing
 *
 */
class M_Mailing extends MY_Model
{

	/**
	 * Costructor
	 * @return M_Mailing
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE),
			'cEMailAddress'		=> array(),		
			'cAsunto'			=> array(),		
			'bAutenticacion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),		
			'cSMTP'				=> array(),
			'cUser'				=> array(),		
			'cPassword'			=> array(),		
			'cBody'				=> array(DATA_MODEL_NO_LIST => TRUE),
		//'cSQL'				=> array()
		);

		parent::__construct('Mailings', 'nIdMailing', 'cDescripcion', array('cDescripcion', 'cAsunto'), $data_model, TRUE);
		$this->load->model('mailing/M_Mailingemail', 'mm');
		#$this->_cache = TRUE;
		
		$this->_relations['emails'] = array (
			'ref'		=> 'mailing/m_mailingemail',
			'type'		=> DATA_MODEL_RELATION_1N,
			'fk'		=> 'nIdMailing',		
			'cascade'	=> TRUE);		
	}

	/**
	 * Devuelve todos los emails del maling
	 *
	 * @param array $datos del email
	 * @return array
	 */
	function get_emails($id)
	{
		$data = $this->mm->get(null,null,null,null, "nIdMailing = {$id} AND nIdEstado = 1");
		return $data;
	}

	/**
	 * Marca un emails como enviado
	 * @param $id
	 * @return unknown_type
	 */
	function sended($id)
	{
		$data['nIdEstado'] = MAILING_STATE_SENDED;
		$data['dEnvio'] = time();
		$data['cEstado'] = $this->lang->line('send-mail-enviado-ok');
		$this->mm->update($id, $data);
	}

	/**
	 * Marca un email como erróneo
	 * @param $id
	 * @param $msg
	 * @return unknown_type
	 */
	function error($id, $msg)
	{
		$data['nIdEstado'] = MAILING_STATE_ERROR;
		$data['cEstado'] = $msg;
		$this->mm->update($id, $data);
	}

	function process($id)
	{
		$this->mm->process($id);
	}

	/**
	 * Añade los emails de un tema
	 * @param $id
	 * @param $idtema
	 * @return unknown_type
	 */
	function add_tema($id, $idtema)
	{
		return $this->mm->add_tema($id, $idtema);
	}

	
	/**
	 * Añade todos los emails del sistema
	 * @param $id
	 * @return unknown_type
	 */
	function add_todos($id)
	{
		return $this->mm->add_todos($id);
	}

	/**
	 * Restea los emails enviados por error
	 * @param int $id Id del email-mailing
	 * @return int
	 */
	function reset($id)
	{
		//$obj = get_instance();
		$data = $this->mm->get(null,null,null,null, "nIdMailing = {$id} AND nIdEstado IN (" . MAILING_STATE_ERROR . ', ' . MAILING_STATE_PROCESS . ')');
		$count = 0;
		foreach($data as $d)
		{
			$id = $d['id'];
			$data['nIdEstado'] = MAILING_STATE_NORMAL;
			$data['cEstado'] = $this->lang->line('mailing-pendiente-reenvio');
			$this->mm->update($id, $data);
			$count++;
		}
		return $count;
	}

	/**
	 * Elimina los emails
	 * @param int $id Id del email-mailing
	 * @return int
	 */
	function del_emails($id)
	{
		//$obj = get_instance();
		return $this->mm->delete_by("nIdMailing = {$id}");
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

		// Clientes antiguo
		$this->db->flush_cache();
		$data['bNoEmail'] = 1;
		$this->_audit_upd($data);
		$this->db->where("nIdCliente IN (SELECT nIdCliente FROM Cli_EMails WHERE cEMail = {$email})");
		$this->db->update('Cli_Clientes', $data);
		$count += $this->db->affected_rows();

		// Contactos
		$this->db->flush_cache();
		$data['bNoEmail'] = 1;
		$this->_audit_upd($data);
		$this->db->where("nIdContacto IN (
			SELECT nIdContacto FROM Mailing_EMailsContacto WHERE nIdEmail IN (
				SELECT nIdEmail FROM Gen_EMails WHERE cEMail = {$email}
				)
			)");
		$this->db->update('Mailing_Contactos', $data);
		$count += $this->db->affected_rows();

		// Suscripciones Internet
		$this->db->flush_cache();
		$this->db->where("cEmail = {$email}");
		$this->db->delete('Web_Newsletter');
		$count += $this->db->affected_rows();

		return $count;
	}

	/**
	 * Busca todos los mailings donde se ha utilizado el email indicado 
	 * @param string/array $email Email 
	 * @return array
	 */
	function sended_email($email)
	{
		// Clientes antiguo
		$this->db->select('m2.nIdMailing, m2.nIdEstado, m2.cEstado, m2.cOrigen, m1.cDescripcion, m2.cEmail')
		->select($this->_date_field('m2.dEnvio', 'dEnvio'))
		->from($this->_tablename .' m1')
		->join($this->mm->get_tablename() . ' m2', 'm1.nIdMailing = m2.nIdMailing')
		->order_by('m2.dEnvio');

		if (is_array($email))
		{
			foreach ($email as $key => $value) 
			{
				$email[$key] = $this->db->escape(trim($value));
			}
			$email = implode(',', $email);
			$this->db->where("m2.cEmail IN ({$email})");
		}
		else
		{
			$email = $this->db->escape(trim($email));
			$this->db->where("m2.cEmail = {$email}");
		}

		$r = $this->db->get();
		$data = $this->_get_results($r);

		return $data;
	}
}

/* End of file M_mailing.php */
/* Location: ./system/application/models/mailling/m_mailing.php */