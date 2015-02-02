<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

define('DEFAULT_SATELITE', 1);
/**
 * Abonos
 *
 */
class M_Abono extends MY_Model
{
	function M_Abono()
	{
		$data_model = array(
			#'nId'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			#'nIdSatelite'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => DEFAULT_SATELITE),		
			'nIdCliente'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'clientes/cliente/search', 'cCliente')),
			'fImporte'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_MONEY, DATA_MODEL_DEFAULT_VALUE => 0),		
			'fUsado'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_MONEY, DATA_MODEL_DEFAULT_VALUE => 0),		
			'cRefCliente' 	=> array(), 
			'cRefInterna'	=> array(),
			'bNoCaduca' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
		);

		parent::__construct('Doc_Abonos', 'nIdAbono', 'nIdAbono', 'nIdAbono', $data_model, TRUE);
		
		$this->_relations['cliente'] = array (
			'ref'	=> 'clientes/m_cliente',
			'fk'	=> 'nIdCliente');
		$this->_relations['modospago'] = array (
			'ref'	=> 'ventas/m_facturamodopago',
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdAbono');
		
	}

	/**
	 * Trigger llamado Antes de insertar los datos
	 * @param array $data Registro a insertar
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			// La fecha solo puede ser indicado por un administrador
			$admin = $this->obj->userauth->roleCheck('ventas.factura.administrar', null, TRUE);

			if (!$admin && (isset($data['dFecha'])))
			{
				$data['dFecha'] = time();
			}
		}

		return TRUE;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		if (parent::onAfterSelect($data, $id))
		{
			$data['fPendiente'] = (float)((isset($data['fImporte'])?$data['fImporte']:0) - (isset($data['fUsado'])?$data['fUsado']:0));  
			return TRUE;
		}
		return FALSE;
	}

}

/* End of file M_abono.php */
/* Location: ./system/application/models/ventas/M_abono.php */
