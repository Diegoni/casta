(function() {

	try {

		var seccion = new Ext.form.ComboBox(Ext.app.combobox({
			url: site_url('generico/seccion/search'),
			label: _s('Seccion'),
			name: 'idseccion',
			anchor: '90%'
		}));

		var comparaciones = new Ext.data.SimpleStore({
			fields: ['v'],
			data: [['>'], ['>='], ['='], ['<='], ['<']]
		});

		var controls = [{
			value: DateAdd('d', -1, new Date()),
			fieldLabel: _s('Desde'),
			name: 'fecha1',
			allowBlank: false,
			startDay: Ext.app.DATESTARTDAY,
			xtype: "datefield"
		},{
			fieldLabel: _s('Hasta'),
			value: DateAdd('d', -1, new Date()),
			name: 'fecha2',
			allowBlank: false,
			startDay: Ext.app.DATESTARTDAY,
			xtype: "datefield"
		},{
			xtype: 'compositefield',
			fieldLabel: _s('Descuento'),
			msgTarget: 'side',
			anchor: '-20',
			defaults: {
				flex: 1
			},
			items: [{
				fieldLabel: _s('Descuento'),
				name: 'cmpdto',
				xtype: 'combo',
				store: comparaciones,
				displayField: 'v',
				typeAhead: true,
				mode: 'local',
				forceSelection: true,
				triggerAction: 'all',
				width: 40,
				selectOnFocus: true
			},{
				name: 'dto',
				value: '0',
				xtype: "uxspinner",
				width: Ext.app.TAM_COLUMN_NUMBER,
				strategy: new Ext.ux.form.Spinner.NumberStrategy()
			}]
		},{
			xtype: 'compositefield',
			fieldLabel: _s('Margen'),
			msgTarget: 'side',
			anchor: '-20',
			defaults: {
				flex: 1
			},
			items: [{
				xtype: 'combo',
				name: 'cmpdto',
				fieldLabel: _s('Margen'),
				store: comparaciones,
				displayField: 'v',
				typeAhead: true,
				mode: 'local',
				forceSelection: true,
				triggerAction: 'all',
				width: 40,
				selectOnFocus: true
			},{
				value: '0',
				name: 'dto',
				xtype: "uxspinner",
				width: Ext.app.TAM_COLUMN_NUMBER,
				strategy: new Ext.ux.form.Spinner.NumberStrategy()
			}]
		}, seccion, new Ext.app.autocomplete2({
			allowBlank: true,
			url: site_url('clientes/cliente/search'),
			fieldLabel: _s('Cliente'),
			name: 'idcliente',
			anchor: '90%'
		})];

		var url = site_url('oltp/oltp/ventas_periodo');

		var form = Ext.app.formStandarForm({
			icon: 'iconoReportTab',
			controls: controls,
			timeout: false,
			title: _s('Ventas en un Periodo'),
			url: url
		});

		seccion.store.load();

		form.show();
		return;
	} catch (e) {
		console.dir(e);
	}
})();