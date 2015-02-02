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
 * Gestión de la cámara del libro
 *
 */
class Documentocamara extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Documentocamara
	 */
	function __construct()
	{
		parent::__construct('compras.documentocamara', 'compras/m_documentocamara', TRUE, 'compras/documentocamara.js', 'Documentos Cámara');
	}

	/**
	* Formas de envío de la mercancía
	* @return JSON
	*/
	function formasenvio()
	{
		$this->out->data($this->reg->formasenvio());
	}

	/**
	 * Añade los albaranes al documento de la cámar
	 * @param int $id Id del albarán agrupado
	 * @param string $ids Ids separados por ; de los albaranes a añadir
	 */
	function add_items($id = null, $ids = null)
	{
		$this->userauth->roleCheck(($this->auth.'.upd'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$ids	= isset($ids)?$ids:$this->input->get_post('ids');

		if ($ids && $id)
		{
			$ids = preg_split('/;/', $ids);
			$this->load->model('compras/m_albaranentrada');
			$count = 0;
			foreach($ids as $id1)
			{
				if (isset($id1) && $id1 != '')
				{
					$this->m_albaranentrada->update($id1, array('nIdDocumentoCamara' => $id));
					$count++;
				}
			}
			$this->out->success(sprintf($this->lang->line('documentocamara-albaranes-add'), $count));
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
			$this->load->model('compras/m_albaranentrada');
			$count = 0;
			foreach($id as $id1)
			{
				if (isset($id1) && $id1 != '')
				{
					$this->m_albaranentrada->update($id1, array('nIdDocumentoCamara' => null));
					$count++;
				}
			}
			$this->out->success(sprintf($this->lang->line('documento-albaranes-del'), $count));
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
		$total = $peso = 0;
		foreach ($data['albaranes'] as $albaran)
		{
			$total += format_decimals($albaran['fImporteCamara'] * $albaran['fCambioCamara']);
			$peso += $albaran['nPeso'];
		}
		$data['fImporteCamara'] = $total;
		$data['nPeso'] = $peso;
		
		$css = $this->config->item('bp.documentos.css');
		return TRUE;
	}
}

/* End of file documentocamara.php */
/* Location: ./system/application/controllers/compras/documentocamara.php */
