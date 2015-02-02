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
 * Trabajador
 *
 */
class M_Trabajador extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';
	/**
	 * Constructor
	 * @return M_trabajador
	 */
	function __construct()
	{
		$data_model = array(
			'cNombre'			=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_DEFAULT => TRUE),
			'dInicio'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'dFinal'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'cUsername'			=> array(),		
			'bActivo'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),		
			'nIdTurno'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'calendario/turno/search')),		
			'nIdGrupo'			=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'calendario/grupostrabajador/search')),		
			'nIdEmail'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'calendario/email/search')),		
			'cTelefonoFijo'		=> array(),		
			'cTelefonoMovil'	=> array(),
			'cExtension'		=> array(),
			'cEmail'			=> array(),
			'tNotas'			=> array()		
		);
		//CI
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.calendario.database');

		parent::__construct($this->prefix . 'Trabajadores', 'nIdTrabajador', 'cNombre', 'cNombre', $data_model);
		$this->_cache = TRUE;
	}

	/**
	 * Crea los días del calendario que no no tienen información en un periodo dado
	 * @param int $id Id del trabajador
	 * @param date $desde Fecha de inicio
	 * @param date $hasta Fecha final
	 */
	protected function _completar_calendario($id, $desde, $hasta)
	{
		//Crea los días que no se han indicado.
		$sql ="INSERT INTO {$this->prefix}TrabajadorCalendario(nIdDia, nIdTrabajador, fHoras)
				SELECT c.nIdDia, {$id}, 0
				FROM {$this->prefix}Calendario c
				WHERE c.dDia >= {$desde}
				AND c.dDia < " . $this->db->dateadd('d', 1, $hasta) . "
				AND nIdDia NOT IN (SELECT nIdDia FROM {$this->prefix}TrabajadorCalendario WHERE nIdTrabajador = {$id})";
		$this->db->flush_cache();
		$this->db->query($sql);
	}

	/**
	 * Crea un calendario
	 * @param int $id Id del trabajador
	 * @param int $year
	 * @param date $desde
	 * @param array $dias
	 * @param array $turnos
	 * @return bool
	 */
	function crear_calendario($id, $desde, $hasta, $dias, $turnos)
	{
		// Comprueba si hay días alternos
		$this->db->trans_begin();

		try {
			// Borra el anterior
			$this->db->flush_cache();
			if ($this->db->dbdriver == 'mssql')
				$this->db->query('SET LANGUAGE Spanish');
			$desde = format_mssql_date($desde);
			$hasta = format_mssql_date($hasta);
			$sql = ($this->db->dbdriver == 'mssql')?"DELETE {$this->prefix}TrabajadorCalendario
			FROM {$this->prefix}TrabajadorCalendario tc (NOLOCK)
			INNER JOIN {$this->prefix}Calendario c (NOLOCK) ON tc.nIdDia = c.nIdDia
			WHERE nIdTrabajador = {$id}
			AND dDia >= {$desde}
			AND dDia < " . $this->db->dateadd('d', 1, $hasta):
			"DELETE  tc
			FROM {$this->prefix}TrabajadorCalendario tc (NOLOCK)
			INNER JOIN {$this->prefix}Calendario c (NOLOCK) ON tc.nIdDia = c.nIdDia
			WHERE nIdTrabajador = {$id}
			AND dDia >= {$desde}
			AND dDia < " . $this->db->dateadd('d', 1, $hasta);

			$this->db->flush_cache();
			$this->db->query($sql);

			//Crea los nuevos
			foreach ($dias as $i => $d)
			{
				$d = explode(' ', $d);
				if ((isset($d[0]) && (float)$d[0] > 0) || $d[0] == '0')
				{
					$t = $turnos[$i];
					$t = ($t === 'true' || $t === '1' || $t === 'on') ? '1' : '0';
					$i++;
					$sql = "INSERT INTO {$this->prefix}TrabajadorCalendario(nIdDia, nIdTrabajador, fHoras, bTarde)
					SELECT c.nIdDia, {$id}, {$d[0]}, {$t}
					FROM {$this->prefix}Calendario c (NOLOCK)
						LEFT JOIN {$this->prefix}Festivos d
							ON year(d.dDia) = year(c.dDia) and month(d.dDia) = month(c.dDia) and day(d.dDia) = day(c.dDia)
					WHERE DATEPART(dw, c.dDia) = {$i} 
						AND c.dDia >= {$desde}
						AND d.nIdFestivo IS NULL
						AND c.dDia < " . $this->db->dateadd('d', 1, $hasta);
					#echo '<pre>' . $sql . '</pre>';
					$this->db->flush_cache();
					$this->db->query($sql);
					// Alternativos?
					$flag = 0;
					if (count($d)>1)
					{
						//Lee todos los días
						$this->db->flush_cache();
						$this->db->select('tc.nIdTrabajadorCalendario id')
						->select('d.nIdFestivo')
						->from("{$this->prefix}TrabajadorCalendario tc")
						->join("{$this->prefix}Calendario c","tc.nIdDia = c.nIdDia")
						->join("{$this->prefix}Festivos d","year(d.dDia) = year(c.dDia) and month(d.dDia) = month(c.dDia) and day(d.dDia) = day(c.dDia)", 'left')
						->where("nIdTrabajador = {$id}")
						->where("DATEPART(dw, c.dDia) = {$i}")
						->where("c.dDia >= {$desde}")
						->where("c.dDia < " . $this->db->dateadd('d', 1, $hasta))
						->order_by("c.dDia");

						$q = $this->db->get();
						$r = $this->_get_results($q);
						#var_dump($r); die();
						foreach($r as $dia)
						{
							$data['fHoras'] = !empty($dia['nIdFestivo'])?0:$d[$flag];
							$data['bTarde'] = $t;

							$this->db->flush_cache();
							$this->db->where("nIdTrabajadorCalendario = {$dia['id']}");
							$this->db->update("{$this->prefix}TrabajadorCalendario", $data);
							$flag++;
							if ($flag >= count($d)) $flag = 0;
						}
					}
				}
			}

			//Crea los días que no se han indicado.
			$this->_completar_calendario($id, $desde, $hasta);

			$this->db->trans_commit();
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Crea las vacaciones
	 * @param int $id Id del trabajador
	 * @param date $desde Fecha de inicio
	 * @param date $hasta Fecha final
	 * @return bool
	 */
	function crear_vacaciones($id, $desde, $hasta)
	{
		$this->db->trans_begin();

		try {
			// Borra lo anterior
			$d = format_mssql_date($desde);
			$h = format_mssql_date($hasta);

			$this->db->flush_cache();
			$this->db->where("dDia >= $d")
			->where("dDia < " . $this->db->dateadd('d', 1, $h))
			->where("nIdTrabajador = $id");
			$this->db->delete("{$this->prefix}Vacaciones");

			// Creae el nuevo
			$count = 0;
			#var_dump($desde, $hasta); die();
			$hasta = dateadd($hasta, 1);
			while (($desde < $hasta))
			{
				$data['dDia'] = format_mssql_date($desde, FALSE);
				$data['nIdTrabajador'] = (int) $id;
				$this->db->insert("{$this->prefix}Vacaciones", $data);
				$count++;
				$this->load->helper('date');
				$desde = dateadd($desde, 1);
			}
			$this->db->trans_commit();
			return $count;
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			return FALSE;
		}
	}

	/**
	 * Genera un resumen de horas de un año
	 * @param int $id Id del trabajador
	 * @param int $year Año
	 * @return array
	 */
	function trabajadas($id, $year)
	{
		$this->db->flush_cache();
		$this->db->select_sum('tc.fHoras', 'fHoras')
		->from("{$this->prefix}TrabajadorCalendario tc")
		->join("{$this->prefix}Calendario c", 'c.nIdDia = tc.nIdDia')
		->join("{$this->prefix}Festivos f", $this->db->date('f.dDia') . ' = ' . $this->db->date('c.dDia'), 'left')
		->join("{$this->prefix}Vacaciones v", $this->db->date('v.dDia') . ' = ' . $this->db->date('c.dDia') . ' AND v.nIdTrabajador = tc.nIdTrabajador', 'left')
		->where('v.nIdVacaciones IS NULL')
		#->where('f.nIdFestivo IS NULL')
		->where('tc.nIdTrabajador=' . $id)
		->where('YEAR(c.dDia)=' . $year);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		
		return (count($data) > 0)?$data[0]['fHoras']:0;
	}

	/**
	 * Genera los sábados de un trabajador
	 *
	 * @param int $id Id del trabajador
	 * @param int $year Año
	 * @param dete $desde Fecha de inicio
	 *
	 * @return array
	 */
	function crear_sabados($id, $desde, $hasta, $horas)
	{
		$this->db->trans_begin();

		try {
			$desde = format_mssql_date($desde);
			$hasta = format_mssql_date($hasta);

			$filter = (isset($id) && $id != '')?"{$this->prefix}TrabajadorCalendario.nIdTrabajador = {$id} AND ":'';
			// Borra el anterior
			$sql = ($this->db->dbdriver == 'mssql')?"DELETE {$this->prefix}TrabajadorCalendario
				FROM {$this->prefix}TrabajadorCalendario tc (NOLOCK)
					INNER JOIN {$this->prefix}Calendario c (NOLOCK)
						ON tc.nIdDia = c.nIdDia
					INNER JOIN {$this->prefix}Sabados s (NOLOCK)
						ON s.dDia = c.dDia
				WHERE {$filter} s.dDia >= {$desde}
					AND s.dDia < " . $this->db->dateadd('d', 1, $hasta):

					"DELETE  {$this->prefix}TrabajadorCalendario
					FROM {$this->prefix}TrabajadorCalendario
					INNER JOIN {$this->prefix}Calendario
						ON {$this->prefix}TrabajadorCalendario.nIdDia = {$this->prefix}Calendario.nIdDia
					INNER JOIN {$this->prefix}Sabados 
						ON {$this->prefix}Sabados.dDia = {$this->prefix}Calendario.dDia
				WHERE {$filter} {$this->prefix}Sabados.dDia >= {$desde}
					AND {$this->prefix}Sabados.dDia < " . $this->db->dateadd('d', 1, $hasta);

			#var_dump($sql); die();
			$this->db->query($sql);

			// Añade los nuevos
			$filter = (isset($id) && $id != '')?"t.nIdTrabajador = {$id} AND ":'';
			$sql = "INSERT INTO {$this->prefix}TrabajadorCalendario (nIdTrabajador, nIdDia, fHoras)
				SELECT t.nIdTrabajador, c.nIdDia, {$horas}
				FROM {$this->prefix}Trabajadores t (NOLOCK)
					INNER JOIN {$this->prefix}Turnos tn (NOLOCK)
						ON t.nIdTurno = tn.nIdTurno
					INNER JOIN {$this->prefix}Sabados s (NOLOCK)
						ON tn.cSabados LIKE " . $this->db->concat(array("'%'", $this->db->varchar('s.nTurno'), "'%'")) . "
							AND ISNULL(s.nTurno, '') <> ''
					INNER JOIN {$this->prefix}Calendario c (NOLOCK)
						ON s.dDia = c.dDia
				WHERE {$filter}
					s.dDia >= {$desde}
					AND s.dDia < " . $this->db->dateadd('d', 1, $hasta);
			//echo $sql;
			$this->db->query($sql);

			// Añadir los sábados que no trabaja a 0
			if ((isset($id) && $id != '')) $this->_completar_calendario($id, $desde, $hasta);

			#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();

			$this->db->trans_commit();
			return TRUE;
		}
		catch (Exception $e)
		{
			$this->db->trans_rollback();
			return FALSE;
		}
	}
	
	/**
	 * Genera un resumen de horas de un año
	 * @param int $id Id del trabajador
	 * @param int $year Año
	 * @param dete $desde Fecha de inicio
	 * @return bool
	 */
	function eliminar_calendario($id, $desde, $hasta)
	{
		$desde = format_mssql_date($desde);
		$hasta = format_mssql_date($hasta);

		$sql =($this->db->dbdriver == 'mssql')?"DELETE {$this->prefix}TrabajadorCalendario
				WHERE nIdTrabajador = {$id} AND nIdDia IN (
				SELECT c.nIdDia
				FROM {$this->prefix}Calendario c
				WHERE dDia >= {$desde}
				AND dDia < " . $this->db->dateadd('d', 1, $hasta) . ")":
			"DELETE tc
			FROM {$this->prefix}TrabajadorCalendario tc
				INNER JOIN {$this->prefix}Calendario c
					ON tc.nIdDia = c.nIdDia
			WHERE tc.nIdTrabajador = {$id} 
				AND c.dDia >= {$desde}
				AND c.dDia < " . $this->db->dateadd('d', 1, $hasta);
		$this->db->flush_cache();
		$this->db->query($sql);
		#echo $sql;

		return TRUE;
	}
}

/* End of file M_trabajador.php */
/* Location: ./system/application/models/calendario/M_trabajador.php */