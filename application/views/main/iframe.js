(function() {

	var border = new Ext.Panel({
		title : "<?php echo $title;?>",
		id : "<?php echo $id;?>",
		region : 'center',
		closable : true,
		iconCls : "<?php echo $icon;?>",
		layout : 'border',

		items : [{
					region : 'center',
					id : "<?php echo $id;?>details-panel",
					xtype : 'iframepanel',
					loadMask : true,
					//html: 'aqui estamos',
					defaultSrc :  "<?php echo $url;?>",
					frameConfig : {
						autoCreate : {
							id : '<?php echo $id;?>details-panel2'
						}
					}
				}],

		tbar : ['->', {
			text : "<?php echo $this->lang->line('Imprimir'); ?>",
			iconCls : 'icon-print',
			id : 'alb_btnprint',
			handler : function() {
				try {
					Ext.getCmp("<?php echo $id;?>details-panel").iframe.print();
				} catch (ex) {

					Ext.app.msgError(
							"<?php echo $this->lang->line('Imprimir'); ?>",
							"<?php echo $this->lang->line('Error de impresión'); ?><br />"
									+ ex);
				}
			}
		}]
	});

	return border;
})();