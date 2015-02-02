mydiv = Ext.DomHelper.insertFirst(document.body, {
			id : 'mydiv'
		}, true);


var viewport = new Ext.Viewport({
			layout : 'border',
			renderTo : 'mydiv',
			items : [{
						xtype : 'panel',
						title : 'Prueba',
						region : 'center'
					}, <?php echo extjs_panel_help($this, 'enviar sms');?>]
		});

Ext.app.msgFly("<?php echo $this->lang->line('Enviar SMS'); ?>",
		"<?php echo $this->lang->line('Plantilla Seleccionada'); ?>");
