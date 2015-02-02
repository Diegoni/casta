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
 * Calendario
 *
 */
class M_Calendario extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Cosntructor
	 *
	 * @return M_Calendario
	 */
	function __construct()
	{
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.calendario.database');
		$data_model = array(
			'nIdTrabajador'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'calendario/trabajador/search')),		
			'nIdDia'			=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'calendario/dia/search')),		
			'fHoras'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),			
			'cComentario'		=> array(DATA_MODEL_DEFAULT => TRUE),
			'bTarde'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN)		
		);
		parent::__construct($this->prefix . 'TrabajadorCalendario', 'nIdTrabajadorCalendario', 'nIdDia', null, $data_model);
	}

	/**
	 * Obtiene el calendario de un trabajador de una fecha a otra
	 * @param int $id ID de trabajador
	 * @param datetime $fecha1 Fecha de inicio
	 * @param datetime $fecha2 Fecha final
	 * @return array
	 */
	function calendario($id, $fecha1, $fecha2, $grupo = null, $sort = null, $dir = null)
	{
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		
		$this->db->flush_cache();
		$this->db->select('nIdTrabajadorCalendario id,
			tc.nIdTrabajador,
			tr.cNombre cTrabajador,
			f.nIdFestivo, 
			v.nIdVacaciones, 
			fHoras,
			bTarde,
			cComentario,
			f.cDescripcion')
		->from($this->prefix . 'Calendario c')
		->join($this->prefix . 'TrabajadorCalendario tc', 'c.nIdDia = tc.nIdDia', 'left')
		->join($this->prefix . 'Trabajadores tr', 'tr.nIdTrabajador = tc.nIdTrabajador', 'left')
		->join($this->prefix . 'Festivos f', $this->db->date('f.dDia') .' = ' . $this->db->date('c.dDia'), 'left')
		->join($this->prefix . 'Vacaciones v', $this->db->date('v.dDia') .' = ' . $this->db->date('c.dDia') .' AND v.nIdTrabajador = tc.nIdTrabajador', 'left')
		->where('c.dDia >= ' . $fecha1)
		->where("c.dDia < " . $this->db->dateadd('d', 1, $fecha2));
		$this->db->select($this->_date_field('c.dDia', 'dDia'));
		
		if (!empty($grupo))
		{
			$this->db->where("tr.nIdGrupo = {$grupo}");
		}
		
		if (!empty($sort))
		{
			$alias = array(
				'Dia' 				=> 'DAY(dw, c.dDia)',
				'dDia2'				=> 'c.dDia',
				'Numero'			=> 'DAY(c.dDia)',
				'Mes'				=> 'MONTH(m, c.dDia)',
				'MesNumero'			=> 'MONTH(c.dDia)',
				'cTrabajador'		=> 'tr.cNombre',
				'nIdTrabajador'		=> 'tc.nIdTrabajador',
				'nIdFestivo'		=> 'f.nIdFestivo',
				'id'				=> 'nIdTrabajadorCalendario',
				'cDescripcion'		=> 'f.cDescripcion');
			$this->_fix_sort($sort, $alias);
			if (!isset($dir)) $dir = 'ASC';
			$this->db->order_by($sort, $dir);
		}
		else
		{
			$this->db->order_by('c.dDia');
		}

		if ($id && ($id!=''))
		{
			$this->db->where('tc.nIdTrabajador', (int) $id);
		}
		/*else
		{
			$this->db->where('tc.nIdTrabajador IS NOT NULL');
		}*/

		$query = $this->db->get();
		#echo $this->db->last_query(); die();
		$data = $this->_get_results($query);
		foreach($data as $k => $v)
		{
			$data[$k]['Dia'] 		= string_encode(strftime('%A', $v['dDia']));
			$data[$k]['Numero'] 	= date('N', $v['dDia']);
			$data[$k]['Mes'] 		= string_encode(strftime('%B', $v['dDia']));
			$data[$k]['MesNumero'] 	= date('m', $v['dDia']);
			$data[$k]['dDia2'] 		= format_date($v['dDia']);
		}
		return $data;
	}

	/**
	 * Devuelve los trabajadores que están en un periodo de tiempo dado
	 * @param date $fecha1 Fecha inicio
	 * @param date $fecha2 Fecha final
	 * @return array
	 */
	function personal_dia($fecha1, $fecha2)
	{
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		$this->db->select('c.dDia, ' . $this->db->date('c.dDia') .' dDia2,
			DATEPART(dw, c.dDia) dw,
			g.cDescripcion cGrupo,
			cNombre,
			f.nIdFestivo, 
			v.nIdVacaciones, 
			fHoras,
			bTarde,
			f.cDescripcion')
		->from($this->prefix . 'Calendario c')
		->join($this->prefix . 'TrabajadorCalendario tc', 'c.nIdDia = tc.nIdDia')
		->join($this->prefix . 'Trabajadores t', 't.nIdTrabajador = tc.nIdTrabajador')
		->join($this->prefix . 'GruposTrabajadores g', 't.nIdGrupo = g.nIdGrupo')
		->join($this->prefix . 'Festivos f', $this->db->date('f.dDia') .' = ' . $this->db->date('c.dDia') , 'left')
		->join($this->prefix . 'Vacaciones v', $this->db->date('v.dDia') .' = ' . $this->db->date('c.dDia') .' AND v.nIdTrabajador = t.nIdTrabajador', 'left')
		->where('c.dDia >= ' . $fecha1)
		->where("c.dDia < " . $this->db->dateadd('d', 1, $fecha2))
		->where('t.bActivo = 1')
		->where('v.nIdVacaciones IS NULL')
		->where('f.nIdFestivo IS NULL')
		->where('ISNULL(tc.fHoras,0) > 0')
		->order_by('c.dDia, g.nOrden, cNombre');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Estado de las horas de todos los trabajadores en un año
	 * @param int $year Año
	 * @return array
	 */
	function estado_horas($year)
	{
		$this->db->select('*')
		->from("{$this->prefix}HorasCalendarioTotales")
		->where("nAnno = {$year}")
		->order_by('cNombre');
		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}

	/**
	 * Copia el calendario de un trabajador al otro. Incluye vacaciones
	 * @param  int $origen  Id trabajador origen
	 * @param  int $destino Id trabajador destino
	 * @param  int $year    Año a copiar
	 * @return bool
	 */
	function copiar($origen, $destino, $year)
	{
		# Borra el anterior calendario
		$sql =($this->db->dbdriver == 'mssql')?"DELETE {$this->prefix}TrabajadorCalendario
				WHERE nIdTrabajador = {$destino} AND nIdDia IN (
				SELECT c.nIdDia
				FROM {$this->prefix}Calendario c
				WHERE YEAR(c.dDia) = {$year})":
				"DELETE tc
				FROM {$this->prefix}TrabajadorCalendario tc
					INNER JOIN {$this->prefix}Calendario c
						ON tc.nIdDia = c.nIdDia
				WHERE tc.nIdTrabajador = {$destino} 
					AND YEAR(c.dDia) = {$year}";
		$this->db->flush_cache();
		$this->db->query($sql);

		# Y copia el otro calendario
		$sql ="INSERT INTO {$this->prefix}TrabajadorCalendario(nIdDia, nIdTrabajador, fHoras)
				SELECT c.nIdDia, {$destino}, tc.fHoras
				FROM {$this->prefix}TrabajadorCalendario tc
					INNER JOIN {$this->prefix}Calendario c
						ON tc.nIdDia = c.nIdDia
				WHERE tc.nIdTrabajador = {$origen} 
				AND YEAR(c.dDia) = {$year}";

		$this->db->flush_cache();
		$this->db->query($sql);

		# Borra las vacaciones
		$sql ="DELETE FROM {$this->prefix}Vacaciones
				WHERE YEAR(dDia) = {$year}
					AND nIdTrabajador = {$destino} ";
		$this->db->flush_cache();
		$this->db->query($sql);

		# Y copia las vacaciones del origen
		$sql ="INSERT INTO {$this->prefix}Vacaciones(dDia, nIdTrabajador)
				SELECT dDia, {$destino}
				FROM {$this->prefix}Vacaciones
				WHERE nIdTrabajador = {$origen} 
				AND YEAR(dDia) = {$year}";

		$this->db->flush_cache();
		$this->db->query($sql);

		# Borra las horas anuales
		$sql ="DELETE FROM {$this->prefix}HorasAnualesTrabajador
				WHERE nAnno = {$year}
					AND nIdTrabajador = {$destino} ";
		$this->db->flush_cache();
		$this->db->query($sql);

		# Y copia las horas del origen
		$sql ="INSERT INTO {$this->prefix}HorasAnualesTrabajador(nAnno, nIdTrabajador, fHoras)
				SELECT nAnno, {$destino}, fHoras
				FROM {$this->prefix}HorasAnualesTrabajador
				WHERE nIdTrabajador = {$origen} 
				AND nAnno = {$year}";

		$this->db->flush_cache();
		$this->db->query($sql);


		return TRUE;
	}

}

/* End of file M_calendario.php */
/* Location: ./system/application/models/calendario/M_calendario.php */