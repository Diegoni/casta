(function(){

    var divisa1 = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/divisa/search'),
        name: 'divisa1',
        label: _s('Divisa Origen')
    }));
    
    var divisa2 = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/divisa/search'),
        name: 'divisa2',
        label: _s('Divisa Final')
    }));
    
    var controls = [{
        xtype: 'compositefield',
        fieldLabel: _s('Importe'),
        msgTarget: 'side',
        anchor: '-20',
        defaults: {
            flex: 1
        },
        items: [{
            xtype: 'textfield',
            name: 'precio',
            allowBlank: false,
            anchor: '50%',
            fieldLabel: _s('Importe')
        }, divisa1]
    }, divisa2];
    
    var url = site_url('ventas/cambiodivisa/cambio');
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        title: _s('Cambio Divisa'),
        labelWidth: 100,
        url: url
    });
    
    Ext.app.loadStores([{
        store: divisa1.store
    }, {
        store: divisa2.store
    }], function(){
        divisa1.setValue("<?php echo $this->config->item('bp.divisa.default');?>");
        divisa2.setValue("<?php echo $this->config->item('bp.divisa.default');?>");
    });
    form.show();
    return;
})();
