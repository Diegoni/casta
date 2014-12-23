(function(){

    var form_id = Ext.app.createId();
    
    var serie = new Ext.form.ComboBox(Ext.app.combobox({
        url : site_url('ventas/serie/search'),
        name : 'serie',
        anchor : '100%',
        label : _s('Serie')
    }));

    var controls = [serie, {
        xtype: 'hidden',
        id: 'cache',
        value: <?php echo isset($cache)?($cache?'true':'false'):'false';?>
    }];

    var url = "<?php echo isset($url)?$url:site_url('sys/import/ventas');?>";
    var title = "<?php isset($title)?$title:$this->lang->line('Importar Ventas');?>";
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        url: url,
        timeout: false,
        title: title,
        icon: 'iconoImportarTab'
    });

    serie.store.load({
        callback: function () {
            var d = Ext.app.get_config('bp.tpv.serie');
            d = d.split(';');
            serie.setValue(parseInt(d[0]));
        }
    });
    form.show();
    return;
    
})();
