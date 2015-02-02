(function() {

	var reload = function() {
		var d1 = Ext.getCmp("<?php echo $id;?>_fecha1").getRawValue();
		var d2 = Ext.getCmp("<?php echo $id;?>_fecha2").getRawValue();

		if (d1 == '' || d2 == '') {
			Ext.app.msgFly("<?php echo $title;?>",
					"<?php echo $this->lang->line('mensaje_faltan_datos'); ?>");
			return;
		}

		var url = "<?php echo site_url('calendario/calendario/personal_dia');?>";

		Ext.app.reportPanelUpdate({
					id : "<?php echo $id;?>",
					url : url,
					params : {
						fecha1 : d1,
						fecha2 : d2
					}
				});
	};

	var bar = [{
				xtype : 'label',
				html : "<?php echo $this->lang->line('Desde'); ?>:"
			}, {
				id : "<?php echo $id;?>_fecha1",
				value : new Date(),startDay: Ext.app.DATESTARTDAY,
				xtype : "datefield"
			}, '-', {
				xtype : 'label',
				html : "<?php echo $this->lang->line('Hasta'); ?>:"
			}, {
				id : "<?php echo $id;?>_fecha2",
				value : new Date(),startDay: Ext.app.DATESTARTDAY,
				xtype : "datefield"
			}];

	return Ext.app.formReport({
				title : "<?php echo $title;?>",
				id : "<?php echo $id;?>",
				icon : "<?php echo $icon;?>",
				filter : bar,
				action : reload
			});

})();