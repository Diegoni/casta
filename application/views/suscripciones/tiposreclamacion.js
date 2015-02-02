(function(){
    try {
        var open_id = "<?php echo $open_id;?>";
        var form_id = "<?php echo $id;?>";
        var title = "<?php echo $title;?>";
        var icon = "<?php echo $icon;?>";
        if (title == '') 
            title = _s('Tipos de reclamación');
        if (icon == '') 
            icon = 'iconoSuscripcionesTiposReclamacionTab';
        
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
            url: site_url('suscripciones/tiporeclamacion'),
            fn_load: fn_load,
            fn_save: fn_save,
            fn_reset: fn_reset,
            button_new: false,
            fn_enable_disable: fn_enable_disable
        });
        
        
        
        var destinos = Ext.app.combobox({
            url : site_url('suscripciones/destinoreclamacion/search'),
            //anchor: "90%",
            disabled : true,
            allowBlank : false,
            readOnly : true,
            fieldLabel : _s('nIdDestino'),
            id : 'nIdDestino'
        });

        // Controles normales
        var controls = [{
            fieldLabel : _s('cDescripcion'),
			xtype : 'textfield',
            id : 'cDescripcion',
            allowBlank : false
        }, destinos,
            Ext.app.formEditor({
                title: _s('tTexto'),
                anchor: '100% 90%',
                id: 'tTexto',
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
            url: site_url('suscripciones/tiporeclamacion'),
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

        var grid_search_m = search_tiporeclamacionsuscripcion(form_id, fn_open);
         
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
