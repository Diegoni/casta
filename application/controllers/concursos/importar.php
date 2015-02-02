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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Pedidos en superpedidos
 *
 */
class Importar extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Pedido
	 */
	function __construct()
	{
		parent::__construct('concursos.importar', null, TRUE);
	}

	function test($ids = null)
	{
		$this->userauth->roleCheck(($this->auth.'.bibliopola'));

		$ids	= isset($ids)?$ids:$this->input->get_post('ids');

		if ($ids)
		{
			$ids = preg_split('/[\;\s\n\r\;]/', $ids);

			set_time_limit(0);

			$this->load->model('ventas/m_pedidocliente');
			$this->load->model('catalogo/m_editorial');
			$this->load->model('catalogo/m_articulo');
			$this->load->model('proveedores/m_proveedor');
			$this->load->model('concursos/m_sala2');
			$this->load->model('concursos/m_biblioteca2');
			$this->load->model('concursos/m_pedido');
			$this->load->model('concursos/m_proveedorconcurso');
			$this->load->model('concursos/m_editorialconcurso');
			$this->load->model('concursos/m_articuloconcurso');
			$this->load->library('Messages');
			$pedidos = 0;

			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					// Existe el proveedor?
					$libro = $this->m_articulo->load($id);
					if (!isset($libro['nIdLibro']))
					{
						$this->messages->error("No se ha encontrado el título {$linea['nIdLibro']}" , 3);
						break;
					}
					$this->messages->info('<pre>' .print_r($libro, TRUE) . '</pre>', 3);

					$proveedor = $this->m_articulo->get_proveedor_habitual($libro);
					$this->messages->info('Id Proveedor BP: '.$proveedor, 3);
					if (isset($proveedor))
					{
						$esta = $this->m_proveedorconcurso->load($proveedor);
						if (!$esta)
						{
							$this->messages->info('Proveedor no está en SP: '.$proveedor, 3);
						}
						else
						{
							$this->messages->info('PROVEEDOR SP<pre>' .print_r($esta, TRUE) . '</pre>', 3);
						}
					}

					//Existe la editorial?
					$editorial = $libro['nIdEditorial'];
					$this->messages->info('Id Editorial BP: '.$editorial, 3);
					if (isset($editorial))
					{
						$esta = $this->m_editorialconcurso->load($editorial);
						if (!$esta)
						{
							$this->messages->info('Editorial  no está en SP: '.$proveedor, 3);
						}
						else
						{
							$this->messages->info('Editotial SP<pre>' .print_r($esta, TRUE) . '</pre>', 3);
						}
					}
				}
			}
			$this->messages->info(sprintf($this->lang->line('concursos_pedidos_creadas'), $pedidos));
			$body = $this->messages->out('Probar títulos');

			$this->out->html_file($body, $this->lang->line('Probar títulos'), 'iconoConcursosTestTab');
		}
		else
		{
			$data['url'] = site_url('concursos/importar/test');
			$data['title'] = $this->lang->line('Probar títulos');				
			$data['label'] = $this->lang->line('Ids');
			$data['icon'] = 'iconoConcursosTestTab';
			$this->_show_js('bibliopola', 'concursos/pedidos.js', $data);
		}
	}

	/**
	 * Traspasa los pedidos de Bibliopola al superpedidos
	 * @param string $ids Ids separados por espacios, puntos y comas
	 */
	function bibliopola($ids = null)
	{
		$this->userauth->roleCheck(($this->auth.'.bibliopola'));

		$ids	= isset($ids)?$ids:$this->input->get_post('ids');

		if ($ids)
		{
			$ids = preg_split('/[\;\s\n\r\;]/', $ids);

			set_time_limit(0);

			$this->load->model('ventas/m_pedidocliente');
			$this->load->model('catalogo/m_editorial');
			$this->load->model('catalogo/m_articulo');
			$this->load->model('proveedores/m_proveedor');
			$this->load->model('concursos/m_sala2');
			$this->load->model('concursos/m_biblioteca2');
			$this->load->model('concursos/m_pedido');
			$this->load->model('concursos/m_proveedorconcurso');
			$this->load->model('concursos/m_editorialconcurso');
			$this->load->model('concursos/m_articuloconcurso');
			$this->load->library('Messages');
			$pedidos = 0;

			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					$this->db->trans_begin();
					$this->messages->info(sprintf($this->lang->line('concursos_traspasando_pedido'), $id));
					$pedido = $this->m_pedidocliente->load($id, TRUE);
					// Existe la sala?
					$sala = $this->m_sala2->get(null, null, null, null, 'cSala=' . $id);
					if (count($sala) > 0)
					{
						$this->messages->error(sprintf($this->lang->line('concursos_pedido_ya_creado'), $id), 1);
					}
					else
					{
						// crea la sala
						$sala = $this->m_sala2->insert(array('cSala' => $id));

						// Existe la biblioteca?
						$desc = $pedido['cliente']['cEmpresa'] . ' - ' .
						$this->lang->line(($pedido['cRefInterna']=='g')?'sp_GENERAL':'sp_NARRATIVA');
						$biblioteca = $this->m_biblioteca2->get(null, null, null, null, 'cBiblioteca=' . $this->db->escape($desc));
						if (count($biblioteca) == 0)
						{
							$this->messages->info(sprintf($this->lang->line('concursos_creando_biblioteca'), $desc), 1);
							$biblioteca = $this->m_biblioteca2->insert(array('cBiblioteca' => utf8_decode($desc)));
							if ($biblioteca < 0)
							{
								$this->messages->error($this->m_biblioteca2->error_message(), 1);
								$this->db->trans_rollback();
								continue;
							}
							else
							{
								$this->messages->info(sprintf($this->lang->line('concursos_biblioteca_creada'), $biblioteca), 1);
							}
						}
						else
						{
							$biblioteca = $biblioteca[0]['nIdBiblioteca'];
						}

						// Crea el pedido
						$desc_pedido = $desc . ' - ' . $id;
						$this->messages->info(sprintf($this->lang->line('concursos_creando_pedido'), $desc_pedido), 1);

						$id_pedido = $this->m_pedido->insert(array(
							'cPedido' 		=> utf8_decode($desc_pedido),
							'dEntrada'		=> $pedido['dCreacion'],
							'nBiblioteca'	=> $biblioteca,
							'nSala'			=> $sala,
						));
						if ($id_pedido < 0)
						{
							$this->messages->error($this->m_pedido->error_message(), 1);
							$this->db->trans_rollback();
							continue;
						}

						$this->messages->info(sprintf($this->lang->line('concursos_pedido_creado'), $id_pedido), 1);

						// Crea las líneas del pedido
						$this->messages->info($this->lang->line('concursos_creando_lineas'), 1);
						$error = FALSE;
						$count = 0;
						#echo '<pre>'; var_dump($pedido['lineas']); echo '</pre>'; die();
						foreach($pedido['lineas'] as $linea)
						{

							// Existe el proveedor?
							$libro = $this->m_articulo->load($linea['nIdLibro']);
							if (!isset($libro['nIdLibro']))
							{
								$this->messages->error("No se ha encontrado el título {$linea['nIdLibro']}" , 3);
								$error = TRUE;
								break;
							}

							$this->messages->info(sprintf($this->lang->line('concursos_creando_articulo'), $libro['cTitulo'], $libro['nIdLibro']), 2);

							$proveedor = $this->m_articulo->get_proveedor_habitual($libro);
							#(isset($libro['nIdProveedorManual'])?$libro['nIdProveedorManual']:$libro['nIdProveedor']);
							#$this->messages->info('<pre>' .print_r($libro, TRUE) . '</pre>', 3);
							if (isset($proveedor))
							{
								$esta = $this->m_proveedorconcurso->load($proveedor);
								if (!$esta)
								{
									$data = $this->m_proveedor->load($proveedor);
									$this->messages->info(sprintf($this->lang->line('concursos_creando_proveedor'), $data['cEmpresa']), 3);
									$proveedor = $this->m_proveedorconcurso->insert(array(
										'cNombre' 		=> utf8_decode($data['cEmpresa']),
										'cNombreCorto' 	=> utf8_decode($data['cEmpresa']),
										'nIdProveedor'	=> $data['nIdProveedor'],
									));

									if ($proveedor < 0)
									{
										$this->messages->error($this->m_proveedorconcurso->error_message(), 3);
										$error = TRUE;
										break;
									}
									else
									{
										$proveedor = $data['nIdProveedor'];
										$this->messages->info(sprintf($this->lang->line('concursos_proveedor_creado'), $data['cEmpresa']), 3);
									}
								}
							}
							#$this->messages->info('Id Proveedor PRE: '.$proveedor, 3);

							//Existe la editorial?
							$editorial = $libro['nIdEditorial'];
							if (isset($editorial))
							{
								$esta = $this->m_editorialconcurso->load($editorial);
								if (!$esta)
								{
									$data = $this->m_editorial->load($editorial);
									$this->messages->info(sprintf($this->lang->line('concursos_creando_editorial'), $data['cNombre']), 3);
									$this->messages->info('Id Proveedor: '.$proveedor, 3);
									$editorial = $this->m_editorialconcurso->insert(array(
										'cEditorial' 	=> utf8_decode($data['cNombre']),
										'nIdProveedor'	=> $proveedor,
										'nIdEditorial'	=> $data['nIdEditorial'],
									));

									if ($editorial < 0)
									{
										$this->messages->error($this->m_editorialconcurso->error_message(), 3);
										$error = TRUE;
										break;
									}
									else
									{
										$editorial = $data['nIdEditorial'];
										$this->messages->info(sprintf($this->lang->line('concursos_editorial_creado'), $data['cNombre']), 3);
									}
								}
							}

							// Crea el artículo
							if ($editorial && $proveedor)
							{
								#print '<pre>'; print_r($linea); print '</pre>';
								$data= array (
									'nIdPedido'		=> $id_pedido,
									'cAutores'		=> utf8_decode($libro['cAutores']),
									'cTitulo'		=> utf8_decode($libro['cTitulo']),
									'fPrecio'		=> isset($linea['fPVP'])?$linea['fPVP']:$libro['fPVP'],
									'nIdProveedor'	=> $proveedor,
									'nIdEditorial'	=> $editorial,
									'cISBN'			=> $libro['cISBN'],
									'cISBNBase'		=> $libro['cISBNBase'],
									'cEAN'			=> $libro['nEAN']
								);
								for($i = 0; $i < $linea['nCantidad']; $i++)
								{
									$id_libro = $this->m_articuloconcurso->insert($data);
									if ($id_libro < 0)
									{
										$this->messages->error($this->m_articuloconcurso->error_message(), 3);
										$error = TRUE;
										break;
									}
									else
									{
										$this->messages->info(sprintf($this->lang->line('concursos_articulo_creado'), ($i+1), $linea['nCantidad'], $libro['cTitulo']), 3);
									}
								}
							}
							else
							{
								$this->messages->error($this->lang->line('concursos_falta_editorial_proveedor'), 3);
								$error = TRUE;
								break;
							}
							$count++;
						}
						if ($error)
						{
							$this->messages->info(sprintf($this->lang->line('concursos_cancelado'), $id), 1);
							$this->db->trans_rollback();
							continue;
						}
						$this->messages->info(sprintf($this->lang->line('concursos_lineas_creadas'), $count), 2);
						$pedidos++;
						$this->db->trans_commit();
						$this->messages->info(sprintf($this->lang->line('concursos_pedido_traspasado'), $id), 1);
					}
				}
			}
			$this->messages->info(sprintf($this->lang->line('concursos_pedidos_creadas'), $pedidos));
			$body = $this->messages->out($this->lang->line('Pasar pedido'));

			$this->out->html_file($body, $this->lang->line('Pasar pedido'), 'iconoConcursosPasarPedidoTab');
		}
		else
		{
			$data['url'] = site_url('concursos/importar/bibliopola');
			$data['title'] = $this->lang->line('Pasar pedido');
			$data['label'] = $this->lang->line('Pedidos');
			$data['icon'] = 'iconoConcursosPasarPedidoTab';
			$this->_show_js('bibliopola', 'concursos/pedidos.js', $data);
		}
	}

	function excel($cliente = null, $file = null, $rango = null, $crear = null, $ref = null, $dto = null, $seccion = null, $crear_libros = null)
	{
		$this->userauth->roleCheck(($this->auth.'.excel'));

		$file = isset($file) ? $file : $this->input->get_post('file');
		$cliente 	= isset($cliente)?$cliente:$this->input->get_post('cliente');
		$rango 		= isset($rango)?$rango:$this->input->get_post('rango');
		$crear 		= isset($crear)?$crear:$this->input->get_post('crear');
		$ref		= isset($ref)?$ref:$this->input->get_post('ref');
		$dto		= isset($dto)?$dto:$this->input->get_post('dto');
		$seccion	= isset($seccion)?$seccion:$this->input->get_post('seccion');
		$crear_libros = isset($crear_libros)?$crear_libros:$this->input->get_post('crear_libros');

		if (!empty($file))
		{
			$files = preg_split('/;/', $file);
			$files = array_unique($files);
			$count = 0;

			foreach ($files as $k => $file)
			{
				$this->load->library('UploadLib');
				if (!empty($file))
				{
					//$path = $this->uploadlib->get_pathfile($file);
					$this->load->library('tasks');
					$params[] = to_null($cliente);
					$params[] = to_null($file);
					$params[] = to_null($rango);
					$params[] = to_null($crear);
					$params[] = to_null($ref);
					$params[] = to_null($dto);
					$params[] = to_null($seccion);
					$params[] = to_null($crear_libros);
					$cmd = site_url("concursos/importar/excel_task");
					$this->tasks->add2(sprintf($this->lang->line('Importar EXCEL')) , $cmd, $params, null, FALSE);
					++$count;
				}
			}
			$this->out->success(sprintf($this->lang->line('importar-concurso-ficheros-tareas'), $count));
		}
		else
		{
			$this->_show_js('excel', 'concursos/excel.js');
		}
	}

	/**
	 * Importa un fichero EXCEL como pedido del cliente
	 * @param int $cliente Id del cliente
	 * @param string $file Fichero EXCEL de <upload> a importar
	 * @param string $filtro Ramgo EXCEL a tratar
	 * @param bool $crear TRUE: Crear el pedido, FALSE: solo analiza
	 * @param string $ref Referencia interna del cliente
	 * @param float $dto Descuento a aplicar
	 * @param int $seccion Sección de las líneas de pedido
	 */
	function excel_task($cliente = null, $file = null, $rango = null, $crear = null, $ref = null, $dto = null, $seccion = null, $crear_libros = null)
	{
		$this->userauth->roleCheck(($this->auth.'.excel'));
		#error_reporting(E_ERROR);
		$cliente 	= isset($cliente)?is_null_str($cliente):$this->input->get_post('cliente');
		$rango 		= isset($rango)?is_null_str($rango):$this->input->get_post('rango');
		$crear 		= isset($crear)?is_null_str($crear):$this->input->get_post('crear');
		$ref		= isset($ref)?is_null_str($ref):$this->input->get_post('ref');
		$dto		= isset($dto)?is_null_str($dto):$this->input->get_post('dto');
		$seccion	= isset($seccion)?is_null_str($seccion):$this->input->get_post('seccion');
		$crear_libros = isset($crear_libros)?is_null_str($crear_libros):$this->input->get_post('crear_libros');

		//echo "C: $cliente, FL: $file , FT: $rango , CR: $crear , R: $ref , DT: $dto , S: $seccion , CR2: $crear_libros , RU: $runner ";
		//die();

		if (isset($ref)) $ref = urldecode($ref);

		if (isset($file))
		{
			$this->load->library('UploadLib');
			$file = urldecode($file);
			//$destino = $this->config->item('bp_upload_path');
			$name = $file;
			$file = $this->uploadlib->get_pathfile($file);
		}
		if ($file)
		{
			set_time_limit(0);

			$this->load->library('Messages');
			$this->load->library('Importador');

			$crear = format_tobool($crear);
			$crear_libros = format_tobool($crear_libros);

			$this->messages->info(sprintf($this->lang->line('concursos_excel_test'), $file));
			$data = $this->importador->excel($file, $rango);
			#echo '<pre>'; print_r($data); die();

			if ($data == E_IMPORT_FILE_ERROR)
			{
				$this->messages->error($this->lang->line('concursos_excel_test_noexcel'));
			}
			else
			{
				// Información de los encontrados
				$this->messages->info(sprintf($this->lang->line('concursos_excel_aplicando_filter'), $data['filtro']['filter']));

				$this->messages->info($this->lang->line('concursos_excel_libros_encontrados'));
				if (count($data['libros']) > 0)
				{
					foreach($data['libros'] as $l)
					{
						$texto = implode(', ', $l['original']);
						$l = sprintf($this->lang->line('concursos_excel_libro_correcto_encontrado'), $l['original']['line'], $l['id'], $texto, format_price($l['original']['precio']),format_price($l['original']['pvp']), format_percent($dto));
						$this->messages->info($l, 1);
					}
				}
				$this->messages->info(sprintf($this->lang->line('concursos_excel_libros_encontrados_total'), count($data['libros'])));

				$this->messages->info($this->lang->line('concursos_excel_libros_no_encontrados'));
				if (count($data['isbns']) > 0)
				{
					foreach($data['isbns'] as $l)
					{
						$texto = implode(', ', $l);
						$l = sprintf($this->lang->line('concursos_excel_libro_correcto_noencontrado'), $l['line'], $texto);
						$this->messages->warning($l, 1);
					}
				}
				if (count($data['no_isbn']) > 0)
				{
					foreach($data['no_isbn'] as $l)
					{
						$texto = implode(', ', $l);
						$l = sprintf($this->lang->line('concursos_excel_no_isbn'), $l['line'], $texto);
						$this->messages->warning($l, 1);
					}
				}
				$this->messages->warning(sprintf($this->lang->line('concursos_excel_libros_no_encontrados_total'), count($data['no_isbn']) + count($data['isbns'])));

				$this->db->trans_begin();
				$error = FALSE;

				// Crea los libros
				if ($crear_libros && (count($data['isbns']) > 0))
				{
					$this->messages->info($this->lang->line('concurso_creando_libros'));
					$creados = $this->importador->crear_libros($data['isbns']);
					#print '<pre>'; var_dump($creados); print '</pre>';
					#$this->db->trans_rollback();
					#die();
					if ($creados === FALSE)
					{
						$this->messages->error($this->importador->get_error_message());
						$error = TRUE;
					}
					else
					{
						// Libros
						$this->load->model('catalogo/m_tipolibro');
						$tipos = array();
						foreach($creados['libros'] as $libro)
						{
							$this->messages->info(sprintf($this->lang->line('concurso_creando_libro'), string_encode($libro['cTitulo'])), 2);
							if (!isset($libro['nIdEditorial']))
							{
								$this->messages->warning($this->lang->line('concurso_creando_libros_no_editorial'), 3);
							}
							if (!isset($libro['nIdProveedor']))
							{
								$this->messages->warning($this->lang->line('concurso_creando_libros_no_proveedor'), 3);
							}
							if (!isset($libro['autores']))
							{
								$this->messages->warning($this->lang->line('concursos_libro_sin_autores'), 3);
							}
							$this->messages->info(sprintf($this->lang->line('concursos_libro_creado'), $libro['id']), 2);
							if (!isset($tipos[$libro['nIdTipo']]))
							{
								$r = $this->m_tipolibro->load($libro['nIdTipo']);
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
								$this->messages->info(sprintf($this->lang->line('concurso_creando_editorial'), string_encode($editorial['cNombre'])), 1);
								$this->messages->info(sprintf($this->lang->line('concursos_editorial_creada'), $editorial['id']), 1);
							}
						}

						// Autores
						if (isset($creados['autores']))
						{
							foreach($creados['autores'] as $autor)
							{
								$this->messages->info(sprintf($this->lang->line('concurso_creando_autor'), string_encode(implode(', ', $autor))), 1);
								$this->messages->info(sprintf($this->lang->line('concursos_autor_creado'), $autor['id']), 1);
							}
						}
					}
				}
				else
				{
					$this->messages->warning($this->lang->line('concurso_no_crear_articulos'));
				}

				// Crea el pedido
				if (!$error && $cliente && $seccion && $crear)
				{
					if ((count($data['libros']) == 0))
					{
						$this->messages->error($this->lang->line('concurso_creando_pedido_nolibros'));
					}
					else
					{
						$this->load->model('clientes/m_cliente');
						$c = $this->m_cliente->load($cliente);
						$this->messages->info(sprintf($this->lang->line('concurso_creando_pedido'), format_name($c['cNombre'], $c['cApellido'], $c['cEmpresa'])));

						$this->load->model('generico/m_seccion');
						$sc = $this->m_seccion->load($seccion);
						$this->messages->info(sprintf($this->lang->line('concurso_usando_seccion'), $sc['cNombre']));

						$dto = isset($dto)?$dto:0;
						$this->messages->info(sprintf($this->lang->line('concurso_usando_datos'), $dto, $ref));
							
						foreach($data['libros'] as $k => $l)
						{
							$data['libros'][$k]['nCantidad'] = 1;
							$data['libros'][$k]['fDescuento'] = $dto;
						}
						$id = $this->importador->crear_pedido_cliente($cliente, $seccion, $data['libros'], $ref, $ref);
						if ($id === FALSE)
						{
							$this->messages->error($this->importador->get_error_message());
							$error = TRUE;
						}
						else
						{
							$this->messages->info(sprintf($this->lang->line('concurso_pedido_creado'), $id));
						}
					}
				}
				else
				{
					$this->messages->warning($this->lang->line('concurso_no_crear_pedido'));
				}
				if ($error)
				{
					$this->db->trans_rollback();
				}
				else
				{
					$this->db->trans_commit();
				}
			}

			$body = $this->messages->out($this->lang->line('Importar EXCEL'));
			$this->out->html_file($body, $this->lang->line('Importar EXCEL'), 'iconoConcursosImportarEXCELTab');
		}
	}

	function excel2($file = null)
	{
		$this->userauth->roleCheck(($this->auth.'.excel'));

		if (isset($file))
		{
			$destino = $this->config->item('bp_upload_path');
			$name = $file;
			$file = $destino . '/' . $file;
		}
		if (!isset($file))
		{
			$this->load->library('Upload');
			$upload = $this->upload->get_file('excelfile');
			$file = $upload['file'];
			$name = $upload['name'];
		}
		$data = array(
			'success' 	=> TRUE,
			'name' 		=> $name,
			'file'		=> $file,
		);
		$this->out->send($data);
	}
}

/* End of file importar.php */
/* Location: ./system/application/controllers/concursos/importar.php */
