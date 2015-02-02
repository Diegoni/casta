(function(){
    var motivos = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('stocks/tiporegulacion/search'),
        label: _s('Motivo 1'),
        name: 'idt',
        anchor: '90%'
    }));
    var motivos2 = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('stocks/tiporegulacion/search'),
        label: _s('Motivo 2'),
        allowBlank: true,
        name: 'idt2',
        anchor: '90%'
    }));
    var controls = [motivos, motivos2, {
        value: DateAdd('d', -1, new Date()),
        fieldLabel: _s('Desde'),
        name: 'desde',
        allowBlank: false,
        startDay: Ext.app.DATESTARTDAY,
        xtype: "datefield"
    }, {
        fieldLabel: _s('Hasta'),
        value: new Date(),
        name: 'hasta',
        startDay: Ext.app.DATESTARTDAY,
        allowBlank: false,
        xtype: "datefield"
    }, new Ext.ux.form.Spinner({
        fieldLabel: _s('Cantidad'),
        name: "cantidad",
        value: 1,
        width: 60,
        strategy: new Ext.ux.form.Spinner.NumberStrategy()
    })];
    var url = site_url('stocks/arreglostock/consultar');
    
    var form = Ext.app.formStandarForm({
        controls: controls,
		icon: 'icon-search',
        timeout: false,
        title: _s('Consultar ajustes de stock'),
        url: url
    });
    motivos.store.load();
    motivos2.store.load();
    form.show();
    return;
    
})();
