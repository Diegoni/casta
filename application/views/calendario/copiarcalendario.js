(function(){

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

    var trabajador = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('calendario/trabajador/search'),
        label: _s('Origen'),
        name: 'origen',
        anchor: '90%'
    }));

	var controls = [trabajador, {
							xtype : 'textfield',
							emptyText : _s('Año'),
							fieldLabel : 'Año',
							name : 'year'
						}, {
							xtype : 'hidden',
							id : id + '_destino',
							name : 'destino'
						}, grid];

	var form = Ext.app.formStandarForm({
		controls : controls,
		autosize : false,
		labelWidth : 100,
		height : 450,
		width : 600,
		url: site_url('calendario/calendario/copiar'),
		title : _s('Copiar calendario'),
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
	trabajador.store.load();
	form.show();
	return true;
})();
