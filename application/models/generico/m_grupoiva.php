<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	generico
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Grupos Iva
 *
 */
class M_Grupoiva extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Grupoiva
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'bImpuestos' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'nIdCobro' 		=> array(),
			'nIdCuenta' 	=> array(),
		);
		
		parent::__construct('Cli_GruposIva', 'nIdGrupoIva', 'cDescripcion', 'cDescripcion', $data_model);
		
		$this->_cache = TRUE;		
	}
}


/* End of file M_grupoiva.php */
/* Location: ./system/application/models/generico/M_grupoiva.php */