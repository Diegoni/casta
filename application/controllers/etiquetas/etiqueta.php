<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	etiquetas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Gestión de etiquetas
 *
 */
class Etiqueta  extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Etiqueta
	 */
	function __construct()
	{
		parent::__construct('etiquetas.etiqueta', 'etiquetas/M_etiqueta', TRUE, null, 'Etiquetas', 'sys/submenuetiquetas.js');
	}

	/**
	 * Devuelve los grupos de etiquetas
	 * @return DATA
	 */
	function grupos()
	{
		$this->out->data($this->reg->grupos());
	}

	/**
	 * Imprime una etiqueta de dirección de cliente
	 * @param int $id Id de la dirección
	 * @param string $grupo Grupo de etiquetas
	 * @return JSON
	 */
	function colacliente($id = null, $grupo = null)
	{
		return $this->colaetq('nIdCliente', 'clientes/m_cliente', 'clientes/m_direccioncliente', 'colacliente', $id, $grupo);
	}

	/**
	 * Imprime una etiqueta de dirección de proveedor
	 * @param int $id Id de la dirección
	 * @param string $grupo Grupo de etiquetas
	 * @return JSON
	 */
	function colaproveedor($id = null, $grupo = null)
	{
		return $this->colaetq('nIdProveedor', 'proveedores/m_proveedor' ,'proveedores/m_direccion', 'colaproveedor', $id, $grupo);
	}

	/**
	 * Imprime una etiqueta de dirección de cliente
	 * @param int $id Id de la dirección
	 * @param string $grupo Grupo de etiquetas
	 * @return JSON
	 */
	function colacontacto($id = null, $grupo = null)
	{
		return $this->colaetq('nIdContacto', 'mailing/m_contacto', 'mailing/m_contactodireccion', 'colacontacto', $id, $grupo);
	}

	/**
	 * Imprime una etiqueta de dirección
	 * @param string $model Modelo de datos que da la dirección
	 * @param string $url URL a la que llamar para imprimir
	 * @param int $id Id de la dirección
	 * @param int $tipo Tipo de etiqueta (modelo)
	 * @param int $formato Formato de impresión de la etiqueta
	 * @param int $row Fila inicial
	 * @param int $column Columna inicial
	 * @return JSON
	 */
	function colaetq($iddesc, $pvcl, $model, $url, $id = null, $grupo = null)
	{
		$this->userauth->roleCheck($this->auth . '.index');

		$id = isset($id) ? $id : $this->input->get_post('id');

		$grupo = isset($grupo) ? $grupo : $this->input->get_post('grupo');

		if (is_numeric($id))
		{
			if (!empty($grupo))
			{
				$this->load->model($model, 'mc');
				$this->load->model($pvcl, 'pvcl');
				$dir = $this->mc->load($id);
				$pvcl = $this->pvcl->load($dir[$iddesc]);
				$dir['cNombre'] = $pvcl['cNombre'];
				$dir['cApellido'] = $pvcl['cApellido'];
				$dir['cEmpresa'] = $pvcl['cEmpresa'];
				if (!isset($dir['cTitular']) || trim($dir['cTitular'])=='')
					$dir['cTitular'] = format_name($dir['cNombre'], $dir['cApellido'], $dir['cEmpresa'], TRUE);

				$this->load->library('Etiquetas');
				$link = format_enlace_cmd($this->lang->line('Ver cola'), site_url('etiquetas/etiqueta/index/'));
				$this->etiquetas->encolar($dir, $grupo)?
					$this->out->success(sprintf($this->lang->line('etiqueta-encolada'), $link)):
					$this->out->error($this->etiquetas->get_error());
			}
			else
			{
				$data['id'] = $id;
				$data['url'] = site_url('etiquetas/etiqueta/' . $url);
				$this->_show_js('index', 'etiquetas/encolar.js', $data);
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Imprime una etiqueta de dirección de cliente
	 * @param int $id Id de la dirección
	 * @param int $tipo Tipo de etiqueta (modelo)
	 * @param int $formato Formato de impresión de la etiqueta
	 * @param int $row Fila inicial
	 * @param int $column Columna inicial
	 * @return JSON
	 */
	function printcliente($id = null, $tipo = null, $formato = null, $row = null, $column = null)
	{
		return $this->printetq('nIdCliente', 'clientes/m_cliente', 'clientes/m_direccioncliente', 'printcliente', $id, $tipo, $formato, $row, $column);
	}

	/**
	 * Imprime una etiqueta de dirección de proveedor
	 * @param int $id Id de la dirección
	 * @param int $tipo Tipo de etiqueta (modelo)
	 * @param int $formato Formato de impresión de la etiqueta
	 * @param int $row Fila inicial
	 * @param int $column Columna inicial
	 * @return JSON
	 */
	function printproveedor($id = null, $tipo = null, $formato = null, $row = null, $column = null)
	{
		return $this->printetq('nIdProveedor', 'proveedores/m_proveedor' ,'proveedores/m_direccion', 'printproveedor', $id, $tipo, $formato, $row, $column);
	}

	/**
	 * Imprime una etiqueta de dirección de contacto
	 * @param int $id Id de la dirección
	 * @param string $grupo Grupo de etiquetas
	 * @return JSON
	 */
	function printcontacto($id = null, $tipo = null, $formato = null, $row = null, $column = null)
	{
		return $this->printetq('nIdContacto', 'mailing/m_contacto', 'mailing/m_contactodireccion', 'printcontacto', $id, $tipo, $formato, $row, $column);
	}

	/**
	 * Imprime una etiqueta de dirección
	 * @param string $model Modelo de datos que da la dirección
	 * @param string $url URL a la que llamar para imprimir
	 * @param int $id Id de la dirección
	 * @param int $tipo Tipo de etiqueta (modelo)
	 * @param int $formato Formato de impresión de la etiqueta
	 * @param $row Fila inicial
	 * @param $column Columna inicial
	 * @return JSON
	 */
	function printetq($iddesc, $pvcl, $model, $url, $id = null, $tipo = null, $formato = null, $row = null, $column = null, $qt = null)
	{
		$this->userauth->roleCheck($this->auth . '.index');

		$id = isset($id) ? $id : $this->input->get_post('id');

		$tipo = isset($tipo) ? $tipo : $this->input->get_post('tipo');
		$formato = isset($formato) ? $formato : $this->input->get_post('formato');
		$row = isset($row) ? $row : $this->input->get_post('row');
		$column = isset($column) ? $column : $this->input->get_post('column');
		$qt = isset($qt) ? $qt : $this->input->get_post('qt');

		if (is_numeric($id))
		{
			if ((is_numeric($formato)) && (is_numeric($tipo)))
			{
				if ($qt<1) $qt = 1;
				$this->load->model($model, 'mc');
				$this->load->model($pvcl, 'pvcl');
				$dir = $this->mc->load($id);
				#var_dump($dir); die();
				$pvcl = $this->pvcl->load($dir[$iddesc]);
				$dir['cNombre'] = $pvcl['cNombre'];
				$dir['cApellido'] = $pvcl['cApellido'];
				$dir['cEmpresa'] = $pvcl['cEmpresa'];
				if (!isset($dir['cTitular']) || trim($dir['cTitular'])=='')
					$dir['cTitular'] = format_name($dir['cNombre'], $dir['cApellido'], $dir['cEmpresa'], TRUE);
				$dir['total'] = $qt;
				$this->load->library('Etiquetas');
				$ct = 1;
				$etq = array();
				do {

					$dir['num']	= $ct;
					$etq[] = $this->etiquetas->etiqueta($dir, $formato);
					++$ct;
				} while ($ct <= $qt);

				$url = $this->etiquetas->paper($etq, $tipo, $row, $column);
				$this->out->url($url, 'Etiquetas', 'iconoReportTab');
			}
			else
			{
				$data['id'] = $id;
				$data['url'] = site_url('etiquetas/etiqueta/' . $url);
				$this->_show_js('index', 'etiquetas/print.js', $data);
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Imprime un grupo de etiquetas
	 * @param string $id Id de la dirección
	 * @param int $tipo Tipo de etiqueta (modelo)
	 * @param int $formato Formato de impresión de la etiqueta
	 * @param $row Fila inicial
	 * @param $column Columna inicial
	 * @return PDF
	 */
	function imprimir($id = null, $tipo = null, $formato = null, $row = null, $column = null)
	{
		$this->userauth->roleCheck($this->auth . '.index');

		$id = isset($id) ? $id : $this->input->get_post('id');
		$tipo = isset($tipo) ? $tipo : $this->input->get_post('tipo');
		$formato = isset($formato) ? $formato : $this->input->get_post('formato');
		$row = isset($row) ? $row : $this->input->get_post('row');
		$column = isset($column) ? $column : $this->input->get_post('column');

		if (!empty($id))
		{
			if ((is_numeric($formato)) && (is_numeric($tipo)))
			{
				$this->load->library('Etiquetas');
				$id = $this->db->escape($id);
				$data = $this->reg->get(null, null, null, null, 'cDescripcion=' . $id);
				$etq = array();
				foreach ($data as $value) 
				{
					$etq[] = $this->etiquetas->etiqueta(unserialize($value['cEtiqueta']), $formato);
				}
				$url = $this->etiquetas->paper($etq, $tipo, $row, $column);
				$this->out->url($url, 'Etiquetas', 'iconoReportTab');
			}
			else
			{
				$data['id'] = $id;
				$data['url'] = site_url('etiquetas/etiqueta/imprimir');
				$this->_show_js('index', 'etiquetas/print.js', $data);
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Elimina un grupo de etiquetas
	 * @param string $id Id de la dirección
	 * @return MSG
	 */
	function delgrupo($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.index');

		$id = isset($id) ? $id : $this->input->get_post('id');

		if (!empty($id))
		{
			$id = $this->db->escape($id);
			$data = $this->reg->delete_by('cDescripcion=' . $id);			
			$this->out->success(sprintf($this->lang->line('delete-grupo-etiquetas'), $id));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
}

/* End of file etiqueta.php */
/* Location: ./system/application/controllers/etiquetas/etiqueta.php */