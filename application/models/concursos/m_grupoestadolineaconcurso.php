<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Estados  Concurso
 *
 */
class M_grupoestadolineaconcurso extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_grupoestadolineaconcurso
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
		);
		
		parent::__construct('Ext_GrupoEstadosConcurso', 'nIdGrupoEstado', 'cDescripcion', 'cDescripcion', $data_model);	
	}
}

/* End of file M_grupoestadolineaconcurso.php */
/* Location: ./system/application/models/concursos/M_grupoestadolineaconcurso.php */