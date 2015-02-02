(function(){
    var caja = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('ventas/caja/search'),
        label: _s('Caja'),
        name: 'caja',
        anchor: '90%'
    }));

    var controls = [{
        value: DateAdd('d', -1, new Date()),
        fieldLabel: _s('Desde'),
        name: 'fecha1',
        allowBlank: false,
        startDay: Ext.app.DATESTARTDAY,
        xtype: "datefield"
    }, {
        fieldLabel: _s('Hasta'),
        value: DateAdd('d', -1, new Date()),
        name: 'fecha2',
        startDay: Ext.app.DATESTARTDAY,
        allowBlank: false,
        xtype: "datefield"
    }, caja];
    var url = site_url('oltp/oltp/caja_dia_modo');
    caja.store.load();
    
    var form = Ext.app.formStandarForm({
    	icon: 'iconoReportTab',
        controls: controls,
        timeout: false,
        title: _s('Cobros por día, caja y modo'),
        url: url
    });
    
    form.show();
    return;
    
})();
