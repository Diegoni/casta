(function(){
    var id = Ext.app.createId();
    var controls = [{
        fieldLabel: _s('Desde'),
        name: "fecha1",
        value: new Date(),
        startDay: Ext.app.DATESTARTDAY,
        xtype: "datefield"
    }, {
        fieldLabel: _s('Hasta'),
        name: "fecha2",
        value: new Date(),
        startDay: Ext.app.DATESTARTDAY,
        xtype: "datefield"
    }];
    var url = site_url('calendario/calendario/personal_dia');
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        title: _s('Consulta turnos'),
        url: url
    });
    
    form.show();
    return;
})();
