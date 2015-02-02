<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Sinopsis
 *
 */
class M_Sinopsis extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Sinopsis
	 */
	function __construct()
	{
		$data_model = array(
			'tSinopsis'			=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
		);

		parent::__construct('Cat_Sinopsis', 'nIdLibro', 'nIdLibro', 'nIdLibro', $data_model, TRUE);
	}
}

/* End of file M_Sinopsis.php */
/* Location: ./system/application/models/catalogo/M_Sinopsis.php */
