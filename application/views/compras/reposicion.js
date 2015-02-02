(function(){

    try {
        var open_id = "<?php echo $open_id;?>";
        var form_id = "<?php echo $id;?>";
        var title = "<?php echo $title;?>";
        var icon = "<?php echo $icon;?>";
        if (title == '') 
            title = _s('Reposición');
        if (icon == '') 
            icon = 'iconoReposicionTab';
        if (form_id == '') 
            form_id = Ext.app.createId();
        
        // Los datos de combos
        var seccion = new Ext.form.ComboBox(Ext.app.combobox({
            url: site_url('generico/seccion/search'),
            name: form_id + '_secc',
            anchor: '100%',
            label: _s('Seccion')
        }));
        
        var proveedor = new Ext.form.ComboBox(Ext.app.autocomplete2({
            url: site_url('proveedores/proveedor/search'),
            name: form_id + '_prv',
            anchor: '100%',
            fieldLabel: _s('Proveedor')
        }));
        
        var materia = new Ext.form.ComboBox(Ext.app.autocomplete2({
            url: site_url('catalogo/materia/search'),
            name: form_id + '_mat',
            anchor: '100%',
            fieldLabel: _s('Materia')
        }));
        
        var editorial = new Ext.form.ComboBox(Ext.app.autocomplete2({
            url: site_url('catalogo/editorial/search'),
            name: form_id + '_edit',
            anchor: '100%',
            fieldLabel: _s('Editorial')
        }));
        
        var pedidos = new Ext.form.ComboBox(Ext.app.combobox({
            url: site_url('compras/pedidoproveedor/abiertos'),
            name: form_id + '_ped',
            width: 250, 
            label: _s('Pedidos')
        }));
        
        var proveedores = new Ext.form.ComboBox(Ext.app.combobox({
            url: site_url('proveedores/proveedor/search'),
            label: _s('Proveedor'),
            name: form_id + '_prv'
        }));
        
        var p = Ext.app.combobox({
            url: site_url('proveedores/proveedor/search'),
            width: 450, 
            label: _s('Proveedor'),
            extrafields: ['text2'],
            name: form_id + '_prv2'
        });
        p['tpl'] = '<tpl for="."><div class="x-combo-list-item"><b>{text}</b>{text2}</div></tpl>'
        p['listAlign'] = [ 'bl-bl', [0,0] ];
        var proveedores2 = new Ext.form.ComboBox(p);
        
        proveedores2.on('select', function(c, r, i){
            //console.log('Cargando pedidos');
            var boton = Ext.getCmp(form_id + '_pedir_btn');
            loadpedidos(r.data.id, boton);
        });
        
        proveedores.on('select', function(c, r, i){
            //console.log('Cargando pedidos');
            var boton = Ext.getCmp(form_id + '_pedir_btn');
            loadpedidos(r.data.id, boton);
        });
        
        proveedores2.store.load({
            params: {
                where: 'nIdProveedor=-1'
            }
        });
        
        var loadpedidos = function(id, boton){
            pedidos.store.removeAll();
            if (id == null) {
                if (boton != null) 
                    boton.enable();
                Ext.app.msgError(title, _s('no_proveedor'));
                return;
            }
            var dp = Ext.getCmp(form_id + "_deposito").getValue();
            id_proveedor = id;
            //pedidos.clearValue();
            pedidos.store.removeAll();
            pedidos.store.baseParams = {
                deposito: (dp ? 1 : 0),
                seccion: id_seccion,
                proveedor: id
            };
            pedidos.store.load({
                callback: function(){
                    if (boton != null) 
                        boton.enable();
                }
            });
        };
        
        var loadproveedores = function(data, id, boton){
            proveedores2.store.removeAll();
            Ext.each(data, function(item){
                if (item.disabled !== true) {
                    var text = item.text;
                    if (item['default']) {
                        text = '(*)' + text;
                        id = item.nIdProveedor;
                    }
                    //console.log('text2 ' + item.text2);                //
                    Ext.app.comboAdd(proveedores2.store, item.nIdProveedor, text, item.text2);
                }
            });
            loadpedidos(id, boton);
            proveedores2.setValue(new String(id));
            proveedores2.onTriggerClick();
        }
        
        var datos_libro = null;
        var datos_select = null;
        var id_proveedor = null;
        var id_seccion = null;
        // Carga datos
        seccion.store.load();
        
        var print_panel = function(text){
            var detailEl = Ext.getCmp(form_id + "details-panel").body;
            
            detailEl.applyStyles({
                'background-color': '#FFFFFF'
            });
            detailEl.update(text);
        }
        
        // Obtener datos libro
        var getdatos = function(id, data){
            var ids = Ext.app.getIdCombo(seccion);
            var boton = Ext.getCmp(form_id + '_pedir_btn');
            boton.disable();
            pedidos.store.removeAll();
            pedidos.clearValue();
            proveedores.store.removeAll();
            proveedores2.store.removeAll();
            pedidos.store.removeAll();
            Ext.app.callRemote({
                url: site_url('compras/reposicion/get_datos_venta'),
                params: {
                    ids: data.nIdSeccion,
                    id: id
                },
                timeout: false,
                nomsg: true,
                title: this.title,
                fnok: function(obj){
                    if (obj.success) {
                        print_panel(obj.message);
                        datos_libro = obj.data;
                        datos_select = data;
                        id_seccion = data.nIdSeccion;
                        var hay = parseInt(data.Firme) + parseInt(data.Deposito) + parseInt(data.APedir) + parseInt(data.Recibir);
                        var ct = 0;
                        if (data.Minimo > 0) {
                            if (hay < data.Minimo) 
                                ct = data.Minimo - hay;
                        }
                        Ext.getCmp("<?php echo $id;?>_cantidad").setValue(ct);
                        loadproveedores(obj.data.proveedores, obj.data.nIdProveedor, boton);
                    }
                    else {
                        boton.enable();
                        Ext.app.msgError(title, _s('registro_error') + ': ' +
                        obj.message);
                    }
                }
            });
        }
        
        var renderStateId = function(val, x, r, row, col){
        
            if (r != null) {
                if (r.data.NoTratados > 0) {
                    return "<b>" + val + '</b>';
                }
            }
            return val;
        }
        
        var renderStock = function(val, x, r, row, col){
        
            if (r != null) {
                if (x != null) 
                    x.css = 'cell-repo-stock';
                return val;
            }
            return val;
        }
        
        
        var renderStockAPedir = function(val, x, r, row, col){
            if (r != null && (x != null)) 
                x.css = 'cell-repo-apedir';            
            return val;
        }
        
        var renderStockRecibir = function(val, x, r, row, col){
        
            if (r != null) {
                if (x != null) 
                    x.css = 'cell-repo-recibir';
                return val;
            }
            return val;
        }
        
        //http://rrishikesh.wordpress.com/2009/04/01/data-store-connection-timeout-in-extjs/
        var connObj = new Ext.data.Connection({
            timeout: Ext.app.TIMEOUTREMOTECALLMAX,
            url: site_url('compras/reposicion/get_list')
        });
        // El store de los libros a reponer
        var store = new Ext.data.Store({
            remoteSort: false,
            autoload: false,
            
            proxy: new Ext.data.HttpProxy(connObj),
            reader: new Ext.data.JsonReader({
                root: 'value_data',
                totalProperty: 'total_data',
                idProperty: 'id',
                remoteSort: false,
                autoload: false
            }, [{
                name: 'nIdLibro',
                type: 'int'
            }, {
                name: 'id',
                type: 'int'
            }, {
                name: 'nIdSeccionLibro',
                type: 'int'
            }, {
                name: 'cTitulo'
            }, {
                name: 'cNombre'
            }, {
                name: 'nIdSeccion',
                type: 'int'
            }, {
                name: 'Vendidos',
                type: 'int'
            }, {
                name: 'Movidos',
                type: 'int'
            }, {
                name: 'Tratados',
                type: 'int'
            }, {
                name: 'NoTratados',
                type: 'int'
            }, {
                name: 'Firme',
                type: 'int'
            }, {
                name: 'Deposito',
                type: 'int'
            }, {
                name: 'Reservado',
                type: 'int'
            }, {
                name: 'Recibir',
                type: 'int'
            }, {
                name: 'APedir',
                type: 'int'
            }, {
                name: 'Servir',
                type: 'int'
            }, {
                name: 'ADevolver',
                type: 'int'
            }, {
                name: 'Minimo',
                type: 'int'
            }, {
                name: 'MinLineaAlbaran',
                type: 'int'
            }, {
                name: 'MinLineaMovimiento',
                type: 'int'
            }, {
                name: 'MaxLineaAlbaran',
                type: 'int'
            }, {
                name: 'MaxLineaMovimiento',
                type: 'int'
            }, {
                name: 'Tratar',
                type: 'bool'
            }])
        });
        
        var reload = function(){
            try {
                var ids = Ext.app.getIdCombo(seccion);
                var idp = /*Ext.app.getIdCombo*/ (proveedor.getValue());
                var idm = Ext.app.getIdCombo(materia);
                var ide = Ext.app.getIdCombo(editorial);
                var d = Ext.getCmp(form_id + "_desde").getRawValue();
                var h = Ext.getCmp(form_id + "_hasta").getRawValue();
                
                if ((ids == '' || ids == null) && (idp == '' || idp == null) &&
                (idm == '' || idm == null) &&
                (ide == '' || ide == null) ||
                (d == '') ||
                (h == '')) {
                    Ext.app.msgFly(title, _s('mensaje_faltan_datos'));
                    return;
                }
                store.baseParams = {
                    ids: ids,
                    idm: idm,
                    ide: ide,
                    idp: idp,
                    d: d,
                    h: h
                };
                id_seccion = ids;
                pedidos.store.removeAll();
                pedidos.clearValue();
                proveedores2.store.removeAll();
                proveedores2.clearValue();
                print_panel(_s('hlp-repo-select'));
                store.load({
                    waitMsg: _s('Cargando')
                });
                Ext.getCmp(form_id).setTitle(title + ' ' + seccion.getValue());
            } 
            catch (e) {
                console.dir(e);
            }
        };
        
        var pedirlibros = function(){
            var idl = datos_libro.nIdLibro;
            var idpd = pedidos.getValue();
            var idp = id_proveedor;
            var ids = id_seccion;
            var dp = Ext.getCmp(form_id + "_deposito").getValue();
            var qt = Ext.getCmp(form_id + "_cantidad").getValue();
            
            
            var rec = grid.getSelectionModel().getSelected();
            if (qt != 0) {
                if (qt < 0) {
                    if (-qt > parseInt(rec.data.APedir)) {
                        Ext.app.msgError(title, _s('reposicion-stock-negativo-incorrecto'));
                        return;
                    }
                    if ((idpd == null) || (idpd < 1)) {
                        Ext.app.msgError(title, _s('reposicion-stock-negativo-no-pedido'));
                        return;
                    }
                }
                
                Ext.app.callRemote({
                    url: site_url('compras/reposicion/pedir'),
                    params: {
                        id: idl,
                        idpd: idpd,
                        idp: idp,
                        ids: ids,
                        dp: dp,
                        qt: qt
                    },
                    fnok: function(obj){
                        rec.set('APedir', parseInt(rec.data.APedir) + parseInt(qt));
                        rec.commit();
                        var boton = Ext.getCmp(form_id + '_pedir_btn');
                        loadpedidos(datos_libro.nIdProveedor, boton);
                    }
                });
            }
            grid.getSelectionModel().selectNext();
            grid.getView().focusEl.focus();
            
            if (rec.data.NoTratados > 0) {
                Ext.app.callRemote({
                    url: site_url('compras/reposicion/marcar'),
                    //nomsg: true,
                    params: {
                        idl: idl,
                        ids: ids,
                        minmov: datos_select.MinLineaMovimiento,
                        maxmov: datos_select.MaxLineaMovimiento,
                        minalb: datos_select.MinLineaAlbaran,
                        maxalb: datos_select.MaxLineaAlbaran
                    },
                    fnok: function(obj){
                        rec.set('Tratados', rec.data.Tratados + rec.data.NoTratados);
                        rec.set('NoTratados', 0);
                        rec.commit();
                    }
                });
            }
        }
        
        var sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });
        
        var grid = new Ext.grid.EditorGridPanel({
        
            store: store,
            autoExpandColumn: "descripcion",
            region: 'center',
            stripeRows: true,
            loadMask: true,
            split: true,
            id: form_id + "_grid",
            sm: sm,
            
            columns: [{
                header: _s('Id'),
                width: Ext.app.TAM_COLUMN_ID,
                dataIndex: 'nIdLibro',
                sortable: true
            }, {
                header: _s('Título'),
                width: Ext.app.TAM_COLUMN_TITLE,
                dataIndex: 'cTitulo',
                renderer: renderStateId,
                id: 'descripcion',
                sortable: true
            }, {
                header: _s('Vendidos'),
                width: Ext.app.TAM_COLUMN_STOCK,
                dataIndex: 'Vendidos',
                sortable: true
            }, {
                header: _s('Movidos'),
                width: Ext.app.TAM_COLUMN_STOCK,
                dataIndex: 'Movidos',
                sortable: true
            }, {
                header: _s('Tratados'),
                width: Ext.app.TAM_COLUMN_STOCK,
                dataIndex: 'Tratados',
                sortable: true
            }, {
                header: _s('NoTratados'),
                width: Ext.app.TAM_COLUMN_STOCK,
                dataIndex: 'NoTratados',
                sortable: true
            }, {
                header: _s('Firme'),
                width: Ext.app.TAM_COLUMN_STOCK,
                dataIndex: 'Firme',
                renderer: renderStock,
                sortable: true
            }, {
                header: _s('Deposito'),
                width: Ext.app.TAM_COLUMN_STOCK,
                dataIndex: 'Deposito',
                renderer: renderStock,
                sortable: true
            }, {
                header: _s('Reservado'),
                width: Ext.app.TAM_COLUMN_STOCK,
                dataIndex: 'Reservado',
                sortable: true
            }, {
                header: _s('Recibir'),
                width: Ext.app.TAM_COLUMN_STOCK,
                dataIndex: 'Recibir',
                renderer: renderStockRecibir,
                sortable: true
            }, {
                header: _s('APedir'),
                width: Ext.app.TAM_COLUMN_STOCK,
                renderer: renderStockAPedir,
                dataIndex: 'APedir',
                sortable: true
            }, {
                header: _s('Servir'),
                width: Ext.app.TAM_COLUMN_STOCK,
                dataIndex: 'Servir',
                sortable: true
            }, {
                header: _s('ADevolver'),
                width: Ext.app.TAM_COLUMN_STOCK,
                dataIndex: 'ADevolver',
                sortable: true
            }, {
                header: _s('Minimo'),
                width: Ext.app.TAM_COLUMN_STOCK,
                dataIndex: 'Minimo',
                sortable: true,
                editor: new Ext.form.NumberField({
                    allowBlank: false,
                    allowNegative: false,
                    style: 'text-align:left'
                })
            }, {
                header: _s('Sección'),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'cNombre',
                sortable: true
            }],
            tbar: Ext.app.gridStandarButtons({
                bar: [{
                    tooltip: _s('cmd-actualizar'),
                    text: _s('Actualizar'),
                    iconCls: 'icon-actualizar',
                    handler: function(){
                        reload();
                    }
                }, {
                    text: _s('Limpiar'),
                    iconCls: 'icon-clean',
                    id: form_id + 'alb_btnnew',
                    handler: function(){
                        seccion.reset();
                        proveedor.reset();
                        materia.reset();
                        editorial.reset();
                        Ext.getCmp(form_id + "_desde").reset();
                        Ext.getCmp(form_id + "_hasta").reset();
                    }
                }],
                title: title,
                id: form_id + "_grid"
            }),
            listeners: {
                afteredit: function(e){
                    var ed = false;
                    var params = {};
                    params['id'] = e.record.data.id;
                    if ((is_null(e.value, '') != is_null(e.originalValue, ''))) {
                        var url = site_url('catalogo/articulo/upd');
                        var rec = grid.getSelectionModel().getSelected();
                        Ext.app.callRemote({
                            url: url,
                            title: title,
                            params: {
                                id: rec.data.nIdLibro,
                                'secciones[0][nIdSeccionLibro]': rec.data.nIdSeccionLibro,
                                'secciones[0][nStockMinimo]': e.value
                            },
                            fnok: function(){
                                e.record.commit();
                            },
                            fnnok: function(){
                                e.record.reject();
                            }
                        });
                    }
                    else {
                        e.record.commit();
                    }
                }
            },
            
            viewConfig: {
                enableRowBody: true,
                getRowClass: function(r, rowIndex, rowParams, store){
                    var hay = parseInt(r.data.Firme) + parseInt(r.data.Deposito) + parseInt(r.data.APedir) + parseInt(r.data.Recibir);
                    return (r.data.NoTratados > 0) ? 'cell-repo-tratar' : ((r.data.Minimo > hay) ? 'cell-repo-minimo' : '');
                }
            }
        });
        
        // Item seleccionado
        grid.getSelectionModel().on('rowselect', function(sm, rowIdx, r){
            getdatos(r.data.nIdLibro, r.data);
        });
        var cm_lineas = fn_contextmenu();
        var contextmenu = Ext.app.addContextMenuLibro(grid, 'nIdLibro', cm_lineas);
        cm_lineas.setContextMenu(contextmenu)
        addMenuDocumentos(cm_lineas);
        addMenuVentas(cm_lineas);
        addMenuStock(cm_lineas);
        addMenuSeparator(cm_lineas);
        addMenuGeneral(_s('pedidos_cliente_articulo'), null, cm_lineas, 'icon-pedcli', function(record){
            return site_url('catalogo/articulo/pedidos_cliente/' + record.data.nIdLibro);
        });
        
        addMenuGeneral(_s('pedidos_cliente_pendientes_articulo'), null, cm_lineas, 'icon-pedcli', function(record){
            return site_url('catalogo/articulo/pedidos_cliente_pendiente/' + record.data.nIdLibro);
        });
        addMenuGeneral(_s('pedidos_proveedor_articulo'), null, cm_lineas, 'icon-pedprv', function(record){
            return site_url('catalogo/articulo/pedidos_proveedor/' + record.data.nIdLibro);
        });
        addMenuGeneral(_s('pedidos_proveedor_pendientes_articulo'), null, cm_lineas, 'icon-pedprv', function(record){
            return site_url('catalogo/articulo/pedidos_proveedor_pendiente/' + record.data.nIdLibro);
        });
        
        pedidos.store.on('load', function(s, r){
            if (s.getTotalCount() > 0) {
                pedidos.setValue(s.getAt(s.getTotalCount() - 1).data.id);
            }
        });
        
        var border = new Ext.Panel({
            title: title,
            id: form_id,
            region: 'center',
            closable: true,
            iconCls: icon,
            layout: 'border',
            listeners: {
                afterrender: function(p){
                    var map = new Ext.KeyMap(p.getEl(), [{
                        key: [10, 13],
                        ctrl: true,
                        stopEvent: true,
                        fn: function(){
                            reload();
                        }
                    }]);
                }
            },
            items: [{
                xtype: 'panel',
                region: 'north',
                height: 60,
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: .33,
                        // layout : 'form',
                        bodyStyle: 'background:transparent;padding:2px',
                        border: false,
                        items: [{
                            bodyStyle: 'padding:0px 5px 0px 5px;',
                            items: [{
                                fieldLabel: _s('Desde'),
                                id: form_id + "_desde",
                                value: DateAdd('d', -1, new Date()),
                                startDay: Ext.app.DATESTARTDAY,
                                xtype: "datefield"
                            }, proveedor],
                            layout: "form",
                            border: false
                        }]
                    }, {
                        columnWidth: .33,
                        // layout : 'form',
                        bodyStyle: 'background:transparent;padding:2px',
                        border: false,
                        items: [{
                            items: [{
                                fieldLabel: _s('Hasta'),
                                id: form_id + "_hasta",
                                value: new Date(),
                                startDay: Ext.app.DATESTARTDAY,
                                xtype: "datefield"
                            }, editorial],
                            border: false,
                            layout: "form"
                        }]
                    }, {
                        columnWidth: .33,
                        layout: 'form',
                        bodyStyle: 'background:transparent;padding:2px',
                        border: false,
                        items: [{
                            items: [seccion, materia],
                            border: false,
                            layout: "form"
                        }]
                    }]
                }]
            }, grid, {
                region: 'south',
                height: Ext.app.REPOINFOHEIGHT,
                bodyStyle: 'padding-bottom:15px;background:#FFFFFF;',
                autoScroll: true,
                split: true,
                cls: 'details-panel',
                html: _s('hlp-repo-select'),
                id: form_id + "details-panel",
                tbar: [{
                    xtype: 'label',
                    html: _s('Depósito')
                }, {
                    fieldLabel: 'Deposito',
                    id: form_id + "_deposito",
                    xtype: "checkbox",
                    listeners: {
                        check: function(f, c){
                            if (id_proveedor != null) {
                                var boton = Ext.getCmp(form_id + '_pedir_btn');
                                loadpedidos(id_proveedor);
                            }
                        }
                    }
                }, '-', {
                    xtype: 'label',
                    html: _s('Pedidos')
                }, proveedores2, '-', pedidos, '-', {
                    xtype: 'label',
                    html: _s('Cantidad')
                }, new Ext.ux.form.Spinner({
                    id: form_id + "_cantidad",
                    width: 60,
                    selectOnFocus: true,
                    strategy: new Ext.ux.form.Spinner.NumberStrategy()
                }), {
                    tooltip: _s('cmd-reposicion-pedir'),
                    text: _s('Pedir'),
                    id: form_id + '_pedir_btn',
                    iconCls: 'icon-pedir',
                    listeners: {
                        click: function(){
                            pedirlibros();
                        }
                    }
                }]
            }]
        });
        
        return border;
    } 
    catch (e) {
        //console.dir(e);
    }
    
})();
