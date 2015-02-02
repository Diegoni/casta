(function() {

	try {

		var seccion = new Ext.form.ComboBox(Ext.app.combobox({
			url: site_url('generico/seccion/search'),
			label: _s('Seccion'),
			name: 'idseccion',
			anchor: '90%'
		}));

		var comparaciones = new Ext.data.SimpleStore({
			fields: ['v'],
			data: [['>'], ['>='], ['='], ['<='], ['<']]
		});

		var controls = [{
			value: DateAdd('m', -1, new Date()),
			fieldLabel: _s('Desde'),
			name: 'fecha1',
			allowBlank: false,
			startDay: Ext.app.DATESTARTDAY,
			xtype: "datefield"
		},{
			fieldLabel: _s('Hasta'),
			value: new Date(),
			name: 'fecha2',
			allowBlank: false,
			startDay: Ext.app.DATESTARTDAY,
			xtype: "datefield"
		}, seccion, new Ext.app.autocomplete2({
			allowBlank: true,
			url: site_url('proveedores/proveedor/search'),
			fieldLabel: _s('Proveedor'),
			name: 'idproveedor',
			anchor: '90%'
		}), {
			allowBlank : false,
			name : 'list',
			fieldLabel : _s('Desglosado'),
			value : true,
			checked: false,
			xtype : "checkbox"
		}, {
			allowBlank : false,
			name : 'sinalbaran',
			fieldLabel : _s('Sin albarán'),
			value : true,
			checked: false,
			xtype : "checkbox"
		}];

		var url = site_url('oltp/oltp/compras_periodo');

		var form = Ext.app.formStandarForm({
			icon: 'iconoReportTab',
			controls: controls,
			timeout: false,
			title: _s('Compras en un periodo'),
			url: url
		});

		seccion.store.load();

		form.show();
		return;
	} catch (e) {
		console.dir(e);
	}
})();