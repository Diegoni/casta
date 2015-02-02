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
 * Dias festivos
 *
 */
class M_Dia extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';
	
	/**
	 * Constructor
	 * @return MY_Model
	 */
	function __construct()
	{
		$data_model = array(
			'dDia'			=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE, DATA_MODEL_DEFAULT => TRUE),		
		);
		//CI
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.calendario.database');
		parent::__construct($this->prefix . 'Calendario', 'nIdDia', 'dDia', 'dDia', $data_model);
		$this->_cache = TRUE;
	}

	/**
	 * Crea un calendario. Crea todos los días del año indicado
	 * @param int $year Año a crear
	 * @return int -1: Ya existe el año, 0 error base de datos, >0 número de días creados
	 */
	function create_year($year)
	{
		set_time_limit(0);
		$d = mktime(0, 0, 0, 1, 1, $year);
		$this->db->select('COUNT(*) as ct')
			->from($this->_tablename)
			->where('YEAR(dDia)', (int)$year);
		$q = $this->db->get();
		$q = $this->_get_results($q);
		//echo '<pre>';print_r($q); echo '</pre>';
		if (isset($q[0]) && $q[0]['ct'] > 0)
		{
			return -1;
		}
		$this->load->helper('date');
		$this->db->trans_begin();
		try {
			$count = 0;
			while (date('Y', $d) == $year)
			{
				$data['dDia'] = $d;
				$this->insert($data);
				$count++;
				$d = dateadd($d, 1);				
			}
			$this->db->trans_commit();
		}
		catch(Exception $e)
		{
			$this->db->trans_rollback();
			return 0;
		}
		return $count;
	}
	
	/**
	 * Años en el calendario
	 * @return array
	 */
	function years()
	{
		$this->db->flush_cache();
		$this->db->select('YEAR(dDia) as id, YEAR(dDia) as text')
			->from($this->_tablename)
			->group_by('YEAR(dDia)')
			->order_by('YEAR(dDia) DESC');
		$q = $this->db->get();
		$q = $this->_get_results($q);
		return $q;
		
	}
}

/* End of file M_dia.php */
/* Location: ./system/application/models/calendario/M_dia.php */