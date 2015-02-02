<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	generico
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Divisas
 *
 */
class Divisa  extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Divisa
	 */
	function __construct()
	{
		parent::__construct('generico.divisa', 'generico/M_divisa', true, null, 'Divisas');
	}

	/**
	 * Actualiza el cambio de la divisa
	 * @return MSG
	 */
	function update()
	{
		$this->load->library('Eurofxref');
		$changes = $this->eurofxref->get();
		$data = $this->reg->get();
		$this->db->trans_begin();
		foreach($data as $currency)
		{
			if (isset($changes[$currency['cSimbolo']]))
			{
				if (!$this->reg->update($currency['nIdDivisa'], array('fCompra' => $changes[$currency['cSimbolo']]['fCompra'], 'fVenta' => $changes[$currency['cSimbolo']]['fVenta'])))
				{
					$this->db->trans_rollback();
					$this->out->error($this->reg->error_message());
				}
			}
		}
		$this->db->trans_commit();
		$this->out->success($this->lang->line('divisas-actualizado-ok'));
	}
}

/* End of file divisa.php */
/* Location: ./system/application/controllers/generico/divisa.php */