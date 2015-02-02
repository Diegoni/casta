<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	calendario
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Sábados
 *
 */
class M_sabado extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';
	
	/**
	 * Cosntructor
	 * 
	 * @return M_sabado
	 */
	function __construct()
	{
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.calendario.database');
		$data_model = array(
			'dDia'				=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),		
			'nTurno'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT)
		);
		parent::__construct($this->prefix . 'Sabados', 'nIdSabado', 'dDia DESC', null, $data_model);
		$this->_cache = TRUE;
	}

	/**
	 * Crea los sábados de un año, sino estaban creados
	 * @param  int $year Año a crear
	 * @return FALSE: ha habido error, int: número de sábados creados
	 */
	function crear($year)
	{
		$desde = mktime(0, 0, 0, 1, 1,  $year);
		$hasta = mktime(0, 0, 0, 1, 1,  $year + 1);

		$this->db->trans_begin();
		$count = 0;
		while (($desde < $hasta))
		{

			if (date('w', $desde) == 6)
			{
				//echo format_date($desde) . "\n"; 
				$fecha = format_mssql_date($desde);
				$esta = $this->get(null, null, null, null, "dDia>={$fecha} AND dDia < " . $this->db->dateadd('d', 1, $fecha));
				if (count($esta) == 0)
				{
					if (!$this->insert(array('dDia' => $desde)))
					{
						$this->db->trans_rollback();
						return FALSE;
					}
					++$count;
				}
			}
			$desde = dateadd($desde, 1);
		}
		$this->db->trans_commit();
		return $count;
	}
}

/* End of file M_sabado.php */
/* Location: ./system/application/models/calendario/M_sabado.php */