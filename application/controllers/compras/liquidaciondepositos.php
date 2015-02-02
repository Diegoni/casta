<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Gestión de liquidación de depósitos
 *
 */
class LiquidacionDepositos extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return LiquidacionDepositos
	 */
	function __construct()
	{
		parent::__construct('compras.liquidaciondepositos', 'compras/m_liquidaciondepositos', TRUE, 'compras/liquidaciondepositos.js', 'Liquidación depósitos');
	}

	/**
	 * Añade los albaranes al documento de la cámar
	 * @param int $id Id del albarán agrupado
	 * @param string $ids Ids separados por ; de los albaranes a añadir
	 */
	function add_items($id = null, $ids = null, $pv = null)
	{
		$this->userauth->roleCheck(($this->auth.'.upd'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$ids	= isset($ids)?$ids:$this->input->get_post('ids');
		$pv		= isset($pv)?$pv:$this->input->get_post('pv');

		#var_dump($ids); die();

		if ($ids && $id)
		{
			$count = 0;
			$this->load->model('ventas/m_albaransalidalinea');
			$this->db->trans_begin();
			foreach($ids as $reg)
			{
				$data = $data = $this->reg->get_items($pv, null, $reg['linea'], $reg['idl']);

				#var_dump($data); die();
				foreach($data as $d)
				{
					if (!$this->m_albaransalidalinea->update($d['nIdLineaAlbaran'], array('nIdDocumentoDeposito' => $id, 'bLiquidado' => TRUE)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_albaransalidalinea->error_message());
					}
					$count += $d['nEnDeposito'];
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('liquidaciondepositos-articulo-add'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Añade los albaranes al albarán agrupado
	 * @param int $id Id del albarán agrupado
	 * @param string $ids Ids separados por ; de los albaranes a añadir
	 */
	function del_items($id = null)
	{
		$this->userauth->roleCheck(($this->auth.'.del'));

		$id		= isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$id = preg_split('/;/', $id);
			$this->load->model('ventas/m_albaransalidalinea');
			$count = 0;
			foreach($id as $id1)
			{
				if (isset($id1) && $id1 != '')
				{
					if (!$this->m_albaransalidalinea->update($id1, array('nIdDocumentoDeposito' => NULL, 'bLiquidado' => FALSE)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_albaransalidalinea->error_message());
					}
					$count++;
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('liquidaciondepositos-articulo-del'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Busca las ventas no liquidadas del proveedor indicado
	 * @param int $id Id del proveedor
	 * @param int $desde Fecha máxima (timespam)
	 */
	function get_items($id = null, $desde = null)
	{
		$this->userauth->roleCheck(($this->auth.'.get_list'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$desde	= isset($desde)?$desde:$this->input->get_post('desde');

		if ($id)
		{
			$data = $this->reg->get_items($id, $desde);
			#echo array_pop($this->db->queries); die();
			#var_dump(count($data)); die();
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cierra el documento
	 * @param int $id Id del documento
	 * @return MSG
	 */
	function cerrar($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.cerrar');
		$id = isset($id) ? $id : $this->input->get_post('id');
		if ($id)
		{
			// La cierra
			$res = $this->reg->cerrar($id);
			if ($res === FALSE)	
				$this->out->error($this->reg->error_message());
			$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('documento-cerrado-history'));
			$this->out->success(sprintf($this->lang->line('documento-cerrada-ok'), $id));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Abre el documento
	 * @param int $id Id del documento
	 * @return MSG
	 */
	function abrir($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.abrir');
		$id = isset($id) ? $id : $this->input->get_post('id');
		if ($id)
		{
			// La cierra
			$res = $this->reg->abrir($id);
			if ($res === FALSE)	
				$this->out->error($this->reg->error_message());
			$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('documento-abierto-history'));
			$this->out->success(sprintf($this->lang->line('documento-abierto-ok'), $id));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#_pre_printer($id, $data, $css)
	 */
	protected function _pre_printer($id, &$data, &$css)
	{
		parent::_pre_printer($id, $data, $css);
		$this->load->model('generico/m_divisa');
		$data['divisa'] = $this->m_divisa->load($this->config->item('bp.divisa.default'));
		
		$css = $this->config->item('bp.documentos.css');
		return TRUE;
	}
}

/* End of file documentocamara.php */
/* Location: ./system/application/controllers/compras/documentocamara.php */
