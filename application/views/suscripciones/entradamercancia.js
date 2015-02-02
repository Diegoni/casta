(function() {
	try {

		var test_id = Ext.app.createId();

		var model = [{
			name : 'nIdTipoTarifa'
		}, {
			name : 'cDescripcion'
		}, {
			name : 'fMargen'
		}, {
			name : 'fMargenDivisa'
		}, {
			name : 'fMargenOriginal'
		}, {
			name : 'fCoste'
		}, {
			name : 'fOriginal'
		}, {
			name : 'fGastos'
		}, {
			name : 'fPrecio'
		}, {
			name : 'fIVA'
		}, {
			name : 'fPVP'
		}];

		var url = site_url('suscripciones/entradamercancia/get_precios');
		var store = Ext.app.createStore({
			model : model,
			url : url
		});

		var precioEditor = new Ext.form.NumberField({
			allowNegative : false,
			allowDecimals : true,
			selectOnFocus : true
		});

		var columns = [{
			id : 'descripcion',
			header : _s("cTipoTarifa"),
			dataIndex : 'cDescripcion',
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}, {
			header : _s("fOriginal"),
			dataIndex : 'fOriginal',
			renderer : Ext.app.rendererPVP,
			width : Ext.app.TAM_COLUMN_NUMBER,
			align : 'right',
			sortable : true
		}, {
			header : _s("fMargenOriginal"),
			dataIndex : 'fMargenOriginal',
			width : Ext.app.TAM_COLUMN_NUMBER,
			align : 'right',
			sortable : true
		}, {
			header : _s("fMargenDivisa"),
			dataIndex : 'fMargenDivisa',
			width : Ext.app.TAM_COLUMN_NUMBER,
			align : 'right',
			sortable : true
		}, {
			header : _s("fMargen"),
			dataIndex : 'fMargen',
			width : Ext.app.TAM_COLUMN_NUMBER,
			align : 'right',
			sortable : true
		}, {
			header : _s("fCoste"),
			dataIndex : 'fCoste',
			width : Ext.app.TAM_COLUMN_NUMBER,
			align : 'right',
			renderer : Ext.app.rendererPVP,
			sortable : true
		}, {
			header : _s("fGastos"),
			dataIndex : 'fGastos',
			width : Ext.app.TAM_COLUMN_NUMBER,
			renderer : Ext.app.rendererPVP,
			align : 'right',
			sortable : true
		}, {
			header : _s("fPrecio"),
			dataIndex : 'fPrecio',
			renderer : Ext.app.rendererPVP,
			align : 'right',
			width : Ext.app.TAM_COLUMN_NUMBER,
			sortable : true
		}, {
			header : _s("fIVA"),
			dataIndex : 'fIVA',
			align : 'right',
			width : Ext.app.TAM_COLUMN_NUMBER,
			sortable : true
		}, {
			header : _s("fPVP"),
			dataIndex : 'fPVP',
			renderer : Ext.app.rendererPVP,
			align : 'right',
			width : Ext.app.TAM_COLUMN_NUMBER,
			sortable : true
		}];

		var grid = new Ext.grid.EditorGridPanel({
			store : store,
			height : 100,
			anchor : '100%',
			autoExpandColumn : 'descripcion',
			stripeRows : true,
			loadMask : true,
			hideLabel : true,
			viewConfig : {
				enableRowBody : true,
				forceFit : true,
				getRowClass : function(r, rowIndex, rowParams, store) {
					if(r.data.nIdTipoTarifa == datos_pedido.tarifa 
						&& (datos_pedido.presupuesto == null || datos_pedido.presupuesto.fPVP == null)) {
							pvp.setValue(r.data.fPVP);
					}
					return (r.data.nIdTipoTarifa == datos_pedido.tarifa) ? 'cell-repo-stock' : '';
				}
			},

			listeners : {
				celldblclick : function(grid, row, column, e) {
					var record = grid.store.getAt(row);
					pvp.setValue(record.data.fPVP);
				}
			},

			// grid columns
			columns : columns
		});

		var resetData = function() {
			form.getForm().reset();
			grid.store.removeAll();
			mpgrid.getStore().removeAll();
			print_panel(_s('hlp-entradamercancia-select'));
			datos_pedido = null;
		}
		var crearEntrada = function(button) {
			if(form.getForm().isValid()) {
				if(datos_pedido == null) {
					Ext.app.msgFly(title, _s('no-suscripcion-select'));
					return;
				}
				var gastos = '';
				mpgrid.getStore().each(function(i) {
					gastos += i.data.nIdTipoCargo + '_' + i.data.fImporte + ';';
				});
				var dirprv = (datos_pedido.direccion != null) ? datos_pedido.direccion.nIdDireccion : null;
				var datos = {
					camara : importecamara.getValue(),
					cantidad : cantidad.getValue(),
					precio : preciocompra.getValue(),
					direccion : dirprv,
					dto : descuento.getValue(),
					divisa : divisas.getValue(),
					cambio : cambio.getValue(),
					gastos : gastos,
					art : datos_pedido.nIdLibro,
					proveedor : datos_pedido.nIdProveedor,
					sus : datos_pedido.nIdSuscripcion,
					numero : numero.getValue(),
					volumen : volumen.getValue(),
					fecha : DateToNumber(fecha.getValue().getTime()),
					iva : datos_pedido.articulo.fIVA,
					pvp : pvp.getValue(),
					factura : factura.getValue(),
					pedido : datos_pedido.nIdPedido,
					dtocl : descuentocliente.getValue(),
					pais : (datos_pedido.direccion != null) ? datos_pedido.direccion.nIdPais : null
				}

				button.disable();
				var url = site_url('suscripciones/entradamercancia/crear');
				Ext.app.callRemote({
					url : url,
					params : datos,
					fnok : function(res) {
						var reg = {
							titulo : res.dialog,
							pvp : datos.pvp,
							dtoprv : datos.dto,
							prprv : datos.precio,
							cantidad : datos.cantidad,
							dtocl : datos.dtocl,
							entrada : res.entrada,
							salida : res.salida,
							suscripcion : res.salida
						}
						store_historico.insert(0, new ComboRecord(reg));
						resetData();
						button.enable();
					},
					fnnok : function() {
						button.enable();
					}
				});
			}

		}
		/*-------------------------------------------------------------------------
		 * Datos Formulario
		 *-------------------------------------------------------------------------
		 */
		var open_id = "<?php echo $open_id;?>";
		var form_id = "<?php echo $id;?>";
		var title = "<?php echo $title;?>";
		var icon = "<?php echo $icon;?>";

		if(title == '')
			title = _s('Entrada de mercancía');
		if(icon == '')
			icon = 'iconoSuscripcionesEntradaMercanciaTab';
		if(form_id == '')
			form_id = Ext.app.createId();

		/**
		 * Función de carga de los datos
		 */
		var print_panel = function(text) {
			var detailEl = Ext.getCmp(form_id + "details-panel").body;
			detailEl.applyStyles({
				'background-color' : '#FFFFFF'
			});
			detailEl.update(text);
		}
		var datos_pedido = null;
		var fnselect = function(id) {
			try {
				Ext.app.callRemote({
					url : site_url('suscripciones/entradamercancia/get_datos'),
					params : {
						id : id
					},
					timeout : false,
					nomsg : true,
					title : this.title,
					fnok : function(obj) {
						if(obj.success) {
							print_panel(obj.message);
							factura.setValue(obj.data.bNoFacturable == false);
							(obj.data.bNoFacturable == true)?factura.disable():factura.enable();
							datos_pedido = obj.data;
							iva.setValue(obj.data.articulo.fIVA);
							calcularPrecios();
							if (obj.data.presupuesto != null && obj.data.presupuesto.fDescuento != null)
								descuentocliente.setValue(obj.data.presupuesto.fDescuento);
							if (obj.data.presupuesto != null && obj.data.presupuesto.fPVP != null)
								pvp.setValue(obj.data.presupuesto.fPVP);
						} else {
							Ext.app.msgError(title, _s('registro_error') + ': ' + obj.message);
						}
					}
				});
			} catch (e) {
				console.dir(e);
			}
		}
		var origen = new Ext.form.ComboBox(Ext.app.autocomplete({
			url : site_url('suscripciones/entradamercancia/pedidos'),
			label : _s('Pedido'),
			anchor : '100%',
			fnselect : fnselect
		}));

		var region = {
			xtype : 'panel',
			height : 150,
			anchor : '100%',
			hideLabel : true,
			bodyStyle : 'padding-bottom:15px;background:#FFFFFF;',
			autoScroll : true,
			cls : 'details-panel',
			html : _s('hlp-entradamercancia-select'),
			id : form_id + "details-panel",
		}

		var fecha = new Ext.form.DateField({
			xtype : 'datefield',
			startDay : Ext.app.DATESTARTDAY,
			value : new Date(),
			name : 'dFecha',
			allowBlank : false
		});

		var calcularPrecios = function() {
			var gastos = '';
			var g = 0;
			mpgrid.getStore().each(function(i) {
				gastos += i.data.fImporte + ';';
				g += i.data.fImporte;
			});
			var imp = g + (cantidad.getValue() * preciocompra.getValue() * (1 - descuento.getValue() / 100));
			importecamara.setValue(imp);

			if(datos_pedido == null) {
				//Ext.app.msgFly(title, _s('no-suscripcion-select'));
				return;
			}

			store.baseParams = {
				precio : preciocompra.getValue(),
				dto : descuento.getValue(),
				divisa : divisas.getValue(),
				cambio : cambio.getValue(),
				pais : (datos_pedido.direccion != null) ? datos_pedido.direccion.nIdPais : null,
				iva : datos_pedido.articulo.fIVA,
				tipo : datos_pedido.articulo.nIdTipo,
				cantidad : cantidad.getValue(),
				gastos : gastos
			}
			store.load();
		}
		var preciocompra = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fPrecioCompra',
			value : 0,
			width : 70,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			selectOnFocus : true,
			listeners : {
				change : function(me, e) {
					calcularPrecios();
				}
			}
		});

		var importecamara = new Ext.form.NumberField({
			xtype : 'numberfield',
			value : 0,
			width : 70,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			selectOnFocus : true,
		});

		var iva = new Ext.form.TextField({
			width : 50,
			readOnly : true
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
			width : 70,
			selectOnFocus : true,
			listeners : {
				change : function(me, e) {
					calcularPrecios();
				}
			}
		});
		var descuento = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fDescuento',
			value : 0,
			width : 50,
			maxValue : 100,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			selectOnFocus : true,
			listeners : {
				change : function(me, e) {
					calcularPrecios();
				}
			}
		});

		var simbolo = '';
		divisas.on('select', function(a, b) {
			simbolo = b.data.cSimbolo;
			cambio.setValue(b.data.fCompra);
			calcularPrecios();
		});
		var cargos = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('compras/tipocargo/search'),
			name : 'nIdTipoCargo'
		}));

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
					selectOnFocus : true
				}),
				renderer : Ext.app.numberFormatter
			}],
			listeners : {
				afteredit : function(e) {
					calcularPrecios();
				}
			},

			sm : mpitemDeleter
		});
		
		Ext.app.addDeleteEvent(mpgrid);
		mpstore.on('remove', function(s, r, i) {
			calcularPrecios();
		});

		var add_mp = function(id, descripcion, importe) {
			var f = mpstore.find('cDescripcion', descripcion);
			importe = str_to_float(importe).decimal(Ext.app.DECIMALS);
			if((f >= 0)) {
				var r = mpstore.getAt(f);
				r.set('fImporte', r.data.fImporte + importe);
			} else {
				mpstore.add(new mprt({
					'nIdTipoCargo' : id,
					'cDescripcion' : descripcion,
					'fImporte' : parseFloat(importe),
				}));
			}
			calcularPrecios();
			return true;
		}
		var importecargo = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fCargo',
			value : 0,
			width : 50,
			allowNegative : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			selectOnFocus : true,
			enableKeyEvents : true
		});

		importecargo.on('keypress', function(t, e) {
			if(e.getKey() === e.ENTER) {
				// El id
				var id = cargos.getValue();
				var text = cargos.getRawValue();
				add_mp(id, text, importecargo.getValue());
				cargos.reset();
				importecargo.setValue(0);
			}
		});
		var cantidad = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fCantidad',
			value : 1,
			width : 50,
			allowNegative : false,
			minValue : 1,
			allowBlank : false,
			allowDecimals : false,
			selectOnFocus : true
		});
		var pie = {
			xtype : 'compositefield',
			fieldLabel : _s('Tipo'),
			msgTarget : 'side',
			items : [cargos, {
				xtype : 'displayfield',
				value : _s('fImporte')
			}, importecargo, mpgrid]
		};
		var pvp = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fPrecioCompra',
			value : 0,
			width : 50,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			selectOnFocus : true,
			listeners : {
				change : function(me, e) {
				}
			}
		});
		var numero = new Ext.form.TextField({
			xtype : 'textfield',
			name : 'cNumero',
			allowBlank : false
		});
		var volumen = new Ext.form.TextField({
			xtype : 'textfield',
			name : 'cVolumen'
		});
		var descuentocliente = new Ext.form.NumberField({
			xtype : 'numberfield',
			name : 'fDescuento',
			value : 0,
			width : 50,
			maxValue : 100,
			allowNegative : false,
			allowBlank : false,
			allowDecimals : true,
			decimalPrecision : Ext.app.DECIMALS,
			selectOnFocus : true
		});
		var factura = new Ext.form.Checkbox({
			checked : true
		});
		var controls = [origen, region, {
			xtype : 'fieldset',
			fieldLabel : _s('fCompra'),
			items : [{
				xtype : 'compositefield',
				fieldLabel : _s('Número'),
				items : [numero, {
					xtype : 'displayfield',
					value : _s('Fecha')
				}, fecha, {
					xtype : 'displayfield',
					value : _s('nIdDivisa')
				}, divisas, {
					xtype : 'displayfield',
					value : _s('fPrecioCambio')
				}, cambio]
			}, {
				xtype : 'compositefield',
				fieldLabel : _s('Año/Vol'),
				items : [volumen, {
					xtype : 'displayfield',
					value : _s('nCantidad')
				}, cantidad, {
					xtype : 'displayfield',
					value : _s('fPrecio')
				}, preciocompra, {
					xtype : 'displayfield',
					value : _s('fDescuento')
				}, descuento, {
					xtype : 'displayfield',
					value : _s('Cámara')
				}, importecamara]
			}]
		}, {
			xtype : 'fieldset',
			fieldLabel : _s('Cargos'),
			items : pie
		}, {
			xtype : 'fieldset',
			fieldLabel : _s('Venta'),
			items : [grid, {
				xtype : 'compositefield',
				fieldLabel : _s('fPVP'),
				items : [pvp, {
					xtype : 'displayfield',
					value : _s('fDescuento')
				}, descuentocliente, {
					xtype : 'displayfield',
					value : _s('fIVA')
				}, iva, {
					xtype : 'displayfield',
					value : _s('Facturar')
				}, factura
				/*, {
				 xtype : 'displayfield',
				 value : _s('fMargen')
				 }, new Ext.form.NumberField({
				 xtype : 'numberfield',
				 name : 'fMargen',
				 value : 0,
				 width : 50,
				 maxValue : 100,
				 allowNegative : false,
				 allowBlank : false,
				 allowDecimals : true,
				 decimalPrecision : Ext.app.DECIMALS,
				 selectOnFocus : true
				 })*/]
			}]
		}];

		var fn_deshacer = function() {
			var controls = [{
				fieldLabel : _s('Albarán Entrada'),
				name : 'entrada',
				allowBlank : false,
				xtype : "numberfield"
			}, {
				fieldLabel : _s('Albarán Salida'),
				name : 'salida',
				allowBlank : false,
				xtype : "numberfield"
			}];
			var url = site_url('suscripciones/entradamercancia/deshacer');

			var form = Ext.app.formStandarForm({
				icon : 'icon-undo',
				controls : controls,
				timeout : false,
				title : _s('Deshacer'),
				url : url
			});

			form.show();

		}
		var tbar = [{
			text : _s('Deshacer'),
			iconCls : 'icon-undo',
			handler : fn_deshacer
		}];

		/**
		 * Formulario
		 */
		var form = new Ext.FormPanel({
			baseCls : 'x-plain',
			frame : true,
			title : _s('General'),
			iconCls : 'icon-general',
			labelWidth : Ext.app.LABEL_SIZE,
			items : controls,
			bodyStyle : 'padding:5px 5px 0',
			defaultType : 'textfield',
			closable : true,
			buttonAlign : 'left',
			tbar : tbar,
			buttons : [{
				text : _s('Borrar'),
				iconCls : 'icon-clean',
				handler : function() {
					resetData();
				}
			}, '->', {
				text : _s('Crear'),
				iconCls : 'icon-accept',
				handler : function(button) {
					crearEntrada(button);
				}
			}]
		});

		divisas.store.load();
		cargos.store.load();

		var model_historico = [{
			name : 'titulo'
		}, {
			name : 'cantidad'
		}, {
			name : 'prprv'
		}, {
			name : 'dtoprv'
		}, {
			name : 'pvp'
		}, {
			name : 'dtocl'
		}, {
			name : 'entrada'
		}, {
			name : 'salida'
		}, {
			name : 'suscripcion'
		}];

		var store_historico = new Ext.data.ArrayStore({
			fields : model_historico
		});

		var grid_historico = new Ext.grid.GridPanel({
			title : _s('Histórico'),
			iconCls : 'icon-history',
			region : 'center',
			autoExpandColumn : "descripcion",
			loadMask : true,
			stripeRows : true,
			store : store_historico,
			id : id + "_grid_historico",
			tbar : Ext.app.gridStandarButtons({
				title : title,
				id : id + "_grid_historico"
			}),

			columns : [{
				header : _s('cDescripcion'),
				width : Ext.app.TAM_COLUMN_TEXT,
				id : 'descripcion',
				dataIndex : 'titulo',
				sortable : true
			}, {
				header : _s('Cantidad'),
				width : Ext.app.TAM_COLUMN_MONEY,
				dataIndex : 'cantidad',
				sortable : true
			}, {
				header : _s('Pr.Prv'),
				width : Ext.app.TAM_COLUMN_MONEY,
				align : 'right',
				renderer : Ext.app.rendererPVP,
				dataIndex : 'prprv',
				sortable : true
			}, {
				header : _s('Dto.Prv'),
				width : Ext.app.TAM_COLUMN_MONEY,
				dataIndex : 'dtoprv',
				sortable : true
			}, {
				header : _s('fPVP'),
				width : Ext.app.TAM_COLUMN_MONEY,
				align : 'right',
				dataIndex : 'pvp',
				renderer : Ext.app.rendererPVP,
				sortable : true
			}, {
				header : _s('Dto.Cl'),
				width : Ext.app.TAM_COLUMN_MONEY,
				dataIndex : 'dtocl',
				sortable : true
			}]
		});

		var cm_lineas = fn_contextmenu();
		var contextmenu = Ext.app.addContextMenuEmpty(grid_historico, cm_lineas);
		contextmenu.add({
			text : _s('Deshacer'),
			handler : function() {
				var record = cm_lineas.getItemSelect();
				if((record != null) && (record.data.entrada != null) && (record.data.salida != null) && (record.data.suscripcion != null)) {
					Ext.app.callRemote({
						url : site_url('suscripciones/entradamercancia/deshacer'),
						params : {
							entrada : record.data.entrada,
							salida : record.data.salida,
							suscripcion : record.data.suscripcion
						},
						fnok : function(res) {
							var reg = {
								titulo : res.message
							}
							store_historico.insert(0, new ComboRecord(reg));
						}
					});
				}
			},
			iconCls : 'icon-undo'
		});

		var tabpanel = {
			xtype : 'tabpanel',
			region : 'center',
			activeTab : 0,
			baseCls : 'x-plain',
			items : [form, grid_historico]
		};

		var panel = new Ext.Panel({
			layout : 'border',
			title : title,
			id : form_id,
			iconCls : icon,
			region : 'center',
			closable : true,
			items : [tabpanel]
		});

		return panel;
	} catch (e) {
		console.dir(e);
	}
})();
