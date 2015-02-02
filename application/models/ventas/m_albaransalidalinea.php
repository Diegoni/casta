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
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Líneas de albarán de salida
 *
 */
class M_albaransalidalinea extends MY_Model
{
	#var $_albaranes = null;
	#var $_tablename = null;
	var $_tablealbaranes = null;
	
	/**
	 * Constructor
	 * @return M_albaransalidalinea
	 */
	function __construct($tablename = null, $albaranes = null, $tablealbaranes = null)
	{
		if(!isset($tablename)) $tablename = 'Doc_LineasAlbaranesSalida';
		if(!isset($albaranes)) $albaranes = 'albaransalida';
		if(!isset($tablealbaranes)) $tablealbaranes = 'ventas/m_albaransalida';
		$this->_tablealbaranes = $tablealbaranes;
		
		$data_model = array(
			'nIdAlbaran' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, "ventas/{$albaranes}/search")),
			'nIdSeccion' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search')),
			'nIdLibro' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),
			'nCantidad' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'fPrecio' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'fIVA' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'fRecargo' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'fDescuento' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'fCoste' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'bReposicion' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
			'cRefCliente' 	=> array(),
			'cRefInterna' 	=> array(),
			'nEnFirme' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nEnDeposito' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'bLiquidado' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
			'nIdDocumentoDeposito' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nFirme1'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nFirme2'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nFirme3'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nFirme4'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'nComision' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdLineaPedido' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/pedidoclientelinea/search')),
		);

		parent::__construct($tablename, 'nIdLineaAlbaran', 'nIdLineaAlbaran', 'nIdLineaAlbaran', $data_model, TRUE);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id =null, &$sort =null, &$dir =null, &$where =null)
	{
		if(parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.cISBN, Cat_Secciones.cNombre cSeccion, Cat_Editoriales.cNombre cEditorial, Cat_Fondo.nIdOferta, Cat_Fondo.bNoDto');
			$this->db->join('Cat_Fondo', "Cat_Fondo.nIdLibro = {$this->_tablename}.nIdLibro");
			$this->db->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = {$this->_tablename}.nIdSeccion");
			$this->db->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial', 'left');
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id =null)
	{
		if(parent::onAfterSelect($data, $id))
		{
			$importes = format_calculate_importes($data);
			$data = array_merge($data, $importes);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($id, $data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		static $albaranes = array();
		if(parent::onBeforeUpdate($id, $data))
		{
			if(isset($id) && isset($data['nCantidad']))
			{
				$linea = $this->load($id);
				if(!isset($albaranes[$linea['nIdAlbaran']]))
				{
					$this->obj->load->model($this->_tablealbaranes, 'malb');
					$albaranes[$linea['nIdAlbaran']] = $this->obj->malb->load($linea['nIdAlbaran']);
				}
				
				if($albaranes[$linea['nIdAlbaran']]['nIdEstado'] != DEFAULT_ALBARAN_SALIDA_STATUS)
				{
					$ln = $this->load($id);
					if ($ln['nCantidad']!=$data['nCantidad'])
					{
						$this->_set_error_message($this->lang->line('albaransalida-error-cambio-cantidad'));					
						return FALSE;
					}
				}
			}
			return $this->_pre_ins($data, $id);
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 */
	protected function onBeforeDelete($id)
	{
		// Si el albarán no está en proceso, no se puede borrar
		$reg = $this->load($id);
		#var_dump($this->_tablealbaranes);
		$this->obj->load->model($this->_tablealbaranes, 'ma');
		$albaran = $this->obj->ma->load($reg['nIdAlbaran']);
		if ($albaran['nIdEstado'] != DEFAULT_ALBARAN_SALIDA_STATUS)
		{
			$this->_set_error_message(sprintf($this->lang->line('error-albaransalida-cerrado'), $id));
			return FALSE;
		}

		# Elimina las referencias del concurso
		$this->obj->load->model('concursos/m_pedidoconcursolinea');
		$this->obj->load->model('concursos/m_estadolineaconcurso');
		$sl = $this->obj->m_pedidoconcursolinea->get(0, 1, null, null, "nIdLineaAlbaranSalida = {$id}");
		foreach ($sl as $reg) 
		{
			if ($reg['nIdEstado'] != CONCURSOS_ESTADO_LINEA_EN_ALBARAN)
			{
				$this->_set_error_message(sprintf($this->lang->line('albaran-salida-linea-error-concurso'), $reg['cConcurso']));
				return FALSE;
			}
			if (!$this->obj->m_pedidoconcursolinea->update($reg['nIdLineaPedidoConcurso'], 
				array(
					'nIdLineaAlbaranSalida' => NULL,
					'nIdEstado' 			=> CONCURSOS_ESTADO_LINEA_CATALOGADO
					)))
			{
				$this->_set_error_message($obj->m_pedidoconcursolinea->error_message());
				return FALSE;
			}
		}

		// Cambia el estado de las líneas de pedido vinculadas al pedido		
		/*if (isset($reg['nIdLineaPedido']))
		{
			$this->obj->load->model('ventas/m_pedidoclientelinea');
			$linea = $this->obj->m_pedidoclientelinea->load($reg['nIdLineaPedido']);
			$st =  ($linea['nCantidadServida'] > 0)?ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA:ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO;
			if (!$this->obj->m_pedidoclientelinea->update($reg['nIdLineaPedido'], array('nIdEstado' => $st)))
			{
				$this->_set_error_message($this->obj->m_pedidoclientelinea->error_message());
				return FALSE;
			}
		}*/
		return parent::onBeforeDelete($id);
	}

	/**
	 * Acciones previas a INSERT/UPDATE
	 * @param array $data Datos
	 * @param int $id Id del registro si UPDATE
	 * @return bool
	 */
	protected function _pre_ins($data, $id = null)
	{
		if (isset($data['nIdSeccion']) && (isset($data['nIdLibro'])))
		{
			# Si no existe la sección, la crea
			$this->obj->load->model('catalogo/m_articuloseccion');
			$sl = $this->obj->m_articuloseccion->get(0, 1, null, null, "nIdLibro = {$data['nIdLibro']} AND nIdSeccion = {$data['nIdSeccion']}");
			if (count($sl) == 0)
			{
				$ids = $this->obj->m_articuloseccion->insert(array('nIdSeccion' => $data['nIdSeccion'], 'nIdLibro' => $data['nIdLibro']));
				if ($ids < 0)
				{
					$this->_set_error_message($this->obj->m_articuloseccion->error_message());
					return FALSE;
				}
			}
		}
		
		return TRUE;
	}


	/**
	 * Trigger llamado Antes de insertar los datos
	 * @param array $data Registro a insertar
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 * @todo Actualizar el estado del pedido cuando se modifican las líneas
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			return $this->_pre_ins($data);
		}

		return FALSE;
	}
}

/* End of file M_albaransalidalinea.php */
/* Location: ./system/application/models/compras/M_albaransalidalinea.php */
