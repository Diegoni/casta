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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Secciones de un artículo
 *
 */
class ArticuloSeccion extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return ArticuloSeccion
	 */
	function __construct()
	{
		parent::__construct('catalogo.articuloseccion', 'catalogo/M_articuloseccion', true, null, 'Secciones artículo');
	}

	/**
	 * Pone a 0 el stock de los genéricos
	 * @return MSG
	 */
	function genericos()
	{
		$count = $this->reg->genericos();
		if ($count===FALSE)
		{
			$this->out->error($this->reg->error_message());
		}
		$this->out->success(sprintf($this->lang->line('genericos-reset-ok'), $count));
	}

}

/* End of file ArticuloSeccion.php */
/* Location: ./system/application/controllers/catalogo/ArticuloSeccion.php */