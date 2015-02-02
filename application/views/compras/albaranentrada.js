(function() {
	try {
		/*-------------------------------------------------------------------------
		 * Datos Formulario
		 *-------------------------------------------------------------------------
		 */
		var open_id = "<?php echo $open_id;?>";
		var form_id = "<?php echo $id;?>";
		var title = _s("Albaran de Entrada");
		var icon = "iconoAlbaranEntradaTab";

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
		var total_tpv = 0;
		var total_mp = 0;

		var id_defecto = null;

		var msgDialog = null;
		var msgText = '';

		var resetMessage = function() {
			msgText = '<div class="data"><table style="border: 1px solid #d0d0d0;"><tr><th>' + _s('Id') + '</th><th>' + _s('cSeccion') + '</th><th>' + _s('Cantidad') + '</th><th>' + _s('Cliente') + '</th></tr>';
		}
		resetMessage();

		var showPedido = function(pd) {
			var msgText = '<div class="data"><table style="border: 1px solid #d0d0d0;"><tr><th>' + _s('Id') + '</th><th>' + _s('cSeccion') + '</th><th>' + _s('Cantidad') + '</th><th>' + _s('Cliente') + '</th></tr>';
			Ext.each(pd, function(item) {
				var msg = '<tr><td>' + item.id + '</td><td>' + item.cSeccion + '</td><td>' + item.nCantidad + '</td><td>' + ((item.cNombre != null) ? (item.cNombre + ' ') : '') + ((item.cApellido != null) ? (item.cApellido + ' ') : '') + item.cEmpresa + '</td></tr>';
				msgText += msg;
			});
			msgText + '</table></div>';

			var fn = function() {
				msgDialog = new Ext.ux.window.MessageWindow({
					title : _s('Pedidos de cliente'),
					autoDestroy : true, //default = true
					autoHeight : true,
					autoHide : false,
					origin : {
						pos : "c-c"
					},
					//baseCls: 'x-box',//defaults to 'x-window'
					//clip: 'bottom',//clips the bottom edge of the window border
					//bodyStyle: 'text-align:center',
					closable : true,
					hideFx : {
						delay : Ext.app.FLY_TIME,
						//duration: 0.25,
						mode : 'standard', //null,'standard','custom',or default ghost
						useProxy : false //default is false to hide window instead
					},
					hideAction : 'close',
					html : msgText,
					iconCls : 'icon-info',
					width : Ext.app.MESSAGEFLYWIDTHPEDIDOS //optional (can also set minWidth which = 200 by default)
				}).show(Ext.getDoc());

			}
			if(msgDialog == null) {
				fn();
			} else {
				//  console.dir(msgDialog);
				try {
					msgDialog.setMessage(msgText + '</table></div>');
					msgDialog.show(Ext.getDoc());
				} catch (e) {
					fn();
				}
			}
		}
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
			if(data.id_direccion) {
				var d = direcciones.store.getById(data.id_direccion);
				if(data_load == null)
					data_load = [];
				if(data_load.direccion == null)
					data_load.direccion = [];
				data_load.direccion.nIdPais = d.data.nIdPais;
				if(data_load.direccion.nIdPais != Ext.app.DEFAULT_PAIS) {
					preciolibre.setValue(true);
					extranjero.setValue(true);
				} else {
					extranjero.setValue(false);
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
		// Carga los cargos
		var fn_load_mp = function(data) {
			Ext.each(data, function(r) {
				add_mp(r.nIdTipoCargo, r.cTipoCargo, r.fImporte, r.nIdCargo, r.cCUser, r.cAUser, r.dCreacion, r.dAct);
			});
		}
		// Carga la venta
		var fn_load = function(id, res) {
			//console.dir(res);
			notas.load(id);
			data_load = res;
			if(res.lineas != null)
				lineas.load(res.lineas);
			modospago_delete = new Array();
			if(res.cargos != null)
				fn_load_mp(res.cargos);

			try {
				if(res.suscripcion != null) {
					suscripcion.setText(res.suscripcion);
					suscripcion.setVisible(true);
				} else {
					suscripcion.setVisible(false);
				}
			} catch(e) {
				//console.dir(e);
			}

			fn_load_direcciones(res.nIdProveedor, res.nIdDireccion);
			fn_load_cliente(res.nIdProveedor);
			numero.setValue((res.nIdEstado == 1) ? _s('albaran-proveedor-abierto') : '');

			monedacamara.setValue(divisas.getRawValue());

			form.setDirty(false);
			lineas.control.focus();
			if(id_defecto != null)
				quitarIVA.setValue(id_defecto);
		}
		var fn_lang = function() {
			return getLang(data_load);
		}
		// Borrado
		var fn_reset = function() {
			suscripcion.setVisible(false);
			notas.reset();
			msg.update('');
			mpstore.removeAll();
			var c = Ext.getCmp(form.idform + 'fld_bDeposito');
			//console.dir(c);
			c.setValue(false);
			c.checked = false;
			cliente_datos = null;
			data_load = null;
			lineas.clear();
			total_mp = 0;
			total_tpv = 0;
			total_base = 0;
			total_divisa = 0;
			resetMessage();
			cliente_id = null;
			form.setData({
				value_data : {
					'nIdEstado' : 1
				}
			}, true);
			lineas.control.focus();
			/*if (id_defecto!=null)
			 quitarIVA.setValue(id_defecto);*/
		}
		// Guardar
		var fn_save = function(id, data) {
			// Añadimos las líneas
			var index = 0;
			if(Ext.getCmp(direcciones.id).getValue() != '')
				data['nIdDireccion'] = Ext.getCmp(direcciones.id).getValue();
			var idcliente = cliente_id;
			if((data_load != null && idcliente != data_load.nIdCliente) || data_load == null)
				data['nIdProveedor'] = idcliente;

			if(Ext.getCmp(divisas.id).getValue() != '')
				data['nIdDivisa'] = Ext.getCmp(divisas.id).getValue();
			if(pais.getValue() != '')
				data['nIdPais'] = pais.getValue();
			if(tipomercancia.getValue() != '')
				data['nIdTipoMercancia'] = tipomercancia.getValue();
			data['bExtranjero'] = extranjero.getValue();
			data = lineas.get(data);
			lineas.control.focus();
			//id_defecto = secciondefecto.getValue();
			mpgrid.getStore().each(function(r) {
				if(r.isModified('fImporte') || (r.data.nIdCargo == null) || (id == null)) {
					data['cargos[' + index + '][nIdTipoCargo]'] = r.data.nIdTipoCargo;
					data['cargos[' + index + '][id]'] = r.data.nIdCargo;
					data['cargos[' + index + '][nIdCargo]'] = r.data.nIdCargo;
					data['cargos[' + index + '][fImporte]'] = r.data.fImporte;
					index++;
				}
			});
			// Los borrados
			Ext.each(modospago_delete, function(i) {
				data['cargos[' + index + '][delete]'] = i;
				index++;
			});
			id_defecto = quitarIVA.getValue();
			//console.dir(data);
			return data;
		}
		// Enables y disables
		var fn_enable_disable = function(form) {
			notas.enable(form.getId() > 0);
			var bloqueado = ((data_load != null) && (data_load.nIdEstado != 1 && data_load.nIdEstado != 5 && data_load.nIdEstado != 6 && data_load.nIdEstado != null));

			if(bloqueado) {
				//fechafactura.disable();
				lineas.disable();
			} else {
				//fechafactura.enable();
				lineas.enable();
			}

			Ext.app.formEnableList({
				list : [form.idform + 'btn_notas', form.idform + 'btn_pedir'],
				enable : (form.getId() > 0)
			});

			Ext.app.formEnableList({
				list : [form.idform + 'btn_asignar', 
					form.idform + 'btn_incidencias'
					],
				enable : (form.getId() > 0) && (data_load != null) && (data_load.nIdEstado == 2 || data_load.nIdEstado == 4)
			});
			var m = Ext.getCmp(form_id + 'btn_asignar');
			m.setText(_s('Asignar'));
			if((form.getId() > 0) && (data_load != null) && (data_load.nIdEstado == 4)) {
				m.setText(_s('DesAsignar'));
			}

			Ext.app.formEnableList({
				list : [form.idform + 'btn_cerrar3', form.idform + 'btn_cancelar', form.idform + 'btn_reclamar'],
				enable : (!bloqueado) && (form.getId() > 0)
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_pedidos', form.idform + 'btn_etiquetas', form.idform + 'btn_etiquetas_albaran', form.idform + 'btn_precios', form.idform + 'btn_dividir'],
				enable : (form.getId() > 0)
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_asignacion',
				form.idform + 'btn_asignacion2',
				form.idform + 'btn_consultados'
				],
				enable : (form.getId() > 0) && (data_load != null) && (data_load.nIdEstado > 2)
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_cancelar', form.idform + 'btn_cancelar_avisar', form.idform + 'btn_reclamar'],
				enable : ((data_load != null) && ((data_load.nIdEstado == 2) || (data_load.nIdEstado == 4)))
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_liquidacion'],
				enable : (form.getId() > 0) && (data_load != null) && (data_load.nIdEstado == 2 || data_load.bDeposito )
			});

			var m = Ext.getCmp(form_id + 'btn_cerrar_menu');
			//var m2 = Ext.getCmp(form_id + 'btn_enviar');
			m.enable();
			//m2.enable();
			m.setText(_s('Cerrar'));
			//m2.setText(_s('Cerrar y enviar'));
			if(data_load == null || data_load.nIdEstado == 5 || data_load.nIdEstado == 6) {
				m.disable();
				//m2.disable();
			} else if(data_load.nIdEstado == 1) {
				m.setText(_s('Cerrar'));
				//m2.setText(_s('Cerrar y enviar'));
			} else if(data_load.nIdEstado == 2) {
				m.setText(_s('Abrir'));
				//m2.setText(_s('Enviar'));

			} else if(data_load.nIdEstado == 4) {
				m.disable();
				//m2.setText(_s('Enviar'));
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
			url : site_url('compras/albaranentrada'),
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

		var base = new Ext.form.DisplayField({
			cls : 'alb-totales-field',
			value : Ext.app.currencyFormatter(0),
			height : 80,
			width : 250
		});
		var total_base = 0;
		var total_divisa = 0;
		var showtotal = function(s) {
			var t = 0;
			var ct = 0;
			var ejs = 0;
			var dv = 0;
			var cb = parseFloat(cambio.getValue());
			var divisa = Ext.getCmp(divisas.id).getValue();
			//Ext.getCmp(form_id + '_moneda').setText(divisa);
			//console.log('Divisa ' + divisa + ' -> ' + cb);
			if(s == null)
				s = lineas.grid.store;
			if(s != null) {
				total_base = 0;
				total_divisa = 0;
				s.each(function(r) {
					//console.log(r.data);
					t += r.data.fTotal;
					//total_divisa += parseFloat(r.data.fPrecioDivisa);
					total_base += r.data.fBase;
					ct++;
					ejs += r.data.nCantidad;
				})
				total_tpv = t.decimal(Ext.app.DECIMALS);
				ejemplares.setValue(sprintf(_s('lineas-ejemplares'), ct, ejs));
			}
			//console.log('Total: ' + t + ' base: ' + total_base + ' gastos: ' + total_mp + ' total tpv: ' + total_tpv);
			if(divisa != null && divisa != Ext.app.DIVISA_DEFAULT) {
				var total = t * ((data_load.nIdEstado != 1) ? cb : 1) + total_mp;
				//console.log('Total: ' + total);

				var text = '<table width="100%"><tr><td></td><th>' + _s('fBase') + '</th>' + '<th>' + _s('fIVA') + '</th>' + '<th>' + _s('fGastos') + '</th>' + '<th>' + _s('fTotal') + '</th>' + '</tr>';
				// EUR
				text += '<tr><td class="texto">' + Ext.app.SIMBOLODIVISA + '</td>' + '<td class="valor">' + Ext.app.currencyFormatter(total_base / ((data_load.nIdEstado == 1) ? cb : 1)) + '</td>' + '<td class="valor">' + Ext.app.currencyFormatter(t / ((data_load.nIdEstado == 1) ? cb : 1) - total_base / ((data_load.nIdEstado == 1) ? cb : 1)) + '</td>' + '<td class="valor">' + Ext.app.currencyFormatter(total_mp / cb) + '</td>' + '<td class="valor">' + Ext.app.currencyFormatter(total / cb) + '</td>' + '</tr>';
				text += '<tr><td class="texto">' + ((simbolo != null && simbolo != '') ? simbolo : data_load.cSimbolo) + '</td>' + '<td class="valor">' + Ext.app.currencyFormatter(total_base * ((data_load.nIdEstado != 1) ? cb : 1)) + '</td>' + '<td class="valor">' + Ext.app.currencyFormatter(t * ((data_load.nIdEstado != 1) ? cb : 1) - (total_base * ((data_load.nIdEstado != 1) ? cb : 1))) + '</td>' + '<td class="valor">' + Ext.app.currencyFormatter(total_mp) + '</td>' + '<td class="valor">' + Ext.app.currencyFormatter(total) + '</td>' + '</tr>';
				text += '</table>';
				total_tpv = total / cb;
				total_divisa = total;
			} else {
				var text = '<table width="100%"><tr><td></td><th>' + _s('fBase') + '</th>' + '<th>' + _s('fIVA') + '</th>' + '<th>' + _s('fGastos') + '</th>' + '<th>' + _s('fTotal') + '</th>' + '</tr>';
				// EUR
				text += '<tr><td class="texto">' + Ext.app.SIMBOLODIVISA + '</td>' + '<td class="valor">' + Ext.app.currencyFormatter((data_load.nIdEstado != 1) ? total_base : (total_base / cb)) + '</td>' + '<td class="valor">' + Ext.app.currencyFormatter((data_load.nIdEstado != 1) ? (t - total_base) : ((t - total_base) / cb)) + '</td>' + '<td class="valor">' + Ext.app.currencyFormatter((data_load.nIdEstado != 1) ? total_mp : (total_mp / cb)) + '</td>' + '<td class="valor">' + Ext.app.currencyFormatter((data_load.nIdEstado != 1) ? (t + total_mp) : ((t + total_mp) / cb)) + '</td>' + '</tr>';
				text += '</table>';
				total_tpv = (data_load.nIdEstado != 1) ? (t + total_mp) : ((t + total_mp) / cb);
				total_divisa = total;
			}
			base.setValue(text);
			form.setDirty();
		}
		var seccion_defecto = get_seccion_defecto(fn_get_data, fn_set_data, 'bp.albaranentrada.secciones.defecto', 'bp.albaranentrada.secciones.vedadas', allsecciones, false);
		//var secciondefecto = seccion_defecto.secciondefecto;
		var fn_get_seccion = seccion_defecto.fn_get_seccion;

		var hide = Ext.app.GRIDCOLUMNS_HIDE_ALBARANENTRADA;

		var fn_get_descuento = function(data) {
			//console.dir(data);
			if(cliente_datos == null) {
				fn_load_direcciones((data.nIdProveedor != null) ? data.nIdProveedor : data.nIdProveedor2);
				fn_load_cliente((data.nIdProveedor != null) ? data.nIdProveedor : data.nIdProveedor2);
				Ext.app.msgFly(title, _s('no-proveedor-select-select-default'));
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
				Ext.app.msgFly(title, _s('pedidor-proveedor-no-proveedor'));
			} else if(si && dto == null) {
				Ext.app.msgFly(title, _s('pedidor-proveedor-no-proveedor-descuento'));
			}
			//console.log('Descuento ' + dto);
			return dto;
		}
		var lineas = docLineaControl({
			nIdDocumento : 'nIdPedido',
			nIdLinea : 'nIdLinea',
			cReferencia : 'cRefProveedor',
			resetextrafields : false,
			coste : true,
			base : true,
			useload : false,
			margen_error : false,
			firmedeposito : false,
			use_secciones : false,
			autoselect : false,
			fn_get_seccion : fn_get_seccion,
			use_creation : true,
			fn_change : showtotal,
			hide : hide,
			introadd : false,
			anchor : "100% 50%",
			fn_get_descuento : fn_get_descuento,
			url_search : site_url('catalogo/articulo/search'),
			url_load : site_url('catalogo/articulo/get3'),
			url_descuentos : site_url('catalogo/articulo/descuentos'),
			extrafields : [{
				header : _s('fGastos'),
				hidden : in_array('fGastos', hide),
				width : Ext.app.TAM_COLUMN_NUMBER,
				dataIndex : 'fGastos',
				align : 'right',
				sortable : true
			}, {
				header : _s('fPrecioDivisa'),
				hidden : in_array('fPrecioDivisa', hide),
				width : Ext.app.TAM_COLUMN_NUMBER,
				dataIndex : 'fPrecioDivisa',
				align : 'right',
				sortable : true
			}, {
				header : _s('fPrecioVenta'),
				hidden : in_array('fPrecioVenta', hide),
				width : Ext.app.TAM_COLUMN_NUMBER,
				dataIndex : 'fPrecioVenta',
				editor : new Ext.form.NumberField({
					allowBlank : false,
					allowNegative : false,
					allowDecimals : true,
					decimalPrecision : Ext.app.DECIMALS,
					style : 'text-align:left',
					selectOnFocus : true
				}),
				align : 'right',
				renderer : Ext.app.rendererPVP,
				sortable : true
			}, {
				header : _s('nCantidadDevuelta'),
				hidden : in_array('nCantidadDevuelta', hide),
				width : Ext.app.TAM_COLUMN_NUMBER,
				dataIndex : 'nCantidadDevuelta',
				sortable : true
			}, {
				header : _s('nCantidadAsignada'),
				hidden : in_array('nCantidadAsignada', hide),
				width : Ext.app.TAM_COLUMN_NUMBER,
				dataIndex : 'nCantidadAsignada',
				sortable : true
			}, {
				header : _s('cEstado'),
				hidden : in_array('cEstado', hide),
				width : Ext.app.TAM_COLUMN_TEXT,
				dataIndex : 'cEstado',
				sortable : true
			}, {
				hidden : true,
				dataIndex : 'nIdEstado',
				hideable : false,
				sortable : false
			}]
		});

		// Añadimos el precio y la sección
		var importe = new Ext.form.NumberField({
			enableKeyEvents : true,
			value : 0,
			allowNegative : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			selectOnFocus : true,
			width : 50
		});
		var precioventa = new Ext.form.NumberField({
			enableKeyEvents : true,
			value : 0,
			allowNegative : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			selectOnFocus : true,
			width : 50
		});
		var original = new Ext.form.NumberField({
			selectOnFocus : true,
			readOnly : true,
			width : 50
		});
		var IVA = new Ext.form.NumberField({
			//readOnly: true,
			width : 50
		});
		var quitarIVA = new Ext.form.NumberField({
			enableKeyEvents : true,
			width : 50
		});
		var seccion = new Ext.form.ComboBox(Ext.app.combobox({
			//readOnly : true,
			allowBlank : true
		}));

		var pendientes = new Ext.form.NumberField({
			readOnly : true,
			width : 50
		});

		var controles_linea = lineas.linea.items;
		var linea_articulo = {
			xtype : 'compositefield',
			msgTarget : 'side',
			fieldLabel : _s('Artículo'),
			anchor : '-20',
			items : [controles_linea[0], controles_linea[1], controles_linea[2], controles_linea[3]]
		};

		var descuento = controles_linea[7];
		var precio = controles_linea[9];
		controles_linea[3].setWidth(500);
		var linea_cantidades = {
			xtype : 'compositefield',
			msgTarget : 'side',
			fieldLabel : _s('Cant'),
			anchor : '-20',
			items : [controles_linea[5], controles_linea[6], descuento, controles_linea[8], precio, {
				xtype : 'displayfield',
				value : _s('IVA')
			}, IVA, {
				xtype : 'displayfield',
				value : _s('Precio (C/I)')
			}, importe, {
				xtype : 'displayfield',
				value : _s('PVP')
			}, precioventa]
		};

		var linea_otros = {
			xtype : 'compositefield',
			msgTarget : 'side',
			fieldLabel : _s('Pendientes'),
			anchor : '-20',
			items : [seccion, {
				xtype : 'displayfield',
				value : _s('Quitar IVA')
			}, quitarIVA, {
				xtype : 'displayfield',
				value : _s('Pendientes')
			}, pendientes]
		};

		controles_linea[0].on('itemload', function(c, field, data) {
			if(data.pedidos_cliente != null) {
				var pd = [];
				Ext.each(data.pedidos_cliente, function(item) {
					if((item.cEstado == _s('EN PROCESO')) && item.bNoAvisar!=1) {
						controles_linea[5].focus();
						pd[pd.length] = item;
					}
				});
				if(pd.length > 0)
					showPedido(pd);

			}
			var pd = 0;
			seccion.store.removeAll();
			if(data.pedidos_proveedor != null) {
				Ext.each(data.pedidos_proveedor, function(item) {
					var id = Ext.app.createId();
					pd += item.nCantidad - item.nRecibidas;
					text = '(' + (item.nCantidad - item.nRecibidas) + ') ' + item.cSeccion;
					Ext.app.comboAdd(seccion.store, id, text);
				});
				seccion.setRawValue(text);
			}
			pendientes.setValue(pd);
			IVA.setValue(data.fIVA);

			importe.setValue(data.fPVP);
			original.setValue(data.fPVP);
			var p = (data.fPrecioDivisa != null) ? data.fPrecioDivisa : ((data.fPrecioProveedor != null) ? data.fPrecioProveedor : data.fBase);
			precio.setValue(p);
			precioventa.setValue((preciolibre.getValue() !== true) ? AplicarIVA(p, data.fIVA) : 0);
		});
		IVA.on('keypress', function(f, e) {
			if(e.getKey() == e.ENTER) {
				importe.focus();
			}
		});
		controles_linea[5].on('keypress', function(f, e) {
			if(e.getKey() == e.ENTER) {
				importe.focus();
			}
		});
		precio.on('keypress', function(f, e) {
			if(e.getKey() == e.ENTER) {
				importe.focus();
				//return false;
			}
		});
		importe.on('keypress', function(me, e) {
			if(e.getKey() == e.ENTER) {
				precioventa.focus();
			}
		});
		quitarIVA.on('keypress', function(me, e) {
			if(e.getKey() == e.ENTER) {
				controles_linea[5].focus();
			}
		});
		precioventa.on('keypress', function(f, e) {
			if(e.getKey() == e.ENTER) {
				if(data_load.direccion == null) {
					Ext.app.msgError(title, _s('albaran-nodireccion'));
					return;
				}
				var pvp = precioventa.getValue();
				controles_linea[0].add({
					'fPrecioVenta' : parseFloat((pvp != '') ? pvp : 0),
					'dCreacion' : DateToNumber(new Date().getTime()),
					'nIdEstado' : null,
					'fIVA' : (data_load.direccion.nIdPais != Ext.app.DEFAULT_PAIS) ? 0 : IVA.getValue(),
					'cEstado' : null
				});
			}
		});
		importe.on('change', function(me, e) {
			var p2 = me.getValue();
			var p = QuitarIVA(p2, IVA.getValue());
			precio.setValue(p);
			if(preciolibre.getValue() !== true)
				precioventa.setValue(me.getValue());
		});
		precio.on('change', function(me, e) {
			var quitar = quitarIVA.getValue();
			var p2 = (quitar > 0) ? QuitarIVA(me.getValue(), quitar) : me.getValue();
			//console.log('Quitar ' + quitar + ' -> ' + me.getValue() + ' -> ' + p2);
			precio.setValue(p2);
			var p = AplicarIVA(p2, IVA.getValue());
			//console.log('+IVA ' + IVA.getValue() + ' -> ' + p2 + ' -> ' + p);
			importe.suspendEvents();
			importe.setValue(p);
			importe.resumeEvents();
			if(preciolibre.getValue() !== true)
				precioventa.setValue(p);
		});
		precio.on('focus', function(me, e) {
			var quitar = quitarIVA.getValue();
			if(quitar > 0) {
				me.setValue(AplicarIVA(me.getValue(), quitar));
			}
			me.selectText();
		});
		addMenuDocumentos(lineas);
		addMenuVentas(lineas);
		addMenuStock(lineas);

		/*-------------------------------------------------------------------------
		 * Resto de los controles
		 *-------------------------------------------------------------------------
		 */
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

		var divisas = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('generico/divisa/get_list'),
			extrafields : ['fCompra', 'cSimbolo'],
			field : 'cDescripcion',
			value : 'nIdDivisa',
			allowBlank : true,
			name : 'nIdDivisa'
		}));

		var cambio = new Ext.form.NumberField({
			name : 'fPrecioCambio',
			allowBlank : true
		});

		var simbolo = '';
		divisas.on('select', function(a, b) {
			//console.log('en divisas');
			//console.dir(b);
			simbolo = b.data.cSimbolo;
			if(b.data.nIdDivisa != Ext.app.DIVISA_DEFAULT)
				preciolibre.setValue(true);
			cambio.setValue(b.data.fCompra);
			console.dir(monedacamara);
			monedacamara.setValue(b.data.cSimbolo + ' - ' + b.data.cDescripcion);
		});
		var extranjero = new Ext.form.Checkbox({
			name : 'bExtranjero',
			allowBlank : true,
			readOnly : true
		});
		var suscripcion = new Ext.Button({
			xtype : 'tbbutton',
			iconCls : "iconoSuscripciones",
			text : '',
			handler : function() {
				if((data_load != null) && (data_load.suscripcion != null)) {
					Ext.app.execCmd({
						url : site_url('suscripciones/suscripcion/index/' + data_load.suscripcion)
					});
				}
			}
		});

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
				width : '200'
			}, {
				xtype : 'displayfield',
				value : _s('cRefInterna')
			}, {
				xtype : 'textfield',
				id : 'cRefInterna',
				allowBlank : true,
				width : '200'
			}, extranjero, {
				xtype : 'displayfield',
				value : _s('bExtranjero')
			}, suscripcion, numero]
		};

		var estado = Ext.app.combobox({
			url : site_url('compras/estadoalbaranentrada/search'),
			//anchor: "90%",
			disabled : true,
			allowBlank : true,
			readOnly : true,
			id : 'nIdEstado'
		});

		var fechacierre = new Ext.form.DateField({
			xtype : 'datefield',
			readOnly : true,
			startDay : Ext.app.DATESTARTDAY,
			name : 'dCierre',
			allowBlank : true
		});

		var fechaproveedor = new Ext.form.DateField({
			xtype : 'datefield',
			startDay : Ext.app.DATESTARTDAY,
			name : 'dFecha',
			value : DateToNumber((new Date).getTime()),
			allowBlank : true //false
		});
		var albaran = new Ext.form.TextField({
			name : 'cNumeroAlbaran',
			allowBlank : false
		});

		var mpfields = {
			id : 'id',
			model : [{
				name : 'nIdCargo'
			}, {
				name : 'id'
			}, {
				name : 'nIdTipoCargo'
			}, {
				name : 'cDescripcion'
			}, {
				name : 'fImporte'
			}, {
				name : 'cCUser'
			}, {
				name : 'dCreacion'
			}, {
				name : 'cAUser'
			}, {
				name : 'dAct'
			}]
		};

		var mprt = Ext.data.Record.create(mpfields);

		var mpstore = new Ext.data.Store({
			reader : new Ext.data.ArrayReader({
				idIndex : 0
			}, mprt)
		});

		var showtotalmodopago = function(s) {
			var t = 0;
			s.each(function(r) {
				//console.log('Gastos ' + r.data.fImporte);
				t += (r.data.fImporte);
			});
			total_mp = t.decimal(Ext.app.DECIMALS);
			//console.log('Gastos Total ' + total_mp);
			showtotal();
		}
		mpstore.on('update', function(s, r, o) {
			showtotalmodopago(s);
			form.setDirty();
		});
		mpstore.on('add', function(s, r, i) {
			showtotalmodopago(s);
			form.setDirty();
		});
		var modospago_delete = new Array()
		mpstore.on('remove', function(s, r, i) {
			if((data_load != null) && (data_load.nIdEstado != 1))
				return false;
			if(r.data.nIdCargo != null)
				modospago_delete.push(r.data.nIdCargo);
			showtotalmodopago(s);
			form.setDirty();
		});
		var mpitemDeleter = new Extensive.grid.ItemDeleter();

		var mpgrid = new Ext.grid.EditorGridPanel({
			autoExpandColumn : "descripcion",
			loadMask : true,
			stripeRows : true,
			store : mpstore,
			height : 80,
			width : 250,
			columns : [mpitemDeleter, {
				header : _s('Id'),
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : 'nIdCargo',
				hidden : true,
				sortable : true
			}, {
				header : _s('cDescripcion'),
				width : Ext.app.TAM_COLUMN_TEXT,
				dataIndex : 'cDescripcion',
				id : 'descripcion',
				sortable : true
			}, {
				align : 'right',
				header : _s('fImporte'),
				width : Ext.app.TAM_COLUMN_NUMBER,
				dataIndex : 'fImporte',
				sortable : true,
				editor : new Ext.form.NumberField({
					allowBlank : false,
					allowNegative : true,
					allowDecimals : true,
					decimalPrecision : Ext.app.DECIMALS,
					style : 'text-align:left',
					selectOnFocus : true
				}),
				renderer : Ext.app.numberFormatter
			}, {
				header : _s('cCUser'),
				width : Ext.app.TAM_COLUMN_TEXT,
				dataIndex : 'cCUser',
				hidden : true,
				sortable : true
			}, {
				header : _s('dCreacion'),
				width : Ext.app.TAM_COLUMN_DATE,
				dateFormat : 'timestamp',
				renderer : Ext.app.renderDate,
				hidden : true,
				dataIndex : 'dCreacion',
				sortable : true
			}, {
				header : _s('cAUser'),
				width : Ext.app.TAM_COLUMN_TEXT,
				hidden : true,
				dataIndex : 'cAUser',
				sortable : true
			}, {
				header : _s('dAct'),
				width : Ext.app.TAM_COLUMN_DATE,
				hidden : true,
				dateFormat : 'timestamp',
				renderer : Ext.app.renderDate,
				dataIndex : 'dAct',
				sortable : true
			}],
			sm : mpitemDeleter,
			listeners : {
				afteredit : function(e) {
					if(e.originalValue != e.value) {
						if((data_load != null) && (data_load.nIdEstado != 1)) {
							e.reject();
						}
					}
				}
			}
		});
		Ext.app.addDeleteEvent(mpgrid);

		var datas = {
			xtype : 'compositefield',
			fieldLabel : _s('cNumeroAlbaran'),
			//msgTarget: 'side',
			anchor : '-20',
			items : [albaran, {
				xtype : 'displayfield',
				value : _s('Fecha Proveedor')
			}, fechaproveedor, {
				xtype : 'displayfield',
				value : _s('dCierre')
			}, fechacierre, {
				xtype : 'displayfield',
				value : _s('Estado')
			}, estado, {
				xtype : 'displayfield',
				value : _s('bSuscripciones')
			}, {
				xtype : 'checkbox',
				id : 'bSuscripciones',
				// anchor : '90%',
				allowBlank : true,
			}]
		};

		var preciolibre = new Ext.form.Checkbox({
			name : 'bPrecioLibre',
			allowBlank : true,
		});

		var datas2 = {
			xtype : 'compositefield',
			hideLabel : true,
			//fieldLabel: _s('bPrecioLibre'),
			msgTarget : 'side',
			anchor : '-20',
			items : [preciolibre, {
				xtype : 'displayfield',
				value : _s('bPrecioLibre')
			}, {
				xtype : 'checkbox',
				id : 'bAplicarGastosDefecto',
				allowBlank : true,
				readOnly : true,
			}, {
				xtype : 'displayfield',
				value : _s('bAplicarGastosDefecto')
			}, {
				xtype : 'checkbox',
				id : 'bDeposito',
				//value: false,
				checked : false,
				allowBlank : true,
			}, {
				xtype : 'displayfield',
				value : _s('bDeposito')
			}, {
				xtype : 'displayfield',
				value : _s('dVencimiento')
			}, {
				xtype : 'datefield',
				startDay : Ext.app.DATESTARTDAY,
				id : 'dVencimiento',
				allowBlank : true
			}, {
				xtype : 'displayfield',
				value : _s('nIdDivisa')
			}, divisas, {
				xtype : 'displayfield',
				value : _s('fPrecioCambio')
			}, cambio]
		};

		var modospago = Ext.app.createStore({
			url : site_url('compras/tipocargo/get_list'),
			model : [{
				name : 'nIdTipoCargo'
			}, {
				name : 'cDescripcion'
			}, {
				name : 'cAlias'
			}]
		});

		var add_mp = function(id, descripcion, importe, id2, cuser, auser, dcreacion, dact) {
			//console.log(descripcion + ' ' + importe + '(' + id2 + ')');
			var f = mpstore.find('cDescripcion', descripcion);
			importe = str_to_float(importe).decimal(Ext.app.DECIMALS);
			if((f >= 0)) {
				var r = mpstore.getAt(f);
				r.set('fImporte', r.data.fImporte + importe);
			} else {
				mpstore.add(new mprt({
					'nIdCargo' : id2,
					'nIdTipoCargo' : id,
					'cDescripcion' : descripcion,
					'fImporte' : parseFloat(importe),
					'cCUser' : cuser,
					'cAUser' : auser,
					'dCreacion' : dcreacion,
					'dAct' : dact
				}));
			}
			return true;
		}
		modospago.load({
			callback : function(r) {
				Ext.each(r, function(r) {
					lineas.control.addPattern("^" + r.data.cAlias + "\\s?(-?[\\d|\\.]+)", function(m, c) {
						//console.log('En cargos');
						var v = (m[1] != null) ? str_to_float(m[1]) : 0;
						if(v != 0) {
							add_mp(r.data.nIdTipoCargo, r.data.cDescripcion, v);
							c.info(r.data.cDescripcion + ' ' + v);
						} else {
							c.info(_s('cargo-importe0-error'));
						}
						return true;
					}, '<b>' + r.data.cAlias + '</b> [' + _s('importe') + '] = ' + r.data.cDescripcion);
				});
			}
		});

		// Añade el comando para guardar
		lineas.control.addPattern("^" + Ext.app.TPV_GUARDAR + "$", function(m, c) {
			form.save();
			return true;
		});
		// Añade el comando para cerrar
		lineas.control.addPattern("^" + Ext.app.TPV_CERRAR + "$", function(m, c) {
			fn_cerrar();
			return true;
		});
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
			width : 400
		});

		var pie = {
			xtype : 'compositefield',
			hideLabel : true,
			msgTarget : 'side',
			items : [/*total, */base, mpgrid, msg]
		};

		// Controles normales
		var controls = [cliente, refs, datas, datas2, linea_articulo, linea_cantidades, linea_otros, lineas.grid, ejemplares, pie];

		// Controles de la cámara
		var pais = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('perfiles/pais/search'),
			fieldLabel : _s('nIdPais'),
			allowBlank : true,
			name : 'nIdPais'
		}));

		var documento = new Ext.Button({
			xtype : 'tbbutton',
			iconCls : "icon-camara",
			text : _s('sin-documento-camara'),
			handler : function() {
				if((data_load != null) && (data_load.nIdFactura != null)) {
					Ext.app.execCmd({
						url : site_url('compras/documentocamara/index/' + data_load.nIdDocumento)
					});
				}
			}
		});

		var tipomercancia = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('compras/tipomercancia/search'),
			allowBlank : true,
			fieldLabel : _s('nIdTipoMercancia'),
			name : 'nIdTipoMercancia'
		}));

		var importecamara = new Ext.form.NumberField({
			//fieldLabel : _s('Importe Cámara'),
			xtype : 'numberfield',
			allowBlank : true,
			allowNegative : true,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			name : 'fImporteCamara',
			selectOnFocus : true
		});
		var monedacamara = new Ext.form.DisplayField();
		var gramos = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'nPeso',
			allowBlank : true,
			allowNegative : false,
			allowDecimals : false,
			selectOnFocus : true
		});
		var controls2 = [pais, {
			xtype : 'compositefield',
			fieldLabel : _s('nPeso'),
			items : [gramos, {
				xtype : 'displayfield',
				value : _s('Gramos')
			}]
		}, {
			xtype : 'compositefield',
			fieldLabel : _s('Importe Cámara'),
			items : [importecamara, monedacamara]
		}, tipomercancia, documento];

		// TABS
		documentosAddTabs(form, controls, 'form-albaranentrada');

		// General
		form.addTab({
			title : _s('Importación'),
			iconCls : 'icon-camara',
			items : {
				xtype : 'panel',
				layout : 'form',
				items : form.addControls(controls2)
			}
		});

		var notas = Ext.app.formNotas();
		var grid_notas = notas.init({
			id : form_id + "_notas",
			url : site_url('compras/albaranentrada'),
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
		var grid_search = search_albaranentrada(form_id, fn_open);

		form.addTab({
			title : _s('Búsqueda'),
			iconCls : 'icon-search',
			items : Ext.app.formSearchForm({
				grid : grid_search,
				audit : false,
				id_grid : form_id + '_g_search_grid'
			})
		});

		/*-------------------------------------------------------------------------
		 * Comandos
		 *-------------------------------------------------------------------------
		 */
		var fn_check_camara = function() {
			// Comprueba el pais
			//var p = Ext.getCmp(form_id + '_nIdPais');
			if(total_tpv == 0)
				return true;
			var p = pais.getValue();
			if(p == '' || p == null) {
				// Coge el país de la dirección
				if(data_load.direccion.nIdPais != null) {
					p = data_load.direccion.nIdPais;
					pais.setValue(p);
				}
			}
			if(p != Ext.app.DEFAULT_PAIS && (importecamara.getValue() == null || importecamara.getValue() == '' )) {
				form.selectTab(4);
				importecamara.setValue((Ext.getCmp(divisas.id).getValue() != null) ? total_divisa : total_tpv);
				Ext.app.msgFly(title, _s('faltan-datos-camara'));
				return false;
			}
			if(p != Ext.app.DEFAULT_PAIS && (tipomercancia.getValue() == null || gramos.getValue() == null)) {
				form.selectTab(4);
				Ext.app.msgFly(title, _s('faltan-datos-camara'));
				return false;
			}
			return true;
		}
		var fn_pedidoscliente = function()
		{
			Ext.app.callRemote({
				url : site_url('compras/albaranentrada/pedidoscliente'),
				params : {
					id : form.getId()
				}
			});
		}
		// Cerrar la venta
		var fn_cerrar = function(fnpost) {
			var fn = function(result) {
				if(result) {
					if(fn_check_camara()) {
						Ext.app.callRemote({
							url : site_url('compras/albaranentrada/cerrar'),
							wait : true,
							timeout: false,
							params : {
								id : form.getId()
							},
							fnok : function(obj) {
								form.refresh();
								fn_pedidoscliente();
								if(fnpost != null) {
									try {
										fnpost();
									} catch (e) {
									}
								}
							}
						});
					}
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
						url : site_url('compras/albaranentrada/abrir'),
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
			text : _s('Cerrar albarán'),
			iconCls : 'icon-generar-doc',
			handler : fn_cerrar,
			id : form.idform + 'btn_cerrar3'
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

		var fn_asignar = function() {
			var url = (data_load.nIdEstado == 2) ? site_url('compras/albaranentrada/asignar') : site_url('compras/albaranentrada/desasignar');
			var f = Ext.getCmp(form.idform);
			if (f.asignado == null)
			{
				f.asignado = function ()
				{
					Ext.app.callRemote({
						url : site_url('compras/albaranentrada/precios'),
						timeout : false,
						params : {
							id : form.getId(),
							cmpid : form.idform
						}
					});
				}
			}
			Ext.app.callRemote({
				url : url,
				timeout: false,
				params : {
					id : form.getId(),
					cmpid : form.idform
				},
				fnok : function() {					
					if(data_load.nIdEstado != 2)
						form.refresh();
				}
			});
		}
		form.addAction('-');
		form.addAction({
			text : _s('Asignar'),
			handler : function() {
				fn_asignar(form);
			},
			iconCls : 'iconoAsignar',
			id : form.idform + 'btn_asignar'
		});

		var fn_precios = function(form) {
			Ext.app.callRemote({
				url : site_url('compras/albaranentrada/precios'),
				timeout : false,
				params : {
					id : form.getId(),
					cmpid : form.idform
				}
			});
		}
		form.addAction('-');
		form.addAction({
			text : _s('Actualizar precios'),
			handler : function() {
				fn_precios(form);
			},
			iconCls : 'icon-precio',
			id : form.idform + 'btn_precios'
		});

		var fn_etiquetas = function(form) {
			Ext.app.callRemote({
				url : site_url('compras/albaranentrada/etiquetas'),
				timeout : false,
				params : {
					id : form.getId()
				},
				fnok : function(res) {
					//form.refresh();
				}
			});
		}
		form.addAction({
			text : _s('Imprimir etiquetas'),
			handler : function() {
				fn_etiquetas(form);
			},
			iconCls : 'icon-etiquetas',
			id : form.idform + 'btn_etiquetas'
		});

		var fn_etiquetas_albaran = function(form) {
			var form_id = Ext.app.createId();

			var select = function(id) {
				var r = report.getValue();
				var url = site_url('catalogo/grupoetiqueta/albaran/' + id + '/' + form.getId() + '/' + r);
				Ext.app.printLabels(url, _s('Imprimir etiquetas albarán'));
				ctl.setValue();
				ctl.focus();
			}
			var ctl = new Ext.form.ComboBox(Ext.app.autocomplete({
				allowBlank : false,
				url : site_url('catalogo/articulo/search'),
				label : _s('Artículo'),
				name : 'idl2',
				fnselect : select,
				anchor : '90%'
			}));

		    var t = Ext.app.combobox({
		        url : site_url('catalogo/grupoetiqueta/printer?list=true'),
		        name : 'report',
		        allowBlank: true,
		        anchor : '100%',
		        label : _s('Formato')
		    });
		    //t['forceSelection'] = false;
		    var report = new Ext.form.ComboBox(t);

			var controls = [ctl, report];

			var form2 = Ext.app.formStandarForm({
				controls : controls,
				timeout : false,
				disableok : true,
				title : _s('Imprimir etiquetas albarán'),
				icon : 'icon-etiquetas'
			});
		    report.store.load();

			form2.show();
			return;

		}

		function incidencias()
		{
            var concurso = new Ext.form.ComboBox(Ext.app.combobox({
                url: site_url('concursos/concurso/search'),
                label: _s('Concursos'),
                name: 'concurso',
                anchor: '90%'
            }));
            
            var controls = [{
                xtype: 'hidden',
                name: 'id',
                value: form.getId()
            }, concurso];
            
            concurso.store.load({
					callback: function() {
						var v = Ext.app.get_config('bp.albaranentrada.concurso.default', 'user');
						if (v != null && v != '')
							concurso.setValue(parseInt(v));
					}
				});
            var url = site_url('compras/albaranentrada/incidencias');
            
            var form2 = Ext.app.formStandarForm({
                controls: controls,
                timeout: false,
                icon: 'icon-page-warning',
                title: _s('Incidencias concurso'),
                url: url,
				fn_pre : function() {
					Ext.app.set_config('bp.albaranentrada.concurso.default', concurso.getValue(), 'user');
				}
            });
            
            form2.show();
		}
		
		form.addAction({
			text : _s('Imprimir etiquetas albarán'),
			handler : function() {
				fn_etiquetas_albaran(form);
			},
			iconCls : 'icon-etiquetas',
			id : form.idform + 'btn_etiquetas_albaran'
		});
		form.addAction('-');
		
		form.addAction({
			text : _s('Ver pedidos clientes'),
			handler : function() {
				fn_pedidoscliente();
			},
			id : form.idform + 'btn_pedidos',
			iconCls : 'iconoPedidoCliente'
		});
		form.addAction({
			text : _s('Ver asignacion'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('compras/albaranentrada/asignacion'),
					params : {
						id : form.getId()
					}
				});
			},
			id : form.idform + 'btn_asignacion',
			iconCls : 'icon-report'
		});
		form.addTools({
			text : _s('Dividir'),
			handler : function() {
				Ext.Msg.prompt(form.getTitle(), _s('Líneas por albarán'), function(ok, v) {
					if(ok != 'ok')
						return;
					v = parseFloat(v);

					Ext.app.callRemote({
						url : site_url('compras/albaranentrada/dividir'),
						timeout : false,
						params : {
							id : form.getId(),
							count : v
						},
						fnok : function(res) {
							form.refresh();
						}
					});
				});
			},
			id : form.idform + 'btn_dividir',
			iconCls : 'icon-split'
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
			text : _s('Incidencias concurso'),
			handler : function() {
				incidencias();
			},
			id : form.idform + 'btn_incidencias',
			iconCls : 'icon-page-warning'
		});
		form.addTools({
			text : _s('Consultar asignación'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('compras/albaranentrada/check_asignacion'),
					params : {
						id : form.getId()
					}
				});
			},
			id : form.idform + 'btn_asignacion2',
			iconCls : 'iconoAsignar'
		});
		form.addTools({
			text : _s('Artículos no consultados'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('compras/albaranentrada/consultados'),
					params : {
						id : form.getId()
					}
				});
			},
			id : form.idform + 'btn_consultados',
			iconCls : 'iconoReport'
		});
        form.addTools('-');
		form.addTools({
			text : _s('Liquidación depósitos'),
			handler : function() {
				Ext.app.callRemote({
					url : site_url('compras/albaranentrada/liquidacion'),
					params : {
						id : form.getId()
					}
				});
			},
			id : form.idform + 'btn_liquidacion',
			iconCls : 'iconoDepositos'
		});

		form.addAction('-');
		addButtonAbiertos(form);
		divisas.store.load();
		pais.store.load();
		tipomercancia.store.load();
		return form.show(open_id);
	} catch (e) {
		console.dir(e);
	}
})();
