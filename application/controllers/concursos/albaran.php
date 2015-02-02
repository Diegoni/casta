<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Albaranes
 *
 */
class Albaran extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Albaran
	 */
	function __construct()
	{
		parent::__construct('concursos.albaran', 'concursos/M_albaran', TRUE, null, 'Albaranes');
	}

	/**
	 * Listado de albaranes que no están agrupados
	 * @param int $id Id de la biblioteca
	 * @return JSON
	 */
	function get_sinagrupar($id = null)
	{
		$this->userauth->roleCheck(($this->auth.'.get_list'));

		$id	= isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$data = $this->reg->get(null, null, null, null,  "nIdBiblioteca = {$id} AND nIdAlbaranAgrupado IS NULL");
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Albaranes de proveedor del concurso
	 * @param string $concurso Concurso
	 * @return HTML
	 */
	function get_albaranes($concurso = null)
	{
		$this->userauth->roleCheck(($this->auth.'.get_list'));
		$concurso	= isset($concurso)?$concurso:$this->input->get_post('concurso', null);

		if ($concurso)
		{
			$datos = $this->reg->get_albaranes($concurso);
			#echo '<pre>';print_r($datos); echo '</pre>'; die();
			$data['concurso'] = $concurso;
			$data['valores'] = $datos;
			$body = $this->load->view('concursos/albaranesproveedor', $data, true);
			$this->out->html_file($body, $this->lang->line('Albaranes del concurso'), 'iconoReportTab');
		}
		else
		{
			$this->_show_js('get_list', 'concursos/albaranes.js');
		}
	}
}

/* End of file albaran.php */
/* Location: ./system/application/controllers/concursos/albaran.php */
