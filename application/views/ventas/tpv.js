(function() {
	try {
		/*-------------------------------------------------------------------------
		 * Datos Formulario
		 *-------------------------------------------------------------------------
		 */
		var open_id = "<?php echo $open_id;?>";
		var form_id = "<?php echo $id;?>";
		var title = "<?php echo $title;?>";
		var icon = "<?php echo $icon;?>";

		if(title == '')
			title = _s('TPV');
		if(icon == '')
			icon = 'iconoTPVTab';
		if(form_id == '')
			form_id = Ext.app.createId();

		var allsecciones = "<?php echo isset($allsecciones)?(($allsecciones)?'true':'false'):'false';?>" == 'true';
		var cerrar = "<?php echo isset($allsecciones)?(($allsecciones)?'true':'false'):'true';?>" == 'true';
		var tpv = "<?php echo isset($tpv)?(($tpv)?'true':'false'):'true';?>" == 'true';
		var descuento = (Ext.app.get_config('ventas.tpv.aplicardescuento')=='true')?parseInt(Ext.app.get_config('ventas.tpv.descuento')):0;
		var base = (tpv) ? 'ventas/tpv' : 'ventas/factura';

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
		var suscripciones = null;

		var fn_set_data = function(data) {
			if(data.cliente_id)
			{
				cliente_id = data.cliente_id;
				if (data.nIdDireccionEnvio!=null || form.getId() == null)
				{
					load_combo_direcciones(cliente_id, direccionenvio, 
							data.nIdDireccionEnvio, 
							Ext.app.PERFIL_DIRIGIDO, null, true);
				}
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
			if(data.tooltip_cliente) {
				tooltip_cliente = data.tooltip_cliente;
				msg.update(data.tooltip_cliente);
			}
			if(data.s_vedadas)
				s_vedadas = data.s_vedadas;

			if(data.cliente_datos) {
				lineas.setTarifas(data.cliente_datos.nIdTipoTarifa, data.cliente_datos.tarifas);
			}
		}
		var fn_get_data = function() {
			return {
				cliente_id : cliente_id,
				tooltip_cliente : tooltip_cliente,
				cliente_datos : cliente_datos,
				info_button : info_button,
				data_load : data_load,
				title : title,
				direcciones : direcciones,
				s_defecto : s_defecto,
				s_vedadas : s_vedadas
			}
		}
		// Carga los modos de pago
		var fn_load_mp = function(data) {
			Ext.each(data, function(r) {
				add_mp(r.nIdModoPago, r.cModoPago, r.fImporte, r.nIdAbono, r.nIdFacturaModoPago, r.cCUser, r.cAUser, r.dCreacion, r.dAct);
			});
		}
		// Carga la venta
		var fn_load = function(id, res) {
			if(!tpv)
				notas.load(id);
			data_load = res;
			if(res._bExentoIVA)
				fn_quitar_IVA();
			if(res.nNumero != null && res.nNumero != null) {
				var n = FormatNumeroFactura(res.nNumero, res.serie.nNumero);
				numero.setValue(n);
				form.setTitle(n);
			} else {
				numero.setValue(_s('SIN NUMERO'));
			}
			modospago_delete = new Array();
			if(res.modospago != null)
				fn_load_mp(res.modospago);

			if(res.lineas != null)
				lineas.load(res.lineas);

			fn_load_direcciones(res.nIdCliente, res.nIdDireccion);
			fn_load_cliente(res.nIdCliente);
			load_combo_direcciones(res.nIdCliente, direccionenvio, 
				(res.nIdDireccionEnvio!=null)?res.nIdDireccionEnvio:-1, 
				Ext.app.PERFIL_DIRIGIDO, 
				null, true);

			ultima.setValue(ultimo_texto);
			form.setDirty(false);
			lineas.control.focus();
			if(id_defecto != null)
				secciondefecto.setValue(id_defecto);

			// Suscripciones
			Ext.app.callRemote({
				url : site_url(base + '/suscripciones'),
				wait : true,
				timeout : false,
				params : {
					id : id
				},
				fnok : function(obj) {
					suscripciones = obj.value_data;
					if(obj.value_data != null && obj.value_data.length > 0) {
						if(obj.value_data.length > 1) {
							suscripcion.setText(_s('Varias suscripciones'));
						} else {
							suscripcion.setText(obj.value_data[0].nIdSuscripcion);
						}
						suscripcion.setVisible(true);
					} else {
						suscripcion.setVisible(false);
					}
				}
			});
		}
		var fn_load_cliente = function(id) {
			cliente_id = id;
			fn_docs_load_cliente({
				id : id,
				clientefield : clientefield
			});
		}
		var fn_lang = function() {
			return getLang(data_load);
		}
		// Borrado
		var fn_reset = function() {
			suscripcion.setVisible(false);
			suscripciones = null;
			msg.update('');
			cliente_datos = null;
			data_load = null;
			exentoivahidden.setValue(0);
			lineas.exentoIVA(false);
			modospago_delete = new Array();
			lineas.clear();
			mpstore.removeAll();
			total_mp = 0;
			total_tpv = 0;
			cliente_id = null;
			ultima.setValue(ultimo_texto);
			ult_cambio.setValue(ultimo_cambio);
			form.setData({
				value_data : {
					'nIdCaja' : parseInt(Ext.app.get_config('bp.tpv.caja')),
					'nIdSerie' : parseInt(Ext.app.get_config('bp.tpv.serie')),
					'dFecha' : DateToNumber((new Date).getTime()),
					'nIdEstado' : 1
				}
			}, true);
			lineas.control.focus();
			if(id_defecto != null)
				secciondefecto.setValue(id_defecto);
			if(descuento != 0 && ctldescuento != null)
				ctldescuento.setValue(descuento);
		}
		// Cerrar la venta
		var fn_cerrar = function(imprimir, impreso, report) {
			var openbox = false;
			var cambio = 0;

			var fn = function(result) {
				Ext.app.openBox();
				Ext.app.callRemote({
					url : site_url(base + '/cerrar'),
					wait : true,
					timeout : false,
					params : {
						id : result.id
					},
					fnok : function(obj) {
						if((Ext.app.get_config('bp.factura.ticket.print')  == 'true') || imprimir === true)
							fn_ticket(impreso, report);

						if(obj.abonos != null) {
							for(var i = 0; i < obj.abonos.length; i++) {
								var id = obj.abonos[i];
								Ext.app.callRemote({
									url : site_url('ventas/abono/printer'),
									params : {
										id : id,
										lang : fn_lang(),
										report : Ext.app.FORMATOABONO,
										title : _s('Vale') + ' ' + id
									}
								});
							}
						}
						var i = result.id;
						ultimo_id = i;
						ultimo_title = form.getTitle();
						ultimo_texto = _s('Última') + ': ' + obj.numero + ' - ' + _s('Total') + ': ' + Ext.app.numberFormatter(total_tpv);
						ultima.setValue(ultimo_texto);
						ultimo_cambio = _s('Cambio') + ': ' + Ext.app.numberFormatter(cambio);
						ult_cambio.setValue(ultimo_cambio);
						// Evento de factura cerrada
						if(tpv) {
							Ext.app.eventos.fire('factura.close', {
								id : result.id,
								numero : obj.numero,
								data : {
									data : obj.data
								},
								importe : total_tpv
							});
						}
						if((Ext.app.get_config('bp.factura.ticket.nuevo')  == 'true') && cerrar)
							form.reset();
						else
							form.load(result.id);
					}
				});
			}
			var fn_final = function() {
				if(form.isDirty()) {
					form.save(fn, false);
				} else {
					fn({
						id : form.getId()
					});
				}
			}
			if(total_tpv != total_mp) {
				Ext.app.msgFly(title, _s('tpv-error-importe-no-completado'));
				return false;
			}
			// Comprueba modos de pagos imposibles
			if (mpstore.getCount() > 1) {
				var modos = {
					'act'	: 0,
					'ab' 	: 0,
					'ef' 	: 0,
					're' 	: 0,
					'otro' 	: 0,
					'ab-'	: 0
					};
				mpstore.each(function(item){
					var a = item.get('nIdModoPago');
					var b = item.get('fImporte');
					if (in_array(a, [
						Ext.app.MODOPAGO_AMEXDINERS, 
						Ext.app.MODOPAGO_CHEQUE, 
						Ext.app.MODOPAGO_DATAFONOECOMMERCE, 
						Ext.app.MODOPAGO_TARJETA, 
						Ext.app.MODOPAGO_METÁLICO, 
						Ext.app.MODOPAGO_TRANSFERENCIA])) {
						++modos['ef'];
					}
					else if (a == Ext.app.MODOPAGO_ABONO && b < 0) {
						++modos['ab-'];
					}
					else if (a == Ext.app.MODOPAGO_ABONO && b > 0) {
						++modos['ab'];
					}
					else if (a == Ext.app.MODOPAGO_ACUENTA) {
						++modos['act'];
					}
					else if (a == Ext.app.MODOPAGO_REEMBOLSO) {
						++modos['re'];
					}
					else {
						++modos['otro'];
					}
				});

				//Comprueba errores
				if (modos['act'] > 0 && modos['act'] != mpstore.getCount())
				{
					Ext.app.msgFly(title, _s('Pago A CUENTA combinado con otros'));
					return false;
				}
				if (modos['ab'] > 0 && ((modos['ab'] + modos['ef']) != mpstore.getCount()))
				{
					Ext.app.msgFly(title, _s('Pago con ABONO combinado con otros diferente a EFECTIVO'));
					return false;
				}
				if (modos['re'] > 0 && modos['re'] != mpstore.getCount())
				{
					Ext.app.msgFly(title, _s('Pago REEMBOLSOS combinado con otros'));
					return false;
				}
				if (modos['re'] > 0 && $ft['total'] < 0)
				{
					Ext.app.msgFly(title, _s('Pago REEMBOLSOS negativo combinado con otros'));
					return false;
				}
				if (modos['ab-'] > 0 && modos['ab-'] != mpstore.getCount())
				{
					Ext.app.msgFly(title, _s('Pago ABONO NEGATIVO combinado con otros'));
					return false;
				}
				if (modos['otro'] > 0)
				{
					Ext.app.msgFly(title, _s('Combinación de pagos no conocida'));
					return false;
				}
				//Solo quedan uso de ABONOS y EFECTIVOS combinados
			}

			var f = mpstore.find('nIdModoPago', Ext.app.MODOPAGO_METALICO);
			if(f >= 0) {
				openbox = true;
				var r = mpstore.getAt(f);
				var apagar = r.data.fImporte;
				//apagar = apagar.replace(/,/, '.');
				apagar = parseFloat(apagar);
				if(apagar > 0) {
					var controls = [{
						id : form_id + 'apagar',
						fieldLabel : _s('Pendiente'),
						value : Ext.app.numberFormatter(apagar),
						disabled : true,
						allowNegative : false,
						allowDecimals : true,
						decimalPrecision : Ext.app.DECIMALS,
						cls : 'pago-apagar-field',
						width : 200,
						height : 35,
						xtype : "textfield"
					}, {
						id : form_id + 'cliente',
						fieldLabel : _s('Cliente'),
						value : Ext.app.numberFormatter(0),
						allowNegative : false,
						width : 200,
						height : 35,
						allowDecimals : true,
						decimalPrecision : Ext.app.DECIMALS,
						enableKeyEvents : true,
						listeners : {
							'keyup' : function(t, e) {
								try {
									if(e.getKey() === e.ENTER) {
										Ext.getCmp(form_id + 'ventana_window').close();
										fn_final();
									} else {
										var act = Ext.getCmp(form_id + 'cliente').getValue();
										act = act.replace(/,/, '.');
										act = parseFloat(act);
										var diff = act - apagar;
										//diff = diff.replace(/,/, '.');
										diff = parseFloat(diff).decimal(Ext.app.DECIMALS);
										//console.log('Act: ' + act + ' APagar: ' + apagar + ' Cambio: ' + diff);
										Ext.getCmp(form_id + 'cambio').setValue(Ext.app.numberFormatter(diff));
										cambio = diff;
									}
								} catch (e) {
									console.dir(e);
								}
							}
						},
						cls : 'pago-cliente-field',
						xtype : "textfield"
					}, {
						id : form_id + 'cambio',
						fieldLabel : _s('Cambio'),
						value : Ext.app.numberFormatter(0),
						disabled : true,
						width : 200,
						height : 35,
						allowNegative : false,
						allowDecimals : true,
						decimalPrecision : Ext.app.DECIMALS,
						cls : 'pago-cambio-field',
						xtype : "textfield"
					}];

					var form_cambio = Ext.app.formStandarForm({
						controls : controls,
						title : _s('Cambio'),
						focus : form_id + 'cliente',
						width : 350,
						id : form_id + 'ventana',
						fn_ok : fn_final
					});

					form_cambio.show();
				} else
					fn_final();
			} else {
				fn_final();
			}
		}
		// Imprimir Ticket
		var fn_ticket = function(impreso, report) {
			//console.log('ticket ' + impreso + ' ' + report);
			var fn = function(result) {
				if(result) {
					if(impreso !== true) {
						var r = '';
						if (report == null) {
							r = Ext.app.get_config('bp.factura.ticket');
							r = (r !== null && r != '')?('/' + r):'';
						} else {
							r = '/' + report;
						}
						Ext.app.printTicket(site_url(base + '/ticket/' + form.getId() + r), form.getTitle());
					}
					else
						form.print();
				}
			}
			if(form.isDirty()) {
				form.save(fn);
			} else {
				fn(true);
			}
		}
		// Guardar
		var fn_save = function(id, data) {
			// Añadimos las líneas
			var index = 0;
			if(Ext.getCmp(direcciones.id).getValue() != '' && Ext.getCmp(direcciones.id).getValue() != '')
				data['nIdDireccion'] = Ext.getCmp(direcciones.id).getValue();
			if(Ext.getCmp(series.id).getValue() == null || Ext.getCmp(series.id).getValue() == '')
				data['nIdSerie'] = parseInt(Ext.app.get_config('bp.tpv.serie'));
			var idcliente = cliente_id;
			var fecha = fechafactura.getValue();
			if(fecha == null || fecha == '' || ((data['nIdEstado'] == 1 || data['nIdEstado'] == null) && !Ext.app.is_allow('ventas.factura.administrar'))) {
				data['dFecha'] = DateToNumber((new Date).getTime());
			}
			if((data_load != null && idcliente != data_load.nIdCliente) || data_load == null) {
				data['nIdCliente'] = idcliente;
			}
			if(id == null) {
				data['nIdCaja'] = Ext.getCmp(cajas.id).getValue();
				data['nIdSerie'] = Ext.getCmp(series.id).getValue();
			}
			data['nIdDireccionEnvio'] = Ext.getCmp(direccionenvio.id).getValue();
			mpgrid.getStore().each(function(r) {
				if(r.isModified('fImporte') || (r.data.nIdFacturaModoPago == null) || (id == null) || (data['nIdCaja'] != null)) {
					data['modospago[' + index + '][nIdModoPago]'] = r.data.nIdModoPago;
					data['modospago[' + index + '][id]'] = r.data.nIdFacturaModoPago;
					data['modospago[' + index + '][nIdFacturaModoPago]'] = r.data.nIdFacturaModoPago;
					data['modospago[' + index + '][fImporte]'] = r.data.fImporte;
					data['modospago[' + index + '][nIdAbono]'] = r.data.nIdAbono;
					if(data['dFecha'] != null)
						data['modospago[' + index + '][dFecha]'] = data['dFecha'];
					data['modospago[' + index + '][nIdCaja]'] = Ext.getCmp(cajas.id).getValue();
					index++;
				}
			});
			// Los borrados
			Ext.each(modospago_delete, function(i) {
				data['modospago[' + index + '][delete]'] = i;
				index++;
			});
			data = lineas.get(data);
			lineas.control.focus();
			id_defecto = secciondefecto.getValue();

			return data;
		}
		var suscripcion = new Ext.Button({
			xtype : 'tbbutton',
			iconCls : "iconoSuscripciones",
			text : '',
			handler : function() {
				if(suscripciones.length == 1) {
					Ext.app.execCmd({
						url : site_url('suscripciones/suscripcion/index/' + suscripciones[0].nIdSuscripcion)
					});
					return;
				}
				var html = '';
				var text = '';
				Ext.each(suscripciones, function(item) {
					var url = "javascript:Ext.app.execCmd({url: site_url('suscripciones/suscripcion/index/" + item.nIdSuscripcion + "')});";
					html += '<a href="' + url + '">' + item.nIdSuscripcion + '</a><br/>';
				});
				Ext.app.msgInfo(title, html);
			}
		});

		// Enables y disables
		var fn_enable_disable = function(form) {
			var bloqueado = ((data_load != null) && (data_load.nIdEstado != 1));
			exentoiva.setText(_s('Aplicar Exento IVA'));
			exentoiva.enable();
			//console.log('Exento IVA: ' + exentoivahidden.getValue());
			if(exentoivahidden.getValue() == 1 || (data_load != null) && data_load._bExentoIVA) {
				exentoiva.setText(_s('FACTURA EXENTA DE IVA'));
				//exentoiva.disable();
			} else if(bloqueado) {
				//exentoiva.disable();
			}
			if(bloqueado) {
				Ext.getCmp(cajas.id).disable();
				Ext.getCmp(series.id).disable();
				fechafactura.disable();
				lineas.disable();
				//mpgrid.disable();
			} else {
				Ext.getCmp(cajas.id).enable();
				Ext.getCmp(series.id).enable();
				fechafactura.enable();
				lineas.enable();
				//mpgrid.enable();
			}

			Ext.app.formEnableList({
				list : [form.idform + 'btn_ult_ticket', form.idform + 'btn_open_ticket'],
				enable : ultimo_id != null
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_cerrar3'],
				enable : (!bloqueado) && (form.getId() > 0)
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_ajustar'],
				enable : (form.getId() > 0)
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_contabilizar'],
				enable : (form.getId() > 0) && data_load.nIdEstado == 2
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_descontabilizar'],
				enable : (form.getId() > 0) && data_load.nIdEstado == 3
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_ticket'
				, form.idform + 'btn_copiar'
				, form.idform + 'btn_copiar2'
				, form.idform + 'btn_pedidos'
				, form.idform + 'btn_coste'
				, form.idform + 'btn_copiar3'],
				enable : (form.getId() > 0)
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_abonar', form.idform + 'btn_enviar'],
				enable : (bloqueado) && (form.getId() > 0)
			});
			Ext.app.formEnableList({
				list : [form.idform + 'btn_abrir'],
				enable : (form.getId() > 0 && data_load.nIdEstado == 4)
			});
			if(Ext.app.is_allow('ventas.factura.administrar')) {
				Ext.getCmp(fechafactura.id).enable();
				Ext.getCmp(cajas.id).enable();
				Ext.getCmp(series.id).enable();
			}
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
			id : form_id,
			title : title,
			icon : icon,
			url : site_url(base),
			fn_load : fn_load,
			fn_reset : fn_reset,
			fn_save : fn_save,
			fn_lang : fn_lang,
			fn_enable_disable : fn_enable_disable
		});

		var controles = documentosCliente(form, 'nIdDireccion', fn_get_data, fn_set_data, Ext.app.PERFIL_FACTURACION)
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

		/*-------------------------------------------------------------------------
		 * Control de línea
		 *-------------------------------------------------------------------------
		 */
		var total_tpv = 0;
		var total_mp = 0;

		var total = new Ext.form.TextField({
			cls : 'total-field',
			readOnly : true,
			fieldLabel : _s('fTotal'),
			value : Ext.app.numberFormatter(0),
			height : 50,
			width : 160
		});

		var pendiente = new Ext.form.DisplayField({
			cls : 'pendiente-field',
			value : Ext.app.numberFormatter(0),
			height : 50,
			width : 110
		});

		var showpendiente = function() {
			pendiente.setValue(Ext.app.numberFormatter(total_tpv - total_mp));
		}
		var showtotal = function(s) {
			var t = 0;
			var ct = 0;
			var ejs = 0;
			s.each(function(r) {
				t += r.data.fTotal;
				ct++;
				ejs += r.data.nCantidad;
			})
			total_tpv = t.decimal(Ext.app.DECIMALS);
			total.setValue(Ext.app.numberFormatter(total_tpv));
			ejemplares.setValue(sprintf(_s('lineas-ejemplares'), ct, ejs));
			showpendiente();
			form.setDirty();
		}
		var showtotalmodopago = function(s) {
			var t = 0;
			s.each(function(r) {
				t += (r.data.fImporte);
			})
			total_mp = t.decimal(Ext.app.DECIMALS);
			showpendiente();
		}
		var seccion_defecto = get_seccion_defecto(fn_get_data, fn_set_data, 'bp.factura.secciones.defecto', 'bp.factura.secciones.vedadas', allsecciones, true);
		var secciondefecto = seccion_defecto.secciondefecto;
		var fn_get_seccion = seccion_defecto.fn_get_seccion;

		var lineas = docLineaControl({
			cache : Ext.app.TPV_CACHE,
			nIdDocumento : 'nIdAlbaran',
			nIdLinea : 'nIdLineaAlbaran',
			cReferencia : 'cRefCliente',
			fn_get_seccion : fn_get_seccion,
			fn_change : showtotal,
			hide : (tpv) ? Ext.app.GRIDCOLUMNS_HIDE_TPV : Ext.app.GRIDCOLUMNS_HIDE_FACTURACION,
			anchor : "100% 40%",
			url_search : site_url('catalogo/articulo/search'),
			url_load : site_url('catalogo/articulo/get2'),
			extrafields : [{
				hidden : true,
				dataIndex : 'nIdLineaPedido',
				hideable : false,
				sortable : false
			}]
		});

		if (!tpv)
		{
			var m_albaran = lineas.addMenu({
				text : _s('Ver albarán'),
				handler : function() {
					var record = lineas.getItemSelect();
					if(record != null) {
						Ext.app.execCmd({
							url : site_url(( tpv ? 'ventas/albaransalida2/index/' : 'ventas/albaransalida/index/') + record.data.nIdDocumento)
						});
					}
				},
				iconCls : 'icon-albaran-salida'
			});
		}
		addMenuPedir(lineas);
		addMenuDocumentos(lineas);
		addMenuVentas(lineas);
		addMenuStock(lineas);

		if (!tpv)
		{
			var fn_check_menu = function(item) {
				(item.data.nIdDocumento != null) ? m_albaran.enable() : m_albaran.disable();
			}
			lineas.setCheckMenu(fn_check_menu);
		}

		/*-------------------------------------------------------------------------
		 * Resto de los controles
		 *-------------------------------------------------------------------------
		 */
		var exentoiva = new Ext.menu.Item({
			xtype : 'tbbutton',
			iconCls : "icon-taxes",
			text : _s('Aplicar Exento IVA'),
			handler : function() {
				if((exentoivahidden.getValue() == 1))
					Ext.app.msgError(title, _s('exentoiva-nodesmarcable'));
				else if(((data_load != null) && (data_load.nIdEstado != 1)))
					Ext.app.msgError(title, _s('exentoiva-facturacerrada'));
				else
					fn_quitar_IVA();
			}
		});

		var fn_quitar_IVA = function() {
			exentoivahidden.setValue(true);
			lineas.exentoIVA(true);
			exentoiva.setText(_s('FACTURA EXENTA DE IVA'));
			exentoiva.disable();
		}
		var exentoivahidden = new Ext.form.Hidden({
			xtype : 'hidden',
			value : 0,
			name : '_bExentoIVA'
		});

		var numero = new Ext.form.DisplayField({
			cls : 'numero-factura-field',
			value : _s('SIN NUMERO'),
			height : 15,
			width : 150
		});

		var ultima = new Ext.form.TextField({
			cls : 'ultima-factura-field',
			fieldLabel : _s('Información'),

			value : '',
			//height: 15,
			disabled : true,
			anchor : '90%',
			width : 500
		});

		var ult_cambio = new Ext.form.TextField({
			cls : 'cambio-factura-field',
			fieldLabel : _s('Información'),

			value : '',
			//height: 15,
			disabled : true,
			anchor : '10%',
			width : 100
		});

		var info = {
			xtype : 'compositefield',
			fieldLabel : _s('Información'),
			msgTarget : 'side',
			anchor : '-20',
			items : [ultima, ult_cambio, numero]
		};

		var refcli = new Ext.form.TextField({
			name : 'cRefCliente',
			allowBlank : true,
			width : '250'
		});

		var refint = new Ext.form.TextField({
			xtype : 'textfield',
			name : 'cRefInterna',
			allowBlank : true,
			width : '250'
		});

		var refs = {
			xtype : 'compositefield',
			fieldLabel : _s('cRefCliente'),
			msgTarget : 'side',
			anchor : '-20',
			/*defaults: {
			 flex: 1
			 },*/
			items : [refcli, {
				xtype : 'displayfield',
				value : _s('cRefInterna')
			}, refint, {
				xtype : 'displayfield',
				value : _s('bCobrado')
			}, {
				xtype : 'checkbox',
				id : 'bCobrado',
				// anchor : '90%',
				allowBlank : true,
				fieldLabel : _s('bCobrado')
			}, {
				xtype : 'displayfield',
				value : _s('bMostrarWeb')
			}, {
				xtype : 'checkbox',
				id : 'bMostrarWeb',
				// anchor : '90%',
				allowBlank : true,
				fieldLabel : _s('bMostrarWeb')
			}, suscripcion]
		};

		var series = Ext.app.combobox({
			url : site_url('ventas/serie/search'),
			//anchor: "90%",
			allowBlank : false,
			id : 'nIdSerie'
		});

		var cajas = Ext.app.combobox({
			url : site_url('ventas/caja/search'),
			//anchor: "90%",
			allowBlank : false,
			id : 'nIdCaja'
		});

		var estado = Ext.app.combobox({
			url : site_url('ventas/estadofactura/search'),
			//anchor: "90%",
			disabled : true,
			allowBlank : false,
			readOnly : true,
			id : 'nIdEstado'
		});
		//estado.disable();

		var fechafactura = new Ext.form.DateField({
			xtype : 'datefield',
			startDay : Ext.app.DATESTARTDAY,
			name : 'dFecha',
			value : new Date(),
			allowBlank : true
		});
		var datas = {
			xtype : 'compositefield',
			fieldLabel : _s('Caja'),
			msgTarget : 'side',
			anchor : '-20',
			/*defaults: {
			 flex: 1
			 },*/
			items : [cajas, {
				xtype : 'displayfield',
				value : _s('Serie')
			}, series, {
				xtype : 'displayfield',
				value : _s('Fecha')
			}, fechafactura, {
				xtype : 'displayfield',
				value : _s('Estado')
			}, estado]
		};

		var ejemplares = new Ext.form.DisplayField({
			cls : 'lineas-ejemplares-field',
			value : '',
			height : 10,
			//disabled: true,
			anchor : '100%'
		});

		var mpfields = {
			id : 'id',
			model : [{
				name : 'nIdFacturaModoPago'
			}, {
				name : 'id'
			}, {
				name : 'nIdModoPago'
			}, {
				name : 'cDescripcion'
			}, {
				name : 'fImporte'
			}, {
				name : 'nIdAbono'
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
			if(r.data.nIdFacturaModoPago != null)
				modospago_delete.push(r.data.nIdFacturaModoPago);
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
			width : 300,
			columns : [mpitemDeleter, {
				header : _s('Id'),
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : 'nIdFacturaModoPago',
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
				header : _s('nIdAbono'),
				align : 'center',
				width : Ext.app.TAM_COLUMN_NUMBER,
				dataIndex : 'nIdAbono',
				sortable : true
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

		var ctxRow = null;
		var contextmenu = new Ext.menu.Menu({
			allowOtherMenus : false/*,
			items : [{
				text : _s('Ver abono'),
				handler : function() {
					try {
						if(ctxRow) {
							Ext.app.execCmd({
								url : site_url('ventas/abono/index/' + ctxRow.data.nIdAbono)
							});
						}
					} catch (e) {
						console.dir(e);
					}
				},
				iconCls : 'icon-abono'
			}]*/
		});
		//contextmenu.add('-');
		var m_verabono = contextmenu.add({
				text : _s('Ver abono'),
				handler : function() {
					//var record = cm_lineas.getItemSelect();
					if(ctxRow.data.nIdAbono != null) {
							Ext.app.execCmd({
								url : site_url('ventas/abono/index/' + ctxRow.data.nIdAbono)
							});
						}
				},
				iconCls : 'icon-abono'
			});
		var m_cambiar = contextmenu.add({
				text : _s('Cambiar modo pago'),
				handler : function() {
					//var record = cm_lineas.getItemSelect();				
					if(ctxRow.data.nIdFacturaModoPago != null) {

					    var modopago = new Ext.form.ComboBox(Ext.app.combobox({
					        url: site_url('ventas/modopago/search'),
					        name: 'mp',
					        anchor: '100%',
					        label: _s('nIdModoPago')
					    }));
					    
					    
					    var controls = [modopago, {
						        xtype:'hidden',
						        name: 'id',
						        value : ctxRow.data.nIdFacturaModoPago
						    }, {
						        xtype:'hidden',
						        name: 'cuenta',
						        value : (cliente_datos.bCredito == true && cliente_datos.nIdCuenta != null)
						    }];
					    
					    var url = site_url(base + '/modopago');
					    
					    var form2 = Ext.app.formStandarForm({
					        controls: controls,
					        title: _s('Cambiar modo pago'),
					        labelWidth: 100,
					        icon: 'icon-change',
					        url: url,
					        fn_ok: function(res) {
					        	form.refresh();
					        	return true;
					        }
					    });
					    
					    Ext.app.loadStores([{
					        store: modopago.store
					    }]);
					    form2.show();
					    return;
					}
				},
				iconCls : 'icon-change'
			});

		mpgrid.on('rowcontextmenu', function(gridPanel, rowIndex, e) {
			e.stopEvent();
			ctxRow = mpgrid.store.getAt(rowIndex);
			(ctxRow.data.nIdAbono != null ) ? m_verabono.enable() : m_verabono.disable();
			(ctxRow.data.nIdFacturaModoPago != null ) ? m_cambiar.enable() : m_cambiar.disable();
			//if(ctxRow.data.nIdAbono != null)
			contextmenu.showAt(e.getXY());
		});


		/*var cm_lineas = fn_contextmenu();
		var contextmenu = Ext.app.addContextMenuEmpty(mpgrid, cm_lineas);
		cm_lineas.setContextMenu(contextmenu)
		contextmenu.add('-');
		var n_verabono = contextmenu.add({
				text : _s('Ver abono'),
				handler : function() {
					var record = cm_lineas.getItemSelect();
					if(record.data.nIdAbono != null) {
							Ext.app.execCmd({
								url : site_url('ventas/abono/index/' + record.data.nIdAbono)
							});
						}
				},
				iconCls : 'icon-abono'
			});


		var fn_check_menu = function(item) {
			//
			(item.data.nIdAbono != null ) ? m_verabono.enable() : m_verabono.disable();
			//(item.data.bProcesado !== true) ? m_procesar.enable() : m_procesar.disable();
		}

		cm_lineas.setCheckMenu(fn_check_menu);*/


		var add_mp = function(id, descripcion, importe, abono, id2, cuser, auser, dcreacion, dact) {
			if(id2 == null) {
				var f = mpstore.find('cDescripcion', descripcion);
				importe = str_to_float(importe).decimal(Ext.app.DECIMALS);
				if((f >= 0) && (id != Ext.app.MODOPAGO_ABONO)) {
					var r = mpstore.getAt(f);
					r.set('fImporte', r.data.fImporte + importe);
				} else {
					if(id == Ext.app.MODOPAGO_ACUENTA) {
						// tiene que tener cuenta
						//console.dir(cliente_datos);
						if(cliente_datos == null) {
							Ext.app.msgFly(title, _s('tpv-cliente-sin-cuenta'));
							return false;
						} else if(cliente_datos.bCredito != true || cliente_datos.nIdCuenta == null) {
							Ext.app.msgFly(title, _s('tpv-cliente-sin-cuenta'));
							return false;
						}
						// tiene que ser modo único
						if(mpstore.getCount() > 0) {
							Ext.app.msgFly(title, _s('tpv-cliente-solo-a-cuenta'));
							return false;
						}
					} else {
						var f = mpstore.find('nIdModoPago', Ext.app.MODOPAGO_ACUENTA);
						if(f >= 0) {
							Ext.app.msgFly(title, _s('tpv-cliente-solo-a-cuenta'));
							return false;
						}				
					}
					if((id == Ext.app.MODOPAGO_ABONO) && cliente_datos != null && cliente_datos.bCredito == true) {
						Ext.app.msgFly(title, _s('tpv-cliente-solo-no-vales'));
						return false;					
					}

					if((id == Ext.app.MODOPAGO_ABONO) && (importe > 0) && (abono == null)) {
						// Abono de positivo, a pagar
						Ext.Msg.prompt(title, _s('Número abono'), function(ok, v) {
							if(ok != 'ok')
								return false;
							abono = parseInt(v);
							Ext.app.callRemote({
								url : site_url('ventas/abono/get2'),
								params : {
									id : abono
								},
								nomsg : true,
								fnok : function(res) {
									var pendiente = res.value_data.fPendiente;
									if(pendiente < importe) {
										importe = pendiente;
										Ext.app.msgFly(title, sprintf(_s('tpv-abono-insuficiente'), abono, importe));
									} else if(pendiente == importe) {
										Ext.app.msgFly(title, sprintf(_s('tpv-abono-usadocompleto'), abono));
									}
									//console.log('importe ' + importe);
									if(importe > 0) {
										mpstore.add(new mprt({
											'nIdFacturaModoPago' : id2,
											'nIdModoPago' : id,
											'cDescripcion' : descripcion,
											'fImporte' : parseFloat(importe),
											'nIdAbono' : abono,
											'cCUser' : cuser,
											'cAUser' : auser,
											'dCreacion' : dcreacion,
											'dAct' : dact
										}));
									}
									var nota = '';
									Ext.each(res.value_data.notas, function (item) {
										nota += item.tObservacion + '<br/>';
									});
									if (nota != '') {
										Ext.app.msgInfo(title, _s('ALARMAS ABONO') + ':<br/> ' + nota);
									}
									lineas.control.focus();
								}
							});
						});
					} else {
						mpstore.add(new mprt({
							'nIdFacturaModoPago' : id2,
							'nIdModoPago' : id,
							'cDescripcion' : descripcion,
							'fImporte' : parseFloat(importe),
							'nIdAbono' : abono,
							'cCUser' : cuser,
							'cAUser' : auser,
							'dCreacion' : dcreacion,
							'dAct' : dact
						}));
					}
				}
			} else {
				mpstore.add(new mprt({
					'nIdFacturaModoPago' : id2,
					'nIdModoPago' : id,
					'cDescripcion' : descripcion,
					'fImporte' : parseFloat(importe),
					'nIdAbono' : abono,
					'cCUser' : cuser,
					'cAUser' : auser,
					'dCreacion' : dcreacion,
					'dAct' : dact
				}));
			}
			return true;
		}
		var modospago = Ext.app.createStore({
			url : site_url('ventas/modopago/get_list'),
			model : [{
				name : 'nIdModoPago'
			}, {
				name : 'cDescripcion'
			}, {
				name : 'cAlias'
			}]
		});

		var fn_add_modopago = function (m,c) {
			var v = total_tpv.decimal(Ext.app.DECIMALS) - total_mp.decimal(Ext.app.DECIMALS);
			v = v.decimal(Ext.app.DECIMALS);
			if(v != 0) {
				if(!add_mp(r.data.nIdModoPago, r.data.cDescripcion, v))
					return true;
				c.info(r.data.cDescripcion + ' ' + v);
			}
			fn_cerrar();
			return true;
		}

		modospago.load({
			callback : function(r) {
				Ext.each(r, function(r) {
					var ar_alias = r.data.cAlias.split(';');
					Ext.each(ar_alias, function(alias) {
						if(alias != '' && alias != null) {
							lineas.control.addPattern("^" + alias + "\\s?(-?[\\d|\\.]+)", function(m, c) {
								var v = (m[1] != null) ? str_to_float(m[1]) : 0;
								if(v != 0) {
									add_mp(r.data.nIdModoPago, r.data.cDescripcion, v);
									c.info(r.data.cDescripcion + ' ' + v);
								} else {
									c.info(_s('modopago-importe0-error'));
								}
								return true;
							}, '<b>' + alias + '</b> [' + _s('importe') + '] = ' + r.data.cDescripcion);
						}
						lineas.control.addPattern("^" + alias + "(\\s?)$", function(m, c) {
							var v = total_tpv.decimal(Ext.app.DECIMALS) - total_mp.decimal(Ext.app.DECIMALS);
							v = v.decimal(Ext.app.DECIMALS);
							if(v != 0) {
								add_mp(r.data.nIdModoPago, r.data.cDescripcion, v);
								c.info(r.data.cDescripcion + ' ' + v);
							} else {
								c.info(_s('modopago-importe0-error'));
							}
							return true;
						});
						lineas.control.addPattern("^" + Ext.app.TPV_CMD + alias + "(\\s?)$", function (m,c) {
							var v = total_tpv.decimal(Ext.app.DECIMALS) - total_mp.decimal(Ext.app.DECIMALS);
							v = v.decimal(Ext.app.DECIMALS);
							if(v != 0) {
								if(!add_mp(r.data.nIdModoPago, r.data.cDescripcion, v))
									return true;
								c.info(r.data.cDescripcion + ' ' + v);
							}
							fn_cerrar();
							return true;
						});
						lineas.control.addPattern("^" + Ext.app.TPV_CMD2 + alias + "(\\s?)$", function (m,c) {
							var v = total_tpv.decimal(Ext.app.DECIMALS) - total_mp.decimal(Ext.app.DECIMALS);
							v = v.decimal(Ext.app.DECIMALS);
							if(v != 0) {
								if(!add_mp(r.data.nIdModoPago, r.data.cDescripcion, v))
									return true;
								c.info(r.data.cDescripcion + ' ' + v);
							}
							fn_cerrar();
							return true;
						});
					});
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

		form.addKeyMap({
			key : Ext.app.KEYMAP_FORM_CLOSEDOC,
			ctrl : Ext.app.KEYMAP_FORM_CTRL,
			alt : Ext.app.KEYMAP_FORM_ALT,
			shift : Ext.app.KEYMAP_FORM_SHIFT,
			stopEvent : true,
			fn : function() {
				fn_cerrar();
			}
		});

		lineas.control.addPattern("^" + Ext.app.TPV_CERRAR_IMPRESO + "$", function(m, c) {
			fn_cerrar(true, true);
			return true;
		});

		lineas.control.addPattern("^" + Ext.app.TPV_CERRAR_TICKET + "$", function(m, c) {
			fn_cerrar(true, false);
			return true;
		});

		lineas.control.addPattern("^" + Ext.app.TPV_CERRAR_TICKET_FACTURA + "$", function(m, c) {
			fn_cerrar(true, false, Ext.app.get_config('bp.factura.ticket.factura'));
			return true;
		});

		lineas.control.addPattern("^" + Ext.app.TPV_CERRAR_NOTICKET + "$", function(m, c) {
			fn_cerrar(false, false);
			return true;
		});

		lineas.control.addPattern("^" + Ext.app.TPV_OPENBOX + "$", function(m, c) {
			Ext.app.openBox();
			return true;
		});

		lineas.control.addPattern("^" + Ext.app.TPV_DESCUENTO + "\\s?([\\d|\\.]+)$", function(m, c) {
			var v = (m[1] != null) ? str_to_float(m[1]) : 0;
			var v = v.decimal(Ext.app.DECIMALS);
			lineas.descuento(v);
			return true;
		});
		var add_albaran = function(v, fn_fin) {
			//Hay Factura?
			if(form.getId() != null) {
				if(data_load.nIdEstado != 1) {
					Ext.app.msgFly(title, _s('error-no-albaran-factura-cerrada'));
					if(fn_fin != null)
						fn_fin(false);
					return;
				}
			}

			var fn = function() {
				Ext.app.callRemote({
					url : site_url(( tpv ? 'ventas/albaransalida2/upd' : 'ventas/albaransalida/upd')),
					params : {
						id : v,
						nIdFactura : form.getId()
					},
					fnok : function(ok) {
						if(ok)
							form.refresh();
						if(fn_fin != null)
							fn_fin(ok);
					}
				});
			}
			//Se crea factura y se añade el albarán
			Ext.app.callRemote({
				url : site_url(( tpv ? 'ventas/albaransalida2/get/' : 'ventas/albaransalida/get/') + v + '/lineas'),
				fnok : function(res) {
					if(res.value_data.nIdFactura != null) {
						Ext.app.msgFly(title, sprintf(_s('error-albaran-facturado'), res.value_data.nIdFactura));
						if(fn_fin != null)
							fn_fin(false);
						return;

					}
					if(form.getId() != null) {
						fn();
					} else {
						cliente_id = res.value_data.nIdCliente;
						form.save(function(ok) {
							if(ok) {
								fn();
							}
						});
					}
				}
			});
		}
		lineas.control.addPattern("^" + Ext.app.TPV_ADD_ALBARAN + "\\s?(\\d+)$", function(m, c) {
			var v = (m[1] != null) ? (m[1]) : 0;
			add_albaran(v);
			return true;
		});
		lineas.control.addPattern("^" + Ext.app.TPV_ADD_ALBARAN + "\\s?$", function(m, c) {

			if(cliente_id == null) {
				Ext.app.msgFly(title, _s('error-albaran-facturar-no-cliente'));
				return true;
			}
			// Muestra todos los albaranes por facturar
			var model = [{
				name : 'dCreacion'
			}, {
				name : 'nIdAlbaran'
			}, {
				name : 'cCUser'
			}, {
				name : 'dCreacion'
			}, {
				name : 'cEstado'
			}, {
				name : 'cRefCliente'
			}, {
				name : 'cRefInterna'
			}];

			var url = site_url("ventas/albaransalida/get_list");
			var store = Ext.app.createStore({
				model : model,
				url : url
			});

			var sm = new Ext.grid.CheckboxSelectionModel();

			var columns = [sm, {
				header : _s("Id"),
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : 'nIdAlbaran',
				sortable : true
			}, {
				header : _s("Estado"),
				dataIndex : 'cEstado',
				width : Ext.app.TAM_COLUMN_TEXT,
				sortable : true
			}, {
				header : _s("cRefCliente"),
				dataIndex : 'cRefCliente',
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

			var form = Ext.app.formStandarForm({
				controls : controls,
				autosize : false,
				labelWidth : 200,
				height : 500,
				width : 700,
				title : _s('Albaranes por facturar'),
				fn_ok : function() {
					var sel = grid.getSelectionModel().getSelections();
					var ids = [];
					Ext.each(sel, function(item) {
						ids.push(item.data.nIdAlbaran);
					});
					var fn = function(ok) {
						if(ok) {
							var v = ids.pop();
							if(v != null) {
								add_albaran(v, fn);
							}
						}
					}
					fn(true);
				}
			});

			store.baseParams = {
				where : 'nIdFactura=NULL&bNoFacturable=0&nIdCliente=' + cliente_id
			};

			store.load();
			form.show();
			return true;
		});

		lineas.control.addPattern("^" + Ext.app.TPV_ADD_PEDIDO_CLIENTE + "\\s?(\\d+)$", function(m, c) {
			var v = (m[1] != null) ? (m[1]) : 0;
			//ºole.log('Add pedido: ' + v);
			fn_docs_select_lineas_pedido(v, function(libros, copy) {
				var ct = 0;
				Ext.each(libros, function(r) {
					r.nIdLineaPedido = r.id;
					lineas.add(r);
					ct++;
				});
				if(ct > 0) {
					Ext.app.callRemote({
						url : site_url('ventas/pedidocliente/get/' + v),
						fnok : function(res) {
							if(copy) {
								refcli.setValue(res.value_data.cRefCliente);
								refint.setValue(res.value_data.cRefInterna);
							}
							fn_load_direcciones(res.value_data.nIdCliente, res.value_data.nIdDirFac);
							fn_load_cliente(res.value_data.nIdCliente);
						}
					});
				}
			});
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
			fieldLabel : _s('fTotal'),
			msgTarget : 'side',
			anchor : '-20',
			items : [total, pendiente, mpgrid, msg]
		};

		// Controles normales
		var controles_linea = lineas.linea;
		var ctldescuento = controles_linea.items[9];
		//console.dir(ctldescuento);
		if(descuento != 0 && ctldescuento != null)
			ctldescuento.setValue(descuento);
		var controls = [cliente, direccionenvio, refs, info, datas, controles_linea, lineas.grid, ejemplares, pie];

		// TABS
		documentosAddTabs(form, controls, allsecciones ? 'form-facturacion' : 'form-tpv');

		/*-------------------------------------------------------------------------
		 * Comandos
		 *-------------------------------------------------------------------------
		 */
		form.addCommand({
			text : _s('Cerrar venta'),
			iconCls : 'icon-generar-doc',
			handler : fn_cerrar,
			id : form.idform + 'btn_cerrar3'
		});

		form.addCommand(new Ext.Toolbar.SplitButton({
			text : _s('Ticket'),
			iconCls : 'icon-ticket',
			handler : function() {
					fn_ticket();
				},
			id : form.idform + 'btn_ticket',
			menu: [{
				text : _s('Imprimir ticket modo factura'),
				handler : function() {
					fn_ticket(false, Ext.app.get_config('bp.factura.ticket.factura'));
				},
				iconCls : 'icon-ticket'
			},{
				text : _s('Imprimir ticket modo regalo'),
				handler : function() {
					fn_ticket(false, Ext.app.get_config('bp.factura.ticket.regalo'));
				},
				iconCls : 'icon-ticket'
			}]
		}));

		if(!tpv) {
			var notas = Ext.app.formNotas();
			var grid_notas = notas.init({
				id : form_id + "_notas",
				url : site_url(base),
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
		}
		// Búsqueda
		var fn_open = function(id) {
			form.load(id);
			form.selectTab(0);
		}
		
		<?php 
		$modelo = $this->reg->get_data_model(array('nIdDireccion', 'nIdVendedor', 'bCobrado', 'nIdAbono', 'bMostrarWeb', 'tNotasExternas', 'tNotasInternas', 'fPortes', 'bTipoFactura'));
		?>
		var grid_search = <?php echo extjs_creategrid($modelo, $id.'_g_search', null, null, ((isset($tpv) && $tpv)?'ventas.tpv':'ventas.factura'), $this->reg->get_id(), null, FALSE, null, 
			'mode:"search", fn_open: fn_open');
		?>;
		form.addTab({
			title : _s('Búsqueda'),
			iconCls : 'icon-search',
			items : Ext.app.formSearchForm({
				grid : grid_search,
				audit : true,
				id_grid : form_id + '_g_search_grid'
			})
		});

		var fn_abrir = function() {
            Ext.app.callRemote({
                url: site_url(base + '/abrir/' + form.getId()),
                params: {
                    id: form.getId()
                },
                fnok : function()
                {
                	form.refresh();
                }
            });        
		}

		form.addAction({
			text : _s('Abrir'),
			handler : function() {
				fn_abrir();
			},
			iconCls : 'icon-generar-doc',
			id : form.idform + 'btn_abrir'
		});

		form.addAction('-');
		addButtonAbiertos(form)

		var ImprimirUltima = function(id, title) {
			if(id != null)
			{
				var r = Ext.app.get_config('bp.factura.ticket');
				r = (r !== null && r != '')?('/' + r):'';
				Ext.app.printTicket(site_url(base + '/ticket/' + id + r), title);
			}
		}

		form.addAction({
			text : _s('Imprimir ticket última venta'),
			handler : function() {
				ImprimirUltima(ultimo_id, ultimo_title);
			},
			iconCls : 'icon-ticket',
			id : form.idform + 'btn_ult_ticket'
		});

		form.addAction({
			text : _s('Abrir última venta'),
			handler : function() {
				form.load(ultimo_id);
			},
			iconCls : 'icon-ticket',
			id : form.idform + 'btn_open_ticket'
		});
		form.addAction('-');
		var fn_enviar = function() {
			documentosEnviar(form, _s('Enviar factura'), site_url(base + '/send'));
		}

		form.addAction({
			text : _s('Enviar'),
			handler : function() {
				fn_enviar();
			},
			iconCls : 'icon-send',
			id : form.idform + 'btn_enviar'
		});

		form.addAction('-');
		form.addAction(exentoiva);

		form.addAction('-');
		addButtonAbonar(form, base + '/abonar');
		form.addAction('-');
		addButtonNegativo(form, lineas);
		addButtonAjustarMargen(form, lineas);

		form.addTools(addButtonLiquidarStock(form, site_url(base + '/liquidarstock')));

		var fn_procesar = function() {
			Ext.app.callRemote({
				timeout: false,
				url : site_url(base + '/procesar/0')
			});
		}
		form.addTools('-');
        form.addTools({
            text: _s('Copiar referencia interna'),
            handler: function(){
                Ext.app.callRemote({
                    url: site_url(base + '/copiarrefinterna'),
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
                    url: site_url(base + '/copiarrefcliente'),
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
        form.addTools({
            text: _s('Copiar referencias albaranes'),
            handler: function(){
                Ext.app.callRemote({
                    url: site_url(base + '/ref'),
                    params: {
                        id: form.getId()
                    },
                    fnok: function(res){
                          form.refresh();
                    }
                });
            },
            iconCls: 'icon-copy',
            id: form.idform + 'btn_copiar3'
        });
		form.addTools('-');
        form.addTools({
            text: _s('Pedidos de cliente'),
            handler: function(){
	            Ext.app.callRemote({
	                url: site_url(base + '/pedidos'),
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
	                url: site_url(base + '/coste'),
	                timeout: false,
	                params: {
	                    id: form.getId()
	                }
	            });
            },
            iconCls: 'iconoConsultaPrecios',
            id: form.idform + 'btn_coste'
        });

		form.addAction('-');
		form.addAction({
			text : _s('Procesar facturas'),
			handler : function() {
				fn_procesar();
			},
			iconCls : 'icon-process',
			id : form.idform + 'btn_procesar'
		});
		form.addAction('-');
		form.addAction({
			text : _s('Ajustar pago'),
			handler : function() {
                if (data_load != null)
                {
                    Ext.app.callRemote({
                        url : site_url(base + '/ajustepago/'+ form.getId()),
	                    fnok: function(res) {
	                          form.refresh();
	                    }
                    });
                }
			},
			iconCls : 'icon-check',
			id : form.idform + 'btn_ajustar'
		});
		form.addAction({
			text : _s('Marcar como contabilizada'),
			handler : function() {
                if (data_load != null)
                {
                    Ext.app.callRemote({
                        url : site_url(base + '/contabilizar/'+ form.getId()),
	                    fnok: function(res) {
	                          form.refresh();
	                    }
                    });
                }
			},
			iconCls : 'icon-contabilizar',
			id : form.idform + 'btn_contabilizar'
		});
		form.addAction({
			text : _s('Marcar como NO contabilizada'),
			handler : function() {
                if (data_load != null)
                {
                    Ext.app.callRemote({
                        url : site_url(base + '/descontabilizar/'+ form.getId()),
	                    fnok: function(res) {
	                          form.refresh();
	                    }
                    });
                }
			},
			iconCls : 'icon-uncheck',
			id : form.idform + 'btn_descontabilizar'
		});
        form.addTools('-');
        form.addTools({
            text: _s('Enviar por courier'),
            handler: function(){
                sendCourier(site_url(base + '/courier'), form, total_tpv);
            },
            iconCls: 'iconoCourier',
            id: form.idform + 'btn_courier'
        });
        form.addTools({
            text: _s('Imprimir etiqueta de courier'),
            handler: function(){
                Ext.app.callRemote({
                    url: site_url('sys/codebar/etiqueta'),
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
                if (data_load != null && data_load.nIdDireccion != null)
                {
                    Ext.app.callRemote({
                        url : site_url('etiquetas/etiqueta/colacliente/' + data_load.nIdDireccion)
                    });
                }
            },
            iconCls : 'icon-label-cola',
            id : form.idform + 'btn_dir_fact_cola'
        });
        form.addTools({
            text : _s('print-dir-fact'),
            handler : function() {
                if (data_load != null && data_load.nIdDireccion != null)
                {
                    Ext.app.callRemote({
                        url : site_url('etiquetas/etiqueta/printcliente/' + data_load.nIdDireccion)
                    });
                }
            },
            iconCls : 'icon-label',
            id : form.idform + 'btn_dir_fact_print'
        });

		return form.show(open_id);
	} catch (e) {
		console.dir(e);
	}
})();
