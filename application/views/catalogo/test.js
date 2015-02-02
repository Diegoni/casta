(function(){
    try {
        var open_id = "<?php echo $open_id;?>";
        var form_id = "<?php echo $id;?>";
        var title = "<?php echo $title;?>";
        var icon = "<?php echo $icon;?>";
        if (title == '') 
            title = _s('Articulo');
        if (icon == '') 
            icon = 'iconoArticulosTab';
        
        var list_grids = [form_id + 'btn_analisis', form_id + 'btn_documents', form_id + 'btn_devoluciones', form_id + 'btn_antiguedad', form_id + 'btn_stockcontado', form_id + '_autores_grid', form_id + '_secciones_grid', form_id + '_materias_grid', form_id + '_proveedores_grid']
        
        // Carga
        var fn_load = function(id, res){
            try {
                Ext.app.formLoadList({
                    list: list_grids,
                    params: {
                        where: 'nIdLibro=' + parseInt(id),
                        id: parseInt(id)
                    }
                });
            } 
            catch (e) {
                console.dir(e);
            }
        }
        
        // Borrado
        var fn_reset = function(){
            //perfiles.reset();
            Ext.app.formResetList({
                list: list_grids,
                params: {
                    where: 'nIdLibro=-1',
                    id: -1
                }
            });
        }
        
        var fn_enable_disable = function(form){
            //perfiles.enable(form.getId() > 0);
            Ext.app.formEnableList({
                list: list_grids,
                enable: (form.getId() > 0)
            });
        }
        
        var store = Ext.app.getStore(site_url('proveedores/proveedor/search'), ['id', 'text'], false, true);
        store.baseParams = {
            start: 0,
            limit: Ext.app.AUTOCOMPLETELISTSIZE
        };
        var proveedor = Ext.app.autocomplete2({
            url: site_url('proveedores/proveedor/search'),
			create: true,
            name: 'nIdProveedor',
            anchor: '100%',
            fieldLabel: _s('Proveedor')
        });
        
        var controls = [proveedor];
        
        // Formulario
        var form = Ext.app.formGeneric();
        form.init({
            id: form_id,
            title: title,
            icon: icon,
            url: site_url('catalogo/articulo'),
            fn_load: fn_load,
            fn_reset: fn_reset,
            fn_enable_disable: fn_enable_disable
        });
        
        form.addTab({
            title: _s('General'),
            iconCls: 'icon-general',
            items: {
                xtype: 'panel',
                layout: 'form',
                items: form.addControls(controls)
            }
        });
        
        // Usuarios
        form.addTabUser();
        
        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
