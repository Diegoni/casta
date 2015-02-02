<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador de ubicaciones
 *
 */
class Ubicacion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Ubicacion
	 */
	function __construct()
	{
		parent::__construct('catalogo.ubicacion', 'catalogo/M_ubicacion', TRUE, null, 'Ubicaciones');
	}

	/**
	 * Comprueba los precios de los artículos en la competencia
	 * @param  string $isbns ISBNS separados por espacio, punto y coma, saltos de línea o tabulador
	 * @return HTML
	 */
	function etiquetas($isbns = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');
		$isbns = isset($isbns)?$isbns:$this->input->get_post('isbns');
		if ($isbns)
		{
			set_time_limit(0);
			$isbns = preg_split('/[\;\s\n\r\;]/', $isbns);
			$data = array();
			$no = array();
			$count = 0;
			$this->db->trans_begin();
			foreach ($isbns as $isbn)
			{
				if (!empty($isbn))
				{
					$text = $this->db->escape($isbn);
					$reg = $this->reg->get(null, null, null, null, "cDescripcion={$text}");

					if (count($reg) == 0)
					{
						$no[] = $isbn;
						$id = $this->reg->insert(array('cDescripcion' => $isbn));
						if ($id < 0)
						{
							$this->db->trans_rollback();
							$this->out->error($this->reg->error_message());
						}
					}
					else
					{
						$id = $reg[0]['nIdUbicacion'];
					}
					++$count;
					$data[$isbn] = $this->reg->load($id);
				}
			}
			$this->db->trans_commit();
			$this->load->library('Etiquetas');
			$report = $this->config->item('catalogo.ubicacion.formato');
			foreach ($data as $value) 
			{
				$html = $this->show_report(null, array('etiquetas' => array($value)), $report, null, FALSE, null, FALSE, FALSE);
				$this->etiquetas->print_direct($html);
			}
			#die();
			$this->out->dialog($this->lang->line('Etiquetas Ubicación'), sprintf($this->lang->line('etq-ubicacion-ok'), $count, implode(', ', $no)));
		}
		else
		{
			$data = array(
				'title' => $this->lang->line('Etiquetas Ubicación'),
				'icon' 	=> 'iconoImprimirEtiquetasUbicacionTab',
				'url'	=> 'catalogo/ubicacion/etiquetas',
				'stock' => 'false'
			);
			$this->_show_js('get_list', 'catalogo/check.js', $data);
		}
	}

}

/* End of file ubicacion.php */
/* Location: ./system/application/controllers/catalogo/ubicacion.php */
