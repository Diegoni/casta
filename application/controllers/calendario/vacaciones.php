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
 * Vacaciones de los trabajadores
 *
 */
class Vacaciones extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()	
	{
		parent::__construct('calendario.vacaciones', 'calendario/M_Vacaciones', true, null, 'Vacaciones');
	}
	
		/**
	 * Devuelve un resumen de las horas
	 * @param int $id Id del trabajador
	 * @param int $year Año a mostrar resumen
	 * @return JSON
	 */
	function resumen($id = null, $year = null)
	{
		$this->userauth->roleCheck(($this->auth . '.resumen'));

		$year 	= isset($year)?$year:$this->input->get_post('year');
		$id		= isset($id)?$id:$this->input->get_post('id');

		if ($id && $year)
		{
			$data = $this->reg->get(null, null, null, null, array('nIdTrabajador' =>$id, 'YEAR(dDia)' => $year));
			$this->load->model('calendario/m_trabajador');
			$tr = $this->m_trabajador->load($id);
			
			$data2['year'] = $year;
			$data2['trabajador'] = $tr;
			$data2['vacaciones'] = $data;
			#print '<pre>'; print_r($data2); print '</pre>'; die();
			
			$message = $this->load->view('calendario/vacaciones', $data2, TRUE);
			// Respuesta
			echo $this->out->html_file($message, $this->lang->line('Vacaciones'). " {$year}", 'iconoReportTab');
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}
	
	
}

/* End of file vacaciones.php */
/* Location: ./system/application/controllers/calendario/vacaciones.php */