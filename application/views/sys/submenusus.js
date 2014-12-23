var cm_lineas = fn_contextmenu();

var contextmenu = Ext.app.addContextMenuEmpty(grid, cm_lineas);

cm_lineas.setContextMenu(contextmenu);

var m_doc = contextmenu.add({
	text : _s('ver-dir-envio-mapa'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			Ext.app.callRemote({
				url : site_url('perfiles/direccion/mapa'),
				params : {
					id : record.data.nIdDireccionEnvio
				}
			});

		}
	},
	iconCls : 'iconoMapa'
});

var m_doc1 = contextmenu.add({
	text : _s('print-dir-envio'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			Ext.app.callRemote({
				url : site_url('etiquetas/etiqueta/printcliente'),
				params : {
					id : record.data.nIdDireccionEnvio
				}
			});

		}
	},
	iconCls : 'icon-label'
});

var m_doc2 = contextmenu.add({
	text : _s('add-dir-envio-cola'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			Ext.app.callRemote({
				url : site_url('etiquetas/etiqueta/colacliente'),
				params : {
					id : record.data.nIdDireccionEnvio
				}
			});

		}
	},
	iconCls : 'icon-label-cola'
});
contextmenu.add('-');

var m_doc3 = contextmenu.add({
	text : _s('ver-dir-fac-mapa'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			Ext.app.callRemote({
				url : site_url('perfiles/direccion/mapa'),
				params : {
					id : record.data.nIdDireccionFactura
				}
			});

		}
	},
	iconCls : 'iconoMapa'
});

var m_doc4 = contextmenu.add({
	text : _s('print-dir-fact'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			Ext.app.callRemote({
				url : site_url('etiquetas/etiqueta/printcliente'),
				params : {
					id : record.data.nIdDireccionFactura
				}
			});

		}
	},
	iconCls : 'icon-label'
});

var m_doc5 = contextmenu.add({
	text : _s('add-dir-fact-cola'),
	handler : function() {
		var record = cm_lineas.getItemSelect();
		if(record != null) {
			Ext.app.callRemote({
				url : site_url('etiquetas/etiqueta/colacliente'),
				params : {
					id : record.data.nIdDireccionFactura
				}
			});

		}
	},
	iconCls : 'icon-label-cola'
});

var fn_check_menu = function(item) {
	if (item.data.nIdDireccionEnvio != null) {
		m_doc.enable();
		m_doc1.enable();
		m_doc2.enable();
	} else {
		m_doc.disable();
		m_doc1.disable();
		m_doc2.disable();
	}

	if (item.data.nIdDireccionFactura != null) {
		m_doc3.enable();
		m_doc4.enable();
		m_doc5.enable();
	} else {
		m_doc3.disable();
		m_doc4.disable();
		m_doc5.disable();
	}
}

cm_lineas.setCheckMenu(fn_check_menu);
