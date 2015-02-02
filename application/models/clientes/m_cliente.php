<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	clientes
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

define('DEFAULT_CLIENTE_STATUS', 	1);
define('DEFAULT_CLIENTE_TARIFA', 	1);
define('DEFAULT_CLIENTE_GRUPOIVA', 	1);

define('STATUS_CLIENTE_ACTIVADO', 	1);
define('STATUS_CLIENTE_BLOQUEADO', 	2);
define('STATUS_CLIENTE_BAJA', 		3);

/**
 * Clientes
 *
 */
class M_Cliente extends MY_Model
{
	/**
	 * Añadir el email a las búsquedas
	 * @var bool
	 */
	public $_addemail = FALSE;

	/**
	 * Constructor
	 * @return M_cliente
	 */
	function __construct()
	{
		$data_model = array(
			'cNombre' 			=> array(), 
			'cApellido'			=> array(),
			'cEmpresa'			=> array(DATA_MODEL_DEFAULT => TRUE),
			'cNIF' 				=> array(),
			'bCredito'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'nIdCuenta' 		=> array(/*DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT*/), 
			'cPass' 			=> array(),
			'bRecargo'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bExamen'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN), 
			'cNIF' 				=> array(),
			'nIdTipoCliente' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/tipocliente/search')), 
			'nIdTratamiento' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/tratamiento/search')),
			'nIdGrupoCliente' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/grupocliente/search')),
			'bNoEmail'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bNoCarta'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN), 
			'bExentoIVA'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN), 
			'cRandom' 			=> array(),
			'nIdEstado' 		=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_CLIENTE_STATUS, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/estadocliente/search')), 
			'nIdTipoTarifa' 	=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_CLIENTE_TARIFA, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/tipotarifa/search')),
			'nIdGrupoIva' 		=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_CLIENTE_GRUPOIVA, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/grupoiva/search')),
			'nIdIdioma' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/idioma/search')), 
			'fImporte1'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'fImporte2'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),

			'tNotas'			=> array(),
			'cIdioma'			=> array(),
			'nIdWeb'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);

		parent::__construct('Cli_Clientes', 'nIdCliente', 'cEmpresa, cNombre, cApellido', array('cNombre', 'cApellido', 'cEmpresa'), $data_model, TRUE);
		#$this->_cache = FALSE;

		$this->_relations['descuentos'] = array (
			'ref'	=> 'clientes/m_descuento',
            'cascade' 	=> TRUE,
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdCliente');

		$this->_relations['descuentosgrupo'] = array (
			'ref'	=> 'clientes/m_grupoclientedescuento',
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdGrupoCliente');

		$this->_relations['tarifas'] = array (
			'ref'	=> 'clientes/m_clientetarifa',
            'cascade' 	=> TRUE,
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdCliente');
	}

	/**
	 * Obtiene el siguiente número de cuenta disponible entre 2 valores
	 * @param int $min Número máximo
	 * @param int $max Número mínimo
	 */
	function next_cuenta($min, $max)
	{
		$this->db->select("ISNULL(MAX(nIdCuenta) + 1, $min) nCuenta")
		->from($this->_tablename)
		->where("nIdCuenta > $min")
		->where("nIdCuenta < $max");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data[0]['nCuenta'];
	}

	/**
	 * Devuelve el perfil del tipo indciado del cliente del tipo de perfil indicado.
	 * Si no existe el tipo indicado se devuelve uno general.
	 * Si no existe el general se devuelve el primero que encuentra.
	 * @param int $id ID del cliente
	 * @param string $model Modelo de datos
	 * @param int $profile Tipo de perfil
	 */
	private function get_profile($id, $model, $profile = null)
	{
		return $this->utils->get_profile_model($id, 'nIdCliente', "clientes/{$model}", $profile);
	}

	/**
	 * Devuelve el email del cliente del tipo indicado.
	 * Si no existe el tipo indicado se devuelve uno general.
	 * Si no existe el general se devuelve el primero que encuentra.
	 * @param int $id ID del cliente
	 * @param int $profile Tipo de perfil
	 */
	function get_email($id, $profile = null)
	{
		return $this->get_profile($id, 'm_email', $profile);
	}

	/**
	 * Devuelve la dirección del cliente del tipo indicado.
	 * Si no existe el tipo indicado se devuelve uno general.
	 * Si no existe el general se devuelve el primero que encuentra.
	 * @param int $id ID del cliente
	 * @param int $profile Tipo de perfil
	 */
	function get_direccion($id, $profile = null)
	{
		return $this->get_profile($id, 'm_direccioncliente', $profile);
	}

	/**
	 * Cesta de la compra del cliente
	 * @param int $id Id del cliente
	 * @return array
	 */
	function cesta($id)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_fondo.nIdLibro, Cat_fondo.cTitulo, Cat_fondo.fPrecio')
		->select('customers_basket_quantity nCantidad, customers_basket_date_added dCreacion')
		->from('customers_basket')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = customers_basket.products_id')
		->where("customers_basket.customers_id = {$id}");
		$r = $this->db->get();
		$data = $this->_get_results($r);
		return $data;
	}

	/**
	 * Favoritos del cliente
	 * @param int $id Id del cliente
	 * @return array
	 */
	function favoritos($id)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_fondo.nIdLibro, Cat_fondo.cTitulo, Cat_fondo.fPrecio')
		->from('customers_wishlist')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = customers_wishlist.products_id')
		->where("customers_wishlist.customers_id = {$id}");
		$r = $this->db->get();
		$data = $this->_get_results($r);
		return $data;
	}

	/**
	 * Unificador de clientes
	 * @param int $id1 Id del cliente destino
	 * @param int $id2 Id del cliente repetida
	 * @return bool, TRUE: correcto, FALSE: incorrecto
	 */
	function unificar($id1, $id2)
	{
		set_time_limit(0);
		foreach($id2 as $k=>$v)
		{
			if ($id2[$k] == '') unset($id2[$k]);
		}
		$id_or = $id2;
		$id2 = implode(',', $id2);
		if ($id2 == '') return TRUE;

		$this->load->helper('unificar');

		$tablas[] = array('tabla' => 'Sus_Suscripciones');
		$tablas[] = array('tabla' => 'Sus_Reclamaciones');
		$tablas[] = array('tabla' => 'Doc_Facturas', 'model' => 'ventas/m_factura');
		$tablas[] = array('tabla' => 'Doc_Facturas2', 'model' => 'ventas/m_factura2');
		$tablas[] = array('tabla' => 'Doc_AlbaranesSalida', 'model' => 'ventas/m_albaransalida');
		$tablas[] = array('tabla' => 'Doc_AlbaranesSalida2', 'model' => 'ventas/m_albaransalida2');
		$tablas[] = array('tabla' => 'Doc_PedidosCliente', 'model' => 'ventas/m_pedidocliente');
		$tablas[] = array('tabla' => 'Cat_A_Examen');
		$tablas[] = array('tabla' => 'Cat_Suscripciones');
		$tablas[] = array('tabla' => 'Doc_Presupuestos');
		$tablas[] = array('tabla' => 'Cli_Descuentos', 'model' => 'clientes/m_descuento');
		$tablas[] = array('tabla' => 'Cli_Direcciones', 'model' => 'clientes/m_direcciondireccion');
		$tablas[] = array('tabla' => 'Cli_EMails', 'model' => 'clientes/m_email');
		$tablas[] = array('tabla' => 'Cli_Profiles');
		$tablas[] = array('tabla' => 'Cli_Telefonos', 'model' => 'clientes/m_telefono');
		$tablas[] = array('tabla' => 'Cli_Contactos', 'model' => 'ventas/m_contacto');
		$tablas[] = array('tabla' => 'Ext_EOIAlbaranesSalida');
		#$tablas[] = array('tabla' => 'customers_basket', 'id' => 'customers_id');
		$tablas[] = array('tabla' => 'Documentos', 'id' => 'nIdRegistro', 'where' => 'nIdTabla = 2', 'alter' => FALSE);
		$tablas[] = array('tabla' => 'Gen_Observaciones', 'id' => 'nIdRegistro', 'where' => 'cTabla=\'Cli_Clientes\'');
		$tablas[] = array('tabla' => 'Ext_EOISDepartamentos');
		$tablas[] = array('tabla' => 'Cli_DireccionesCliente', 'model' => 'clientes/m_clientedireccion');
		$tablas[] = array('tabla' => 'Cli_EMailsCliente', 'model' => 'clientes/m_clienteemail');
		$tablas[] = array('tabla' => 'Cli_TelefonosCliente', 'model' => 'clientes/m_clientetelefono');
		$tablas[] = array('tabla' => 'Cli_ContactosCliente', 'model' => 'ventas/m_clientecontacto');
		$tablas[] = array('tabla' => 'Sus_Clientes_Temas', 'model' => 'clientes/m_clientetema');

		// TRANS
		$this->db->trans_begin();

		foreach ($id_or as $id)
		{
			// Palabras Clave
			if (!unificar_nn($this, 'Sus_Clientes_Temas', 'nIdCliente', 'nIdTema', $id1, $id))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			// Tablas
			if (!unificar_do($this, $tablas, $id1, $id, 'nIdCliente'))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		// Borrado
		$this->db->flush_cache();
		$this->db->where("nIdCliente IN ({$id2})")
		->delete('Cli_Clientes');
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Limpieza de caches
		unificar_clear_cache($tablas);
		$this->clear_cache();

		// COMMIT
		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cli_TiposCliente.cDescripcion cTipoCliente')
			->select('Cli_GruposCliente.cDescripcion cGrupoCliente')
			->select('Cat_TiposTarifa.cDescripcion cTipoTarifa')
			->join('Cat_TiposTarifa', 'Cat_TiposTarifa.nIdTipoTarifa = Cli_Clientes.nIdTipoTarifa', 'left')
			->join('Cli_TiposCliente', 'Cli_TiposCliente.nIdTipoCliente=Cli_Clientes.nIdTipoCliente', 'left')
			->join('Cli_GruposCliente', 'Cli_GruposCliente.nIdGrupoCliente=Cli_Clientes.nIdGrupoCliente', 'left');

			if ($this->_addemail)
			{
				$this->db->select('Cli_Clientes.*, Cli_EMails.cEmail, Gen_TiposPerfil.cDescripcion cPerfil')
				->join('Cli_EMails', 'Cli_EMails.nIdCliente = Cli_Clientes.nIdCliente')
				->join('Gen_TiposPerfil', 'Gen_TiposPerfil.nIdTipo = Cli_EMails.nIdTipo');
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert($data)
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			//print_r($data);
			// Encripta password
			if (isset($data['cPass']))
			{
				$data['cPass'] = sha1($data['cPass']);
			}
			if (isset($data['nIdCuenta']) && ($data['nIdCuenta'] == ''))
			{
				$data['nIdCuenta'] = null;
			}
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
		if (parent::onBeforeUpdate($id, $data))
		{
			// Encripta password
			if (isset($data['cPass']))
			{
				$data['cPass'] = sha1($data['cPass']);
			}
			if (isset($data['nIdCuenta']) && ($data['nIdCuenta'] == ''))
			{
				$data['nIdCuenta'] = null;
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSearch($where, $fields)
	 */
	protected function onBeforeSearch($query, &$where, &$fields)
	{
		if (parent::onBeforeSearch($query, $where, $fields))
		{
			//Si es un ISBN lo añade a la búsqueda
			$query = trim($query);
			if (is_email($query))
			{
				$where = "{$this->_tablename}.nIdCliente IN (SELECT Cli_EMails.nIdCliente FROM Cli_EMails WHERE Cli_EMails.cEmail = " . $this->db->escape($query) . ")";
			} 
			elseif (is_phone($query))
			{				
				$where2 = "{$this->_tablename}.nIdCliente IN (SELECT Cli_Telefonos.nIdCliente FROM Cli_Telefonos WHERE REPLACE(REPLACE(REPLACE(Cli_Telefonos.cTelefono, ' ', ''), '.', ''), '-','') = " . $this->db->escape(clean_phone($query)) . ")";
				$where =(!empty($where))?('(' . $where . ') OR (' . $where2 . ')'):$where2;
			}
			#var_dump($where); die();

			return TRUE;
		}
		return FALSE;
	}
}
/* End of file M_cliente.php */
/* Location: ./system/application/models/cliente/M_cliente.php */