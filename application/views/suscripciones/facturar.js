(function() {
	/*-------------------------------------------------------------------------
	 * Datos Formulario
	 *-------------------------------------------------------------------------
	 */
	var open_id = "<?php echo $open_id;?>";
	var form_id = "<?php echo $id;?>";
	var title = "<?php echo $title;?>";
	var icon = "<?php echo $icon;?>";

	if(title == '')
		title = _s('Facturar albaranes');
	if(icon == '')
		icon = 'iconoSuscripcionesFacturarAlbaranes';
	if(form_id == '')
		form_id = Ext.app.createId();

	// Modelo albaranes
	var model_albaranes = [{
		name : 'nIdAlbaran',
		type : 'int',
		column : {
			header : _s('Id'),
			width : Ext.app.TAM_COLUMN_ID,
			sortable : true
		}
	}, {
		name : 'nIdCliente'
	}, {
		name : 'fTotal',
		type : 'float'
	}, {
		name : 'cCliente'
	}, {
		name : 'dCreacion'
	}, {
		name : 'nIdLibro',
		type : 'int'
	}, {
		name : 'cTitulo'
	}, {
		name : 'nIdSuscripcion',
		type : 'int'
	}];

	// Store de pendientes de enviar
	var store_albaranes = Ext.app.createStore({
		model : model_albaranes,
		url : site_url('suscripciones/entradamercancia/albaranes')
	});

	var sm = new Ext.grid.CheckboxSelectionModel();

	var crearFacturas = function(button) {
		var serie = Ext.getCmp(series.id).getValue();
		if(serie == null || serie == '') {
			Ext.app.msgFly(title, _s('no-serie-seleccionada'));
			return;
		}
		var fecha = fechafactura.getValue();
		if(fecha == null || fecha == '') {
			fecha = DateToNumber((new Date).getTime());
		} else {
			fecha = DateToNumber(fecha.getTime());
		}

		var sel = grid_albaranes.getSelectionModel().getSelections();
		var codes = '';
		for(var i = 0; i < sel.length; i = i + 1) {
			codes += sel[i].data.nIdAlbaran + ';';
		}

		if(sel.length == 0) {
			Ext.app.msgFly(title, _s('no-items-marcados'));
			return;
		}

		Ext.MessageBox.show({
			msg : title,
			width : 300,
			wait : true,
			icon : 'ext-mb-download'
		});

		var fnok = function(obj) {
			Ext.MessageBox.hide();
			try {
				if(obj.success) {
					store_albaranes.load();
					if(obj.value_data) {
						Ext.each(obj.value_data, function(item) {
							var reg = {
								cliente : item.cliente,
								id : item.id,
								total : item.total,
								numero : item.numero
							}
							store_historico.insert(0, new ComboRecord(reg));
						});
						var tab = Ext.getCmp(form_id + '_tab');
						if(tab != null)
							tab.setActiveTab(1);
					}
				}
			} catch(e) {
				console.dir(e);
			}
		};
		var fnnok = function() {
			try {
				Ext.MessageBox.hide();
			} catch(e) {
				console.dir(e);
			}
		};
		var url = site_url('suscripciones/entradamercancia/facturar');

		Ext.app.callRemote({
			url : url,
			title : title,
			timeout : false,
			params : {
				ids : codes,
				serie : serie,
				fecha : fecha
			},
			fnok : fnok,
			fnnok : fnnok
		});

	}
	var series = Ext.app.combobox({
		url : site_url('ventas/serie/search'),
		//anchor: "90%",
		allowBlank : false,
		id : 'nIdSerie'
	});
	var fechafactura = new Ext.form.DateField({
		xtype : 'datefield',
		startDay : Ext.app.DATESTARTDAY,
		name : 'dFecha',
		value : new Date(),
		allowBlank : true
	});

	var tbar = [{
		xtype : 'displayfield',
		value : _s('Serie')
	}, '-', series, {
		xtype : 'displayfield',
		value : _s('Fecha')
	}, '-', fechafactura];
	tbar = tbar.concat(Ext.app.gridStandarButtons({
		title : title,
		id : form_id + "_grid_albaranes"
	}));
	// Grid albaranes
	var grid_albaranes = new Ext.grid.GridPanel({
		title : _s('Albaranes'),
		iconCls : 'icon-documents',
		region : 'center',
		autoExpandColumn : "descripcion",
		loadMask : true,
		stripeRows : true,
		store : store_albaranes,
		id : form_id + "_grid_albaranes",
		sm : sm,
		buttons : [{
			text : _s('Crear'),
			iconCls : 'icon-accept',
			handler : function(button) {
				crearFacturas(button);
			}
		}],

		tbar : tbar,
		bbar : new Ext.PagingToolbar({
			store : store_albaranes,
			pageSize : 1000000,
			displayInfo : true,
			displayMsg : _s('grid_desplay_result'),
			emptyMsg : _s('grid_desplay_no_topics')
		}),
		columns : [sm, {
			header : _s('nIdAlbaran'),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'nIdAlbaran',
			sortable : true
		}, {
			header : _s("fImporte"),
			dataIndex : 'fTotal',
			renderer : Ext.app.rendererPVP,
			width : Ext.app.TAM_COLUMN_NUMBER,
			align : 'right',
			sortable : true
		}, {
			header : _s('Cliente'),
			width : Ext.app.TAM_COLUMN_TEXT,
			id : 'descripcion',
			dataIndex : 'cCliente',
			sortable : true
		}, {
			header : _s('dFecha'),
			width : Ext.app.TAM_COLUMN_DATE,
			dateFormat : 'timestamp',
			renderer : Ext.app.renderDate,
			dataIndex : 'dCreacion',
			sortable : true
		}, {
			header : _s('cTitulo'),
			width : Ext.app.TAM_COLUMN_TEXT * 2,
			dataIndex : 'cTitulo',
			sortable : true
		}, {
			header : _s('nIdSuscripcion'),
			width : Ext.app.TAM_COLUMN_ID,
			dataIndex : 'nIdSuscripcion',
			sortable : true
		}]
	});

	var cm_albaranes = fn_contextmenu();
	var contextmenu = Ext.app.addContextMenuLibro(grid_albaranes, 'nIdLibro', cm_albaranes);
	//Ext.app.addContextMenuEmpty(grid_albaranes, cm_albaranes);
	contextmenu.add({
		text : _s('Ver albarán'),
		handler : function() {
			var record = cm_albaranes.getItemSelect();
			if(record != null) {
				Ext.app.execCmd({
					url : site_url('ventas/albaransalida/index/' + record.data.nIdAlbaran)
				});
			}
		},
		iconCls : 'iconoAlbaranSalida'
	});
	contextmenu.add({
		text : _s('Ver cliente'),
		handler : function() {
			var record = cm_albaranes.getItemSelect();
			if(record != null) {
				Ext.app.execCmd({
					url : site_url('clientes/cliente/index/' + record.data.nIdCliente)
				});
			}
		},
		iconCls : 'iconoClientes'
	});
	contextmenu.add({
		text : _s('Ver suscripción'),
		handler : function() {
			var record = cm_albaranes.getItemSelect();
			if(record != null) {
				Ext.app.execCmd({
					url : site_url('suscripciones/suscripcion/index/' + record.data.nIdSuscripcion)
				});
			}
		},
		iconCls : 'iconoSuscripciones'
	});

	// Modelo histórico
	var model_historico = [{
		name : 'cliente'
	}, {
		name : 'numero'
	}, {
		name : 'id'
	}, {
		name : 'total'
	}];

	// Store histórico
	var store_historico = new Ext.data.ArrayStore({
		fields : model_historico
	});

	var sm2 = new Ext.grid.CheckboxSelectionModel();

	// Grid histórico
	var grid_historico = new Ext.grid.GridPanel({
		title : _s('Histórico'),
		iconCls : 'icon-history',
		region : 'center',
		autoExpandColumn : "descripcion",
		loadMask : true,
		stripeRows : true,
		store : store_historico,
		sm : sm2,
		id : form_id + "_grid_historico",
		tbar : Ext.app.gridStandarButtons({
			title : title,
			id : form_id + "_grid_historico"
		}),

		columns : [sm2, {
			header : _s('cDescripcion'),
			width : Ext.app.TAM_COLUMN_TEXT,
			id : 'descripcion',
			dataIndex : 'cliente',
			sortable : true
		}, {
			header : _s('nIdFactura'),
			width : Ext.app.TAM_COLUMN_MONEY,
			dataIndex : 'numero',
			sortable : true
		}, {
			header : _s('Importe'),
			width : Ext.app.TAM_COLUMN_MONEY,
			align : 'right',
			renderer : Ext.app.rendererPVP,
			dataIndex : 'total',
			sortable : true
		}]
	});

	grid_historico.on('dblclick', function(view, index) {
		var sm = grid_historico.getSelectionModel();
		if(sm.hasSelection()) {
			var sel = sm.getSelected();
			Ext.app.callRemote({
				url : site_url('ventas/factura/index/' + sel.data.id)
			});
		}
	});
	var cm_lineas = fn_contextmenu();
	var contextmenu = Ext.app.addContextMenuEmpty(grid_historico, cm_lineas);
	contextmenu.add({
		text : _s('ver-factura'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			if((record != null) && (record.data.id != null)) {
				Ext.app.execCmd({
					url : site_url('ventas/factura/index/' + record.data.id)
				});
			}
		},
		iconCls : 'iconoFacturacion'
	});
	contextmenu.add('-');
	contextmenu.add({
		text : _s('Imprimir'),
		handler : function() {
			var sel = grid_historico.getSelectionModel().getSelections();
			var count = 0;
			for(var i = 0; i < sel.length; i = i + 1) {
				count++;
				if(sel[i].data.id != null) {
					Ext.app.callRemote({
						url : site_url('ventas/factura/printer/' + sel[i].data.id),
						params : {
							title : _s('Factura') + ' ' + sel[i].data.numero.trim()
						}
					});
				}
			}
			if(count == 0) {
				var record = cm_lineas.getItemSelect();
				if((record != null) && (record.data.id != null)) {
					Ext.app.callRemote({
						url : site_url('ventas/factura/printer/' + record.data.id),
						params : {
							title : _s('Factura') + ' ' + record.data.numero.trim()
						}
					});
				}
			}
		},
		iconCls : 'icon-print'
	});
	contextmenu.add({
		text : _s('Enviar'),
		handler : function() {
			var sel = grid_historico.getSelectionModel().getSelections();
			var count = 0;
			for(var i = 0; i < sel.length; i = i + 1) {
				count++;
				if(sel[i].data.id != null) {
					Ext.app.callRemote({
						url : site_url('ventas/factura/send/' + sel[i].data.id),
						fnok : function(res) {
							var reg = {
								cliente : res.dialog
							}
							store_historico.insert(0, new ComboRecord(reg));
							store_albaranes.load();
						}
					});
				}
			}
			if(count == 0) {
				var record = cm_lineas.getItemSelect();
				if((record != null) && (record.data.id != null)) {
					Ext.app.callRemote({
						url : site_url('ventas/factura/send/' + record.data.id),
						fnok : function(res) {
							var reg = {
								cliente : res.dialog
							}
							store_historico.insert(0, new ComboRecord(reg));
							store_albaranes.load();
						}
					});
				}
			}
		},
		iconCls : 'icon-send'
	});
	contextmenu.add('-');
	contextmenu.add({
		text : _s('Deshacer'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			if((record != null) && (record.data.id != null)) {
				Ext.app.callRemote({
					url : site_url('suscripciones/entradamercancia/desfacturar/' + record.data.id),
					fnok : function(res) {
						var reg = {
							cliente : res.message
						}
						store_historico.insert(0, new ComboRecord(reg));
						store_albaranes.load();
					}
				});
			}
		},
		iconCls : 'icon-undo'
	});

	/**
	 * Formulario
	 */
	var form = {
		title : title,
		id : form_id,
		region : 'center',
		closable : true,
		iconCls : icon,
		layout : 'border',
		items : [new Ext.TabPanel({
			xtype : 'tabpanel',
			region : 'center',
			id : form_id + '_tab',
			activeTab : 0,
			items : [grid_albaranes, grid_historico]
		})],
		tbar : [/*{
		 xtype : 'tbbutton',
		 text : _s('Acciones'),
		 iconCls : 'icon-actions',
		 menu : [{
		 text : _s('Consultar modos de envío'),
		 iconCls : 'icon-tool',
		 id : form_id + '_btn_consultar'
		 }]
		 }, '-', */
		{
			tooltip : _s('cmd-calcular'),
			text : _s('Actualizar'),
			iconCls : 'icon-refresh',
			handler : function() {
				store_albaranes.load();
			}
		}]
	};
	store_albaranes.baseParams = {
		sort : 'cCliente'
	};
	store_albaranes.load();
	series.store.load({
		callback : function() {
			Ext.getCmp(series.id).setValue(parseInt("<?php echo $this->config->item('oltp.suscripciones.serienormal');?>"))
		}
	});
	return form;
})();
