(function(){
    /**
     * Elimina la factura del listado
     * @param {Object} grid
     * @param {Object} compra
     */
    function eliminar(grid, compra){
        var sel = grid.getSelectionModel().getSelections();
        var codes = '';
        for (var i = 0; i < sel.length; i = i + 1) {
            codes += sel[i].data.id + '_' + sel[i].data.nIdSuscripcion + ';';
        }
        if (sel.length == 0) {
            Ext.app.msgFly("<?php echo $title;?>", "<?php echo $this->lang->line('no-items-marcados'); ?>");
            return;
        }
        
        Ext.MessageBox.show({
            msg: "<?php echo $title;?>",
            width: 300,
            wait: true,
            icon: 'ext-mb-download'
        });
        
        var fnok = function(obj){
            Ext.MessageBox.hide();
            if (obj.success) {
                for (var i = 0; i < sel.length; i = i + 1) {
                    // storeVentas.remove(sel[i]);
                    reload();
                }
            }
        };
        
        var fnnok = function(){
            Ext.MessageBox.hide();
        };
        
        var url = '';
        if (compra) 
            url = site_url('suscripciones/oltpsuscripcion/del_compra');
        else 
            url = site_url('suscripciones/oltpsuscripcion/del_venta');
        
        Ext.app.callRemote({
            url: url,
            title: "<?php echo $title;?>",
            errormessage: "<?php echo $this->lang->line('registro_error'); ?>",
            params: {
                ids: codes
            },
            fnok: fnok,
            fnnok: fnnok
        });
        
    }
    
    /**
     * Función de carga de los datos
     */
    var reload = function(){
        var d = Ext.getCmp("<?php echo $id;?>_fecha").getRawValue();
        
        if (d == '') {
            Ext.app.msgFly("<?php echo $title;?>", "<?php echo $this->lang->line('mensaje_faltan_datos'); ?>");
            return;
        }
        
        storeCompras.baseParams = {
            fecha: d
        };
        storeCompras.load({
            params: {
                fecha: d
            }
        });
        
        storeComprasSinVenta.baseParams = {
            fecha: d
        };
        storeComprasSinVenta.load({
            params: {
                fecha: d
            }
        });
        
        storeVentas.baseParams = {
            fecha: d
        };
        storeVentas.load({
            params: {
                fecha: d
            }
        });
    }
    
    /**
     * Crea el corte de operaciones
     */
    var crearcorte = function(){
        var f = this;
        Ext.app.callRemoteAsk({
            url: "<?php echo site_url('suscripciones/oltpsuscripcion/update_corte');?>",
            title: "<?php echo $title;?>",
            askmessage: "<?php echo $this->lang->line('actualizar-corte'); ?>"
        });
    }
    
    /**
     * Modelo Compras anticipadas
     */
    var modelCompras = [{
        name: 'id',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Id'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'nIdFactura'
    }, {
        name: 'Factura',
        column: {
            header: "<?php echo $this->lang->line('Factura'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            // id : 'descripcion',
            sortable: true
        }
    }, {
        name: 'Fecha',
        column: {
            header: "<?php echo $this->lang->line('Fecha'); ?>",
            width: Ext.app.TAM_COLUMN_DATE,
            sortable: true
        }
    }, {
        name: 'nCantidad',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Cantidad'); ?>",
            width: Ext.app.TAM_COLUMN_NUMBER,
            sortable: true
        }
    }, {
        name: 'fTotal',
        type: 'float',
        column: {
            header: "<?php echo $this->lang->line('Total'); ?>",
            width: Ext.app.TAM_COLUMN_NUMBER,
            renderer: Ext.app.euroFormatter,
            sortable: true
        }
    }, {
        name: 'nIdSuscripcion',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Suscripcion'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'nIdAlbaranEntrada',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Albaran'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'FechaProveedor',
        column: {
            header: "<?php echo $this->lang->line('Fecha Proveedor'); ?>",
            width: Ext.app.TAM_COLUMN_DATE,
            sortable: true
        }
    }, {
        name: 'ImporteCompra',
        type: 'float',
        column: {
            header: "<?php echo $this->lang->line('Importe Compra'); ?>",
            width: Ext.app.TAM_COLUMN_NUMBER,
            renderer: Ext.app.euroFormatter,
            sortable: true
        }
    }, {
        name: 'Cargos',
        type: 'float',
        column: {
            header: "<?php echo $this->lang->line('Cargos'); ?>",
            width: Ext.app.TAM_COLUMN_NUMBER,
            sortable: true
        }
    }, {
        name: 'RefAlbaran',
        column: {
            header: "<?php echo $this->lang->line('Ref. Albaran'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }, {
        name: 'CreacionAlbaran',
        column: {
            header: "<?php echo $this->lang->line('Fecha Albaran'); ?>",
            width: Ext.app.TAM_COLUMN_DATE,
            sortable: true
        }
    }, {
        name: 'nIdLibro',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Libro'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'cTitulo',
        column: {
            header: "<?php echo $this->lang->line('Titulo'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }, {
        name: 'nIdCliente',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Id.Cliente'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'cCliente',
        column: {
            header: "<?php echo $this->lang->line('Cliente'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }, {
        name: 'nIdProveedor',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Id.Proveedor'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'cProveedor',
        column: {
            header: "<?php echo $this->lang->line('Proveedor'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }];
    
    /**
     * Modelo Compras sin venta
     */
    var modelComprasSinVenta = [{
        name: 'id',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Albarán'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'AlbaranProveedor',
        type: 'string',
        column: {
            header: "<?php echo $this->lang->line('Alb. Proveedor'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }, {
        name: 'FechaProveedor',
        column: {
            header: "<?php echo $this->lang->line('Fecha Proveedor'); ?>",
            width: Ext.app.TAM_COLUMN_DATE,
            sortable: true
        }
    }, {
        name: 'ImporteCompra',
        type: 'float',
        column: {
            header: "<?php echo $this->lang->line('Importe Compra'); ?>",
            width: Ext.app.TAM_COLUMN_NUMBER,
            renderer: Ext.app.euroFormatter,
            sortable: true
        }
    }, {
        name: 'Cargos',
        type: 'float',
        column: {
            header: "<?php echo $this->lang->line('Cargos'); ?>",
            width: Ext.app.TAM_COLUMN_NUMBER,
            renderer: Ext.app.euroFormatter,
            sortable: true
        }
    }, {
        name: 'nIdSuscripcion',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Suscripcion'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'RefAlbaran',
        column: {
            header: "<?php echo $this->lang->line('Ref. Albaran'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }, {
        name: 'CreacionAlbaran',
        column: {
            header: "<?php echo $this->lang->line('Fecha Albaran'); ?>",
            width: Ext.app.TAM_COLUMN_DATE,
            sortable: true
        }
    }, {
        name: 'nIdLibro',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Libro'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'cTitulo',
        column: {
            header: "<?php echo $this->lang->line('Titulo'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }, {
        name: 'nIdCliente',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Id.Cliente'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'cCliente',
        column: {
            header: "<?php echo $this->lang->line('Cliente'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }, {
        name: 'nIdProveedor',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Id.Proveedor'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'cProveedor',
        column: {
            header: "<?php echo $this->lang->line('Proveedor'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }];
    
    /**
     * Modelo Ventas anticipadas
     */
    var modelVentas = [{
        name: 'id',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Id'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'nIdFactura',
        type: 'int'
    }, {
        name: 'Factura',
        column: {
            header: "<?php echo $this->lang->line('Factura'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            // id : 'descripcion',
            sortable: true
        }
    }, {
        name: 'Fecha',
        column: {
            header: "<?php echo $this->lang->line('Fecha'); ?>",
            width: Ext.app.TAM_COLUMN_DATE,
            sortable: true
        }
    }, {
        name: 'nCantidad',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Cantidad'); ?>",
            width: Ext.app.TAM_COLUMN_NUMBER,
            sortable: true
        }
    }, {
        name: 'fTotal',
        type: 'float',
        column: {
            header: "<?php echo $this->lang->line('Total'); ?>",
            width: Ext.app.TAM_COLUMN_NUMBER,
            align: 'right',
            renderer: Ext.app.euroFormatter,
            sortable: true
        }
    }, {
        name: 'nIdSuscripcion',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Suscripcion'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'nIdAlbaranEntrada',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Albaran'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'cRefInterna',
        column: {
            header: "<?php echo $this->lang->line('Ref. Interna'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            // id : 'descripcion',
            sortable: true
        }
    }, {
        name: 'cNumeroAlbaran',
        column: {
            header: "<?php echo $this->lang->line('Alb. Proveedor'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }, {
        name: 'FechaProveedor',
        column: {
            header: "<?php echo $this->lang->line('Fecha Proveedor'); ?>",
            width: Ext.app.TAM_COLUMN_DATE,
            sortable: true
        }
    }, {
        name: 'ImporteCompra',
        type: 'float',
        column: {
            header: "<?php echo $this->lang->line('Importe Compra'); ?>",
            width: Ext.app.TAM_COLUMN_NUMBER,
            renderer: Ext.app.euroFormatter,
            sortable: true
        }
    }, {
        name: 'Cargos',
        type: 'float',
        column: {
            header: "<?php echo $this->lang->line('Cargos'); ?>",
            width: Ext.app.TAM_COLUMN_NUMBER,
            renderer: Ext.app.euroFormatter,
            sortable: true
        }
    }, {
        name: 'RefAlbaran',
        column: {
            header: "<?php echo $this->lang->line('Ref. Albaran'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }, {
        name: 'CreacionAlbaran',
        column: {
            header: "<?php echo $this->lang->line('Fecha Albaran'); ?>",
            width: Ext.app.TAM_COLUMN_DATE,
            sortable: true
        }
    }, {
        name: 'nIdLibro',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Libro'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'cTitulo',
        column: {
            header: "<?php echo $this->lang->line('Titulo'); ?>",
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }, {
        name: 'nIdCliente',
        type: 'int',
        column: {
            header: "<?php echo $this->lang->line('Id.Cliente'); ?>",
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'cCliente',
        column: {
            header: _s('Cliente'),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }
    }, {
        name: 'nIdProveedor',
        type: 'int',
        column: {
            header: _s('Id.Proveedor'),
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
    }];
    
    /**
     * Store de compras anticipadas
     */
    var storeCompras = Ext.app.createStore({
        model: modelCompras,
        url: site_url('suscripciones/oltpsuscripcion/compras_anticipadas'),
        id: 'nIdSuscripcion',
        groupField: 'cCliente',
        sortInfo: 'cCliente',
        remotesort: false
    });
    
    /**
     * Store de ventas
     */
    var storeVentas = Ext.app.createStore({
        model: modelVentas,
        url: site_url('suscripciones/oltpsuscripcion/ventas_anticipadas'),
        id: 'nIdSuscripcion',
        groupField: 'cCliente',
        sortInfo: 'cCliente',
        remotesort: false
    });
    
    /**
     * Crea el store del Grid de comrpas sin venta
     */
    var storeComprasSinVenta = Ext.app.createStore({
        model: modelCompras,
        url: site_url('suscripciones/oltpsuscripcion/compras_sin_venta'),
        id: 'nIdSuscripcion',
        groupField: 'cCliente',
        sortInfo: 'cCliente',
        remotesort: false
    });
    
    /**
     * Grid de compras
     */
    var gridCompras = Ext.app.createGrid({
        id: "<?php echo $id;?>_compras",
        title: "<?php echo $this->lang->line('Compras Anticipadas'); ?>",
        icon: 'icon-grid',
        pages: false,
        store: storeCompras,
        model: modelCompras,
        grouping: true,
        rownumber: true,
        checkbox: true,
        tbar: [{
            tooltip: "<?php echo $this->lang->line('cmd-marcar-facturas'); ?>",
            text: "<?php echo $this->lang->line('Quitar'); ?>",
            iconCls: 'icon-delete',
            listeners: {
                click: function(){
                    eliminar(gridCompras, true);
                }
            }
        }]
    });
    
    /**
     * Grid de compras sin venta
     */
    var gridComprasSinVenta = Ext.app.createGrid({
        id: "<?php echo $id;?>_comprassinventa",
        title: "<?php echo $this->lang->line('Compras sin Venta'); ?>",
        pages: false,
        icon: 'icon-grid',
        store: storeComprasSinVenta,
        model: modelComprasSinVenta,
        grouping: true,
        rownumber: true,
        checkbox: true,
        tbar: [{
            tooltip: "<?php echo $this->lang->line('cmd-marcar-facturas'); ?>",
            text: "<?php echo $this->lang->line('Quitar'); ?>",
            iconCls: 'icon-delete',
            listeners: {
                click: function(){
                    eliminar(gridComprasSinVenta, true);
                }
            }
        }]
    });
    
    /**
     * Grid de ventas
     */
    var gridVentas = Ext.app.createGrid({
        id: "<?php echo $id;?>_ventas",
        title: "<?php echo $this->lang->line('Ventas Anticipadas'); ?>",
        pages: false,
        icon: 'icon-grid',
        store: storeVentas,
        model: modelVentas,
        grouping: true,
        rownumber: true,
        checkbox: true,
        tbar: [{
            tooltip: "<?php echo $this->lang->line('cmd-marcar-facturas'); ?>",
            text: "<?php echo $this->lang->line('Quitar'); ?>",
            iconCls: 'icon-delete',
            listeners: {
                click: function(){
                    eliminar(gridVentas, false);
                }
            }
        }]
    });
    
    /**
     * Formulario
     */
    var form = {
        title: "<?php echo $title;?>",
        id: "<?php echo $id;?>",
        region: 'center',
        closable: true,
        iconCls: "<?php echo $icon;?>",
        layout: 'border',
        items: [new Ext.TabPanel({
            xtype: 'tabpanel',
            region: 'center',
            activeTab: 0,
            items: [{
                title: "<?php echo $this->lang->line('Compras Anticipadas'); ?>",
                iconCls: 'icon-grid',
                region: 'center',
                layout: 'fit',
                items: gridCompras
            }, {
                title: "<?php echo $this->lang->line('Compras sin Venta'); ?>",
                iconCls: 'icon-grid',
                region: 'center',
                layout: 'fit',
                items: gridComprasSinVenta
            }, {
                title: "<?php echo $this->lang->line('Ventas Anticipadas'); ?>",
                iconCls: 'icon-grid',
                region: 'center',
                layout: 'fit',
                items: gridVentas
            }]
        })],
        tbar: [{
            xtype: 'tbbutton',
            text: _s('Acciones'),
            menu: [{
                text: _s('Actualizar facturas'),
                handler: crearcorte,
                id: '<?php echo $id;?>_btn_generar'
            }]
        }, '-', {
            text: _s('Fecha'),            
            xtype: 'label'
        
        }, {
            xtype: 'datefield',
            startDay: Ext.app.DATESTARTDAY,
            id: "<?php echo $id;?>_fecha"
        }, '-', {
            tooltip: "<?php echo $this->lang->line('cmd-calcular'); ?>",
            text: "<?php echo $this->lang->line('Calcular'); ?>",
            iconCls: 'icon-refresh',
            listeners: {
                click: reload
            }
        }, '-', {
            text: "<?php echo $this->lang->line('Limpiar'); ?>",
            tooltip: "<?php echo $this->lang->line('cmd-limpiar'); ?>",
            iconCls: 'icon-new',
            id: id + '_btnnew',
            handler: function(f){
                var filter = Ext.getCmp("<?php echo $id;?>").getTopToolbar();
                Ext.app.clearFields(filter);
            }
        }]
    };
    
    return form;
})();
