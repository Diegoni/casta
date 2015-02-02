(function() {

	var form_id = Ext.app.createId();

    var txtISBN = new Ext.form.TextField({
    	fieldLabel: _s('Código'),
        enableKeyEvents : true
    }); 

    txtISBN.on('specialkey',
        function(o, e){
            if (e.getKey() == e.ENTER){
                var idb = biblioteca.getValue();
                var ids = sala.getValue();
                var ido = origen.getValue();
                if (idb < 1) {
                    biblioteca.focus();
                    return;
                }
                /*if (ids < 1) {
                    sala.focus();
                    return;
                }*/
                if (ido < 1) {
                    origen.focus();
                    return;
                }
                try {
                    Ext.app.set_config('bp.concursos.mover.secciondefecto', origen.getValue(), 'user');
                } catch(e) {}

                Ext.app.callRemote({
                    url: site_url('concursos/concurso/add_lineaalbaransalida'),
                    params: {
                        code: txtISBN.getValue(),
                        biblioteca: idb,
                        sala: ids,
                        seccion: ido
                    },
                    fnok: function (res) {
                        grid.store.load();
                        txtISBN.setValue(null);
                        txtISBN.focus();
                    },
                    fnnok: function (res) {  
                        txtISBN.setValue(null);
                        txtISBN.focus();
                    }                                            
                });
            }
        },
        this
    );

    var biblioteca = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('concursos/biblioteca/search'),
        label: _s('Bibliotecas'),
        name: 'biblioteca',
        anchor: '90%'
    }));            
    biblioteca.store.load();

    var sala = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('concursos/sala/search'),
        label: _s('Salas'),
        name: 'sala',
        anchor: '90%'
    }));            
    sala.store.load();

    biblioteca.on('select', function(c, r, i){
        loadlineas()
    });

    sala.on('select', function(c, r, i){
        loadlineas()
    });

    var origen = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/seccion/search'),
        id: id + '_origen',
        anchor: '70%',
        label: _s('Origen')
    }));

    origen.store.load({
        callback: function () {
            var d = parseInt(Ext.app.get_config('bp.concursos.mover.secciondefecto'));
            if (d > 0)
                origen.setValue(parseInt(d));
        }
    });

    var loadlineas = function() {
        var b = biblioteca.getValue();
        var s = sala.getValue();
        if (b > 0 /*&& s > 0*/) {
            grid.store.baseParams = {
                biblioteca: b,
                sala: s
            }
            grid.store.load();
        }
    }

    // Relacionados
    var model = [{
        name : 'nIdLibro',
        column : {
            header : _s("Id"),
            width : Ext.app.TAM_COLUMN_ID,
            dataIndex : 'id',
            sortable : true
        }
    }, {
        name : 'id'
    }, {
        name : 'cAutores',
        column : {
            header : _s("cAutores"),
            width : Ext.app.TAM_COLUMN_TEXT,
            sortable : true
        },
        ro : true
    }, {
        name : 'cTitulo',
        column : {
            header : _s('cTitulo'),
            width : Ext.app.TAM_COLUMN_TEXT,
            id : 'descripcion',
            sortable : true
        },
        ro : true
    }];

    var fn_cerrar = function (nofacturable) {
        var idb = biblioteca.getValue();
        var ids = sala.getValue();
        if (idb < 1) {
            biblioteca.focus();
            return;
        }
        /*if (ids < 1) {
            sala.focus();
            return;
        }*/
        Ext.app.callRemote({
            url: site_url('concursos/concurso/cerrar_albaransalida'),
            params: {
                biblioteca: idb,
                sala: ids,
                nofacturable: nofacturable
            },
            fnok: function (res) {
                loadlineas();
            }                            
        });        
    }

    var lineas = Ext.app.createFormGrid({
        model : model,
        id : form_id + "_libros",
        idfield : 'id',
        show_filter: false,
        urlget : site_url("concursos/concurso/get_lineasalbaransalida"),
        urldel : site_url("ventas/albaransalidalinea/del"),
        anchor : '100% 85%',
        load : false,
        rbar : [{
            text : _s('Actualizar'),
            iconCls : 'icon-actualizar',
            handler : function(button) {
                loadlineas();
            }
        }, {
            text : _s('Cerrar facturable'),
            iconCls : 'icon-generar-doc',
            handler : function(button) {
                fn_cerrar(false);
            }
        }, {
            text : _s('Cerrar no facturable'),
            iconCls : 'icon-generar-doc',
            handler : function(button) {
                fn_cerrar(true);
            }
        }]
    });
    var grid = Ext.getCmp(form_id + '_libros_grid');
    /*
    var cm_lineas = fn_contextmenu();
    var contextmenu = Ext.app.addContextMenuLibro(grid, 'nIdLibro', cm_lineas);
    cm_lineas.setContextMenu(contextmenu)*/

    var controls = [biblioteca, sala, origen, txtISBN, lineas];

    var form = new Ext.FormPanel({
        labelWidth: Ext.app.LABEL_SIZE,
        bodyStyle: 'padding:5px 5px 0',
        defaultType: 'textfield',
        region: 'center',
        closable: true,
        baseCls: 'x-plain',
        frame: true,
        items: [controls]
    });

    var panel = new Ext.Panel({
        layout: 'border',
        title: _s('Albaran de Salida'),
        id: form_id,
        iconCls: 'iconoAlbaranSalidaTab', 
        region: 'center',
        closable: true,
        baseCls: 'x-plain',
        frame: true,
        items: [form]
    });
        
    return panel;
})();