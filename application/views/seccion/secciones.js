(function() {
	var secciones = new Ext.Panel({
		layout : 'border',
		title : "<?php echo $title;?>",
		id : "<?php echo $id;?>",
		iconCls : "<?php echo $icon;?>",
		region : 'center',
		closable : true,
		baseCls : 'x-plain',
		frame : true,
		items : [new Ext.tree.ColumnTree({
			region : 'center',
			id : "<?php echo $id;?>_tree",
			autoScroll : true,
			// lines : false,
			// split : true,
			useArrows : true,
			loadMask : true,
			// animate: true,
			rootVisible : false,
			/*
			 * listeners : { click : function(node, e) { if (node.leaf == true) {
			 * var T = Ext.getCmp('Tabs'); Ext.app.AddCommandToTab(T, node.id,
			 * node.text, node.attributes.iconCls + 'Tab'); } } },
			 */
			columns : [{
						header : 'Sección',
						width : 330,
						dataIndex : 'text'
					}, {
						header : 'Id',
						width : Ext.app.TAM_COLUMN_ID,
						dataIndex : 'nIdSeccion'
					}, {
						header : 'Bloqueada',
						width : Ext.app.TAM_COLUMN_BOOL,
						dataIndex : 'bBloqueada'
					}, {
						header : 'Web',
						width : Ext.app.TAM_COLUMN_BOOL,
						dataIndex : 'bWeb',
						format: 'bool',
						editable: 'true'
					}, {
						header : 'U.Creación',
						width : Ext.app.TAM_COLUMN_USER,
						dataIndex : 'cCUser'
					}, {
						header : 'F.Creación',
						width : Ext.app.TAM_COLUMN_DATE,
						//renderer: Ext.app.renderDate,
						//sortable : true,
						dataIndex : 'dCreacion'
					}, {
						header : 'U.Actualización',
						width : Ext.app.TAM_COLUMN_USER,
						dataIndex : 'cAUser'
					}, {
						header : 'F.Actualización',
						width : Ext.app.TAM_COLUMN_DATE,
						dataIndex : 'dAct'
					}],
			tbar : [{
						tooltip : "<?php echo $this->lang->line('cmd-expandir'); ?>",
						iconCls : 'iconoExpandir',
						listeners : {
							click : function() {
								var f = Ext.getCmp("<?php echo $id;?>_tree");
								f.expandAll();
							}
						}
					}, {
						tooltip : "<?php echo $this->lang->line('cmd-contraer'); ?>",
						iconCls : 'iconoContraer',
						listeners : {
							click : function() {
								var f = Ext.getCmp("<?php echo $id;?>_tree");
								f.collapseAll();
							}
						}
					}],
			loader : new Ext.tree.TreeLoader({
						loadMask : true,
						uiProviders : {
							'col' : Ext.tree.ColumnNodeUI
						},
						dataUrl : "<?php echo site_url('seccion/get_tree');?>"
					}),
			root : new Ext.tree.AsyncTreeNode({
						expanded : true
					})
		})]
	});

	return secciones;
})();
