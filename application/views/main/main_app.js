Ext.app.auth_reload(true, function() {
	var c = <?php echo $form;?>;
	var viewport = new Ext.Viewport({
			layout : 'border',
			renderTo : <?php echo isset($divid)?"'$divid'":'Ext.getBody()';?>,
			items : [c]
		});
});
