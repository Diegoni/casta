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
 * facturas
 *
 */
class FacturaConcurso extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return FacturaConcurso
	 */
	function __construct()
	{
		parent::__construct('concursos.facturaconcurso', 'concursos/M_facturaconcurso', TRUE, null, 'Facturas');
	}

	function crear($fecha = null, $albaranes = null)
	{
		
		$this->out->error('Aún no está hecho');
		
		$this->userauth->roleCheck($this->auth .'.crear');
		$fecha 		= isset($fecha)?$fecha:$this->input->get_post('fecha');
		$albaranes 	= isset($albaranes)?$albaranes:$this->input->get_post('albaranes');

		if ($fecha)
		{
			if ($albaranes)
			{
				// Crea la factura del concurso
				$this->db->trans_begin();
				$id_factura_concurso = $this->reg->insert();
				if ($id_factura_concurso < 0)
				{
					$this->db->trans_rollback();
					$this->out->error($this->reg->error_message());
				}

				// Configuración
				$this->load->model('concursos/m_configuracion');
				$data = $this->m_configuracion->get();
				$configuracion = $data[0];

				// Lee los albaranes agrupados
				$this->load->model('concursos/m_albaranagrupado');
				$ids = preg_split('/;/', $albaranes);
				$albaranes = array();
				$iva = 4;
				$base = array(
					'nCantidad' 	=> 1, 
					'fRecargo'		=> 0, 
					'fIVA' 			=> $iva, 
					'fDescuento' 	=> $configuracion['fDescuento']
				);

				$totales = array(
					'fImporte' 			=> 0,
                    'fPVP' 				=> 0,
                    'fBase' 			=> 0,
                    'fIVAImporte' 		=> 0,
                    'fRecargoImporte' 	=> 0,
                    'fTotal' 			=> 0
				);
				foreach($ids as $id)
				{
					if (is_numeric($id))
					{
						$data = $this->m_albaranagrupado->load($id, TRUE);
						if ($data === FALSE)
						{
							$this->db->trans_rollback();
							$this->out->error($this->m_albaranagrupado->error_message());
						}
						$base['fPrecio'] = format_quitar_iva($this->m_albaranagrupado->importe($id), $iva);
						$importes = format_calculate_importes($base);
						$data['importe'] = $importes;

						$totales['fImporte'] 		+= $importes['fImporte'];
						$totales['fPVP'] 			+= $importes['fPVP'];
						$totales['fBase'] 			+= $importes['fBase'];
						$totales['fIVAImporte'] 	+= $importes['fIVAImporte'];
						$totales['fRecargoImporte'] += $importes['fRecargoImporte'];
						$totales['fTotal'] 			+= $importes['fTotal'];

						$albaranes[] = $data;

						// Asigna como facturado
						if (!$this->m_albaranagrupado->update($id, array('nIdFactura' => $id_factura_concurso)))
						{
							$this->db->trans_rollback();
							$this->out->error($this->m_albaranagrupado->error_message());								
						}
					}
				}

				// Lee los libros pendientes del concurso
				$idseccion = $configuracion['nIdSeccion'];
				$this->load->model('catalogo/m_articulosearch');
				$librosbp = $this->m_articulosearch->get(null, null, null, null,
				"nIdSeccion={$idseccion} AND nStock <> 0");
				#"{$this->m_articuloseccion->get_tablename()}.nIdSeccion={$idseccion} AND ({$this->m_articuloseccion->get_tablename()}.nStockFirme + {$this->m_articuloseccion->get_tablename()}.nStockDeposito) <> 0");
				$this->load->model('ventas/m_albaransalida');
				$albaranbp['nIdCliente'] = $configuracion['nIdCliente'];
				foreach($librosbp as $librobp)
				{
					$librobp['nCantidad'] = $librobp['nStockFirme'] + $librobp['nStockDeposito'];
					$librobp['fRecargo'] = 0;
					$albaranbp['lineas'][] = $librobp;
				}

				$id_albaranbp = $this->m_albaransalida->insert($albaranbp);
				if ($id_albaranbp < 0)
				{
					$this->out->error($this->m_albaransalida->error_message());
				}
				// Calcula el importe pendiente
				var_dump($id_albaranbp);
				$importes = $this->m_albaransalida->importes($id_albaranbp);
				echo '<pre>'; print_r($importes); echo '</pre>';
				#echo '<pre>'; print_r($albaranbp); echo '</pre>';
				#echo '<pre>'; print_r($librosbp); echo '</pre>';
				#echo '<pre>'; print_r($albaranes); echo '</pre>';
				#echo '<pre>'; print_r($totales); echo '</pre>';
				$this->db->trans_rollback();
				die();

				// Crea la factura en Bibliopola
				$this->load->model('concursos/m_albaran');
				$count = 0;
				foreach($ids as $id1)
				{
					if (isset($id1) && $id1 != '')
					{
						$this->m_albaran->update($id1, array('nIdAlbaranAgrupado' => $id));
						$count++;
					}
				}
				$this->out->success(sprintf($this->lang->line('concursos-albaranes-add'), $count));
			}
			else
			{
				$this->out->error($this->lang->line('mensaje_faltan_datos'));
					
			}
		}
		else
		{
			$this->_show_js('crear', 'concursos/crearfactura.js');
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#_pre_printer($id, $data, $css)
	 */
	protected function _pre_printer($id, &$data, &$css)
	{
		parent::_pre_printer($id, $data, $css);
		
		$this->load->model('ventas/m_factura');
		$factura = $this->m_factura->load($data['nIdFacturaBibliopola'], TRUE);
		$data = array_merge($data, $factura);
		#echo '<pre>'; var_dump($data); echo '</pre>';
		
		$css = $this->config->item('bp.documentos.css');
		
		return TRUE;
	}
	}

/* End of file facturaconcurso.php */
/* Location: ./system/application/controllers/concursos/facturaconcurso.php */
