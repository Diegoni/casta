(function(){

    var form_id = Ext.app.createId();

    var concurso =<?php echo (isset($concurso))?$concurso:"null";?>;
    var id =<?php echo (isset($id))?"'{$id}'":"null";?>;

    var concursos = (concurso==null)?new Ext.form.ComboBox(Ext.app.combobox({
        url : site_url('concursos/concurso/search'),
        name : 'concurso',
        anchor : '100%',
        label : _s('Concurso')
    })):{
        xtype: 'hidden',
        name: 'concurso',
        value: concurso
    };
    
    
    var seccion = new Ext.form.ComboBox(Ext.app.combobox({
        url : site_url('generico/seccion/search'),
        name : 'seccion',
        anchor : '100%',
        label : _s('Sección')
    }));

    var direccionenvio = new Ext.form.ComboBox(Ext.app.combobox({
        url : site_url('proveedores/perfilproveedor/get_list'),
        anchor : '100%',
        extrafields : ['nIdPais'],
        label : _s('nIdDireccionEnvio'),
        name : 'direccion'
    }));

    direccionenvio.store.baseParams = {
        id: parseInt(Ext.app.get_config('bp.compras.direcciones')),
        tipo: 'D'
    }

    var controls = [{
            xtype: 'hidden',
            name: 'id',
            value: id
        }, concursos, seccion, direccionenvio, { 
        xtype: 'textfield',
        name: 'ref',
        anchor : '100%',
        allowBlank: true,
        value: Ext.app.get_config('bp.concursos.referenciadefecto'),
        id: form_id + '_ref',
        fieldLabel: _s('cRefProveedor')
        },
        Ext.app.formHtmlEditor({
            name: 'nota',
            id: form_id + '_nota',
            hideLabel : true,
            value : Ext.app.get_config('bp.concursos.notadefecto'),
            height: 100,
            anchor: '100%'
            })[0]
        ];
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        url: site_url('concursos/concurso/pedirproveedor'),
        timeout: false,
        title: _s('Crear pedidos proveedor'),
        icon: 'iconoConcursosPedirProveedorTab',
        fn_ok : function(res) {
            try {
                Ext.app.set_config('bp.concursos.direccionenviodefecto', direccionenvio.getValue(), 'user');
                Ext.app.set_config('bp.concursos.secciondefecto', seccion.getValue(), 'user');
                Ext.app.set_config('bp.concursos.concursodefecto', concursos.getValue(), 'user');
                Ext.app.set_config('bp.concursos.referenciadefecto', Ext.getCmp(form_id + '_ref').getValue(), 'user');
                Ext.app.set_config('bp.concursos.notadefecto', Ext.getCmp(form_id + '_nota').getValue(), 'user');
            } catch(e) {
                //console.dir(e);
            }
            var f = Ext.getCmp('<?php echo $cmpid;?>');
            if (f!=null)
                f.refresh();
        }
    });

    direccionenvio.store.load({
        callback: function () {
            var d = parseInt(Ext.app.get_config('bp.concursos.direccionenviodefecto'));
            if (d > 0)
                direccionenvio.setValue(parseInt(d));
        }
    });
    seccion.store.load({
        callback: function () {
            var d = parseInt(Ext.app.get_config('bp.concursos.secciondefecto'));
            if (d > 0)
                seccion.setValue(parseInt(d));
        }
    });

    if (concurso == null) {
        concursos.store.load({
            callback: function () {
                var d = parseInt(Ext.app.get_config('bp.concursos.concursodefecto'));
                if (d > 0)
                    concursos.setValue(parseInt(d));
            }
        });
    }

    form.show();
    return;
    
})();
