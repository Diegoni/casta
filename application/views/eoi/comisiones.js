(function() {
	var url = site_url('eoi/escuela/comisiones');

	var controls = [new Ext.ux.form.Spinner({
        fieldLabel: _s('Mes'),
        name: 'mes',
        value: "<?php echo date('m', time());?>",
        maxValue: 12,
        minValue: 1,
        width: 60,
        strategy: new Ext.ux.form.Spinner.NumberStrategy()
    }), new Ext.ux.form.Spinner({
        fieldLabel: _s('Año'),
        name: 'year',
        value: "<?php echo date('Y', time());?>",
        maxValue: parseFloat("<?php echo date('Y', time());?>"),
        width: 60,
        strategy: new Ext.ux.form.Spinner.NumberStrategy()
    })];

	var form = Ext.app.formStandarForm({
		icon : 'iconoCalculoComisionesTab',
		controls : controls,
		timeout : false,
		title : _s('Cálculo de comisiones'),
		url : url
	});

	form.show();
	return;

})();
