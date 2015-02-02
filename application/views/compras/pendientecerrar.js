(function(){

    var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
    if (title == '') 
        title = _s('Pedidos pendientes de cerrar');
    if (icon == '') 
        icon = 'iconoPendientesCerrarTab';
    if (form_id == '') 
        form_id = Ext.app.createId();
    
    var model = [{
        name: 'id',
        type: 'int',
        column: {
            header: _s('Id'),
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'nIdProveedor'
    }, {
        name: 'nIdPedido',
        type: 'int',
        column: {
            header: _s('nIdPedido'),
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'cProveedor',
        column: {
            header: _s('Proveedor'),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }, {
        name: 'nLibros',
        type: 'int',
        column: {
            align : 'right',
            header: _s('Unidades'),
            width: Ext.app.TAM_COLUMN_NUMBER,
            sortable: true
        }
    }, {
        name: 'fTotal',
        type: 'int',
        column: {
            header: _s('Total'),
            width: Ext.app.TAM_COLUMN_NUMBER,
            renderer : Ext.app.numberFormatter,
            align : 'right',
            sortable: true
        }
    }, {
        name: 'dCreacion',
        column: {
            header: _s('dCreacion'),
            width: Ext.app.TAM_COLUMN_DATE,
            renderer: Ext.app.renderDateShort,
            sortable: true
        }
    }, {
        name: 'nDias',
        column: {
            align : 'right',
            header: _s('Días'),
            width: Ext.app.TAM_COLUMN_NUMBER,
            sortable: true
        }
    }, {
        name: 'cRefInterna',
        column: {
            header: _s('cRefInterna'),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }];
    
    var proveedor = new Ext.form.ComboBox(Ext.app.autocomplete({
        url: site_url('proveedores/proveedor/search'),
        width: 400
    }));
    
    var reload = function(idp){
        if (idp == null) {
            idp = Ext.app.getIdCombo(proveedor);
        }
        
        var grid = Ext.getCmp(form_id + '_grid');
        grid.store.baseParams = {
            start: 0,
            limit: Ext.app.PAGESIZE,
            idp: idp,
            sort: 'cProveedor'
        };
        grid.store.load();
    }
    
    var accion = function(url){
        var grid = Ext.getCmp(form_id + '_grid');
        var codes = Ext.app.gridGetChecked(grid);
		if(codes == null) {
			var record = cm_lineas.getItemSelect();
			if(record != null) {
				codes = record.data.nIdLinea + ';';
			}
		}
        if (codes == null) {
            Ext.app.msgFly(title, _s('no-pedidos-marcados'));
            return;
        }
        grid.getEl().mask();
        Ext.app.callRemote({
            url: url,
            timeout: false,
            wait: true,
            params: {
                id: codes
            },
            fnok: function(){
                grid.getEl().unmask();
                grid.store.load();
                grid.getSelectionModel().deselectRange(0, grid.store.getTotalCount());
            },
            fnnok: function(){
                grid.getEl().unmask();
            }
        });
    }
    
    var cerrarenviar = function(){
        var grid = Ext.getCmp(form_id + '_grid');
        var codes = Ext.app.gridGetChecked(grid);
        if (codes == null) {
            Ext.app.msgFly(title, _s('no-pedidos-marcados'));
            return;
        }
        var url = site_url('compras/pedidoproveedor/cerrar');
        grid.getEl().mask();
        Ext.app.callRemote({
            url: url,
            timeout: false,
            wait: true,
            params: {
                id: codes
            },
            fnok: function(){
                var url = site_url('compras/pedidoproveedor/send');
                Ext.app.callRemote({
                    url: url,
                    timeout: false,
                    wait: true,
                    params: {
                        id: codes
                    },
                    fnok: function(){
                        grid.getEl().unmask();
                        grid.store.load();
                        grid.getSelectionModel().deselectRange(0, grid.store.getTotalCount());
                    },
                    fnnok: function(){
                        grid.getEl().unmask();
                        grid.store.load();
                    }
                });
            },
            fnnok: function(){
                grid.getEl().unmask();
                grid.store.load();
            }
        });
    }
    
    var tbar = [{
        xtype: 'label',
        html: _s('Proveedor')
    }, proveedor, '-', {
        tooltip: _s('cmd-actualizar'),
        text: _s('Actualizar'),
        iconCls: 'icon-actualizar',
        handler: function(){
            reload();
        }
    }];
    
    var panel = Ext.app.createFormGrid({
        model: model,
        checkbox: true,
        show_filter: false,
        timeout: false,
        id: form_id,
        title: title,
        icon: icon,
        idfield: 'id',
        urlget: site_url('compras/pedidoproveedor/get_pendientecerrar'),
        //loadstores: stores,
        fn_pre: null,
        fn_add: null,
        tbar: tbar,
        load: false
    });
    
    var grid = Ext.getCmp(form_id + '_grid');
    
    var cm_lineas = fn_contextmenu();
    
    var ctxRow = null;
    var contextmenu = new Ext.menu.Menu({
        allowOtherMenus: false,
        items: [{
            text: _s('Ver pedido'),
            handler: function(){
                try {
                    if (ctxRow) {
                        Ext.app.execCmd({
                            url: site_url('compras/pedidoproveedor/index/' + ctxRow.data.nIdPedido)
                        });
                    }
                } 
                catch (e) {
                    console.dir(e);
                }
            },
            iconCls: 'iconoPedidoProveedor'
        }]
    });
    
    grid.on('rowcontextmenu', function(grid, rowIndex, e){
        e.stopEvent();
        ctxRow = grid.store.getAt(rowIndex);
        contextmenu.showAt(e.getXY());
    });
    
    cm_lineas.setContextMenu(contextmenu)
    
    var m_pedido = contextmenu.add({
        text: _s('Ver proveedor'),
        handler: function(){
            if (ctxRow) {
                Ext.app.execCmd({
                    url: site_url('proveedores/proveedor/index/' + ctxRow.data.nIdProveedor)
                });
            }
        },
        iconCls: 'iconoProveedores'
    });
    
    addMenuSeparator(cm_lineas);
    
    contextmenu.add({
        iconCls: "icon-generar-doc",
        text: _s('Cerrar'),
        handler: function(){
            accion(site_url('compras/pedidoproveedor/cerrar'));
        }
    });
    contextmenu.add({
        iconCls: "icon-send",
        text: _s('Cerrar y enviar'),
        handler: function(){
            cerrarenviar();
        }
    });
    
    contextmenu.add('-');
    contextmenu.add({
        iconCls: "icon-delete",
        text: _s('Eliminar'),
        handler: function(){
            accion(site_url('compras/pedidoproveedor/del'));
        }
    });
    
    
    panel.on('afterrender', function(p){
        var map = new Ext.KeyMap(p.getEl(), [{
            key: [10, 13],
            ctrl: true,
            stopEvent: true,
            fn: function(){
                reload();
            }
        }]);
    });
    
    return panel;
})();
