var cm_lineas = fn_contextmenu();

var contextmenu = Ext.app.addContextMenuEmpty(grid, cm_lineas);

var m_desc = contextmenu.add({
	text : _s('Resetear'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null && record.data.nGrupo>0) {
			Ext.app.execCmd({
				url : site_url('concursos/concurso2/reset_grupo/' + record.data.nGrupo),
				fnok: function() {
					grid.store.load();
				}
			});
		}
	},
	iconCls : 'icon-reset'
});

var fn_check_menu = function(item) {
	(item.data.nGrupo > 0 ) ? m_desc.enable() : m_desc.disable();
}

cm_lineas.setCheckMenu(fn_check_menu);
