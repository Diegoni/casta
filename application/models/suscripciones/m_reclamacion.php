<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	suscripciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

define('DESTINO_RECLAMACION_PROVEEDOR', 1);
define('DESTINO_RECLAMACION_CLIENTE', 2);
/**
 * Reclamaciones
 *
 */
class M_reclamacion extends MY_Model
{
	/**
	 * Costructor
	 * @return M_reclamacion
	 */
	function __construct()
	{
		$data_model = array(
			'nIdSuscripcion'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
            'nIdDireccionProveedor'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
            'nIdDireccionCliente'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdTipoReclamacion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'suscripciones/tiporeclamacion/search', 'cTipoReclamacion')),
			'nIdCliente'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'clientes/cliente/search', 'cCliente')),
			'nIdProveedor'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'proveedores/proveedor/search', 'cProveedor')),
			'tDescripcion' 			=> array(DATA_MODEL_DEFAULT => TRUE), 
			'dEnvio'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'nIdReclamacionAsociada'=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'bCancelada' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
		);

		$this->_relations['cliente'] = array (
			'ref'	=> 'clientes/m_cliente',
			'fk'	=> 'nIdCliente');

		$this->_relations['proveedor'] = array (
			'ref'	=> 'proveedores/m_proveedor',
			'fk'	=> 'nIdProveedor');

		$this->_relations['direccioncliente'] = array (
			'ref'	=> 'clientes/m_direccioncliente',
			'fk'	=> 'nIdDireccionCliente');

		$this->_relations['direccionproveedor'] = array (
			'ref'	=> 'proveedores/m_direccion',
			'fk'	=> 'nIdDireccionProveedor');

		parent::__construct('Sus_Reclamaciones', 'nIdReclamacion', 'nIdReclamacion DESC', 'nIdReclamacion', $data_model, TRUE);
		$this->_cache = TRUE;
	}

	/**
	 * Reemplaza las variables de la plantilla de la reclamación con los valores indicados
	 * @param string $texto Plantilla
	 * @param array $data Variables - valores
	 */
	private function replaces(&$texto, &$data)
	{
		foreach($data as $nombre => $valor)
		{
			if (!is_object($valor)) 
			{
				if (is_array($valor))
				{
					$this->replaces($texto, $valor);
				}
				else
				{
					$texto = str_replace('[' . $nombre . ']', $valor, $texto);
				}
			}
		}
	}

	/**
	 * Cancelar la reclamación
	 * @param int $id Id de la reclamación
	 * @return book FALSE: error, TRUE: se ha cancelado correctamente
	 */
	function cancelar($id)
	{
		if (!$this->update($id, array('bCancelada' => TRUE)))
		{
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Marca como enviada la reclamación
	 * @param int $id Id de la reclamación
	 * @return book FALSE: error, TRUE: correcto
	 */
	function enviada($id)
	{
		if (!$this->update($id, array('dEnvio' => time())))
		{
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Crea una nota de reclamación
	 * @param int $tipo Tipo de reclamación
	 * @param int Id del cliente
	 * @param int Id del proveedor
	 * @param int Id de la suscripcion
	 * @param array $datos Datos de la suscripción para crear la nota
	 * @param int $rel Reclamación relacionada
	 * @return FALSE, error, int Id de la nueva reclamación
	 */
	function create($tipo, $cliente, $proveedor, $suscripcion, $data, $rel = null)
	{
		$this->obj->load->model('suscripciones/m_tiporeclamacion');
		$reclamacion =  $this->obj->m_tiporeclamacion->load($tipo);
		$texto = $reclamacion['tTexto'];
		$data['cDireccionEnvio'] = isset($data['direccionenvio'])?format_address_print($data['direccionenvio']):'';
		$data['cDireccionProveedor'] = isset($data['direccionproveedor'])?format_address_print($data['direccionproveedor']):'';
		$error = error_reporting();
		error_reporting(0);
		$data['cProveedor'] = format_name($data['cNombre'], $data['cApellido'], $data['cEmpresa']);
		$data['cCliente'] = format_name($data['cNombre2'], $data['cApellido2'], $data['cEmpresa2']);
		error_reporting($error);
		$data['FECHA'] = format_date(time());

		$this->replaces($texto, $data);

		$add['nIdCliente'] = $cliente;
		$add['nIdProveedor'] = $proveedor;
		$add['nIdSuscripcion'] = $suscripcion;
		$add['nIdTipoReclamacion'] = $tipo;
		$add['tDescripcion'] = $texto;
		$add['nIdReclamacionAsociada'] = $rel;

		return $this->insert($add);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		if (parent::onAfterSelect($data, $id))
		{
			$data['cProveedor'] = format_name($data['cNombre'], $data['cApellido'], $data['cEmpresa']);
			$data['cCliente'] = format_name($data['cNombre2'], $data['cApellido2'], $data['cEmpresa2']);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa');
			$this->db->join('Prv_Proveedores', 'Sus_Reclamaciones.nIdProveedor = Prv_Proveedores.nIdProveedor');
			$this->db->select('Cli_Clientes.cNombre cNombre2, Cli_Clientes.cApellido cApellido2, Cli_Clientes.cEmpresa cEmpresa2');
			$this->db->join('Cli_Clientes', 'Sus_Reclamaciones.nIdCliente = Cli_Clientes.nIdCliente');
			$this->db->select('Sus_TiposReclamacion.cDescripcion cTipoReclamacion, Sus_TiposReclamacion.nIdDestino');
			$this->db->join('Sus_TiposReclamacion', 'Sus_Reclamaciones.nIdTipoReclamacion = Sus_TiposReclamacion.nIdTipoReclamacion');
			$this->db->select('Sus_DestinosReclamacion.cDescripcion cDestino');
			$this->db->join('Sus_DestinosReclamacion', 'Sus_TiposReclamacion.nIdDestino = Sus_DestinosReclamacion.nIdDestino');
			return TRUE;
		}
		return FALSE;
	}	
}

/* End of file M_reclamacion.php */
/* Location: ./system/application/models/suscripciones/M_reclamacion.php */
