<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	suscripciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Medios de renovación 
 *
 */
class Reclamacion extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Reclamacion
	 */
	function __construct()
	{
		parent::__construct('suscripciones.reclamacion', 'suscripciones/m_reclamacion', TRUE, 'suscripciones/reclamacion.js', 'Reclamaciones', 'sys/submenureclamacion.js');
	}

	/**
	 * Hook al que se llama cuando se ha realizado un envio positivo
	 * @param int $id Id del documento
	 * @param string $message Mensaje de resultado
	 */
	protected function _post_send($id, $message)
	{
		$this->reg->update($id, array('dEnvio' => time()));
		$data = $this->reg->load($id);
		$this->load->model('suscripciones/m_suscripcion');
		$link = format_enlace_cmd($id, site_url('suscripciones/reclamacion/index/' . $id));
		$message = sprintf($this->lang->line('reclamacion-envio-nota-suscripcion'), $link, $message); 
		$this->_add_nota(null, $data['nIdSuscripcion'], NOTA_INTERNA, $message, $this->m_suscripcion->get_tablename());
	}

	/**
	 * Cancela un pedido de proveedor
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function cancelar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.cancelar');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$count = 0;
			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->cancelar($id);
					if (!$res) $this->out->error($this->reg->error_message());
					$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('reclamacion-cancelado-history'));
					++$count;
				}
			}
			$this->out->success(sprintf($this->lang->line('reclamacion-cancelado'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Marca una reclamación como enviada
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function enviada($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.cancelar');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$count = 0;
			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->enviada($id);
					if (!$res) $this->out->error($this->reg->error_message());
					$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('reclamacion-enviada-history'));
					++$count;
				}
			}
			$this->out->success(sprintf($this->lang->line('reclamacion-enviada'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Información para el envío de los pedidos
	 * @param int $id Id del pedido
	 * @return array, información para el envío
	 */
	protected function _get_profile_sender($id)
	{
		$pd = $this->reg->load($id, TRUE);
		if ($pd['nIdDestino'] == DESTINO_RECLAMACION_PROVEEDOR)
		{
			$this->load->model('proveedores/m_email');
			$this->load->model('proveedores/m_telefono');
			$lang = (isset($pd['proveedor']['cIdioma']) && trim($pd['proveedor']['cIdioma'])!='')?$pd['proveedor']['cIdioma']:(isset($pd['direccionproveedor'])?$pd['direccionproveedor']['cIdioma']:null);
			$idpvcl = $pd['nIdProveedor'];
		}
		else
		{
			$this->load->model('clientes/m_email');
			$this->load->model('clientes/m_telefono');
			$lang = (isset($pd['cliente']['cIdioma']) && trim($pd['cliente']['cIdioma'])!='')?$pd['cliente']['cIdioma']:(isset($pd['direccioncliente'])?$pd['direccioncliente']['cIdioma']:null);
			$idpvcl = $pd['nIdCliente'];
		}
		if (!isset($lang) || trim($lang) == '' )
		{
			$lang = $this->config->item('reports.language');
			$lang = preg_split('/;/', $lang);
			$lang = $lang[0];
		}
		$this->load->language("report.{$lang}");
		$subject = $this->lang->line('report-reclamacion-email-' . $pd['cTipoReclamacion']);
		$subject = sprintf($subject, $pd['nIdSuscripcion']);
		
		return array(
			'perfil' 		=> PERFIL_RECLAMACIONES,
			'emails'		=> $this->m_email,
			'faxes'			=> $this->m_telefono,
			'report_email' 	=> $this->config->item('sender.reclamacionsuscripcion'),
			'report_normal' => $this->_get_report_default(),
			'report_lang'	=> $lang,
			'subject'		=> $subject,
			'data'			=> $pd,
			'css'			=> $this->config->item('bp.documentos.css'),
			'id'			=> $idpvcl
		);
	}
}

/* End of file reclamacion.php */
/* Location: ./system/application/controllers/suscripciones/reclamacion.php */