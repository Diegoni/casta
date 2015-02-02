(function(){
    try {
        var open_id = "<?php echo isset($open_id)?$open_id:'';?>";
        var form_id = "<?php echo isset($id)?$id:'';?>";
        var title = "<?php echo isset($title)?$title:'';?>";
        var icon = "<?php echo isset($icon)?$icon:'';?>";
        if (title == '') 
            title = _s('Proveedores');
        if (icon == '') 
            icon = 'iconoProveedoresTab';
        
        var perfiles = Ext.app.formPerfiles();
        
        var list_grids = [form_id + '_descuentos_grid', 
            form_id + 'btn_documentos', 
            form_id + 'btn_analisis',
            form_id + 'btn_comprados',
            form_id + 'btn_comprados2',
            form_id + 'btn_suscripciones']

        var notas = Ext.app.formNotas();

        // Carga
        var fn_load = function(id, res){
            notas.load(id);
            perfiles.load(id);
            Ext.app.formLoadList({
                list: list_grids,
                params: {
                    where: 'nIdProveedor=' + parseInt(id),
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
                    where: 'nIdProveedor=-1',
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
            url: site_url('proveedores/proveedor'),
            fn_load: fn_load,
            fn_reset: fn_reset,
            fn_enable_disable: fn_enable_disable
        });
        
         var idioma = Ext.app.comboLangs(null, 'cIdioma');

        // Perfiles
        
        var grid_perfiles = perfiles.init({
            id: form_id + "_perfiles",
            etq: 'proveedor',
            url: site_url('proveedores/perfilproveedor'),
            mainform: form
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
                id: 'nIdProveedor',
                value: form.getId()
            }
            controls[controls.length] = c;
            return controls;
        }        
         
         <?php 	$obj =& get_instance();
         $obj->load->model('catalogo/M_proveedoreditorial', 'ml');
         $modelo = $obj->ml->get_data_model(array('nIdProveedor', 'dCreacion', 'dAct', 'cCUser', 'cAUser'));
         ?>
         
         var descuentos = <?php echo extjs_creategrid($modelo, $id . '_descuentos', $this->lang->line('Descuentos'), 'icon-descuentos', 'catalogo/proveedoreditorial', $obj->ml->get_id(), null, FALSE, 'fn_add');?>;
         
        descuentos.anchor = '100% 50%';
        
        var cuenta = new Ext.form.TextField({
            name: 'nIdCuenta',
            id: 'nIdCuenta',
            allowBlank: true
        });
        
        var fn_crear_cuenta = function(){
            Ext.app.msgFly(title, 'No implementado todavía');
            return;
            var t = Ext.getCmp(tipocliente.id);
            var tipo = t.getValue();
            Ext.app.callRemote({
                url: site_url('proveedores/proveedor/cuenta'),
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
        
        var controls = [{
            xtype: 'compositefield',
            anchor: '-20',
            defaults: {
                flex: 1
            },
            items: [{
                xtype: 'textfield',
                id: 'cNombre',
                allowBlank: true,
                fieldLabel: _s('cNombre')
            }, {
                xtype: 'textfield',
                id: 'cApellido',
                //anchor: '90%',
                allowBlank: true,
                fieldLabel: _s('cApellido')
            }]
        }, {
            xtype: 'textarea',
            grow: true,
            id: 'cEmpresa',
            anchor: '90%',
            allowBlank: true,
            fieldLabel: _s('cEmpresa')
        }, {
            fieldLabel: _s('NIF'),
            xtype: 'textfield',
            allowBlank: true,
            id: 'cNIF'
        }, {
            xtype: 'compositefield',
            fieldLabel: _s('bRecargo'),
            items: [            {
                xtype: 'checkbox',
                id: 'bRecargo',
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
            fieldLabel: _s('cSINLI'),
            items: [{
                xtype: 'textfield',
                id: 'cSINLI',
                width: 150,
                allowBlank: true,
            }, {
                xtype: 'displayfield',
                value: _s('cSINLIBuzon')
            }, {
                xtype: 'textfield',
                id: 'cSINLIBuzon',
                width: 300,
                allowBlank: true
            }, {
                xtype: 'displayfield',
                value: _s('bEnviarSINLI')
            }, {
                xtype: 'checkbox',
                id: 'bEnviarSINLI',                
                allowBlank: true
            }, {
                xtype: 'displayfield',
                value: _s('bEnviarSINLIDep')
            }, {
                xtype: 'checkbox',
                id: 'bEnviarSINLIDep',
                allowBlank: true
            }]
        }, {
            xtype: 'compositefield',
            fieldLabel: _s('Descuento'),
            items: [{
                xtype: 'numberfield',
                id: 'fDescuento',
                allowBlank: true,
                allowNegative: false,
                allowDecimals: true,
                width: 30,
                maxValue: 100,
                minValue: 0,
                decimalPrecision: Ext.app.DECIMALS,
                fieldLabel: _s('Descuento')
            }, {
                xtype: 'displayfield',
                value: _s('fCompraMinima')
            }, {
                xtype: 'numberfield',
                id: 'fCompraMinima',
                allowBlank: true,
                allowNegative: false,
                allowDecimals: true,
                width: 30,
                minValue: 0,
                decimalPrecision: Ext.app.DECIMALS,
                fieldLabel: _s('fCompraMinima')
            }, {
                xtype: 'displayfield',
                value: _s('bDisabled')
            }, {
                xtype: 'checkbox',
                id: 'bDisabled',
                allowBlank: true
            }]
        }, idioma, Ext.app.formHtmlEditor({
            id: 'tComentario',
            anchor: '100% 50%'
        })[0]];
        
        // General
        form.addTab({
            title: _s('General'),
            iconCls: 'icon-general',
            items: {
                xtype: 'panel',
                layout: 'form',
                cls: 'form-proveedor',
                items: form.addControls(controls)
            }
        });
        
        // Notas
        // descuentos        
        form.addTab({
            title: _s('Descuentos'),
            iconCls: 'icon-discount',
            items: form.addControls([descuentos])
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
        
        var grid_notas = notas.init({
            id: form_id + "_notas2",
            url: site_url('proveedores/proveedor'),
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
        
         <?php $modelo = $this->reg->get_data_model(array('tComentario', 'cRandom'));?>
         var grid_search = <?php echo extjs_creategrid($modelo, $id.'_g_search', null, null, 'proveedores.proveedor', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
        
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
					params: {idp:   form.getId()},
                    url: site_url('proveedores/proveedor/documentos_articulos')
                });
            }
        }
        form.addAction({
            text: _s('Documentos artículos'),
            iconCls: 'icon-documents',
            handler: fn_documentos_art,
            id: form_id + 'btn_documentos'
        });
        form.addAction('-');
		var fn_suscripciones = function(){
            if (form.getId() != null) {
                Ext.app.callRemote({
					params: {proveedor:   form.getId()},
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

        form.addTools({
            text: _s('Análisis'),
            iconCls: 'icon-statistics',
            handler: function(){
                if (form.getId() != null) {
                    var controls = [{
                        fieldLabel: _s('Desde'),
                        name: 'desde',
                        value: new Date(),
                        startDay: Ext.app.DATESTARTDAY,
                        xtype: "datefield"
                    }, {
                        fieldLabel: _s('Hasta'),
                        name: 'hasta',
                        value: new Date(),
                        startDay: Ext.app.DATESTARTDAY,
                        xtype: "datefield"
                    },{
                        name: 'id',
                        value: form.getId(),
                        xtype: "hidden"
                    }];
                    var url = site_url('proveedores/proveedor/analisis');
                    
                    var form2 = Ext.app.formStandarForm({
                        icon: 'icon-statistics',
                        timeout: false,
                        controls: controls,
                        title: _s('Análisis'),
                        url: url
                    });
                    
                    form2.show();
                }
            },
            id: form.idform + 'btn_analisis'
        });
        form.addTools({
            text: _s('Artículos comprados en stock'),
            iconCls: 'iconoReport',
            handler: function(){
                if (form.getId() != null) {
                    Ext.app.callRemote({
                        url: site_url('proveedores/proveedor/comprados'),
                        params: {
                            id: form.getId(),
                            stock: true
                        }
                    });
                }
            },
            id: form.idform + 'btn_comprados'
        });
        form.addTools({
            text: _s('Artículos comprados general'),
            iconCls: 'iconoReport',
            handler: function(){
                if (form.getId() != null) {
                    Ext.app.callRemote({
                        url: site_url('proveedores/proveedor/comprados'),
                        params: {
                            id: form.getId(),
                            stock: false
                        }
                    });
                }
            },
            id: form.idform + 'btn_comprados2'
        });
        
        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
