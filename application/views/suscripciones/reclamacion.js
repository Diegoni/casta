(function(){
    try {
        var open_id = "<?php echo $open_id;?>";
        var form_id = "<?php echo $id;?>";
        var title = "<?php echo $title;?>";
        var icon = "<?php echo $icon;?>";
        if (title == '') 
            title = _s('Reclamaciones');
        if (icon == '') 
            icon = 'iconoSuscripcionesReclamacionesTab';
        
        var list_grids = []
        
        var iva = null;

        // Carga
        var fn_load = function(id, res){
			data_load = res;
            notas.load(id);
            fn_load_direcciones(res.nIdCliente, res.nIdDireccionCliente);
            fn_load_cliente(res.nIdCliente);
            fn_load_direcciones2(res.nIdProveedor, res.nIdDireccionProveedor);
            fn_load_proveedor(res.nIdProveedor);
            if(res.nIdSuscripcion != null) {
                suscripcion.setText(res.nIdSuscripcion);
            } else {
                suscripcion.setText(_s('SIN SUSCRIPCIÓN'));
            }

            if(res.nIdReclamacionAsociada != null) {
                asociada.setText(res.nIdReclamacionAsociada);
            } else {
                asociada.setText(_s('SIN RECLAMACIÓN'));
            } 
        }
        
        var fn_save = function(id, data){
            data['nIdDireccionProveedor'] = Ext.getCmp(direccionesproveedor.id).getValue();
            data['nIdProveedor'] = Ext.getCmp(proveedorfield.id).getValue();
            data['nIdDireccionCliente'] = Ext.getCmp(direccionescliente.id).getValue();
            data['nIdCliente'] = Ext.getCmp(clientefield.id).getValue();
            return data;
        }
        
        // Borrado
        var fn_reset = function() {

        }
        
        var fn_enable_disable = function(form){
  			Ext.app.formEnableList({
                list: [form.idform + 'btn_enviar'],
                enable: (form.getId() > 0)
            });
            Ext.app.formEnableList({
                list: [form.idform + 'btn_respuesta'],
                enable: (form.getId() > 0) && data_load != null && (data_load.nIdTipoReclamacion == 3)
            });
		}
        
        var data_load = null;
        var tooltip_cliente = null;
        var cliente_datos = null;
        var cliente_id = null;
        var id_defecto = null;
        
        // Cliente
        var fn_set_data = function(data){
            if (data.cliente_id) 
                cliente_id = data.cliente_id;
            if (data.cliente_datos) 
                cliente_datos = data.cliente_datos;
            if (data.info_button) 
                info_button = data.info_button;
            if (data.data_load) 
                data_load = data.data_load;
            if (data.title) 
                title = data.title;
            if (data.direcciones) 
                direccionescliente = data.direcciones;
            if (data.tooltip_cliente) {
                tooltip_cliente = data.tooltip_cliente;
				try {
					msg.update(tooltip_cliente + tooltip_cliente2);
				} 
				catch (e) {
				}
            }
        }
        
        var fn_get_data = function(){
            return {
                cliente_id: cliente_id,
                cliente_datos: cliente_datos,
                tooltip_cliente: tooltip_cliente,
                info_button: info_button,
                data_load: data_load,
                title: title,
                direcciones: direccionescliente
            }
        }

        var data_load2 = null;
        var tooltip_cliente2 = null;
        var cliente_datos2 = null;
        var cliente_id2 = null;
        var id_defecto2 = null;

        var fn_set_data2 = function(data){
            if (data.cliente_id) 
                cliente_id2 = data.cliente_id;
            if (data.cliente_datos) 
                cliente_datos2 = data.cliente_datos;
            if (data.info_button) 
                info_button = data.info_button;
            if (data.data_load) 
                data_load2 = data.data_load;
            if (data.title) 
                title = data.title;
            if (data.direcciones) 
                direccionesproveedor = data.direcciones;
            if (data.tooltip_cliente) {
                tooltip_cliente2 = data.tooltip_cliente;
                try {
                    msg.update(tooltip_cliente + tooltip_cliente2);
                } 
                catch (e) {
                }
            }
        }
        
        var fn_get_data2 = function(){
            return {
                cliente_id: cliente_id2,
                cliente_datos: cliente_datos2,
                tooltip_cliente: tooltip_cliente2,
                info_button: info_button,
                data_load: data_load2,
                title: title,
                direcciones: direccionesproveedor
            }
        }
        
		var fn_lang = function(){
			return getLang(data_load);
        }

        // Formulario
        var form = Ext.app.formGeneric();
        form.init({
            id: form_id,
            title: title,
            icon: icon,
            url: site_url('suscripciones/reclamacion'),
            fn_load: fn_load,
			fn_lang: fn_lang,
            fn_save: fn_save,
            fn_reset: fn_reset,
            button_new: false,
            fn_enable_disable: fn_enable_disable
        });
        
        
        var controles = documentosCliente(form, 'nIdDireccionCliente', fn_get_data, fn_set_data, Ext.app.PERFIL_RECLAMACIONES);
        var controles2 = documentosProveedor(form, 'nIdDireccionProveedor', fn_get_data2, fn_set_data2, Ext.app.PERFIL_RECLAMACIONES);
        
        // Carga direcciones
        var cliente = controles.cliente;
        var info_button = controles.info_button;
        var clientefield = controles.clientefield;
        var direccionescliente = controles.direcciones;
        var fn_load_direcciones = controles.fn_load_direcciones;
        var fn_load_cliente = controles.fn_load_cliente;

        var proveedor = controles2.cliente;
        var info_button = controles.info_button;
        var proveedorfield = controles2.clientefield;
        var direccionesproveedor = controles2.direcciones;
        var fn_load_direcciones2 = controles2.fn_load_direcciones;
        var fn_load_proveedor = controles2.fn_load_cliente;
        
        var msg = new Ext.Panel({
            cls: 'info-msg',            
			autoScroll: true,
			anchor: '100%'
            /*height: 80,
            width: 600*/
        });
        
        var tiporeclamacion = Ext.app.combobox({
            url : site_url('suscripciones/tiporeclamacion/search'),
            //anchor: "90%",
            disabled : true,
            allowBlank : false,
            readOnly : true,
            fieldLabel : _s('nIdTipoReclamacion'),
            id : 'nIdTipoReclamacion'
        });

        var fechaenvio = new Ext.form.DateField({
            xtype : 'datefield',
            readOnly : true,
            startDay : Ext.app.DATESTARTDAY,
            name : 'dEnvio',
            fieldLabel : _s('dEnvio'),
            allowBlank : true
        });

        var suscripcion = new Ext.Button({
            xtype : 'tbbutton',
            iconCls : "iconoSuscripciones",
            text : '',
            fieldLabel : _s('nIdSuscripcion'),
            handler : function() {
                if(data_load.nIdSuscripcion != null) {
                    Ext.app.execCmd({
                        url : site_url('suscripciones/suscripcion/index/' + data_load.nIdSuscripcion)
                    });
                    return;
                }
            }
        });

        var asociada = new Ext.Button({
            xtype : 'tbbutton',
            iconCls : "iconoSuscripcionesReclamaciones",
            text : '',
            fieldLabel : _s('nIdReclamacionAsociada'),
            handler : function() {
                if(data_load.nIdReclamacionAsociada != null) {
                    Ext.app.execCmd({
                        url : site_url('suscripciones/reclamacion/index/' + data_load.nIdReclamacionAsociada)
                    });
                    return;
                }
            }
        });

        // Controles normales
        var controls = [{
            xtype : 'compositefield',
            fieldLabel : _s('nIdTipoReclamacion'),
            msgTarget : 'side',
            anchor : '-20',
            items : [tiporeclamacion, {
                xtype : 'displayfield',
                value : _s('nIdDestino')
            }, {
                xtype : 'textfield',
                readOnly : true,
                id : 'cDestino',
                allowBlank : true
            }]
        }, cliente, proveedor, fechaenvio, {
                xtype : 'checkbox',
                id : 'bCancelada',
                // anchor : '90%',
                allowBlank : true,
                fieldLabel : _s('bCancelada')
            }, suscripcion, asociada, 
            Ext.app.formEditor({
                title: _s('tDescripcion'),
                anchor: '100% 75%',
                id: 'tDescripcion',
                allowBlank: false
            })];
    
        // General
        form.addTab({
            title: _s('General'),
            iconCls: 'icon-general',
            items: {
                xtype: 'panel',
                layout: 'form',
				cls: 'form-reclamacion',
                items: form.addControls(controls)
            }
        });

        var notas = Ext.app.formNotas();
        var grid_notas = notas.init({
            id: form_id + "_notas",
            url: site_url('suscripciones/reclamacion'),
            mainform: form
        });
        form.addTab(new Ext.Panel({
            layout: 'border',
            id: form_id + "_notas",
            title: _s('Histórico'),
            iconCls: 'icon-history',
            region: 'center',
            baseCls: 'x-plain',
            frame: true,
            items: grid_notas
        }));
        
        // Usuarios
        form.addTabUser();
        // Búsqueda
        var fn_open = function(id){
            form.load(id);
            form.selectTab(0);
        }

        var grid_search_m = search_reclamacionsuscripcion(form_id, fn_open);
         
         form.addTab({
            title: _s('Búsqueda'),
            iconCls: 'icon-search',
            items: Ext.app.formSearchForm({
                grid: grid_search_m,
                //audit: false,
                id_grid: form_id + '_g_search_grid'
            })
         });
        var temp = new Ext.form.TextField();
        temp.refresh = function() {
            form.refresh();
        }

        form.addAction({
            text: _s('Respuesta a cliente'),
            handler: function(){
                Ext.app.callRemote({
                    url: site_url('suscripciones/suscripcion/respuesta'),
                    params: {
                        id: form.getId(),
                        cmpid: temp.id
                    }
                });
            },
            iconCls: 'iconoSuscripcionesReclamaciones',
            id: form.idform + 'btn_respuesta'
        });
        form.addAction('-');
        var fn_enviar = function(){
            console.log(DateToNumber((new Date()).getTime()));
            documentosEnviar(form,
                _s('Enviar reclamación'), 
                site_url('suscripciones/reclamacion/send')
            );
        }
        
        form.addAction({
            text: _s('Enviar'),
            handler: function(){
                fn_enviar();
            },
            iconCls: 'icon-send',
            id: form.idform + 'btn_enviar'
        });
        
        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
