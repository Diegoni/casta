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
 * Ofertas
 *
 */
class Oferta extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Oferta
	 */
	function __construct()
	{
		parent::__construct('catalogo.oferta', 'catalogo/M_oferta', TRUE, 'catalogo/ofertas.js', 'Ofertas');
	}
	
	function del_items($id = null)
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.del'));
		}

		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$res = TRUE;
			if (is_string($id))
			{
				$ids = preg_split('/\;/', $id);
			}
			else
			{
				$ids[] = $id;
			}
			$this->db->trans_begin();
			$this->load->model('catalogo/m_articulo');
			$count = 0;
			foreach($ids as $i)
			{
				if (is_numeric($i))
				{
					if (!$this->m_articulo->update($i, array('nIdOferta' => null)))
					{
						$res = $this->reg->error_message();
						break;
					}
					$count++;
				}
			}
			if ($res === TRUE)
			{
				$this->db->trans_commit();
			}
			else
			{
				$this->db->trans_rollback();
			}
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}

		if ($res === TRUE)
		{
			if (count($ids) == 1)
			{
				$this->out->success(sprintf($this->lang->line('registro_eliminado'), $ids[0]));
			}
			else
			{
				$this->out->success(sprintf($this->lang->line('registro_eliminado_varios'), $count));
			}
		}
		else
		{
			$this->out->error($res);
		}		
	}
}

/* End of file Oferta.php */
/* Location: ./system/application/controllers/catalogo/Oferta.php */
