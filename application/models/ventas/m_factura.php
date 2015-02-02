<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

define('FACTURA_STATUS_EN_PROCESO', 	1);
define('FACTURA_STATUS_CERRADA', 		2);
define('FACTURA_STATUS_CONTABILIZADA', 	3);
define('FACTURA_STATUS_A_PROCESAR', 	4);

define('DEFAULT_FACTURA_STATUS', FACTURA_STATUS_EN_PROCESO);

define('MODOPAGO_ABONO',	4);

/**
 * Albaranes de salida
 *
 */
class M_factura extends MY_Model
{
	var $_albaranes = null;
	var $_modospago = null;
	var $obj = null;
	var $admin = null;
	/**
	 * Constructor
	 * @return M_factura
	 */
	function __construct($facturas = null, $albaranes = null, $modospago = null, $lineasfactura = null, $tablaalbaranesalida = null)
	{
		$this->obj = get_instance();
		$idcliente = $this->obj->config->item('bp.tpv.cliente');
		$data_model = array(
			'nIdCliente'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_DEFAULT_VALUE => $idcliente, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'clientes/cliente/search', 'cCliente')),
			'nIdDireccion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'perfiles/dreccion/search', 'nIdDireccion')),
			'nIdDireccionEnvio'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'perfiles/dreccion/search', 'nIdDireccion')),
			'nIdSerie'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/serie/search', 'cSerie')),
			'nIdCaja'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/caja/search', 'cCaja')),
			'cRefCliente' 	=> array(), 
			'cRefInterna'	=> array(),
			'nIdEstado'		=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_FACTURA_STATUS, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/estadofactura/search')),

			'bTipoFactura' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),		
			'nNumero'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'dFecha'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'fPortes' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 

			'nIdVendedor'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/vendedor/search')),		
			'bCobrado' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),		
			'nIdAbono'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'bMostrarWeb'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),		

			'tNotasExternas'	=> array(),
			'tNotasInternas'	=> array(),
			
			'cIdShipping'	=> array(),
            'nLibros' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
            #'fTotal' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_MONEY),

			'_fTotal' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_MONEY), 
			'_bExentoIVA' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),		
		);

		if (!isset($facturas)) $facturas = 'Doc_Facturas';
		if (!isset($albaranes)) $albaranes = 'ventas/m_albaransalida';
		if (!isset($modospago)) $modospago = 'ventas/m_facturamodopago';
		if (!isset($lineasfactura)) $lineasfactura = 'ventas/m_facturalinea';
		if (!isset($tablaalbaranesalida)) $tablaalbaranesalida = 'Doc_AlbaranesSalida';

		$this->_albaranes = $albaranes;
		$this->_modospago = $modospago;
		parent::__construct($facturas, 'nIdFactura', 'nIdFactura', array('cRefCliente', 'cRefInterna'), $data_model, TRUE);

		$this->_relations['albaranes'] = array (
			'ref'	=> $albaranes,
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdFactura');

		$this->_relations['cliente'] = array (
			'ref'	=> 'clientes/m_cliente',
			'fk'	=> 'nIdCliente');

		$this->_relations['direccion'] = array (
			'ref'	=> 'clientes/m_direccioncliente',
			'fk'	=> 'nIdDireccion');

		$this->_relations['serie'] = array (
			'ref'	=> 'ventas/m_serie',
			'fk'	=> 'nIdSerie');

		$this->_relations['caja'] = array (
			'ref'	=> 'ventas/m_caja',
			'fk'	=> 'nIdCaja');

		$this->_relations['modospago'] = array (
			'ref'	=> $modospago,
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdFactura');

		$this->_relations['lineas'] = array (
			'ref'	=> $lineasfactura,
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk_table' => $tablaalbaranesalida,
			'fk'	=> 'nIdFactura');
	}

	/**
	 * Devuelve el siguiente número de factura libre
	 * @param int $idserie Id de la serie
	 * @return int
	 */
	protected function _get_numero($idserie)
	{
		// Número de factura
		$this->db->flush_cache();
		$this->db->select("(nContador + 1) nNumero")
		->from('Doc_Series')
		->where("nIdSerie = {$idserie}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$n = $data[0]['nNumero'];
		$this->db->flush_cache();
		$this->db->where("nIdSerie = {$idserie}")
		->update('Doc_Series', array("nContador" => $n));
		return $n;
	}

	/**
	 * Devuelve las suscripciones de una factura
	 * @param int $id Id de la factura
	 * @return int
	 */
	function get_suscripciones($id)
	{
		// Número de factura
		$this->db->flush_cache();
		$this->db->select("Sus_SuscripcionesAlbaranes.nIdSuscripcion")
		->from('Doc_AlbaranesSalida')
		->join('Sus_SuscripcionesAlbaranes', 'Sus_SuscripcionesAlbaranes.nIdAlbaran=Doc_AlbaranesSalida.nIdAlbaran')
		->where("Doc_AlbaranesSalida.nIdFactura = {$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}
	
	/**
	 * Abona una factura
	 * @param int $id Id de la factura
	 * @return int, Id del abono, < 0 error
	 */
	function abonar($id)
	{
		$d = $this->load($id, 'albaranes');
		# Datos factura
		unset($d['nIdFactura']);
		unset($d['nIdEstado']);
		unset($d['dFecha']);
		unset($d['nNumero']);
		unset($d['cCUser']);
		unset($d['cAUser']);
		unset($d['dCreacion']);
		unset($d['dAct']);
		$albaranes = $d['albaranes'];
		unset($d['albaranes']);
		$this->db->trans_begin();
		$id_n = $this->reg->insert($d);
		if ($id_n < 1)
		{
			$this->db->trans_rollback();
			return -1;
		}
		
		$this->obj->load->model($this->_albaranes, 'al');
		
		# Albaranes
		foreach($albaranes as $k => $v)
		{
			$id_a = $this->obj->al->abonar($v['nIdAlbaran']);
			if ($id_a < 0)
			{
				$this->_set_error_message($this->obj->al->error_message());
				$this->db->trans_rollback();
				return -1;			
			}
			if (!$this->obj->al->update($id_a, array('nIdFactura' => $id_n)))
			{
				$this->_set_error_message($this->obj->al->error_message());
				$this->db->trans_rollback();
				return -1;				
			}
		}
		$this->db->trans_commit();
		return $id_n;		
	}

	/**
	 * Procesa la factura
	 * @param int $id Id de la factura
	 * @return JSON
	 */
	function abrir($id)
	{
		$factura = $this->load($id);
		// Estado en proceso
		if ($factura['nIdEstado'] != FACTURA_STATUS_A_PROCESAR)
		{
			$this->_set_error_message(sprintf($this->lang->line('error-factura-abrir_'. $factura['nIdEstado']), $id));
			return FALSE;
		}
		$data['nIdEstado'] = FACTURA_STATUS_EN_PROCESO;
		if (!$this->update($id, $data))
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Cierra una factura
	 * @param int $id Id de la factura
	 * @return bool, TRUE: Cierre correcto, FALSE: Cierre no correcto
	 */
	function cerrar($id)
	{
		$factura = $this->load($id, array('modospago', 'serie', 'cliente', 'lineas'));
		// Estado en proceso
		if ($factura['nIdEstado'] != DEFAULT_FACTURA_STATUS)
		{
			$this->_set_error_message(sprintf($this->lang->line('error-factura-cerrada'), $id));
			return FALSE;
		}
		if (!isset($factura['nIdSerie']))
		{
			$this->_set_error_message(sprintf($this->lang->line('mensaje_faltan_datos_fields'), $this->lang->line('nIdSerie')));
			return FALSE;
		}

		// Número de factura
		 
		$numero = (is_numeric($factura['nNumero']) && $factura['nNumero'] > 0)?$factura['nNumero']:$this->_get_numero($factura['nIdSerie']);

		$this->db->trans_begin();

		// Actualiza abonos
		$this->obj->load->model('ventas/m_abono');
		$this->obj->load->model($this->_modospago, 'mp');
		$abonos = array();
		$total = 0;
		foreach($factura['modospago'] as $mp)
		{
			$total += $mp['fImporte'];
			if ($mp['nIdModoPago'] == MODOPAGO_ABONO)
			{
				if ($factura['cliente']['bCredito']) 
				{
					$this->db->trans_rollback();
					$this->_set_error_message(sprintf($this->lang->line('tpv-abono-no-cuenta'), 
						format_name($factura['cliente']['cNombre'], $factura['cliente']['cApellido'], $factura['cliente']['cEmpresa'])));
					return FALSE;
				}

				// Abonos positivos
				if ($mp['fImporte'] > 0)
				{
					if (!isset($mp['nIdAbono']))
					{
						$this->db->trans_rollback();
						$this->_set_error_message($this->lang->line('factura-no-abono'));
						return FALSE;
					}
					$abono = $this->obj->m_abono->load($mp['nIdAbono']);
					if (format_decimals($abono['fPendiente']) < format_decimals($mp['fImporte']))
					{
						$this->db->trans_rollback();
						$this->_set_error_message(sprintf($this->lang->line('tpv-abono-insuficiente'), $mp['nIdAbono'], $abono['fPendiente']));
						return FALSE;
					}
					if (!$this->obj->m_abono->update($mp['nIdAbono'], array('fUsado' => $abono['fUsado'] + $mp['fImporte'], 'dFecha' => $factura['dFecha'])))
					{
						$this->db->trans_rollback();
						$this->_set_error_message($this->obj->m_abono->error_message());
						return FALSE;
					}
					if ($abono['fUsado'] + $mp['fImporte'] < $abono['fImporte']) $abonos[] = $mp['nIdAbono'];
				}
				elseif ($mp['fImporte'] < 0)
				{
					if (isset($mp['nIdAbono']))
					{
						$this->db->trans_rollback();
						$this->_set_error_message(sprintf($this->lang->line('factura-abono-creado'), $mp['nIdAbono']));
						return FALSE;
					}
					$id_abono = $this->obj->m_abono->insert(array('nIdCliente' => $factura['nIdCliente'], 'fImporte' => -$mp['fImporte']));
					if ($id_abono < 0)
					{
						$this->db->trans_rollback();
						$this->_set_error_message($this->obj->m_abono->error_message());
						return FALSE;
					}
					if (!$this->obj->mp->update($mp['nIdFacturaModoPago'], array('nIdAbono' => $id_abono, 'dFecha' => $factura['dFecha'])))
					{
						$this->db->trans_rollback();
						$this->_set_error_message($this->obj->mp->error_message());
						return FALSE;
					}

					$abonos[] = $id_abono;
				}
				else
				{
					$this->db->trans_rollback();
					$this->_set_error_message($this->lang->line('factura-abono-importe-0'));
					return FALSE;
				}
			}
			else
			{
				if (!$this->obj->mp->update($mp['nIdFacturaModoPago'], array('dFecha' => $factura['dFecha'])))
				{
					$this->db->trans_rollback();
					$this->_set_error_message($this->obj->mp->error_message());
					return FALSE;
				}
			}
		}
		$libros = 0;
		$total2 = 0;
 		foreach($factura['lineas'] as $reg)
		{
			$libros += $reg['nCantidad'];
			if ($reg['nCantidad'] != 0)
			{
				$linea = format_calculate_importes($reg);
				$total2 += $linea['fTotal2'];
			}						
		}

		/*if (format_decimals($total) != format_decimals($total2))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->lang->line('tpv-pagos-incorrecto'));
			return FALSE;
		}*/

		# Actualiza las suscripciones
		$this->db->flush_cache();
		$this->db->select('Sus_SuscripcionesAlbaranes.nIdSuscripcion')
		->select('Doc_LineasAlbaranesSalida.nCantidad, Sus_Suscripciones.nFacturas')
		->from('Sus_SuscripcionesAlbaranes')
		->join('Sus_Suscripciones', 'Sus_Suscripciones.nIdSuscripcion=Sus_SuscripcionesAlbaranes.nIdSuscripcion')
		->join('Doc_AlbaranesSalida', 'Doc_AlbaranesSalida.nIdAlbaran=Sus_SuscripcionesAlbaranes.nIdAlbaran')
		->join('Doc_LineasAlbaranesSalida', 'Doc_AlbaranesSalida.nIdAlbaran=Doc_LineasAlbaranesSalida.nIdAlbaran AND Sus_Suscripciones.nIdRevista=Doc_LineasAlbaranesSalida.nIdLibro')
		->where('Doc_AlbaranesSalida.nIdFactura=' . $id);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		if (count($data)>0)
		{
			$this->obj->load->model('suscripciones/m_suscripcion');
			$sus = array();
			$upd['nIdUltimaFactura'] = $id;
			foreach ($data as $v)
			{
				$ft = (isset($sus[$v['nIdSuscripcion']]))?($sus[$v['nIdSuscripcion']] + $v['nCantidad']):($v['nFacturas']+ $v['nCantidad']); 
				$upd['nFacturas'] = $ft; 
				$sus[$v['nIdSuscripcion']] = $ft;
				if (!$this->obj->m_suscripcion->update($v['nIdSuscripcion'], $upd))
				{
					$this->_set_error_message($this->obj->m_suscripcion->error_message());
					return FALSE;
				}
			}
		}		

		// Actualiza la factura
		$total = format_decimals($total);		
		$data['nNumero'] = $numero;
		$data['_fTotal'] = format_decimals($total);
		$data['nLibros'] = $libros;
		$data['nIdEstado'] = FACTURA_STATUS_A_PROCESAR;

		if (!$this->update($id, $data))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		//$this->db->trans_rollback();
		$this->db->trans_commit();
		if ($this->_tablename != 'Doc_Facturas')
		{
			$this->add_last_factura($id, $factura['dFecha'], $data['_fTotal'],
			format_numerofactura($numero, $factura['serie']['nNumero']),
			$factura['cCliente'], $factura['cCUser'], $factura['dCreacion']);
		}
		return array(
			'numero' 	=> $numero, 
			'serie' 	=> $factura['serie']['nNumero'], 
			'abonos'	=> $abonos
		);
	}

	/**
	 * Procesa la factura
	 * @param int $id Id de la factura
	 * @return JSON
	 */
	function cerrar2($id)
	{
		$factura = $this->load($id, array('albaranes'));
		// Estado en proceso
		if ($factura['nIdEstado'] != FACTURA_STATUS_A_PROCESAR)
		{
			$this->_set_error_message(sprintf($this->lang->line('error-factura-cerrada'), $id));
			return FALSE;
		}
		// Cierre de los albaranes
		$this->db->trans_begin();
		// Actualiza la factura
		$data['nIdEstado'] = FACTURA_STATUS_CERRADA;
		if (!$this->update($id, $data))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		$this->obj->load->model('ventas/m_albaransalida');
		foreach ($factura['albaranes'] as $albaran)
		{
			#echo ' Cerrando albarán ' . $albaran['nIdAlbaran'];
			if (!$this->obj->m_albaransalida->cerrar($albaran['nIdAlbaran']))
			{
				$this->db->trans_rollback();
				$this->_set_error_message($this->obj->m_albaransalida->error_message());
				return FALSE;
			}
		}

		$this->db->trans_commit();
		if ($this->_tablename != 'Doc_Facturas')
		{
			$this->add_last_factura($id, $factura['dFecha'], $factura['_fTotal'], $factura['cNumero'],
			$factura['cCliente'], $factura['cCUser'], $factura['dCreacion']);
		}

		return TRUE;
	}

	/**
	 * Añade la venta a la caché de las últimas ventas
	 * @param $id
	 * @param $fecha
	 * @param $importe
	 * @param $numero
	 * @param $cliente
	 * @param $user
	 * @param $creacion
	 */
	function add_last_factura($id, $fecha, $importe, $numero, $cliente, $user, $creacion)
	{
		$cache_id = 'last_facturas';
		$new[] = array(
			'nIdFactura' 	=> $id,
			'id'			=> $id,
			'nIdCliente'	=> $cliente,
			'cCliente'		=> $cliente,
			'dFecha'		=> $fecha,
			'_fTotal'		=> $importe,
			'cNumero'		=> $numero,
			'cCUser'		=> $user,
			'dCreacion'		=> $creacion,				
		);

		if ($cache = $this->cache->fetch(null, $cache_id, $this->DATA_MODE_CACHE_TYPE))
		{
			$cache = array_merge($new, $cache);
		}
		else
		{
			$cache = $new;
		}

		array_splice($cache, $this->config->item('bp.data.search.limit'));

		$this->cache->store(null, $cache_id, $cache, $this->DATA_MODE_CACHE_TIME, $this->DATA_MODE_CACHE_TYPE);
	}

	/**
	 * Últimas facturas
	 */
	function get_last_factura()
	{
		$cache_id = 'last_facturas';
		$data = $this->cache->fetch(null, $cache_id, $this->DATA_MODE_CACHE_TYPE);
		return (!isset($data))?array():$data;
	}

	/**
	 * Calcula el próximo número de factura
	 * @return bool
	 */
	function numeros()
	{
		$this->db->flush_cache();
		$this->db->select('nIdSerie')
		->select_max('nNumero', 'nNumero')
		->from('Doc_Facturas')
		->where('nIdSerie IS NOT NULL AND nNumero IS NOT NULL')
		->group_by('nIdSerie');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		if (count($data)>0)
		{
			foreach ($data as $value) 
			{
				$this->db->where('nIdSerie=' . $value['nIdSerie'])
				->update('Doc_Series', array('nContador' => (int) $value['nNumero']));
			}
		}
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
			$this->db->select('Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cli_Clientes.cEmpresa');
			$this->db->join('Cli_Clientes', "Cli_Clientes.nIdCliente = {$this->_tablename}.nIdCliente", 'left');
			$this->db->select('Doc_Series.nNumero nSerieNumero, Doc_Series.cDescripcion cSerie');
			$this->db->join('Doc_Series', "Doc_Series.nIdSerie = {$this->_tablename}.nIdSerie", 'left');
			$this->db->select('Gen_Cajas.cDescripcion cCaja');
			$this->db->join('Gen_Cajas', "Gen_Cajas.nIdCaja = {$this->_tablename}.nIdCaja", 'left');
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
			if (isset($data['nSerieNumero']) && isset($data['nNumero']))
			{
				$data['cNumero'] = format_numerofactura($data['nNumero'], $data['nSerieNumero']);
			}
			$data['cCliente'] = format_name($data['cNombre'], $data['cApellido'], $data['cEmpresa']);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSearch($query, $where, $fields)
	 */
	protected function onBeforeSearch($query, &$where, &$fields)
	{
		if (parent::onBeforeSearch($query, $where, $fields))
		{
			//Número de factura?
			if (preg_match('/^(\d+)-(\d+)/', $query, $num))
			{
				$numero = $num[1];
				$serie = $num[2];
				$where = "{$this->_tablename}.nNumero = {$numero}";
				$where .= " AND {$this->_tablename}.nIdSerie IN (SELECT Doc_Series.nIdSerie FROM Doc_Series WHERE Doc_Series.nNumero={$serie})";
				//$fields .= ($fields != '')?',':'' . $this->_tablename . '.cISBN';
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 */
	protected function onBeforeDelete($id)
	{
		// Si la factura no está en proceso, no se puede borrar
		$factura = $this->load($id, array('albaranes', 'modospago'));
		if (!isset($factura['nIdFactura']))
		{
			$this->_set_error_message($this->lang->line('registro_no_encontrado'));
			return FALSE;
		}
		if ($factura['nIdEstado'] != DEFAULT_FACTURA_STATUS)
		{
			$this->_set_error_message(sprintf($this->lang->line('error-factura-cerrada'), $id));
			return FALSE;
		}
		
		# Actualiza las suscripciones
		$this->db->flush_cache();
		$this->db->select('Sus_SuscripcionesAlbaranes.nIdSuscripcion')
		->select('Doc_LineasAlbaranesSalida.nCantidad, Sus_Suscripciones.nFacturas, Sus_Suscripciones.nIdUltimaFactura')
		->from('Sus_SuscripcionesAlbaranes')
		->join('Sus_Suscripciones', 'Sus_Suscripciones.nIdSuscripcion=Sus_SuscripcionesAlbaranes.nIdSuscripcion')
		->join('Doc_AlbaranesSalida', 'Doc_AlbaranesSalida.nIdAlbaran=Sus_SuscripcionesAlbaranes.nIdAlbaran')
		->join('Doc_LineasAlbaranesSalida', 'Doc_AlbaranesSalida.nIdAlbaran=Doc_LineasAlbaranesSalida.nIdAlbaran AND Sus_Suscripciones.nIdRevista=Doc_LineasAlbaranesSalida.nIdLibro')
		->where('Doc_AlbaranesSalida.nIdFactura=' . $id);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		
		#var_dump($data);
		if (count($data)>0)
		{
			$this->obj->load->model('suscripciones/m_suscripcion');
			$sus = array();
			foreach ($data as $v)
			{
				#$last = $this->m_suscripcion->get_factura($sus, TRUE, TRUE);
				#$last = (isset($last[0]['nIdFactura']))?$last[0]['nIdFactura']:null;
				$upd['nIdUltimaFactura'] = ($v['nIdUltimaFactura']==$id)?null:$v['nIdUltimaFactura'];
				$ft = (isset($sus[$v['nIdSuscripcion']]))?($sus[$v['nIdSuscripcion']] - $v['nCantidad']):($v['nFacturas'] - $v['nCantidad']); 
				$upd['nFacturas'] = $ft; 
				$sus[$v['nIdSuscripcion']] = $ft;
				if (!$this->obj->m_suscripcion->update($v['nIdSuscripcion'], $upd))
				{
					$this->_set_error_message($this->obj->m_suscripcion->error_message());
					return FALSE;
				}
			}
		}		

		// Borra los albaranes de salida que no están cerrados
		$this->obj->load->model($this->_albaranes, 'al');
		foreach($factura['albaranes'] as $albaran)
		{
			if ($albaran['nIdEstado'] == DEFAULT_ALBARAN_SALIDA_STATUS)
			{
				// Elimina el abierto
				if (!$this->obj->al->delete($albaran['nIdAlbaran']))
				{
					$this->_set_error_message($this->obj->al->error_message());
					return FALSE;
				}
			}
			else
			{
				// Quita de la factura el cerrado
				if (!$this->obj->al->update($albaran['nIdAlbaran'], array('nIdFactura' => null)))
				{
					$this->_set_error_message($this->obj->al->error_message());
					return FALSE;
				}
			}
		}

		// Borra los modos de pago
		$this->obj->load->model($this->_modospago, 'mp');
		if (!$this->obj->mp->delete_by("nIdFactura = {$id}"))
		{
			$this->_set_error_message($this->obj->mp->error_message());
			return FALSE;
		}

		// Actualiza los anticipos de los pedidos de cliente
		$this->obj->load->model('ventas/m_pedidocliente');
		$data = $this->obj->m_pedidocliente->get(null, null, null, null, "Doc_PedidosCliente.nIdFactura = {$id}");
		if (isset($data[0]))
		{
			if (!$this->obj->m_pedidocliente->update($data[0]['nIdPedido'], array('nIdFactura' => null, 'fAnticipo' => 0)))
			{
				$this->_set_error_message($this->obj->m_pedidocliente->error_message());
				return FALSE;
			}
		}

		# El abono no tiene que actualizarse

		return parent::onBeforeDelete($id);
	}

	/**
	 * Comprueba la validez de la serie
	 * @param array $data Datos a actualizar
	 * @return bool, TRUE: ok, FALSE: Serie no válida
	 */
	protected function _check_serie($data)
	{
		# Comprueba si la serie está bloqueada
		if (isset($data['nIdSerie']))
		{
			$this->obj->load->model('ventas/m_serie');
			$d = $this->obj->m_serie->load($data['nIdSerie']);
			if ((isset($d['dDesde']) && $d['dDesde'] > time()) ||
				(isset($d['dHasta']) && $d['dHasta'] < time()))
			{
				$this->_set_error_message($this->lang->line('factura-serie-bloqueda-fecha'));
				return FALSE;
			}
			if ($d['nIdSatelite'] != $this->config->item('bp.application.sucursal'))
			{
				$this->_set_error_message($this->lang->line('factura-serie-bloqueda-tienda'));
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Marca la factura como contabilizada
	 * @param  string $ids IDS separados por ;
	 * @return bool TRUE: Facturas contabilizadas
	 */
	function contabilizar($ids)
	{
		$id2 = array();
		$ids = preg_split('/;/', $ids);
		foreach ($ids as $id)
		{
			if (is_numeric($id)) $id2[] = $id;
		}
		if (count($id2) > 0) 
		{
			$reg = $this->_modospago;
			$this->obj->load->model($this->_modospago, 'mp');
			$this->db->trans_begin();
			if (!$this->db->where('nIdFactura IN (' . implode(',', $id2) . ')')->update($this->_tablename, array('nIdEstado' => FACTURA_STATUS_CONTABILIZADA)))
			{
				$this->_set_error_message($this->db->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}

			if (!$this->db->where('nIdFactura IN (' . implode(',', $id2) . ')')->update($this->obj->mp->get_tablename(), array('bContabilizado' => TRUE)))
			{
				$this->_set_error_message($this->db->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}

			$this->db->trans_commit();
		}
		return TRUE;
	}

	/**
	 * Quita la marca de contabilizada
	 * @param  string $ids IDS separados por ;
	 * @return bool TRUE: Facturas des contabilizadas
	 */
	function descontabilizar($ids)
	{
		$id2 = array();
		$ids = preg_split('/;/', $ids);
		foreach ($ids as $id)
		{
			if (is_numeric($id)) $id2[] = $id;
		}
		if (count($id2) > 0) 
		{
			$reg = $this->_modospago;
			$this->obj->load->model($this->_modospago, 'mp');
			$this->db->trans_begin();
			if (!$this->db->where('nIdFactura IN (' . implode(',', $id2) . ')')->update($this->_tablename, array('nIdEstado' => FACTURA_STATUS_CERRADA)))
			{
				$this->_set_error_message($this->db->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}

			if (!$this->db->where('nIdFactura IN (' . implode(',', $id2) . ')')->update($this->obj->mp->get_tablename(), array('bContabilizado' => FALSE)))
			{
				$this->_set_error_message($this->db->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}

			$this->db->trans_commit();
		}
		return TRUE;
	}

	/**
	 * Ajusta la factura para cuadrar con el pago
	 * @param  int $id Id de la factura
	 * @return mixed, FALSE: error, int diferencia
	 */
	function ajustepago($id)
	{
		$factura = $this->load($id, array('modospago', 'lineas'));
		$total = 0;
		$id_mp = null;
		foreach($factura['modospago'] as $mp)
		{
			$total += $mp['fImporte'];
			if (!isset($id_mp) && (in_array($mp['nIdModoPago'], array(6))))
			{
				$id_mp = $mp;
			}
		}
		$total2 = 0;
 		foreach($factura['lineas'] as $reg)
		{
			if ($reg['nCantidad'] != 0)
			{
				$linea = format_calculate_importes($reg);
				$total2 += $linea['fTotal2'];
			}						
		}

		$diff = format_decimals($total) - format_decimals($total2);
		if ($diff != 0)
		{
			#var_dump($id_mp); die();
			if (isset($id_mp))
			{
				$this->obj->load->model('ventas/m_facturamodopago');
				$upd = array('fImporte' => $id_mp['fImporte'] - (($diff > 0)?$diff:-$diff));
				if (!$this->obj->m_facturamodopago->update($id_mp['nIdFacturaModoPago'], $upd))
				{
					$this->_set_error_message($this->obj->m_facturamodopago->error_message());
					return FALSE;
				}
			}
			else
			{
				$upd[] = array(
					'nIdAlbaran'	=> $reg['nIdAlbaran'],
					'nIdFactura'	=> $id,
					'nCantidad' 	=> ($diff > 0)?1:-1,
					'fPrecio' 		=> ($diff > 0)?$diff:-$diff,
					'fIVA' 			=> 0,
					'fRecargo' 		=> 0,
					'fDescuento' 	=> 0,
					'nIdSeccion'	=> $this->config->item('bp.factura.idseccionajuste'),
					'nIdLibro'		=> $this->config->item('bp.factura.idlibroajuste')
				);

				if (!$this->update($id, array('lineas' => $upd)))
				{
					return FALSE;
				}

				$factura = $this->load($id, array('albaranes'));
				foreach ($factura['albaranes'] as $alb)
				{
					if ($alb['nIdEstado'] != ALBARAN_SALIDA_STATUS_CERRADO)
					{
						$this->obj->load->model($this->_albaranes, 'al');
						if (!$this->obj->al->cerrar($alb['nIdAlbaran']))
						{
							$this->_set_error_message($this->obj->al->error_message());
							return FALSE;
						}
					}
				}
			}
		}
		else
		{
			$this->_set_error_message(sprintf($this->lang->line('ajuste-factura-cuadarada'), $id));
			return FALSE;
		}
		return $diff;
	}

	/**
	 * Copia el nombre del cliente como referencia de cada albaran
	 * @param  int $id Id de la factura
	 * @return bool
	 */
	function ref($id)
	{
		$sql = ($this->db->dbdriver == 'mssql')?"update Doc_AlbaranesSalida 
			set cRefCliente = REPLACE(c.cEmpresa, '(DDGi)', '')
			from Doc_AlbaranesSalida a
				inner join Cli_Clientes c
					on a.nIdCliente = c.nIdCliente
			where a.nIdFactura = {$id}":
			"update Doc_AlbaranesSalida a
				inner join Cli_Clientes c
					on a.nIdCliente = c.nIdCliente
			set cRefCliente = REPLACE(c.cEmpresa, '(DDGi)', '')
			where a.nIdFactura = {$id}";
		return $this->db->query($sql);
	}

	/**
	 * Devuelve el listado de pedidos que sirven una factura
	 * @param int $id Id de la factura
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
		->join('Doc_Facturas', 'Doc_Facturas.nIdFactura=Doc_AlbaranesSalida.nIdFactura')
		->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = Doc_LineasPedidoCliente.nIdSeccion")
		->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial', 'left')
		->where("Doc_Facturas.nIdFactura={$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		
		return $data;
	}

	/**
	 * Trigger llamado Antes de insertar los datos
	 * @param array $data Registro a insertar
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			// La fecha solo puede ser indicado por un administrador
			$admin = $this->obj->userauth->roleCheck('ventas.factura.administrar', null, TRUE);

			if (!($admin || $this->admin) && (isset($data['dFecha'])))
			{
				$data['dFecha'] = time();
			}
			if (empty($data['dFecha']))
			{
				$data['dFecha'] = time();
			}
			return $this->_check_serie($data);
		}

		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($id, $data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		#echo 'en onBeforeUpdate';
		#var_dump($_GET);
		#var_dump($this);
		#var_dump($data); die();
		//echo 'En onBeforeUpdate';
		if (parent::onBeforeUpdate($id, $data))
		{
			if (!$this->_check_serie($data)) return FALSE;

			if (isset($id) && (isset($data['nIdCliente']) || isset($data['nIdSerie'])||isset($data['dFecha'])||isset($data['nIdCaja'])))
			{
				$factura = $this->load($id, 'modospago');
				// Cambio de cliente
				if (isset($data['nIdCliente']))
				{
					if (isset($factura['nIdCliente']) && ($factura['nIdCliente'] != $data['nIdCliente']) && ($factura['nIdEstado'] != DEFAULT_FACTURA_STATUS))
					{
						//Lee el anterior
						$this->obj->load->model('clientes/m_cliente');
						$cl1 = $this->obj->m_cliente->load($factura['nIdCliente']);
						$cl2 = $this->obj->m_cliente->load($data['nIdCliente']);
						if (($cl1['bExentoIVA'] != $cl2['bExentoIVA']) || isset($cl1['nIdCuenta']) || isset($cl2['nIdCuenta']))
						{
							$this->_set_error_message($this->lang->line('factura-error-cambio-cliente'));
							return FALSE;
						}
					}
				}
				
				// Cambio de serie, caja o fecha
				$admin = $this->obj->userauth->roleCheck('ventas.factura.administrar', null, TRUE);

				if (($factura['nIdEstado'] != DEFAULT_FACTURA_STATUS) && !$admin &&
				((isset($data['nIdSerie']) && $data['nIdSerie'] != $factura['nIdSerie'])
				||(isset($data['nIdCaja']) && $data['nIdCaja'] != $factura['nIdCaja'])
				))
				{
					$this->_set_error_message($this->lang->line('factura-error-cambio-serie-caja-fecha'));
					return FALSE;
				}
				if (($factura['nIdEstado'] != DEFAULT_FACTURA_STATUS) && !$admin &&
				(isset($data['dFecha']) && $data['dFecha'] != $factura['dFecha']))
				{
					unset($data['dFecha']);
				}

				// Cambia el número de factura
				if ((isset($data['nIdSerie']) && $data['nIdSerie'] != $factura['nIdSerie']) && ($factura['nIdEstado'] != DEFAULT_FACTURA_STATUS))
				{
					$data['nNumero'] = $this->_get_numero($data['nIdSerie']);
				}

				// Cambia las cajas de los modos de pago
				if (((isset($data['nIdCaja']) && $data['nIdCaja'] != $factura['nIdCaja']) && isset($factura['modospago']) && ($factura['nIdEstado'] != DEFAULT_FACTURA_STATUS))||(isset($data['dFecha'])))
				{
					foreach($factura['modospago'] as $mp)
					{
						$reg = array(
							'nIdFacturaModoPago' 	=> $mp['nIdFacturaModoPago'], 
						);
						if ((isset($data['nIdCaja']) && $data['nIdCaja'] != $factura['nIdCaja']) && isset($factura['modospago']) && ($factura['nIdEstado'] != DEFAULT_FACTURA_STATUS))
						{
							$reg['nIdCaja'] = $data['nIdCaja'];
						}
						if (isset($data['dFecha']))
						{
							$reg['dFecha'] = $data['dFecha'];
						}

						$data['modospago'][] = $reg;
					}
				}
				# Albarán en las líneas?
				if (isset($data['lineas']))
				{
					$id_albaran = null;
					foreach ($data['lineas'] as $linea )
					{
						if (isset($linea['nIdAlbaran'])&&($linea['nIdAlbaran'] != '')) $id_albaran = $linea['nIdAlbaran'];
					}
					# Si no hay albarán en las líneas, coge uno abierto
					if (!isset($id_albaran))
					{
						$this->obj->load->model($this->_albaranes, 'al');
						$albs = $this->obj->al->get(0, 1, 0, 0, "nIdFactura ={$id} AND nIdEstado=".DEFAULT_ALBARAN_SALIDA_STATUS);
						if (count($albs)==1)
						{
							$id_albaran = $albs[0]['id'];
						}						
					}
					
					// Si no hay albarán y si factura lo crea
					if (!isset($id_albaran))
					{
						$albaran['nIdCliente'] = isset($data['nIdCliente'])?$data['nIdCliente']:$factura['nIdCliente'];
						$albaran['nIdDireccion'] = isset($data['nIdDireccion'])?$data['nIdDireccion']:$factura['nIdDireccion'];
						$albaran['nIdFactura'] = $id;
						$id_albaran = $this->obj->al->insert($albaran);
						if ($id_albaran <= 0)
						{
							$this->_set_error_message($this->obj->al->error_message());
							return FALSE;
						}
					}
					foreach ($data['lineas'] as $k => $linea)
					{
						if (!isset($linea['nIdAlbaran'])||($linea['nIdAlbaran'] == '')) $data['lineas'][$k]['nIdAlbaran'] = $id_albaran;
					}
				}
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert()
	 */
	protected function onAfterInsert($id, &$data)
	{
		if (isset($data['lineas']))
		{
			$id_albaran = null;
			foreach ($data['lineas'] as $linea )
			{
				if (isset($linea['nIdAlbaran'])&&($linea['nIdAlbaran'] != '')) $id_albaran = $linea['nIdAlbaran'];
			}
			// Si no hay albarán y si factura lo crea
			if (!isset($id_albaran))
			{
				$this->obj->load->model($this->_albaranes, 'al');
				$albaran['nIdCliente'] = $data['nIdCliente'];
				isset($data['nIdDireccion'])?$albaran['nIdDireccion'] = $data['nIdDireccion']:null;
				$albaran['nIdFactura'] = $id;
				#echo 'ID F: ' . $id;
				$id_albaran = $this->obj->al->insert($albaran);
				if ($id_albaran <= 0)
				{
					$this->_set_error_message($this->obj->al->error_message());
					return FALSE;
				}
			}
			foreach ($data['lineas'] as $k => $linea)
			{
				if (!isset($linea['nIdAlbaran'])||($linea['nIdAlbaran'] == '')) $data['lineas'][$k]['nIdAlbaran'] = $id_albaran;
			}
		}
		# Comprueba si la serie está bloqueada
		/*IF(EXISTS(SELECT * 
			FROM Inserted i 
				INNER JOIN Doc_Series s (NOLOCK)
					ON i.nIdSerie = s.nIdSerie
				WHERE s.nIdSatelite <> @IdSatelite OR
					(s.dDesde > GetDate() AND s.dDesde IS NOT NULL) OR
					(s.dHasta < GetDate() AND s.dHasta IS NOT NULL)))*/

		#echo 'ID ' . $id_albaran;
		#echo '<pre>'; var_dump($data); echo '</pre>';
		return parent::onAfterInsert($id, $data);
	}
}

/* End of file M_factura.php */
/* Location: ./system/application/models/ventas/M_factura.php */