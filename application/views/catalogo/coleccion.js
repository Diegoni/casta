(function(){
    try {
        var open_id = "<?php echo isset($open_id)?$open_id:'';?>";
        var form_id = "<?php echo isset($id)?$id:'';?>";
        var title = "<?php echo isset($title)?$title:'';?>";
        var icon = "<?php echo isset($icon)?$icon:'';?>";
        if (title == '') 
            title = _s('Colección');
        if (icon == '') 
            icon = 'iconoColeccionesTab';
        
        var list_grids = [form_id + '_descuentos_grid', form_id + '_codigos_grid']
        
        // Carga
        var fn_load = function(id){
            Ext.app.formLoadList({
                list: list_grids,
                params: {
                    where: 'nIdColeccion=' + parseInt(id),
                    id: parseInt(id)
                }
            });
        }
        
        // Borrado
        var fn_reset = function(){
            Ext.app.formResetList({
                list: list_grids,
                params: {
                    where: 'nIdColeccion=-1',
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
            url: site_url('catalogo/coleccion'),
            fn_load: fn_load,
            fn_reset: fn_reset,
            fn_enable_disable: fn_enable_disable
        });
                
	   var editorial = /*new Ext.form.ComboBox*/ (Ext.app.autocomplete2({
            url: site_url('catalogo/editorial/search'),
            name: 'nIdEditorial',
			allowBlank: false,
            anchor: '90%',
            //id: form_id + '_pv',
            create: true,
            fieldLabel: _s('Editorial')
        }));
	                
        var controls = [{
                xtype: 'textfield',
                id: 'cNombre',
				anchor: '90%',
                allowBlank: false,
                fieldLabel: _s('cNombre')
        }, editorial];
		        
        // General
        form.addTab({
            title: _s('General'),
            iconCls: 'icon-general',
            items: {
                xtype: 'panel',
                layout: 'form',
                cls: 'form-coleccion',
                items: form.addControls(controls)
            }
        });
        
        // Usuarios
        form.addTabUser();
        
        // Búsqueda
        
        var fn_open = function(id){
            form.load(id);
            form.selectTab(0);
        }
        
         <?php $modelo = $this->reg->get_data_model();?>
         var grid_search = <?php echo extjs_creategrid($modelo, $id.'_g_search', null, null, 'catalogo.coleccion', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
        
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
        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
