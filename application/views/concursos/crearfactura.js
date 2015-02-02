(function(){
    var model = [{
        name: 'nIdAlbaran'
    }, {
        name: 'id'
    }, {
        name: 'cBiblioteca'
    }];
    
    var url = site_url("concursos/albaranagrupado/search");
    
    var store = Ext.app.createStore({
        model: model,
        url: url
    });
    store.baseParams = {
        where: 'nIdFactura=NULL'
    }
    
    var sm = new Ext.grid.CheckboxSelectionModel();
    
    var id = Ext.app.createId();
    var columns = [sm, {
        header: _s("Id"),
        width: Ext.app.TAM_COLUMN_ID,
        dataIndex: 'id',
        sortable: true
    }, {
        id: 'descripcion',
        header: _s("cBiblioteca"),
        dataIndex: 'cBiblioteca',
        width: Ext.app.TAM_COLUMN_TEXT,
        sortable: true
    }];
    
    var grid = new Ext.grid.GridPanel({
        store: store,
        anchor: '100% 80%',
        height: 400,
        autoExpandColumn: 'descripcion',
        stripeRows: true,
        loadMask: true,
        sm: sm,
        
        bbar: Ext.app.gridBottom(store, true),
        
        // grid columns
        columns: columns
    });
    
    var controls = [grid];
    
    var controls = [{
        value: DateAdd('d', -1, new Date()),
        fieldLabel: _s('Fecha'),
        name: 'fecha',
        id: id + '_fecha',
        allowBlank: false,
        startDay: Ext.app.DATESTARTDAY,
        xtype: "datefield"
    }, grid];
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        autosize: false,
        height: 500,
        title: _s('Crear factura'),
        fn_ok: function(){
            var sel = grid.getSelectionModel().getSelections();
            var url = site_url('concursos/factura/crear')
            var ids = [];
            Ext.each(sel, function(item){
                ids.push(item.data.id);
            });
            ids = implode(';', ids);
            var d = Ext.getCmp(id + '_fecha');
            Ext.app.callRemote({
                url: url,
                params: {
                    fecha: DateToNumber(d.getValue().getTime()),
                    albaranes: ids
                },
                fnok: function(){
                    form.close();
                }
            })
        }
    });
    
    store.load();
    form.show();
    return;
    
})();
