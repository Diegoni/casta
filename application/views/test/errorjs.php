<?php
/**
 * Bibliopola
 *
 * Estructura básica para mostrar un formulario como una aplicación independiente
 *
 * @package		Bibliopola 5.0
 * @subpackage	Views
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

//@todo Implementar un sistema de caché y que los elementos JS se generen desde PHP
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es">
<head>
<link rel="shortcut icon"
	href="<?php echo image_asset_url($this->config->item('bp.application.name')); ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $title; ?></title>
<?php if (!isset($css_include)) $css_include = null; ?>
<?php $this->load->view('main/css', array('css_include' => $css_include));?>
</head>
<body>

<?php if (!isset($js_include)) $js_include = null; ?>
<?php
$js_files = array (
array('jQuery/jquery'),
array('jQuery/jquery.measure'),
array('jQuery/jquery.place'),
array('jQuery/jquery.pulse'),
array('jQuery/jquery.mask'),
array('jQuery/jquery.loading'),
array('jQuery/jquery.facebox'),
#array('jQuery/jquery.tipsy'),
array('ext/adapter/jquery/ext-jquery-adapter', 'ext/adapter/jquery/ext-jquery-adapter-debug'),
array('ext/ext-all', 'ext/ext-all-debug'),
#array('ext-fixes'),
array('ext/locale/ext-lang-es'),
array('tiny_mce/tiny_mce', 'tiny_mce/tiny_mce_src'),
array('tiny_mce/langs/es', 'tiny_mce/langs/es'),
array('ux/FileUploadField'),
array('ux/miframe'),
array('ux/Spinner'),
array('ux/SpinnerStrategy'),
array('ux/TreeCheckNode'),
array('ux/data-view-plugins'),
array('ux/GroupSummary'),
array('ux/HistoryClearableComboBox'),
array('ux/Ext.ux.TDGi.MenuKeyTrigger'),
array('ux/Ext.ux.TinyMCE'),
array('ux/StatusBar'),
array('ux/ColumnTree'),
array('ux/DateTimeEx'),
array('ux/CheckColumn'),
#array('ux/iframe'),
array('ux/SuperBoxSelect'),
array('ux/autocomplete'),
array('ux/browsebutton'),
array('ux/Ext.ux.TextID'),
array('ux/Ext.ux.CellToolTips'),
array('ux/Ext.ux.ISBN'),
array('ux/Ext.ux.SearchCombo'),
array('ux/Ext.ux.HtmlEditor.Plugins'),
array('ux/StartMenu'),
array('ux/Portal'),
array('ux/PortalColumn'),
array('ux/Portlet'),
array('ux/TabScrollerMenu'),
#array('ux/ToolbarDroppable'),
#array('ux/ToolbarReorderer'),
#array('ux/Reorderer'),
array('ux/Ext.ux.IconCombo'),
#array('zeroclipboard/ZeroClipboard'),
array('ux/Ext.ux.MessageWindow'),
array('ux/slidingtabs'),
array('ux/TabCloseMenu'),
array('jsDate'),
array('gridToExcel'),
array('functions'),
array('documentos'),
);
$dbg = $this->config->item('js.debug');
foreach($js_files as $js_file)
{
	$file = ($dbg)?(isset($js_file[1])?$js_file[1]:$js_file[0]):($js_file[0]. ((isset($js_file[1])?'':'.min')));
	echo js_asset($file .'.js');
}
?>

<!-- 
<script type="text/javascript"
	src="<?php echo site_url('sys/app/lang');?>"></script>
<script type="text/javascript"
	src="<?php echo site_url('sys/app/routes');?>"></script>
<script type="text/javascript"
	src="<?php echo site_url('sys/app/js_status');?>"></script>
<script type="text/javascript"
	src="<?php echo site_url('sys/app/js_menu');?>"></script>
<script type="text/javascript"
	src="<?php echo site_url('sys/app/lib');?>"></script>
	-->
<script type="text/javascript">
	// TamaÃ±os de columnas
	Ext.app.TAM_COLUMN_DATE = 80;
	Ext.app.TAM_COLUMN_ID = 60;
	Ext.app.TAM_COLUMN_MONEY = 70;
	Ext.app.TAM_COLUMN_NAMES = 150;
	Ext.app.TAM_COLUMN_NUMBER = 60;
	Ext.app.TAM_COLUMN_DEFAULT = 60;
	Ext.app.TAM_COLUMN_TEXT = 100;
	Ext.app.TAM_COLUMN_TITLE = 150;
	Ext.app.TAM_COLUMN_ISBN = 80;
	Ext.app.TAM_COLUMN_AUTHORS = 120;
	Ext.app.TAM_COLUMN_STOCK = 30;
	Ext.app.TAM_COLUMN_ICON = 25;
	Ext.app.TAM_COLUMN_IMAGE = 50;
	Ext.app.TAM_COLUMN_BOOL = 25;

	Ext.app.LABEL_SIZE = 100;

	Ext.app.REPOINFOHEIGHT = 300;
	Ext.app.PEDIRWIDTH = 800;

	Ext.app.SEARCHFILTERHEIGHT = 200;

	Ext.app.DIRECCIONESCOMBOWIDTH = 300;
	Ext.app.CLIENTEFIELDWIDTH = 350;
	Ext.app.STANDARDFORM_WIDTH = 500;
	Ext.app.STANDARDFORM_HEIGHT = 300;
	Ext.app.SEARCH_COLUMNS = 2;
	Ext.app.GRID_SEARCH_COMBO_WIDTH = 200;

	Ext.app.SEARCHPICTURE_WIDTH = 700;
	Ext.app.SEARCHPICTURE_HEIGHT = 550;

	Ext.app.TASKBAR_WIDTH = 400;
	Ext.app.TASKBAR_PANEL_WIDTH = 200;
	Ext.app.TASKBAR_HEIGHT = 500;

	Ext.app.FORM_SEARCH_HEIGHT = 250;

	Ext.app.DECIMALS = parseInt("2");
	Ext.app.DEC_POINTS = ",";
	Ext.app.THOUSANDS_SET = ".";
	Ext.app.SYMBOL_LEFT = "";
	Ext.app.SYMBOL_RIGHT = "&euro;";

	Ext.app.client_version = "2594";
	Ext.app.version = "2594";
	Ext.app.APLICATION_TITLE = "Bibliopola 5.0.2594";
	Ext.app.TEXT_CARGANDO = "<img src=\"http://localhost/app/assets/modules/main/images/snake_transparent.gif?1266970012\" style=\"margin-right: 8px;\" align=\"absmiddle\" /> Cargando...";
	Ext.app.DATEFORMATLONG = "d/m/Y G:i:s";
	Ext.app.DATEFORMATSHORT = "d/m/Y";
	Ext.app.DATESTARTDAY = parseInt("1");
	Ext.app.PRINT_CSS = "http://localhost/app/assets/css/print.css?1295397259";
	Ext.app.EDITOR_CSS = "http://localhost/app/assets/css/mailing.css?1272886820";

	Ext.app.TIMEOUTREMOTECALL = parseInt("60000");
	Ext.app.TIMEOUTREMOTECALLMAX = 3000000;
	Ext.app.HOSTNAME = 'dbtest';
	Ext.app.DATABASE = 'Bibliopola';
	Ext.app.PAGESIZE = "100";
	Ext.app.AUTOCOMPLETELISTSIZE = "20";
	Ext.app.BLANK = "http://localhost/app/assets/images/s.gif?1266970010";
	Ext.app.STAYALIVETIME = "5000";
	Ext.app.FACTURAREFRESH = "60000";
	Ext.app.base_url = "http://localhost/app/";
	Ext.app.index_page = "index.php";
	Ext.app.url_suffix = "";

	Ext.app.MARGEN_MINIMO = "5";
	Ext.app.NUM_CEROS_DOCUMENTOS = "8";

	Ext.app.MSG_FLY_ALIGN = "bl-bl";
	Ext.app.FLY_TIME = parseInt("5000");

	Ext.app.PERFIL_GENERAL= 		1;
	Ext.app.PERFIL_ENVIO= 			2;
	Ext.app.PERFIL_FACTURACION= 	3;
	Ext.app.PERFIL_PEDIDO= 		4;
	Ext.app.PERFIL_DEVOLUCION= 	5;
	Ext.app.PERFIL_CONTABILIDAD= 	6;
	Ext.app.PERFIL_FISCAL= 		7;
	Ext.app.PERFIL_RECLAMACIONES= 	8;
	Ext.app.PERFIL_SUSCRIPCIONES= 	9;
	Ext.app.PERFIL_RECLAMACIONESSUSCRIPCIONES= 	10;
	Ext.app.PERFIL_FACTURACIONSUSCRIPCIONES= 		11;
	Ext.app.PERFIL_ENVIOFACTURACION= 	10;

	Ext.app.MODOPAGO_METALICO = 5;
	Ext.app.MODOPAGO_ABONO = 4;
	Ext.app.MODOPAGO_ACUENTA = 6;

	//Comandos de los documentos
	Ext.app.TPV_CMD = '/';
	Ext.app.TPV_GUARDAR = Ext.app.TPV_CMD + 'g';
	Ext.app.TPV_CERRAR = Ext.app.TPV_CMD + 'c';
	Ext.app.TPV_CERRAR_IMPRESO = Ext.app.TPV_CMD + 'cf';
	Ext.app.TPV_CERRAR_TICKET = Ext.app.TPV_CMD + 'ct';
	Ext.app.TPV_OPENBOX = Ext.app.TPV_CMD + 'ob';
	Ext.app.TPV_DESCUENTO = Ext.app.TPV_CMD + 'dto';
	Ext.app.TPV_ADD_ALBARAN = Ext.app.TPV_CMD + 'alb';
	Ext.app.TPV_ADD_PEDIDO_CLIENTE = Ext.app.TPV_CMD + 'ped';

	Ext.app.DOCSCANTIDAD = /^([\+|\-|c|q])\s?(\-?\d+)/;
	Ext.app.DOCSDESCUENTO = /^[\*|d]\s?(\d+)/;
	Ext.app.DOCSREF = /^ref\s?(.*)/;
	Ext.app.DOCSIMPORTE = /^\/\s?\-?([\d\\.\,]+)/;


	Ext.app.PRINT_SERVER_HOST = "localhost";
	Ext.app.PRINT_SERVER_PORT = "1234";
	Ext.app.DRAWER_SERVER_HOST = "localhost";
	Ext.app.DRAWER_SERVER_PORT = "1234";

	Ext.app.MENU_STYLE = "menubar";
	Ext.app.HELP = "doc/wiki/index.php";
	Ext.app.PRINT_TICKET = true;
	Ext.app.NEW_TICKET = true;

	Ext.app.DEFAULT_PAIS = parseInt("48");
	Ext.app.DEFAULT_REGION = parseInt("963");
	Ext.app.MSG_TAGET = "under";

	Ext.app.ITEMS_BUSQUEDA_ARTICULOS = parseInt(100);

	Ext.app.DOMAINS = (""!='')?new String("").split(';'):null;
	Ext.app.REPORTS_LANG = "es;ca;en";
	Ext.app.PRINT_PREVIEW = false;

	Ext.app.GRIDCOLUMNS_HIDE_TPV = "nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;";
	Ext.app.GRIDCOLUMNS_HIDE_ALBARANSALIDA = "nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;";
	Ext.app.GRIDCOLUMNS_HIDE_PEDIDOCLIENTE = "nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;";


	Ext.app.GRIDCOLUMNS_HIDE_PEDIDOPROVEEDOR = "nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;";
	Ext.app.GRIDCOLUMNS_HIDE_ALBARANENTRADA = "nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;";
	Ext.app.GRIDCOLUMNS_HIDE_DEVOLUCION = "nIdDocumento;nEnFirme;nEnDeposito;cReferencia;nIdLinea;fRecargo;fRecargoImporte;dCreacion;dAct;cCUser;cAUser;";

	Ext.app.LOADINGMASKALIGN = 'top-center';
	Ext.app.LOADINGMASKEFFECT = 'ellipsis';

	Ext.app.SHOW_PORTADA_BUSCAR = true;

	Ext.app.KEYMAP_FORM_CTRL = true;
	Ext.app.KEYMAP_FORM_ALT = false;
	Ext.app.KEYMAP_FORM_SHIFT = false;

	Ext.app.KEYMAP_FORM_PRINT = 'p';
	Ext.app.KEYMAP_FORM_REFRESH = 'r';
	Ext.app.KEYMAP_FORM_SAVE = 'sg';
	Ext.app.KEYMAP_FORM_NEW = 'n';
	Ext.app.KEYMAP_FORM_CLOSEDOC = 'w';

	Ext.app.TAB_SCROLLER_MENU = false;
	Ext.app.TAB_CLOSE_MENU = true;

	Ext.app.LINEASLASTFIRST = true;
	</script>

<script type="text/javascript">
</script>
<?php
if (isset($js_include))
{
	foreach($js_include as $js_file)
	{
		if (isset($js_file[0]))	echo js_asset($js_file[0], isset($js_file[1])?$js_file[1]:null)."\n";
	}
}
?>


<?php if (isset($body)) echo $body;?>

<script language="javascript">
// Constructor de la interfaz ExtJS
Ext.onReady(function() {

	//Ext.app.initApp();
	//Ext.app.auth_reload(true);
	try {
		console.log('Done');
	}
	catch (e)
	{
		console.dir(e);
	}
});
</script>

</body>
</html>
