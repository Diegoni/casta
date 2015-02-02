<?php echo extjs_dynamicjsonreader($this);?>

var afterSubmit = function(options, success, response) {
	Ext.Msg.alert('Response', response.responseText);
};

var resetGrid = function() {
	propStore.load();
};

var onSubmit = function() {
	var propGridValues = Ext.util.JSON.encode(propStore.getAt(0).data);
	Ext.Ajax.request({
				url : '/extexample/propertygridpost/post.php',
				method : 'POST',
				scope : this,
				callback : afterSubmit,
				params : {
					js : propGridValues
				}
			});
};

var propGrid = new Ext.grid.PropertyGrid({
		region : 'center',
		xtype : 'propertygrid',
		bodyStyle : 'padding:5px 5px 0',
		baseCls : 'x-plain',
		labelWidth : 55,
		url : 'index.php?c=app&m=configure_set',
		id : 'configure',
		closable : true,
	buttons : [{
		text : "<?php echo $this->lang->line('Aceptar'); ?>",
		handler : function() {
			Ext.getCmp('smsform').getForm().submit({
				method : 'POST',
				waitTitle : "<?php echo $this->lang->line('Enviar SMS');?>",
				waitMsg : "<?php echo $this->lang->line('Enviando Mensaje');?>",

				success : function(form, action) {
					Ext.Msg
							.alert(
									"<?php echo $this->lang->line('Enviar SMS'); ?>",
									"<?php echo $this->lang->line('mensaje_envio_ok'); ?>");
					Ext.getCmp('smsform').getForm().reset();
				},

				failure : function(form, action) {
					obj = Ext.util.JSON.decode(action.response.responseText);
					Ext.Msg.alert("<?php echo $this->lang->line('Error'); ?>",
							"<?php echo $this->lang->line('mensaje_envio_error'); ?>:"
									+ obj.error);
				}

			});
		}
	}, {
		text : "<?php echo $this->lang->line('Restaurar'); ?>",
		handler : function() {
			propStore.load();
		}
	}]
});

var propStore = new Ext.data.Store({
			proxy : new Ext.data.HttpProxy({
						url : 'index.php?c=test&m=datagrid_data'
					}),
			reader : new Ext.data.DynamicJsonReader({
						root : 'props'
					})
		});

propStore.on('load', function() {
			propStore.fields = propStore.recordType.prototype.fields;
			propGrid.setSource(propStore.getAt(0).data);
		});

propStore.load();

var viewport = new Ext.Viewport({
			layout : 'border',
			renderTo : <?php echo isset($divid)?"'$divid'":'Ext.getBody()';?>,
			items : [propGrid,
					<?php echo extjs_panel_help($this, 'configurar');?>]
		});
