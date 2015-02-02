(function(){
    try {
        var open_id = "<?php echo $open_id;?>";
        var form_id = "<?php echo $id;?>";
        var title = "<?php echo $title;?>";
        var icon = "<?php echo $icon;?>";
        if (title == '') 
            title = _s('Formatos de Etiquetas');
        if (icon == '') 
            icon = 'iconoFormatosEtiquetasTab';
        
        var list_grids = []
        
        var iva = null;

        // Carga
        var fn_load = function(id, res){
			data_load = res;
            notas.load(id);
        }
        
        var fn_save = function(id, data){
            return data;
        }
        
        // Borrado
        var fn_reset = function() {

        }
        
        var fn_enable_disable = function(form){
		}
        
        var data_load = null;

        // Formulario
        var form = Ext.app.formGeneric();
        form.init({
            id: form_id,
            title: title,
            icon: icon,
            url: site_url('etiquetas/etiquetaformato'),
            fn_load: fn_load,
            fn_save: fn_save,
            fn_reset: fn_reset,
            fn_enable_disable: fn_enable_disable
        });
        
        // Controles normales
        var controls = [{
            xtype: 'textfield',
            id : 'cDescripcion',
            width : 700,
            allowBlank : false,
            selectOnFocus : true,
            fieldLabel : _s('cDescripcion')
        }, Ext.app.formEditor({
                title: _s('tFormato'),
                anchor: '90% 90%',
                id: 'tFormato',
                allowBlank: false
            })];
    
        // General
        form.addTab({
            title: _s('General'),
            iconCls: 'icon-general',
            items: {
                xtype: 'panel',
                layout: 'form',
				cls: 'form-etiqueta',
                items: form.addControls(controls)
            }
        });

        var notas = Ext.app.formNotas();
        var grid_notas = notas.init({
            id: form_id + "_notas",
            url: site_url('etiquetas/etiquetaformato'),
            mainform: form
        });

        // Usuarios
        form.addTabUser();
        // Búsqueda
        var fn_open = function(id){
            form.load(id);
            form.selectTab(0);
        }

        var grid_search_m = search_etiquetasformatos(form_id, fn_open);
         
         form.addTab({
            title: _s('Búsqueda'),
            iconCls: 'icon-search',
            items: Ext.app.formSearchForm({
                grid: grid_search_m,
                //audit: false,
                id_grid: form_id + '_g_search_grid'
            })
         });

        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
