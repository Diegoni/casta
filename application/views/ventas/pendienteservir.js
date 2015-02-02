(function() {

	var open_id = "<?php echo $open_id;?>";
	var form_id = "<?php echo $id;?>";
	var title = "<?php echo $title;?>";
	var icon = "<?php echo $icon;?>";
	if(title == '')
		title = _s('Pendientes de servir');
	if(icon == '')
		icon = 'iconoPendientesServirTab';
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
		name : 'cCliente',
		column : {
			id : 'descripcion',
			header : _s('Cliente'),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}
	}, {
		name : 'nIdLibro',
		type : 'int',
		column : {
			header : _s('nIdLibro'),
			width : Ext.app.TAM_COLUMN_ID,
			hidden : true,
			sortable : true
		}
	}, {
		name : 'cISBN',
		column : {
			header : _s('cISBN'),
			width : Ext.app.TAM_COLUMN_ISBN,
			hidden : true,
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
		name : 'cEstado',
		column : {
			header : _s('cEstado'),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}
	}, {
		name : 'nIdEstado',
		column : {
			hidden : true,
			hideable : false,
			sortable : false
		}
	}, {
		name : 'nIdEstadoLibro',
		column : {
			hidden : true,
			hideable : false,
			sortable : false
		}
	}, {
		name : 'cEstadoLibro',
		column : {
			header : _s('cEstadoLibro'),
			width : Ext.app.TAM_COLUMN_TEXT,
			renderer : rendererEstadoLibro,
			sortable : true
		}
	}, {
		name : 'nCantidad',
		type : 'int',
		column : {
			header : _s('Pedidas'),
			width : Ext.app.TAM_COLUMN_NUMBER / 2,
			sortable : true
		}
	}, {
		name : 'nPendientes',
		type : 'int',
		column : {
			header : _s('Pendientes'),
			width : Ext.app.TAM_COLUMN_NUMBER / 2,
			sortable : true
		}
	}, {
		name : 'nCantidadServida',
		type : 'int',
		column : {
			header : _s('nCantidadServida'),
			width : Ext.app.TAM_COLUMN_NUMBER / 2,
			sortable : true
		}
	}, {
		name : 'nStock',
		type : 'int',
		column : {
			header : _s('Stock'),
			width : Ext.app.TAM_COLUMN_NUMBER / 2,
			sortable : true
		}
	}, {
		name : 'nStockDisponible',
		type : 'int',
		column : {
			header : _s('Disponible'),
			width : Ext.app.TAM_COLUMN_NUMBER / 2,
			sortable : true
		}
	}, {
		name : 'nRecibir',
		type : 'int',
		column : {
			header : _s('nStockRecibir'),
			width : Ext.app.TAM_COLUMN_NUMBER / 2,
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
		name : 'nDias',
		column : {
			header : _s('Días'),
			width : Ext.app.TAM_COLUMN_NUMBER / 2,
			sortable : true
		}
	}, {
		name : 'cRefCliente',
		column : {
			header : _s('cRefCliente'),
			width : Ext.app.TAM_COLUMN_TEXT,
			sortable : true
		}
	}, {
		name : 'nIdInformacion',
	}, {
		name : 'nIdTipoInformacion',
	}, {
		name : 'cInformacion',
		column : {
			header : _s('cInformacion'),
			width : Ext.app.TAM_COLUMN_TEXT,
			renderer : renderInfoCliente,
			sortable : true
		}
	}, {
		name : 'dFechaInformacion',
		column : {
			header : _s('dFechaInformacion'),
			width : Ext.app.TAM_COLUMN_DATE,
			renderer : Ext.app.renderDate,
			sortable : true
		}
	}, {
		name : 'dAviso',
		column : {
			header : _s('bAviso'),
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

	var clientes = new Ext.form.ComboBox(Ext.app.autocomplete({
		url : site_url('clientes/cliente/search'),
		width : 200
	}));

	var reload = function(ids, idcl, idl, cpp) {
		if(ids == null)
			ids = Ext.app.getIdCombo(seccion);
		if(idl == null)
			idl = Ext.app.getIdCombo(art);
		if(idcl == null) {
			idcl = Ext.app.getIdCombo(clientes);
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
			idcl : idcl,
			pp : pp,
			sort : 'cCliente'
		};
		grid.store.load();
	}
	var stores = [{
		store : seccion.store
	}];

	var accion = function(url, refresh, data) {
		var grid = Ext.getCmp(form_id + '_grid');
		var codes = Ext.app.gridGetChecked(grid);
		if(codes == null) {
			var record = cm_lineas.getItemSelect();
			if(record != null) {
				codes = record.data.nIdLinea + ';';
			}
		}
		//console.log('Codes: ' + codes);
		if(codes == null) {
			Ext.app.msgError(title, _s('no-libros-marcados'));
			return;
		}
		var params = (data != null) ? data : {};
		params['id'] = codes;
		params['cmpid'] = form_id + '_grid';


		if(refresh == null)
			refresh = true;
		if(refresh)
			grid.getEl().mask();
		Ext.app.callRemote({
			url : url,
			timeout : false,
			wait : true,
			params : params,
			fnok : function() {
				if(refresh) {
					grid.getEl().unmask();
					grid.store.load();
					grid.getSelectionModel().deselectRange(0, grid.store.getTotalCount());
				}
			},
			fnnok : function() {
				if(refresh)
					grid.getEl().unmask();
			}
		});
	}
	var tbar = [{
		xtype : 'label',
		html : _s('Sección')
	}, seccion, '-', {
		xtype : 'label',
		html : _s('Cliente')
	}, clientes, '-', {
		xtype : 'label',
		html : _s('Artículo')
	}, art, '-', {
		xtype : 'label',
		html : _s('Con Ped. Prv.')
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
			clientes.reset();
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
		urlget : site_url('ventas/pedidocliente/get_pendienteservir'),
		loadstores : stores,
		fn_pre : null,
		fn_add : null,
		tbar : tbar,
		load : false,
		viewConfig : {
			enableRowBody : true,
			getRowClass : function(r, rowIndex, rowParams, store) {
				return (r.data.nPendientes > r.data.nStockDisponible) ? 'cell-repo-tratar' : ((r.data.nPendientes > 0) ? 'cell-servir-pend' : '');
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
					url : site_url('ventas/pedidocliente/index/' + record.data.nIdPedido)
				});
			}
		},
		iconCls : 'iconoPedidoCliente'
	});

	addMenuSeparator(cm_lineas);
	var m_pedidoproveedor = contextmenu.add({
		text : _s('Ver pedidos proveedor'),
		handler : function() {
			var record = cm_lineas.getItemSelect();
			if(record != null) {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/pedidos_proveedor'),
					params : {
						idl : record.data.nIdLibro,
						pendientes : true
					}

				});
			}
		},
		iconCls : 'iconoPedidoProveedor'
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
					}
				});
			}
		},
		iconCls : 'iconoAltaRapidaArticulo'
	});

	var fn_check_menu = function(item) {
		//(item.data.nIdReclamacion) ? m_reclamacion.enable() : m_reclamacion.disable();
		(item.data.nRecibir > 0) ? m_pedidoproveedor.enable() : m_pedidoproveedor.disable();
	}

	cm_lineas.setCheckMenu(fn_check_menu);

	addMenuSeparator(cm_lineas);
	addMenuDocumentos(cm_lineas);
	addMenuCompras(cm_lineas);
	addMenuSeparator(cm_lineas);

	contextmenu.add({
		iconCls : "icon-accept",
		text : _s('Reservar'),
		handler : function() {
			accion(site_url('ventas/pedidoclientelinea/reservar'));
		}
	});
	contextmenu.add({
		iconCls : "icon-imposible",
		text : _s('Imposible Servir'),
		handler : function() {
			accion(site_url('ventas/pedidoclientelinea/imposibleservir'));
		}
	});

	contextmenu.add({
		iconCls : "icon-cancel",
		text : _s('Cancelar'),
		handler : function() {
			accion(site_url('ventas/pedidoclientelinea/cancelar'));
		}
	});

	addMenuSeparator(cm_lineas);
	contextmenu.add({
		iconCls : "icon-avisado",
		text : _s('Avisado'),
		handler : function() {
			accion(site_url('ventas/pedidoclientelinea/avisado'), true, {
				aviso : true
			});
		}
	});
	contextmenu.add({
		iconCls : "icon-email",
		text : _s('avisar-clientes'),
		handler : function() {
			accion(site_url('ventas/pedidocliente/avisar'), false);
		}
	});

	Ext.app.callRemote({
		url : site_url('ventas/informacioncliente/get_list'),
		params : {
			sort : 'nIdTipo'
		},
		fnok : function(res) {
			contextmenu.add('-');
			Ext.each(res.value_data, function(item) {
				contextmenu.add({
					iconCls : "icon-status-" + item.nIdTipo,
					text : _s(item.cDescripcion),
					handler : function() {
						accion(site_url('ventas/pedidoclientelinea/info/' + item.id));
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
