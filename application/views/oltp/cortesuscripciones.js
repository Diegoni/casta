Ext.onReady(function(){

    var CorteSuscripcionesForm = new Ext.FormPanel({
        region: 'west',
        width: 300,
        labelWidth: 75,
        title: "<?php echo $this->lang->line('Corte Operaciones'); ?>",
        bodyStyle: 'padding:5px 5px 0',
        defaultType: 'textfield',
        collapsible: true,
        collpaseMode: 'mini',
        
        items: [{
            xtype: 'datefield',
            startDay: Ext.app.DATESTARTDAY,
            fieldLabel: "<?php echo $this->lang->line('Fecha'); ?>",
            name: 'fecha',
            anchor: '95%',
            id: 'fecha'
        }],
        buttons: [{
            text: "<?php echo $this->lang->line('Calcular'); ?>",
            handler: function(){
                ComprasAnticipadasGrid.store.load({
                    params: {
                        fecha: CorteSuscripcionesForm.findById('fecha').value
                    },
                    waitMsg: "<?php echo $this->lang->line('Cargando'); ?>"
                });
                VentasAnticipadasGrid.store.load({
                    params: {
                        fecha: CorteSuscripcionesForm.findById('fecha').value
                    },
                    waitMsg: "<?php echo $this->lang->line('Cargando'); ?>"
                });
            }
        }, {
            text: "<?php echo $this->lang->line('Limpiar'); ?>",
            handler: function(){
                CorteSuscripcionesForm.getForm().reset();
            }
        }]
    });
    
    var ComprasAnticipadasGrid = new Ext.grid.GridPanel({
    
        region: 'center',
        title: "<?php echo $this->lang->line('Compras Anticipadas'); ?>",
        // autoExpandColumn : "descripcion",
        loadMask: true,
        stripeRows: true,
        store: new Ext.data.JsonStore({
            url: 'index.php?c=oltp&m=get_cortesuscripcionescompras',
            root: 'value_data',
            fields: ['nIdFactura', 'Factura', 'Fecha', 'nCantidad', 'fTotal', 'nIdSuscripcion', 'nIdAlbaranEntrada', 'cRefInterna', 'cNumeroAlbaran', 'FechaProveedor', 'ImporteCompra', 'Cargos', 'RefAlbaran', 'CreacionAlbaran', 'nIdLibro', 'cTitulo', 'nIdCliente', 'cCliente', 'nIdProveedor', 'cProveedor']
        }),
        columns: [{
            header: "<?php echo $this->lang->line('Id'); ?>",
            width: TAM_COLUMN_ID,
            dataIndex: 'nIdFactura',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Factura'); ?>",
            width: TAM_COLUMN_DEFAULT,
            dataIndex: 'Factura',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Fecha'); ?>",
            width: TAM_COLUMN_DATE,
            dataIndex: 'Fecha',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Cantidad'); ?>",
            width: TAM_COLUMN_NUMBER,
            dataIndex: 'nCantidad',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Total'); ?>",
            width: TAM_COLUMN_MONEY,
            renderer: euroFormatter,
            dataIndex: 'fTotal',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Suscripción'); ?>",
            width: TAM_COLUMN_ID,
            dataIndex: 'nIdSuscripcion',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Albarán'); ?>",
            width: TAM_COLUMN_NUMBER,
            dataIndex: 'nIdAlbaranEntrada',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Ref Albaran'); ?>",
            width: TAM_COLUMN_DEFAULT,
            dataIndex: 'cRefInterna',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Número'); ?>",
            width: TAM_COLUMN_DEFAULT,
            dataIndex: 'cNumeroAlbaran',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Fecha Proveedor'); ?>",
            width: TAM_COLUMN_DATE,
            dataIndex: 'FechaProveedor',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Importe Compra'); ?>",
            width: TAM_COLUMN_MONEY,
            // renderer : euroFormatter,
            dataIndex: 'ImporteCompra',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Cargos'); ?>",
            width: TAM_COLUMN_MONEY,
            // renderer : euroFormatter,
            dataIndex: 'Cargos',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Ref Albaran'); ?>",
            width: TAM_COLUMN_DEFAULT,
            dataIndex: 'RefAlbaran',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Creación Albarán'); ?>",
            width: TAM_COLUMN_DATE,
            dataIndex: 'CreacionAlbaran',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Id Libro'); ?>",
            width: TAM_COLUMN_NUMBER,
            dataIndex: 'nIdLibro',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Título'); ?>",
            width: TAM_COLUMN_TITLE,
            dataIndex: 'cTitulo',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Id Cliente'); ?>",
            width: TAM_COLUMN_ID,
            dataIndex: 'nIdCliente',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Cliente'); ?>",
            width: TAM_COLUMN_NAMES,
            dataIndex: 'cCliente',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Id Proveedor'); ?>",
            width: TAM_COLUMN_ID,
            dataIndex: 'nIdProveedor',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Proveedor'); ?>",
            width: TAM_COLUMN_NAMES,
            dataIndex: 'cProveedor',
            sortable: true
        }]
    });
    
    var VentasAnticipadasGrid = new Ext.grid.GridPanel({
    
        region: 'center',
        title: "<?php echo $this->lang->line('Ventas Anticipadas'); ?>",
        // autoExpandColumn : "descripcion",
        loadMask: true,
        stripeRows: true,
        store: new Ext.data.JsonStore({
            url: 'index.php?c=oltp&m=get_cortesuscripcionesventas',
            root: 'value_data',
            fields: ['nIdFactura', 'Factura', 'Fecha', 'nCantidad', 'fTotal', 'nIdSuscripcion', 'nIdAlbaranEntrada', 'cRefInterna', 'cNumeroAlbaran', 'FechaProveedor', 'ImporteCompra', 'Cargos', 'RefAlbaran', 'CreacionAlbaran', 'nIdLibro', 'cTitulo', 'nIdCliente', 'cCliente', 'nIdProveedor', 'cProveedor']
        }),
        
        columns: [{
            header: "<?php echo $this->lang->line('Id'); ?>",
            width: TAM_COLUMN_ID,
            dataIndex: 'nIdFactura',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Factura'); ?>",
            width: TAM_COLUMN_DEFAULT,
            dataIndex: 'Factura',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Fecha'); ?>",
            width: TAM_COLUMN_DATE,
            dataIndex: 'Fecha',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Cantidad'); ?>",
            width: TAM_COLUMN_NUMBER,
            dataIndex: 'nCantidad',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Total'); ?>",
            width: TAM_COLUMN_MONEY,
            renderer: euroFormatter,
            dataIndex: 'fTotal',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Suscripción'); ?>",
            width: TAM_COLUMN_ID,
            dataIndex: 'nIdSuscripcion',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Albarán'); ?>",
            width: TAM_COLUMN_NUMBER,
            dataIndex: 'nIdAlbaranEntrada',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Ref Albaran'); ?>",
            width: TAM_COLUMN_DEFAULT,
            dataIndex: 'cRefInterna',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Número'); ?>",
            width: TAM_COLUMN_DEFAULT,
            dataIndex: 'cNumeroAlbaran',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Fecha Proveedor'); ?>",
            width: TAM_COLUMN_DATE,
            dataIndex: 'FechaProveedor',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Importe Compra'); ?>",
            width: TAM_COLUMN_MONEY,
            // renderer : euroFormatter,
            dataIndex: 'ImporteCompra',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Cargos'); ?>",
            width: TAM_COLUMN_MONEY,
            // renderer : euroFormatter,
            dataIndex: 'Cargos',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Ref Albaran'); ?>",
            width: TAM_COLUMN_DEFAULT,
            dataIndex: 'RefAlbaran',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Creación Albarán'); ?>",
            width: TAM_COLUMN_DATE,
            dataIndex: 'CreacionAlbaran',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Id Libro'); ?>",
            width: TAM_COLUMN_NUMBER,
            dataIndex: 'nIdLibro',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Título'); ?>",
            width: TAM_COLUMN_TITLE,
            dataIndex: 'cTitulo',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Id Cliente'); ?>",
            width: TAM_COLUMN_ID,
            dataIndex: 'nIdCliente',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Cliente'); ?>",
            width: TAM_COLUMN_NAMES,
            dataIndex: 'cCliente',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Id Proveedor'); ?>",
            width: TAM_COLUMN_ID,
            dataIndex: 'nIdProveedor',
            sortable: true
        }, {
            header: "<?php echo $this->lang->line('Proveedor'); ?>",
            width: TAM_COLUMN_NAMES,
            dataIndex: 'cProveedor',
            sortable: true
        }]
    });
    
    var viewport = new Ext.Viewport({
        layout: 'border',
        renderTo: Ext.getBody(),
        items: [CorteSuscripcionesForm, {
            region: 'center',
            xtype: 'tabpanel',
            activeTab: 0,
            items: [ComprasAnticipadasGrid, VentasAnticipadasGrid]
        }]
    });
});
