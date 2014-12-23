<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	libraries
 * @category	core
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

define('E_IMPORT_FILE_ERROR', -1);
define('IMPORT_TIPO_AUTOR', 1);

/**
 * Importador
 * @author alexl
 *
 */
class Importador
{

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Tipo de IVA por defecto
	 *
	 * bp.import.iva
	 * @var int
	 */
	private $iva;

	/**
	 * Tipo de artículo por defecto
	 *
	 * bp.import.tipo
	 * @var int
	 */
	private $tipo;

	/**
	 * Mensaje de error
	 * @var string
	 */
	private $error;

	/**
	 * Constructor
	 * @return Out
	 */
	function __construct()
	{
		$this->obj = &get_instance();
		$this->iva = $this->obj->config->item('bp.import.iva');
		$this->tipo = $this->obj->config->item('bp.import.tipo');

		log_message('debug', 'Importador Class Initialised via ' . get_class($this->obj));
	}

	/**
	 * Último mensaje de error
	 * @return string
	 */
	function get_error_message()
	{
		return $this->error;
	}

	/**
	 * Lee todas las entradas de un fichero EXCEL
	 * @param  string $filename Nombre del fichero
	 * @return mixed array -> datos, int -> número de error
	 */
	function read($filename)
	{
		$this->obj->load->library('ExcelData');
		$data = $this->obj->exceldata->read($filename);
		if (!$data)
		{
			return E_IMPORT_FILE_ERROR;
		}
		return $data;
	}

	/**
	 * Lee un fichero EXCEL aplicando un filtro y un orden de columnas
	 * @param  string $filename Nombre del fichero
	 * @param  string $filtro   Filtro a aplicar
	 * @param  array $columns  Descripción de las columnas
	 * @return array
	 */
	function read_excel($filename, $filtro = null, $columns = null)
	{
		return $this->excel($filename, $filtro, $columns);
	}

	/**
	 * Lee los datos de artículos de un fichero EXCEL y los convierte en array
	 * @code
	 * 	$columns['isbn'] 		= array('column' => 1);
	 * 	$columns['autor'] 		= array('column' => 2, 'type' => 'string');
	 * 	$columns['titulo'] 		= array('column' => 3, 'type' => 'string');
	 * 	$columns['editorial'] 	= array('column' => 4, 'type' => 'string');
	 * 	$columns['pvp'] 		= array('column' => 5);
	 * 	$columns['año'] 		= array('column' => 6);
	 * @endcode
	 * @param string $filename Fichero EXCEL origen
	 * @param string $filtro Rango EXCEL a leer
	 * @param array $columns Nombre de las columnas a importar
	 * @return array
	 */
	function excel($filename, $filtro = null, $columns = null)
	{
		// Columnas por defecto
		if (!isset($columns))
		{
			$columns = array(
					'isbn' => array('column' => 0),
					'autor' => array(
							'column' => 1,
							'type' => 'string'
					),
					'titulo' => array(
							'column' => 2,
							'type' => 'string'
					),
					'editorial' => array(
							'column' => 3,
							'type' => 'string'
					),
					'pvp' => array('column' => 4),
					'año' => array('column' => 5)
			);
		}

		$libros = null;
		$isbns = null;
		$no_isbns = null;
		$data = $this->read($filename);

		if (!$data)
		{
			return E_IMPORT_FILE_ERROR;
		}
		else
		{
			$this->obj->load->library('ISBNEAN');
			$this->obj->load->model('catalogo/m_articulo');
			$filtro = $this->obj->exceldata->getFilter($data, $filtro);
			#echo '<pre>'; print_r($data); echo '</pre>'; die();
			$this->error = null;

			for ($i = $filtro['from'][3] - 1; $i < $filtro['to'][3]; $i++)
			{
				$libro = array();
				foreach ($columns as $k => $v)
				{
					$libro[$k] = (isset($data['cells'][$i][$v['column']])) ? $data['cells'][$i][$v['column']] : null;
					if (isset($libro[$k]) && isset($v['type']) && ($v['type'] == 'string'))
						$libro[$k] = string_encode($libro[$k]);
				}

				if (isset($libro['pvp']))
				{
					$pvp = ((float)format_tofloat($libro['pvp']));
					$iva = ((isset($libro['iva']) ? $libro['iva'] : $this->iva));
					$libro['precio'] = ((float)format_tofloat($libro['pvp'])) / (1 + ($iva / 100));
					//echo " {$libro['precio']} - {$pvp} - {$iva}<br/>";
				}

				$libro['line'] = $i + 1;
				$libro['isbn'] = trim($libro['isbn']);
				$is_isbn10 = $this->obj->isbnean->is_isbn($libro['isbn'], TRUE);
				$is_isbn13 = $this->obj->isbnean->is_isbn($libro['isbn']);
				if ($is_isbn10 || $is_isbn13)
				{
					// Busca el artículo
					$find = $this->obj->m_articulo->search($libro['isbn']);
					if (count($find) > 0)
					{
						$encontrado = $this->obj->m_articulo->load($find[0]['id']);
						$encontrado['original'] = $libro;
						$libros[] = $encontrado;
					}
					else
					{
						$isbns[] = $libro;
					}
				}
				else
				{
					$no_isbns[] = $libro;
				}
			}
		}

		return array(
				'libros' => $libros,
				'isbns' => $isbns,
				'no_isbn' => $no_isbns,
				'filtro' => $filtro
		);
	}
	
	/**
	 * Función de carga de EXCEL con un documento genérico
	 * @param  string $file            Nombre del archivo
	 * @param  string $rango           Rango de selección
	 * @param  bool $crear_libros    TRUE: crea los artículos que no existen
	 * @param  bool $crear_autor     TRUE: crea los autores
	 * @param  bool $crear_coleccion TRUE: crea las colecciones
	 * @param  Messages $messages        Gestor de mensajes
	 * @param  MY_Language $lang            Gestor de i18n
	 * @return array (libros => los libros, no_isbn => los artículos sin ISBN)
	 */
	function excel_generic($file, $rango, $crear_libros, $crear_autor, $crear_coleccion, &$messages, &$lang)
	{
		$columns = array(
				'cantidad'=> array('column' => 0),
				'descuento'=> array('column' => 1),
				'isbn' => array('column' => 2),
				'autor' => array(
						'column' => 3,
						'type' => 'string'
				),
				'titulo' => array(
						'column' => 4,
						'type' => 'string'
				),
				'editorial' => array(
						'column' => 5,
						'type' => 'string'
				),
				'pvp' => array('column' => 6),
				'año' => array('column' => 7)
		);
		$data = $this->excel($file, $rango, $columns);
		#print_r
		#echo '<pre>'; print_r($data); die();
		$error = FALSE;

		if ($data == E_IMPORT_FILE_ERROR)
		{
			$messages->error($lang->line('concursos_excel_test_noexcel'));
			$error = TRUE;
		}
		else
		{
			// Información de los encontrados
			$messages->info(sprintf($lang->line('concursos_excel_aplicando_filter'), $data['filtro']['filter']));

			$messages->info($lang->line('concursos_excel_libros_encontrados'));
			if (count($data['libros']) > 0)
			{
				foreach($data['libros'] as $l)
				{
					#var_dump(format_price($l['original']['precio']), $l['original']['pvp']); die();
					$texto = implode(', ', $l['original']);
					$link = format_enlace_cmd($l['id'], site_url('catalogo/articulo/index/' . $l['id']));													
					$l = sprintf($lang->line('concursos_excel_libro_correcto_encontrado'), $l['original']['line'], $link, $texto, 
						isset($l['original']['precio'])?format_price($l['original']['precio']):'??',
						format_price($l['original']['pvp']), format_percent($l['original']['descuento']));
					$messages->info($l, 1);
				}
			}
			$messages->info(sprintf($lang->line('concursos_excel_libros_encontrados_total'), count($data['libros'])));

			$messages->info($lang->line('concursos_excel_libros_no_encontrados'));
			if (count($data['isbns']) > 0)
			{
				foreach($data['isbns'] as $l)
				{
					$texto = implode(', ', $l);
					$l = sprintf($lang->line('concursos_excel_libro_correcto_noencontrado'), $l['line'], $texto);
					$messages->warning($l, 1);
				}
			}
			if (count($data['no_isbn']) > 0)
			{
				foreach($data['no_isbn'] as $l)
				{
					$texto = implode(', ', $l);
					$l = sprintf($lang->line('concursos_excel_no_isbn'), $l['line'], $texto);
					$messages->warning($l, 1);
				}
			}
			$messages->warning(sprintf($lang->line('concursos_excel_libros_no_encontrados_total'), count($data['no_isbn']) + count($data['isbns'])));

			// Crea los libros
			if ($crear_libros && (count($data['isbns']) > 0))
			{
				$messages->info($lang->line('concurso_creando_libros'));
				$creados = $this->crear_libros($data['isbns'], $crear_autor, $crear_coleccion);

				if ($creados === FALSE)
				{
					$messages->error($this->importador->get_error_message());
					$error = TRUE;
				}
				else
				{
					// Libros
					$this->obj->load->model('catalogo/m_tipolibro');
					$tipos = array();
					foreach($creados['libros'] as $libro)
					{
						$messages->info(sprintf($lang->line('concurso_creando_libro'), string_encode($libro['cTitulo'])), 2);
						if (!isset($libro['nIdEditorial']))
						{
							$messages->warning($lang->line('concurso_creando_libros_no_editorial'), 3);
						}
						if (!isset($libro['nIdProveedor']))
						{
							$messages->warning($lang->line('concurso_creando_libros_no_proveedor'), 3);
						}
						if (!isset($libro['autores']))
						{
							$messages->warning($lang->line('concursos_libro_sin_autores'), 3);
						}
						$messages->info(sprintf($lang->line('concursos_libro_creado'), $libro['id']), 2);
						if (!isset($tipos[$libro['nIdTipo']]))
						{
							$r = $this->obj->m_tipolibro->load($libro['nIdTipo']);
							$tipos[$libro['nIdTipo']] = $r['fIVA'];
						}
						$data['libros'][] = array('id' => $libro['id'], 'fIVA' => $tipos[$libro['nIdTipo']], 'fPrecio' => $libro['fPrecio']);
						#print '<pre>'; var_dump($libro); print '</pre>';
					}

					// Editoriales
					if (isset($creados['editoriales']))
					{
						foreach($creados['editoriales'] as $editorial)
						{
							$messages->info(sprintf($lang->line('concurso_creando_editorial'), string_encode($editorial['cNombre'])), 1);
							$messages->info(sprintf($lang->line('concursos_editorial_creada'), $editorial['id']), 1);
						}
					}

					// Autores
					if (isset($creados['autores']))
					{
						foreach($creados['autores'] as $autor)
						{
							$messages->info(sprintf($lang->line('concurso_creando_autor'), string_encode(implode(', ', $autor))), 1);
							$messages->info(sprintf($lang->line('concursos_autor_creado'), $autor['id']), 1);
						}
					}
				}
			}
			else
			{
				$messages->warning($lang->line('concurso_no_crear_articulos'));
			}
		}

		return (!$error)?$data:FALSE;
	}
	/**
	 * Busca y crea la editorial si no existe
	 * @param string $isbn ISBN 
	 * @param string $nombre Nombre de la editorial
	 * @return mixed, FALSE si error, array(editorial => Id editorial, proveedor => Id proveedor, new => array de editoriales nuevas)
	 */
	function crear_editorial($isbn, $nombre)
	{
		$this->obj->load->model('catalogo/m_editorial');
		$parts = $this->obj->isbnean->isbnparts($isbn);
		$editoriales = array();
		$id_editorial = $id_proveedor = null;

		if (isset($parts['publisher_id']))
		{
			$editorial = $this->obj->m_editorial->search($parts['publisher_id'], 0, 1);
			if (!isset($editorial[0]))
			{
				// Se crea la editorial
				if (isset($libro['editorial']))
				{
					$insert = array(
							'cNombre' => string_decode($nombre),
							'codigos' => array( array('cCodigo' => $parts['publisher_id']))
					);
					$id_editorial = $this->obj->m_editorial->insert($insert);
					//echo 'CREANDO EDITORIAL';
					if ($id_editorial < 0)
					{
						$this->error = $this->obj->m_editorial->error_message();
						return FALSE;
					}
					else
					{
						$insert['id'] = $id_editorial;
						$editoriales[] = $insert;
					}
					$id_proveedor = null;
				}
			}
			else
			{
				// Editorial ya existente
				$editorial = $this->obj->m_editorial->load($editorial[0]['id']);
				$id_editorial = $editorial['id'];
				$id_proveedor = $editorial['nIdProveedor'];
			}
		}
		return array('editorial' => $id_editorial, 'proveedor' => $id_proveedor, 'new' => $editoriales);		
	}

	/**
	 * Busca y crea la editorial si no existe
	 * @param string $autores Nombre de los autores separados por ; y el nombre y apellido por ,
	 * @param bool $crear Crea los autores si no existen 
	 * @return mixed, FALSE si error, array(autores => array de los autores, new => array de autores nuevos)
	 */
	function crear_autores($autores, $crear = TRUE)
	{
		$this->obj->load->model('catalogo/m_autor');
		$lautores = preg_split('/\//', string_decode($autores));
		$autores = array();
		$new = array();
		foreach ($lautores as $a)
		{
			$a = preg_replace('/\s\s+/', ' ', $a);
			$n = preg_split('/\,/', $a);
			$aut = null;
			if (count($n) == 2)
			{
				$aut['cNombre'] = ucwords(trim($n[1]));
				$aut['cApellido'] = ucwords(trim($n[0]));
			}
			else
			{
				$aut['cNombre'] = ucwords(trim($a));
			}
			$nombre = string_encode(trim(implode(' ', $aut)));
			if (isset($nombre) && ($nombre != ''))
			{
				$nombre =str_replace(array('(', ')'), '', $nombre);
				#echo "BUSCANDO AUTOR: {$nombre}<br/>";
				$res = $this->obj->m_autor->search($nombre, 0, 1);
				#print '<pre>'; var_dump($res); print '</pre>';
				if (isset($res[0]))
				{
					$autores[] = array(
							'nIdAutor' => $res[0]['id'],
							'nIdTipoAutor' => IMPORT_TIPO_AUTOR
					);
				}
				elseif ($crear)
				{
					$id_autor = $this->obj->m_autor->insert($aut);
					if ($id_autor < 0)
					{
						$this->error = $this->obj->m_autor->error_message();
						return FALSE;
					}
					else
					{
						$autores[] = array(
								'nIdAutor' => $id_autor,
								'nIdTipoAutor' => IMPORT_TIPO_AUTOR
						);
						$aut['id'] = $id_autor;
						$new[] = $aut;
					}
				}
			}
		}	
		return array('autores' => $autores, 'new' => $new);
	}

	/**
	 * Busca y crea las palabras clave si no existen
	 * @param string $palabrasclave Array de palabras clave 
	 * @return mixed, FALSE si error, array(palabras => array de las palabras, new => array de palabras clave nuevas)
	 */
	function crear_palabrasclave($palabrasclave)
	{
		$this->obj->load->model('catalogo/m_palabraclave');
		$palabras = array();
		$new = array();
		$palabrasclave =preg_split('/\n/', $palabrasclave);
		#var_dump($palabrasclave); die();
		foreach ($palabrasclave as $nombre)
		{
			$nombre = trim($nombre);
			if (!empty($nombre))
			{
				$col2 = str_replace(array(' ', '.'), '', $nombre);
				$res = $this->obj->m_palabraclave->get(0, 0, 0, 0, "LTRIM(RTRIM(REPLACE(REPLACE(cPalabraClave, ' ', ''), '.', ''))) =". $this->obj->db->escape($col2));
				if (isset($res[0]))
				{
					$palabras[] = array(
						'nIdPalabraClave' => $res[0]['id'],
					);
				}
				else
				{
					$pal = array('cPalabraClave' => trim($nombre));
					#var_dump($pal);
					$id = $this->obj->m_palabraclave->insert($pal);
					if ($id < 0)
					{
						$this->error = $this->obj->m_palabraclave->error_message();
						return FALSE;
					}
					else
					{
						$palabras[] = array(
							'nIdPalabraClave' => $id,
						);
						$pal['id'] = $id;
						$new[] = $pal;
					}
				}
			}
		}	
		return array('palabras' => $palabras, 'new' => $new);
	}

	/**
	 * Busca y crea la colección si no existe
	 * @param string $nombre Nombre de la colección 
	 * @param string $id_editorial Id de la editorial
	 * @param bool $crear Crea la colección si no existe
	 * @return mixed, FALSE si error, array(id => Id de la colección, new => array de colecciones nuevas)
	 */
	function crear_coleccion($nombre, $id_editorial, $crear = TRUE)
	{
		$this->obj->load->model('catalogo/m_coleccion');
		$colecciones = array();
		if (!empty($nombre) && isset($id_editorial))
		{
			$col2 = trim(str_replace(array(' ', '.'), '', $nombre));
			$coleccion = $this->obj->m_coleccion->get(0, 0, 0, 0, "nIdEditorial={$id_editorial} AND LTRIM(RTRIM(REPLACE(REPLACE(cNombre, ' ', ''), '.', ''))) =". $this->obj->db->escape($col2));
			if (!isset($coleccion[0]) && $crear)
			{
				$col = array(
					'cNombre' => $nombre,
					'nIdEditorial' =>  $id_editorial
				);
				$id_col = $this->obj->m_coleccion->insert($col); 
				if ($id_col < 0)
				{
					$this->error = $this->obj->m_coleccion->error_message();
					return FALSE;
				}
				$col['id'] = $id_col;
				$colecciones[] = $col;
			}
			else
			{
				$id_col = $coleccion[0]['id'];
			}
			return array('id' => $id_col, 'new' => $colecciones);
		}
		return null; 
	}

	/**
	 * Crea los libros en la base de datos. También crea los autores y la editoriales
	 * si no existen
	 * @param array $libros Datos de los libros a crear
	 * @param bool $autor Crear autores si no existen
	 * @param bool $coleccion Crear colección si no existe 
	 * @return bool/array FALSE: ha habido error, array: libros, editoriales, autores
	 * creados
	 */
	function crear_libros($libros, $autor = TRUE, $coleccion = TRUE)
	{
		$this->obj->load->library('ISBNEAN');
		$this->obj->load->model('catalogo/m_articulo');
		$this->obj->load->model('catalogo/m_articulopalabraclave');
		$this->obj->load->model('catalogo/m_articuloautor');
		$this->obj->load->model('generico/m_idioma');
		$creados = array();
		$editoriales = array();
		$palabrasclave = array();
		$autores = array();
		$colecciones = array();
		$this->obj->db->trans_begin();
		foreach ($libros as $libro)
		{
			// Editor y proveedor
			$isbn = $this->obj->isbnean->to_isbn($libro['isbn']);
			$ed = $this->crear_editorial($isbn, $libro['editorial']);
			if ($ed===FALSE)
			{
				$this->obj->db->trans_rollback();
				return FALSE;			
			} 
			$editoriales = array_merge($editoriales, $ed['new']);
			$id_editorial = $ed['editorial'];
			$id_proveedor = $ed['proveedor'];

			// Autores
			if (isset($libro['autor']))
			{
				$aut = $this->crear_autores($libro['autor'], $autor);
				if ($aut === FALSE)
				{
					$this->obj->db->trans_rollback();
					return FALSE;			
				} 
				$autores = array_merge($autores, $aut['new']);
				$libro['autores'] = $aut['autores'];
			}
			
			// Palabras clave
			if (isset($libro['materias']) && count($libro['materias'])>0)
			{
				$pal = $this->crear_palabrasclave($libro['materias']);
				if ($pal === FALSE)
				{
					$this->obj->db->trans_rollback();
					return FALSE;			
				} 
				$palabrasclave = array_merge($palabrasclave, $pal['new']);
				$libro['palabrasclave'] = $pal['palabras'];
			}
			
			// Idioma
			$lang = null; 
			if (isset($libro['language']))
			{
				$res = $this->obj->m_idioma->get(0, 0, 0, 0, 'cISOCode3='. $this->obj->db->escape($libro['language']));
				if (isset($res[0]))
				{
					$lang = $res[0]['id'];
				}
			}
			
			// Fecha edicion
			$fecha = null;
			if (isset($libro['año']) && is_numeric($libro['año']))
				$fecha = mktime(0, 0, 0, 1, 1, $libro['año']);				
			if (isset($libro['publicacion'])) 
				$fecha = $libro['publicacion']; 
			
			// Colección
			$id_col = NULL;
			if (isset($libro['coleccion']))
			{
				$col = $this->crear_coleccion($libro['coleccion'], $id_editorial, $coleccion);
				if (isset($col))
				{
					$colecciones = array_merge($colecciones, $col['new']);
					$id_col =  $col['id'];
				}
			}

			//Libro
			$insert = array(
				'cTitulo' => ucfirst(/*string_decode*/($libro['titulo'])),
				'cISBN' => $isbn,
				'fPrecio' => isset($libro['precio']) ? $libro['precio'] : null,
				'nIdTipo' => isset($libro['tipo']) ? $libro['tipo'] : $this->tipo,
				'dEdicion' => $fecha,
				'nIdEditorial' => $id_editorial,
				'nIdColeccion' => $id_col,
				'nIdIdioma' => $lang,
				'cNColeccion' => isset($libro['ncol']) ? $libro['ncol'] : null, 
				'nIdProveedor' => $id_proveedor,
				'cEdicion' => isset($libro['edicion']) ? $libro['edicion'] : null,
				'nPag' => isset($libro['paginas']) ? $libro['paginas'] : null,
			);

			#var_dump($insert); die();


			if (isset($libro['autores']))
			{
				$insert['autores'] = $libro['autores'];
			}

			if (isset($libro['palabrasclave']))
			{
				$insert['palabrasclave'] = $libro['palabrasclave'];
			}
			
			if (isset($libro['sinopsis']))
			{
				$insert['sinopsis']['tSinopsis'] = $libro['sinopsis'];				
			}
			#var_dump($insert); die();
			if (isset($libro['id']))
			{
				// Borra autores y palabras clave
				$id_libro = $libro['id'];
				
				if (isset($libro['autores']) || isset($libro['palabrasclave']))				
				{
					$old = $this->obj->m_articulo->load($id_libro, array('autores', 'palabrasclave'));
					if (isset($libro['autores']))
					{
						foreach($old['autores'] as $a)
						{
							if (!$this->obj->m_articuloautor->delete($a['id']))
							{
								$this->error = $this->obj->m_articuloautor->error_message();
								$this->obj->db->trans_rollback();
								return FALSE;							
							}
						} 
					}
					if (isset($libro['palabrasclave']))
					{
						foreach($old['palabrasclave'] as $a)
						{
							if (!$this->obj->m_articulopalabraclave->delete($a['id']))
							{
								$this->error = $this->obj->m_articulopalabraclave->error_message();
								$this->obj->db->trans_rollback();
								return FALSE;							
							}
						} 
					}
				}
				// Quita los null
				foreach($insert as $k => $v)
				{
					if (!isset($v)) unset($insert[$k]);
				}
				#var_dump($insert); die();
				if (!$this->obj->m_articulo->update($id_libro, $insert))
				{
					$this->error = $this->obj->m_articulo->error_message();
					$this->obj->db->trans_rollback();
					return FALSE;												
				}
			}
			else
			{						
				$id_libro = $this->obj->m_articulo->insert($insert);
				if ($id_libro < 0)
				{
					$this->error = $this->obj->m_articulo->error_message();
					$this->obj->db->trans_rollback();
					return FALSE;
				}
				$insert['original'] = $libro;
				$insert['id'] = $id_libro;
				$creados[] = $insert;
			}
			
			# Portada
			if (isset($libro['portada']))
			{
				#var_dump($id_libro, $libro['portada']); die();
				if (!$this->obj->m_articulo->set_portada($id_libro, $libro['portada']))
				{
					$this->error = $this->obj->m_articulo->error_message();
					$this->obj->db->trans_rollback();
					return FALSE;					
				}
			}
		}
		$this->obj->db->trans_commit();

		return array(
				'libros' => $creados,
				'autores' => $autores,
				'colecciones' => $colecciones,
				'palabrasclave' => $palabrasclave,
				'editoriales' => $editoriales
		);
	}

	/**
	 * Crea un pedido de cliente a partir de las líneas indicadas. Se debe indicar en
	 * cada línea
	 * el <strong>id</strong> del artículo, así como el fIVA y fPrecio.
	 * El fDescuento y la nCantidad son opcionales
	 * @param int $cliente Id del cliente
	 * @param int $seccion Id de la sección
	 * @param array $libros Array de artículos
	 * @param string $refint Referencia interna del pedido
	 * @param string $refcli Referencia del cliente
	 */
	function crear_pedido_cliente($cliente, $seccion, $libros, $refint = null, $refcli = null)
	{
		$this->obj->load->model('generico/m_seccion');
		$dto = isset($dto) ? $dto : 0;
		$this->obj->load->model('ventas/m_pedidocliente');
		$lineas = array();
		foreach ($libros as $l)
		{
			$lineas[] = array(
					'nIdLibro' => $l['id'],
					'nIdSeccion' => $seccion,
					'nCantidad' => (isset($l['nCantidad']) ? $l['nCantidad'] : 1),
					'fIVA' => $l['fIVA'],
					'fPrecio' => $l['fPrecio'],
					'fDescuento' => (isset($l['fDescuento']) ? $l['fDescuento'] : 0),
			);
		}
		$data = array(
				'nIdCliente' => $cliente,
				'cRefInterna' => $refint,
				'cRefCliente' => $refcli,
				'lineas' => $lineas
		);

		$id = $this->obj->m_pedidocliente->insert($data);
		if ($id < 0)
		{
			$this->error = $this->obj->m_pedidocliente->error_message();
			return FALSE;
		}
		else
		{
			return $id;
		}
	}

	/**
	 * Crea un pedido de proveedor a partir de las líneas indicadas. Se debe indicar
	 * en
	 * cada línea
	 * el <strong>id</strong> del artículo, así como el fIVA y fPrecio.
	 * El fDescuento y la nCantidad son opcionales
	 * @param int $cliente Id del cliente
	 * @param int $seccion Id de la sección
	 * @param array $libros Array de artículos
	 * @param string $refint Referencia interna del pedido
	 * @param string $refcli Referencia del cliente
	 */
	/*function crear_albaran_entrada($proveedor, $libros, $refint = null, $refprv = null)
	{
		$this->obj->load->model('compras/m_albaranentrada');
		return $this->crear_documento_generico(FALSE, $proveedor, $libros, $this->m_albaranentrada, FALSE, $refint, $refprv);
	}*/

	/**
	 * Crea un documento de cliente o proveedor  a partir de las líneas indicadas. Se debe indicar
	 * en cada línea el <strong>id</strong> del artículo, así como el fIVA y fPrecio.
	 * El fDescuento y la nCantidad son opcionales
	 * @param bool $clpv  FALSE: es un proveedor, TRUE: es un cliente
	 * @param int $idclpv  Id del cliente/proveedor
	 * @param array $libros Array de artículos
	 * @param int $seccion Id de la sección (NULL no hay sección)
	 * @param string $refint Referencia interna del pedido
	 * @param string $refcli Referencia del cliente
	 */
	function crear_documento_generico($clpv, $idclpv, $libros, $model, $seccion = FALSE, $refint = null, $refclprv = null)
	{
		$this->obj->load->model('generico/m_seccion');
		$dto = isset($dto) ? $dto : 0;
		$this->obj->load->model('compras/m_albaranentrada');
		$lineas = array();
		foreach ($libros as $l)
		{
			$linea = array(
					'nIdLibro' 		=> $l['id'],
					'nCantidad' 	=> (isset($l['nCantidad']) ? $l['nCantidad'] : 1),
					'fIVA' 			=> $l['fIVA'],
					'fPrecio' 		=> $l['fPrecio'],
					'fPrecioVenta' 	=> format_add_iva($l['fPrecio'], $l['fIVA']),
					'fDescuento' 	=> (isset($l['fDescuento']) ? $l['fDescuento'] : 0),
			);
			if(isset($seccion)) $linea['nIdSeccion'] = $seccion;
			$lineas[]  = $linea;
		}
		$data = array(
				($clpv?'nIdCliente':'nIdProveedor') 	=> $idclpv,
				'cRefInterna' 							=> $refint,
				($clpv?'cRefCliente':'cRefProveedor')  	=> $refclprv,
				'lineas' 								=> $lineas
			);

		$id = $model->insert($data);
		if ($id < 0)
		{
			$this->error = $model->error_message();
			return FALSE;
		}
		else
		{
			return $id;
		}
	}

	/**
	 * Obtiene el ISBN de los datos ONIX
	 * @param  array $data Datos ONIX
	 * @return string
	 */
	function get_isbn($data)
	{
		$isbn = null;
		if (isset($data['ProductIdentifier'][0]))
		{
			foreach($data['ProductIdentifier'] as $id)
			{
				if ($id['ProductIDType'] == '03')
				{
					$isbn = $id['IDValue'];
					break;
				}
			}
		}
		elseif (isset($data['ProductIdentifier']) && $data['ProductIdentifier']['ProductIDType'] == '03')
		{
			$isbn = $data['ProductIdentifier']['IDValue']; 
		}
		return $isbn;
	}
	
	/**
	 * Convierte un formato ONIX al formato de importación interno
	 * @param array $data Campos en formato ONIX
	 * @return array Campos en formato de Importación Bibliopola
	 */
	function onix($data)
	{
		$autores = array();
		if (isset($data['Contributor']))
		{
			foreach($data['Contributor'] as $aut)
			{
				if (isset($aut['ContributorRole']) && ($aut['ContributorRole'] == 'A01') && isset($aut['PersonNameInverted']))
				{
					$autores[] = $aut['PersonNameInverted'];
				}
			}
		}
		$autores = implode(';', $autores);
		if ($autores == '') $autores = null;
		$lang = null;
		if (isset($data['Language']))
		{
			if (isset($data['Language']['LanguageCode']))
			{
				$lang = $data['Language']['LanguageCode'];
			}
			else
			{
				foreach ($data['Language'] as $l)
				{
					if ($l['LanguageRole'] == '01')
					{
						$lang = $l['LanguageCode'];
						break;
					} 
				}
			}
		}
		$isbn = $this->get_isbn($data);
		$sinopsis = null;
		if (isset($data['OtherText'][0]))
		{
			foreach ($data['OtherText'] as $t)
			{
				if (isset($t['Text']))
					$sinopsis[] = $t['Text'];
			}
			$sinopsis = implode( '<br/><br/>', $sinopsis);
		}
		elseif (isset($data['OtherText']['Text']))
		{
			$sinopsis = $data['OtherText']['Text'];
		}
		$materias = null;
		if (isset($data['Subject'][0]))
		{
			foreach ($data['Subject'] as $s)
			{
				if (isset($s['SubjectHeadingText']))
				{
				 	$materias[] = $s['SubjectHeadingText'];
				}
				elseif (isset($s['SubjectCode']))
				{
				 	$materias[] = $s['SubjectCode'];
				}
			}
			$materias = implode("\n", $materias);
		}
		elseif (isset($data['Subject']['SubjectCode']))
		{
			if (isset($data['Subject']['SubjectHeadingText']))
			{
			 	$materias[] = $data['Subject']['SubjectHeadingText'];
			}
			elseif (isset($data['Subject']['SubjectCode']))
			{
			 	$materias[] = $data['Subject']['SubjectCode'];
			}
			$materias = implode("\n", $materias);
		}		
		$iva = (isset($data['SupplyDetail']['Price']['TaxRatePercent1']))?((float) $data['SupplyDetail']['Price']['TaxRatePercent1']):4;
		if (!is_numeric($iva) || ($iva == 0)) $iva = 4;
		$precio = null;
		#var_dump($data['SupplyDetail']); die();
		if (isset($data['SupplyDetail']['Price']))
		{
			if (isset($data['SupplyDetail']['Price']['PriceTypeCode']))
			{
				$t = $data['SupplyDetail']['Price']['PriceTypeCode'];
				#var_dump($t); die();
				if (in_array($t, array('01', '03', /*'04',*/ '05', '06', '08', '11', '13', '21', '23', '41')))
				{
					$precio = (float)$data['SupplyDetail']['Price']['PriceAmount'];					
					#var_dump($precio); die();
				}
				else
				{
					$precio = format_quitar_iva((float)$data['SupplyDetail']['Price']['PriceAmount'], $iva);
				}
			}
		}
		$libro = array(
			'isbn' => $isbn,
			'editorial' => isset($data['Publisher'])?$data['Publisher']['PublisherName']:null,
			'autor' => $autores,
			'paginas' => isset($data['NumberOfPages'])?(int)$data['NumberOfPages']:null,
			'edicion' => isset($data['EditionNumber'])?(int)$data['EditionNumber']:null,
			'titulo'  => isset($data['Title']['TitleText'])?$data['Title']['TitleText']:(isset($data['Title'][0]['TitleText'])?$data['Title'][0]['TitleText']:null),
			'precio' => $precio,
			#'iva' => $iva,
			'language' => $lang,
			'coleccion' => isset($data['Series']['Title']['TitleText'])?$data['Series']['Title']['TitleText']:null,
			'ncol' => isset($data['Series']['NumberWithinSeries'])?(int)$data['Series']['NumberWithinSeries']:null,
			'tipo' => ($iva == 4)?1:11,
			'sinopsis' => $sinopsis,
			'materias' => $materias,
			'portada' => isset($data['MediaFile']['MediaFileLink'])?$data['MediaFile']['MediaFileLink']:null,
			'publicacion' => isset($data['PublicationDate'])?mktime(0, 0, 0, substr($data['PublicationDate'], 4, 2), substr($data['PublicationDate'], 6, 2) , substr($data['PublicationDate'], 0, 4)):null, #YYYYMMDD Transformar
		) ;
		return $libro;
	}

	/**
	 * Lee un artículo de un fichero ONIX
	 * @param  string $file Fichero ONIX
	 * @return array datos del producto
	 */
	function onix_file($file)
	{
		$src = file_get_contents($file);
		$this->obj->load->library('Utils');
		$res = $this->obj->utils->xml2array($src);
		if ($res && isset($res['ONIXMessage']['Product']))
		{
			return $res['ONIXMessage']['Product'];
		}
		return $res;			
	}
}

/* End of file importador.php */
/* Location: ./system/libraries/importador.php */
