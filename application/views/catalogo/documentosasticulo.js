(function(){

    var idl = '<?php if (!empty($idl)) echo $idl;?>';
    var fechas = '<?php echo (isset($fechas))?($fechas?"true":"false"):"true";?>';
    var now = new Date();
    var controls = [];
    var name = '<?php echo isset($name)?$name:"idl";?>';
    var tipo = '<?php echo isset($tipo)?$tipo:"";?>';
    
    var form_id = Ext.app.createId();
    controls[controls.length] = {
        name: name,
        id: form_id + '_idl',
        value: idl,
        xtype: 'hidden'
    }
    
    if (idl == '') {
        var ctl = new Ext.form.ComboBox(Ext.app.autocomplete({
            allowBlank: false,
            url: site_url('catalogo/articulo/search'),
            label: _s('Artículo'),
            name: 'idl2',
            anchor: '90%'
        }));
        controls[controls.length] = ctl;
    }
    if (fechas == 'true') {
        controls[controls.length] = {
            fieldLabel: _s('Desde'),
            name: 'fecha1',
            value: Date.DateAdd('m', -6, now),
            allowBlank: false,
            startDay: Ext.app.DATESTARTDAY,
            xtype: "datefield"
        }
        controls[controls.length] = {
            fieldLabel: _s('Hasta'),
            allowBlank: false,
            name: 'fecha2',
            value: now,
            startDay: Ext.app.DATESTARTDAY,
            xtype: "datefield"
        }
    }
    if (tipo != '') 
        controls[controls.length] = {
            name: 'tipo',
            value: tipo,
            xtype: "hidden"
        }
    
    var url = '<?php echo $url;?>';
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        timeout: false,
        title: '<?php echo $title;?>',
		icon: 'icon-documents',
        url: url,
        fn_pre: function(){
            if (idl == '') {
                var c = Ext.getCmp(form_id + '_idl');
                c.setValue(ctl.getValue());
                //console.log('form ' + ctl.getValue());
            }
        }
    });
    
    form.show();
    return;
    
})();
