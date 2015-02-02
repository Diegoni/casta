(function(){

    var open_id = "<?php echo $open_id;?>";
    var id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "iconoRegulacionStockTab";
    if (title == '') 
        title = _s('Regulación Stock');
    if (id == '') 
        id = Ext.app.createId();
    
    var motivomas = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('stocks/tiporegulacion/entrada'),
        name: id + '_mas',
        anchor: '100%',
        label: _s('Motivo Mas')
    }));
    
    var motivomenos = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('stocks/tiporegulacion/salida'),
        anchor: '100%',
        name: id + '_menos',
        label: _s('Motivo Menos')
    }));
    
    var select = function(id){
        //console.log('Art ' + id);
        secciones.load({
            params: {
                where: 'nIdLibro=' + id
            },
            callback: function(){
                secciones.focus();
            }
        });        
    }
	
    var ctl = new Ext.form.ComboBox(Ext.app.autocomplete({
        allowBlank: false,
        url: site_url('catalogo/articulo/search'),
        label: _s('Artículo'),
        anchor: '100%',
        fnselect: select
    }));
    
    var secciones = Ext.app.createStore({
        url: site_url('catalogo/articuloseccion/get_list'),
        model: [{
            name: 'id'
        }, {
            name: 'nIdLibro'
        }, {
            name: 'nIdSeccion'
        }, {
            name: 'nStockFirme'
        }, {
            name: 'nStockDeposito'
        }, {
            name: 'cNombre'
        }]
    });
    
    var stocks = new Ext.grid.EditorGridPanel({
        region: 'center',
        autoExpandColumn: "descripcion",
        loadMask: true,
        stripeRows: true,
        store: secciones,
        id: id + "_secc",
        anchor: '100% 50%',
        columns: [{
            header: _s('Id'),
            width: Ext.app.TAM_COLUMN_ID,
            dataIndex: 'id',
            hidden: true,
            sortable: true
        }, {
            header: _s('Sección'),
            width: Ext.app.TAM_COLUMN_TEXT,
            id: 'descripcion',
            dataIndex: 'cNombre',
            sortable: true
        }, {
            header: _s('Firme'),
            width: Ext.app.TAM_COLUMN_NUMBER,
            dataIndex: 'nStockFirme',
            editor: new Ext.form.NumberField({
                listeners: {
                    focus: function(f){
                        f.selectText();
                    }
                }
            }),
            sortable: true
        }, {
            header: _s('Depósito'),
            width: Ext.app.TAM_COLUMN_NUMBER,
            editor: new Ext.form.NumberField({
                listeners: {
                    focus: function(f){
                        f.selectText();
                    }
                }
            }),
            dataIndex: 'nStockDeposito',
            sortable: true
        }]
    });
    
    stocks.on('afteredit', function(e){
        var params = {
            id: e.record.data.id,
            motivomas: parseInt(motivomas.getValue()),
            motivomenos: parseInt(motivomenos.getValue())
        };
        if (e.field == 'nStockFirme') 
            params['firme'] = e.value;
        if (e.field == 'nStockDeposito') 
            params['deposito'] = e.value;
        
        var url = site_url('stocks/arreglostock/arreglar');
        
        Ext.app.onAfterEdit(e, params, url, _s('Regulación Stock'), function(res){
            var reg = {
                id: e.record.data.nIdLibro,
                titulo: ctl.getRawValue(),
                cantidad: e.value,
                motivo: res.message
            }
            store.insert(0, new ComboRecord(reg));
        });
    });
    
    var model = [{
        name: 'id'
    }, {
        name: 'titulo'
    }, {
        name: 'motivo'
    }, {
        name: 'cantidad'
    }];
    
    var store = new Ext.data.ArrayStore({
        fields: model
    });
    
    var form = new Ext.FormPanel({
        region: 'west',
        baseCls: 'form-arreglostock',
        width: 500,
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
        items: [ctl, motivomas, motivomenos, stocks]
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
            header: _s('Acción'),
            width: Ext.app.TAM_COLUMN_TEXT,
            dataIndex: 'motivo',
            sortable: true
        }, {
            header: _s('Cantidad'),
            width: Ext.app.TAM_COLUMN_MONEY,
            dataIndex: 'cantidad',
            sortable: true
        }]
    
    });
    
    var fn_consultar = function(){
        var url = site_url('stocks/arreglostock/consultar');
        Ext.app.execCmd({
            url: url
        });        
    }
    
    var tbar = [{
        text: _s('Consultar ajustes de stock'),
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
        listeners: {
            afterrender: function(p){
                if (open_id != '') {
                    ctl.setValue(parseInt(open_id));
                }
            }
        },
        
        items: [form, grid]
    });
    
    Ext.app.loadStores([{
        store: motivomas.store
    }, {
        store: motivomenos.store
    }]);
    return panel;
})();
