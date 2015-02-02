<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */


/**
 * Comandos
 *
 */
class M_Comando extends MY_Model
{
	/**
	 * Constructoir
	 * @return unknown_type
	 */
	function __construct()
	{
		$data_model = array(
			'tComando'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_DEFAULT => TRUE),
			'cOrigen'		=> array(DATA_MODEL_REQUIRED => TRUE),
			'cDestino'		=> array(DATA_MODEL_REQUIRED => TRUE),
			'nIdTarea'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'bEjecutado' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => 0),
		);

		parent::__construct('Ext_Comandos', 'nIdComando', 'dCreacion DESC', 'dCreacion', $data_model, TRUE);
		$this->_cache = TRUE;
	}

	/**
	 * Comandos no ejecutados
	 * @param string $username Usuario
	 * @return array
	 */
	function unexec($username)
	{
		if (isset($username))
		{
			$username= $this->db->escape((string)$username);
			$data = $this->get(null, null, 'dCreacion', 'ASC', "bEjecutado = 0 AND cDestino = {$username}");
			return $data;
		}
		return null;
	}
}

/* End of file M_comando.php */
/* Location: ./system/application/models/sys/M_comando.php */
