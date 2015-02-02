(function() {
	try {
		/*-------------------------------------------------------------------------
		 * Datos Formulario
		 *-------------------------------------------------------------------------
		 */
		var open_id = "<?php echo $open_id;?>";
		var form_id = "<?php echo $id;?>";
		var title = _s("Pedido a proveedor");
		var icon = "iconoPedidoProveedorTab";

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

		var fn_set_data = function(data) {
			if(data.cliente_id)
				cliente_id = data.cliente_id;
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
				msg.update(data.tooltip_cliente);
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
		// Carga la venta
		var fn_load = function(id, res) {
			notas.load(id);
			data_load = res;
			if(res.lineas != null)
				lineas.load(res.lineas);

			fn_load_direcciones(res.nIdProveedor, res.nIdDireccion);
			fn_load_cliente(res.nIdProveedor);
			try {
				if(res.pedidosuscripcion != null && res.pedidosuscripcion[0] != null) {
					suscripcion.setText(res.pedidosuscripcion[0].nIdSuscripcion);
					suscripcion.setVisible(true);
				} else {
					suscripcion.setVisible(false);
				}
			} catch(e) {
				console.dir(e);
			}
			numero.setValue((res.nIdEstado == 1) ? _s('pedido-proveedor-abierto') : '');

			form.setDirty(false);
			lineas.control.focus();
			if(id_defecto != null)
				secciondefecto.setValue(id_defecto);
			//console.log('Set ' + res.nIdEntrega);
			if (res.nIdEntrega != null) 
				direccionenvio.setValue(res.nIdEntrega);
			if (res.nIdSeccion != null) 
				seccion.setValue(res.nIdSeccion);
		}
		var fn_lang = function() {
			return getLang(data_load);
		}
		// Borrado
		var fn_reset = function() {
			suscripcion.setVisible(false);
			notas.reset();
			msg.update('');
			cliente_datos = null;
			data_load = null;
			lineas.clear();
			total_tpv = 0;
			cliente_id = null;
			direccionenvio.setValue(parseInt(Ext.app.get_config('bp.compras.direcciones.default')));

			form.setData({
				value_data : {
					'nIdEstado' : 1
				}
			}, true);
			lineas.control.focus();
			if(id_defecto != null)
				secciondefecto.setValue(id_defecto);
		}
		// Guardar
		var fn_save = function(id, data) {
			// Añadimos las líneas
			var index = 0;
			data['nIdEntrega'] = direccionenvio.getValue();
			data['nIdSeccion'] = seccion.getValue();
			if(Ext.getCmp(direcciones.id).getValue() != '' && Ext.getCmp(direcciones.id).getValue() != '')
				data['nIdDireccion'] = Ext.getCmp(direcciones.id).getValue();
			var idcliente = cliente_id;
			if((data_load != null && idcliente != data_load.nIdCliente) || data_load == null) {
				data['nIdProveedor'] = idcliente;
			}
			data = lineas.get(data);
			//console.dir(data);
			lineas.control.focus();
			id_defecto = secciondefecto.getValue();

			return data;
		}
		// Enables y disables
		var fn_enable_disable = function(form) {
			notas.enable(form.getId() > 0);
			var bloqueado = ((data_load != null) && (data_load.nIdEstado != 1 && data_load.nIdEstado != 5 && data_load.nIdEstado != 6));
			if(bloqueado) {
				//fechafactura.disable();
				lineas.disable();
			} else {
				//fechafactura.enable();
				lineas.enable();
			}

			Ext.app.formEnableList({
				list : [form.idform + 'btn_notas', form.idform + 'btn_pedir', form.idform + 'btn_excel'],
				enable : (form.getId() > 0)
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_cerrar3', form.idform + 'btn_cancelar', form.idform + 'btn_reclamar'],
				enable : (!bloqueado) && (form.getId() > 0)
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_unificar'],
				enable : (!bloqueado) && (form.getId() > 0) && (data_load.nIdEstado == 1)
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_cancelar', form.idform + 'btn_cancelar_avisar', form.idform + 'btn_reclamar'],
				enable : ((data_load != null) && ((data_load.nIdEstado == 2) || (data_load.nIdEstado == 4)))
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_cerrar_excel'],
				enable : ((data_load != null) && (data_load.nIdEstado == 1) && (form.getId() > 0))
			});

			var m = Ext.getCmp(form_id + 'btn_cerrar_menu');
			var m4 = Ext.getCmp(form_id + 'btn_cerrar_menu2');
			var m2 = Ext.getCmp(form_id + 'btn_enviar2');
			var m3 = Ext.getCmp(form_id + 'btn_enviar');
			m.disable();
			m2.disable();
			m3.disable();
			m.setText(_s('Cerrar'));
			m4.disable();
			m2.setText(_s('Cerrar y enviar'));
			if(data_load != null)
			{
				if (data_load.nIdEstado == 1) {
					m.enable();
					m4.enable();
					m2.enable();
				} else if(data_load.nIdEstado == 2) {
					m.enable();
					m2.enable();
					m3.enable();
					m.setText(_s('Abrir'));
					m2.setText(_s('Reenviar'));

				} else if(data_load.nIdEstado == 4) {
					m2.enable();
					m3.enable();
					m2.setText(_s('Reenviar'));
				}
			}
            Ext.app.formEnableList({
                list : [form.idform + 'btn_dir_env_print',
                    form.idform + 'btn_dir_env_cola'
                    ],
                enable : (form.getId() > 0) && (data_load!=null) && (data_load.nIdDireccion != null)
            });         
		}
		// Formulario
		var form = Ext.app.formGeneric();
		form.init({
			id : form_id,
			title : title,
			icon : icon,
			url : site_url('compras/pedidoproveedor'),
			fn_load : fn_load,
			fn_reset : fn_reset,
			fn_lang : fn_lang,
			fn_save : fn_save,
			fn_enable_disable : fn_enable_disable
		});

		var controles = documentosProveedor(form, 'nIdDireccion', fn_get_data, fn_set_data, Ext.app.PERFIL_PEDIDO);

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
			cls : 'total-field',
			readOnly : true,
			fieldLabel : _s('fTotal'),
			value : Ext.app.currencyFormatter(0),
			height : 50,
			width : 160
		});
		var base = new Ext.form.DisplayField({
			cls : 'pendiente-field',
			value : Ext.app.currencyFormatter(0),
			height : 50,
			width : 110
		});

		var showtotal = function(s) {
			var t = 0;
			var ct = 0;
			var ejs = 0;
			var b = 0;
			s.each(function(r) {
				t += r.data.fTotal;
				b += r.data.fImporte;
				ct++;
				ejs += r.data.nCantidad;
			})
			total_tpv = t.decimal(Ext.app.DECIMALS);
			total.setValue(Ext.app.numberFormatter(t));
			base.setValue(Ext.app.numberFormatter(b));
			ejemplares.setValue(sprintf(_s('lineas-ejemplares'), ct, ejs));
			form.setDirty();
		}
		var seccion_defecto = get_seccion_defecto(fn_get_data, fn_set_data, 'bp.pedidoproveedor.secciones.defecto', 'bp.pedidoproveedor.secciones.vedadas', allsecciones, false);
		var secciondefecto = seccion_defecto.secciondefecto;
		var fn_get_seccion = seccion_defecto.fn_get_seccion;

		var hide = Ext.app.GRIDCOLUMNS_HIDE_PEDIDOPROVEEDOR;

		var fn_get_descuento = function(data) {
			///console.dir(data);
			if(cliente_datos == null) {
				fn_load_direcciones(data.nIdProveedor);
				fn_load_cliente(data.nIdProveedor);
				Ext.app.msgFly(title, _s('no-proveedor-select'));
				return;
			}
			var dto = cliente_datos.fDescuento;
			var si = false;
			if(cliente_datos) {
				// Busca el descuento por defecto
				Ext.each(data.descuentos, function(item) {
					if(item.nIdProveedor == cliente_datos.nIdProveedor) {
						si = true;
						dto = item.fDescuento;
						return false;
					}
				});
			}
			if(!si) {
				//Ext.app.msgFly(title, _s('pedidor-proveedor-no-proveedor'));
				lineas.info(_s('pedidor-proveedor-no-proveedor'));
			} else if(si && dto == null) {
				//Ext.app.msgFly(title, _s('pedidor-proveedor-no-proveedor-descuento'));
				lineas.info(_s('pedidor-proveedor-no-proveedor-descuento'));
			}
			return dto;
		}
		var lineas = docLineaControl({
			nIdDocumento : 'nIdPedido',
			nIdLinea : 'nIdLinea',
			cReferencia : 'cRefProveedor',
			coste : false,
			firmedeposito : false,
			fn_get_seccion : fn_get_seccion,
			fn_change : showtotal,
			hide : hide,
			anchor : "100% 50%",
			fn_get_descuento : fn_get_descuento,
			url_search : site_url('catalogo/articulo/search'),
			url_load : site_url('catalogo/articulo/get3'),
			url_descuentos : site_url('catalogo/articulo/descuentos'),
			extrafields : [{
				header : _s('Pendientes'),
				hidden : in_array('nPendientes', hide),
				width : Ext.app.TAM_COLUMN_NUMBER,
				dataIndex : 'nPendientes',
				sortable : true
			}, {
				header : _s('cEstado'),
				hidden : in_array('cEstado', hide),
				width : Ext.app.TAM_COLUMN_TEXT,
				dataIndex : 'cEstado',
				sortable : true
			}, {
				header : _s('cInformacion'),
				hidden : in_array('Informacion', hide),
				width : Ext.app.TAM_COLUMN_TEXT,
				dataIndex : 'cInformacion',
				renderer : renderInfo,
				sortable : true
			}, {
				hidden : true,
				dataIndex : 'nIdInformacion',
				hideable : false,
				sortable : false
			}, {
				hidden : true,
				dataIndex : 'nIdEstado',
				hideable : false,
				sortable : false
			}]
		});

		addMenuDocumentos(lineas);
		addMenuVentas(lineas);
		addMenuStock(lineas);
		addMenuSeparator(lineas);

		var accion = function(url, refresh) {
			var grid = lineas.grid;
			var codes = Ext.app.gridGetChecked(grid, 'nIdLinea');
			if(codes == null) {
				Ext.app.msgFly(title, _s('no-libros-marcados'));
				return;
			}
			grid.getEl().mask();
			Ext.app.callRemote({
				url : url,
				timeout : false,
				wait : true,
				params : {
					id : codes
				},
				fnok : function() {
					grid.getEl().unmask();
					if(refresh === true)
						form.refresh();
				},
				fnnok : function() {
					grid.getEl().unmask();
				}
			});
		}

		var m_reclamar = lineas.addMenu({
			iconCls : "iconoReclamacionPedidoProveedor",
			text : _s('Reclamar'),
			handler : function() {
				accion(site_url('compras/reclamacion/crear'), false);
			}
		});
		addMenuSeparator(lineas);

		var m_cancelar = addMenuCancelar(form, lineas, site_url('compras/pedidoproveedorlinea/cancelar'));

		var m_cancelar2 = lineas.addMenu({
			iconCls : "iconoCancelacionPedidoProveedor",
			text : _s('Cancelar y avisar'),
			handler : function() {
				accion(site_url('compras/cancelacion/crear'), true);
			}
		});

		var count = 0;
		Ext.app.callRemote({
			url : site_url('compras/informacionproveedor/get_list'),
			fnok : function(res) {
				addMenuSeparator(lineas);
				count = 0
				Ext.each(res.value_data, function(item) {
					lineas.addMenu({
						iconCls : "icon-status-" + item.id,
						text : _s(item.cDescripcion),
						id : form_id + 'cancel_' + count,
						handler : function() {
							accion(site_url('compras/pedidoproveedorlinea/info/' + item.id), true);
						}
					});
					++count;
				});
			}
		});
		var fn_check_menu = function(item) {
			(item.data.nIdLinea != null && (item.data.nIdEstado == 2 || item.data.nIdEstado == 4)) ? m_cancelar.enable() : m_cancelar.disable();
			(item.data.nIdLinea != null && (item.data.nIdEstado == 2 || item.data.nIdEstado == 4)) ? m_cancelar2.enable() : m_cancelar2.disable();
			(item.data.nIdLinea != null && (item.data.nIdEstado == 2 || item.data.nIdEstado == 4)) ? m_reclamar.enable() : m_reclamar.disable();
			for(var i = 0; i < count; i++) {
				var c = Ext.getCmp(form_id + 'cancel_' + i);
				(item.data.nIdLinea != null && (item.data.nIdEstado == 2 || item.data.nIdEstado == 4)) ? c.enable() : c.disable();
			}
		}
		lineas.setCheckMenu(fn_check_menu);

		/*-------------------------------------------------------------------------
		 * Resto de los controles
		 *-------------------------------------------------------------------------
		 */
		var direccionenvio = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('proveedores/perfilproveedor/get_list'),
			anchor : '90%',
			extrafields : ['nIdPais'],
			label : _s('nIdDireccionEnvio'),
			name : 'nIdEntrega'
		}));

		var ejemplares = new Ext.form.DisplayField({
			cls : 'lineas-ejemplares-field',
			value : 'ejemplares',
			height : 10,
			//disabled: true,
			anchor : '100%'
		});
		var numero = new Ext.form.DisplayField({
			cls : 'numero-factura-field',
			value : '',
			height : 15,
			width : 150
		});
		var suscripcion = new Ext.Button({
			xtype : 'tbbutton',
			iconCls : "iconoSuscripciones",
			text : '',
			handler : function() {
				if((data_load != null) && (data_load.pedidosuscripcion[0] != null)) {
					Ext.app.execCmd({
						url : site_url('suscripciones/suscripcion/index/' + data_load.pedidosuscripcion[0].nIdSuscripcion)
					});
				}
			}
		});

        if (Ext.app.CONCURSOS) {
            var concursos = Ext.app.combobox({
                url: site_url('concursos/concurso/search'),
                allowBlank: true,
                id: 'nIdConcurso'
            });
        }

		var seccion = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('generico/seccion/search'),
			//anchor: "90%",
			//disabled : true,
			allowBlank : true,
			//readOnly : true,
			id : 'nIdSeccion'
		}));

		var refs = {
			xtype : 'compositefield',
			fieldLabel : _s('cRefProveedor'),
			msgTarget : 'side',
			anchor : '-20',
			/*defaults: {
			 flex: 1
			 },*/
			items : [{
				xtype : 'textfield',
				id : 'cRefProveedor',
				allowBlank : true,
				width : '250'
			}, {
				xtype : 'displayfield',
				value : _s('cRefInterna')
			}, {
				xtype : 'textfield',
				id : 'cRefInterna',
				allowBlank : true,
				width : '250'
			}, {
				xtype : 'displayfield',
				value : _s('nIdSeccion')
			}, seccion, suscripcion, numero]
		};

		var estado = Ext.app.combobox({
			url : site_url('compras/estadopedidoproveedor/search'),
			//anchor: "90%",
			disabled : true,
			allowBlank : false,
			readOnly : true,
			id : 'nIdEstado'
		});

		var fechaenvio = new Ext.form.DateField({
			xtype : 'datefield',
			readOnly : true,
			startDay : Ext.app.DATESTARTDAY,
			name : 'dFechaEntrega',
			allowBlank : true
		});

		var datas = {
			xtype : 'compositefield',
			fieldLabel : _s('dFechaEntrega'),
			msgTarget : 'side',
			anchor : '-20',
			items : [fechaenvio, {
				xtype : 'displayfield',
				value : _s('Estado')
			}, estado, {
				xtype : 'displayfield',
				value : _s('bBloqueado')
			}, {
				xtype : 'checkbox',
				id : 'bBloqueado',
				// anchor : '90%',
				allowBlank : true,
				fieldLabel : _s('bBloqueado')
			}, {
				xtype : 'displayfield',
				value : _s('bDeposito')
			}, {
				xtype : 'checkbox',
				id : 'bDeposito',
				// anchor : '90%',
				allowBlank : true,
				fieldLabel : _s('bDeposito')
			}, {
				xtype : 'displayfield',
				value : _s('bRevistas')
			}, {
				xtype : 'checkbox',
				id : 'bRevistas',
				// anchor : '90%',
				allowBlank : true,
				fieldLabel : _s('bRevistas')
			}]
		};

		// Añade el comando para guardar
		lineas.control.addPattern("^" + Ext.app.TPV_GUARDAR + "$", function(m, c) {
			form.save();
			return true;
		});
		// Añade el comando para cerrar
		/*lineas.control.addPattern("^" + Ext.app.TPV_CERRAR + "$", function(m, c){
		 fn_cerrar();
		 return true;
		 });*/
		lineas.control.addPattern("^" + Ext.app.TPV_DESCUENTO + "\\s?(\\d+)$", function(m, c) {
			var v = (m[1] != null) ? parseFloat(m[1]) : 0;
			var v = v.decimal(Ext.app.DECIMALS);
			lineas.descuento(v);
			return true;
		});
		var msg = new Ext.Panel({
			cls : 'info-msg',
			autoScroll : true,
			height : 80,
			width : 600
		});

		var pie = {
			xtype : 'compositefield',
			fieldLabel : _s('fTotal'),
			msgTarget : 'side',
			anchor : '-20',
			items : [total, base, msg]
		};

        var varios = {
            xtype: 'compositefield',
            fieldLabel: _s('Sección def.'),
            msgTarget: 'side',
            anchor: '-20',
            items: [secciondefecto]
        };
        if (Ext.app.CONCURSOS) {
            varios.items.push({
                xtype: 'displayfield',
                value: _s('nIdConcurso')
            });
            varios.items.push(concursos);
        }

		// Controles normales
		var controls = [cliente, direccionenvio, refs, datas, varios, lineas.linea, lineas.grid, ejemplares, pie];

		/*-------------------------------------------------------------------------
		* Comandos
		*-------------------------------------------------------------------------
		*/
		// Cerrar la venta
		var fn_cerrar = function(fnpost, forzar) {
			var fn = function(result) {
				if(result) {
					Ext.app.callRemote({
						url : site_url('compras/pedidoproveedor/cerrar'),
						wait : true,
						timeout: false,
						params : {
							force: forzar,
							id : form.getId()
						},
						fnok : function(obj) {
							form.refresh();
							if(fnpost != null) {
								try {
									fnpost();
								} catch(e) {
								}
							}
						}
					});
				}
			}
			if(form.isDirty()) {
				form.save(fn);
			} else {
				fn(true);
			}
		}
		// Abrir documento
		var fn_abrir = function() {
			var fn = function(result) {
				if(result) {
					Ext.app.callRemote({
						url : site_url('compras/pedidoproveedor/abrir'),
						wait : true,
						params : {
							id : form.getId()
						},
						fnok : function(obj) {
							form.refresh();
						}
					});
				}
			}
			if(form.isDirty()) {
				form.save(fn);
			} else {
				fn(true);
			}
		}

		form.addCommand({
			text : _s('Cerrar pedido'),
			iconCls : 'icon-generar-doc',
			handler : function() { fn_cerrar(); },
			id : form.idform + 'btn_cerrar3'
		});
		// TABS
		documentosAddTabs(form, controls, 'form-pedidoproveedor');

		var notas = Ext.app.formNotas();
		var grid_notas = notas.init({
			id : form_id + "_notas",
			url : site_url('compras/pedidoproveedor'),
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

		// Búsqueda
		var fn_open = function(id) {
			form.load(id);
			form.selectTab(0);
		}
		
		var grid_search = search_pedidoproveedor(form_id, fn_open);

		form.addTab({
			title : _s('Búsqueda'),
			iconCls : 'icon-search',
			items : Ext.app.formSearchForm({
				grid : grid_search,
				audit : true,
				id_grid : form_id + '_g_search_grid'
			})
		});

		form.addAction({
			text : _s('Cerrar'),
			handler : function(f) {
				if(data_load == null)
					return;
				if(data_load.nIdEstado == 1)
					fn_cerrar();
				else if(data_load.nIdEstado == 2) {
					fn_abrir(form);
				}
			},
			iconCls : 'icon-generar-doc',
			id : form.idform + 'btn_cerrar_menu'
		});
		form.addAction({
			text : _s('Forzar cierre'),
			handler : function(f) {
				if(data_load == null)
					return;
				if(data_load.nIdEstado == 1)
					fn_cerrar(null, true);
				else if(data_load.nIdEstado == 2) {
					fn_abrir(form);
				}
			},
			iconCls : 'icon-generar-doc',
			id : form.idform + 'btn_cerrar_menu2'
		});

		var fn_enviar = function() {
			if(data_load == null)
				return;
			var fn = function() {
				documentosEnviar(form, _s('Enviar pedido'), site_url('compras/pedidoproveedor/send'));
			}
			if(data_load.nIdEstado == 1)
				fn_cerrar(fn);
			else if(data_load.nIdEstado == 2) {
				fn();
			}
		}

		form.addAction({
			text : _s('Cerrar y enviar'),
			handler : function() {
				fn_enviar();
			},
			iconCls : 'icon-send',
			id : form.idform + 'btn_enviar2'
		});

		form.addAction({
			text : _s('Enviar'),
			handler : function() {
				fn_enviar();
			},
			iconCls : 'icon-send',
			id : form.idform + 'btn_enviar'
		});
		form.addAction('-');
		form.addAction({
			iconCls : "icon-excel",
			text : _s('Exportar EXCEL'),
			id : form.idform + 'btn_excel',
			handler : function() {
				Ext.app.callRemote({
					url : site_url('compras/pedidoproveedor/exportar_excel'),
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
		form.addAction({
			iconCls : "icon-excel",
			text : _s('Cerrar y exportar EXCEL'),
			id : form.idform + 'btn_cerrar_excel',
			handler : function() {
				if(data_load.nIdEstado == 1)
					fn_cerrar(function() {
						Ext.app.callRemote({
							url : site_url('compras/pedidoproveedor/exportar_excel'),
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
					});
				}
		});

		var Reclamar = function(form) {
			Ext.app.callRemote({
				url : site_url('compras/reclamacion/pedido'),
				params : {
					id : form.getId()
				},
				fnok : function(res) {
					form.refresh();
				}
			});
		}
		form.addAction('-');
		form.addAction({
			text : _s('Reclamar'),
			handler : function() {
				Reclamar(form);
			},
			iconCls : 'iconoReclamacionPedidoProveedor',
			id : form.idform + 'btn_reclamar'
		});

		var Cancelar = function(form) {
			Ext.app.callRemote({
				url : site_url('compras/pedidoproveedor/cancelar'),
				params : {
					id : form.getId()
				},
				fnok : function(res) {
					form.refresh();
				}
			});
		}
		form.addAction('-');
		form.addAction({
			text : _s('Cancelar'),
			handler : function() {
				Cancelar(form);
			},
			iconCls : 'icon-cancel',
			id : form.idform + 'btn_cancelar'
		});

		var CancelarAvisar = function(form) {
			Ext.app.callRemote({
				url : site_url('compras/cancelacion/pedido'),
				params : {
					id : form.getId()
				},
				fnok : function(res) {
					form.refresh();
				}
			});
		}
		form.addAction({
			text : _s('Cancelar y avisar'),
			handler : function() {
				CancelarAvisar(form);
			},
			iconCls : 'iconoCancelacionPedidoProveedor',
			id : form.idform + 'btn_cancelar_avisar'
		});

		form.addAction('-');
		form.addAction({
			text : _s('Ver asignacion'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('compras/pedidoproveedor/asignacion'),
					params : {
						id : form.getId()
					}
				});
			},
			id : form.idform + 'btn_asignacion',
			iconCls : 'icon-report'
		});

        form.addTools({
            text : _s('Unificar pedidos abiertos'),
            handler : function() {
                if (data_load != null && data_load.nIdProveedor != null) {
					// Muestra todos los albaranes por facturar
					var model = [{
						name : 'dCreacion'
					}, {
						name : 'nIdPedido'
					}, {
						name : 'cCUser'
					}, {
						name : 'dCreacion'
					}, {
						name : 'cRefProveedor'
					}, {
						name : 'cRefInterna'
					}, {
						name : 'cSeccion'
					}];

					var url = site_url("compras/pedidoproveedor/get_list");
					var store = Ext.app.createStore({
						model : model,
						url : url
					});

					var sm = new Ext.grid.CheckboxSelectionModel();

					var columns = [sm, {
						header : _s("Id"),
						width : Ext.app.TAM_COLUMN_ID,
						dataIndex : 'nIdPedido',
						sortable : true
					}, {
						header : _s("Seccion"),
						dataIndex : 'cSeccion',
						width : Ext.app.TAM_COLUMN_TEXT,
						sortable : true
					}, {
						header : _s("cRefProveedor"),
						dataIndex : 'cRefProveedor',
						width : Ext.app.TAM_COLUMN_TEXT,
						sortable : true
					}, {
						header : _s("cRefInterna"),
						dataIndex : 'cRefInterna',
						width : Ext.app.TAM_COLUMN_TEXT,
						sortable : true
					}, {
						header : _s("dCreacion"),
						dataIndex : 'dCreacion',
						width : Ext.app.TAM_COLUMN_DATETIME,
						dateFormat : 'timestamp',
						renderer : Ext.app.renderDate,
						sortable : true
					}, {
						header : _s("cCUser"),
						dataIndex : 'cCUser',
						width : Ext.app.TAM_COLUMN_TEXT,
						sortable : true
					}];

					var grid = new Ext.grid.GridPanel({
						store : store,
						anchor : '95% 80%',
						height : 400,
						//autoExpandColumn: 'descripcion',
						stripeRows : true,
						loadMask : true,
						sm : sm,

						bbar : Ext.app.gridBottom(store, true),

						// grid columns
						columns : columns
					});

					var controls = [grid];

					var form2 = Ext.app.formStandarForm({
						controls : controls,
						autosize : false,
						labelWidth : 200,
						height : 500,
						width : 700,
						icon: 'iconoPedidoProveedor',
						title : _s('Pedidos del proveeder abiertos'),
						fn_ok : function() {
							var sel = grid.getSelectionModel().getSelections();
							var ids = '';
							Ext.each(sel, function(item) {
								ids +=item.data.nIdPedido + ';';
							});
							Ext.app.callRemote({
								url : site_url('compras/pedidoproveedor/unificar'),
								params : {
									destino : form.getId(),
									origen : ids
								},
								fnok : function(ok) {
									if(ok)
										form.refresh();
								}
							});
						}
					});

					store.baseParams = {
						where : 'bRevistas=0&nIdEstado=1&bBloqueado=0&nIdProveedor=' + data_load.nIdProveedor 
						+ '&bDeposito=' + data_load.bDeposito
						+ '&nIdPedido=<>' + form.getId()
					};

					store.load();
					form2.show();
					return true;
                }
            },
            iconCls : 'icon-unificar',
            id : form.idform + 'btn_unificar'
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
        form.addTools('-');
        form.addTools({
            text : _s('Añadir precio'),
            handler : function() {
                if (data_load != null && data_load.nIdDireccion != null)
                {
					Ext.Msg.prompt(form.getTitle(), _s('Precio'), function(ok, v) {
						if(ok != 'ok')
							return;
						v = parseFloat(v);
						lineas.aplicarPrecio0(v);
					});
                }
            },
            iconCls : 'icon-precio',
            id : form.idform + 'btn_add_precio'
        });

		form.addAction('-');
		addButtonAbiertos(form);

		direccionenvio.store.baseParams = {
			id: parseInt(Ext.app.get_config('bp.compras.direcciones')),
			tipo: 'D'
		}

		direccionenvio.store.load();
		secciondefecto.store.load();
		seccion.store.load();
		return form.show(open_id);
	} catch (e) {
		console.dir(e);
	}
})();
