(function(){

    try {
        var open_id = "<?php echo $open_id;?>";
        var form_id = "<?php echo $id;?>";
        var title = "<?php echo $title;?>";
        var icon = "<?php echo $icon;?>";
        if (title == '') 
            title = _s('Trabajadores');
        if (icon == '') 
            icon = 'iconoTrabajadoresTab';
        
        /**
         * Crea un calendario de un trabajador
         *
         * @param {Object}
         *            padre
         */
        var CrearCalendario = function(padre){
            var from = new Date();
            var end = new Date(1900 + from.getYear(), 11, 31);
            var controls = [{
                xtype: 'hidden',
                name: 'id',
				value: padre.getId()
            }, {
                xtype: 'datefield',
				startDay: Ext.app.DATESTARTDAY,
                name: 'desde',
                value: from,
                allowBlank: false,
                fieldLabel: _s('Desde')
            }, {
                xtype: 'datefield',
				startDay: Ext.app.DATESTARTDAY,
                name: 'hasta',
                value: end,
                allowBlank: false,
                fieldLabel: _s('Hasta')
            }, {
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    bodyStyle: 'background:transparent;padding:2px',
                    border: false,
                    items: [{
                        xtype: 'textfield',
                        id: 'd1',
                        width: 60,
                        fieldLabel: _s('Lunes')
                    }, {
                        xtype: 'textfield',
                        id: 'd2',
                        width: 60,
                        fieldLabel: _s('Martes')
                    }, {
                        xtype: 'textfield',
                        id: 'd3',
                        width: 60,
                        fieldLabel: _s('Miércoles')
                    }, {
                        xtype: 'textfield',
                        id: 'd4',
                        width: 60,
                        fieldLabel: _s('Jueves')
                    }, {
                        xtype: 'textfield',
                        id: 'd5',
                        width: 60,
                        fieldLabel: _s('Viernes')
                    }, {
                        xtype: 'textfield',
                        id: 'd6',
                        width: 60,
                        fieldLabel: _s('Sábado')
                    }, {
                        xtype: 'textfield',
                        id: 'd7',
                        width: 60,
                        fieldLabel: _s('Domingo')
                    }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    bodyStyle: 'background:transparent;padding:2px',
                    border: false,
                    items: [{
                        xtype: 'checkbox',
                        id: 't1',
                        fieldLabel: _s('Tarde Lunes')
                    }, {
                        xtype: 'checkbox',
                        id: 't2',
                        fieldLabel: _s('Tarde Martes')
                    }, {
                        xtype: 'checkbox',
                        id: 't3',
                        fieldLabel: _s('Tarde Miércoles')
                    }, {
                        xtype: 'checkbox',
                        id: 't4',
                        fieldLabel: _s('Tarde Jueves')
                    }, {
                        xtype: 'checkbox',
                        id: 't5',
                        fieldLabel: _s('Tarde Viernes')
                    }, {
                        xtype: 'checkbox',
                        id: 't6',
                        fieldLabel: _s('Tarde Sábado')
                    }, {
                        xtype: 'checkbox',
                        id: 't7',
                        fieldLabel: _s('Tarde Domingo')
                    }]
                }]
            }];
            
            var form = Ext.app.formStandarForm({
                controls: controls,
                title: _s('Crear Calendario'),
                fn_ok: function(){
                    reload();
                },
                url: site_url('calendario/trabajador/crear_calendario')
            });
            form.show();
        }
        
        /**
         * Crea las vacaciones de un trabajador
         *
         * @param {Object}
         *            padre
         */
        var CrearVacaciones = function(padre){
            var controls = [{
                xtype: 'hidden',
                value: padre.getId(),
                name: 'id'
            }, {
                xtype: 'datefield', startDay: Ext.app.DATESTARTDAY,
                name: 'desde',
                value: new Date(),
                allowBlank: false,
                fieldLabel: _s('Desde')
            }, {
                xtype: 'datefield', startDay: Ext.app.DATESTARTDAY,
                name: 'hasta',
                value: new Date(),
                allowBlank: false,
                fieldLabel: _s('Hasta')
            }];
            
            var form = Ext.app.formStandarForm({
                controls: controls,
                title: _s('Crear Vacaciones'),
                fn_ok: function(){
                    Ext.getCmp(form_id + '_cal_grid').getStore().load({
                        waitMsg: _s('Cargando'),
                        callback: function(){
                            g.doLayout();
                        }
                    });
                },
                url: site_url('calendario/trabajador/crear_vacaciones')
            });
            form.show();
        }
        
        /**
         * Asigna los sábados
		 * @param {Object} padre
		 * @param {Object} todos
		 */
        var AsignarSabados = function(padre, todos){
            var from = new Date();
            var end = new Date(1900 + from.getYear(), 11, 31);
            var controls = [{
                xtype: 'datefield', startDay: Ext.app.DATESTARTDAY,
                name: 'desde',
                value: from,
                allowBlank: false,
                fieldLabel: _s('Desde')
            }, {
                xtype: 'datefield', startDay: Ext.app.DATESTARTDAY,
                name: 'hasta',
                value: end,
                allowBlank: false,
                fieldLabel: _s('Hasta')
            }, {
                xtype: 'textfield',
                name: 'horas',
                allowBlank: false,
                fieldLabel: _s('Horas')
            }];
            
			if (todos == null || todos === false)
			{
				controls[controls.length] = {
                xtype: 'hidden',
                value: padre.getId(),
                name: 'id'
            } 
			}
			
            var form = Ext.app.formStandarForm({
                controls: controls,
                icon: 'icon-calendar-sabados',
                title: _s('asignar-sabados'),
                fn_ok: function(){
                    reload();
                },
                url: site_url('calendario/trabajador/crear_sabados')
            });
            form.show();
        }
        
        // Grids
        var list_grids = [form_id + '_cal_grid', form_id + '_inc_grid', form_id + '_h_grid', form_id + '_ha_grid']
        
        var list_grids2 = [form_id + '_cal_grid', form_id + '_inc_grid', form_id + '_h_grid', form_id + '_cal2', form_id + '_ha_grid']
        
        // Función de reset elementos
        var fn_reset = function(id){
            Ext.app.formResetList({
                list: list_grids2,
                params: {
                    where: 'nIdTrabajador=-1'
                }
            });
        };
        
        // Función de carga
        var fn_load = function(id){
            Ext.app.formLoadList({
                list: list_grids,
                params: {
                    where: 'nIdTrabajador=' + id
                }
            });
            
            reload();
        };
        
        // Menús
        var list_buttons = [form_id + 'btn_consultar', form_id + 'btn_resumen', form_id + 'btn_add', form_id + 'btn_asignar', form_id + 'btn_elmcal', 
		form_id + '_btn_new_cal' , form_id + 'btn_new'];
        
        var fn_enable_disable = function(){
            Ext.app.formEnableList({
                list: list_grids2,
                enable: (form.getId() > 0)
            });
            
            Ext.app.formEnableList({
                list: list_buttons,
                enable: (form.getId() > 0)
            });
        };
        
        /**
         * Muestra un resumen de las horas de un trabajador
         *
         * @param {Object}
         *            form
         */
        var verResumen = function(form){
            Ext.Msg.prompt(_s('Resumen Año'), _s('Resumen Año'), function(ok, v){
                if (ok != 'ok') 
                    return;
                var id = form.getId();
                Ext.app.callRemote({
                    params: {
                        id: parseInt(id),
                        year: v
                    },
                    title: _s('Año'),
                    url: site_url('calendario/trabajador/resumen')
                })
            }, null, null, (new Date()).getYear() + 1900);
        }
        
        /**
         * Elimina un calendario
         *
         * @param {Object}
         *            form
         */
        var elmCalendario = function(padre){
            var from = new Date();
            var end = new Date(1900 + from.getYear(), 11, 31);
            var controls = [{
                xtype: 'hidden',
                name: 'id',
				value: padre.getId()				
            }, {
                xtype: 'datefield', startDay: Ext.app.DATESTARTDAY,
                name: 'desde',
                value: from,
                allowBlank: false,
                fieldLabel: _s('Desde')
            }, {
                xtype: 'datefield', startDay: Ext.app.DATESTARTDAY,
                name: 'hasta',
                value: end,
                allowBlank: false,
                fieldLabel: _s('Hasta')
            }];
            
            var form = Ext.app.formStandarForm({
                controls: controls,
                title: _s('elm-calendario'),
                fn_ok: function(){
                    reload();
                },
                url: site_url('calendario/trabajador/eliminar_calendario')
            });
            form.show();
        }
        
        /*var liquidarHoras = function(padre){
            var years = Ext.app.combobox({
                url: site_url('calendario/dia/years'),
                id: 'year',
                label: _s('Año')
            });
            var controls = [years, {
                xtype: 'textfield',
                id: 'msg',
                allowBlank: false,
                fieldLabel: _s('cDescripcion')
            }];
            
            var form = Ext.app.formStandarForm({
                controls: controls,
                title: _s('liquidar-horas'),
                fn_ok: function(){
                    if (padre.getId() != null) {
                        Ext.app.formLoadList({
                            list: [form_id + '_h_grid'],
                            params: {
                                where: 'nIdTrabajador=' + padre.getId()
                            }
                        });
                    }
                },
                url: site_url('calendario/calendario/liquidar_horas')
            });
            years.store.load();
            form.show();
        }*/
        
        /**
         * Estado de las horas de un año
         * @param {Object} form
         */
        var verEstadoHoras = function(form){
            Ext.Msg.prompt(_s('estado-horas'), _s('Año'), function(ok, v){
                if (ok != 'ok') 
                    return;
                var id = form.getId();
                Ext.app.callRemote({
                    params: {
                        year: v
                    },
                    title: _s('estado-horas'),
                    url: site_url('calendario/calendario/estado_horas')
                })
            }, null, null, (new Date()).getYear() + 1900);
        }
        
        // Formulario
        var form = Ext.app.formGeneric();
        
        form.init({
            id: form_id,
            title: title,
            icon: icon,
            url: site_url('calendario/trabajador'),
            fn_load: fn_load,
            fn_reset: fn_reset,
            fn_enable_disable: fn_enable_disable
        });
        
        // Conntroles Combos
        var turnos = Ext.app.combobox({
            url: site_url('calendario/turno/search'),
            id: 'nIdTurno',
            label: _s('Turno')
        });
        var grupos = Ext.app.combobox({
            url: site_url('calendario/grupostrabajador/search'),
            id: 'nIdGrupo',
            label: _s('Grupo')
        });
        var emails = Ext.app.combobox({
            url: site_url('calendario/email/search'),
            id: 'nIdEmail',
            label: _s('Email')
        });
        
        // Controles normales
        var controls = [{
            xtype: 'textfield',
            id: 'cNombre',
            anchor: '90%',
            allowBlank: false,
            fieldLabel: _s('cNombre')
        }, {
            xtype: 'datefield', startDay: Ext.app.DATESTARTDAY,
            id: 'dInicio',
            allowBlank: true,
            fieldLabel: _s('dInicio')
        }, {
            xtype: 'datefield', startDay: Ext.app.DATESTARTDAY,
            id: 'dFinal',
            allowBlank: true,
            fieldLabel: _s('dFinal')
        }, {
            xtype: 'textfield',
            id: 'cUsername',
            // anchor : '90%',
            allowBlank: true,
            fieldLabel: _s('cUsername')
        }, {
            xtype: 'checkbox',
            id: 'bActivo',
            allowBlank: true,
            fieldLabel: _s('bActivo')
        }, {
            xtype: 'textfield',
            id: 'cTelefonoFijo',
            // anchor : '90%',
            allowBlank: true,
            fieldLabel: _s('cTelefonoFijo')
        }, {
            xtype: 'textfield',
            id: 'cTelefonoMovil',
            // anchor : '90%',
            allowBlank: true,
            fieldLabel: _s('cTelefonoMovil')
        }, {
            xtype: 'textfield',
            id: 'cExtension',
            // anchor : '90%',
            allowBlank: true,
            fieldLabel: _s('cExtension')
        }, {
            xtype: 'textfield',
            id: 'cEmail',
            anchor: '90%',
            allowBlank: true,
            fieldLabel: _s('cEmail')
        }, grupos, turnos, emails];
        
        // Notas
        var notas = [{
            fieldLabel: _s('Notas'),
            xtype: 'htmleditor',
            id: 'tNotas',
            anchor: '100% 91%'
        }];
         // Datos calendario
         <?php
         $data['name'] = 'dst';
         $data['id'] = 'id';
         $data['url'] = site_url('calendario/calendario/calendario_trabajador');
         $data['fields'][] = array('name' => 'id');
         $data['fields'][] = array('name' => 'dDia', 'type' => 'date');
         $data['fields'][] = array('name' => 'dDia2');
         $data['fields'][] = array('name' => 'Dia');
         $data['fields'][] = array('name' => 'Numero');
         $data['fields'][] = array('name' => 'Mes');
         $data['fields'][] = array('name' => 'MesNumero');
         $data['fields'][] = array('name' => 'nIdFestivo');
         $data['fields'][] = array('name' => 'nIdVacaciones');
         $data['fields'][] = array('name' => 'fHoras');
         $data['fields'][] = array('name' => 'cDescripcion');
         $data['fields'][] = array('name' => 'cComentario');
         $data['fields'][] = array('name' => 'bTarde', 'type' => 'bool');
         echo extjs_createjsonreader($data, true, 'MesNumero', 'Mes');
         ?>
        function reload(button){
            var id = form.getId();
			if (id == null) return;
            try {
                var d = Ext.getCmp(form_id + "_cal_fecha").getRawValue();
            } 
            catch (e) {
                var d = new Date();
            }
            
            if (d == '' || id == null) {
                Ext.app.msgFly("<?php echo $title;?>", _s('mensaje_faltan_datos'));
                return;
            }
            
            var b = Ext.getCmp(form_id + "_cal_btn");
            if (b != null) 
                b.disable();
            var g = Ext.getCmp(form_id + '_cal2');
            var dst = g.getStore();
            
            dst.baseParams = {
                id: id,
                fecha1: d
            };
            dst.removeAll();
            dst.load({
                waitMsg: _s('Cargando'),
                callback: function(){
                    if (b != null) 
                        b.enable();
                }
            });
        };
        
        var bar = [{
            xtype: 'label',
            html: _s('Fecha')
        }, {
            id: form_id + "_cal_fecha",
            value: new Date(),
            //dateFormat: 'd/m/Y',
			startDay: Ext.app.DATESTARTDAY,
            xtype: "datefield"
        }, '-', {
            tooltip: _s('cmd-calcular'),
			text : _s('Actualizar'),
			iconCls : 'icon-actualizar',
            id: form_id + "_cal_btn",
            listeners: {
                click: function(b){
                    reload(b);
                }
            }
        }];
        
        var checkActivo = Ext.app.checkColumn('bTarde', _s('Tarde'));
        
        Ext.grid.GroupSummary.Calculations['totalCost'] = function(v, record, field){
            return v + parseFloat(record.data.fHoras);
        }
        
        var summary = new Ext.grid.GroupSummary();
        
        var calendario = {
            region: 'center',
            xtype: 'editorgrid',
            id: form_id + "_cal2",
            autoExpandColumn: "descripcion",
            loadMask: true,
            stripeRows: true,
            clickstoEdit: 1,
            store: dst,
            bbar: Ext.app.gridBottom(dst, false),
            tbar: Ext.app.gridStandarButtons({
                id: form_id + "_cal2",
                bar: bar,
                title: _s('Calendario')
            }),
            plugins: [summary],
            sm: new Ext.grid.RowSelectionModel({
                singleSelect: true
            }),
            columns: [{
                header: _s('Id'),
                width: Ext.app.TAM_COLUMN_ID,
                dataIndex: 'nIdTrabajador',
                hidden: true,
                sortable: true
            }, {
                header: _s('Número Mes'),
                width: Ext.app.TAM_COLUMN_ID,
                dataIndex: 'MesNumero',
                renderer: function(v, m, r){
                    if (r != null) 
                        return r.data.Mes;
                    else 
                        return v;
                },
                hidden: true,
                sortable: true
            }, {
                header: _s('Mes'),
                width: Ext.app.TAM_COLUMN_DATE,
                dataIndex: 'Mes',
                summaryType: 'max',
                sortable: true
            }, {
                header: _s('Dia'),
                width: Ext.app.TAM_COLUMN_DATE,
                dataIndex: 'Dia',
                sortable: true
            }, {
                header: _s('Tarde'),
                width: Ext.app.TAM_COLUMN_BOOL,
                dataIndex: 'bTarde',
                editor: new Ext.form.Checkbox(),
                renderer: Ext.app.renderCheck,
                sortable: false
            }, {
                header: _s('Fecha'),
                width: Ext.app.TAM_COLUMN_DATE,
                dataIndex: 'dDia2',
                summaryType: 'max',
                // renderer : Ext.app.renderDateShort,
                sortable: true
            }, {
                header: _s('Horas'),
                width: Ext.app.TAM_COLUMN_NUMBER,
                dataIndex: 'fHoras',
                editor: new Ext.form.NumberField({
                    allowBlank: false,
                    allowNegative: false,
                    style: 'text-align:left'
                
                }),
                renderer: function(v){
                    return v + " " + _s('horas');
                },
                
                summaryType: 'totalCost',
                sortable: true
            }, {
                header: _s('Descripción'),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'cDescripcion',
                sortable: false
            }, {
                header: _s('Comentario'),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'cComentario',
                editor: new Ext.form.TextField(),
                id: 'descripcion',
                sortable: false
            }],
            listeners: {
                afteredit: function(e){
                    var ed = false;
                    var params = {};
                    params['id'] = e.record.data.id;
                    if ((is_null(e.value, '') != is_null(e.originalValue, ''))) {
                        params[e.field] = e.value;
                        ed = true;
                    }
                    if (ed) {
                        var url = "<?php echo site_url('calendario/calendario/upd');?>";
                        Ext.app.callRemote({
                            url: url,
                            title: "<?php echo $title;?>",
                            waitmessage: _s('Actualizando'),
                            params: params,
                            fnok: function(){
                                e.record.commit();
                            },
                            fnnok: function(){
                                e.record.reject();
                            }
                        });
                    }
                    else {
                        e.record.commit();
                    }
                }
            },
            view: new Ext.grid.GroupingView({
                enableRowBody: true,
                getRowClass: function(r, rowIndex, rowParams, store){
                    //console.log('cell-calendario-' + r.data.Numero);
                    if (r != null) 
                        return 'cell-calendario-' + r.data.Numero;
                },
                forceFit: true,
                hideGroupedColumn: true
            })
        };
        
        var fn = function(m){
            return m;
        }
        
        var fn_add = function(controls){
            // console.log('En ADD');
            var c = {
                xtype: 'hidden',
                id: 'nIdTrabajador',
                value: form.getId()
            }
            controls[controls.length] = c;
            return controls;
        }
         <?php 	$obj =& get_instance();
         $obj->load->model('calendario/M_vacaciones', 'vacas');
         $modelo = $obj->vacas->get_data_model(array('nIdTrabajador'));
         ?>
         var vacaciones = <?php echo extjs_creategrid($modelo, $id . '_cal', $this->lang->line('Vacaciones'), 'icon-vacaciones', 'calendario/vacaciones', $obj->vacas->get_id(), 'fn', FALSE, 'fn_add');?>;
         <?php $obj->load->model('calendario/M_incidencia', 'inci');
         $modelo = $obj->inci->get_data_model(array('nIdTrabajador'));
         ?>
         var incidencias = <?php echo extjs_creategrid($modelo, $id . '_inc', $this->lang->line('Incidencias'), 'icon-incidencias', 'calendario/incidencia', $obj->inci->get_id(), null, FALSE, 'fn_add');?>;
         <?php
         $obj =& get_instance();
         $obj->load->model('calendario/M_horaextratrabajador', 'hora');
         $modelo = $obj->hora->get_data_model(array('nIdTrabajador'));
         ?>
         var horas = <?php echo extjs_creategrid($modelo, $id . '_h', $this->lang->line('Horas Extras'), 'icon-horas', 'calendario/horaextratrabajador', $obj->hora->get_id(), null, FALSE, 'fn_add');?>;
         <?php
         $obj =& get_instance();
         $obj->load->model('calendario/M_horasanuales', 'hora_a');
         $modelo = $obj->hora_a->get_data_model(array('nIdTrabajador'));
         ?>
         var horas_anuales = <?php echo extjs_creategrid($modelo, $id . '_ha', $this->lang->line('Horas Anuales'), 'icon-horasanaules', 'calendario/horasanuales', $obj->hora_a->get_id(), null, FALSE, 'fn_add');?>;

        // Tabs
        form.addTab({
            title: _s('General'),
            iconCls: 'icon-general',
            items: {
                xtype: 'panel',
                layout: 'form',
                items: form.addControls(controls)
            }
        });
        
        form.addTab(new Ext.Panel({
            layout: 'border',
            xtype: 'editorgrid',
            id: form_id + "_cal2_p",
            title: _s('Calendario'),
            iconCls: 'icon-calendar',
            region: 'center',
            baseCls: 'x-plain',
            frame: true,
            items: calendario
        }));
        
        form.addTab(vacaciones);
        form.addTab(horas);
        form.addTab(incidencias);
        form.addTab(horas_anuales);
        
        form.addTab({
            title: _s('Notas'),
            iconCls: 'icon-notes',
            items: form.addControls(notas)
        });
        
        var fn_open = function(id){
            form.load(id);
            form.selectTab(0);
        }
         <?php
         $obj =& get_instance();
         $obj->load->model('calendario/m_trabajador');
         $modelo2 = $obj->m_trabajador->get_data_model();
         ?>
         
         var grid_search_m = <?php echo extjs_creategrid($modelo2, $id.'_g_search', null, null, 'calendario.trabajador', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;

        form.addTab({
            title: _s('Búsqueda'),
            iconCls: 'icon-search',
            items: Ext.app.formSearchForm({
                grid: grid_search_m,
                id_grid: form_id + '_g_search_grid'
            })
        });
        
        // Herramientas
        form.addAction({
            text: _s('Crear Calendario'),
            handler: function(){
                CrearCalendario(form);
            },
            iconCls: 'icon-new-calendar',
            id: form_id + "_btn_new_cal"
        });
        form.addAction({
                text: _s('Crear Vacaciones'),
                handler: function(){
                    CrearVacaciones(form);
                },
                iconCls: 'icon-new-holidays',
                id: form_id + 'btn_new'
        });
        form.addAction({
            text: _s('asignar-sabados'),
            iconCls: 'icon-calendar-sabados',
            handler: function(){
                AsignarSabados(form, false);
            },
            id: form.idform + 'btn_asignar'
        });
        /*form.addAction({
            text: _s('asignar-sabados-todos'),
            iconCls: 'icon-calendar-sabados',
            handler: function(){
                AsignarSabados(form, true);
            },
            id: form.idform + 'btn_asignar_todos'
        });*/
        form.addAction('-');       

        form.addAction({
            text: _s('elm-calendario'),
            iconCls: 'icon-calendar-del',
            handler: function(){
                elmCalendario(form);
            },
            id: form.idform + 'btn_elmcal'
        });

        form.addAction('-');

        form.addAction({
            text: _s('Resumen Año'),
            iconCls: 'icon-report',
            handler: function(){
                verResumen(form);
            },
            id: form.idform + 'btn_resumen'
        });
                      
        /*form.addAction({
            text: _s('estado-horas'),
            iconCls: 'icon-report',
            handler: function(){
                verEstadoHoras(form);
            },
            id: form.idform + 'btn_estado'
        });*/
        
        /*form.addTools({
            text: _s('liquidar-horas'),
            iconCls: 'icon-tool',
            handler: function(){
                liquidarHoras(form);
            },
            id: form.idform + 'btn_liquidar'
        });*/
        form.addTools({
            text: _s('Consultar'),
            iconCls: 'iconoReport',
            handler: function(){				
                Ext.app.callRemote({
						url: site_url('calendario/trabajador/consultar'),
						params: {
							open_id: form.getId()
							}
						});				
            },
            id: form.idform + 'btn_consultar'
        });
        
        
        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
