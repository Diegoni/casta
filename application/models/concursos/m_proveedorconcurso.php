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
 * Proveedores Concurso
 *
 */
class M_proveedorconcurso extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Constructor
	 * @return M_Pedido
	 */
	function __construct()
	{
		$data_model = array(
			'cNombre'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'cNombreCorto'	=> array(),
		);

		//CI
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.concursos.database');
		parent::__construct($this->prefix . 'Diba_Proveedores', 'nIdProveedor', 'nIdProveedor', 'cNombre', $data_model);
	}
}

/* End of file M_proveedorconcurso.php */
/* Location: ./system/application/models/concursos/M_proveedorconcurso.php */