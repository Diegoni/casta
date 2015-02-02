(function(){
    var icon = "iconoCalculoTarifasTab";
    var title = _s('Cálculo Tarifas');
    var form_id = Ext.app.createId();

    var divisa = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/divisa/search'),
        name: 'divisa',
        label: _s('Divisa Compra')
    }));
    
    var tipos = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('catalogo/tipolibro/search'),
        name: 'tipo',
        label: _s('Tipo artículo')
    }));
    
    var url = site_url('ventas/cambiodivisa/tarifas');

    var reload = function(){
        try {
            var params = CalcularTarifasForm.getForm().getValues();
            //console.dir(params);
            Ext.app.callRemote({
                url: url,
                params: params,
                fnok: function(res) {
                    var detailEl = Ext.getCmp(form_id + "details-panel").body;
                    
                    detailEl.applyStyles({
                        'background-color': '#FFFFFF'
                    });
                    detailEl.update(res.info);
                }
            });
        } 
        catch (e) {
            console.dir(e);
        }
    }
    
    var CalcularTarifasForm = new Ext.FormPanel({
        region: 'west',
        baseCls: 'form-tarifasenvio',
        width: 300,
        labelWidth: 75,
        bodyStyle: 'padding:5px 5px 0',
        defaultType: 'textfield',
        
        items: [{
                xtype: 'textfield',
                name: 'precio',
                allowBlank: false,
                anchor: '50%',
                fieldLabel: _s('Importe')
            }, divisa, {
                xtype: 'textfield',
                name: 'portes',
                value: "<?php echo $this->config->item('bp.divisa.portes');?>",
                allowBlank: false,
                anchor: '50%',
                fieldLabel: _s('Portes')
            }, {
                xtype: 'textfield',
                name: 'dto',
                value: "<?php echo $this->config->item('bp.divisa.dtoprv');?>",
                allowBlank: false,
                anchor: '50%',
                fieldLabel: _s('Dto. Proveedor')
            }, tipos
        ],
        buttons: [{
            text: _s('Calcular'),
            iconCls: 'icon-refresh',
            handler: reload
        }, {
            text: _s('Limpiar'),
            iconCls: 'icon-clean',
            handler: function(){
                CalcularTarifasForm.getForm().reset();
                tipos.setValue("<?php echo $this->config->item('bp.divisa.tipodefault');?>");
                divisa.setValue("<?php echo $this->config->item('bp.divisa.default');?>");
            }
        }]
    });    
    
    var panel = new Ext.Panel({
        layout: 'border',
        title: title,
        id: id,
        iconCls: icon,
        region: 'center',
        closable: true,
        baseCls: 'x-plain',
        frame: true,
        listeners: {
            afterrender: function(p){
                var map = new Ext.KeyMap(p.getEl(), [{
                    key: [10, 13],
                    ctrl: true,
                    stopEvent: true,
                    fn: function(){
                        reload();
                    }
                }]);
            }
        },
        
        items: [CalcularTarifasForm, {
                region: 'center',
                height: Ext.app.REPOINFOHEIGHT,
                bodyStyle: 'padding-bottom:15px;background:#FFFFFF;',
                autoScroll: true,
                split: true,
                cls: 'details-panel',
                tbar: ['->', {
                    tooltip : _s('cmd-print'),
                    iconCls : 'icon-print',
                    text : _s('Imprimir'),
                    handler: function() {
                        var detailEl = Ext.getCmp(form_id + "details-panel").body;
                        var html = detailEl.dom.innerHTML;
                        // Llama al controlador de exportaciones
                        Ext.app.callRemote({
                            url : site_url('sys/export/html'),
                            title : _s('Imprimir'),
                            icon : 'iconoPreviewTab',
                            params : {
                                html : html,
                                type : 'print'
                            },
                            fnok : function(o) {
                                o.title = _s('Imprimir');
                                o.icon = 'iconoPreviewTab';
                                o.url = o.src;
                                o.print = false;
                            }
                        });
                    }
                }],
                id: form_id + "details-panel"
            }]
    });

    Ext.app.loadStores([{
        store: divisa.store
    }, {
        store: tipos.store
    }], function(){
        tipos.setValue("<?php echo $this->config->item('bp.divisa.tipodefault');?>");
        divisa.setValue("<?php echo $this->config->item('bp.divisa.default');?>");
    });
    
    return panel;
})();
