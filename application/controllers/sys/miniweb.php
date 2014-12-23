<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 *
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

define('MAX_ITEMS', 1000);

/**
 * Controlador de interfaz de acceso a Solr
 *
 */
class Miniweb extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Solr
	 */
	function __construct()
	{
		parent::__construct(null, null, FALSE);
		//$this->load->library('SolrApi');
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#index($open_id, $data)
	 */
	function index($query = null)
	{
		$query = isset($query)?$query:$this->input->get_post('query');
		if ($query)
		{
			$data['results'] = $this->solrapi->query($query);
		}
		$data['query'] = $query;
		$this->load->view('sys/solr_query.php', $data);
	}

	/**
	 * Realiza una búsqueda de datos
	 * @param string $query Texto a buscar
	 * @return JSON
	 */
	function query($query = null)
	{
		$query = isset($query)?$query:$this->input->get_post('query');
		if ($query)
		{
			$data = $this->solrapi->query($query);
			echo $this->out->send($data);
		}
		else
		{
			echo $this->out->message(FALSE, $this->lang->line('mensaje_faltan_datos'));
		}
	}

	function materias($file = FALSE)
	{
		if (isset($_SERVER['REMOTE_ADDR'])) {
			die($this->lang->line('task-runner-cmd-only'));
		}

		// Tarda tiempo el amigo
		set_time_limit(0);
		$this->output->enable_profiler(TRUE);
		$this->benchmark->mark('materias_start');

		print "Leyendo Materias\n";

		$this->load->model('catalogo/m_materia');
		$fields = 'nIdMateria, cNombre, nIdMateriaPadre, cCodMateria, nHijos, nLibrosTotal, nLibrosLocal';

		$data = $this->m_materia->get(null, null, 'cNombre', null, null, $fields);
		$alias = array (
			'id'				=> 'nIdMateria',
			'nIdLibro'			=> 'nIdMateria',
			'cTitulo'			=> 'cNombre',
			'nEAN'				=> 'nIdMateriaPadre',
			'cISBN'				=> 'cCodMateria',
			'fPrecio'			=> 'nHijos',
			'fPrecioOriginal' 	=> 'nLibrosTotal',
			'nPag' 				=> 'nLibrosLocal');
			
		$defaults = array ('fPrecio' => 0, 'nEAN' => 0, 'tipo' => 'M', 'fPrecioOriginal' => 0, 'nPag' => 0);

		$mat = $this->_process_data($data, $alias, $defaults);

		$dir = $this->config->item('bp.solr.path.materia');
		// Borra el anterior
		$this->utils->recursiveDelete($dir);
		// Crea el nuevo
		mkdir($dir);
		// Crea el fichero XML
		$xml = $this->solrapi->create_document($mat);
		file_put_contents("{$dir}mat.xml", $xml);

		print "UPDATE\n";
		print $this->solrapi->update($xml);
		print "COMMIT\n";
		print $this->solrapi->commit();

		$total = count($mat);
		print "Se han creado {$total} documentos\n";
		print "Proceso terminado\n";
		//$this->out->message(TRUE, "Se han creado {$count} materias");
	}

	/**
	 * Envía los libros al servidor SOLR
	 * @param string $type all: todos, web: los que se ven en la web, noweb: los que no se ven en la web
	 * @return CLI
	 */
	function articulos($type = 'web')
	{
		if (isset($_SERVER['REMOTE_ADDR'])) {
			die($this->lang->line('task-runner-cmd-only'));
		}

		$this->_log('Inicio');
		// Tarda tiempo el amigo
		set_time_limit(0);
		$this->output->enable_profiler(TRUE);
		$this->benchmark->mark('articulos_start');

		$this->_log('Leyendo libros');
		$this->load->model('catalogo/m_articulo');

		// Lee los IDs
		$fields = 'nIdLibro';
		if (is_numeric($type))
		{
			$filter = "nIdLibro >= {$type}";
		}
		else
		{
			switch ($type)
			{
				case 'all':
					$filter = null;
					break;
				case 'noweb':
					$filter = 'ISNULL(bMostrarWebManual, bMostrarWeb) = 0';
					break;
				case 'web':
					$filter = 'ISNULL(bMostrarWebManual, bMostrarWeb) = 1';
					break;
			}
		}

		#$data = $this->m_articulo->get(null, null, 'nIdLibro', null, $filter, $fields);
		#print $data[94000 + 11000]['nIdLibro']; die();

		$dir = $this->config->item('bp.export.path.articulo');

		$alias = array (
			'id'				=> 'nIdLibro',
			'nIdLibro'			=> 'nIdLibro',
			'cTitulo'			=> 'cTitulo',
			'cISBN' 			=> 'cISBN',
			'cISBN10' 			=> 'cISBN10',
			'cISBNBase' 		=> 'cISBNBase',
			'nISBNBase10'		=> 'nISBNBase10',
			'nEAN' 				=> 'nEAN',
			'tSinopsis' 		=> 'tSinopsis',
			'cAutores' 			=> 'cAutores',
			'nIdTipo' 			=> 'nIdTipo',
			'cEdicion'			=> 'cEdicion',
			'fPrecio'			=> 'fPrecio',
			'fPrecioOriginal' 	=> 'fPrecioOriginal',
			'fIVA'				=> 'fIVA',
			'nIdEditorial'		=> 'nIdEditorial',
			'nColVol'			=> 'nColVol', 
			'nIdEstado'			=> 'nIdEstado',
			'nPag'				=> 'nPag',
			'cSoporte'			=> 'cSoporte',
			'cIdioma'			=> 'cIdioma',
			'cFormato'			=> 'cFormato',
			'cEditorial'		=> 'cEditorial',
			'nYear'				=> 'nYear',
			'nIdMateria'		=> 'nIdMateria',
			'cPlazoEnvio'		=> 'cPlazoEnvio',
			'nStock'			=> 'nStock',
			'nIdAutor'			=> 'nIdAutor',
			'nIdColeccion'		=> 'nIdColeccion',
			'cColeccion'		=> 'cColeccion',
			'bStatus'			=> 'bStatus');

		$defaults = array ('fPrecio' => 0, 'tipo' => 'L');

		$inicio = time();

		$this->_log("Se van a crear los archivos en {$dir}");
		// Borra el anterior
		$this->utils->recursiveDelete($dir);
		// Crea el nuevo
		mkdir($dir);

		$libros = array();
		$count = 0;
		$total = 0;
		$fcount = 0;

		$this->db->select('nIdLibro')->from('Cat_Fondo');
		if (isset($filter)) $this->db->where($filter);

		$q = $this->db->get();
		if ($q->num_rows() > 0)
		{
			$this->_log('Se han leído ' . $q->num_rows() . " libros");
			foreach ($q->result_array() as $row)
			{
				$count++;
				$total++;
				$id = $row['nIdLibro'];
				$this->_log("Leyendo {$id} - {$total}/" . $q->num_rows());
				$libro = $this->m_articulo->load(/*$v['id']*/ $id, TRUE);

				// prepara los datos
				$libro['tSinopsis'] = (isset($libro['sinopsis']['tSinopsis']))?$libro['sinopsis']['tSinopsis']:null;
				$libro['cSoporte'] = (isset($libro['soporte']['cDescripcion']))?trim(str_replace(array('4%', '16%'), '', $libro['soporte']['cDescripcion'])):null;
				$libro['cIdioma'] = (isset($libro['idioma']['cDescripcion']))?$libro['idioma']['cDescripcion']:null;
				$libro['bStatus'] = isset($libro['bMostrarWebManual'])?$libro['bMostrarWebManual']:$libro['bMostrarWeb'];
				$libro['cFormato'] = (isset($libro['encuadernacion']['cDescripcion']))?$libro['encuadernacion']['cDescripcion']:null;
				$libro['cEditorial'] = (isset($libro['editorial']['cNombre']))?$libro['editorial']['cNombre']:null;
				$libro['nYear'] = (isset($libro['dEdicion']))?date('Y', $libro['dEdicion']):null;
				$libro['cPlazoEnvio'] = (isset($libro['plazoenvio']['cDescripcion']))?$libro['plazoenvio']['cDescripcion']:null;
				$libro['cColeccion'] = (isset($libro['coleccion']['cDescripcion']))?$libro['coleccion']['cDescripcion']:null;
				/*
				 if (isset($libro['autores']))
				 {
					foreach ($libro['autores'] as $a)
					{
					$libro['nIdAutor'][] = $a['id'];
					}
					}*/

				/*
				 if (isset($libro['materias']))
				 {
					foreach ($libro['materias'] as $a)
					{
					$libro['nIdMateria'][] = $a['id'];
					}
					}
					*/

				$libros[] = $libro;
				if ($count >= MAX_ITEMS)
				{
					$this->_log("Enviando bloque de datos {$total}/" .$q->num_rows());
					$libros = $this->_process_data($libros, $alias, $defaults);

					// envia los datos
					//$xml = $this->solrapi->create_document($libros);
					if ($file)
					{
						file_put_contents("{$dir}cat.{$fcount}.xml", serialize($xml));
						$fcount++;
					}

					#$this->solrapi->update($xml);
					#$this->solrapi->commit();

					unset($libros);
					#unset($xml);
					//$libros = array();
					gc_enable();
					$count = 0;
				}
			}
			$this->_log("Se han creado {$total} documentos");
		}
		$this->benchmark->mark('articulos_end');

		$this->_log($this->benchmark->elapsed_time('articulos_start', 'articulos_end'));
		$final = time();
		$this->_log("Proceso terminado");

		//$this->out->message(TRUE, "Se han creado {$count} artículos");
	}

	/**
	 * Prepara los datos para ser enviados al servidor
	 * @param array $data Datos a procesar
	 * @param array $alias Alias de los campos
	 * @param array $defaults Valores por defecto
	 * @return array Datos actualizados
	 */
	private function _process_data($data, $alias, $defaults)
	{
		$mat = array();
		foreach ($data as $m)
		{
			$m2 = array();
			foreach ($alias as $k => $v)
			{
				if (isset($m[$v]))
				{
					$m2[$k] = $m[$v];
				}
			}
			foreach ($defaults as $k => $v)
			{
				if (!isset($m2[$k]))
				{
					$m2[$k] = $v;
				}
			}

			$mat[] = $m2;
		}

		return $mat;
	}

	function echo_memory_usage() {
		$mem_usage = memory_get_usage(true);

		if ($mem_usage < 1024)
		return $mem_usage." bytes";
		elseif ($mem_usage < 1048576)
		return round($mem_usage/1024,2)." K";
		else
		return round($mem_usage/1048576,2)." M";
	}

	/**
	 * Muestra un mensaje de LOG en la pantalla
	 * @param string $text Mensaje a mostrar
	 */
	private function _log($text)
	{
		print '[' . date("Y-m-d G:i:s") . '] - ' . $this->echo_memory_usage() . ' : ' . $text . "\n";
	}
}

/* End of file solr.php */
/* Location: ./system/application/controllers/sys/solr.php */