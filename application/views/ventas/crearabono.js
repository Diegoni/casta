(function(){

    var controls = [Ext.app.autocomplete2({
        fieldLabel: _s('Cliente'),
        name: 'cliente',
        anchor: '100%',
        url: site_url('clientes/cliente/search')
    }), {
        xtype: 'numberfield',
        fieldLabel: _s('Importe'),
        value: 0,
        name: 'importe',
        allowNegative: false,
        //width: 200,
        allowDecimals: true,
        decimalPrecision: Ext.app.DECIMALS
    }, new Ext.ux.form.Spinner({
        fieldLabel: _s('Cantidad'),
        name: 'cantidad',
        value: 1,
        width: 60,
        strategy: new Ext.ux.form.Spinner.NumberStrategy()
    }), {
        xtype: 'checkbox',
        name: 'sv',
        fieldLabel: _s('Sin vencimiento')
    }];
    
    var url = site_url('ventas/abono/crear');
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        icon: 'iconoCrearValesTab',
        title: _s('Crear Vales'),
        labelWidth: 100,
        url: url
    });
    
    form.show();
    return;
})();
