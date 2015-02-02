(function(){
    try {
	var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
	if (title == '') title = _s('Contactos');
	if (icon == '') icon = 'iconoContactosTab';

        var perfiles = Ext.app.formPerfiles();
        
        var list_grids = [form_id + '_temas_grid']
        
        // Carga
        var fn_load = function(id){
            perfiles.load(id);
            // console.log('load ' + id);
            Ext.app.formLoadList({
                list: list_grids,
                params: {
                    id: parseInt(id)
                }
            });
        }
        
        // Borrado
        var fn_reset = function(){
            perfiles.reset();
            Ext.app.formResetList({
                list: list_grids,
                params: {
                    id: -1
                }
            });
        }
        
        var fn_enable_disable = function(form){
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
            url: site_url('mailing/contacto'),
            fn_load: fn_load,
            fn_reset: fn_reset,
            fn_enable_disable: fn_enable_disable
        });
        

        var idioma = Ext.app.comboLangs(null, 'cIdioma');
        
        // Temas
        var grid_temas = Ext.app.formCheckList({
            urllist: site_url('mailing/temascontacto/get_list'),
            urlupd: site_url('mailing/temascontacto/upd'),
            idreg: 'nIdTema',
            id: form_id + "_temas_grid",
            text: 'cDescripcion',
            form: form
        });
        
        // Perfiles
        var grid_perfiles = perfiles.init({
            id: form_id + "_perfiles",
            url: site_url('mailing/perfilcontacto'),
            etq: 'contacto',
            mainform: form
        });
        
        // Controles normales
        var tipocliente = Ext.app.combobox({
            url: site_url('mailing/tipocliente/search'),
            id: 'nIdTipoCliente',
            label: _s('Tipo Contacto')
        });
        var tratamiento = Ext.app.combobox({
            url: site_url('clientes/tratamiento/search'),
            id: 'nIdTratamiento',
            label: _s('Tratamiento')
        });
        var grupocliente = Ext.app.combobox({
            url: site_url('mailing/grupocliente/search'),
            id: 'nIdGrupoCliente',
            label: _s('Grupo Contacto')
        });
        
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
        var controls = [tratamiento, {
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
            xtype: 'textfield',
            id: 'cEmpresa',
            anchor: '90%',
            allowBlank: true,
            fieldLabel: _s('Empresa')
        }, {
            xtype: 'textfield',
            id: 'cNIF',
            // anchor : '90%',
            allowBlank: true,
            fieldLabel: _s('NIF')
        }, {
            xtype: 'textfield',
            id: 'cWebPage',
            // anchor : '90%',
            allowBlank: true,
            fieldLabel: _s('Página Web')
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
                value: _s('cIdioma')
            }, idioma]
        }, tipocliente, grupocliente];
        
        // General
        form.addTab({
            title: _s('General'),
            iconCls: 'icon-general',
            items: {
                xtype: 'panel',
                layout: 'form',
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
        
        // Usuarios
        form.addTabUser();
        
        // Búsqueda
        
        var fn_open = function(id){
            form.load(id);
            form.selectTab(0);
        }
        
         <?php $modelo = $this->reg->get_data_model();?>
         var grid_search = <?php echo extjs_creategrid($modelo, $id.'_g_search', null, null, 'mailing.contacto', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;

    	form.addTab({
			title: _s('Búsqueda'),
	        iconCls : 'icon-search',
			items : Ext.app.formSearchForm({        
				grid: grid_search,
				id_grid: form_id + '_g_search_grid'
			})
		});
		 
        return form.show(open_id);
    } 
    catch (e) {
        console.dir(e);
    }
})();
