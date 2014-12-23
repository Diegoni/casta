var tb = grid.getTopToolbar();
tb.insert(0, {
	text : _s('Actualizar'),
	handler : function() {
		Ext.app.callRemote({
			url : site_url('compras/divisacamara/actualizar'),
			params: { 
				cmpid: grid.getId() 
			}
		});
	},
	iconCls : 'icon-actualizar'
});