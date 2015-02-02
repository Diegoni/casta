(function(){

    var form_id = Ext.app.createId();
    
    var store2 = new Ext.data.ArrayStore({
        fields: ['id', 'text'],
        data: [[_s('simbolo-firme'), _s('Firme')], [_s('simbolo-deposito'), _s('Depósito')]]
    });
    
    var tipo = new Ext.form.ComboBox({
        store: store2,
        displayField: 'text',
        valueField: 'id',
        typeAhead: true,
        mode: 'local',
        forceSelection: true,
        triggerAction: 'all',
        emptyText: _s('Seleccione'),
        selectOnFocus: true,
        name: 'simbolo',
        hiddenName: 'simbolo',
        fieldLabel: _s('Tipo')
    });

	tipo.setValue(_s('simbolo-firme'));

    var t = Ext.app.combobox({
        url : site_url('catalogo/grupoetiqueta/printer?list=true'),
        name : 'report',
        allowBlank: true,
        anchor : '100%',
        label : _s('Formato')
    });
    //t['forceSelection'] = false;
    var report = new Ext.form.ComboBox(t);

    var fn_imprimir = function (id, t, c, idg) {
        var r = report.getValue();
        var url = site_url('catalogo/grupoetiqueta/imprimir_una/' + id + '/' + t + '/' + c + '/' + r);
        if (isNumber(idg) && (idg > 0)) {
            try {
                Ext.app.callRemote({
                    url: url,
                    nomsg: true,
                    params: {
                        idg: idg
                    }
                });
            } catch (e) {}
        } else {
            Ext.app.printLabels(url, _s('Imprimir Etiquetas'));
        }
        ctl.setValue();
        cantidad.reset();
        ctl.reset();
        ctl.focus();
    };

    var act_idg = null;
    var select = function(id){
        var ct = Ext.getCmp(form_id + '_cantidad');
        var c = ct.getValue();
        if (c <= 0)
            c = 1;
        var t = tipo.getValue();
        if (t == null || t == '') 
            t = _s('simbolo-firme');

        var idg = grupos.getValue();
        if (!isNumber(idg) && act_idg > 0) idg = act_idg;

        if (!isNumber(idg) && (idg.trim() != '')) {
            // Crea el grupo
            Ext.app.callRemote({
                url: site_url('catalogo/grupoetiqueta/upd'),
                params: {
                    cDescripcion: idg
                },
                nomsg: true,
                fnok: function(res) {
                    idg = res.id;
                    act_idg = idg;
                    fn_imprimir(id, t, c, idg);
                }
            });
        }
        else {
            act_idg = idg;
            //grupos.setValue(idg);
            fn_imprimir(id, t, c, idg);
        }
    }

    var cfg = Ext.app.autocomplete({
        allowBlank: false,
        url: site_url('catalogo/articulo/search'),
        label: _s('Artículo'),
        name: 'idl2',
        fnselect: select,
        anchor: '90%'
    });
    cfg['id'] = form_id + '_idl2';

    var ctl = new Ext.form.ComboBox(cfg);

    var cantidad = new Ext.ux.form.Spinner({
        fieldLabel: _s('Cantidad'),
        enableKeyEvents: true,
        id: form_id + "_cantidad",
        value: 1,
        width: 60,
        listeners: [{
            'keypress': function(f, e){
                if (e.getKey() == e.ENTER) {
                    ctl.focus();
                }
            }
        }],

        strategy: new Ext.ux.form.Spinner.NumberStrategy()
    });

    var grupos = new Ext.form.ComboBox(Ext.app.autocomplete({
        allowBlank: false,
        url: site_url('catalogo/grupoetiqueta/search'),
        label: _s('Grupo'),
        name: 'idg',
        anchor: '90%'
    }));

    var controls = [tipo, cantidad, ctl, grupos, report];

    var form = Ext.app.formStandarForm({
        controls: controls,
        timeout: false,
        disableok: true,
        title: _s('Imprimir Etiquetas'),
        icon: 'icon-etiquetas',
        focus: form_id + '_idl2',
        buttons: [{
            text: _s('Imprimir'),
            iconCls: 'icon-print',
            handler: function(b){
                var idg = grupos.getValue();
                var r = report.getValue();
                if (!isNumber(idg) && act_idg > 0) idg = act_idg;
                if (isNumber(idg) && (idg > 0)) {
                    var url = site_url('catalogo/grupoetiqueta/imprimir2/' + idg);
                    Ext.app.callRemote({
                        url: url
                    });
                }
            }
        }, {
            text: _s('Limpiar'),
            iconCls: 'icon-trash',
            handler: function(){
                var idg = grupos.getValue();
                if (!isNumber(idg) && act_idg > 0) idg = act_idg;
                if (isNumber(idg) && (idg > 0)) {
                   var url = site_url('catalogo/grupoetiqueta/del/' + idg);
                    Ext.app.callRemote({
                        url: url,
                        fnok : function() {
                            grupos.setValue();
                            act_idg = null;
                        }
                    });
                }
            }
        }]
    });
    report.store.load();
    form.show();
    return;
})();
