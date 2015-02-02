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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

define('DEFAULT_ALBARAN_SALIDA_STATUS', 1);
define('ALBARAN_SALIDA_STATUS_CERRADO', 2);

/**
 * Albaranes de salida
 *
 */
class M_albaransalida extends MY_Model
{
	var $_lineas = null;

	/**
	 * Constructor
	 * @return M_albaransalida
	 */
	function __construct($tablename = null, $lineas = null)
	{
		$data_model = array(
			'nIdCliente'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'clientes/cliente/search', 'cCliente')),
			'nIdFactura'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/factura/search')),
			'nIdDireccion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'cliente/direccion/search')),
			'cRefCliente' 	=> array(), 
			'cRefInterna'	=> array(),

			'nIdEstado'		=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_ALBARAN_SALIDA_STATUS, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/estadoalbaransalida/search')),
			'fPortes' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'dFechaEnvio'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'nIdModoEnvio'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/modoenvio/search')),		
			'dFechaEntrega'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'bConforme' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),

			'tNotasExternas'	=> array(),
			'tNotasInternas'	=> array(),

			'bNoFacturable' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
			'bMostrarWeb' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => TRUE),
			'bExamen' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
            'nLibros' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
            'fTotal' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_MONEY),
            
			'cIdShipping'	=> array(),

			'nIdBiblioteca'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/biblioteca/search', 'cBiblioteca')),
			'nIdSala'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/sala/search', 'cSala')),
		);

		if (!isset($tablename)) $tablename = 'Doc_AlbaranesSalida';
		if (!isset($lineas)) $lineas = 'ventas/m_albaransalidalinea';
		$this->_lineas = $lineas;
		parent::__construct($tablename, 'nIdAlbaran', 'nIdAlbaran', array('cRefCliente', 'cRefInterna'), $data_model, TRUE);

		$this->_relations['lineas'] = array (
			'ref'		=> $lineas,
			'type'		=> DATA_MODEL_RELATION_1N,
			'cascade'	=> TRUE,
			'fk'		=> 'nIdAlbaran');

		$this->_relations['cliente'] = array (
			'ref'	=> 'clientes/m_cliente',
			'fk'	=> 'nIdCliente');

		$this->_relations['direccion'] = array (
			'ref'	=> 'clientes/m_direccioncliente',
			'fk'	=> 'nIdDireccion');				

		$this->_relations['albaransalidasuscripcion'] = array(
            'ref' => 'suscripciones/m_albaransalidasuscripcion',
            'cascade' => TRUE,
            'type' => DATA_MODEL_RELATION_1N,
            'fk' => 'nIdAlbaran');
	}

	/**
	 * Abona una factura albarán de salida 
	 * @param int $id Id de la factura
	 * @return mixed: FALSE, error, int Id del abono
	 */
	function abonar($id)
	{
		$d = $this->load($id, array('lineas', 'albaransalidasuscripcion'));
		if (empty($d))
		{
			$this->_set_error_message($this->lang->line('registro_no_encontrado'));
			return FALSE;
		}		
		unset($d['nIdAlbaran']);
		unset($d['nIdFactura']);
		unset($d['nIdEstado']);
		unset($d['dFechaEnvio']);
		unset($d['cCUser']);
		unset($d['cAUser']);
		unset($d['dCreacion']);
		unset($d['dAct']);
		foreach($d['lineas'] as $k => $v)
		{
			$d['lineas'][$k]['nCantidad'] = -$v['nCantidad'];
			$d['lineas'][$k]['fCoste'] = -$v['fCoste'];
			unset($d['lineas'][$k]['nIdLineaAlbaran']);
			unset($d['lineas'][$k]['nIdAlbaran']);
			unset($d['lineas'][$k]['cCUser']);
			unset($d['lineas'][$k]['cAUser']);
			unset($d['lineas'][$k]['dCreacion']);
			unset($d['lineas'][$k]['dAct']);
		}
		$sus = $d['albaransalidasuscripcion'];
		foreach($d['albaransalidasuscripcion'] as $k => $v)
		{
			unset($d['albaransalidasuscripcion'][$k]['nIdSuscripcionAlbaran']);
			unset($d['albaransalidasuscripcion'][$k]['nIdAlbaran']);
		}
		$this->db->trans_begin();
		$new_id = $this->insert($d);
		if ($new_id > 0)
		{
			# Añade el vínculo a la suscripción
			foreach($sus as $k => $v)
			{
				unset($sus[$k]['nIdSuscripcionAlbaran']);
				$sus[$k]['nIdAlbaran'] = $new_id;
			}
			if (!$this->update($new_id, $sus))
			{
				$this->db->trans_rollback();
				return -1;
			}
		}
		$this->db->trans_commit();		 
		return $new_id;		
	}

	/**
	 * Cierra un albarán de salida
	 * @param int $id Id del albarán de salida
	 */
	function cerrar($id)
	{
		$albaran = $this->load($id);
		if (empty($albaran))
		{
			$this->_set_error_message($this->lang->line('registro_no_encontrado'));
			return FALSE;
		}		
		if ($albaran['nIdEstado'] != DEFAULT_ALBARAN_SALIDA_STATUS)
		{
			#$this->_set_error_message($this->lang->line('error-albaransalida-cerrado'));
			return TRUE;
		}
		
		// Comprueba albarán a Examen
		if (isset($albaran['bExamen']))
		{
			if ($albaran['bExamen'])
			{
				$this->obj->load->model('clientes/m_cliente');
				$cl = $this->obj->m_cliente->load($albaran['nIdCliente']);
				if (!$cl['bExamen'])
				{
					$this->_set_error_message($this->lang->line('cliente-noexamen'));
					return FALSE;
				}
			}
		}

		// Leemos las líneas
		$this->db->flush_cache();
		$this->db->select('la.nIdLibro, la.nIdSeccion, la.nIdLineaAlbaran, la.fCoste, la.cRefCliente, la.nIdLineaPedido')
		->select('la.nCantidad, la.fPrecio, la.fDescuento, la.fIVA, la.fRecargo')
		->select('sl.nStockFirme, sl.nStockDeposito, sl.nIdSeccionLibro')
		->select('s.bBloqueada, s.cNombre')
		->select('a.nIdCliente, sl.nStockServir, sl.nStockReservado')
		->from("{$this->_tablename} a")
		->join('Doc_LineasAlbaranesSalida la', 'la.nIdAlbaran = a.nIdAlbaran')
		//->join('Cat_Fondo f', 'la.nIdLibro = f.nIdLibro')
		->join('Cat_Secciones s', 'la.nIdSeccion = s.nIdSeccion')
		->join('Cat_Secciones_Libros sl', 'la.nIdLibro = sl.nIdLibro AND la.nIdSeccion = sl.nIdSeccion', 'left')
		->where("la.nIdAlbaran = {$id}");

		$query = $this->db->get();
		$data = $this->_get_results($query);
		#echo '<pre>'; var_dump($data); die();

		// Si no tiene líneas se elimina
		if (count($data) == 0)
		{
			return $this->delete($id);
		}

		// Modelos de datos que se van a usar
		$obj = get_instance();
		$obj->load->model('ventas/m_albaransalidalinea');
		$obj->load->model('catalogo/m_articuloseccion');
		$obj->load->model('ventas/m_pedidoclientelineapendiente');
		$obj->load->model('ventas/m_pedidocliente');

		// Id del anticipo
		$idanticipo = $this->config->item('bp.anticipo.idarticulo');

		// Se comprueba línea a línea
		$this->db->trans_begin();
		// BUG #1556
		// Si hay más de una línea de albarán, solo resta el stock de la última
		// Se crea un array para ir almacenando los stocks
		$stock = array();
		$libros = 0;
 		$total = 0; $ivas = array(); $bases = array(); $totales = array(); $actual = 0;
 		foreach($data as $reg)
		{
			$libros += $reg['nCantidad'];
			#$totales = format_calculate_importes($reg);
			#$total += $totales['fTotal'];
			if ($reg['nCantidad'] != 0)
			{
				$linea = format_calculate_importes($reg);
				$linea = array_merge($reg, $linea);
				$total += format_decimals($linea['fTotal2']);
				$ivas[$linea['fIVA']] = (isset($ivas[$linea['fIVA']])?$ivas[$linea['fIVA']]:0) + ($linea['fIVAImporte2']);
				$bases[$linea['fIVA']] = (isset($bases[$linea['fIVA']])?$bases[$linea['fIVA']]:0) + ($linea['fBase2']);
				$totales[$linea['fIVA']] = (isset($totales[$linea['fIVA']])?$totales[$linea['fIVA']]:0) + ($linea['fTotal2']);
			}
						
			$id_stock = "{$reg['nIdSeccion']}_{$reg['nIdLibro']}";
			if (isset($stock[$id_stock]))
			{
				$reg['nStockFirme'] 	= $stock[$id_stock]['nStockFirme'];
				$reg['nStockDeposito'] 	= $stock[$id_stock]['nStockDeposito'];
				if (!isset($reg['nIdSeccionLibro'])) $reg['nIdSeccionLibro'] = $stock[$id_stock]['nIdSeccionLibro'];
			}

			// Bloqueada?
			if ($reg['bBloqueada'] == 1)
			{
				$this->_set_error_message(sprintf($this->lang->line('albaranes-cerrar-seccion-bloqueada'), $reg['cNombre']));
				$this->db->trans_rollback();
				return FALSE;
			}

			// Si no existe la relación con la sección, la crea
			if (!isset($reg['nIdSeccionLibro']))
			{
				// Crea la relación
				$idsl = $obj->m_articuloseccion->insert(array(
					'nIdSeccion'	=> $reg['nIdSeccion'],
					'nIdLibro'		=> $reg['nIdLibro'],
					'nStockFirme'	=> -$reg['nCantidad']
				));
				if ($idsl < 0)
				{
					$this->_set_error_message($obj->m_articuloseccion->error_message());
					$this->db->trans_rollback();
					return FALSE;
				}
				$firme 		= $reg['nCantidad'];
				$deposito 	= 0;

				$stock[$id_stock]['nStockFirme'] 		= -$reg['nCantidad'];
				$stock[$id_stock]['nStockDeposito'] 	= 0;
				$stock[$id_stock]['nIdSeccionLibro'] 	= $idsl;
			}
			else
			{
				// Asigna el stock
				$firme 		= min($reg['nStockFirme'], $reg['nCantidad']);
				$deposito 	= min($reg['nCantidad'] - $firme, $reg['nStockDeposito']);
				$firme 		+= $reg['nCantidad'] - ($firme + $deposito);

				// Actualiza la relación secciones-artículos
				if (!$obj->m_articuloseccion->update($reg['nIdSeccionLibro'], array(
					'nStockFirme'		=> $reg['nStockFirme'] - $firme,
					'nStockDeposito'	=> $reg['nStockDeposito'] - $deposito)))
				{
					$this->_set_error_message($obj->m_articuloseccion->error_message());
					$this->db->trans_rollback();
					return FALSE;
				}

				// Asigna el pedido de cliente
				if ((($reg['nStockReservado'] > 0) || ($reg['nStockServir'] > 0)) && ($reg['nCantidad'] > 0))
				{
					if (!$obj->m_pedidoclientelineapendiente->asignar_albaran($id, $albaran['nIdCliente'], $reg['nIdSeccion'], $reg['nIdLibro'], $reg['nCantidad'], $reg['nIdLineaPedido']))
					{
						$this->_set_error_message($obj->m_pedidoclientelineapendiente->error_message());
						$this->db->trans_rollback();
						return FALSE;
					}
				}
				$stock[$id_stock]['nStockFirme'] 		= $reg['nStockFirme'] - $firme;
				$stock[$id_stock]['nStockDeposito'] 	= $reg['nStockDeposito'] - $deposito;
			}

			//Si es un aticipo, actualiza el pedido de cliente
			if ($reg['nIdLibro'] == $idanticipo)
			{
				// En la referencia está el Id del pedido
				if (!isset($reg['cRefCliente']) || !is_numeric($reg['cRefCliente']))
				{
					$this->_set_error_message($this->lang->line('anticipo-no-id-pedido'));
					$this->db->trans_rollback();
					return FALSE;
				}
				$idpedido = $reg['cRefCliente'];
				if ($reg['nCantidad'] < 0)
				{
					// Se usa el anticipo en un albarán
					if (!$obj->m_pedidocliente->update($idpedido, array('nIdAlbaranDescuentaAnticipo' => $id)))
					{
						$this->_set_error_message($obj->m_pedidocliente->error_message());
						$this->db->trans_rollback();
						return FALSE;
					}
				}
				elseif ($reg['nCantidad'] > 0)
				{
					// Se abona el anticipo
					// Se usa el anticipo en un albarán
					$abono = array('nIdAlbaranDescuentaAnticipo' => null, 'nIdFactura' => $albaran['nIdFactura']);
					if (!$obj->m_pedidocliente->update($idpedido, $abono))
					{
						$this->_set_error_message($obj->m_pedidocliente->error_message());
						$this->db->trans_rollback();
						return FALSE;
					}
				}
			}

			// Actualiza la línea de albarán
			if (!$obj->m_albaransalidalinea->update($reg['nIdLineaAlbaran'], array(
				'nEnFirme'		=> $firme,
				'nEnDeposito'	=> $deposito)))
			{
				$this->_set_error_message($obj->m_albaransalidalinea->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
			#echo '<pre>';
			#var_dump($reg);
			#echo "Firme: {$firme}, Deposito: {$deposito}";
			#echo '</pre>';
		}
		$iva = 0;
		$base = 0;
		$total = 0;
		foreach($ivas as $k => $v)
		{
			#$i = format_iva(format_quitar_iva($totales[$k], $k), $k);
			#$b = format_quitar_iva($totales[$k], $k);
			$i = format_iva($bases[$k], $k);
			$b = $bases[$k];
			$iva += $i;
			$base += $b;
			$total += format_decimals($i + $b);
		}
		
		# Si hay suscripciones, actualiza el precio
		$this->db->select('a.nIdAlbaran, a.nIdSuscripcion, d.fPrecio, d.fDescuento, d.fCoste')
		->from('Sus_SuscripcionesAlbaranes a')
		->join('Doc_AlbaranesSalida b', 'a.nIdAlbaran = b.nIdAlbaran')
		->join('Sus_Suscripciones c', 'c.nIdSuscripcion = a.nIdSuscripcion')
		->join('Doc_LineasAlbaranesSalida d', 'd.nIdAlbaran = a.nIdAlbaran AND d.nIdLibro = c.nIdRevista')
		->where('a.nIdAlbaran = ' . $id);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		if (count($data) > 0)
		{
			$obj->load->model('suscripciones/m_suscripcion');
			foreach ($data as $sus)
			{
				$reg['fPrecio'] = $sus['fPrecio'];
				$reg['fPrecioCompra'] = abs($sus['fCoste']);
				$reg['fDescuento'] = $sus['fDescuento'];
				if (!$obj->m_suscripcion->update($sus['nIdSuscripcion'], $reg))
				{
					$this->_set_error_message($obj->m_suscripcion->error_message());
					$this->db->trans_rollback();
					return FALSE;
				}
			}
		}

		// Actualiza el estado del albarán
		if (!$this->update($id, array(
			'nIdEstado' 	=> ALBARAN_SALIDA_STATUS_CERRADO,
			'fTotal'		=> (float)$total,
			'nLibros'		=> $libros,
			)))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		//$this->db->trans_rollback();
		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Devuelve los importes de un albaránde salida
	 * @param int $id Id del albarán
	 * @return array
	 */
	function importes($id)
	{
		$obj = get_instance();
		$obj->load->model($this->_lineas, 'la');

		$tablename = $obj->la->get_tablename();
		$idname = $this->get_id();

		$this->db->flush_cache();
		$pvp = $this->db->numeric('(fPrecio * (1 + (fIVA / 100)))');
		$unitario = $this->db->numeric("({$pvp} * (1 - (fDescuento / 100)))");
		$total = "({$unitario} * nCantidad)";
		$base = $this->db->numeric("({$total} / (1 + (fIVA / 100)))");
		$iva = "({$total} - {$base})";
		$recargo = $this->db->numeric("({$base} * (fRecargo / 100))");

		$this->db->select_sum($base, 'fBase')
		->select_sum($iva, 'fIVAImporte')
		->select_sum($recargo, 'fRecargoImporte')
		->select('fIVA')
		->from($tablename)
		->group_by('fIVA')
		->where("{$tablename}.{$idname} = {$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$ivas = array();
		$totales['fBase'] = 0;
		$totales['fIVAImporte'] = 0;
		$totales['fRecargoImporte'] = 0;
		$totales['fTotal'] = 0 ;
		foreach ($data as $iva)
		{
			$iva['fTotal'] = $iva['fBase'] + $iva['fIVAImporte'] + $iva['fRecargoImporte'];
			$totales['fBase'] += $iva['fBase'];
			$totales['fIVAImporte'] += $iva['fIVAImporte'];
			$totales['fRecargoImporte'] += $iva['fRecargoImporte'];
			$totales['fTotal'] += $iva['fTotal'] ;
			$data = $iva;
			unset($data['fIVA']);
			$ivas[$iva['fIVA']] = $data;
		}
		return array('ivas' => $ivas, 'totales' => $totales);
	}

	/**
	 * Cierra un albarán de salida
	 * @param int $id Id del albarán de salida
	 */
	function set_total($id, $base_mode = FALSE, $pvp = TRUE)
	{
		$albaran = $this->load($id, 'lineas');
		if (empty($albaran))
		{
			$this->_set_error_message($this->lang->line('registro_no_encontrado'));
			return FALSE;
		}

		$total = 0; 
		$ivas = array(); 
		$bases = array(); 
		$totales = array(); 
		$libros = 0;

		foreach($albaran['lineas'] as $linea)
		{

			if ($linea['nCantidad'] != 0)
			{
				$libros += $linea['nCantidad'];
				$total += $base_mode?$linea['fTotal2']:$linea['fTotal'];
				$ivas[$linea['fIVA']] = (isset($ivas[$linea['fIVA']])?$ivas[$linea['fIVA']]:0) + ($base_mode?$linea['fIVAImporte2']:$linea['fIVAImporte']);
				$bases[$linea['fIVA']] = (isset($bases[$linea['fIVA']])?$bases[$linea['fIVA']]:0) + ($base_mode?$linea['fBase2']:$linea['fBase']);
				$totales[$linea['fIVA']] = (isset($totales[$linea['fIVA']])?$totales[$linea['fIVA']]:0) + ($base_mode?$linea['fTotal2']:$linea['fTotal']);
			}
		}

		$iva = 0;
		$base = 0;
		$total = 0;
		foreach($ivas as $k => $v)
		{
			$i = format_iva((($pvp)?format_quitar_iva($totales[$k], $k):$bases[$k]), $k);
			$b = ($pvp)?format_quitar_iva($totales[$k], $k):$bases[$k];
			$iva += $i;
			$base += $b;
			$total += $i + $b;
		}

		#var_dump($total); die();

		// Actualiza el estado del albarán
		return $this->update($id, array(
			'fTotal'		=> (float)$total,
			'nLibros'		=> $libros,
			));
	}

	/**
	 * Devuelve las suscripciones de un albarán de salida
	 * @param int $id Id del albarán
	 * @return int
	 */
	function get_suscripciones($id)
	{
		// Número de factura
		$this->db->flush_cache();
		$this->db->select("Sus_SuscripcionesAlbaranes.nIdSuscripcion")
		->from('Sus_SuscripcionesAlbaranes')
		->where("Sus_SuscripcionesAlbaranes.nIdAlbaran = {$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Albaranes pendientes de facturar
	 * @return array
	 */
	function sinfacturar()
	{
		$this->db->flush_cache();
		$this->db->select('Doc_AlbaranesSalida.nIdAlbaran, Doc_AlbaranesSalida.cCUser')
		->select($this->db->date_field('Doc_AlbaranesSalida.dCreacion', 'dCreacion'))
		->select('Doc_AlbaranesSalida.nLibros, Doc_AlbaranesSalida.fTotal')
		->select('Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cli_Clientes.cEmpresa, Cli_Clientes.nIdCliente')
		->from('Doc_AlbaranesSalida')
		->join('Cli_Clientes', 'Cli_Clientes.nIdCliente=Doc_AlbaranesSalida.nIdCliente')
		->where('nIdFactura IS NULL')
		->where('bNoFacturable <> 1')
		->where('Doc_AlbaranesSalida.nIdCliente NOT IN (SELECT Ext_EOISDepartamentos.nIdCliente FROM Ext_EOISDepartamentos)')
		->order_by('Cli_Clientes.cEmpresa, Cli_Clientes.cNombre, Cli_Clientes.cApellido');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}	

	/**
	 * Devuelve el listado de pedidos que sirve un albarán
	 * @param int $id Id del albarán
	 * @return array
	 */
	function pedidos($id)
	{
		$this->db->flush_cache();			
		$this->db->select('Doc_LineasAlbaranesSalida.nIdAlbaran nIdAlbaran, Doc_LineasAlbaranesSalida.nCantidad')
		->select('Doc_PedidosCliente.nIdPedido')
		->select('Cli_Clientes.cEmpresa, Cli_Clientes.cApellido, Cli_Clientes.cNombre, Cli_Clientes.nIdCliente')
		->select('Doc_LineasPedidoCliente.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.cISBN, Cat_Secciones.cNombre cSeccion, Cat_Editoriales.cNombre cEditorial')
		->select('Cat_Secciones.cNombre cSeccion')
		->from('Doc_LineasPedidoCliente')
		->join('Doc_PedidosCliente', 'Doc_PedidosCliente.nIdPedido=Doc_LineasPedidoCliente.nIdPedido')
		->join('Cli_Clientes', 'Doc_PedidosCliente.nIdCliente=Cli_Clientes.nIdCliente')
		->join('Cat_Fondo', "Cat_Fondo.nIdLibro = Doc_LineasPedidoCliente.nIdLibro")
		->join('Doc_LineasAlbaranesSalida', 'Doc_LineasAlbaranesSalida.nIdLineaPedido=Doc_LineasPedidoCliente.nIdLinea')
		->join('Doc_AlbaranesSalida', 'Doc_AlbaranesSalida.nIdAlbaran=Doc_LineasAlbaranesSalida.nIdAlbaran')
		->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = Doc_LineasPedidoCliente.nIdSeccion")
		->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial', 'left')
		->where("Doc_AlbaranesSalida.nIdAlbaran={$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		
		return $data;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 */
	protected function onBeforeDelete($id)
	{
		// Si el albarán no está en proceso, no se puede borrar
		$albaran = $this->load($id);
		if ($albaran['nIdEstado'] != DEFAULT_ALBARAN_SALIDA_STATUS)
		{
			$this->_set_error_message(sprintf($this->lang->line('error-albaransalida-cerrado'), $id));
			return FALSE;
		}

		// Actualiza los anticipos de los pedidos de cliente
		$obj = get_instance();
		$obj->load->model('ventas/m_pedidocliente');
		$data = $obj->m_pedidocliente->get(null, null, null, null, "Doc_PedidosCliente.nIdAlbaranDescuentaAnticipo = {$id}");
		if (isset($data[0]))
		{
			if (!$obj->m_pedidocliente->update($data[0]['nIdPedido'], array('nIdAlbaranDescuentaAnticipo' => null)))
			{
				$this->_set_error_message($obj->m_pedidocliente->error_message());
				return FALSE;
			}
		}

		return parent::onBeforeDelete($id);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cli_Clientes.cEmpresa')
			->select('Doc_EstadosAlbaranSalida.cDescripcion cEstado')
			->select('Ext_Bibliotecas.cDescripcion cBiblioteca')
			->select('Ext_Salas.cDescripcion cSala')
			->join('Cli_Clientes', "Cli_Clientes.nIdCliente = {$this->_tablename}.nIdCliente", 'left')
			->join('Doc_EstadosAlbaranSalida', "Doc_EstadosAlbaranSalida.nIdEstado = {$this->_tablename}.nIdEstado")
			->join('Ext_Bibliotecas', "Ext_Bibliotecas.nIdBiblioteca = {$this->_tablename}.nIdBiblioteca", 'left')
			->join('Ext_Salas', "Ext_Salas.nIdSala = {$this->_tablename}.nIdSala", 'left');
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		if (parent::onAfterSelect($data, $id))
		{
			$data['cCliente'] = format_name($data['cNombre'], $data['cApellido'], $data['cEmpresa']);
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Lee las ventas a las que aún no se les ha calculado la antigüedad
	 * @return arrayp
	 */
	function antiguedad()
	{
		$this->db->flush_cache();			
		$this->db->select('Doc_LineasAlbaranesSalida.nIdAlbaran nIdAlbaran')
		->select('Doc_LineasAlbaranesSalida.nIdLineaAlbaran, Doc_LineasAlbaranesSalida.nIdLibro, Doc_LineasAlbaranesSalida.nEnFirme')
		->select($this->db->date_field("{$this->_tablename}.dCreacion", 'dCreacion'))
		->from('Doc_LineasAlbaranesSalida')
		->join($this->_tablename, "Doc_LineasAlbaranesSalida.nIdAlbaran = {$this->_tablename}.nIdAlbaran")
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesSalida.nIdLibro')
		->where("{$this->_tablename}.nIdEstado=" . ALBARAN_SALIDA_STATUS_CERRADO)
		->where("YEAR({$this->_tablename}.dCreacion) >= 2010")
		->where("Doc_LineasAlbaranesSalida.nFirme1 IS NULL")
		->where('Doc_LineasAlbaranesSalida.nEnFirme > 0')
		->where('Cat_Fondo.nIdEstado <> 16')
		->order_by("{$this->_tablename}.dCreacion");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		
		return $data;
	}

	/**
	 * Muestra la antigüedad de las ventas por secciones
	 * @return array
	 */
	function antiguedad_salida()
	{
		$this->db->select('Cat_Secciones.cNombre,Cat_Secciones.cCodigo')
		->select_sum('Doc_LineasAlbaranesSalida.nFirme1', 'f1')
		->select_sum('Doc_LineasAlbaranesSalida.nFirme2', 'f2')
		->select_sum('Doc_LineasAlbaranesSalida.nFirme3', 'f3')
		->select_sum('Doc_LineasAlbaranesSalida.nFirme4', 'f4')
		->from('Doc_LineasAlbaranesSalida')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Doc_LineasAlbaranesSalida.nIdLibro')	
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion = Doc_LineasAlbaranesSalida.nIdSeccion')
		->where('Doc_LineasAlbaranesSalida.nFirme1 IS NOT NULL')
		->where('Cat_Fondo.nIdEstado NOT IN (16, 12, 15)')
		->group_by('Cat_Secciones.cNombre, Cat_Secciones.cCodigo')
		->order_by('Cat_Secciones.cNombre');

		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}
}

/* End of file M_albaransalida.php */
/* Location: ./system/application/models/ventas/M_albaransalida.php */
