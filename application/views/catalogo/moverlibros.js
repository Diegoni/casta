(function() {
	<?php
	echo extjs_reader_secciones('dssec');
	 
	$data['name'] = 'dslibros';
	$data['id'] = 'nIdLibro';
	$data['url'] = site_url('seccion/get_libros');
	$data['fields'][] = array('name' => 'nIdLibro');
	$data['fields'][] = array('name' => 'cISBN');
	$data['fields'][] = array('name' => 'cAutores');
	$data['fields'][] = array('name' => 'cTitulo');
	$data['fields'][] = array('name' => 'nStock');
	echo extjs_createjsonreader($data); 
	?>

	function accion(url, title, checkdestino) {
		var idsec1 = seccionOrigen.getValue();
		var idsec2 = seccionDestino.getValue();
		var codes = '';
		var sel = sm.getSelections();
		for (var i = 0; i < sel.length; i = i + 1) {
			codes += sel[i].data.nIdLibro + ';';
		}
		if (sel.length == 0) {
			Ext.app.msgFly("<?php echo $title;?>",
					"<?php echo $this->lang->line('no-libros-marcados'); ?>");
			return;
		}

		if (idsec2 == '' && checkdestino) {
			Ext.app.msgFly("<?php echo $title;?>",
					"<?php echo $this->lang->line('no-sec-destino'); ?>");
			return;
		}

		Ext.MessageBox.show({
					msg : title,
					width : 300,
					wait : true,
					icon : 'ext-mb-download'
				});

		var fnok = function(obj) {
			Ext.MessageBox.hide();
			if (obj.success) {
				var g = Ext.getCmp("<?php echo $id;?>_grid");

				for (var i = 0; i < sel.length; i = i + 1) {
					g.getStore().remove(sel[i]);
				}
			}
		};

		var fnnok = function() {
			Ext.MessageBox.hide();
		};
		// console.dir(codes);
		Ext.app.callRemote({
			url : url,
			title : "<?php echo $title;?>",
			errormessage : "<?php echo $this->lang->line('registro_error'); ?>",
			params : {
				ids : codes,
				idorigen : idsec1,
				iddestino : idsec2
			},
			fnok : fnok,
			fnnok : fnnok
		});
	};

	var seccionOrigen = new Ext.form.ComboBox(Ext.app
			.combobox({url: "<?php echo site_url('seccion/search');?>"}));
	var seccionDestino = new Ext.form.ComboBox(Ext.app
			.combobox({url: "<?php echo site_url('seccion/search');?>"}));

	var sm = new Ext.grid.CheckboxSelectionModel();
	var paging = new Ext.PagingToolbar({
		pageSize : <?php echo $this->config->item('bp.data.limit_big');?>,
		store : dslibros,
		displayInfo : true,
		displayMsg : "<?php echo $this->lang->line('grid_desplay_result'); ?>",
		emptyMsg : "<?php echo $this->lang->line('grid_desplay_no_topics'); ?>",
		width : 450
	});

	function reload() {
		var idsec = seccionOrigen.getValue();
		if (idsec != '') {
			dslibros.baseParams = {
				id : idsec
			};
			dslibros.load({
				params : {
					id : idsec,
					start : 0,
					limit : <?php echo $this->config->item('bp.data.limit_big');?>
				},
				waitMsg : "<?php echo $this->lang->line('Cargando'); ?>"
			});
		}
	};

	var secciones = new Ext.Panel({
		layout : 'border',
		title : "<?php echo $title;?>",
		id : "<?php echo $id;?>",
		iconCls : "<?php echo $icon;?>",
		region : 'center',
		closable : true,
		baseCls : 'x-plain',
		frame : true,
		items : [{
			region : 'center',
			xtype : 'grid',
			id : "<?php echo $id;?>_grid",
			autoExpandColumn : "cTitulo",
			loadMask : true,
			stripeRows : true,
			store : dslibros,
			sm : sm,
			columns : [new Ext.grid.RowNumberer(), sm, {
						header : "<?php echo $this->lang->line('Id'); ?>",
						width : Ext.app.TAM_COLUMN_ID,
						dataIndex : 'nIdLibro',
						sortable : true
					}, {
						header : "<?php echo $this->lang->line('ISBN'); ?>",
						width : Ext.app.TAM_COLUMN_ISBN,
						dataIndex : 'cISBN',
						sortable : true
					}, {
						header : "<?php echo $this->lang->line('Autores'); ?>",
						width : Ext.app.TAM_COLUMN_AUTHORS,
						dataIndex : 'cAutores',
						sortable : true
					}, {
						header : "<?php echo $this->lang->line('Título'); ?>",
						width : Ext.app.TAM_COLUMN_TITLE,
						dataIndex : 'cTitulo',
						// renderer: render_html,
						id : 'cTitulo',
						sortable : true
					}, {
						header : "<?php echo $this->lang->line('Stock'); ?>",
						width : Ext.app.TAM_COLUMN_STOCK,
						dataIndex : 'nStock',
						sortable : true
					}],
			tbar : [{
				tooltip : "<?php echo $this->lang->line('cmd-eliminarlibros'); ?>",
				text : "<?php echo $this->lang->line('Eliminar Libros Sección'); ?>",
				iconCls : 'icon-delete',
				listeners : {
					click : function(button) {
						accion(
								"<?php echo site_url('seccion/del_books_ids');?>",
								"<?php echo $this->lang->line('Eliminando Libros en Seción');?>",
								false);
					}

				}
			}, '->', {
				xtype : 'label',
				html : "<?php echo $this->lang->line('Sección Origen'); ?>&nbsp;"
			}, seccionOrigen, {
				tooltip : "<?php echo $this->lang->line('cmd-actualizar'); ?>",
				iconCls : 'icon-refresh',
				listeners : {
					click : function() {
						reload();
					}
				}
			}],
			bbar : [paging, '->', {
				xtype : 'label',
				html : "<?php echo $this->lang->line('Sección Destino'); ?>&nbsp;"
			}, seccionDestino, '->', {
				tooltip : "<?php echo $this->lang->line('cmd-moverlibros'); ?>",
				text : "<?php echo $this->lang->line('Mover Libros'); ?>",
				iconCls : 'icon-accept',
				listeners : {
					click : function() {
						accion(
								"<?php echo site_url('seccion/move_books_ids');?>",
								"<?php echo $this->lang->line('Movimiendo Libros');?>",
								true);
					}

				}
			}]
		}]
	});

	var stores = [{
				store : seccionOrigen.store
			}, {
				store : seccionDestino.store
			}];
	Ext.app.loadStores(stores);
	return secciones;
})();
