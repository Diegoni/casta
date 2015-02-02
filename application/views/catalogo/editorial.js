(function(){
    try {
        var open_id = "<?php echo isset($open_id)?$open_id:'';?>";
        var form_id = "<?php echo isset($id)?$id:'';?>";
        var title = "<?php echo isset($title)?$title:'';?>";
        var icon = "<?php echo isset($icon)?$icon:'';?>";
        if (title == '') 
            title = _s('Editorial');
        if (icon == '') 
            icon = 'iconoEditorialesTab';
        
        var list_grids = [form_id + '_descuentos_grid', 
            form_id + 'btn_analisis',
            form_id + '_codigos_grid']
        
        // Carga
        var fn_load = function(id){
            Ext.app.formLoadList({
                list: list_grids,
                params: {
                    where: 'nIdEditorial=' + parseInt(id),
                    id: parseInt(id)
                }
            });
        }
        
        // Borrado
        var fn_reset = function(){
            Ext.app.formResetList({
                list: list_grids,
                params: {
                    where: 'nIdEditorial=-1',
                    id: -1
                }
            });
        }
        
        var fn_enable_disable = function(form){
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
            url: site_url('catalogo/editorial'),
            fn_load: fn_load,
            fn_reset: fn_reset,
            fn_enable_disable: fn_enable_disable
        });
                
        // Descuentos                
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
                id: 'nIdEditorial',
                value: form.getId()
            }
            controls[controls.length] = c;
            return controls;
        }        
         
         <?php 	$obj =& get_instance();
         $obj->load->model('catalogo/M_editorialcodigo', 'ml2');
         $modelo = $obj->ml2->get_data_model(array('nIdEditorial', 'dCreacion', 'dAct', 'cCUser', 'cAUser'));
         ?>
         
         var codigos = <?php echo extjs_creategrid($modelo, $id . '_codigos', $this->lang->line('Códigos'), 'icon-descuentos', 'catalogo/editorialcodigo', $obj->ml2->get_id(), null, FALSE, 'fn_add');?>;
         
        codigos.anchor = '50% 40%';

         <?php 	$obj =& get_instance();
         $obj->load->model('catalogo/M_proveedoreditorial', 'ml');
         $modelo = $obj->ml->get_data_model(array('nIdEditorial', 'dCreacion', 'dAct', 'cCUser', 'cAUser'));
         ?>
         
         var descuentos = <?php echo extjs_creategrid($modelo, $id . '_descuentos', $this->lang->line('Descuentos'), 'icon-descuentos', 'catalogo/proveedoreditorial', $obj->ml->get_id(), null, FALSE, 'fn_add');?>;
         
        descuentos.anchor = '100% 50%';
        
	   var proveedor = /*new Ext.form.ComboBox*/ (Ext.app.autocomplete2({
            url: site_url('proveedores/proveedor/search'),
            name: 'nIdProveedor',
            anchor: '100%',
				anchor: '90%',
            //id: form_id + '_pv',
            create: true,
            fieldLabel: _s('Proveedor')
        }));
	                
        var controls = [{
                xtype: 'textfield',
                id: 'cNombre',
				anchor: '90%',
                allowBlank: false,
                fieldLabel: _s('cNombre')
            }, {
                xtype: 'textfield',
                id: 'cNombreCorto',
				anchor: '90%',
                allowBlank: true,
                fieldLabel: _s('cNombreCorto')            
        }, proveedor, codigos, Ext.app.formHtmlEditor({
            id: 'tComentario',
            hideLabel: true,
            anchor: '100% 30%'
        })[0]];
        
        // General
        form.addTab({
            title: _s('General'),
            iconCls: 'icon-general',
            items: {
                xtype: 'panel',
                layout: 'form',
                cls: 'form-editorial',
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
        
        // Usuarios
        form.addTabUser();
        
        // Búsqueda
        
        var fn_open = function(id){
            form.load(id);
            form.selectTab(0);
        }
        
         <?php $modelo = $this->reg->get_data_model();?>
         var grid_search = <?php echo extjs_creategrid($modelo, $id.'_g_search', null, null, 'catalogo.editorial', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
        
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
                    var url = site_url('catalogo/editorial/analisis');
                    
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

        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
