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

/**
 * Avisos de renovacion
 *
 */
class M_avisorenovacion extends MY_Model
{
	/**
	 * Costructor
	 * @return M_avisorenovacion
	 */
	function __construct()
	{
		$data_model = array(
			'nIdSuscripcion'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'suscripciones/suscripcion/search')),
			'nIdGrupoAviso'			=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'suscripciones/grupoaviso/search')),
			'dEnviada'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'dGestionada'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'dFecha'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'bAceptada' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'cPersona'				=> array(), 
			'nIdMedioRenovacion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'suscripciones/mediorenovacion/search')),
		);

		parent::__construct('Sus_AvisosRenovacion', 'nIdAvisoRenovacion', 'nIdAvisoRenovacion', 'nIdAvisoRenovacion', $data_model, TRUE);
		$this->_cache = TRUE;
	}

	/**
	 * Devuelve los clientes y el número de suscripciones pendientes de renovar de la campaña indicada
	 * @param int $id Id de la campaña
	 * @param bool $enviadas TRUE: los avisos enviados, FALSE: los avisos no enviados
	 * @return array
	 */
	function pendientes($id, $enviadas = FALSE)
	{
		$this->db->flush_cache();
		$this->db->select('Cli_Clientes.nIdCliente')
		->select("Cli_Clientes.cEmpresa, Cli_Clientes.cNombre, Cli_Clientes.cApellido")
		->select('COUNT(*) nSuscripciones')
		->select($this->_date_field('MAX(Sus_Suscripciones.dRenovacion)', 'dRenovacion'))
		->from('Cli_Clientes')
		->join('Sus_Suscripciones', 'Sus_Suscripciones.nIdCliente = Cli_Clientes.nIdCliente')
		->join('Sus_AvisosRenovacion', 'Sus_AvisosRenovacion.nIdSuscripcion = Sus_Suscripciones.nIdSuscripcion')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Sus_Suscripciones.nIdRevista')
		->where('Sus_AvisosRenovacion.bAceptada IS NULL')
		->where('Sus_AvisosRenovacion.dEnviada ' . ($enviadas?'IS NOT NULL':'IS NULL'))
		->where('Sus_AvisosRenovacion.nIdGrupoAviso = ' . $id)
		->group_by('Cli_Clientes.nIdCliente, Cli_Clientes.cEmpresa, Cli_Clientes.cNombre, Cli_Clientes.cApellido');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}
	
	/**
	 * Devuelve los avisos de renovación pendientes de un cliente
	 * @param int $idcliente ID del cliente
	 * @param int $idgrupo ID de la campaña
	 * @return array
	 */
	function avisos_cliente($idcliente, $idgrupo)
	{
		$this->db->flush_cache();
		$this->db->select('av.nIdAvisoRenovacion,
						s.nIdSuscripcion,
                        f.cTitulo,
                        tp.fIVA,
                        f.fPrecio,
                        s.cRefCliente,
                        s.nIdDireccionEnvio')
		->select($this->_date_field('s.dRenovacion', 'dRenovacion'))
		->from('Sus_Suscripciones s')
		->join('Sus_AvisosRenovacion av', 's.nIdSuscripcion = av.nIdSuscripcion AND dGestionada IS NULL')
		->join('Cat_Fondo f', 's.nIdRevista = f.nIdLibro')
		->join('Cat_Tipos tp', 'f.nIdTipo = tp.nIdTipo')
		->join('Cat_Revistas r', 'f.nIdLibro = r.nIdLibro', 'left')
		->where("s.nIdCliente = {$idcliente}")
		->where("s.bActiva = 1 AND av.nIdGrupoAviso = {$idgrupo}")
		->where('ISNULL(r.nIdTipoSuscripcion, 0) <> 5')
		->order_by('f.cTitulo');		

		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}
	
	/**
	 * Cancela un aviso de renovación
	 * @param int $id Id de la suscripción
	 * @param string $contacto Persona que realiza la cancelación
	 * @param int $id Id del modo en el que ha dado la orden de cancelación
	 * @param date $fecha Fecha en la que se ha recibido la cancelación
	 * @return bool
	 */
	function cancelar($id, $contacto, $modo, $fecha = null)
	{
		#Comprobar si ya está cancelada, y dar error
		if (empty($fecha)) $fecha = time();
		$data['dGestionada'] = time();
		$data['bAceptada'] = 0;
		$data['dFecha'] = $fecha;
		$data['cPersona'] = $contacto;
		$data['nIdMedioRenovacion'] = $modo;
		
		return $this->update($id, $data);
	}
	
	/**
	 * Acepta un aviso de renovación
	 * @param int $id Id de la suscripción
	 * @param string $contacto Persona que realiza la cancelación
	 * @param int $id Id del modo en el que ha dado la orden de cancelación
	 * @param date $fecha Fecha en la que se ha recibido la cancelación
	 * @return bool
	 */
	function aceptar($id, $contacto, $modo, $fecha = null)
	{
		#Comprobar si ya está aceptada, y dar error
		if (empty($fecha)) $fecha = time();
		$data['dGestionada'] = time();
		$data['bAceptada'] = 1;
		$data['dFecha'] = $fecha;
		$data['cPersona'] = $contacto;
		$data['nIdMedioRenovacion'] = $modo;
		
		return $this->update($id, $data);		
	}

	/**
	 * Devuelve los clientes y el número de suscripciones pendientes de renovar de la campaña indicada
	 * @param int $id Id de la campaña
	 * @return array
	 */
	function por_confirmar($id)
	{
		$this->db->flush_cache();
		$this->db->select('Cli_Clientes.nIdCliente')
		->select("Cli_Clientes.cEmpresa, Cli_Clientes.cNombre, Cli_Clientes.cApellido")
		->select('COUNT(*) nSuscripciones')
		->select($this->_date_field('MAX(Sus_Suscripciones.dRenovacion)', 'dRenovacion'))
		->from('Cli_Clientes')
		->join('Sus_Suscripciones', 'Sus_Suscripciones.nIdCliente = Cli_Clientes.nIdCliente')
		->join('Sus_AvisosRenovacion', 'Sus_AvisosRenovacion.nIdSuscripcion = Sus_Suscripciones.nIdSuscripcion')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Sus_Suscripciones.nIdRevista')
		->where('Sus_AvisosRenovacion.bAceptada IS NULL')
		->where('Sus_AvisosRenovacion.dEnviada IS NOT NULL')
		->where('Sus_AvisosRenovacion.nIdGrupoAviso = ' . $id)
		->group_by('Cli_Clientes.nIdCliente, Cli_Clientes.cEmpresa, Cli_Clientes.cNombre, Cli_Clientes.cApellido');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Devuelve los avisos de renovación pendientes de la campaña y cliente indicados
	 * @param int $id Id de la campaña
	 * @param int $cliente Id del cliente
	 * @param bool $enviadas TRUE: los avisos enviados, FALSE: los avisos no enviados
	 * @return array
	 */
	function get_pendientes($id, $cliente, $enviadas = FALSE)
	{
		$this->db->flush_cache();
		$this->db->select('Sus_Suscripciones.nIdSuscripcion, Sus_AvisosRenovacion.nIdAvisoRenovacion')
		->select('Sus_Suscripciones.nIdSuscripcion, Cat_Fondo.cTitulo, Cat_Fondo.nIdLibro')
		->select('Sus_Suscripciones.cRefCliente')
		->select($this->_date_field('Sus_Suscripciones.dRenovacion', 'dRenovacion'))
		->from('Sus_Suscripciones')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Sus_Suscripciones.nIdRevista')
		->join('Sus_AvisosRenovacion', 'Sus_AvisosRenovacion.nIdSuscripcion = Sus_Suscripciones.nIdSuscripcion')
		->where('Sus_AvisosRenovacion.bAceptada IS NULL')
		->where('Sus_AvisosRenovacion.dEnviada ' . ($enviadas?'IS NOT NULL':'IS NULL'))
		->where('Sus_AvisosRenovacion.nIdGrupoAviso = ' . $id)
		->where('Sus_Suscripciones.nIdCliente=' . $cliente)
		->order_by('Cat_Fondo.cTitulo');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Crea los aviosos de renovación de la campaña indicada
	 * @param int $id Id de la campaña
	 * @param data $renovacion Fecha de renovación máxima
	 * @return int Número de avisos creados
	 */
	function crear_avisos($id, $renovacion)
	{
		$data = $this->por_crear($id, $renovacion);
		$count = 0;
		if (count($data) > 0 )
		{
			foreach($data as $reg)
			{
				$this->insert(array(
				'nIdSuscripcion'  	=> $reg['nIdSuscripcion'], 
				'nIdGrupoAviso' 	=> $id
				));
				++$count;
			}
			return $count;
		}
		return 0;
	}

	/**
	 * Consulta los aviosos de renovación de la campaña indicada
	 * @param int $id Id de la campaña
	 * @param data $renovacion Fecha de renovación máxima
	 * @return array Avisos de renovación por crear
	 */
	function por_crear($id, $renovacion)
	{
		$renovacion = format_mssql_date($renovacion);

		$this->db->flush_cache();
		$this->db->select('Sus_Suscripciones.nIdSuscripcion, Cat_Fondo.cTitulo, Cat_Fondo.nIdLibro')
		->select($this->_date_field('Sus_Suscripciones.dRenovacion', 'dRenovacion'))
		->select('Cli_Clientes.*')
		->from('Sus_Suscripciones')
		->join('Cat_Fondo', 'Sus_Suscripciones.nIdRevista = Cat_Fondo.nIdLibro')
		->join('Cli_Clientes', 'Cli_Clientes.nIdCliente = Sus_Suscripciones.nIdCliente')
		->join('Cat_Revistas', 'Cat_Revistas.nIdLibro = Cat_Fondo.nIdLibro', 'left')
		->where('Sus_Suscripciones.bActiva = 1')
		->where('ISNULL(Cat_Revistas.nIdTipoSuscripcion,0) <> 5')
		->where("(Sus_Suscripciones.dRenovacion < " . $this->db->dateadd('d', 1, $renovacion) . " OR dRenovacion IS NULL)")
		->where("nIdSuscripcion NOT IN (
			SELECT s.nIdSuscripcion
			FROM Sus_AvisosRenovacion a (NOLOCK)
				INNER JOIN Sus_Suscripciones s (NOLOCK)
					ON a.nIdSuscripcion = s.nIdSuscripcion
			WHERE nIdGrupoAviso = {$id}
		)");
		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}

	/**
	 * Estado de los avisos de renovación
	 * @param int $id Id de la campaña
	 * @return array
	 */
	function estado($id)
	{
		$this->db->flush_cache();
		$this->db->select("{$this->_tablename}.*")
		->select('Gen_MediosRenovacion.cDescripcion')
		->from($this->_tablename)
		->join('Gen_MediosRenovacion', "Gen_MediosRenovacion.nIdMedioRenovacion = {$this->_tablename}.nIdMedioRenovacion", 'left')
		->where("nIdGrupoAviso = {$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);

		$estado['aceptadas']['cantidad'] = 0;
		$estado['rechazadas']['cantidad'] = 0;
		$estado['total'] = 0;

		foreach($data as $reg)
		{
			if (isset($reg['dGestionada']) && ($reg['bAceptada'] == 1))
			{
				++$estado['aceptadas']['cantidad'];

				(!isset($estado['aceptadas']['medios'][$reg['cDescripcion']]))?
				($estado['aceptadas']['medios'][$reg['cDescripcion']] = 1):
				(++$estado['aceptadas']['medios'][$reg['cDescripcion']]);
			}
			elseif (isset($reg['dGestionada']) && ($reg['bAceptada'] == 0))
			{
				++$estado['rechazadas']['cantidad'];

				(!isset($estado['rechazadas']['medios'][$reg['cDescripcion']]))?
				($estado['rechazadas']['medios'][$reg['cDescripcion']] = 1):
				(++$estado['rechazadas']['medios'][$reg['cDescripcion']]);

			}
			(!isset($estado['medios'][$reg['cDescripcion']]))?
			($estado['medios'][$reg['cDescripcion']] = 1):
			(++$estado['medios'][$reg['cDescripcion']]);
			++$estado['total'];
		}
		
		return $estado;

	}	
}

/* End of file M_avisorenovacion.php */
/* Location: ./system/application/models/suscripciones/M_avisorenovacion.php */