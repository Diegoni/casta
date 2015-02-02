<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	proveedores
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador de proveedores
 *
 */
class Proveedor extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return proveedor
	 */
	function __construct()
	{
		parent::__construct('proveedores.proveedor', 'proveedores/M_proveedor', TRUE, 'proveedores/proveedor.js', 'Proveedores');
	}

	/**
	 * Busca una cuenta libre para el tipo de proveedor indicado
	 * @param int $tipo Id del tipo de proveedor
	 * @return JSON
	 */
	function cuenta($tipo = null)
	{
		$this->userauth->roleCheck(($this->auth.'.cuenta'));

		$tipo	= isset($tipo)?$tipo:$this->input->get_post('tipo');

		if ($tipo)
		{
			$this->load->model('proveedores/m_tipoproveedor');
			$tipo = $this->m_tipoproveedor->load($tipo);
			$base = $tipo['nCuenta'];
			$digitos = $this->config->item('bp.proveedores.digitoscuenta');
			$min = (float)($base . str_repeat('0', $digitos - strlen($base)));
			$max = (float)($base . str_repeat('9', $digitos - strlen($base)));
			$cuenta = $this->reg->next_cuenta($min, $max);
			$this->out->success($cuenta);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Unificador de proveedores
	 * @param int $id1 Id de la proveedor destino
	 * @param string $id2 Ids de las proveedores repetidos, separados por ;
	 * @return JSON
	 */
	function unificar($id1 = null, $id2 = null)
	{
		$this->userauth->roleCheck(($this->auth.'.unificar'));

		$id1	= isset($id1)?$id1:$this->input->get_post('id1');
		$id2	= isset($id2)?$id2:$this->input->get_post('id2');

		if ($id1 && $id2)
		{
			$ids = preg_split('/\;/', $id2);
			$t = '';
			$this->load->library('Logger');
			if (!$this->reg->unificar($id1, $ids))
			{
				$str = $this->reg->error_message();
				$this->out->error($str);
			}
			$this->logger->log('proveedor unificado ' . implode(',', $ids) . ' con ' .$id1, 'unificador');
			$this->out->success($this->lang->line('proveedores-unificados-ok'));
		}
		else
		{
			$data['title'] = $this->lang->line('Unificar proveedor');
			$data['icon'] = 'iconoUnficarProveedorTab';
			$data['url_search'] = site_url('proveedores/proveedor/search');
			$data['url'] = site_url('proveedores/proveedor/unificar');
			$this->_show_form('unificar', 'catalogo/unificador.js', $this->lang->line('Unificar proveedor'), null, null, null, $data);
		}
	}

	/**
	 * Devuelve la información del proveedor para mostrar en los documentos
	 * @param int $id Id del proveedor
	 */
	function info($id = null)
	{
		$this->userauth->roleCheck(($this->auth.'.index'));

		$id	= isset($id)?$id:$this->input->get_post('id');

		$proveedor = null;
			
		if ($id)
		{
			$proveedor = $this->reg->load($id, TRUE);
			#print '<pre>'; print_r($proveedor); print '</pre>';
			$info = array();
			#if ($proveedor['bCredito']) $info[] = sprintf($this->lang->line('proveedor-tiene-cuenta'), $proveedor['nIdCuenta']);
			#if (isset($proveedor['cGrupoproveedor'])) $info[] = sprintf($this->lang->line('proveedor-grupo'), $proveedor['cGrupoproveedor']);
			/*if (isset($proveedor['descuentos']))
			 {
				foreach($proveedor['descuentos'] as $descuento)
				{
				$info[] = sprintf($this->lang->line('proveedor-descuento'), $descuento['cEditorial'], $descuento['cTipo'], format_percent($descuento['fDescuento']));
				}
				}*/
			if (isset($proveedor['tComentario']) && (trim($proveedor['tComentario']) != '')) $info[] = str_replace("\n", '<br/>', $proveedor['tComentario']);
			$text = (count($info) > 0)?implode($info, '<br/>'):null;
			$proveedor['info'] = $text;
		}
		$this->out->data($proveedor);
	}

	/**
	 * Alta rápida de proveedor
	 * @return FORM
	 */
	function alta()
	{
		$this->userauth->roleCheck(($this->auth.'.add'));
		$data = get_post_all();
		foreach($data as $k => $v)
		{
			if (trim($v) == '') unset($data[$k]);
		}
		if (isset($data['cEmpresa']) || isset($data['cNombre']) || isset($data['cApellido']))
		{
			//Preparamos los datos
			$this->load->model('proveedores/m_proveedor');
			$this->load->model('proveedores/m_telefono');
			$this->load->model('proveedores/m_direccion');
			$this->load->model('proveedores/m_email');
			$this->db->trans_begin();
			$id_proveedor = $this->m_proveedor->insert($data);
			if ($id_proveedor < 1)
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_proveedor->error_message());
			}
			if (isset($data['cEmail']))
			{
				$id = $this->m_email->insert(array('nIdProveedor' => $id_proveedor, 'cEMail' => $data['cEmail']));
				if ($id < 1)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_email->error_message());
				}
			}
			if (isset($data['cTelefono']))
			{
				$id = $this->m_telefono->insert(array('nIdProveedor' => $id_proveedor, 'cTelefono' => $data['cTelefono']));
				if ($id < 1)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_telefono->error_message());
				}
			}
			if (isset($data['cCalle']))
			{
				$data['nIdProveedor'] =  $id_proveedor;
				$id = $this->m_direccion->insert($data);
				if ($id < 1)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_direccion->error_message());
				}
			}
			$this->db->trans_commit();

			$res = array (
				'success'	=> TRUE,
				'message'	=> sprintf($this->lang->line('registro_generado'), $id_proveedor),
				'id'		=> $id_proveedor
			);

			$this->out->send($res);
		}
		else
		{
			$this->_show_js('add', 'proveedores/altarapida.js');
		}
	}

	/**
	 * Documentos desglosado por titulos de un proveedor
	 * @param int $idp ID del proveedor
	 * @param date $fecha1 Fecha desde
	 * @param date $fecha2 Fecha hasta
	 * @param string $tipo Tipo de documento a miostrar, separado por ;
	 * @return HTML_FILE
	 */
	function documentos_articulos($idp = null, $fecha1 = null, $fecha2 = null, $tipo = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$idp		= isset($idp)?$idp:$this->input->get_post('idp');
		$fecha1 	= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2 	= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');
		$tipo 		= isset($tipo)?$tipo:$this->input->get_post('tipo');
		if ($tipo !== FALSE) $tipo = preg_split("/[,\|\s]/", $tipo);
		if ($tipo === FALSE) $tipo = TRUE;

		if (!empty($idp) && !empty($fecha1) && !empty($fecha2))
		{
			set_time_limit(0);
			$pv = $this->reg->load($idp);
			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);
			$this->load->model('catalogo/m_articulo');
			$docs = $this->m_articulo->get_documentos(null, $fecha1, $fecha2, null, $tipo, null, $idp);
			#echo '<pre>'; print_r($docs); echo '</pre>'; die();
			if (count($docs)>0)
			{
				sksort($docs, 'dFecha');
				$data['docs'] = $docs;
				$data['fecha1'] = $fecha1;
				$data['fecha2'] = $fecha2;
				$data['titulo'] = $this->lang->line('Documentos proveedor artículos');
				$data['item'] = format_name($pv['cNombre'], $pv['cApellido'], $pv['cEmpresa']);
				$data['id'] = $pv['id'];
				$data['clpv'] = FALSE;
				$message = $this->load->view('catalogo/documentos', $data, TRUE);
				$this->out->html_file($message, sprintf($this->lang->line('Documentos proveedor'), $idp), 'iconoReportTab');
			}
			$this->out->success($this->lang->line('no-hay-documentos'));
		}
		elseif (is_numeric($idp))
		{
			$data['url'] = site_url('proveedores/proveedor/documentos_articulos');
			$data['title'] = sprintf($this->lang->line('Documentos proveedor artículos'), '');
			$data['idl'] = $idp;
			$data['name'] = 'idp';

			$this->_show_js('get_list', 'catalogo/documentosasticulo.js', $data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Análisis de los mivimientos de un proveedor
	 * @param int $id Id del proveedor
	 * @return HTML_FILE
	 */
	function analisis($id = null, $desde = null, $hasta = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$desde = isset($desde) ? $desde : $this->input->get_post('desde');
		$hasta = isset($hasta) ? $hasta : $this->input->get_post('hasta');


		if (is_numeric($id) && !empty($desde) && !empty($hasta)  )
		{
			$desde = to_date($desde);
			$hasta = to_date($hasta);
			$datos = $this->reg->analisis($id, $desde, $hasta);

			$datos['proveedor'] = $this->reg->load($id);

			$message = $this->load->view('compras/analisis_proveedor', $datos, TRUE);
			#echo $message; die();
			$this->out->html_file($message, $this->lang->line('Análisis') . ' ' . $id, 'iconoReportTab', $this->config->item('bp.data.css'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Listado de artículos compradós (última compra) por el proveedor indicado
	 * @param  int  $id    Id del proveedor
	 * @param  boolean $stock TRUE: tiene Stock
	 * @param  date  $desde Fecha inicio de compra
	 * @param  date  $hasta Fecha final de compra
	 * @return HTML
	 */
	function comprados($id = null, $stock = null, $desde = null, $hasta = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$desde = isset($desde) ? $desde : $this->input->get_post('desde');
		$hasta = isset($hasta) ? $hasta : $this->input->get_post('hasta');
		$stock = isset($stock) ? $stock : $this->input->get_post('stock');

		if (is_numeric($id))			
		{
			$stock = format_tobool($stock);

			if (!empty($desde)) $desde = to_date($desde);
			if (!empty($hasta)) $hasta = to_date($hasta);

			$datos['titulos'] = $this->reg->comprados($id, $stock, $desde, $hasta);
			$datos['proveedor'] = $this->reg->load($id);
			$datos['stock'] = $stock;
			$datos['desde'] = $desde;
			$datos['hasta'] = $hasta;

			$message = $this->load->view('compras/comprados', $datos, TRUE);
			#echo $message; die();
			$this->out->html_file($message, $this->lang->line('Comprados') . ' ' . $id, 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

}

/* End of file proveedor.php */
/* Location: ./system/application/controllers/proveedores/proveedor.php */