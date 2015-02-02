<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	oltp
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Funciones OLTP de Suscripciones
 *
 */
class M_Oltpsuscripcion extends MY_Model
{
	/**
	 * Base de datos OLTP
	 * @var string
	 */
	private $_prefix = '';

	/**
	 * Constructor
	 *
	 * @return M_Oltp
	 */
	function M_Oltpsuscripcion()
	{
		parent::__construct('', '');
		$this->load->library('cache');
		$this->_prefix = $this->config->item('bp.oltp.database');
	}

	/**
	 * Actualiza el corte de operaciones
	 * @return bool
	 */
	function crear_corte()
	{
		$this->out->error('Ya no se utiliza mas');
		set_time_limit(0); // Tarda bastante tiempo
		#$sql = "EXEC {$this->_prefix}spSuscripcionesComprasVentasAnticipadasCrear";
		$query = $this->db->query($sql);
		return ($query !== FALSE);
	}

	/**
	 * Busca las ventas a una fecha en las que aún no había entrado la compra
	 * @param date $fecha Fecha de corte
	 * @return array
	 */
	function get_ventas_anticipadas($fecha)
	{
		$fecha = format_mssql_date($fecha);

		$this->db->select('
			f.nIdFactura,
			f.nIdFactura id,
			f.Factura Factura,
			' . $this->db->date('f.dFecha') .' Fecha,
			f.nCantidad,
			f.fTotal,
			f.nIdSuscripcion,
			f.nIdAlbaranEntrada,
			f.cRefInterna,		
			em.cNumeroAlbaran,
			' . $this->db->date('em.dFecha') .' FechaProveedor,
			em.BaseImponible ImporteCompra,
			em.Cargos,
			em.cRefInterna RefAlbaran,
			' . $this->db->date('em.dCreacion') .' CreacionAlbaran,
			fd.nIdLibro,
			fd.cTitulo,
			c.nIdCliente,
			c.cNombre,c.cApellido, c.cEmpresa,
			pv.nIdProveedor,
			pv.cNombre cNombre2, pv.cApellido cApellido2, pv.cEmpresa cEmpresa2')
		->from("{$this->_prefix}Tmp_FacturasSuscripciones  f")
		->join('Sus_Suscripciones s ','f.nIdSuscripcion = s.nIdSuscripcion')
		->join('Cat_Fondo fd', 'fd.nIdLibro = s.nIdRevista')
		->join('Doc_Facturas f2', 'f.nIdFactura = f2.nIdFactura')
		->join('Cli_Clientes c', 'c.nIdCliente = f2.nIdCliente')
		->join("{$this->_prefix}Tmp_EntradasMercancia em", 'em.nIdAlbaran  = f.nIdAlbaranEntrada', 'left')
		->join('Doc_AlbaranesEntrada ae', 'ae.nIdAlbaran = f.nIdAlbaranEntrada', 'left')
		->join('Prv_Proveedores pv', 'pv.nIdProveedor = ae.nIdProveedor', 'left')
		->where("f.dFecha < " . $this->db->dateadd('d', 1, $fecha))
		->where("(ae.dCreacion >= {$fecha} OR ae.dCreacion IS NULL)")
		->where('ISNULL(f.bAsignada, 0) = 0')
		->order_by('f.dFecha');

		set_time_limit(0); // Tarda bastante tiempo
		if (($query = $this->db->get()) === FALSE)
		{
			return FALSE;
		}
		$r = $this->_get_results($query);
		foreach ($r as $k => $v)
		{
			$r[$k]['cCliente'] = format_name($v['cNombre'], $v['cApellido'], $v['cEmpresa']);
			$r[$k]['cProveedor'] = format_name($v['cNombre2'], $v['cApellido2'], $v['cEmpresa2']);
		}
		return $r;
	}


	/**
	 * Marca una factura como procesada
	 * @param $ids Array de Ids Factura_Ids suscripciones
	 * @return int Elementos procesados
	 */
	function del_venta($ids)
	{
		$count = 0;
		foreach($ids as $id)
		{
			list($idf, $ids) = preg_split('/\_/', $id);
			if (isset($idf) && isset($ids))
			{
				//print $idf. "->". $ids;
				//die();
				$this->db->where('nIdFactura', (int) $idf);
				$this->db->where('nIdSuscripcion', (int) $ids);
				$this->db->update("{$this->_prefix}Tmp_FacturasSuscripciones", array('bAsignada' => 1));
				$count++;
			}
		}
		return $count;
	}

	/**
	 * Marca un albaán como procesado
	 * @param $ids Array de Ids Albarán_Ids suscripciones
	 * @return int Elementos procesados
	 */
	function del_compra($ids)
	{
		$count = 0;
		foreach($ids as $id)
		{
			list($idf, $ids) = preg_split('/\_/', $id);
			if (isset($idf) && isset($ids))
			{
				//print $idf. "->". $ids;
				//die();
				$this->db->where('nIdAlbaran', (int) $idf);
				$this->db->where('nIdSuscripcion', (int) $ids);
				$this->db->update("{$this->_prefix}Tmp_EntradasMercancia", array('bAsignada' => 1));
				$count++;
			}
		}
		return $count;
	}

	/**
	 * Busca las compras a una fecha en las que las ventas se han realizado posterior a esa fecha
	 * @param date $fecha Fecha de corte
	 * @return array
	 */
	function get_compras_anticipadas($fecha)
	{

		$fecha = format_mssql_date($fecha);

		$this->db->select('f.nIdFactura,
				f.nIdFactura id,
				f.Factura Factura, 
				' . $this->db->date('f.dFecha') . ' Fecha,
				f.nCantidad,
				f.fTotal,
				f.nIdSuscripcion,
				f.nIdAlbaranEntrada,
				f.cRefInterna,			
				em.cNumeroAlbaran,
				' . $this->db->date('em.dFecha') . ' FechaProveedor,
				em.BaseImponible ImporteCompra,
				em.Cargos,
				em.cRefInterna RefAlbaran,
				' . $this->db->date('em.dCreacion') . ' CreacionAlbaran,
				fd.nIdLibro,
				fd.cTitulo,
				c.nIdCliente,
				c.cNombre, c.cApellido, c.cEmpresa,
				pv.nIdProveedor,
				pv.cNombre cNombre2, pv.cApellido cApellido2, pv.cEmpresa cEmpresa2')
		->from("{$this->_prefix}Tmp_FacturasSuscripciones  f")
		->join('Sus_Suscripciones s ','f.nIdSuscripcion = s.nIdSuscripcion')
		->join('Cat_Fondo fd', 'fd.nIdLibro = s.nIdRevista')
		->join('Doc_Facturas f2', 'f.nIdFactura = f2.nIdFactura')
		->join('Cli_Clientes c', 'c.nIdCliente = f2.nIdCliente')
		->join("{$this->_prefix}Tmp_EntradasMercancia em", 'em.nIdAlbaran  = f.nIdAlbaranEntrada', 'left')
		->join('Doc_AlbaranesEntrada ae', 'ae.nIdAlbaran = f.nIdAlbaranEntrada', 'left')
		->join('Prv_Proveedores pv', 'pv.nIdProveedor = ae.nIdProveedor', 'left')
		->where("f.dFecha >= " . $this->db->dateadd('d', 1, $fecha))
		->where("(ae.dCreacion <= {$fecha})")
		->where('ISNULL(f.bAsignada, 0) = 0')
		->order_by("f.dFecha");

		set_time_limit(0); // Pueder tarda bastante tiempo
		if (($query = $this->db->get()) === FALSE)
		{
			return FALSE;
		}
		$r = $this->_get_results($query);
		foreach ($r as $k => $v)
		{
			$r[$k]['cCliente'] = format_name($v['cNombre'], $v['cApellido'], $v['cEmpresa']);
			$r[$k]['cProveedor'] = format_name($v['cNombre2'], $v['cApellido2'], $v['cEmpresa2']);
		}
		return $r;
	}

	/**
	 * Busca las compras realizadas a una fecha en las que no existen ventas
	 * @param date $fecha Fecha de corte
	 * @return array
	 */
	function get_compras_sin_venta($fecha)
	{
		set_time_limit(0);

		$fecha = format_mssql_date($fecha);

		$this->db->flush_cache();
		$this->db->select('
				em.nIdAlbaran,
				em.nIdAlbaran id,
				em.cNumeroAlbaran AlbaranProveedor,
				' . $this->db->date('em.dFecha') .' FechaProveedor,
				em.BaseImponible ImporteCompra,
				em.Cargos,
				em.nIdSuscripcion,
				em.cRefInterna RefAlbaran,
				' . $this->db->date('em.dCreacion') .' CreacionAlbaran,
				fd.nIdLibro,
				fd.cTitulo,
				c.nIdCliente,
				c.cNombre, c.cApellido, c.cEmpresa,
				pv.nIdProveedor,
				pv.cNombre cNombre2, pv.cApellido cApellido2, pv.cEmpresa cEmpresa2')
		->from("{$this->_prefix}Tmp_EntradasMercancia em")
		->join('Sus_Suscripciones s', 'em.nIdSuscripcion = s.nIdSuscripcion')
		->join('Cat_Fondo fd', 'fd.nIdLibro = s.nIdRevista')
		->join('Cli_Clientes c', 'c.nIdCliente = s.nIdCliente')
		->join('Doc_AlbaranesEntrada ae', 'ae.nIdAlbaran = em.nIdAlbaran', 'left')
		->join('Prv_Proveedores pv', 'pv.nIdProveedor = ae.nIdProveedor', 'left')
		->where("ae.dCreacion <= {$fecha}")
		->where('ISNULL(em.bAsignada, 0) = 0')
		->where('em.nIdSuscripcion NOT IN (
					SELECT nIdSuscripcion
					FROM Sus_Suscripciones
					WHERE nIdCliente IN (
						SELECT nIdCliente
						FROM Ext_EOISDepartamentos
					)
				)')
		->order_by('ae.dCreacion');

		$query = $this->db->get();
		$data = $this->_get_results($query);
		foreach ($data as $k => $v)
		{
			$data[$k]['cCliente'] = format_name($v['cNombre'], $v['cApellido'], $v['cEmpresa']);
			$data[$k]['cProveedor'] = format_name($v['cNombre2'], $v['cApellido2'], $v['cEmpresa2']);
		}
		return $data;
	}

	/**
	 * Devuelve las facturas de suscripciones entre fechas 
	 * @param date $fecha1 Desde
	 * @param date $fecha2 Hasta
	 * @return array
	 */
	function facturas($desde = null, $hasta = null)
	{
		$desde = format_mssql_date($desde);
		$hasta = format_mssql_date($hasta);

		$this->db->flush_cache();
		$this->db->select('c.nIdFactura, c.nNumero, d.nNumero cSerie')
		->select($this->_date_field('c.dFecha', 'dFecha'))
		->select('b.nIdSuscripcion, e.cEmpresa, e.cNombre, e.cApellido, e.nIdCliente')
		->select('f.fPrecio, f.fDescuento, f.fIVA, f.nCantidad')
		->from('Doc_AlbaranesSalida a')
		->join('Sus_SuscripcionesAlbaranes b', 'a.nIdAlbaran = b.nIdAlbaran')
		->join('Doc_Facturas c', 'c.nIdFactura = a.nIdFactura')
		->join('Doc_Series d', 'c.nIdSerie = d.nIdSerie')
		->join('Cli_Clientes e', 'e.nIdCliente = a.nIdCliente')
		->join('Doc_LineasAlbaranesSalida f', 'f.nidAlbaran = a.nIdAlbaran')
		->where("c.dFecha >= {$desde}")
		->where("c.dFecha < " . $this->db->dateadd('d', 1, $hasta));
		
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}
}

/* End of file M_Oltpsuscripcion.php */
/* Location: ./system/application/models/M_Oltpsuscripcion.php */
