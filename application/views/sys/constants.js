// Tamaños de columnas

var Ext = Ext || {};
Ext.app = Ext.app || {};

Ext.app.DECIMALS = parseInt("<?php echo $this->config->item('bp.currency.decimals');?>");
Ext.app.DEC_POINTS = "<?php echo $this->config->item('bp.currency.dec_points');?>";
Ext.app.THOUSANDS_SET = "<?php echo $this->config->item('bp.currency.thousands_sep');?>";
Ext.app.SYMBOL_LEFT = "<?php echo $this->config->item('bp.currency.symbol_left');?>";
Ext.app.SYMBOL_RIGHT = "<?php echo $this->config->item('bp.currency.symbol_right');?>";

Ext.app.client_version = "<?php echo CLIENT_REV; ?>";
Ext.app.version = "<?php echo SVN_REV; ?>";
Ext.app.PRINT_CSS = "<?php echo css_asset_url('print.css');?>";
Ext.app.EDITOR_CSS = "<?php echo css_asset_url($this->config->item('bp.editor.css'));?>";
Ext.app.TEXT_CARGANDO = "<img src=\"<?php echo image_asset_url('snake_transparent.gif','main');?>\" style=\"margin-right: 8px;\" align=\"absmiddle\" /> <?php echo $this->lang->line('Cargando'); ?>";

Ext.app.BLANK = "<?php echo image_asset_url('s.gif'); ?>";

Ext.app.base_url = "<?php echo $this->config->item('base_url');?>";
Ext.app.index_page = "<?php echo $this->config->item('index_page');?>";
Ext.app.url_suffix = "<?php echo $this->config->item('url_suffix');?>";

Ext.app.SESSION_NAME = "<?php echo $this->config->item('sess_cookie_name');?>";
Ext.app.SESSION_ID = "<?php echo $this->session->get_session_id();?>";

Ext.app.TAB_SCROLLER_MENU = <?php echo $this->config->item('bp.application.tabscrollmenu')?'true':'false';?>;
Ext.app.TAB_CLOSE_MENU = <?php echo $this->config->item('bp.application.tabclosemenu')?'true':'false';?>;

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
Ext.app.TAM_COLUMN_NUMBER_SHORT = 20;
Ext.app.LABEL_SIZE = 100;

Ext.app.REPOINFOHEIGHT = 300;
Ext.app.PEDIRWIDTH = 800;
Ext.app.MESSAGEERRORWIDTH = 400;
Ext.app.MESSAGEFLYWIDTH = 250;
Ext.app.MESSAGELIGHTBOXWIDTH = 600;
Ext.app.SEARCHFILTERHEIGHT = 200;
Ext.app.MESSAGEFLYWIDTHPEDIDOS = 500

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


Ext.app.TIMEOUTREMOTECALLMAX = 3000000;
Ext.app.HOSTNAME = 'dbtest';
Ext.app.DATABASE = 'Bibliopola';

Ext.app.PERFIL_GENERAL= 		1;
Ext.app.PERFIL_ENVIO= 			2;
Ext.app.PERFIL_FACTURACION= 	3;
Ext.app.PERFIL_PEDIDO= 		4;
Ext.app.PERFIL_DEVOLUCION= 	5;
Ext.app.PERFIL_CONTABILIDAD= 	6;
Ext.app.PERFIL_FISCAL=	 7;
Ext.app.PERFIL_RECLAMACIONES= 	8;
Ext.app.PERFIL_SUSCRIPCIONES= 	9;
Ext.app.PERFIL_RECLAMACIONESSUSCRIPCIONES= 	10;
Ext.app.PERFIL_FACTURACIONSUSCRIPCIONES= 		11;
Ext.app.PERFIL_ENVIOFACTURACION= 	12;
Ext.app.PERFIL_DIRIGIDO = 		13;

Ext.app.MODOPAGO_METALICO = 5;
Ext.app.MODOPAGO_ABONO = 4;
Ext.app.MODOPAGO_ACUENTA = 6;
Ext.app.MODOPAGO_AMEXDINERS = 9;
Ext.app.MODOPAGO_CHEQUE = 3;
Ext.app.MODOPAGO_DATAFONOECOMMERCE = 1;
Ext.app.MODOPAGO_METÁLICO =	5;
Ext.app.MODOPAGO_NODEFINIDO = 10;
Ext.app.MODOPAGO_REEMBOLSO = 7;
Ext.app.MODOPAGO_TARJETA =	2;
Ext.app.MODOPAGO_TRANSFERENCIA =8;


//Comandos de los documentos
Ext.app.TPV_CMD = '/';
Ext.app.TPV_CMD2 = '.';
Ext.app.TPV_HELP = '\\?';
Ext.app.TPV_GUARDAR = Ext.app.TPV_CMD + 'g';
Ext.app.TPV_CERRAR = Ext.app.TPV_CMD + 'c';
Ext.app.TPV_CERRAR_IMPRESO = Ext.app.TPV_CMD + 'cf';
Ext.app.TPV_CERRAR_TICKET = Ext.app.TPV_CMD + 'ct';
Ext.app.TPV_CERRAR_TICKET_FACTURA = Ext.app.TPV_CMD + 'ctf';
Ext.app.TPV_CERRAR_NOTICKET = Ext.app.TPV_CMD + 'cn';
Ext.app.TPV_OPENBOX = Ext.app.TPV_CMD + 'ob';
Ext.app.TPV_DESCUENTO = Ext.app.TPV_CMD + 'dto';
Ext.app.TPV_ADD_ALBARAN = Ext.app.TPV_CMD + 'alb';
Ext.app.TPV_ADD_PEDIDO_CLIENTE = Ext.app.TPV_CMD + 'ped';

Ext.app.DOCSCANTIDAD = /^([\+|\-|c|q])\s?(\-?\d+)/;
Ext.app.DOCSDESCUENTO = /^[\*|d]\s?(\d+)/;
Ext.app.DOCSREF = /^ref\s?(.*)/;
Ext.app.DOCSIMPORTE = /^\/\s?\-?([\d\\.\,]+)/;

Ext.app.LOADINGMASKALIGN = 'top-center';
Ext.app.LOADINGMASKEFFECT = 'ellipsis';


Ext.app.KEYMAP_FORM_CTRL = true;
Ext.app.KEYMAP_FORM_ALT = false;
Ext.app.KEYMAP_FORM_SHIFT = false;

Ext.app.KEYMAP_FORM_PRINT = 'p';
Ext.app.KEYMAP_FORM_REFRESH = 'r';
Ext.app.KEYMAP_FORM_SAVE = 'sg';
Ext.app.KEYMAP_FORM_NEW = 'n';
Ext.app.KEYMAP_FORM_CLOSEDOC = 'w';

Ext.app.LINEASLASTFIRST = true;

Ext.app.FACTURAREFRESH = 60000;

Ext.app.CONCURSOS = <?php echo $this->config->item('bp.concursos.install')?'true':'false';?>;
