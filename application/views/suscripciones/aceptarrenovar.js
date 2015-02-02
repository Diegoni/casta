(function() {

	var id = "<?php echo $id;?>";
	var ref = "<?php echo isset($ref)?(($ref)?'true':'false'):'true';?>" == 'true';

	var medio = new Ext.form.ComboBox(Ext.app.combobox({
		url : site_url('suscripciones/mediorenovacion/search'),
		allowBlank : false,
		name : 'modo',
		anchor : '100%',
		label : _s('Medio')
	}));

	var controls = [{
		xtype : 'hidden',
		name : 'id',
		value : id
	}, {
		fieldLabel : _s('Responsable'),
		xtype : 'textfield',
		allowBlank : false,
		name : 'contacto',
		selectOnFocus : true,
		anchor : '100%'
	}, {
		fieldLabel : _s('Fecha'),
		value : new Date(),
		name : 'fecha',
		startDay : Ext.app.DATESTARTDAY,
		allowBlank : false,
		xtype : "datefield"
	}, medio, {
		fieldLabel : _s('Referencia'),
		xtype : 'textfield',
		name : 'ref',
		hidden : !ref,
		selectOnFocus : true,
		anchor : '100%'
	}];

	var url = "<?php echo $url;?>";

	var form = Ext.app.formStandarForm({
		controls : controls,
		title : "<?php echo $title;?>",
		icon : "<?php echo $icon;?>",
		labelWidth : 100,
		url : url,
		fn_ok : function() {
			var f = Ext.getCmp('<?php echo $cmpid;?>');
			f.refresh();
		}
	});

	medio.store.load();
	form.show();
	return;
})();
