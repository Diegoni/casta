(function(){

    var tpv = "<?php echo isset($tpv)?(($tpv)?'true':'false'):'true';?>" == 'true';
    
    var caja = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('ventas/caja/search'),
        name: 'caja',
        anchor: '100%',
        label: _s('Caja')
    }));
    
    var seccion = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/seccion/search'),
        name: 'seccion',
        anchor: '100%',
        label: _s('Sección')
    }));
    
    var descuento = new Ext.form.NumberField({
        allowBlank: true,
        allowNegative: false,
        allowDecimals: true,
        minValue: 0,
        maxValue: 100,
        name: 'dto',
        decimalPrecision: Ext.app.DECIMALS,
        fieldLabel: _s('Descuento')
    });
    
    var cliente = Ext.app.autocomplete2({
        fieldLabel: _s('Cliente'),
        name: 'cliente',
        anchor: '100%',
        url: site_url('clientes/cliente/search')
    });
    
    var controls = (tpv) ? [cliente, seccion, caja, descuento] : [cliente, seccion, descuento];
    
    var url = site_url('ventas/albaransalida/liquidarstock');
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        title: _s('Liquidar stock de una sección'),
        labelWidth: 100,
        url: url,
        fn_ok: function(res){
            url = site_url('ventas/albaransalida/index/' + res.id);
            Ext.app.execCmd({
                url: url
            });
            //console.dir(res);
        }
    });
    
    Ext.app.loadStores([{
        store: seccion.store
    }, {
        store: caja.store
    }]);
    form.show();
    return;
})();
