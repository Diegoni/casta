(function(){
    var id = "<?php echo $id;?>";
    var model = [{
        name: 'nIdLibro'
    }, {
        name: 'id'
    }, {
        name: 'nCantidad'
    }, {
        name: 'nStock'
    }, {
        name: 'cTitulo'
    }, {
        name: 'fPrecioActual'
    }, {
        name: 'fPrecioVenta'
    }, {
        name: 'fPrecioRecomendado'
    }, {
        name: 'fCoste'
    }, {
        name: 'fGastos'
    }, {
        name: 'fIVA'
    }, {
        name: 'nStock'
    }, {
        name: 'nPedidos'
    }, {
        name: 'nIdTipo'
    }, {
        name: 'fPrecio'
    }, {
        name: 'fPrecioAsignado'
    }];
    
    var url = site_url('compras/albaranentrada/get_precios/' + id);
    var store = Ext.app.createStore({
        model: model,
        url: url
    });
    
    var precioEditor = new Ext.form.NumberField({
        allowNegative: false,
        allowDecimals: true,
        selectOnFocus: true
    });
    
    var columns = [{
        header: _s("Id"),
        width: Ext.app.TAM_COLUMN_ID,
        dataIndex: 'nIdLibro',
        hidden: true,
        sortable: true
    }, {
        id: 'descripcion',
        header: _s("cTitulo"),
        dataIndex: 'cTitulo',
        width: Ext.app.TAM_COLUMN_TEXT,
        renderer: function(v, x, r, row, col){
            if (r.data.nPedidos > 0)
                x.css = 'cell-hay-pedidos';
            return v;
        },
        sortable: true
    }, {
        header: _s("nCantidad"),
        dataIndex: 'nCantidad',
        width: Ext.app.TAM_COLUMN_NUMBER_SHORT,
        sortable: true
    }, {
        header: _s("nStock"),
        dataIndex: 'nStock',
        width: Ext.app.TAM_COLUMN_NUMBER_SHORT,
        sortable: true
    }, {
        header: _s("fCoste"),
        dataIndex: 'fCoste',
        width: Ext.app.TAM_COLUMN_NUMBER,
        align: 'right',
        sortable: true
    }, {
        header: _s("fGastos"),
        dataIndex: 'fGastos',
        width: Ext.app.TAM_COLUMN_NUMBER,
        editor: new Ext.form.NumberField({
                allowNegative: false,
                allowDecimals: true,
                selectOnFocus: true
            }),
        align: 'right',
        sortable: true
    }, {
        header: _s("fPrecioActual"),
        dataIndex: 'fPrecioActual',
        align: 'right',
        width: Ext.app.TAM_COLUMN_NUMBER,
        sortable: true
    }, {
        header: _s("fPrecioAlbaran"),
        dataIndex: 'fPrecioVenta',
        align: 'right',
        width: Ext.app.TAM_COLUMN_NUMBER,
        sortable: true
    }, {
        header: _s("fPrecioRecomendado"),
        dataIndex: 'fPrecioRecomendado',
        align: 'right',
        width: Ext.app.TAM_COLUMN_NUMBER,
        sortable: true
    }, {
        header: _s("fPrecioAsignado"),
        dataIndex: 'fPrecioAsignado',
        align: 'right',
        width: Ext.app.TAM_COLUMN_NUMBER,
        editor: precioEditor,
        renderer: function(v, x, r, row, col){
            //console.log('Asignado ' + v + '->' + r.data.fPrecioRecomendado);
            //          if(r != null && x != null)
            if (v > r.data.fPrecioActual)
                x.css = 'cell-precio-up';
            else if (v < r.data.fPrecioActual)
                x.css = 'cell-precio-down';
            else
                x.css = 'cell-docs-pvp';

            return Ext.app.numberFormatter(((v == null || v == '') ? r.data.fPrecioRecomendado : v));

            //return Ext.app.rendererPVP(, x, r, row, col);
        },
        sortable: true
    }, {
        header: _s("fMargen"),
        dataIndex: 'fPrecioAsignado',
        align: 'right',
        width: Ext.app.TAM_COLUMN_NUMBER_SHORT,
        renderer: function(v, x, r, row, col){
            if (r != null) 
                x.css = 'cell-docs-referencia';
            var v = r.data.fPrecioAsignado;
            return Margen((v == null || v == '') ? r.data.fPrecioRecomendado : v, r.data.fCoste).decimal(Ext.app.DECIMALS);
        },
        sortable: true
    }];
    
    var grid = new Ext.grid.EditorGridPanel({
        store: store,
        anchor: '100% 80%',
        //height: 400,
        autoExpandColumn: 'descripcion',
        stripeRows: true,
        loadMask: true,
        
        bbar: Ext.app.gridBottom(store, true),
        
        listeners: {
            celldblclick: function(grid, row, column, e){
                //console.log('DblClick ' + row + ', ' + column);
                var record = grid.store.getAt(row);
                if (column == 6) 
                    record.set('fPrecioAsignado', record.data.fPrecioActual)
                else 
                    if (column == 7) 
                        record.set('fPrecioAsignado', record.data.fPrecioVenta)
                    else 
                        if (column == 8) 
                            record.set('fPrecioAsignado', record.data.fPrecioRecomendado)
                record.commit();
            },
            afteredit : function(e) {
                if(e.field == 'fGastos' && e.originalValue != e.value) {
                    Ext.app.callRemote({
                        url: site_url('compras/albaranentrada/precio'),
                        timeout: false,
                        params: {
                            coste: e.record.data.fCoste,
                            gastos: e.value,
                            id: id,
                            iva: e.record.data.fIVA,
                            tipo : e.record.data.nIdTipo,
                            stock: e.record.data.nStock,
                            precio: e.record.data.fPrecio,
                            actual: e.record.data.fPrecioActual,
                            venta: e.record.data.fPrecioVenta
                        },
                        fnok: function(obj){
                            e.record.set('fPrecioRecomendado', obj.fPrecioRecomendado);
                            e.record.set('fPrecioAsignado', obj.fPrecioAsignado);
                        }
                    });
                }
            }
        },
        
        // grid columns
        columns: columns
    });
    
    var actualizar = new Ext.form.Checkbox({
        fieldLabel: _s('Imprimir etiquetas stock actual'),
        checked: true,
        allowBlank: true
    });
    
    var controls = [grid, actualizar];
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        autosize: false,
        labelWidth: 200,
        height: 500,
        icon: 'icon-precio',
        width: 700,
        title: _s('Actualizar precios'),
        fn_ok: function(){
            var asig = '';
            store.each(function(e){
                //console.dir(e);
                asig += e.data.nIdLibro + '##' + e.data.fPrecioAsignado + '##' + e.data.fGastos + ';';
            });
            var act = actualizar.getValue();
            Ext.app.callRemote({
                url: site_url('compras/albaranentrada/precios'),
                timeout: false,
                params: {
                    id: id,
                    precios: asig,
                    etq: act
                },
                fnok: function(obj){
					var f = Ext.getCmp('<?php echo $cmpid;?>');
					f.refresh();
                    form.close();
                }
            });
        }
    });
    
    store.baseParams = {
        id: id
    };
    
    store.load();
    form.show();
    
    return;
})();
