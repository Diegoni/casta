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
 * Listanovedad
 *
 */
class M_Listanovedad extends MY_Model
{
	/**
	 * Costructor
	 * @return M_Listanovedad
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE),
		);

		parent::__construct('Diba_ListaNovedades', 'nIdLista', 'cDescripcion', 'cDescripcion', $data_model, TRUE);

		$this->_relations['lineas'] = array (
			'ref'		=> 'concursos/m_listanovedadlinea',
            'cascade' 	=> TRUE,
			'type'		=> DATA_MODEL_RELATION_1N,
			'fk'		=> 'nIdLista');
	}

}
/* End of file M_boletin.php */
/* Location: ./system/application/models/mailing/M_boletin.php */
