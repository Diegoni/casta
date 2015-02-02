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
 * Albaranes agrupados
 *
 */
class Albaranagrupado extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Albaranagrupado
	 */
	function __construct()
	{
		parent::__construct('concursos.albaranagrupado', 'concursos/M_albaranagrupado', TRUE, 'concursos/albaranagrupado.js', 'Albarán agrupado');
	}

	/**
	 * Añade los albaranes al albarán agrupado
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
			$this->load->model('concursos/m_albaran');
			$count = 0;
			foreach($ids as $id1)
			{
				if (isset($id1) && $id1 != '')
				{
					$this->m_albaran->update($id1, array('nIdAlbaranAgrupado' => $id));
					$count++;
				}
			}
			$this->out->success(sprintf($this->lang->line('concursos-albaranes-add'), $count));
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
			$this->load->model('concursos/m_albaran');
			$count = 0;
			foreach($id as $id1)
			{
				if (isset($id1) && $id1 != '')
				{
					$this->m_albaran->update($id1, array('nIdAlbaranAgrupado' => null));
					$count++;
				}
			}
			$this->out->success(sprintf($this->lang->line('concursos-albaranes-del'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	function print_group($id = null, $task = null)
	{
		$id = isset($id)?$id:$this->input->get_post('id');
		$task = isset($task)?$task:$this->input->get_post('task');
		if ($task === FALSE) $task = 1;
		if ($id)
		{
			if ($task == 1)
			{
				$this->load->library('tasks');
				$cmd = site_url("concursos/albaranagrupado/print_group/{$id}/0");
				$this->tasks->add2(sprintf($this->lang->line('albaranesagrupados-factura'), $id) , $cmd);
			}
			else
			{
				set_time_limit(0);

				$this->load->model('concursos/m_facturaconcurso');
				$this->load->library('PdfLib');
				$this->load->library('HtmlFile');
				$this->load->library('zip');
				$data = $this->m_facturaconcurso->load($id, 'albaranesagrupados');
				if (count($data['albaranesagrupados']) > 0)
				{
					$temp_dir = DIR_TEMP_PATH . time();
					mkdir($temp_dir);
					$report = $this->_get_report_default();
					foreach($data['albaranesagrupados'] as $reg)
					{
						$id = $reg['nIdAlbaranAgrupado'];
						$filename = $this->printer($id, $report, null, FALSE, null, null, null, TRUE);
						$pdf = $this->pdflib->create($this->htmlfile->pathfile($filename), null, null, null, FALSE, FALSE);
						$src = $this->obj->htmlfile->pathfile($pdf);
						$dest = $temp_dir . DIRECTORY_SEPARATOR . $id .'.pdf';
						rename($src, $dest);
						$this->zip->read_file($dest);
					}

					$zipname = time() . '.zip';
					$zip = DIR_TEMP_PATH . $zipname;
					$this->zip->archive($zip);
					$url = $this->htmlfile->url($zipname);
					$message = "<a href='{$url}'>$zipname</a>";
					// Envía un mensaje
					$this->load->library('Mensajes');
					$this->mensajes->usuario($this->userauth->get_username(), $message);
					$this->out->success($message);
				}
				$this->out->error($this->lang->line('factura-sin-albaranes'));

			}
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}

	function resumen()
	{
		$albaranesagrupados = $this->reg->get();
		#var_dump($data); die();
		$this->load->language('report.es');
		$this->load->model('concursos/m_configuracion');
		$this->load->model('concursos/m_albaranagrupado');
		$this->load->model('clientes/m_cliente');
		$data = $this->m_configuracion->get();
		$configuracion = $data[0];
		$ejemplares = 0;
		$titulos = 0;
		$total = 0;
		$ivas = array();
		$bases = array();
		$importes_pvp = array();
		$actual = 0;
		$albaranes = array();
		$bibliotecas = $this->reg->bibliotecas();
		foreach($albaranesagrupados as $k => $linea) 
		{
			$importes = $this->m_albaranagrupado->importe($linea['nIdAlbaranAgrupado'], $configuracion);
			$ejemplares += $importes['ejemplares'];
			$titulos += $importes['titulos'];
			$iva = 4;
			$bases[$iva] = (isset($bases[$iva])?$bases[$iva]:0) + $importes['fBase'];
			$importes_pvp[$iva] = (isset($importes_pvp[$iva])?$importes_pvp[$iva]:0) + $importes['fTotal'];
			$albaranesagrupados[$k] = array_merge($linea, $importes);
			if (!isset($albaranes[$linea['cBiblioteca']]))
			{
				if (isset($bibliotecas[$linea['cBiblioteca']]))
				{
					$cl = $this->m_cliente->load($bibliotecas[$linea['cBiblioteca']]);
					$albaranes[$linea['cBiblioteca']] = $albaranesagrupados[$k];	
					$albaranes[$linea['cBiblioteca']]['fImporte1'] = $cl['fImporte1'];
					$albaranes[$linea['cBiblioteca']]['fImporte2'] = $cl['fImporte2'];
					$albaranes[$linea['cBiblioteca']]['nIdCliente'] = $cl['nIdCliente'];
				}
			}
			else
			{
				$albaranes[$linea['cBiblioteca']]['fBase'] += $importes['fBase'];
				$albaranes[$linea['cBiblioteca']]['fTotal'] += $importes['fTotal'];
				$albaranes[$linea['cBiblioteca']]['ejemplares'] += $importes['ejemplares'];
				$albaranes[$linea['cBiblioteca']]['titulos'] += $importes['titulos'];
			}
		}
		sksort($albaranes, 'cBiblioteca');
		$data['albaranesagrupados'] = $albaranes;
		#var_dump($data); die();

		$message = $this->load->view('concursos/resumen', $data, TRUE);
		#echo $message; die();
		$this->out->html_file($message, $this->lang->line('Albaranes Agrupados (viejo)'), 'iconoReportTab');
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#_pre_printer($id, $data, $css)
	 */
	protected function _pre_printer($id, &$data, &$css)
	{
		parent::_pre_printer($id, $data, $css);
		$css = $this->config->item('bp.documentos.css');
		return TRUE;
	}
}

/* End of file Albaranagrupado.php */
/* Location: ./system/application/controllers/concursos/Albaranagrupado.php */
