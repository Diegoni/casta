(function() {
	// Carga
	var fn_load = function(id) {
	}

	// Borrado
	var fn_reset = function() {
	}

	var fn_enable_disable = function(form) {
	}

	// Formulario
	var form = Ext.app.formGeneric();
	form.init({
				id : "<?php echo $id;?>",
				title : "<?php echo $title;?>",
				icon : "<?php echo $icon;?>",
				url : "<?php echo site_url('mailing/contacto');?>",
				fn_load : fn_load,
				fn_reset : fn_reset,
				fn_enable_disable : fn_enable_disable
			});

	// Usuarios
	form.addTab(new Ext.Panel({
				layout : 'border',
				id : "<?php echo $id;?>_log",
				title : "<?php echo $this->lang->line('Log'); ?>",
				iconCls : 'icon-temas',
				region : 'center',
				baseCls : 'x-plain',
				frame : true,
				html : '<strong>Test</strong>'
			}));
	return form.show();
})();
