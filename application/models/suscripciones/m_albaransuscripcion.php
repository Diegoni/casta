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

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'ventas' . DIRECTORY_SEPARATOR . 'm_albaransalida.php');
 
/**
 * Albaranes de salida de una suscripción 
 *
 */
class M_albaransuscripcion extends M_albaransalida
{

	/**
	 * Constructor
	 * @return M_albaransuscripcion
	 */
	function __construct()
	{
		parent::__construct();
		$this->_alias = array(
				'cCliente' 			=> array($this->db->concat(array('Cli_Clientes.cEmpresa', 'Cli_Clientes.cNombre', 'Cli_Clientes.cApellido'))),
				'nIdSuscripcion' 	=> array('Sus_SuscripcionesAlbaranes.nIdSuscripcion'),
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Sus_SuscripcionesAlbaranes.nIdSuscripcion')
			->select('Cat_Fondo.cTitulo, Cat_Fondo.nIdLibro')
			->join('Sus_SuscripcionesAlbaranes', "Sus_SuscripcionesAlbaranes.nIdAlbaran = {$this->_tablename}.nIdAlbaran")
			->join('Sus_Suscripciones', "Sus_SuscripcionesAlbaranes.nIdSuscripcion = Sus_Suscripciones.nIdSuscripcion")
			->join('Cat_Fondo', "Sus_Suscripciones.nIdRevista = Cat_Fondo.nIdLibro");
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_albaransuscripcion.php */
/* Location: ./system/application/models/suscripciones/M_albaransuscripcion.php */