(function() {

	var cola = new Array();
	var running = false;

	function run() {
		if (cola.length > 0) {
			var per = 1.0 / cola.length;
			pbar2
					.updateProgress(
							per,
							Math.round(per * 100)
									+ "<?php echo $this->lang->line('% completado'); ?>");
			if (!running) {
				var id = cola.pop();
				running = true;
				grid.store.load({
					params : {
						codes : id
					},
					add : true,
					method : 'POST',
					timeout : 300000,
					callback : function() {
						running = false;
						run();
					}// ,
						// waitMsg : "<?php echo $this->lang->line('Cargando');
						// ?>"
					});
			}
		} else {
			pbar2.reset();
			pbar2.updateText("<?php echo $this->lang->line('Finalizado'); ?>");
			running = false;
		}
	}
	function getdatos() {
		var id = form.findById('isbn').getValue();
		var ids = id.split(" ");
		var codes = '';
		var code = '';
		for (var i = 0; i < ids.length; i = i + 1) {
			code = ids[i].trim();
			if (code != '') {
				if (cola.indexOf(code) == -1) {
					codes += code + '<BR/>';
					cola.push(code);
				}
			}
		}
		Ext.app.msgFly("<?php echo $this->lang->line('add-codes'); ?>", codes);
		run();
		return;
	}
	/*
	 * var robots = new Ext.tree.TreePanel('tree-div', { region : 'west',
	 * animate : true, loader : new Ext.tree.CustomUITreeLoader({ dataUrl : "
	 * site_url('buscadorlibros/get_robots');?>", baseAttr : { uiProvider :
	 * Ext.tree.CheckboxNodeUI } }), enableDD : false, containerScroll : true,
	 * rootUIProvider : Ext.tree.CheckboxNodeUI, selModel : new
	 * Ext.tree.CheckNodeMultiSelectionModel(), rootVisible : false }); // set
	 * the root node var root = new Ext.tree.AsyncTreeNode({ text : 'root',
	 * draggable : false, id : 'source', uiProvider : Ext.tree.CheckboxNodeUI
	 * }); tree.setRootNode(root); // render the tree tree.render();
	 */

	function render_imagen(val, x, r, row, col) {
		return '<img src=\'' + r.data.fields.Imagen + '\'/>';
	}
	function render_html(val, x, r, row, col) {
		return r.code;
	}

	var form = new Ext.FormPanel({
		region : 'west',
		width : 150,
		labelWidth : 75,
		labelAlign : 'top',
		collapsible : true,
		collapseMode : 'mini',
		title : "<?php echo $this->lang->line('Códigos'); ?>",
		bodyStyle : 'padding:5px 5px 0',
		defaultType : 'textfield',

		items : [{
					xtype : 'textarea',
					fieldLabel : "<?php echo $this->lang->line('ISBNs/EAN'); ?>",
					name : 'isbn',
					anchor : '95% 90%',
					id : 'isbn'
				}],
		bbar : [{
					tooltip : "<?php echo $this->lang->line('cmd-bucarlibros'); ?>",
					text : "<?php echo $this->lang->line('Buscar'); ?>",
					iconCls : 'icon-search',
					listeners : {
						click : function() {
							getdatos();
						}

					}
				}, {
					text : "<?php echo $this->lang->line('Limpiar'); ?>",
					iconCls : 'icon-clean',
					listeners : {
						click : function() {
							form.getForm().reset();
						}
					}
				}]
	});
	var store = new Ext.data.JsonStore({
				url : "<?php echo site_url('buscadorlibros/search');?>",
				root : 'value_data',
				fields : ['isbn', 'url', 'source', 'version', 'fields', 'time',
						'code', 'isbn13']
			});

	var grid = new Ext.grid.GridPanel({
				region : 'center',
				autoExpandColumn : "descripcion",
				// loadMask : true,
				stripeRows : true,
				store : store,
				columns : [{
							header : "<?php echo $this->lang->line('Imagen'); ?>",
							width : Ext.app.TAM_COLUMN_IMAGE,
							dataIndex : 'fields',
							renderer : render_imagen,
							sortable : true
						}, {
							header : "<?php echo $this->lang->line('Id'); ?>",
							width : Ext.app.TAM_COLUMN_ISBN,
							dataIndex : 'code',
							sortable : true
						}, {
							header : "<?php echo $this->lang->line('ISBN'); ?>",
							width : Ext.app.TAM_COLUMN_ISBN,
							dataIndex : 'isbn13',
							sortable : true
						}, {
							header : "<?php echo $this->lang->line('Fuente'); ?>",
							width : 50,
							dataIndex : 'source',
							sortable : true
						}, {
							header : "<?php echo $this->lang->line('URL'); ?>",
							width : 50,
							id : 'descripcion',
							dataIndex : 'url',
							sortable : true
						}, {
							header : "<?php echo $this->lang->line('Tiempo'); ?>",
							width : 50,
							dataIndex : 'time',
							sortable : true
						}],
				viewConfig : {
					forceFit : true,
					enableRowBody : true,
					showPreview : true,
					getRowClass : function(record, rowIndex, p, store) {
						p.body = '<p>' + record.data.code + '</p>' + '<b>'
								+ record.data.fields.Titulo + '</b>' + '<p>'
								+ record.data.fields.Sinopsis + '</p>';
						return 'x-grid3-row-expanded';
						// return 'x-grid3-row-collapsed';
					}
				}
			});

	var pbar2 = new Ext.ProgressBar({
				text : "<?php echo $this->lang->line('Preparado'); ?>",
				id : 'pbar2',
				cls : 'custom',
				region : 'south',
				height : 20
			});
	var border = new Ext.Panel({
				layout : 'border',
				title : "<?php echo $title;?>",
				id : "<?php echo $id;?>",
				iconCls : "<?php echo $icon;?>",
				region : 'center',
				closable : true,
				baseCls : 'x-plain',
				frame : true,
				items : [form, grid, pbar2]
			});
	return border;
})();
