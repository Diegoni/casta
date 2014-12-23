var search_albaranentrada = function(grid_id, fn_open) {
	return (function() {
		var compras_estadoalbaranentrada_search = new Ext.form.ComboBox(Ext.app.combobox({
			url : "http://localhost:80/app/compras/estadoalbaranentrada/search"
		}));
		var generico_divisa_search = new Ext.form.ComboBox(Ext.app.combobox({
			url : "http://localhost:80/app/generico/divisa/search"
		}));
		var model = [{
			name : 'id',
			column : {
				header : "Id",
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : 'id',
				sortable : true
			}
		}, {
			name : 'nIdAlbaran'
		}, {
			name : 'cProveedor',
			add : Ext.app.autocomplete2({
				url : "http://localhost:80/app/proveedores/proveedor/search",
				hiddenName : 'nIdProveedor',
				name : 'proveedores_proveedor_search',
				fieldLabel : _s("nIdProveedor"),
				allowBlank : false
			}),
			ro : false
		}, {
			name : 'nIdProveedor',
			column : {
				header : "Id Proveedor",
				width : Ext.app.TAM_COLUMN_NUMBER,
				renderer : function(val, x, r) {
					return r.data.cProveedor;
				},
				id : 'descripcion',
				editor : new Ext.form.NumberField({
					selectOnFocus : true
				}),
				sortable : true
			},
			add : {
				xtype : 'numberfield',
				allowBlank : false
			},
			ro : true
		}, {
			name : 'nIdEstado',
			column : {
				header : "Estado",
				width : Ext.app.TAM_COLUMN_NUMBER,
				renderer : function(val) {
					return Ext.app.renderCombo(val, compras_estadoalbaranentrada_search);
				},
				editor : compras_estadoalbaranentrada_search,
				sortable : true
			},
			add : Ext.app.combobox({
				url : "http://localhost:80/app/compras/estadoalbaranentrada/search",
				id : 'nIdEstado',
				anchor : '100%',
				fieldLabel : _s("nIdEstado"),
				autoload : true,
				allowBlank : true
			}),
			ro : false
		}, {
			name : 'cRefProveedor',
			extras : {
				anchor : '95%'
			},
			column : {
				header : "Ref.Proveedor",
				width : Ext.app.TAM_COLUMN_TEXT,
				editor : new Ext.form.TextField({
					selectOnFocus : true
				}),
				sortable : true
			},
			add : {
				xtype : 'textfield',
				allowBlank : true
			},
			ro : false
		}, {
			name : 'cRefInterna',
			extras : {
				anchor : '95%'
			},
			column : {
				header : "Ref.Interna",
				width : Ext.app.TAM_COLUMN_TEXT,
				editor : new Ext.form.TextField({
					selectOnFocus : true
				}),
				sortable : true
			},
			add : {
				xtype : 'textfield',
				allowBlank : true
			},
			ro : false
		}, {
			name : 'nIdDivisa',
			column : {
				header : "Id Divisa",
				width : Ext.app.TAM_COLUMN_NUMBER,
				renderer : function(val) {
					return Ext.app.renderCombo(val, generico_divisa_search);
				},
				editor : generico_divisa_search,
				sortable : true
			},
			add : Ext.app.combobox({
				url : "http://localhost:80/app/generico/divisa/search",
				id : 'nIdDivisa',
				anchor : '100%',
				fieldLabel : _s("nIdDivisa"),
				autoload : true,
				allowBlank : true
			}),
			ro : false
		}, {
			name : 'bDeposito',
			column : {
				header : "Depósito",
				width : Ext.app.TAM_COLUMN_BOOL,
				renderer : Ext.app.renderCheck,
				editor : new Ext.form.Checkbox(),
				sortable : true
			},
			add : {
				xtype : 'checkbox',
				allowBlank : true
			},
			ro : false
		}, {
			name : 'dVencimiento',
			extras : {
				dateFormat : 'timestamp',
				startDay : Ext.app.DATESTARTDAY
			},
			type : 'date',
			column : {
				header : "Vencimiento",
				width : Ext.app.TAM_COLUMN_DATE,
				renderer : Ext.app.renderDateShort,
				editor : new Ext.form.DateField({
					startDay : Ext.app.DATESTARTDAY,
					format : "d/m/Y"
				}),
				sortable : true
			},
			add : {
				xtype : 'datefield',
				allowBlank : true,
				startDay : Ext.app.DATESTARTDAY
			},
			ro : false
		}, {
			name : 'dCierre',
			extras : {
				dateFormat : 'timestamp',
				startDay : Ext.app.DATESTARTDAY
			},
			type : 'date',
			column : {
				header : "Cierre",
				width : Ext.app.TAM_COLUMN_DATE,
				renderer : Ext.app.renderDateShort,
				editor : new Ext.form.DateField({
					startDay : Ext.app.DATESTARTDAY,
					format : "d/m/Y"
				}),
				sortable : true
			},
			add : {
				xtype : 'datefield',
				allowBlank : true,
				startDay : Ext.app.DATESTARTDAY
			},
			ro : false
		}, {
			name : 'dFecha',
			extras : {
				dateFormat : 'timestamp',
				startDay : Ext.app.DATESTARTDAY
			},
			type : 'date',
			column : {
				header : "Fecha",
				width : Ext.app.TAM_COLUMN_DATE,
				renderer : Ext.app.renderDateShort,
				editor : new Ext.form.DateField({
					startDay : Ext.app.DATESTARTDAY,
					format : "d/m/Y"
				}),
				sortable : true
			},
			add : {
				xtype : 'datefield',
				allowBlank : true,
				startDay : Ext.app.DATESTARTDAY
			},
			ro : false
		}, {
			name : 'cNumeroAlbaran',
			extras : {
				anchor : '95%'
			},
			column : {
				header : "Número Albarán",
				width : Ext.app.TAM_COLUMN_TEXT,
				editor : new Ext.form.TextField({
					selectOnFocus : true
				}),
				sortable : true
			},
			add : {
				xtype : 'textfield',
				allowBlank : true
			},
			ro : false
		}, {
			name : 'bExtranjero',
			column : {
				header : "Extranjero ",
				width : Ext.app.TAM_COLUMN_BOOL,
				renderer : Ext.app.renderCheck,
				editor : new Ext.form.Checkbox(),
				sortable : true
			},
			add : {
				xtype : 'checkbox',
				allowBlank : true
			},
			ro : false
		}, {
			name : 'bSuscripciones',
			column : {
				header : "Suscripciones",
				width : Ext.app.TAM_COLUMN_BOOL,
				renderer : Ext.app.renderCheck,
				editor : new Ext.form.Checkbox(),
				sortable : true
			},
			add : {
				xtype : 'checkbox',
				allowBlank : true
			},
			ro : false
		}, {
			name : 'nLibros',
			column : {
				header : "Artículos",
				width : Ext.app.TAM_COLUMN_NUMBER,
				editor : new Ext.form.NumberField({
					selectOnFocus : true
				}),
				sortable : true
			},
			add : {
				xtype : 'numberfield',
				allowBlank : true
			},
			ro : false
		}, {
			name : 'fTotal',
			column : {
				header : "Total",
				width : Ext.app.TAM_COLUMN_NUMBER,
				editor : new Ext.form.NumberField({
					selectOnFocus : true
				}),
				sortable : true
			},
			add : {
				xtype : 'numberfield',
				allowBlank : true
			},
			ro : false
		}, {
			name : 'dCreacion',
			extras : {
				dateFormat : 'timestamp',
				startDay : Ext.app.DATESTARTDAY
			},
			type : 'date',
			column : {
				header : "F.Cre.",
				width : Ext.app.TAM_COLUMN_DATE,
				renderer : Ext.app.renderDate,
				editor : new Ext.form.DateField({
					format : "d/m/Y",
					startDay : Ext.app.DATESTARTDAY
				}),
				sortable : true
			},
			add : {
				xtype : 'datefield',
				allowBlank : true,
				startDay : Ext.app.DATESTARTDAY
			},
			ro : true
		}, {
			name : 'cCUser',
			extras : {
				anchor : '95%'
			},
			column : {
				header : "U.Creación",
				width : Ext.app.TAM_COLUMN_TEXT,
				editor : new Ext.form.TextField({
					selectOnFocus : true
				}),
				sortable : true
			},
			add : {
				xtype : 'textfield',
				allowBlank : true
			},
			ro : true
		}];
		var stores = [{
			store : compras_estadoalbaranentrada_search.store
		}, {
			store : generico_divisa_search.store
		}];
		var form_id = Ext.app.createId();
		var panel = Ext.app.createFormGrid({
			model : model,
			id : "" + grid_id + "_g_search",
			title : "",
			icon : "",
			idfield : 'id',
			urlget : "http://localhost:80/app/compras/albaranentrada/get_list",
			show_filter : false,
			urladd : "http://localhost:80/app/compras/albaranentrada/add",
			urlupd : "http://localhost:80/app/compras/albaranentrada/upd",
			urldel : "http://localhost:80/app/compras/albaranentrada/del",
			loadstores : stores,
			fn_pre : null,
			fn_add : null,
			load : false,
			mode : "search",
			fn_open : fn_open
		});
		var grid = Ext.getCmp("" + grid_id + "_g_search_grid");
		return panel;
	})();
	;
}