(function() {
	var controls = [{
		allowBlank : true,
		name : 'desde',
		fieldLabel : _s('Desde'),
		value : new Date(),
		startDay : Ext.app.DATESTARTDAY,
		xtype : "datefield"
	}, {
		allowBlank : true,
		name : 'hasta',
		fieldLabel : _s('Hasta'),
		value : new Date(),
		startDay : Ext.app.DATESTARTDAY,
		xtype : "datefield"
	}, {
		allowBlank : false,
		name : 'test',
		fieldLabel : _s('Modo prueba'),
		value : true,
		checked: true,
		xtype : "checkbox"
	}];

	var url = site_url("sys/bp2lc/run");
	var form = Ext.app.formStandarForm({
		icon : 'iconoForzarTraspasosContabilidadTab',
		controls : controls,
		title : _s('Forzar traspaso'),
		timeout : false,
		url : url
	});

	form.show();
})();
