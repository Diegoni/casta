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
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Modos de pago
 */
define('MP_ACUENTA' 			,6);
define('MP_ABONO'				,4);
define('MP_AMEXDINERS'			,9);
define('MP_CHEQUE'				,3);
define('MP_DATAFONOECOMMERCE'	,1);
define('MP_METÁLICO'			,5);
define('MP_NODEFINIDO'			,10);
define('MP_REEMBOLSO'			,7);
define('MP_TARJETA'				,2);
define('MP_TRANSFERENCIA'		,8);

/**
 * Generador de los asientos para el traspaso a contabilidad
 * @author alexl
 *
 */
class Bp2lc  extends MY_Controller
{
	/**
	 * Configuración del sistema
	 * @var array
	 */
	private $local_config;
	/**
	 * Formato de la etiqueta
	 * @var string
	 */
	private $debug = FALSE;
	/**
	 * Conexión a BBDD Logic Class
	 * @var resource
	 */
	private $lc;
	/**
	 * Asientos generados
	 * @var array
	 */
	private $asientos = array();
	/**
	 * Movimiento de facturas generadas
	 * @var array
	 */
	private $facturas = array();
	/**
	 * Movimientos de IVA generados
	 * @var array
	 */
	private $ivas = array();
	/**
	 * Movimientos de cartera
	 * @var array
	 */
	private $cartera = array();
	/**
	 * Disparos de cartera
	 * @var array
	 */
	private $disparos = array();
	/**
	 * Movimientos
	 * @var array
	 */
	private $movimientos = array();

	/**
	 * Caché de cuentas de cliente
	 * @var array
	 */
	private $cuentas = array();
	/**
	 * Iniciador del traspaso
	 * @var string
	 */
	private $iniciador;
	/**
	 * Número de asiento
	 * @var integer
	 */
	private $num_asientos = 0;
	/**
	 * Número de apunte dentro del asiento
	 * @var integer
	 */
	private $num_apuntes = 0;
	/**
	 * Errores de traspasos
	 * @var array
	 */
	private $errores = array();
	/**
	 * Advertencias sin error
	 * @var array
	 */
	private $warnings = array();
	/**
	 * Log
	 * @var array
	 */
	private $log = array();
	/**
	 * Factruas a contabilizar
	 * @var array
	 */
	private $contabilizar = array();
	/**
	 * Fichero ACCESS generado
	 * @var string
	 */
	private $access;
	/**
	 * Modo test, no contabiliza
	 * @var boolean
	 */
	private $test = TRUE;
	/**
	 * Id del traspaso generado
	 * @var int
	 */
	private $id;

	/**
	 * Constructor
	 * @return  BP2LC
	 */
	function __construct()
	{
		parent::__construct('sys.bp2lc', 'sys/m_bp2lc', TRUE, null, 'Traspasos a contabilidad', 'sys/submenubp2lc.js');

		$this->local_config['lc_username'] = $this->config->item('bp2lc.lc_username');
		$this->local_config['lc_password'] = $this->config->item('bp2lc.lc_password');
		$this->local_config['lc_server'] = $this->config->item('bp2lc.lc_server');
		$this->local_config['lc_database'] = $this->config->item('bp2lc.lc_database');
		$this->local_config['debug'] = $this->config->item('bp2lc.debug');
		$this->local_config['mdb'] = $this->config->item('bp2lc.mdb');
		$this->local_config['force'] = $this->config->item('bp2lc.force.mdb');

		$this->CLIENTES_TIENDA = $this->config->item('bp.contabilidad.cc.cliente');
		$this->VENTAS = $this->config->item('bp.contabilidad.cc.ventas');
		$this->FACTURA_EMITIDA = $this->config->item('bp2lc.FACTURA_EMITIDA');
		$this->TIPO_EFECTO_EXCLUIDO = $this->config->item('bp2lc.TIPO_EFECTO_EXCLUIDO');
		$this->PREVISION_COBRO = $this->config->item('bp2lc.PREVISION_COBRO');
		$this->DIARIO_DEFECTO = $this->config->item('bp2lc.DIARIO_DEFECTO');
		$this->EFECTO_REEMBOLSO = $this->config->item('bp2lc.EFECTO_REEMBOLSO');
		$this->TAM_MODOSPAGO_DESC = $this->config->item('bp2lc.TAM_MODOSPAGO_DESC');
	}

	/**
	 * Añade un asiento al sistema
	 * @param array $asiento     Asiento
	 * @param string $dia         Día (dd/mm/yyyy)
	 * @param string $descripcion Descripción del asiento
	 */
	private function add_asiento($asiento, $dia, $descripcion)
	{
		if (isset($asiento['apuntes']) && count($asiento['apuntes']) > 0)
		{
			$ct = 1;
			foreach ($asiento['apuntes'] as $key => $value) 
			{
				# $asiento, $cuenta, $apunte, $debehaber, $descripcion, $prevision, $importe, $diario, $fecha, $documento
				$this->movimiento($asiento, $value['cc'], $ct, $value['dh'], $value['desc'], $this->PREVISION_COBRO,
					$value['valor'], $this->DIARIO_DEFECTO, $dia, $value['doc']);

				++$ct;
			}
			if ($this->debug)
			{
				$this->console->separator(100);
				$this->console->line('');
			}
			$asiento['dia'] = $dia;
			$asiento['desc'] = $descripcion;
			$this->asientos[] = $asiento;
		}
	}

	/**
	 * Crea un nuevo asiento
	 * @return array 'num' => Número de asiento, 'apuntes' => Apuntes del asiento
	 */
	private function asiento()
	{
		return array(
			'num'		=> ++$this->num_asientos,
			'apuntes'	=> array()
		);
	}

	/**
	 * Añade un apunte a un asiento
	 * @param  array  $asiento     Asiento
	 * @param  bool  $debe        TRUE: DEBE, FALSE: HABER
	 * @param  string  $cc          Cuenta contable
	 * @param  float  $valor       Valor
	 * @param  string  $descripcion Descripción
	 * @param  integer $documento   Id del documento asociado
	 * @return array  apunte genernado
	 */
	private function apunte(&$asiento, $debe, $cc, $valor, $descripcion, $documento = -1)
	{
		++$this->num_apuntes;
		$data = array(
			'dh' 	=> $debe?'D':'H',
			'cc'	=> $cc,
			'valor' => $valor,
			'desc'	=> trim($descripcion),
			'doc'	=> $documento
			);

		$asiento['apuntes'][] = $data;
		return $data;
	}

	/**
	 * Conecta a la base de datos Logic Class
	 * @return bool
	 */
	private function connect_lc()
	{
		# Conexión a Logic MSSQL
		$username = $this->local_config['lc_username'];
		$password = $this->local_config['lc_password'];
		$server = $this->local_config['lc_server'];
		$database = $this->local_config['lc_database'];

		$this->add_log(sprintf($this->lang->line('bp2lc-conectando-lc'), $server . '.' . $database . '.'. $username));

		$link = mssql_connect($server, $username, $password);
		if (!$link)
		{
			$this->terminar($this->lang->line('bp2lc-error-lc'));
		}
		mssql_select_db($database, $link);

		$this->lc = $link;

		return TRUE;
	}

	/**
	 * Finalza con errores
	 * @param  string $error Mensaje de error
	 * @return int -1
	 */
	private function terminar($error)
	{
		$this->add_log($this->lang->line('ERROR') . ': '. $error);
		$this->crear_fichero_log(date('Ymdih'));
		$this->out->error($error);
	}
	
	/**
	 * Comprueba la existencia de una cuenta de cliente ebn Logic Class y devuelve los datos si existe
	 * @param  int $cuenta Cuenta a buscar
	 * @return mixed, FALSE: No se ha encontrado, array con los datos si se ha encontrado
	 */
	private function check_cuenta($cuenta)
	{
		if (isset($this->cuentas[$cuenta]))
			return $this->cuentas[$cuenta];
		$sql = "SELECT * FROM ClientesConta WHERE CodigoCuenta = \"{$cuenta}\"";
		$query = mssql_query($sql, $this->lc);    
    	$row = mssql_fetch_array($query);
    	$this->cuentas[$cuenta] = $row;
    	return $row;
    } 

    /**
     * Lee los tipos de efecto de Logic Class
     * @return array
     */
    private function tipos_efecto()
    {
    	$efectos = array();
		$sql = "SELECT * FROM TipoEfectos_";
		$query = mssql_query($sql, $this->lc);
    	while ($row = mssql_fetch_array($query))
    	{
    		$efectos[$row['CodigoTipoEfecto']] = $row;
    	}
    	return $efectos;
    }
    
    /**
     * Generación del array base para todos los movimientos
     * @param  array $asiento Asiento
     * @return array
     */
    private function movimiento_base($asiento)
    {
    	return array(
		    'StatusTraspasadoConta'		=> '0',    					#1
		    'Proceso'                  	=> $this->iniciador,		#2
		    'Aplicacion'               	=> '"CON"',     			#3
		    'CodigoAsesor'             	=> '"000"',                	#4
		    'CodigoEmpresa'            	=> '1',                    	#5 
		    'Asiento'                  	=> $asiento['num'],        	#6
		    );  
    }

    /**
     * Movimientos de Factura
     * @param  [type] $asiento [description]
     * @param  [type] $serie   [description]
     * @param  [type] $factura [description]
     * @param  [type] $cuenta  [description]
     * @param  [type] $nombre  [description]
     * @param  [type] $nif     [description]
     * @param  [type] $tipo    [description]
     * @param  [type] $fecha   [description]
     * @param  [type] $importe [description]
     * @return [type]          [description]
     */
	private function movimiento_factura($asiento, $serie, $factura, $cuenta, $nombre, $nif, $tipo, $fecha, $importe)
	{
		$data = array_merge($this->movimiento_base($asiento),
			array(
			    'OrdenMov'                 	=> '1',                    	#7
			    'Serie'                    	=> (isset($serie)?"\"{$serie}\"":'NULL'),          #8
			    'FacturaRegistro'          	=> trim($factura),               	#9
			    'FechaFactura'           	=> "\"{$fecha}\"",          #10
			    'ImporteFactura'          	=> $importe,               	#11
			    'RegistroCartera'         	=> trim($factura),               	#12
			    'TipoIva'                  	=> "\"{$tipo}\"",           #13
			    'CodigoCuenta'            	=> "\"{$cuenta}\"",         #14
			    'Nif'                      	=> (isset($nif)?"\"{$nif}\"":'NULL'),            #15
			    'Nombre'                  	=> utf8_decode(isset($nombre)?"\"{$nombre}\"":'NULL'),      #16
			    'CodigoRetencion'          	=> '0',                    	#17
			    '[%Retencion]'             	=> '0',                    	#18
			    'Retencion'                	=> '0',                    	#19
			    'BaseRetencion'            	=> '0',                    	#20
			    'IntraComunitaria'         	=> '0',                    	#21
			    'Fecha347'                 	=> "\"{$fecha}\"",          #22
			    'EnEuros_'                  => '2'                     	#23
			    )
			);
		$this->facturas[] = $data;
		return $data;
	}

	/**
	 * Movimientos de IVA
	 * @param  [type]  $asiento      [description]
	 * @param  [type]  $ordeniva     [description]
	 * @param  [type]  $iva          [description]
	 * @param  [type]  $baseiva      [description]
	 * @param  [type]  $valor        [description]
	 * @param  [type]  $precargo     [description]
	 * @param  [type]  $recargo      [description]
	 * @param  integer $deducible    [description]
	 * @param  integer $exclusion347 [description]
	 * @param  integer $mediacion    [description]
	 * @return [type]                [description]
	 */
	private function movimiento_iva($asiento, $ordeniva, $iva, $baseiva, $valor, $precargo, $recargo, $deducible = -1, $exclusion347 = -1, $mediacion = 0)
	{
		$data = array_merge($this->movimiento_base($asiento),
			array(
			    'OrdenMov'                 	=> '1',                    	#7
			    'OrdenIVa'                  => $ordeniva,               #8
			    'CodigoIva'          		=> $iva,              		#9
			    'BaseIva'           		=> $baseiva,          		#10
			    '[%BaseCorrectora]'         => 0,               		#11
			    '[%Iva]'         			=> $iva,              		#12
			    'CuotaIva'                  => $valor,           		#13
			    '[%RecargoEquivalencia]'    => ($recargo!=0)?$precargo:0,	#14
			    'RecargoEquivalencia'       => $recargo,            	#15
			    'CodigoTransaccion'         => 1,         				#16
			    'Deducible'          		=> $deducible,              #17
			    'Exclusion347'             	=> $exclusion347,           #18
			    'Mediacion'                	=> $mediacion,              #19
			    'Prorrateo'            		=> 0,                    	#20
		    )
		);
		$this->ivas[] = $data;
		return $data;
	}

	/*private function cartera_efecto($asiento, $prevision, $serie, $factura, $cuenta, $fecha, $importe, $debehaber, $tipoefecto, $claseefecto, $diario, $vencimiento)
	{
		$comentario = sprintf(COMENTARIO_EFECTO, $factura, $serie);
		$data = array_merge($this->movimiento_base($asiento),
			array(
			    'OrdenMov'                 	=> '1',                    	#7
			    'OrdenCartera'              => 1,                 		#8
			    'Prevision'          		=> "\"{$prevision}\"",      #9
			    'SerieFactura'              => $serie,                 	#10
			    'Factura'          			=> $factura,               	#11
			    'Documento'          		=> $factura,               	#12
			    'NumeroOrdenEfecto'         => 1,         				#13
			    'CargoAbono'         		=> "\"{$debehaber}\"",      #14
			    'CodigoCuenta'            	=> "\"{$cuenta}\"",         #15
			    'CodigoClienteProveedor'    => "\"{$cuenta}\"",         #16
			    'CodigoDomicilioRecibido'   => 0,         				#17
			    'CodigoCanal'   			=> 0,         				#18
			    'CodigoDiario'   			=> $diario,         		#19
			    'Comentario'           		=> "\"{$comentario}\"",     #20
			    'CodigoTipoEfecto'   		=> $tipoefecto,         	#21
			    'TipoEfecto'				=> $tipoefecto,				#22
			    'ClaseEfecto'				=> $claseefecto,			#23
			    'ImporteEfecto'				=> $importe,				#24
			    'FechaEmision'           	=> "\"{$fecha}\"",          #25
			    'FechaFactura'           	=> "\"{$fecha}\"",          #26
			    'FechaVencimiento'         	=> "\"". (isset($vencimiento)?$vencimiento:$fecha) ."\"",          #27
				'NumeroRemesa'				=> 0,						#28
			    'ImportePendiente'			=> $importe,				#29
			    'ImporteGastos'				=> 0,						#30
			    'StatusContabilizado'		=> 0,						#31
			    'StatusRemesado'			=> 0,						#32
			    'SatusRecibido'				=> 0,						#33
			    'StatusRiesgo'				=> 0  						#34
		    )
		);  
		$this->cartera[] = $data;
		return $data;
	}*/

	/**
	 * Disparos de cartera
	 * @param  [type] $asiento         [description]
	 * @param  [type] $cuenta          [description]
	 * @param  [type] $numeroplazos    [description]
	 * @param  [type] $diasprimerplazo [description]
	 * @param  [type] $diasentreplazos [description]
	 * @param  [type] $diasfijos1      [description]
	 * @param  [type] $diasfijos2      [description]
	 * @param  [type] $diasfijos3      [description]
	 * @param  [type] $inicionopago    [description]
	 * @param  [type] $finnopago       [description]
	 * @param  [type] $diasretroceso   [description]
	 * @param  [type] $tipoefecto      [description]
	 * @return [type]                  [description]
	 */
	private function disparo_cartera($asiento, $cuenta, $numeroplazos, $diasprimerplazo, $diasentreplazos, $diasfijos1, $diasfijos2, $diasfijos3, $inicionopago, $finnopago, $diasretroceso, $tipoefecto)
	{
		$data = array_merge($this->movimiento_base($asiento),
			array(
			    'OrdenMov'                 	=> '1',                    	#7
			    'NumeroPlazos'              => $numeroplazos,           #8
			    'DiasPrimerPlazo'          	=> $diasprimerplazo,      	#9
			    'DiasEntrePlazos'           => $diasentreplazos,        #10
			    'DiasFijos1'          		=> $diasfijos1,             #11
			    'DiasFijos2'          		=> $diasfijos2,             #12
			    'DiasFijos3'          		=> $diasfijos3,             #13
			    'InicioNoPago'          	=> $inicionopago,           #14
			    'FinNoPago'         		=> $finnopago,         		#15
			    'ControlarFestivos'         => -1,      				#16
			    'DiasRetroceso'            	=> $diasretroceso,         	#17
			    'MesesComerciales'    		=> 0,         				#18
			    'CodigoTipoEfecto'  	 	=> $tipoefecto,         	#19
			    'CodigoClienteProveedor'   	=> $cuenta,         		#20
		    )
		);

		$this->disparos[] = $data;
		return $data;
	}

	/**
	 * Movimientos
	 * @param  [type] $asiento     [description]
	 * @param  [type] $cuenta      [description]
	 * @param  [type] $apunte      [description]
	 * @param  [type] $debehaber   [description]
	 * @param  [type] $descripcion [description]
	 * @param  [type] $prevision   [description]
	 * @param  [type] $importe     [description]
	 * @param  [type] $diario      [description]
	 * @param  [type] $fecha       [description]
	 * @param  [type] $documento   [description]
	 * @return [type]              [description]
	 */
	private function movimiento($asiento, $cuenta, $apunte, $debehaber, $descripcion, $prevision, $importe, $diario, $fecha, $documento)
	{
		$descripcion = str_replace('\'', ' ', $descripcion);
		$data = array_merge($this->movimiento_base($asiento),
			array(
			    'Orden'                 	=> $apunte,                 #7
			    'Fecha'           			=> "\"{$fecha}\"",          #8
			    'StatusRecalculoAsiento'	=> -1,						#9
			    'Periodo'					=> -1,						#10
			    'CargoAbono'         		=> "\"{$debehaber}\"",      #11
			    'CodigoCuenta'   			=> $cuenta,         		#12
			    'Documento'              	=> "\"{$documento}\"",      #13
			    'Comentario'				=> utf8_decode("\"{$descripcion}\""),	#14
			    'Importe'					=> $importe,				#15
			    'CodigoDiario'           	=> $diario,        			#16
			    'CodigoCanal'          		=> 0,             			#17
			    'Previsiones'          		=> "\"{$prevision}\"",		#18
			    'StatusConciliacion'        => 0,             			#19
			    'StatusSaldo'          		=> 0,           			#20
			    'StatusTraspaso'         	=> 0,         				#21
			    'CodigoUsuario'         	=> 0,      					#22
			    'FechaGrabacion'           	=> "\"{$fecha}\"",          #23
			    'TipoEntrada'				=> '"99"',					#24
			    'Impagado'            		=> 0,         				#25
			    'ImporteCambio'            	=> 0,         				#26
			    'ImporteDivisa'            	=> 0,         				#27
			    'FactorCambio'            	=> 0,         				#28
			    'CodigoConciliacion'        => 0,         				#29
			    'EnEuros_'					=> -1,						#30
			    'StatusAnalitica'			=> 0,						#31
			    'TipoMovimiento'			=> 0,						#32
		    )
		);

		$this->movimientos[] = $data;
		return $data;
	}

	/**
	 * Crea un iniciador de traspaso
	 * @return [type] [description]
	 */
	private function iniciador()
	{
		$this->iniciador = '1' . date('mdGi');
		return $this->iniciador;
	}

	/**
	 * Genera el nombre del archivo para el LOG
	 * @param  string $file Nombre de fichero base
	 * @return string
	 */
	private function file_log_name($file)
	{
		return DIR_BP2LC_PATH . $file . '_LOG.html';
	}

	/**
	 * Genera la URL del archivo para el LOG
	 * @param  string $file Nombre de fichero base
	 * @return string
	 */
	private function url_log_name($file)
	{
		return URL_BP2LC_PATH . $file . '_LOG.html';
	}

	/**
	 * Crea el fichero de LOG
	 * Genera el nombre del archivo para el LOG
	 * @param  string $file Nombre de fichero base
	 * @return bool
	 */
	private function crear_fichero_log($file, $success = TRUE)
	{
		$file = $this->file_log_name($file);

		$this->add_log(sprintf($this->lang->line('bp2lc-log-fichero'), $file));

		# Crea el archivo de LOG
		$data = array(
			'errores' 	=> $this->errores,
			'warnings'	=> $this->warnings,
			'asientos'	=> $this->asientos,
			'log'		=> $this->log,
			'iniciador'	=> $this->iniciador,
			'num_asientos'	=> $this->num_asientos,
			'num_apuntes'	=> $this->num_apuntes,
			'access'	=> $this->access,
			'test'		=> $this->test,
			'id'		=> $this->id,
			);
		$info = $this->load->view('sys/bp2lc', $data, TRUE);
		file_put_contents($file, $info);
		return TRUE;
	}

	/**
	 * Genera el nombre del archivo para el MDB
	 * @param  string $file Nombre de fichero base
	 * @return string
	 */
	private function file_mdb_name($file)
	{
		return DIR_BP2LC_PATH . $file . '.mdb';
	}

	/**
	 * Genera la URL del archivo para el MDB
	 * @param  string $file Nombre de fichero base
	 * @return string
	 */
	private function url_mdb_name($file)
	{
		return URL_BP2LC_PATH . $file . '.mdb';
	}

	/**
	 * Crea el fichero ACCESS. Utiliza un servidor WINDOWS porque con LINUX no ha sido posible crear un ACCESS
	 * @param  string $file Nombre del fochero
	 * @return bool
	 */
	private function crear_fichero_mdb($file)
	{
		# Crea el archivo ACCESS
		$this->add_log($this->lang->line('bp2lc-creando-ACCESS'));
		$data = array(
			'MovimientosFacturas' 	=> $this->facturas,
			'MovimientosIva'		=> $this->ivas,
			'DisparoCartera'		=> $this->disparos,
			'Movimientos'			=> $this->movimientos
			);

		$data['Iniciador'][] = array(
		    'Proceso'		=> $this->iniciador,				#1
		    'Aplicacion'    => '"CON"',     					#2
			'Comentario' 	=> '"Traspaso de Bibliopola 2.0"',	#3
			);

		$data = base64_encode(gzcompress(serialize($data), 9));
		$res = $this->get_url($this->local_config['mdb'], array('data' => $data, 'file' => $file . '.mdb'));		
		$res = json_decode($res, TRUE);
		if (isset($res['error']))
		{
			$this->terminar('Error al crear ACCESS: ' . $error);
		}
		if (isset($res['file']))
		{
			$temp = $this->file_mdb_name($file);

			file_put_contents($temp, file_get_contents($res['file']));
			$this->add_log("Fichero ACCESS creado <strong>{$temp}</strong>");
			$this->access = $file;
			$res = $this->get_url($this->local_config['mdb'], array('del' => 1, 'file' => $file . '.mdb'));
		}
		else
		{
			$this->terminar($this->lang->line('access-error'));
		}
		return TRUE;
	}

	/**
	 * Contabiliza la facturas
	 */
	private function contabilizar_facturas()
	{
		if (!$this->test)
		{
			$this->add_log($this->lang->line('Contabilizando facturas'));
			$conta = implode(';', $this->contabilizar);
			$this->load->model('ventas/m_factura');
			if (!$this->m_factura->contabilizar($conta))
			{
				$this->out->error($this->m_factura->error_message());
			}
			$this->add_log(sprintf($this->lang->line('bp2lc-contabilizado-ok'), count($this->contabilizar)));
		}		
	}

	/**
	 * Descontabiliza la facturas
	 */
	private function descontabilizar_facturas()
	{
		if (!$this->test)
		{
			$this->add_log($this->lang->line('Descontabilizando facturas'));
			$conta = implode(';', $this->contabilizar);
			$this->load->model('ventas/m_factura');
			if (!$this->m_factura->descontabilizar($conta))
			{
				$this->out->error($this->m_factura->error_message());
			}
			$this->add_log(sprintf($this->lang->line('bp2lc-descontabilizado-ok'), count($this->contabilizar)));
		}		
	}

	/**
	 * Crea el informe final, el archivo ACCESS y marca las facturas como contabilizadas
	 * @return int ID del traspaso realizado
	 */
	private function final_proceso()
	{
		$this->add_log('Proceso finalizado');

		# Crea el archivo ACCESS
		$file = date('Ymdih');
		if ($this->test) $file .= '_test';
		#$this->crear_fichero_mdb($file);

		# Contabiliza
		$this->contabilizar_facturas();

		$data = array(
			'MovimientosFacturas' 	=> $this->facturas,
			'MovimientosIva'		=> $this->ivas,
			'DisparoCartera'		=> $this->disparos,
			'Movimientos'			=> $this->movimientos,
			'errores' 				=> $this->errores,
			'warnings'				=> $this->warnings,
			'asientos'				=> $this->asientos,
			'log'					=> $this->log,
			'iniciador'				=> $this->iniciador,
			'num_asientos'			=> $this->num_asientos,
			'num_apuntes'			=> $this->num_apuntes,
			'contabilizar'			=> $this->contabilizar,
			'test'					=> $this->test,
			);

		$data = base64_encode(gzcompress(serialize($data),9));
		$ins = array(
			'bSuccess'		=> TRUE,
			'cFichero'		=> $file,
			'tDatos'		=> $data,
			'cDescripcion'	=> (($this->test)?($this->lang->line('MODO PRUEBA').': '):'') . sprintf($this->lang->line('bp2lc-descripcion-traspaso'), format_datetime(time()))
			);
		$id = $this->reg->insert($ins);
		$this->id = $id;
		#Crea el fichero de LOG
		#$this->crear_fichero_log($file);

		$this->add_log(sprintf($this->lang->line('bp2lc-id-registro'), $id));
		return $id;
	}

	/**
	 * Añade un error
	 * @param string $error Mensaje de error
	 * @param array $ft    Factura
	 * @param string $cmd Comando para llamar y que se arregle el problema
	 */
	private function add_error($error, $ft, $cmd = null)
	{
		$ft['error'] = $error;
		$ft['cmd'] = $cmd;
		$this->errores[] = $ft;
	}

	/**
	 * Añade un warning
	 * @param string $error Mensaje de warning
	 * @param array $ft    Factura
	 */
	private function add_warning($error, $ft)
	{
		$ft['warning'] = $error;
		$this->warnings[] = $ft;
	}

	/**
	 * Añade un log
	 * @param string $text Mensaje de log
	 * @param array $ft    Factura
	 */
	private function add_log($text)
	{
		$this->log[] = '[' . format_datetime() . '] ' . $text;
	}

	/**
	 * Marca una factura como que debe ser contabilizada
	 * @param array $ft Factura
	 */
	private function add_contabilizar($ft)
	{
		$this->contabilizar[] = $ft['nIdFactura'];
	}

	/**
	 * Función interna para construir la llamada al Bibliopola
	 * @param string $cmd Nombre del procedimiento
	 * @param array $post Parámetros pasados al procedimiento
	 * @return string, JSON del resultado de la llamada
	 */
	function get_url($url, $post = null)
	{
		$curly = curl_init();
		#echo $url . "\n";
		if ($this->debug)
			$this->add_log('URL: ' . $url);
		if ($this->debug)
			$this->add_log('POST: ' . print_r($post, TRUE));

		curl_setopt($curly, CURLOPT_URL, $url);
		curl_setopt($curly, CURLOPT_HEADER, 0);
		curl_setopt($curly, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curly, CURLOPT_TIMEOUT, 0);

		// post?
		if (!empty($post))
		{
			$post_string = '';
			foreach ($post as $k => $v)
			{
				$post_string .= $k . '=' . urlencode($v) . '&';
			}
			curl_setopt($curly, CURLOPT_POST, 1);
			curl_setopt($curly, CURLOPT_POSTFIELDS, $post_string);
		}

		$res = curl_exec($curly);
		$info = curl_getinfo($curly);
		$code = (string)$info['http_code'];
		if ($this->debug)
			$this->add_log('RESPONSE INFO: ' . print_r($info, TRUE));
		if ($this->debug)
			$this->add_log('RESPONSE: ' . $res);
		if ($code[0] == 4 || $code[0] == 5)
		{
			return FALSE;
		}

		return $res;
	}

	/**
	 * Muestra un diálogo con las opciones para generar el traspaso
	 * @return FORM
	 */
	function generar()
	{
		$this->userauth->roleCheck(($this->auth . '.run'));

		$this->_show_js('run', 'sys/bp2lc.js');
	}

	/**
	 * Calcula los totales de una factura
	 * @param  array $ft      Factura
	 * @param  array $cc_ivas Cuentas de los IVAS
	 * @return array
	 */
	function totales($ft, &$cc_ivas)
	{
		$ivas = array();
		$total = $total2 = $base = $base2 = 0;
		$break = FALSE;
		foreach ($ft['lineas'] as $linea)
		{
			if ($linea['fIVA'] > 0 && !isset($cc_ivas[$linea['fIVA']]))
			{
				$this->add_error(sprintf($this->lang->line('bp2lc-iva-desconocido'), $linea['fIVA']), $ft);
				$break = TRUE;
				break;
			}
			if (!isset($ivas[$linea['fIVA']]))
				$ivas[$linea['fIVA']] = array(
					'base' => 0, 
					'valor' => 0, 
					'recargo' => 0, 
					'total' => 0, 
					'total2' => 0
					);
			$ivas[$linea['fIVA']]['base'] += $linea['fBase'];
			$ivas[$linea['fIVA']]['valor'] += $linea['fIVAImporte'];
			$ivas[$linea['fIVA']]['recargo'] += $linea['fRecargoImporte'];
			$ivas[$linea['fIVA']]['total2'] += $linea['fBase'] + $linea['fIVAImporte'] + $linea['fRecargoImporte'];
			$ivas[$linea['fIVA']]['total'] += $linea['fTotal'];
			$total2 += $linea['fBase'] + $linea['fIVAImporte'] + $linea['fRecargoImporte'];
			$total += $linea['fTotal'];
			$base += $linea['fBase'];
		}
		$total = $total2 = $base = $base2 = 0;
		foreach($ivas as $k => $v)
		{
			$i = format_iva(format_quitar_iva($v['total'], $k), $k);
			$b = format_quitar_iva($v['total'], $k);
			$ivas[$k]['base'] = $b;
			$ivas[$k]['valor'] = $i;
			$ivas[$k]['total'] = $b + $i;
			$total += $i + $b;
			$base += $b;
		}
		$total2 = $total;
		$base2 = $base;
		return array(
			'ivas'		=> $ivas,
			'total'		=> $total,
			'total2'	=> $total2,
			'base'		=> $base,
			'base2'		=> $base2,
			'break'		=> $break
			);
	}

	/**
	 * Rutina principal
	 * @param  date $desde Fecha Desde de factura
	 * @param  date $hasta Fecha Hasta de factura
	 * @param  bool $test  TRUE: no contabiliza, genera un test
	 * @param  int 	$idf  Id de la factura a comprobar (DEBUG)
	 * @return DIALOG
	 */
	function run($desde = null, $hasta = null, $test = null, $idf = null)
	{
		$this->userauth->roleCheck(($this->auth . '.run'));

		$this->load->library('Configurator');

		$desde = isset($desde)?$desde:$this->input->get_post('desde');
		$hasta = isset($hasta)?$hasta:$this->input->get_post('hasta');
		$test = isset($test)?$test:$this->input->get_post('test');
		$desde = empty($desde)?null:to_date($desde);
		$hasta = empty($hasta)?time():to_date($hasta);
		$idf = isset($idf)?$idf:$this->input->get_post('idf');
		$test = (empty($test)?FALSE:format_tobool($test)) || $this->configurator->user('bp2lc.var_dump');

		#test($test); die();

		$this->test = $test;

		set_time_limit(0);

		# Lee la configuración de los traspasos
		$this->add_log($this->lang->line('Leyendo configuración'));

		$fecha_limite = $this->config->item('bp2lc.fechalimite');
		$fecha_limite = empty($fecha_limite)?null:to_date($fecha_limite);

		$diff_total = $this->config->item('bp2lc.diff');
		# Conexión a Logic Class
		$this->connect_lc();

		# Lee los modos de pago
		$this->load->model('ventas/m_modopago');
		$data = $this->m_modopago->get();
		$modospago = array();
		foreach ($data as $value) 
		{
			$modospago[$value['cDescripcionCorta']] = $value;
		}

		# Lee los IVAS
		$this->load->model('generico/m_iva');
		$data = $this->m_iva->get();
		$cc_ivas = array();
		foreach ($data as $value) 
		{
			$cc_ivas[$value['fValor']] = $value;
		}

		# Lee los modos de pago - caja
		$this->load->model('ventas/m_modopagocaja');
		$data = $this->m_modopagocaja->get();
		$modospagocaja = array();
		foreach ($data as $value) 
		{
			$modospagocaja[$value['cModoPagoCorto']][$value['cCajaCorto']] = $value['nIdCuenta'];
		}

		# Lee los efectos de Logic Class
		$efectos = $this->tipos_efecto();

		#
		# Lee las facturas
		# 
		$this->add_log($this->lang->line('Leyendo facturas sin traspasar'));
		$this->load->model('ventas/m_factura');
		$where = ($test)
			?('nIdEstado IN (' . FACTURA_STATUS_CERRADA. ',' . FACTURA_STATUS_CONTABILIZADA . ')')
			:('nIdEstado=' . FACTURA_STATUS_CERRADA);

		#Si hay fecha límite, la tiene en cuenta
		if (!empty($desde)) 
		{
			$fecha1 = format_mssql_date($desde);
			$where .= " AND dFecha >= {$fecha1}";
		}
		if (!empty($hasta)) 
		{
			$fecha1 = format_mssql_date($hasta);
			$where .= ' AND dFecha < ' . $this->db->dateadd('d', 1, $fecha1);
		}
		if (is_numeric($idf))
			$where = 'nIdFactura=' . $idf;
		$data_fact = $this->m_factura->get(null, null, null, null, $where);

		$acontar = count($data_fact);
		$this->add_log(sprintf($this->lang->line('bp2lc-facturas-contabilizadas'), $acontar));
		$this->add_log($this->lang->line('Agrupando facturas'));
		$facturas = array();
		$contadas = 0;

		# Agrupa las facturas por días y modos de pago. Si son múltiples los mete en otro array
		if (!empty($fecha_limite)) $f2 = format_date($fecha_limite);
		foreach ($data_fact as $ft) 
		{
			++$contadas;

			$ft = $this->m_factura->load($ft['nIdFactura'], TRUE);
			if ($this->debug)
				$this->add_log(sprintf('[DEBUG] %%g%s/%s%%n Leyendo factura (%s) %s con fecha %s creada el %s', 
					$contadas, $acontar,
					$res2['value_data']['nIdFactura'], 
					$res2['value_data']['cNumero'], 
					date('d/m/Y', $res2['value_data']['dFecha']),
					date('d/m/Y G:i:s', $res2['value_data']['dCreacion'])
					));

			# Comrprueba si está dentro de la fecha límite
			if (!empty($fecha_limite) && $ft['dFecha'] < $fecha_limite)
			{
				$f1 = format_date($ft['dFecha']);
				$this->add_error(sprintf($this->lang->line('bp2lc-fecha-factura-error'), $f1, $f2), $ft);
				continue;
			}

			# Calcula el total
			# agrupa por días
			$dia = date('d/m/Y', $ft['dFecha']);
			if (!isset($facturas[$dia]))
				$facturas[$dia] = array(
					'docs' 	=> array(),
					'dia' 	=> $ft['dFecha'],
					'modos'	=> array(),
					);
			# Cliente con cuenta?
			if (is_numeric($ft['cliente']['nIdCuenta']) && !$ft['cliente']['bCredito'])
				$this->add_warning($this->lang->line('bp2lc-clientecenta-nocredito'), $ft); 			
			$cuenta = (is_numeric($ft['cliente']['nIdCuenta']))?$ft['cliente']['nIdCuenta']:FALSE;

			# Cálculo del total
			$res = $this->totales($ft, $cc_ivas);
			$ivas = $res['ivas'];
			$total = $res['total'];
			$total2 = $res['total2']; 
			$base = $res['base'];
			$base2 = $res['base2'];;
			$break = $res['break'];

			#var_dump($ivas, $base, $total, $total2); die();
			if ($break) continue;
			$total = format_decimals($total);
			$ft['ivas'] = $ivas;
			$ft['total'] = $total;
			$ft['total2'] = $total2;
			$ft['base'] = $base;
			$ft['cuenta'] = $cuenta;
			if (is_numeric($cuenta))
			{
				#var_dump($ft['cliente'], $cuenta); die();
				$cliente = $this->check_cuenta($cuenta);
				if (!$cliente)
				{
					$this->add_error(sprintf($this->lang->line('bp2lc-cuentanoexiste'), $cuenta), $ft);
					continue;
				}
				$ft['cliente'] = $cliente;
				if ((count($ft['modospago']) > 0) && in_array($ft['modospago'][0]['nIdModoPago'], array(MP_ABONO)))
				{
					$this->add_error(sprintf($this->lang->line('bp2lc-abonono'), $cuenta), $ft);
					continue;
				}		
			}
			# Factura a 0 y no hay modos de pago OK
			if ((count($ft['modospago']) == 0) && ($total == 0))
			{
				$this->add_warning($this->lang->line('bp2lc-importe0'), $ft);
				$this->add_contabilizar($ft);
				continue;
			}
			# Factura no a 0 pero con modos de pago ERROR
			if (count($ft['modospago']) == 0)
			{
				$this->add_error(sprintf($this->lang->line('bp2lc-nohaymodospago'), format_price($total)), $ft);
				continue;
			}
			$total2 = 0;
			foreach($ft['modospago'] as $mp)
			{
				$total2 += $mp['fImporte'];
			}
			$total2 = format_decimals($total2);
			if ($total != $total2)
			{
				# si la diferencia es menor o igual a límite, ajusta el pago
				if (abs($total-$total2) <= $diff_total)
				{
					$diff = $this->m_factura->ajustepago($ft['nIdFactura']);
					$this->add_warning(sprintf($this->lang->line('bp2lc-cobrosdiferentes-warning'), format_price($total2), format_price($total), format_price($total2-$total)), $ft);
					$ft = $this->m_factura->load($ft['nIdFactura'], TRUE);
					$res = $this->totales($ft, $cc_ivas);
					$ivas = $res['ivas'];
					$total = $res['total'];
					$total2 = $res['total2']; 
					$base = $res['base'];
					$base2 = $res['base2'];;
					$break = $res['break'];
					$ft['ivas'] = $ivas;
					$ft['total'] = $total;
					$ft['total2'] = $total2;
					$ft['base'] = $base;
					$ft['cuenta'] = $cuenta;
				}
				else
				{
					$this->add_error(sprintf($this->lang->line('bp2lc-cobrosdiferentes'), format_price($total2), format_price($total)), $ft, 'ventas/factura/ajustepago');
					continue;
				}
			}

			if (count($ft['modospago']) > 1)
			{
				$facturas[$dia]['docs']['multi'][] = $ft;
			}
			else
			{
				if ($total==0)
					$this->add_warning($this->lang->line('bp2lc-importe0-0'), $ft);

				if ($ft['modospago'][0]['nIdModoPago'] == MP_REEMBOLSO)
				{
					$cuenta = isset($modospagocaja[$ft['modospago'][0]['cModoPagoCorto']][$ft['modospago'][0]['cCajaCorto']])
						?$modospagocaja[$ft['modospago'][0]['cModoPagoCorto']][$ft['modospago'][0]['cCajaCorto']]
						:$modospago[$ft['modospago'][0]['cModoPagoCorto']]['cCuenta'];
					$ft['cuenta'] = $cuenta;
				}

				if ($ft['modospago'][0]['nIdModoPago'] == MP_ACUENTA && !is_numeric($cuenta))
				{
					$this->add_error($this->lang->line('bp2lc-acuenta-cliente-sincuenta'), $ft);
				}
				else
				{
					if (!in_array($ft['modospago'][0]['nIdModoPago'], array(MP_ACUENTA, MP_REEMBOLSO)) 
						&& is_numeric($cuenta))
					{
						$this->add_warning(sprintf($this->lang->line('bp2lc-clientecenta-nocuenta'), $cuenta), $ft);
					}
					$facturas[$dia]['docs']['mp'][$ft['modospago'][0]['cCajaCorto']][$ft['modospago'][0]['cModoPagoCorto']][] = $ft;
				}
			}
		}

		# 
		# Genera los apuntes y movimientos
		# 
		# Tenemos en facturas un array de días. En cada día hay
		# ['docs']['mp'] 				=> Las facturas con un solo modo de pago separadas por modos de pago
		# ['docs']['multi']				=> Las facturas con más de un modo de pago
		$this->iniciador();
		$this->add_log($this->lang->line('Procesando grupos de facturas'));
		# Procesa por días
		foreach ($facturas as $dia => $datos)
		{
			# Modos de pago únicos
			if (isset($datos['docs']['mp']) && count($datos['docs']['mp']) > 0)
			{
				# Por cajas
				foreach ($datos['docs']['mp'] as $caja => $mps) 
				{
					foreach ($mps as $key => $value) 
					{
						$cobros = array();
						if (in_array($modospago[$key]['nIdModoPago'], array(
							MP_AMEXDINERS, 
							MP_CHEQUE, 
							MP_DATAFONOECOMMERCE, 
							MP_TARJETA, 
							MP_METÁLICO, 
							MP_TRANSFERENCIA, 
							MP_REEMBOLSO,
							MP_ACUENTA)))
						{
							# Por modos de pago
							$cobros = $this->asiento();
							# Acumular
							$ivas = array();
							$total = $base = $cobro = 0;
							foreach ($value as $ft)
							{
								$cuenta = $modospago[$key]['cCuenta'];
								# IVAS y totales
								$subtotal = 0;
								$subbase = 0;
								$subivas = array();
								foreach ($ft['ivas'] as $k => $v)
								{
									#var_dump($v); #die();
									if (!isset($subivas[$k]))
										$subivas[$k] = array('valor' => 0, 'recargo' => 0, 'base' => 0);
									$subbase += $v['base'];
									$subivas[$k]['base'] += $v['base'];
									$subivas[$k]['valor'] += $v['valor'];
									$subivas[$k]['recargo'] += $v['recargo'];

									$subtotal += $v['base'] + $v['valor'] + $v['recargo'];
								}

								if ($ft['cuenta'])
								{
									# tiene cuenta
									$asiento = $this->asiento();
									$this->apunte($asiento,
										TRUE, 
										$ft['cuenta'],
										$subtotal,
										$this->lang->line('N/F') . ' ' . $ft['cNumero'],
										$ft['nIdFactura']
										);
									$this->apunte($asiento,
										FALSE, 								
										$this->VENTAS,
										$subbase,
										$this->lang->line('N/F') . ' ' . $ft['cNumero'],
										$ft['nIdFactura']
										);
									# Movimiento de factura
									# $serie, $factura, $cuenta, $nombre, $nif, $tipo, $fecha, $importe
									$this->movimiento_factura($asiento, $ft['nSerieNumero'], $ft['nNumero'], $ft['cuenta'], 
										$ft['cCliente'],
										isset($ft['cliente']['CifEuropeo'])?$ft['cliente']['CifEuropeo']:$ft['cliente']['cNIF'], 
										$this->FACTURA_EMITIDA, $dia, $subtotal);
									$niva = 0;
									foreach ($subivas as $k => $v)
									{
										if ($v['valor'] != 0 && $k != 0)
											$this->apunte($asiento,
												FALSE,
												$cc_ivas[$k]['nIdCuenta'],
												$v['valor'],
												$this->lang->line('N/F') . ' ' . $ft['cNumero'],
												$ft['nIdFactura']
												);
										if ($v['recargo'] != 0 && $k != 0)
											$this->apunte($asiento,
												FALSE,
												$cc_ivas[$k]['nIdCuentaREC'],
												$v['recargo'],
												$this->lang->line('N/F') . ' ' . $ft['cNumero'],
												$ft['nIdFactura']
												);
										# Movimiento de IVA
										# $asiento, $ordeniva, $codigoiva, $baseiva, $precargo, $recargo, $deducible = -1, $exclusion347 = -1, $mediacion = -1
										if ($v['base'] != 0)
											$this->movimiento_iva($asiento, ++$niva, $k, $v['base'], $v['valor'], 
												(isset($cc_ivas[$k]['fRecargo'])?$cc_ivas[$k]['fRecargo']:0),
												$v['recargo'], 
												isset($ft['cliente']['Deducible'])?$ft['cliente']['Deducible']:-1,
												isset($ft['cliente']['Exclusion347'])?$ft['cliente']['Exclusion347']:-1,
												isset($ft['cliente']['Mediacion'])?$ft['cliente']['Mediacion']:-1
												);
									}
									# VENTA
									if ($subtotal != 0)
										$this->add_asiento($asiento, $dia, $this->lang->line('VENTA') . ' ' . $caja .' ' .$key . ' ' . $this->lang->line('N/F') . ' ' . $ft['cNumero']);
									if (!in_array($modospago[$key]['nIdModoPago'], array(MP_ACUENTA, MP_REEMBOLSO, MP_ABONO)))
									{
										# COBRO
										$asiento = $this->asiento();
										$this->apunte($asiento,
											TRUE, 
											$cuenta,
											$subtotal,
											$caja . ' ' .$key . ' ' . $this->lang->line('N/F') . ' ' . $ft['cNumero'],
											$ft['nIdFactura']
											);
										$this->apunte($asiento,
											FALSE,
											$ft['cuenta'],
											$subtotal,
											$caja . ' ' .$key . ' ' . $this->lang->line('N/F') . ' ' . $ft['cNumero'],
											$ft['nIdFactura']
											);
										$this->add_asiento($asiento, $dia, $this->lang->line('COBRO') . ' ' . $caja .' '. $key . ' ' . $this->lang->line('N/F') . ' ' . $ft['cNumero']);
									}							
								}
								else
								{
									# Sin cuenta, acumula la venta
									foreach ($subivas as $k => $v)
									{
										if (!isset($ivas[$k]))
											$ivas[$k] = array('valor' => 0, 'recargo' => 0, 'base' => 0);
										$ivas[$k]['valor'] += $v['valor'];
										$ivas[$k]['base'] += $v['base'];
										$ivas[$k]['recargo'] += $v['recargo'];
									}
									$total += $subtotal;
									$cobro += $ft['modospago'][0]['fImporte'];
									$base += $subbase;
								}
								# Cobro de contados si no es A CUENTA, NI METÁLICO, NI REEMBOLSO ni tiene cuenta
								if (!in_array($modospago[$key]['nIdModoPago'], array(MP_METÁLICO, MP_ACUENTA, MP_REEMBOLSO)) && !$ft['cuenta'])
								{
									$cc = (isset($modospagocaja[$key]) && isset($modospagocaja[$key][$caja]))?$modospagocaja[$key][$caja]:$cuenta;
									$this->apunte($cobros,
										TRUE, 
										$cc,
										$ft['modospago'][0]['fImporte'],
										$caja . ' ' .$key . ' ' . $this->lang->line('N/F') . ' ' . $ft['cNumero'],
										$ft['nIdFactura']
										);
									$this->apunte($cobros,
										FALSE,
										$this->CLIENTES_TIENDA,
										$ft['modospago'][0]['fImporte'],
										$caja . ' ' .$key . ' ' . $this->lang->line('N/F') . ' ' . $ft['cNumero'],
										$ft['nIdFactura']
										);
								}
								elseif ($modospago[$key]['nIdModoPago'] == MP_ACUENTA)
								{
									# A CUENTA
									$efecto = $efectos[$ft['cliente']['CodigoTipoEfecto']];
									# Si no tiene número de plazos muestra una incidencia pero genera el disparo
									if ($ft['cliente']['NumeroPlazos'] == 0)
									{
										$this->add_warning(sprintf($this->lang->line('bp2lc-efecto-noplazos'), $ft['cuenta']), $ft);
										$ft['cliente']['NumeroPlazos'] = 1;
									}
									if ($efecto['CodigoTipoEfecto'] == $this->TIPO_EFECTO_EXCLUIDO)
									{
										# Los recibos mensuales no generan disparos
										$this->add_warning(sprintf($this->lang->line('bp2lc-efecto-excluido'), $ft['cuenta']), $ft);
									}
									else
									{
										# El resto si
										# Disparo cartera
										# $asiento, $cuenta, $numeroplazos, $diasprimerplazo, $diasentreplazos, $diasfijos1, $diasfijos2, $diasfijos3, $inicionopago, $finnopago, $diasretroceso, $tipoefecto)
										$this->disparo_cartera($asiento, $ft['cuenta'], 
											$ft['cliente']['NumeroPlazos'], $ft['cliente']['DiasPrimerPlazo'], $ft['cliente']['DiasEntrePlazos'], 
											$ft['cliente']['DiasFijos1'], $ft['cliente']['DiasFijos2'], $ft['cliente']['DiasFijos3'],
											$ft['cliente']['InicioNoPago'], $ft['cliente']['FinNoPago'], $ft['cliente']['DiasRetroceso'],
											$ft['cliente']['CodigoTipoEfecto']
											);
									}
								}
								elseif ($modospago[$key]['nIdModoPago'] == MP_REEMBOLSO)
								{
									# REEMBOLSO
									# Disparo cartera
									# $asiento, $cuenta, $numeroplazos, $diasprimerplazo, $diasentreplazos, $diasfijos1, $diasfijos2, $diasfijos3, $inicionopago, $finnopago, $diasretroceso, $tipoefecto)
									$this->disparo_cartera($asiento, $ft['cuenta'], 1, 0, 0, 0, 0, 0, 0, 0, 0, $this->EFECTO_REEMBOLSO);
								}
								$this->add_contabilizar($ft);
							}

							# Ya están sumados todos los de un día
							# Añade el COBRO del acumulado
							if ($modospago[$key]['nIdModoPago'] == MP_METÁLICO && $cobro != 0)
							{
								# Si es metálico añade el cobro
								$cc = (isset($modospagocaja[$key]) && isset($modospagocaja[$key][$caja]))?$modospagocaja[$key][$caja]:$modospago[$key]['cCuenta'];
								$this->apunte($cobros,
									TRUE, 
									$cc,
									$cobro,
									$caja . ' ' . $key
									);
								$this->apunte($cobros,
									FALSE, 									
									$this->CLIENTES_TIENDA,
									$cobro,
									$caja . ' ' . $key
									);
							}
							# Crea el asiento de la venta acumulada del día
							if ($total != 0)
							{
								$asiento = $this->asiento();
								$this->apunte($asiento,
									TRUE, 
									$this->CLIENTES_TIENDA,
									$total,
									$key . ' ' . $caja
									);
								$this->apunte($asiento,
									FALSE, 								
									$this->VENTAS,
									$base,
									$key . ' ' . $caja
									);
								# Movimiento de factura
								# $serie, $factura, $cuenta, $nombre, $nif, $tipo, $fecha, $importe
								$this->movimiento_factura($asiento, null, null, $this->CLIENTES_TIENDA, 
									$this->lang->line('TEXTO_VENTAS_CONTADO'), null, $this->FACTURA_EMITIDA, $dia, $total);
								$niva = 0;
								foreach ($ivas as $k => $v)
								{
									if ($v['valor'] != 0 && $k != 0)								
										$this->apunte($asiento,
											FALSE,
											$cc_ivas[$k]['nIdCuenta'],
											$v['valor'],
											$key . ' ' . $caja
											);
									if ($v['recargo'] != 0 && $k != 0)
										$this->apunte($asiento,
											FALSE,
											$cc_ivas[$k]['nIdCuentaREC'],
											$v['recargo'],
											$key . ' ' . $caja
											);
									# $asiento, $ordeniva, $codigoiva, $baseiva, $precargo, $recargo, $deducible = -1, $exclusion347 = -1, $mediacion = -1
									$this->movimiento_iva($asiento, ++$niva, $k, $v['base'], $v['valor'], 
										(isset($cc_ivas[$k]['fRecargo'])?$cc_ivas[$k]['fRecargo']:0),
										$v['recargo'],
										isset($ft['cliente']['Deducible'])?$ft['cliente']['Deducible']:-1,
										isset($ft['cliente']['Exclusion347'])?$ft['cliente']['Exclusion347']:-1,
										isset($ft['cliente']['Mediacion'])?$ft['cliente']['Mediacion']:-1
										);
								}
								$this->add_asiento($asiento, $dia, $this->lang->line('VENTA') . ' ' . $key . ' ' . $caja);
							}
							# Añade los cobros al final
							$this->add_asiento($cobros, $dia, $this->lang->line('COBROS CONTADOS') . ' ' . $caja);
						}
						elseif ($modospago[$key]['nIdModoPago'] == MP_ABONO )
						{
							foreach ($value as $ft)
							{
								$cuenta = $modospago[$key]['cCuenta'];
								# IVAS y totales
								$total = 0;
								$base = 0;
								$ivas = array();
								foreach ($ft['ivas'] as $k => $v)
								{
									#var_dump($v); #die();
									if (!isset($ivas[$k]))
										$ivas[$k] = array('valor' => 0, 'recargo' => 0, 'base' => 0);
									$base += $v['base'];
									$ivas[$k]['base'] += $v['base'];
									$ivas[$k]['valor'] += $v['valor'];
									$ivas[$k]['recargo'] += $v['recargo'];

									$total += $v['base'] + $v['valor'] + $v['recargo'];
								}
								$asiento = $this->asiento();
								$this->apunte($asiento,
									TRUE, 
									$this->CLIENTES_TIENDA,
									$ft['total2'],
									$this->lang->line('N/F') . ' ' . $ft['cNumero'],
									$ft['nIdFactura']
									);
								$this->apunte($asiento,
									FALSE, 								
									$this->VENTAS,
									$ft['base'],
									$this->lang->line('N/F') . ' ' . $ft['cNumero'],
									$ft['nIdFactura']
									);									
								# Movimiento de factura
								# $serie, $factura, $cuenta, $nombre, $nif, $tipo, $fecha, $importe
								$this->movimiento_factura($asiento, $ft['nSerieNumero'], $ft['nNumero'], $this->CLIENTES_TIENDA, 
									$this->lang->line('TEXTO_VENTAS_CONTADO'), null, $this->FACTURA_EMITIDA, $dia, $ft['total']); 								
								$niva = 0;
								foreach ($ft['ivas'] as $k => $v)
								{
									if ($v['valor'] != 0 && $k != 0)
										$this->apunte($asiento,
											FALSE,
											$cc_ivas[$k]['nIdCuenta'],
											$v['valor'],
											$this->lang->line('N/F') . ' ' . $ft['cNumero'],
											$ft['nIdFactura']
											);
									if ($v['recargo'] != 0 && $k != 0)
										$this->apunte($asiento,
											FALSE,
											$cc_ivas[$k]['nIdCuentaREC'],
											$v['recargo'],
											$this->lang->line('N/F') . ' ' . $ft['cNumero'],
											$ft['nIdFactura']
											);
									# Movimiento de IVA
									# $asiento, $ordeniva, $codigoiva, $baseiva, $precargo, $recargo, $deducible = -1, $exclusion347 = -1, $mediacion = -1
									$this->movimiento_iva($asiento, ++$niva, $k, $v['base'], $v['valor'], 
										(isset($cc_ivas[$k]['fRecargo'])?$cc_ivas[$k]['fRecargo']:0),
										$v['recargo'],
										isset($ft['cliente']['Deducible'])?$ft['cliente']['Deducible']:-1,
										isset($ft['cliente']['Exclusion347'])?$ft['cliente']['Exclusion347']:-1,
										isset($ft['cliente']['Mediacion'])?$ft['cliente']['Mediacion']:-1
										);
								}
								$this->add_asiento($asiento, $dia, $this->lang->line('VENTA') . ' ' . $key . ' ' . $this->lang->line('N/F') . ' ' . $ft['cNumero']);
								$cc = (isset($modospagocaja[$key]) && isset($modospagocaja[$key][$caja]))?$modospagocaja[$key][$caja]:$modospago[$key]['cCuenta'];
								$asiento = $this->asiento();
								$this->apunte($asiento,
									TRUE, 
									$cc,
									$ft['total'],
									$caja . ' ' .$key . ' ' . $this->lang->line('N/F') . ' ' . $ft['cNumero'],
									$ft['nIdFactura']
									);
								$this->apunte($asiento,
									FALSE,
									$this->CLIENTES_TIENDA,
									$ft['total'],
									$caja . ' ' .$key . ' ' . $this->lang->line('N/F') . ' ' . $ft['cNumero'],
									$ft['nIdFactura']
									);
								$this->add_asiento($asiento, $dia, $this->lang->line('COBRO') . ' ' . $key . ' ' . $this->lang->line('N/F') . ' ' . $ft['cNumero']);
								$this->add_contabilizar($ft);
							}
						}
					}
				}
			}
			if (isset($datos['docs']['multi']) && count($datos['docs']['multi']) > 0)
			{
				foreach ($datos['docs']['multi'] as $ft) 
				{
					$modos = array(
						'act' 	=> 0,
						'ab' 	=> 0,
						'ef' 	=> 0,
						're' 	=> 0,
						'otro' 	=> 0,
						'ab-'	=> 0,
						);
					#var_dump($ft['modospago']); die();
					# Comprueba los modos de pago
					foreach ($ft['modospago'] as $mp)
					{
						if (in_array($mp['nIdModoPago'], array(
							MP_AMEXDINERS, 
							MP_CHEQUE, 
							MP_DATAFONOECOMMERCE, 
							MP_TARJETA, 
							MP_METÁLICO, 
							MP_TRANSFERENCIA)))
						{
							++$modos['ef'];
						}
						elseif ($mp['nIdModoPago'] == MP_ABONO && $mp['fImporte'] < 0)
						{
							++$modos['ab-'];
						}
						elseif ($mp['nIdModoPago'] == MP_ABONO && $mp['fImporte'] > 0)
						{
							++$modos['ab'];
						}
						elseif ($mp['nIdModoPago'] == MP_ACUENTA)
						{
							++$modos['act'];
						}
						elseif ($mp['nIdModoPago'] == MP_REEMBOLSO)
						{
							++$modos['re'];
						}
						else
						{
							++$modos['otro'];
						}
					}
					# Comprueba errores
					if ($modos['act'] > 0 && $modos['act'] != count($ft['modospago']))
					{
						$this->add_error($this->lang->line('Pago A CUENTA combinado con otros'), $ft);
						continue;
					}
					if ($modos['ab'] > 0 && (($modos['ab'] + $modos['ef']) != count($ft['modospago'])))
					{
						$this->add_error($this->lang->line('Pago con ABONO combinado con otros diferente a EFECTIVO'), $ft);
						continue;
					}
					if ($modos['re'] > 0 && $modos['re'] != count($ft['modospago']))
					{
						$this->add_error($this->lang->line('Pago REEMBOLSOS combinado con otros'), $ft);
						continue;
					}
					if ($modos['re'] > 0 && $ft['total'] < 0)
					{
						$this->add_error($this->lang->line('Pago REEMBOLSOS negativo combinado con otros'), $ft);
						continue;
					}
					if ($modos['ab-'] > 0 && $modos['ab-'] != count($ft['modospago']))
					{
						$this->add_error($this->lang->line('Pago ABONO NEGATIVO combinado con otros'), $ft);
						continue;
					}
					if ($modos['otro'] > 0)
					{
						$this->add_error($this->lang->line('Combinación de pagos no conocida'), $ft);
						continue;
					}
					# Solo quedan uso de ABONOS y EFECTIVOS combinados
					$cuenta = !empty($ft['cuenta'])?$ft['cuenta']:$this->CLIENTES_TIENDA;
					if ($ft['total2'] != 0)
					{
						$asiento = $this->asiento();
						$this->apunte($asiento,
							TRUE, 
							$cuenta,
							$ft['total2'],
							$this->lang->line('N/F') . ' ' . $ft['cNumero'],
							$ft['nIdFactura']
							);
						$this->apunte($asiento,
							FALSE, 								
							$this->VENTAS,
							$ft['base'],
							$this->lang->line('N/F') . ' ' . $ft['cNumero'],
							$ft['nIdFactura']
							);
						# Movimiento de factura
						# $serie, $factura, $cuenta, $nombre, $nif, $tipo, $fecha, $importe
						$this->movimiento_factura($asiento, $ft['nSerieNumero'], $ft['nNumero'], $ft['cuenta'], 
							$ft['cCliente'], 
							isset($ft['cliente']['CifEuropeo'])?$ft['cliente']['CifEuropeo']:$ft['cliente']['cNIF'], 
							$this->FACTURA_EMITIDA, $dia, $ft['total']);
						$niva = 0;
						foreach ($ft['ivas'] as $k => $v)
						{
							if ($v['valor'] != 0 && $k != 0)
								$this->apunte($asiento,
									FALSE,
									$cc_ivas[$k]['nIdCuenta'],
									$v['valor'],
									$this->lang->line('N/F') . ' ' . $ft['cNumero'],
									$ft['nIdFactura']
									);
							if ($v['recargo'] != 0 && $k != 0)
								$this->apunte($asiento,
									FALSE,
									$cc_ivas[$k]['nIdCuentaREC'],
									$v['recargo'],
									$this->lang->line('N/F') . ' ' . $ft['cNumero'],
									$ft['nIdFactura']
									);
							# Movimiento de IVA
							# $asiento, $ordeniva, $codigoiva, $baseiva, $precargo, $recargo, $deducible = -1, $exclusion347 = -1, $mediacion = -1
							$this->movimiento_iva($asiento, ++$niva, $k, $v['base'], $v['valor'], 
								(isset($cc_ivas[$k]['fRecargo'])?$cc_ivas[$k]['fRecargo']:0),
								$v['recargo'],
								isset($ft['cliente']['Deducible'])?$ft['cliente']['Deducible']:-1,
								isset($ft['cliente']['Exclusion347'])?$ft['cliente']['Exclusion347']:-1,
								isset($ft['cliente']['Mediacion'])?$ft['cliente']['Mediacion']:-1
								);
						}
						$this->add_asiento($asiento, $dia, $this->lang->line('MULTI N/F') . ' ' . $ft['cNumero']);
					}
					$asiento = $this->asiento();
					foreach ($ft['modospago'] as $mp)
					{
						$key = $mp['cModoPagoCorto'];
						$caja = $mp['cCajaCorto'];
						$cc = (isset($modospagocaja[$key]) && isset($modospagocaja[$key][$caja]))?$modospagocaja[$key][$caja]:$modospago[$key]['cCuenta'];
						#if (empty($modospago[$key]['cCuenta'])) $cc = $modospago[$key]['cCuenta'];
						$this->apunte($asiento,
							TRUE, 
							$cc,
							$mp['fImporte'],
							$caja . ' ' . $key . ' ' . $this->lang->line('N/F') . ' ' . $ft['cNumero'],
							$ft['nIdFactura']
							);
						$this->apunte($asiento,
							FALSE,
							$this->CLIENTES_TIENDA,
							$mp['fImporte'],
							$caja . ' ' . $key . ' ' . $this->lang->line('N/F') . ' ' . $ft['cNumero'],
							$ft['nIdFactura']
							);
					}												
					$this->add_asiento($asiento, $dia, $this->lang->line('PAGO MULTI N/F') . ' ' . $ft['cNumero']);
					$this->add_contabilizar($ft);
				}				
			}
		}
		$this->log($this->final_proceso());
		#$this->out->dialog($this->lang->line('Traspasos'), '<pre>' . implode("\n", $this->log) . '</pre>');
	}

	/**
	 * Muestra el archivo de LOG
	 * @param  int $id ID del LOG
	 * @return HTML_FILE
	 */
	function log($id = null)
	{
		$this->userauth->roleCheck(($this->auth . '.get_list'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$reg = $this->reg->load($id);
			if ($reg)
			{
				$file = $this->file_log_name($reg['cFichero']);
				if (file_exists($file))
				{
					$temp = file_get_contents($file);
					file_put_contents($file, str_replace(array('http://localhost:80/app', 'http://http://', ':80/:80'), 
						array(site_url(), 'http://', ':80'), $temp));
					$this->out->url(site_url() . $this->url_log_name($reg['cFichero']), $this->lang->line('Traspasos'), 'iconoReportTab');
				}
				else
				{
					$data = unserialize(gzuncompress(base64_decode($reg['tDatos'])));

					$this->errores = $data['errores'];
					$this->warnings = $data['warnings'];
					$this->asientos = $data['asientos'];
					$this->log = $data['log'];
					$this->iniciador = $data['iniciador'];
					$this->num_asientos = $data['num_asientos'];
					$this->num_apuntes = $data['num_apuntes'];
					$this->test = $data['test'];
					$this->access = '';
					$this->id = $id;

					$this->crear_fichero_log($reg['cFichero']);
					$this->out->url(site_url() . $this->url_log_name($reg['cFichero']), $this->lang->line('Traspasos'), 'iconoReportTab');
				}
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra el archivo de LOG
	 * @param  int $id ID del LOG
	 * @return HTML_FILE
	 */
	function download($id = null)
	{
		$this->userauth->roleCheck(($this->auth . '.get_list'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$reg = $this->reg->load($id);
			if ($reg)
			{
				$file = $this->file_mdb_name($reg['cFichero']);
				$data = unserialize(gzuncompress(base64_decode($reg['tDatos'])));

				if ($data['test'])
				{
					$this->out->error($this->lang->line('traspasos-modo-prueba'));				
				}

				if (!$this->local_config['force'] && file_exists($file))
				{
					$url = site_url() . $this->url_mdb_name($reg['cFichero']);
					$this->out->redirect($url);
				}
				else
				{
			 		$this->facturas = $data['MovimientosFacturas'];
			 		foreach ($this->facturas as $key => $value) 
			 		{
			 			$value['Nombre'] = str_replace('"',' ', $value['Nombre']);
			 			$this->facturas[$key]['Nombre'] = '"' . substr(utf8_decode(((str_replace("\n", " ", $this->utils->UTF8entities(sanear_string($value['Nombre'])))))), 0, 34) . '"';
			 			foreach ($this->facturas[$key] as $key2 => $value2) 
			 			{
			 				if (is_string($value2) && $value2 == '')
			 					$this->facturas[$key][$key2] = "NULL";
			 			}
			 		}
					$this->ivas = $data['MovimientosIva'];
					$this->disparos = $data['DisparoCartera'];
					$this->movimientos = $data['Movimientos'];
					$this->iniciador = $data['iniciador'];

					#var_dump($this->facturas); die();

					$this->crear_fichero_mdb($reg['cFichero']);

					$url = site_url() . $this->url_mdb_name($reg['cFichero']);
					$this->out->redirect($url);
				}
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Marcar como traspasdo y contabilizado
	 * @param  int $id ID del LOG
	 * @return MSG
	 */
	function procesar($id = null)
	{
		$this->userauth->roleCheck(($this->auth . '.get_list'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$reg = $this->reg->load($id);
			if ($reg)
			{
				if ($reg['bTraspasado'])
				{
					$this->out->error($this->lang->line('traspaso-ya-realizado'));
				}
				$data = unserialize(gzuncompress(base64_decode($reg['tDatos'])));
				if ($data['test'])
				{
					$this->out->error($this->lang->line('traspasos-modo-prueba-procesar'));				
				}
				$this->contabilizar = $data['contabilizar'];
				$this->test = FALSE;
				$this->contabilizar_facturas();
				$this->reg->update($id, array('bTraspasado' => TRUE));
				#var_dump($this->test, $this->log); die();
				$this->out->success(array_pop($this->log));
			}
		}
	}

	/**
	 * Ver facturas contabilizadas
	 * @param  int $id ID del LOG
	 * @return MSG
	 */
	function ver($id = null)
	{
		$this->userauth->roleCheck(($this->auth . '.get_list'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$reg = $this->reg->load($id);
			if ($reg)
			{
				$data = unserialize(gzuncompress(base64_decode($reg['tDatos'])));
				$msg = '<pre>' . print_r($data['contabilizar'], TRUE) .'</pre>';
				$this->out->html_file($msg, $this->lang->line('Contabilizadas'), 'iconoReportTab');
			}
		}
	}

	/**
	 * Ver facturas contabilizadas
	 * @param  int $id ID del LOG
	 * @return MSG
	 */
	function ver_movimientos($id = null)
	{
		$this->userauth->roleCheck(($this->auth . '.get_list'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$reg = $this->reg->load($id);
			if ($reg)
			{
				$data = unserialize(gzuncompress(base64_decode($reg['tDatos'])));
				#var_dump($data); die();
				$msg = "<pre>IVA\n-----\n" . print_r($data['MovimientosIva'], TRUE) .
					"DISPAROS\n-----\n" . print_r($data['DisparoCartera'], TRUE) .
					"FACTURAS\n-----\n" . print_r($data['MovimientosFacturas'], TRUE) .
					'</pre>';
				#echo $msg; die();
				$this->out->html_file($msg, $this->lang->line('Movimientos'), 'iconoReportTab');
			}
		}
	}

	/**
	 * Desmarca como contabilizado
	 * @param  int $id ID del LOG
	 * @return MSG
	 */
	function descontabilizar($id = null)
	{
		$this->userauth->roleCheck(($this->auth . '.get_list'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$reg = $this->reg->load($id);
			if ($reg)
			{
				$data = unserialize(gzuncompress(base64_decode($reg['tDatos'])));
				if ($data['test'])
				{
					$this->out->error($this->lang->line('traspasos-modo-prueba-procesar'));				
				}
				$this->contabilizar = $data['contabilizar'];
				$this->test = FALSE;
				$this->descontabilizar_facturas();
				$this->reg->update($id, array('bTraspasado' => TRUE));
				#var_dump($this->test, $this->log); die();
				$this->out->success(array_pop($this->log));
			}
		}
	}

}

/* End of file bp2lc.php */
/* Location: ./system/bin/bp2lc/Bp2lc.php */