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
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Perfiles
 *
 */
class PerfilController extends MY_Controller
{

	/**
	 * Configuración de los perfiles
	 * @var array
	 */
	protected $_config;
	
	/**
	 * Nombre del campo de Id de cruce de los registros
	 * @var string
	 */
	protected $_idref;
	
	/**
	 * Constructor
	 *
	 * @return PerfilController
	 */
	function __construct($auth)
	{
		parent::__construct($auth, null, TRUE, null, null);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#get_list($start, $limit, $sort, $dir, $where)
	 */
	function get_list($id = null, $long = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));

		$id	= isset($id)?$id:$this->input->get_post('id');
		$long	= isset($long)?$long:$this->input->get_post('long');
		
		if (isset($id) && ($id != ''))
		{
			$data = array();
			foreach($this->_config as $k => $v)
			{
				$this->load->model($v[1], $k);
				$d = $this->$k->get_list($id, $long);
				if (isset($d))
				{
					$data = array_merge($data, $d);
				}
			}
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
	 * Obtiene los datos de un perfil
	 * @param int $id Id del perfil
	 * @param string $tipo Tipo de perfil
	 */
	function get($id = null, $tipo = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$tipo	= isset($tipo)?$tipo:$this->input->get_post('tipo');

		if (isset($id) && ($id != ''))
		{
			$v = $this->_config[$tipo];
			if (isset($v))
			{
				$this->load->model($v[0], 'r1');
				#var_dump($v[0]); die();
				$data = $this->r1->load($id);
			}

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
	 * Añade un nuevo perfil
	 */
	function add()
	{
		$this->userauth->roleCheck(($this->auth .'.add'));

		$id_c		= isset($id_c)?$id_c:$this->input->get_post('id_c');
		$id			= isset($id)?$id:$this->input->get_post('id');
		$tipo		= isset($tipo)?$tipo:$this->input->get_post('tipo');

		if ($id_c && $tipo)
		{
			$v = $this->_config[$tipo];
			if (isset($v))
			{
				$this->_add_datos($v[0], $v[1], $v[2], $id, $id_c);
			}
		}
	}

	/**
	 * Añade un nuevo perfil (uso interno)
	 * @param string $reg Modelo de datos de cruce
	 * @param string $reg2 Modelo de datos final
	 * @param string $id_name Nombre del campo de enlace en la tabla final
	 * @param string $id Id del registro (si se actualiza)
	 * @param string $id_c Id del propietario del perfil (cliente, contacto, etc)
	 * @return JSON
	 */
	protected function _add_datos($reg, $reg2, $id_name, $id = null, $id_c = null)
	{
		$this->userauth->roleCheck(($this->auth .'.add'));

		$id	= isset($id)?$id:$this->input->get_post('id');
		$id_c	= isset($id_c)?$id_c:$this->input->get_post('id_c');

		$upd = is_numeric($id);

		$this->load->model($reg, 'r2');

		$this->db->trans_begin();

		if ($upd)
		{
			list($res, $id_new) = $this->_add_reg($this->r2, $id);
		}
		else
		{
			$this->load->model($reg2, 'r1');
			list($res, $id_new) = $this->_add_reg($this->r2);

			if ($res === TRUE)
			{
				list($res, ) = $this->_add_reg($this->r1, null, array($this->_idref => $id_c, $id_name => $id_new));
			}
		}
		if ($res === TRUE)
		{
			$this->db->trans_commit();
			$ajax_res = array(
				'success' 	=> true,
				'message'	=> sprintf($this->lang->line(($upd?'registro_actualizado':'registro_generado')), $id_new),
				'id'		=> (int) $id_new
			);
		}
		else
		{
			$this->db->trans_rollback();

			$ajax_res = array(
				'success' 	=> false,
				'message'	=> $res,
			);
		}

		$this->out->send($ajax_res);
	}

	/**
	 * Elimina un perfil
	 * @param int $id Id del perfil
	 * @param string $tipo Tipo de perfil
	 */
	function del($id = null, $tipo = null)
	{
		$this->userauth->roleCheck(($this->auth .'.del'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$tipo	= isset($tipo)?$tipo:$this->input->get_post('tipo');

		if ($id && $tipo)
		{
			$v = $this->_config[$tipo];
			if (isset($v))
			{
				$this->_del_datos($v[0], $v[1], $v[2],(isset($v[3])?$v[3]:$v[2]), $id);
			}
		}
	}

	/**
	 * Elimina un perfil (uso interno)
	 * @param string $reg Modelo de datos de cruce
	 * @param string $reg2 Modelo de datos final
	 * @param string $id_name Nombre del campo de enlace en la tabla final
	 * @param string $id_name2 Nombre del campo de enlace en la tabla de cruce
	 * @param string $id Id del registro a eliminar
	 */
	protected function _del_datos($reg, $reg2, $id_name, $id_name2, $id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.del'));

		$id		= isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$res = TRUE;
			$this->load->model($reg2, 'r1');

			$this->db->trans_begin();
			if (!$this->r1->delete_by("{$id_name} = {$id}"))
			{
				$res = $this->db->_error_message();
			}
			else
			{
				$this->load->model($reg, 'r2');
				if (!$this->r2->delete_by("{$id_name2} = {$id}"))
				{
					$res = $this->db->_error_message();
				}
			}
		}
		else
		{
			$res = sprintf($this->lang->line('mensaje_faltan_datos'));
		}

		// Respuesta
		if ($res === TRUE)
		{
			$this->db->trans_commit();
			$res = array(
				'success' 	=> true,
				'message'	=> sprintf($this->lang->line('registro_eliminado'), $id)
			);
		}
		else
		{
			$this->db->trans_rollback();
			$res = array(
				'success' 	=> false,
				'message'	=> $res
			);
		}
		// Respuesta
		echo $this->out->send($res);
	}
}
/* End of file perfilescontroller.php */
/* Location: ./system/application/controllers/perfilescontroller.php */