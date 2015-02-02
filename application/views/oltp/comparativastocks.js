(function() {
	var una = "<?php echo isset($una)?(($una)?'true':'false'):'true';?>" == 'true';

	var seccion = new Ext.form.ComboBox(Ext.app.combobox({
		url: site_url('generico/seccion/search'),
		label: _s('Seccion'),
		name: 'id',
		anchor: '90%'
	}));

	var controls = [{
		value: new Date(),
		fieldLabel: _s((una)?'Fecha':'Desde'),
		name: 'fecha1',
		allowBlank: false,
		startDay: Ext.app.DATESTARTDAY,
		xtype: "datefield"
	},{
		fieldLabel: _s('Hasta'),
		value: new Date(),
		name: 'fecha2',
		hidden : una,
		allowBlank: false,
		startDay: Ext.app.DATESTARTDAY,
		xtype: "datefield"
	}, seccion];

	seccion.store.load();
	var url = "<?php echo $url;?>";

	var form = Ext.app.formStandarForm({
		icon: 'iconoReportTab',
		controls: controls,
		timeout: false,
		title: "<?php echo $title;?>",
		url: url
	});

	form.show();
	return;
})();