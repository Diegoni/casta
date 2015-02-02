(function(){
    var t = Ext.app.combobox({
        url : site_url('etiquetas/etiqueta/grupos'),
        name : 'grupo',
        allowBlank: false,
        anchor : '100%',
        label : _s('Grupo')
    });
    t['forceSelection'] = false;
    var grupos = new Ext.form.ComboBox(t);

    var controls = [grupos,
        {
            xtype : 'hidden',
            name : 'id',
            value : '<?php echo $id; ?>'
        }];

    var url = '<?php echo $url; ?>';

    var form = Ext.app.formStandarForm({
        controls : controls,
        title : _s('Encolar etiqueta'),
        icon : 'icon-label-cola-form',
        url : url,
        fn_ok : function(res) {
            Ext.app.set_config('bp.print_label.cola', grupos.getValue(), 'user');
        }
    }); 

    grupos.store.load({
        callback: function() {
            var v = Ext.app.get_config('bp.print_label.cola', 'user');
            if (v != null && v != '')
                grupos.setValue(v);
        }
    });
    form.show();
    return;
    
})();
