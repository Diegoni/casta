<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	controllers
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Temas
 *
 */
class TemasController extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return TemasController
	 */
	function __construct($auth, $model)
	{
		parent::__construct($auth, $model, TRUE);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#get_list($start, $limit, $sort, $dir, $where)
	 */
	function get_list($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));

		$id	= isset($id)?$id:$this->input->get_post('id');

		if (isset($id) && ($id != ''))
		{
			$data = $this->reg->get_list($id);
			$res = array(
				'success' 		=> TRUE,
				'value_data' 	=> $data
			);
		}
		else
		{
			$res = array(
				'success' 		=> FALSE,
				'message' 		=> sprintf($this->lang->line('registro_no_encontrado'), $id)
			);
		}
		// Respuesta
		echo $this->out->send($res);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#upd()
	 */
	function upd($id = null, $idtema = null, $value = null)
	{
		$this->userauth->roleCheck(($this->auth .'.upd'));
		
		$id		= isset($id)?$id:$this->input->get_post('id');
		$idtema	= isset($idtema)?$idtema:$this->input->get_post('nIdTema');
		$value	= isset($value)?$value:$this->input->get_post('value');
		
		$res = $this->reg->add($id, $idtema, $value);

		// Respuesta
		if ($res === TRUE)
		{
			$ajax_res = array(
				'success' 	=> true,
				'message'	=> sprintf($this->lang->line('registro_actualizado'), $idtema),
				'id'		=> (int) $idtema
			);
		}
		else
		{
			$ajax_res = array(
				'success' 	=> false,
				'message'	=> $res,
			);
		}
		echo $this->out->send($ajax_res);
	}
}
/* End of file TemasController.php */
/* Location: ./system/application/controllers/TemasController.php */