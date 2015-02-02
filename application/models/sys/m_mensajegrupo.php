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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Grupos para mensajes del sistema
 *
 */
class M_MensajeGrupo extends MY_Model
{
	/**
	 * Constructoir
	 * @return unknown_type
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE),
		);

		parent::__construct('Ext_MensajesGrupos', 'nIdGrupo', 'cDescripcion', 'cDescripcion', $data_model, TRUE);
		$this->_cache = TRUE;
	}
}

/* End of file M_MensajeGrupo.php */
/* Location: ./system/application/models/sys/M_MensajeGrupo.php */
