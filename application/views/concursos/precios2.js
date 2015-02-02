(function() {

	var form_id = Ext.app.createId();

    var txtISBN = new Ext.form.TextField({
    	fieldLabel: _s('Código'),
        enableKeyEvents : true
    }); 

    txtISBN.on('specialkey',
        function(o, e){
            if (e.getKey() == e.ENTER){
                var detailEl = Ext.getCmp(form_id + '_html').body;                        
                detailEl.update('');

                var idb = concurso.getValue();
                if (idb < 1) {
                    concurso.focus();
                    return;
                }
                Ext.app.callRemote({
                    url: site_url('concursos/concurso/check_precio'),
                    params: {
                        code: txtISBN.getValue(),
                        biblioteca: idb,
                        catalogar: catalogar.getValue()
                    },
                    fnok: function (res) {
                        detailEl.applyStyles({
                            'background-color': '#FFFFFF'
                        });
                        /*var text = '<div style="font-size: 200%;color:green;align:center;">' + res.text + '</div>';
                        '<div style="font-size: 150%;color:black;align:center;">' + res.titulo + '</div>';*/
                        var text = '<div style="font-size: 200%;color:green;align:center;">' + res.cTitulo + '</div>'
                        + '<div style="font-size: 150%;color:blue;">' + ((res.cAutores2!=null)?res.cAutores2:'') + '</div>'
                        + '<div style="font-size: 150%;color:grey;">' + ((res.cPedido!=null)?res.cPedido:'') + '</div>'
                        + '<div style="font-size: 150%;color:grey;">' + ((res.cBiblioteca!=null)?res.cBiblioteca:'') + '</div>'
                        + '<div style="font-size: 150%;color:grey;">' + ((res.cSala!=null)?(_s('Sala') + ': ' + res.cSala):'') + '</div>'
                        + '<div style="font-size: 150%;color:orange;">' + ((res.cSala!=null)?(_s('cCDU') + ': ' + res.cCDU):'') + '</div>'
                        + '<div style="font-size: 500%;color:red;float:center;display:block;">' + ((res.fPVP2 != null)?Ext.app.currencyFormatter(res.fPVP2):'') + '</div>';
                        detailEl.update(text);
                        txtISBN.setValue(null);
                    }                            
                });
            }
        },
        this
    );

    var concurso = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('concursos/biblioteca/search'),
        label: _s('Bibliotecas'),
        name: 'biblioteca',
        anchor: '90%'
    }));
            
    concurso.store.load();
    var catalogar = new Ext.form.Checkbox({ 
        fieldLabel: _s('Catalogar'), 
        checked: true, 
        value: true
    });

    var controls = [concurso, catalogar, txtISBN, {
        xtype: 'iframepanel',
        id: form_id + '_html',
        region: 'center'
    }];

    var form = new Ext.FormPanel({
        labelWidth: Ext.app.LABEL_SIZE,
        bodyStyle: 'padding:5px 5px 0',
        defaultType: 'textfield',
        region: 'center',
        closable: true,
        baseCls: 'x-plain',
        frame: true,
        items: [controls]
    });

    var panel = new Ext.Panel({
        layout: 'border',
        title: _s('Consulta de precios'),
        id: form_id,
        iconCls: 'iconoConsultaPreciosTab', 
        region: 'center',
        closable: true,
        baseCls: 'x-plain',
        frame: true,
        items: [form]
    });
        
    return panel;
})();