(function() {

	var form_id = Ext.app.createId();

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
                    url: site_url('concursos/concurso2/add_teixell'),
                    params: {
                        code: txtISBN.getValue(),
                        cantidad: cantidad.getValue()
                    },
                    fnok: function (res) {
                        detailEl.applyStyles({
                            'background-color': '#FFFFFF'
                        });
                        /*var text = '<div style="font-size: 200%;color:green;align:center;">' + res.text + '</div>';
                        '<div style="font-size: 150%;color:black;align:center;">' + res.titulo + '</div>';*/
                        text = text + '[' + cantidad.getValue() + ']' + txtISBN.getValue() + '<br/>';
                        detailEl.update(text);
                        txtISBN.setValue(null);
                        cantidad.reset();
                    }                            
                });
            }
        },
        this
    );

    var cantidad = new Ext.ux.form.Spinner({
        width: 60,
        fieldLabel: _s('Cantidad'),
        selectOnFocus: true,
        value: 1,
        strategy: new Ext.ux.form.Spinner.NumberStrategy()
    })
    var controls = [cantidad, txtISBN, {
        xtype: 'iframepanel',
        id: form_id + '_html',
        anchor: '100% 90%',
        region: 'center'
    }];

    var reload = function() {
        Ext.app.callRemote({
            url: site_url('concursos/concurso2/get_teixells'),
            fnok: function (res) {
                text = '';
                var detailEl = Ext.getCmp(form_id + '_html').body;
                Ext.each(res.value_data, function(item){
                    text = text + '[1]' + item.cTexto + '<br/>';
                });
                detailEl.update(text);
            }                            
        });
    }

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
                text: _s('Refrescar'),
                iconCls: 'icon-refresh',
                handler: function(button){
                    reload();
                }
            }, {
                text: _s('Imprimir'),
                iconCls: 'icon-print',
                handler: function(button){
                    Ext.app.callRemote({
                        url: site_url('concursos/concurso2/imprimir_teixells'),
                        timeout: false,
                        fnok: function(){
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
        iconCls: 'iconoTeixellsDirectosTab', 
        region: 'center',
        closable: true,
        baseCls: 'x-plain',
        frame: true,
        items: [form]
    });

    reload();

    return panel;
})();