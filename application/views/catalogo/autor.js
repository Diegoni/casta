(function() {
	try {
		var open_id = "<?php echo $open_id;?>";
		var form_id = "<?php echo $id;?>";
		var title = "<?php echo $title;?>";
		var icon = "<?php echo $icon;?>";
		if(title == '')
			title = _s('Autor');
		if(icon == '')
			icon = 'iconoAutoresTab';

		var list_grids = [form_id + 'btn_analisis', form_id + 'btn_documents', form_id + 'btn_devoluciones', form_id + 'btn_antiguedad', form_id + 'btn_stockcontado']

		// Carga
		var fn_load = function(idm, res) {
		}
		// Borrado
		var fn_reset = function() {
			//perfiles.reset();
			Ext.app.formResetList({
				list : list_grids,
				params : {
					where : 'nIdAutor=-1',
					id : -1
				}
			});
		}
		var fn_enable_disable = function(form) {
			Ext.app.formEnableList({
				list : list_grids,
				enable : (form.getId() > 0)
			});
		}
		// Formulario
		var form = Ext.app.formGeneric();
		form.init({
			id : form_id,
			title : title,
			icon : icon,
			url : site_url('catalogo/autor'),
			fn_load : fn_load,
			fn_reset : fn_reset,
			fn_enable_disable : fn_enable_disable
		});

		var nombre = new Ext.form.TextField({
			name : 'cNombre',
			value : '',
			allowBlank : true
		});
		var apellido = new Ext.form.TextField({
			xtype : 'textfield',
			name : 'cApellido',
			allowBlank : true,
			fieldLabel : _s('cApellido')
		});
		var controls = [{
			xtype : 'compositefield',
			fieldLabel : _s('cNombre'),
			items : [nombre, {
				xtype : 'button',
				iconCls : 'icon-split',
				width : 30,
				value : '',
				handler : function() {
					part_names(nombre, apellido);
				}
			}]
		}, apellido]

		// General
		form.addTab({
			title : _s('General'),
			iconCls : 'icon-general',
			items : {
				xtype : 'panel',
				layout : 'form',
				cls : 'form-autor',
				items : form.addControls(controls)
			}
		});

		// Usuarios
		form.addTabUser();

		// Búsqueda
		var fn_open = function(id) {
			form.load(id);
			form.selectTab(0);
		}
		
		<?php $modelo = $this->reg->get_data_model(array('bTipo'));?>
		var grid_search = <?php echo extjs_creategrid($modelo, $id.'_g_search', null, null, 'catalogo.autor', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;

		form.addTab({
			title : _s('Búsqueda'),
			iconCls : 'icon-search',
			items : Ext.app.formSearchForm({
				grid : grid_search,
				id_grid : form_id + '_g_search_grid'
			})
		});

		return form.show(open_id);
	} catch (e) {
		console.dir(e);
	}
})();
