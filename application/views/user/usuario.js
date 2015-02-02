(function() {
	try {
	var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
	if (title == '') title = _s('Usuarios');
	if (icon == '') icon = 'iconoUsuariosTab';

		var list_grids = [ form_id + '_permisos_grid', form_id + '_grupos_grid' ];

		// Carga
		var fn_load = function(id) {
			Ext.app.formLoadList( {
				list : list_grids,
				params : {
					id : parseInt(id)
				}
			});
		}

		// Borrado
		var fn_reset = function() {
			Ext.app.formResetList( {
				list : list_grids,
				params : {
					id : -1
				}
			});
		}

		var fn_enable_disable = function(form) {
			Ext.app.formEnableList( {
				list : list_grids,
				enable : (form.getId() > 0)
			});
		}

		// Formulario
		var form = Ext.app.formGeneric();
		form.init( {
			id : form_id,
			title : title,
			icon : icon,
			url : site_url('user/usuario'),
			fn_load : fn_load,
			fn_reset : fn_reset,
			fn_enable_disable : fn_enable_disable
		});

		// Controles normales
		var controls = [ {
			xtype : 'textfield',
			id : 'cUsername',
			allowBlank : true,
			fieldLabel : _s('cUsername')
		}, {
			xtype : 'textfield',
			id : 'cNombre',
			// anchor : '90%',
			allowBlank : true,
			fieldLabel : _s('cNombre')
		}, {
			xtype : 'textfield',
			id : 'cPassword',
			// anchor : '90%',
			allowBlank : true,
			fieldLabel : _s('cPassword')
		}, {
			xtype : 'checkbox',
			id : 'bEnabled',
			// anchor : '90%',
			allowBlank : true,
			fieldLabel : _s('bEnabled')
		} ];

		// General
		form.addTab( {
			title : _s('General'),
			iconCls : 'icon-general',
			items : {
				xtype : 'panel',
				cls: 'form-usuario',
				layout : 'form',
				items : form.addControls(controls)
			}
		});

		// Grupos del usuario
		var grupos = Ext.app.formCheckList( {
			urllist : site_url('user/grupousuario/get_list'),
			urlupd : site_url('user/grupousuario/upd'),
			idreg : 'nIdGrupo',
			id: "<?php echo $id;?>_grupos_grid",
			text : 'cDescripcion',
			form : form
		});

		form.addTab(new Ext.Panel( {
			layout : 'border',
			id : form_id + "_grupos",
			title : _s('Grupos'),
			iconCls : 'icon-grupos',
			region : 'center',
			baseCls : 'x-plain',
			frame : true,
			items : grupos
		}));

		// Permisos del usuario
		var permisos = Ext.app.formCheckList( {
			urllist : site_url('user/permisousuario/get_list'),
			urlupd : site_url('user/permisousuario/upd'),
			idreg : 'nIdPermiso',
			id: form_id + "_permisos_grid",
			text : 'cDescripcion',
			form : form
		});

		form.addTab(new Ext.Panel( {
			layout : 'border',
			id : form_id + '_permisos',
			title : _s('Permisos'),
			iconCls : 'icon-permisos',
			region : 'center',
			baseCls : 'x-plain',
			frame : true,
			items : permisos
		}));

    var fn_open = function(id){
        form.load(id);
        form.selectTab(0);
    }
    
     <?php
     $obj =& get_instance();
     $obj->load->model('user/m_usuario');
     $modelo2 = $obj->m_usuario->get_data_model(array('cPassword'));
     ?>
     
     var grid_search_m = <?php echo extjs_creategrid($modelo2, $id.'_g_search', null, null, 'user.usuario', $obj->m_usuario->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
	 
   	form.addTab({
			title: _s('Búsqueda'),
	        iconCls : 'icon-search',
			items : Ext.app.formSearchForm({        
				grid: grid_search_m,
				id_grid: form_id + '_g_search_grid'
			})
		});
		
		return form.show(open_id);
	} catch (e) {
		console.dir(e);
	}
})();
