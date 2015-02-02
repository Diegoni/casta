(function() {
	var id = "<?php echo $id;?>";
	var model = [{
		name : 'nIdSuscripcion'
	}, {
		name : 'cTitulo'
	}, {
		name : 'nCantidad'
	}, {
		name : 'fDescuento'
	}, {
		name : 'fPrecio'
	}, {
		name : 'fPVPAsignado'
	}, {
		name : 'fIVA'
	}, {
		name : 'fPVP'
	}, {
		name : 'fCoste'
	}, {
		name : 'tipo'
	}, {
		name : 'nIdAlbaran'
	}, {
		name : 'text_aviso'
	}, {
		name : 'id_aviso'
	}];

	var url = site_url('suscripciones/suscripcion/get_facturas');
	var store = Ext.app.createStore({
		model : model,
		timeout: false,
		url : url
	});

	var precioEditor = new Ext.form.NumberField({
		allowNegative : false,
		allowDecimals : true,
		selectOnFocus : true
	});
	var descuentoEditor = new Ext.form.NumberField({
		allowNegative : false,
		minValue : 0,
		maxValue : 100,
		allowDecimals : true,
		selectOnFocus : true
	});
	var cantidadEditor = new Ext.form.NumberField({
		allowNegative : false,
		minValue : 0,
		maxValue : 100,
		allowDecimals : false,
		selectOnFocus : true
	});

	var sm = new Ext.grid.CheckboxSelectionModel();
	var columns = [sm, {
		header : _s("Id"),
		width : Ext.app.TAM_COLUMN_ID,
		dataIndex : 'nIdSuscripcion',
		hidden : false,
		sortable : true
	}, {
		header : _s("Albarán"),
		width : Ext.app.TAM_COLUMN_ID,
		dataIndex : 'nIdAlbaran',
		hidden : false,
		sortable : true
	}, {
		id : 'descripcion',
		header : _s("cTitulo"),
		dataIndex : 'cTitulo',
		width : Ext.app.TAM_COLUMN_TEXT,
		sortable : true
	}, {
		header : _s("Tipo"),
		width : Ext.app.TAM_COLUMN_TEXT / 2,
		dataIndex : 'tipo',
		sortable : true
	}, {
		header : _s("Aviso"),
		width : Ext.app.TAM_COLUMN_TEXT,
		dataIndex : 'text_aviso',
		sortable : true
	}, {
		header : _s("nCantidad"),
		dataIndex : 'nCantidad',
		width : Ext.app.TAM_COLUMN_NUMBER / 2,
		editor : cantidadEditor,
		sortable : true
	}, {
		header : _s("fDescuento"),
		dataIndex : 'fDescuento',
		width : Ext.app.TAM_COLUMN_NUMBER / 2,
		editor : descuentoEditor,
		sortable : true
	}, {
		header : _s("fCoste"),
		dataIndex : 'fCoste',
		width : Ext.app.TAM_COLUMN_NUMBER,
		align : 'right',
		sortable : true
	}, {
		header : _s("fPrecio"),
		dataIndex : 'fPrecio',
		align : 'right',
		width : Ext.app.TAM_COLUMN_NUMBER,
		sortable : true
	}, {
		header : _s("fIVA"),
		dataIndex : 'fIVA',
		align : 'center',
		width : Ext.app.TAM_COLUMN_NUMBER / 2,
		sortable : true
	}, {
		header : _s("fPVP"),
		dataIndex : 'fPVP',
		align : 'right',
		width : Ext.app.TAM_COLUMN_NUMBER,
		sortable : true
	}, {
		header : _s("Albarán"),
		width : Ext.app.TAM_COLUMN_ID,
		dataIndex : 'nIdAlbaran',
		hidden : true,
		sortable : true
	}, {
		header : _s("fPVPAsignado"),
		dataIndex : 'fPVPAsignado',
		align : 'right',
		width : Ext.app.TAM_COLUMN_NUMBER,
		editor : precioEditor,
		renderer : function(v, x, r, row, col) {
			return Ext.app.rendererPVP(((v == null || v == '') ? r.data.fPVP : v), x, r, row, col);
		},
		sortable : true
	}, {
		header : _s("fMargen"),
		dataIndex : 'fPrecio',
		align : 'right',
		width : Ext.app.TAM_COLUMN_NUMBER,
		renderer : function(v, x, r, row, col) {
			if(r != null)
				x.css = 'cell-docs-referencia';
			var v = r.data.fPVPAsignado;
			return Margen((v == null || v == '') ? r.data.fPrecioRecomendado : v, r.data.fCoste).decimal(Ext.app.DECIMALS);
		},
		sortable : true
	}];

	var grid = new Ext.grid.EditorGridPanel({
		store : store,
		anchor : '100% 80%',
		sm : sm,
		autoExpandColumn : 'descripcion',
		stripeRows : true,
		loadMask : true,

		bbar : Ext.app.gridBottom(store, true),
		viewConfig : {
			/*forceFit: true,*/
			enableRowBody : true,
			getRowClass : function(r, rowIndex, rowParams, store) {
				return (r.data.nIdAlbaran == null || r.data.nIdAlbaran == '') ? 'cell-repo-tratar' : '';
			}
		},

		listeners : {
			celldblclick : function(grid, row, column, e) {
				var record = grid.store.getAt(row);
				record.set('fPVPAsignado', record.data.fPVP)
				record.commit();
			}
		},

		// grid columns
		columns : columns
	});

	var obras = new Ext.Button({
		text : _s('Mostrar Obras'),
		enableToggle : true,
		iconCls : 'icon-filter',
		handler : function(e, b) {
			store.baseParams = {
				id : id,
				activas : true,
				obras : !store.baseParams.obras
			};
			(store.baseParams.obras)?this.setText(_s('Ocultar Obras')):this.setText(_s('Mostrar Obras'));

			store.load();
		}
	});

	var ref = new Ext.form.TextField({
		fieldLabel : _s('cReferencia')
	});

	var aceptadas = new Ext.form.Checkbox({
		checked : false,
		value : false,
		allowBlank : true
	});
	var singestionar = new Ext.form.Checkbox({
		checked : false,
		value : false,
		allowBlank : true,
	});
	var rechazadas = new Ext.form.Checkbox({
		checked : false,
		value : false,
		allowBlank : true
	});
	var marcar = function() {
		console.log('Marcar');
		var sel = grid.getSelectionModel();
		sel.clearSelections();
		var selectedRecords = new Array();
		grid.getStore().each(function(i) {
			console.dir(i.data);
			console.log('sin gestionar: ' + singestionar.getValue());
			if(((i.data.id_aviso == 1) && aceptadas.getValue()) || ((i.data.id_aviso == -1) && rechazadas.getValue()) || ((i.data.id_aviso == 0 || i.data.id_aviso == 2) && singestionar.getValue()))
				selectedRecords.push(i);
		});
		console.dir(selectedRecords);
		sel.selectRecords(selectedRecords, true);
	}
	aceptadas.on('check', function() {
		marcar();
	});
	rechazadas.on('check', function() {
		marcar();
	});
	singestionar.on('check', function() {
		marcar();
	});
	var controls = [{
		xtype : 'compositefield',
		hideLabel : true,
		items : [obras, {
			xtype : 'displayfield',
			value : _s('Aceptadas')
		}, aceptadas, {
			xtype : 'displayfield',
			value : _s('Rechazadas')
		}, rechazadas, {
			xtype : 'displayfield',
			value : _s('Sin Gestionar')
		}, singestionar]
	}, ref, grid];

	var form = Ext.app.formStandarForm({
		controls : controls,
		autosize : false,
		height : 600,
		icon : 'iconoSuscripcionesFacturarAlbaranesTab',
		width : 1000,
		title : _s('Facturar Suscripciones'),
		fn_ok : function() {
			var asig = '';
			var sel = grid.getSelectionModel().getSelections();
			Ext.each(sel, function(e) {
				asig += e.data.nIdSuscripcion + '##' + e.data.fPVPAsignado + '##' + e.data.fDescuento + '##' + e.data.nIdAlbaran + '##' + e.data.nCantidad + ';';
			});
			if(asig == '') {
				Ext.app.msgError(title, _s('no-items-marcados'));
				return false;
			}
			Ext.app.callRemote({
				url : site_url('suscripciones/suscripcion/crear_factura'),
				timeout : false,
				params : {
					id : id,
					volumen : ref.getValue(),
					precios : asig
				},
				fnok : function(obj) {
					form.close();
				}
			});
		}
	});

	store.baseParams = {
		id : id,
		activas : true,
		obras : false
	};

	store.load();
	form.show();

	return;
})();
