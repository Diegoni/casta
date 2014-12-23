(function(){

    var form_id = Ext.app.createId();

    var url = "<?php echo isset($url)?$url:site_url('sys/import/catalogo');?>";
    
    var seccion = new Ext.form.ComboBox(Ext.app.combobox({
        url : site_url('generico/seccion/search'),
        name : 'seccion',
        anchor : '100%',
        label : _s('Sección')
    }));

    var controls = [seccion, {
        xtype: 'hidden',
        id: 'cache',
        value: <?php echo isset($cache)?($cache?'true':'false'):'false';?>
    }];
    
    var title = "<?php isset($title)?$title:$this->lang->line('Importar Catalogo');?>";
    var form = Ext.app.formStandarForm({
        controls: controls,
        url: url,
        timeout: false,
        title: title,
        icon: 'iconoImportarTab'
    });

    Ext.app.comboAdd(seccion.store, -2, _s('[TODAS]'));
    seccion.store.load({
        callback: function () {
            var d = Ext.app.get_config('bp.factura.secciones.defecto');
            d = d.split(';');
            seccion.setValue(parseInt(d[0]));
        }
    });
    form.show();
    return;
    
})();
