(function(){
    try {
        /*-------------------------------------------------------------------------
         * Datos Formulario
         *-------------------------------------------------------------------------
         */
        var open_id = "<?php echo $open_id;?>";
        var form_id = "<?php echo $id;?>";
        var title = _s("Devolución");
        var icon = "iconoDevolucionTab";
        
        var allsecciones = "<?php echo isset($allsecciones)?(($allsecciones)?'true':'false'):'true';?>" == 'true';
        
        var data_load = null;
        var ultimo_texto = null;
        var ultimo_cambio = null;
        var ultimo_id = null;
        var ultimo_title = null;
        var tooltip_cliente = null;
        var cliente_datos = null;
        var cliente_id = null;
        var s_defecto = null;
        var s_vedadas = null;
        var id_defecto = null;
        
        var fn_set_data = function(data){
            if (data.cliente_id) 
                cliente_id = data.cliente_id;
            if (data.cliente_datos) 
                cliente_datos = data.cliente_datos;
            if (data.info_button) 
                info_button = data.info_button;
            if (data.data_load) 
                data_load = data.data_load;
            if (data.title) 
                title = data.title;
            if (data.direcciones) 
                direcciones = data.direcciones;
            if (data.s_defecto) 
                s_defecto = data.s_defecto;
            if (data.s_vedadas) 
                s_vedadas = data.s_vedadas;
            if (data.tooltip_cliente) {
                tooltip_cliente = data.tooltip_cliente;
                msg.update(data.tooltip_cliente);
            }
        }
        
        var fn_get_data = function(){
            return {
                cliente_id: cliente_id,
                cliente_datos: cliente_datos,
                tooltip_cliente: tooltip_cliente,
                info_button: info_button,
                data_load: data_load,
                title: title,
                direcciones: direcciones,
                s_defecto: s_defecto,
                s_vedadas: s_vedadas
            }
        }
        
        var notas = Ext.app.formNotas();
        
        // Carga la venta
        var fn_load = function(id, res){
            notas.load(id);
            data_load = res;
            if (res.lineas != null) 
                lineas.load(res.lineas);
            
            fn_load_direcciones(res.nIdProveedor, res.nIdDireccion);
            fn_load_cliente(res.nIdProveedor);
            numero.setValue((res.nIdEstado == 1) ? _s('devolucion-abierta') : '');
            form.setDirty(false);
            lineas.control.focus();
            
            if (id_defecto != null) 
                secciondefecto.setValue(id_defecto);
        }
        
        var fn_lang = function(){
            return getLang(data_load);
        }
        
        // Borrado
        var fn_reset = function(){
            notas.reset();
            msg.update('');
            cliente_datos = null;
            data_load = null;
            lineas.clear();
            total_tpv = 0;
            cliente_id = null;
            form.setData({
                value_data: {
                    'nIdEstado': 1
                }
            }, true);
            lineas.control.focus();
            if (id_defecto != null) 
                secciondefecto.setValue(id_defecto);
        }
        
        // Guardar
        var fn_save = function(id, data){
            // Añadimos las líneas
            var index = 0;
            if (Ext.getCmp(direcciones.id).getValue() != '' && Ext.getCmp(direcciones.id).getValue() != '') 
                data['nIdDireccion'] = Ext.getCmp(direcciones.id).getValue();
            var idcliente = cliente_id;
            if ((data_load != null && idcliente != data_load.nIdCliente) || data_load == null) {
                data['nIdProveedor'] = idcliente;
            }
            data = lineas.get(data);
            //console.dir(data);
            lineas.control.focus();
            
            id_defecto = secciondefecto.getValue();
            return data;
        }
        
        // Enables y disables
        var fn_enable_disable = function(form){
            notas.enable(form.getId() > 0);
            var bloqueado = ((data_load != null) && (data_load.nIdEstado != 1 && data_load.nIdEstado != 5 && data_load.nIdEstado != 6));
            if (bloqueado) {
                //fechafactura.disable();
                lineas.disable();
            }
            else {
                //fechafactura.enable();
                lineas.enable();
            }
            
            Ext.app.formEnableList({
                list: [form.idform + 'btn_notas', form.idform + 'btn_pedir'],
                enable: (form.getId() > 0)
            });
            
            Ext.app.formEnableList({
                list: [form.idform + 'btn_cerrar3'],
                enable: (!bloqueado) && (form.getId() > 0)
            });
            Ext.app.formEnableList({
                list: [form.idform + 'btn_cancelar'],
                enable: ((data_load != null) && (data_load.nIdEstado == 3))
            });
            Ext.app.formEnableList({
                list: [form.idform + 'btn_entregar', form.idform + 'btn_entregar'],
                enable: (data_load != null) && (data_load.nIdEstado == 2)
            });
            Ext.app.formEnableList({
                list: [form.idform + 'btn_entregar', form.idform + 'btn_rechazar'],
                enable: (data_load != null) && (data_load.nIdEstado == 3)
            });
            
            var m = Ext.getCmp(form_id + 'btn_cerrar_menu');
            var m2 = Ext.getCmp(form_id + 'btn_enviar');
            m.enable();
            m2.enable();
            m.setText(_s('Cerrar'));
            m2.setText(_s('Cerrar y enviar'));
            if (data_load == null || data_load.nIdEstado == 5 || data_load.nIdEstado == 6) {
                m.disable();
                m2.disable();
            }
            else 
                if (data_load.nIdEstado == 1) {
                    m.setText(_s('Cerrar'));
                    m2.setText(_s('Cerrar y enviar'));
                }
                else 
                    if (data_load.nIdEstado == 2) {
                        m.setText(_s('Abrir'));
                        m2.setText(_s('Enviar'));
                        
                    }
                    else 
                        if (data_load.nIdEstado == 3) {
                            m.disable();
                            m2.setText(_s('Enviar'));
                        }
            
            var m3 = Ext.getCmp(form_id + 'btn_entregar');
            (data_load == null || data_load.nIdEstado != 2) ? m3.disable() : m3.enable();
            Ext.app.formEnableList({
                list : [form.idform + 'btn_dir_env_print',
                    form.idform + 'btn_dir_env_cola'
                    ],
                enable : (form.getId() > 0) && (data_load!=null) && (data_load.nIdDireccion != null)
            });         
            Ext.app.formEnableList({
                list: [form.idform + 'btn_courier'],
                enable: (form.getId() > 0)
            });
            Ext.app.formEnableList({
                list: [form.idform + 'btn_courieretq'],
                enable: (form.getId() > 0) && (data_load != null) && (data_load.cIdShipping != '') && (data_load.cIdShipping != null)
            });
            var b = Ext.getCmp(form.idform + 'btn_courier');
            if ((data_load != null) && (data_load.cIdShipping != '') && (data_load.cIdShipping != null)) {
                b.setText(_s('Renviar por courier'));
            } else {
                b.setText(_s('Enviar por courier'));
            }       
        }
        
        // Formulario
        var form = Ext.app.formGeneric();
        form.init({
            id: form_id,
            title: title,
            icon: icon,
            url: site_url('compras/devolucion'),
            fn_load: fn_load,
            fn_reset: fn_reset,
            fn_save: fn_save,
            fn_lang: fn_lang,
            fn_enable_disable: fn_enable_disable
        });
        
        var controles = documentosProveedor(form, 'nIdDireccion', fn_get_data, fn_set_data, Ext.app.PERFIL_DEVOLUCION);
        
        // Carga direcciones
        var cliente = controles.cliente;
        var info_button = controles.info_button;
        var clientefield = controles.clientefield;
        var direcciones = controles.direcciones;
        var fn_load_direcciones = controles.fn_load_direcciones;
        var fn_load_cliente = controles.fn_load_cliente;
        
        /*-------------------------------------------------------------------------
         * Control de línea
         *-------------------------------------------------------------------------
         */
        var total_tpv = 0;
        
        var total = new Ext.form.TextField({
            cls: 'total-field',
            readOnly: true,
            fieldLabel: _s('fTotal'),
            value: Ext.app.currencyFormatter(0),
            height: 50,
            width: 160
        });
        
        var showtotal = function(s){
            var t = 0;
            var ct = 0;
            var ejs = 0;
            s.each(function(r){
                t += r.data.fTotal;
                ct++;
                ejs += r.data.nCantidad;
            })
            total_tpv = t.decimal(Ext.app.DECIMALS);
            total.setValue(Ext.app.numberFormatter(t));
            ejemplares.setValue(sprintf(_s('lineas-ejemplares'), ct, ejs));
            form.setDirty();
        }
        
        var seccion_defecto = get_seccion_defecto(fn_get_data, fn_set_data, 'bp.devolucion.secciones.defecto', 'bp.devolucion.secciones.vedadas', allsecciones, false, true);
        var secciondefecto = seccion_defecto.secciondefecto;
        var fn_get_seccion = seccion_defecto.fn_get_seccion;
        
        var hide = Ext.app.GRIDCOLUMNS_HIDE_DEVOLUCION;
        
        var fn_get_descuento = function(data){
            //console.dir(data);
            if (cliente_datos == null) {
				fn_load_direcciones((data.nIdProveedor!=null)?data.nIdProveedor:data.nIdProveedor2);
				fn_load_cliente((data.nIdProveedor!=null)?data.nIdProveedor:data.nIdProveedor2);
                Ext.app.msgFly(title, _s('no-proveedor-select'));
                return;
            }
            var dto = cliente_datos.fDescuento;
            var si = false;
            if (cliente_datos) {
                // Busca el descuento por defecto
                Ext.each(data.descuentos, function(item){
                    if (item.nIdProveedor == cliente_datos.nIdProveedor) {
                        si = true;
                        dto = item.fDescuento;
                        return false;
                    }
                });
            }
            if (!si) {
                Ext.app.msgError(title, _s('pedidor-proveedor-no-proveedor'));
            }
            else 
                if (si && dto == null) {
                    Ext.app.msgFly(title, _s('pedidor-proveedor-no-proveedor-descuento'));
                }
            return dto;
        }
        
        var lineas = docLineaControl({
            nIdDocumento: 'nIdPedido',
            nIdLinea: 'nIdLinea',
            cReferencia: 'cRefProveedor',
            coste: false,
            firmedeposito: false,
            fn_get_seccion: fn_get_seccion,
            fn_change: showtotal,
            hide: hide,
            anchor: "100% 50%",
            fn_get_descuento: fn_get_descuento,
            url_search: site_url('catalogo/articulo/search'),
            url_load: site_url('catalogo/articulo/get3'),
            url_descuentos: site_url('catalogo/articulo/descuentos'),
            extrafields: [{
                header: _s('cEstado'),
                hidden: in_array('cEstado', hide),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'cEstado',
                sortable: true
            }, {
                hidden: true,
                dataIndex: 'nIdEstado',
                hideable: false,
                sortable: false
            }, {
                header: _s('cAlbaranProveedor'),
                hidden: in_array('cAlbaranProveedor', hide),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'cAlbaranProveedor',
                sortable: true
            }, {
                header: _s('nRechazadas'),
                hidden: in_array('nRechazadas', hide),
                width: Ext.app.TAM_COLUMN_NUMBER,
                dataIndex: 'nRechazadas',
                sortable: true
            }, {
                hidden: true,
                dataIndex: 'nIdAlbaran',
                hideable: false,
                sortable: false
            }, {
                header: _s('nIdDevolucionRechazada'),
                hidden: in_array('nIdDevolucionRechazada', hide),
                width: Ext.app.TAM_COLUMN_NUMBER,
                dataIndex: 'nIdDevolucionRechazada',
                sortable: true
            }]
        });
        
        addMenuDocumentos(lineas);
        addMenuVentas(lineas);
        addMenuStock(lineas);
        addMenuSeparator(lineas);
        var m_albaran = lineas.addMenu({
            text: _s('Ver albarán entrada'),
            handler: function(){
                var record = lineas.getItemSelect();
                if (record != null) {
                    Ext.app.execCmd({
                        url: site_url('compras/albaranentrada/index/' + record.data.nIdAlbaran)
                    });
                }
            },
            iconCls: 'iconoAlbaranEntrada'
        });
        var m_cambiaralbaran = lineas.addMenu({
            text: _s('Cambiar albarán de entrada'),
            handler: function(){
                var record = lineas.getItemSelect();
                if (record != null) {
                    var model = [{
                        name : 'dCreacion'
                    }, {
                        name : 'dAct'
                    }, {
                        name : 'cCUser'
                    }, {
                        name : 'cAUser'
                    }, {
                        name : 'nCantidad'
                    }, {
                        name : 'fDescuento'
                    }, {
                        name : 'fPrecio'
                    }, {
                        name : 'id'
                    }];

                    var columns = [{
                        header : _s("Id"),
                        width : Ext.app.TAM_COLUMN_ID,
                        dataIndex : 'id',
                        sortable : true,
                        hidden: true
                    }, {
                        header : _s("nCantidad"),
                        dataIndex : 'nCantidad',
                        width : Ext.app.TAM_COLUMN_NUMBER,
                        sortable : true
                    }, {
                        header : _s("fDescuento"),
                        dataIndex : 'fDescuento',
                        width : Ext.app.TAM_COLUMN_NUMBER,
                        sortable : true
                    }, {
                        header : _s("fPrecio"),
                        dataIndex : 'fPrecio',
                        width : Ext.app.TAM_COLUMN_NUMBER,
                        sortable : true
                    }, {
                        header : _s('cCUser'),
                        width : Ext.app.TAM_COLUMN_TEXT,
                        dataIndex : 'cCUser',
                        sortable : true
                    }, {
                        header : _s('dCreacion'),
                        width : Ext.app.TAM_COLUMN_DATE,
                        dateFormat : 'timestamp',
                        renderer : Ext.app.renderDate,
                        dataIndex : 'dCreacion',
                        sortable : true
                    }, {
                        header : _s('cAUser'),
                        width : Ext.app.TAM_COLUMN_TEXT,
                        dataIndex : 'cAUser',
                        sortable : true
                    }, {
                        header : _s('dAct'),
                        width : Ext.app.TAM_COLUMN_DATE,
                        dateFormat : 'timestamp',
                        renderer : Ext.app.renderDate,
                        dataIndex : 'dAct',
                        sortable : true
                    }];
                    var store = Ext.app.getStore(site_url('compras/albaranentrada/get_albaranesentrada'), model, false, true);
                    var fn_change = function(nueva) {
                        Ext.app.callRemote({
                            url: site_url('compras/devolucion/set_albaranentrada/' + record.data.nIdLinea + '/' + nueva)
                        });
                    }
                    var fn = function() {
                        if((parseInt(store.getTotalCount()) >= 1)) {
                            var listView = Ext.app.createGrid({
                                store : store,
                                columns : columns,
                                title : _s('Búsqueda de registros'),
                                mode : 'search',
                                fn_open : function(id) {
                                    fn_change(id);
                                    form.close();
                                }
                            });

                            listView.setHeight(Ext.app.FORM_SEARCH_HEIGHT);

                            var fn_ok = function() {
                                var sm = listView.getSelectionModel();
                                console.log('fn_ok');
                                if(sm.hasSelection()) {
                                    var sel = sm.getSelected();
                                    fn_change(sel.data.id);
                                }
                            };
                            var form = Ext.app.formStandarForm({
                                controls : [listView],
                                title: record.data.cTitulo,
                                height : Ext.app.FORM_SEARCH_HEIGHT,
                                fn_ok : fn_ok
                            });

                            listView.on('dblclick', function(view, index) {
                                var sm = listView.getSelectionModel();
                                if(sm.hasSelection()) {
                                    var sel = sm.getSelected();
                                    form.close();
                                    fn_change(sel.data.id);
                                }
                            });
                            form.show();
                        } else {
                            Ext.app.msgInfo(record.data.cTitulo, _s('registros no encontrados'));
                        }
                    }
                    store.load({
                        params : {
                            idln: record.data.nIdLinea
                        },
                        callback : fn
                    });
                }
            },
            iconCls: 'icon-unlink'
        });
    
        var m_devolucionrechazada = lineas.addMenu({
            text: _s('Ver devolución rechazada'),
            handler: function(){
                var record = lineas.getItemSelect();
                if (record != null && record.data.nIdDevolucionRechazada != null) {
                    Ext.app.execCmd({
                        url: site_url('compras/devolucion/index/' + record.data.nIdDevolucionRechazada)
                    });
                }
            },
            iconCls: 'iconoDevolucion'
        });

        var fn_check_menu = function(item){
            console.dir(item.data);
            (item.data.nIdDevolucionRechazada != null) ? m_devolucionrechazada.enable() : m_devolucionrechazada.disable();
            (item.data.nIdAlbaran != null) ? m_albaran.enable() : m_albaran.disable();
            (item.data.nIdAlbaran != null && item.data.nIdEstado ==2 ) ? m_cambiaralbaran.enable() : m_cambiaralbaran.disable();
        }
        lineas.setCheckMenu(fn_check_menu);
        
        /*-------------------------------------------------------------------------
         * Resto de los controles
         *-------------------------------------------------------------------------
         */
        var grid_notas = notas.init({
            id: form_id + "_notas",
            url: site_url('compras/devolucion'),
            mainform: form
        });
        
        var ejemplares = new Ext.form.DisplayField({
            cls: 'lineas-ejemplares-field',
            value: 'ejemplares',
            height: 10,
            //disabled: true,
            anchor: '100%'
        });
        
        var tipodevolucion = Ext.app.combobox({
            url: site_url('compras/tipodevolucion/search'),
            allowBlank: true,
            id: 'nIdTipoDevolucion'
        });
        
        var numero = new Ext.form.DisplayField({
            cls: 'numero-factura-field',
            value: '',
            height: 15,
            width: 150
        });
        
        var refs = {
            xtype: 'compositefield',
            fieldLabel: _s('cRefProveedor'),
            msgTarget: 'side',
            anchor: '-20',
            /*defaults: {
             flex: 1
             },*/
            items: [{
                xtype: 'textfield',
                id: 'cRefProveedor',
                allowBlank: true,
                width: '250'
            }, {
                xtype: 'displayfield',
                value: _s('cRefInterna')
            }, {
                xtype: 'textfield',
                id: 'cRefInterna',
                allowBlank: true,
                width: '250'
            }, numero]
        };
        
        var estado = Ext.app.combobox({
            url: site_url('compras/estadodevolucion/search'),
            //anchor: "90%",
            disabled: true,
            allowBlank: false,
            readOnly: true,
            id: 'nIdEstado'
        });
        
        var fechacierre = new Ext.form.DateField({
            xtype: 'datefield',
            readOnly: true,
            startDay: Ext.app.DATESTARTDAY,
            name: 'dCierre',
            allowBlank: true
        });
        
        var fechaentrega = new Ext.form.DateField({
            xtype: 'datefield',
            //readOnly: true,
            startDay: Ext.app.DATESTARTDAY,
            name: 'dEntrega',
            allowBlank: true
        });
        
        var datas = {
            xtype: 'compositefield',
            fieldLabel: _s('dCierre'),
            msgTarget: 'side',
            anchor: '-20',
            items: [fechacierre, {
                xtype: 'displayfield',
                value: _s('dEntrega')
            }, fechaentrega, {
                xtype: 'displayfield',
                value: _s('Estado')
            }, estado, {
                xtype: 'displayfield',
                value: _s('bDeposito')
            }, {
                xtype: 'checkbox',
                id: 'bDeposito',
                // anchor : '90%',
                allowBlank: true,
                checked: false,
                fieldLabel: _s('bDeposito')
            }, {
                xtype: 'displayfield',
                value: _s('nPaquetes')
            }, new Ext.ux.form.Spinner({
                name: 'nPaquetes',
                width: 60,
                strategy: new Ext.ux.form.Spinner.NumberStrategy()
            })]
        };
        
        // Añade el comando para guardar        
        lineas.control.addPattern("^" + Ext.app.TPV_GUARDAR + "$", function(m, c){
            form.save();
            return true;
        });
        
        // Añade el comando para cerrar
        /*lineas.control.addPattern("^" + Ext.app.TPV_CERRAR + "$", function(m, c){
         fn_cerrar();
         return true;
         });*/
        lineas.control.addPattern("^" + Ext.app.TPV_DESCUENTO + "\\s?(\\d+)$", function(m, c){
            var v = (m[1] != null) ? parseFloat(m[1]) : 0;
            var v = v.decimal(Ext.app.DECIMALS);
            lineas.descuento(v);
            return true;
        });
        
        var msg = new Ext.Panel({
            cls: 'info-msg',
            autoScroll: true,
            height: 80,
            width: 600
        });
        
        var pie = {
            xtype: 'compositefield',
            fieldLabel: _s('fTotal'),
            msgTarget: 'side',
            anchor: '-20',
            items: [total, msg]
        };
		secciondefecto.width = 300;
        var scd = {
            xtype: 'compositefield',
            fieldLabel: _s('Sección def.'),
            msgTarget: 'side',
            anchor: '-20',
            items: [secciondefecto, {
                xtype: 'displayfield',
                value: _s('nIdTipoDevolucion')
            }, tipodevolucion]
        }
        // Controles normales
        var controls = [cliente, refs, datas, scd, lineas.linea, lineas.grid, ejemplares, pie];
        
        /*-------------------------------------------------------------------------
         * Comandos
         *-------------------------------------------------------------------------
         */
        // Cerrar la venta
        var fn_cerrar = function(fnpost){
            var fn = function(result){
                if (result) {
                    Ext.app.callRemote({
                        url: site_url('compras/devolucion/cerrar'),
                        wait: true,
                        params: {
                            id: form.getId()
                        },
                        fnok: function(obj){
                            Ext.app.eventos.fire('devolucion.close', {
                                id: form.getId(),
                                data: data_load,
                                importe: total_tpv
                            });
                            form.refresh();
                            /*console.log(fnpost);
                             if (fnpost != null)
                             fnpost();*/
                        }
                    });
                }
            }
            if (form.isDirty()) {
                form.save(fn);
            }
            else {
                fn(true);
            }
        }
        
        // Abrir documento
        var fn_abrir = function(){
            var fn = function(result){
                if (result) {
                    Ext.app.callRemote({
                        url: site_url('compras/devolucion/abrir'),
                        wait: true,
                        params: {
                            id: form.getId()
                        },
                        fnok: function(obj){
                            form.refresh();
                        }
                    });
                }
            }
            if (form.isDirty()) {
                form.save(fn);
            }
            else {
                fn(true);
            }
        }
        
        // Cerrar la venta
        var fn_entregar = function(v){
            var fn = function(result){
                if (result) {
                    Ext.app.callRemote({
                        url: site_url('compras/devolucion/entregar'),
                        wait: true,
                        params: {
                            id: form.getId()
                        },
                        fnok: function(obj){
                            form.refresh();
                        }
                    });
                }
            }
            if (form.isDirty()) {
                form.save(fn);
            }
            else {
                fn(true);
            }
        }
        
        var fn_enviar = function(){
            if (data_load == null) 
                return;
            var fn = function(){
                documentosEnviar(form, _s('Enviar devolucion'), site_url('compras/devolucion/send'));
            }
            if (data_load.nIdEstado == 1) 
                fn_cerrar(fn);
            else 
                if (data_load.nIdEstado == 2) {
                    fn();
                }
        }
        
        form.addCommand({
            text: _s('Cerrar devolución'),
            iconCls: 'icon-generar-doc',
            handler: fn_cerrar,
            id: form.idform + 'btn_cerrar3'
        });
        // TABS
        documentosAddTabs(form, controls, 'form-devolucion');
        
        form.addTab(new Ext.Panel({
            layout: 'border',
            id: form_id + "_notas",
            title: _s('Histórico'),
            iconCls: 'icon-history',
            region: 'center',
            baseCls: 'x-plain',
            frame: true,
            items: grid_notas
        }));
        
        // Búsqueda
        var fn_open = function(id){
            form.load(id);
            form.selectTab(0);
        }
        
         <?php $modelo = $this->reg->get_data_model(array('nIdDivisa', 'nIdDireccion', 'fValorDivisa'));?>
         <?php echo 'var grid_search = ' . extjs_creategrid($modelo, $id.'_g_search', null, null, 'compras.devolucion', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
         
        form.addTab({
            title: _s('Búsqueda'),
            iconCls: 'icon-search',
            items: Ext.app.formSearchForm({
                grid: grid_search,
                audit: false,
                id_grid: form_id + '_g_search_grid'
            })
        });
        var Cancelar = function(form){
            Ext.app.callRemote({
                url: site_url('compras/devolucion/cancelar'),
                params: {
                    id: form.getId()
                },
                fnok: function(res){
                    form.refresh();
                }
            });
        }
        
        form.addAction({
            text: _s('Cerrar'),
            handler: function(f){
                if (data_load == null) 
                    return;
                if (data_load.nIdEstado == 1) 
                    fn_cerrar();
                else 
                    if (data_load.nIdEstado == 2) {
                        fn_abrir(form);
                    }
            },
            iconCls: 'icon-generar-doc',
            id: form.idform + 'btn_cerrar_menu'
        });
        
        form.addAction({
            text: _s('Cerrar y enviar'),
            handler: function(){
                fn_enviar();
            },
            iconCls: 'icon-send',
            id: form.idform + 'btn_enviar'
        });
        
        var fn_rechazar = function(){
            fn_docs_select_lineas_devolucion(form.getId(), function(libros, motivo){
                var ct = 0;
                var rec = '';
                Ext.each(libros, function(r){
                    rec += r.nIdLinea + '|' + r.nCantidad + ';';
                    ct++;
                });
                if (ct > 0) {
                    Ext.app.callRemote({
                        url: site_url('compras/devolucion/rechazar'),
                        params: {
                            ids: rec,
							motivo: motivo
                        },
                        fnok: function(res){
                            form.refresh();
                        }
                    });
                }
            });
        }
                
        form.addAction('-');
        form.addAction({
            text: _s('Entregar'),
            handler: function(){
                fn_entregar();
            },
            iconCls: 'icon-deliver',
            id: form.idform + 'btn_entregar'
        });
        form.addAction({
            text: _s('Rechazar'),
            handler: function(){
                fn_rechazar();
            },
            iconCls: 'icon-rechazar',
            id: form.idform + 'btn_rechazar'
        });
        form.addTools({
            text: _s('Enviar por courier'),
            handler: function(){
                sendCourier(site_url('compras/devolucion/courier'), form, total_tpv);
            },
            iconCls: 'iconoCourier',
            id: form.idform + 'btn_courier'
        });
        form.addTools({
            text: _s('Imprimir etiqueta de courier'),
            handler: function(){
                Ext.app.callRemote({
                    url: site_url ('sys/codebar/etiqueta'),
                    params: {
                        idetq: data_load.cIdShipping
                    }
                });                
            },
            iconCls: 'iconoCourierEtq',
            id: form.idform + 'btn_courieretq'
        });
        form.addTools('-');
        form.addTools({
            text : _s('add-dir-envio-cola'),
            handler : function() {
                if (data_load != null && data_load.nIdDireccion != null)
                {
                    Ext.app.callRemote({
                        url : site_url('etiquetas/etiqueta/colaproveedor/' + data_load.nIdDireccion)
                    });
                }
            },
            iconCls : 'icon-label-cola',
            id : form.idform + 'btn_dir_env_cola'
        });
        form.addTools({
            text : _s('print-dir-envio'),
            handler : function() {
                if (data_load != null && data_load.nIdDireccion != null)
                {
                    Ext.app.callRemote({
                        url : site_url('etiquetas/etiqueta/printproveedor/' + data_load.nIdDireccion)
                    });
                }
            },
            iconCls : 'icon-label',
            id : form.idform + 'btn_dir_env_print'
        });        
        
        form.addAction('-');
        addButtonAbiertos(form);
        
        
        //tipodevolucion.store.load();
        secciondefecto.store.load();
        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
