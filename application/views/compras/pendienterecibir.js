(function() {

	var open_id = "<?php echo $open_id;?>";
	var form_id = "<?php echo $id;?>";
	var title = "<?php echo $title;?>";
	var icon = "<?php echo $icon;?>";
	if(title == '')
		title = _s('Pedidos pendientes de recibir');
	if(icon == '')
		icon = 'iconoPendientesRecibirTab';
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
		name : 'cISBN',
		column : {
			header : _s('cISBN'),
			width : Ext.app.TAM_COLUMN_ISBN,
			sortable : true
		}
	}, {
		name : 'cAutores',
		column : {
			header : _s('cAutores'),
			width : Ext.app.TAM_COLUMN_TEXT,
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
		name : 'nPendientes',
		type : 'int',
		column : {
			header : _s('Pendientes'),
			width : Ext.app.TAM_COLUMN_NUMBER,
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
		name : 'dFechaEntrega',
		column : {
			header : _s('dFechaEntrega'),
			width : Ext.app.TAM_COLUMN_DATE,
			renderer : Ext.app.renderDateShort,
			sortable : true
		}
	}, {
		name : 'nDias',
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
	}, {
		name : 'nIdInformacion',
	}, {
		name : 'cInformacion',
		column : {
			header : _s('cInformacion'),
			width : Ext.app.TAM_COLUMN_TEXT,
			renderer : renderInfo,
			sortable : true
		}
	}, {
		name : 'dFechaInformacion',
		column : {
			header : _s('Fec. Inf.'),
			width : Ext.app.TAM_COLUMN_DATE,
			renderer : Ext.app.renderDate,
			sortable : true
		}
	}, {
		name : 'nIdReclamacion',
		column : {
			header : _s('nIdReclamacion'),
			width : Ext.app.TAM_COLUMN_ID,
			sortable : true,
			hidden : true
		}
	}, {
		name : 'dReclamacion',
		column : {
			header : _s('Ult. Reclamacion'),
			width : Ext.app.TAM_COLUMN_DATE,
			renderer : Ext.app.renderDate,
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

    var proveedores = new Ext.form.ComboBox(Ext.app.autocomplete({
        url: site_url('proveedores/proveedor/search'),
        width: 200
    }));
	/*var proveedores = new Ext.form.ComboBox(Ext.app.autocomplete2({
		url : site_url('proveedores/proveedor/search'),
		name : form_id + '_prv',
		id: form_id + '_prv',
		anchor : '100%',
		fieldLabel : _s('Proveedor')
	}));*/

	var reload = function(ids, idpv, idl, cpp) {
		if(ids == null)
			ids = Ext.app.getIdCombo(seccion);
		if(idl == null)
			idl = Ext.app.getIdCombo(art);
		if(idpv == null) {
			//var p = Ext.getCmp(proveedores.id);
			idpv = /*p.getValue(); //*/Ext.app.getIdCombo(proveedores);
		}
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

	var accion = function(url) {
		var grid = Ext.getCmp(form_id + '_grid');
		var codes = Ext.app.gridGetChecked(grid);
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
				grid.store.load();
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

	var panel = Ext.app.createFormGrid({
		model : model,
		checkbox : true,
		show_filter : false,
		id : form_id,
		title : title,
		icon : icon,
		idfield : 'id',
		timeout: false,
		urlget : site_url('compras/pedidoproveedor/get_pendienterecibir'),
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
	//console.dir(g);
	var cm_lineas = fn_contextmenu();
	var contextmenu = Ext.app.addContextMenuLibro(grid, 'nIdLibro', cm_lineas);
	cm_lineas.setContextMenu(contextmenu)

	var m_pedido = contextmenu.add({
		text : _s('Ver pedido'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			if(record != null) {
				Ext.app.execCmd({
					url : site_url('compras/pedidoproveedor/index/' + record.data.nIdPedido)
				});
			}
		},
		iconCls : 'iconoPedidoProveedor'
	});

	var m_reclamacion = contextmenu.add({
		text : _s('Ver reclamación'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			if(record != null) {
				Ext.app.execCmd({
					url : site_url('compras/reclamacion/index/' + record.data.nIdReclamacion)
				});
			}
		},
		iconCls : 'iconoReclamacionPedidoProveedor'
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

	var fn_check_menu = function(item) {
		(item.data.nIdReclamacion) ? m_reclamacion.enable() : m_reclamacion.disable(); 
		(item.data.nServir > 0) ? m_pedidocliente.enable() : m_pedidocliente.disable();
	}

	cm_lineas.setCheckMenu(fn_check_menu);

	addMenuSeparator(cm_lineas);
	addMenuDocumentos(cm_lineas);
	addMenuVentas(cm_lineas);
	addMenuSeparator(cm_lineas);

	contextmenu.add({
		iconCls : "iconoReclamacionPedidoProveedor",
		text : _s('Reclamar'),
		handler : function() {
			accion(site_url('compras/reclamacion/crear'));
		}
	});

	contextmenu.add('-');
	contextmenu.add({
		iconCls : "icon-cancel",
		text : _s('Cancelar'),
		handler : function() {
			accion(site_url('compras/pedidoproveedorlinea/cancelar'));
		}
	});

	contextmenu.add({
		iconCls : "iconoCancelacionPedidoProveedor",
		text : _s('Cancelar y avisar'),
		handler : function() {
			accion(site_url('compras/cancelacion/crear'));
		}
	});

	Ext.app.callRemote({
		url : site_url('compras/informacionproveedor/get_list'),
		fnok : function(res) {
			contextmenu.add('-');
			Ext.each(res.value_data, function(item) {
				contextmenu.add({
					iconCls : "icon-status-" + item.id,
					text : _s(item.cDescripcion),
					handler : function() {
						accion(site_url('compras/pedidoproveedorlinea/info/' + item.id));
					}
				});
			});
		}
	});

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
