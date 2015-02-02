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

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'ventas' . DIRECTORY_SEPARATOR . 'm_factura.php');

/**
 * Facturas temporales
 *
 */
class M_factura2 extends M_factura
{
	/**
	 * Constructor
	 * @return M_factura2
	 */
	function __construct()
	{
		parent::__construct('Doc_Facturas2', 'ventas/m_albaransalida2', 'ventas/m_facturamodopago2', 'ventas/m_facturalinea2', 'Doc_AlbaranesSalida2');
	}

	/**
	 * Procesa la factura
	 * Copia la factura cerrada al sistema de facturación normal y liquida los stocks
	 * @param int $id Id de la factura
	 * @return JSON
	 */
	function cerrar2($id)
	{
		$factura = $this->load($id, array('lineas', 'modospago'));
		// Estado en proceso
		if ($factura['nIdEstado'] != FACTURA_STATUS_A_PROCESAR)
		{
			$this->_set_error_message(sprintf($this->lang->line('error-factura-cerrada'), $id));
			return FALSE;
		}

		$albaranes = array();
		$this->db->trans_begin();
		// Actualiza la factura
		$data['nIdEstado'] = FACTURA_STATUS_CERRADA;
		if (!$this->update($id, $data))
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Copia la factura
		unset($factura['nIdFactura']);
		unset($factura['nIdEstado']);

		// Copia las líneas
		foreach ($factura['lineas'] as $k => $lineas)
		{
			$albaranes[$lineas['nIdAlbaran']] = $lineas['nIdAlbaran'];
			unset($lineas['nIdLineaAlbaran']);
			unset($lineas['nIdAlbaran']);
			$factura['lineas'][$k] = $lineas;
		}
		// Copia los modos de pago
		foreach ($factura['modospago'] as $k => $modopago)
		{
			unset($modopago['nIdFacturaModoPago']);
			unset($modopago['nIdFactura']);
			$factura['modospago'][$k] = $modopago;
		}

		// Crea la factura nueva
		$obj = get_instance();
		$obj->load->model('ventas/m_factura');
		$factura['nIdEstado'] = FACTURA_STATUS_A_PROCESAR;
		$obj->m_factura->admin = TRUE;
		$id_n = $obj->m_factura->insert($factura);
		if ($id_n < 0)
		{
			$this->_set_error_message($obj->m_factura->error_message());
			$this->db->trans_rollback();
			return FALSE;
		}
		$ft = $obj->m_factura->load($id_n, TRUE);
		#echo '<pre>'; print_r($ft); echo '</pre>'; die();

		// La cierra
		if (!$obj->m_factura->cerrar2($id_n))
		{
			$this->_set_error_message($obj->m_factura->error_message());
			$this->db->trans_rollback();
			return FALSE;
		}

		// Elimina la factura del tpv
		/*
		$data = array('nIdEstado' => FACTURA_STATUS_EN_PROCESO);
		if (!$this->update($id, $data))
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Abre los albaranes
		$obj->load->model('ventas/m_albaransalida2');
		foreach ($albaranes as $alb)
		{
			$data = array('nIdEstado' => DEFAULT_ALBARAN_SALIDA_STATUS);
			#echo '<pre>'; print_r($alb); echo '</pre>';
			if (!$obj->m_albaransalida2->update($alb, $data))
			{
				$this->_set_error_message($obj->m_albaransalida2->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
			if (!$obj->m_albaransalida2->delete($alb))
			{
				$this->_set_error_message($obj->m_albaransalida2->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		// La factura
		if (!$this->delete($id))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		*/
		$this->db->trans_commit();

		return TRUE;
	}
}

/* End of file M_factura2.php */
/* Location: ./system/application/models/ventas/M_factura2.php */