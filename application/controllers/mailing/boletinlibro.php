<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	mailing
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Libros de un boletín
 * @author alexl
 *
 */
class Boletinlibro extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Boletinlibro
	 */
	function __construct()
	{
		parent::__construct('mailing.boletinlibro', 'mailing/M_boletinlibro', TRUE);
	}

	/**
	 * Añade las novedades a un boletín
	 * @param int $id ID del boletín
	 * @param int $tema ID del tema
	 * @param int $materia ID de la materia
	 * @param date $desde Fecha desde fecha novedad
	 * @param int $libros número de libros máximos
	 * @return JSON
	 */
	function novedades($id = null, $tema = null, $materia = null, $desde = null, $libros = null)
	{
		$this->userauth->roleCheck($this->auth . '.add');
		$id 		= isset($id)?$id:$this->input->get_post('id');
		$tema 		= isset($tema)?$tema:$this->input->get_post('tema');
		$materia 	= isset($materia)?$materia:$this->input->get_post('materia');
		$desde 		= isset($desde)?$desde:$this->input->get_post('desde');
		$libros 	= isset($libros)?$libros:$this->input->get_post('libros');

		if ($id && ($tema || $materia))
		{
			//Los datos
			if ($tema)
			{
				$count = $this->reg->add_tema($id, $tema, $desde, $libros);
			}
			else
			{
				$count = $this->reg->add_materia($id, $materia, $desde, $libros);
			}
			//$count = 0;
			$this->out->success(sprintf($this->lang->line('mailing-add-ok'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Añade los libros que tienen stock en la sección indicada
	 * @param int $id Id del boletín
	 * @param int $seccion Id de la sección
	 * @param bool $pendientes TRUE: Añade los títulos que tienen stock pendiente
	 * @return JSON
	 */
	function stock($id = null, $seccion= null, $pendientes = null)
	{
		$this->userauth->roleCheck($this->auth . '.add');
		$id 		= isset($id)?$id:$this->input->get_post('id');
		$seccion	= isset($seccion)?$seccion:$this->input->get_post('seccion');
		$pendientes	= isset($pendientes)?$pendientes:$this->input->get_post('pendientes');

		if ($id && $seccion)
		{
			$count = $this->reg->add_stock($id, $seccion, $pendientes);
			//$count = 0;
			$this->out->success(sprintf($this->lang->line('mailing-add-ok'), $count));
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}
}

/* End of file Boletinlibro.php */
/* Location: ./system/application/controllers/mailing/Boletinlibro.php */