(function() {
	var title = "<?php echo $title;?>";
	var icon = "<?php echo $icon;?>";
	if(title == '')
		title = _s('Fecha');
	if(icon == '')
		icon = 'iconoReportTab';
	var url = "<?php echo $url;?>";

	var controls = [{
		fieldLabel : _s('Fecha'),
		name : 'fecha',
		id: 'fecha',
		value : new Date(),
		startDay : Ext.app.DATESTARTDAY,
		xtype : "datefield"
	}];

	var form = Ext.app.formStandarForm({
		icon : icon,
		controls : controls,
		timeout : false,
		title : title,
		url : url
	});

	form.show();
	return;

})();
