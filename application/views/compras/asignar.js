(function() {
	var id = "<?php echo $id;?>";
	var model = [{
		name: 'nIdLibro'
	},{
		name: 'id'
	},{
		name: 'text'
	},{
		name: 'nCantidad'
	},{
		name: 'cTitulo'
	},{
		name: 'nCantidadAsignada'
	},{
		name: 'cSeccion'
	},{
		name: 'nIdSeccion'
	},{
		name: 'nIdLineaPedido'
	},{
		name: 'nPedidoPendientes'
	},{
		name: 'nIdPedido'
	},{
		name: 'dFechaEntrega'
	},{
		name: 'nDias'
	},{
		name: 'nAsignar'
	},{
		name: 'nIdLineaPedidoConcurso'
	}];

	var url = site_url('compras/albaranentrada/get_asignacion/' + id);
	var store = Ext.app.createStore({
		model: model,
		timeout: false,
		sortInfo: {
			field: 'nDias',
			direction: "DESC"
		},
		groupField: 'text',
		url: url
	});

	var seccion = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('generico/seccion/search'),
		anchor : "90%",
		allowBlank : true,
		name : 'ids',
		label : _s('Filtro')
	}));

	seccion.on('select', function(c, r, i) {
		grid.getView().refresh();
	});

	var cantidadEditor = new Ext.ux.form.Spinner({
		allowNegative: false,
		allowDecimals: false,
		selectOnFocus: true,
		strategy: new Ext.ux.form.Spinner.NumberStrategy()
	});

	var columns = [{
		header: _s("Id"),
		width: Ext.app.TAM_COLUMN_ID,
		dataIndex: 'nIdLibro',
		hidden: true,
		sortable: true
	},{
		//id: 'descripcion',
		header: _s("cTitulo"),
		dataIndex: 'text',
		width: Ext.app.TAM_COLUMN_TEXT,
		sortable: true
	},{
		header: _s("nCantidad"),
		dataIndex: 'nCantidad',
		width: Ext.app.TAM_COLUMN_NUMBER/2,
		hidden: true,
		sortable: true
	},{
		header: _s("nCantidadAsignada"),
		dataIndex: 'nCantidadAsignada',
		width: Ext.app.TAM_COLUMN_NUMBER/2,
		hidden: true,
		sortable: true
	},{
		header: _s("Sección"),
		dataIndex: 'cSeccion',
		width: Ext.app.TAM_COLUMN_TEXT*2,
		sortable: true
	},{
		header: _s("Pendientes"),
		dataIndex: 'nPedidoPendientes',
		width: Ext.app.TAM_COLUMN_NUMBER/2,
		sortable: true
	},{
		header: _s("nIdPedido"),
		dataIndex: 'nIdPedido',
		width: Ext.app.TAM_COLUMN_NUMBER,
		sortable: true
	},{
		header: _s('dFechaEntrega'),
		dataIndex: 'dFechaEntrega',
		width: Ext.app.TAM_COLUMN_DATE,
		renderer: Ext.app.renderDateShort,
		sortable: true
	},{
		header: _s("Días"),
		dataIndex: 'nDias',
		width: Ext.app.TAM_COLUMN_NUMBER,
		sortable: true
	},{
		header: _s("Asignar"),
		dataIndex: 'nAsignar',
		width: Ext.app.TAM_COLUMN_NUMBER,
		editor: cantidadEditor,
		sortable: true
	}];

	var ajustar = function(originalValue, value, record) {
		//console.log(originalValue + '->' + value);
		if (originalValue != value) {
			var dif = originalValue - value;
			// Si es un autopedido, actualiza las cantidades
			if (record.data.nIdPedido == null) {
				store.suspendEvents(false);
				store.each( function(r) {
					if ((r.data.nIdPedido == null) && (r.data.id != record.data.id) && (r.data.nIdLibro == record.data.nIdLibro)) {
						var n = parseInt(r.data.nAsignar) + dif;
						if (n < 0) {
							dif = n;
							n = 0;
						} else {
							dif = 0;
						}
						r.set('nAsignar', n);
						return (dif != 0);
					}
				});
				if (dif != 0) {
					record.set('nAsignar', parseInt(value) + dif);
				}
				store.resumeEvents();
				grid.getView().refresh();
			} else {
				var v = value;
				if (v > record.data.nPedidoPendientes) {
					v = record.data.nPedidoPendientes;
					record.set('nAsignar', v);
				}
				dif = originalValue - v;
				store.suspendEvents(false);
				store.each( function(r) {
					if ((r.data.nIdPedido != null) && (r.data.id != record.data.id) && (r.data.nIdLibro == record.data.nIdLibro)) {
						var n = parseInt(r.data.nAsignar) + dif;
						if (n < 0) {
							dif = n;
							n = 0;
						} else {
							dif = 0;
						}
						r.set('nAsignar', n);
						return (dif != 0);
					}
				});
				if (dif != 0) {
					record.set('nAsignar', parseInt(value) + dif);
				}
				store.resumeEvents();
				grid.getView().refresh();
			}
		}
	}
	var grid = new Ext.grid.EditorGridPanel({
		store: store,
		anchor: '100% 82%',
		//height: 400,
		//autoExpandColumn: 'descripcion',
		stripeRows: true,
		loadMask: true,

		bbar: Ext.app.gridBottom(store, true),

		view: new Ext.grid.GroupingView({
			forceFit: true,
			getRowClass: function(r, rowIndex, rowParams, store) {
				var id = seccion.getValue();
				if (id>0 && r.data.nIdSeccion != id && r.data.nAsignar>0)
					return 'cell-doc-precio0';
				return (r.data.nIdPedido == null) ? 'cell-repo-stock' : '';
			}
		}),
		listeners: {
			celldblclick: function(grid, row, column, e) {
				var record = grid.store.getAt(row);
				//console.dir(record);
				ajustar(record.data.nAsignar, (record.data.nPedidoPendientes != null) ? record.data.nPedidoPendientes : 1000, record)
				if (record.data.nPedidoPendientes != null)
					record.set('nAsignar', Math.min(record.data.nPedidoPendientes, record.data.nCantidad));
			},
			afteredit: function(e) {
				//console.dir(e);
				if (e.originalValue != e.value) {
					var dif = e.originalValue - e.value;
					// Si es un autopedido, actualiza las cantidades
					if (e.record.data.nIdPedido == null) {
						store.suspendEvents(false);
						store.each( function(r) {
							if ((r.data.nIdPedido == null) && (r.data.id != e.record.data.id) && (r.data.nIdLibro == e.record.data.nIdLibro)) {
								var n = parseInt(r.data.nAsignar) + dif;
								if (n < 0) {
									dif = n;
									n = 0;
								} else {
									dif = 0;
								}
								r.set('nAsignar', n);
								return (dif != 0);
							}
						});
						if (dif != 0) {
							e.record.set('nAsignar', parseInt(e.value) + dif);
						}
						store.resumeEvents();
						grid.getView().refresh();
					} else {
						var v = e.value;
						if (v > e.record.data.nPedidoPendientes) {
							v = e.record.data.nPedidoPendientes;
							e.record.set('nAsignar', v);
						}
						dif = e.originalValue - v;
						store.suspendEvents(false);
						store.each( function(r) {
							if ((r.data.nIdPedido != null) && (r.data.id != e.record.data.id) && (r.data.nIdLibro == e.record.data.nIdLibro)) {
								var n = parseInt(r.data.nAsignar) + dif;
								if (n < 0) {
									dif = n;
									n = 0;
								} else {
									dif = 0;
								}
								r.set('nAsignar', n);
								return (dif != 0);
							}
						});
						if (dif != 0) {
							e.record.set('nAsignar', parseInt(e.value) + dif);
						}
						store.resumeEvents();
						grid.getView().refresh();
					}
				}
			}
		},

		// grid columns
		columns: columns
	});
	var controls = [grid, seccion];

	var form = Ext.app.formStandarForm({
		controls: controls,
		autosize: false,
		labelWidth: 200,
		height: 600,
		icon: 'iconoAsignarTab',
		width: 700,
		title: _s('Asignación de albarán'),
		fn_ok: function() {
			var asig = '';
			var sec = '';
			store.each( function(e) {
				//console.dir(e.data);
				if (e.data.nAsignar > 0) {
					if (e.data.nIdPedido != null) {
						asig += e.data.nIdLibro + '##' + e.data.nIdLineaPedido + '##' + e.data.nAsignar + '##' + e.data.cSeccion + '##' + e.data.nIdPedido + '##' + e.data.nIdSeccion + '##' + e.data.nIdLineaPedidoConcurso + ';';
					} else {
						sec += e.data.nIdLibro + '##' + e.data.nIdSeccion + '##' + e.data.nAsignar + '##' + e.data.cSeccion + ';';
					}
				}
			});
			Ext.app.callRemote({
				url: site_url('compras/albaranentrada/asignar'),
				timeout: false,
				params: {
					id: id,
					asig: asig,
					auto: sec
				},
				fnok: function(obj) {
					var f = Ext.getCmp('<?php echo $cmpid;?>');
					if (f!=null)
					{						
						f.refresh();
						if (f.asignado != null) f.asignado();
					}
					form.close();
				}
			});
		}
	});

	store.baseParams = {
		id: id
	};
	seccion.store.load();

	store.load();
	form.show();
	return;
})();