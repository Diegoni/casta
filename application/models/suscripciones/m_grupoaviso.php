<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	suscripciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Grupos de avisos de renovación
 *
 */
class M_grupoaviso extends MY_Model
{
	/**
	 * Costructor
	 * @return M_grupoaviso
	 */
	function __construct()
	{
		$data_model = array('cDescripcion' => array(
					DATA_MODEL_DEFAULT => TRUE,
					DATA_MODEL_REQUIRED => TRUE
			), );

		parent::__construct('Sus_GruposAvisos', 'nIdGrupoAviso', 'cDescripcion DESC', 'cDescripcion', $data_model);
		$this->_cache = TRUE;
	}

	/**
	 * Obtiene el ID del campaña más reciente
	 * @return int
	 */
	function get_last_grupo()
	{
		$this->db->flush_cache();
		$this->db->select_max('nIdGrupoAviso', 'nIdGrupoAviso')->from($this->_tablename);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data[0]['nIdGrupoAviso'];
	}

}

/* End of file M_grupoaviso.php */
/* Location: ./system/application/models/suscripciones/M_grupoaviso.php */
