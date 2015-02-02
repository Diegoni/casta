(function() {
	try {

		var open_id = "<?php echo $open_id;?>";
		var form_id = "<?php echo $id;?>";
		var title = "<?php echo $title;?>";
		var icon = "<?php echo $icon;?>";
		if(title == '')
			title = _s('Búsqueda Artículos');
		if(icon == '')
			icon = 'iconBusquedaTab';
		if(form_id == '')
			form_id = Ext.app.createId();

		var fast_query = "<?php echo isset($fast_query)?$fast_query:false;?>";
		var query = "<?php echo $query;?>";

		var fn_open = function(id) {
			Ext.app.execCmd({
				url : site_url('catalogo/articulo/index/') + id
			});
		}
		var catalogo_tipolibro_search = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url("catalogo/tipolibro/search")
		}));
		var catalogo_encuadernacion_search = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url("catalogo/encuadernacion/search")
		}));
		var catalogo_estadolibro_search = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url("catalogo/estadolibro/search")
		}));

		var rendererTitulo = function(r) {
			var t = '';
			if(Ext.app.SHOW_PORTADA_BUSCAR)
				t += '<table border="0" width="100%"><tr><td width="50px">' + Ext.app.getPortada(50, null, 'se_' + r.data.id).replace('{id}', r.data.nIdLibro) + '</td><td>';
			t += '<b>' + (r.data.cTitulo) + '</b>';
			if(r.data.cAutores)
				t += '<br/><i>' + (r.data.cAutores) + '</i>';
			if (r.data.dCreacion)
			 t += '<br/><b>' + _s('dCreacion') + ':</b> ' + Ext.app.renderDateShort(r.data.dCreacion);
			if (r.data.dEdicion)
			 t += ' | <b>' + _s('dEdicion') + ':</b> ' + Ext.app.renderDateShort(r.data.dEdicion);
			/*if (r.data.cEditorial)
			 t += '<br/><b>' + _s('Editorial') + ':</b> ' + r.data.cEditorial + ' | ';
			 if (r.data.cProveedor)
			 t += '<br/><b>' + _s('Proveedor') + ':</b> ' + r.data.cProveedor;*/
			if((Ext.app.SHOW_PORTADA_BUSCAR))
				t += '</td></tr></table>'
			return t;
		}
		var rendererCodigo = function(r) {
			return '<img src="' + site_url('sys/codebar/out/' + r) + '/15" />';
		}
		if(!fast_query) {

			var searchcontrols = [{
				name : 'cTitulo',
				fieldLabel : _s("cTitulo"),
				xtype : 'textfield',
				anchor : '100%',
				allowBlank : false
			}, {
				name : 'cAutores',
				fieldLabel : _s("cAutores"),
				anchor : '100%',
				xtype : 'textfield',
				allowBlank : false
			}, Ext.app.autocomplete2({
				url : site_url("generico/seccion/search"),
				name : 'Scn',
				fieldLabel : _s("Sección"),
				allowBlank : true
			}), Ext.app.autocomplete2({
				url : site_url("catalogo/editorial/search"),
				name : 'nIdEditorial',
				fieldLabel : _s("Editorial"),
				allowBlank : true
			}), Ext.app.autocomplete2({
				url : site_url("proveedores/proveedor/search"),
				name : 'nIdProveedor',
				fieldLabel : _s("Proveedor"),
				allowBlank : true
			}), Ext.app.autocomplete2({
				url : site_url("catalogo/materia/search"),
				name : 'Idm',
				fieldLabel : _s("Materia"),
				allowBlank : true
			}), Ext.app.autocomplete2({
				url : site_url("catalogo/coleccion/search"),
				name : 'nIdColeccion',
				fieldLabel : _s("nIdColeccion"),
				allowBlank : true
			}), Ext.app.autocomplete2({
				url : site_url("catalogo/oferta/search"),
				name : 'nIdOferta',
				fieldLabel : _s("Oferta"),
				allowBlank : true
			}), {
				name : 'cISBN',
				fieldLabel : _s("cISBN"),
				xtype : 'textfield',
				allowBlank : true
			}, {
				name : 'dCreacion',
				fieldLabel : _s("dCreacion"),
				xtype : 'textfield',
				allowBlank : false
			}, {
				name : 'dEdicion',
				fieldLabel : _s("dEdicion"),
				xtype : 'textfield',
				allowBlank : false
			}, {
				name : 'nIdLibro',
				fieldLabel : _s("Id"),
				xtype : 'textfield',
				allowBlank : false
			}, {
				name : 'Stk',
				fieldLabel : _s("Stock"),
				xtype : 'textfield',
				allowBlank : false
			}, {
				name : 'Frm',
				fieldLabel : _s("Firme"),
				xtype : 'textfield',
				allowBlank : false
			}, {
				name : 'Dpt',
				fieldLabel : _s("Deposito"),
				xtype : 'textfield',
				allowBlank : false
			}, {
				name : 'Rcbir',
				fieldLabel : _s("Recibir"),
				xtype : 'textfield',
				allowBlank : false
			}, {
				name : 'ADvr',
				fieldLabel : _s("A Devolver"),
				xtype : 'textfield',
				allowBlank : false
			}, {
				name : 'fPVP',
				fieldLabel : _s("fPVP"),
				xtype : 'textfield',
				allowBlank : false
			}, {
				name : 'fPrecioCompra',
				fieldLabel : _s("fPrecioCompra"),
				xtype : 'textfield',
				allowBlank : false
			},Ext.app.autocomplete2({
				url : site_url("catalogo/tipolibro/search"),
				name : 'nIdTipo',
				fieldLabel : _s("nIdTipo"),
				allowBlank : true
			}), {
				store : Ext.app.combo_data,
				xtype : 'combo',
				fieldLabel : _s("Portada"),
				name: 'portada',
				typeAhead : true,
				triggerAction : 'all',
				emptyText : _s('bool_noselect'),
				selectOnFocus : true	
			}, {
				store : Ext.app.combo_data,
				xtype : 'combo',
				fieldLabel : _s("Sinopsis"),
				name: 'sinopsis',
				typeAhead : true,
				triggerAction : 'all',
				emptyText : _s('bool_noselect'),
				selectOnFocus : true	
			}];
		} else {
			var searchcontrols = [{
				name : 'query',
				fieldLabel : _s("Consulta"),
				xtype : 'textfield',
				anchor : '100%',
				allowBlank : false
			}];
		}
		var model = [{
			name : 'id',
			column : {
				header : "Id",
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : 'id',
				sortable : true
			}
		}, {
			name : 'nIdLibro'
			,
			 column: {
			 header: _s("Código"),
			 width: Ext.app.TAM_COLUMN_IMAGE,
			 renderer: rendererCodigo,
			 hidden: true
			 },
		}, {
			name : 'id2'
		}, {
			name : 'nIdSeccion'
		}, {
			name : 'dEdicion'
		}, {
			name : 'dCreacion'
		}, {
			name : 'nEAN'
		}, {
			name : 'cISBN',
			column : {
				header : _s("cISBN"),
				width : Ext.app.TAM_COLUMN_TEXT,
				editor : new Ext.form.TextField(),
				sortable : true
			},
			ro : false
		}, {
			name : 'cTitulo',
			/*column: {
			 header: _s("cTitulo"),
			 width: Ext.app.TAM_COLUMN_TEXT,
			 renderer: rendererTitulo,
			 id: 'Descripcion',
			 editor: new Ext.form.TextField(),
			 sortable: true
			 },*/
			ro : false
		}, {
			name : 'cAutores',
			/*column: {
			 header: _s("cAutores"),
			 width: Ext.app.TAM_COLUMN_TEXT,
			 hidden: true,
			 sortable: true
			 },*/
			ro : false
		}, {
			name : 'cEditorial',
			column : {
				header : _s("Editorial"),
				hidden : false,
				width : Ext.app.TAM_COLUMN_TEXT * 2,
				sortable : true
			},
			ro : true
		}, {
			name : 'fPVP',
			column : {
				align : 'right',
				header : _s("fPVP"),
				width : Ext.app.TAM_COLUMN_NUMBER,
				renderer : Ext.app.rendererPVP,
				sortable : true
			},
			ro : false
		}, {
			name : 'cSeccion',
			column : {
				id : 'Descripcion',
				header : _s("Sección"),
				width : Ext.app.TAM_COLUMN_TEXT,
				sortable : true
			},
			ro : true
		}, {
			name : 'Stk',
			column : {
				header : _s("Stock"),
				width : Ext.app.TAM_COLUMN_STOCK,
				sortable : true
			},
			ro : false
		}, {
			name : 'nStockDisponible',
			column : {
				header : _s('Disponible'),
				width : Ext.app.TAM_COLUMN_STOCK,
				sortable : true
			}
		}, {
			name : 'nStockFirme',
			column : {
				header : _s('Firme'),
				width : Ext.app.TAM_COLUMN_STOCK,
				sortable : true
			}
		}, {
			name : 'nStockDeposito',
			column : {
				header : _s('Deposito'),
				width : Ext.app.TAM_COLUMN_STOCK,
				sortable : true
			},
			ro : false
		}, {
			name : 'nStockReservado',
			column : {
				header : _s('Reservado'),
				width : Ext.app.TAM_COLUMN_STOCK,
				sortable : true
			},
			ro : false
		}, {
			name : 'nStockRecibir',
			column : {
				header : _s('Recibir'),
				width : Ext.app.TAM_COLUMN_STOCK,
				sortable : true
			},
			ro : false
		}, {
			name : 'nStockAPedir',
			column : {
				header : _s('APedir'),
				width : Ext.app.TAM_COLUMN_STOCK,
				sortable : true
			},
			ro : false
		}, {
			name : 'nStockServir',
			column : {
				header : _s('Servir'),
				width : Ext.app.TAM_COLUMN_STOCK,
				sortable : true
			},
			ro : false
		}, {
			name : 'nStockADevolver',
			column : {
				header : _s('ADevolver'),
				width : Ext.app.TAM_COLUMN_STOCK,
				sortable : true
			},
			ro : false
		}, {
			name : 'nIdEstado',
			column : {
				header : _s("Estado"),
				width : Ext.app.TAM_COLUMN_NUMBER,
				renderer : function(val) {
					return Ext.app.renderCombo(val, catalogo_estadolibro_search);
				},
				editor : catalogo_estadolibro_search,
				sortable : true
			},
			ro : false
		}, {
			name : 'nIdTipo',
			column : {
				header : _s("Tipo"),
				width : Ext.app.TAM_COLUMN_NUMBER,
				renderer : function(val) {
					return Ext.app.renderCombo(val, catalogo_tipolibro_search);
				},
				editor : catalogo_tipolibro_search,
				sortable : true
			},
			ro : false
		}, {
			name : 'cProveedor',
			column : {
				header : _s("Proveedor"),
				width : Ext.app.TAM_COLUMN_TEXT * 2,
				hidden : false,
				sortable : true
			},
			ro : true
		}, {
			name : 'fPrecioCompra',
			column : {
				align : 'right',
				header : _s("fCoste"),
				width : Ext.app.TAM_COLUMN_NUMBER,
				renderer : Ext.app.rendererPVP,
				sortable : true
			},
			ro : false
		}];

		var stores = [{
			store : catalogo_tipolibro_search.store
		}, {
			store : catalogo_encuadernacion_search.store
		}, {
			store : catalogo_estadolibro_search.store
		}];

		var cellTips = new Ext.ux.CellToolTips({
			tipConfig : {
				anchor : 'left'
			},
			ajaxTips : [{
				field : 'id',
				tpl : '<b>{cTitulo}</b><br />' + Ext.app.getPortada(75)
			}]
		});

		var grid = Ext.app.createFormGrid({
			model : model,
			idfield : 'id2',
			id : form_id + "_g_search",
			title : "",
			icon : "",
			urlget : site_url((!fast_query) ? "catalogo/articulosearch/get_list" : "catalogo/articulosearch/query"),
			urlupd : site_url("catalogo/articulo/upd"),
			loadstores : stores,
			fn_pre : null,
			fn_add : null,
			load : false,
			reorder : [{
				name : ('cTitulo'),
				dataIndex : 'cTitulo'
			}, {
				name : ('cAutores'),
				dataIndex : 'cAutores'
			}, {
				name : ('dCreacion'),
				dataIndex : 'dCreacion'
			}, {
				name : ('dEdicion'),
				dataIndex : 'dEdicion'
			}],
			show_filter : false,
			timeoout: false,
			mode : "search",
			pagesize : Ext.app.ITEMS_BUSQUEDA_ARTICULOS,
			plugins : (Ext.app.SHOW_PORTADA_BUSCAR) ? null : [cellTips],
			fn_open : fn_open,
			preview : rendererTitulo
		});

		var g = Ext.getCmp(form_id + "_g_search_grid");
		//console.dir(g);
		var cm_lineas = fn_contextmenu();
		var contextmenu = Ext.app.addContextMenuLibro(g, 'nIdLibro', cm_lineas);
		cm_lineas.setContextMenu(contextmenu)
		addMenuSeparator(cm_lineas);
		contextmenu.add({
			text : _s('Eliminar del listado'),
			handler : function() {
				var record = cm_lineas.getItemSelect();
				var rowIndex = g.getStore().indexOf(record);
				g.getView().getRow(rowIndex).style.display = 'none';
			},
			iconCls : 'icon-delete'
		});

		addMenuSeparator(cm_lineas);
		addMenuReservar(cm_lineas);
		addMenuPedir(cm_lineas);
		addMenuSeparator(cm_lineas);
		addMenuDocumentos(cm_lineas);
		addMenuCompras(cm_lineas);
		addMenuVentas(cm_lineas);
		addMenuSeparator(cm_lineas);

		var fn_portada = function(id, texto, el) {
			var fn = function(res) {
				Ext.app.callRemote({
					url : site_url('catalogo/articulo/set_cover'),
					params : {
						url : res.url,
						id : id
					},
					fnok : function() {
						// Refresca el elemento
						if(el != null) {
							try {
								el.src = site_url('catalogo/articulo/cover/' + id + '/' + el.width + '?' + Ext.app.createId());
							} catch (e) {
								console.dir(e);
							}
						}
					}
				});
			}
			searchPicture(texto, fn);
		}

		contextmenu.add({
			text : _s('Buscar portada por título'),
			handler : function() {
				var record = cm_lineas.getItemSelect();
				var el = Ext.get('se_' + record.data.id);
				var texto = record.data.cTitulo;
				fn_portada(record.data.nIdLibro, texto, el.dom);
			},
			iconCls : 'icon-portada'
		});

		contextmenu.add({
			text : _s('Buscar portada por ISBN'),
			handler : function() {
				var record = cm_lineas.getItemSelect();
				var el = Ext.get('se_' + record.data.id);
				var texto = record.data.cISBN;
				fn_portada(record.data.nIdLibro, texto, el.dom);
			},
			iconCls : 'icon-portada'
		});
		contextmenu.add({
			text : _s('Buscar portada por EAN'),
			handler : function() {
				var record = cm_lineas.getItemSelect();
				var el = Ext.get('se_' + record.data.id);
				var texto = record.data.nEAN;
				fn_portada(record.data.nIdLibro, texto.toString(), el.dom);
			},
			iconCls : 'icon-portada'
		});
		contextmenu.add({
			text : _s('Copiar portada de una dirección'),
			handler : function() {
				Ext.Msg.prompt(form.getTitle(), _s('Dirección'), function(ok, v) {
					if(ok != 'ok')
						return;
					var record = cm_lineas.getItemSelect();
					var el = Ext.get('se_' + record.data.id);
					fn_portada(record.data.nIdLibro, v.toString(), el.dom);
				});
			},
			iconCls : 'icon-portada'
		});

		var form = Ext.app.formSearchForm({
			grid : grid,
			show_id : false,
			icon : 'icon-portada',
			query : query,
			searchcontrols : searchcontrols,
			pagesize : Ext.app.ITEMS_BUSQUEDA_ARTICULOS,
			id_grid : form_id + '_g_search_grid'
		});

		var panel = new Ext.Panel({
			layout : 'border',
			title : title,
			id : id,
			iconCls : icon,
			region : 'center',
			closable : true,
			baseCls : 'x-plain',
			frame : true,
			items : [form]
		});

		return panel;
	} catch (e) {
		console.dir(e);
	}
})();
