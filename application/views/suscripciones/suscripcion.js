(function() {
	try {
		var open_id = "<?php echo $open_id;?>";
		var form_id = "<?php echo $id;?>";
		var title = "<?php echo $title;?>";
		var icon = "<?php echo $icon;?>";
		if(title == '')
			title = _s('Suscripción');
		if(icon == '')
			icon = 'iconoSuscripcionesTab';

		var list_grids = []

		var iva = null;

		// Carga
		var fn_load = function(id, res) {
			data_load = res;
			notas.load(id);
			fn_load_direcciones(res.nIdCliente, res.nIdDireccionFactura);
			load_combo_direcciones(res.nIdCliente, direccionenvio, res.nIdDireccionEnvio, Ext.app.PERFIL_ENVIO);
			fn_load_cliente(res.nIdCliente);

			var p = Ext.getCmp(revista.id);
			p.setValue(res.nIdRevista);
			iva = res.fIVA;

			try {
				var panel = Ext.getCmp(form_id + "details-panel");
				panel.setSrc(res.info);
			} catch (e) {
				console.dir(e);
			}
		}
		var fn_save = function(id, data) {
			data['nIdDireccionFactura'] = Ext.getCmp(direcciones.id).getValue();
			data['nIdDireccionEnvio'] = Ext.getCmp(direccionenvio.id).getValue();
			data['nIdCliente'] = Ext.getCmp(clientefield.id).getValue();
			data['nIdRevista'] = Ext.getCmp(revista.id).getValue();
			var modo = (data['nIdTipoEnvio'] != null)?data['nIdTipoEnvio']:((data_load != null && data_load.nIdTipoEnvio != null)?data_load.nIdTipoEnvio:null);
			
			// Comprueba el cambio de dirección
			if (modo == 1 && data_load!= null && data_load.nIdDireccionEnvio != null && data_load.nIdDireccionEnvio != data['nIdDireccionEnvio']) {
				Ext.app.callRemoteAsk({
					title: title,
					askmessage: _s('suscripcion-cambio-direccion-q'),
					url : site_url('suscripciones/suscripcion/cambio_direccion'),
					params : {
							id: form.getId(),
							old_id : data_load.nIdDireccionEnvio,
							new_id : data['nIdDireccionEnvio']
						},
					fnok : function(res) {
						form.refresh();
						Ext.app.execCmd({
							url : site_url('suscripciones/reclamacion/index/' + res.id)
						});
					}
				});
			}
			return data;
		}
		// Borrado
		var fn_reset = function() {
			var panel = Ext.getCmp(form_id + "details-panel");
			panel.setSrc('about:blank');
			iva = 0;
		}
		var fn_enable_disable = function(form) {
			Ext.app.formEnableList({
				list : [form.idform + 'btn_enviar', form.idform + 'btn_pedir', 
				form_id + 'btn_precios', 
				form_id + 'btn_reclamar',
				form_id + 'btn_reclamar_cliente',
				form_id + 'btn_realizar_envio',
				form_id + 'btn_courier',
				form_id + 'btn_courieretq', 
				form_id + 'btn_clientes'],
				enable : (form.getId() > 0)
			});
            Ext.app.formEnableList({
                list: [form.idform + 'btn_courieretq'],
                enable: (form.getId() > 0) && (data_load != null) && (data_load.cIdShipping != '') && (data_load.cIdShipping != null)
            });
			Ext.app.formEnableList({
				list : [form_id + 'btn_aviso_cancelar', 
				form_id + 'btn_aviso_aceptar'],
				enable : (form.getId() > 0) && (data_load!=null) 
					&& (data_load.avisos[0]!=null)  && (data_load.avisos[0]['dGestionada']==null)
			});

			//console.dir(data_load);
			Ext.app.formEnableList({
				list : [form.idform + 'btn_cancelar'],
				enable : (form.getId() > 0) && (data_load!=null) && (data_load.bActiva != 0)
			});

			//console.dir(data_load);
			Ext.app.formEnableList({
				list : [form.idform + 'btn_activar'],
				enable : (form.getId() > 0) && (data_load!=null) && (data_load.bActiva == 0)
			});

			//console.dir(data_load);
			Ext.app.formEnableList({
				list : [form.idform + 'btn_resetanticipada'],
				enable : (form.getId() > 0) && (data_load!=null) && (data_load.nFacturas > data_load.nEntradas)
			});			
			Ext.app.formEnableList({
				list : [form.idform + 'btn_dir_env_print',
					form.idform + 'btn_dir_env_cola'
					],
				enable : (form.getId() > 0) && (data_load!=null) && (data_load.nIdDireccionEnvio != null)
			});			
			Ext.app.formEnableList({
				list : [form.idform + 'btn_dir_fact_print',
					form.idform + 'btn_dir_fact_cola'
					],
				enable : (form.getId() > 0) && (data_load!=null) && (data_load.nIdDireccionFactura != null)
			});			
		}
		
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

		var fn_set_data = function(data) {
			if(data.cliente_id)
			{
				cliente_id = data.cliente_id;
				if (data.nIdDireccionEnvio!=null || form.getId() == null)
				load_combo_direcciones(cliente_id, direccionenvio, data.nIdDireccionEnvio, Ext.app.PERFIL_ENVIO);
			}
			if(data.cliente_datos)
				cliente_datos = data.cliente_datos;
			if(data.info_button)
				info_button = data.info_button;
			if(data.data_load)
				data_load = data.data_load;
			if(data.title)
				title = data.title;
			if(data.direcciones)
				direcciones = data.direcciones;
			if(data.s_defecto)
				s_defecto = data.s_defecto;
			if(data.s_vedadas)
				s_vedadas = data.s_vedadas;
			if(data.tooltip_cliente) {
				tooltip_cliente = data.tooltip_cliente;
				try {
					msg.update(data.tooltip_cliente);
				} catch (e) {
				}
			}
		}
		var fn_get_data = function() {
			return {
				cliente_id : cliente_id,
				cliente_datos : cliente_datos,
				tooltip_cliente : tooltip_cliente,
				info_button : info_button,
				data_load : data_load,
				title : title,
				direcciones : direcciones,
				s_defecto : s_defecto,
				s_vedadas : s_vedadas
			}
		}
		var fn_lang = function() {
			return getLang(data_load);
		}
		// Formulario
		var form = Ext.app.formGeneric();
		form.init({
			id : form_id,
			title : title,
			icon : icon,
			url : site_url('suscripciones/suscripcion'),
			fn_load : fn_load,
			fn_lang : fn_lang,
			fn_save : fn_save,
			fn_reset : fn_reset,
			fn_enable_disable : fn_enable_disable
		});

		var controles = documentosCliente(form, 'nIdDireccionFactura', fn_get_data, fn_set_data, Ext.app.PERFIL_SUSCRIPCIONES);

		var direccionenvio = Ext.app.combobox({
			url : site_url('clientes/perfilcliente/get_list'),
			anchor : '90%',
			extrafields : ['nIdPais'],
			label : _s('nIdDireccionEnvio'),
			name : 'nIdDireccionEnvio'
		});

		// Carga direcciones
		var cliente = controles.cliente;
		var info_button = controles.info_button;
		var clientefield = controles.clientefield;
		var direcciones = controles.direcciones;
		var fn_load_direcciones = controles.fn_load_direcciones;
		var fn_load_cliente = controles.fn_load_cliente;

		var msg = new Ext.Panel({
			cls : 'info-msg',
			autoScroll : true,
			anchor : '100%'
			/*height: 80,
			 width: 600*/
		});

		var refs = {
			xtype : 'compositefield',
			fieldLabel : _s('cRefCliente'),
			msgTarget : 'side',
			anchor : '-20',
			items : [{
				xtype : 'textfield',
				id : 'cRefCliente',
				allowBlank : true,
				width : '250'
			}, {
				xtype : 'displayfield',
				value : _s('cRefProveedor')
			}, {
				xtype : 'textfield',
				id : 'cRefProveedor',
				allowBlank : true,
				width : '250'
			}]
		};

		var precio = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fPrecio',
			width : 50,
			value : 0,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			style : 'text-align:left',
			selectOnFocus : true,
			allowBlank : true,
			listeners : {
				change : function(me, e) {
					pvp.setValue(AplicarIVA(me.getValue(), iva));
				}
			}
		});

		var pvp = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fPVP',
			value : 0,
			width : 50,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			style : 'text-align:left',
			selectOnFocus : true,
			allowBlank : true,
			listeners : {
				change : function(me, e) {
					precio.setValue(QuitarIVA(me.getValue(), iva));
				}
			}
		});
		var descuento = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fDescuento',
			value : 0,
			width : 50,
			maxValue: 100,
			minValue: 0, 
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			style : 'text-align:left',
			selectOnFocus : true,
			allowBlank : true,
			listeners : {
				change : function(me, e) {
					precio.setValue(QuitarIVA(me.getValue(), iva));
				}
			}
		});

		var revista = /*new Ext.ux.form.SuperBoxSelect*/(Ext.app.autocomplete2({
			url : site_url('catalogo/articulo/revista'),
			name : 'nIdRevista_',
			anchor : '90%',
			create : true,
			fieldLabel : _s('Publicación')
		}));

		var envio = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('suscripciones/tipoenvio/search'),
			name : 'nIdTipoEnvio',
			fieldLabel : _s('nIdTipoEnvio'),
			allowBlank : true
		}));

		// Controles normales
		var controls = [cliente, direccionenvio, refs, revista, new Ext.form.DateField({
			xtype : 'datefield',
			startDay : Ext.app.DATESTARTDAY,
			name : 'dInicio',
			fieldLabel: _s('dInicio'),
			value : new Date(),
			allowBlank : true
		}), new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'nDuracion',
			width : 50,
			value : 0,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : false,
			decimalPrecision : Ext.app.DECIMALS,
			selectOnFocus : true,
			fieldLabel : _s('nDuracion'),
			allowBlank : true
		}), new Ext.form.DateField({
			xtype : 'datefield',
			startDay : Ext.app.DATESTARTDAY,
			name : 'dRenovacion',
			fieldLabel: _s('dRenovacion'),
			allowBlank : true
		}), new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'nEjemplares',
			fieldLabel : _s('nEjemplares'),
			width : 50,
			value : 0,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : false,
			decimalPrecision : Ext.app.DECIMALS,
			selectOnFocus : true,
			allowBlank : true
		}), envio, {
			xtype : 'fieldset',
			items : [{
				xtype : 'compositefield',
				fieldLabel : _s('fPrecio'),
				items : [precio, {
					xtype : 'displayfield',
					value : _s('fPVP')
				}, pvp, {
					xtype : 'displayfield',
					value : _s('fDescuento')
				}, descuento]
			}]
		}, {
            fieldLabel: _s('bNoFacturable'),
            xtype: 'checkbox',
            checked: false,
            value: false,
            id: 'bNoFacturable',
            allowBlank: true
          }, msg];

		// General
		form.addTab({
			title : _s('Vista'),
			iconCls : 'icon-report',
			items : {
				cls : 'form-suscripcion',
				id : form_id + "details-panel",
				xtype : 'iframepanel'
			}
		});

		form.addTab({
			title : _s('General'),
			iconCls : 'icon-general',
			items : {
				xtype : 'panel',
				layout : 'form',
				cls : 'form-suscripcion',
				items : form.addControls(controls)
			}
		});

		var notas = Ext.app.formNotas();
		var grid_notas = notas.init({
			id : form_id + "_notas",
			url : site_url('suscripciones/suscripcion'),
			mainform : form
		});
		form.addTab(new Ext.Panel({
			layout : 'border',
			id : form_id + "_notas",
			title : _s('Histórico'),
			iconCls : 'icon-history',
			region : 'center',
			baseCls : 'x-plain',
			frame : true,
			items : grid_notas
		}));

		// Usuarios
		form.addTabUser();
		// Búsqueda
		var fn_open = function(id) {
			form.load(id);
			form.selectTab(0);
		}
		
        var grid_search_m = search_suscripciones(form_id, fn_open);

		form.addTab({
			title : _s('Búsqueda'),
			iconCls : 'icon-search',
			items : Ext.app.formSearchForm({
                grid: grid_search_m,
                //audit: false,
                id_grid: form_id + '_g_search_grid'
			})
		});

        var temp = new Ext.form.TextField();
        temp.refresh = function() {
        	form.refresh();
        }

		form.addTools({
			text : _s('No anticipada'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('suscripciones/suscripcion/resetanticipada'),
					params : {
							id: form.getId()
						},
					fnok : function() {
						form.refresh();
					}
				});
			},
			iconCls : 'icon-tool',
			id : form.idform + 'btn_resetanticipada'
		});
		form.addTools('-');
		form.addTools({
			text : _s('precios_articulo'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('suscripciones/suscripcion/historicoprecios/' + form.getId())
				});
			},
			iconCls : 'icon-precio',
			id : form.idform + 'btn_precios'
		});
		form.addTools({
			text : _s('historico_clientes'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('suscripciones/suscripcion/historicoclientes/' + form.getId())
				});
			},
			iconCls : 'iconoClientes',
			id : form.idform + 'btn_clientes'
		});
        form.addTools('-');
        form.addTools({
            text: _s('Enviar por courier'),
            handler: function(){
                sendCourier(site_url('suscripciones/suscripcion/courier'), form, pvp.getValue());
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
				if (data_load != null && data_load.nIdDireccionEnvio != null)
				{
					Ext.app.callRemote({
						url : site_url('etiquetas/etiqueta/colacliente/' + data_load.nIdDireccionEnvio)
					});
				}
			},
			iconCls : 'icon-label-cola',
			id : form.idform + 'btn_dir_env_cola'
		});
		form.addTools({
			text : _s('print-dir-envio'),
			handler : function() {
				if (data_load != null && data_load.nIdDireccionEnvio != null)
				{
					Ext.app.callRemote({
						url : site_url('etiquetas/etiqueta/printcliente/' + data_load.nIdDireccionEnvio)
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
				if (data_load != null && data_load.nIdDireccionFactura != null)
				{
					Ext.app.callRemote({
						url : site_url('etiquetas/etiqueta/colacliente/' + data_load.nIdDireccionFactura)
					});
				}
			},
			iconCls : 'icon-label-cola',
			id : form.idform + 'btn_dir_fact_cola'
		});
		form.addTools({
			text : _s('print-dir-fact'),
			handler : function() {
				if (data_load != null && data_load.nIdDireccionFactura != null)
				{
					Ext.app.callRemote({
						url : site_url('etiquetas/etiqueta/printcliente/' + data_load.nIdDireccionFactura)
					});
				}
			},
			iconCls : 'icon-label',
			id : form.idform + 'btn_dir_fact_print'
		});
		
		form.addAction({
			text : _s('Pedir al proveedor'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('suscripciones/suscripcion/crear_pedido'),
					params : {
							id: form.getId()
						},
					fnok : function() {
						form.refresh();
					}
				});
			},
			iconCls : 'iconoPedidoProveedor',
			id : form.idform + 'btn_pedir'
		});
        form.addAction('-');
		form.addAction({
			text : _s('Reclamación de cliente'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('suscripciones/suscripcion/reclamar_cliente'),
					params : {
							id: form.getId(),
		                    cmpid: temp.id
						}
				});
			},
			iconCls : 'iconoSuscripcionesReclamaciones',
			id : form.idform + 'btn_reclamar_cliente'
		});
		form.addAction({
			text : _s('Reclamar pedido proveedor'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('suscripciones/suscripcion/reclamar_pedido'),
					params : {
							id: form.getId()
						},
					fnok : function(res) {
						form.refresh();
						Ext.app.execCmd({
							url : site_url('suscripciones/reclamacion/index/' + res.id)
						});
					}
				});
			},
			iconCls : 'iconoReclamacionPedidoProveedor',
			id : form.idform + 'btn_reclamar'
		});
        form.addAction('-');
		form.addAction({
			text : _s('Aceptar aviso renovación'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('suscripciones/avisorenovacion/aceptar'),
					params : {
							id: data_load.avisos[0].nIdAvisoRenovacion,
                    		cmpid: temp.id
						}
				});
			},
			iconCls : 'icon-accept-aviso',
			id : form.idform + 'btn_aviso_aceptar'
		});
		form.addAction({
			text : _s('Cancelar aviso renovacion'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('suscripciones/avisorenovacion/cancelar'),
					params : {
							id: data_load.avisos[0].nIdAvisoRenovacion,
                    		cmpid: temp.id
						}
				});
			},
			iconCls : 'icon-cancel-aviso',
			id : form.idform + 'btn_aviso_cancelar'
		});
        form.addAction('-');
		form.addAction({
			text : _s('Realizar envío'),
			handler : function() {
				notas.crearNota(null, 4);
			},
			iconCls : 'cell-nota4',
			id : form.idform + 'btn_realizar_envio'
		});
        form.addAction('-');

        var Activar = function(form){
            Ext.app.callRemote({
                url: site_url('suscripciones/suscripcion/activar'),
                params: {
                    id: form.getId(),
                    cmpid: temp.id
                }
            });
        }
        
        form.addAction({
            text: _s('Activar'),
            handler: function(){
                Activar(form);
            },
            iconCls: 'icon-accept',
            id: form.idform + 'btn_activar'
        });
        form.addAction('-');
        var Cancelar = function(form){
            Ext.app.callRemote({
                url: site_url('suscripciones/suscripcion/cancelar'),
                params: {
                    id: form.getId(),
                    cmpid: temp.id,
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

		envio.store.load();

		return form.show(open_id);
	} catch (e) {
		console.dir(e);
	}
})();
