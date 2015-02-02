(function(){
    var tipos = new Ext.form.ComboBox(Ext.app.combobox({
        url : site_url('etiquetas/etiquetatipo/search'),
        name : 'tipo',
        allowBlank: false,
        anchor : '100%',
        label : _s('nIdTipo')
    }));

    var formatos = new Ext.form.ComboBox(Ext.app.combobox({
        url : site_url('etiquetas/etiquetaformato/search'),
        name : 'formato',
        anchor : '100%',
        allowBlank: false,
        label : _s('Formato')
    }));

    var controls = [tipos, formatos, 
            new Ext.ux.form.Spinner({
                    fieldLabel: _s('Fila'),
                    name: 'row',
                    value: 1,
                    width: 60,
                    strategy: new Ext.ux.form.Spinner.NumberStrategy({minValue: 1})
                }),
            new Ext.ux.form.Spinner({
                    fieldLabel: _s('Columna'),
                    name: 'column',
                    value: 1,
                    width: 60,
                    strategy: new Ext.ux.form.Spinner.NumberStrategy({minValue: 1})
                }),
            new Ext.ux.form.Spinner({
                    fieldLabel: _s('Cantidad'),
                    name: 'qt',
                    value: 1,
                    width: 60,
                    strategy: new Ext.ux.form.Spinner.NumberStrategy({minValue: 1})
                }),
                {
                    xtype : 'hidden',
                    name : 'id',
                    value : '<?php echo $id; ?>'
            }];

    var url = '<?php echo $url; ?>';

    var form = Ext.app.formStandarForm({
        controls : controls,
        title : _s('Imprimir etiqueta'),
        icon : 'icon-label-form',
        url : url,
        fn_ok : function(res) {
            Ext.app.set_config('bp.print_label.tipo', tipos.getValue(), 'user');
            Ext.app.set_config('bp.print_label.formato', formatos.getValue(), 'user');
        }
    }); 

    tipos.store.load({
        callback: function() {
            var v = Ext.app.get_config('bp.print_label.tipo', 'user');
            if (v != null && v != '')
                tipos.setValue(parseInt(v));
        }
    });
    formatos.store.load({
        callback: function() {
            var v = Ext.app.get_config('bp.print_label.formato', 'user');
            if (v != null && v != '')
                formatos.setValue(parseInt(v));
        }
    });
    form.show();
    return;
    
})();
