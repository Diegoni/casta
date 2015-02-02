(function() {
	var id = Ext.app.createId();
	var stock = '<?php echo $stock;?>' == 'true' ? true : false;
	var controls = [{
		//fieldLabel : _s('ISBNs'),
		hideLabel: true,
		name : 'isbns',
		xtype : "textarea",
		anchor : '100% 100%'
		//grow: true
	}];
	if (stock) controls[controls.length] = new Ext.ux.form.Spinner({
		fieldLabel : _s('Stock'),
		name : 'stock',
		value : 0,
		width : 60,
		strategy : new Ext.ux.form.Spinner.NumberStrategy()
	});
	var url = site_url('<?php echo $url;?>');

	var form = Ext.app.formStandarForm({
		icon : '<?php echo $icon;?>',
		controls : controls,
		timeout : false,
		//height: 400,
		title : _s('<?php echo $title;?>'),
		url : url
	});

	form.show();
	return;
})();
