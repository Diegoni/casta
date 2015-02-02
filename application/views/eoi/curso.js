(function() {
	try {
		var open_id = "<?php echo $open_id;?>";
		var form_id = "<?php echo $id;?>";
		var title = "<?php echo $title;?>";
		var icon = "<?php echo $icon;?>";
		if(title == '')
			title = _s('Cursos Internet');
		if(icon == '')
			icon = 'iconoCursosInternetTab';

		var list_grids = [form_id + '_tree', form_id + '_entregas_grid']

		var iva = null;
		var notas = Ext.app.formNotas();

		// Carga
		var fn_load = function(id, res) {
			try {
				notas.load(id);

				loader.baseParams = { 
					id : id
				}				
				//reload();
				loader.load(titulos.getRootNode());
		        
				data_load = res;

				Ext.app.formLoadList({
					list : [form_id + '_entregas_grid'],
					params : {
						id : parseInt(id)
					}
				});
			} catch (e) {
				console.dir(e);
			}
		}
		var fn_save = function(id, data) {

			return data;
		}
		// Borrado
		var fn_reset = function() {
			notas.reset();
			loader.baseParams = { 
				nIdCurso: -1
			}
			iva = 0;
			Ext.app.formResetList({
				list : list_grids,
				params : {
					id : -1
				}
			});
		}
		var fn_enable_disable = function(form) {
			notas.enable(form.getId() > 0);
			Ext.app.formEnableList({
				list : list_grids,
				enable : (form.getId() > 0)
			});
		}
		// Formulario
		var form = Ext.app.formGeneric();
		form.init({
			id : form_id,
			title : title,
			icon : icon,
			url : site_url('eoi/curso'),
			fn_load : fn_load,
			fn_save : fn_save,
			fn_reset : fn_reset,
			fn_enable_disable : fn_enable_disable
		});

		// Controles normales

		var descripcion = new Ext.form.TextField({
			name : 'cDescripcion',
			width : 700,
			allowBlank : false,
			selectOnFocus : true,
			fieldLabel : _s('cDescripcion')
		});

		var eoi = new Ext.form.ComboBox(Ext.app.combobox({
			url : site_url('eoi/escuela/search'),
			name : 'nIdEOI',
			fieldLabel : _s('nIdEOI'),
			anchor : '50%',
			label : _s('EOI')
		}));

		var desde = new Ext.form.DateField({
			xtype : 'datefield',
			startDay : Ext.app.DATESTARTDAY,
			fieldLabel: _s('dDesde'),
			name : 'dDesde',
			allowBlank : true
		});

		var hasta = new Ext.form.DateField({
			xtype : 'datefield',
			startDay : Ext.app.DATESTARTDAY,
			fieldLabel: _s('dHasta'),
			name : 'dHasta',
			allowBlank : true 
		});

	    var reload = function(){
			loader.load(titulos.getRootNode(), function() {
				titulos.expandAll();	
			});
			//titulos.expandAll();
	        //var f = Ext.getCmp(form_id + "_tree");
	        //f.root.reload();	        
			//f.expandAll();
	    }

	    var add_curso = function(){
	        Ext.Msg.prompt(_s('curso-add-idioma'), _s('cDescripcion'), function(ok, v){
	            if (ok != 'ok') 
	                return;
	            
	            Ext.app.callRemote({
	                url: site_url('eoi/titulo/add'),
	                params: {
	                    nIdCurso: form.getId(),
	                    nTipo: 1,
	                    cDescripcion: v
	                },
	                fnok: function(){
	                    reload();
	                }
	            });
	        });
	    }

	    var add_nivel = function(idpadre){
	        Ext.Msg.prompt(_s('curso-add-nivel'), _s('cDescripcion'), function(ok, v){
	            if (ok != 'ok') 
	                return;
	            
	            Ext.app.callRemote({
	                url: site_url('eoi/titulo/add'),
	                params: {
	                    nIdCurso: form.getId(),
	                    nIdTituloPadre: idpadre,
	                    nTipo: 2,
	                    cDescripcion: v
	                },
	                fnok: function(){
	                    reload();
	                }
	            });
	        });
	    }

	    var change_titulo = function(id, tipo){
            Ext.app.callRemote({
                url: site_url('eoi/titulo/upd'),
                params: {
                    nTipo: tipo,
                    id: id
                },
                fnok: function(){
                    reload();
                }
            });
	    }

	    var add_titulo = function(idpadre, tipo, title) {

	    	var ctl = new Ext.form.ComboBox(Ext.app.autocomplete({
		        allowBlank: false,
		        url: site_url('catalogo/articulo/search'),
		        label: _s('Artículo'),
		        name: 'nIdRegistro',
		        anchor: '90%'
		    }));

		    var controls = [{
					xtype : 'hidden',
					name : 'nTipo',
					value: tipo,
				}, {
					xtype : 'hidden',
					name : 'nIdCurso',
					value: form.getId(),
				}, {
					xtype : 'hidden',
					name : 'nIdTituloPadre',
					value: idpadre,
				}, ctl, {
					xtype : 'textfield',
					name : 'cDescripcion',
					fieldLabel: _s('cDescripcion')
				}];
		    
		    var form2 = Ext.app.formStandarForm({
		    	url: site_url('eoi/titulo/add'),
		        controls: controls,
		        timeout: false,
		        title: title,
		        icon: 'icon-add', 	                
		        fn_ok: function() {
	            	reload();
	            }

		    });
		    
		    form2.show();
	    }

	    var add_wiki = function(idpadre){

		    var controls = [{
					xtype : 'textarea',
					name : 'wiki',
					fieldLabel: _s('Texto'),
			        anchor: '100%',
			        height: 300
				}, {
					xtype : 'hidden',
					name : 'padre',
					value: idpadre,
				}, {
					xtype : 'hidden',
					name : 'id',
					value: form.getId(),
				}];
		    
		    var form2 = Ext.app.formStandarForm({
		    	url: site_url('eoi/curso/add_wiki'),
		        controls: controls,
		        timeout: false,
		        title: _s('eoi-curso-add-wiki'),		     
		        icon: 'icon-wiki', 	                
		        fn_ok: function() {
	            	reload();
	            }
		    });
		    
		    form2.show();
	    }

		var loader = new Ext.tree.TreeLoader({
                loadMask: true,
                baseParams: {nIdCruso: -1},
                uiProviders: {
                    'col': Ext.tree.ColumnNodeUI
                },
                dataUrl: site_url('eoi/curso/get_titulos')
            });

	    var contextmenu = new Ext.menu.Menu({
	        allowOtherMenus: false,
	        items: [{
	            text: _s('Cambiar nombre'),
	            handler: function(){
	                if (ctxRow) {
	                    Ext.Msg.prompt(title, _s('Nuevo nombre'), function(ok, v){
	                        if (ok != 'ok') 
	                            return;
	                        Ext.app.callRemote({
	                            url: site_url('eoi/titulo/upd'),
	                            params: {
	                                id: ctxRow.attributes.nIdTitulo,
	                                cDescripcion: v
	                            },
	                            fnok: function(){
	                                reload();
	                            }
	                        });
	                    }, null, null, ctxRow.attributes.cDescripcion);
	                }
	            },
	            iconCls: 'icon-edit' 
        	}, {
				text : _s('Ver artículo'),
				handler : function() {
	                if (ctxRow && ctxRow.attributes.nIdRegistro != null) {
						Ext.app.execCmd({
							url : site_url('catalogo/articulo/index/' + ctxRow.attributes.nIdRegistro)
						});
	                }
				},
				iconCls : 'iconoArticulos'
	        }, {
	            text: _s('curso-change-obligatorio'),
	            id: form_id + '_obligatorio2',
	            handler: function(){
	                if (ctxRow) {
	                	change_titulo(ctxRow.attributes.nIdTitulo, 3);
	                }
	            },
	            iconCls: 'icon-obligatorio'
	        }, {
	            text: _s('curso-change-lectura'),
	            id: form_id + '_lectura2',
	            handler: function(){
	                if (ctxRow) {
	                	change_titulo(ctxRow.attributes.nIdTitulo, 4);
	                }
	            },
	            iconCls: 'icon-lectura'
	        }, {
	            text: _s('curso-change-opcional'),
	            id: form_id + '_opcional2',
	            handler: function(){
	                if (ctxRow) {
	                	change_titulo(ctxRow.attributes.nIdTitulo, 5);
	                }
	            },
	            iconCls: 'icon-opcional'
	        }, new Ext.menu.Separator({id: form_id + '_separator'}) , {
	            text: _s('curso-add-nivel'),
	            id: form_id + '_nivel',
	            handler: function(){
	                if (ctxRow) {
	                	add_nivel(ctxRow.attributes.nIdTitulo);
	                }
	            },
	            iconCls: 'icon-nivel'
	        }, {
	            text: _s('curso-add-obligatorio'),
	            id: form_id + '_obligatorio',
	            handler: function(){
	                if (ctxRow) {
	                	add_titulo(ctxRow.attributes.nIdTitulo, 3, _s('curso-add-obligatorio'));
	                }
	            },
	            iconCls: 'icon-obligatorio'
	        }, {
	            text: _s('curso-add-lectura'),
	            id: form_id + '_lectura',
	            handler: function(){
	                if (ctxRow) {
	                	add_titulo(ctxRow.attributes.nIdTitulo, 4, _s('curso-add-lectura'));
	                }
	            },
	            iconCls: 'icon-lectura'
	        }, {
	            text: _s('curso-add-opcional'),
	            id: form_id + '_opcional',
	            handler: function(){
	                if (ctxRow) {
	                	add_titulo(ctxRow.attributes.nIdTitulo, 5, _s('curso-add-opcional'));
	                }
	            },
	            iconCls: 'icon-opcional'
	        }, {
				text : _s('eoi-curso-add-wiki'),
				iconCls : 'icon-wiki',
				handler : function() {
					add_wiki(ctxRow.attributes.nIdTitulo);
				},
				id : form.idform + '_wiki'
			}, '-', {
	            text: _s('Eliminar'),
	            handler: function(){
	                if (ctxRow) {
	                    Ext.app.callRemoteAsk({
	                        url: site_url('eoi/titulo/del/'),
	                        title: title,
	                        askmessage: _s('elm-registro'),
	                        params: {
	                            id: ctxRow.attributes.nIdTitulo
	                        },
	                        fnok: function(){
	                            reload();
	                        }
	                    });
	                }
	            },
	            iconCls: 'icon-delete'
	        }]
	    });

		var titulos = new Ext.ux.tree.TreeGrid({
            region: 'center',
            id: form_id + "_tree",
            autoScroll: true,
            useArrows: true,
            loadMask: true,
            rootVisible: false,
            columns: [{
                header: _s('Título'),
                width: Ext.app.TAM_COLUMN_TEXT * 4,
                dataIndex: 'text'
            }, {
                header: _s('Libro'),
                width: Ext.app.TAM_COLUMN_ID,
                dataIndex: 'nIdRegistro'
            }, {
                header: _s('Id'),
                width: Ext.app.TAM_COLUMN_ID,
                dataIndex: 'nIdTitulo'
            }, {
                header: _s('cCUser'),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'cCUser'
            }, {
                header: _s('dCreacion'),
                width: Ext.app.TAM_COLUMN_DATE,
                dateFormat: 'timestamp',
                renderer: Ext.app.renderDate,
                sortable: true,
                dataIndex: 'dCreacion'
            }, {
                header: _s('cAUser'),
                width: Ext.app.TAM_COLUMN_TEXT,
                dataIndex: 'cAUser'
            }, {
                header: _s('dAct'),
                width: Ext.app.TAM_COLUMN_DATE,
                dataIndex: 'dAct'
            }],
            listeners: {
                contextmenu: function(node, event){
                    node.select();
                    ctxRow = node;
                    var m = Ext.getCmp(form_id + '_nivel');
                    var m2 = Ext.getCmp(form_id + '_obligatorio');
                    var m3 = Ext.getCmp(form_id + '_lectura');
                    var m4 = Ext.getCmp(form_id + '_opcional');
                    var m21 = Ext.getCmp(form_id + '_obligatorio2');
                    var m31 = Ext.getCmp(form_id + '_lectura2');
                    var m41 = Ext.getCmp(form_id + '_opcional2');
                    var sep = Ext.getCmp(form_id + '_separator');
                    var w = Ext.getCmp(form_id + '_wiki');
                    m.setVisible(false);
                    m2.setVisible(false);
                    m3.setVisible(false);
                    m4.setVisible(false);
                    sep.setVisible(false);
                    m21.setVisible(false);
                    m31.setVisible(false);
                    m41.setVisible(false);
                    w.setVisible(false);
                    if (ctxRow.attributes.nTipo == 1) {
                        m.setVisible(true);
	                    sep.setVisible(true);
                    	w.setVisible(true);	
                    }
                    if (ctxRow.attributes.nTipo == 2) {
                        m2.setVisible(true);
                        m3.setVisible(true);
                        m4.setVisible(true);
	                    sep.setVisible(true);
                    }
                    if (ctxRow.attributes.nTipo == 3) {
                        m31.setVisible(true);
                        m41.setVisible(true);
                    }
                    if (ctxRow.attributes.nTipo == 4) {
                        m21.setVisible(true);
                        m41.setVisible(true);
                    }
                    if (ctxRow.attributes.nTipo == 5) {
                        m21.setVisible(true);
                        m31.setVisible(true);
                    }
                    contextmenu.showAt(event.xy);
                    return;
                }
            },
            sm: new Ext.grid.RowSelectionModel({
                singleSelect: true
            }),
            tbar: [{
                tooltip: _s('cmd-actualizar'),
                iconCls: 'icon-refresh',
                listeners: {
                    click: function(){
                        reload();
                    }
                }
            }, '-', {
                text: _s('curso-add-idioma'),
                tooltip: _s('cmd-addregistro'),
                handler: function(){
                    add_curso(null);
                },
                iconCls: 'icon-add'
            }, '-', {
                tooltip: _s('cmd-expandir'),
                iconCls: 'iconoExpandir',
                listeners: {
                    click: function(){
                        var f = Ext.getCmp(form_id + "_tree");
                        f.expandAll();
                    }
                }
            }, {
                tooltip: _s('cmd-contraer'),
                iconCls: 'iconoContraer',
                listeners: {
                    click: function(){
                        var f = Ext.getCmp(form_id + "_tree");
                        f.collapseAll();
                    }
                }
            }],
            loader: loader,
            root: new Ext.tree.AsyncTreeNode({
                expanded: true
            })
        });

		// Entregas
		var model2 = [{
			name : 'nIdEntrega',
			column : {
				header : _s("Id"),
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : 'id',
				sortable : true
			}
		}, {
			name : 'id'
		}, {
			name : 'cDescripcion',
			column : {
				header : _s("cDescripcion"),
				width : Ext.app.TAM_COLUMN_TEXT,
				editor : new Ext.form.TextField({
					selectOnFocus : true
				}),
				id : 'descripcion',
				sortable : true
			}
		}];

		var add_entrega = function() {
			var controls = [{
				name : 'cDescripcion',
				xtype : 'textarea',
				hideLabel : true,
				anchor : '100%',
				allowBlank : true,
				width : '90%'
			},
			{
				xtype : 'hidden',
				name : 'nIdCurso',
				value : form.getId()
			}];

			var url = site_url('eoi/entrega/add');

			var form2 = Ext.app.formStandarForm({
				icon : 'icon-direccion',
				controls : controls,
				timeout : false,
				title : _s('Añadir dirección de entrega'),
				url : url,
				fn_ok : function() {
					var grid = Ext.getCmp(form_id + '_entregas_grid');
					grid.store.load();
				}
			});

			form2.show();
		}

		var entregas = Ext.app.createFormGrid({
			model : model2,
			id : form_id + "_entregas",
			idfield : 'id',
			urlget : site_url("eoi/curso/get_entregas"),
			urldel : site_url("eoi/entrega/del"),
			urlupd : site_url("eoi/entrega/del"),
			rbar : [{
				tooltip : _s('cmd-addentrega'),
				text : _s('Añadir dirección de entrega'),
				iconCls : 'icon-direccion',
				id : form_id + "_add_entrega",
				listeners : {
					click : function(b) {
						add_entrega();
					}
				}
			}],
			anchor : '100% 85%',
			load : false
		});

		var controls = [descripcion, eoi, desde, hasta,{
                xtype: 'checkbox',
                id: 'bMostrarWeb',
                allowBlank: true,
                fieldLabel: _s('bMostrarWeb')
				}];

		form.addTab({
			title : _s('General'),
			iconCls : 'icon-general',
			items : {
				xtype : 'panel',
				layout : 'form',
				cls : 'form-eoi',
				items : form.addControls(controls)
			}
		});

		// Títulos
		form.addTab(new Ext.Panel({
		            layout: 'border',
		            id: form_id + "_titulos",
		            title: _s('Títulos'),
		            iconCls: 'iconoArticulosTab',
		            region: 'center',
		            baseCls: 'x-plain',
		            frame: true,
		            items: titulos
		        }));

		// Entregas
		form.addTab(new Ext.Panel({
		            layout: 'border',
		            id: form_id + "_entregas",
		            title: _s('Entregas'),
		            iconCls: 'icon-direccion',
		            region: 'center',
		            baseCls: 'x-plain',
		            frame: true,
		            items: entregas
		        }));

		// Notas
		var grid_notas = notas.init({
			id : form_id + "_notas",
			url : site_url('eoi/curso'),
			mainform : form
		});

		form.addTab(new Ext.Panel({
			layout : 'border',
			id : form_id + "_notas",
			title : _s('Histórico'),
			iconCls : 'icon-history',
			region : 'center',
			baseCls : 'x-plain',
			frame : true,
			items : grid_notas
		}));

		// Usuarios
		form.addTabUser();

		// Búsqueda
		var fn_open = function(id) {
			form.load(id);			
			form.selectTab(0);
		}

		var grid_search = search_cursointernet(form_id, fn_open);

		form.addTab({
			title : _s('Búsqueda'),
			iconCls : 'icon-search',
			items : Ext.app.formSearchForm({
				grid : grid_search,
				id_grid : form_id + '_g_search_grid'
			})
		});

		eoi.store.load();
		/*serie.store.load();
		caja.store.load();*/
		return form.show(open_id);
	} catch(e) {
		console.dir(e);
	}
})();
