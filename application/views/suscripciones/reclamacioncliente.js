(function() {

	var id = "<?php echo $id;?>";
	var ref = "<?php echo isset($ref)?(($ref)?'true':'false'):'true';?>" == 'true';

	var controls = [{
		xtype : 'hidden',
		name : 'id',
		value : id
	}, {
		fieldLabel : _s('Volumen'),
		xtype : 'textfield',
		hidden : !ref,
		name : 'volumen',
		selectOnFocus : true,
		anchor : '100%'
	}, Ext.app.formHtmlEditor({
            name: 'texto',
			hideLabel : true,
            anchor: '100% 91%'
        })[0]
	];

	var url = "<?php echo $url;?>";

	var form = Ext.app.formStandarForm({
		controls : controls,
		title : "<?php echo $title;?>",
		icon : "iconoSuscripcionesReclamacionesTab",
		labelWidth : 100,
		url : url,
		fn_ok : function(res) {			
			Ext.app.execCmd({
				url : site_url('suscripciones/reclamacion/index/' + res.id)
			});
			var f = Ext.getCmp('<?php echo $cmpid;?>');
			if (f!=null)			
				f.refresh();
		}
	});

	form.show();
	return;
})();
