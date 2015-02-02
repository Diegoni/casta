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
 * Configuración  Concurso
 *
 */
class M_configuracion extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';
	
	/**
	 * Costructor 
	 * @return M_configuracion
	 */
	function __construct()
	{
		$data_model = array(
			'cAplicacion'			=> array(),
			'cDocumento'			=> array(),
			'cCliente'				=> array(),
			'cDireccion'			=> array(),
			'cNIF'					=> array(),
			'cBanco1'				=> array(),
			'cBanco2'				=> array(),
			'cCC1'					=> array(),
			'cCC2'					=> array(),
			'fDescuento'			=> array(),
			'cAlbaranesBibliopola'	=> array(),
			'fImporteConcurso'		=> array(),
			'fOtrosImportes'		=> array(),
			'nIdCliente'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdSerie'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/serie/search')),
			'nIdCaja'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/caja/search')),
			'nIdSeccion'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search')),		
			'nIdLibro'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nIdSeccionFactura'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search')),		
		);
		
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.concursos.database');
		
		parent::__construct($this->prefix . 'Diba_Configuracion', 'nIdConfiguracion', 'nIdConfiguracion', 'nIdConfiguracion', $data_model);	
	}
}

/* End of file M_configuracion.php */
/* Location: ./system/application/models/concursos/M_configuracion.php */