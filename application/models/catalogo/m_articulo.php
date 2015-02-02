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
 * @filesource
 */

/**
 * Estado por defecto de un artículo
 * @var int
 */
define('ARTICULO_DEFAULT_STATE', 3);

/**
 * Tipo de artículo por defecto
 * @var int
 */
define('DEFAULT_ARTICULO_TIPO', 1);

/**
 * Mostrar Web por defecto, si/no
 * @var bool
 */
define('ARTICULO_DEFAULT_MOSTRAR_WEB', TRUE);

/**
 * Oferta de precio fijo
 * @var int
 */
define('OFERTA_PRECIOFIJO', 2);
/**
 * Oferta de precio fijo
 * @var int
 */
define('OFERTA_DESCUENTO', 1);
/**
 * Artículos
 *
 */
class M_articulo extends MY_Model
{
	private $_addcodigos = FALSE;
	private $_where = null;
	/**
	 * Constructor
	 * @return M_articulo
	 */
	function __construct()
	{
		$data_model = array(
			'cISBN' 				=> array(),
			'cISBN10' 				=> array(),
			'cISBNBase' 			=> array(),
			'cISBNBase10' 			=> array(),
			'nEAN' 					=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'cTitulo' 				=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_DEFAULT => TRUE),
			'cAutores' 				=> array(),
			'nIdEditorial' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/editorial/search', 'cEditorial')), 
			'nIdTipo' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => DEFAULT_ARTICULO_TIPO ,DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/tipolibro/search')), 
			'cEdicion' 				=> array(),
			'nPag' 					=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'fPeso' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
			'nIdEncuadernacion' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/encuadernacion/search')),
			'nVolumenes' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdColeccion' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/coleccion/search')), 
			'cNColeccion' 			=> array(),
			'nIdColeccion2' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/coleccion/search')),
			'cNColeccion2' 			=> array(),
			'nIdColeccion3' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/coleccion/search')),
			'cNColeccion3' 			=> array(),
			'nColVol' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'fPrecio' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
			'fPrecioCompraBase' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE, DATA_MODEL_DEFAULT_VALUE => 0),
			'fPrecioOriginal' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE, DATA_MODEL_DEFAULT_VALUE => 0),
			'fUltimoPrecioCompra' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
			'bPrecioLibre' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'fPrecioProveedor' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
			'dFechaPrecioProveedor' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
			'nIdDivisaProveedor' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'generico/divisa/search')), 
			'fPrecioCompra' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE, DATA_MODEL_DEFAULT_VALUE => 0),
			'bNov' 					=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bNovLb' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'dFinNov' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
			'nIdOferta' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/oferta/search')), 
			'dEdicion' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'nIdEstado' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => ARTICULO_DEFAULT_STATE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/estadolibro/search')),
			'nIdProveedor' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'compras/proveedor/search')),
			'nIdProveedorManual' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'compras/proveedor/search')), 
			'nIdPlazoEnvio' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/plazoenvio/search')), 
			'nIdPlazoEnvioManual' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/plazoenvio/search')), 
			'nDiasEnvio' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'nIdIdioma' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'generico/idioma/search')), 
			'cMedidas' 				=> array(),
			'bMostrarWeb' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => ARTICULO_DEFAULT_MOSTRAR_WEB),
			'bMostrarWebManual' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'dUltimaCompra' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
			'dUltimaVenta' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
			'nIdLibroEdicionAnterior' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'bNoDto' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
		);

		#$this->_cache = FALSE;

		$name = array('cTitulo', 'nEAN', 'cAutores');
		parent::__construct('Cat_Fondo', 'nIdLibro', 'cTitulo', $name, $data_model, TRUE);
		$this->_deleted = TRUE;

		$this->_relations['editorial'] = array (
			'ref'	=> 'catalogo/m_editorial',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdEditorial');

		$this->_relations['coleccion'] = array (
			'ref'	=> 'catalogo/m_coleccion',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdColeccion');

		$this->_relations['tipo'] = array (
			'ref'	=> 'catalogo/m_tipolibro',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdTipo');

		$this->_relations['idioma'] = array (
			'ref'	=> 'generico/m_idioma',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdIdioma');

		$this->_relations['sinopsis'] = array (
			'ref'	=> 'catalogo/m_sinopsis',
            'cascade' 	=> TRUE,
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdLibro');

		$this->_relations['proveedor'] = array (
			'ref'	=> 'proveedores/m_proveedor',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdProveedor');

		$this->_relations['proveedormanual'] = array (
			'ref'	=> 'proveedores/m_proveedor',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdProveedorManual');

		$this->_relations['encuadernacion'] = array (
			'ref'	=> 'catalogo/m_encuadernacion',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdEncuadernacion');		

		$this->_relations['plazosenvio'] = array (
			'ref'	=> 'compras/m_plazoenvio',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdPlazoEnvio');

		$this->_relations['plazosenviomanual'] = array (
			'ref'	=> 'compras/m_plazoenvio',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdPlazoEnvioManual');

		$this->_relations['plazosenviomanual'] = array (
			'ref'	=> 'catalogo/m_coleccion',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdColeccion');

		$this->_relations['divisa'] = array (
			'ref'	=> 'generico/m_divisa',
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdDivisaProveedor');

		$this->_relations['autores'] = array (
			'ref'	=> 'catalogo/m_articuloautor',
            'cascade' 	=> TRUE,
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdLibro');

		$this->_relations['palabrasclave'] = array (
			'ref'	=> 'catalogo/m_articulopalabraclave',
            'cascade' 	=> TRUE,
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdLibro');

		$this->_relations['materias'] = array (
			'ref'	=> 'catalogo/m_articulomateria',
			'type'	=> DATA_MODEL_RELATION_1N,
            'cascade' 	=> TRUE,
			'fk'	=> 'nIdLibro');

		$this->_relations['secciones'] = array (
			'ref'	=> 'catalogo/m_articuloseccion',
            'cascade' 	=> TRUE,
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdLibro');		

		$this->_relations['ubicaciones'] = array (
			'ref'	=> 'catalogo/m_articuloubicacion',
			'type'	=> DATA_MODEL_RELATION_1N,
            'cascade' 	=> TRUE,
			'fk'	=> 'nIdLibro');		

		$this->_relations['tarifas'] = array (
			'ref'	=> 'catalogo/m_articulotarifa',
            'cascade' 	=> TRUE,
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdLibro');		

		$this->_relations['revista'] = array (
			'ref'	=> 'catalogo/m_revista',
            'cascade' 	=> TRUE,
			'type'	=> DATA_MODEL_RELATION_11,
			'fk'	=> 'nIdLibro');		

		$this->_relations['codigos'] = array (
			'ref'	=> 'catalogo/m_articulocodigo',
			'type'	=> DATA_MODEL_RELATION_1N,
            'cascade' 	=> TRUE,
			'fk'	=> 'nIdLibro');
	}

	/**
	 * Borra las imágenes de la cache
	 * @param int $id Id del atículo
	 */
	private function _delete_cover($id)
	{
		$files = sdir(DIR_COVERS_PATH, "{$id}.*");
		if(count($files) > 0)
		{
			foreach ($files as $i)
			{
				unlink(DIR_COVERS_PATH . $i);
			}
		}
		$files = sdir(DIR_THUMB_PATH, "{$id}.*");
		if(count($files) > 0)
		{
			foreach ($files as $i)
			{
				unlink(DIR_THUMB_PATH . $i);
			}
		}
	}

	/**
	 * Actualiza la portada de un artículo
	 * @param int $id ID del artículo
	 * @param string $url URI de la imagen
	 * @return MSG
	 */
	function set_portada($id, $url = null, $file = null, $dAct = null)
	{
		#$this->db->trans_begin();

		if (is_file($url))
		{
			$file = $url;
			$url = null;
		}
		if (isset($url) || isset($file))
		{
			if (isset($url))
			{
				$this->obj->load->library('SearchImages');
				//Descarga
				$res = $this->obj->searchimages->download($url);
			}
			else
			{
				$parts = pathinfo($file);
				$res['file'] = $file;
				$res['ext'] = $parts['extension']; 
			}
			if (isset($res))
			{
				$res['ext'] = strtolower($res['ext']);
				$this->obj->load->library('SearchImages');
				# Convierte a JPG
				if (in_array($res['ext'], array('gif', 'tif', 'tiff', 'bmp', 'psd', 'pdf', 'png')))
				{
					$res['file'] = $this->searchimages->to_jpeg($res['file']);
					$res['ext'] = 'jpg';
				}
				#var_dump($res); die();
				# Solo formartos WEB
				if (!in_array($res['ext'], array('jpeg', 'jpg', 'png')))
				{
					$this->_set_error_message(sprintf($this->lang->line('Formato de imagen no soportado'), $res['ext']));
					#$this->db->trans_rollback();
					return FALSE;					
				}
				# Optimiza
				$this->searchimages->optimize($res['file']);
				# Elimina anterior
				$this->db->flush_cache();
				$this->db->where("nIdRegistro={$id}")->delete('Fotos');
				
				# Inserta la nueva en la base de datos
				$data['nIdRegistro'] = $id;
				$data['cDescripcion'] = 'portada';
				$data['cExtension'] = $res['ext'];
				$data['nIdTabla'] = 5;
				$data['bBase64'] = 1;
				$data['nFotoSize'] = filesize($res['file']);
				if ($data['nFotoSize']==0)
				{
					$this->_set_error_message($this->lang->line('Fichero incorrecto'));
					#$this->db->trans_rollback();
					return FALSE;
				}

				$data['dAct'] = (isset($dAct)?format_todate($dAct):format_todate(time()));
				$data['dCreacion'] = format_todate(time());
				$data['tImagen'] = base64_encode(addslashes(fread(fopen($res['file'], "r"), $data['nFotoSize'])));
	
				if (!$this->db->insert('Fotos', $data))
				{
					$this->_set_error_message($this->db->error_message());
					#$this->db->trans_rollback();
					return FALSE;
				}
				// Elimina la cache
				$this->_delete_cover($id);
	
				// Crea la caché
				$this->db->flush_cache();
				$this->db->select('cExtension')
				->select($this->_date_field('dAct', 'dAct'))
				->from('Fotos')
				->where("nIdRegistro = {$id}");
				$query = $this->db->get();
				$result = $query->row_array();
	
				$data = $this->_get_filenames($id, $result['cExtension']);
				$file = $data['file'];
				if ($res['file'] != $file) rename($res['file'], $file);
				$this->_set_stamp($file, $result['dAct']);
			}
		}
		else
		{
			$this->db->flush_cache();
			$this->db->where("nIdRegistro = {$id}")
			->delete('Fotos');
			if ($this->_check_error())
			{
				#$this->db->trans_rollback();
				return FALSE;
			}

			// Elimina la cache
			$this->_delete_cover($id);
		}
		#$this->db->trans_commit();

		return TRUE;
	}

	/**
	 * Comprueba si existe un archivo y crea el stamp
	 * @param string $file Fichero a comprobar
	 * @param int $stamp Valor de stamp
	 * @return bool, TRUE: está, FALSE: no está o está fuera de sincronismo
	 */
	private function _file_exists($file, $stamp)
	{
		$esta = FALSE;
		$filename_stamp =  $file . '.stamp';

		if (file_exists($filename_stamp))
		{
			// Lee el stamp
			$fs = fopen($filename_stamp, 'r');
			$stamp_act = fgets($fs, 4096);
			fclose($fs);

			if (($stamp == $stamp_act))
			{
				// Está el fichero en caché?
				$esta = file_exists($file);
				if ($esta) $esta = (filesize($file) > 0);
			}
		}

		return $esta;
	}

	/**
	 * Añade la firma de stampo al fichero
	 * @param string $file Fichero
	 * @param int $stamp Valor del stamp
	 */
	private function _set_stamp($file, $stamp)
	{
		$filename_stamp =  $file . '.stamp';
		$fs = fopen($filename_stamp, 'w');
		fwrite($fs, $stamp);
		fclose($fs);
	}

	/**
	 * Devuelve los nombres de los ficheros de trabajo para las portadas
	 * @param int $id Id del artículo
	 * @param string $extension Extensión de la imagen
	 * @param string $thumbname Nombre del thumb
	 */
	private function _get_filenames($id, $extension, $thumbname = null)
	{
		$filename = $id . '.' . $extension;
		$file = DIR_COVERS_PATH . $filename;

		$data = array(
			'filename' 	=> $filename,
			'file'		=> $file
		);

		if (isset($thumbname))
		{
			$filename_thumb = $id . '.' . $thumbname . '.' . $extension;
			$file_thumb = DIR_THUMB_PATH . $filename_thumb;
			$data['filename_thumb']	= $filename_thumb;
			$data['file_thumb']		= $file_thumb;
		}

		return $data;
	}

	/**
	 * Devuelve la portada del artículo
	 * @param int $id Id del artículo
	 * @param int $thumbsize Tamaño de la imagen (null para tamaño real)
	 * @return array, 'file' => Nombre del fichero, 'filename' => path completo, 'ext' => Extensión
	 */
	function get_portada($id, $thumbsize = null)
	{
		$thumb = (is_numeric($thumbsize) && ($thumbsize > 0));
		$thumbname = ($thumb)?"-{$thumbsize}px-":'';

		// Comprueba si hay imagen
		$this->db->flush_cache();
		$this->db->select('cExtension, bBase64')
		->select($this->_date_field('dAct', 'dAct'))
		->from('Fotos')
		->where("nIdRegistro = {$id}");
		$query = $this->db->get();
		$result = $query->row_array();
		if (count($result) == 0)
		{
			// No hay imagen, devuelve la blanca
			$filename = $this->obj->config->item('bp.catalogo.nocover');
			$ext = $this->obj->config->item('bp.catalogo.nocover.extension');

			$this->obj->load->helper('asset');
			$file = image_asset_path($filename);
			if ($thumb)
			{
				$filename_thumb = $thumbname . $filename;
				$file_thumb = DIR_THUMB_PATH . $filename_thumb;
				$time = filemtime($file);
				$esta = $this->_file_exists($file_thumb, $time);
				// Existe la grande, pero no el thumb?
				if (!$esta)
				{
					$this->obj->load->library('SearchImages');
					$this->obj->searchimages->thumbnail($file, $file_thumb, $ext, $thumbsize);
					$this->_set_stamp($file_thumb, $time);
				}
			}

			return array(
				'file' 		=> ($thumb)?$file_thumb:$file, 
				'filename' 	=> ($thumb)?$file_thumb:$filename, 
				'ext' 		=> $ext
			);
		}

		//
		// Crea la imagen
		//
		$data = $this->_get_filenames($id, $result['cExtension'], $thumbname);
		$filename 		= $data['filename'];
		$filename_thumb = $data['filename_thumb'];
		$file 			= $data['file'];
		$file_thumb 	= $data['file_thumb'];

		// Crea la imagen estándar
		$esta = $this->_file_exists($file, $result['dAct']);
		#var_dump($esta);
		if (!$esta)
		{
			$this->db->flush_cache();
			$this->db->select('cExtension, bBase64')
			->select($this->_date_field('dAct', 'dAct'))
			->from('Fotos')
			->where("nIdRegistro = {$id}");
			if ($result['bBase64'] == 1)
			{
				$this->db->select('tImagen');
			}
			else
			{
				$this->db->select('imgFoto');
			}
			$query = $this->db->get();
			$result = $query->row_array();

			$fs = fopen($file, 'wb');
			if ($result['bBase64'] == 1)
			{
				fwrite($fs, stripslashes(base64_decode($result['tImagen'])));
			}
			else
			{
				fwrite($fs, $result['imgFoto']);
			}
			fclose($fs);

			// Guarda la caché
			$this->_set_stamp($file, $result['dAct']);
		}

		//
		// Crea el thumb
		//
		if ($thumb)
		{
			$esta = FALSE;
			$esta = $this->_file_exists($file_thumb, $result['dAct']);
			#var_dump($esta);
			// Existe la grande, pero no el thumb?
			if (!$esta)
			{
				$this->obj->load->library('SearchImages');
				$this->obj->searchimages->thumbnail($file, $file_thumb, $result['cExtension'], $thumbsize);
				$this->_set_stamp($file_thumb, $result['dAct']);
			}
		}
		//
		// Devuelve la imagen
		//
		return array(
			'file' 		=> ($thumb)?$file_thumb:$file, 
			'filename' 	=> ($thumb)?$file_thumb:$filename, 
			'ext' 		=> $result['cExtension']
		);
	}

	/**
	 * Precios de un artículo
	 * @param int $id Id del artículo
	 */
	function get_avisos($id)
	{
		// Pedidos
		$this->db->flush_cache();
		$this->db->select('lp.nIdSeccion nIdSeccion')
		->select('s.cNombre cSeccion')
		->select('lp.nIdLinea id')
		->select('lp.fPrecio fPrecio')
		->select('lp.nCantidad nCantidad')
		->select('c.nIdCliente')
		->select('c.cNombre, c.cApellido, c.cEmpresa')
		->select('lp.cCUser, lp.nIdPedido, lp.nIdLinea, lp.nCantidadServida')
		->select('st.cDescripcion cEstado')
		->select($this->_date_field('lp.dCreacion',	'dFecha'))
		->select('lp.fDescuento	fDescuento,lp.nIdEstado')
		->from('Doc_LineasPedidoCliente lp')
		->join('Doc_PedidosCliente p', 'lp.nIdPedido = p.nIdPedido')
		->join('Cat_Secciones s', 'lp.nIdSeccion = s.nIdSeccion')
		->join('Cli_Clientes c', 'c.nIdCliente = p.nIdCliente')
		->join('Doc_EstadosLineaPedidoCliente st', 'lp.nIdEstado = st.nIdEstado')
		->where('lp.nIdEstado IN (1, 5, 6, 2, 3)')
		->where('lp.nIdLibro = ' . $id);
		
		$query = $this->db->get();
		return $this->_get_results($query);
	}

	/**
	 * Precios de un artículo
	 * @param int $id Id del artículo
	 * @return array
	 */
	function get_precios($id)
	{
		$this->db->flush_cache();
		$this->db->select('fPrecioAntiguo, fPrecioNuevo, cCUser')
		->select($this->_date_field('dCambio', 'dCambio'))
		->from('Cat_CambiosPrecio')
		->where("nIdLibro = {$id}")
		->order_by('dCambio DESC');
		$query = $this->db->get();
		return $this->_get_results($query);
	}

	/**
	 * Devuelve el rtock contado en el inventario
	 * @param int $id Id del artículo
	 * @return array
	 */
	function get_stockcontado($id)
	{
		$this->db->flush_cache();
		$this->db->select('s.cNombre, ts.cDescripcion, rs.nCantidad, rs.bDone')
		//->select($this->_date_field('rs.dCreacion' , 'dCreacion'))
		->from('Cat_RegulacionStock rs')
		->join('Cat_TiposStock ts', 'rs.nIdTipoStock = ts.nIdTipoStock')
		->join('Cat_Secciones s', 'rs.nIdSeccion = s.nIdSeccion')
		->where("rs.nIdLibro = {$id}")
		->order_by('s.cNombre');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Obtiene el descuento de un artículo para un proveedor dado, sino se indica el proveedor
	 * devuelve el descuento del proveedor habitual
	 * @param int $id Id del artículo
	 * @param int $idp Id del proveedor
	 * @return float
	 */
	function get_descuento($id, $idp = null)
	{
		$pv = $this->get_proveedores($id);
		if (isset($idp))
		{
			foreach ($pv as $p)
			{
				if ($p['nIdProveedor'] == $idp) return isset($p['fDescuento'])?$p['fDescuento']:0;
			}
			return 0;
		}
		// el default
		foreach($pv as $p)
		{
			if ($p['default'])
			{
				return $p['fDescuento'];
			}
		}
		return 0;
	}

	/**
	 * Devuelve el proveedor habitual basado en los datos del libro
	 * @param array $datos Datos del libro
	 * @return int
	 */
	function get_proveedor_habitual($datos)
	{
		return (isset($datos['nIdProveedor']))?$datos['nIdProveedor']:((isset($datos['nIdProveedor2']))?$datos['nIdProveedor2']:null);
	}

	/**
	 * Busca los últimos documentos del artículo
	 * @param int $id Id del libro
	 * @param int $ids Id de la sección
	 * @return array
	 */
	function get_last_docs($id, $ids = null)
	{
		$datos = array();
		// Entrada
		$this->db->flush_cache();
		#$this->db->select_max($this->_date_field('a.dCierre'), 'dFecha')
		$this->db->select_max('a.nIdAlbaran', 'nIdAlbaran')
		->from('Doc_AlbaranesEntrada a')
		->join('Doc_LineasAlbaranesEntrada la', 'a.nIdAlbaran = la.nIdAlbaran')
		->where("la.nIdLibro = {$id}")
		->where('a.nIdEstado=4')
		->group_by('la.nIdLibro');
		if (isset($ids))
		{
			$this->db->join('Doc_LineasPedidosRecibidas las', 'las.nIdLineaAlbaran = la.nIdLinea')
			->join('Doc_LineasPedidoProveedor pd', 'las.nIdLineaPedido = pd.nIdLinea')
			->where("pd.nIdSeccion = {$ids}");
		}
		$query = $this->db->get();
		$d = $this->_get_results($query);
		if (isset($d[0]))
		{
			$obj = get_instance();
			$obj->load->model('compras/m_albaranentrada');
			$alb = $obj->m_albaranentrada->load($d[0]['nIdAlbaran']);
			$datos['entrada'] = $alb;
		}

		// Salida
		$this->db->flush_cache();
		$this->db->select_max($this->_date_field('ISNULL(f.dFecha, a.dCreacion)'), 'dFecha')
		->from('Doc_AlbaranesSalida a')
		->join('Doc_LineasAlbaranesSalida la', 'a.nIdAlbaran = la.nIdAlbaran')
		->join('Doc_Facturas f', 'f.nIdFactura = a.nIdFactura', 'left')
		->where("la.nIdLibro = {$id}")
		->group_by('la.nIdLibro');
		if (isset($ids))
		{
			$this->db->where("la.nIdSeccion = {$ids}");
		}
		$query = $this->db->get();
		$d = $this->_get_results($query);
		#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();
		if (isset($d[0])) $datos['salida'] = $d[0]['dFecha'];

		// Devolución
		$this->db->flush_cache();
		$this->db->select_max($this->_date_field('a.dCierre'), 'dFecha')
		->from('Doc_Devoluciones a')
		->join('Doc_LineasDevolucion la', 'a.nIdDevolucion = la.nIdDevolucion')
		->where("la.nIdLibro = {$id}")
		->group_by('la.nIdLibro');
		if (isset($ids))
		{
			$this->db->where("la.nIdSeccion = {$ids}");
		}
		$query = $this->db->get();
		$d = $this->_get_results($query);
		if (isset($d[0])) $datos['devolucion'] = $d[0]['dFecha'];

		// Pedidos a Proveedor APedir
		$this->db->flush_cache();
		$this->db->select_max($this->_date_field('la.dAct'), 'dFecha')
		->from('Doc_PedidosProveedor a')
		->join('Doc_LineasPedidoProveedor la', 'a.nIdPedido = la.nIdPedido')
		->where("la.nIdLibro = {$id}")
		->where('la.nIdEstado=1')
		->group_by('la.nIdLibro');
		if (isset($ids))
		{
			$this->db->where("la.nIdSeccion = {$ids}");
		}
		$query = $this->db->get();
		$d = $this->_get_results($query);
		if (isset($d[0])) $datos['apedir'] = $d[0]['dFecha'];

		// Pedidos a Proveedor Pedido
		$this->db->flush_cache();
		$this->db->select_max($this->_date_field('a.dFechaEntrega'), 'dFecha')
		->from('Doc_PedidosProveedor a')
		->join('Doc_LineasPedidoProveedor la', 'a.nIdPedido = la.nIdPedido')
		->where("la.nIdLibro = {$id}")
		->where('la.nIdEstado IN (4, 2)')
		->group_by('la.nIdLibro');
		if (isset($ids))
		{
			$this->db->where("la.nIdSeccion = {$ids}");
		}
		$query = $this->db->get();
		$d = $this->_get_results($query);
		if (isset($d[0])) $datos['pendiente'] = $d[0]['dFecha'];

		return $datos;
	}

	/**
	 * Obtiene el listado de proveedores de un artículo
	 * @param int $id Id del artículo
	 * @return array
	 */
	function get_proveedores($id)
	{
		// Libro
		$this->db->flush_cache();
		$this->db->select("{$this->_tablename}.nIdProveedor, Cat_Editoriales.nIdProveedor nIdProveedor2, nIdTipo, {$this->_tablename}.nIdEditorial")
		->from($this->_tablename)
		->join("Cat_Editoriales", "Cat_Editoriales.nIdEditorial = {$this->_tablename}.nIdEditorial" ,'left')
		->where("nIdLibro =  {$id}");
		$query = $this->db->get();
		$libro = $this->_get_results($query);
		if (isset($libro[0])) $libro = $libro[0];
		$hab = $this->get_proveedor_habitual($libro);

		$obj = get_instance();
		$proveedores = array();

		// Proveedores de la editorial
		if (isset($libro['nIdEditorial']))
		{
			$obj->load->model('catalogo/m_proveedoreditorial');
			
			$prv2 = (isset($libro['nIdTipo']))?$obj->m_proveedoreditorial->get(null, null, null, null, "nIdEditorial={$libro['nIdEditorial']} AND nIdTipo={$libro['nIdTipo']}"):null;
			if (count($prv2) > 0)
			{
				foreach($prv2 as $p)
				{
					$p['default'] = ($hab == $p['nIdProveedor']);
					$p['disabled'] = ($p['bDisabled'] == 1);
					$p['origen'] = 'editorial';
					$p['text'] = format_name($p['cNombre'], $p['cApellido'], $p['cEmpresa']);
					#$p['text'] = trim(isset($p['cEmpresa'])?$p['cEmpresa']:((isset($p['cNombre'])?$p['cNombre']:'') . (isset($p['cApellido'])?' ' . $p['cApellido']:'')));
					$proveedores[$p['nIdProveedor']] = $p;
				}
			}
			else
			{
				// No hay para el tipo dado, coge el general
				$prv2 = $obj->m_proveedoreditorial->get(null, null, null, null, "nIdEditorial={$libro['nIdEditorial']} AND nIdTipo IS NULL");
				if (count($prv2) > 0)
				{
					foreach($prv2 as $p)
					{
						$p['default'] = ($hab == $p['nIdProveedor']);
						$p['disabled'] = ($p['bDisabled'] == 1);
						$p['origen'] = 'editorial';
						$p['text'] = format_name($p['cNombre'], $p['cApellido'], $p['cEmpresa']);
						#$p['text'] = trim(isset($p['cEmpresa'])?$p['cEmpresa']:((isset($p['cNombre'])?$p['cNombre']:'') . (isset($p['cApellido'])?' ' . $p['cApellido']:'')));
						$proveedores[$p['nIdProveedor']] = $p;
					}
				}
			}
		}

		// Proveedores del libro
		$obj->load->model('catalogo/m_proveedorarticulo');
		$prv = $obj->m_proveedorarticulo->get(null, null, null, null, "nIdLibro = {$id}");
		foreach($prv as $p)
		{
			$p['default'] = ($hab == $p['nIdProveedor']);
			$p['disabled'] = ($p['bDisabled'] == 1);
			$p['origen'] = 'libro';
			$p['text'] = format_name($p['cNombre'], $p['cApellido'], $p['cEmpresa']);
			#$p['text'] = trim(isset($p['cEmpresa'])?$p['cEmpresa']:((isset($p['cNombre'])?$p['cNombre']:'') . (isset($p['cApellido'])?' ' . $p['cApellido']:'')));
			$proveedores[$p['nIdProveedor']] = $p;
		}
		//print $hab;
		if (!isset($proveedores[$hab]) && isset($hab))
		{
			$obj->load->model('proveedores/m_proveedor');
			$p = $obj->m_proveedor->load($hab);
			$p['default'] = TRUE;
			$p['disabled'] = ($p['bDisabled'] == 1);
			$p['origen'] = 'libro';
			$p['text'] = format_name($p['cNombre'], $p['cApellido'], $p['cEmpresa']);
			#trim(isset($p['cEmpresa'])?$p['cEmpresa']:((isset($p['cNombre'])?$p['cNombre']:'') . (isset($p['cApellido'])?' ' . $p['cApellido']:'')));
			$proveedores[$hab] = $p;
		}
		$prv = array();

		# Proveedores a los que se ha comprado
		$this->db->flush_cache();
		$this->db->select('Prv_Proveedores.nIdProveedor, Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa')
		->select('Prv_Proveedores.bDisabled')
		->select('Doc_LineasAlbaranesEntrada.fCoste, Doc_LineasAlbaranesEntrada.fGastos, Doc_LineasAlbaranesEntrada.fPrecio, Doc_LineasAlbaranesEntrada.fDescuento fUltDescuento, Doc_LineasAlbaranesEntrada.fPrecioVenta')
		->select($this->_date_field('Doc_LineasAlbaranesEntrada.dCreacion', 'dCreacion'))
		->from('Prv_Proveedores_Fondo_Compras')
		->join('Doc_LineasAlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdLinea=Prv_Proveedores_Fondo_Compras.nIdLinea')
		->join('Prv_Proveedores', 'Prv_Proveedores.nIdProveedor=Prv_Proveedores_Fondo_Compras.nIdProveedor')		
		->where('Prv_Proveedores_Fondo_Compras.nIdLibro='  . $id);
		$query = $this->db->get();
		$datos = $this->_get_results($query);
		foreach($datos as $p)
		{
			$p['text2'] = sprintf($this->lang->line('pedir-proveedor-precios'), 
				format_date($p['dCreacion']),
				format_price($p['fPrecio']), 
				format_percent($p['fUltDescuento']), 
				format_price($p['fCoste']), 
				format_price($p['fGastos']), 
				format_price($p['fPrecioVenta']));
	
			if (isset($proveedores[$p['nIdProveedor']]))
			{
				$proveedores[$p['nIdProveedor']] = array_merge($proveedores[$p['nIdProveedor']], $p);
			}
			else
			{
				$p['id'] = $p['nIdProveedor'];
				$p['fDescuento'] = $p['fUltDescuento'];
				$p['default'] = FALSE;
				$p['disabled'] = ($p['bDisabled'] == 1);
				$p['origen'] = 'compras';
				$p['text'] = format_name($p['cNombre'], $p['cApellido'], $p['cEmpresa']);
				$proveedores[$p['nIdProveedor']] = $p;
			}
		}
		foreach($proveedores as $p)
		{
			if (!isset($p['text2'])) $p['text2'] = $this->lang->line('pedir-proveedor-precios-sin');
			$prv[] = $p;
		}
		#var_dump($datos, $prv); die();
		return $prv;
	}

	/**
	 * Obtiene la antigüedad de un artículo
	 * @param  int $id    Id del artículo
	 * @param  date $desde Fecha desde la que mostrar la antigüedad
	 * @return array
	 */
	function get_antiguedad($id, $desde = null)
	{
		$_prefix = $this->config->item('bp.oltp.database');
		$this->db->flush_cache();
		$this->db->select('	cTitulo, nDeposito, nFirme1, nFirme2, nFirme3, nFirme4,
			fCoste,
			nEntradas1, nEntradas2, nEntradas3, nEntradas4')
		->select($this->_date_field('dCreacion', 'dCreacion'))
		->from("{$_prefix}Ext_AntiguedadStock a")
		->join("{$_prefix}Ext_AntiguedadStockVolcados b", "a.nIdVolcado = b.nIdVolcado")
		->where("nIdLibro = {$id}")
		->order_by('dCreacion', 'DESC');
		if (!empty($desde))
		{
			$desde= format_mssql_date($desde);
			$this->db->where("dCreacion >= {$desde}");
		}

		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}

	/**
	 * Documentos de un artículo
	 * @param int $id Id del artículo
	 * @param date $fecha1 Fecha desde
	 * @param date $fecha2 Fecha hasta
	 * @param int $ids Id de la sección
	 * @param mixed $tipo array: lista de listados a mostrar, TRUE: todos, string: uno solo
	 * @return array
	 */
	function get_documentos($id = null, $fecha1 = null, $fecha2 = null, $ids = null, $tipo = TRUE, $idcl = null, $idpv = null)
	{
		if (is_string($tipo)) $tipo = array($tipo);
		$todo = ($tipo === TRUE);
		if ($todo) $tipo = array();

		if (!empty($fecha1)) $fecha1 = format_mssql_date($fecha1);
		if (!empty($fecha1)) $fecha2 = format_mssql_date($fecha2);

		$clpv = is_numeric($idcl) || is_numeric($idpv);

		$data = array();

		// 1 -Suscripciones
		if (($todo || in_array('docsus', $tipo)) && (($clpv && is_numeric($idcl)) || !$clpv))
		{
			$this->db->flush_cache();
			$this->db->select("'docsus' tipo")
			->select('lp.nIdRevista nIdLibro')
			->select('lp.nIdSuscripcion id')
			->select('lp.fPrecio fPrecio')
			->select('lp.nEjemplares nCantidad')
			->select('c.nIdCliente nIdCl, c.cNombre, c.cApellido, c.cEmpresa')
			->select('lp.cCUser')
			->select('NULL ES')
			->select($this->_date_field('lp.dCreacion',	'dFecha'))
			->select('0	fDescuento')
			->from('Sus_Suscripciones lp')
			->join('Cli_Clientes c', 'lp.nIdCliente = c.nIdCliente');
			if (is_numeric($id)) $this->db->where('lp.nIdRevista = ' . $id);
			if ($clpv)
			{
				$this->db->select('Cat_Fondo.cTitulo');
				$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = lp.nIdRevista');
			}
			if (is_numeric($idcl))
			{
				$this->db->where("c.nIdCliente = {$idcl}");
			}

			if (!empty($fecha1)) $this->db->where('lp.dCreacion >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("lp.dCreacion < " . $this->db->dateadd('d', 1, $fecha2));

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			$data = array_merge($data, $data2);
		}

		// 2-Presupuestos
		if (($todo || in_array('docpre', $tipo))  && (($clpv && is_numeric($idcl)) || !$clpv))
		{
			$this->db->flush_cache();
			$this->db->select("'docpre' tipo")
			->select('lp.nIdLibro nIdLibro')
			->select('lp.nIdSeccion nIdSeccion')
			->select('s.cNombre cSeccion')
			->select('lp.nIdPedido id')
			->select('Doc_EstadosLineaPedidoCliente.cDescripcion')
			->select('lp.fPrecio fPrecio')
			->select('lp.nCantidad nCantidad')
			->select('lp.cCUser')
			->select('c.nIdCliente nIdCl, c.cNombre, c.cApellido, c.cEmpresa')
			->select('NULL ES')
			->select($this->_date_field('lp.dCreacion',	'dFecha'))
			->select('lp.fDescuento	fDescuento')
			->from('Doc_LineasPedidoCliente lp')
			->join('Doc_PedidosCliente p', 'lp.nIdPedido = p.nIdPedido')
			->join('Cli_Clientes c', 'p.nIdCliente = c.nIdCliente')
			->join('Doc_EstadosLineaPedidoCliente', 'lp.nIdEstado = Doc_EstadosLineaPedidoCliente.nIdEstado')
			->join('Cat_Secciones s', 'lp.nIdSeccion = s.nIdSeccion')
			->where('p.nIdEstado=3');
			if (is_numeric($id)) $this->db->where('lp.nIdLibro = ' . $id);
			if ($clpv)
			{
				$this->db->select('Cat_Fondo.cTitulo');
				$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = lp.nIdLibro');
			}
			if (is_numeric($idcl))
			{
				$this->db->where("c.nIdCliente = {$idcl}");
			}

			if (!empty($fecha1)) $this->db->where('lp.dCreacion >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("lp.dCreacion < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('s.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			$data = array_merge($data, $data2);
		}
		/*
		if (($todo || in_array('docpre', $tipo)) && (($clpv && is_numeric($idcl)) || !$clpv))
		{
			$this->db->flush_cache();
			$this->db->select("'docpre' tipo")
			->select('lp.nIdLibro nIdLibro')
			->select('lp.nIdPresupuesto id')
			->select('lp.fPrecio fPrecio')
			->select('lp.nCantidad nCantidad')
			->select('c.nIdCliente nIdCl, c.cNombre, c.cApellido, c.cEmpresa')
			->select('lp.cCUser')
			->select('NULL ES')
			->select($this->_date_field('lp.dCreacion',	'dFecha'))
			->select('lp.fDescuento	fDescuento')
			->from('Doc_LineasPresupuestos lp')
			->join('Doc_Presupuestos p', 'lp.nIdPresupuesto = p.nIdPresupuesto')
			->join('Cli_Clientes c', 'p.nIdCliente = c.nIdCliente');
			if (is_numeric($id)) $this->db->where('lp.nIdLibro = ' . $id);
			if ($clpv)
			{
				$this->db->select('Cat_Fondo.cTitulo');
				$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = lp.nIdLibro');
			}
			if (is_numeric($idcl))
			{
				$this->db->where("c.nIdCliente = {$idcl}");
			}

			if (!empty($fecha1)) $this->db->where('lp.dCreacion >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("lp.dCreacion < " . $this->db->dateadd('d', 1, $fecha2));

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			$data = array_merge($data, $data2);
		}
		*/

		// 3-Pedidos Cliente
		if (($todo || in_array('docpedcli', $tipo))  && (($clpv && is_numeric($idcl)) || !$clpv))
		{
			$this->db->flush_cache();
			$this->db->select("'docpedcli' tipo")
			->select('lp.nIdLibro nIdLibro')
			->select('lp.nIdSeccion nIdSeccion')
			->select('s.cNombre cSeccion')
			->select('lp.nIdPedido id')
			->select('Doc_EstadosLineaPedidoCliente.cDescripcion')
			->select('lp.fPrecio fPrecio')
			->select('lp.nCantidad nCantidad')
			->select('lp.cCUser')
			->select('c.nIdCliente nIdCl, c.cNombre, c.cApellido, c.cEmpresa')
			->select('NULL ES')
			->select($this->_date_field('lp.dCreacion',	'dFecha'))
			->select('lp.fDescuento	fDescuento')
			->from('Doc_LineasPedidoCliente lp')
			->join('Doc_PedidosCliente p', 'lp.nIdPedido = p.nIdPedido')
			->join('Cli_Clientes c', 'p.nIdCliente = c.nIdCliente')
			->join('Doc_EstadosLineaPedidoCliente', 'lp.nIdEstado = Doc_EstadosLineaPedidoCliente.nIdEstado')
			->join('Cat_Secciones s', 'lp.nIdSeccion = s.nIdSeccion')
			->where('p.nIdEstado <> 3');

			if (is_numeric($id)) $this->db->where('lp.nIdLibro = ' . $id);
			if ($clpv)
			{
				$this->db->select('Cat_Fondo.cTitulo');
				$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = lp.nIdLibro');
			}
			if (is_numeric($idcl))
			{
				$this->db->where("c.nIdCliente = {$idcl}");
			}

			if (!empty($fecha1)) $this->db->where('lp.dCreacion >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("lp.dCreacion < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('s.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			$data = array_merge($data, $data2);
		}

		// 4-Pedido proveedor
		if (($todo || in_array('docpedpro', $tipo)) && (($clpv && is_numeric($idpv)) || !$clpv))
		{
			$this->db->flush_cache();
			$this->db->select("'docpedpro' tipo")
			->select('lp.nIdLibro nIdLibro')
			->select('lp.nIdSeccion nIdSeccion')
			->select('s.cNombre cSeccion')
			->select('lp.nIdPedido id')
			->select('lp.fPrecio fPrecio')
			->select('lp.nCantidad nCantidad')
			->select('lp.cCUser')
			->select('pv.nIdProveedor nIdPv, pv.cNombre, pv.cApellido, pv.cEmpresa')
			->select('Doc_EstadosLineaPedidoProveedor.cDescripcion')
			->select('NULL ES')
			->select($this->_date_field('p.dFechaEntrega',	'dFecha'))
			->select('lp.fDescuento	fDescuento')
			->from('Doc_LineasPedidoProveedor lp')
			->join('Doc_PedidosProveedor p', 'lp.nIdPedido = p.nIdPedido')
			->join('Cat_Secciones s', 'lp.nIdSeccion = s.nIdSeccion')
			->join('Prv_Proveedores pv', 'p.nIdProveedor = pv.nIdProveedor')
			->join('Doc_EstadosLineaPedidoProveedor', 'lp.nIdEstado = Doc_EstadosLineaPedidoProveedor.nIdEstado')
			#->where('(lp.nIdEstado NOT IN (5, 6, 7))')
			->where('p.dFechaEntrega IS NOT NULL');
			if (is_numeric($id)) $this->db->where('lp.nIdLibro = ' . $id);
			if ($clpv)
			{
				$this->db->select('Cat_Fondo.cTitulo');
				$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = lp.nIdLibro');
			}
			if (is_numeric($idpv))
			{
				$this->db->where("pv.nIdProveedor = {$idpv}");
			}

			if (!empty($fecha1)) $this->db->where('p.dFechaEntrega >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("p.dFechaEntrega < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('s.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			$data = array_merge($data, $data2);
		}

		// 5-Entradas de mercancia por Albaranes
		if (($todo || in_array('entalb', $tipo)) && (($clpv && is_numeric($idpv)) || !$clpv))
		{
			$this->db->flush_cache();
			$this->db->select("'entalb' tipo")
			->select('lp.nIdLibro nIdLibro')
			->select('lp.nIdSeccion nIdSeccion')
			->select('s.cNombre cSeccion')
			->select("ae.nIdAlbaran id1, lp.nIdPedido id2")
			->select('lae.fPrecio fPrecio')
			->select('lae.cCUser')
			->select('lpr.nCantidad nCantidad')
			->select('pv.nIdProveedor nIdPv, pv.cNombre, pv.cApellido, pv.cEmpresa')
			->select('1 ES')
			->select($this->_date_field('ae.dCierre',	'dFecha'))
			->select('lae.fDescuento	fDescuento')
			->from('Doc_LineasPedidosRecibidas lpr')
			->join('Doc_LineasAlbaranesEntrada lae', 'lpr.nIdLineaAlbaran = lae.nIdLinea')
			->join('Doc_AlbaranesEntrada ae', 'lae.nIdAlbaran = ae.nIdAlbaran')
			->join('Doc_LineasPedidoProveedor lp', 'lpr.nIdLineaPedido = lp.nIdLinea')
			->join('Prv_Proveedores pv', 'ae.nIdProveedor = pv.nIdProveedor')
			->join('Cat_Secciones s', 'lp.nIdSeccion = s.nIdSeccion')
			->where('ae.nIdEstado IN (2, 3, 4)');
			if (is_numeric($id)) $this->db->where('lp.nIdLibro = ' . $id);
			if ($clpv)
			{
				$this->db->select('Cat_Fondo.cTitulo');
				$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = lp.nIdLibro');
			}
			if (is_numeric($idpv))
			{
				$this->db->where("pv.nIdProveedor = {$idpv}");
			}

			if (!empty($fecha1)) $this->db->where('ae.dCierre >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("ae.dCierre < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('s.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			foreach ($data2 as $key => $value) {
				$data2[$key]['id'] = $value['id1'] . (isset($value['id2'])?(' (PP: '. $value['id2'] .')'):'');
			}
			$data = array_merge($data, $data2);
		}

		// 6-Entrada de mercancia por devoluciones de clientes
		if (($todo || in_array('entdevcmp', $tipo)) && (($clpv && is_numeric($idcl)) || !$clpv))
		{
			$this->db->flush_cache();
			$this->db->select("'entdevcmp' tipo")
			->select('la.nIdLibro nIdLibro')
			->select('la.nIdSeccion nIdSeccion')
			->select('s.cNombre cSeccion')
			->select("la.nIdAlbaran, f.nIdFactura")
			->select('c.nIdCliente nIdCl, c.cNombre, c.cApellido, c.cEmpresa')
			->select("la.nIdAlbaran id1, f.nIdFactura id2")
			->select('la.fPrecio fPrecio')
			->select('la.cCUser')
			->select('-la.nCantidad nCantidad')
			->select('1 ES')
			->select($this->_date_field('isnull(f.dFecha, a.dCreacion)',	'dFecha'))
			->select('la.fDescuento	fDescuento')
			->from('Doc_LineasAlbaranesSalida la')
			->join('Doc_AlbaranesSalida a', 'a.nIdAlbaran = la.nIdAlbaran')
			->join('Cat_Secciones s', 'la.nIdSeccion = s.nIdSeccion')
			->join('Doc_Facturas f', 'f.nIdFactura = a.nIdFactura', 'left')
			->join('Cli_Clientes c', 'ISNULL(f.nIdCliente, a.nIdCliente) = c.nIdCliente')
			->where('a.nIdEstado in (2,3) and la.nCantidad < 0');
			if (is_numeric($id)) $this->db->where('la.nIdLibro = ' . $id);
			if ($clpv)
			{
				$this->db->select('Cat_Fondo.cTitulo');
				$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = la.nIdLibro');
			}
			if (is_numeric($idcl))
			{
				$this->db->where("c.nIdCliente = {$idcl}");
			}

			if (!empty($fecha1)) $this->db->where('isnull(f.dFecha, a.dCreacion) >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("isnull(f.dFecha, a.dCreacion) < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('s.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			foreach ($data2 as $key => $value) {
				$data2[$key]['id'] = $value['id1'] . (isset($value['id2'])?(' (F: '. $value['id2'] .')'):'');
			}

			$data = array_merge($data, $data2);
		}

		// 7-Entrada de mercancia regulación de stock
		if (($todo || in_array('entreg', $tipo)) && !$clpv)
		{
			$this->db->flush_cache();
			$this->db->select("'entreg' tipo")
			->select('s.nIdLibro nIdLibro')
			->select('m.cDescripcion cDescripcion')
			->select('sc.nIdSeccion nIdSeccion')
			->select('sc.cNombre cSeccion')
			->select('s.cCUser')
			->select('s.nIdMovimiento id')
			//->select('0 fPrecio')
			->select('isnull(s.nCantidadFirme, 0) + isnull(s.nCantidadDeposito, 0) nCantidad')
			->select($this->_date_field('s.dCreacion', 'dFecha'))
			//->select('0	fDescuento')
			->select('1 ES')
			->from('Cat_Movimiento_Stock s')
			->join('Gen_Movimiento_Stock  m', 's.nIdMotivo = m.nId')
			->join('Cat_Secciones sc', 'sc.nIdSeccion = s.nIdSeccion')
			->where('isnull(s.nCantidadFirme, 0) + isnull(s.nCantidadDeposito, 0) > 0 and m.bSigno = 1');
			if (is_numeric($id)) $this->db->where('s.nIdLibro = ' . $id);

			if (!empty($fecha1)) $this->db->where('s.dCreacion >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("s.dCreacion < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('sc.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			$data = array_merge($data, $data2);
		}

		// 8-Entrada de mercancia por movimientos
		if (($todo || in_array('entmov', $tipo)) && !$clpv)
		{
			$this->db->flush_cache();
			$this->db->select("'entmov' tipo")
			->select('la.nIdLibro nIdLibro')
			->select('la.nIdSeccionDestino nIdSeccion')
			->select('s.cNombre cSeccion')
			->select('la.cCUser')
			->select('nIdMovimiento id')
			->select('la.nCantidad nCantidad')
			->select('1 ES')
			->select($this->_date_field('la.dCreacion',	'dFecha'))
			->from('Doc_Movimientos la')
			->join('Cat_Secciones s', 'la.nIdSeccionDestino = s.nIdSeccion');
			if (is_numeric($id)) $this->db->where('la.nIdLibro = ' . $id);

			if (!empty($fecha1)) $this->db->where('la.dCreacion >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("la.dCreacion < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('s.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			$data = array_merge($data, $data2);
		}

		// 9-Entrada de mercancía por devoluciones rechazadas
		if (($todo || in_array('entdev', $tipo)) && (($clpv && is_numeric($idpv)) || !$clpv) || in_array('entdevall', $tipo))
		{
			$this->db->flush_cache();
			$this->db->select("'entdev' tipo")
			->select('lp.nIdLibro nIdLibro')
			->select('lp.nIdSeccion nIdSeccion')
			->select('s.cNombre cSeccion')
			->select('lp.nIdDevolucion id')
			->select('lp.fPrecio fPrecio')
			->select('-lp.nCantidad nCantidad')
			->select('lp.cCUser')
			->select('pv.nIdProveedor nIdPv, pv.cNombre, pv.cApellido, pv.cEmpresa')
			->select('1 ES')
			->select($this->_date_field('ISNULL(d.dCierre, d.dCreacion)',	'dFecha'))
			->select('lp.fDescuento	fDescuento')
			->from('Doc_LineasDevolucion lp')
			->join('Doc_Devoluciones d', 'lp.nIdDevolucion = d.nIdDevolucion')
			->join('Cat_Secciones s', 'lp.nIdSeccion = s.nIdSeccion')
			->join('Prv_Proveedores pv', 'd.nIdProveedor = pv.nIdProveedor')
			->where('lp.nCantidad < 0');
			(in_array('entdevall', $tipo))?$this->db->where('d.nIdEstado  IN (1, 2,3)'):$this->db->where('d.nIdEstado  IN (2,3)');

			if (is_numeric($id)) $this->db->where('lp.nIdLibro = ' . $id);
			if ($clpv)
			{
				$this->db->select('Cat_Fondo.cTitulo');
				$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = lp.nIdLibro');
			}
			if (is_numeric($idpv))
			{
				$this->db->where("pv.nIdProveedor = {$idpv}");
			}

			if (!empty($fecha1)) $this->db->where('ISNULL(d.dCierre,d.dCreacion) >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("ISNULL(d.dCierre,d.dCreacion) < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('s.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			$data = array_merge($data, $data2);
		}

		// 10-Salida de mercancia por movimientos
		if (($todo || in_array('salmov', $tipo)) && !$clpv)
		{
			$this->db->flush_cache();
			$this->db->select("'salmov' tipo")
			->select('la.nIdLibro nIdLibro')
			->select('la.nIdSeccionOrigen nIdSeccion')
			->select('la.cCUser')
			->select('s.cNombre cSeccion')
			->select('nIdMovimiento id')
			->select('0 ES')
			->select('la.nCantidad nCantidad')
			->select($this->_date_field('la.dCreacion',	'dFecha'))
			->from('Doc_Movimientos la')
			->join('Cat_Secciones s', 'la.nIdSeccionOrigen = s.nIdSeccion');
			if (is_numeric($id)) $this->db->where('la.nIdLibro = ' . $id);

			if (!empty($fecha1)) $this->db->where('la.dCreacion >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("la.dCreacion < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('s.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			$data = array_merge($data, $data2);
		}

		// 11-Salida de mercancia por Ventas
		if (($todo || in_array('salcmp', $tipo)) && (($clpv && is_numeric($idcl)) || !$clpv))
		{
			$this->db->flush_cache();
			$this->db->select("'salcmp' tipo")
			->select('la.nIdLibro nIdLibro')
			->select('la.nIdSeccion nIdSeccion')
			->select('s.cNombre cSeccion')
			->select('c.nIdCliente nIdCl, c.cNombre, c.cApellido, c.cEmpresa')
			->select("la.nIdAlbaran id1, f.nIdFactura id2")
			->select('la.fPrecio fPrecio')
			->select('la.nCantidad nCantidad')
			->select('la.cCUser')
			->select($this->_date_field('isnull(f.dFecha, a.dCreacion)',	'dFecha'))
			->select('la.fDescuento	fDescuento')
			->select('0 ES')
			->from('Doc_LineasAlbaranesSalida la')
			->join('Doc_AlbaranesSalida a', 'a.nIdAlbaran = la.nIdAlbaran')
			->join('Cat_Secciones s', 'la.nIdSeccion = s.nIdSeccion')
			->join('Doc_Facturas f', 'f.nIdFactura = a.nIdFactura', 'left')
			->join('Cli_Clientes c', 'ISNULL(f.nIdCliente, a.nIdCliente) = c.nIdCliente')
			->where('a.nIdEstado in (2,3) and la.nCantidad > 0');
			if (is_numeric($id)) $this->db->where('la.nIdLibro = ' . $id);
			if ($clpv)
			{
				$this->db->select('Cat_Fondo.cTitulo');
				$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = la.nIdLibro');
			}
			if (is_numeric($idcl))
			{
				$this->db->where("c.nIdCliente = {$idcl}");
			}

			if (!empty($fecha1)) $this->db->where('isnull(f.dFecha, a.dCreacion) >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("isnull(f.dFecha, a.dCreacion) < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('s.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			foreach ($data2 as $key => $value) {
				$data2[$key]['id'] = $value['id1'] . (isset($value['id2'])?(' (F: '. $value['id2'] .')'):'');
			}
			$data = array_merge($data, $data2);
		}

		// 12-Salida de mercancía por devoluciones
		if (($todo || in_array('saldev', $tipo)) && (($clpv && is_numeric($idpv)) || !$clpv) || in_array('saldevall', $tipo))
		{
			$this->db->flush_cache();
			$this->db->select("'saldev' tipo")
			->select('lp.nIdLibro nIdLibro')
			->select('lp.nIdSeccion nIdSeccion')
			->select('s.cNombre cSeccion')
			->select('lp.cCUser')
			->select('lp.nIdDevolucion id')
			->select('lp.fPrecio fPrecio')
			->select('lp.nCantidad nCantidad')
			->select('pv.nIdProveedor nIdPv, pv.cNombre, pv.cApellido, pv.cEmpresa')
			->select($this->_date_field('ISNULL(d.dCierre, d.dCreacion)', 'dFecha'))
			->select($this->_date_field('d.dEntrega', 'dEntrega'))
			->select('lp.fDescuento	fDescuento')
			->select('0 ES')
			->from('Doc_LineasDevolucion lp')
			->join('Doc_Devoluciones d', 'lp.nIdDevolucion = d.nIdDevolucion')
			->join('Cat_Secciones s', 'lp.nIdSeccion = s.nIdSeccion')
			->join('Prv_Proveedores pv', 'd.nIdProveedor = pv.nIdProveedor')
			->where('lp.nCantidad > 0');
			(in_array('saldevall', $tipo))?$this->db->where('d.nIdEstado  IN (1, 2,3)'):$this->db->where('d.nIdEstado  IN (2,3)');
			if (is_numeric($id)) $this->db->where('lp.nIdLibro = ' . $id);
			if ($clpv)
			{
				$this->db->select('Cat_Fondo.cTitulo');
				$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = lp.nIdLibro');
			}
			if (is_numeric($idpv))
			{
				$this->db->where("pv.nIdProveedor = {$idpv}");
			}

			if (!empty($fecha1)) $this->db->where('ISNULL(d.dCierre,d.dCreacion) >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("ISNULL(d.dCierre,d.dCreacion) < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('s.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			$data = array_merge($data, $data2);
		}

		// 13-Salida de mercancia por regulación de stock
		if (($todo || in_array('salreg', $tipo)) && !$clpv)
		{
			$this->db->flush_cache();
			$this->db->select("'salreg' tipo")
			->select('s.nIdLibro nIdLibro')
			->select('m.cDescripcion cDescripcion')
			->select('sc.nIdSeccion nIdSeccion')
			->select('sc.cNombre cSeccion')
			->select('s.nIdMovimiento id')
			->select('s.cCUser')
			->select('0 ES')
			->select('isnull(s.nCantidadFirme, 0) + isnull(s.nCantidadDeposito, 0) nCantidad')
			->select($this->_date_field('s.dCreacion', 'dFecha'))
			->from('Cat_Movimiento_Stock s')
			->join('Gen_Movimiento_Stock  m', 's.nIdMotivo = m.nId')
			->join('Cat_Secciones sc', 'sc.nIdSeccion = s.nIdSeccion')
			->where('isnull(s.nCantidadFirme, 0) + isnull(s.nCantidadDeposito, 0) > 0 and m.bSigno = 0');
			if (is_numeric($id)) $this->db->where('s.nIdLibro = ' . $id);

			if (!empty($fecha1)) $this->db->where('s.dCreacion >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("s.dCreacion < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('sc.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			$data = array_merge($data, $data2);
		}

		// 14-Liquidación de depósitos
		if (($todo || in_array('liqdep', $tipo)) && (($clpv && is_numeric($idpv)) || !$clpv))
		{
			$this->db->flush_cache();
			$this->db->select("'liqdep' tipo")
			->select('lp.nIdLibro nIdLibro')
			->select('lp.nIdSeccion nIdSeccion')
			->select('s.cNombre cSeccion')
			->select("Doc_LiquidacionDepositos.nIdDocumento id1, ae.nIdAlbaran id2")
			->select('Doc_LineasLiquidacionDeposito.fPrecio')
			->select('Doc_LineasLiquidacionDeposito.cCUser')
			->select('Doc_LineasLiquidacionDeposito.nCantidad nCantidad')
			->select('pv.nIdProveedor nIdPv, pv.cNombre, pv.cApellido, pv.cEmpresa')
			->select('NULL ES')
			->select($this->_date_field('Doc_LiquidacionDepositos.dFecha',	'dFecha'))
			->select('Doc_LineasLiquidacionDeposito.fDescuento	fDescuento')
			->from('Doc_LineasPedidosRecibidas lpr')
			->join('Doc_LineasAlbaranesEntrada lae', 'lpr.nIdLineaAlbaran = lae.nIdLinea')
			->join('Doc_AlbaranesEntrada ae', 'lae.nIdAlbaran = ae.nIdAlbaran')
			->join('Doc_LineasPedidoProveedor lp', 'lpr.nIdLineaPedido = lp.nIdLinea')
			->join('Prv_Proveedores pv', 'ae.nIdProveedor = pv.nIdProveedor')
			->join('Cat_Secciones s', 'lp.nIdSeccion = s.nIdSeccion')
			->join('Doc_LineasLiquidacionDeposito', 'Doc_LineasLiquidacionDeposito.nIdLineaEntrada=lae.nIdLinea')
			->join('Doc_LiquidacionDepositos', 'Doc_LineasLiquidacionDeposito.nIdDocumento=Doc_LiquidacionDepositos.nIdDocumento')
			->where('ae.nIdEstado IN (2, 3, 4)');
			if (is_numeric($id)) $this->db->where('lp.nIdLibro = ' . $id);
			if ($clpv)
			{
				$this->db->select('Cat_Fondo.cTitulo');
				$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = lp.nIdLibro');
			}
			if (is_numeric($idpv))
			{
				$this->db->where("pv.nIdProveedor = {$idpv}");
			}

			if (!empty($fecha1)) $this->db->where('Doc_LiquidacionDepositos.dFecha >= ' . $fecha1);
			if (!empty($fecha2)) $this->db->where("Doc_LiquidacionDepositos.dFecha < " . $this->db->dateadd('d', 1, $fecha2));
			if (!empty($ids)) $this->db->where('s.nIdSeccion = ' . $ids);

			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			foreach ($data2 as $key => $value) {
				$data2[$key]['id'] = $value['id1'] . (isset($value['id2'])?(' (AE: '. $value['id2'] .')'):'');
			}
			#var_dump($data2); die();
			$data = array_merge($data, $data2);
		}


		sksort($data, 'dFecha');	
		#var_dump($data); die();
		return $data;
	}

	/**
	 * Pedidos de cliente de un artículo
	 * @param int $id Id del artículo
	 * @param date $fecha1 Fecha desde
	 * @param date $fecha2 Fecha hasta
	 * @param bool $pendientes TRUE: Muestra solo los pendientes, FALSE: todos
	 * @return array
	 */
	function get_pedidos_cliente($id, $fecha1 = null, $fecha2 = null, $pendientes = FALSE)
	{
		$this->db->flush_cache();
		$this->db->select('lp.nIdSeccion')
		->select('s.cNombre cSeccion')
		->select('lp.nIdPedido id')
		->select('lp.fPrecio')
		->select('lp.fIVA')
		->select('lp.fRecargo')
		->select('lp.nCantidad')
		->select('c.nIdCliente nIdCl')
		->select("'docpedcli' tipo")
		->select('c.cNombre, c.cApellido, c.cEmpresa')
		->select('lp.cCUser, lp.bAviso')
		->select($this->_date_field('lp.dAviso', 'dAviso'))
		->select('st.cDescripcion cEstado')
		->select($this->_date_field('lp.dCreacion',	'dFecha'))
		->select('lp.fDescuento	fDescuento')
		->select('p.bNoAvisar')
		->from('Doc_LineasPedidoCliente lp')
		->join('Doc_PedidosCliente p', 'lp.nIdPedido = p.nIdPedido')
		->join('Cat_Secciones s', 'lp.nIdSeccion = s.nIdSeccion')
		->join('Cli_Clientes c', 'c.nIdCliente = p.nIdCliente')
		->join('Doc_EstadosLineaPedidoCliente st', 'lp.nIdEstado = st.nIdEstado')
		->where('lp.nIdLibro = ' . $id);

		if (!empty($fecha1)) $fecha1 = format_mssql_date($fecha1);
		if (!empty($fecha1)) $fecha2 = format_mssql_date($fecha2);

		if (!empty($fecha1)) $this->db->where('lp.dCreacion >= ' . $fecha1);
		if (!empty($fecha2)) $this->db->where("lp.dCreacion < " . $this->db->dateadd('d', 1, $fecha2));
		if ($pendientes) $this->db->where('lp.nIdEstado IN (1, 6, 2, 3)');

		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Pedidos de cliente de un artículo en formaro presupuesto
	 * @param int $id Id del artículo
	 * @param date $fecha1 Fecha desde
	 * @param date $fecha2 Fecha hasta
	 * @param int $idc Id del cliente (opcional)
	 * @return array
	 */
	function get_presupuestos($id, $fecha1 = null, $fecha2 = null, $idc = null, $aceptado = NULL)
	{
		$this->db->flush_cache();
		$this->db->select('lp.nIdSeccion')
		->select('s.cNombre cSeccion')
		->select('lp.nIdPedido id')
		->select('lp.fPrecio')
		->select('lp.fIVA')
		->select('lp.cRefInterna')
		->select('lp.fRecargo')
		->select('lp.nCantidad')
		->select('c.nIdCliente nIdCl')
		->select("'docpedcli' tipo")
		->select('c.cNombre, c.cApellido, c.cEmpresa')
		->select('lp.cCUser, lp.bAviso')
		->select($this->_date_field('lp.dAviso', 'dAviso'))
		->select('st.cDescripcion cEstado')
		->select($this->_date_field('lp.dCreacion',	'dFecha'))
		->select('lp.fDescuento	fDescuento')
		->select('p.bNoAvisar')
		->from('Doc_LineasPedidoCliente lp')
		->join('Doc_PedidosCliente p', 'lp.nIdPedido = p.nIdPedido')
		->join('Cat_Secciones s', 'lp.nIdSeccion = s.nIdSeccion')
		->join('Cli_Clientes c', 'c.nIdCliente = p.nIdCliente')
		->join('Doc_EstadosLineaPedidoCliente st', 'lp.nIdEstado = st.nIdEstado')
		->where('lp.nIdLibro = ' . $id)
		->where('p.nIdEstado = 3');

		if (!empty($fecha1)) $fecha1 = format_mssql_date($fecha1);
		if (!empty($fecha1)) $fecha2 = format_mssql_date($fecha2);

		if (!empty($fecha1)) $this->db->where('lp.dCreacion >= ' . $fecha1);
		if (!empty($fecha2)) $this->db->where("lp.dCreacion < " . $this->db->dateadd('d', 1, $fecha2));

		if (is_numeric($idc)) $this->db->where('p.nIdCliente = ' . $idc);
		if ($aceptado) 
		{
			$this->load->model('ventas/m_pedidoclientelinea');
			$this->db->where('lp.nIdEstado = ' . ESTADO_LINEA_PEDIDO_CLIENTE_ACEPTADO);
		}

		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Pedidos de proveedor de un artículo
	 * @param int $id Id del artículo
	 * @param date $fecha1 Fecha desde
	 * @param date $fecha2 Fecha hasta
	 * @param bool $pendientes TRUE: Muestra solo los pendientes, FALSE: todos
	 * @return array
	 */
	function get_pedidos_proveedor($id, $fecha1 = null, $fecha2 = null, $pendientes = FALSE)
	{
		$this->db->flush_cache();
		$this->db->select('lp.nIdSeccion nIdSeccion')
		->select('s.cNombre cSeccion')
		->select('lp.nIdPedido id, lp.nIdLinea')
		->select('lp.fPrecio')
		->select('lp.nCantidad')
		->select('lp.nRecibidas')
		->select('lp.cCUser')
		->select('c.nIdProveedor nIdPv')
		->select("'docpedprv' tipo")
		->select('c.cNombre, c.cApellido, c.cEmpresa')
		->select('st.cDescripcion cEstado')
		->select('p.bDeposito')
		->select('lp.dCreacion, Doc_InformacionProveedor.cDescripcion cInformacion, lp.dFechaInformacion')
		->select('Doc_InformacionProveedor.cDescripcion cInformacion, lp.nIdInformacion')
		->select($this->_date_field('lp.dFechaInformacion', 'dFechaInformacion'))
		->select($this->_date_field('ISNULL(p.dFechaEntrega, lp.dCreacion)', 'dFecha'))
		->select('lp.fDescuento	fDescuento')
		->from('Doc_LineasPedidoProveedor lp')
		->join('Doc_PedidosProveedor p', 'lp.nIdPedido = p.nIdPedido')
		->join('Cat_Secciones s', 'lp.nIdSeccion = s.nIdSeccion')
		->join('Prv_Proveedores c', 'c.nIdProveedor = p.nIdProveedor')
		->join('Doc_EstadosLineaPedidoProveedor st', 'lp.nIdEstado = st.nIdEstado')
		->join('Doc_InformacionProveedor', "Doc_InformacionProveedor.nIdInformacion = lp.nIdInformacion", 'left')
		->where('lp.nIdLibro = ' . $id);

		if (!empty($fecha1)) $fecha1 = format_mssql_date($fecha1);
		if (!empty($fecha1)) $fecha2 = format_mssql_date($fecha2);

		if (!empty($fecha1)) $this->db->where('p.dFechaEntrega >= ' . $fecha1);
		if (!empty($fecha2)) $this->db->where("p.dFechaEntrega < " . $this->db->dateadd('d', 1, $fecha2));
		if ($pendientes) $this->db->where('lp.nIdEstado IN (1, 2, 4)');

		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Devuelve los títulos con el coste erróneo
	 * @param int $count Número de registros máximo a corregir
	 * @return array
	 */
	function get_coste_error($count = null)
	{
		$this->db->flush_cache();
		$this->db->select("nIdLibro, cTitulo, fPrecioCompra, fPrecio")
		->select('dbo.CalcularCosteLibro(nIdLibro, NULL) fCoste')
		->from('Cat_Fondo')
		->where('nIdOferta IS NULL')
		->where('fPrecioCompra <= 0 OR fPrecioCompra IS NULL');

		if (isset($count)) $this->db->limit($count);

		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
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
			$this->load->library('ISBNEAN');
			$query = trim($query);
			if ($this->isbnean->is_isbn($query) || $this->isbnean->is_isbn($query, TRUE))
			{
				$isbn = $this->isbnean->to_isbn($query);
				$ean = $this->isbnean->to_ean($isbn);
				
				#$this->_where = "Cat_Codigos_Fondo.nCodigo={$ean}";
				if ($ean)
				{
					$where = "Cat_Codigos_Fondo.nCodigo={$ean}";
					$this->_addcodigos = TRUE;
				}
				else
					$where = "{$this->_tablename}.cISBNBase = " . $this->db->escape($this->isbnean->clean_code($isbn));
				/*$where = "({$this->_tablename}.cISBNBase = " . $this->db->escape($this->isbnean->clean_code($isbn)) 
					. " OR {$this->_tablename}.nIdLibro IN (SELECT Cat_Codigos_Fondo.nIdLibro FROM Cat_Codigos_Fondo WHERE nCodigo={$ean}))";*/
				#$where = "{$this->_tablename}.cISBNBase = " . $this->db->escape($this->isbnean->clean_code($isbn));
				if (is_array($fields))
				{
					$fields[] = $this->_tablename . '.cISBN';
				}
				else
				{
					$fields .= ($fields != '')?',':'' . $this->_tablename . '.cISBN';
				}
			}
			elseif (is_numeric($query))
			{
				$query = (int) $query;
				$this->_addcodigos = TRUE;
				#$where = "{$this->_tablename}.nIdLibro IN (SELECT Cat_Codigos_Fondo.nIdLibro FROM Cat_Codigos_Fondo WHERE nCodigo={$query})";
				#$where = "{$this->_tablename}.nIdLibro={$query} OR Cat_Codigos_Fondo.nCodigo={$query}";
				$where = "Cat_Codigos_Fondo.nCodigo={$query}";
				#$where = "{$this->_tablename}.nIdLibro={$query}";
				#$this->_addcodigos = TRUE;
				#$this->_where = "Cat_Codigos_Fondo.nCodigo={$query}";
				#$this->db->join('Cat_Codigos_Fondo', 'Cat_Codigos_Fondo.nIdLibro=Cat_Fondo.nIdLibro', 'left');
				#" OR {$this->_tablename}.nIdLibro IN (SELECT Cat_Codigos_Fondo.nIdLibro FROM Cat_Codigos_Fondo WHERE nCodigo={$query})";
			}

			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @see system/application/libraries/MY_Model#onParseWhere
	 */
	protected function onParseWhere(&$where)
	{
		parent::onParseWhere($where);
		#print '<pre>'; print_r($where); print '</pre>'; die();
		if (isset($where['cISBN']))
		{
			$this->load->library('ISBNEAN');
			$isbn = $this->isbnean->clean_code($this->isbnean->to_isbn($where['cISBN']));
			if ($isbn)
			{
				$where['cISBNBase'] = $isbn;
			}
			else
			{
				$where['cISBNBase'] = $this->isbnean->clean_code($where['cISBN']);
			}
			unset($where['cISBN']);
		}
		if (isset($where['Scn']) && is_numeric($where['Scn']))
		{
			$where[count($where)] = "Cat_Secciones.nIdSeccion = {$where['Scn']} OR Cat_Secciones.cCodigo LIKE '{$where['Scn']}.%' OR Cat_Secciones.cCodigo LIKE '%.{$where['Scn']}.%'";
			unset($where['Scn']);
		}
		if (isset($where['Idm']) && is_numeric($where['Idm']))
		{
			$where[count($where)] = "Cat_Materias.nIdMateria = {$where['Idm']} OR Cat_Materias.cCodMateria LIKE '{$where['Idm']}.%' OR Cat_Materias.cCodMateria LIKE '%.{$where['Idm']}.%'";
			unset($where['Idm']);
		}
		#print '<pre>'; print_r($where); print '</pre>'; die();
		return TRUE;
	}

	/**
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			#echo '<pre>'; var_dump($where); echo '</pre>'; die();
			if ($this->_addcodigos || strpos($where, 'Cat_Codigos_Fondo') !== FALSE)
				$this->db->join('Cat_Codigos_Fondo', 'Cat_Codigos_Fondo.nIdLibro=Cat_Fondo.nIdLibro', 'left');

			$this->db->select('Cat_Editoriales.cNombre cEditorial, c1.cNombre cColeccion, c2.cNombre cColeccion2, c3.cNombre cColeccion3')
			->select('Cat_Tipos.fIVA, Cat_Tipos.fRecargo, Cat_EstadosLibro.cDescripcion cEstado, Cat_Editoriales.cNombre cEditorial')
			->select('Cat_Editoriales.nIdProveedor nIdProveedor2')
			->select('Gen_Ofertas.nIdTipoOferta, Gen_Ofertas.fValor')
			#->join('Cat_Codigos_Fondo', 'Cat_Codigos_Fondo.nIdLibro=Cat_Fondo.nIdLibro', 'left')
			->join('Cat_Tipos', 'Cat_Tipos.nIdTipo = Cat_Fondo.nIdTipo', 'left')
			->join('Cat_EstadosLibro', 'Cat_EstadosLibro.nIdEstado = Cat_Fondo.nIdEstado', 'left')
			->join('Cat_Editoriales' ,'Cat_Editoriales.nIdEditorial = Cat_Fondo.nIdEditorial', 'left')
			->join('Cat_Colecciones c1', 'c1.nIdColeccion = Cat_Fondo.nIdColeccion', 'left')
			->join('Cat_Colecciones c2', 'c2.nIdColeccion = Cat_Fondo.nIdColeccion2', 'left')
			->join('Cat_Colecciones c3', 'c3.nIdColeccion = Cat_Fondo.nIdColeccion3', 'left')
			->join('Gen_Ofertas', 'Gen_Ofertas.nIdOferta = Cat_Fondo.nIdOferta', 'left');

			$this->db->select('Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa')
			->select("ISNULL({$this->_tablename}.nIdProveedor, Cat_Editoriales.nIdProveedor) nIdProveedor")
			->join('Prv_Proveedores', "Prv_Proveedores.nIdProveedor = ISNULL({$this->_tablename}.nIdProveedor, Cat_Editoriales.nIdProveedor)", 'left');

			return TRUE;
		}
		return FALSE;
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
			if (isset($data['nIdOferta']))
			{
				if ($data['nIdTipoOferta'] == OFERTA_PRECIOFIJO)
				{
					$data['fPrecioOriginal'] = $data['fPrecio'];
					$data['fPrecio'] = format_quitar_iva($data['fValor'], $data['fIVA']);
				}
				elseif ($data['nIdTipoOferta'] == OFERTA_DESCUENTO)
				{
					$data['fPrecioOriginal'] = $data['fPrecio'];
					$data['fPrecio'] = format_decimals($data['fPrecio'] * (1 - $data['fValor']/100));					
				}	
			}
			$data['fPVP'] = (isset($data['fPrecio']) && isset($data['fIVA']))?format_add_iva($data['fPrecio'], $data['fIVA']):0;
			#if (isset($data['nIdProveedorManual'])) $data['nIdProveedor'] = $data['nIdProveedorManual'];
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
			if (isset($data['sinopsis']['tSinopsis']))
			{
				#echo $data['sinopsis']['tSinopsis'];
				$data['sinopsis']['tSinopsis'] = strip_tags_attributes($data['sinopsis']['tSinopsis'], $this->config->item('bp.sinopsis.allowtags'));
			}
			$this->_check_isbn($data);
		}
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert($data)
	 */
	protected function onAfterInsert($id, &$data)
	{
		if (parent::onAfterInsert($id, $data))
		{
			# Almacena el nuevo EAN
			if (isset($data['nEAN']) && trim($data['nEAN']) != '')
			{
				$this->obj->load->model('catalogo/m_articulocodigo');
				$ins = array(
					'nIdLibro' => (int)$id,
					'nCodigo' =>  $data['nEAN'],
				);
				if (!$this->obj->m_articulocodigo->insert($ins))
				{
					$this->_set_error_message($this->obj->m_articulocodigo->_error_message());
					return FALSE;
				}
			}
			$this->obj->load->model('catalogo/m_articulocodigo');
			$ins = array(
				'nIdLibro' => (int)$id,
				'nCodigo' =>  $id,
			);
			if (!$this->obj->m_articulocodigo->insert($ins))
			{
				$this->_set_error_message($this->obj->m_articulocodigo->_error_message());
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			if (isset($data['sinopsis']['tSinopsis']))
			{
				$data['sinopsis']['tSinopsis'] = strip_tags_attributes($data['sinopsis']['tSinopsis'], $this->config->item('bp.sinopsis.allowtags'));
			}
			$this->_check_isbn($data);
			
			# Cambios de precio de venta
			$old = null;
			if (isset($data['fPrecio']))
			{
				if (!isset($old)) $old = $this->load($id);
				if ($old['fPrecio'] != $data['fPrecio'])
				{
					$ins = array(
						'nIdLibro' => (int)$id,
						'fPrecioAntiguo' =>  $this->_tofloat($old['fPrecio']),
						'fPrecioNuevo' =>  $this->_tofloat($data['fPrecio']),
						'cCUser' => $this->userauth->get_username(),
						'dCambio' => $this->_todate(time())
					);
					if (!$this->db->insert('Cat_CambiosPrecio', $ins))
					{
						$this->_set_error_message($this->db->_error_message());
						return FALSE;
					}
				}	
			}
			
			# Cambios de precio de compra
			if (isset($data['fPrecioProveedor']))
			{
				if (!isset($old)) $old = $this->load($id);
				if ($old['fPrecioProveedor'] != $data['fPrecioProveedor'])
				{
					$ins = array(
						'nIdLibro' => (int)$id,
						'fPrecioAntiguo' => $this->_tofloat($old['fPrecioProveedor']),
						'fPrecioNuevo' =>  $this->_tofloat($data['fPrecioProveedor']),
						'cCUser' => $this->userauth->get_username(),
						'nIdDivisa' => (isset($data['nIdDivisaProveedor'])?$data['nIdDivisaProveedor']:nulll),
						'dCambio' => $this->_todate(time())
					);
					if (!$this->db->insert('Cat_CambiosPrecioProveedor', $ins))
					{
						$this->_set_error_message($this->db->_error_message());
						return FALSE;
					}
				}	
			}

			#Cambios de tarifa por defecto
			if (isset($data['fPrecio']))
			{
				$this->obj->load->model('catalogo/m_articulotarifa');
				$tar = $this->obj->m_articulotarifa->get(null, null, null, null, "nIdLibro={$id} AND nIdTipoTarifa=" . $this->config->item('ventas.tarifas.defecto'));
				if (count($tar) > 0)
				{
					# Evita ciclo de actualizaciones
					$this->obj->m_articulotarifa->triggers_disable();
					if (!$this->obj->m_articulotarifa->update($tar[0]['nIdTarifaLibro'], array('fPrecio' => $data['fPrecio'])))
					{
						$this->obj->m_articulotarifa->triggers_enable();
						$this->_set_error_message($this->obj->m_articulotarifa->error_message());
						return FALSE;
					}
					$this->obj->m_articulotarifa->triggers_enable();

				}
			}
			# Almacena el nuevo EAN
			if (isset($data['nEAN']))				
			{
				if (!isset($old)) $old = $this->load($id);
				if ($old['nEAN'] != $data['nEAN'] && trim($data['nEAN']) != '')
				{
					$this->obj->load->model('catalogo/m_articulocodigo');
					# Si ya existía no lo duplica
					$res = $this->obj->m_articulocodigo->get(0, 1, null, null, "nIdLibro={$id} AND nCodigo=" .(int) $data['nEAN']);
					if (count($res) == 0)
					{
						$ins = array(
							'nIdLibro' => (int)$id,
							'nCodigo' =>  $data['nEAN'],
						);
						if (!$this->obj->m_articulocodigo->insert($ins))
						{
							$this->_set_error_message($this->obj->m_articulocodigo->error_message());
							return FALSE;
						}
					}
				}	
			}
		}
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 */
	protected function onBeforeDelete($id) 
	{
		if(parent::onBeforeDelete($id))
		{
			$this->obj->load->model('catalogo/m_proveedorarticulo');
			if (!$this->obj->m_proveedorarticulo->delete_by("nIdLibro={$id}"))
			{
				$this->_set_error_message($this->obj->m_proveedorarticulo->error_message());
				return FALSE;
			}
			
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Comprueba el ISBN y completa los datos
	 * @param array $data Registro a comprobar
	 */
	private function _check_isbn(&$data)
	{
		$isbn = null;
		if (isset($data['cISBN']))
		{
			$this->load->library('ISBNEAN');
			$isbn = $this->isbnean->to_isbn($data['cISBN'], TRUE);
		}
		elseif (isset($data['nEAN']))
		{
			$this->load->library('ISBNEAN');
			$isbn = $this->isbnean->to_isbn($data['nEAN'], TRUE);
		}
		if (isset($isbn))
		{
			$data['cISBNBase'] = $this->isbnean->clean_code($isbn['isbn13']);
			$data['nEAN'] = $this->isbnean->to_ean($isbn['isbn13']);
			$data['cISBN'] = $isbn['isbn13'];
			$data['cISBN10'] = $isbn['isbn10'];
			$data['cISBNBase10'] = $this->isbnean->clean_code($isbn['isbn10']);
		}
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

		$obj = get_instance();
		$obj->load->model('catalogo/m_articuloseccion');
		$obj->load->model('catalogo/m_articulocodigo');
		$this->load->helper('unificar');

		$tablas[] = array('tabla' => 'Doc_LineasPresupuestos');
		$tablas[] = array('tabla' => 'Doc_LineasPedidoCliente', 'model' => 'ventas/m_pedidoclientelinea');
		$tablas[] = array('tabla' => 'Doc_LineasAlbaranesSalida', 'model' => 'ventas/m_albaransalidalinea');
		$tablas[] = array('tabla' => 'Ext_LineasPedidoConcurso', 'model' => 'concursos/m_pedidoconcursolinea');
		$tablas[] = array('tabla' => 'Doc_LineasAlbaranesSalida2', 'model' => 'ventas/m_albaransalidalinea2');
		$tablas[] = array('tabla' => 'Doc_LineasAlbaranesEntrada', 'model' => 'compras/m_albaranentradalinea');
		$tablas[] = array('tabla' => 'Doc_LineasPedidoProveedor', 'model' => 'compras/m_pedidoproveedorlinea');
		$tablas[] = array('tabla' => 'Doc_Movimientos', 'model' => 'catalogo/m_movimiento');
		$tablas[] = array('tabla' => 'Tmp_Ubicaciones');
		$tablas[] = array('tabla' => 'Alm_EtqAcumuladas');
		$tablas[] = array('tabla' => 'Cat_Movimiento_Stock', 'model' => 'stocks/m_arreglostock');
		$tablas[] = array('tabla' => 'Cat_RegulacionStock', 'model' => 'stocks/m_stockcontado');
		$tablas[] = array('tabla' => 'Cat_Reimpresiones');
		$tablas[] = array('tabla' => 'Cat_Multimedia');
		$tablas[] = array('tabla' => 'Cat_Relacionados', 'id' => 'nIdLibro1');
		$tablas[] = array('tabla' => 'Cat_Relacionados', 'id' => 'nIdLibro2');
		$tablas[] = array('tabla' => 'Cat_Promociones');
		$tablas[] = array('tabla' => 'Ext_EOISTitulos', 'id' => 'nIdRegistro');
		#$tablas[] = array('tabla' => 'Ext_FotoStockLibros');
		#$tablas[] = array('tabla' => 'Ext_LibrosEtiquetas');
		$tablas[] = array('tabla' => 'Ext_Movimientos', 'model' => 'catalogo/m_movimiento');
		$tablas[] = array('tabla' => 'Cat_Codigos_Fondo', 'model' => 'catalogo/m_articulocodigo');
		$tablas[] = array('tabla' => 'Sus_Suscripciones', 'id' => 'nIdRevista');
		$tablas[] = array('tabla' => 'Cat_Ubicaciones_Libros', 'model' => 'catalogo/m_articuloubicacion');
		$tablas[] = array('tabla' => 'Cat_PalabrasClave_Libros');
		$tablas[] = array('tabla' => 'Sus_Boletines_Libros', 'model' => 'mailing/m_boletinlibro');
		$tablas[] = array('tabla' => 'Gen_Observaciones', 'id' => 'nIdRegistro', 'where' => 'cTabla=\'Cat_Fondo\'');
		$tablas[] = array('tabla' => 'Prv_Proveedores_Fondo_Compras');

		// TRANS
		#$this->db->save_queries = TRUE;
		$correcto = $this->load($id1);
		if (!$correcto)
		{
			$this->_set_error_message($this->lang->line('registro_no_encontrado'));
			return FALSE;
		}
		$ean = $correcto['nEAN'];

		$this->db->trans_begin();

		foreach ($id_or as $id)
		{
			# Códigos
			if (!unificar_nn($this, 'Cat_Codigos_Fondo', 'nIdLibro', 'nCodigo', $id1, $id))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			// Palabras Clave
			if (!unificar_nn($this, 'Cat_PalabrasClave_Libros', 'nIdLibro', 'nIdPalabraClave', $id1, $id))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			// Ubicaciones
			if (!unificar_nn($this, 'Cat_Ubicaciones_Libros', 'nIdLibro', 'nIdUbicacion', $id1, $id))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			// Boletines
			if (!unificar_nn($this, 'Sus_Boletines_Libros', 'nIdLibro', 'nIdBoletin', $id1, $id))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			// Compras
			if (!unificar_nn($this, 'Prv_Proveedores_Fondo_Compras', 'nIdLibro', 'nIdProveedor', $id1, $id))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			// Relacionados
			$this->db->flush_cache();
			$this->db->where("(nIdLibro1 ={$id} AND nIdLibro2 = {$id1}) OR (nIdLibro1 ={$id1} AND nIdLibro2 = {$id}) ")
			->delete('Cat_Relacionados');
			if ($this->_check_error())
			{
				$this->db->trans_rollback();
				return FALSE;
			}

			// Tablas
			if (!unificar_do($this, $tablas, $id1, $id, 'nIdLibro'))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
		}
		
		// Secciones
		$this->db->flush_cache();
		$this->db->select_sum('nStockFirme', 'nStockFirme')
		->select_sum('nStockDeposito', 'nStockDeposito')
		->select_sum('nStockReservado', 'nStockReservado')
		->select_sum('nStockRecibir', 'nStockRecibir')
		->select_sum('nStockAPedir', 'nStockAPedir')
		->select_sum('nStockServir', 'nStockServir')
		->select_sum('nStockADevolver', 'nStockADevolver')
		->select('MAX(nStockMaximo)', 'nStockMaximo')
		->select('MAX(nStockMinimo)', 'nStockMinimo')
		->select('nIdSeccion')
		->from('Cat_Secciones_Libros')
		->where("nIdLibro IN ({$id1}, {$id2})")
		->group_by('nIdSeccion');
		$secs = $this->_get_results($this->db->get());

		if (!unificar_alter($this, 'Cat_Secciones_Libros',FALSE))
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->flush_cache();
		$this->db->where("nIdLibro IN ({$id1}, {$id2})")
		->delete('Cat_Secciones_Libros');
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		foreach($secs as $sec)
		{
			$sec['nIdLibro'] = $id1;
			if ($obj->m_articuloseccion->insert($sec)<0)
			{
				$this->_set_error_message($obj->m_articuloseccion->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		// Borrado
		$delete = array(
			'Cat_Autores_Libros',
			'Cat_Indices',
			'Cat_Libros_Materias',
			'Cat_Sinopsis',
			'Prv_Proveedores_Cat_Fondo',
			'Tmp_Importar',
			'Cat_Sinopsis',
			'Cat_Revistas',
			'Web_Portadas',
			'Cat_Revistas',
			'Cat_Libros_Tarifas',
			'Cat_Fondo');
		if (!unificar_delete($this, 'nIdLibro', $delete, $id2))
		{			
			$this->db->trans_rollback();
			return FALSE;
		}

		// Borrado portadas
		foreach($id_or as $id)
		{
			if (!$this->set_portada($id))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		// Limpieza de caches
		unificar_clear_cache($tablas);
		$this->clear_cache();

		// COMMIT
		$this->db->trans_commit();
		#echo '<pre>'; print_r($this->db->queries); echo '</pre>';
		return TRUE;
	}

	/**
	 * Crea los autores de un artículo
	 * @param int $id Id del artículo
	 * @return bool TRUE: ok, sino: error
	 */
	function crear_autores($id)
	{
		$this->obj->db->flush_cache();
		$this->obj->db->select('Cat_Autores.cNombre, Cat_Autores.cApellido')
		->from('Cat_Autores')
		->join('Cat_Autores_Libros', 'Cat_Autores_Libros.nIdAutor=Cat_Autores.nIdAutor')
		->where('Cat_Autores_Libros.nIdLibro=' . $id);
		$query = $this->obj->db->get();
		$data = $this->_get_results($query);
		$auts = array();
		foreach ($data as $aut) 
		{
			if (trim($aut['cNombre']) != '' || trim($aut['cApellido']) != '')
			{
				$auts[] = ((trim($aut['cNombre'])=='')?trim($aut['cApellido']):
					((trim($aut['cApellido'])=='')?trim($aut['cNombre']):
						str_replace(array('%n%', '%a%'), 
						array(trim($aut['cNombre']), trim($aut['cApellido'])),
						$this->config->item('bp.catalogo.autores.format'))));
			}
		}
		$auts = implode($this->config->item('bp.catalogo.autores.separator'), $auts);

		$this->triggers_disable();
		if (!$this->update($id, array('cAutores' => $auts)))
		{
			$this->triggers_enable();
			return FALSE;
		}
		$this->triggers_enable();

		return TRUE;
	}

	/**
	 * Quita la oferta a todos los títulos que no tienen stock
	 * @return bool
	 */
	function quitar_oferta()
	{
		$sql = ($this->db->dbdriver == 'mssql')
		?"UPDATE Cat_Fondo
		SET nIdOferta = NULL
		WHERE nIdOferta IS NOT NULL 
			AND nIdLibro NOT IN (
			SELECT nIdLibro 
			FROM Cat_Secciones_Libros
			GROUP BY nIdLibro
			HAVING SUM(nStockFirme + nStockDeposito) > 0
			)"
		:"UPDATE Cat_Fondo a
			INNER JOIN (
			SELECT nIdLibro 
			FROM Cat_Secciones_Libros
			GROUP BY nIdLibro
			HAVING SUM(nStockFirme + nStockDeposito) = 0
			) b
        	ON a.nIdLibro = b.nIdLibro AND a.nIdOferta IS NOT NULL 
			SET nIdOferta = NULL;";

		$this->db->query($sql);

		return $this->db->affected_rows();
	}

	/**
	 * Actualiza los proveedores a los que se compra por artículo
	 * @return  array 'last' => fecha límite de actualización, 'count' => registros afectados
	 */
	function compras_proveedores($last = null)
	{
		$count = 0;
		$act = time();
		$fecha = format_mssql_date($act);
		if (isset($last))
		{
			$desde = format_mssql_date($last);
			$where = "Doc_LineasAlbaranesEntrada.dAct >= {$desde} AND Doc_LineasAlbaranesEntrada.dAct < {$fecha}";
			#Borrar viejos
			$sql = ($this->db->dbdriver == 'mssql')?"DELETE Prv_Proveedores_Fondo_Compras
				FROM Prv_Proveedores_Fondo_Compras a
				INNER JOIN (
						    SELECT 
						        Doc_LineasAlbaranesEntrada.nIdLibro,
						        Doc_AlbaranesEntrada.nIdProveedor
						    FROM Doc_LineasAlbaranesEntrada 
						        INNER JOIN Doc_AlbaranesEntrada
						            ON Doc_LineasAlbaranesEntrada.nIdAlbaran = Doc_AlbaranesEntrada.nIdAlbaran
						    WHERE Doc_AlbaranesEntrada.nIdEstado <> 1 AND ({$where})
						    GROUP BY Doc_LineasAlbaranesEntrada.nIdLibro,
						        Doc_AlbaranesEntrada.nIdProveedor
				) b
				    ON a.nIdLibro = b.nIdLibro AND a.nIdProveedor = b.nIdProveedor;"
			:"DELETE a
				FROM Prv_Proveedores_Fondo_Compras a
				INNER JOIN (
						    SELECT 
						        Doc_LineasAlbaranesEntrada.nIdLibro,
						        Doc_AlbaranesEntrada.nIdProveedor
						    FROM Doc_LineasAlbaranesEntrada 
						        INNER JOIN Doc_AlbaranesEntrada
						            ON Doc_LineasAlbaranesEntrada.nIdAlbaran = Doc_AlbaranesEntrada.nIdAlbaran
						    WHERE Doc_AlbaranesEntrada.nIdEstado <> 1 AND ({$where})
						    GROUP BY Doc_LineasAlbaranesEntrada.nIdLibro,
						        Doc_AlbaranesEntrada.nIdProveedor
				) b
				    ON a.nIdLibro = b.nIdLibro AND a.nIdProveedor = b.nIdProveedor";
			$this->db->query($sql);
			$count = $this->db->affected_rows();
		}
		else
		{
			$where = "Doc_LineasAlbaranesEntrada.dAct <{$fecha}";
		}
		# Y añade los nuevos
		$sql = "INSERT INTO Prv_Proveedores_Fondo_Compras (nIdLibro, nIdProveedor, nIdLinea)
		    SELECT 
		        Doc_LineasAlbaranesEntrada.nIdLibro,
		        Doc_AlbaranesEntrada.nIdProveedor,
		        MAX(nIdLinea) nIdLinea
		    FROM Doc_LineasAlbaranesEntrada 
		        INNER JOIN Doc_AlbaranesEntrada
		            ON Doc_LineasAlbaranesEntrada.nIdAlbaran = Doc_AlbaranesEntrada.nIdAlbaran
		    WHERE Doc_AlbaranesEntrada.nIdEstado <> 1 AND ({$where})
		    GROUP BY Doc_LineasAlbaranesEntrada.nIdLibro,
		        Doc_AlbaranesEntrada.nIdProveedor";
		$this->db->query($sql);
 		$count += $this->db->affected_rows();
 		return array('last' => $act, 'count' => $count);
	}

	/**
	 * Busca los artículos sin materia de la sección indicada
	 * @param int $id Id de la sección
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $order Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param bool $conventas Solo los artículos con ventas
	 * @return array count => número de registros, data => Los registros
	 */
	function sinmateria($id, $start, $limit, $sort, $dir, $conventas, $nacional = FALSE)
	{
		$this->obj->load->model('generico/m_seccion');
		$codigo = $this->obj->m_seccion->load($id);
		$codigo_s = $codigo['cCodigo'];
		$this->db->flush_cache();
		$this->db->select('COUNT(DISTINCT Cat_Fondo.nIdLibro) as numrows')
		->from('Cat_Fondo')
		->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdLibro=Cat_Fondo.nIdLibro')
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion=Cat_Secciones_Libros.nIdSeccion')
		->join('Cat_Libros_Materias', 'Cat_Fondo.nIdLibro=Cat_Libros_Materias.nIdLibro', 'left')
		->where('Cat_Libros_Materias.nIdLibro IS NULL') # Sin materia
		->where("(Cat_Secciones.cCodigo LIKE '{$codigo_s}.%' OR Cat_Secciones.nIdSeccion = {$id})");

		if ($conventas)
		{
			$this->db->join('Doc_LineasAlbaranesSalida', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesSalida.nIdLibro');
		}

		if ($nacional)
		{
			$this->db->where('Cat_Fondo.cISBN LIKE \'978-84-%\'');
		}

		$query = $this->db->get();
		#echo array_pop($this->db->queries); die();
		$count = $this->_get_results($query);
		$count = $count[0]['numrows'];

		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.nIdLibro, Cat_Fondo.cISBN')
		->from('Cat_Fondo')
		->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdLibro=Cat_Fondo.nIdLibro')
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion=Cat_Secciones_Libros.nIdSeccion')
		->join('Cat_Libros_Materias', 'Cat_Fondo.nIdLibro=Cat_Libros_Materias.nIdLibro', 'left')
		->where('Cat_Libros_Materias.nIdLibro IS NULL') # Sin materia
		->where("(Cat_Secciones.cCodigo LIKE '{$codigo_s}.%' OR Cat_Secciones.nIdSeccion = {$id})")
		->group_by('Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.nIdLibro, Cat_Fondo.dCreacion, Cat_Fondo.cISBN');
		if ($conventas)
		{
			$this->db->join('Doc_LineasAlbaranesSalida', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesSalida.nIdLibro');
		}
		if ($nacional)
		{
			$this->db->where('Cat_Fondo.cISBN LIKE \'978-84-%\'');
		}
		$this->_limits_sort($start, $limit, $sort, $dir);
		$query = $this->db->get();
		#echo array_pop($this->db->queries); die();
		#var_dump($count); die();
		return array('count' => $count, 'data' => $this->_get_results($query));
	}

	/**
	 * Lee los n artículos indicados nacionales y con materia
	 * @param int $id Id de la materia
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $order Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @return array
	 */
	function conmateria($id, $start, $limit, $sort, $dir, $nacionales = TRUE)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.cISBN, Cat_Fondo.nIdLibro')
		->from('Cat_Fondo')
		->join('Cat_Libros_Materias', 'Cat_Fondo.nIdLibro=Cat_Libros_Materias.nIdLibro')
		->where('Cat_Libros_Materias.nIdMateria=' . $id);
		if ($nacionales)
			$this->db->where('Cat_Fondo.cISBN LIKE \'978-84-%\'');
		
		$this->_limits_sort($start, $limit, $sort, $dir);
		$query = $this->db->get();
		return $this->_get_results($query);
	}

	/**
	 * Obtiene los IDs de las portadas de una sección dada
	 * @param  int $id Id de la sección
	 * @return array
	 */
	function portadas($id)
	{
		$this->db->flush_cache();
		$this->db->select('nIdRegistro, nIdFoto, cExtension')
		->select($this->_date_field('Fotos.dAct', 'dAct'))
		->from('Cat_Fondo')
		->join('Fotos', 'Cat_Fondo.nIdLibro = Fotos.nIdRegistro')
		->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdLibro=Cat_Fondo.nIdLibro')
		->where('Fotos.cDescripcion=\'portada\'')
		->where('Cat_Secciones_Libros.nIdSeccion=' . $id);
		$query = $this->db->get();
		return $this->_get_results($query);
	}

	/**
	 * Todas las ventas de un artículo por sección, año y mes
	 * @param  int $id Id del artículo
	 * @return array
	 */
	function ventas($id)
	{
		$this->db->flush_cache();
		$this->db->select_sum('Doc_LineasAlbaranesSalida.nCantidad', 'nCantidad')
		->select('YEAR(Doc_AlbaranesSalida.dCreacion) year')
		->select('MONTH(Doc_AlbaranesSalida.dCreacion) month')
		->select('Cat_Secciones.cNombre')
		->from('Doc_LineasAlbaranesSalida')
		->join('Doc_AlbaranesSalida' ,'Doc_LineasAlbaranesSalida.nIdAlbaran = Doc_AlbaranesSalida.nIdAlbaran')
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion=Doc_LineasAlbaranesSalida.nIdSeccion')
		->where("Doc_LineasAlbaranesSalida.nIdLibro = {$id}")
		->where('Doc_AlbaranesSalida.nIdEstado = 2')
		->group_by('Doc_LineasAlbaranesSalida.nIdSeccion')
		->group_by('Cat_Secciones.cNombre')
		->group_by('YEAR(Doc_AlbaranesSalida.dCreacion)')
		->group_by('MONTH(Doc_AlbaranesSalida.dCreacion)')
		->order_by('YEAR(Doc_AlbaranesSalida.dCreacion), MONTH(Doc_AlbaranesSalida.dCreacion)');

		$query = $this->db->get();
		return $this->_get_results($query);
	}

	/**
	 * Busca los artículos con estado DESCATALOGADO que han entrado libros desde el número de días indicaod
	 * @param int $dias Número de días
	 * @return array
	 */
	function revision_estado($dias)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.nIdLibro')
		->from('Cat_Fondo')
		->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdLibro=Cat_Fondo.nIdLibro')
		->where('Cat_Fondo.nIdEstado=4')
		->where('(Cat_Secciones_Libros.nStockDeposito + Cat_Secciones_Libros.nStockFirme) > 0')
		->where("((" . $this->db->datediff('dUltimaCompra', 'GETDATE()') . " < {$dias}) AND dUltimaCompra IS NOT NULL)");
		$query = $this->db->get();
		$data = $this->_get_results($query);

		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.nIdLibro')
		->from('Cat_Fondo')
		#->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdLibro=Cat_Fondo.nIdLibro')
		->where('Cat_Fondo.nIdEstado IN(6,7,8)')
		#->where('(Cat_Secciones_Libros.nStockDeposito + Cat_Secciones_Libros.nStockFirme) > 0')
		->where("((" . $this->db->datediff('dUltimaCompra', 'GETDATE()') . " < 1) AND dUltimaCompra IS NOT NULL)");
		$query = $this->db->get();
		$data2 = $this->_get_results($query);
		return array_merge($data, $data2);
	}

	/**
	 * Arreglo de los stocks a pasar a deposito [OBSOLETO]
	 * @return array
	 */
	function check_firme()
	{
		$this->db->flush_cache();
		$sql = "select *
			from Cat_Movimiento_Stock
			where dCreacion >= {d '2013-12-20'}
				and nIdMotivo IN (22,23,24)
			order by nIdSeccion, nIdLibro, nIdMovimiento";
		$query = $this->db->query($sql);
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Arreglo de los stocks a pasar a deposito [OBSOLETO]
	 * @return array
	 */
	function check_error()
	{
		$sql ="select a.*, b.*
			from  BibliopolaStock.[dbo].[Ext_AntiguedadStock] a
				inner join Cat_Movimiento_Stock b
					on a.nIdLibro = b.nIdLibro and a.nIdSeccion = b.nIdSeccion
			where a.nIdVolcado = 3114
				and b.dCreacion >= {d '2013-12-20'}
				and nIdMotivo IN (22 ,23,24)
			)";
		$query = $this->db->query($sql);
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Busca los artículos sin foto
	 * @param int $id1 Id del artículo inicial
	 * @param int $id2 Id del artículo límite
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @return array Los registros
	 */
	function sinportada($id1 = null, $id2 = null, $start=null, $limit=null, $sort='nIdLibro', $dir='ASC', $nacional=FALSE)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.nIdLibro, Cat_Fondo.nEAN, Cat_Fondo.cISBN')
		->from('Cat_Fondo')
		->join('Fotos', 'Cat_Fondo.nIdLibro=Fotos.nIdRegistro', 'left')
		->where('Cat_Fondo.nEAN IS NOT NULL')
		->where('Fotos.nIdRegistro IS NULL');

		if (is_numeric($id1))
		{
			$this->db->where('Cat_Fondo.nIdLibro > '. $id1);
		}
		if (is_numeric($id2))
		{
			$this->db->where('Cat_Fondo.nIdLibro <= '. $id2);
		}

		if ($nacional)
		{
			$this->db->where('Cat_Fondo.cISBN LIKE \'978-84-%\'');
		}
		$this->_limits_sort($start, $limit, $sort, $dir);
		$query = $this->db->get();
		#echo array_pop($this->db->queries); die();
		#var_dump($count); die();
		return $this->_get_results($query);
	}

	/**
	 * Busca los artículos sin reseña
	 * @param int $id1 Id del artículo inicial
	 * @param int $id2 Id del artículo límite
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @return array Los registros
	 */
	function sinsinopsis($id1 = null, $id2 = null, $start=null, $limit=null, $sort='nIdLibro', $dir='ASC', $nacional=FALSE)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.nIdLibro, Cat_Fondo.nEAN, Cat_Fondo.cISBN')
		->from('Cat_Fondo')
		->join('Cat_Sinopsis', 'Cat_Fondo.nIdLibro=Cat_Sinopsis.nIdLibro', 'left')
		->where('Cat_Fondo.nEAN IS NOT NULL')
		->where('Cat_Sinopsis.nIdLibro IS NULL');

		if (is_numeric($id1))
		{
			$this->db->where('Cat_Fondo.nIdLibro > '. $id1);
		}
		if (is_numeric($id2))
		{
			$this->db->where('Cat_Fondo.nIdLibro <= '. $id2);
		}

		if ($nacional)
		{
			$this->db->where('Cat_Fondo.cISBN LIKE \'978-84-%\'');
		}
		$this->_limits_sort($start, $limit, $sort, $dir);
		$query = $this->db->get();
		#echo array_pop($this->db->queries); die();
		#var_dump($count); die();
		return $this->_get_results($query);
	}

	/**
	 * Modifica la reseña
	 * @param int $id Id del artículo
	 * @param string $sinopsis Reseña
	 * @return bool
	 */
	function set_sinopsis($id, $sinopsis)
	{
		$upd['sinopsis']['tSinopsis'] = $sinopsis;
		return $this->update($id, $upd);
	}

	/**
	 * Deuelve las portadas de la extensíón indicada
	 * @param  string $ext Extensión
	 * @param int $limit Número de elementos
	 * @return array
	 */
	function portadas_ext($ext, $limit)
	{
		$ext = $this->db->escape($ext);
		$this->db->flush_cache();
		$this->db->select('nIdRegistro')
		->from('Fotos')
		->where("cExtension = {$ext}")
		->limit($limit);
		$query = $this->db->get();
		return $this->_get_results($query);
	}

	/*
	function portadamala()
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.nIdLibro, Cat_Fondo.nEAN, Cat_Fondo.cISBN')
		->from('Cat_Fondo')
		->join('Fotos', 'Cat_Fondo.nIdLibro=Fotos.nIdRegistro')
		->where('Cat_Fondo.nEAN IS NOT NULL')
		->where('Fotos.cExtension IS NULL');
		$query = $this->db->get();
		return $this->_get_results($query);
	}
	*/

	function sj()
	{
		$this->db->flush_cache();
		$this->db->select('a.nIdPedido,
			c.cEmpresa,
			e.nIdLibro, 
			e.cTitulo,
			e.cAutores,
			b.nCantidad,
			(d.nStockFirme + d.nStockDeposito) nStock')
		->from('Doc_PedidosCliente a')
		->join('Doc_LineasPedidoCliente b', 'a.nIdPedido = b.nIdPedido')
		->join('Cli_Clientes c', 'a.nIdCliente = c.nIdCliente')
		->join('Cat_Secciones_Libros d', 'd.nIdLibro = b.nIdLibro')
		->join('Cat_Fondo e', 'e.nIdLibro = b.nIdLibro')
		->where('c.nIdGrupoCliente = 50')
		->where('b.nIdEstado = 1')
		->where('d.nIdSeccion IN (870, 904)')
		->where('(d.nStockFirme + d.nStockDeposito) > 0')
		->order_by('e.cTitulo');
		$query = $this->db->get();
		return $this->_get_results($query);
	}

	function sj2()
	{
		$this->db->flush_cache();
		$this->db->select('a.nIdPedido,
			c.cEmpresa,
			e.nIdLibro, 
			e.cTitulo,
			e.cAutores,
			b.nCantidad,
			s.cNombre,
			(d.nStockFirme + d.nStockDeposito) nStock')
		->select($this->db->date_field('dUltimaCompra', 'dUltimaCompra'))
		->select($this->db->date_field('dUltimaVenta', 'dUltimaVenta'))
		->from('Doc_PedidosCliente a')
		->join('Doc_LineasPedidoCliente b', 'a.nIdPedido = b.nIdPedido')
		->join('Cli_Clientes c', 'a.nIdCliente = c.nIdCliente')
		->join('Cat_Secciones_Libros d', 'd.nIdLibro = b.nIdLibro')
		->join('Cat_Secciones s', 's.nIdSeccion=d.nIdSeccion')
		->join('Cat_Fondo e', 'e.nIdLibro = b.nIdLibro')
		->where('c.nIdGrupoCliente = 50')
		->where('b.nIdEstado = 1')
		->where("(s.cCodigo LIKE '821.%' OR s.cCodigo LIKE '855.%' OR s.cCodigo LIKE '857.%' OR s.cCodigo LIKE '856.%' OR s.cCodigo LIKE '858.%' OR s.cCodigo LIKE '859.%')")
		->where('(d.nStockFirme + d.nStockDeposito) > 0')
		->where('b.nIdPedido IN (170625, 170629, 170667, 170632, 170702, 170707, 170709, 170710)')
		->order_by('s.cCodigo, e.cTitulo');
		$query = $this->db->get();
		return $this->_get_results($query);
	}
}

/* End of file M_articulo.php */
/* Location: ./system/application/models/catalogo/M_articulo.php */