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
			name : 'fecha',
			fieldLabel : _s('Fecha'),
			value : new Date(),
			startDay : Ext.app.DATESTARTDAY,
			xtype : "datefield"
		}, seccion];

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
