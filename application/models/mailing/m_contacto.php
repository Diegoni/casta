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
 * Contactos para mailings
 *
 */
class M_Contacto extends MY_Model
{
	/**
	 * Añadir el email a las búsquedas
	 * @var bool
	 */
	public $_addemail = FALSE;

	/**
	 * Costructor
	 * @return M_Contacto
	 */
	function __construct()
	{
		$data_model = array(
				'cNombre' => array(),
				'cApellido' => array(),
				'cEmpresa' => array(DATA_MODEL_DEFAULT => TRUE),
				'cNIF' => array(),
				'cWebPage' => array(),
				'nIdTipoCliente' => array(
						DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT,
						DATA_MODEL_EDITOR => array(
								DATA_MODEL_EDITOR_COMBO,
								'mailing/tipocliente/search'
						)
				),
				'nIdTratamiento' => array(
						DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT,
						DATA_MODEL_EDITOR => array(
								DATA_MODEL_EDITOR_COMBO,
								'clientes/tratamiento/search'
						)
				),
				'nIdGrupoCliente' => array(
						DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT,
						DATA_MODEL_EDITOR => array(
								DATA_MODEL_EDITOR_COMBO,
								'mailing/grupocliente/search'
						)
				),
				'bNoEmail' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
				'bNoCarta' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
				'cIdioma' => array(),
		);

		parent::__construct('Mailing_Contactos', 'nIdContacto', 'cNombre, cApellido, cEmpresa', array(
				'cNombre',
				'cApellido',
				'cEmpresa'
		), $data_model, true);
		$this->_cache = TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir,
	 * $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Mailing_TiposCliente.cDescripcion cTipoCliente')->select('Mailing_GruposCliente.cDescripcion cGrupoCliente')->join('Mailing_TiposCliente', 'Mailing_TiposCliente.nIdTipoCliente=Mailing_Contactos.nIdTipoCliente', 'left')->join('Mailing_GruposCliente', 'Mailing_GruposCliente.nIdGrupoCliente=Mailing_Contactos.nIdGrupoCliente', 'left');
			if ($this->_addemail)
			{
				$this->db->select('Mailing_Contactos.*, Gen_EMails.cEmail, Gen_TiposPerfil.cDescripcion cPerfil')->join('Mailing_EMailsContacto', 'Mailing_EMailsContacto.nIdContacto = Mailing_Contactos.nIdContacto')->join('Gen_EMails', 'Mailing_EMailsContacto.nIdEmail = Gen_EMails.nIdEmail')->join('Gen_TiposPerfil', 'Gen_TiposPerfil.nIdTipo = Gen_EMails.nIdTipo');
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSearch($where, $fields)
	 */
	protected function onBeforeSearch($query, &$where, &$fields)
	{
		if (parent::onBeforeSearch($query, $where, $fields))
		{
			//Si es un ISBN lo añade a la búsqueda
			$query = trim($query);
			if (is_email($query))
			{
				$where = "{$this->_tablename}.nIdContacto IN (
				SELECT Mailing_EMailsContacto.nIdContacto 
				FROM Gen_EMails JOIN Mailing_EMailsContacto ON Mailing_EMailsContacto.nIdEmail = Gen_EMails.nIdEmail
				WHERE Gen_EMails.cEmail = " . $this->db->escape($query) . ")";								
			}
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_Contacto.php */
/* Location: ./system/application/models/mailing/M_Contacto.php */
