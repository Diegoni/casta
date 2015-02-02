(function(){
    var id = Ext.app.createId();
    var controls = [{
        id: id + "_fecha1",
        fieldLabel: _s('Desde'),
        name: 'fecha1',
        value: new Date(),
        startDay: Ext.app.DATESTARTDAY,
        xtype: "datefield"
    }, {
        id: id + "_fecha2",
        fieldLabel: _s('Hasta'),
        name: 'fecha2',
        value: new Date(),
        startDay: Ext.app.DATESTARTDAY,
        xtype: "datefield"
    }];
    var url = "<?php echo $url;?>";
    
    var form = Ext.app.formStandarForm({
    	icon: 'iconoReportTab',
    	timeout: false,
        controls: controls,
        title: "<?php echo $title; ?>",
        url: url
    });
    
    form.show();
    return;
})();
