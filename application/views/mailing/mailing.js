(function(){
<?php $id = rand(0, 100000);?>
    var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
    if (title == '') 
        title = _s('Mensajes');
    if (icon == '') 
        icon = 'iconoMensajesTab';
    /*if (form_id == '') 
        form_id = Ext.app.createId();*/
    
    //console.log('mailing ' + open_id); 	
    var addVarios = function(id, emails, texto){
        Ext.app.callRemoteAsk({
            url: site_url('mailing/mailing/add_emails'),
            title: this.title,
            askmessage: _s('add-emails-mailing'),
            fly: false,
            params: {
                id: parseInt(id),
                email: emails,
                texto: texto
            },
            fnok: function(){
                fn_load(id);
            }
        });
    }
    
    var fn_contactos = function(id){
        // Formulario de añadir contactos
        var tipocliente = Ext.app.combobox({
            url: site_url('mailing/tipocliente/search'),
            name: 'nIdTipoCliente',
            label: _s('Tipo Contacto')
        });
        
        var grupocliente = Ext.app.combobox({
            url: site_url('mailing/grupocliente/search'),
            name: 'nIdGrupoCliente',
            label: _s('Grupo Contacto')
        });
        
        var controls = [{
            xtype: 'textfield',
            anchor: '100%',
            name: 'text',
            allowBlank: true,
            fieldLabel: _s('cNombre')
        }, {
            xtype: 'hidden',
            name: 'bNoEmail',
            value: 0
        }, tipocliente, grupocliente];
        
        var columns = [{
            header: "Id",
            dataIndex: 'id',
            width: Ext.app.TAM_COLUMN_ID,
            align: 'right',
            sortable: true
        }, {
            header: _s('Tipo Cliente'),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true,
            renderer: tipocliente,
            dataIndex: 'cTipoCliente'
        }, {
            header: _s('Grupo Cliente'),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true,
            renderer: grupocliente,
            dataIndex: 'cGrupoCliente'
        }, {
            header: _s('Nombre'),
            dataIndex: 'cNombre',
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }, {
            header: _s('Empresa'),
            dataIndex: 'cEmpresa',
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }, {
            header: _s('Email'),
            dataIndex: 'cEmail',
            // id : 'descripcion',
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }];
        
        var frm = Ext.app.formSearchDialog({
            url: site_url('mailing/contacto/search2'),
            columns: columns,
            controls: controls,
            icon: 'iconoContactosTab',
            fn_add: addVarios,
            id: id,
            title: _s('Añadir Contactos'),
            texto: _s('Contactos')
        });
        tipocliente.store.load();
        grupocliente.store.load();
        frm.run();
    }
    
    var fn_clientes = function(id){
        // Formulario de añadir clientes
        var tipocliente = Ext.app.combobox({
            url: site_url('clientes/tipocliente/search'),
            name: 'nIdTipoCliente',
            label: _s('Tipo Cliente')
        });
        
        var grupocliente = Ext.app.combobox({
            url: site_url('clientes/grupocliente/search'),
            name: 'nIdGrupoCliente',
            label: _s('Grupo Cliente')
        });
        
        var controls = [{
            xtype: 'textfield',
            anchor: '100%',
            name: 'text',
            allowBlank: true,
            fieldLabel: _s('cNombre')
            /*}, {
             xtype : 'textfield',
             name : 'cEmpresa',
             anchor : '100%',
             allowBlank : true,
             fieldLabel : _s('cEmpresa')*/
        }, {
            xtype: 'hidden',
            name: 'bNoEmail',
            value: 0
        }, tipocliente, grupocliente];
        
        var columns = [{
            header: "Id",
            dataIndex: 'id',
            width: Ext.app.TAM_COLUMN_ID,
            align: 'right',
            sortable: true
        }, {
            header: _s('Tipo Cliente'),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true,
            renderer: tipocliente,
            dataIndex: 'cTipoCliente'
        }, {
            header: _s('Grupo Cliente'),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true,
            renderer: grupocliente,
            dataIndex: 'cGrupoCliente'
        }, {
            header: _s('Nombre'),
            dataIndex: 'cNombre',
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }, {
            header: _s('Apellido'),
            dataIndex: 'cApellido',
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }, {
            header: _s('Empresa'),
            dataIndex: 'cEmpresa',
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }, {
            header: _s('Email'),
            dataIndex: 'cEmail',
            // id : 'descripcion',
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        }];
        
        var frm = Ext.app.formSearchDialog({
            url: site_url('clientes/cliente/search2'),
            columns: columns,
            controls: controls,
            icon: 'iconoClientesTab',
            fn_add: addVarios,
            id: id,
            title: _s('Añadir Clientes'),
            texto: _s('Clientes')
        });
        tipocliente.store.load();
        grupocliente.store.load();
        frm.run();
    }
    
    var form = Ext.app.formGeneric();
    
    var list_grids = [form_id + '_emails_grid']
    
    // Carga
    var fn_load = function(id){
        Ext.app.formLoadList({
            list: list_grids,
            params: {
                where: 'nIdMailing=' + id
            }
        });
    }
    
    // Borrado
    var fn_reset = function(){    
        Ext.app.formResetList({
            list: list_grids,
            params: {
                where: 'nIdMailing=-1'
            }
        });
    }
    
    var fn_enable_disable = function(){
        var list_buttons = [form.idform + 'btn_enviar', 
            form.idform + 'btn_reset', 
            form.idform + 'btn_tema', 
            form.idform + 'btn_todos', 
            form.idform + 'btn_contacto', 
            form.idform + 'btn_cliente', 
            form.idform + 'btn_enviaruno', 
            form.idform + 'btn_publicar',
            form.idform + 'btn_delete_emails', 
            form.idform + 'btn_add_emails'];
        
        Ext.app.formEnableList({
            list: list_buttons,
            enable: (form.getId() > 0)
        });
        
        Ext.app.formEnableList({
            list: list_grids,
            enable: (form.getId() > 0)
        });
    }
    
    form.init({
        id: form_id,
        title: title,
        icon: icon,
        url: site_url('mailing/mailing'),
        fn_load: fn_load,
        fn_reset: fn_reset,
        fn_enable_disable: fn_enable_disable
    });
    
    // Controles normales
    var controls = [{
        xtype: 'textfield',
        id: 'cDescripcion',
        anchor: '90%',
        allowBlank: false,
        fieldLabel: _s('cDescripcion')
    }, {
        xtype: 'textfield',
        id: 'cAsunto',
        anchor: '90%',
        allowBlank: false,
        fieldLabel: _s('Asunto')
    }, Ext.app.formEditor({
        title: _s('Email'),
        anchor: '100% 100%',
        id: 'cBody'
    })];
    
    form.addTab({
        title: _s('General'),
        iconCls: 'icon-general',
        items: {
            xtype: 'panel',
            layout: 'form',
            items: form.addControls(controls)
        }
    });
    
    // SMTP
    var smtp = [{
        xtype: 'textfield',
        id: 'cEMailAddress',
        anchor: '50%',
        allowBlank: true,
        fieldLabel: _s('Email')
    
    }, {
        xtype: 'checkbox',
        id: 'bAutenticacion',
        // anchor : '90%',
        allowBlank: true,
        fieldLabel: _s('Autentificación')
    }, {
        xtype: 'textfield',
        id: 'cSMTP',
        anchor: '50%',
        allowBlank: true,
        fieldLabel: _s('Servidor SMTP')
    
    }, {
        xtype: 'textfield',
        id: 'cUser',
        anchor: '50%',
        allowBlank: true,
        fieldLabel: _s('Usuario SMTP')
    }, {
        xtype: 'textfield',
        id: 'cPassword',
        anchor: '50%',
        allowBlank: true,
        fieldLabel: _s('Contraseña SMTP')
    }];
    
    form.addTab({
        title: _s('Servidor'),
        iconCls: 'icon-smtp-server',
        items: {
            xtype: 'panel',
            layout: 'form',
            items: form.addControls(smtp)
        }
    });
    
    var fn_add = function(controls){
        // console.log('En ADD');
        var c = {
            xtype: 'hidden',
            id: 'nIdMailing',
            value: form.getId()
        }
        controls[controls.length] = c;
        return controls;
    }
    
    
     <?php 	$obj =& get_instance();
     $obj->load->model('mailing/M_mailingemail', 'ml');
     $modelo = $obj->ml->get_data_model(array('nIdMailing'));
     ?>
     
     var emails = <?php echo extjs_creategrid($modelo, $id . '_emails', $this->lang->line('Emails'), 'icon-emails', 'mailing/mailingemail', $obj->ml->get_id(), null, FALSE, 'fn_add');?>;
     
    form.addTab(emails);
    
    var notas = [{
        fieldLabel: _s('Notas'),
        xtype: 'htmleditor',
        id: 'tNotas',
        anchor: '100% 91%'
    }];
    
    form.addTab({
        title: _s('Notas'),
        iconCls: 'icon-notes',
        items: form.addControls(notas)
    });
    form.addTabUser();
    
    var fn_open = function(id){
        form.load(id);
        form.selectTab(0);
    }
    
     <?php
     $obj =& get_instance();
     $obj->load->model('mailing/m_mailing');
     $modelo2 = $obj->m_mailing->get_data_model(array('cBody'));
     ?>
     var grid = <?php echo extjs_creategrid($modelo2, $id.'_g_search', null, null, 'mailing.mailing', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
     
    form.addTab({
        title: _s('Búsqueda'),
        iconCls: 'icon-search',
        items: Ext.app.formSearchForm({
            grid: grid,
            id_grid: form_id + '_g_search_grid'
        })
    });
    
    // Acciones
    // Envia el mailing
    var sendEmails = function(padre){
        var fn = function(){        
            var url = site_url('mailing/mailing/send');
            var form = Ext.app.formStandarForm({
				icon: 'icon-email',
                controls: [{
                    xtype: 'hidden',
                    name: 'id',
                    value: padre.getId()
                }, {
                    xtype: 'xdatetime',
                    name: 'time',
                    fieldLabel: _s('Programación'),
                    //anchor: '-18',
                    timeFormat: Ext.app.TIMEFORMAT,
                    timeConfig: {
						dateFormat: 'timestamp',
						allowBlank: true
                    },
                    dateFormat: Ext.app.DATEFORMATSHORT,
                    dateConfig: {
						startDay: Ext.app.DATESTARTDAY,                        
                        allowBlank: true
                    }
                }],
                title: _s('Enviar'),
                url: url
            });
			form.show();
            return;
            
            Ext.app.callRemoteAsk({
                url: site_url('mailing/mailing/send'),
                title: this.title,
                askmessage: _s('send-mailing'),
                params: {
                    id: padre.getId()
                }
                /*,
                 fnok : function(obj) {
                 fn_load(padre.getId());
                 Ext.app.msgFly(padre.title, obj.message);
                 }*/
            });
        }
        
        try {
            if (padre.getId() != null) {
                if (padre.isDirty()) {
                    Ext.Msg.show({
                        title: padre.title,
                        buttons: Ext.MessageBox.YESNO,
                        msg: _s('send-mailing-dirty'),
                        fn: function(btn, text){
                            if (btn == 'yes') {
                                fn();
                            }
                        }
                    });
                }
                else {
                    fn();
                }
            }
        } 
        catch (e) {
            console.dir(e);
        }
    }
    
    var resetEmails = function(padre){
        Ext.app.callRemoteAsk({
            url: site_url('mailing/mailing/reset'),
            title: this.title,
            wait: true,
            askmessage: _s('mailing-reset'),
            params: {
                id: padre.getId()
            },
            fnok: function(obj){
                fn_load(padre.getId());
                //Ext.app.msgFly(padre.title, obj.message);
            }
        });
    }
    
    // Función añadir Tema
    var addTema = function(padre){
        var temas = Ext.app.combobox({
            url: site_url('mailing/tema/search'),
            name: 'idtema',
            anchor: '90%',
            label: _s('Tema')
        });
        
        var controls = [temas, {
            xtype: 'hidden',
            id: 't_new_id',
            name: 'id'
        }];
        
        var form = Ext.app.formStandarForm({
            controls: controls,
            title: _s('Añadir Tema'),
            icon: 'icon-temas',
            fn_pre: function(){
                Ext.getCmp('t_new_id').setValue(padre.getId());
            },
            fn_ok: function(){
                fn_load(padre.getId());
            },
            url: site_url('mailing/mailing/add_tema')
        });
        temas.store.load();
        form.show();
    }
    
    // Acción añadir todos
    var addTodos = function(padre){
        Ext.app.callRemoteAsk({
            url: site_url('mailing/mailing/add_todos'),
            title: padre.title,
            askmessage: _s('mailing-add-todos-q'),
            wait: true,
            params: {
                id: padre.getId()
            },
            fnok: function(obj){
                fn_load(padre.getId());
                //Ext.app.msgFly(padre.title, obj.message);
            }
        });
    }
    
    var elmEmails = function(padre){
        Ext.app.callRemoteAsk({
            url: "<?php echo site_url('mailing/mailing/del_emails');?>",
            title: padre.title,
            wait: true,
            askmessage: _s('mailing-del-todos-email-q'),
            params: {
                id: padre.getId()
            },
            fnok: function(obj){
                fn_load(padre.getId());
                //Ext.app.msgFly(padre.title, obj.message);
            }
        });
    }
    
    var consultarEmails = function(padre){
        var controls = [{
            xtype: 'textfield',
            fieldLabel: _s('cEmail'),
            name: 'email'
        }];
        
        var form = Ext.app.formStandarForm({
            controls: controls,
            icon: 'icon-doc-search',
            title: _s('Consultar Emails'),
            url: site_url('mailing/mailing/sended_emails')
        });
        form.show();
    }
    
    var addEmails = function(padre){
        var controls = [{
            xtype: "textarea",
            anchor: '100% 100%',
            fieldLabel: _s('cEmail'),
            name: 'email'
        }, {
            xtype: 'hidden',
            value: padre.getId(),
            name: 'id'
        }];
        
        var form = Ext.app.formStandarForm({
            controls: controls,
            icon: 'icon-email',
            title: _s('Añadir emails en bloque'),
            url: site_url('mailing/mailing/add_emails'),
            fn_ok: function(obj){
                fn_load(padre.getId());
            }
        });
        form.show();
    }
    
    // Función añadir Tema
    var enviarUno = function(padre){
        var controls = [{
            xtype: "textarea",
            anchor: '100% 100%',
            grow: true,
            name: 'email',
            allowBlank: false,
            fieldLabel: _s('Email')
        }, {
            xtype: 'hidden',
            value: padre.getId(),
            name: 'id'
        }];
        
        var form = Ext.app.formStandarForm({
            controls: controls,
			icon: 'icon-email2',
            title: _s('mailing-enviar-uno'),
            timeout: false,
            url: site_url('mailing/mailing/send_uno')
        });
        form.show();
    }
    
    // Acciones
    form.addAction({
        text: _s('Enviar'),
        handler: function(){
            sendEmails(form);
        },
        iconCls: 'icon-email',
        id: form.idform + 'btn_enviar'
    });
    form.addAction({
        text: _s('mailing-enviar-uno'),
        handler: function(){
            enviarUno(form);
        },
        iconCls: 'icon-email2',
        id: form.idform + 'btn_enviaruno'
    });
    
    form.addAction('-');
    
    form.addAction({
        text: _s('mailing-reset'),
        handler: function(){
            resetEmails(form);
        },
        iconCls: 'icon-reset',
        id: form.idform + 'btn_reset'
    });
    
    form.addAction({
        text: _s('mailing-del-todos-email'),
        handler: function(){
            elmEmails(form);
        },
        iconCls: 'icon-delete',
        id: form.idform + 'btn_delete_emails'
    });
    
    form.addAction('-');
    
    form.addAction({
        text: _s('Consultar Emails'),
        handler: function(){
            consultarEmails(form);
        },
        iconCls: 'icon-doc-search',
        id: form.idform + 'btn_consultar'
    });
    
    // Herramientas
    form.addTools({
        text: _s('Añadir Tema'),
        iconCls: 'icon-temas',
        handler: function(){
            addTema(form);
        },
        id: form.idform + 'btn_tema'
    });
    form.addTools({
        text: _s('Añadir Todos'),
        iconCls: 'icon-email-all',
        handler: function(){
            addTodos(form);
        },
        id: form.idform + 'btn_todos'
    });
    
    form.addTools({
        text: _s('Añadir Contactos'),
        iconCls: 'iconoContactos',
        handler: function(){
            fn_contactos(form.getId());
        },
        id: form.idform + 'btn_contacto'
    });
    
    form.addTools({
        text: _s('Añadir Clientes'),
        iconCls: 'iconoClientes',
        handler: function(){
            fn_clientes(form.getId());
        },
        id: form.idform + 'btn_cliente'
    });
    
    form.addTools({
        text: _s('Añadir emails en bloque'),
        iconCls: 'icon-email',
        handler: function(){
            addEmails(form);
        },
        id: form.idform + 'btn_add_emails'
    });
    form.addTools('-');

    // Acciones
    form.addTools({
        text: _s('boletin-publicar-web'),
        handler: function() {
            Ext.app.callRemote({
                url: site_url('mailing/mailing/publicar'),
                params: {id: form.getId()},
                fn_ok: function()
                {
                    form.refresh();
                }
            });            
        },
        iconCls: 'icon-web',
        id: form.idform + 'btn_publicar'
    });
    
    return form.show(open_id);
})();
