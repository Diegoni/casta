<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Estados
 *
 */
class PedidoConcursoLinea extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return PedidoConcursoLinea
	 */
	function __construct()
	{
		parent::__construct('concursos.pedidoconcursolinea', 'concursos/M_pedidoconcursolinea', TRUE, 'concursos/lineaspedido.js', 'Líneas de pedido concursos');
	}

	/**
	 * Asignación de un artículo a la línea de pedido (interno)
	 * @param  int $bueno Id del articulo bueno
	 * @param  int $malo  Id de la línea de pedido de concurso
	 * @param int $idl Id de la línea de concurso
	 * @return mixed, TRUE: Ok, string: Error
	 */
	private function _asignar_uno($idln, $bueno, $idl)
	{
		$this->load->model('catalogo/m_articulo');
		$this->load->model('compras/m_pedidoproveedorlinea');
		$this->load->model('catalogo/m_articuloseccion');

		# El pedido de proveedor debe ser de este título
		$old = $this->m_pedidoproveedorlinea->load($idln);

		$this->m_pedidoproveedorlinea->triggers_disable();
		if (!$this->m_pedidoproveedorlinea->update($idln, array('nIdLibro' => $bueno)))
		{
			$this->m_pedidoproveedorlinea->triggers_enable();
			return $this->m_pedidoproveedorlinea->error_message();
		}
		$this->m_pedidoproveedorlinea->triggers_enable();

		# Actualiza los pendientes de recibir
		$sec = $this->m_articuloseccion->get(null, null, null, null, "nIdSeccion={$old['nIdSeccion']} AND nIdLibro={$old['nIdLibro']}");
		$sec = $sec[0];
		if (!$this->m_articuloseccion->update($sec['nIdSeccionLibro'], array(
			'nStockRecibir' => $sec['nStockRecibir'] - $old['nCantidad']
			)))
		{
			return $this->m_articuloseccion->error_message();
		}
		$sec = $this->m_articuloseccion->get(null, null, null, null, "nIdSeccion={$old['nIdSeccion']} AND nIdLibro={$bueno}");
		if (count($sec) == 0)
		{
			if ($this->m_articuloseccion->insert(array(
					'nStockRecibir' 	=> $old['nCantidad'],
					'nIdLibro' 			=> $bueno,
					'nIdSeccion' 		=> $old['nIdSeccion']
				)) < 0 )
			{
				return $this->m_articuloseccion->error_message();
			}
		}
		else
		{
			$sec = $sec[0];
			$upd = array('nStockRecibir' => $sec['nStockRecibir'] + $old['nCantidad']);
			if (!$this->m_articuloseccion->update($sec['nIdSeccionLibro'], $upd))
			{
				return $this->m_articuloseccion->error_message();
			}
		}

		# Cambia la línea de pedido del concurso
		if (!$this->reg->update($idl, array('nIdLibro' => $bueno)))
		{
			return $this->reg->error_message();
		}
		return TRUE;
	}

	/**
	 * Asignación de un artículo a la línea de pedido (interno)
	 * @param  int $bueno Id del articulo bueno
	 * @param  int $malo  Id de la línea de pedido de concurso
	 * @return mixed, TRUE: Ok, string: Error
	 */
	private function _asignar($bueno, $malo)
	{
		# Cambia todos los casos que sean iguales
		$this->db->trans_begin();
		$casos = $this->reg->get(null, null, null, null, "nIdLibro={$malo}");
		foreach ($casos as $l) 
		{	
			if (isset($l['nIdLineaPedidoProveedor']))
			{
				$res = $this->_asignar_uno($l['nIdLineaPedidoProveedor'], $bueno, $l['nIdLineaPedidoConcurso']);

				if ($res !== TRUE)
				{
					$this->db->trans_rollback();
					return $res;
				}
			}
		}
		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Asignación de un artículo a la línea de pedido
	 * @param  int $bueno Id del articulo bueno
	 * @param  int $malo  Id de la línea de pedido de concurso
	 * @return MSG
	 */
	function asignar($bueno = null, $malo = null)
	{
		$this->userauth->roleCheck($this->auth . '.asignar');

		$bueno = isset($bueno) ? $bueno : $this->input->get_post('bueno');
		$malo = isset($malo) ? $malo : $this->input->get_post('malo');

		if (is_numeric($bueno) && is_numeric($malo))
		{
			# Lee la línea del pedido
			$linea = $this->reg->load($malo);
			$res = $this->_asignar($bueno, $linea['nIdLibro']);
			if ($res === TRUE)
				$this->out->success($this->lang->line('pedidoconcurso-articulo-asignado-ok'));
			$this->out->error($res);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));

	}

	/**
	 * Unifica un artículo de la línea de pedido con otro artículo
	 * @param  int $bueno Id del articulo bueno
	 * @param  int $malo  Id de la línea de pedido de concurso
	 * @return MSG
	 */
	function unificar($bueno = null, $malo = null)
	{
		$this->userauth->roleCheck($this->auth . '.asignar');

		$bueno = isset($bueno) ? $bueno : $this->input->get_post('bueno');
		$malo = isset($malo) ? $malo : $this->input->get_post('malo');

		if (is_numeric($bueno) && is_numeric($malo))
		{
			# Lee la línea del pedido
			$l = $this->reg->load($malo);
			$this->db->trans_begin();

			$res = $this->_asignar($bueno, $l['nIdLibro']);
			if ($res !== TRUE)
				$this->out->error($res);

			$this->load->model('catalogo/m_aunificar');

			if (!$this->m_aunificar->insert(array('nIdBueno' => $bueno, 'nIdMalo' => $l['nIdLibro'])))
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_aunificar->error_message());
			}
			$this->db->trans_commit();
			$this->out->success($this->lang->line('pedidoconcurso-articulo-unificado-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambia el aŕtículo asignado a un pedido del concurso
	 * @param  int $id    Id de la línea a duplicar
	 * @return MSG
	 */
	function duplicar($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (!empty($id))
		{
			$this->load->model('concursos/m_estadolineaconcurso');
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$this->db->trans_begin();
			$count = 0;
			foreach ($ids as $value) 
			{
				if (is_numeric($value)) 
				{
					$l = $this->reg->load($value);
					unset($l['nIdLineaPedidoProveedor']);
					unset($l['nIdLineaAlbaranEntrada']);
					unset($l['nIdLineaPedidoCliente']);
					unset($l['nIdLineaDevolucion']);
					unset($l['nIdLineaAlbaranSalida']);
					unset($l['nIdCambioLibro']);
					unset($l['nCaja']);
					unset($l['cProveedor']);
					unset($l['cConcurso']);
					unset($l['bNuevo']);
					unset($l['bAnticipado']);
					unset($l['nAlternativas']);
					unset($l['bValidado']);
					unset($l['nIdLineaPedidoConcurso']);
					unset($l['dCreacion']);
					unset($l['dAct']);
					unset($l['cCUser']);
					unset($l['cAUSer']);
					$l['nIdEstado'] = CONCURSOS_ESTADO_LINEA_EN_PROCESO;
					if ($this->reg->insert($l) < 0)
					{
						$this->db->trans_rollback();
						$this->out->error($this->reg->error_message());
					}
					++$count;
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('pedidoconcurso-articulo-duplicado-ok'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambia el aŕtículo asignado a un pedido del concurso
	 * @param  int $id    Id de la línea a duplicar
	 * @return MSG
	 */
	function ver($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (!empty($id))
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$count = 0;
			$data = array();
			foreach ($ids as $value) 
			{
				if (is_numeric($value)) 
				{
					$l = $this->reg->load($value);
					$data[$value] = $l;
				}
			}
			$message = $this->load->view('concursos/original', array('libros' => $data), TRUE);
			#echo $message; die();
			$this->out->html_file($message, $this->lang->line('Original'), 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambia el aŕtículo asignado a un pedido del concurso
	 * @param  int $id    Id de la línea a duplicar
	 * @return MSG
	 */
	function desvincular($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (!empty($id))
		{
			$this->load->model('concursos/m_estadolineaconcurso');
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$this->db->trans_begin();
			$count = 0;
			foreach ($ids as $value) 
			{
				if (is_numeric($value)) 
				{
					$l = $this->reg->load($value);
					if (in_array($l['nIdEstado'], array(CONCURSOS_ESTADO_LINEA_RECIBIDO_PROVEEDOR)))
					{
						if (!$this->reg->update($l['nIdLineaPedidoConcurso'], array(
							'nIdLineaPedidoProveedor'	=> null,
							'nIdLineaAlbaranEntrada'	=> null,
							'nIdEstado'					=> CONCURSOS_ESTADO_LINEA_EN_PROCESO
							)))
						{
							$this->db->trans_rollback();
							$this->out->error($this->reg->error_message());
						}
						++$count;
					}
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('pedidoconcurso-articulo-duplicado-ok'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambia el artículo de una línea de concurso
	 * @param  int $id    Id del la línea de concurso
	 * @param  int $nuevo Id del nuevo artículo
	 * @return MSG
	 */
	function cambiar($id = null, $nuevo = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$nuevo = isset($nuevo) ? $nuevo : $this->input->get_post('nuevo');
		if (!empty($id) && !empty($nuevo))
		{
			$this->load->model('concursos/m_estadolineaconcurso');
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$this->db->trans_begin();
			foreach ($ids as $value) 
			{
				if (is_numeric($value)) 
				{
					$l = $this->reg->load($value);
					$st = array(
						CONCURSOS_ESTADO_LINEA_EN_PROCESO,
						CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR,
						CONCURSOS_ESTADO_LINEA_CAMBIADO_POR_OTRO
						);
					if (!in_array($l['nIdEstado'], $st))
					{
						$this->db->trans_rollback();
						$this->out->error(sprintf($this->lang->line('linea-pedido-no-proceso'), $l['cTitulo']));
					}
					if ($l['nIdEstado'] == CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR)
					{
						$res = $this->_asignar_uno($l['nIdLineaPedidoProveedor'], $nuevo, $l['nIdLineaPedidoConcurso']);
						if ($res !== TRUE)
						{
							$this->db->trans_rollback();
							$this->out->error($res);
						}
					}
					if (!$this->reg->update($l['nIdLineaPedidoConcurso'], array('nIdLibro' => $nuevo)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->reg->error_message());
					}
				}
			}
			$this->db->trans_commit();
			$this->out->success($this->lang->line('pedidoconcurso-articulo-cambiado-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambia el artículo de una línea de concurso
	 * @param  int $id    Id del la línea de concurso
	 * @param  int $nuevo Id del nuevo artículo
	 * @return MSG
	 */
	function alternativa($id = null, $nuevo = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$nuevo = isset($nuevo) ? $nuevo : $this->input->get_post('nuevo');

		if (!empty($id) && !empty($nuevo))
		{
			$this->load->model('concursos/m_estadolineaconcurso');
			$this->load->model('catalogo/m_articulo');

			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$libro = $this->m_articulo->load($nuevo);
			$this->db->trans_begin();
			foreach ($ids as $value) 
			{
				if (is_numeric($value)) 
				{					
					$l = $this->reg->load($value);

					# Comprueba si ya existe
					$where = "nIdLibro={$nuevo} AND nIdBiblioteca={$l['nIdBiblioteca']} AND nIdEstado IN (" . 
						implode(',', array(CONCURSOS_ESTADO_LINEA_RECIBIDO_PROVEEDOR, 
								CONCURSOS_ESTADO_LINEA_CATALOGADO,
								CONCURSOS_ESTADO_LINEA_EN_PROCESO, 
								CONCURSOS_ESTADO_LINEA_EN_ALBARAN,					
								CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR, 
								CONCURSOS_ESTADO_LINEA_A_PEDIR,
								CONCURSOS_ESTADO_LINEA_ALTERNATIVA,
								CONCURSOS_ESTADO_LINEA_DESCARTADO
							)) . ')';
					$data = $this->reg->get(null, null, null, null, $where);
					if (count($data) > 0)
					{
						$this->out->error($this->lang->line('concurso-linea-libro-existente'));
					}

					$new = array(
						'nIdBiblioteca'		=> $l['nIdBiblioteca'],
						'nIdSala'			=> $l['nIdSala'],
						'nIdEstado' 		=> CONCURSOS_ESTADO_LINEA_ALTERNATIVA,
						'cISBN' 			=> $l['cISBN'],
						'cEAN' 				=> $l['cEAN'],
						'cAutores' 			=> $l['cAutores'],
						'cTitulo' 			=> $l['cTitulo'],
						'nIdLibro' 			=> $nuevo,
						'cRefCli' 			=> $l['cRefCli'],
						'cEdicion' 			=> $l['cEdicion'],
						'cEditorial1a' 		=> $l['cEditorial1a'],
						'fPrecio' 			=> $libro['fPrecio'],
						'nIdAlternativa' 	=> $value,
						'bNuevo' 			=> TRUE,
						'cSignatura' 		=> $l['cSignatura'],
						'tTitolVolum' 		=> $l['tTitolVolum'],
						'tTitolUniforme' 	=> $l['tTitolUniforme'],
						'tMateries' 		=> $l['tMateries'],
						'cBibid' 			=> $l['cBibid'],
						'cEditorial1a' 		=> $l['cEditorial1a'],
						'cEditorial2a' 		=> $l['cEditorial2a'],
						'cElxurro' 			=> $l['cElxurro'],
						'cCDU' 				=> $l['cCDU'],
						'cCDU2' 			=> $l['cCDU2'],
					);
					/*
					$st = array(
						CONCURSOS_ESTADO_LINEA_EN_PROCESO,
						CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR,
						CONCURSOS_ESTADO_LINEA_CAMBIADO_POR_OTRO
						);
					if (!in_array($l['nIdEstado'], $st))
					{
						$this->db->trans_rollback();
						$this->out->error(sprintf($this->lang->line('linea-pedido-no-proceso'), $l['cTitulo']));
					}
					if ($l['nIdEstado'] == CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR)
					{
						$res = $this->_asignar_uno($l['nIdLineaPedidoProveedor'], $nuevo, $l['nIdLineaPedidoConcurso']);
						if ($res !== TRUE)
						{
							$this->db->trans_rollback();
							$this->out->error($res);
						}
					}*/
					if (($id_n=$this->reg->insert($new)) < 0)
					{
						$this->db->trans_rollback();
						$this->out->error($this->reg->error_message());
					}

					if (!$this->reg->update($l['nIdLineaPedidoConcurso'], array('nAlternativas' => (int)$l['nAlternativas'] + 1)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->reg->error_message());
					}
				}
			}
			$this->db->trans_commit();
			$this->out->success($this->lang->line('pedidoconcurso-articulo-alternativa-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Elimina el cambiado
	 * @param  array $reg Registro
	 * @return mixed, bool: TRUE: Ok, string: error
	 */
	private function _delete_cambio($reg)
	{
		$otro = $this->reg->load($reg['nIdCambioLibro']);
		if ($otro['nIdEstado'] == CONCURSOS_ESTADO_LINEA_CAMBIADO_POR_OTRO)
		{
			$res = $this->_delete_cambio($otro);
			if ($res !== TRUE)
				return $res;
		}
		elseif ($otro['nIdEstado'] !== CONCURSOS_ESTADO_LINEA_EN_PROCESO)
		{
			return $this->lang->line('linea-pedido-no-cambio-otro');
		}
		
		#echo 'Actualizando ' . $reg['nIdLineaPedidoConcurso'];	
		if (!$this->reg->update($reg['nIdLineaPedidoConcurso'], array(
			'nIdCambioLibro' 	=> NULL,
			'nIdEstado' 		=> CONCURSOS_ESTADO_LINEA_EN_PROCESO
			)))
			return $this->reg->error_message();

		#echo 'Eliminando ' . $reg['nIdCambioLibro'];	
		if (!$this->reg->delete($reg['nIdCambioLibro']))
			return $this->reg->error_message();

		return TRUE;
	}

	/**
	 * Mueve un artículo de una sección al concurso, y vincula la compra
	 * @param  int $id      Id de la línea de concurso
	 * @param  int $idl     Id del artículo
	 * @param  int $origen  Id de la sección de origen
	 * @param  int $destino Id de la sección de destino
	 * @param  int $concurso Id del concurso
	 * @return  MSG
	 */
	function mover($id = null, $idl = null, $origen = null, $destino = null, $concurso = null)
	{
		$id 		= isset($id) ? $id : $this->input->get_post('id');
		$idl 		= isset($idl)?$idl:$this->input->get_post('idl');
		$origen 	= isset($origen)?$origen:$this->input->get_post('origen');
		$destino 	= isset($destino)?$destino:$this->input->get_post('destino');
		$concurso 	= isset($concurso)?$concurso:$this->input->get_post('concurso');
		if (!empty($id) && is_numeric($idl) && is_numeric($origen) && is_numeric($destino))
		{
			$this->load->model('concursos/m_estadolineaconcurso');
			if ($id == -2)
			{
				if (!is_numeric($concurso))
					$this->out->error($this->lang->line('mensaje_faltan_datos'));

				# Busca un pedido EN PROCESO del artículo
				$st = array(CONCURSOS_ESTADO_LINEA_EN_PROCESO, 
					CONCURSOS_ESTADO_LINEA_AGOTADOS,
					CONCURSOS_ESTADO_LINEA_DESCATALOGADO,
					CONCURSOS_ESTADO_LINEA_NO_DISPONIBLE,
					CONCURSOS_ESTADO_LINEA_CAMBIADO_POR_OTRO,
					CONCURSOS_ESTADO_LINEA_EN_REIMPRESION);
				$st = '(' . implode(',', $st) . ')';
				$data = $this->reg->get(0, 1, null, null, "nIdLibro={$idl} AND nIdEstado IN {$st} AND Ext_Bibliotecas.nIdConcurso={$concurso}");
				if (count($data) != 1)
				{
					$this->out->error($this->lang->line('linea-pedido-no-enproceso-mover'));
				}
				#var_dump($data); die();
				#var_dump($data[0]['nIdEstado'], CONCURSOS_ESTADO_LINEA_CAMBIADO_POR_OTRO); die();
				if ($data[0]['nIdEstado'] == CONCURSOS_ESTADO_LINEA_CAMBIADO_POR_OTRO)
				{
					#echo 'Eliminando'; die();
					$this->db->trans_begin();
					$res = $this->_delete_cambio($data[0]);
					if ($res !== TRUE)
					{
						$this->db->trans_rollback();
						$this->out->error($res);		
					}
					#die();
					$this->db->trans_commit();					
				}
				$id = array($data[0]['nIdLineaPedidoConcurso']);
			}
			#var_dump($id); die();
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			#var_dump($ids); die();
			$ids = array_unique($ids);
			$this->load->model('catalogo/m_movimiento');
			$this->db->trans_begin();
			$count = 0;
			$biblios = array();
			foreach ($ids as $value) 
			{
				#var_dump($value); die();
				if (is_numeric($value))
				{
					# Comprueba el estado
					$l = $this->reg->load($value);
					#var_dump($l);
					if (!in_array($l['nIdEstado'], array(CONCURSOS_ESTADO_LINEA_EN_PROCESO, 
						CONCURSOS_ESTADO_LINEA_AGOTADOS,
						CONCURSOS_ESTADO_LINEA_DESCATALOGADO,
						CONCURSOS_ESTADO_LINEA_NO_DISPONIBLE,
						CONCURSOS_ESTADO_LINEA_EN_REIMPRESION)))
					{
						$this->db->trans_rollback();
						$this->out->error(sprintf($this->lang->line('linea-pedido-no-proceso'), $l['cTitulo']));
					}
					# Mueve el artículo
					if ($origen != $destino)
					{
						if ($this->m_movimiento->mover($idl, $origen, $destino, 1) < 0)
						{
							$this->db->trans_rollback();
							$this->out->error($this->m_movimiento->error_message());
						}
					}
					# Busca el albarán de entrada para asignarlo a
					$idln = $this->reg->get_last_albaran($idl);
					if (!isset($idln))
					{
						$this->db->trans_rollback();
						$this->out->error(sprintf($this->lang->line('no-albaran-entrada'), $idl));
					}
					# Cambia la línea de concurso
					if (!$this->reg->update($l['nIdLineaPedidoConcurso'], array(
						'nIdLibro' 				 => $idl,
						'nIdLineaAlbaranEntrada' => $idln,
						'nIdEstado'				 => CONCURSOS_ESTADO_LINEA_RECIBIDO_PROVEEDOR
						)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->reg->error_message());
					}
					$biblios[] = $l['cBiblioteca'] . ' - ' .$l['cSala'];
					++$count;
				}
			}
			$this->db->trans_commit();
			$res = array(
				'success' => TRUE,
				'message' => sprintf($this->lang->line('pedidoconcurso-articulo-movido-ok'), $count),
				'biblioteca'	=> implode('<br/>', $biblios)
				);
			$this->out->send($res);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambia una línea de concurso al estado agotado
	 * @param  int $id Id de la línea
	 * @return MSG
	 */
	function agotado($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (!empty($id))
		{
			$this->load->model('catalogo/m_estadolibro');
			$this->load->model('concursos/m_estadolineaconcurso');
			$this->db->trans_begin();
			$res = $this->_cambioestado($id, CONCURSOS_ESTADO_LINEA_AGOTADOS, ESTADO_ARTICULO_AGOTADO_EN_PROVEEDOR);
			if (!is_numeric($res))
			{
				$this->db->trans_rollback();
				$this->out->error($res);
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('pedidoconcurso-articulo-agotado-ok'), $res));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambia una línea de concurso al estado DESCATALOGADO
	 * @param  int $id Id de la línea
	 * @return MSG
	 */
	function descatalogado($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (!empty($id))
		{
			$this->load->model('catalogo/m_estadolibro');
			$this->load->model('concursos/m_estadolineaconcurso');
			$this->db->trans_begin();
			$res = $this->_cambioestado($id, CONCURSOS_ESTADO_LINEA_DESCATALOGADO, ESTADO_ARTICULO_DESCATALOGADO);
			if (!is_numeric($res))
			{
				$this->db->trans_rollback();
				$this->out->error($res);
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('pedidoconcurso-articulo-descatalogado-ok'), $res));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambia una línea de concurso al estado DESCATALOGADO
	 * @param  int $id Id de la línea
	 * @return MSG
	 */
	function enproceso($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (!empty($id))
		{
			$this->load->model('catalogo/m_estadolibro');
			$this->load->model('concursos/m_estadolineaconcurso');
			$this->db->trans_begin();
			$res = $this->_cambioestado($id, CONCURSOS_ESTADO_LINEA_EN_PROCESO);
			if (!is_numeric($res))
			{
				$this->db->trans_rollback();
				$this->out->error($res);
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('pedidoconcurso-articulo-en-proceso-ok'), $res));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambia una línea de concurso al estado REIMPRESION
	 * @param  int $id Id de la línea
	 * @return MSG
	 */
	function reimpresion($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (!empty($id))
		{
			$this->load->model('catalogo/m_estadolibro');
			$this->load->model('concursos/m_estadolineaconcurso');
			$this->db->trans_begin();
			$res = $this->_cambioestado($id, CONCURSOS_ESTADO_LINEA_EN_REIMPRESION);
			if (!is_numeric($res))
			{
				$this->db->trans_rollback();
				$this->out->error($res);
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('pedidoconcurso-articulo-reimpresion-ok'), $res));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambia una línea de concurso al estado DESCARTAR
	 * @param  int $id Id de la línea
	 * @return MSG
	 */
	function descartar($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (!empty($id))
		{
			$this->load->model('catalogo/m_estadolibro');
			$this->load->model('concursos/m_estadolineaconcurso');
			$this->db->trans_begin();
			$res = $this->_cambioestado($id, CONCURSOS_ESTADO_LINEA_DESCARTADO);
			if (!is_numeric($res))
			{
				$this->db->trans_rollback();
				$this->out->error($res);
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('pedidoconcurso-articulo-descartado-ok'), $res));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Se acepta la alternativa
	 * @param  int $id Id de la línea
	 * @return MSG
	 */
	function aceptar_alternativa($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (!empty($id))
		{
			$this->load->model('concursos/m_estadolineaconcurso');
			if (!$this->reg->update($id, array('nIdEstado' => CONCURSOS_ESTADO_LINEA_EN_PROCESO)))
			{
				$this->out->error($this->reg->error_message());
			}
			$l = $this->reg->load($id);
			if (isset($l['nIdAlternativa']))
			{
				$upd = array(
					'nIdEstado' 		=> CONCURSOS_ESTADO_LINEA_CAMBIADO_POR_OTRO,
					'nIdCambioLibro'	=> $id
					);
				if (!$this->reg->update($l['nIdAlternativa'], $upd))
				{
					$this->out->error($this->reg->error_message());
				}				
			}
			$this->load->library('Emails');
			$message = $this->load->view('concursos/alternativa_aceptada', $l, TRUE);
			$res = $this->emails->send(
					$this->lang->line('Concurso: Alternativa aceptada'),
					$message,
					$this->config->item('bp.concursos.emails.alternativas'));
			$this->out->success($this->lang->line('pedidoconcurso-articulo-aceptado-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Se descarta la alternativa
	 * @param  int $id Id de la línea
	 * @return MSG
	 */
	function descartar_alternativa($id = null, $motivo = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$motivo = isset($motivo) ? $motivo : $this->input->get_post('motivo');
		if (!empty($id))
		{
			$this->load->model('concursos/m_estadolineaconcurso');
			if (!$this->reg->update($id, array('nIdEstado' => CONCURSOS_ESTADO_LINEA_DESCARTADO)))
			{
				$this->out->error($this->reg->error_message());
			}
			$this->load->library('Emails');
			$l = $this->reg->load($id);
			$l['motivo'] = $motivo;
			$message = $this->load->view('concursos/alternativa_descartada', $l, TRUE);
			$res = $this->emails->send(
					$this->lang->line('Concurso: Alternativa descartada'),
					$message,
					$this->config->item('bp.concursos.emails.alternativas'));
			$this->out->success(sprintf($this->lang->line('pedidoconcurso-articulo-descartado-ok'), 1));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve las sala con alternativas
	 * @param  int $concurso ID del concurso
	 * @return DATA
	 */
	function alternativas_salas($concurso = null)
	{
		$concurso = isset($concurso) ? $concurso : $this->input->get_post('concurso');
		if (is_numeric($concurso))
		{
			$data = $this->reg->alternativas_salas($concurso);
			#var_dump($data);
			foreach ($data as $k => $v)
			{
				$data[$k]['cDescripcion'] = "{$data[$k]['cDescripcion']} ({$data[$k]['nContador']})";
			}
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve las salas con alternativas descartadas y/o acertadas
	 * @param  int $concurso ID del concurso
	 * @param  int  $tipo, 0 = todo, 1 = decartadas, 2 = aceptadas, 3 = sin filtro
	 * @param  string $estado Id de los estados separados por |
	 * @return DATA
	 */
	function estadisticas_salas($concurso = null, $tipo = null, $estado = null)
	{
		$concurso = isset($concurso) ? $concurso : $this->input->get_post('concurso');
		$estado = isset($estado) ? $estado : $this->input->get_post('estado');
		$tipo = isset($tipo) ? $tipo : $this->input->get_post('tipo');
		if (is_numeric($concurso))
		{
			if (!is_numeric($tipo) || ($tipo > 3)) $tipo = 0;
			if (isset($estado)) $estado = explode('|', $estado);
			$data = $this->reg->estadisticas_salas($concurso, $tipo, $estado);
			#var_dump($data);
			foreach ($data as $k => $v)
			{
				$data[$k]['cDescripcion'] = "{$data[$k]['cDescripcion']} ({$data[$k]['nContador']})";
			}
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve las bibliotecas con alternativas descartadas y/o acertadas
	 * @param  int $concurso ID del concurso
	 * @param  int  $tipo, 0 = todo, 1 = decartadas, 2 = aceptadas, 3 = sin filtro
	 * @param  string $estado Id de los estados separados por |
	 * @return DATA
	 */
	function estadisticas_bibliotecas($concurso = null, $tipo = null, $estado = null)
	{
		$concurso = isset($concurso) ? $concurso : $this->input->get_post('concurso');
		$estado = isset($estado) ? $estado : $this->input->get_post('estado');
		$tipo = isset($tipo) ? $tipo : $this->input->get_post('tipo');
		if (is_numeric($concurso))
		{
			if (!is_numeric($tipo) || ($tipo > 3)) $tipo = 0;
			if (isset($estado)) $estado = explode('|', $estado);
			$data = $this->reg->estadisticas_bibliotecas($concurso, $tipo, $estado);
			#var_dump($data);
			foreach ($data as $k => $v)
			{
				$data[$k]['cDescripcion'] = "{$data[$k]['cDescripcion']} ({$data[$k]['nContador']})";
			}
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra el histórico de cambios de estado
	 * @param  int $id Id de la línea
	 * @return MSG
	 */
	function estado($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		if (!empty($id))
		{
			#$this->load->model('catalogo/m_estadolibro');
			#$this->load->model('concursos/m_estadolineaconcurso');
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$count = 0;
			foreach ($ids as $k => $value) 
			{
				if (is_numeric($value))
				{
					$ids[$k] = array(
						'libro' 	=> $this->reg->load($value),
						'estados'	=> $this->reg->estados($value)
						);
				}
				else
					unset($ids[$k]);
			}
			$message = $this->load->view('concursos/estados', array('estados' => $ids), TRUE);
			#echo $message; die();
			$this->out->html_file($message, $this->lang->line('Cambios estado'), 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambia el estado una línea de concurso 
	 * @param  int $id    Id del la línea de concurso
	 * @param  int $estadi  Id del nuevo estado
	 * @return MSG
	 */
	private function _cambiar_uno($l, $estado)
	{
		$this->load->model('compras/m_pedidoproveedorlinea');
		if (in_array($l['nIdEstado'], array(CONCURSOS_ESTADO_LINEA_EN_PROCESO, 
			CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR, 
			CONCURSOS_ESTADO_LINEA_A_PEDIR,
			CONCURSOS_ESTADO_LINEA_AGOTADOS,
			CONCURSOS_ESTADO_LINEA_DESCATALOGADO,
			CONCURSOS_ESTADO_LINEA_EN_REIMPRESION
			)))
		{
			$upd = array('nIdEstado' => $estado);

			# Cancela la línea de pedido
			if ($l['nIdEstado'] == CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR && isset($l['nIdLineaPedidoProveedor']))
			{
				if (!$this->m_pedidoproveedorlinea->cancelar($l['nIdLineaPedidoProveedor']))
				{
					return $this->m_pedidoproveedorlinea->error_message();
				}
				$upd['nIdLineaPedidoProveedor'] = null;
			}
			# Elimina la línea de pedido
			if ($l['nIdEstado'] == CONCURSOS_ESTADO_LINEA_A_PEDIR && isset($l['nIdLineaPedidoProveedor']))
			{
				if (!$this->m_pedidoproveedorlinea->delete($l['nIdLineaPedidoProveedor']))
				{
					return $this->m_pedidoproveedorlinea->error_message();
				}
				$upd['nIdLineaPedidoProveedor'] = null;
			}

			if (!$this->reg->update($l['nIdLineaPedidoConcurso'], $upd))
			{
				return $this->reg->error_message();
			}
		}
		return TRUE;
	}

	/**
	 * Cambia el estado de la línea de pedido
	 * @param  int $id   Id de la línea
	 * @param  int $estado Id del estado de la línea de pedido
	 * @param  int $libro  Id del estado del aertículo
	 * @return mixed, int: número de artículos tocados, string: error
	 */
	private function _cambioestado($id, $estado, $libro = null)
	{
		$this->load->model('concursos/m_estadolineaconcurso');
		$this->load->model('catalogo/m_articulo');
		$this->load->model('catalogo/m_estadolibro');

		$ids = is_string($id)?preg_split('/\;/', $id):$id;
		$ids = array_unique($ids);
		$count = 0;
		foreach ($ids as $value) 
		{
			if (is_numeric($value)) 
			{
				$l = $this->reg->load($value);
				if (isset($l['nIdLibro']))
				{
					$regs = $this->reg->get(null, null, null, null, 'nIdLibro='. $l['nIdLibro']);
					foreach($regs as $l2)
					{
						if (!($res = $this->_cambiar_uno($l2, $estado)))
						{
							return $res;
						}
					}
					if (isset($libro))
					{
						if (!$this->m_articulo->update($l['nIdLibro'], array('nIdEstado' => $libro)))
						{
							return $this->m_articulo->error_message();
						}
					}
					++$count;
				}
				else
				{
					if (!($res = $this->_cambiar_uno($l, $estado)))
					{
						return $res;
					}
					++$count;
				}
			}
		}
		return $count;
	}

	/**
	 * Crea una nueva línea de pedido
	 * @param  int $idl     Id del artículo
	 * @param  int $origen  Id de la sección de origen
	 * @param  int $destino Id de la sección de destino
	 * @param  int $biblioteca Id de la biblioteca
	 * @param  int $sala       Id de la sala
	 * @return  MSG
	 */
	function crear($idl = null, $oriden = null, $destino = null, $biblioteca = null, $sala = null)
	{
		$idl 		= isset($idl)?$idl:$this->input->get_post('idl');
		$origen 	= isset($origen)?$origen:$this->input->get_post('origen');
		$destino 	= isset($destino)?$destino:$this->input->get_post('destino');
		$biblioteca = isset($biblioteca)?$biblioteca:$this->input->get_post('biblioteca');
		$sala 		= isset($sala)?$sala:$this->input->get_post('sala');
		if (is_numeric($sala)  && is_numeric($biblioteca) && is_numeric($idl) && is_numeric($destino))
		{
			$this->load->model('catalogo/m_articulo');
			$this->load->model('concursos/m_estadolineaconcurso');
			# Comprueba si ya existe
			$where = "nIdLibro={$idl} AND nIdBiblioteca={$biblioteca} AND nIdEstado IN (" . 
				implode(',', array(CONCURSOS_ESTADO_LINEA_RECIBIDO_PROVEEDOR, 
						CONCURSOS_ESTADO_LINEA_CATALOGADO,
						CONCURSOS_ESTADO_LINEA_EN_PROCESO, 
						CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR, 
						CONCURSOS_ESTADO_LINEA_A_PEDIR,
						CONCURSOS_ESTADO_LINEA_AGOTADOS,
						CONCURSOS_ESTADO_LINEA_DESCATALOGADO,
						CONCURSOS_ESTADO_LINEA_EN_REIMPRESION						
					)) . ')';
			$data = $this->reg->get(null, null, null, null, $where);
			#var_dump($data);
			if (count($data) > 0 && $origen > 0)
			{
				foreach ($data as $reg) 
				{
					if (in_array($reg['nIdEstado'], array(
						CONCURSOS_ESTADO_LINEA_EN_PROCESO, 
						CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR, 
						CONCURSOS_ESTADO_LINEA_A_PEDIR,
						CONCURSOS_ESTADO_LINEA_AGOTADOS,
						CONCURSOS_ESTADO_LINEA_DESCATALOGADO,
						CONCURSOS_ESTADO_LINEA_EN_REIMPRESION						
					)))
					{
						$res = $this->_cambiar_uno($reg['nIdLineaPedidoConcurso'], CONCURSOS_ESTADO_LINEA_EN_PROCESO);
						if ($res===TRUE)
						{
							return $this->mover($reg['nIdLineaPedidoConcurso'], $idl, $origen, $destino);	
						}
						$this->out->error($res);
					}
				}
				$this->out->error($this->lang->line('concurso-linea-libro-existente'));
			}
			elseif (count($data) > 0)
			{
				$this->out->error($this->lang->line('concurso-linea-libro-existente'));	
			}
				
			$l = $this->m_articulo->load($idl);
			$ins = array(
				'nIdBiblioteca'		=> $biblioteca,
				'nIdSala'			=> $sala,
				'nIdEstado' 		=> CONCURSOS_ESTADO_LINEA_EN_PROCESO,
				'cISBN' 			=> $l['cISBN'],
				'cEAN' 				=> $l['nEAN'],
				'cAutores' 			=> $l['cAutores'],
				'cTitulo' 			=> $l['cTitulo'],
				'nIdLibro' 			=> $idl,
				'fPrecio' 			=> $l['fPVP'],
				'bNuevo' 			=> TRUE
				);
			$this->db->trans_begin();
			$id_n = $this->reg->insert($ins);
			if ($id_n < 1)
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}
			$this->load->model('catalogo/m_articuloseccion');
			$res = $this->m_articuloseccion->get(null, null, null, null, "nIdSeccion={$destino} AND nIdLibro={$idl}");
			if (count($res)==0)
			{
				if ($this->m_articuloseccion->insert(array('nIdSeccion' => $destino, 'nIdLibro' => $idl)) < 1)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_articuloseccion->error_message());
				}
			}
			$this->db->trans_commit();
			#var_dump($id_n); die();

			return ($origen > 0)?
				$this->mover($id_n, $idl, $origen, $destino):
				$this->out->success($this->lang->line('concurso-linea-libro-add'));
			//$this->out->success($this->lang->line('concurso-linea-creada'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Lista los registros
	 *
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $where Condiciones de la consulta
	 * @param string $query Palabra clave de búsqueda
	 * @return DATA
	 */
	function get_estado($start = null, $limit = null, $sort = null, $dir = null, $where = null, $query = null)
	{
		if (isset($this->auth))
		{
			$this->obj->load->library('Userauth');
			$this->userauth->roleCheck(($this->auth .'.get_list'));
		}
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		$where 	= isset($where)?$where:$this->input->get_post('where');
		$query 	= isset($query)?$query:$this->input->get_post('query');
		if (trim($query) == '') $query = null;
		// El where tiene el formato <field>=<valor>&....
		#var_dump($where);
		$where = $this->reg->parse_where($where);
		$where = str_replace('Ext_LineasPedidoConcurso.nIdEstado = 8', 
			'Ext_LineasPedidoConcurso.nIdEstado = 8 AND Ext_LineasPedidoConcurso.nIdCambioLibro IS NOT NULL', $where);
		#var_dump($where); die();
		$data = $this->reg->get($start, $limit, $sort, $dir, $where, null, $query);
		foreach ($data as $k => $reg)
		{
			if (is_numeric($reg['nIdCambioLibro']))
			{
				$new = $this->reg->load($reg['nIdCambioLibro']);
				$new['cEstado'] = $reg['cEstado'];
				$data[$k] = $new;
			}
		}

		$this->out->data($data, $this->reg->get_count());
	}

	function percentatges($idc = null)
	{
		$idc = isset($idc)?$idc:$this->input->get_post('idc');
		$data = $this->reg->percentatges($idc);
		$final = array();
		foreach($data as $reg)
		{
			if (!isset($final[$reg['cBiblioteca']][$reg['cSala']]))
			{
				$final[$reg['cBiblioteca']][$reg['cSala']] = array('total' => 0, 'descatalogados' => 0, 'servidos' => 0);
			}
			$final[$reg['cBiblioteca']][$reg['cSala']]['total'] += $reg['nContador'];
			if (in_array($reg['nIdEstado'], array(6)))
			{
				$final[$reg['cBiblioteca']][$reg['cSala']]['descatalogados'] += $reg['nContador'];
			}
			if (in_array($reg['nIdEstado'], array(3, 19, 4, 20)))
			{
				$final[$reg['cBiblioteca']][$reg['cSala']]['servidos'] += $reg['nContador'];
			}
		}
		$message = $this->load->view('concursos/percentatges', array('datos' => $final), TRUE);

		$this->out->send(array('success' => TRUE, 'html' => $message));
	}

	function preusmig($idc = null)
	{
		$idc = isset($idc)?$idc:$this->input->get_post('idc');
		$data = $this->reg->percentatges($idc);
		$final = array();
		foreach($data as $reg)
		{
			if (!isset($final[$reg['cBiblioteca']][$reg['cSala']]))
			{
				$final[$reg['cBiblioteca']][$reg['cSala']] = array('total' => 0, 'unidades' => 0);
			}
			if (in_array($reg['nIdEstado'], array(3, 19, 4, 20)))
			{
				$final[$reg['cBiblioteca']][$reg['cSala']]['unidades'] += $reg['nContador'];
				$final[$reg['cBiblioteca']][$reg['cSala']]['total'] += $reg['fPrecio'];
			}
		}
		$message = $this->load->view('concursos/preusmig', array('datos' => $final), TRUE);

		$this->out->send(array('success' => TRUE, 'html' => $message));
	}

	function albarans($idc = null)
	{
		$idc = isset($idc)?$idc:$this->input->get_post('idc');
		$final = $this->reg->albarans($idc);
		$message = $this->load->view('concursos/albarans', array('datos' => $final), TRUE);

		$this->out->send(array('success' => TRUE, 'html' => $message));
	}

}

/* End of file Estado.php */
/* Location: ./system/application/controllers/concursos/Estado.php */
