<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	mailing
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Contactos
 * @author alexl
 *
 */
class Contacto extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Contacto
	 */
	function __construct()
	{
		parent::__construct('mailing.contacto', 'mailing/M_Contacto', TRUE, 'mailing/contacto.js', 'Contactos');
	}

	/**
	 * Realiza una búsqueda por palabra clave (ampliada)
	 *
	 * @param string $query Palabra de búsqueda
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $order Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param string $where Campos WHERE
	 */
	function search2($query = null, $start = null, $limit = null, $sort = null, $dir = null, $where = null)
	{
		$query	= isset($query)?$query:$this->input->get_post('query');
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		$where 	= isset($where)?$where:$this->input->get_post('where');

		$this->reg->_addemail = TRUE;
		parse_str($where, $fields);
		if (isset($fields['text']) && (!isset($query) || ($query == '')))
		{
			$query = $fields['text'];
			unset($fields['text']);
		}
		return $this->search($query, $start, $limit, $sort, $dir, $fields);
	}
}

/* End of file contacto.php */
/* Location: ./system/application/controllers/mailing/contacto.php */