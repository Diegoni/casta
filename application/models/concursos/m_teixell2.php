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
 * Etiquetas directas de Teixells
 *
 */
class M_teixell2 extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_teixell
	 */
	function __construct()
	{
		$data_model = array(
			'cTexto'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'nGrupo' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);
		
		parent::__construct('Ext_Teixells2', 'nIdTeixell', 'nIdTeixell', 'nIdTeixell', $data_model, TRUE);	
	}
}

/* End of file M_teixell2.php */
/* Location: ./system/application/models/concursos/M_teixell2.php */