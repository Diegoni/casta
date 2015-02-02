(function(){
    var controls = [{
        value: new Date(),
        fieldLabel: _s('Fecha'),
        name: 'fecha',
        allowBlank: false,
        startDay: Ext.app.DATESTARTDAY,
        xtype: "datefield"
   }];
    var url = site_url('compras/divisacamara/actualizar');
    
    var form = Ext.app.formStandarForm({
    	icon: 'iconoCambiosDivisaCamaraTab',
        controls: controls,
        timeout: false,
        title: _s('Cambios divisa de la cámara'),
        url: url,
        fn_ok: function ()
        {
            Ext.getCmp('<?php echo $cmpid;?>').store.load();
        }
    });
    
    form.show();
    return;
})();
