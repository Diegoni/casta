(function() {
	var open_id = "<?php echo $open_id;?>";
	var form_id = "<?php echo $id;?>";
	var title = "<?php echo $title;?>";
	var icon = "<?php echo $icon;?>";
	if(title == '')
		title = _s('Materias');
	if(icon == '')
		icon = 'iconoMateriaTab';
	var ctxRow = null;
	var reload = function() {
		var f = Ext.getCmp(form_id + "_tree");
		f.root.reload();
	}
	var add = function(nIdMateriaPadre) {
		Ext.Msg.prompt(title, _s('cNombre'), function(ok, v) {
			if(ok != 'ok')
				return;

			Ext.app.callRemote({
				url : site_url('catalogo/materia/upd'),
				params : {
					nIdMateriaPadre : nIdMateriaPadre,
					cNombre : v
				},
				fnok : function() {
					reload();
				}
			});
		});
	}
	var mover = function(nIdMateria, libros) {
		var materia = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('catalogo/materia/search'),
			name : 'destino',
			anchor : '100%',
			label : _s('Materia')
		}));

		var controls = [materia, {
			xtype : 'hidden',
			name : 'id',
			value : nIdMateria
		}];

		var url = site_url('catalogo/materia/mover');

		var form2 = Ext.app.formStandarForm({
			controls : controls,
			title : _s((libros) ? 'Mover artículos' : 'Mover'),
			icon : (libros) ? 'icon-move-articulos' : 'icon-move',
			url : url,
			fn_ok : function(res) {
				reload();
			}
		});

		Ext.app.loadStores([{
			store : materia.store
		}]);
		form2.show();
	}
	
	var contextmenu = new Ext.menu.Menu({
		allowOtherMenus : false,
		items : [{
			text : _s('Cambiar nombre'),
			handler : function() {
				if(ctxRow) {
					Ext.Msg.prompt(title, _s('Nuevo nombre'), function(ok, v) {
						if(ok != 'ok')
							return;
						Ext.app.callRemote({
							url : site_url('catalogo/materia/upd'),
							params : {
								id : ctxRow.attributes.nIdMateria,
								cNombre : v
							},
							fnok : function() {
								reload();
							}
						});
					}, null, null, ctxRow.attributes.cNombre);
				}
			},
			iconCls : 'icon-edit'
		}, '-', {
			text : _s('Añadir'),
			handler : function() {
				add((ctxRow) ? ctxRow.attributes.nIdMateria : null);
			},
			iconCls : 'icon-add'
		}, '-', {
			text : _s('Eliminar'),
			handler : function() {
				if(ctxRow) {
					Ext.app.callRemoteAsk({
						url : site_url('catalogo/materia/del/'),
						title : title,
						askmessage : _s('elm-registro'),
						params : {
							id : ctxRow.attributes.nIdMateria
						},
						fnok : function() {
							reload();
						}
					});
				}
			},
			iconCls : 'icon-delete'
		}, '-', {
			text : _s('Mover'),
			handler : function() {
				mover((ctxRow) ? ctxRow.attributes.nIdMateria : null);
			},
			iconCls : 'icon-move'
		/*}, '-', {
			text : _s('Mover artículos'),
			handler : function() {
				moverarticulos((ctxRow) ? ctxRow.attributes.nIdMateria : null);
			},
			iconCls : 'icon-move-articulo'*/
		}]
	});

	var secciones = new Ext.Panel({
		layout : 'border',
		title : title,
		id : form_id,
		iconCls : icon,
		region : 'center',
		closable : true,
		baseCls : 'x-plain',
		frame : true,
		items : [new Ext.ux.tree.TreeGrid({
			region : 'center',
			id : form_id + "_tree",
			autoScroll : true,
			useArrows : true,
			loadMask : true,
			rootVisible : false,
			autoExpandColumn : "descripcion",
			columns : [{
				header : _s('Materia'),
				width : Ext.app.TAM_COLUMN_TEXT * 3,
				id : 'descripcion',
				dataIndex : 'text'
			}, {
				header : _s('Id'),
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : 'nIdMateria'
			}, {
				header : _s('cCodMateria'),
				width : Ext.app.TAM_COLUMN_TEXT,
				dataIndex : 'cCodMateria'
			/*}, {
				header : _s('nHijos'),
				width : Ext.app.TAM_COLUMN_NUMBER,
				dataIndex : 'nHijos'
			}, {
				header : _s('nLibrosLocal'),
				width : Ext.app.TAM_COLUMN_NUMBER,
				dataIndex : 'nLibrosLocal'
			}, {
				header : _s('nLibrosTotal'),
				width : Ext.app.TAM_COLUMN_NUMBER,
				dataIndex : 'nLibrosLocal'*/
			}, {
				header : _s('cCUser'),
				width : Ext.app.TAM_COLUMN_TEXT,
				dataIndex : 'cCUser'
			}, {
				header : _s('dCreacion'),
				width : Ext.app.TAM_COLUMN_DATE,
				dateFormat : 'timestamp',
				renderer : Ext.app.renderDate,
				sortable : true,
				dataIndex : 'dCreacion'
			}, {
				header : _s('cAUser'),
				width : Ext.app.TAM_COLUMN_TEXT,
				dataIndex : 'cAUser'
			}, {
				header : _s('dAct'),
				width : Ext.app.TAM_COLUMN_DATE,
				dataIndex : 'dAct'
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
				tooltip : _s('cmd-actualizar'),
				iconCls : 'icon-refresh',
				listeners : {
					click : function() {
						reload();
					}
				}
			}, '-', {
				text : _s('Añadir'),
				tooltip : _s('cmd-addregistro'),
				handler : function() {
					add(null);
				},
				iconCls : 'icon-add'
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
			loader : new Ext.tree.TreeLoader({
				loadMask : true,
				uiProviders : {
					'col' : Ext.tree.ColumnNodeUI
				},
				dataUrl : site_url('catalogo/materia/get_tree')
			}),
			root : new Ext.tree.AsyncTreeNode({
				expanded : true
			})
		})],

	});

	return secciones;
})();
