<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Kilos máximos para la carta certificada
 *
 */
define('BP_TARIFAS_ENVIO_MAX_CARTA', 20);

//@todo Pasar los SQL a delete, inserts, etc de la clase Database de CI

/**
 * Cálculo de tarifas de envío
 */
class M_TarifasEnvio extends MY_Model
{
	/**
	 * Constructor
	 * @return M_TarifasEnvio
	 */
	function __construct()
	{
		$data_model = array(
			'nIdTipo'			=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nIdGrupoRegion'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
		);
		for ($i = 1; $i <= 20; ++$i)
		{
			$data_model['fV' . $i] = array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT); 			
		}
		
		parent::__construct('Ext_TarifasEnvios', 'nIdTarifa', 'nIdTarifa', 'nIdTarifa', $data_model);
		$this->_cache = TRUE;
	}

	/**
	 * Muestra el listado completo de tarifas de un tipo
	 *
	 * @param int $tipo Tipo de envío
	 *
	 * @return string
	 */
	function get_tarifas($tipo)
	{
		$this->db->flush_cache();
		$sql = "INSERT INTO Ext_TarifasEnvios(nIdTipo, nIdGrupoRegion) SELECT {$tipo}, nIdZona
				from Web_Zonas z
				where nIdZona NOT IN (
					SELECT nIdGrupoRegion FROM Ext_TarifasEnvios where nIdTipo = {$tipo}
				)";
		$query = $this->db->query($sql);
		$this->db->flush_cache();
		$this->db->select('t.nIdTarifa id, z.cNombre, z.cDescripcion,
		fV1, fV2, fV3, fV4, fV5, fV6, fV7, fV8,
		fV9, fV10, fV11, fV12, fV13, fV14, fV15, fV16,
		fV17, fV18, fV19, fV20')
		->from('Web_Zonas z')
		->join('Ext_TarifasEnvios t', 't.nIdGrupoRegion = z.nIdZona', 'left')
		->where("nIdTipo = {$tipo}")
		->order_by('z.nIdZona');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		foreach ($data as $k => $v)
		{
			$data[$k]['text'] = $v['cNombre'] . ': ' . $v['cDescripcion'];
		}
		
		#var_dump($data);
		
		return $data;		
	}

	/**
	 * Calcula el precio de un envío según el peso
	 *
	 * @param int $country_id Id del país
	 * @param int $zone_id Id de la región
	 * @param int $peso Peso en gramos
	 * @param int $web Solo tarifas Web
	 * @return string
	 */
	function get_tarifas_peso($country_id, $zone_id, $peso, $web)
	{
		$web = $web?' AND bWeb=1':'';
		
		$pesokg = ceil($peso / 1000);

		$data = array();
		$idzona = null;
		$this->db->flush_cache();
		$this->db->select('nIdZona')
		->from('Gen_Regiones')
		->where("nIdRegion = {$zone_id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		if (count($data) > 0) $idzona = $data[0]['nIdZona'];		

		if (!isset($idzona))
		{
			$this->db->flush_cache();
			$this->db->select('nIdZona')
			->from('Gen_Paises')
			->where("nIdPais = {$country_id}");
			$query = $this->db->get();
			$data = $this->_get_results($query);
			if (count($data) > 0) $idzona = $data[0]['nIdZona'];		
		}
		
		if (isset($idzona))
		{
			if ($pesokg > 0 && $pesokg <= 20)
			{
				$this->db->flush_cache();
				$this->db->select('t.nIdTipo id, t.cNombre text, t.fMinimo min, t.nOrden orden')
				->select("te.fV{$pesokg} coste")
				->select('d.cDescripcion descripcion, t.cModosPago modos')
				->from('Ext_TarifasEnvios te')
				->join('Web_TiposEnvio t', "te.nIdTipo = t.nIdTipo {$web}")
				->join('Web_TiposEnvioDescripcion d', 'd.nIdTipo = t.nIdTipo AND d.nIdIdioma = 3', 'left')
				->where("te.nIdGrupoRegion = {$idzona}")
				->where("te.fV{$pesokg} > 0")
				->order_by('t.cNombre');
			}
			else
			{				
				$this->db->flush_cache();
				$this->db->select('t.nIdTipo id, t.cNombre text')
				->select("0 coste")
				->select('d.cDescripcion descripcion, t.cModosPago modos')
				->from('Ext_TarifasEnvios te')
				->join('Web_TiposEnvio t', "te.nIdTipo = t.nIdTipo {$web}")
				->join('Web_TiposEnvioDescripcion d', 'd.nIdTipo = t.nIdTipo AND d.nIdIdioma = 3')
				->where("te.nIdGrupoRegion = {$idzona}")
				->where("te.fV1 > 0")
				->order_by('t.cNombre');
			}				
		}
		else
		{
			$this->db->flush_cache();
			$this->db->select('t.nIdTipo id, t.cNombre text')
			->select("0 coste")
			->select('d.cDescripcion descripcion, t.cModosPago modos')
			->from('Ext_TarifasEnvios te')
			->join('Web_TiposEnvio t', "te.nIdTipo = t.nIdTipo {$web}")
			->join('Web_TiposEnvioDescripcion d', 'd.nIdTipo = t.nIdTipo AND d.nIdIdioma = 3')
			->where("te.nIdGrupoRegion = 1")
			->where("te.fV1 > 0")
			->order_by('t.cNombre');
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		
		
		#$sql = 'EXEC spWebCalcularCosteEnvio @IdPais = ?, @IdRegion	= ?, @Peso = ?, @IdIdioma = 3';
		#$query = $this->db->query( $sql, array( (int)$country_id, (int)$zone_id , (int)$pesokg));
		#if ($query !== FALSE) $data = $query->result_array();

		if (isset($idzona))
		{			
			$sql = 'SELECT MIN(nIdPrecio) id FROM Ext_TarifasEnvioGramos (NOLOCK)
				WHERE nIdZona = ? AND ? < nPeso'; 
			$query = $this->db->query( $sql, array((int) $idzona, (int) $peso ));
			if ($query->num_rows() > 0)
			{
				$trama = $query->row_array();
				$trama = $trama['id'];
			}
			$sql = "SELECT nPeso, fPrecio 
				FROM Ext_TarifasEnvioGramos (NOLOCK)
				WHERE nIdPrecio = ?";
			$query = $this->db->query( $sql, array( (int)$trama ) );
			if ($query->num_rows() > 0)
			{
				$data2 = $query->result_array();
				foreach ($data2 as $key => $value) 
				{
					$data2[$key]['text'] = $this->lang->line('Carta Certificada');
					$data2[$key]['id'] = 99;
					$data2[$key]['descripcion'] = $this->lang->line('Trama') . ' ' . $value['nPeso'];
					$data2[$key]['coste'] = $value['fPrecio'];
				}
				$data = array_merge($data, $data2);
			}
		}
		foreach($data as $k => $v)
		{
			$data[$k]['coste'] = (float) $data[$k]['coste'];
		}
		sksort($data, 'coste', FALSE);

		return string_encode($data);//, 'gramos' => $data2);
	}

	/**
	 * Calcula el precio de un envío según el pedido
	 *
	 * @param int $id Id del pedido
	 * @param int $grlibro Peso por defecto de los títulos 
	 * @param int $web Solo tarifas Web
	 * @return string
	 */
	function get_tarifas_pedido($id, $grlibro, $web)
	{		
		$this->db->select('r.nIdPais idp, r.nIdRegion idr')
		->from('Doc_PedidosCliente p')
		->join('Cli_Direcciones d', 'p.nIdDirEnv = d.nIdDireccion')
		->join('Gen_Regiones r', 'r.nIdRegion = d.nIdRegion')
		->where('p.nIdPedido', (int)$id);
		$query = $this->db->get();
		if ($query->num_rows() > 0)
		{
			$idpais = $query->row_array();
			$idpais = $idpais['idp'];
			$idregion = $idpais['idr'];
		}
		if (! isset($idpais))
		{
			$sql = 'SELECT r.nIdPais idp, r.nIdRegion idr
				FROM Doc_PedidosCliente p (NOLOCK)
					INNER JOIN Cli_Direcciones d (NOLOCK)
						ON p.nIdCliente = d.nIdCliente
					INNER JOIN Gen_Regiones r (NOLOCK)
						ON r.nIdRegion = d.nIdRegion
				WHERE p.nIdPedido = ? AND d.nIdTipo = 2';
			$query = $this->db->query( $sql, array( (int)$id ));
			if ($query->num_rows() > 0)
			{
				$idpais = $query->row_array();
				$idpais = $idpais['idp'];
				$idregion = $idpais['idr'];
			}
			if (! isset($idpais))
			{
					
				$sql = 'SELECT r.nIdPais idp, r.nIdRegion idr
					FROM Doc_PedidosCliente p (NOLOCK)
						INNER JOIN Cli_Direcciones d (NOLOCK)
							ON p.nIdCliente = d.nIdCliente
						INNER JOIN Gen_Regiones r (NOLOCK)
							ON r.nIdRegion = d.nIdRegion
					WHERE p.nIdPedido = ? AND d.nIdTipo = 1';
				$query = $this->db->query( $sql, array( (int)$id ));
				if ($query->num_rows() > 0)
				{
					$idpais = $query->row_array();
					$idpais = $idpais['idp'];
					$idregion = $idpais['idr'];
				}
				if (! isset($idpais))
				{

					$sql = 'SELECT r.nIdPais idp, r.nIdRegion idr
						FROM Doc_PedidosCliente p (NOLOCK)
							INNER JOIN Cli_Direcciones d (NOLOCK)
								ON p.nIdCliente = d.nIdCliente
							INNER JOIN Gen_Regiones r (NOLOCK)
								ON r.nIdRegion = d.nIdRegion
						WHERE p.nIdPedido = ?';
					$query = $this->db->query( $sql, array( (int)$id ));
					if ($query->num_rows() > 0)
					{
						$idpais = $query->row_array();
						$idpais = $idpais['idp'];
						$idregion = $idpais['idr'];
					}
				}
			}
		}
		if (isset($idpais))
		{
			$sql = 'SELECT (? * SUM(nCantidad)) / 1000 peso
					FROM Doc_LineasPedidoCliente (NOLOCK)
					WHERE nIdPedido = ?';
			$query = $this->db->query( $sql, array( (int) $grlibro, (int)$id ));
			if ($query->num_rows() > 0)
			{
				$peso = $query->row_array();
				$peso = $peso['peso'];
				return $this->get_tarifas_peso($idpais, $idregion, $peso, $web);
			}
		}
		return array();
	}

	/**
	 * Actualiza los precios de envío a partir de un fichero EXCEL
	 *
	 * @param int $tipo Id del tipo de envío
	 * @param string $filename Fichero a procesar
	 * @return string
	 */
	function set_tarifas($tipo, $filename)
	{
		//@todo comprobar que el excel es correcto y tiene todos los campos
		// sino dar error
		$this->load->helper('excelReader');
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('CP1251');
		$data->read($filename);
		$pesos = array();
		//$r = '';
		//Columna A2:A21: los Pesos
		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++)
		{
			$pesos[$data->sheets[0]['cells'][$i][1]] = 0;
		}
		echo '<pre>';
		for ($i = 2; $i <= $data->sheets[0]['numCols']; $i++)
		{
			//Carga valores
			for ($j = 2; $j <= $data->sheets[0]['numRows']; $j++)
			{
				$pesos[$data->sheets[0]['cells'][$j][1]] = $data->sheets[0]['cells'][$j][$i];
			}
			//Borra anteriores
			$sql = 'DELETE FROM Ext_TarifasEnvios WHERE nIdTipo =  ' . $tipo . ' AND nIdGrupoRegion = ' . ($i - 1);
			$this->db->simple_query($sql);
			$campos = 'nIdTipo, nIdGrupoRegion';
			$valores = $tipo . ', ' . (string)($i-1);
			foreach($pesos as $key => $value)
			{
				$campos .= ', fV' . $key;
				echo $value;
				$valores .= ', ' . str_replace(',', '.', (string)$value);
			}
			$sql = "INSERT INTO Ext_TarifasEnvios($campos) VALUES ($valores)";
			echo "{$sql}\n";
			//$r .= $sql . '<br/>';
			$this->db->simple_query($sql);
		}
		echo '</pre>';

		//return $r;
		return TRUE;
	}

	/**
	 * Actualiza los precios de envío en Gramos a partir de un fichero EXCEL
	 *
	 * @param string $filename Fichero a procesar
	 * @return string
	 */
	function set_tarifas_gramos($filename)
	{
		//@todo comprobar que el excel es correcto y tiene todos los campos
		// sino dar error
		$this->load->helper('excelReader');
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('CP1251');
		$data->read($filename);
		$pesos = array();

		#var_dump($filename); die();

		$this->db->where('1=1')->delete('Ext_TarifasEnvioGramos');
		//Columna A2:A21: los Pesos
		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++)
		{
			$pesos[$data->sheets[0]['cells'][$i][1]] = 0;
		}

		for ($i = 2; $i <= $data->sheets[0]['numCols']; $i++)
		{
			//Carga valores
			for ($j = 2; $j <= $data->sheets[0]['numRows']; $j++)
			{
				//$pesos[$data->sheets[0]['cells'][$j][1]] = $data->sheets[0]['cells'][$j][$i];
				$campos = 'nIdZona, nPeso, fPrecio';
				$valores = (string)($i-1). ', ' . $data->sheets[0]['cells'][$j][1] .', ' . (string)$this->_tofloat($data->sheets[0]['cells'][$j][$i]);
				$sql = "INSERT INTO Ext_TarifasEnvioGramos($campos) VALUES ($valores)";
				$this->db->query($sql);
			}
		}

		return TRUE;
	}

	/**
	 * Clona un modo de envío en un nuevo modo de envío
	 *
	 * @param int $id Id del modo de envío clonar
	 * @param int $id_n Id del modo de envío nuevo 
	 * @return bool
	 */
	function clonar($id, $id_n)
	{
		$sql = "INSERT INTO Ext_TarifasEnvios(nIdTipo, nIdGrupoRegion, 
			fV1, fV2, fV3, fV4, fV5, fV6, fV7, fV8,
			fV9, fV10, fV11, fV12, fV13, fV14, fV15, fV16,
			fV17, fV18, fV19, fV20) SELECT {$id_n}, nIdGrupoRegion, 
			fV1, fV2, fV3, fV4, fV5, fV6, fV7, fV8,
			fV9, fV10, fV11, fV12, fV13, fV14, fV15, fV16,
			fV17, fV18, fV19, fV20
				from Ext_TarifasEnvios 
				where nIdTipo = {$id}";
		$query = $this->db->query($sql);
		return $id_n;
	}
}

/* End of file M_tarifasenvio.php */
/* Location: ./system/application/models/M_tarifasenvio.php */