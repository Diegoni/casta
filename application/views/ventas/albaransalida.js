(function(){
    try {
        /*-------------------------------------------------------------------------
         * Datos Formulario
         *-------------------------------------------------------------------------
         */
        var open_id = "<?php echo $open_id;?>";
        var form_id = "<?php echo $id;?>";
        var title = _s("Albarán de Salida");
        var icon = "iconoAlbaranSalidaTab";
        
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
            if (data.cliente_datos) {
                lineas.setTarifas(data.cliente_datos.nIdTipoTarifa, data.cliente_datos.tarifas);
            }
        }
        
        var fn_get_data = function(){
            return {
                cliente_id: cliente_id,
				tooltip_cliente: tooltip_cliente, 
                cliente_datos: cliente_datos,
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
            
            if (res.lineas != null) 
                lineas.load(res.lineas);
			try {
				if(res.albaransalidasuscripcion != null) {
					suscripcion.setText(_s('nIdSuscripcion') + ': ' + res.albaransalidasuscripcion[0].nIdSuscripcion);
					suscripcion.setVisible(true);
				} else {
					suscripcion.setVisible(false);
				}
			}
			catch(e)
			{
				console.dir(e);
			}
            
            fn_load_direcciones(res.nIdCliente, res.nIdDireccion);
            fn_load_cliente(res.nIdCliente);
            
            form.setDirty(false);
            lineas.control.focus();
			if (id_defecto!=null)
				 secciondefecto.setValue(id_defecto);
        }
        
        // Borrado
        var fn_reset = function(){
			suscripcion.setVisible(false);
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
			if (id_defecto!=null)
				 secciondefecto.setValue(id_defecto);
        }
        
        // Cerrar la venta
        var fn_cerrar = function(imprimir, impreso){
            var fn = function(result){
                if (result) {
                    Ext.app.callRemote({
                        url: site_url('ventas/albaransalida/cerrar'),
                        wait: true,
                        params: {
                            id: form.getId()
                        },
                        fnok: function(obj){
                            Ext.app.eventos.fire('albaransalida.close', {
                                id: form.getId(),
                                data: data_load,
                                importe: total_tpv
                            });
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

        var fn_lang = function(){
			return getLang(data_load);
        }
        
        // Guardar
        var fn_save = function(id, data){
            // Añadimos las líneas
            var index = 0;
            if (Ext.getCmp(direcciones.id).getValue() != '' && Ext.getCmp(direcciones.id).getValue() != '') 
                data['nIdDireccion'] = Ext.getCmp(direcciones.id).getValue();
            var idcliente = cliente_id;
            if ((data_load != null && idcliente != data_load.nIdCliente) || data_load == null) {
                data['nIdCliente'] = idcliente;
            }
            data = lineas.get(data);
            lineas.control.focus();
			id_defecto = secciondefecto.getValue();
            
            return data;
        }
        
        // Enables y disables
        var fn_enable_disable = function(form){
            var bloqueado = ((data_load != null) && (data_load.nIdEstado != 1));
            if (bloqueado) {
                lineas.disable();
            }
            else {
                lineas.enable();
            }
            
            if ((data_load != null) && (data_load.nIdFactura != null)) {
                factura.enable();
				examen.disable();
                factura.setText(_s('ver-factura') + ' ' + data_load.nIdFactura);
            }
            else {
				examen.enable();
                factura.disable();
                factura.setText(_s('sin-facturar'));
            }
            
            Ext.app.formEnableList({
                list: [form.idform + 'btn_cerrar3'],
                enable: (!bloqueado) && (form.getId() > 0)
            });
            Ext.app.formEnableList({
                list: [form.idform + 'btn_abonar', form.idform + 'btn_enviar'],
                enable: (bloqueado) && (form.getId() > 0)
            });
            Ext.app.formEnableList({
                list: [form.idform + 'btn_copiar', 
                    form.idform + 'btn_copiar2',
                    form.idform + 'btn_coste',
                    form.idform + 'btn_pedidos'
                    ],
                enable:(form.getId() > 0)
            });
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
            url: site_url('ventas/albaransalida'),
            fn_load: fn_load,
            fn_reset: fn_reset,
			fn_lang: fn_lang,
            fn_save: fn_save,
            fn_enable_disable: fn_enable_disable
        });
        
        var controles = documentosCliente(form, 'nIdDireccion', fn_get_data, fn_set_data, Ext.app.PERFIL_ENVIO)
        
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
        
        var s_defecto = null;
        var s_vedadas = null;
        
        var seccion_defecto = get_seccion_defecto(fn_get_data, fn_set_data, 'bp.albaransalida.secciones.defecto', 'bp.albaransalida.secciones.vedadas', allsecciones, false);
        var secciondefecto = seccion_defecto.secciondefecto;
        var fn_get_seccion = seccion_defecto.fn_get_seccion;
        
        var lineas = docLineaControl({
            nIdDocumento: 'nIdAlbaran',
            nIdLinea: 'nIdLineaAlbaran',
            cReferencia: 'cRefCliente',
            fn_get_seccion: fn_get_seccion,
            fn_change: showtotal,
            hide: Ext.app.GRIDCOLUMNS_HIDE_ALBARANSALIDA,
            anchor: "100% 50%",
            url_search: site_url('catalogo/articulo/search'),
            url_load: site_url('catalogo/articulo/get2'),
			extrafields: [ {
                hidden: true,
                dataIndex: 'nIdLineaPedido',
                hideable: false,
                sortable: false
            }]
        });
        addMenuPedir(lineas);
        addMenuDocumentos(lineas);
        addMenuVentas(lineas);
        addMenuStock(lineas);
        
        var ejemplares = new Ext.form.DisplayField({
            cls: 'lineas-ejemplares-field',
            value: 'ejemplares',
            height: 10,
            //disabled: true,
            anchor: '100%'
        });

		var suscripcion = new Ext.Button({
			xtype : 'tbbutton',
			iconCls : "iconoSuscripciones",
			text : '',
			handler : function() {
				if((data_load != null) && (data_load.albaransalidasuscripcion[0] != null)) {
					Ext.app.execCmd({
						url : site_url('suscripciones/suscripcion/index/' + data_load.albaransalidasuscripcion[0].nIdSuscripcion)
					});
				}
			}
		});
        
        var refs = {
            xtype: 'compositefield',
            fieldLabel: _s('cRefCliente'),
            msgTarget: 'side',
            anchor: '-20',
            /*defaults: {
             flex: 1
             },*/
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
                value: _s('bCobrado')
            }, {
                xtype: 'checkbox',
                id: 'bCobrado',
                // anchor : '90%',
                allowBlank: true,
                fieldLabel: _s('bCobrado')
            }, suscripcion]
        };
        
        var estado = Ext.app.combobox({
            url: site_url('ventas/estadoalbaransalida/search'),
            //anchor: "90%",
            disabled: true,
            allowBlank: false,
            readOnly: true,
            id: 'nIdEstado'
        });
        //estado.disable();
        var biblioteca = Ext.app.combobox({
            url: site_url('concursos/biblioteca/search'),
            disabled: true,
            allowBlank: false,
            readOnly: true,
            id: 'nIdBiblioteca'
        });
        var sala = Ext.app.combobox({
            url: site_url('concursos/sala/search'),
            disabled: true,
            allowBlank: false,
            readOnly: true,
            id: 'nIdSala'
        });
        
		var examen = new Ext.form.Checkbox({
                xtype: 'checkbox',
                name: 'bExamen',
                allowBlank: true
            });
		
        var fechaenvio = new Ext.form.DateField({
            xtype: 'datefield',
			startDay: Ext.app.DATESTARTDAY,
            name: 'dFechaEnvio',
            value: new Date(),
            allowBlank: true
        });
        
        var factura = new Ext.Button({
            xtype: 'tbbutton',
            iconCls: "iconoTPV",
            text: _s('ver-factura'),
            handler: function(){
                if ((data_load != null) && (data_load.nIdFactura != null)) {
                    Ext.app.execCmd({
                        url: site_url('ventas/factura/index/' + data_load.nIdFactura)
                    });
                }
            }
        });
        
        var datas = {
            xtype: 'compositefield',
            fieldLabel: _s('Fecha Envío'),
            msgTarget: 'side',
            anchor: '-20',
            /*defaults: {
             flex: 1
             },*/
            items: [fechaenvio, {
                xtype: 'displayfield',
                value: _s('bNoFacturable')
            }, {
                xtype: 'checkbox',
                id: 'bNoFacturable',
                //anchor: '90%',
                allowBlank: true
            }, {
                xtype: 'displayfield',
                value: _s('bMostrarWeb')
            }, {
                xtype: 'checkbox',
                checked: true,
                value: true,
                id: 'bMostrarWeb',
                //anchor: '90%',
                allowBlank: true
            }, estado, {
                xtype: 'displayfield',
                value: _s('bExamen')
            }, examen, factura]
        };
        
        // Añade el comando para guardar        
        lineas.control.addPattern("^" + Ext.app.TPV_GUARDAR + "$", function(m, c){
            form.save();
            return true;
        });
        
        // Añade el comando para cerrar
        lineas.control.addPattern("^" + Ext.app.TPV_CERRAR + "$", function(m, c){
            fn_cerrar();
            return true;
        });

        form.addKeyMap({
            key: Ext.app.KEYMAP_FORM_CLOSEDOC,
            ctrl: Ext.app.KEYMAP_FORM_CTRL,
            alt: Ext.app.KEYMAP_FORM_ALT,
            shift: Ext.app.KEYMAP_FORM_SHIFT,
            stopEvent: true,
            fn: function(){
                fn_cerrar();
            }
        });
        
        lineas.control.addPattern("^" + Ext.app.TPV_DESCUENTO + "\\s?(\\d+)$", function(m, c){
            var v = (m[1] != null) ? parseFloat(m[1]) : 0;
            var v = v.decimal(Ext.app.DECIMALS);
            lineas.descuento(v);
            return true;
        });
        
        
        lineas.control.addPattern("^" + Ext.app.TPV_ADD_PEDIDO_CLIENTE + "\\s?(\\d+)$", function(m, c){
            var v = (m[1] != null) ? (m[1]) : 0;
            //console.log('Add pedido: ' + v);
            fn_docs_select_lineas_pedido(v, function(libros){
                var ct = 0;
                Ext.each(libros, function(r){
                	r.nIdLineaPedido = r.id;
                    lineas.add(r);
                    ct++;
                });
                if (ct > 0) {
                    Ext.app.callRemote({
                        url: site_url('ventas/pedidocliente/get/' + v),
                        fnok: function(res){
                            fn_load_direcciones(res.value_data.nIdCliente, res.value_data.nIdDirFac);
                            fn_load_cliente(res.value_data.nIdCliente);
                        }
                    });
                }
            });
            return true;
        });
        var msg = new Ext.Panel({
            cls: 'info-msg',
			autoScroll: true,
            height: 80,
            width: 500
        });

        var pie = {
            xtype: 'compositefield',
            fieldLabel: _s('fTotal'),
            msgTarget: 'side',
            anchor: '-20',
            items: [total, msg]
        };

        var varios = {
            xtype: 'compositefield',
            fieldLabel: _s('Sección def.'),
            msgTarget: 'side',
            anchor: '-20',
            items: [secciondefecto,{
                xtype: 'displayfield',
                value: _s('nIdBiblioteca')
            }, biblioteca, {
                xtype: 'displayfield',
                value: _s('nIdSala')
            }, sala]
        };
        
        // Controles normales
        var controls = [cliente, refs, datas, varios, lineas.linea, lineas.grid, ejemplares, pie];
        
        // TABS
        documentosAddTabs(form, controls, 'form-albaransalida');
        
        /*-------------------------------------------------------------------------
         * Comandos
         *-------------------------------------------------------------------------
         */
        form.addCommand({
            text: _s('Cerrar albarán'),
            iconCls: 'icon-generar-doc',
            handler: fn_cerrar,
            id: form.idform + 'btn_cerrar3'
        });

        var notas = Ext.app.formNotas();
        var grid_notas = notas.init({
            id: form_id + "_notas",
            url: site_url('ventas/albaransalida'),
            mainform: form
        });
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
        
         <?php $modelo = $this->reg->get_data_model(array('nIdFactura', 'nIdDireccion'));?>
         var grid_search = <?php echo extjs_creategrid($modelo, $id.'_g_search', null, null, 'ventas.albaransalida', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
         var fn_open = function(id){
         form.load(id);
         form.selectTab(0);
         }
         
        form.addTab({
            title: _s('Búsqueda'),
            iconCls: 'icon-search',
            items: Ext.app.formSearchForm({
                grid: grid_search,
				audit: true,
                id_grid: form_id + '_g_search_grid'
            })
        });
        
        addButtonAbiertos(form);
        form.addAction('-');
        var fn_enviar = function(){
            documentosEnviar(form, _s('Enviar albarán'), site_url('ventas/albaransalida/send'));
        }
        
        form.addAction({
            text: _s('Enviar'),
            handler: function(){
                fn_enviar();
            },
            iconCls: 'icon-send',
            id: form.idform + 'btn_enviar'
        });
        form.addAction('-');
        addButtonAbonar(form, 'ventas/albaransalida/abonar');
        form.addAction('-');
        addButtonNegativo(form, lineas);
        addButtonAjustarMargen(form, lineas);
        
        form.addTools({
            text: _s('Copiar referencia interna'),
            handler: function(){
                Ext.app.callRemote({
                    url: site_url('ventas/albaransalida/copiarrefinterna'),
                    params: {
                        id: form.getId()
                    },
                    fnok: function(res){
                          form.refresh();
                    }
                });
            },
            iconCls: 'icon-copy',
            id: form.idform + 'btn_copiar'
        });
        form.addTools({
            text: _s('Copiar referencia cliente'),
            handler: function(){
                Ext.app.callRemote({
                    url: site_url('ventas/albaransalida/copiarrefcliente'),
                    params: {
                        id: form.getId()
                    },
                    fnok: function(res){
                          form.refresh();
                    }
                });
            },
            iconCls: 'icon-copy',
            id: form.idform + 'btn_copiar2'
        });
        form.addTools('-');
        form.addTools({
            text: _s('Pedidos de cliente'),
            handler: function(){
                Ext.app.callRemote({
                    url: site_url('ventas/albaransalida/pedidos'),
                    timeout: false,
                    params: {
                        id: form.getId()
                    }
                });
            },
            iconCls: 'iconoPedidoCliente',
            id: form.idform + 'btn_pedidos'
        });
        form.addTools({
            text: _s('Coste'),
            handler: function(){
                Ext.app.callRemote({
                    url: site_url('ventas/albaransalida/coste'),
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
                sendCourier(site_url('ventas/albaransalida/courier'), form, total_tpv);
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
                        url : site_url('etiquetas/etiqueta/colacliente/' + data_load.nIdDireccion)
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
                        url : site_url('etiquetas/etiqueta/printcliente/' + data_load.nIdDireccion)
                    });
                }
            },
            iconCls : 'icon-label',
            id : form.idform + 'btn_dir_env_print'
        });
        
        secciondefecto.store.load();
        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
