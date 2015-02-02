(function() {

	try {
		var seccion = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('generico/seccion/search'),
			anchor : "90%",
			allowBlank : true,
			name : 'seccion',
			label : _s('Seccion')
		}));

		var controls = [{
			allowBlank : false,
			name : 'fecha1',
			fieldLabel : _s('Desde'),
			value : new Date(),
			startDay : Ext.app.DATESTARTDAY,
			xtype : "datefield"
		}, {
			allowBlank : false,
			name : 'fecha2',
			fieldLabel : _s('Hasta'),
			value : new Date(),
			startDay : Ext.app.DATESTARTDAY,
			xtype : "datefield"
		}, seccion, {
	        fieldLabel: _s('Quitar Sant Jordi'),
	        xtype: 'checkbox',
	        name: 'sj',
	        checked: true,
	        value: true
    	}, {
	        fieldLabel: _s('Múltiple'),
	        xtype: 'textfield',
	        name: 'multi'
    	}];

		var url = "<?php echo $url; ?>";
		var form = Ext.app.formStandarForm({
			icon : 'iconoReportTab',
			controls : controls,
			title : "<?php echo $title; ?>",
			timeout : false,
			url : url
		});

		seccion.store.load();

		form.show();
	} catch (e) {
		console.dir(e);
	}
})();
