(function() {
	var form_id = Ext.app.createId();

	var preview = "<?php echo $preview;?>";
	var next = "<?php echo $next;?>";
	var file = "<?php echo $file;?>";

	var controls = [/*new Ext.ux.form.Spinner({
		fieldLabel: _s('1ª Fila'),
		name: 'fila1',
		value: 1,
		width: 60,
		strategy: new Ext.ux.form.Spinner.NumberStrategy()
	}),*/new Ext.ux.form.Spinner({
		fieldLabel: _s('ISBN/EAN'),
		name: 'isbn',
		value: 1,
		width: 60,
		strategy: new Ext.ux.form.Spinner.NumberStrategy()
	}),new Ext.ux.form.Spinner({
		fieldLabel: _s('Precio S/I'),
		name: 'precio',
		value: 3,
		width: 60,
		strategy: new Ext.ux.form.Spinner.NumberStrategy()
	}),new Ext.ux.form.Spinner({
		fieldLabel: _s('PVP'),
		name: 'pvp',
		value: 4,
		width: 60,
		strategy: new Ext.ux.form.Spinner.NumberStrategy()
	}),{
		xtype:'hidden',
		name:'next',
		value: next
	},{
		xtype:'hidden',
		name:'file',
		value: file
	},{
		//xtype: 'iframepanel',
		height: Ext.app.REPOINFOHEIGHT,
		width: Ext.app.PEDIRWIDTH - 35,
		autoScroll: true,
		id: form_id + '_info'
	}];

	var url = site_url('catalogo/articulo/precios');

	var form = Ext.app.formStandarForm({
		controls: controls,
		title: _s('Seleccionar columnas y campos'),
		icon: 'icon-excel',
		timeout: false,
		width: Ext.app.PEDIRWIDTH,
		url: url,
		show: function() {
			var detailEl = Ext.getCmp(form_id + "_info").body;
			detailEl.applyStyles({
				'background-color': '#FFFFFF'
			});
			detailEl.update(preview);
		}
	});

	form.show();
	return;
})();