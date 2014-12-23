(function(){
    try {
        var open_id = "<?php echo isset($open_id)?$open_id:'';?>";
        var form_id = "<?php echo isset($id)?$id:'';?>";
        var title = "<?php echo isset($title)?$title:'';?>";
        var icon = "<?php echo isset($icon)?$icon:'';?>";
        if (title == '') 
            title = _s('Clientes');
        if (icon == '') 
            icon = 'iconoClientesTab';
        
        var perfiles = Ext.app.formPerfiles();
        
        var list_grids = [
            form_id + '_temas_grid', 
            form_id + '_descuentos_grid', 
            form_id + '_tarifas_grid', 
			form_id + 'btn_contenido_favoritos', 
            form_id + 'btn_contenido_cesta', 
            form_id + 'btn_documentos', 
			form_id + 'btn_suscripciones', 
            form_id + 'btn_fac_suscripciones', 
            form_id + 'btn_send_web',
            form_id + 'btn_password_web',            
            form_id + 'btn_sincro',
            form_id + 'btn_emails_enviados'
        ]

        var notas = Ext.app.formNotas();
        
        // Carga
        var fn_load = function(id, res){
            notas.load(id);
            perfiles.load(id);
            Ext.app.formLoadList({
                list: list_grids,
                params: {
                    where: 'nIdCliente=' + parseInt(id),
                    id: parseInt(id)
                }
            });
			idioma.setValue(res.cIdioma);			
        }
        
        // Borrado
        var fn_reset = function(){
            notas.reset();
            perfiles.reset();
            Ext.app.formResetList({
                list: list_grids,
                params: {
                    where: 'nIdCliente=-1',
                    id: -1
                }
            });
        }
        
        var fn_enable_disable = function(form){
            notas.enable(form.getId() > 0);
            perfiles.enable(form.getId() > 0);
            Ext.app.formEnableList({
                list: list_grids,
                enable: (form.getId() > 0)
            });
        }
        
        // Formulario
        var form = Ext.app.formGeneric();
        form.init({
            id: form_id,
            title: title,
            icon: icon,
            url: site_url('clientes/cliente'),
            fn_load: fn_load,
            fn_reset: fn_reset,
            fn_enable_disable: fn_enable_disable
        });
        
        var idioma = Ext.app.comboLangs(null, 'cIdioma');
        // Temas
        
        var grid_temas = Ext.app.formCheckList({
            urllist: site_url('clientes/temascliente/get_list'),
            urlupd: site_url('clientes/temascliente/upd'),
            idreg: 'nIdTema',
            id: form_id + "_temas_grid",
            text: 'cDescripcion',
            form: form
        });
        
        // Perfiles
        
        var grid_perfiles = perfiles.init({
            id: form_id + "_perfiles",
            etq: 'cliente',
            url: site_url('clientes/perfilcliente'),
            mainform: form
        });
        
        // Controles normales
        var tipocliente = Ext.app.combobox({
            url: site_url('clientes/tipocliente/search'),
            id: 'nIdTipoCliente',
            width: 300,
            //anchor: '50%',
            label: _s('Tipo Cliente')
        });
        var tratamiento = Ext.app.combobox({
            url: site_url('clientes/tratamiento/search'),
            id: 'nIdTratamiento',
            //width: 90,
            anchor: "50%",
            label: _s('Tratamiento')
        });
        var grupocliente = Ext.app.combobox({
            url: site_url('clientes/grupocliente/search'),
            width: 300,
            id: 'nIdGrupoCliente',
            label: _s('Grupo Cliente')
        });
        
        var estado = Ext.app.combobox({
            url: site_url('clientes/estadocliente/search'),
            id: 'nIdEstado',
            width: 300,
            label: _s('nIdEstado')
        });
        
        var tarifa = Ext.app.combobox({
            url: site_url('ventas/tipotarifa/search'),
            width: 300,
            //anchor: '50%',
            id: 'nIdTipoTarifa',
            label: _s('Tarifa General')
        });
        
        var model = [{
            name: 'nIdDescuento',
            column: {
                header: _s("Id"),
                width: Ext.app.TAM_COLUMN_ID,
                dataIndex: 'id',
                sortable: true
            }
        }, {
            name: 'id'
        }, {
            name: 'cDescripcion',
            column: {
                header: _s("cDescripcion"),
                width: Ext.app.TAM_COLUMN_TEXT,
                id: 'descripcion',
                sortable: true
            }
        }, {
            name: 'fValor',
            column: {
                header: _s('fValor'),
                width: Ext.app.TAM_COLUMN_NUMBER,
                sortable: true
            }
        }];
        
        var fn_add = function(controls){
            var c = {
                xtype: 'hidden',
                id: 'nIdCliente',
                value: form.getId()
            }
            controls[controls.length] = c;
            return controls;
        }
        
         <?php 	$obj =& get_instance();
         $obj->load->model('clientes/M_descuento', 'ml');
         $modelo = $obj->ml->get_data_model(array('nIdCliente'));
         ?>
         
         var descuentos = <?php echo extjs_creategrid($modelo, $id . '_descuentos', $this->lang->line('Descuentos'), 'icon-descuentos', 'clientes/descuento', $obj->ml->get_id(), null, FALSE, 'fn_add');?>;
         
        descuentos.anchor = '100% 30%';

         <?php 	$obj =& get_instance();
         $obj->load->model('clientes/M_clientetarifa', 'ml2');
         $modelo = $obj->ml2->get_data_model(array('nIdCliente'));
         ?>
         
         var tarifas = <?php echo extjs_creategrid($modelo, $id . '_tarifas', $this->lang->line('Tarifas'), null, 'clientes/clientetarifa', $obj->ml2->get_id(), null, FALSE, 'fn_add');?>;
         
         tarifas.anchor = '100% 30%';        
        
        var cuenta = new Ext.form.TextField({
            name: 'nIdCuenta',
            id: 'nIdCuenta',
            allowBlank: true
        });
        
        var fn_crear_cuenta = function(){
            var t = Ext.getCmp(tipocliente.id);
            var tipo = t.getValue();
            if (tipo == null || tipo == -1 || tipo == '') {
                Ext.app.msgFly(title, _s('error-no-tipo-cliente'));
                t.focus();
                return;
            }
            Ext.app.callRemote({
                url: site_url('clientes/cliente/cuenta'),
                params: {
                    tipo: tipo
                },
                nomsg: true,
                title: title,
                fnok: function(obj){
                    if (obj.success) {
                        cuenta.setValue(obj.message);
                        form.setDirty();
                    }
                    else {
                        Ext.app.msgError(title, _s('registro_error') + ': ' +
                        obj.message);
                    }
                }
            });
        }
        var nombre = new Ext.form.TextField({
                xtype: 'textfield',
                name: 'cNombre',
                width: 300,
                allowBlank: true,
                fieldLabel: _s('cNombre')
            });
        var apellido = new Ext.form.TextField({
                xtype: 'textfield',
                width: 300,
                name: 'cApellido',
                allowBlank: true,
                fieldLabel: _s('cApellido')
          });
        var controls = [{
            xtype: 'compositefield',
            anchor: '-20',
            defaults: {
                flex: 1
            },
            items: [nombre, apellido, {
				xtype : 'button',
				iconCls : 'icon-split',
				width : 30,
				handler : function () {
					var a = apellido;
					var n = nombre;
					part_names(n, a);					
				}
			}, {
				xtype : 'button',
				iconCls : 'icon-clean',
				width : 30,
				handler : function () {
					apellido.setValue(ucwords(apellido.getValue().toLowerCase()));
					nombre.setValue(ucwords(nombre.getValue().toLowerCase()));
				}
			}]
        }, {
            xtype: 'textarea',
            /*grow: true,*/
            height: 40,
            id: 'cEmpresa',
            anchor: '50%',
            allowBlank: true,
            fieldLabel: _s('cEmpresa')
        }, {
            xtype: 'compositefield',
            fieldLabel: _s('NIF'),
            anchor: '-20',
            items: [{
                xtype: 'textfield',
                allowBlank: true,
                id: 'cNIF'
            }, {
                xtype: 'displayfield',
                value: _s('bExentoIVA')
            }, {
                xtype: 'checkbox',
                id: 'bExentoIVA',
                //anchor: '90%',
                allowBlank: true
            }, {
                xtype: 'displayfield',
                value: _s('bRecargo')
            }, {
                xtype: 'checkbox',
                id: 'bRecargo',
                //anchor: '90%',
                allowBlank: true
            }, {
                xtype: 'displayfield',
                value: _s('bCuenta')
            }, {
                xtype: 'checkbox',
                id: 'bCredito',
                //anchor: '90%',
                allowBlank: true
            }, {
                xtype: 'displayfield',
                value: _s('nIdCuenta')
            }, cuenta, {
                xtype: 'button',
                iconCls: 'icon-new',
                width: 30,
                text: _s('crear-cuenta'),
                handler: fn_crear_cuenta
            }]
        }, {
            xtype: 'compositefield',
            anchor: '-20',
            fieldLabel: _s('bNoCarta'),
            
            items: [{
                xtype: 'checkbox',
                id: 'bNoCarta',
                //anchor: '90%',
                allowBlank: true
            }, {
                xtype: 'displayfield',
                value: _s('bNoEmail')
            }, {
                xtype: 'checkbox',
                id: 'bNoEmail',
                //anchor: '90%',
                allowBlank: true
            }, {
                xtype: 'displayfield',
                value: _s('cPassword')
            }, {
                xtype: 'textfield',
                id: 'cPass',
                allowBlank: true
            }, {
                xtype: 'displayfield',
                value: _s('nIdEstado')
            }, estado]
        }, {
            xtype: 'compositefield',
            anchor: '-20',
            fieldLabel: _s('Tipo Cliente'),
            items: [tipocliente, {
                xtype: 'displayfield',
                value: _s('Grupo Cliente')
            }, grupocliente]
        }, {
			xtype: 'compositefield',
			anchor: '-20',
			fieldLabel: _s('nIdTipoTarifa'),
			items: [tarifa, {
                xtype: 'displayfield',
                value: _s('Idioma')
            },idioma, {
				xtype: 'displayfield',
				value: _s('bExamen')
			}, {
				xtype: 'checkbox',
				id: 'bExamen',
				allowBlank: true
			},{
                xtype: 'displayfield',
                value: _s('nIdWeb')
            }, {
                xtype: 'textfield',
                cls : 'static-info-field',
                readOnly : true,
                width: 60,
                id: 'nIdWeb',
                allowBlank: true
            }]
		},
		<?php if ($this->config->item('bp.concursos.database')!= NULL):?>
		{
			xtype: 'compositefield',
			anchor: '-20',
			fieldLabel: _s('Saldo 1'),
			items: [{
                xtype: 'textfield',
                id: 'fImporte1',
                allowBlank: true
            }, {
				xtype: 'displayfield',
				value: _s('Saldo 2')
			}, {
                xtype: 'textfield',
                id: 'fImporte2',
                allowBlank: true
            }, {
				xtype: 'displayfield',
				value: _s('cReferencia')
			}, {
                xtype: 'textfield',
                id: 'cRandom',
                allowBlank: true
            }]
		} ,	
		<?php endif;?>
		tarifas, descuentos		
		];
        
        // General
        form.addTab({
            title: _s('General'),
            iconCls: 'icon-general',
            items: {
                xtype: 'panel',
                layout: 'form',
				cls: 'form-cliente',
                items: form.addControls(controls)
            }
        });
        
        // Perfiles
        
        form.addTab(new Ext.Panel({
            layout: 'border',
            id: form_id + "_perfiles",
            title: _s('Perfiles'),
            iconCls: 'icon-perfiles',
            region: 'center',
            baseCls: 'x-plain',
            frame: true,
            items: grid_perfiles
        }));
        
        // Temas
        form.addTab(new Ext.Panel({
            layout: 'border',
            id: form_id + "_temas",
            title: _s('Temas'),
            iconCls: 'icon-temas',
            region: 'center',
            baseCls: 'x-plain',
            frame: true,
            items: grid_temas
        }));
        
        // Notas
        var notas1 = {
            xtype: 'textarea',
            id: 'tNotas',
            anchor: '100% 91%'
        };
        form.addTab({
            title: _s('tNotas'),
            iconCls: 'icon-notes',
            items: form.addControls([notas1])
        });
        
        var grid_notas = notas.init({
            id: form_id + "_notas2",
            url: site_url('clientes/cliente'),
            mainform: form
        });
        
        // Usuarios
        form.addTabUser();
        
        form.addTab(new Ext.Panel({
            layout: 'border',
            id: form_id + "_notas3",
            title: _s('Histórico'),
            iconCls: 'icon-history',
            region: 'center',
            baseCls: 'x-plain',
            frame: true,
            items: grid_notas
        }));
        
        // Búsqueda        
        var fn_open = function(id){
            form.load(id);
            form.selectTab(0);
        }
        
         <?php $modelo = $this->reg->get_data_model(array('nIdTratamiento', 'cRandom', 'cPass', 'bExentoIVA', 'fImporte1', 'fImporte2', 'nIdIdioma'));?>
         var grid_search = <?php echo extjs_creategrid($modelo, $id.'_g_search', null, null, 'clientes.cliente', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
         
        form.addTab({
            title: _s('Búsqueda'),
            iconCls: 'icon-search',
            items: Ext.app.formSearchForm({
                grid: grid_search,
                id_grid: form_id + '_g_search_grid'
            })
        });
        
        /*-------------------------------------------------------------------------
         * Comandos
         *-------------------------------------------------------------------------
         */
		
		var fn_documentos_art = function(){
            if (form.getId() != null) {
                Ext.app.callRemote({
					params: {idc:   form.getId()},
                    url: site_url('clientes/cliente/documentos_articulos')
                });
            }
        }
        form.addAction({
            text: _s('Documentos cliente'),
            iconCls: 'icon-documents',
            handler: fn_documentos_art,
            id: form_id + 'btn_documentos'
        });

        form.addAction('-');
		var fn_suscripciones = function(){
            if (form.getId() != null) {
                Ext.app.callRemote({
					params: {cliente:   form.getId()},
                    url: site_url('suscripciones/suscripcion/buscar')
                });
            }
        }
        form.addAction({
            text: _s('Suscripciones'),
            iconCls: 'iconoSuscripciones',
            handler: fn_suscripciones,
            id: form_id + 'btn_suscripciones'
        });
		var fn_facturar_suscripciones = function(){
            if (form.getId() != null) {
                Ext.app.callRemote({
					params: {
						id: form.getId()
					},
                    url: site_url('suscripciones/suscripcion/facturar')
                });
            }
        }
        form.addAction({
            text: _s('Facturar Suscripciones'),
            iconCls: 'iconoSuscripcionesFacturarAlbaranes',
            handler: fn_facturar_suscripciones,
            id: form_id + 'btn_fac_suscripciones'
        });

        var fn_contenidocesta = function(){
            Ext.app.callRemote({
                url: site_url('clientes/cliente/cesta'),
                params: {
                    id: form.getId()
                }
            });
        }
        
        var fn_contenidofavoritos = function(){
            Ext.app.callRemote({
                url: site_url('clientes/cliente/favoritos'),
                params: {
                    id: form.getId()
                }
            });        
        }

        var fn_passwordweb = function(){
            Ext.app.callRemote({
                url: site_url('clientes/cliente/passwordweb'),
                params: {
                    id: form.getId()
                }
            });        
        }

        var fn_mailing = function(){
            Ext.app.callRemote({
                url: site_url('clientes/cliente/mailings'),
                params: {
                    id: form.getId()
                }
            });        
        }

        var fn_sendweb = function(){
            Ext.app.callRemote({
                url: site_url('web/webpage/publicarcliente'),
                params: {
                    id: form.getId()
                },
                fnok: function() {
                    form.refresh();
                }
            });        
        }
        
        form.addTools({
            text: _s('Contenido de la cesta de Internet'),
            iconCls: 'icon-cart',
            handler: fn_contenidocesta,
            id: form.idform + 'btn_contenido_cesta'
        });
        
        form.addTools({
            text: _s('Contenido de los favoritos de Internet'),
            iconCls: 'icon-bookmark',
            handler: fn_contenidofavoritos,
            id: form.idform + 'btn_contenido_favoritos'
        });

        form.addTools('-');

        form.addTools({
            text: _s('Publicar cliente en Internet'),
            iconCls: 'icon-publish',
            handler: fn_sendweb,
            id: form.idform + 'btn_send_web'
        });
        form.addTools({
            text: _s('Cambiar contraseña Internet'),
            iconCls: 'icon-password',
            handler: fn_passwordweb,
            id: form.idform + 'btn_password_web'
        });
        form.addTools('-');
        form.addTools({
            text: _s('Sincronizar pedidos'),
            iconCls: 'icon-publish',
            handler: function() {
                Ext.app.callRemote({
                    url: site_url('web/webpage/syncro_pedidos'),
                    params: {
                        cliente: form.getId()
                    }
                });
            },

            id: form.idform + 'btn_sincro'
        });
        form.addTools('-');

        form.addTools({
            text: _s('Boletines enviados'),
            iconCls: 'icon-email',
            handler: fn_mailing,
            id: form.idform + 'btn_emails_enviados'
        });


        
        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
