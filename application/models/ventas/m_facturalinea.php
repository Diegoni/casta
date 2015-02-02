<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

require_once (APPPATH . 'models' . DS . 'ventas' . DS . 'm_albaransalidalinea.php');

/**
 * Líneas de factura
 *
 */
class M_facturalinea extends M_albaransalidalinea
{
	/**
	 * Nombre de la tabla de albaranes de salida
	 * @var string
	 */
	var $_albaranes = null;
	/**
	 * Nombre de la tabla de líneas albarán de salida
	 * @var string
	 */
	var $_lineas = null;

	var $_modelfactura = null;
	var $_modelalbaran = null;

	/**
	 * Constructor
	 * @return M_facturalinea
	 */
	function __construct($albaranes =null, $lineas =null, $modelfactura =null, $modelalbaran =null, $calbaranes = null)
	{
		if(!isset($albaranes))
			$albaranes = 'Doc_AlbaranesSalida';
		if(!isset($lineas))
			$lineas = 'Doc_LineasAlbaranesSalida';
		if(!isset($modelfactura))
			$modelfactura = 'ventas/m_factura';
		if(!isset($modelalbaran))
			$modelalbaran = 'ventas/m_albaransalida';
		if(!isset($calbaranes))
			$calbaranes = 'albaransalida';

		$this->_albaranes = $albaranes;
		$this->_lineas = $lineas;
		$this->_modelfactura = $modelfactura;
		$this->_modelalbaran = $modelalbaran;

		parent::__construct($lineas, $calbaranes, $modelalbaran);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id =null, &$sort =null, &$dir =null, &$where =null)
	{
		if(parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->join($this->_albaranes, "{$this->_albaranes}.nIdAlbaran = {$this->_lineas}.nIdAlbaran");
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert()
	 */
	protected function onBeforeInsert(&$data)
	{
		static $albaranes = array();
		static $id_last = null;
		#echo '<pre>'; print_r($data); echo '</pre>';
		if(parent::onBeforeInsert($data))
		{
			// Si no hay albarán y si factura lo crea
			$id_albaran = null;
			$id_factura = null;
			$this->obj->load->model($this->_modelalbaran, 'al');
			if(is_numeric($data['nIdAlbaran']))
			{
				$id_albaran = $data['nIdAlbaran'];
				if(!isset($albaranes[$id_albaran]))
				{
					$albaranes[$id_albaran] = $this->obj->al->load($id_albaran);
				}

				if($albaranes[$id_albaran]['nIdEstado'] != DEFAULT_ALBARAN_SALIDA_STATUS)
					$data['nIdAlbaran'] = null;
				$id_factura = $albaranes[$id_albaran]['nIdFactura'];
			}

			if((!is_numeric($data['nIdAlbaran'])) && isset($id_factura))
			{
				if(isset($id_last))
					$id = $id_last;
				else
				{
					$this->obj->load->model($this->_modelfactura, 'ft');
					$factura = $this->obj->ft->load($id_factura);
					$albaran['nIdCliente'] = $factura['nIdCliente'];
					$albaran['nIdDireccion'] = $factura['nIdDireccion'];
					$albaran['nIdFactura'] = $id_factura;
					$id = $this->obj->al->insert($albaran);
					if($id <= 0)
						return FALSE;
				}
				$data['nIdAlbaran'] = $id;
			}
			#echo '<pre>'; print_r($data); echo '</pre>';
			return TRUE;
		}
		return FALSE;
	}

}

/* End of file M_facturalinea.php */
/* Location: ./system/application/models/compras/M_facturalinea.php */
