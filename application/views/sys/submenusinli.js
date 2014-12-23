var cm_lineas = fn_contextmenu();
var contextmenu = Ext.app.addContextMenu(grid, 'nIdFichero', cm_lineas, 'sys/sinli/ver', _s('Ver'), 'icon-ver');
cm_lineas.setContextMenu(contextmenu)
contextmenu.add('-');
var m_doc = contextmenu.add({
	text : _s('Abrir'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			if(record.data.cTipo == 'ENVIO')
				Ext.app.execCmd({
					url : site_url('compras/albaranentrada/index/' + record.data.nIdDocumento)
				});
		}
	},
	iconCls : 'icon-openfile'
});
contextmenu.add('-');
var m_procesar = contextmenu.add({
	text : _s('Procesar'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			Ext.app.execCmd({
				url : site_url('sys/sinli/procesar/' + record.data.nIdFichero)
			});
		}
	},
	iconCls : 'icon-doit'
});

var fn_check_menu = function(item) {
	//
	(item.data.nIdDocumento > 0) ? m_doc.enable() : m_doc.disable();
	(item.data.bProcesado !== true) ? m_procesar.enable() : m_procesar.disable();
}

cm_lineas.setCheckMenu(fn_check_menu);
