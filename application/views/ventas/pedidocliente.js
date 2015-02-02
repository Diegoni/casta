(function(){
    try {
        /*-------------------------------------------------------------------------
         * Datos Formulario
         *-------------------------------------------------------------------------
         */
        var open_id = "<?php echo $open_id;?>";
        var form_id = "<?php echo $id;?>";
        var title = _s("Pedido de cliente");
        var icon = "iconoPedidoClienteTab";
        
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
            if (data.tooltip_cliente) {
                tooltip_cliente = data.tooltip_cliente;
                msg.update(data.tooltip_cliente);
            }
            if (data.cliente_id) {
                cliente_id = data.cliente_id;
                if (data.nIdDirFac!=null || form.getId() == null)
                {
                    load_combo_direcciones(cliente_id, direccionfactura, 
                            data.nIdDirFac, 
                            Ext.app.PERFIL_FACTURACION);
                }
            }
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
            if (data.cliente_datos) {
                lineas.setTarifas(data.cliente_datos.nIdTipoTarifa, data.cliente_datos.tarifas);
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
        
        // Carga la venta
        var fn_load = function(id, res){
            notas.load(id);
            data_load = res;
            if (data_load.nIdFactura != null) {
                var a = Ext.app.currencyFormatter(data_load.fAnticipo);
                if (data_load.nIdAlbaranDescuentaAnticipo != null) {
                    if (data_load.nIdEstadoAlbaran != 1) {
                        anticipo.setText(sprintf(_s('anticipo-usado'), a, data_load.nIdAlbaranDescuentaAnticipo));
                    }
                    else {
                        anticipo.setText(sprintf(_s('anticipo-albaran-cerrar'), a, data_load.nIdAlbaranDescuentaAnticipo));
                    }
                }
                else {
                    if (data_load.nIdEstadoFactura != 1) {
                        anticipo.setText(sprintf(_s('anticipo-pendiente-usar'), a, data_load.nIdFactura));
                    }
                    else {
                        anticipo.setText(sprintf(_s('anticipo-factura-cerrar'), a, data_load.nIdFactura));
                    }
                }
            }
            
            if (res.lineas != null) 
                lineas.load(res.lineas);
            
            fn_load_direcciones(res.nIdCliente, res.nIdDirEnv);
            fn_load_cliente(res.nIdCliente);
            
            form.setDirty(false);
            lineas.control.focus();
            if (id_defecto != null) 
                secciondefecto.setValue(id_defecto);

            load_combo_direcciones(res.nIdCliente, direccionfactura, 
                (res.nIdDirFac!=null)?res.nIdDirFac:-1, 
                Ext.app.PERFIL_FACTURACION);

        }
        
        // Borrado
        var fn_reset = function(){
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
        
        var fn_lang = function(){
            return getLang(data_load);
        }
        
        // Guardar
        var fn_save = function(id, data){
            // Añadimos las líneas
            var index = 0;
            if (Ext.getCmp(direcciones.id).getValue() != '' && Ext.getCmp(direcciones.id).getValue() != '') 
                data['nIdDirEnv'] = Ext.getCmp(direcciones.id).getValue();
            var idcliente = cliente_id;
            if ((data_load != null && idcliente != data_load.nIdCliente) || data_load == null) {
                data['nIdCliente'] = idcliente;
            }
            data = lineas.get(data);
            //console.dir(data);
            lineas.control.focus();
            id_defecto = secciondefecto.getValue();
            data['nIdDirFac'] = Ext.getCmp(direccionfactura.id).getValue();
            
            return data;
        }
        
        // Enables y disables
        var fn_enable_disable = function(form){
            //perfiles.enable(form.getId() > 0);
            var bloqueado = ((data_load != null) && (data_load.nIdEstado == 2));
            if (data_load != null) {
                anticipo.enable();
            }
            else {
                anticipo.setText(_s('crear-anticipo')), anticipo.disable();
            }
            if (bloqueado) {
				
                lineas.disable();
            }
            else {
                lineas.enable();
            }
            
            Ext.app.formEnableList({
                list: [form.idform + 'btn_albaran', form.idform + 'btn_exentoiva', 
                form.idform + 'btn_aplicariva', form.idform + 'btn_precio', 
                form.idform + 'btn_mover', form.idform + 'btn_enviar', 
                form.idform + 'btn_pedir', form.idform + 'btn_duplicar', 
                form.idform + 'btn_pedir_seccion', form.idform + 'btn_coste',
                form.idform + 'btn_servir', form.idform + 'btn_courier',
                form.idform + 'btn_courieretq', 
                form.idform + 'btn_publicar', 
                form.idform + 'btn_resumen',
                form.idform + 'btn_excel'],
                enable: (form.getId() > 0)
            });
            
            Ext.app.formEnableList({
                list: [form.idform + 'btn_cerrar3', form.idform + 'btn_cancelar'],
                enable: (!bloqueado) && (form.getId() > 0)
            });
            Ext.app.formEnableList({
                list: [form.idform + 'btn_abrir'],
                enable: (bloqueado)
            });
            Ext.app.formEnableList({
                list: [form.idform + 'btn_courieretq'],
                enable: (form.getId() > 0) && (data_load != null) && (data_load.cIdShipping != '') && (data_load.cIdShipping != null)
            });
            Ext.app.formEnableList({
                list: [form.idform + 'btn_enviado'],
                enable: (form.getId() > 0) && (data_load != null) && (data_load.nIdEstado == 1 || data_load.nIdEstado == 2)
            });
            var b = Ext.getCmp(form.idform + 'btn_courier');
            if ((data_load != null) && (data_load.cIdShipping != '') && (data_load.cIdShipping != null)) {
                b.setText(_s('Renviar por courier'));
            } else {
                b.setText(_s('Enviar por courier'));
            }		

            Ext.app.formEnableList({
                list: [form.idform + 'btn_presupuesto'],
                enable: (form.getId() > 0) && (data_load != null) && (data_load.nIdEstado != 2)
            });
            var b = Ext.getCmp(form.idform + 'btn_presupuesto');
            if ((data_load != null) && (data_load.nIdEstado == 3)) {
                b.setText(_s('Convertir en pedido'));
            } else {
                b.setText(_s('Convertir en presupuesto'));
            }       

            Ext.app.formEnableList({
                list : [form.idform + 'btn_dir_env_print',
                    form.idform + 'btn_dir_env_cola'
                    ],
                enable : (form.getId() > 0) && (data_load!=null) && (data_load.nIdDirEnv != null)
            });         
            Ext.app.formEnableList({
                list : [form.idform + 'btn_dir_fact_print',
                    form.idform + 'btn_dir_fact_cola'
                    ],
                enable : (form.getId() > 0) && (data_load!=null) && (data_load.nIdDirFac != null)
            });         
        }
        
        // Formulario
        var form = Ext.app.formGeneric();
        form.init({
            id: form_id,
            title: title,
            icon: icon,
            url: site_url('ventas/pedidocliente'),
            fn_load: fn_load,
            fn_reset: fn_reset,
            fn_lang: fn_lang,
            fn_save: fn_save,
            fn_enable_disable: fn_enable_disable
        });
        
        var controles = documentosCliente(form, 'nIdDirEnv', fn_get_data, fn_set_data)
        
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
        
        var seccion_defecto = get_seccion_defecto(fn_get_data, fn_set_data, 'bp.pedidocliente.secciones.defecto', 'bp.pedidocliente.secciones.vedadas', allsecciones, false);
        var secciondefecto = seccion_defecto.secciondefecto;
        var fn_get_seccion = seccion_defecto.fn_get_seccion;
        
        var hide = Ext.app.GRIDCOLUMNS_HIDE_PEDIDOCLIENTE;
        
        var lineas = docLineaControl({
            nIdDocumento: 'nIdPedido',
            nIdLinea: 'nIdLinea',
            cReferencia: 'cRefCliente',
            fn_get_seccion: fn_get_seccion,
            fn_change: showtotal,
            hide: hide,
            getRowClass: function(r, rowIndex, rowParams, store){
                if (r.data.bAviso == 1) 
                    return 'cell-repo-stock'
            },
            anchor: "100% 50%",
            url_search: site_url('catalogo/articulo/search'),
            url_load: site_url('catalogo/articulo/get2'),
            extrafields: [{
                header: _s('nCantidadServida'),
                width: Ext.app.TAM_COLUMN_NUMBER,
                hidden: in_array('nCantidadServida', hide),
                dataIndex: 'nCantidadServida',
                renderer: 'rendererCantidad',
                editor: new Ext.form.NumberField({
                    allowBlank: false,
                    allowNegative: true,
                    allowDecimals: false,
                    listeners: {
                        focus: function(t){
                            t.selectText(0, t.getValue().length);
                        }
                    }
                }),
                sortable: true
            }, {
                header: _s('PVP.Act'),
                width: Ext.app.TAM_COLUMN_NUMBER,
                hidden: in_array('fPVPL', hide),
                dataIndex: 'fPVPL',
                align : 'right',
                renderer : Ext.app.rendererPVP,
                sortable: true
            }, {
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
                hidden: true,
                dataIndex: 'nIdEstadoLibro',
                hideable: false,
                sortable: false
            }, {
                header: _s('cEstadoLibro'),
                hidden: in_array('cEstadoLibro', hide),
                width: Ext.app.TAM_COLUMN_TEXT,
                renderer: rendererEstadoLibro,
                dataIndex: 'cEstadoLibro',
                sortable: true
            }, {
                hidden: true,
                dataIndex: 'nIdTipoInformacion',
                hideable: false,
                sortable: false
            }, {
                header: _s('cInformacion'),
                hidden: in_array('cInformacion', hide),
                width: Ext.app.TAM_COLUMN_TEXT,
                renderer: renderInfoCliente,
                dataIndex: 'cInformacion',
                sortable: true
			}, {
				dataIndex : 'dFechaInformacion',
				header : _s('dFechaInformacion'),
                hidden: in_array('dFechaInformacion', hide),
				width : Ext.app.TAM_COLUMN_DATE,
				renderer : Ext.app.renderDate,
				sortable : true
            }, {
                header: _s('bAviso'),
                hidden: in_array('bAviso', hide),
                dataIndex: 'dAviso',
				width : Ext.app.TAM_COLUMN_DATE,
				dateFormat : 'timestamp',
				renderer : Ext.app.renderDate,
            }, {
                hidden: true,
                hideable: false,
                dataIndex: 'bAviso'
    	    }, {
                header: _s('nIdAlbaranSal'),
                hidden: in_array('nIdAlbaranSal', hide),
                dataIndex: 'nIdAlbaranSal'
            }]
        });
        console.dir(lineas);
        addMenuSeparator(lineas);
        addMenuPedir(lineas);
        addMenuDocumentos(lineas);
        addMenuVentas(lineas);
        addMenuStock(lineas);
        addMenuSeparator(lineas);
        var m_imposible = addMenuGeneral(_s('Imposible Servir'), form, lineas, 'icon-imposible', function(record){
            return site_url('ventas/pedidoclientelinea/imposibleservir/' + record.data.nIdLinea);
        });
        var m_catalogado = addMenuGeneral(_s('Catalogado'), form, lineas, 'icon-catalogado', function(record){
            return site_url('ventas/pedidoclientelinea/catalogado/' + record.data.nIdLinea);
        });
        var m_avisado = addMenuGeneral(_s('Avisado'), form, lineas, 'icon-avisado', function(record){
            return site_url('ventas/pedidoclientelinea/avisado/' + record.data.nIdLinea);
        });
        var m_actualizarprecio = addMenuGeneral(_s('Actualizar precio'), form, lineas, 'icon-precio', function(record){
            return site_url('ventas/pedidoclientelinea/actualizarprecio/' + record.data.nIdLinea +'/'+ cliente_datos.nIdTipoTarifa);
        });		
        var m_albaran = addMenuGeneral(_s('Ver albarán de salida'), form, lineas, 'iconoAlbaranSalida', function(record){
            return site_url('ventas/albaransalida/index/' + record.data.nIdAlbaranSal);
        });
        addMenuSeparator(lineas);
        var m_reservar = addMenuGeneral(_s('Reservar'), form, lineas, 'icon-accept', function(record){
            return site_url('ventas/pedidoclientelinea/reservar/' + record.data.nIdLinea);
        });
                
        var m_cancelar = addMenuCancelar(form, lineas, site_url('ventas/pedidoclientelinea/cancelar'));
        addMenuSeparator(lineas);
        var m_aceptar = addMenuGeneral(_s('Aceptar'), form, lineas, 'icon-check', function(record){
            return site_url('ventas/pedidoclientelinea/aceptar/' + record.data.nIdLinea);
        });
        var m_rechazar = addMenuGeneral(_s('Rechazar'), form, lineas, 'icon-uncheck', function(record){
            return site_url('ventas/pedidoclientelinea/rechazar/' + record.data.nIdLinea);
        });

		Ext.app.callRemote({
			url : site_url('ventas/informacioncliente/get_list'),
			params : {
				sort : 'nIdTipo'
			},
			fnok : function(res) {
				addMenuSeparator(lineas);
				Ext.each(res.value_data, function(item) {				
					addMenuGeneral(_s(item.cDescripcion), form, lineas, "icon-status-" + item.nIdTipo, function(record){
	            		return site_url('ventas/pedidoclientelinea/info/' + item.id +'/' + record.data.nIdLinea);
	        		});
				});
			}
		});

        var fn_check_menu = function(item){
        
            if (item.data.nIdEstado != null) {
                ((item.data.nIdEstado == 1 || item.data.nIdEstado == 2 || item.data.nIdEstado == 3)) ? m_cancelar.enable() : m_cancelar.disable();
                ((item.data.nIdEstado == 1 || item.data.nIdEstado == 2 || item.data.nIdEstado == 3) && data_load.bCatalogar == true) ? m_catalogado.enable() : m_catalogado.disable();
                (((item.data.nIdEstado == 6 || item.data.nIdEstado == 5) && data_load.bCatalogar == true && item.data.bAviso == 0) ||
                (data_load.bCatalogar == false && item.data.bAviso == 0)) ? m_avisado.enable() : m_avisado.disable();
				(item.data.nIdEstado == 1 || item.data.nIdEstado == 5 ) ? m_actualizarprecio.enable() : m_actualizarprecio.disable();
				(item.data.nIdAlbaranSal != null ) ? m_albaran.enable() : m_albaran.disable();
				(item.data.nCantidad > item.data.nCantidadServida ) ? m_reservar.enable() : m_reservar.disable();
                (item.data.nIdEstado == 11 || item.data.nIdEstado == 10) ? m_aceptar.enable() : m_aceptar.disable();
                (item.data.nIdEstado == 11 || item.data.nIdEstado == 9) ? m_rechazar.enable() : m_rechazar.disable();

				m_avisado.setText(_s('Avisado'));
                if (item.data.bAviso == 1) {
                    m_avisado.enable();
                    m_avisado.setText(_s('Quitar avisado'));
                }
                m_imposible.setVisible(true);
                if (item.data.nIdEstado == 5) {
                    m_imposible.setText(_s('Pasar a EN PROCESO'));
                }
                else 
                    if (item.data.nIdEstado == 1) {
                        m_imposible.setText(_s('Imposible Servir'));
                    }
                    else {
                        m_imposible.setVisible(false);
                    }                
            }
        }
        lineas.setCheckMenu(fn_check_menu);
        
        /*-------------------------------------------------------------------------
         * Resto de los controles
         *-------------------------------------------------------------------------
         */
        var ejemplares = new Ext.form.DisplayField({
            cls: 'lineas-ejemplares-field',
            value: 'ejemplares',
            height: 10,
            //disabled: true,
            anchor: '100%'
        });
        
        var refs = {
            xtype: 'compositefield',
            fieldLabel: _s('cRefCliente'),
            msgTarget: 'side',
            anchor: '-20',
            items: [{
                xtype: 'textfield',
                id: 'cRefCliente',
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
            }, {
                xtype: 'displayfield',
                value: _s('bLock')
            }, {
                xtype: 'checkbox',
                id: 'bLock',
                allowBlank: true
            }, {
                xtype: 'displayfield',
                value: _s('bCatalogar')
            }, {
                xtype: 'checkbox',
                id: 'bCatalogar',
                allowBlank: true
            }, {
                xtype: 'displayfield',
                value: _s('bPagado')
            }, {
                xtype: 'checkbox',
                id: 'bPagado',
                allowBlank: true
            }]
        };
        
        var estado = Ext.app.combobox({
            url: site_url('ventas/estadopedidocliente/search'),
            //anchor: "90%",
            disabled: true,
            allowBlank: false,
            readOnly: true,
            id: 'nIdEstado'
        });
        
        var tipoorigen = Ext.app.combobox({
            url: site_url('ventas/tipoorigen/search'),
            /*disabled: true,*/
            allowBlank: true,
            /*readOnly: true,*/
            id: 'nIdTipoOrigen'
        });
        
        var modopago = Ext.app.combobox({
            url: site_url('ventas/modopago/search'),
            /*disabled: true,*/
            allowBlank: false,
            /*readOnly: true,*/
            id: 'nIdModoPago'
        });
        
        var modoenvio = Ext.app.combobox({
            url: site_url('ventas/modoenvio/search'),
            /*disabled: true,*/
            allowBlank: false,
            /*readOnly: true,*/
            id: 'nIdModoEnvio'
        });
        
        var fechaenvio = new Ext.form.DateField({
            xtype: 'datefield',
            startDay: Ext.app.DATESTARTDAY,
            name: 'dEnvio',
            allowBlank: true
        });
        
        var datas = {
            xtype: 'compositefield',
            fieldLabel: _s('Fecha Envío'),
            msgTarget: 'side',
            anchor: '-20',
            items: [fechaenvio, {
                xtype: 'displayfield',
                value: _s('Tipo Origen')
            }, tipoorigen, {
                xtype: 'displayfield',
                value: _s('Estado')
            }, estado, {
                xtype: 'displayfield',
                value: _s('bMostrarWeb')
            }, {
                xtype: 'checkbox',
                id: 'bMostrarWeb',
                // anchor : '90%',
                allowBlank: true,
                fieldLabel: _s('bMostrarWeb')
            }]
        };
        
        var modos = {
            xtype: 'compositefield',
            fieldLabel: _s('Modo Pago'),
            msgTarget: 'side',
            anchor: '-20',
            items: [modopago, {
                xtype: 'displayfield',
                value: _s('Modo Envío')
            }, modoenvio,{
                xtype: 'displayfield',
                value: _s('nIdWeb')
            }, {
                xtype: 'textfield',
                cls : 'static-info-field',
                readOnly : true,
                width: 60,
                id: 'nIdWeb',
                allowBlank: true
            }]
        };
        
        // Añade el comando para guardar        
        lineas.control.addPattern("^" + Ext.app.TPV_GUARDAR + "$", function(m, c){
            form.save();
            return true;
        });
        
        // Añade el comando para cerrar
        lineas.control.addPattern("^" + Ext.app.TPV_DESCUENTO + "\\s?(\\d+)$", function(m, c){
            var v = (m[1] != null) ? parseFloat(m[1]) : 0;
            var v = v.decimal(Ext.app.DECIMALS);
            lineas.descuento(v);
            return true;
        });
        
        var anticipo = new Ext.Button({
            xtype: 'tbbutton',
            iconCls: "iconoTPV",
            text: _s('crear-anticipo'),
            handler: function(){
                //console.dir(data_load);
                if (data_load != null) {
                    if (data_load.nIdFactura != null) {
                        var a = Ext.app.currencyFormatter(data_load.fAnticipo);
                        if (data_load.nIdAlbaranDescuentaAnticipo != null) {
                            if (data_load.nIdEstadoAlbaran != 1) {
                                Ext.app.msgError(title, _s('anticipo-usado'));
                            }
                            else {
                                Ext.app.msgError(title, _s('anticipo-albaran-abierto'));
                            }
                        }
                        else {
                            if (data_load.nIdEstadoFactura != 1) {
                                Ext.Msg.show({
                                    title: this.title,
                                    buttons: Ext.MessageBox.YESNOCANCEL,
                                    msg: _s('devolver-anticipo-query'),
                                    fn: function(btn, text){
                                        if (btn == 'yes') {
                                            Ext.app.callRemote({
                                                url: site_url('ventas/pedidocliente/devolveranticipo'),
                                                params: {
                                                    id: data_load.nIdPedido
                                                },
                                                fnok: function(res){
                                                    form.refresh();
                                                    Ext.app.execCmd({
                                                        url: site_url('ventas/factura/index/' + res.id)
                                                    });
                                                }
                                            });
                                        }
                                    }
                                });
                            }
                            else {
                                Ext.app.msgError(title, _s('anticipo-factura-abierta'));
                            }
                        }
                    }
                    else {
                        // Pregunta el importe
                        Ext.Msg.prompt(title, _s('Importe del anticipo'), function(ok, v){
                            if (ok != 'ok') 
                                return;
                            
                            v = parseFloat(v);
                            Ext.app.callRemote({
                                url: site_url('ventas/pedidocliente/crearanticipo'),
                                params: {
                                    id: data_load.nIdPedido,
                                    importe: v
                                },
                                fnok: function(res){
                                    form.refresh();
                                    Ext.app.execCmd({
                                        url: site_url('ventas/factura/index/' + res.id)
                                    });
                                }
                            })
                        });
                    }
                }
            }
        });
        
        var msg = new Ext.Panel({
            cls: 'info-msg',
            autoScroll: true,
            height: 80,
            width: 400
        });
        
        var pie = {
            xtype: 'compositefield',
            fieldLabel: _s('fTotal'),
            msgTarget: 'side',
            anchor: '-20',
            items: [total, msg, anticipo]
        };

        var direccionfactura = Ext.app.combobox({
            url : site_url('clientes/perfilcliente/get_list'),
            anchor : '50%',
            extrafields : ['nIdPais'],
            label : _s('nIdDirFac'),
            name : 'nIdDirFac'
        });

        if (Ext.app.CONCURSOS) {
            var salas = Ext.app.combobox({
                url: site_url('concursos/sala/search'),
                allowBlank: true,
                id: 'nIdSala'
            });

            var bibliotecas = Ext.app.combobox({
                url: site_url('concursos/biblioteca/search'),
                allowBlank: true,
                id: 'nIdBiblioteca'
            });
        }

        var varios = {
            xtype: 'compositefield',
            fieldLabel: _s('nIdDirFac'),
            msgTarget: 'side',
            anchor: '-20',
            items: [direccionfactura, {
                xtype: 'displayfield',
                value: _s('bMantenerPrecio')
            }, {
                xtype: 'checkbox',
                id: 'bMantenerPrecio',
                allowBlank: true
            }, {
                xtype: 'displayfield',
                value: _s('bNoAvisar')
            }, {
                xtype: 'checkbox',
                id: 'bNoAvisar',
                allowBlank: true
            }]
        };
        if (Ext.app.CONCURSOS) {
            varios.items.push({
                xtype: 'displayfield',
                value: _s('nIdBiblioteca')
            });
            varios.items.push(bibliotecas);
            varios.items.push({
                xtype: 'displayfield',
                value: _s('nIdSala')
            });
            varios.items.push(salas);
        }
        // Controles normales
        var controls = [cliente, varios, refs, datas, modos, secciondefecto, lineas.linea, lineas.grid, ejemplares, pie];
        
        /*-------------------------------------------------------------------------
         * Comandos
         *-------------------------------------------------------------------------
         */
        // TABS
        documentosAddTabs(form, controls, 'form-pedidocliente');
        
        var notas = Ext.app.formNotas();
        var grid_notas = notas.init({
            id: form_id + "_notas",
            url: site_url('ventas/pedidocliente'),
            mainform: form
        });
		var notaspanel = new Ext.Panel({
            layout: 'border',
            id: form_id + "_notas",
            title: _s('Histórico'),
            iconCls: 'icon-history',
            region: 'center',
            baseCls: 'x-plain',
            frame: true,
            items: grid_notas
        });
        form.addTab(notaspanel);
        
        // Búsqueda
        var fn_open = function(id){
            form.load(id);
            form.selectTab(0);
        }
            
        var grid_search_m = search_pedidocliente(form_id, fn_open);
         
        form.addTab({
            title: _s('Búsqueda'),
            iconCls: 'icon-search',
            items: Ext.app.formSearchForm({
                grid: grid_search_m,
                audit: true,
                id_grid: form_id + '_g_search_grid'
            })
        });
        
        var Abrir = function(){
            Ext.app.callRemote({
                url: site_url('ventas/pedidocliente/abrir'),
                params: {
                    id: form.getId()
                },
                fnok: function(res){
                    form.refresh();
                }
            });
        }
        
        form.addAction({
            text: _s('Abrir'),
            handler: function(){
                Abrir();
            },
            iconCls: 'icon-generar-doc',
            id: form.idform + 'btn_abrir'
        });
        
        var fn_enviar = function(){
            documentosEnviar(form, _s('Enviar pedido'), site_url('ventas/pedidocliente/send'));
        }
        
        form.addAction({
            text: _s('Enviar'),
            handler: function(){
                fn_enviar();
            },
            //menu: [],
            iconCls: 'icon-send',
            id: form.idform + 'btn_enviar'
        });
        
        var Cancelar = function(form){
            Ext.app.callRemote({
                url: site_url('ventas/pedidocliente/cancelar'),
                params: {
                    id: form.getId()
                },
                fnok: function(res){
                    form.refresh();
                }
            });
        }
        
        form.addAction({
            text: _s('Cancelar'),
            handler: function(){
                Cancelar(form);
            },
            iconCls: 'icon-cancel',
            id: form.idform + 'btn_cancelar'
        });
        form.addAction('-');
        form.addAction({
            text: _s('Aplicar Exento IVA'),
            handler: function(){
				lineas.exentoIVA(true);
            },
            iconCls: "icon-taxes",
            id: form.idform + 'btn_exentoiva'
        });
        form.addAction('-');
        form.addAction({
            text: _s('Convertir en presupuesto'),
            handler: function(){                
                Ext.app.callRemote({
                    url: site_url('ventas/pedidocliente/presupuesto'),
                    params: {
                        id: form.getId()
                    },
                    fnok: function(res){
                        form.refresh();
                    }
                });                
            },
            iconCls: "icon-presupuesto",
            id: form.idform + 'btn_presupuesto'
        });

        form.addAction({
            text: _s('Añadir IVA'),
            handler: function(){
            var fn = function(){
                Ext.app.callRemote({
                    url: site_url('ventas/pedidocliente/add_iva'),
                    params: {
                        id: form.getId()
                    },
                    fnok: function(res){
                        form.refresh();
                    }
                });
            }
            if (form.isDirty()) {
                Ext.Msg.show({
                    title: _s('Añadir IVA'),
                    buttons: Ext.MessageBox.YESNOCANCEL,
                    msg: _s('register-dirty-lost'),
                    fn: function(btn, text){
                        if (btn == 'yes') {
                            form.setDirty(false);
                            fn();
                        }
                    }
                });
            }
            else 
                fn()
            },
            iconCls: "icon-add-taxes",
            id: form.idform + 'btn_aplicariva'
        });
        form.addAction('-');
        form.addAction({
            iconCls : "icon-excel",
            text : _s('Exportar EXCEL'),
            id : form.idform + 'btn_excel',
            handler : function() {
                Ext.app.callRemote({
                    url : site_url('ventas/pedidocliente/exportar_excel'),
                    params : {
                        id : form.getId()
                    },
                    fnok : function(res) {
                        Ext.app.askexit = false;
                        document.location = res.src;
                        setTimeout(function() {
                            Ext.app.askexit = true;
                        }, 2);
                    }
                });
            }
        });
        
        form.addAction('-');
        addButtonNegativo(form, lineas);
        addButtonAjustarMargen(form, lineas);
        
        var PediraProveedor = function(form, seccion){
            Ext.app.callRemote({
                url: site_url('ventas/pedidocliente/pedir_list'),
                timeout: false,
                params: {
                    id: form.getId(),
                    seccion: seccion
                }
            });
        }
        
        form.addTools({
            text: _s('Pedir artículos al proveedor'),
            handler: function(){
                PediraProveedor(form, false);
            },
            iconCls: 'icon-pedir',
            id: form.idform + 'btn_pedir'
        });

        form.addTools({
            text: _s('Pedir artículos al proveedor solo sección'),
            handler: function(){
                PediraProveedor(form, true);
            },
            iconCls: 'icon-pedir',
            id: form.idform + 'btn_pedir_seccion'
        });
        
        var Duplicar = function(form){
            var fn = function(){
                Ext.app.callRemote({
                    url: site_url('ventas/pedidocliente/duplicar'),
                    params: {
                        id: form.getId()
                    },
                    fnok: function(res){
                        if (res.id != null) {
                            form.load(res.id);
                        }
                    }
                });
            }
            if (form.isDirty()) {
                Ext.Msg.show({
                    title: _s('Duplicar'),
                    buttons: Ext.MessageBox.YESNOCANCEL,
                    msg: _s('register-dirty-lost'),
                    fn: function(btn, text){
                        if (btn == 'yes') {
                            form.setDirty(false);
                            fn();
                        }
                    }
                });
            }
            else 
                fn()
        }
        
        form.addTools({
            text: _s('Duplicar'),
            handler: function(){
                Duplicar(form);
            },
            iconCls: 'icon-duplicate',
            id: form.idform + 'btn_duplicar'
        });
        
        var ServirTodo = function(form){
            var store = lineas.grid.getStore();
            store.suspendEvents(false);
            store.each(function(r){
                r.set('nCantidadServida', r.data.nCantidad);
            });
            store.resumeEvents();
            lineas.grid.getView().refresh();
            form.setDirty(true);
        }
        
        form.addTools({
            text: _s('Servir todos'),
            handler: function(){
                ServirTodo(form);
            },
            iconCls: 'icon-tick',
            id: form.idform + 'btn_servir'
        });
        
        var CambiarSeccion = function(form){
            var seccion = new Ext.form.ComboBox(Ext.app.combobox({
                url: site_url('generico/seccion/search'),
                label: _s('Seccion'),
                name: 'ids',
                anchor: '90%'
            }));
            
            var controls = [{
                xtype: 'hidden',
                name: 'id',
                value: form.getId()
            }, seccion];
            
            seccion.store.load();
            var url = site_url('ventas/pedidocliente/cambiarseccion');
            
            var form2 = Ext.app.formStandarForm({
                controls: controls,
                timeout: false,
                icon: 'iconoSeccionMoverTab',
                title: _s('Cambiar de sección'),
                url: url,
                fn_ok: function(){
                    form.refresh();
                }
            });
            
            form2.show();
        }
        form.addTools({
            text: _s('Cambiar de sección'),
            handler: function(){
                CambiarSeccion(form);
            },
            iconCls: 'iconoSeccionMover',
            id: form.idform + 'btn_mover'
        });
        
        var ActualizarPrecio = function(form){
            var url = site_url('ventas/pedidocliente/actualizarprecios');
            
            Ext.app.callRemote({
                url: url,
                params: {
                    id: form.getId(),
					tarifa: cliente_datos.nIdTipoTarifa
                },
                fnok: function(){
                    form.refresh();
                }
            });
        }
        form.addTools({
            text: _s('Actualizar precios'),
            handler: function(){
                ActualizarPrecio(form);
            },
            iconCls: 'icon-precio',
            id: form.idform + 'btn_precio'
        });

        var VerAlbaranes = function(form){
            Ext.app.callRemote({
                url: site_url('ventas/pedidocliente/albaranes'),
                timeout: false,
                params: {
                    id: form.getId()
                }
            });
        }
        form.addTools({
            text: _s('Albaranes de salida'),
            handler: function(){
                VerAlbaranes(form);
            },
            iconCls: 'iconoAlbaranSalida',
            id: form.idform + 'btn_albaran'
        });
        form.addTools({
            text: _s('Resumen estado'),
            handler: function(){
            Ext.app.callRemote({
                url: site_url('ventas/pedidocliente/resumen'),
                params: {
                    id: form.getId()
                }
            });
            },
            iconCls: 'iconoReport',
            id: form.idform + 'btn_resumen'
        });
        form.addTools({
            text: _s('Coste'),
            handler: function(){
                Ext.app.callRemote({
                    url: site_url('ventas/pedidocliente/coste'),
                    timeout: false,
                    params: {
                        id: form.getId()
                    }
                });
            },
            iconCls: 'iconoConsultaPrecios',
            id: form.idform + 'btn_coste'
        });
        form.addTools('-');
        form.addTools({
            text: _s('Enviar por courier'),
            handler: function(){
                sendCourier(site_url('ventas/pedidocliente/courier'), form, total_tpv, function() {
                    fn_enviado();
                });
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
        var fn_enviado = function() {
            Ext.app.callRemote({
                url : site_url('ventas/pedidocliente/enviado/' + form.getId()),
                fnok: function(res){
                    form.refresh();
                }
            });            
        }

        form.addTools({
            text: _s('Marcar como enviado'),
            handler: function(){
                fn_enviado();
            },
            iconCls: 'iconoCourier',
            id: form.idform + 'btn_enviado'
        });
        form.addTools('-');
        form.addTools({
            text : _s('add-dir-envio-cola'),
            handler : function() {
                if (data_load != null && data_load.nIdDirEnv != null)
                {
                    Ext.app.callRemote({
                        url : site_url('etiquetas/etiqueta/colacliente/' + data_load.nIdDirEnv)
                    });
                }
            },
            iconCls : 'icon-label-cola',
            id : form.idform + 'btn_dir_env_cola'
        });
        form.addTools({
            text : _s('print-dir-envio'),
            handler : function() {
                if (data_load != null && data_load.nIdDirEnv != null)
                {
                    Ext.app.callRemote({
                        url : site_url('etiquetas/etiqueta/printcliente/' + data_load.nIdDirEnv)
                    });
                }
            },
            iconCls : 'icon-label',
            id : form.idform + 'btn_dir_env_print'
        });
        form.addTools('-');
        form.addTools({
            text : _s('add-dir-fact-cola'),
            handler : function() {
                if (data_load != null && data_load.nIdDirFac != null)
                {
                    Ext.app.callRemote({
                        url : site_url('etiquetas/etiqueta/colacliente/' + data_load.nIdDirFac)
                    });
                }
            },
            iconCls : 'icon-label-cola',
            id : form.idform + 'btn_dir_fact_cola'
        });
        form.addTools({
            text : _s('print-dir-fact'),
            handler : function() {
                if (data_load != null && data_load.nIdDirFac != null)
                {
                    Ext.app.callRemote({
                        url : site_url('etiquetas/etiqueta/printcliente/' + data_load.nIdDirFac)
                    });
                }
            },
            iconCls : 'icon-label',
            id : form.idform + 'btn_dir_fact_print'
        });
        form.addTools('-');
        form.addTools({
            text : _s('Actualizar/Publicar Internet'),
            handler : function() {
                Ext.app.callRemote({
                    url : site_url('web/webpage/syncro_pedidos/' + form.getId()),
                    fnok: function(){
                        form.refresh();
                    }
                });
            },
            iconCls : 'icon-publish',
            id : form.idform + 'btn_publicar'
        });
        
        secciondefecto.store.load();
        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
