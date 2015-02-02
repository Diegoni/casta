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
 * Antigüedad del stock
 *
 */
class M_antiguedadstock extends MY_Model
{
	/**
	 * Base de datos OLTP
	 * @var string
	 */
	private $_prefix = '';

	/**
	 * Costructor
	 * @return M_antiguedadstock
	 */
	function __construct()
	{
		$data_model = array(
			'cTitulo' 		=> array(),
			'cISBN' 		=> array(),
			'nDeposito'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nFirme1'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nFirme2'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nFirme3'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nFirme4'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nTotalFirme'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nEntradas1'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nEntradas2'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nEntradas3'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nEntradas4'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'fCoste' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
		);

		parent::__construct('Ext_AntiguedadStock', 'nIdLibro', 'nIdLibro', 'nIdLibro', $data_model);
		
		$this->_prefix = $this->config->item('bp.stocks.database');
	}

	/**
	 * Análisis de la antigüedad de stock
	 * @return array
	 */
	function analisis()
	{
		$this->db->flush_cache();
		$this->db->select_sum('nFirme1', 'nFirme1')
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

		return $data[0];
	}

	/**
	 * Retrocede el stock
	 * @return bool
	 */
	function retroceder2()
	{
		set_time_limit(0);
		$idajustemas = $this->config->item('bp.stocks.idajustemas');
		$idajustemenos = $this->config->item('bp.stocks.idajustemenos');
		$fechainventario = $this->config->item('bp.stocks.fechainventario');
		$fechainventario = format_mssql_date(to_date(($fechainventario)));
		$fecharetroceso = $this->config->item('bp.oltp.fechadpr');
		$fecharetroceso = format_mssql_date(to_date(($fecharetroceso)));
		$margen = (string) str_replace(',', '.', $this->config->item('bp.oltp.margen'));
		#echo $margen; die;
			
		// Borra el anterior
		$this->db->flush_cache();
		#$this->db->where('1=1')->delete($this->_tablename);

		$this->db->flush_cache();
		//AÑADE LOS NUEVOS
		//Ignora genéricos
		$sql = "INSERT INTO Ext_AntiguedadStock(
				nIdLibro, 
				cTitulo, 
				cISBN, 
				nDeposito, 
				nFirme1, 
				nFirme2, 
				nFirme3, 
				nFirme4, 
				nTotalFirme,
				nEntradas1,
				nEntradas2,
				nEntradas3,
				nEntradas4,
				fCoste)
				SELECT f.nIdLibro,
					f.cTitulo,
					f.cISBN,
					0, 
					0,
					0,
					0,
					0,
					0, 
					0, 
					0, 
					0, 
					0, 
					isnull(f.fPrecioCompra, 0) fCoste
				
				FROM Cat_Fondo f (NOLOCK)
				WHERE f.nIdEstado <> 16";
		#$this->db->query($sql);
		$sql = "UPDATE Ext_AntiguedadStock a 
			INNER JOIN (
						SELECT nIdLibro,
							SUM(ISNULL(nCantidad, 0)) nTotalFirme
						FROM Cat_RegulacionStock (NOLOCK)
						WHERE 	nIdTipoStock = 9000000
						GROUP BY nIdLibro
					) firme 
				ON a.nIdLibro = firme.nIdLibro 
			SET a.nTotalFirme = firme.nTotalFirme";
		#$this->db->query($sql);
		$sql = "UPDATE Ext_AntiguedadStock a 
			INNER JOIN (
						SELECT nIdLibro,
							SUM(ISNULL(nCantidad, 0)) nDeposito
						FROM Cat_RegulacionStock (NOLOCK)
						WHERE 	nIdTipoStock = 9000001
						GROUP BY nIdLibro
					) deposito
						ON deposito.nIdLibro = a.nIdLibro
			SET a.nDeposito = deposito.nDeposito";
		#$this->db->query($sql);
		$sql = "UPDATE Ext_AntiguedadStock a 
			INNER JOIN (
						SELECT lae.nIdlibro,
					    	SUM(lae.nCantidad) 	Cantidad
					    FROM  Doc_LineasAlbaranesEntrada lae (NOLOCK)
					        INNER JOIN Doc_AlbaranesEntrada ae (NOLOCK)
					            ON lae.nIdAlbaran = ae.nIdAlbaran
					    WHERE ae.nIdEstado <> 1					    	
							AND ae.dCierre >= " . $this->db->dateadd('yy', -1, $fecharetroceso) . "			
						GROUP BY lae.nIdlibro
					) ENT
						ON ENT.nIdLibro = a.nIdLibro
				SET a.nEntradas1=ENT.Cantidad";
		$this->db->query($sql);

		$sql = "UPDATE Ext_AntiguedadStock a 
			INNER JOIN (
						SELECT 	lae.nIdlibro,
					    	SUM(lae.nCantidad) 	Cantidad
					    FROM Doc_LineasAlbaranesEntrada lae (NOLOCK)
					        INNER JOIN Doc_AlbaranesEntrada ae (NOLOCK)
					            ON lae.nIdAlbaran = ae.nIdAlbaran
					    WHERE ae.nIdEstado <> 1  
							AND ae.dCierre >= " . $this->db->dateadd('yy', -2, $fecharetroceso) . "	
							AND ae.dCierre < " . $this->db->dateadd('yy', -1, $fecharetroceso) . "
						GROUP BY lae.nIdlibro
					) ENT  ON ENT.nIdLibro = a.nIdLibro
				SET a.nEntradas2=ENT.Cantidad";

		$this->db->query($sql);

		$sql = "UPDATE Ext_AntiguedadStock a 
			INNER JOIN (
						SELECT 	lae.nIdlibro,
					    	SUM(lae.nCantidad) Cantidad
					    FROM Doc_LineasAlbaranesEntrada lae (NOLOCK)
					        INNER JOIN Doc_AlbaranesEntrada ae (NOLOCK)
					            ON lae.nIdAlbaran = ae.nIdAlbaran
					    WHERE ae.nIdEstado <> 1  
							AND ae.dCierre >= " . $this->db->dateadd('yy', -3, $fecharetroceso) . "	
							AND ae.dCierre < " . $this->db->dateadd('yy', -2, $fecharetroceso) . "
						GROUP BY lae.nIdlibro
					) ENT  ON ENT.nIdLibro = a.nIdLibro
				SET a.nEntradas3=ENT.Cantidad";

		$this->db->query($sql);

		$sql = "UPDATE Ext_AntiguedadStock a 
			INNER JOIN (
						SELECT 	lae.nIdlibro,
					    	SUM(lae.nCantidad) Cantidad
					    FROM Doc_LineasAlbaranesEntrada lae (NOLOCK)
					        INNER JOIN Doc_AlbaranesEntrada ae (NOLOCK)
					            ON lae.nIdAlbaran = ae.nIdAlbaran
					    WHERE ae.nIdEstado <> 1  
							AND ae.dCierre < " . $this->db->dateadd('yy', -3, $fecharetroceso) . "
						GROUP BY lae.nIdlibro 
					) ENT ON ENT.nIdLibro = a.nIdLibro
				SET a.nEntradas4=ENT.Cantidad";

		$this->db->query($sql);

		/*
		 2. Resta las entradas del 1 al día del inventario y suma las salidas
		 y las devoluciones
		 Suma los ajustes de stock.
		 */
		echo 'OK';die();

		##ALBARANES DE ENTRADA
		$sql = "UPDATE Ext_AntiguedadStock
		SET nTotalFirme = nTotalFirme - Cantidad
		FROM Ext_AntiguedadStock s1
			INNER JOIN
			(
					SELECT 	lae.nidlibro,
					    	SUM(lae.nCantidad) 	Cantidad
					    FROM Doc_LineasAlbaranesEntrada lae (NOLOCK)
					        INNER JOIN Doc_AlbaranesEntrada ae (NOLOCK)
					            ON lae.nIdAlbaran = ae.nIdAlbaran
					    WHERE ae.nIdEstado = 4  AND bDeposito = 0
						AND ae.dCierre >= " . $this->db->dateadd('d', 1, $fecharetroceso) . "
						AND ae.dCierre < {$fechainventario}
					GROUP BY lae.nidlibro 
			) s2
				ON s1.nIdLibro = s2.nIdLibro";
		$this->db->query($sql);

		//VENTAS
		//Ignora genéricos
		$sql = "UPDATE Ext_AntiguedadStock
			SET nTotalFirme = nTotalFirme + Cantidad
			FROM Ext_AntiguedadStock s1
				INNER JOIN
				(
					SELECT lal.nIdLibro,
						SUM(lal.nCantidad) Cantidad
					FROM Doc_LineasAlbaranesSalida lal 
						INNER JOIN Doc_AlbaranesSalida al
							ON lal.nIdAlbaran = al.nIdAlbaran
						INNER JOIN Cat_Fondo f
							ON lal.nIdLibro = f.nIdLibro AND f.nIdEstado <> 16
					WHERE al.nIdEstado IN (2, 3)
						AND al.dCreacion >= " . $this->db->dateadd('d', 1, $fecharetroceso) . "
						AND al.dCreacion < {$fechainventario}
					GROUP BY lal.nIdLibro
				) s2
					ON s1.nIdLibro = s2.nIdLibro";
		$this->db->query($sql);

		//DEVOLUCIONES
		$sql = "UPDATE Ext_AntiguedadStock
			SET nTotalFirme = nTotalFirme + Cantidad
			FROM Ext_AntiguedadStock s1
				INNER JOIN
				(
					SELECT ld.nIdLibro,
						SUM(ld.nCantidad) Cantidad
					FROM Doc_LineasDevolucion ld
						INNER JOIN Doc_Devoluciones d 
							ON ld.nIdDevolucion = d.nIdDevolucion
					WHERE d.dEntrega >= " . $this->db->dateadd('d', 1, $fecharetroceso) . "
						AND d.dEntrega < {$fechainventario}
						AND d.nIdEstado = 3
						 AND bDeposito = 0
					GROUP BY ld.nIdLibro
				) s2
					ON s1.nIdLibro = s2.nIdLibro";
		$this->db->query($sql);

		//AJUSTES DE STOCK CONTADO
		$sql = "UPDATE Ext_AntiguedadStock
		SET nTotalFirme = nTotalFirme - Cantidad
		FROM Ext_AntiguedadStock s1
			INNER JOIN
			(
				SELECT mv.nIdLibro,
				SUM(nCantidadFirme) Cantidad
				FROM Cat_Movimiento_Stock mv (NOLOCK)
					INNER JOIN Gen_Movimiento_Stock t (NOLOCK)
						ON mv.nIdMotivo = t.nId
				WHERE dCreacion > {$fechainventario}
					AND nIdMotivo IN ($idajustemenos)
				GROUP BY mv.nidlibro 
			) s2
				ON s1.nIdLibro = s2.nIdLibro";
		$this->db->query($sql);

		$sql = "UPDATE Ext_AntiguedadStock
		SET nTotalFirme = nTotalFirme + Cantidad
		FROM Ext_AntiguedadStock s1
			INNER JOIN
			(
				SELECT mv.nIdLibro,
				SUM(nCantidadFirme) Cantidad
				FROM Cat_Movimiento_Stock mv (NOLOCK)
					INNER JOIN Gen_Movimiento_Stock t (NOLOCK)
						ON mv.nIdMotivo = t.nId
				WHERE dCreacion > {$fechainventario}
					AND nIdMotivo IN ($idajustemas)
				GROUP BY mv.nidlibro 
			) s2
				ON s1.nIdLibro = s2.nIdLibro";
		$this->db->query($sql);

		//BORRA LOS STOCKS 0
		$sql = "DELETE FROM Ext_AntiguedadStock
			WHERE ISNULL(nTotalFirme, 0) = 0 AND ISNULL(nDeposito,0) = 0";
		$this->db->query($sql);

		//BORRA LAS OFERTAS Y GENERICOS
		/*$sql = "DELETE FROM Ext_AntiguedadStock
			WHERE cTitulo like '%oferta%' 
				OR cTitulo like '%€%'";
		$this->db->query($sql);*/

		$sql = "DELETE Ext_AntiguedadStock
			FROM Ext_AntiguedadStock s (NOLOCK)
				INNER JOIN Cat_Fondo f (NOLOCK)
					ON s.nIdLibro = f.nIdLibro
			WHERE f.nIdEstado = 16";
		$this->db->query($sql);

		/*
		 3. Calculamos la antigüedad
		 */
		//APLICAMOS LA ANTIGUEDAD POR ENTRADAS
		//año 1
		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nFirme1 = nTotalFirme,
				nTotalFirme = 0			
			WHERE nEntradas1 >= nTotalFirme";
		$this->db->query($sql);

		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nFirme1 = nEntradas1,
					nTotalFirme = nTotalFirme - nEntradas1
			WHERE nEntradas1 < nTotalFirme";
		$this->db->query($sql);

		#año 2
		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nFirme2 = nTotalFirme,
					nTotalFirme = 0
			WHERE nEntradas2 >= nTotalFirme";
		$this->db->query($sql);

		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nFirme2 = nEntradas2,
					nTotalFirme = nTotalFirme - nEntradas2
			WHERE nEntradas2 < nTotalFirme";
		$this->db->query($sql);

		//año 3
		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nFirme3 = nTotalFirme,
					nTotalFirme = 0
			WHERE nEntradas3 >= nTotalFirme";
		$this->db->query($sql);

		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nFirme3 = nEntradas3,
					nTotalFirme = nTotalFirme - nEntradas3
			WHERE nEntradas3 < nTotalFirme";
		$this->db->query($sql);

		// año 4
		$sql = "UPDATE 	Ext_AntiguedadStock
		SET nFirme4 = nTotalFirme,
				nTotalFirme = 0
		WHERE nTotalFirme > 0";
		$this->db->query($sql);

		//RECUPERA LOS VALORES
		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nTotalFirme = nFirme1 + nFirme2 + nFirme3 + nFirme4";
		$this->db->query($sql);

		//ELIMINA LAS COSAS RARAS
		$sql = "DELETE FROM Ext_AntiguedadStock
			WHERE nTotalFirme <= 0";
		$this->db->query($sql);

		//LOS PRECIOS A 0 LOS PONE AL PRECIO DE VENTA - MARGEN MEDIO
		$sql = "UPDATE Ext_AntiguedadStock
			SET fCoste = fPrecio - fPrecio * {$margen}
			FROM Ext_AntiguedadStock s
				INNER JOIN Cat_Fondo f 
					ON f.nIdLibro = s.nIdLibro
			WHERE fCoste = 0 OR fCoste IS NULL";
		//$this->db->query($sql);

		//Elimina los que tienen precio 0
		$sql = "DELETE FROM Ext_AntiguedadStock
			WHERE fCoste = 0";
		$this->db->query($sql);

		$this->clear_cache();

		return TRUE;
	}

	/**
	 * Retrocede el stock
	 * @return bool
	 */
	function retroceder()
	{
		set_time_limit(0);
		$idajustemas = $this->config->item('bp.stocks.idajustemas');
		$idajustemenos = $this->config->item('bp.stocks.idajustemenos');
		$fechainventario = $this->config->item('bp.stocks.fechainventario');
		$fechainventario = format_mssql_date(to_date(($fechainventario)));
		$fecharetroceso = $this->config->item('bp.oltp.fechadpr');
		$fecharetroceso = format_mssql_date(to_date(($fecharetroceso)));
		$margen = (string) str_replace(',', '.', $this->config->item('bp.oltp.margen'));
		#echo $margen; die;
			
		// Borra el anterior
		$this->db->flush_cache();
		$this->db->where('1=1')->delete($this->_tablename);

		$this->db->flush_cache();
		//AÑADE LOS NUEVOS
		//Ignora genéricos
		$sql = "INSERT INTO Ext_AntiguedadStock(
				nIdLibro, 
				cTitulo, 
				cISBN, 
				nDeposito, 
				nFirme1, 
				nFirme2, 
				nFirme3, 
				nFirme4, 
				nTotalFirme,
				nEntradas1,
				nEntradas2,
				nEntradas3,
				nEntradas4,
				fCoste)
				SELECT f.nIdLibro,
					f.cTitulo,
					f.cISBN,
					isnull(deposito.nDeposito, 0),
					0,
					0,
					0,
					0,
					isnull(firme.nTotalFirme, 0),
					isnull(ENT.Cantidad, 0) Cantidad, 
					isnull(ENT2.Cantidad, 0) Cantidad, 
					isnull(ENT3.Cantidad, 0) Cantidad, 
					isnull(ENT4.Cantidad, 0) Cantidad, 
					isnull(f.fPrecioCompra, 0) fCoste
				
				FROM Cat_Fondo f (NOLOCK)
					LEFT JOIN
					(
						SELECT nIdLibro,
							SUM(ISNULL(nCantidad, 0)) nTotalFirme
						FROM Cat_RegulacionStock (NOLOCK)
						WHERE 	nIdTipoStock = 9000000
						GROUP BY nIdLibro
					) firme 
						ON firme.nIdLibro = f.nIdLibro AND f.nIdEstado <> 16
					LEFT JOIN
					(
						SELECT nIdLibro,
							SUM(ISNULL(nCantidad, 0)) nDeposito
						FROM Cat_RegulacionStock (NOLOCK)
						WHERE 	nIdTipoStock = 9000001
						GROUP BY nIdLibro
					) deposito
						ON deposito.nIdLibro = f.nIdLibro
				LEFT JOIN
					(
						SELECT lae.nIdlibro,
					    	SUM(lae.nCantidad) 	Cantidad
					    FROM  Doc_LineasAlbaranesEntrada lae (NOLOCK)
					        INNER JOIN Doc_AlbaranesEntrada ae (NOLOCK)
					            ON lae.nIdAlbaran = ae.nIdAlbaran
					    WHERE ae.nIdEstado <> 1					    	
							AND ae.dCierre >= " . $this->db->dateadd('yy', -1, $fecharetroceso) . "			
						GROUP BY lae.nIdlibro
					) ENT
						ON ENT.nIdLibro = f.nIdLibro 
		
				LEFT JOIN
					(
						SELECT 	lae.nIdlibro,
					    	SUM(lae.nCantidad) 	Cantidad
					    FROM Doc_LineasAlbaranesEntrada lae (NOLOCK)
					        INNER JOIN Doc_AlbaranesEntrada ae (NOLOCK)
					            ON lae.nIdAlbaran = ae.nIdAlbaran
					    WHERE ae.nIdEstado <> 1  
							AND ae.dCierre >= " . $this->db->dateadd('yy', -2, $fecharetroceso) . "	
							AND ae.dCierre < " . $this->db->dateadd('yy', -1, $fecharetroceso) . "
						GROUP BY lae.nIdlibro
					) ENT2
						ON ENT2.nIdLibro = f.nIdLibro 
			
				LEFT JOIN
					(
						SELECT 	lae.nIdlibro,
					    	SUM(lae.nCantidad) Cantidad
					    FROM Doc_LineasAlbaranesEntrada lae (NOLOCK)
					        INNER JOIN Doc_AlbaranesEntrada ae (NOLOCK)
					            ON lae.nIdAlbaran = ae.nIdAlbaran
					    WHERE ae.nIdEstado <> 1  
							AND ae.dCierre >= " . $this->db->dateadd('yy', -3, $fecharetroceso) . "	
							AND ae.dCierre < " . $this->db->dateadd('yy', -2, $fecharetroceso) . "
						GROUP BY lae.nIdlibro
					) ENT3
						ON ENT3.nIdLibro = f.nIdLibro 
				LEFT JOIN
					(
						SELECT 	lae.nIdlibro,
					    	SUM(lae.nCantidad) Cantidad
					    FROM Doc_LineasAlbaranesEntrada lae (NOLOCK)
					        INNER JOIN Doc_AlbaranesEntrada ae (NOLOCK)
					            ON lae.nIdAlbaran = ae.nIdAlbaran
					    WHERE ae.nIdEstado <> 1  
							AND ae.dCierre < " . $this->db->dateadd('yy', -3, $fecharetroceso) . "
						GROUP BY lae.nIdlibro 
					) ENT4
						ON ENT4.nIdLibro = f.nIdLibro"; 
		$this->db->query($sql);

		/*
		 2. Resta las entradas del 1 al día del inventario y suma las salidas
		 y las devoluciones
		 Suma los ajustes de stock.
		 */

		##ALBARANES DE ENTRADA
		$sql = "UPDATE Ext_AntiguedadStock
		SET nTotalFirme = nTotalFirme - Cantidad
		FROM Ext_AntiguedadStock s1
			INNER JOIN
			(
					SELECT 	lae.nidlibro,
					    	SUM(lae.nCantidad) 	Cantidad
					    FROM Doc_LineasAlbaranesEntrada lae (NOLOCK)
					        INNER JOIN Doc_AlbaranesEntrada ae (NOLOCK)
					            ON lae.nIdAlbaran = ae.nIdAlbaran
					    WHERE ae.nIdEstado = 4  AND bDeposito = 0
						AND ae.dCierre >= " . $this->db->dateadd('d', 1, $fecharetroceso) . "
						AND ae.dCierre < {$fechainventario}
					GROUP BY lae.nidlibro 
			) s2
				ON s1.nIdLibro = s2.nIdLibro";
		$this->db->query($sql);

		//VENTAS
		//Ignora genéricos
		$sql = "UPDATE Ext_AntiguedadStock
			SET nTotalFirme = nTotalFirme + Cantidad
			FROM Ext_AntiguedadStock s1
				INNER JOIN
				(
					SELECT lal.nIdLibro,
						SUM(lal.nCantidad) Cantidad
					FROM Doc_LineasAlbaranesSalida lal 
						INNER JOIN Doc_AlbaranesSalida al
							ON lal.nIdAlbaran = al.nIdAlbaran
						INNER JOIN Cat_Fondo f
							ON lal.nIdLibro = f.nIdLibro AND f.nIdEstado <> 16
					WHERE al.nIdEstado IN (2, 3)
						AND al.dCreacion >= " . $this->db->dateadd('d', 1, $fecharetroceso) . "
						AND al.dCreacion < {$fechainventario}
					GROUP BY lal.nIdLibro
				) s2
					ON s1.nIdLibro = s2.nIdLibro";
		$this->db->query($sql);

		//DEVOLUCIONES
		$sql = "UPDATE Ext_AntiguedadStock
			SET nTotalFirme = nTotalFirme + Cantidad
			FROM Ext_AntiguedadStock s1
				INNER JOIN
				(
					SELECT ld.nIdLibro,
						SUM(ld.nCantidad) Cantidad
					FROM Doc_LineasDevolucion ld
						INNER JOIN Doc_Devoluciones d 
							ON ld.nIdDevolucion = d.nIdDevolucion
					WHERE d.dEntrega >= " . $this->db->dateadd('d', 1, $fecharetroceso) . "
						AND d.dEntrega < {$fechainventario}
						AND d.nIdEstado = 3
						 AND bDeposito = 0
					GROUP BY ld.nIdLibro
				) s2
					ON s1.nIdLibro = s2.nIdLibro";
		$this->db->query($sql);

		//AJUSTES DE STOCK CONTADO
		$sql = "UPDATE Ext_AntiguedadStock
		SET nTotalFirme = nTotalFirme - Cantidad
		FROM Ext_AntiguedadStock s1
			INNER JOIN
			(
				SELECT mv.nIdLibro,
				SUM(nCantidadFirme) Cantidad
				FROM Cat_Movimiento_Stock mv (NOLOCK)
					INNER JOIN Gen_Movimiento_Stock t (NOLOCK)
						ON mv.nIdMotivo = t.nId
				WHERE dCreacion > {$fechainventario}
					AND nIdMotivo IN ($idajustemenos)
				GROUP BY mv.nidlibro 
			) s2
				ON s1.nIdLibro = s2.nIdLibro";
		$this->db->query($sql);

		$sql = "UPDATE Ext_AntiguedadStock
		SET nTotalFirme = nTotalFirme + Cantidad
		FROM Ext_AntiguedadStock s1
			INNER JOIN
			(
				SELECT mv.nIdLibro,
				SUM(nCantidadFirme) Cantidad
				FROM Cat_Movimiento_Stock mv (NOLOCK)
					INNER JOIN Gen_Movimiento_Stock t (NOLOCK)
						ON mv.nIdMotivo = t.nId
				WHERE dCreacion > {$fechainventario}
					AND nIdMotivo IN ($idajustemas)
				GROUP BY mv.nidlibro 
			) s2
				ON s1.nIdLibro = s2.nIdLibro";
		$this->db->query($sql);

		//BORRA LOS STOCKS 0
		$sql = "DELETE FROM Ext_AntiguedadStock
			WHERE ISNULL(nTotalFirme, 0) = 0 AND ISNULL(nDeposito,0) = 0";
		$this->db->query($sql);

		//BORRA LAS OFERTAS Y GENERICOS
		/*$sql = "DELETE FROM Ext_AntiguedadStock
			WHERE cTitulo like '%oferta%' 
				OR cTitulo like '%€%'";
		$this->db->query($sql);*/

		$sql = "DELETE Ext_AntiguedadStock
			FROM Ext_AntiguedadStock s (NOLOCK)
				INNER JOIN Cat_Fondo f (NOLOCK)
					ON s.nIdLibro = f.nIdLibro
			WHERE f.nIdEstado = 16";
		$this->db->query($sql);

		/*
		 3. Calculamos la antigüedad
		 */
		//APLICAMOS LA ANTIGUEDAD POR ENTRADAS
		//año 1
		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nFirme1 = nTotalFirme,
				nTotalFirme = 0			
			WHERE nEntradas1 >= nTotalFirme";
		$this->db->query($sql);

		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nFirme1 = nEntradas1,
					nTotalFirme = nTotalFirme - nEntradas1
			WHERE nEntradas1 < nTotalFirme";
		$this->db->query($sql);

		#año 2
		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nFirme2 = nTotalFirme,
					nTotalFirme = 0
			WHERE nEntradas2 >= nTotalFirme";
		$this->db->query($sql);

		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nFirme2 = nEntradas2,
					nTotalFirme = nTotalFirme - nEntradas2
			WHERE nEntradas2 < nTotalFirme";
		$this->db->query($sql);

		//año 3
		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nFirme3 = nTotalFirme,
					nTotalFirme = 0
			WHERE nEntradas3 >= nTotalFirme";
		$this->db->query($sql);

		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nFirme3 = nEntradas3,
					nTotalFirme = nTotalFirme - nEntradas3
			WHERE nEntradas3 < nTotalFirme";
		$this->db->query($sql);

		// año 4
		$sql = "UPDATE 	Ext_AntiguedadStock
		SET nFirme4 = nTotalFirme,
				nTotalFirme = 0
		WHERE nTotalFirme > 0";
		$this->db->query($sql);

		//RECUPERA LOS VALORES
		$sql = "UPDATE 	Ext_AntiguedadStock
			SET nTotalFirme = nFirme1 + nFirme2 + nFirme3 + nFirme4";
		$this->db->query($sql);

		//ELIMINA LAS COSAS RARAS
		$sql = "DELETE FROM Ext_AntiguedadStock
			WHERE nTotalFirme <= 0";
		$this->db->query($sql);

		//LOS PRECIOS A 0 LOS PONE AL PRECIO DE VENTA - MARGEN MEDIO
		$sql = "UPDATE Ext_AntiguedadStock
			SET fCoste = fPrecio - fPrecio * {$margen}
			FROM Ext_AntiguedadStock s
				INNER JOIN Cat_Fondo f 
					ON f.nIdLibro = s.nIdLibro
			WHERE fCoste = 0 OR fCoste IS NULL";
		//$this->db->query($sql);

		//Elimina los que tienen precio 0
		$sql = "DELETE FROM Ext_AntiguedadStock
			WHERE fCoste = 0";
		$this->db->query($sql);

		$this->clear_cache();

		return TRUE;
	}

	/**
	 * Devuelve los documentos que afectan al retroceso de stock
	 * @return array,  'albaranesentrada', 'albaranessalida', 'devoluciones', 'ajustes'
	 */
	function documentos()
	{
		set_time_limit(0);
		$idajustemas = $this->config->item('bp.stocks.idajustemas');
		$idajustemenos = $this->config->item('bp.stocks.idajustemenos');
		$fechainventario = $this->config->item('bp.stocks.fechainventario');
		$fechainventario = format_mssql_date(to_date(($fechainventario)));
		$fecharetroceso = $this->config->item('bp.oltp.fechadpr');
		$fecharetroceso = format_mssql_date(to_date(($fecharetroceso)));

		##ALBARANES DE ENTRADA
		$sql = "SELECT 	f.nIdLibro, f.cTitulo,
			    	SUM(lae.nCantidad) nCantidad, ae.nIdAlbaran
			    FROM Doc_LineasAlbaranesEntrada lae (NOLOCK)
			        INNER JOIN Doc_AlbaranesEntrada ae (NOLOCK)
			            ON lae.nIdAlbaran = ae.nIdAlbaran
			        INNER JOIN Cat_Fondo f (NOLOCK)
			        	ON f.nIdLibro = lae.nIdLibro
				WHERE ae.nIdEstado = 4  AND bDeposito = 0					         
					AND ae.dCierre >= " . $this->db->dateadd('d', 1, $fecharetroceso) . "
					AND ae.dCierre < {$fechainventario}
				GROUP BY f.nIdLibro, f.cTitulo, ae.nIdAlbaran";
		$query = $this->db->query($sql);
		$data['albaranesentrada'] = $this->_get_results($query);

		//VENTAS
		$sql = "SELECT f.nIdLibro, f.cTitulo,
						SUM(lal.nCantidad) Cantidad, al.nIdAlbaran
					FROM Doc_LineasAlbaranesSalida lal 
						INNER JOIN Doc_AlbaranesSalida al
							ON lal.nIdAlbaran = al.nIdAlbaran
					     INNER JOIN Cat_Fondo f (NOLOCK)
					       	ON f.nIdLibro = lal.nIdLibro AND f.nIdEstado <> 16
					WHERE al.nIdEstado IN (2, 3)
						AND al.dCreacion >= " . $this->db->dateadd('d', 1, $fecharetroceso) . "
						AND al.dCreacion < {$fechainventario}
					GROUP BY f.nIdLibro, f.cTitulo, al.nIdAlbaran";
		$query = $this->db->query($sql);
		$data['albaranessalida'] = $this->_get_results($query);

		//DEVOLUCIONES
		$sql = "SELECT f.nIdLibro, f.cTitulo,
						SUM(ld.nCantidad) Cantidad, d.nIdDevolucion
					FROM Doc_LineasDevolucion ld
						INNER JOIN Doc_Devoluciones d 
							ON ld.nIdDevolucion = d.nIdDevolucion
				        INNER JOIN Cat_Fondo f (NOLOCK)
				        	ON f.nIdLibro = ld.nIdLibro												
					WHERE d.dCierre >= " . $this->db->dateadd('d', 1, $fecharetroceso) . "
						AND d.dCierre < {$fechainventario}
						 AND bDeposito = 0
						 AND d.nIdEstado = 3
					GROUP BY f.nIdLibro, f.cTitulo, d.nIdDevolucion";
		$query = $this->db->query($sql);
		$data['devoluciones'] = $this->_get_results($query);

		//AJUSTES DE STOCK CONTADO
		$sql = "SELECT f.nIdLibro, f.cTitulo,
				SUM(nCantidadFirme) Cantidad
				FROM Cat_Movimiento_Stock mv (NOLOCK)
					INNER JOIN Gen_Movimiento_Stock t (NOLOCK)
						ON mv.nIdMotivo = t.nId
			        INNER JOIN Cat_Fondo f (NOLOCK)
			        	ON f.nIdLibro = mv.nIdLibro
						WHERE mv.dCreacion > {$fechainventario}
					AND nIdMotivo IN ($idajustemas)
				GROUP BY f.nIdLibro, f.cTitulo";
		$query = $this->db->query($sql);
		$data['ajustes'] = $this->_get_results($query);

		$sql = "SELECT f.nIdLibro, f.cTitulo,
				-SUM(nCantidadFirme) Cantidad
				FROM Cat_Movimiento_Stock mv (NOLOCK)
					INNER JOIN Gen_Movimiento_Stock t (NOLOCK)
						ON mv.nIdMotivo = t.nId
			        INNER JOIN Cat_Fondo f (NOLOCK)
			        	ON f.nIdLibro = mv.nIdLibro
						WHERE mv.dCreacion > {$fechainventario}
					AND nIdMotivo IN ($idajustemenos)
				GROUP BY f.nIdLibro, f.cTitulo";
		$query = $this->db->query($sql);
		$data['ajustes'] = array_merge($data['ajustes'], $this->_get_results($query));

		return $data;
	}

	/**
	 * Valorar el stock de los artículos en firme
	 * @return bool, TRUE: ha ido bien, FALSE: ha habido error
	 */
	function valorar()
	{
		set_time_limit(0);
		# Id
		$this->db->flush_cache();
		$this->db->select_max('nIdVolcado', 'nIdVolcado')
		->from("{$this->_prefix}Ext_AntiguedadStockVolcados");

		$query = $this->db->get();
		$data = $this->_get_results($query);
		$id = (isset($data[0]['nIdVolcado']))?$data[0]['nIdVolcado']:0;
		++$id;

		# Entrada nueva
		$data = array('nIdVolcado' => $id, 'dCreacion' => $this->_todate(time()));
		$this->db->insert("{$this->_prefix}Ext_AntiguedadStockVolcados", $data);
		#$id = 2592;

		# Añade nuevos
		$sql = "INSERT INTO {$this->_prefix}Ext_AntiguedadStock(
			nIdVolcado,
			nIdLibro, 
			cTitulo, 
			cISBN, 
			nTotalFirme,
			nDeposito,
			fCoste,
			nFirme1,
			nFirme2,
			nFirme3,
			nFirme4,
			nEntradas1,
			nEntradas2,
			nEntradas3,
			nEntradas4
			)
			SELECT {$id},
				sl.nIdLibro,
				f.cTitulo,
				f.cISBN,
				SUM(sl.nStockFirme),
				SUM(sl.nStockDeposito),
				f.fPrecioCompra,
				0, 0, 0, 0,
				0, 0, 0, 0		
	        FROM Cat_Secciones_Libros sl 
	            INNER JOIN Cat_Fondo f 
	                ON sl.nIdLibro = f.nIdLibro
	        WHERE ISNULL(sl.nStockFirme, 0) <> 0 OR
	            ISNULL(sl.nStockDeposito, 0) <> 0
	        GROUP BY sl.nIdLibro,
	            f.cTitulo,
	            f.cISBN,
	            f.fPrecioCompra";
		$this->db->query($sql);

		$sql = ($this->db->dbdriver == 'mssql')?"UPDATE {$this->_prefix}Ext_AntiguedadStock
			SET nEntradas1 = ENT.Cantidad
        	FROM {$this->_prefix}Ext_AntiguedadStock a INNER JOIN 
			(SELECT 	lp.nidlibro,
			    	SUM(lpr.nCantidad) 	Cantidad
			    FROM Doc_LineasPedidosRecibidas lpr 
			        INNER JOIN Doc_LineasAlbaranesEntrada lae 
			            ON lpr.nIdLineaAlbaran = lae.nIdLinea
			        INNER JOIN Doc_AlbaranesEntrada ae 
			            ON lae.nIdAlbaran = ae.nIdAlbaran AND ae.nIdEstado IN (2, 4)
                            AND ae.dCierre >= " . $this->db->dateadd('yy', -1, 'GETDATE()') . "
			        INNER JOIN Doc_LineasPedidoProveedor lp 
			            ON lpr.nIdLineaPedido = lp.nIdLinea
			GROUP BY lp.nidlibro 
			) ENT
				ON (ENT.nIdLibro = a.nIdLibro AND a.nIdVolcado = {$id})":
			"UPDATE {$this->_prefix}Ext_AntiguedadStock a
        	INNER JOIN 
			(SELECT 	lp.nidlibro,
			    	SUM(lpr.nCantidad) 	Cantidad
			    FROM Doc_LineasPedidosRecibidas lpr 
			        INNER JOIN Doc_LineasAlbaranesEntrada lae 
			            ON lpr.nIdLineaAlbaran = lae.nIdLinea
			        INNER JOIN Doc_AlbaranesEntrada ae 
			            ON lae.nIdAlbaran = ae.nIdAlbaran AND ae.nIdEstado IN (2, 4)
                            AND ae.dCierre >= " . $this->db->dateadd('yy', -1, 'GETDATE()') . "
			        INNER JOIN Doc_LineasPedidoProveedor lp 
			            ON lpr.nIdLineaPedido = lp.nIdLinea
			GROUP BY lp.nidlibro 
			) ENT
				ON (ENT.nIdLibro = a.nIdLibro AND a.nIdVolcado = {$id})
			SET a.nEntradas1 = ENT.Cantidad";
		$this->db->query($sql);


		$sql = ($this->db->dbdriver == 'mssql')?"UPDATE {$this->_prefix}Ext_AntiguedadStock
			SET nEntradas2 = ENT.Cantidad
        	FROM {$this->_prefix}Ext_AntiguedadStock a INNER JOIN 
			(SELECT 	lp.nidlibro,
			    	SUM(lpr.nCantidad) 	Cantidad
			    FROM Doc_LineasPedidosRecibidas lpr 
			        INNER JOIN Doc_LineasAlbaranesEntrada lae 
			            ON lpr.nIdLineaAlbaran = lae.nIdLinea
			        INNER JOIN Doc_AlbaranesEntrada ae 
			            ON lae.nIdAlbaran = ae.nIdAlbaran AND ae.nIdEstado IN (2, 4)
				AND ae.dCierre >=" . $this->db->dateadd('yy', -2, 'GETDATE()') . "
				AND ae.dCierre < " . $this->db->dateadd('yy', -1, 'GETDATE()') . "
			        INNER JOIN Doc_LineasPedidoProveedor lp 
			            ON lpr.nIdLineaPedido = lp.nIdLinea
			GROUP BY lp.nidlibro 
			) ENT
				ON (ENT.nIdLibro = a.nIdLibro AND a.nIdVolcado = {$id})":
			"UPDATE {$this->_prefix}Ext_AntiguedadStock a
        	INNER JOIN 
			(SELECT 	lp.nidlibro,
			    	SUM(lpr.nCantidad) 	Cantidad
			    FROM Doc_LineasPedidosRecibidas lpr 
			        INNER JOIN Doc_LineasAlbaranesEntrada lae 
			            ON lpr.nIdLineaAlbaran = lae.nIdLinea
			        INNER JOIN Doc_AlbaranesEntrada ae 
			            ON lae.nIdAlbaran = ae.nIdAlbaran AND ae.nIdEstado IN (2, 4)
				AND ae.dCierre >=" . $this->db->dateadd('yy', -2, 'GETDATE()') . "
				AND ae.dCierre < " . $this->db->dateadd('yy', -1, 'GETDATE()') . "
			        INNER JOIN Doc_LineasPedidoProveedor lp 
			            ON lpr.nIdLineaPedido = lp.nIdLinea
			GROUP BY lp.nidlibro 
			) ENT
				ON (ENT.nIdLibro = a.nIdLibro AND a.nIdVolcado = {$id})
			SET a.nEntradas2 = ENT.Cantidad";
		$this->db->query($sql);

		$sql = ($this->db->dbdriver == 'mssql')?"UPDATE {$this->_prefix}Ext_AntiguedadStock
			SET nEntradas3 = ENT.Cantidad
        	FROM {$this->_prefix}Ext_AntiguedadStock a INNER JOIN 
			(SELECT 	lp.nidlibro,
			    	SUM(lpr.nCantidad) 	Cantidad
			    FROM Doc_LineasPedidosRecibidas lpr 
			        INNER JOIN Doc_LineasAlbaranesEntrada lae 
			            ON lpr.nIdLineaAlbaran = lae.nIdLinea
			        INNER JOIN Doc_AlbaranesEntrada ae 
			            ON lae.nIdAlbaran = ae.nIdAlbaran AND ae.nIdEstado IN (2, 4)
				AND ae.dCierre >= " . $this->db->dateadd('yy', -3, 'GETDATE()') . "
				AND ae.dCierre < " . $this->db->dateadd('yy', -2, 'GETDATE()') . "
			        INNER JOIN Doc_LineasPedidoProveedor lp 
			            ON lpr.nIdLineaPedido = lp.nIdLinea
			GROUP BY lp.nidlibro 
			) ENT
				ON (ENT.nIdLibro = a.nIdLibro AND a.nIdVolcado = {$id})":
			"UPDATE {$this->_prefix}Ext_AntiguedadStock a
        	INNER JOIN 
			(SELECT 	lp.nidlibro,
			    	SUM(lpr.nCantidad) 	Cantidad
			    FROM Doc_LineasPedidosRecibidas lpr 
			        INNER JOIN Doc_LineasAlbaranesEntrada lae 
			            ON lpr.nIdLineaAlbaran = lae.nIdLinea
			        INNER JOIN Doc_AlbaranesEntrada ae 
			            ON lae.nIdAlbaran = ae.nIdAlbaran AND ae.nIdEstado IN (2, 4)
				AND ae.dCierre >= " . $this->db->dateadd('yy', -3, 'GETDATE()') . "
				AND ae.dCierre < " . $this->db->dateadd('yy', -2, 'GETDATE()') . "
			        INNER JOIN Doc_LineasPedidoProveedor lp 
			            ON lpr.nIdLineaPedido = lp.nIdLinea
			GROUP BY lp.nidlibro 
			) ENT
				ON (ENT.nIdLibro = a.nIdLibro AND a.nIdVolcado = {$id})
			SET a.nEntradas3 = ENT.Cantidad";
		$this->db->query($sql);

		$sql = ($this->db->dbdriver == 'mssql')?"UPDATE {$this->_prefix}Ext_AntiguedadStock
			SET nEntradas4 = ENT.Cantidad
        	FROM {$this->_prefix}Ext_AntiguedadStock a INNER JOIN 
			(SELECT 	lp.nidlibro,
			    	SUM(lpr.nCantidad) 	Cantidad
			    FROM Doc_LineasPedidosRecibidas lpr 
			        INNER JOIN Doc_LineasAlbaranesEntrada lae 
			            ON lpr.nIdLineaAlbaran = lae.nIdLinea
			        INNER JOIN Doc_AlbaranesEntrada ae 
			            ON lae.nIdAlbaran = ae.nIdAlbaran AND ae.nIdEstado IN (2, 4)
				AND ae.dCierre < " . $this->db->dateadd('yy', -3, 'GETDATE()') . "
			        INNER JOIN Doc_LineasPedidoProveedor lp 
			            ON lpr.nIdLineaPedido = lp.nIdLinea
			GROUP BY lp.nidlibro 
			) ENT
				ON (ENT.nIdLibro = a.nIdLibro AND a.nIdVolcado = {$id})":
			"UPDATE {$this->_prefix}Ext_AntiguedadStock a
        	INNER JOIN 
			(SELECT 	lp.nidlibro,
			    	SUM(lpr.nCantidad) 	Cantidad
			    FROM Doc_LineasPedidosRecibidas lpr 
			        INNER JOIN Doc_LineasAlbaranesEntrada lae 
			            ON lpr.nIdLineaAlbaran = lae.nIdLinea
			        INNER JOIN Doc_AlbaranesEntrada ae 
			            ON lae.nIdAlbaran = ae.nIdAlbaran AND ae.nIdEstado IN (2, 4)
				AND ae.dCierre < " . $this->db->dateadd('yy', -3, 'GETDATE()') . "
			        INNER JOIN Doc_LineasPedidoProveedor lp 
			            ON lpr.nIdLineaPedido = lp.nIdLinea
			GROUP BY lp.nidlibro 
			) ENT
				ON (ENT.nIdLibro = a.nIdLibro AND a.nIdVolcado = {$id})
			SET a.nEntradas4 = ENT.Cantidad";
		$this->db->query($sql);

		# APLICAMOS LA ANTIGUEDAD POR ENTRADAS
		# Año 1
		$sql = "UPDATE 	{$this->_prefix}Ext_AntiguedadStock 
			SET nFirme1 = nTotalFirme,
				nTotalFirme = 0			
			WHERE nEntradas1 >= nTotalFirme AND nIdVolcado = {$id}";
		$this->db->query($sql);

		$sql = "UPDATE 	{$this->_prefix}Ext_AntiguedadStock 
			SET nFirme1 = nEntradas1,
				nTotalFirme = nTotalFirme - nEntradas1
			WHERE nEntradas1 < nTotalFirme AND nIdVolcado = {$id}";
		$this->db->query($sql);

		# año 2
		$sql = "UPDATE 	{$this->_prefix}Ext_AntiguedadStock 
			SET nFirme2 = nTotalFirme,
				nTotalFirme = 0
			WHERE nEntradas2 >= nTotalFirme AND nIdVolcado = {$id}";
		$this->db->query($sql);

		$sql = "UPDATE 	{$this->_prefix}Ext_AntiguedadStock 
			SET nFirme2 = nEntradas2,
				nTotalFirme = nTotalFirme - nEntradas2
			WHERE nEntradas2 < nTotalFirme AND nIdVolcado = {$id}";
		$this->db->query($sql);

		# año 3
		$sql = "UPDATE 	{$this->_prefix}Ext_AntiguedadStock 
			SET nFirme3 = nTotalFirme,
				nTotalFirme = 0
			WHERE nEntradas3 >= nTotalFirme AND nIdVolcado = {$id}";
		$this->db->query($sql);

		$sql = "UPDATE 	{$this->_prefix}Ext_AntiguedadStock 
			SET nFirme3 = nEntradas3,
				nTotalFirme = nTotalFirme - nEntradas3
			WHERE nEntradas3 < nTotalFirme AND nIdVolcado = {$id}";
		$this->db->query($sql);

		# año 4
		$sql = "UPDATE 	{$this->_prefix}Ext_AntiguedadStock 
			SET nFirme4 = nTotalFirme,
				nTotalFirme = 0
			WHERE nTotalFirme > 0 AND nIdVolcado = {$id}";
		$this->db->query($sql);

		# RECUPERA LOS VALORES
		$sql = "UPDATE 	{$this->_prefix}Ext_AntiguedadStock 
			SET nTotalFirme = nFirme1 + nFirme2 + nFirme3 + nFirme4
		WHERE nIdVolcado = {$id}";
		$this->db->query($sql);

		#return $id;

		#ANTIGUEDAD POR SECCIONES
		$sql = "INSERT INTO {$this->_prefix}Ext_AntiguedadStockSecciones (
			nIdVolcado,
			nIdSeccion,
			cSeccion,
			nIdLibro,
			nStockFirme,
			fFirme1,
			fImporte1,
			fFirme2,
			fImporte2,
			fFirme3,
			fImporte3,
			fFirme4,
			fImporte4)

				SELECT {$id},
					sl.nIdSeccion,
					s.cNombre,
					sl.nIdLibro,
					sl.nStockFirme,
					" . $this->db->numeric('(sl.nStockFirme / (nTotalFirme * 1.0)) * (nFirme1)') . " fFirme1,
					" . $this->db->numeric('(sl.nStockFirme / (nTotalFirme * 1.0)) * (nFirme1)*fCoste') . " fImporte1,
					" . $this->db->numeric('(sl.nStockFirme / (nTotalFirme * 1.0)) * (nFirme2)') . " fFirme2,
					" . $this->db->numeric('(sl.nStockFirme / (nTotalFirme * 1.0)) * (nFirme2)*fCoste') . " fImporte2,
					" . $this->db->numeric('(sl.nStockFirme / (nTotalFirme * 1.0)) * (nFirme3)') . " fFirme3,
					" . $this->db->numeric('(sl.nStockFirme / (nTotalFirme * 1.0)) * (nFirme3)*fCoste') . " fImporte3,
					" . $this->db->numeric('(sl.nStockFirme / (nTotalFirme * 1.0)) * (nFirme4)') . " fFirme4,
					" . $this->db->numeric('(sl.nStockFirme / (nTotalFirme * 1.0)) * (nFirme4)*fCoste') . " fImporte4
				FROM {$this->_prefix}Ext_AntiguedadStock a (NOLOCK)
					INNER JOIN Cat_Secciones_Libros sl (NOLOCK)
						ON a.nIdLibro = sl.nIdLibro
					INNER JOIN Cat_Secciones s (NOLOCK)
						ON sl.nIdSeccion = s.nIdSeccion
				WHERE a.nIdVolcado = {$id} AND nTotalFirme <>0";
		$this->db->query($sql);

		#ACTUALIZA LOS PRECIOS A 0
		$sql = "UPDATE {$this->_prefix}Ext_AntiguedadStock
		SET fCoste = 0
		WHERE nIdVolcado = {$id} AND fCoste IS NULL";
		$this->db->query($sql);

		#Elimina Stock Suscripciones
		$sql = "DELETE FROM {$this->_prefix}Ext_AntiguedadStockSecciones
		WHERE cSeccion = 'SUSCRIPCIONES'
			AND nIdVolcado = {$id}";
		$this->db->query($sql);

		return $id;
	}
	
	/**
	 * Limpia los stocks atrasados
	 * @param int $dias Número de días hacía atrás que limpiar
	 * @return bool
	 */
	function limpiar($dias = 30)
	{
		set_time_limit(0);
		ini_set('mssql.timeout', 60 * 60 * 24);

		$sql = "SELECT nIdVolcado
				FROM {$this->_prefix}Ext_AntiguedadStockVolcados
				WHERE Day(" . $this->db->dateadd('d', 1, 'dCreacion') . ") = 1
					OR " . $this->db->datediff('dCreacion', 'GETDATE()') . " < {$dias}";
		$query = $this->db->query($sql);
		#var_dump(array_pop($this->db->queries)); die();
		$data = $this->_get_results($query);
		if (count($data) > 0 )
		{
			$ids = array();
			foreach ($data as $reg)
			{
				$ids[] = $reg['nIdVolcado'];
			}
			$filter = implode(',', $ids);

			$sql = "DELETE FROM {$this->_prefix}Ext_AntiguedadStockSecciones
			WHERE nIdVolcado NOT IN ({$filter})";
			$this->db->query($sql);
			
			$sql = "DELETE FROM {$this->_prefix}Ext_AntiguedadStock
			WHERE nIdVolcado NOT IN ({$filter})";
			$this->db->query($sql);
			
			$sql = "DELETE FROM {$this->_prefix}Ext_AntiguedadStockVolcados
			WHERE nIdVolcado NOT IN ({$filter})";
			$this->db->query($sql);
		}
		
		return TRUE;
	}

	/**
	 * Documentos vinculados a una sección (para testeo)
	 * @param  int $seccion Id de la sección
	 * @param  date $desde   Fecha desde
	 * @param  date $hasta   Fecha hasta
	 * @return array
	 */
	function documentos_seccion($seccion, $desde, $hasta)
	{
		set_time_limit(0);
		$idajustemas = $this->config->item('bp.stocks.idajustemas');
		$idajustemenos = $this->config->item('bp.stocks.idajustemenos');
		$fechainventario = $hasta;
		$fechainventario = format_mssql_date(to_date(($fechainventario)));
		$fecharetroceso = $desde;
		$fecharetroceso = format_mssql_date(to_date(($fecharetroceso)));

		##ALBARANES DE ENTRADA
		$sql = "SELECT 	f.nIdLibro, f.cTitulo,
			    	SUM(lae.nCantidad) nCantidad, ae.nIdAlbaran
			    FROM Doc_LineasAlbaranesEntrada lae (NOLOCK)
			        INNER JOIN Doc_AlbaranesEntrada ae (NOLOCK)
			            ON lae.nIdAlbaran = ae.nIdAlbaran
			        INNER JOIN Cat_Fondo f (NOLOCK)
			        	ON f.nIdLibro = lae.nIdLibro
			        INNER JOIN Doc_LineasPedidosRecibidas lpr (NOLOCK)
			        	ON lpr.nIdLineaAlbaran = lae.nIdLinea
			        INNER JOIN Doc_LineasPedidoProveedor lpp (NOLOCK)
			        	ON lpr.nIdLineaPedido = lpp.nIdLinea
				WHERE ae.nIdEstado = 4  AND bDeposito = 0					         
					AND ae.dCierre >= " . $this->db->dateadd('d', 1, $fecharetroceso) . "
					AND ae.dCierre < {$fechainventario}
					AND lpp.nIdSeccion = {$seccion}
				GROUP BY f.nIdLibro, f.cTitulo, ae.nIdAlbaran";
		$query = $this->db->query($sql);
		$data['albaranesentrada'] = $this->_get_results($query);

		//VENTAS
		$sql = "SELECT f.nIdLibro, f.cTitulo,
						SUM(lal.nCantidad) Cantidad, al.nIdAlbaran
					FROM Doc_LineasAlbaranesSalida lal 
						INNER JOIN Doc_AlbaranesSalida al
							ON lal.nIdAlbaran = al.nIdAlbaran
					     INNER JOIN Cat_Fondo f (NOLOCK)
					       	ON f.nIdLibro = lal.nIdLibro
					WHERE al.nIdEstado IN (2, 3)
						AND al.dCreacion >= " . $this->db->dateadd('d', 1, $fecharetroceso) . "
						AND al.dCreacion < {$fechainventario}
						AND lal.nIdSeccion = {$seccion}
					GROUP BY f.nIdLibro, f.cTitulo, al.nIdAlbaran";
		$query = $this->db->query($sql);
		$data['albaranessalida'] = $this->_get_results($query);

		//DEVOLUCIONES
		$sql = "SELECT f.nIdLibro, f.cTitulo,
						SUM(ld.nCantidad) Cantidad, d.nIdDevolucion
					FROM Doc_LineasDevolucion ld
						INNER JOIN Doc_Devoluciones d 
							ON ld.nIdDevolucion = d.nIdDevolucion
				        INNER JOIN Cat_Fondo f (NOLOCK)
				        	ON f.nIdLibro = ld.nIdLibro												
					WHERE d.dCierre >= " . $this->db->dateadd('d', 1, $fecharetroceso) . "
						AND d.dCierre < {$fechainventario}
						AND bDeposito = 0
						AND d.nIdEstado = 3
						AND ld.nIdSeccion = {$seccion}

					GROUP BY f.nIdLibro, f.cTitulo, d.nIdDevolucion";
		$query = $this->db->query($sql);
		$data['devoluciones'] = $this->_get_results($query);

		//AJUSTES DE STOCK CONTADO
		$sql = "SELECT f.nIdLibro, f.cTitulo,
				SUM(nCantidadFirme) Cantidad
				FROM Cat_Movimiento_Stock mv (NOLOCK)
					INNER JOIN Gen_Movimiento_Stock t (NOLOCK)
						ON mv.nIdMotivo = t.nId
			        INNER JOIN Cat_Fondo f (NOLOCK)
			        	ON f.nIdLibro = mv.nIdLibro
				WHERE mv.dCreacion > {$fechainventario}
					AND nIdMotivo IN ($idajustemas)
					AND mv.nIdSeccion = {$seccion}
				GROUP BY f.nIdLibro, f.cTitulo";
		$query = $this->db->query($sql);
		$data['ajustes'] = $this->_get_results($query);

		$sql = "SELECT f.nIdLibro, f.cTitulo,
				-SUM(nCantidadFirme) Cantidad
				FROM Cat_Movimiento_Stock mv (NOLOCK)
					INNER JOIN Gen_Movimiento_Stock t (NOLOCK)
						ON mv.nIdMotivo = t.nId
			        INNER JOIN Cat_Fondo f (NOLOCK)
			        	ON f.nIdLibro = mv.nIdLibro
						WHERE mv.dCreacion > {$fechainventario}
					AND nIdMotivo IN ($idajustemenos)
					AND mv.nIdSeccion = {$seccion}
				GROUP BY f.nIdLibro, f.cTitulo";
		$query = $this->db->query($sql);
		$data['ajustes'] = array_merge($data['ajustes'], $this->_get_results($query));

		return $data;
	}

	/**
	 * Lee el volcado completo
	 * @param  int $id Id del volcado
	 * @return string
	 */
	function get_volcado($id)
	{
		$this->db->flush_cache();
		$this->db->select('*')
		->from("{$this->_prefix}Ext_AntiguedadStock")
		->where('nIdVolcado =' . $id);
		$query = $this->db->get();
		return $this->_get_results($query);
	}
}

/* End of file M_antiguedadstock.php */
/* Location: ./system/application/models/stocks/M_antiguedadstock.php */
