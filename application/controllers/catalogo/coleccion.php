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
 * Coleccions de libro
 *
 */
class Coleccion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Coleccion
	 */
	function __construct()
	{
		parent::__construct('catalogo.coleccion', 'catalogo/M_coleccion', TRUE, 'catalogo/coleccion.js', 'Colecciones');
	}

	/**
	 * Unificador de colecciones
	 * @param int $id1 Id de la colección destino
	 * @param string $id2 Ids de las colecciones repetidas, separadas por ;
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
					$this->logger->log("Colección unificada {$id2} con {$id1}", 'unificador');
				}
			}
			$this->out->success($this->lang->line('coleccion-unificados-ok'));
		}
		else
		{
			$data['title'] = $this->lang->line('Unificar colección');
			$data['icon'] = 'iconoUnficarColeccionTab';
			$data['url_search'] = site_url('catalogo/coleccion/search');
			$data['url'] = site_url('catalogo/coleccion/unificar');
			$this->_show_form('unificar', 'catalogo/unificador.js', $this->lang->line('Unificar colección'), null, null, null, $data);
		}
	}
}

/* End of file coleccion.php */
/* Location: ./system/application/controllers/catalogo/coleccion.php */
