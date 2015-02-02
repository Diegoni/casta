(function() {
	var years = Ext.app.combobox({
		url : site_url('calendario/dia/years'),
		name : 'year',
		label : _s('Año')
	});

	var id= Ext.app.createId();

	// Muestra todos los albaranes por facturar
	var model = [{
		name : 'nIdTrabajador'
	}, {
		name : 'cNombre'
	}];

	var url = site_url("calendario/trabajador/get_list");
	var store = Ext.app.createStore({
		model : model,
		url : url
	});

	var sm = new Ext.grid.CheckboxSelectionModel();

	var columns = [sm, {
		header : _s("Id"),
		width : Ext.app.TAM_COLUMN_ID,
		dataIndex : 'nIdTrabajador',
		sortable : true
	}, {
		header : _s("Nombre"),
		dataIndex : 'cNombre',
		id: 'descripcion',
		width : Ext.app.TAM_COLUMN_TEXT,
		sortable : true
	}];

	var grid = new Ext.grid.GridPanel({
		store : store,
		//anchor : '95% 95%',
		height : 300,
		autoExpandColumn: 'descripcion',
		stripeRows : true,
		loadMask : true,
		sm : sm,

		bbar : Ext.app.gridBottom(store, true),

		// grid columns
		columns : columns
	});

	var controls = [years, {
							xtype : 'hidden',
							id : id + '_destino',
							name : 'destino'
						}, {
							xtype : 'textfield',
							name : 'msg',
							allowBlank : false,
							fieldLabel : _s('cDescripcion')
							}, grid];

	var form = Ext.app.formStandarForm({
		controls : controls,
		title : _s('liquidar-horas'),
		icon: 'icon-tool',
		autosize : false,
		labelWidth : 100,
		height : 450,
		width : 600,
		url : site_url('calendario/trabajador/liquidar_horas'),
		fn_pre : function() {
			var sel = grid.getSelectionModel().getSelections();
			var ids = [];
			Ext.each(sel, function(item) {
				ids.push(item.data.nIdTrabajador);
			});
			Ext.getCmp(id + '_destino').setValue(ids);
			return true;
		}
	});
	store.load();
	years.store.load();
	form.show();

	return;
})();
