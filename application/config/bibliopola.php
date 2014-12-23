<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Config
 * @category	Config
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0                                          
 * @filesource
 */
define('DS', 				DIRECTORY_SEPARATOR);
//PDF
define('ORIENTATION_PORTRAIT', 'Portrait');
define('ORIENTATION_LANDSCAPE', 'Landscape');
 
$file = __DIR__ . '/bibliopola.defines.php';
if (file_exists($file))
{ 
	require_once($file);
}

define('MY_REV', 			'$Rev: 4427 $');
if (!defined('SEARCH_DEFAULT_OPERATOR'))
	define('SEARCH_DEFAULT_OPERATOR', 'and');
if (!defined('DATA_DOCUMENT_LINES'))
	define('DATA_DOCUMENT_LINES', 'lines');
if (!defined('DIR_TEMP_PATH'))
	define('DIR_TEMP_PATH', 	BASEPATH. 'temp' . DS);
if (!defined('URL_TEMP_PATH'))
	define('URL_TEMP_PATH', 	'system/temp/');
if (!defined('URL_CACHE_PATH'))
	define('URL_CACHE_PATH', 	'system/cache/');
if (!defined('URL_BP2LC_PATH'))
	define('URL_BP2LC_PATH', 	URL_CACHE_PATH . 'bp2lc/');

define('DIR_BIN_GENERAL_PATH', 		BASEPATH. 'bin' .DS);

if (!defined('DIR_BIN_PATH'))
{
	if (PHP_OS == 'WINNT')
		define('DIR_BIN_PATH', 		BASEPATH. 'bin' .DS . 'win' . DS);
	elseif (PHP_OS == 'Darwin')
		define('DIR_BIN_PATH', 		BASEPATH. 'bin' .DS . 'osx' . DS);
	else
		define('DIR_BIN_PATH', 		BASEPATH. 'bin' .DS . 'unix' . DS);
}

if (!defined('DIR_CONTRIB_PATH'))
	define('DIR_CONTRIB_PATH', 	BASEPATH. 'contrib' . DS);
if (!defined('DIR_CACHE_PATH'))
	define('DIR_CACHE_PATH', 	BASEPATH. 'cache' . DS);
if (!defined('DIR_LOG_PATH'))
	define('DIR_LOG_PATH', 		BASEPATH. 'logs' . DS);
if (!defined('DIR_COVERS_PATH'))
	define('DIR_COVERS_PATH', 	DIR_CACHE_PATH. 'covers' . DS);
if (!defined('DIR_THUMB_PATH'))
	define('DIR_THUMB_PATH', 	DIR_CACHE_PATH. 'thumb' . DS);
if (!defined('DIR_SINLI_PATH'))
	define('DIR_SINLI_PATH', 	DIR_CACHE_PATH. 'sinli' . DS);
if (!defined('DIR_BP2LC_PATH'))
	define('DIR_BP2LC_PATH', 	DIR_CACHE_PATH. 'bp2lc' . DS);
if (!defined('DIR_SINLI_TEMP_PATH'))
	define('DIR_SINLI_TEMP_PATH', 	DIR_CACHE_PATH. 'sinlitemp' . DS);
if (!defined('DIR_CODEBAR_PATH'))
	define('DIR_CODEBAR_PATH', 	DIR_CACHE_PATH. 'codebar' . DS);
if (!defined('DIR_CONFIG_PATH'))
	define('DIR_CONFIG_PATH', 	APPPATH. 'config' . DS);

if (!defined('LANGUAGE_CREATE'))
	define('LANGUAGE_CREATE', 	FALSE);

// General
$config['bp.application.title'] 	= 'Bibliopola';
$config['bp.application.major'] 	= 5;
$config['bp.application.minor'] 	= 0;                    

$config['bp.application.revision'] 	= SVN_REV; //str_replace(array('$', 'Rev:', ' '), '', MY_REV); 

$config['bp.application.version'] 	= $config['bp.application.major'] . '.' . $config['bp.application.minor'] . '.' . $config['bp.application.revision'];
$config['bp.application.name'] 		= $config['bp.application.title'] . ' ' . $config['bp.application.version'];

$config['bp.application.extjs'] 	= 'ext';
$config['bp.application.style'] 	= 'xtheme-slate.css';
$config['bp.application.styles'] 	= array('xtheme-slate', 'xtheme-black', 'xtheme-darkgray', 'xtheme-olive', 'xtheme-purple', 'xtheme-slickness', 'xtheme-gray', 'xtheme-blue');
$config['bp.application.icon']		= 'bibliopola.ico';
$config['bp.application.icon.beta']		= 'bibliopola-black.ico';
$config['bp.application.menu'] 		= 'menu.xml';
$config['bp.application.menustyle'] 		= 'menubar';
$config['bp.application.help'] 		= 'doc/wiki/index.php';
$config['bp.application.help.topic'] 		= 'doc/wiki/index.php?title=%s';
$config['bp.report.style'] 			= 'sort3.css';

$config['js.debug']					= TRUE;
$config['js.debug.firebug']	= FALSE;
$config['css.debug']				= TRUE;

$config['bp.application.msgtarget'] = 'under';
$config['bp.application.tabscrollmenu'] 		= FALSE;
$config['bp.application.tabclosemenu'] 		= TRUE;

$config['bp.application.stayalive'] = 1000 * 5;//1000 * 60 * 5; // 5 minutos
$config['bp.application.fly'] 		=  5000; // 5 segundos
$config['bp.application.fly.align'] 		=  'bl-bl'; // 5 segundos
$config['bp.application.timeout'] = 1000 * 60;//1000 * 60 * 5; // 5 minutos

$config['bp.application.sucursal'] 	= 1;

// Caché
$config['bp.cache']					= FALSE;
$config['bp.cache.html']			= FALSE && $config['bp.cache'];
$config['bp.cache.models']			= FALSE && $config['bp.cache'];
$config['bp.cache.memory']			= 'apc';
$config['bp.cache.memcache']		= array(array('host' => 'localhost', 'port' => 11211, 'weight' => 10));
$config['bp.cache.covers']			= TRUE && $config['bp.cache'];

// Exportaciones
$config['bp.export.extensions']		= array ('XLS', 'HTML', 'PDF', 'RTF', 'PRINT', 'ODT', 'RTF' ,'DOCX', 'XLSX');

// Formato de salida por defecto
$config['bp.data.format'] 			= 'json';
$config['bp.data.limit'] 			= 100;
$config['bp.data.limit_big'] 		= 300;
$config['bp.data.search.limit'] 	= 20;

$config['bp.currency.decimals'] 	= 2;
$config['bp.currency.dec_points'] 	= ',';
$config['bp.currency.thousands_sep']= '.';
$config['bp.currency.symbol_left'] 	= '';
$config['bp.currency.symbol_right'] = '&euro;';

$config['bp.percent.decimals'] 		= 2;
$config['bp.percent.dec_points'] 	= ',';
$config['bp.percent.thousands_sep']	= '.';
$config['bp.percent.symbol_left'] 	= '';
$config['bp.percent.symbol_right'] 	= '%';

$config['bp.date.format']			= 'd/m/Y';
$config['bp.date.formatlong']		= 'd/m/Y G:i:s';
$config['bp.date.formattime']		= 'G:i:s';
$config['bp.date.format.i.dia']		= 0;
$config['bp.date.format.i.mes']		= 1;
$config['bp.date.format.i.year']	= 2;
$config['bp.date.startday']		= 1;

$config['bp.data.separator']		= ' / ';
$config['bp.data.id_text']			= ' (%1)';
// Upload
$config['bp.upload.path'] = BASEPATH . 'upload';
$config['bp.upload.maxnamelength'] = 260;

$config['bp.upload.max_file_size_in_bytes'] = 2147483647; // 2GB in bytes
$config['max.excel.preview'] = 20;

$config['bp_upload_path'] 			= BASEPATH . 'upload';
// Tarifas de envío
$config['bp_tarifasenvio_gramos_libro'] = 850;

// SMS
$config['bp.sms.url'] 				= 'http://localhost/app/index.php/sys/test/request';
#$config['bp.sms.url'] 				= 'http://www.smstrend.net/esp/sendMessageFromPost.oeg';

$config['bp.sms.username'] 			= 'alex@alibri.es';
$config['bp.sms.password'] 			= 'zlBhOjuU';
#$config['bp.sms.username'] 			= 'alopez@alibri.es';
#$config['bp.sms.password'] 			= 'test';
$config['bp.sms.quality'] 			= 'GOLD';
$config['bp.sms.type'] 				= 'PLUS';
$config['bp.sms.from'] 				= 'alibri';

//Mailing
$config['bp.mailing.fromname']		= 'ALIBRI Información';
$config['bp.mailing.from']			= 'info@alibri.es';
$config['bp.mailing.host']			= 'localhost';
$config['bp.mailing.auth']			= FALSE;
$config['bp.mailing.pass']			= NULL; #'Alibri2012';
$config['bp.mailing.user']			= NULL; #'wec671c';
$config['bp.mailing.protocol']		= 'smtp';
$config['bp.mailing.maxgroup']		= 20;
$config['bp.mailing.debug']			= TRUE;
$config['bp.mailing.debugemails']	= 'alex@alibri.cat;alopez@alibri.es;bolaschinas@msn.com';
$config['bp.mailing.domains']		= array('www.alibri.es', 'www.ai-sl.com', 'www.alibri.cat');
$config['bp.boletin.allowtags'] 	= '<p><li><br><strong><b><ul><div><blockquote>';
$config['bp.sinopsis.allowtags'] = '<p><li><br><strong><b><ul><div><a><img><blockquote>';

$config['bp.mailing.css']			= 'mailing.css';
$config['bp.editor.css']			= 'mailing.css';
$config['bp.data.css']			= 'data.css';

$config['bp.mensajes.limit']		= 10;

//OLTP
$config['bp.oltp.valordpr']			= 269093.99;
$config['bp.oltp.fechadpr'] 		= "31/12/2013";
$config['bp.oltp.depreciacion1'] 	= 0;
$config['bp.oltp.depreciacion2'] 	= 0.5;
$config['bp.oltp.depreciacion3'] 	= 0.95;
$config['bp.oltp.depreciacion4'] 	= 1;
$config['bp.oltp.margen'] 	= 0.3042;
$config['bp.max.excel.rows'] = 50000;

$config['oltp.suscripciones.serieanticipo'] 	= 39;
$config['oltp.suscripciones.serienormal'] 		= 38;

$config['bp.calendario.database']	= 'Personal..';
$config['bp.oltp.database']			= 'BibliopolaStock..';
$config['bp.stocks.database']			= 'BibliopolaStock..';
$config['bp.concursos.database']	= 'Consorci2012..';

//Devoluciones
$config['devoluciones.lineaspedidoseccion'] 	= 10;

//PDF
$config['pdf.papersize']			= 'A4';
$config['pdf.orientation']			= ORIENTATION_PORTRAIT;
$config['pdf.leftmargin']			= 10;
$config['pdf.rightmargin']			= 10;
$config['pdf.topmargin']			= 10;
$config['pdf.bottommargin']			= 10;
$config['pdf.parameters']			= '-q --footer-right "[page]/[toPage]" --footer-line';
if (PHP_OS == 'WINNT')
	$config['pdf.path']					= DIR_BIN_PATH .'wkhtmltopdf.exe';
else {
	if (strpos(php_uname(), 'x86_64') !== FALSE)
		$config['pdf.path']					= DIR_BIN_PATH .'wkhtmltopdf-amd64';
	else
		$config['pdf.path']					= DIR_BIN_PATH .'wkhtmltopdf-i386';
}

$config['pdf.replaces']				= array(
	'www.alibri.es:8888/app/' 	=> 'app.alibri.es/', 
	'www.alibri.es/app/' 		=> 'app.alibri.es/', 
	'app.alibri.es:8888/app/' 	=> 'app.alibri.es/'
	);

//SUBVERSION
$config['svn.username']				= 'bibliopola';
$config['svn.password']				= 'bibliopola';
$config['svn.cmd']					= 'git pull';
$config['svn.path']					= DIR_BIN_PATH .'svn/';
$config['svn.src']					= dirname(FCPATH);

//TASKS
$config['bp.runner.max']			= 3;
$config['bp.runner.wait']			= 5;
$config['bp.runner.mode']			= 'resident';
$config['bp.runner.echo']			= TRUE;
$config['bp.runner.username']		= 'runner';
$config['bp.runner.password']		= 'runner';
$config['bp.runner.alias']			= array(
	'www.alibri.es' => 'localhost', 
	'localhost:80/app' => 'app.alibri.es',
	'galicia:80/app' => 'app.alibri.es'
	);
$config['bp.runner.cron.debug'] = 1;

$config['bp.runner.extraprocess']['Scan'] = BASEPATH . 'bin/scan/scan.php';

$config['bp.runner.sudo.pass'] = '';


//Plugins
#$config['bp.plugins.stayalive']		= '';
#$config['bp.plugins.status']		= 'StatusMessages;StatusDatabase;StatusComandos;StatusVersion';
$config['bp.plugins.status']		= 'StatusDatabase;StatusComandos;StatusVersion';

// Calendario
/**
 * Mínimo de horas que se considera jornada completa
 * @var double
 */
$config['calendario.jornada']		= 7.0;

// Reports
$config['bp.reports.path']			= 'reports';// 
$config['bp.reports.css']			= 'print.css';//
$config['bp.documentos.css']		= 'docs.css';//

//Solr
$config['bp.solr.path.articulo']		= DIR_TEMP_PATH . 'solr_art' . DS;		
$config['bp.solr.path.materia']			= DIR_TEMP_PATH . 'solr_mat' . DS;		
$config['bp.solr.update']			= 'http://localhost:8983/solr/update';		
$config['bp.solr.query']			= 'http://localhost:8983/solr/select?wt=phps';		

//Export
$config['bp.export.path.articulo']		= DIR_TEMP_PATH . 'export_art' . DS;		
$config['bp.export.path.materia']		= DIR_TEMP_PATH . 'export_mat' . DS;		

// Web
$config['bp.web.pagesize']			= 20;

// Temporales
$config['bp.temporal.time']			= 24; // 24 horas		

//Sphinx
$config['bp.sphinx.host']			= '127.0.0.1';
$config['bp.sphinx.port']			= '9312';
$config['bp.sphinx.index']			= 'wiki_main';
$config['bp.sphinx.matches']		= '10';
# How many matches searchd will keep in RAM while searching
$config['bp.sphinx.maxmatches']		= '1000';
# Weights of individual indexed columns. This gives page titles extra weight
$config['bp.sphinx.weights']		= array('old_text'=>1, 'page_title'=>100);
# When to stop searching all together (if different from zero)
$config['bp.sphinx.cutoff'] 		= 0;

// Cambios divisa
$config['bp.divisa.portes'] 		= 2.5;
$config['bp.divisa.dtoprv'] 		= 30;
$config['bp.divisa.default'] 		= 2;
$config['bp.divisa.tipodefault'] 	= 1;
$config['bp.divisa.margenmoneda'] 	= 3;
$config['bp.divisa.simbolo'] 	= 'EUR';

// Importar
$config['bp.import.iva']		= 4;		
$config['bp.import.tipo']		= 1;

// Logger
$config['bp.logger.enabled']	= TRUE;
$config['bp.logger.groups']['default'] = TRUE;
$config['bp.logger.groups']['scheduler'] = TRUE;
$config['bp.logger.groups']['unificador'] = TRUE;
$config['bp.logger.groups']['login'] = TRUE;
$config['bp.logger.groups']['mailing'] = TRUE;
$config['bp.logger.groups']['cron'] = TRUE;
$config['bp.logger.groups']['catalogo'] = TRUE;
$config['bp.logger.groups']['email'] = TRUE;
$config['bp.logger.groups']['avisosrenovacion'] = TRUE;
$config['bp.logger.groups']['errores'] = TRUE;
$config['bp.logger.groups']['stocks'] = TRUE;

//TPV
$config['bp.tpv.caja']	= 1;
$config['bp.tpv.serie']	= 25;
$config['bp.tpv.cliente']	= 100000;

$config['bp.clientes.digitoscuenta']	= 9;

$config['bp.ventas.margenminimo']	= 5;
$config['bp.ventas.mantenerpreciosuperior']	= FALSE;
$config['bp.ventas.margenoriginal']	= 10;


$config['bp.docs.ceros']	= 8;
$config['bp.factura.format']	= '%d-%d';
$config['bp.format.titlelen']	= 150;
$config['bp.format.reflen']	= 30;
$config['bp.grid.tpv.hide']	= 'nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;';
$config['bp.grid.facturacion.hide']	= 'nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;';
$config['bp.factura.ticket']= 'factura.ticket.titulo.60';
$config['bp.factura.ticket.factura']= 'factura.ticket.titulo.factura.60';
$config['bp.factura.ticket.regalo']= 'factura.ticket.titulo.regalo.60';
$config['bp.factura.ticket.print']	= TRUE;
$config['bp.factura.ticket.nuevo']	= TRUE;
$config['bp.factura.secciones.defecto']	= '215;811;627;628;522;106;891;898;629;901;886;878;103;887;710;885;309;102;873;899;101;523;625;313;903;420;524;895;875;900;888;889;311;631;893;880;897;882;884;894;879;896;881;883;104;105;877;831;892;417;630;876;419;418;521;312;890;107;626;909;831;927;928';
$config['bp.factura.secciones.vedadas']	= '869;837;803;802;835;907;841;816;863;806;808;871;906;804;845;866;852;842;843;809;807;867;860;905;818;832;904;915;916;917;918;910;912;920;925;924;923;922;921;931'; 

$config['bp.printerserver.host'] = 'localhost';
$config['bp.printerserver.port'] = '1234';
$config['bp.drawerserver.host'] = 'localhost';
$config['bp.drawerserver.port'] = '1234';
$config['bp.labelserver.host'] = '192.168.0.74';
$config['bp.labelserver.port'] = '1234';
$config['bp.teixellserver.host'] = '192.168.0.171';
$config['bp.teixellserver.port'] = '1234';

$config['bp.anticipo.idarticulo'] = 195498;
$config['bp.anticipo.idseccion'] = 101;
$config['bp.anticipo.iva'] = 4;

$config['bp.etiquetas.idseccion'] = 101;

$config['bp.config.app']	= 'bp.factura.secciones.defecto;bp.factura.secciones.vedadas;bp.albaransalida.secciones.defecto;bp.albaransalida.secciones.vedadas;bp.pedidocliente.secciones.defecto;bp.pedidocliente.secciones.vedadas;bp.pedidoproveedor.secciones.defecto;bp.pedidoproveedor.secciones.vedadas;bp.devolucion.secciones.defecto;bp.devolucion.secciones.vedadas'; 

$config['bp.address.pais'] = 48;
$config['bp.address.region'] = 963; 

$config['bp.pais.local'] = 48;

$config['bp.abono.caducidad'] = 180;
$config['bp.abono.formatodefecto'] = 'abono.general';

$config['ventas.tpv.aplicardescuento'] = TRUE;
$config['ventas.tpv.descuento'] = 5;

$config['bp.factura.idseccionajuste'] = 101;
$config['bp.factura.idlibroajuste'] = 312241;

//Albarán de salida
$config['bp.grid.albaransalida.hide']	= 'nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;';
$config['bp.albaransalida.secciones.defecto']			= '';// 
$config['bp.albaransalida.secciones.vedadas']			= '';// 

// Portal
$config['bp.portal.portlets'] = 'ultimasventas;texto;rss;precios';
$config['bp.portal.columns'] = 3;
$config['bp.portal.portlets.1'] = 'Últimas ventas#ultimasventas#20';
#$config['bp.portal.portlets.3'] = 'Twitter#texto#300::\'tools/twitter/follow/libreriaALIBRI\''; #Intranet:rss';
$config['bp.portal.portlets.2'] = 'Precios#precios#'; 
#Twitter#texto#300::\'tools/twitter/follow/libreriaALIBRI\'
$config['bp.portal.portlets.3'] = 'Intranet#rss#\'http://app.alibri.es/doc/intranet/?feed=rss2\';';

$config['bp.portal.portlets.facturasrefresh'] = 60 * 1000; # 60 segundos

#Codebar
$config['codebar.path']					= 'zint';
$config['codebar.command.direct']					= DIR_BIN_PATH .'zint.exe --directpng  -i "%code" -barcode=%type';
$config['codebar.default']					= 129;
$config['codebar.documents']					= FALSE;

$config['bp.articulos.busquedas.items'] = 100;

//Email
$config['bp.email.fromname']		= 'ALIBRI Información';
$config['bp.email.from']			= 'interno@alibri.es';
$config['bp.email.host']			= 'localhost';
$config['bp.email.auth']			= FALSE;
$config['bp.email.pass']			= NULL; #'Alibri2009';
$config['bp.email.user']			= NULL; #'qbj683c';
$config['bp.email.protocol']		= 'smtp';

// Suscripciones
$config['bp.suscripciones.fromname']		= 'ALIBRI Libreria - Suscripcions - Suscripciones';
$config['bp.suscripciones.from']			= 'info@alibri.es';
$config['bp.suscripciones.host']			= 'localhost';
$config['bp.suscripciones.auth']			= FALSE;
$config['bp.suscripciones.pass']			= NULL;  #'Alibri2012';
$config['bp.suscripciones.user']			= NULL; #'wec671c';
$config['bp.suscripciones.protocol']		= 'smtp';
$config['bp.suscripciones.css']				= 'docs.css';
$config['bp.suscripciones.debug']			= TRUE;
$config['bp.suscripciones.debugemails']		= 'alex@alibri.cat';
$config['bp.suscripciones.avisos']			= 'alex@alibri.cat';

$config['bp.suscripciones.caja'] 		= 29;
$config['bp.suscripciones.modopago'] 		= 6;
$config['bp.suscripciones.seccion'] 		= 800;

$config['bp.entradamercancia.peso']	= 1000;
$config['bp.entradamercancia.tipomercancia']	= 1;

// Pedido cliente
$config['bp.grid.pedidocliente.hide']	= 'nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;';
$config['bp.pedidocliente.secciones.defecto']			= '';// 
$config['bp.pedidocliente.secciones.vedadas']			= '';// 
$config['bp.pedidocliente.excel.report'] = 'pedidocliente.excel';

$config['ventas.pedidocliente.actualizar'] = TRUE;

$config['bp.presupuesto.caducidad'] = 30;

// Webmail
$config['bp.webmail.url']			= 'http://mail.alibri.es/src/redirect.php?just_logged_in=1&login_username=%username%&secretkey=%password%';//

//Mapa
$config['bp.map.url'] = 'http://maps.google.com/maps?q=%direccion%';

// Concursos
$config['bp.concursos.idgrupo'] = 45;
$config['bp.concursos.general'] = FALSE;
$config['bp.concursos.narrativa'] = TRUE;

$config['bp.concursos'][] = 'Consorci2012';
$config['bp.concursos'][] = 'Consorci2011';
$config['bp.concursos'][] = 'Consorci2010';
$config['bp.concursos'][] = 'Diba2010';
$config['bp.concursos'][] = 'Diba2009';
$config['bp.concursos'][] = 'ElMasnou';

$config['bp.concursos.install'][] = TRUE;

$config['bp.concursos.secciondefecto'] = NULL;
$config['bp.concursos.direccionenviodefecto'] = NULL;
$config['bp.concursos.secciondefecto'] = NULL;
$config['bp.concursos.concursodefecto'] = NULL;
$config['bp.concursos.referenciadefecto'] = NULL;
$config['bp.concursos.notadefecto'] = NULL;

$config['concursos.teixells.formato'] = 'etiquetas.general';
$config['concursos.teixells.length'] = 12;

$config['bp.concurso.filas'] = 4;
$config['bp.concurso.columnas'] = 11;
$config['bp.concurso.lineas'] = 6;
$config['bp.concurso.caracteres'] = 8;
$config['bp.concurso.caracteres2'] = 14;
$config['bp.concurso.caracteres3'] = 18;

$config['bp.concurso.dias'] = 90;
$config['bp.concursos.emails.alternativas'] = array('alopez@alibri.cat', 'lgarcia@alibri.cat', 'lvidal@alibri.cat');

// Conversor
$config['convert2.parameters']			= '--format %format% --output %out% %in%';
$config['convert2.path']					= 'html-convert';
$config['convert.xfcdir'] = '/opt/xfc_perso_java/bin';
$config['convert.parameters']			= '-s --smart --normalize -o %out% %in%'; # -t %format% 
$config['convert.path']					= 'pandoc';

//Reports
$config['reports.language'] = 'ca;es;en';
$config['reports.preview'] = FALSE;

//Sender
$config['sender.pedidoproveedor'] = 'pedidoproveedor.email.titulo';
$config['sender.cc'] = TRUE;
$config['sender.debug'] = 'alex@alibri.cat';
$config['sender.factura'] = 'factura.email.titulo';
$config['sender.devolucion'] = 'devolucion.email.titulo';
$config['sender.reclamacionpedidoproveedor'] = 'reclamacionpedidoproveedor.email.titulo';
$config['sender.cancelacionpedidoproveedor'] = 'cancelacionpedidoproveedor.email.titulo';
$config['sender.albaransalida'] = 'albaransalida.email.titulo';
$config['sender.albaranentrada'] = 'albaranentrada.email.titulo';
$config['sender.pedidocliente'] = 'pedidocliente.infocliente.titulo';
$config['sender.abono'] = 'abono.general';
$config['sender.reclamacionsuscripcion'] = 'suscripciones.reclamacion.general';

$config['catalogo.buscar.showportada'] = TRUE;
$config['catalogo.webpage.url'] = 'http://www.alibri.es/%id%';
$config['catalogo.webpage.cover'] = 'http://www.alibri.es/product/product/cover?id=%id%';

// Pedido proveedor
$config['bp.grid.pedidoproveedor.hide']	= 'nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;';
$config['bp.pedidoproveedor.secciones.defecto']			= '';// 
$config['bp.pedidoproveedor.secciones.vedadas']			= '';// 
$config['bp.pedidoproveedor.excel.report'] = 'pedidoproveedor.excel';

// Albarán de entrada
$config['bp.grid.albaranentrada.hide']	= 'nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;';
$config['bp.albaranentrada.secciones.defecto']			= '';// 
$config['bp.albaranentrada.secciones.vedadas']			= '';// 

// Devolución
$config['bp.grid.devolucion.hide']	= 'nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;';
$config['bp.devolucion.secciones.defecto']			= '';// 
$config['bp.devolucion.secciones.vedadas']			= '';// 

//Stocks
$config['bp.oltp.unidades_muestra'] = 20;
$config['bp.stocks.idajustemas'] = 11;
$config['bp.stocks.idajustemenos'] = 12;
$config['bp.stocks.idregulacionmas'] = 41;
$config['bp.stocks.idregulacionmenos'] = 42;
$config['bp.stocks.fechainventario'] = '11/01/2014';
$config['bp.ventas.year.inicio'] = '2004';
$config['bp.contarstocks.firme'] = 9000000;
$config['bp.contarstocks.deposito'] = 9000001;
//Webpage
$config['bp.webpage.bestsellers.dias'] = 30;
$config['bp.webpage.bestsellers.notsec'] = '860, 800, 819, 861';
$config['bp.webpage.origenpedido'] = 5;
$config['bp.webpage.tiponota'] = 3;

//Catálogo
$config['bp.catalogo.idtipoarticulodefault'] = 1;
$config['bp.catalogo.idsecciondefault'] = 101;
$config['bp.catalogo.idtipoautordefault'] = 1;
$config['bp.catalogo.nocover'] = 'no_image-180x180.jpg';
$config['bp.catalogo.nocover.extension'] = 'gif';
$config['bp.catalogo.cover.thumb'] = 120;
$config['bp.catalogo.cover.articulo'] = 120;
$config['bp.catalogo.cover.datosventa'] = 75;
$config['bp.catalogo.cover.2columnas'] = 50;
$config['bp.catalogo.cover.small'] = 40;
$config['bp.catalogo.autores.format'] = '%a%, %n%';
$config['bp.catalogo.autores.separator'] = ' / ';
$config['bp.catalogo.estado.descatalogados.dias'] = 7;
$config['bp.depositos.motivomas'] = 23;
$config['bp.depositos.motivomenos'] = 24;
$config['catalogo.ubicacion.formato'] = 'etiquetas.general';

// Tarifas
$config['ventas.tarifas.defecto'] = 1;

// SINLI
#{mail.server.com:993/novalidate-cert/pop3/ssl}
$config['sinli.mailbox.url'] = 'mail.alibri.es:110/novalidate-cert/pop3';
$config['sinli.mailbox.username'] = 'wdx445c';
$config['sinli.mailbox.password'] = 'Sinli2011';
$config['sinli.identificacion'] = 'LIB00497';
$config['sinli.email'] = 'sinli@alibri.es';
$config['sinli.cc'] = TRUE;
$config['sinli.emaildebug'] = 'alex@alibri.es';
$config['sinli.debug'] = TRUE;
$config['sinli.debug.temp'] = TRUE;

// Etiquetas
$config['compras.etiquetas.formato'] = 'etiquetas.general';
$config['compras.etiquetas.grupos'] = 5;

// Precios de venta
$config['compras.precio.variacion'] = 5;

$config['bp.promocion.idweb'] = 12;

// Cache de líneas de artículos
$config['bp.cache.tpv'] = FALSE;

$config['bp.articulo.diaspromocion'] = 30;
$config['bp.promocion.web'] = 12;

// Webshop
$config['bp.webshop.server'] = 'http://localhost/shop';
$config['bp.webshop.username'] = 'admin';
$config['bp.webshop.password'] = 'xavier';

// Scribd
$config['bp.scribd.api_key'] = '7fovjyak3yj56zzyfugc4';
$config['bp.scribd.secret'] = 'sec-aizoe2saunxda1iszrxhndtoky';

// Screenshot
if (PHP_OS == 'WINNT')
{
	$config['bp.screenshot.url2png'] = 'python ' .DIR_BIN_PATH. 'webkit2png.py --xvfb 500 500 "{input}" -o "{output}"'; 
}
else 
{
	if (strpos(php_uname(), 'x86_64') !== FALSE)
		$config['bp.screenshot.url2png'] = DIR_BIN_PATH. 'wkhtmltoimage-amd64 --height 500 -f PNG "{input}" "{output}"'; 
	else
		$config['bp.screenshot.url2png'] = DIR_BIN_PATH. 'wkhtmltoimage-i386 --height 500 -f PNG "{input}" "{output}"'; 
}

$config['bp.screenshot.pdf2png'] = 'convert -monitor -density 75 "{input}" "{output}"';
$config['bp.screenshot.timeout'] = 60; # 60 segundos

// Download
$config['bp.path.download.path'] = 'python ' .DIR_BIN_GENERAL_PATH. 'download.py'; 
$config['bp.path.download.threads'] = 10;
$config['bp.path.download.timeout'] = 30;
// Portes
$config['bp.idportes'] = 500;
$config['bp.ivaportes'] = 21;
set_include_path(get_include_path() . PATH_SEPARATOR . DIR_CONTRIB_PATH);

// Dilve
$config['bp.dilve.username'] = 'alopez03';
$config['bp.dilve.password'] = '2BY3CDSM';
$config['bp.dilve.url']  = 'http://www.dilve.es/dilve/dilve/%action%.do?user=%username%&password=%password%%params%';

// Cámara del libro
$config['bp.camara.codigoimportador'] = '1.278-73';
$config['bp.camara.posicionestadistica'] = '49.01.99.00.00';
$config['bp.camara.tramesa'] = 'VARIOS';

// Servicio novedades
$config['bp.servnov.idseccion'] = 816;

// Pedidos Proveedor
$config['bp.compras.direcciones'] = 154;
$config['bp.compras.direcciones.default'] = 32564;

// Importar / Exportar
$config['bp.export.numregs']  = 25000;

$config['bp.import.database.allow'] = FALSE;
$config['bp.export.database.allow'] = FALSE;
$config['bp.export.general.allow'] = FALSE;

$config['bp.ventas.procesar.allow'] = TRUE;

$config['bp.import.remoto.allow'] = TRUE;

$config['bp.import.remote.server'] = 'http://localhost/app/index.php';
$config['bp.import.remote.username'] = 'export';
$config['bp.import.remote.password'] = 'ca7feb341e7a3bac2bc2839efaa3c952b59f2d23';

// EOI
$config['bp.eoi.wiki'] = 'http://wikieoi.alibri.es/?dbg=1212';
$config['bp.eoi.wiki.url'] = 'http://wikieoi.alibri.es/index.php?title=%s&dbg=1212';
$config['bp.eoi.wiki.replace'] = array('/index.php?title=', '/index.php/');

// ASM
$config['bp.asm.url.enviar'] = 'http://www.asmred.com/websrvs/b2b.asmx';
$config['bp.asm.url.etiqueta'] = 'http://www.asmred.com/WebSrvs/printserver.asmx';

$config['bp.asm.uid'] = 'fbea3703-ae5e-49d3-bbc1-0ededb96d2f2';
$config['bp.ventas.direcciones.recoger'] = 32564;
$config['bp.ventas.telefono.recoger'] = '933170578';
$config['bp.ventas.email.recoger'] = 'alopez@alibri.es';
$config['bp.ventas.hora.desde'] = '16:00';
$config['bp.ventas.hora.hasta'] = '18:00';

// Contabilidad
$config['bp.contabilidad.cc.cliente'] = '430000001';
$config['bp.contabilidad.cc.ventas'] = '700000001';

// BP2LC
$config['bp2lc.lc_username'] = 'sa';
$config['bp2lc.lc_password']= 'xavier';
$config['bp2lc.lc_server'] = '192.168.0.250';
$config['bp2lc.lc_database'] = 'ALIBRI';

$config['bp2lc.debug'] = TRUE;
$config['bp2lc.mdb'] = 'http://192.168.0.250/lc/mdb.php';
$config['bp2lc.fechalimite'] = null; #'05/01/2013';
$config['bp2lc.test'] = FALSE;
$config['bp2lc.diff'] = 0.05;
$config['bp2lc.force.mdb'] = TRUE;

$config['bp2lc.FACTURA_EMITIDA']		 ='E';
$config['bp2lc.TIPO_EFECTO_EXCLUIDO']	 =2;
$config['bp2lc.PREVISION_COBRO']		 = 'C';
$config['bp2lc.DIARIO_DEFECTO']		=	10;
$config['bp2lc.EFECTO_REEMBOLSO'] =3;
$config['bp2lc.TAM_MODOSPAGO_DESC'] = 	6;

// SYS
$config['temp.blacklist'] = array('loadingAnimation.gif');

// Imágenes
$config['bp.images.convert.gif'] = 	'convert -strip %in %out';
$config['bp.images.optim.jpeg'] = 'jpegoptim --strip-all %in';
if (PHP_OS == 'WINNT')
	$config['bp.images.optim.png'] = null;
else {
	if (strpos(php_uname(), 'x86_64') !== FALSE)
		$config['bp.images.optim.png'] = DIR_BIN_PATH .'pngout-amd64  -y -c2 %in %out ';
	else
		$config['bp.images.optim.png'] = DIR_BIN_PATH .'pngout-i686  -y -c2 %in %out ';
}

$file = __DIR__ . '/bibliopola.local.php'; 
if (file_exists($file)) require_once($file);

/* End of file bibliopola.php */
/* Location: ./system/application/config/bibliopola.php */