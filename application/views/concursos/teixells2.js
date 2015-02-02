(function() {

	var form_id = Ext.app.createId();

    var store2 = new Ext.data.ArrayStore({
        fields: ['id', 'text'],
        data: [
            [1, _s('tx-general')], 
            [2, _s('tx-porqueres-adults')], 
            [3, _s('tx-porqueres-adults-prim')], 
            [4, _s('tx-porqueres-adults-superprim')], 
            [5, _s('tx-porqueres-infants')],
            [6, _s('tx-porqueres-infants-prim')],
            [7, _s('tx-porqueres-imaginacio')],
            [8, _s('tx-belianes')],
        ]
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
        name: 'tipo',
        hiddenName: 'tipo',
        fieldLabel: _s('Tipo')
    });
    tipo.setValue(1);
    
    var txtISBN = new Ext.form.TextField({
    	fieldLabel: _s('Código'),
        enableKeyEvents : true
    }); 

    var text = '';
    txtISBN.on('specialkey',
        function(o, e){
            if (e.getKey() == e.ENTER){
                var detailEl = Ext.getCmp(form_id + '_html').body;

                Ext.app.callRemote({
                    url: site_url('concursos/concurso/add_teixell'),
                    params: {
                        code: txtISBN.getValue(),
                        cantidad: cantidad.getValue(),
                        tipo: tipo.getValue()
                    },
                    fnok: function (res) {
                        detailEl.applyStyles({
                            'background-color': '#FFFFFF'
                        });
                        /*var text = '<div style="font-size: 200%;color:green;align:center;">' + res.text + '</div>';
                        '<div style="font-size: 150%;color:black;align:center;">' + res.titulo + '</div>';*/
                        text = '[' + cantidad.getValue() + ']' + txtISBN.getValue() + '(' + tipo.getRawValue() + ')<br/>' + text;
                        detailEl.update(text);
                        txtISBN.setValue(null);
                        cantidad.reset();
                        ++count;
                        ejemplares.setValue(count);
                    }                            
                });
            }
        },
        this
    );
    var count = 0;
    var ver = function() {
        Ext.app.callRemote({
            url: site_url('concursos/concurso/get_teixells'),
            timeout: false,
            fnok: function(res) {
                    var detailEl = Ext.getCmp(form_id + '_html').body;  
                    text = "";
                    count = 0;
                    Ext.each(res.value_data, function (item) {
                        text = item.cTexto + '(' + item.nTipo + ')<br/>' + text;
                        ++count;
                    });
                    detailEl.update(text);
                    ejemplares.setValue(count);
                }
        });
    }

    var cantidad = new Ext.ux.form.Spinner({
        width: 60,
        fieldLabel: _s('Cantidad'),
        selectOnFocus: true,
        value: 1,
        strategy: new Ext.ux.form.Spinner.NumberStrategy()
    })

    var ejemplares = new Ext.form.DisplayField({
        cls : 'total-field',
        value : '',
        height : 10,
        anchor : '100%'
    });

    var controls = [cantidad, tipo, txtISBN, {
        xtype: 'panel',
        id: form_id + '_html',
        anchor: '100% 80%',
        autoScroll : true,
        region: 'center'
    }, ejemplares];

    var form = new Ext.FormPanel({
        labelWidth: Ext.app.LABEL_SIZE,
        bodyStyle: 'padding:5px 5px 0',
        defaultType: 'textfield',
        region: 'center',
        closable: true,
        baseCls: 'x-plain',
        frame: true,
        items: [controls],
        buttons: [{
                text: _s('Borrar'),
                iconCls: 'icon-delete',
                handler: function(button){
                    Ext.app.callRemote({
                        url: site_url('concursos/concurso/clear_teixells'),
                        timeout: false,
                        fnok: function(res) {
                                var detailEl = Ext.getCmp(form_id + '_html').body;  
                                text = _s('ETIQUETAS ELIMINADAS') + '<br/>' + res.message + '<br/>';
                                detailEl.update(text);
                                count = 0;
                                ejemplares.setValue(0);
                            }
                        });
                    }
            }, {
                text: _s('Ver'),
                iconCls: 'icon-ver',
                handler: function(button){
                    ver();
                    }
            }, {
                text: _s('Imprimir'),
                iconCls: 'icon-print',
                handler: function(button){
                    Ext.app.callRemote({
                        url: site_url('concursos/concurso/imprimir_teixells'),
                        timeout: false,
                        fnok: function() {
                                var detailEl = Ext.getCmp(form_id + '_html').body;
                                text = _s('ETIQUETAS IMPRIMIDAS') + '<br/>';
                                detailEl.update(text);

                            }
                        });
                    }
            }]
    });

    var panel = new Ext.Panel({
        layout: 'border',
        title: _s('Teixells'),
        id: form_id,
        iconCls: 'iconoTeixellsTab', 
        region: 'center',
        closable: true,
        baseCls: 'x-plain',
        frame: true,
        listeners : {
            'show' : function() {
                ver();
            }
        },
        items: [form]
    });
        
    return panel;
})();