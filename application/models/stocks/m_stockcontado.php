<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	stocks
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Stock contado
 *
 */
class M_stockcontado extends MY_Model
{
	/**
	 * Costructor
	 * @return M_stockcontado
	 */
	function __construct()
	{
		$data_model = array(
			'nIdSeccion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'generico/seccion/search', 'cSeccion')),		
			'nIdLibro'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/articulo/search', 'cTitulo')),		
			'nIdTipoStock'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'stocks/tipostock/search', 'cTipoStock')),		
			'nCantidad' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'bDone' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
			'dCreacion' 	=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
		);

		parent::__construct('Cat_RegulacionStock', 'nIdRegulacionStock', 'nIdRegulacionStock', 'nIdRegulacionStock', $data_model);
	}
	
	/**
	 * Realiza un backup del stock contado
	 * @param string $name Nombre del backup
	 * @return mixed: FALSE ha habido error, string nombre del tabla de backup 
	 */
	function reset($name)
	{
		$name = "Cat_RegulacionStock{$name}";
		
		$sql = ($this->db->dbdriver == 'mssql')?"SELECT * INTO {$name} FROM Cat_RegulacionStock":
			"CREATE TABLE {$name} SELECT * FROM Cat_RegulacionStock";
		  
		$this->db->trans_begin();
		if (!$this->db->query($sql))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->db->error_message());
			return FALSE;
		}
		if (!$this->delete())
		{
			$this->db->trans_rollback();
			return FALSE;			
		}
		$this->db->trans_commit();
		return $name;
	}
	
	/**
	 * Devuelve el stock contado en las secciones
	 * @return array
	 */
	function stocks()
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Secciones.nIdSeccion, Cat_Secciones.cNombre')
		->from('Cat_Secciones')
		->order_by('Cat_Secciones.cNombre');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$data3 = array();
		
		foreach($data as $d)
		{
			$data3[$d['nIdSeccion']] = $d;
			$data3[$d['nIdSeccion']]['nStockFirme'] = 0;			
			$data3[$d['nIdSeccion']]['nStockDeposito'] = 0;			
		}
		
		$this->db->flush_cache();
		$this->db->select('Cat_Secciones.nIdSeccion, Cat_Secciones.cNombre')
		->select_sum('ISNULL(nCantidad, 0)', 'nStockFirme')
		->from('Cat_Secciones')
		->join('Cat_RegulacionStock', 'Cat_RegulacionStock.nIdSeccion=Cat_Secciones.nIdSeccion', 'left')
		->where('Cat_RegulacionStock.nIdTipoStock='. $this->config->item('bp.contarstocks.firme'))
		->group_by('Cat_Secciones.nIdSeccion, Cat_Secciones.cNombre')		
		->order_by('Cat_Secciones.cNombre');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		
		$this->db->flush_cache();
		$this->db->select('Cat_Secciones.nIdSeccion, Cat_Secciones.cNombre')
		->select_sum('ISNULL(nCantidad, 0)', 'nStockDeposito')
		->from('Cat_Secciones')
		->join('Cat_RegulacionStock', 'Cat_RegulacionStock.nIdSeccion=Cat_Secciones.nIdSeccion', 'left')
		->where('Cat_RegulacionStock.nIdTipoStock='. $this->config->item('bp.contarstocks.deposito'))
		->group_by('Cat_Secciones.nIdSeccion, Cat_Secciones.cNombre')		
		->order_by('Cat_Secciones.cNombre');
		$query = $this->db->get();
		$data2 = $this->_get_results($query);
		
		foreach($data as $d)
		{
			$data3[$d['nIdSeccion']]['nStockFirme'] = $d['nStockFirme'];			
		}
		foreach($data2 as $d)
		{
			$data3[$d['nIdSeccion']]['nStockDeposito'] = $d['nStockDeposito'];
		}
		return $data3;
	}
	
	/**
	 * Devuelve las diferencias de stock entre lo contado y lo actual
	 * @return array
	 */
	function diferencias()
	{
		$sql = 'SELECT 
			Cat_Secciones_Libros.nIdLibro, 
			Cat_Secciones_Libros.nIdSeccion,
			Cat_Fondo.cTitulo,
			Cat_Secciones.cNombre,
			Cat_Secciones.cCodigo,
			Cat_Secciones_Libros.nCF nStockFirmeReal, 
			Cat_Secciones_Libros.nCD nStockDepositoReal, 
			Cat_Secciones_Libros.nRF nStockFirme, 
			Cat_Secciones_Libros.nRD nStockDeposito,
			Cat_Secciones_Libros.nCF - Cat_Secciones_Libros.nRF df,
			Cat_Secciones_Libros.nCD - Cat_Secciones_Libros.nRD dd
		FROM Cat_Secciones_Libros
			INNER JOIN Cat_Fondo
				ON Cat_Fondo.nIdLibro = Cat_Secciones_Libros.nIdLibro
			INNER JOIN Cat_Secciones
				ON Cat_Secciones.nIdSeccion = Cat_Secciones_Libros.nIdSeccion
		WHERE (Cat_Secciones_Libros.nCF - Cat_Secciones_Libros.nRF) <> 0 OR 
			(Cat_Secciones_Libros.nCD - Cat_Secciones_Libros.nRD) <> 0 
		ORDER BY Cat_Secciones.cNombre, Cat_Fondo.cTitulo';
		$query = $this->db->query($sql);
		$data = $this->_get_results($query);

		return $data;		
	}
	
	/**
	 * Asigna el stock contado
	 * @param  int $idmas   Id de regulación de stock en el caso en que haya más stock
	 * @param  int $idmenos Id de regulación de stock en el caso en que haya menos stock
	 * @return string
	 */
	function asignar($idmas, $idmenos)
	{
		$this->db->trans_begin();

		# Añade las secciones nuevas
		$sql = "INSERT INTO Cat_Secciones_Libros(nIdSeccion, nIdLibro, nStockFirme, nStockDeposito)
			SELECT a.nIdSeccion, a.nIdLibro, 0, 0
			FROM Cat_RegulacionStock a 
				LEFT JOIN Cat_Secciones_Libros b
					ON a.nIdSeccion = b.nIdSeccion AND a.nIdLibro = b.nIdLibro
			WHERE b.nIdLibro IS NULL";

		if (!$this->db->query($sql))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->db->_error_message());
			return FALSE;
		}

		$sql  ="UPDATE Cat_Secciones_Libros SET nCF = 0, nCD=0, nRF = nStockFirme, nRD = nStockDeposito";
		if (!$this->db->query($sql))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->db->_error_message());
			return FALSE;
		}

		$sql = ($this->db->dbdriver == 'mssql')?"UPDATE Cat_Secciones_Libros
			SET nCF = nCantidad
			FROM Cat_Secciones_Libros sl
				INNER JOIN Cat_RegulacionStock s
					ON s.nIdSeccion = sl.nIdSeccion
						AND s.nIdLibro = sl.nIdLibro
			WHERE nIdTipoStock = " . $this->config->item('bp.contarstocks.firme'):
			"UPDATE Cat_Secciones_Libros sl
				INNER JOIN Cat_RegulacionStock s
					ON s.nIdSeccion = sl.nIdSeccion
						AND s.nIdLibro = sl.nIdLibro
			SET sl.nCF = s.nCantidad
			WHERE nIdTipoStock = " . $this->config->item('bp.contarstocks.firme');
		if (!$this->db->query($sql))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->db->_error_message());
			return FALSE;
		}
		 
		$sql = ($this->db->dbdriver == 'mssql')?"UPDATE Cat_Secciones_Libros
			SET nCD = nCantidad
			FROM Cat_Secciones_Libros sl
				INNER JOIN Cat_RegulacionStock s
					ON s.nIdSeccion = sl.nIdSeccion
						AND s.nIdLibro = sl.nIdLibro
			WHERE nIdTipoStock = " . $this->config->item('bp.contarstocks.deposito'):
			"UPDATE Cat_Secciones_Libros sl
				INNER JOIN Cat_RegulacionStock s
					ON s.nIdSeccion = sl.nIdSeccion
						AND s.nIdLibro = sl.nIdLibro
				SET sl.nCD = s.nCantidad
			WHERE nIdTipoStock = " . $this->config->item('bp.contarstocks.deposito');
		if (!$this->db->query($sql))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->db->_error_message());
			return FALSE;
		}
		
		$sql = "INSERT INTO Cat_Movimiento_Stock (nIdLibro, nIdSeccion, nIdMotivo, nCantidadFirme, dCreacion, cCUser, dAct, cAUser)
			SELECT nIdLibro, nIdSeccion, {$idmenos}, 
				nStockFirme - ISNULL(nCF, 0),
				{$date}, {$user}, {$date}, {$user}
			FROM Cat_Secciones_Libros
			WHERE (nStockFirme - ISNULL(nCF, 0)) > 0";
		if (!$this->db->query($sql))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->db->_error_message());
			return FALSE;
		}

		$date = format_mssql_date(time());
		$user = $this->db->escape($this->userauth->get_username());
		 
		$sql = "INSERT INTO Cat_Movimiento_Stock (nIdLibro, nIdSeccion, nIdMotivo, nCantidadFirme, dCreacion, cCUser, dAct, cAUser)
			SELECT nIdLibro, nIdSeccion, {$idmas}, 
				ISNULL(nCF, 0) - nStockFirme,
				{$date}, {$user}, {$date}, {$user}
			FROM Cat_Secciones_Libros
			WHERE (ISNULL(nCF, 0) - nStockFirme) > 0";
		if (!$this->db->query($sql))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->db->_error_message());
			return FALSE;
		}
		 
		$sql = "INSERT INTO Cat_Movimiento_Stock (nIdLibro, nIdSeccion, nIdMotivo, nCantidadDeposito, dCreacion, cCUser, dAct, cAUser)
			SELECT nIdLibro, nIdSeccion, {$idmenos}, 
				nStockDeposito - ISNULL(nCD, 0),
				{$date}, {$user}, {$date}, {$user}
			FROM Cat_Secciones_Libros
			WHERE (nStockDeposito - ISNULL(nCD, 0)) > 0";
		if (!$this->db->query($sql))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->db->_error_message());
			return FALSE;
		}
		 
		$sql = "INSERT INTO Cat_Movimiento_Stock (nIdLibro, nIdSeccion, nIdMotivo, nCantidadDeposito, dCreacion, cCUser, dAct, cAUser)
			SELECT nIdLibro, nIdSeccion, {$idmas}, 
				ISNULL(nCD, 0) - nStockDeposito,
				{$date}, {$user}, {$date}, {$user}
			FROM Cat_Secciones_Libros
			WHERE (ISNULL(nCD, 0) - nStockDeposito) > 0";
		if (!$this->db->query($sql))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->db->_error_message());
			return FALSE;
		}

		$sql = "UPDATE Cat_Secciones_Libros
			SET nStockDeposito = ISNULL(nCD, 0)
			WHERE (ISNULL(nCD, 0) <> nStockDeposito)";
		if (!$this->db->query($sql))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->db->_error_message());
			return FALSE;
		}
		 
		$sql = "UPDATE Cat_Secciones_Libros
			SET nStockFirme = ISNULL(nCF, 0)
			WHERE (ISNULL(nCF, 0) <> nStockFirme)";
		if (!$this->db->query($sql))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->db->_error_message());
			return FALSE;
		}

		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Secciones.cNombre cSeccion, Cat_Fondo.cTitulo, Cat_TiposStock.cDescripcion cTipoStock')
			->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = {$this->_tablename}.nIdSeccion")
			->join('Cat_Fondo', "Cat_Fondo.nIdLibro = {$this->_tablename}.nIdLibro")
			->join('Cat_TiposStock' , "Cat_TiposStock.nIdTipoStock = {$this->_tablename}.nIdTipoStock");
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Arregla los stocks contados de una sección madre y lo asigna a los hijos. Error creado por CECILIO al forzar.
	 * 
	 * @return null
	 */
	function cecilio()
	{
		$this->db->flush_cache();
		$this->db->select('nFirme1', 'nFirme1')
		->select_sum('nFirme2', 'nFirme2')
		->select_sum('nFirme3', 'nFirme3')
		->select_sum('nFirme4', 'nFirme4')
		->select_sum('nFirme1*fCoste', 'nCosteFirme1')
		->select_sum('nFirme2*fCoste', 'nCosteFirme2')
		->select_sum('nFirme3*fCoste', 'nCosteFirme3')
		->select_sum('nFirme4*fCoste', 'nCosteFirme4')
		->from($this->_tablename);

		$query = $this->db->get();
		$data = $this->_get_results($query);
	}
}

/* End of file M_stockcontado.php */
/* Location: ./system/application/models/stocks/M_stockcontado.php */