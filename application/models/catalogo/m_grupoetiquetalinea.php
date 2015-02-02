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
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Articulos en los grupos de etiquetas
 *
 */
class M_grupoetiquetalinea extends MY_Model 
{
	/**
	 * Constructor
	 * @return M_grupoetiquetalinea
	 */
	function __construct()
	{
		$data_model = array(
			'nIdPaquete'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/etiqueta/search')),
			'nIdLibro'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/articulo/search', 'cTitulo')),
            'nIdSeccion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'generico/seccion/search', 'cSeccion')),
			'nCantidad' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE), 
			'fPVP' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0),
			'cSimbolo'		=> array() 
		);

		parent::__construct('Alm_EtqAcumuladas', 'nIdAcumulado', 'nIdAcumulado', 'nIdAcumulado', $data_model, TRUE);
		$this->_cache = TRUE;		
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.cISBN, Cat_Secciones.cNombre cSeccion');
			$this->db->join('Cat_Fondo', "Cat_Fondo.nIdLibro = {$this->_tablename}.nIdLibro");
			$this->db->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = {$this->_tablename}.nIdSeccion", 'left');
						
			return TRUE;
		}
		return FALSE;
	}
	
}

/* End of file m_grupoetiquetalinea.php */
/* Location: ./system/application/models/catalogo/m_grupoetiquetalinea.php */
