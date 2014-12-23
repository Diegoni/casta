<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Comandos de usuario
 *
 */
class Comando extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Comando
	 */
	function __construct()
	{
		parent::__construct('sys.comando', 'sys/m_comando', TRUE, null, 'Comandos', 'sys/submenucomandos.js');
	}

	/**
	 * Marca un comando como ejecutado
	 * @param int $id Id del comando
	 * @return JSON
	 */
	function ejecutado( $id = null )
	{
		$this->userauth->roleCheck(($this->auth . '.ejecutado'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$this->load->library('Comandos');
			$this->comandos->ejecutado($id);
			$this->out->success();
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}
	
	/**
	 * Devuelve los comandos ejecutados
	 * @return HTML_FILE
	 */
	/*function get_list()
	{
		$this->userauth->roleCheck(($this->auth . '.get_list'));
		$this->load->library('Comandos');
		$data = $this->comandos->get_list();
		$message = $this->load->view('sys/comandos', array('comandos' => $data), TRUE);
		echo $this->out->html_file($message, $this->lang->line('Cola de comandos'), 'iconoComandosTab');
	}*/

	/**
	 * Ejecuta de nuevo el comando indicado
	 * @param int $id Id del comando a ejecutar
	 * @return JSON
	 */
	function runcmd($id = null)
	{
		$this->userauth->roleCheck(($this->auth . '.runcmd'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$this->load->library('Comandos');
			$cmd = $this->comandos->get($id);
			if (isset($cmd))
			{
				header("Content-length: ". $cmd['tComando']);
				header('Content-type: application/json');
				echo $cmd['tComando'];
				return;
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
}
/* End of file comando.php */
/* Location: ./system/application/controllers/sys/comando.php */