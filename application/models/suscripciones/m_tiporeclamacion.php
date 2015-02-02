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

define('TIPORECLAMACION_RECLAMACIONCLIENTE', 3);
define('TIPORECLAMACION_RESPUESTAACLIENTE',	4);
define('TIPORECLAMACION_CANCELARSUSCRIPCION',	5);
define('TIPORECLAMACION_ACTIVARSUSCRIPCION', 7);
define('TIPORECLAMACION_CAMBIODIRECCION',	8);
define('TIPORECLAMACION_RECLAMACIONPEDIDOPROVEEDOR',	9);
define('TIPORECLAMACION_RECLAMACIONFACTURA',	10);
define('TIPORECLAMACION_RENOVACIONSUSCRIPCION',	11);

/**
 * Tipos de reclamación 
 *
 */
class M_tiporeclamacion extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_tiporeclamacion
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'tTexto' 			=> array(), 
			'nIdDestino'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'suscripciones/destinoreclamacion/search', 'cDestino')),
		);

		parent::__construct('Sus_TiposReclamacion', 'nIdTipoReclamacion', 'cDescripcion', 'cDescripcion', $data_model);	
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
			$this->db->select('Sus_DestinosReclamacion.cDescripcion cDestino');
			$this->db->join('Sus_DestinosReclamacion', 'Sus_DestinosReclamacion.nIdDestino = Sus_TiposReclamacion.nIdDestino');
			return TRUE;
		}
		return FALSE;
	}	
}

/* End of file M_tiporeclamacion.php */
/* Location: ./system/application/models/suscripciones/M_tiporeclamacion.php */