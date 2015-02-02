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
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador de editoriales
 *
 */
class Editorial extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Editorial
	 */
	function __construct()
	{
		parent::__construct('catalogo.editorial', 'catalogo/M_editorial', TRUE, 'catalogo/editorial.js', 'Editoriales');
	}

	/**
	 * Unificador de editoriales
	 * @param int $id1 Id de la editorial destino
	 * @param string $id2 Ids de las editoriales repetidas, separadas por ;
	 * @return JSON
	 */
	function unificar($id1 = null, $id2 = null)
	{
		$this->userauth->roleCheck(($this->auth.'.unificar'));

		$id1	= isset($id1)?$id1:$this->input->get_post('id1');
		$id2	= isset($id2)?$id2:$this->input->get_post('id2');

		if ($id1 && $id2)
		{
			$ids = preg_split('/\;/', $id2);
			$t = '';
			$this->load->library('Logger');
			foreach ($ids as $id)
			{
				if (isset($id) && ($id != ''))
				{
					$t .= '  ' . $id;
					if (!$this->reg->unificar($id1, $id))
					{
						$str = $this->reg->error_message();
						$this->out->error($str);
					}
					$this->logger->log("Editorial unificada {$id2} con {$id1}", 'unificador');										
				}
			}
			$this->out->success($this->lang->line('editorial-unificadas-ok'));
		}
		else
		{
			$data['title'] = $this->lang->line('Unificar editorial');
			$data['icon'] = 'iconoUnficarEditorialTab';
			$data['url_search'] = site_url('catalogo/editorial/search');
			$data['url'] = site_url('catalogo/editorial/unificar');
			$this->_show_form('unificar', 'catalogo/unificador.js', $this->lang->line('Unificar editorial'), null, null, null, $data);
		}		
	}

	/**
	 * Análisis de los mOvimientos de una editorial 
	 * @param int $id Id de la editorial
	 * @return HTML_FILE
	 */
	function analisis($id = null, $desde = null, $hasta = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$desde = isset($desde) ? $desde : $this->input->get_post('desde');
		$hasta = isset($hasta) ? $hasta : $this->input->get_post('hasta');


		if (is_numeric($id) && !empty($desde) && !empty($hasta)  )
		{
			$desde = to_date($desde);
			$hasta = to_date($hasta);
			$datos = $this->reg->analisis($id, $desde, $hasta);

			for($i=1; $i < 13; $i++)
			{
				$base[$i] = 0;
			}
			$secciones = array();
			$totales = array();
			$ventas2 = array();
			$anos = array();
			foreach($datos['ventas'] as $v)
			{
				if (!isset($ventas2[$v['cNombre']][$v['year']]))
					$ventas2[$v['cNombre']][$v['year']] = $base;
				$ventas2[$v['cNombre']][$v['year']][$v['month']] += $v['nCantidad'];
				if (!isset($totales[$v['year']]))
					$totales[$v['year']] = $base;
				$totales[$v['year']][$v['month']] += $v['nCantidad'];
				$anos[$v['year']] = $v['year'];
			}

			$datos = array_merge($datos, array(
				'articulo' 		=> $this->reg->load($id),
				'secciones'		=> $ventas2,
				'totales'		=> $totales,
				'anos'			=> $anos
				));


			$datos['editorial'] = $this->reg->load($id);

			$message = $this->load->view('catalogo/analisis_editorial', $datos, TRUE);
			#echo $message; die();
			$this->out->html_file($message, $this->lang->line('Análisis') . ' ' . $id, 'iconoReportTab', $this->config->item('bp.data.css'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
}

/* End of file editorial.php */
/* Location: ./system/application/controllers/catalogo/editorial.php */
