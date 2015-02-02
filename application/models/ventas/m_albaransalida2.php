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

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'ventas' . DIRECTORY_SEPARATOR . 'm_albaransalida.php');
/**
 * Albaranes de salida
 *
 */
class M_albaransalida2 extends M_albaransalida
{
	/**
	 * Constructor
	 * @return M_albaransalida2
	 */
	function __construct()
	{
		parent::__construct('Doc_AlbaranesSalida2', 'ventas/m_albaransalidalinea2');

	}

	/**
	 * Cierra un albarán de salida
	 * @param int $id Id del albarán de salida
	 */
	function cerrar($id)
	{
		$albaran = $this->load($id);
		if ($albaran['nIdEstado'] != DEFAULT_ALBARAN_SALIDA_STATUS)
		{
			#$this->_set_error_message($this->lang->line('error-albaransalida-cerrado'));
			return TRUE;
		}

		// Leemos las líneas
		$this->db->flush_cache();
		$this->db->select('la.nIdLibro, la.nIdSeccion, la.nCantidad, la.nIdLineaAlbaran, la.fCoste, la.cRefCliente')
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
		foreach($data as $reg)
		{
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
					if (!$obj->m_pedidoclientelineapendiente->asignar_albaran($id, $albaran['nIdCliente'], $reg['nIdSeccion'], $reg['nIdLibro'], $reg['nCantidad']))
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

		// Actualiza el estado del albarán
		if (!$this->update($id, array('nIdEstado' => ALBARAN_SALIDA_STATUS_CERRADO)))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		//$this->db->trans_rollback();
		$this->db->trans_commit();
		return TRUE;
	}
}

/* End of file M_albaransalida.php */
/* Location: ./system/application/models/ventas/M_albaransalida.php */
