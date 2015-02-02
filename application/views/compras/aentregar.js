(function() {
	var seccion = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('generico/seccion/search'),
		anchor : "90%",
		allowBlank : true,
		name : 'ids',
		label : _s('Seccion')
	}));
    var ctl = new Ext.form.ComboBox(Ext.app.autocomplete({
        allowBlank: true,
        url: site_url('proveedores/proveedor/search'),
        label: _s('Proveedores'),
        name: 'pv',
        anchor: '90%'
    }));
	var controls = [seccion, ctl, {
		xtype : 'checkbox',
		fieldLabel : _s('Devoluciones'),
		name : 'devolucion',
		checked : true
	}, {
		xtype : 'checkbox',
		fieldLabel : _s('Líneas'),
		name : 'lineas',
		checked : true
	}];

	var url = site_url('compras/devolucion/a_entregar2');

	var form = Ext.app.formStandarForm({
		controls : controls,
		icon : 'icon-report',
		timeout: false,
		title : _s('a-entregar'),
		url : url
	});
	seccion.store.load();

	form.show();
	return;
})();
