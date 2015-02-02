<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	eoi
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Importes EOI
 *
 */
class M_Importe extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Importe
	 */
	function __construct()
	{
		$data_model = array(
			'nIdEOI'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'eoi/escuela/search', 'cEscuela')), 			
			'cConcepto'		=> array(DATA_MODEL_REQUIRED => TRUE),
			'dFecha'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'fImporte'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),		
			'fEntrada'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_NO_LIST => TRUE),		
			'fSalida'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_NO_LIST => TRUE),		
		);

		parent::__construct('Ext_EOISImportes', 'nIdImporte', 'dFecha DESC', 'dFecha', $data_model);
		$this->_cache = TRUE;
	}

	/**
	 * Suma de los importes de las escuelas a una fecha dada
	 * @param date $fecha Fecha límite de los albaranes
	 * @return array
	 */
	function totales($fecha = null)
	{
		$this->db->flush_cache();
		$this->db->select_sum($this->_tablename . '.fImporte', 'fImporte')
		->select('Ext_EOIS.nIdEOI, Ext_EOIS.cDescripcion')
		->from($this->_tablename)
		->join('Ext_EOIS', $this->_tablename . '.nIdEOI = Ext_EOIS.nIdEOI')
		->group_by('Ext_EOIS.nIdEOI, Ext_EOIS.cDescripcion')
		->order_by('Ext_EOIS.cDescripcion');

		if(isset($fecha) && $fecha != '')
		{
			$fecha = format_mssql_date($fecha);
			$this->db->where("{$this->_tablename}.dFecha < " . $this->db->dateadd('d', 1, $fecha));
		}
		
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;				
	}

	/**
	 * Trigger llamado antes de actualizar los datos
	 * @param int $id Id del registro actualizado
	 * @param array $data Registro
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			if (isset($data['fEntrada']))
			{
				$data['fImporte'] = $data['fEntrada'];
				unset($data['fEntrada']);
			}
			if (isset($data['fSalida']))
			{
				$data['fImporte'] = $data['fSalida'];
				unset($data['fSalida']);
			}
			return TRUE;
		}

		return FALSE;
	}
}

/* End of file M_importe.php */
/* Location: ./system/application/models/eoi/M_importe.php */