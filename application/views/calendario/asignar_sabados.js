(function() {

	var from = new Date();
	var end = new Date(1900 + from.getYear(), 11, 31);
	var controls = [{
		xtype : 'datefield',
		startDay : Ext.app.DATESTARTDAY,
		name : 'desde',
		value : from,
		allowBlank : false,
		fieldLabel : _s('Desde')
	}, {
		xtype : 'datefield',
		startDay : Ext.app.DATESTARTDAY,
		name : 'hasta',
		value : end,
		allowBlank : false,
		fieldLabel : _s('Hasta')
	}, {
		xtype : 'textfield',
		name : 'horas',
		allowBlank : false,
		fieldLabel : _s('Horas')
	}];

	var form = Ext.app.formStandarForm({
		controls : controls,
		icon : 'icon-calendar-sabados',
		title : _s('asignar-sabados'),
		url : site_url('calendario/trabajador/crear_sabados')
	});
	form.show();

	return;
})();
