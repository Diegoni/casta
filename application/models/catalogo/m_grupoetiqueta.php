<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Grupos de etiquetas para imprimir
 *
 */
class M_grupoetiqueta extends MY_Model
{
	/**
	 * Constructor
	 * @return M_grupoetiqueta
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		);

		parent::__construct('Alm_PaquetesEtiquetas', 'nIdPaquete', 'cDescripcion', 'cDescripcion', $data_model, TRUE);
		$this->_cache = TRUE;

		$this->_relations['lineas'] = array(
            'ref' => 'catalogo/m_grupoetiquetalinea',
            'cascade' => TRUE,
            'type' => DATA_MODEL_RELATION_1N,
            'fk' => 'nIdPaquete');
	}

	/**
	 * Obtiene las etiquetas de una sección/subsecciónes
	 * @param int $id Id del grupo 
	 * @param int $ids Id de la sección
	 * @return array
	 */
	function get_seccion($id, $ids = null)
	{
		if (isset($ids))
		{
			$this->obj->load->model('generico/m_seccion');
			$codigo = $this->obj->m_seccion->load($ids);
			$codigo_s = $codigo['cCodigo'];
		}
		
		$this->db->flush_cache();
		$this->db->select("Cat_Fondo.nIdLibro,
			Cat_Fondo.cTitulo, 
			Cat_Fondo.cAutores,
			Cat_Fondo.cISBN,
			Cat_Secciones.cNombre cSeccion,
			Cat_Secciones.nIdSeccion,
			Alm_EtqAcumuladas.cSimbolo,
			Alm_EtqAcumuladas.fPVP,
			Alm_EtqAcumuladas.nCantidad,
			Alm_EtqAcumuladas.nIdAcumulado")
		->from('Alm_EtqAcumuladas')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Alm_EtqAcumuladas.nIdLibro')
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion = Alm_EtqAcumuladas.nIdSeccion')
		->where("Alm_EtqAcumuladas.nIdPaquete = {$id}");
		if (isset($ids))
		{
			$this->db->where("(Cat_Secciones.cCodigo LIKE '{$codigo_s}.%' OR Cat_Secciones.nIdSeccion = {$ids})");
		}
		
		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}

	/**
	 * Elimina las etiquetas de una sección y sus subsecciones
	 * @param int $id Id del grupo etiquetas
	 * @param int $ids Id de la sección
	 * @return array
	 */
	function del_seccion($id, $ids = null)
	{
		$this->obj->load->model('generico/m_seccion');
		$codigo = $this->obj->m_seccion->load($ids);
		$codigo_s = $codigo['cCodigo'];

		$this->db->flush_cache();
		$this->db->select("Alm_EtqAcumuladas.nIdAcumulado")
		->from('Alm_EtqAcumuladas')
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion = Alm_EtqAcumuladas.nIdSeccion')
		->where("Alm_EtqAcumuladas.nIdPaquete = {$id}")
		->where("(Cat_Secciones.cCodigo LIKE '{$codigo_s}.%' OR Cat_Secciones.nIdSeccion = {$ids})");

		$query = $this->db->get();
		$data = $this->_get_results($query);

		$this->obj->load->model('catalogo/m_grupoetiquetalinea');
		$this->db->trans_begin();
		foreach($data as $reg)
		{
			if (!$this->obj->m_grupoetiquetalinea->delete($reg['nIdAcumulado']))
			{
				$this->db->trans_rollback();
				$this->_set_error_message($this->obj->m_grupoetiquetalinea->error_message());
				return FALSE;
			}
		}
		$this->db->trans_commit();

		return TRUE;
	}
}

/* End of file M_grupoetiqueta.php */
/* Location: ./system/application/models/catalogo/M_grupoetiqueta.php */
