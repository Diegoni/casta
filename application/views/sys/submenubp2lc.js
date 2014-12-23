var cm_lineas = fn_contextmenu();
var contextmenu = Ext.app.addContextMenu(grid, 'nIdBP2LC', cm_lineas, 'sys/bp2lc/log', _s('Ver resultado'), 'icon-ver');
cm_lineas.setContextMenu(contextmenu)
contextmenu.add('-');
var m_doc = contextmenu.add({
	text : _s('Descargar fichero traspasos'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			//if(record.data.bSuccess)
				Ext.app.execCmd({
					url : site_url('sys/bp2lc/download/' + record.data.nIdBP2LC)
				});
		}
	},
	iconCls : 'icon-openfile'
});
contextmenu.add('-');
var m_procesar = contextmenu.add({
	text : _s('Marcar como traspasado'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			Ext.app.execCmd({
				url : site_url('sys/bp2lc/procesar/' + record.data.nIdBP2LC)
			});
		}
	},
	iconCls : 'icon-doit'
});
contextmenu.add('-');
var m_ver = contextmenu.add({
	text : _s('Facturas contabilizadas'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			Ext.app.execCmd({
				url : site_url('sys/bp2lc/ver/' + record.data.nIdBP2LC)
			});
		}
	},
	iconCls : 'icon-check'
});
var m_ver2 = contextmenu.add({
	text : _s('Ver otros movimientos'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			Ext.app.execCmd({
				url : site_url('sys/bp2lc/ver_movimientos/' + record.data.nIdBP2LC)
			});
		}
	},
	iconCls : 'icon-ver'
});
contextmenu.add('-');
var m_desc = contextmenu.add({
	text : _s('Descontabilizar'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			Ext.app.execCmd({
				url : site_url('sys/bp2lc/descontabilizar/' + record.data.nIdBP2LC)
			});
		}
	},
	iconCls : 'icon-uncheck'
});

var fn_check_menu = function(item) {
	//
	//(item.data.bSuccess) ? m_doc.enable() : m_doc.disable();
	(item.data.bTraspasdo !== true) ? m_procesar.enable() : m_procesar.disable();
}

cm_lineas.setCheckMenu(fn_check_menu);
