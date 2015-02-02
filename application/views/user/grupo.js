(function() {
	try {
	var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
	if (title == '') title = _s('Grupos de usuarios');
	if (icon == '') icon = 'iconoGruposTab';
	
		var list_grids = [ '<?php echo $id;?>_permisos_grid', '<?php echo $id;?>_usuarios_grid' ];

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
			id : "<?php echo $id;?>",
			title : "<?php echo $title;?>",
			icon : "<?php echo $icon;?>",
			url : "<?php echo site_url('user/grupo');?>",
			fn_load : fn_load,
			fn_reset : fn_reset,
			fn_enable_disable : fn_enable_disable
		});

		// Controles normales
		var controls = [ {
			xtype : 'textfield',
			id : 'cDescripcion',
			allowBlank : true,
			fieldLabel : "<?php echo $this->lang->line('cDescripcion'); ?>"
		} ];

		// General
		form.addTab( {
			title : "<?php echo $this->lang->line('General'); ?>",
			iconCls : 'icon-general',
			items : {			
				xtype : 'panel',
				layout : 'form',
				cls: 'form-grupo-usuario',
				items : form.addControls(controls)
			}
		});

		// Grupos del usuario
		var usuarios = Ext.app.formCheckList( {
			urllist : "<?php echo site_url('user/grupousuario/get_list_usr');?>",
			urlupd : "<?php echo site_url('user/grupousuario/upd');?>",
			idreg : 'nIdUsuario',
			id: "<?php echo $id;?>_usuarios_grid",
			text : 'cUsername',
			form : form
		});

		form.addTab(new Ext.Panel( {
			layout : 'border',
			id : "<?php echo $id;?>_usuarios",
			title : "<?php echo $this->lang->line('Usuarios'); ?>",
			iconCls : 'icon-usuarios',
			region : 'center',
			baseCls : 'x-plain',
			frame : true,
			items : usuarios
		}));

		// Permisos del usuario
		var permisos = Ext.app.formCheckList( {
			urllist : "<?php echo site_url('user/permisogrupo/get_list');?>",
			urlupd : "<?php echo site_url('user/permisogrupo/upd');?>",
			idreg : 'nIdPermiso',
			id: "<?php echo $id;?>_permisos_grid",
			text : 'cDescripcion',
			form : form
		});

		form.addTab(new Ext.Panel( {
			layout : 'border',
			id : "<?php echo $id;?>_permisos",
			title : "<?php echo $this->lang->line('Permisos'); ?>",
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
     $obj->load->model('user/m_grupo');
     $modelo2 = $obj->m_grupo->get_data_model();
     ?>
     
     var grid_search_m = <?php echo extjs_creategrid($modelo2, $id.'_g_search', null, null, 'user.grupo', $obj->m_grupo->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
	 
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
