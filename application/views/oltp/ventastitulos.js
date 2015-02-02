(function(){
    var seccion = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/seccion/search'),
        label: _s('Sección'),
        name: 'id',
        anchor: '90%'
    }));
    var id = Ext.app.createId();
    var controls = [{
        id: id + "_fecha1",
        fieldLabel: _s('Desde'),
        name: 'fecha1',
        value: new Date(),
        allowBlank: false,
        startDay: Ext.app.DATESTARTDAY,
        xtype: "datefield"
    }, {
        id: id + "_fecha2",
        fieldLabel: _s('Hasta'),
        name: 'fecha2',
        allowBlank: false,
        value: new Date(),
        startDay: Ext.app.DATESTARTDAY,
        xtype: "datefield"
    }, {
        value: '1',
        xtype: "uxspinner",
        width: 60,
        name: 'min',
        fieldLabel: _s('Mínimo'),
        strategy: new Ext.ux.form.Spinner.NumberStrategy()
    }, seccion];
    
    var url = site_url('oltp/oltp/ventas_titulos');
    
    var form = Ext.app.formStandarForm({
    	icon: 'iconoReportTab',
        controls: controls,
        title: _s('Ventas por Títulos'),
        url: url
    });
    seccion.store.load();
    
    form.show();
    return;
})();
