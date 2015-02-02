(function() {
	var form_id = Ext.app.createId();
	var id = "<?php echo $id;?>";
	var ctxRow = null;

	var url = "<?php echo (isset($url))?$url:site_url('compras/albaranentrada/get_tree/' . $id);?>"

    var t = Ext.app.combobox({
        url : site_url('catalogo/grupoetiqueta/printer?list=true'),
        name : 'report',
        allowBlank: true,
        anchor : '100%',
        label : _s('Formato')
    });
    //t['forceSelection'] = false;
    var report = new Ext.form.ComboBox(t);

	var reload = function(list) {
		var f = Ext.getCmp(form_id + "_tree");
		//console.dir(loader);
		loader.baseParams = {
			list : list
		}
		f.root.reload();
	}
	var fn_nodes = function(node) {
		var nodes = [];
		if(node.attributes.nIdLibro) {
			node.attributes.cSeccion = node.parentNode.text;
			node.attributes.nIdSeccion = node.parentNode.id;
			nodes[nodes.length] = node.attributes;
		}
		node.expand();
		Ext.each(node.childNodes, function(item) {
			var n = fn_nodes(item);
			nodes = nodes.concat(n);
		});
		return nodes;
	}
	var fn_imprimir = function(data) {
		var i = 0;
		var c = '';
		var r = report.getValue();
		Ext.each(data, function(item) {
			//console.dir(item);
			if(item.nIdLibro != null) {
				c += item.nIdLibro + '_' + item.nCantidad + '_' + item.cSimbolo + '_' + item.fPVP + '_' + ((item.cSeccion != null) ? item.cSeccion : '.') + '_' + ((item.cSeccion != null) ? item.nIdSeccion : '.') + ';';
				console.log(item.cTitulo);
				++i;
				if(i >= Ext.app.PRINTETQGROUP) {
					//console.log('Imprimiendo ' + c);
					Ext.app.printLabels(site_url('catalogo/grupoetiqueta/imprimir_grupo/' + c + '/' + r), _s('Imprimir etiquetas'));
					i = 0;
					c = '';
				}
			}
		});
		if(i > 0) {
			//console.log('Imprimiendo ' + c);
			Ext.app.printLabels(site_url('catalogo/grupoetiqueta/imprimir_grupo/' + c + '/' + r), _s('Imprimir etiquetas'));
		}
	}
	var add = function(nIdSeccionPadre) {
		Ext.Msg.prompt(title, _s('cNombre'), function(ok, v) {
			if(ok != 'ok')
				return;

			Ext.app.callRemote({
				url : site_url('generico/seccion/upd'),
				params : {
					nIdSeccionPadre : nIdSeccionPadre,
					cNombre : v
				},
				fnok : function() {
					reload();
				}
			});
		});
	}
	var contextmenu = new Ext.menu.Menu({
		allowOtherMenus : false,
		items : [{
			text : _s('Imprimir'),
			handler : function() {
				if(ctxRow) {
					var n = fn_nodes(ctxRow);
					fn_imprimir(n);
				}
			},
			iconCls : 'icon-print'
		}, '-', {
			text : _s('Eliminar'),
			handler : function() {
				if(ctxRow) {
					//console.dir(ctxRow);
					ctxRow.parentNode.removeChild(ctxRow);
				}
			},
			iconCls : 'icon-delete'
		}]
	});

	var loader = new Ext.tree.TreeLoader({
		loadMask : true,
		uiProviders : {
			'col' : Ext.tree.ColumnNodeUI
		},
		baseParams : {
			list : true
		},
		dataUrl : url
	});
	var grid = new Ext.ux.tree.TreeGrid({
		region : 'center',
		id : form_id + "_tree",
		useArrows : true,
		loadMask : true,
		rootVisible : false,
		anchor : '100% 80%',
		autoExpandColumn : 'descripcion',
		columns : [
		{
			header : _s('Sección'),
			id : 'descripcion',
			width : Ext.app.TAM_COLUMN_TEXT * 4,
			sortable : false,
			dataIndex : 'text'
		}, {
			header : _s('nCantidad'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			sortable : false,
			dataIndex : 'nCantidad'
		}, {
			header : _s('cSimbolo'),
			width : Ext.app.TAM_COLUMN_ID,
			sortable : false,
			dataIndex : 'cSimbolo'
		}, {
			header : _s('fPVP'),
			width : Ext.app.TAM_COLUMN_NUMBER,
			sortable : false,
			dataIndex : 'fPVP'
		}],
		listeners : {
			contextmenu : function(node, event) {
				node.select();
				ctxRow = node;
				contextmenu.showAt(event.xy);
				return;
			}
		},
		sm : new Ext.grid.RowSelectionModel({
			singleSelect : true
		}),
		tbar : [{
			tooltip : _s('cmd-arbol'),
			iconCls : 'icon-tree',
			listeners : {
				click : function() {
					reload(false);
				}
			}
		}, '-', {
			tooltip : _s('cmd-lista'),
			iconCls : 'icon-list',
			listeners : {
				click : function() {
					reload(true);
				}
			}
		}, '-', {
			text : _s('Imprimir todo'),
			tooltip : _s('cmd-print'),
			handler : function() {
				var f = Ext.getCmp(form_id + "_tree");
				var n = fn_nodes(f.getRootNode());
				fn_imprimir(n);
			},
			iconCls : 'icon-print'
		}, '-', {
			tooltip : _s('cmd-expandir'),
			iconCls : 'iconoExpandir',
			listeners : {
				click : function() {
					var f = Ext.getCmp(form_id + "_tree");
					f.expandAll();
				}
			}
		}, {
			tooltip : _s('cmd-contraer'),
			iconCls : 'iconoContraer',
			listeners : {
				click : function() {
					var f = Ext.getCmp(form_id + "_tree");
					f.collapseAll();
				}
			}
		}],
		loader : loader,
		root : new Ext.tree.AsyncTreeNode({
			//uiProvider: Ext.tree.CheckboxNodeUI,
			expanded : true
		})
	});

	var controls = [grid, report];

	var form = Ext.app.formStandarForm({
		controls : controls,
		autosize : false,
		labelWidth : 200,
		height : 520,
		disableok : true,
		icon : 'icon-etiquetas',
		width : 700,
		title : _s('Imprimir etiquetas')
	});

    report.store.load();
	form.show();

	return;
})();
