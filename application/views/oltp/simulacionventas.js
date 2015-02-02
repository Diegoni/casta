(function() {

	var reload = function() {
		var d1 = Ext.getCmp("<?php echo $id;?>_fecha1").getValue();

		if (d1 == '') {
			Ext.app.msgFly("<?php echo $title;?>",
					"<?php echo $this->lang->line('mensaje_faltan_datos'); ?>");
			return;
		}
		var url = "<?php echo site_url('oltp/oltp/simulacion_ventas');?>";
		Ext.app.reportPanelUpdate({
					id : "<?php echo $id;?>",
					url : url,
					params : {
						year : d1
					}
				});
	};

	var bar = [{
				xtype : 'label',
				html : "<?php echo $this->lang->line('Año'); ?>:"
			}, {
				id : "<?php echo $id;?>_fecha1",
				value : new Date(),
				xtype : "numberfield"
			}];

	return Ext.app.formReport({
				title : "<?php echo $title;?>",
				id : "<?php echo $id;?>",
				icon : "<?php echo $icon;?>",
				filter : bar,
				action : reload
			});
})();