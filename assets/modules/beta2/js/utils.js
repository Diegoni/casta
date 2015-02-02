window.utils = {

    // Asynchronously load templates located in separate .html files
    loadTemplate: function(views, callback) {

        var deferreds = [];

        $.each(views, function(index, view) {
            if (App.Views[view[1]]) {
                var tpl = site_url('sys/app/tpl/' + view[0] + '::' + view[1]);
                deferreds.push($.get(tpl, function(data) {
                    App.Views[view[1]].prototype.template = _.template(data);
                }));
            } else {
                console.error(view + " not found");
            }
        });

        $.when.apply(null, deferreds).done(callback);
    },

    displayValidationErrors: function (messages) {
        for (var key in messages) {
            if (messages.hasOwnProperty(key)) {
                this.addValidationError(key, messages[key]);
            }
        }
        this.showAlert('Warning!', 'Fix validation errors and try again', 'alert-warning');
    },

    addValidationError: function (field, message) {
        var controlGroup = $('#' + field).parent().parent();
        controlGroup.addClass('error');
        $('.help-inline', controlGroup).html(message);
    },

    removeValidationError: function (field) {
        var controlGroup = $('#' + field).parent().parent();
        controlGroup.removeClass('error');
        $('.help-inline', controlGroup).html('');
    },

    showAlert: function(title, text, klass) {
        $('.alert').removeClass("alert-error alert-warning alert-success alert-info");
        $('.alert').addClass(klass);
        $('.alert').html('<strong>' + title + '</strong> ' + text);
        $('.alert').show();
    },

    hideAlert: function() {
        $('.alert').hide();
    }
};

var Ext = Ext || {};
Ext.app = Ext.app || {};

Ext.app.callRemote = function (config) {
    $.post(config.url, config.params,
        function(data){
            if (data.success) 
            {
                if (config.fnok) 
                    config.fnok(data);
            }
            else
            { 
                if (config.fnnok) 
                    config.fnnok(data);
            }
        }, "json"
    );
}

Ext.app.config_system = new Hash();
Ext.app.config_user = new Hash();

Ext.app.reload_constants = function() {
    /*Ext.app.DECIMALS = parseInt(Ext.app.get_config('bp.currency.decimals'));
    Ext.app.DEC_POINTS = Ext.app.get_config('bp.currency.dec_points');
    Ext.app.THOUSANDS_SET = Ext.app.get_config('bp.currency.thousands_sep');
    Ext.app.SYMBOL_LEFT = Ext.app.get_config('bp.currency.symbol_left');
    Ext.app.SYMBOL_RIGHT = Ext.app.get_config('bp.currency.symbol_right');*/
    Ext.app.APLICATION_TITLE = Ext.app.get_config('bp.application.name');
    Ext.app.DATEFORMATLONG = Ext.app.get_config('bp.date.formatlong');
    Ext.app.DATEFORMATSHORT = Ext.app.get_config('bp.date.format');
    Ext.app.TIMEFORMAT = Ext.app.get_config('bp.date.formattime');
    Ext.app.DATESTARTDAY = parseInt(Ext.app.get_config('bp.date.startday'));

    Ext.app.TIMEOUTREMOTECALL = parseInt(Ext.app.get_config('bp.application.timeout'));
    Ext.app.PAGESIZE = parseInt(Ext.app.get_config('bp.data.limit'));
    Ext.app.AUTOCOMPLETELISTSIZE = Ext.app.get_config('bp.data.search.limit');
    Ext.app.STAYALIVETIME = parseInt(Ext.app.get_config('bp.application.stayalive'));
    //Ext.app.FACTURAREFRESH = parseInt(Ext.app.get_config('bp.portal.portlets.facturasrefresh'));
    Ext.app.MARGEN_MINIMO = parseInt(Ext.app.get_config('bp.ventas.margenminimo'));
    Ext.app.NUM_CEROS_DOCUMENTOS = parseInt(Ext.app.get_config('bp.docs.ceros'));
    Ext.app.MSG_FLY_ALIGN = Ext.app.get_config('bp.application.fly.align');
    Ext.app.FLY_TIME = parseInt(Ext.app.get_config('bp.application.fly'));


    //Ext.app.PRINT_SERVER_HOST = Ext.app.get_config('bp.printerserver.host');
    //Ext.app.PRINT_SERVER_PORT = Ext.app.get_config('bp.printerserver.port');
    //Ext.app.DRAWER_SERVER_HOST = Ext.app.get_config('bp.drawerserver.host');
    //Ext.app.DRAWER_SERVER_PORT = Ext.app.get_config('bp.drawerserver.port');
    //Ext.app.LABEL_SERVER_HOST = Ext.app.get_config('bp.labelserver.host');
    //Ext.app.LABEL_SERVER_PORT = Ext.app.get_config('bp.labelserver.port');

    Ext.app.MENU_STYLE = Ext.app.get_config('bp.application.menustyle');
    Ext.app.HELP = Ext.app.get_config('bp.application.help');
    //Ext.app.PRINT_TICKET = Ext.app.get_config('bp.factura.ticket.print')  == 'true';
    //Ext.app.NEW_TICKET = Ext.app.get_config('bp.factura.ticket.nuevo')  == 'true';

    Ext.app.DEFAULT_PAIS = parseInt(Ext.app.get_config('bp.address.pais'));
    Ext.app.DEFAULT_REGION = parseInt(Ext.app.get_config('bp.address.region'));
    Ext.app.MSG_TAGET = Ext.app.get_config('bp.application.msgtarget');

    Ext.app.ITEMS_BUSQUEDA_ARTICULOS = parseInt(Ext.app.get_config('bp.articulos.busquedas.items'));

    Ext.app.REPORTS_LANG = Ext.app.get_config('reports.language');
    //Ext.app.PRINT_PREVIEW = Ext.app.get_config('reports.preview') == 'true';

    Ext.app.GRIDCOLUMNS_HIDE_FACTURACION = Ext.app.get_config('bp.grid.facturacion.hide');
    Ext.app.GRIDCOLUMNS_HIDE_TPV = Ext.app.get_config('bp.grid.tpv.hide');
    Ext.app.GRIDCOLUMNS_HIDE_ALBARANSALIDA = Ext.app.get_config('bp.grid.albaransalida.hide');
    Ext.app.GRIDCOLUMNS_HIDE_PEDIDOCLIENTE = Ext.app.get_config('bp.grid.pedidocliente.hide');

    Ext.app.GRIDCOLUMNS_HIDE_PEDIDOPROVEEDOR = Ext.app.get_config('bp.grid.pedidoproveedor.hide');
    Ext.app.GRIDCOLUMNS_HIDE_ALBARANENTRADA = Ext.app.get_config('bp.grid.albaranentrada.hide');
    Ext.app.GRIDCOLUMNS_HIDE_DEVOLUCION = Ext.app.get_config('bp.grid.devolucion.hide');

    Ext.app.SHOW_PORTADA_BUSCAR = Ext.app.get_config('catalogo.buscar.showportada', 'user') == 'true';
    Ext.app.PEDIDOS_ACTUALIZAR_PRECIOS = Ext.app.get_config('ventas.pedidocliente.actualizar') == 'true';
    Ext.app.FORMATOABONO = Ext.app.get_config('bp.abono.formatodefecto');

    Ext.app.PRINTETQGROUP = parseInt(Ext.app.get_config('compras.etiquetas.grupos'));

    Ext.app.SIMBOLODIVISA = Ext.app.get_config('bp.divisa.simbolo');
    Ext.app.DIVISA_DEFAULT = Ext.app.get_config('bp.divisa.default');
    Ext.app.TPV_CACHE = Ext.app.get_config('bp.cache.tpv')  == 'true';

    Ext.app.DIASPROMOCIONARTICULO = parseInt(Ext.app.get_config('bp.articulo.diaspromocion'));
    Ext.app.PROMOCIONWEB = Ext.app.get_config('bp.promocion.web');
}

/**
 * Carga la configuración del usuario
 */
Ext.app.config_load = function(fn) {
    Ext.app.callRemote({
        url : site_url('sys/configuracion/config'),
        nomsg : true,
        fnok : function(res) {
            Ext.app.config_system = res.system;
            Ext.app.config_user = res.user;
            Ext.app.reload_constants();
            if (fn != null) fn();
        }
    });
}

/**
 * Devuelve una valor de la configuración
 * @param {String} item Id de la variable
 * @param {String} mode terminal, user, system
 * @param {bool} cascade Solo busca la variable del tipo, no en cascada
 */
Ext.app.get_config = function(item, mode, cascade) {
    if (cascade == null) cascade = true;
    if (item != null) {
        item = item.replace(/\s/g, '.');
        if (mode == 'terminal' || mode == null) {
            var b = localStorage.getItem(item);
            //console.log('LOAD: ' + item + ' -> ' + b);
            if(b != null && b != '') {
                return b;
            }
            return (cascade)?Ext.app.get_config(item, 'user'):new String('');
        }
        if (mode == 'user') {
            if (Ext.app.config_user[item] != null 
                && Ext.app.config_user[item].toString() != '')
                return new String(Ext.app.config_user[item]);
            return (cascade)?Ext.app.get_config(item, 'system'):new String('');
        }
        if (mode == 'system') {
            if(Ext.app.config_system[item] != null)
                return new String(Ext.app.config_system[item]);
            return new String('');
        }
    }
    return Ext.app.config_system;
}

/**
 * Asigna un valor a la configuración
 * @param {Object} item
 */
Ext.app.set_config = function(item, value, mode) {
    item = item.replace(/\s/g, '.');
    if (mode == 'terminal') {
        //console.log('SAVE: ' + item + ' -> ' + value);
        localStorage.setItem(item, value);
    }
    if (mode == 'user'  || mode == null) {
        Ext.app.config_user[item] = value;
        // Guarda en el sistema
        Ext.app.callRemote({
            url : site_url('sys/configuracion/set'),
            nomsg: true,
            params : { 'var': item, value: value, type: 'user'}
        });         
    }
    if (mode == 'system') {
        Ext.app.config_system[item] = value;
    }
    // Recarga las constantes
    //Ext.app.reload_constants();
}
