(function() {

	var open_id = "<?php echo $open_id;?>";
	var form_id = "<?php echo $id;?>";
	var title = "<?php echo $title;?>";
	var icon = "<?php echo $icon;?>";
	if(title == '')
		title = _s('Pedidos pendientes de cerrar por línea');
	if(icon == '')
		icon = 'iconoPendientesCerrarLineaTab';
	if(form_id == '')
		form_id = Ext.app.createId();

	var model = [{
		name : 'id',
		type : 'int',
		column : {
			header : _s('Id'),
			width : Ext.app.TAM_COLUMN_ID,
			sortable : true,
			hidden : true
		}
	}, {
		name : 'nIdLinea'
	}, {
		name : 'nIdProveedor'
	}, {
		name : 'nIdCliente'
	}, {
		name : 'nIdSuscripcion'
	}, {
		name : 'nIdLibro'
	}, {
		name : 'cSeccion',
		column : {
			header : _s('cSeccion'),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}
	}, {
		name : 'nIdPedido',
		type : 'int',
		column : {
			header : _s('nIdPedido'),
			width : Ext.app.TAM_COLUMN_ID,
			sortable : true
		}
	}, {
		name : 'cProveedor',
		column : {
			header : _s('Proveedor'),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}
	}, {
		name : 'cCliente',
		column : {
			header : _s('Cliente'),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}
	}, {
		name : 'cISBN',
		column : {
			header : _s('cISBN'),
			width : Ext.app.TAM_COLUMN_ISBN,
			hidden: true,
			sortable : true
		}
	}, {
		name : 'cTitulo',
		column : {
			header : _s('cTitulo'),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}
	}, {
		name : 'cEditorial',
		column : {
			header : _s('Editorial'),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}
	}, {
		name : 'nCantidad',
		type : 'int',
		column : {
			header : _s('Pedidas'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			sortable : true
		}
	}, {
		name : 'nPendientes',
		type : 'int',
		column : {
			header : _s('Pendientes'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			sortable : true
		}
	}, {
		name : 'nServir',
		type : 'int',
		column : {
			header : _s('Servir'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			sortable : true
		}
	}, {
		name : 'nStock',
		type : 'int',
		column : {
			header : _s('Stock'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			sortable : true
		}
	}, {
		name : 'dCreacion',
		column : {
			header : _s('dCreacion'),
			width : Ext.app.TAM_COLUMN_DATE,
			renderer : Ext.app.renderDateShort,
			sortable : true
		}
	}, {
		name : 'nDias2',
		column : {
			header : _s('Días'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			sortable : true
		}
	}, {
		name : 'cRefInterna',
		column : {
			header : _s('cRefInterna'),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}
	}];

	var art = new Ext.form.ComboBox(Ext.app.autocomplete({
		allowBlank : false,
		url : site_url('catalogo/articulo/search'),
		label : _s('Artículo'),
		anchor : '100%',
	}));

	var seccion = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('generico/seccion/search'),
		id : form_id + '_sec'
	}));

	var proveedores = new Ext.form.ComboBox(Ext.app.autocomplete2({
		url : site_url('proveedores/proveedor/search'),
		name : form_id + '_prv',
		anchor : '100%',
		fieldLabel : _s('Proveedor')
	}));

	var reload = function(ids, idpv, idl, cpp) {
		if(ids == null)
			ids = Ext.app.getIdCombo(seccion);
		if(idl == null)
			idl = Ext.app.getIdCombo(art);
		if(idpv == null)
			idpv = Ext.app.getIdCombo(proveedores);
		var pp = Ext.getCmp(form_id + "_pp").getValue();
		if(pp == true)
			pp = 1;
		var grid = Ext.getCmp(form_id + '_grid');
		grid.store.baseParams = {
			start : 0,
			limit : Ext.app.PAGESIZE,
			ids : ids,
			idl : idl,
			idpv : idpv,
			pp : pp,
			sort : 'cProveedor'
		};
		grid.store.load();
	}
	var stores = [{
		store : seccion.store
	}];

	var accion = function(url, id, fn) {
		var grid = Ext.getCmp(form_id + '_grid');
		if (id == null) id = 'nIdPedido';
		var codes = Ext.app.gridGetChecked(grid, id);
		if(codes == null) {
			var record = cm_lineas.getItemSelect();
			if(record != null) {
				codes = record.data.nIdLinea + ';';
			}
		}
		if(codes == null) {
			Ext.app.msgFly(title, _s('no-libros-marcados'));
			return;
		}
		//console.log('Codes: ' + codes);
		grid.getEl().mask();
		Ext.app.callRemote({
			url : url,
			timeout : false,
			wait : true,
			params : {
				id : codes
			},
			fnok : function(res) {
				grid.getEl().unmask();
				grid.store.load({
							callback: function() {
								if (fn)
									fn(res)
								}
							});
				grid.getSelectionModel().deselectRange(0, grid.store.getTotalCount());
			},
			fnnok : function() {
				grid.getEl().unmask();
			}
		});
	}
	var tbar = [{
		xtype : 'label',
		html : _s('Sección')
	}, seccion, '-', {
		xtype : 'label',
		html : _s('Proveedor')
	}, proveedores, '-', {
		xtype : 'label',
		html : _s('Artículo')
	}, art, '-', {
		xtype : 'label',
		html : _s('Con Ped. Cli.')
	}, {
		id : form_id + "_pp",
		xtype : "checkbox"
	}, '-', {
		tooltip : _s('cmd-limpiar'),
		//text : _s('Limpiar'),
		iconCls : 'icon-clean',
		handler : function() {
			art.reset();
			seccion.reset();
			proveedores.reset();
			//reload();
		}
	}, '-', {
		tooltip : _s('cmd-actualizar'),
		//text : _s('Actualizar'),
		iconCls : 'icon-actualizar',
		handler : function() {
			reload();
		}
	}];

	var cerrarenviar = function(send, fn) {
		var grid = Ext.getCmp(form_id + '_grid');
		var codes = '';
		var sel = grid.getSelectionModel().getSelections();
		if(sel.length == 0)
			return null;
		var esta = new Array();
		for(var i = 0; i < sel.length; i = i + 1) {
			if (esta[sel[i].data.nIdPedido] !== true)
			{
				codes += sel[i].data.nIdPedido + ';';
				esta[sel[i].data.nIdPedido] = true;
			}
		}
		if(codes == null) {
			Ext.app.msgFly(title, _s('no-pedidos-marcados'));
			return;
		}
		//console.log('Codes: '+ codes);
		var url = site_url('compras/pedidoproveedor/cerrar');
		grid.getEl().mask();
		Ext.app.callRemote({
			url : url,
			timeout : false,
			wait : true,
			params : {
				id : codes
			},
			fnok : function() {
				var url = site_url(send);
				Ext.app.callRemote({
					url : url,
					timeout : false,
					wait : true,
					params : {
						id : codes
					},
					fnok : function(res) {
						grid.getEl().unmask();
						grid.store.load({
							callback: function() {
								if (fn)
									fn(res)
								}
							});
						grid.getSelectionModel().deselectRange(0, grid.store.getTotalCount());
					},
					fnnok : function() {
						grid.getEl().unmask();
						grid.store.load();
					}
				});
			},
			fnnok : function() {
				grid.getEl().unmask();
				grid.store.load();
			}
		});
	}
	var panel = Ext.app.createFormGrid({
		model : model,
		checkbox : true,
		show_filter : false,
		timeout: false,		
		id : form_id,
		title : title,
		icon : icon,
		idfield : 'id',
		urlget : site_url('compras/pedidoproveedor/get_pendientecerrarlinea'),
		loadstores : stores,
		fn_pre : null,
		fn_add : null,
		tbar : tbar,
		load : false,
		viewConfig : {
			enableRowBody : true,
			getRowClass : function(r, rowIndex, rowParams, store) {
				return (r.data.nServir > 0) ? 'cell-repo-tratar' : ((r.data.nStock <= 0) ? 'cell-repo-stock' : '');
			}
		}
	});

	var grid = Ext.getCmp(form_id + '_grid');

	var cm_lineas = fn_contextmenu();
	var contextmenu = Ext.app.addContextMenuLibro(grid, 'nIdLibro', cm_lineas);
	cm_lineas.setContextMenu(contextmenu)
	var ctxRow = null;
	contextmenu.add({
		text : _s('Ver pedido'),
		handler : function() {
			try {
				var ctxRow = cm_lineas.getItemSelect();
				if(ctxRow != null) {
					Ext.app.execCmd({
						url : site_url('compras/pedidoproveedor/index/' + ctxRow.data.nIdPedido)
					});
				}
			} catch (e) {
				console.dir(e);
			}
		},
		iconCls : 'iconoPedidoProveedor'
	});

	cm_lineas.setContextMenu(contextmenu)

	var m_pedido = contextmenu.add({
		text : _s('Ver proveedor'),
		handler : function() {
			var ctxRow = cm_lineas.getItemSelect();
			if(ctxRow != null) {
				Ext.app.execCmd({
					url : site_url('proveedores/proveedor/index/' + ctxRow.data.nIdProveedor)
				});
			}
		},
		iconCls : 'iconoProveedores'
	});
	var m_cliente = contextmenu.add({
		text : _s('Ver cliente'),
		handler : function() {
			var ctxRow = cm_lineas.getItemSelect();
			if(ctxRow != null) {
				Ext.app.execCmd({
					url : site_url('clientes/cliente/index/' + ctxRow.data.nIdCliente)
				});
			}
		},
		iconCls : 'iconoClientes'
	});
	var m_suscripcion = contextmenu.add({
		text : _s('Ver suscripción'),
		handler : function() {
			var ctxRow = cm_lineas.getItemSelect();
			if(ctxRow != null) {
				Ext.app.execCmd({
					url : site_url('suscripciones/suscripcion/index/' + ctxRow.data.nIdSuscripcion)
				});
			}
		},
		iconCls : 'iconoSuscripciones'
	});

	addMenuSeparator(cm_lineas);

	contextmenu.add({
		iconCls : "icon-generar-doc",
		text : _s('Cerrar'),
		handler : function() {
			accion(site_url('compras/pedidoproveedor/cerrar'));
		}
	});
	contextmenu.add({
		iconCls : "icon-send",
		text : _s('Cerrar y enviar'),
		handler : function() {
			cerrarenviar('compras/pedidoproveedor/send');
		}
	});
	contextmenu.add('-');
	contextmenu.add({
		iconCls : "icon-generar-doc",
		text : _s('Forzar cierre'),
		handler : function() {
			accion(site_url('compras/pedidoproveedor/cerrar?force=1'));
		}
	});
	contextmenu.add('-');

	contextmenu.add({
		iconCls : "icon-excel",
		text : _s('Exportar EXCEL'),
		handler : function() {
			accion(site_url('compras/pedidoproveedor/exportar_excel'), null, function (res) {
				Ext.app.askexit = false;
				document.location = res.src;
				setTimeout(function() {
					Ext.app.askexit = true;
				}, 2);
			});
		}
	});
	contextmenu.add({
		iconCls : "icon-excel",
		text : _s('Cerrar y exportar EXCEL'),
		handler : function() {
			cerrarenviar('compras/pedidoproveedor/exportar_excel', function (res) {
				Ext.app.askexit = false;
				document.location = res.src;
				setTimeout(function() {
					Ext.app.askexit = true;
				}, 2);
			});
		}
	});
	contextmenu.add('-');


	contextmenu.add({
		iconCls : "icon-delete",
		text : _s('Eliminar'),
		handler : function() {
			accion(site_url('compras/pedidoproveedorlinea/del'), 'nIdLinea');
		}
	});

	addMenuSeparator(cm_lineas);
	var m_pedidocliente = contextmenu.add({
		text : _s('Ver pedidos cliente'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			if(record != null) {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/pedidos_cliente'),
					params : {
						idl : record.data.nIdLibro,
						pendientes : true
					}

				});
			}
		},
		iconCls : 'iconoPedidoCliente'
	});
	var m_resumen = contextmenu.add({
		text : _s('Ver resumen artículo'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			if(record != null) {
				Ext.app.callRemote({
					url : site_url('compras/reposicion/get_datos_venta'),
					params : {
						id : record.data.nIdLibro,
						dialog : true
					},
					fn_ok : function(res) {
						console.dir(res);
					}
				});
			}
		},
		iconCls : 'iconoAltaRapidaArticulo'
	});

	addMenuSeparator(cm_lineas);
	addMenuDocumentos(cm_lineas);
	addMenuVentas(cm_lineas);

    var fn_check_menu = function(item) {
        (item.data.nIdCliente > 0) ? m_cliente.setVisible(true) : m_cliente.setVisible(false);
        (item.data.nIdSuscripcion > 0) ? m_suscripcion.setVisible(true) : m_suscripcion.setVisible(false);
    }
    cm_lineas.setCheckMenu(fn_check_menu);

	panel.on('afterrender', function(p) {
		var map = new Ext.KeyMap(p.getEl(), [{
			key : [10, 13],
			ctrl : true,
			stopEvent : true,
			fn : function() {
				reload();
			}
		}]);
	});
	return panel;
})();
