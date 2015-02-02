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
 * Dias de un año
 *
 */
class Dia extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('calendario.dia', 'calendario/M_Dia', true, 'calendario/dia.js', 'Días');
	}

	/**
	 * Crea un calendario nuevo
	 * @param int $year Año a crear
	 * @return JSON
	 */
	function crear( $year = null )
	{
		$this->userauth->roleCheck(($this->auth . '.create'));
		$year = isset($year)?$year:$this->input->get_post('year');
		if ($year)
		{
			$count = $this->reg->create_year($year);
			//$count = 2;
			if ($count==-1)
			{
				$success = FALSE;
				$message = $this->lang->line('calendario_year_repetido');
			}
			elseif ($count==0)
			{
				$success = FALSE;
				$message = $this->lang->line('calendario_year_error');
			}
			else
			{
				$success = TRUE;
				$message = sprintf($this->lang->line('calendario_year_creado'), $count);
			}
		}
		else
		{
			$success = FALSE;
			$message = $this->lang->line('calendario_no_year');
		}

		// Respuesta
		echo $this->out->message($success, $message);
	}

	/**
	 * Devuelve los años existentes
	 * @return JSON
	 */
	function years()
	{
		$this->userauth->roleCheck(($this->auth . '.get'));
		$data = $this->reg->years();
		$this->out->data($data);		
	}
}

/* End of file dia.php */
/* Location: ./system/application/controllers/calendario/dia.php */