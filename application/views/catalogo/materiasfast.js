(function(){

    var open_id = "<?php echo $open_id;?>";
    var id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "iconoMateriasTab";

    if (title == '') 
        title = _s('Materías-artículos');
    if (id == '') 
        id = Ext.app.createId();
    
    var select = function(){
        var url = site_url('catalogo/articulo/materias');
        var idl = parseInt(art.getValue());
        var idm = parseInt(materia.getValue());
        console.log(idl, idm);
        if ((idl > 0) && (idm > 0)) {
            grid.getEl().mask(Ext.app.TEXT_CARGANDO);
            Ext.app.callRemote({
                url: url,
                params: {
                    idl: idl,
                    idm: idm
                },
                fnok: function(){
                    grid.getEl().unmask();
                    var reg = {
                        id: Ext.app.createId(),
                        titulo: art.getRawValue(),
                        materia: materia.getRawValue()
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
            Ext.app.msgFly(title, _s('mensaje_falta_materia'));
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
                materia.getStore().load({
                    params: {
                        query: ub,
                        start: 0,
                        limit: Ext.app.AUTOCOMPLETELISTSIZE
                    },
                    callback: function(c){
                        t.setValue();
                        materia.setValue(ub);
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
    
    var materia = new Ext.form.ComboBox(Ext.app.combobox({
        allowBlank: false,
        url: site_url('catalogo/materia/search'),
        label: _s('Materia'),
        anchor: '100%'
    }));
    
    var model = [{
        name: 'id'
    }, {
        name: 'titulo'
    }, {
        name: 'materia'
    }];
    
    var store = new Ext.data.ArrayStore({
        fields: model
    });
    
    var form = new Ext.FormPanel({
        region: 'north',
        baseCls: 'form-materia',
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
        items: [materia, ctl, art]
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
            header: _s('Materia'),
            width: Ext.app.TAM_COLUMN_TEXT*2,
            dataIndex: 'materia',
            sortable: true
        }]
    });
    
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
    
    Ext.app.loadStores([{
        store: materia.store
    }]);
    return panel;
})();
