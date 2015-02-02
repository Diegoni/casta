(function(){
    var origen = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/seccion/search'),
        label: _s('Origen'),
        name: 'ido',
        anchor: '90%'
    }));
    var destino = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/seccion/search'),
        label: _s('Destino'),
        name: 'idd',
        anchor: '90%'
    }));
    var controls = [origen, destino, {
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
    }];
	
    var url = site_url('catalogo/movimiento/consultar');
    
    var form = Ext.app.formStandarForm({
        controls: controls,
		icon: 'icon-search',
        timeout: false,
        title: _s('Consultar ajustes de stock'),
        url: url
    });
    origen.store.load();
    destino.store.load();
    form.show();
    return;
    
})();
