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
   
/**
 * SINLI
 * @author alexl
 *
 */
class SinliLib
{
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;
	/**
	 * Último error
	 * @var string
	 */
	private $_error;
	/**
	 * URL del buzón de correo
	 * @var string
	 */
	private $url;
	/**
	 * Username del buzón de correo
	 * @var string
	 */
	private $username;
	/**
	 * Contraseña del buzón de correo
	 * @var string
	 */
	private $password;

	/**
	 * Tipos de información de estado
	 * @var array
	 */
	public $estadosenvio = array (
		0 => 'Sin clasificar',
		1 => 'Disponible',
		2 => 'Descatalogado',
		3 => 'Agotado',
		4 => 'En reimpresión inmediata, hasta 1 mes',
		5 => 'En reimpresión, sin fecha conocida de servicio',
		6 => 'Sin existencia',
		7 => 'No pertenece a nuestro fondo',
		8 => 'Novedad. Próxima aparición',
		9 => 'Últimas novedades'
		);
	
	/**
	 * Formas de pago
	 * @var array
	 */
	public $formapago = array(	 
		1 => 'Contado',
		2 => '30 días',
		3 => '60 días',
		4 => '90 días',
		5 => '120 días',
		6 => 'Otras'
	);
	
	/**
	 * Formato de registros generales para ser reutilizados por diferentes métodos
	 * @var array
	 */
	private $parts = array(
		'id' => array(
			'tipo' => '1A', 
			'formato' =>	'1A', 
			'id' => '6A', 
			'version' => '2A', 
			'emisor' =>'8A', 
			'destino' => '8A', 
			'cantidad' => '5I', 
			'numero' => '7I', 
			'usemisor' => '15A', 
			'usdestino' => '15A', 
			'libre' => '7A', 
			'FANDE' => '5A'),
		# Identificador
		'id2' => array(
			'tipo' => '1A', 
			'origen' =>	'50A', 
			'destino' => '50A', 
			'fichero' => '6A', 
			'version' => '2A',
			'emisor' =>'8N', 
			),			
		);
	
	/**
	 * Constructor
	 * @return SinliLib
	 */
	function __construct()
	{
		$this->obj = &get_instance();
		$this->url = $this->obj->config->item('sinli.mailbox.url');
		$this->password = $this->obj->config->item('sinli.mailbox.password');
		$this->username = $this->obj->config->item('sinli.mailbox.username');

		log_message('debug', 'Notas Class Initialised via ' . get_class($this->obj));
	}

	/**
	 * Comprueba si hay mensajes en el buzón de SINLI
	 * @return array Ficheros descargados de SINLI
	 */
	function check($msgs = 100)
	{
		set_time_limit(0);
		$files = array();
		$debug = $this->obj->config->item('sinli.debug');	
		$debug2 = $this->obj->config->item('sinli.debug.temp');	
		if (($emailbox = imap_open('{' . $this->url . '}INBOX', $this->username, $this->password)) !== FALSE)
		{
			$emailinfo = imap_check($emailbox);
			$count = min($emailinfo->Nmsgs, $msgs);
			for ($m = 1; $m <= $count; $m++)
			{
				$res = $this->getmsg($emailbox, $m);
				$attachments = $res['attachments']; 
				
				foreach($attachments as $filename => $data)
				{
					$name = $filename;
					$d = pathinfo($name);
					$name = $d['basename'];
					$filename = DIR_SINLI_PATH . $name;
					$filename2 = DIR_SINLI_TEMP_PATH . $name;
					$fp = fopen($filename, 'w');
					if ($fp)
					{
						fputs($fp, utf8_encode($data));
						fclose($fp);
						$res = $this->process(array('filename' => $filename, 'date' => $res['date'], 'subject' => $res['subject']));
						if ($res === TRUE)
						{
							# No reconoce el formato, lo guarda
							$files[] = array('file' => $filename, 'type' => 2);
						}
						elseif ($res===FALSE)
						{
							# ha habido error
							$files[] = array('file' => $filename, 'type' => 3, 'error' => $this->get_error());
						}
						else
						{
							# Correcto
							$files[] = array('file' => $filename, 'type' => 1, 'id' => $res);							
							if ($debug2)
							{
								copy($filename, $filename2);
							}
							if (!$debug)
							{
								#borrar mensaje
								unlink($filename);
								imap_delete($emailbox, $m);
							} 
						}
					}					
					else
					{
						# ha habido error
						$files[] = array('file' => $filename, 'type' => 4, 'error' => $this->obj->lang->line('sinli-error-fichero-no-encontrado'));						
					}
				}			
			}
			imap_close($emailbox, CL_EXPUNGE);
		}
		else
		{
			return FALSE;
		}
		return array('emails' => $count, 'files' => $files, 'count' => $emailinfo->Nmsgs);
	}
	
	/**
	 * Transforma la fecha de SINLI al sistema
	 * Formatos: mmyyyy o yyyymmdd
	 * @param string $value Fecha SINLI 
	 * @return int
	 */
	private function to_date($value)
	{
		if ((int) $value > 0)
		{
			return (strlen($value)==8)?mktime(0, 0, 0, substr($value, 4, 2), substr($value, 6, 2), substr($value, 0, 4)):
				mktime(0, 0, 0, substr($value, 0, 2), 1, substr($value, 2, 4));
		}
		
		return null;
	}
	
	/**
	 * Codifica un valor individual
	 * @param mixed $value Valor a codificar
	 * @param string $type N: flotante, I: Entero, D: Fecha, A: alfanumérico
	 * @param int $len Tamaño del campo
	 * @return string Valor codificado
	 */
	private function encode_value($value, $type , $len)
	{
		$value2 = '';
		$left = FALSE;
		switch ($type) {
			case 'N':
				$left = TRUE;
				$value2 = (string)(int) ($value * 100);				
				break;
			case 'I':
				$left = TRUE;
				$value2 = (string)(int) ($value);
				break;
			case 'D':
				$value2 = date('Ymd', $value);
				break;
			case 'A':
				$value2 = $value;
			default:				
				break;
		}
		$value2 = str_pad($value2, $len, ' ', ($left?STR_PAD_LEFT:STR_PAD_RIGHT));
		return substr($value2, 0, $len);
	}
	
	/**
	 * Codifica un array en una línea de texto
	 * @param array $dato Datos a codificar con la estructura 'indice' => 'valor'
	 * @param array $format Formato de los campos. 'indice' => 'formato'
	 * @return string, línea codificada
	 */
	private function encode($data, $format)
	{
		$res = '';
		foreach ($format as $k => $v)
		{
			$type = substr($v, strlen($v) - 1);
			$len = substr($v, 0, strlen($v) - 1);
			$value = $this->encode_value(isset($data[$k])?$data[$k]:'', $type, $len);
			$res .= $value;
		}
		
		return $res;
	}
	
	/**
	 * Decodifica una línea del archivo
	 * @param string $data Línea a formatear
	 * @param array $format Formato de la línea $k => $v donde $k es el campo y $v es <tam><tipo>, donde <tam> es tamaño del campo y <tipo> el formato (N, A, D)
	 * @return array, formateado según el formato  
	 */
	private function decode($data, $format)
	{
		$pos = 0;
		$parts = array();
		foreach($format as $k => $f)
		{
			$type = substr($f, strlen($f) - 1);
			$len = substr($f, 0, strlen($f) - 1);
			$value = substr($data, $pos, $len);
			$parts[$k] = ($type == 'N')?(float)($value/100):(($type == 'I')?(int)$value:(($type == 'D')?$this->to_date($value):trim($value)));
			$pos += $len; 			
		}		
		$parts['_original'] = $data;
		return $parts;
	}

	/**
	 * Tipo 04, Envio
	 * Procesa el archivo de ENVIO y genera un array con el contenido del documento.
	 * @param array $lineas Líneas del archivo
	 * @return array Documento según normalización
	 */
	private function import_ENVIO($lineas)
	{
		# Cabecera
		$parts['C'] = array(
			'tipo' => '1A',
			'proveedor' => '40A',
			'cliente' => '40A',
			'numero' => '10A',
			'fecha' => '8D',
			'tipodoc' => '1A',
			'tipoenvio' => '1A',
			'feria' => '1A',
			'gastos' => '10N',
			'moneda' => '1A',
			'buzon' => '8A'
			);
		# Líneas de documento
		$parts['D'] = array(
			'tipo' => '1A',
			'isbn' => '17A',
			'ean' => '18A',
			'referencia' => '15A',
			'titulo' => '50A',
			'cantidad' => '6I',
			'precio' => '10N',
			'pvp' => '10N',
			'descuento' => '6N',
			'iva' => '5N',
			'novedad' => '1A',
			'tipoprecio' => '1A',
			'fechadevolucion' => '8D',
			'codigo' => '10A'
			);
		# Información adicional
		$parts['M'] = array(
			'tipo' => '1A',
			'Texto' => '80A'
			);
		# Ivas
		$parts['V'] = array(
			'tipo' => '1A',
			'iva' => '5N',
			'base' => '10N',
			'valoriva' => '10N',
			'recargo' => '5N',
			'req' => '10N'
			);
		# Totales
		$parts['T'] = array(
			'tipo' => '1A',
			'unidades' => '8I',
			'bruto' => '10N',
			'neto' => '10N',
			);
		# Estado
		$parts['E'] = array(
			'tipo' => '1A',
			'isbn' => '17A',
			'ean' => '18A',
			'referencia' => '15A',
			'titulo' => '50A',
			'estado' => '1I',
			'Eliminar' => '1N',
			'fechaservir' => '8D'
		);
		
		return $this->import($lineas, $parts);
	}

	/**
	 * Tipo 08, Cambio de precios
	 * Procesa el archivo de CAMPRE y genera un array con el contenido del documento.
	 * @param array $lineas Líneas del archivo
	 * @return array Documento según normalización, FALSE si no es un formato correcto
	 */
	private function import_CAMPRE($lineas)
	{
		# Cabecera
		$parts['C'] = array(
			'tipo' => '1A',
			'proveedor' => '40A',
			'fecha' => '8D',
			'moneda' => '1A',
			);
		# Líneas de documento
		$parts['D'] = array(
			'tipo' => '1A',
			'isbn' => '17A',
			'ean' => '18A',
			'referencia' => '15A',
			'precio' => '10N',
			'pvp' => '10N',
			'iva' => '5N',
			'titulo' => '50A',
			'tipoprecio' => '1A',
			);
		# Estado
		$parts['E'] = array(
			'tipo' => '1A',
			'isbn' => '17A',
			'ean' => '18A',
			'referencia' => '15A',
			'titulo' => '50A',
			'estado' => '1N',
			'fechaservir' => '8D'
		);
		
		return $this->import($lineas, $parts);
	}

	/**
	 * Tipo 09. Cambio de estado
	 * Procesa el archivo de ESTADO y genera un array con el contenido del documento.
	 * @param array $lineas Líneas del archivo
	 * @return array Documento según normalización, FALSE si no es un formato correcto
	 */
	private function import_ESTADO($lineas)
	{
		# Cabecera
		$parts['C'] = array(
			'tipo' => '1A',
			'proveedor' => '40A',
			);
		# Líneas de documento
		$parts['E'] = array(
			'tipo' => '1A',
			'isbn' => '17A',
			'ean' => '18A',
			'referencia' => '15A',
			'titulo' => '50A',
			'estado' => '1I',
			'fecha' => '8D',
			);
		
		return $this->import($lineas, $parts);
	}

	/**
	 * Tipo 02. Validación del albarán de pedido del cliente
	 * Procesa el archivo de VALPED y genera un array con el contenido del documento.
	 * @param array $lineas Líneas del archivo
	 * @return array Documento según normalización, FALSE si no es un formato correcto
	 */
	private function import_VALPED($lineas)
	{
		# Cabecera
		$parts['C'] = array(
			'tipo' => '1A',
			'cliente' => '40A',
			'proveedor' => '40A',
			'fecha' => '8D'
			);
		# Líneas de documento
		$parts['D'] = array(
			'tipo' => '1A',
			'isbn' => '17A',
			'ean' => '18A',
			'referencia' => '15A',
			'titulo' => '50A',
			'codigo' => '10A',
			'cantidad' => '6I',
			'estado' => '1N',
			'gastos' => '10N',
			);
		
		return $this->import($lineas, $parts);
	}

	/**
	 * Tipo 10. Ficha del libro
	 * Procesa el archivo de LIBROS y genera un array con el contenido del documento.
	 * @param array $lineas Líneas del archivo
	 * @return array Documento según normalización, FALSE si no es un formato correcto
	 */
	private function import_LIBROS($lineas)
	{
		# Cabecera
		$parts['C'] = array(
			'tipo' => '1A',
			'proveedor' => '40A',
			'moneda' => '1A'
			);
		# Líneas de documento
		$libro = array(
			'ean' => '18A',
			'isbn' => '17A',
			'isbn-obra' => '17A',
			'isbn-tomo' => '17A',
			'isbn-fasciculo' => '17A',
			'referencia' => '15A',
			'titulo' => '80A',
			'subtitulo' => '80A',
			'Autor' => '150A',
			'pais' => '2A',
			'editorial-codigo' => '8A',
			'editorial' => '40A',
			'encuadernacion' => '2I',
			'lengua' => '3A',
			'edicion' => '2A',
			'publicacion' => '6D',
			'paginas' => '4I',
			'ancho' => '4I',
			'alto' => '4I',
			'cdu' => '20A',
			'palabrasclave' => '80A',
			'situacion' => '1I',
			'tipo' => '2I',
			'precio' => '10N',
			'pvp' => '10N',
			'iva' => '5N',
			'coleccion' => '40A',
			'ncoleccion' => '10A',
			'nvolumen' => '4A',
			'imagen' => '1A',
			'url' => '50A',
			'ilustador-cubierta' => '150A',
			'ilustador-interior' => '150A',
			'ilustraciones' => '5I',
			'traductor' => '150A',
			'idioma-original' => '3A',
			'grosor' => '3I',
			'peso' => '6I',
			'audiencia' => '3I',
			'nivel-lectura' => '1I',
			'nivel' => '15A',
			'curso' => '80A',
			'asignatura' => '80A',
			'comunidad-autonoma' => '36A',
			'resumen' => '255A',
			'tipoprecio' => '1A'
			);
		$sublineas = array_slice($lineas, 0, 3);
		$res = $this->import($sublineas, $parts);
		for ($i = 3; $i < count($lineas); $i++)
		{
			$res['L'][] = $this->decode($lineas[$i], $libro); 
		}
		return $res;
		#var_dump($res, $lineas); die();
	}

	/**
	 * Tipo 07. Factura
	 * Procesa el archivo de FACTUL y genera un array con el contenido del documento.
	 * @param array $lineas Líneas del archivo
	 * @return array Documento según normalización, FALSE si no es un formato correcto
	 */
	private function import_FACTUL($lineas)
	{
		# Cabecera
		# Cabecera
		$parts['C'] = array(
			'tipo' => '1A',
			'proveedor' => '40A',
			'cliente' => '40A',
			'numero' => '10A',
			'fecha' => '8D',
			'moneda' => '1A',
			);
		# Líneas de documento
		$parts['D'] = array(
			'tipo' => '1A',
			'numero' => '10A',
			'fecha' => '8D',
			'importe' => '10N',
			);
		# Ivas detalle
		$parts['E'] = array(
			'tipo' => '1A',
			'numero' => '10A',
			'iva' => '5N',
			'base' => '10N',
			'valoriva' => '10N',
			'recargo' => '5N',
			'req' => '10N'
			);
		# Totales
		$parts['T'] = array(
			'tipo' => '1A',
			'total' => '10N',
			);
		
		# Ivas
		$parts['V'] = array(
			'tipo' => '1A',
			'iva' => '5N',
			'base' => '10N',
			'valoriva' => '10N',
			'recargo' => '5N',
			'req' => '10N'
			);

		# Vencimientos
		$parts['P'] = array(
			'tipo' => '1A',
			'forma' => '1A',
			'importe' => '10N',
			'fecha' => '8D',
			);
		return $this->import($lineas, $parts);
	}

	/**
	 * Tipo 06. Albarán o factura de abono
	 * Procesa el archivo de ABONO y genera un array con el contenido del documento.
	 * @param array $lineas Líneas del archivo
	 * @return array Documento según normalización, FALSE si no es un formato correcto
	 */
	private function import_ABONO($lineas)
	{
		# Cabecera
		# Cabecera
		$parts['C'] = array(
			'tipo' => '1A',
			'proveedor' => '40A',
			'cliente' => '40A',
			'numero' => '10A',
			'fecha' => '8D',
			'tipodocumento' => '1A',
			'referencia' => '10A',
			'fechadevolucion' => '8D',
			'tipoabono' => '1A',
			'feria' => '1A',
			'gastos' => '10N',
			'moneda' => '1A',
			);
		# Líneas de documento
		$parts['D'] = array(
			'tipo' => '1A',
			'numero' => '10A',
			'fecha' => '8D',
			'importe' => '10N',
			);

		# Líneas de documento
		$parts['D'] = array(
			'tipo' => '1A',
			'isbn' => '17A',
			'ean' => '18A',
			'referencia' => '15A',
			'titulo' => '50A',
			'cantidad' => '6I',
			'precio' => '10N',
			'pvp' => '10N',
			'descuento' => '6N',
			'iva' => '5N',
			'novedad' => '1A',
			'tipoprecio' => '1A',
			);

		# Totales
		$parts['T'] = array(
			'tipo' => '1A',
			'unidades' => '8I',
			'total' => '10N',
			);
		
		# Ivas
		$parts['V'] = array(
			'tipo' => '1A',
			'iva' => '5N',
			'base' => '10N',
			'valoriva' => '10N',
			'recargo' => '5N',
			'req' => '10N'
			);

		# Artículos rechazados
		$parts['R'] = array(
			'tipo' => '1A',
			'isbn' => '17A',
			'ean' => '18A',
			'referencia' => '15A',
			'titulo' => '50A',
			'causa' => '30A',
			);
		#$res =  $this->import($lineas, $parts);
		#var_dump($res); 
		return $this->import($lineas, $parts);
	}

	/**
	 * Procesa el archivo según el formato indicado
	 * @param array $lineas Líneas del archivo
	 * @param array $parts Formato de las líneas del archivo
	 * @return array Documento según normalización, FALSE si no es un formato correcto
	 */
	private function import($lineas, $parts)
	{
		# Lee las líneas
		$doc = array();
		for ($i = 2; $i < count($lineas); $i++)
		{
			if (!empty($lineas[$i]))
			{
				$t = $lineas[$i][0];
				if (isset($parts[$t]))
				{
					$doc[$t][] = $this->decode($lineas[$i], $parts[$t]);
				}
			}			
		}
		return $doc;							
	}

	/**
	 * Obtiene el identificador del documento a partir de la línea
	 * @param string $linea Línea de texto con el Id (la 2ª línea del archivo)
	 * @return array
	 */
	private function identificacion($linea)
	{

		$res = $this->decode($linea, $this->parts['id']);
		if ($res['FANDE'] != 'FANDE')
		{
			$this->_error = $this->obj->lang->line('sinli-id-invalido');
			return FALSE;			
		}
		return $res;
	}

	/**
	 * Procesa el archivo sinli indicado en el parámetro
	 * @param array $file 'filename' => Fichero a procesar, 'date' => Fecha del email, 'subject' => Asunto del email
	 * @return mixed, FALSE: no se ha podido procesar, 
	 * 	TRUE: no es un formato soportado, 
	 * 	int: El id del registro añadido a la tabla
	 */
	function process($file)
	{
		#if (!is_array($files)) $files = array($files);
		$this->obj->load->model('sys/m_sinli');
		$data = file_get_contents($file['filename']);
		#echo $data . "\n";
		$data = utf8_decode($data);
		#echo $data . "\n";
		$data = quoted_printable_decode($data);
		#echo $data . "\n";
		$lineas = preg_split('/\n/', $data);
		#var_dump($lineas);
		#die();
		if (count($lineas) > 0)
		{
			$id = $this->identificacion($lineas[0]);
			#var_dump($id);
			if ($id === FALSE) return FALSE;
			$method = 'import_' . trim($id['id']);
			if (method_exists($this, $method))
			{				
				$res = $this->$method($lineas);
				$res['ID'] = $id;
			}
			else
			{
				$res = null;							
			}
			if (isset($res))
			{
				$data = array(
					'cOrigen' 	=> $id['emisor'],
					'cTipo' 	=> $id['id'],
					'cFichero' 	=> serialize($res),
					'dFecha' 	=> !empty($file['date'])?$file['date']:time(),
					'cAsunto' 	=> $file['subject']
				);
				$id = $this->obj->m_sinli->insert($data);
				if ($id < 0)
				{
					$this->_error = $this->obj->lang->line('sinli-error-basedatos');
					return FALSE;
				}
				return $id;	
			}
			#$this->_error = $this->obj->lang->line('sinli-error-formato-nosportado');
			return TRUE;
		}
		else
		{
			$this->_error = $this->obj->lang->line('sinli-fichero-vacio');
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * Obtiene el mensaje del buzón de correo
	 * @link http://es.php.net/manual/es/function.imap-fetchstructure.php 
	 * @param int $mbox IMAP Stream
	 * @param int $mid Número de mensaje
	 * @return array  	'htmlmsg' => Mensaje HTML,  
	 * 					'plainmsg' => mensaje Texto, 
	 * 					'charset' => codificación, 
	 * 					'date' => fecha del mensaje
	 * 					'subject' => Asunto del mensaje
	 * 					'attachments' => Ficheros adjuntos);
	 */
	private function getmsg($mbox, $mid)
	{
		// input $mbox = IMAP stream, $mid = message id

		// HEADER
		$h = imap_header($mbox, $mid);
		// add code here to get date, from, to, cc, subject...
		#var_dump($h);
		// BODY
		$s = imap_fetchstructure($mbox, $mid);
		$attachments = array();
		if (!isset($s->parts))// simple
			$res = $this->getpart($mbox, $mid, $s, 0, $attachments);
		// pass 0 as part-number
		else
		{
			// multipart: cycle through each part
			foreach ($s->parts as $partno0 => $p)
			{
				$res = $this->getpart($mbox, $mid, $p, $partno0 + 1, $attachments);
			}
		}
		
		$res['attachments'] = $attachments;
		$res['date'] = strtotime($h->date);
		$res['subject'] = $h->subject;
		
		return $res;
	}
	
	/**
	 * Decodifica una de las partes del email
	 * @param string $message Mensaje a decodificar
	 * @param int $coding Id de la codificación
	 * @return string Texto codificado
	 */
	private function getdecodevalue($message, $coding)
	{
		if ($coding == 0)
		{
			return imap_8bit($message);
		}
		elseif ($coding == 1)
		{
			return imap_8bit($message);
		}
		elseif ($coding == 2)
		{
			return imap_binary($message);
		}
		elseif ($coding == 3)
		{
			return base64_decode($message);
		}
		elseif ($coding == 4)
		{
			return quoted_printable_decode($message);
		}
		elseif ($coding == 5)
		{
			return base64_decode($message);
		}
		return $message;
	}
	
	/**
	 * Obtiene una parte de un mensaje multipart
	 * @param object $mbox IMAP Stream
	 * @param int $mid Número de mensaje
	 * @param object $p Parte
	 * @param int $partno Número de parte
	 * @param array $attachments Ficheros adjuntos
	 * @return array  'htmlmsg' => Mensaje HTML,  'plainmsg' => mensaje Texto, 'charset' => codificación);
	 */
	private function getpart($mbox, $mid, $p, $partno, &$attachments)
	{
		// $partno = '1', '2', '2.1', '2.1.3', etc for multipart, 0 if simple

		// DECODE DATA
		$data = ($partno) ? imap_fetchbody($mbox, $mid, $partno) : // multipart
		imap_body($mbox, $mid);
		// simple
		// Any part may be encoded, even plain text messages, so check everything.
		$data = $this->getdecodevalue($data, (int)$p->encoding);
		/*if ($p->encoding == 4)
			$data = quoted_printable_decode($data);
		elseif ($p->encoding == 3)
			$data = base64_decode($data);*/

		// PARAMETERS
		// get all parameters, like charset, filenames of attachments, etc.
		$params = array();
		if ($p->parameters)
			foreach ($p->parameters as $x)
				$params[strtolower($x->attribute)] = $x->value;
		if (isset($p->dparameters))
			foreach ($p->dparameters as $x)
				$params[strtolower($x->attribute)] = $x->value;

		// ATTACHMENT
		// Any part with a filename is an attachment,
		// so an attached text file (type 0) is not mistaken as the message.
		if (isset($params['filename']) || isset($params['name']))
		{
			// filename may be given as 'Filename' or 'Name' or both
			$filename = isset($params['filename']) ? $params['filename'] : $params['name'];
			// filename may be encoded, so see imap_mime_header_decode()
			$attachments[$filename] = $data;
			// this is a problem if two files have same name
		}

		$htmlmsg = $plainmsg = $charset = '';
		// TEXT
		if ($p->type == 0 && $data)
		{
			// Messages may be split in different parts because of inline attachments,
			// so append parts together with blank row.
			if (strtolower($p->subtype) == 'plain')
				$plainmsg .= trim($data) . "\n\n";
			else
				$htmlmsg .= $data . "<br><br>";
			$charset = isset($params['charset'])?$params['charset']:null;
			// assume all parts are same charset
		}

		// EMBEDDED MESSAGE
		// Many bounce notifications embed the original message as type 2,
		// but AOL uses type 1 (multipart), which is not handled here.
		// There are no PHP functions to parse embedded messages,
		// so this just appends the raw source to the main message.
		elseif ($p->type == 2 && $data)
		{
			$plainmsg .= $data . "\n\n";
		}

		// SUBPART RECURSION
		if (isset($p->parts))
		{
			foreach ($p->parts as $partno0 => $p2)
				$this->getpart($mbox, $mid, $p2, $partno . '.' . ($partno0 + 1), $attachments);
			// 1.2, 1.2.1, etc.
		}
		
		return array('htmlmsg' => $htmlmsg,  'plainmsg' => $plainmsg, 'charset' => $charset);		
	}

	/**
	 * Envía un mensaje de SINLI
	 * @param string $tipo Tipo de documento
	 * @param array $data Datos a enviar. Depende de cada tipo de documento
	 * @param string $destino Id de SINLI
	 * @param string $emaildestino Email de destino
	 * @param string $udestino Nombre de la persona en destino
	 * @return bool, TRUE: se ha podido enviar sin problemas
	 */
	function send($tipo, $data, $destino, $emaildestino, $udestino = null)
	{
		# Genera el texto
		$texto = $this->export($tipo, $data, $destino, $emaildestino, $udestino);
		if (!isset($texto))
		{
			$this->_error = sprintf($this->obj->lang->line('sinli-error-fichero-no-generado'), $tipo);
			return FALSE;
		}
		# Prepara el email
		$subject = 'ESFANDE' . 
			$this->encode_value($this->obj->config->item('sinli.identificacion'), 'A', 8) .
			'ESFANDE' .
			$this->encode_value($destino, 'A', 8) .
			$this->encode_value($tipo, 'A', 6) .
			$this->encode_value($texto['version'], 'A', 2) .
			'FANDE';
			
		# Crea el archivo
		$filename = DIR_TEMP_PATH . $tipo . str_pad($texto['numero'], 8, '0') . '.txt';
		file_put_contents($filename, $texto['Texto']);
		$this->obj->load->library('Emails');
		$this->obj->load->library('Configurator');
		$cc = ($this->obj->config->item('sinli.cc') === TRUE) ? array($this->obj->configurator->user('bp.email.from')) : null;
		$debug = $this->obj->config->item('sinli.debug');
		if ($debug != FALSE)
		{
			$e = preg_split('/\;/', $this->obj->config->item('sinli.emaildebug'));
			foreach ($e as $em)
			{
				$to[] = trim($em);
			}
		}
		else
		{
			$to[] = trim($emaildestino);
		}
		$res = $this->obj->emails->send($subject, '', $to, $cc, null, array($filename), null, $this->obj->config->item('sinli.email'));
		if ($res === TRUE) return TRUE;
		$this->_error = $res;
		return FALSE;	
	}
	
	/**
	 * Crea un documento de SINLI
	 * @param string $tipo Tipo de documento
	 * @param array $data Datos a enviar. Depende de cada tipo de documento
	 * @param string $destino Id de SINLI
	 * @param string $emaildestino Email de destino
	 * @param string $udestino Nombre de la persona en destino
	 * @return array, 'version' => version, 'Texto' => documento en texto, 'numero' => Id del documento);
	 */
	function export($tipo, $data, $destino, $emaildestino, $udestino = null)
	{
		$method = 'export_' . trim($tipo);
		if (method_exists($this, $method))
		{				
			$texto = $this->$method($data, $destino, $emaildestino, $udestino);
			return $texto;
		}
		return null;
	}
	
	/**
	 * Tipo 01. Albarán de pedido del cliente
	 * Crea un documento de SINLI de tipo PEDIDO
	 * @param array $data Datos a enviar. Depende de cada tipo de documento
	 * @param string $destino Id de SINLI
	 * @param string $emaildestino Email de destino
	 * @param string $udestino Nombre de la persona en destino
	 * @return array, 'version' => 03, 'Texto' => documento en texto, 'numero' => Id del documento);
	 */
	private function export_PEDIDO($data, $destino, $emaildestino, $udestino)
	{
		$version = '03';
		if (!isset($data['entrega']) || (count($data['entrega']) == 0))
		{
			$this->obj->load->model('proveedores/m_direccion');
			$data['entrega'] = $this->obj->m_direccion->load($this->obj->config->item('bp.compras.direcciones.default'));
		}
		#var_dump($data['entrega']); die();
		# Cabecera
		$format = array(
			'tipo' => '1A',
			'cliente' => '40A',
			'proveedor' => '40A',
			'fecha' => '8D',
			'numero' => '10A',
			'tipodoc' => '1A',
			'moneda' => '1A'
			);
		$linea = array(
			'tipo' => 'C',
			'cliente' => $this->obj->config->item('company.name'),
			'proveedor' => $data['cProveedor'],
			'fecha' => $data['dFechaEntrega'],
			'numero' => $data['nIdPedido'],
			'tipodoc' => ($data['bDeposito'])?'D':'N',
			'moneda' => 'E'
			);
		$lineas[] =  $this->encode($linea, $format);
		if (isset($data['entrega']))
		{
			$format = array(
				'tipo' => '1A',
				'Nombre' => '50A',
				'direccion' => '80A',
				'cp' => '5A',
				'localidad' => '50A',
				'provincia' => '40A',
				);
			$linea = array(
				'tipo' => 'E',
				'Nombre' => $data['entrega']['cTitular'],
				'direccion' => $data['entrega']['cCalle'],
				'cp' => $data['entrega']['cCP'],
				'localidad' => (trim($data['entrega']['cPoblacion'])!='')?$data['entrega']['cPoblacion']:$data['entrega']['cRegion'],
				'provincia' => $data['entrega']['cRegion'],
				);
			$lineas[] =  $this->encode($linea, $format);
		}
		#var_dump(format_date($data['dFechaEntrega']), $linea, $lineas); die();
		# Líneas de documento
		$format = array(
			'tipo' => '1A',
			'isbn' => '17A',
			'ean' => '18A',
			'referencia' => '15A',
			'titulo' => '50A',
			'cantidad' => '6I',
			'precio' => '10N',
			'pendientes' => '1A',
			'origen' => '1A',
			'urgente' => '1A',
			'codigo' => '10A'
			);
		foreach ($data['lineas'] as $l)
		{
			$linea = array(
				'tipo' => 'D',
				'isbn' => $l['cISBN'],
				'ean' => $l['nEAN'],
				'referencia' => '',
				'titulo' => utf8_decode($l['cTitulo']),
				'cantidad' => $l['nCantidad'],
				'precio' => $l['fPrecio'],
				'pendientes' => 'S',
				'origen' => 'N',
				'urgente' => 'N',
				'codigo' => $l['nIdLinea']
			);
			$lineas[] = $this->encode($linea, $format);			
		}
		$texto = $this->crear_fichero($lineas, 'PEDIDO', $destino, $emaildestino, $udestino, $version, $data['nIdPedido']);
		#echo '<pre>'; print $texto; echo '</pre>'; die();
		return array('version' => $version, 'Texto' => $texto, 'numero' => $data['nIdPedido']);		
	}

	/**
	 * Crea el documento de SINLI, con la identificación FANDE y SINLI y el resto de las líneas
	 * @param array $lineas Líneas según tipo de documento
	 * @param string $tipo Tipo de documento
	 * @param string $destino Id de SINLI
	 * @param string $emaildestino Email de destino
	 * @param string $udestino Nombre de la persona en destino
	 * @param string $version Versión del documento
	 * @param string $numero Número de documento
	 * @return string, Texto con las líneas separdas por CR+LF
	 */
	private function crear_fichero($lineas, $tipo, $destino, $emaildestino, $udestino, $version, $numero)
	{
		$this->obj->load->library('Userauth');
		$linea = array(
			'tipo' => 'I', 
			'formato' => 'N', 
			'id' => $tipo, 
			'version' => $version, 
			'emisor' => $this->obj->config->item('sinli.identificacion'), 
			'destino' => $destino, 
			'cantidad' => 2 + count($lineas),
			'numero' => $numero, 
			'usemisor' => $this->obj->userauth->get_username(), 
			'usdestino' => $udestino, 
			'libre' => '', 
			'FANDE' => 'FANDE'
		);

		$id1 = $this->encode($linea, $this->parts['id']);
		$linea = array(
			'tipo' => 'I', 
			'origen' =>	$this->obj->config->item('sinli.email'), 
			'destino' => $emaildestino, 
			'fichero' => $tipo, 
			'version' =>  $version,
			'emisor' => $numero, 
		);
		$id2 = $this->encode($linea, $this->parts['id2']);
		$fichero[] = $id1;
		$fichero[] = $id2;
		return implode(chr(13).chr(10) ,array_merge($fichero, $lineas));
	}

	/**
	 * Último error generado
	 * @return string
	 */
	function get_error()
	{
		return $this->_error;
	}

}

/* End of file sinli.php */
/* Location: ./system/application/libraries/sinli.php */
