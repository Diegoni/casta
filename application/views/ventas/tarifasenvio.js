(function(){

    var id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "iconoFormasEnvioTab";
    
    var modoenvioCombo = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('ventas/modoenvio/search'),
        name: 'modoenvio',
        label: _s('Modos de Envio')
    }));
    
    var c = Ext.app.formComboPaises({
        idpais: id + '_p',
        idregion: id + '_r',
        allowblank: false
    });
    
    var ActualizarTarifasForm = new Ext.FormPanel({
        monitorValid: true,
        fileUpload: true,
        labelWidth: 80,
        border: false,
        bodyStyle: 'background:transparent;padding:2px',
        defaults: {
            anchor: '95%',
            allowBlank: false,
            msgTarget: 'side'
        },
        items: [modoenvioCombo, {
            xtype: 'fileuploadfield',
            id: 'excelfile',
            emptyText: 'Fichero EXCEL',
            fieldLabel: 'Fichero',
            name: 'excelfile',
            buttonCfg: {
                text: '',
                iconCls: 'upload-icon'
            }
        }]
    });
    
    var ActualizarTarifaWindow = new Ext.Window({
        title: _s('Act K'),
        autoHeight: true,
        bodyStyle: 'padding: 10px 10px 0 10px;',
        layout: 'form',
        width: 500,
        height: 300,
        closeAction: 'hide',
        resizable: false,
        plain: true,
        modal: true,
        items: ActualizarTarifasForm,
        buttons: [{
            text: _s('Aceptar'),
            handler: function(){
                Ext.app.sendForm({
                    form: ActualizarTarifasForm,
                    url: site_url('ventas/tarifasenvio/set_tarifas'),
                    upload: true,
                    wait: true,
                    fnok: function(){
                        //Ext.app.msgInfo(_s('Act K'), _s('tarifasenvio-set-tarifas-ok'));*/
                        ActualizarTarifaWindow.hide();
                    }
                });
            }
        }, {
            text: _s('Cerrar'),
            handler: function(){
                ActualizarTarifaWindow.hide();
            }
        }]
    });
    
    
    var store = Ext.app.createStore({
        url: site_url('ventas/tarifasenvio/get_tarifas'),
        id: 'id',
        model: [{
            name: 'id'
        }, {
            name: 'text'
        }, {
            name: 'coste'
        }, {
            name: 'descripcion'
        }]
    });
    
    var reload = function(){
        try {
            Ext.app.loadStores([{
                store: store,
                params: {
                    regionId: Ext.app.getIdCombo(Ext.getCmp(id + '_r')),
                    paisId: Ext.app.getIdCombo(Ext.getCmp(id + '_p')),
                    peso: Ext.getCmp(id + 'pesoField').getValue(),
                    unidades: Ext.getCmp(id + 'unidadesField').getValue(),
                    pedido: Ext.getCmp(id + 'pedidoField').getValue()
                }
            }]);
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
        
        items: [c[0], c[1], {
            xtype: 'numberfield',
            fieldLabel: _s('Peso Gr'),
            name: 'peso',
            anchor: '95%',
            id: id + 'pesoField'
        }, {
            xtype: 'numberfield',
            fieldLabel: _s('Unidades'),
            name: 'unidades',
            anchor: '95%',
            id: id + 'unidadesField'
        }, {
            xtype: 'numberfield',
            fieldLabel: _s('Pedido'),
            name: 'pedido',
            anchor: '95%',
            id: id + 'pedidoField'
        }],
        buttons: [{
            text: _s('Calcular'),
            iconCls: 'icon-refresh',
            handler: reload
        }, {
            text: _s('Limpiar'),
            iconCls: 'icon-clean',
            handler: function(){
                CalcularTarifasForm.getForm().reset();
            }
        }]
    });
    
    var TarifasEnvioGrid = new Ext.grid.GridPanel({
        region: 'center',
        autoExpandColumn: "descripcion",
        loadMask: true,
        stripeRows: true,
        store: store,
        id: id + "_grid",
        tbar: Ext.app.gridStandarButtons({
            title: title,
            id: id + "_grid"
        }),
        columns: [{
            header: _s('Id'),
            width: Ext.app.TAM_COLUMN_ID,
            dataIndex: 'id',
			hidden: true,
            sortable: true
        }, {
            header: _s('Nombre'),
            width: Ext.app.TAM_COLUMN_TEXT,
            dataIndex: 'text',
            sortable: true
        }, {
            header: _s('Coste'),
            width: Ext.app.TAM_COLUMN_MONEY,
            // type : 'dobule',
            renderer: Ext.app.euroFormatter,
            dataIndex: 'coste',
            sortable: true
        }, {
            header: _s('Descripcion'),
            width: Ext.app.TAM_COLUMN_TEXT,
            dataIndex: 'descripcion',
            // renderer: render_html,
            id: 'descripcion',
            sortable: true
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
        tbar: [{
            text: _s('Act K'),
            iconCls: 'icon-upload',
            handler: function(button){
                ActualizarTarifaWindow.show();
            }
        }, {
            text: _s('Act Grm'),
            iconCls: 'icon-upload',
            handler: function(button){
                Ext.app.execCmd({url: site_url('ventas/tarifasenvio/set_tarifasgramos')});
                //ActualizarTarifaGramosWindow.show();
            }
        }],
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
        
        items: [CalcularTarifasForm, TarifasEnvioGrid]
    });
    
    Ext.app.loadStores([{
        store: modoenvioCombo.store
    }]);
    return panel;
})();
