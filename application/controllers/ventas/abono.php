<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-20100, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Abonos
 *
 */
class Abono extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Abono
	 */
	function Abono()
	{
		parent::__construct('ventas.abono', 'ventas/M_abono', TRUE, 'ventas/abono.js', 'Vales');
	}

	/**
	 * Crea vales en grupo
	 * @param int $cliente Id del cliente
	 * @param float $importe Importe de los vales
	 * @param int $cantidad Número de vales
	 * @return HTML
	 */
	function crear($cliente = null, $importe = null, $cantidad = null, $sv = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$cliente 	= isset($cliente)?$cliente:$this->input->get_post('cliente');
		$importe	= isset($importe)?$importe:$this->input->get_post('importe');
		$cantidad 	= isset($cantidad)?$cantidad:$this->input->get_post('cantidad');
		$sv			= isset($sv)?$sv:$this->input->get_post('sv');
		
		if ($cliente && $importe)
		{
			$sv = format_tobool($sv);
			if (!is_numeric($cantidad) || $cantidad <= 0) $cantidad = 1;
			$this->db->trans_begin();
			$abonos = array();
			while ($cantidad >= 1)
			{
				$id = $this->reg->insert(array(
					'nIdCliente'	=> $cliente,
					'fImporte'		=> $importe,
					'bNoCaduca'		=> $sv				
				));
				if ($id < 1)
				{
					$this->db->trans_rollback();
					$this->out->error($this->reg->error_message());
				}
				$abonos[] = $id;
				--$cantidad;
			}
			$this->db->trans_commit();
			if (count($abonos) == 1)
			{
				$this->printer($abonos[0]);
			}
			
			$this->load->model('clientes/m_cliente');
			$data['cliente'] = $this->m_cliente->load($cliente);			
			$data['abonos'] = $abonos;
			$data['importe'] = $importe;
			$message = $this->load->view('ventas/listaabonos', $data, TRUE);
			$this->out->html_file($message, $this->lang->line('Crear Abonos'), 'iconoReportTab');
			
			
			$res = array ( 
				'success'	=> TRUE,
				'message'	=> sprintf($this->lang->line('abonos-creados'), count($abonos), $importe),
				'id'		=> $abonos
			);
			$this->out->send($res);
		}
		else
		{
			$this->_show_js('add', 'ventas/crearabono.js');
		}
	}

	/**
	 * Obtiene el abono con las notas
	 * @param  int $id Id del abono
	 * @return DATOS
	 */
	function get2($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));

		$id	= isset($id)?$id:$this->input->get_post('id');
		if (isset($id) && ($id != ''))
		{
			$data = $this->reg->load($id);
			if ($data!==FALSE)
			{
				$data['notas'] = $this->reg->get_notas($id);
				$this->out->data($data);
			}
			else
			{
				$this->out->error(sprintf($this->lang->line('registro_no_encontrado'), $id));
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
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

	/**
	 * Hook para las llamadas después de leer los datos
	 * @param int $id Id del registro
	 * @param mixed $relations Relaciones
	 * @param array $data Datos leídos
	 */
	protected function _post_get($id, $relations, &$data, $cmpid = null)
	{
		parent::_post_get($id, $relations, $data, $cmpid);
		$message = $this->load->view('ventas/abono', $data, TRUE);
		$this->load->library('HtmlFile');
		$filename = $this->obj->htmlfile->create($message, $this->lang->line('Vale') . ' ' . $id, $this->config->item('bp.documentos.css'));
		$url = $this->obj->htmlfile->url($filename);
		$data['info'] = $url;

		return TRUE;
	}
	
	/**
	 * Información para el envío de los documentos
	 * @param int $id Id del documento
	 * @return array, información para el envío
	 */
	protected function _get_profile_sender($id)
	{
		$this->load->model('clientes/m_email');
		$this->load->model('clientes/m_telefono');
		$pd = $this->reg->load($id, TRUE);
		$subject = $this->lang->line('abono-subject-email');
		$subject = str_replace('%id%', $id, $subject);
		return array(
			'perfil' 		=> array(PERFIL_ENVIOFACTURACION, PERFIL_FACTURACION),
			'emails'		=> $this->m_email,
			'faxes'			=> $this->m_telefono,
			'report_email' 	=> $this->config->item('sender.abono'),
			'report_normal' => $this->_get_report_default(),
			'report_lang'	=> (isset($pd['cliente']['cIdioma']) && trim($pd['cliente']['cIdioma'])!='')?$pd['cliente']['cIdioma']:(isset($pd['direccion'])?$pd['direccion']['cIdioma']:null),
			'subject'		=> $subject,
			'data'			=> $pd,
			'css'			=> $this->config->item('bp.documentos.css'),
			'id'			=> $pd['nIdCliente']		
		);
	}
}

/* End of file abono.php */
/* Location: ./system/application/controllers/ventas/abono.php */