<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	comunicaciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador de Envíos de SMS
 *
 */
class Sms extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Sms
	 */
	function __construct()
	{
		parent::__construct('comunicaciones.sms', 'comunicaciones/M_sms', true, 'comunicaciones/sms.js', 'Enviar SMS');
	}

	/**
	 * Envía un SMS
	 * @param string $to Número
	 * @param string $msg Mensaje
	 * @return JSON
	 */
	function send($to = null, $msg = null)
	{
		$this->userauth->roleCheck($this->auth . '.send');

		$to 	= isset($to)?$to:urldecode($this->input->get_post('to'));
		$msg 	= isset($msg)?$msg:urldecode($this->input->get_post('msg'));

		$this->load->library('SmsServer');

		if (!empty($to) && !empty($msg))
		{
			//Crea el sms
			$sms = array(
				'cMensaje'	=> $msg,
				'cTo'		=> $to
			);
			$id = $data = $this->reg->insert($sms);

			$res = $this->smsserver->send($to, $msg, $id);
			if ($res === TRUE)
			{
				$this->out->success(sprintf($this->lang->line('registro_generado'), $id));
			}
			else
			{
				$this->out->error($res);
			}
		}
		else if (!empty($to))
		{
			$this->index(null, array('to' => $to));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Envía un SMS
	 * @param string $to Número
	 * @param string $msg Mensaje
	 * @return JSON
	 */
	function resend($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.send');

		$id 	= isset($id)?$id:urldecode($this->input->get_post('id'));

		$this->load->library('SmsServer');

		if (!empty($id))
		{
			$data = $this->reg->load($id);
			$this->reg->update($id, array('bDone' => 0));
			
			$res = $this->smsserver->send($data['cTo'], $data['cMensaje'], $id);
			if ($res === TRUE)
			{
				$this->out->success(sprintf($this->lang->line('mensaje_reenviado'), $id, $data['cTo']));
			}
			else
			{
				$this->out->error($res);
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Crea la tarea para comprobar el estado de los SMS envíados
	 */
	function check()
	{
		$cmd = site_url("comunicaciones/sms/check_task");

		$this->load->library('tasks');
		$this->tasks->add2($this->lang->line('Actualizar estado') , $cmd);
	}

	/**
	 * Comprueba el estado de los SMS envíados
	 * @return JSON
	 */
	function check_task()
	{
		set_time_limit(0);
		$datos = $this->reg->get(0, 10, null, null, 'bDone=0');
		$this->load->library('SmsServer');
		$count = 0;
		foreach($datos as $data)
		{
			$status = $this->smsserver->status($data['nIdSMS']);
			if ($status['ok'])
			{
				if (is_object($status['status']))
				{
					$upd = array(
						'bDone' 	=> 1,
						'cEstado' 	=> $status['status']->str_status(), 
						'dEnviado' 	=> $status['status']->get_sms_received_timestamp()
					);
				}
				else
				{
					$upd['bDone'] = 1;
				}

				$this->reg->update($data['nIdSMS'], $upd);
				++$count;
			}
		}
		$this->out->success(sprintf($this->lang->line('sms-checked-ok'), $count));
	}

	/**
	 * Comprueba el estado de los SMS envíados
	 * @return JSON
	 */
	function test_status($id)
	{
		set_time_limit(0);
		$this->load->library('SmsServer');
		$status = $this->smsserver->status($id);
		print '<pre>'; print_r($status); print '</pre>';
	}
}
/* End of file sms.php */
/* Location: ./system/application/controllers/comunicaciones/sms.php */
