(function(){

    var open_id = "<?php echo $open_id;?>";
    var id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "iconoUbicarTab";
    if (title == '') 
        title = _s('Ubicar artículos');
    if (id == '') 
        id = Ext.app.createId();
    
    var select = function(){
        var url = site_url('catalogo/articulo/ubicar');
        var idl = parseInt(art.getValue());
        var idu = parseInt(ubicacion.getValue());
        if ((idu > 0) && (idu > 0)) {
            grid.getEl().mask(Ext.app.TEXT_CARGANDO);
            Ext.app.callRemote({
                url: url,
                params: {
                    idl: idl,
                    idu: idu
                },
                fnok: function(){
                    grid.getEl().unmask();
                    var reg = {
                        id: Ext.app.createId(),
                        titulo: art.getRawValue(),
                        ubicacion: ubicacion.getRawValue()
                    }
                    var p = new store.recordType(reg, reg.id)
                    store.insert(0, p);
                    art.setValue();
                },
                fnnok: function(){
                    grid.getEl().unmask();
                }
            });
        }
        else {
            Ext.app.msgFly(title, _s('mensaje_falta_ubicacion'));
            ctl.focus();
        }
    }
    
    var ctl = new Ext.form.TextField({
        enableKeyEvents: true,
        fieldLabel: _s('Id')
    });
    
    ctl.on('keypress', function(t, e){
        if (e.getKey() === e.ENTER) {
            var tx = t.getValue();
            if (tx.substr(0, 1).toLowerCase() == 'u') {
                var ub = tx.substr(1);
                ubicacion.getStore().load({
                    params: {
                        query: ub,
                        start: 0,
                        limit: Ext.app.AUTOCOMPLETELISTSIZE
                    },
                    callback: function(c){
                        t.setValue();
                        ubicacion.setValue(ub);
                    }
                });
            }
            else {
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
        }
    });
    
    var art = new Ext.form.ComboBox(Ext.app.autocomplete({
        allowBlank: false,
        url: site_url('catalogo/articulo/search'),
        label: _s('Artículo'),
        anchor: '100%',
        fnselect: select
    }));
    
    var ubicacion = new Ext.form.ComboBox(Ext.app.autocomplete({
        allowBlank: false,
        url: site_url('catalogo/ubicacion/search'),
        label: _s('Ubicación'),
        anchor: '100%'
    }));
    
    var model = [{
        name: 'id'
    }, {
        name: 'titulo'
    }, {
        name: 'ubicacion'
    }];
    
    var store = new Ext.data.ArrayStore({
        fields: model
    });
    
    var form = new Ext.FormPanel({
        region: 'north',
        baseCls: 'form-ubicaciones',
        heigth: 200,
        labelWidth: 100,
        bodyStyle: 'padding:5px 5px 0',
        defaultType: 'textfield',
        buttons: [{
            text: _s('Limpiar'),
            iconCls: 'icon-clean',
            handler: function(){
                form.getForm().reset();
            }
        }],
        items: [ctl, art, ubicacion]
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
            header: _s('Ubicación'),
            width: Ext.app.TAM_COLUMN_TEXT,
            dataIndex: 'ubicacion',
            sortable: true
        }]
    
    });
    
    /*var tbar = [{
     text: _s('Consultar ajustes de stock'),
     iconCls: 'icon-search',
     handler: fn_consultar
     }];*/
    var tbar = Ext.app.gridStandarButtons({
        title: title,
        id: id + "_grid"
    });

    var contextmenu = Ext.app.addContextMenuLibro(grid, 'id');
    
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
        items: [form, grid]
    });
    
    return panel;
})();
