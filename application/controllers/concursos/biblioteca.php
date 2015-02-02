<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Bibliotecas
 *
 */
class Biblioteca extends MY_Controller
{

	/**
	 * Palabras que si aparecen en el ISBN mandan como único
	 * @var array
	 */
	private $mandan;
	/**
	 * Palabras que si aparecen en el ISBN se deben crear todos los ISBNS
	 * @var array
	 */
	private $todos;

	/**
	 * Constructor
	 *
	 * @return Biblioteca
	 */
	function __construct()
	{
		parent::__construct('concursos.biblioteca', 'concursos/M_biblioteca', TRUE, null, 'Bibliotecas');
	}

	/**
	 * Crea las líneas de pedido de una biblioteca de un concurso
	 * @param  int $biblioteca Id de la biblioteca del concurso
	 * @param  string $file ficheros EXCEL separados por ; si hay más de uno
	 * @return MSG/DIALOG
	 */
	function importar_excel($biblioteca = null, $sala = null, $file = null)
	{
		$this->userauth->roleCheck($this->auth.'.excel');

		$biblioteca = isset($biblioteca)?$biblioteca:$this->input->get_post('biblioteca');
		$sala = isset($sala)?$sala:$this->input->get_post('sala');
		$file = isset($file) ? $file : $this->input->get_post('file');

		if (!empty($file))
		{
			set_time_limit(0);
			$files = preg_split('/;/', $file);
			$files = array_unique($files);
			$count = 0;
			$this->load->library('Messages');
			$bl = $this->reg->load($biblioteca);
			$this->load->model('concursos/m_sala');

			$sl = ($sala>0)?$this->m_sala->load($sala):null;

			$this->messages->info(sprintf($this->lang->line('concursos_excel_importar_biblioteca'), $bl['cDescripcion'], 
				(isset($sl)?$sl['cDescripcion']:$this->lang->line('SIN SALA'))));

			foreach ($files as $k => $file)
			{
				if (!empty($file))
				{
					$this->_importar_excel($file, $bl['nIdBiblioteca'], isset($sl)?$sl['nIdSala']:null);
				}
			}
		}
		else
		{
			$this->_show_js('excel', 'concursos/excel2.js');
		}
	}

	/**
	 * Obtiene todos los ISBNS del registro y decide si se deben tener en cuenta todos o solo uno
	 * @param  string $original Texto ISBN
	 * @return array (type, isbns), FALSE
	 */
	private function get_isbns($original)
	{
		if (!isset($this->mandan))
			$this->mandan = array_filter(explode("\n", file_get_contents(__DIR__ . DS . 'mandan.txt')));

		if (!isset($this->todos))
			$this->todos = array_filter(explode("\n", file_get_contents(__DIR__ . DS . 'todos.txt')));
		#var_dump($this->mandan, $this->todos); die();

		if (!empty($original))
		{
			$isbns = explode(';', $original);
			$res = null;
			if (count($isbns) == 1)
			{
				$res = array('type' => 'unique',  'isbns' => $isbns);
			}
			elseif (count($isbns) > 1)
			{
				#var_dump($isbns);
				foreach ($isbns as $isbn) 
				{
					preg_match_all('/\(([^)]*)\)/', $isbn, $matches);
					# Tiene textos?
					if (isset($matches[1][0]))
					{
						foreach ($matches[1] as $v) 
						{
							$value = trim($v);
							if (in_array($v, $this->mandan))
							{
								$isbn = preg_replace('/(\([^)]*\))/', '', $isbn);
								$res = array('type' => 'unique',  'isbns' => array(trim($isbn)));
								#$this->flush($original ."\n");
								#$this->flush(print_r($res, TRUE)); 
								#die();
								break;
							}
							if (in_array($v, $this->todos))
							{
								$res = array('type' => 'all',  'isbns' => $isbns);
								#var_dump($res); die();
								break;
							}

							$res = array('type' => 'onlyone',  'isbns' => $isbns);
						}
					}
					else
					{
						# No tiene palabras
						$res = array('type' => 'onlyone',  'isbns' => $isbns);
					}
				}
			}
			if ($res['type'] != 'all')
			{
				foreach ($res['isbns'] as $key => $value) 
				{
					$res['isbns'][$key] = trim(preg_replace('/(\([^)]*\))/', '', $value));
				}
				#if ($count == 10) die();
			}
			else
			{
				#Convierte los ISBNs a array(texto, isbn)
				$data = array();
				foreach ($res['isbns'] as $key => $isbn) 
				{
					preg_match_all('/\(([^)]*)\)/', $isbn, $matches);
					# Tiene textos?
					if (isset($matches[1][0]))
						$data[$matches[1][0]] = trim(preg_replace('/(\([^)]*\))/', '', $isbn));
				}
				$res['isbns'] = $data;
			}
			return $res;
		}
		return FALSE;
	}

	/**
	 * Crea el libro y actualiza las estadísticas
	 * @param  mixed $original    string: 1 ISBN, array: lista de ISBNs posibles
	 * @param  int $idb         Id de la biblioteca
	 * @param  int $ids         Id de la sala
	 * @param  int $ok_isbn     Contador de ISBNs correctos
	 * @param  int $found       Contador de artículos encontrados
	 * @param  int $nook_isbn   Contados de ISBNs incorrectos
	 * @param  array $value       Registro a añadir
	 * @param  array $campos      Campos del fichero EXCEL
	 * @param  array $editoriales Array de editoriales encontradas
	 * @param  array $creados     Array de artículos creados
	 * @param  array $cab         Cabeceras del fichero EXCEL para crear Churro
	 * @return bool, TRUE: OK, FALSE: error
	 */
	private function _crear_libro($original, $idb, $ids, &$ok_isbn, &$found, &$nook_isbn, &$value, &$campos, &$editoriales, &$creados, &$cab)
	{		
		$where = null;
		if (is_array($original))
		{
			$vale = array();
			foreach($original as $isbn)
			{
				$ean = $this->isbnean->to_ean($isbn);
				$isbn = $this->isbnean->to_isbn($isbn);
				if ($this->isbnean->is_ean($ean))
					$vale[] = $ean;
			}
			if (count($vale) > 0 )
			{
				$vale = implode(',', $vale);
				$where = "Cat_Codigos_Fondo.nCodigo IN ({$vale})";
			}
		}
		else
		{
			#var_dump($original);
			$ean = $this->isbnean->to_ean($original);
			$isbn = $this->isbnean->to_isbn($original);
			if ($this->isbnean->is_ean($ean))
				$where = "Cat_Codigos_Fondo.nCodigo={$ean}";
		}
		#var_dump($ean, $isbn); die();
		if (!empty($where))
		{
			#$ean = $this->isbnean->to_ean($original);
			++$ok_isbn;
			$arts = $this->m_articulo->get(null, null, null, null, $where);
			if (count($arts) > 0) 
			{
				$id = $arts[0]['nIdLibro'];					
				++$found;
				$link = format_enlace_cmd($arts[0]['cTitulo'], site_url('catalogo/articulo/index/' . $arts[0]['nIdLibro']));
				$status = sprintf($this->lang->line('concursos_excel_importar_found'), $link, trim($value['C']));
			}
			else
			{
				$status = sprintf($this->lang->line('concursos_excel_importar_nofound'), $ean);
			}
		}
		else
		{
			$status = sprintf($this->lang->line('concursos_excel_importar_nook_isbn'), $original);
			#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();
			++$nook_isbn;
		}

		$this->messages->info(sprintf($this->lang->line('concursos_excel_importar_linea'), 
			$value[$campos['QT']], $value[$campos['AUT']], $value[$campos['TIT']], $value[$campos['EDIT']],
			$value[$campos['EDIC']], $value[$campos['COL']], $value[$campos['ISBN']],
			$value[$campos['PRECIO']], $value[$campos['CDU']], $value[$campos['CDU2']]), 2);
		#Crea el artículo
		if (!isset($id))
		{
			$id_editorial = null;
			$id_proveedor = null;
			if (isset($isbn))
			{
				$parts = $this->isbnean->isbnparts($isbn);
				if (isset($parts['publisher_id']))
				{
					if (isset($editoriales[$parts['publisher_id']]))
					{
						$id_editorial = $editoriales[$parts['publisher_id']]['id'];
						$id_proveedor = $editoriales[$parts['publisher_id']]['nIdProveedor'];
					}
					else
					{
						$editorial = $this->obj->m_editorial->search($parts['publisher_id'], 0, 1);
						if (isset($editorial[0]))
						{
							// Editorial ya existente
							$editorial = $this->obj->m_editorial->load($editorial[0]['id']);
							$id_editorial = $editorial['id'];
							$id_proveedor = $editorial['nIdProveedor'];
							$editoriales[$parts['publisher_id']] = array(
								'id'			=> $id_editorial,
								'nIdProveedor' 	=> $id_proveedor
								);
						}
						else
						{
							$editoriales[$parts['publisher_id']] = array(
								'id'			=> null,
								'nIdProveedor' 	=> null
								);
						}
					}
				}
			}

			$reg = array(
				'nEAN' 		=> $ean,
				'cISBN'		=> $isbn,
				'cAutores'	=> trim($value[$campos['AUT']]),
				'cTitulo'	=> trim($value[$campos['TIT']]),
				'dEdicion'	=> trim($value[$campos['EDIC']]),
				'fPrecio'	=> format_quitar_iva($value[$campos['PRECIO']], $this->config->item('bp.import.iva')),
				'nIdTipo'	=> $this->config->item('bp.import.tipo'),
				'bMostrarWebManual' => 0,
				'nIdEditorial' => $id_editorial
			);
			$id = $this->m_articulo->insert($reg);
			if ($id < 0)
			{
				$this->messages->error($this->lang->line('concursos_excel_importar_error_creando_libro'), 2);
				$this->messages->error($this->m_articulo->error_message(), 2);
				return FALSE;
			}
			$link = format_enlace_cmd($reg['cTitulo'], site_url('catalogo/articulo/index/' . $id));
			$this->messages->info(sprintf($this->lang->line('concursos_excel_importar_libro_creado'), $link), 2);
			$reg['id'] = $id;
			$reg['link'] = $link;
			$creados[] = $reg;
			#var_dump($id, $reg, $link); die();
		}
		$this->messages->info($status, 3);

		# Crea la línea de pedido
		$cantidad =is_numeric($value[$campos['QT']])?$value[$campos['QT']]:1;

		$xurro = array();

		foreach ($cab as $key => $val) 
		{
			$xurro[$val] = $value[$key];
		}

		$xurro = serialize($xurro);

		$reg = array (
			'nIdBiblioteca' => $idb,
			'nIdSala'		=> $ids,
			'cEAN' 			=> $ean,
			'nIdLibro'		=> $id,
			'cAutores'		=> trim($value[$campos['AUT']]),
			'cTitulo'		=> trim($value[$campos['TIT']]),
			'cEditorial1a'	=> trim($value[$campos['EDIT']]),
			'cEdicion'		=> trim($value[$campos['EDIC']]),
			'tTitolVolum'	=> trim($value[$campos['COL']]),
			'cISBN'			=> trim($value[$campos['ISBN']]),
			'fPrecio'		=> $value[$campos['PRECIO']],
			'cCDU'			=> $value[$campos['CDU']],
			'cCDU2'			=> $value[$campos['CDU2']],
			'cElxurro'		=> $xurro,
			'nIdEstado'		=> CONCURSOS_ESTADO_LINEA_EN_PROCESO
			);

		for($j=0; $j<$cantidad; $j++)
		{
			if (!$this->m_pedidoconcursolinea->insert($reg))
			{
				$this->messages->error($this->m_pedidoconcursolinea->error_message());
				return FALSE;
			}
		}
		return TRUE;
	}
	
	/**
	 * Importa un EXCEL con las líneas de pedido de una biblioteca
	 * @param  string $file Fichero EXCEL a importar
	 * @param  int $idb  Id de la Biblioteca
	 * @param  int $ids  Id de la sala (opcional)
	 * @return MSG
	 */
	private function _importar_excel($file, $idb, $ids)
	{
		#var_dump($file); die();
		$this->messages->info(sprintf($this->lang->line('concursos_excel_importar'), $file));

		require_once DIR_CONTRIB_PATH . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel.php';

		$this->load->library('UploadLib');
		$this->load->library('ISBNEAN');
		$this->load->model('catalogo/m_articulo');
		$this->load->model('concursos/m_pedidoconcursolinea');
		$this->load->model('concursos/m_estadolineaconcurso');
		$this->load->model('catalogo/m_articulo');
		$this->load->model('catalogo/m_editorial');

		$filename = ($this->uploadlib->get_pathfile($file));
		$objPHPExcel = new PHPExcel();
		$objReader = PHPExcel_IOFactory::createReaderForFile($filename);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($filename);

		$editoriales = array();
		$creados = array();
		$ok_isbn = 0;
		$nook_isbn = 0;
		$found = 0;

		#$this->db->trans_begin();
		$campos = array(
			'QT'		=> 'A',
			'AUT'		=> 'B',
			'TIT'		=> 'C',
			'EDIT'		=> 'D',
			'EDIC'		=> 'E',
			'COL'		=> 'F',
			'ISBN'		=> 'G',
			'PRECIO'	=> 'H',
			'CDU'		=> 'I',
			'CDU2'		=> 'J'
			);

		foreach ($objPHPExcel->getAllSheets() as $sheet) 
		{
			$name = $sheet->getTitle();
			$this->messages->info(sprintf($this->lang->line('concursos_excel_importar_hoja'), $name, $file));
			$sheetData = $sheet->toArray(null, FALSE, TRUE, TRUE);		
			# Lee la cabecera
			$cab = $sheetData[1];
			foreach ($cab as $key => $value) 
			{
				$campos[$value] = $key;
			}
			#Fuera cabecera
			for($i=2; $i <= count($sheetData); $i++) 
			{
				$value = $sheetData[$i];
				if (trim($value[$campos['TIT']])!='')
				{
					$id = null;
					$status = null;
					$original = $value[$campos['ISBN']];
					$isbns = $this->get_isbns($original);
					$res = TRUE;
					if ($isbns === FALSE)
					{
						$res = $this->_crear_libro(null, $idb, $ids, $ok_isbn, $found, $nook_isbn, $value, $campos, $editoriales, $creados, $cab);
					}
					elseif ($isbns['type'] == 'unique')
					{
						$res = $this->_crear_libro($isbns['isbns'][0], $idb, $ids, $ok_isbn, $found, $nook_isbn, $value, $campos, $editoriales, $creados, $cab);
					}
					elseif ($isbns['type'] == 'onlyone')
					{
						$res = $this->_crear_libro($isbns['isbns'], $idb, $ids, $ok_isbn, $found, $nook_isbn, $value, $campos, $editoriales, $creados, $cab);
					}
					elseif ($isbns['type'] == 'all')
					{
						$titulo = $value[$campos['TIT']];
						foreach ($isbns['isbns'] as $key => $isbn) 
						{
							$value[$campos['TIT']] = $titulo . ' (' . $key . ')';
							$res = $this->_crear_libro($isbn, $idb, $ids, $ok_isbn, $found, $nook_isbn, $value, $campos, $editoriales, $creados, $cab);
							if (!$res)
								break;
						}
						$value[$campos['TIT']] = $titulo;
					}

					if (!$res)
					{
						#$this->db->trans_rollback();
						$body = $this->messages->out($this->lang->line('Importar Concurso EXCEL'));
						$this->out->html_file($body, $this->lang->line('Importar Concurso EXCEL'), 'iconoConcursosImportarEXCELTab');						
					}
				}
			}
		}

		$this->messages->info(sprintf($this->lang->line('concursos_excel_importar_hoja_ok'), $ok_isbn));
		$this->messages->info(sprintf($this->lang->line('concursos_excel_importar_hoja_nook_isbn'), $nook_isbn));
		$this->messages->info(sprintf($this->lang->line('concursos_excel_importar_hoja_found'), $found));

		$this->messages->info(sprintf($this->lang->line('concursos_excel_importar_libroscreados'), count($creados)), 1);
		foreach ($creados as $value) 
		{
			$this->messages->info(sprintf($this->lang->line('concursos_excel_importar_libro_creado_list'), $value['id'], isset($value['cEAN'])?$value['cEAN']:'', $value['link']), 2);
		}

		#$this->db->trans_rollback();
		#$this->db->trans_commit();
		$body = $this->messages->out($this->lang->line('Importar Concurso EXCEL'));
		$this->out->html_file($body, $this->lang->line('Importar Concurso EXCEL'), 'iconoConcursosImportarEXCELTab');
	}

	function exportar_excel($biblioteca = null)
	{
		$this->userauth->roleCheck($this->auth.'.excel');

		$biblioteca = isset($biblioteca)?$biblioteca:$this->input->get_post('biblioteca');

		if (is_numeric($biblioteca))
		{
			set_time_limit(0);

			$this->load->library('ExcelData');
			$this->load->library('HtmlFile');
			$this->load->library('zip');
			$this->load->model('concursos/m_sala');
			$this->load->model('concursos/m_pedidoconcursolinea');

			# Crea un fichero para cada Sala
			$salas = $this->m_sala->get();
			$error = error_reporting();
			error_reporting(E_ERROR);
			foreach ($salas as $sala)
			{
				$data = $this->m_pedidoconcursolinea->get(null, null, 'Cat_Fondo.cTitulo', null, "nIdBiblioteca={$biblioteca} AND nIdSala={$sala['nIdSala']}");
				$regs = array();
				foreach ($data as $reg)
				{
					$xurro = unserialize($reg['cElxurro']);
					if (!is_array($xurro)) $xurro = array();
					$regs[] = array_merge(
						array(
							$this->lang->line('cISBN')		=> $reg['nEAN'],
							$this->lang->line('cTitulo') 	=> $reg['cTitulo2'],
							$this->lang->line('cAutores')	=> $reg['cAutores2'],
							$this->lang->line('Editorial') 	=> $reg['cEditorial'],
							$this->lang->line('fPVP')		=> $reg['fPrecio']
							), 
						$xurro);
					#break;
				}

				$wb = $this->exceldata->create();
				$data = array_chunk($regs, $this->config->item('bp.max.excel.rows'));
				$ct = 1;
				foreach ($data as $d)
				{
					$name = $sala['cDescripcion'];
					if ($ct > 1) $name .= ' (' . $ct . ')';
					$this->exceldata->add($wb, $d, $name . $ct);
					++$ct;
				}

				$this->exceldata->close($wb);
				$file = $this->htmlfile->pathfile($this->exceldata->get_filename($wb));
				$filename = str_replace('*', '+', $sala['cDescripcion']);
				$xls = $this->htmlfile->pathfile($filename . '.xls');
				copy($file, $xls);
				unlink($file);
				$this->zip->read_file($xls);
				#die();
				#break;
			}
			error_reporting($error);

			$b = $this->reg->load($biblioteca);
			$zipname = $b['cDescripcion'] . '-' . time() . '.zip';
			$zip = DIR_TEMP_PATH . $zipname;
			$this->zip->archive($zip);
			$url = $this->htmlfile->url($zipname);
			$message = sprintf($this->lang->line('msg-stock-retrocedido-fichero-ok'), "<a href='" . $url . "'>{$zipname}</a>");
			$this->load->library('Mensajes');
			$this->mensajes->usuario($this->userauth->get_username(), $message);
			$this->out->dialog(TRUE, $message);
		}
		else
		{
			$this->_show_js('excel', 'concursos/biblioteca.js', array('url' => site_url('concursos/biblioteca/exportar_excel')));
		}
	}
}

/* End of file Biblioteca.php */
/* Location: ./system/application/controllers/concursos/Biblioteca.php */