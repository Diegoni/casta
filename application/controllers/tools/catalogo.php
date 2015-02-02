<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	tools
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Procesos relacionados con el catálogo
 * @author alexl
 *
 */
class Catalogo extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Pedidos
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Arregla de los precios de coste
	 */
	function fixcostearticulos($fix = 0, $count = 100)
	{
		set_time_limit(0);
		$this->load->library('Logger');
		$this->load->model('catalogo/m_articulo');
		$this->load->library('Messages');

		$data = $this->m_articulo->get_coste_error($count);
		$ok = 0;
		foreach($data as $reg)
		{
			if (isset($reg['fCoste']))
			{
				$id = format_enlace_cmd($reg['nIdLibro'], site_url('catalogo/articulo/index/' . $reg['nIdLibro']));
				if ($reg['fCoste'] < 0)
				{
					$this->messages->warning("{$id} - {$reg['cTitulo']} ({$reg['fPrecioCompra']}) -> {$reg['fCoste']} <strong>NEGATIVO</strong>");
				}
				else
				{
					$this->messages->info("{$id} - {$reg['cTitulo']} ({$reg['fPrecioCompra']}) -> {$reg['fCoste']}");
					if ($fix == 1)
					{
						$this->logger->Log("{$reg['nIdLibro']} - {$reg['cTitulo']} ({$reg['fPrecioCompra']}) -> {$reg['fCoste']}", 'catalogo');
						$this->m_articulo->update($reg['nIdLibro'], array('fPrecioCompra' => $reg['fCoste']));
						++$ok;
					}
				}
			}
		}
		$this->messages->info("Se han corregido {$ok} artículos");
		$body = $this->messages->out($this->lang->line('Corregir precios de coste'));
		$this->out->html_file($body, $this->lang->line('Corregir precios de coste'), 'iconoReportTab');
	}

	/**
	 * Genera un listado de títulos con portadas
	 * @return HTML
	 */
	function listado()
	{
		$this->load->model('catalogo/m_articulo');
		$this->db->select('f.nIdLibro')
		->select('lp.cTitulo, f.cAutores')
		->select('p.cPedido')
		->from('Consorci2011..Diba_LineasPedido lp')
		->join('Cat_Fondo f', $this->db->varchar('lp.cEAN') . ' = f.nEAN')
		->join('Consorci2011..Diba_Pedidos p', 'p.nIdPedido = lp.nIdPedido')
		->where('lp.nIdEstado = 1')
		->where('f.nIdLibro IN (
		SELECT nIdLibro
		FROM Cat_Secciones_Libros (NOLOCK)
		WHERE nStockFirme + nStockDeposito > 0
			AND nIdSeccion NOT IN (868)
		GROUP BY nIdLibro)')
		->order_by('lp.cTitulo');

		$html = '<table>';
		$query = $this->db->get();
		$ar = $query->result_array();
		$ar = string_encode($ar);
		$par = FALSE;
		$html .= '<tr>';
		foreach($ar as $item)
		{
			if ($par) $html .= '</tr><tr>';								
			$par = !$par;
			$l = $this->m_articulo->load($item['nIdLibro'], array('secciones', 'ubicaciones'));
			$html .= '<td>' . format_cover($item['nIdLibro'], 50) . '</td><td valign="top"><strong>[' . $item['nIdLibro'] .'] ' . $item['cTitulo'] . '</strong><br />'; 
			$html .= $item['cAutores'] . '<br/>';
			$html .= $item['cPedido'] . '<br/>';
			$lineas = array(); 
			foreach ($l['secciones'] as $sec)
			{
				$stock = $sec['nStockFirme'] + $sec['nStockDeposito'];
				$lineas[] = "{$sec['cNombre']} - {$stock}"; 				
			}
			$html .= implode(', ', $lineas). '<br/>';
			$lineas = array(); 
			foreach ($l['ubicaciones'] as $sec)
			{
				$lineas[] = $sec['cDescripcion']; 				
			}
			$html .= implode(', ', $lineas). '<br/>';
			$html .= '</td>'; 				
		}
		$html .= '</tr></table>';
		$this->out->html_file($html, 'Títulos', 'iconReportTab');
	}

	/**
	 * Crea la relación materias-artículos basándose en DILVE
	 * @param  int $id ID de la sección
	 * @return bool
	 */
	function materias_cdu($id = null)
	{
		error_reporting(E_ALL);
		$muestra = 100;
		$max_mat = null;
		$this->load->model('catalogo/m_materia');
		$this->load->model('catalogo/m_articulo');
		$this->load->model('catalogo/m_articulomateria');
		$this->load->model('generico/m_seccion');
		$this->load->library('Dilve');
		$this->load->library('Color2');
		$this->load->library('ISBNEAN');

		if (file_exists('RELACIONES.DAT'))
		{
			$timer_global = microtime(true);
			$this->color2->info('Leyendo materias');
			$materias = $this->m_materia->get(null, $max_mat);
			$this->color2->line('%_' . count($materias) . ' materias leídas');
			$nombres = array();
			foreach ($materias as $reg)
			{
				$nombres[$reg['nIdMateria']] = $reg['cNombre'];
			}

			$relaciones = unserialize(file_get_contents('RELACIONES.DAT'));
			#print_r($relaciones);die();
			$materias = array();
			foreach($relaciones as $k => $rel)
			{
				arsort($rel);
				$last = reset($rel);
				$key = key($rel);
				foreach($rel as $key => $r)
				{
					if ($r > 5)
					{
						#$this->color2->line("%g{$k}%n [$r] ->" . $nombres[$key]);
						if (!isset($materias[$k]['act']) || $materias[$k]['act']['count'] < $r)
						{
							$materias[$k]['act'] = array('id' => $key, 'count' => $r);
						}
					}
				}
			}
			#print_r($materias); die();
			$sec = $this->m_seccion->load($id);
			$this->color2->line('%rBuscando artículos sin materia de la sección %_' . $sec['cNombre']);
			$articulos = $this->m_articulo->sinmateria($id, null, null, 'dCreacion', 'DESC', TRUE, TRUE);
			$this->color2->line('%_' . count($articulos['data']) . ' artículos leídos');
			$articulos = array_chunk($articulos['data'], $muestra);
			$count = 0;
			$total = count($articulos);
			$creados = 0;
			foreach ($articulos as $bloque)
			{
				++$count;
				$this->color2->line("Leyendo bloque %_({$count}/{$total})");
				$codes = array();
				$ids = array();

				#var_dump($bloque); die();
				foreach ($bloque as $art)
				{
					$ean = $this->isbnean->to_ean($art['cISBN']);
					$codes[$ean] = $art['nIdLibro'];
				}
				$this->color2->line('Buscando en DILVE...');
				#var_dump(array_keys($codes)); die();
				$res = $this->dilve->get(array_keys($codes));
				#var_dump(count($res)); die();
				if (count($res) > 0)
				{
					foreach($res as $p)
					{
		 				$isbn = $this->dilve->get_isbn($p);
						$ean = $this->isbnean->to_ean($isbn);
						if (isset($p['MediaFile']['MediaFileTypeCode'])&&($p['MediaFile']['MediaFileTypeCode']=='04'))
						{
							if ($p['MediaFile']['MediaFileLinkTypeCode'] == '06')
							{
								$file = DIR_TEMP_PATH . $p['MediaFile']['MediaFileLink'];
								file_put_contents($file, $this->dilve->media($isbn, $p['MediaFile']['MediaFileLink']));
								$p['MediaFile']['MediaFileLink'] = $file;
							}
							$f = $this->websave->set_cover($ean, $p['MediaFile']['MediaFileLink']);
							if ($p['MediaFile']['MediaFileLinkTypeCode'] == '06')
							{
								#var_dump($file, $f); die();
								unlink($p['MediaFile']['MediaFileLink']);
							}
			 				$this->color2->line("Artículo {$isbn} -> COVER -> %_{$f}");
						}
						$text = null;
						if (isset($p['Contributor']['BiographicalNote']) && isset($p['Contributor']['BiographicalNote']))
						{
							$text[] = $p['Contributor']['PersonNameInverted'];
							$text[] = $p['Contributor']['BiographicalNote'];
						}
						if (isset($p['OtherText']['Text']))
						{
							$text[] = $p['OtherText']['Text'];
							#var_dump($p['OtherText']['Text']); die();
						}
						if (count($text) > 0)
						{
							$f = $this->websave->set_description($ean, implode("\n", $text));
			 				$this->color2->line("Artículo {$isbn} -> SINOPSIS -> %_{$f}");
						}
						if (isset($p['Subject']))
						{
							$mats = array();
							foreach ($p['Subject'] as $s)
							{
							 	if (isset($s['SubjectHeadingText']))
							 	{
							 		if (isset($materias[$s['SubjectHeadingText']]))
							 		{
							 			foreach ($materias[$s['SubjectHeadingText']] as $rel)
							 			{
							 				$this->color2->line("Artículo {$isbn} -> {$s['SubjectHeadingText']} -> " . $nombres[$rel['id']]);
							 				$mats[$rel['id']] = $rel['id'];
							 			}
							 		}
							 		else
							 		{
							 			$this->color2->error("No hay relación con la materia %_{$s['SubjectHeadingText']}%n");
							 		}
							 	}
							 	if (isset($s['SubjectCode']))
							 	{
							 		if (isset($materias[$s['SubjectCode']]))
							 		{
							 			foreach ($materias[$s['SubjectCode']] as $rel)
							 			{
							 				$this->color2->line("Artículo {$isbn} -> {$s['SubjectCode']} -> " . $nombres[$rel['id']]);
							 				$mats[$rel['id']] = $rel['id'];
							 			}
							 		}
							 		else
							 		{
							 			$this->color2->error("No hay relación con la materia %_{$s['SubjectCode']}%n");
							 		}
							 	}
							}
							#var_dump($mats); die();
							foreach ($mats as $mat)
							{
								$ins = array( 
									'nIdLibro'		=> $codes[$isbn],
									'nIdMateria' 	=> $mat,
									'bAutomatico'	=> TRUE
									);

								$this->m_articulomateria->insert($ins);
								++$creados;
								$this->color2->line("[{$creados}] Artículo {$codes[$isbn]} - {$isbn} -> {$mat} -> " . $nombres[$mat]);

								#die();
							}
						}
					}
				}
				else
				{
					$this->color2->error('No hay artículos en DILVE');
				}
			}
			$this->color2->line("Se han creado %_{$creados}%n relaciones de materia-articulo ". sprintf("Total: %%_%fs%%n", microtime(true)-$timer_global));

			exit;
		}
		else
		{
			#file_put_contents('RELACIONES.DAT', serialize($relaciones));
			#phpinfo(); die();

			#$this->load->plugin('MateriasCDU');

			$this->color2->head();
			$this->color2->title('Materias .vs. CDU');
			$this->color2->head();

			$this->color2->info('Leyendo materias');
			$materias = $this->m_materia->get(null, $max_mat);
			$this->color2->line('%_' . count($materias) . ' materias leídas');
			$timer_global = microtime(true);
			$relaciones = array();
			$codes = array();
			$total = count($materias);
			$count = 0;
			foreach ($materias as $mat)
			{
				++$count;
				$ini = 0;
				do {
					$this->color2->line("[%g{$mat['cNombre']}%n] - Leyendo %_{$ini}..{$muestra}%n artículos CON materia %B({$count}/{$total})");
					$arts = $this->m_articulo->conmateria($mat['nIdMateria'], $ini, $muestra, 'Cat_Fondo.dCreacion', 'DESC');
					if (count($arts) == 0) break;
					$codes = array();
					foreach($arts as $art)
					{
						$codes[] = $this->isbnean->to_ean($art['cISBN']);
						#$codes[] = array('id' => $mat['nIdMateria'], 'code' => $this->isbnean->to_ean($art['cISBN']));
					}
					$res = $this->dilve->get($codes);
					$ini += $muestra;
					if (count($res) > 0 ) break;
				} while (TRUE);
				if (count($res) > 0)
				{
					foreach($res as $p)
					{
						if (isset($p['Subject']))
						{
							foreach ($p['Subject'] as $s)
							{
							 	if (isset($s['SubjectHeadingText']))
							 	{
							 		if (!isset($relaciones[$s['SubjectHeadingText']][$mat['nIdMateria']]))
							 		{
							 			$relaciones[$s['SubjectHeadingText']][$mat['nIdMateria']] = 0;
							 		}
							 		++$relaciones[$s['SubjectHeadingText']][$mat['nIdMateria']];
							 	}
							 	if (isset($s['SubjectCode']))
							 	{
							 		if (!isset($relaciones[$s['SubjectCode']][$mat['nIdMateria']]))
							 		{
							 			$relaciones[$s['SubjectCode']][$mat['nIdMateria']] = 0;
							 		}
							 		++$relaciones[$s['SubjectCode']][$mat['nIdMateria']];
							 	}
							}
						}
					}
				}
				else
				{
					$this->color2->error('No hay artículos en DILVE');
				}
				#var_dump($relaciones);
				#die();
			}
			file_put_contents('RELACIONES.DAT', serialize($relaciones));
			$this->color2->line('Se han encotrado %_'.count($relaciones). ' relaciones de materia-cdu '. sprintf("Total: %%_%fs%%n", microtime(true)-$timer_global));
		}
	}

	/**
	 * Crea la relación materias-artículos basándose en DILVE
	 * @param  int $id ID de la sección
	 * @return bool
	 */
	function materias_amazon($id = null)
	{
		error_reporting(E_ALL);
		$muestra = 50;
		$max_mat = null;
		$this->load->model('catalogo/m_materia');
		$this->load->model('catalogo/m_articulo');
		$this->load->model('catalogo/m_articulomateria');
		$this->load->model('generico/m_seccion');
		$this->load->library('Color2');
		$this->load->library('ISBNEAN');
		$this->load->library('Importador');
		$this->load->library('SearchInternet');
		$this->load->library('WebSave');

		if (file_exists('RELACIONESAMAZON.DAT'))
		{
			$timer_global = microtime(true);
			$this->color2->head();
			$this->color2->title('Materias .vs. AMAZON -> Asignando');
			$this->color2->head();
			$this->color2->info('Leyendo materias');
			$materias = $this->m_materia->get(null, $max_mat);
			$this->color2->line('%_' . count($materias) . ' materias leídas');
			$nombres = array();
			foreach ($materias as $reg)
			{
				$nombres[$reg['nIdMateria']] = $reg['cNombre'];
			}

			$relaciones = unserialize(file_get_contents('RELACIONESAMAZON.DAT'));
			#print_r($relaciones);die();
			$materias = array();
			foreach($relaciones as $k => $rel)
			{
				arsort($rel);
				$last = reset($rel);
				$key = key($rel);
				foreach($rel as $key => $r)
				{
					if ($r > 5)
					{
						#$this->color2->line("%g{$k}%n [$r] ->" . $nombres[$key]);
						if (!isset($materias[$k]['act']) || $materias[$k]['act']['count'] < $r)
						{
							$materias[$k]['act'] = array('id' => $key, 'count' => $r);
						}
					}
				}
			}
			#print_r($materias); die();
			$sec = $this->m_seccion->load($id);
			$this->color2->line('%rBuscando artículos sin materia de la sección %_' . $sec['cNombre']);
			$articulos = $this->m_articulo->sinmateria($id, null, null, 'dCreacion', 'DESC', TRUE, FALSE);
			$this->color2->line('%_' . count($articulos['data']) . ' artículos leídos');
			$articulos = array_chunk($articulos['data'], $muestra);
			$count = 0;
			$total = count($articulos);
			$creados = 0;
			foreach ($articulos as $bloque)
			{
				++$count;
				$this->color2->line("Leyendo bloque %_({$count}/{$total})");
				$codes = array();
				$ids = array();

				#var_dump($bloque); die();
				foreach ($bloque as $art)
				{
					$ean = $this->isbnean->to_ean($art['cISBN']);
					$codes[$ean] = $art['nIdLibro'];
				}
				$this->color2->line('Buscando en AMAZON...');
				#var_dump(array_keys($codes)); die();
				$res = $this->searchinternet->amazon(array_keys($codes));
				#var_dump(($res)); die();
				if (count($res) > 0)
				{
					foreach($res as $isbn => $p)
					{
						$isbn = $this->isbnean->to_ean($isbn);
						if (isset($p['cover']))
						{
							$f = $this->websave->set_cover($isbn, $p['cover']);							
			 				$this->color2->line("Artículo {$isbn} -> COVER -> %_{$f}");
						}
						if (isset($p['description']))
						{
							$f = $this->websave->set_description($isbn, $p['description']);
			 				$this->color2->line("Artículo {$isbn} -> SINOPSIS -> %_{$f}");
						}
						
						if (isset($p['category']))
						{
							$mats = array();
							foreach ($p['category'] as $s)
							{
						 		if (isset($materias[$s]))
						 		{
						 			foreach ($materias[$s] as $rel)
						 			{
						 				$this->color2->line("Artículo {$isbn} -> {$s} -> " . $nombres[$rel['id']]);
						 				$mats[$rel['id']] = $rel['id'];
						 			}
						 		}
						 		else
						 		{
						 			$this->color2->error("No hay relación con la materia %_{$s}%n");
						 		}
							}
							if (count($mats) > 0)
							{
								$mat = array_pop($mats);
							/*var_dump($mats); die();
							foreach ($mats as $mat)
							{*/
								$ins = array( 
									'nIdLibro'		=> $codes[$isbn],
									'nIdMateria' 	=> $mat,
									'bAutomatico'	=> TRUE
									);

								$this->m_articulomateria->insert($ins);
								++$creados;
								$this->color2->line("[{$creados}] Artículo {$codes[$isbn]} - {$isbn} -> {$mat} -> " . $nombres[$mat]);
								#die();
							}
						}
					}
				}
				else
				{
					$this->color2->error('No hay artículos en AMAZON');
				}
			}
			$this->color2->line("Se han creado %_{$creados}%n relaciones de materia-articulo AMAZON". sprintf("Total: %%_%fs%%n", microtime(true)-$timer_global));

			exit;
		}
		else
		{
			$this->color2->head();
			$this->color2->title('Materias .vs. AMAZON');
			$this->color2->head();

			$this->color2->info('Leyendo materias');
			$materias = $this->m_materia->get(null, $max_mat);
			$this->color2->line('%_' . count($materias) . ' materias leídas');
			$timer_global = microtime(true);
			$relaciones = array();
			$codes = array();
			$total = count($materias);
			$count = 0;
			foreach ($materias as $mat)
			{
				++$count;
				$ini = 0;
				do {
					$this->color2->line("[%g{$mat['cNombre']}%n] - Leyendo %_{$ini}..{$muestra}%n artículos CON materia %B({$count}/{$total})");
					$arts = $this->m_articulo->conmateria($mat['nIdMateria'], $ini, $muestra, 'Cat_Fondo.dCreacion', 'DESC', FALSE);
					if (count($arts) == 0) break;
					$codes = array();
					foreach($arts as $art)
					{
						$codes[] = $this->isbnean->to_ean($art['cISBN']);
						#$codes[] = array('id' => $mat['nIdMateria'], 'code' => $this->isbnean->to_ean($art['cISBN']));
					}
					$this->color2->line('Buscando en AMAZON...');
					$res = $this->searchinternet->amazon($codes);
					$this->color2->line('Se han encontrado %_' . count($res) . '%n registros');
					$ini += $muestra;
					if (count($res) > 0 ) break;
				} while (TRUE);
				if (count($res) > 0)
				{
					foreach($res as $p)
					{
						if (isset($p['category']))
						{
							foreach ($p['category'] as $s)
							{
						 		if (!isset($relaciones[$s][$mat['nIdMateria']]))
						 		{
						 			$relaciones[$s][$mat['nIdMateria']] = 0;
						 		}
						 		++$relaciones[$s][$mat['nIdMateria']];
							}
						}
					}
				}
				else
				{
					$this->color2->error('No hay artículos en AMAZON');
				}
				#var_dump($relaciones);
				#die();
			}
			file_put_contents('RELACIONESAMAZON.DAT', serialize($relaciones));
			$this->color2->line('Se han encotrado %_'.count($relaciones). ' relaciones de materia-AMAZON '. sprintf("Total: %%_%fs%%n", microtime(true)-$timer_global));
		}
		exit;
	}
}
/* End of file catalogo.php */
/* Location: ./system/application/controllers/tools/catalogo.php */

