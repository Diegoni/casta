var cm_lineas = fn_contextmenu();
var contextmenu = Ext.app.addContextMenu(grid, 
	'cDescripcion', 
	cm_lineas, 
	'etiquetas/etiqueta/imprimir', 
	_s('Imprimir grupo'), 
	'icon-print');
cm_lineas.setContextMenu(contextmenu);

contextmenu.add('-');
var m_doc = contextmenu.add({
	text : _s('Eliminar grupo'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
				Ext.app.execCmd({
					url : site_url('etiquetas/etiqueta/delgrupo/' + record.data.cDescripcion),
					fnok: function() {
						grid.store.load();
					}
				});
		}
	},
	iconCls : 'icon-delete'
});
