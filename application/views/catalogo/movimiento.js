(function(){

    var open_id = "<?php echo $open_id;?>";
    var id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "iconoSeccionMoverTab";
    if (title == '') 
        title = _s('Mover libros secciones');
    if (id == '') 
        id = Ext.app.createId();
    
    var origen = new Ext.form.ComboBox(Ext.app.combobox({
        id: id + '_origen',
        anchor: '70%',
        label: _s('Origen')
    }));
    
    var destino = new Ext.form.ComboBox(Ext.app.combobox({
        triggerAction: 'all',
        anchor: '70%',
        label: _s('Destino'),
        id: id + '_destino'
    }));
    
    var destinocrear = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/seccion/search'),
        anchor: '70%',
        id: id + '_crear',
        label: _s('Nueva')
    }));
    
    destinocrear.on('select', function(g){
        (g.getValue() > 0) ? destino.disable() : destino.enable();
    });
    
    var cantidad = new Ext.ux.form.Spinner({
        fieldLabel: _s('Cantidad'),
        selectOnFocus: true,
        enableKeyEvents: true,
        id: id + "_cantidad",
        value: 1,
        width: 60,
        strategy: new Ext.ux.form.Spinner.NumberStrategy()
    });
    var ejemplares = new Ext.form.DisplayField({
        cls: 'movimientos-ejemplares-field',
        value: 'ejemplares',
        height: 20,
        anchor: '100%'
    });
    
    var destinos = [];
    var origenes = [];
    var count = 0;
    var count_lineas = 0;
    
    var accion = function(){
        var idl = art.getValue();
        var ido = origen.getValue();
        var idd = destino.getValue();
        var qt = cantidad.getValue();
        var nueva = destinocrear.getValue();
        if (nueva > 0) 
            idd = nueva;
        cantidad.disable();
        Ext.app.callRemote({
            url: site_url('catalogo/movimiento/mover'),
            params: {
                id: idl,
                ido: ido,
                idd: idd,
                cantidad: qt
            },
            fnok: function(obj){
                var reg = {
                    id: idl,
                    titulo: art.getRawValue(),
                    cantidad: qt,
                    origen: origen.getRawValue(),
                    destino: (nueva > 0) ? destinocrear.getRawValue() : destino.getRawValue()
                }
                count += parseInt(qt);
                ++count_lineas;
                store.insert(0, new ComboRecord(reg));
                origenes[origenes.length] = ido;
                destinos[destinos.length] = idd;
                ctl.reset();
                origen.reset();
                destino.reset();
                origen.store.removeAll();
                destino.store.removeAll();
                cantidad.reset();
                art.reset();
                ctl.focus();
                cantidad.enable();
                ejemplares.setValue(sprintf(_s('lineas-ejemplares'), count_lineas, count));
                
            },
            fnnok: function(){
                cantidad.enable();
            }
        });
        
    }
    origen.on('keypress', function(f, e){
        if (e.getKey() == e.ENTER) {
            (destinocrear.getValue() > 0) ? cantidad.focus() : destino.focus();
        }
    });
    
    destino.on('keypress', function(f, e){
        if (e.getKey() == e.ENTER) {
            cantidad.focus();
        }
    });
    
    destinocrear.on('keypress', function(f, e){
        if (e.getKey() == e.ENTER) {
            cantidad.focus();
        }
    });
    
    cantidad.on('keypress', function(f, e){
        if (e.getKey() == e.ENTER) {
            accion();
        }
    });
    
    var select = function(id) {
        if (id == undefined)
            id = art.getValue();
        origen2.load({
            params: {
                where: 'nIdLibro=' + id
            },
            callback: function(data){
                var last_origen = origen.getValue();
                var last_destino = destino.getValue();
                var new_origen = null;
                var new_destino = null;
                origen.store.removeAll();
                destino.store.removeAll();
                Ext.each(data, function(item){
                    var text = item.data.cNombre;
                    var id = item.data.nIdSeccion;
                    var stk = parseInt(item.data.nStockFirme) + parseInt(item.data.nStockDeposito);
                    if (stk > 0) {
                        if (item.data.id == last_origen) 
                            new_origen = last_origen;
                        if (new_origen != last_origen) {
                            if (in_array(id, origenes)) 
                                new_origen = id;
                        }
                        if (new_origen == null) 
                            new_origen = id;
                        
                        Ext.app.comboAdd(origen.store, id, text + '(' + stk + ')');
                    }
                    if (item.data.id == last_destino) 
                        new_destino = last_destino;
                    if (new_destino != last_destino) {
                        if (in_array(id, destinos)) 
                            new_destino = id;
                    }
                    
                    if (new_destino == null) 
                        new_destino = id;
                    Ext.app.comboAdd(destino.store, id, text);
                });
                origen.setValue(new_origen);
                destino.setValue(new_destino);
                origen.focus();
            }
        });
    }
    /*var ctl = new Ext.form.ComboBox(Ext.app.autocomplete({
        allowBlank: false,
        url: site_url('catalogo/articulo/search'),
        label: _s('Artículo'),
        anchor: '100%',
        fnselect: select
    }));*/
    
    var ctl = new Ext.form.TextField({
        enableKeyEvents: true,
        fieldLabel: _s('Id')
    });
    
    ctl.on('keypress', function(t, e){
        if (e.getKey() === e.ENTER) {
            var tx = t.getValue();
            art.store.load({
                params: {
                    query: tx,
                    start: 0,
                    limit: Ext.app.AUTOCOMPLETELISTSIZE
                },
                callback: function(c){
                    t.setValue();
                    if (c.length == 1) {
                        art.setValue(c[0].id);
                        select();
                    }
                    else {
                        art.setValue(tx);                            
                    }
                }
            });
        }
    });
    
    var art = new Ext.form.ComboBox(Ext.app.autocomplete({
        allowBlank: false,
        url: site_url('catalogo/articulo/search'),
        label: _s('Artículo'),
        anchor: '100%',
        fnselect: select
    }));

    var origen2 = Ext.app.createStore({
        url: site_url('catalogo/articuloseccion/get_list'),
        model: [{
            name: 'id'
        }, {
            name: 'nIdLibro'
        }, {
            name: 'nIdSeccion'
        }, {
            name: 'nStock'
        }, {
            name: 'nStockFirme'
        }, {
            name: 'nStockDeposito'
        }, {
            name: 'cNombre'
        }]
    });
    
    var model = [{
        name: 'id'
    }, {
        name: 'titulo'
    }, {
        name: 'origen'
    }, {
        name: 'destino'
    }, {
        name: 'cantidad'
    }];
    
    var store = new Ext.data.ArrayStore({
        fields: model
    });
    
    var form = new Ext.FormPanel({
        region: 'north',
        baseCls: 'form-movimiento',
        height: 175,
        labelWidth: 100,
        bodyStyle: 'padding:5px 5px 0',
        defaultType: 'textfield',
        buttons: [{
            text: _s('Aceptar'),
            iconCls: 'icon-accept-form',
            handler: function(b){
				accion();
            }
        }, {
            text: _s('Limpiar'),
            iconCls: 'icon-trash',
            handler: function(){
                destinos = [];
                origenes = [];
                form.getForm().reset();
            }
        }],
        items: [ctl, art, origen, destino, destinocrear, cantidad]
    });
    
    var grid = new Ext.grid.GridPanel({
        region: 'center',
        autoExpandColumn: "descripcion",
        loadMask: true,
        stripeRows: true,
        store: store,
        id: id + "_grid",
        columns: [{
            header: _s('Id'),
            width: Ext.app.TAM_COLUMN_ID,
            dataIndex: 'id',
            sortable: true,
            hidden: true
        }, {
            header: _s('cTitulo'),
            width: Ext.app.TAM_COLUMN_TEXT,
            id: 'descripcion',
            dataIndex: 'titulo',
            sortable: true
        }, {
            header: _s('Origen'),
            width: Ext.app.TAM_COLUMN_TEXT,
            dataIndex: 'origen',
            sortable: true
        }, {
            header: _s('Destino'),
            width: Ext.app.TAM_COLUMN_TEXT,
            dataIndex: 'destino',
            sortable: true
        }, {
            header: _s('Cantidad'),
            width: Ext.app.TAM_COLUMN_MONEY,
            dataIndex: 'cantidad',
            sortable: true
        }]
    
    });

    //var grid = Ext.getCmp(form_id + '_libros_grid');
    var contextmenu = Ext.app.addContextMenuLibro(grid, 'id');
    
    var fn_consultar = function(){
        var url = site_url('catalogo/movimiento/consultar');
        Ext.app.execCmd({
            url: url
        });
    }
    
    var tbar = [{
        text: _s('Consultar movimientos sección'),
        iconCls: 'icon-search',
        handler: fn_consultar
    }];
    
    tbar = tbar.concat(Ext.app.gridStandarButtons({
        title: title,
        id: id + "_grid"
    }));
    
    var panel = new Ext.Panel({
        layout: 'border',
        title: title,
        id: id,
        iconCls: icon,
        region: 'center',
        closable: true,
        baseCls: 'x-plain',
        frame: true,
        tbar: tbar,
        bbar: [ejemplares],
        listeners: {
            afterrender: function(p){
                ctl.focus();
                if (open_id != '') {
                    ctl.setValue(parseInt(open_id));
                }
            },
            afterlayout : function() {
                ctl.focus();
            }
        },
        
        items: [form, grid]
    });
    
    Ext.app.loadStores([{
        store: destinocrear.store
    }]);
    return panel;
})();
