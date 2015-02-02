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
 * Cambios de divisa para la cámara del libro
 *
 */
class Divisacamara extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Divisacamara
	 */
	function __construct()
	{
		parent::__construct('compras.divisacamara', 'compras/M_divisacamara', TRUE, null, 'Divisas cámara', 'sys/submenudivisacamara.js');
	}

	/**
	 * Actualiza el cambio de la divisa
	 * @return MSG
	 */
	function actualizar($fecha = null, $cmpid = null)
	{
		$this->userauth->roleCheck($this->auth . '.upd');
		$fecha = isset($fecha)?$fecha:$this->input->get_post('fecha');
		$cmpid = isset($cmpid)?$cmpid:$this->input->get_post('cmpid');
		
		if (!empty($fecha))
		{
			$fecha = to_date($fecha);
			$this->load->library('Eurofxref');
			$changes = $this->eurofxref->get($fecha);
			if (count($changes) == 0)
			{
				$this->out->error($this->lang->line('divisas-fecha-no-existe'));
			}
			$data = $this->reg->get();
			$this->db->trans_begin();
			foreach($data as $currency)
			{
				if (isset($changes[$currency['cSimbolo']]))
				{
					if (!$this->reg->update($currency['nIdDivisa'], 
						array(
							'fCompra' => $changes[$currency['cSimbolo']]['fCompra'], 
							'fVenta' => $changes[$currency['cSimbolo']]['fVenta'],
							'dAct' => $fecha
						)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->reg->error_message());
					}
				}
			}
			$this->db->trans_commit();
			$this->out->success($this->lang->line('divisas-actualizado-ok'));
		}
		else
		{
			$this->_show_js('upd', 'compras/divisacamara.js', array('cmpid' => $cmpid));
		}
	}
}

/* End of file divisacamara.php */
/* Location: ./system/application/controllers/compras/divisacamara.php */