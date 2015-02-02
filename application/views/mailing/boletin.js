(function(){

	var open_id = "<?php echo $open_id;?>";
    var form_id = "<?php echo $id;?>";
    var title = "<?php echo $title;?>";
    var icon = "<?php echo $icon;?>";
	if (title == '') title = _s('Boletín');
	if (icon == '') icon = 'iconoBoletinesTab';
	if (form_id == '') form_id = Ext.app.createId();
    
    var form = Ext.app.formGeneric();
    
    var list_grids = [form_id + '_libros_grid']
    
    // Carga
    var fn_load = function(id){
        Ext.app.formLoadList({
            list: list_grids,
            params: {
                where: 'nIdBoletin=' + id
            }
        });
    }
    
    // Borrado
    var fn_reset = function(){
        Ext.app.formResetList({
            list: list_grids,
            params: {
                where: 'nIdBoletin=-1'
            }
        });
    }
    
    var fn_enable_disable = function(){
        var list_buttons = [form.idform + 'btn_mailing', 
				form.idform + 'btn_noportada', 
                form.idform + 'btn_publicar', 
				form.idform + 'btn_stock', 
				form.idform + 'btn_nov_mat',
				form.idform + 'btn_sin_portada',
				form.idform + 'btn_promocionar',
				form.idform + 'btn_nov_tema'];
        
        Ext.app.formEnableList({
            list: list_buttons,
            enable: (form.getId() > 0)
        });
		
        Ext.app.formEnableList({
            list: list_grids,
            enable: (form.getId() > 0)
        });
    }
    
    form.init({
        id: id,
        title: title,
        icon: icon,
        url: site_url('mailing/boletin'),
        fn_load: fn_load,
        fn_reset: fn_reset,
        fn_enable_disable: fn_enable_disable
    });
    
    // Controles normales    
    var temas = Ext.app.combobox({
        url: site_url('mailing/tema/search'),
        id: 'nIdTema',
		anchor: "100%",
		allowBlank: true,
        label: _s('Tema')
    });
    
    var model = [{
        name: 'nIdLibro',
        column: {
            header: _s("Id"),
            width: Ext.app.TAM_COLUMN_ID,
            dataIndex: 'id',
            sortable: true
        }
    }, {
        name: 'id'
    }, {
        name: 'cAutores',
        column: {
            header: _s("cAutores"),
            width: Ext.app.TAM_COLUMN_TEXT,
            sortable: true
        },
        ro: true
    }, {
        name: 'cTitulo',
        column: {
            header: _s('cTitulo'),
            width: Ext.app.TAM_COLUMN_TEXT,
            id: 'descripcion',
            sortable: true
        },
        ro: true
    }];
    
    var fnselect = function(id){
        try {
            Ext.app.callRemote({
                url: site_url('mailing/boletinlibro/add'),
                params: {
                    'nIdBoletin': parseInt(form.getId()),
                    'nIdLibro': parseInt(id)
                },
                fnok: function(){
                    new_libro.setValue(null);
                    Ext.app.formLoadList({
                        list: [form_id + '_libros_grid'],
                        params: {
                            where: 'nIdBoletin=' + form.getId()
                        }
                    });
                }
            });
        } 
        catch (e) {
            console.dir(e);
        }
    }
    
    var new_libro = new Ext.form.ComboBox(Ext.app.autocomplete({
        url: site_url('catalogo/articulo/search'),
        name: form_id + "_addlibro",
        id: form_id + "_addlibro",
        fnselect: fnselect
    }));
    
    var libros = Ext.app.createFormGrid({
        model: model,
        id: form_id + "_libros",
        //title: _s("Libros"),
        //icon: "icon-libros",
        idfield: 'id',
        urlget: site_url("mailing/boletinlibro/get_list"),
        urldel: site_url("mailing/boletinlibro/del"),
        rbar: [{
            xtype: 'label',
            html: _s('new-libro')
        }, new_libro],
		anchor: '100% 70%',
        load: false
    });
    
	var grid = Ext.getCmp(form_id + '_libros_grid');
	var cm_lineas = fn_contextmenu();
	var contextmenu = Ext.app.addContextMenuLibro(grid, 'nIdLibro', cm_lineas);
	cm_lineas.setContextMenu(contextmenu)

    var controls = [{
        xtype: 'textfield',
        id: 'cDescripcion',
        anchor: '90%',
        allowBlank: false,
        fieldLabel: _s('cDescripcion')
    }, temas, {
        xtype: 'checkbox',
        id: 'bWeb',
        allowBlank: true,
        checked: true,
        fieldLabel: _s('Promocionar Web')
    },{
        xtype: 'textarea',
        id: 'cDescripcionCorta',
        anchor: '90%',
        allowBlank: false,
        fieldLabel: _s('cDescripcionCorta')
    },libros    
    ];
    
    form.addTab({
        title: _s('General'),
        iconCls: 'icon-general',
        items: {
            xtype: 'panel',
            layout: 'form',
            items: form.addControls(controls)
        }
    });
    
    
    form.addTabUser();
    
    var fn_open = function(id){
        form.load(id);
        form.selectTab(0);
    }
    
     <?php
     $obj =& get_instance();
     $obj->load->model('mailing/m_boletin');
     $modelo2 = $obj->m_boletin->get_data_model(array('tTexto'));
     ?>
     
     var grid_search_m = <?php echo extjs_creategrid($modelo2, $id.'_g_search', null, null, 'mailing.boletin', $this->reg->get_id(), null, FALSE, null, 'mode:"search", fn_open: fn_open');?>;
	 
   	form.addTab({
			title: _s('Búsqueda'),
	        iconCls : 'icon-search',
			items : Ext.app.formSearchForm({        
				grid: grid_search_m,
                audit: true,
				id_grid: form_id + '_g_search_grid'
			})
		});
	 
    // Acciones
    
    // Genera mailing
    var generarMailing = function(padre){
		    
        Ext.app.formSelectReport({
            list: site_url('mailing/boletin/report_list'),
			action: site_url('mailing/boletin/mailing'),
			id: padre.getId()
        });        
    };
	
	var addNovedades = function (padre, tema)
	{
		var list = (tema === true)?Ext.app.combobox({
					url : site_url('mailing/tema/search'),
					name : 'tema',
					label : _s('Tema')
				}):new Ext.form.ComboBox(Ext.app.autocomplete({
					url : site_url('catalogo/materia/search'),
					hiddenName : 'materia',
					label : _s('Materia')
				}));

		var controls = [list, {
					xtype : 'hidden',
					id : 'v_new_id',
					name : 'id',
					value: padre.getId()
					
				}, {
					xtype : 'datefield', startDay: Ext.app.DATESTARTDAY,
					name : 'desde',
					fieldLabel : _s('Desde')
				}, {
					xtype : 'numberfield',
					name : 'libros',
					fieldLabel : _s('Libros')
				}];

		var form = Ext.app.formStandarForm({
			controls : controls,
			title : (tema===true) ? _s('boletin-generar-add-novedades-tema'):_s('boletin-generar-add-novedades-materia'),			
			fn_ok: function()
			{			
				if (padre.getId() != null)
				{
					fn_load(padre.getId());
				}
			},
			url : site_url('mailing/boletinlibro/novedades')
		});
		if (tema === true) list.store.load();
		form.show();				
	};

	var addStock = function (padre)
	{
		var list = new Ext.form.ComboBox(Ext.app.autocomplete({
					url : site_url('generico/seccion/search'),
					hiddenName : 'seccion',
					label : _s('Sección')
				}));

		var controls = [list, {
					xtype : 'hidden',
					id : 'v_new_id',
					name : 'id',
					value: padre.getId()
					
				}, {
					xtype : 'checkbox',
					name : 'pendientes',
					fieldLabel : _s('Añadir pendientes')
				}];

		var form = Ext.app.formStandarForm({
			controls : controls,
			title : _s('boletin-generar-add-stock'),			
			fn_ok: function()
			{			
				if (padre.getId() != null)
				{
					fn_load(padre.getId());
				}
			},
			url : site_url('mailing/boletinlibro/stock')
		});
		
		form.show();				
	};
    
	
    form.addAction({
        text: _s('boletin-generar-mailing'),
        handler: function(){
            generarMailing(form);
        },
        iconCls: 'icon-email2',
        id: form.idform + 'btn_mailing'
    });
    

	// Herramientas
    form.addTools({
        text: _s('boletin-generar-add-stock'),
		iconCls: 'icon-add',
        handler: function(){
            addStock(form);
        },
        //iconCls: 'icon-stock',
        id: form.idform + 'btn_stock'
    });

    form.addTools({
        text: _s('boletin-generar-add-novedades-materia'),
		iconCls: 'icon-add',
        handler: function(){
            addNovedades(form);
        },
        //iconCls: 'icon-stock',
        id: form.idform + 'btn_nov_mat'
    });

    form.addTools({
        text: _s('boletin-generar-add-novedades-tema'),
		iconCls: 'icon-add',
        handler: function(){
            addNovedades(form, true);
        },
        //iconCls: 'icon-stock',
        id: form.idform + 'btn_nov_tema'
    });
	
	form.addTools('-');

    form.addTools({
        text: _s('Artículos sin portada en boletín'),
		iconCls: 'icon-portada',
        handler: function(){
			Ext.app.callRemote({
				url: site_url('mailing/boletin/sinportada'),
				params: {id: form.getId()}
			});            
        },
        id: form.idform + 'btn_sin_portada'
    });

    form.addTools({
        text: _s('Promocionar en la Web'),
		iconCls: 'iconoPromociones',
        handler: function(){
			Ext.app.callRemote({
				url: site_url('mailing/boletin/promocionar'),
				params: {id: form.getId()}
			});            
        },
        id: form.idform + 'btn_promocionar'
    });
    
    form.addTools('-');

    // Acciones
    form.addTools({
        text: _s('boletin-publicar-web'),
        handler: function() {
            Ext.app.callRemote({
                url: site_url('mailing/boletin/publicar'),
                params: {id: form.getId()},
                fn_ok: function()
                {
                    form.refresh();
                }
            });            
        },
        iconCls: 'icon-web',
        id: form.idform + 'btn_publicar'
    });
	    
    return form.show(open_id);
})();
