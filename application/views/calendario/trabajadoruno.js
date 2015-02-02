(function(){

    var id = Ext.app.createId();
    var title = _s('Consultar calendario');
    var icon = 'iconoTrabajadoresTab';
    var open_id = parseInt("<?php echo $open_id;?>");
    
    var reload = function(url){
        var d = Ext.getCmp(id + "_fecha").getRawValue();
        var year = Ext.getCmp(id + "_year").getValue();
        Ext.app.callRemote({
            params: {
                id: open_id,
                year: year,
                desde: d
            },
            url: url
        })
    };
    
    var controls = [{
        id: id + "_fecha",
        fieldLabel: _s('Desde'),
        value: new Date(),
        startDay: Ext.app.DATESTARTDAY,
        xtype: "datefield"
    }, {
        fieldLabel: _s('Año'),
        xtype: 'textfield',
        value: (new Date()).getYear() + 1900,
        id: id + "_year"
    }];
    
    var buttons = [{
        text: _s('Calendario'),
        iconCls: 'icon-new-calendar',
        handler: function(){
            reload(site_url('calendario/calendario/resumen'))
        }
    }, {
        text: _s('Vacaciones'),
        iconCls: 'icon-new-holidays',
        handler: function(){
            reload(site_url('calendario/vacaciones/resumen'))
        }
    }, {
        text: _s('Resumen'),
        iconCls: 'icon-report',
        handler: function(){
            reload(site_url('calendario/trabajador/resumen'))
        }
    }];
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        buttons: buttons,
        title: _s('Consultar')
    });
    
    form.show();
    return;
})();
