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
 * Estados de envío NORMAL
 * @var int
 */
define('MAILING_STATE_NORMAL', 	1);
/**
 * Estados de envío ENVIADO
 * @var int
 */
define('MAILING_STATE_SENDED', 	2);
/**
 * Estados de envío ERROR
 * @var int
 */
define('MAILING_STATE_ERROR', 	3);
/**
 * Estados de envío en proceso
 * @var int
 */
define('MAILING_STATE_PROCESS', 4);


/**
 * Mailings Email
 *
 */
class M_Mailingemail extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Mailingemail
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'cEmail'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),		
			'nIdMailing'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'mailing/mailing/search')),
			'dEnvio'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME, DATA_MODEL_READONLY => TRUE),
			'nIdEstado'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => MAILING_STATE_NORMAL, DATA_MODEL_READONLY => TRUE),		
			'cEstado'		=> array(DATA_MODEL_DEFAULT_VALUE => $obj->lang->line('send-mailing-default-state'), DATA_MODEL_READONLY => TRUE),		
			'cOrigen'		=> array(DATA_MODEL_READONLY => TRUE),		
		);
		#$this->_cache = TRUE;

		parent::__construct('Mailing_Emails', 'nIdEmail', 'nIdEmail', 'nIdEmail', $data_model, TRUE);
	}

	/**
	 * Comprueba que un email sea único
	 * @param array $data Datos a añadir/modificar
	 * @return bool TRUE: no está duplicado, FALSE: está duplicado
	 */
	private function _check_email($data)
	{
		if (isset($data['cEmail']))
		{
			$email = $this->db->escape($data['cEmail']);
			$where = "cEmail = {$email} AND nIdMailing = {$data['nIdMailing']}";
			if (isset($data['nIdEmail']))
			{
				$where .= " AND nIdEmail <> {$data['nIdEmail']}";
			}
			$data = $this->get(null, null, null, null, $where);
			if ($this->get_count()>0)
			{
				$this->_set_error_message($this->lang->line('mailing-email-duplicate'));
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert($data)
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			return $this->_check_email($data);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($id, $data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			if (isset($data['cEmail']))
			{
				$data2 = $this->load($id);
				$data2['cEmail'] = $data['cEmail'];
				return $this->_check_email($data2);
			}
			return TRUE;
		}
	}

	/**
	 * Añade todos los emails de un tema
	 * @param int $id Id del mailing
	 * @param int $idtema Id del tema
	 */
	function add_tema($id, $idtema)
	{
		$obj = get_instance();
		$count = 0;

		$text = $this->db->escape($obj->lang->line('send-mailing-tema-state-cliente'));
		$user = $this->db->escape($this->userauth->get_username());
		$time = $this->db->escape($this->_todate(time()));
		//$time = format_mssql_date(time());
		$sql = "INSERT INTO Mailing_Emails(nIdMailing, cEmail, nIdEstado, cOrigen, cCUser, cAUser, dCreacion, dAct)
			SELECT {$id}, RTRIM(LTRIM(E.cEMail)), 1, {$text}, {$user}, {$user}, {$time}, {$time} 
			FROM  Cli_Clientes  C (NOLOCK)
			INNER JOIN Cli_EMails E (NOLOCK)
				ON E.nIdCliente = C.nIdCliente   
			INNER JOIN Sus_Clientes_Temas tc (NOLOCK)
				ON tc.nIdCliente = c.nIdCliente AND nIdTema = {$idtema}
			WHERE e.cEMail IS NOT NULL
				AND ISNULL(c.bNoEmail, 0) = 0
				AND E.cEMail NOT IN (SELECT cEMail FROM Mailing_Emails WHERE nIdMailing = {$id})
			GROUP BY E.cEMail";

		$this->db->query($sql);
		$count += $this->db->affected_rows();

		$text = $this->db->escape($obj->lang->line('send-mailing-tema-state-contacto'));

		$sql = "INSERT INTO Mailing_Emails(nIdMailing, cEmail, nIdEstado, cOrigen, cCUser, cAUser, dCreacion, dAct)
			SELECT {$id}, RTRIM(LTRIM(e.cEMail)), 1, {$text}, {$user}, {$user}, {$time}, {$time} 
			FROM  Mailing_Contactos  c (NOLOCK)
			INNER JOIN Mailing_EMailsContacto em (NOLOCK) 
				ON em.nIdContacto = c.nIdContacto
			INNER JOIN Gen_EMails e (NOLOCK)
				ON em.nIdEmail = e.nIdEmail   
			INNER JOIN Mailing_TemasContacto tc (NOLOCK)
				ON tc.nIdContacto = c.nIdContacto AND nIdTema = {$idtema}
			WHERE e.cEMail IS NOT NULL
				AND ISNULL(c.bNoEmail, 0) = 0
				AND e.cEMail NOT IN (SELECT cEMail FROM Mailing_Emails WHERE nIdMailing = {$id})
			GROUP BY e.cEMail";

		$this->db->query($sql);
		$count += $this->db->affected_rows();

		$text = $this->db->escape($obj->lang->line('send-mailing-tema-state-web'));

		$sql = "INSERT INTO Mailing_Emails(nIdMailing, cEmail, nIdEstado, cOrigen, cCUser, cAUser, dCreacion, dAct)
			SELECT {$id}, LTRIM(RTRIM(cEMail)), 1, {$text}, {$user}, {$user}, {$time}, {$time} 
			FROM Web_NewsLetter (NOLOCK)
			WHERE nIdTema = {$idtema}
			AND cEmail IS NOT NULL
			AND cEmail <> ''
			AND cEmail NOT IN (SELECT cEMail FROM Mailing_Emails WHERE nIdMailing = {$id})
			GROUP BY cEmail";
		$this->db->query($sql);
		$count += $this->db->affected_rows();

		if ($count > 0)	$this->clear_cache();

		return $count;
	}

	/**
	 * Añade todos los emails del sistema
	 * @param int $id Id del mailing
	 */
	function add_todos($id)
	{
		$obj = get_instance();
		$count = 0;

		$text = $this->db->escape($obj->lang->line('send-mailing-todos-state-cliente'));
		$user = $this->db->escape($this->userauth->get_username());
		$time = $this->db->escape($this->_todate(time()));
		$idestado = MAILING_STATE_NORMAL;

		$sql = "INSERT INTO Mailing_Emails(nIdMailing, cEmail, nIdEstado, cOrigen, cCUser, cAUser, dCreacion, dAct)
			SELECT {$id}, RTRIM(LTRIM(E.cEMail)), {$idestado}, {$text}, {$user}, {$user}, {$time}, {$time} 
			FROM  Cli_Clientes  c
			INNER JOIN Cli_EMails E 
				ON E.nIdCliente = c.nIdCliente   
			INNER JOIN Sus_Clientes_Temas tc 
				ON tc.nIdCliente = c.nIdCliente
			WHERE E.cEMail IS NOT NULL
				AND ISNULL(c.bNoEmail, 0) = 0
				AND E.cEMail NOT IN (SELECT cEMail FROM Mailing_Emails WHERE nIdMailing = {$id})
			GROUP BY E.cEMail";
		$this->db->query($sql);
		$count += $this->db->affected_rows();

		$text = $this->db->escape($obj->lang->line('send-mailing-todos-state-web'));

		$sql = "INSERT INTO Mailing_Emails(nIdMailing, cEmail, nIdEstado, cOrigen, cCUser, cAUser, dCreacion, dAct)
			SELECT {$id}, RTRIM(LTRIM(cEMail)), {$idestado}, {$text}, {$user}, {$user}, {$time}, {$time} 
			FROM Web_NewsLetter
			WHERE cEmail IS NOT NULL
			AND cEmail <> ''
			AND cEmail NOT IN (SELECT cEMail FROM Mailing_Emails WHERE nIdMailing = {$id})
			GROUP BY cEmail";
		$this->db->query($sql);
		$count += $this->db->affected_rows();

		$text = $this->db->escape($obj->lang->line('send-mailing-todos-state-contacto'));
		$sql = "INSERT INTO Mailing_Emails(nIdMailing, cEmail, nIdEstado, cOrigen, cCUser, cAUser, dCreacion, dAct)
			SELECT {$id}, RTRIM(LTRIM(e.cEMail)), 1, {$text}, {$user}, {$user}, {$time}, {$time} 
			FROM  Mailing_Contactos  c (NOLOCK)
			INNER JOIN Mailing_EMailsContacto em (NOLOCK) 
				ON em.nIdContacto = c.nIdContacto
			INNER JOIN Gen_EMails e (NOLOCK)
				ON em.nIdEmail = e.nIdEmail   
			WHERE e.cEMail IS NOT NULL
				AND ISNULL(c.bNoEmail, 0) = 0
				AND e.cEMail NOT IN (SELECT cEMail FROM Mailing_Emails WHERE nIdMailing = {$id})
			GROUP BY e.cEMail";
		$this->db->query($sql);
		$count += $this->db->affected_rows();

		$text = $this->db->escape($obj->lang->line('send-mailing-tema-state-web'));

		if ($count > 0)	$this->clear_cache();
		return $count;
	}

	/**
	 * Marca los emails del mailing como que están en proceso
	 * @param int $id Id Mailing
	 */
	function process($id)
	{
		$data = array();
		$this->_audit_upd($data);
		$data = $this->_filtra_datos($data, FALSE);
		$data['nIdEstado'] = MAILING_STATE_PROCESS;

		$this->db->flush_cache();
		$this->db->where("nIdMailing = {$id}");
		$this->db->where('nIdEstado = ' . MAILING_STATE_NORMAL);
		$this->db->update($this->_tablename, $data);

		$this->clear_cache();
	}
}

/* End of file M_Mailingemail.php */
/* Location: ./system/application/models/mailing/M_Mailingemail.php */