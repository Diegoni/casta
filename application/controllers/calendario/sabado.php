<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	calendario
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Sábados
 *
 */
class Sabado extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()	
	{
		parent::__construct('calendario.sabado', 'calendario/M_sabado', true, null, 'Sábados', 'calendario/submenusabado.js');
	}

	function crear($year = null)
	{
		$this->userauth->roleCheck(($this->auth . '.add'));

		$year = isset($year)?$year:$this->input->get_post('year');

		if (!is_integer($year))
		{
			$count = $this->reg->crear($year);
			if ($count === FALSE)
				$this->out->error($this->reg->error_message());

			$this->out->success(sprintf($this->lang->line('calendario-sabados-creados'), $count, $year));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
}

/* End of file turno.php */
/* Location: ./system/application/controllers/calendario/turno.php */