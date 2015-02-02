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
 * Boletin
 *
 */
class M_Boletin extends MY_Model
{
	/**
	 * Costructor
	 * @return M_Boletin
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE),
			'cDescripcionCorta'	=> array(),
			'nIdTema' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'mailing/tema/search')), 
			'tTexto'			=> array(DATA_MODEL_NO_LIST => TRUE),
			'bWeb' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => TRUE),
		);

		parent::__construct('Sus_Boletines', 'nIdBoletin', 'cDescripcion', 'cDescripcion', $data_model, TRUE);

		$this->_relations['libros'] = array (
			'table' 	=> 'Sus_Boletines_Libros',
			'ref'		=> 'catalogo/m_articulo',
			'fk'		=> 'nIdLibro',
			'cascade'	=> TRUE);
	}

	/**
	 * Genera un listado de artículos que no tienen port
	 * @param int $id Id del boletín
	 * @return array
	 */
	function sinportada($id)
	{
		$this->db->select('Cat_Fondo.nIdLibro, Cat_Fondo.cISBN, Cat_Fondo.nEAN, Cat_Fondo.cTitulo, Cat_Fondo.cAutores')
		->from('Sus_Boletines_Libros')
		->join('Cat_Fondo', 'Sus_Boletines_Libros.nIdLibro = Cat_Fondo.nIdLibro')
		->join('Fotos', 'Fotos.nIdRegistro=Sus_Boletines_Libros.nIdLibro', 'left')
		->where("Sus_Boletines_Libros.nIdBoletin={$id}")
		->where('Fotos.nIdRegistro IS NULL');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}
}
/* End of file M_boletin.php */
/* Location: ./system/application/models/mailing/M_boletin.php */
