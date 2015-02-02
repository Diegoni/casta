(function() {
	var cliente = '<?php echo $cliente;?>';
	var revista = '<?php echo $revista;?>';
	var proveedor = '<?php echo $proveedor;?>';
	var controls = [{
		xtype : 'checkbox',
		fieldLabel : _s('Activas'),
		name : 'activas',
		checked : true
	}, {
		xtype : 'checkbox',
		fieldLabel : _s('No mostrar Obras'),
		name : 'obras',
		checked : false
	}, {
		xtype : 'hidden',
		name : 'revista',
		value : revista
	}, {
		xtype : 'hidden',
		name : 'cliente',
		value : cliente
	}, {
		xtype : 'hidden',
		name : 'proveedor',
		value : proveedor
	}];

	if(cliente != '' && proveedor == '') {
		controls[controls.length] = new Ext.app.autocomplete2({
			allowBlank : true,
			url : site_url('proveedores/proveedor/search'),
			fieldLabel : _s('Proveedor'),
			name : 'proveedor',
			anchor : '100%'
		})
	}
	if(proveedor != '' && cliente == '') {
		controls[controls.length] = new Ext.app.autocomplete2({
			allowBlank : true,
			url : site_url('clientes/cliente/search'),
			fieldLabel : _s('Cliente'),
			name : 'cliente',
			anchor : '100%'
		})
	}

	var url = site_url('suscripciones/suscripcion/buscar2');

	var form = Ext.app.formStandarForm({
		controls : controls,
		icon : 'iconoSuscripcionesTab',
		timeout : false,
		title : _s('Suscripciones'),
		url : url
	});

	form.show();
	return;
})();
