(function(){
    /*-------------------------------------------------------------------------
     * Datos Formulario
     *-------------------------------------------------------------------------
     */
    var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = 'iconoSuscripcionesAvisoRenovacionTab';
    
    if (title == '') 
        title = _s('Avisos de renovación');
    if (form_id == '') 
        form_id = Ext.app.createId();
    
    /**
     * Función de carga de los datos
     */
    var reload = function(){
        var d = campanas.getValue();
        
        if (d == '') {
            Ext.app.msgFly(title, _s('mensaje_faltan_datos'));
            campanas.focus();
            return;
        }
        
        storePedientesEnviar.baseParams = {
            id: d
        };
        storePedientesEnviar.load();
        
        storePedientesConfirmar.baseParams = {
            id: d
        };
        storePedientesConfirmar.load();
    }
    
    /**
     * Modelo Compras anticipadas
     */
    var modelPedientesEnviar = [{
        name: 'nIdCliente',
        type: 'int',
        column: {
            header: _s('Id'),
            width: Ext.app.TAM_COLUMN_ID,
            sortable: true
        }
    }, {
        name: 'cCliente',
        column: {
            header: _s('Cliente'),
            width: Ext.app.TAM_COLUMN_ID,
            id: 'descripcion',
            sortable: true
        }
    }, {
        name: 'dRenovacion',
        column: {
            header: _s('Renovacion'),
            width: Ext.app.TAM_COLUMN_DATE,
            dateFormat: 'timestamp',
            renderer: Ext.app.renderDate,
            sortable: true
        }
    }, {
        name: 'nSuscripciones',
        type: 'int',
        column: {
            header: _s('Suscripciones'),
            width: Ext.app.TAM_COLUMN_NUMBER,
            sortable: true
        }
    }];
    
    // Store de pendientes de enviar
    var storePedientesEnviar = Ext.app.createStore({
        model: modelPedientesEnviar,
        url: site_url('suscripciones/avisorenovacion/pendientes')
    });
    // Store de pendientes de confirmar
    var storePedientesConfirmar = Ext.app.createStore({
        model: modelPedientesEnviar,
        url: site_url('suscripciones/avisorenovacion/por_confirmar')
    });
    
    // Grid de pendientes de enviar
    var gridPendientesEnviar = Ext.app.createGrid({
        title: _s("Pendientes de enviar"),
        pages: false,
        store: storePedientesEnviar,
        model: modelPedientesEnviar,
        autoexpand: true,
        stripeRows: true,
        loadMask: true,
        rownumber: true,
        checkbox: true,
        tbar: [{
            text: _s('Enviar'),
            iconCls: 'icon-send',
            listeners: {
                click: function(){
                    enviar(gridPendientesEnviar);
                }
            }
        }]
    });

        var temp = new Ext.form.TextField();
        temp.refresh = function() {
            reload();
        }

    var cm_lineas2 = fn_contextmenu();
    var contextmenu = Ext.app.addContextMenuEmpty(gridPendientesEnviar,cm_lineas2);
    cm_lineas2.setContextMenu(contextmenu)
    contextmenu.add({
        text : _s('Cancelar'),
        handler : function() {
            try {
                var ctxRow = cm_lineas2.getItemSelect();
                if(ctxRow != null) {
                    Ext.app.execCmd({
                        url : site_url('suscripciones/avisorenovacion/del_avisos/' + campanas.getValue() + '/' + ctxRow.data.nIdCliente),
                        fnok: reload
                    });
                }
            } catch (e) {
                console.dir(e);
            }
        },
        iconCls : 'icon-cancel-form'
    });
    contextmenu.add('-');
    contextmenu.add({
        text : _s('Imprimir'),
        handler : function() {
            try {
                var ctxRow = cm_lineas2.getItemSelect();
                if(ctxRow != null) {
                    Ext.app.execCmd({
                        url : site_url('suscripciones/avisorenovacion/printer/' + campanas.getValue() + '/' + ctxRow.data.nIdCliente + '/0')
                    });
                }
            } catch (e) {
                console.dir(e);
            }
        },
        iconCls : 'icon-print'
    });
    contextmenu.add('-');
    contextmenu.add({
        text : _s('Ver cliente'),
        handler : function() {
            try {
                var ctxRow = cm_lineas2.getItemSelect();
                if(ctxRow != null) {
                    Ext.app.execCmd({
                        url : site_url('clientes/cliente/index/' + ctxRow.data.nIdCliente)
                    });
                }
            } catch (e) {
                console.dir(e);
            }
        },
        iconCls : 'iconoClientes'
    });
    
    // Grid de pendientes de confirmar
    var gridPendientesConfirmar = Ext.app.createGrid({
        title: _s("Pendientes de enviar"),
        pages: false,
        store: storePedientesConfirmar,
        model: modelPedientesEnviar,
        autoexpand: true,
        stripeRows: true,
        loadMask: true,
        rownumber: true,
        checkbox: true
    });

    var cm_lineas = fn_contextmenu();
    var contextmenu = Ext.app.addContextMenuEmpty(gridPendientesConfirmar, cm_lineas);
    cm_lineas.setContextMenu(contextmenu)
    contextmenu.add({
        text : _s('Renovar'),
        handler : function() {
            try {
                var ctxRow = cm_lineas.getItemSelect();
                if(ctxRow != null) {
                    Ext.app.execCmd({
                        url : site_url('suscripciones/avisorenovacion/gestionar/' + campanas.getValue() + '/' + ctxRow.data.nIdCliente + '/' + temp.id + '/1')
                    });
                }
            } catch (e) {
                console.dir(e);
            }
        },
        iconCls : 'icon-accept-form'
    });
    contextmenu.add('-');
    contextmenu.add({
        text : _s('Cancelar'),
        handler : function() {
            try {
                var ctxRow = cm_lineas.getItemSelect();
                if(ctxRow != null) {
                    Ext.app.execCmd({
                        url : site_url('suscripciones/avisorenovacion/gestionar/' + campanas.getValue() + '/' + ctxRow.data.nIdCliente + '/' + temp.id + '/0')
                    });
                }
            } catch (e) {
                console.dir(e);
            }
        },
        iconCls : 'icon-cancel-form'
    });
    contextmenu.add('-');
    contextmenu.add({
        text : _s('Imprimir'),
        handler : function() {
            try {
                var ctxRow = cm_lineas.getItemSelect();
                if(ctxRow != null) {
                    Ext.app.execCmd({
                        url : site_url('suscripciones/avisorenovacion/printer/' + campanas.getValue() + '/' + ctxRow.data.nIdCliente + '/1')
                    });
                }
            } catch (e) {
                console.dir(e);
            }
        },
        iconCls : 'icon-print'
    });
    contextmenu.add('-');
    contextmenu.add({
        text : _s('Reenviar'),
        handler : function() {
            try {
                var ctxRow = cm_lineas.getItemSelect();
                if(ctxRow != null) {
                    Ext.app.execCmd({
                        url : site_url('suscripciones/avisorenovacion/send/' + campanas.getValue() + '/' + ctxRow.data.nIdCliente + '/1')
                    });
                }
            } catch (e) {
                console.dir(e);
            }
        },
        iconCls : 'icon-send'
    });
    contextmenu.add('-');
    contextmenu.add({
        text : _s('Ver cliente'),
        handler : function() {
            try {
                var ctxRow = cm_lineas.getItemSelect();
                if(ctxRow != null) {
                    Ext.app.execCmd({
                        url : site_url('clientes/cliente/index/' + ctxRow.data.nIdCliente)
                    });
                }
            } catch (e) {
                console.dir(e);
            }
        },
        iconCls : 'iconoClientes'
    });
    
    var campanas = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('suscripciones/grupoaviso/search'),
        allowBlank: false
    }));
    
    var ConsultarModoEnvio = function(){
        Ext.app.msgFly(title, 'Por hacer');
    }
    
    var EstadoAvisos = function(url, title, doreload){
        var d = campanas.getValue();
        
        if (d == '' || d == null) {
            Ext.app.msgFly(title, _s('mensaje_faltan_datos'));
            campanas.focus();
            return;
        }
        Ext.app.callRemote({
            url: site_url('suscripciones/avisorenovacion/estado'),
            params: {
                id: d
            }
        });
    }
    

    var Avisos = function(url, title, doreload){
        var d = campanas.getValue();
        
        if (d == '' || d == null) {
            Ext.app.msgFly(title, _s('mensaje_faltan_datos'));
            campanas.focus();
            return;
        }
        var controls = [{
            fieldLabel: _s('Fecha'),
            name: 'renovacion',
            value: new Date(),
            startDay: Ext.app.DATESTARTDAY,
            xtype: "datefield"
        }, {
            xtype: 'hidden',
            name: 'id',
            value: d
        }];
        var form = Ext.app.formStandarForm({
            controls: controls,
            title: title,
            url: url,
            icon: 'icon-add-aviso-tab',
            fn_ok: function(){
                if (doreload) {
                    reload();
                }
            }
        });
        
        form.show();
    }
    
    var GenerarAvisos = function(){
        Avisos(site_url('suscripciones/avisorenovacion/crear_avisos'), _s('Añadir avisos de renovación'), true);
    }
    
    var ConsultarAvisos = function(){
        Avisos(site_url('suscripciones/avisorenovacion/por_crear'), _s('Avisos de renovación por añadir'));
    }
    
    var enviar = function(grid){
        var d = campanas.getValue();
        
        if (d == '') {
            Ext.app.msgFly(title, _s('mensaje_faltan_datos'));
            campanas.focus();
            return;
        }
        
        var sel = grid.getSelectionModel().getSelections();
        var codes = '';
        for (var i = 0; i < sel.length; i = i + 1) {
            codes += sel[i].data.nIdCliente + ';';
        }
        if (sel.length == 0) {
            Ext.app.msgFly(title, _s('no-items-marcados'));
            return;
        }
        
        Ext.app.callRemoteAsk({
            url: site_url('suscripciones/avisorenovacion/send_all'),
            title: title,
            askmessage: sprintf(_s('send-avisos'), sel.length),
            params: {
                id: d,
                clientes: codes
            },
            fnok: function(obj){
                reload();
            }
        });
    }
    
    var reinit = function () {
        campanas.store.removeAll();
        campanas.store.load({
            callback: function() {
                if (campanas.store.getCount() > 1) {
                    campanas.setValue(campanas.store.getAt(1).id);
                    reload();
                }
            }
        });
    }

    var crear = function() {
        var controls = [{
            fieldLabel: _s('Fecha'),
            name: 'renovacion',
            value: new Date(),
            startDay: Ext.app.DATESTARTDAY,
            xtype: "datefield"
        }, {
            fieldLabel: _s('Campaña'),
            xtype: 'textfield',
            name: 'descripcion'
        }];

        var form = Ext.app.formStandarForm({
            controls: controls,
            title: _s('Nueva campaña'),
            icon: 'icon-new-aviso-tab',
            url: site_url('suscripciones/avisorenovacion/crear'),
            fn_ok: function(){
                reinit();
            }
        });
        
        form.show();
    }
    /**
     * Formulario
     */
    var form = {
        title: title,
        id: form_id,
        region: 'center',
        closable: true,
        iconCls: icon,
        layout: 'border',
        items: [new Ext.TabPanel({
            xtype: 'tabpanel',
            region: 'center',
            activeTab: 0,
            items: [{
                title: _s("Pendientes de enviar"),
                iconCls: 'icon-grid',
                region: 'center',
                layout: 'fit',
                items: gridPendientesEnviar
            }, {
                title: _s('Pendientes de confirmar'),
                iconCls: 'icon-grid',
                region: 'center',
                layout: 'fit',
                items: gridPendientesConfirmar
            }]
        })],
        tbar: [{
            xtype: 'tbbutton',
            text: _s('Acciones'),
            iconCls: 'icon-actions',
            menu: [{
                text: _s('Nueva campaña'),
                iconCls: 'icon-new-aviso',
                handler: crear,
                id: form_id + '_btn_crear'
            }, '-', {
                text: _s('Añadir avisos de renovación'),
                iconCls: 'icon-add-aviso',
                handler: GenerarAvisos,
                id: form_id + '_btn_generar'
            }, '-', {
                text: _s('Avisos de renovación por añadir'),
                iconCls: 'iconoReport',
                handler: ConsultarAvisos,
                id: form_id + '_btn_add'
            }, '-', {
                text: _s('Estado de la campaña'),
                iconCls: 'iconoReport',
                handler: EstadoAvisos,
                id: form_id + '_btn_estado'
            }]
        }, '-', {
            text: _s('Campaña'),
            xtype: 'label'
        
        }, campanas, '-', {
            tooltip: _s('cmd-calcular'),
            text: _s('Actualizar'),
            iconCls: 'icon-refresh',
            listeners: {
                click: reload
            }
        }],
        listeners : {
            render: function(){
                reinit();
            }
        }
    };
    return form;
})();
