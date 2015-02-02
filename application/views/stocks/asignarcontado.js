(function(){
    var motivomas = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('stocks/tiporegulacion/entrada'),
        name: 'idmas',
        anchor: '100%',
        allowBlank: false,
        label: _s('Motivo Mas')
    }));
    
    var motivomenos = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('stocks/tiporegulacion/salida'),
        anchor: '100%',
        name: 'idmenos',
        allowBlank: false,
        label: _s('Motivo Menos')
    }));
	
    var controls = [motivomas, motivomenos];
    var url = site_url('stocks/stockcontado/asignar');
    
    var form = Ext.app.formStandarForm({
    	icon: 'icon-accept',
        controls: controls,
        timeout: false,
        title: _s('Asignar stock contado'),
        url: url
    });

    Ext.app.loadStores([{
        store: motivomas.store
    }, {
        store: motivomenos.store
    }]);
    
    form.show();
    return;
    
})();
