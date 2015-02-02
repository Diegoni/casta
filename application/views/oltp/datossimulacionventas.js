(function() {

	var series = new Ext.form.ComboBox(Ext.app
			.combobox({url: "<?php echo site_url('generico/serie/search');?>"}));

	var model = [{
				name : 'id',
				column : {
					header : "<?php echo $this->lang->line('Id'); ?>",
					width : Ext.app.TAM_COLUMN_ID,
					dataIndex : 'id',
					sortable : true
				}
			}, {
				name : 'nIdIncremento'
			}, {
				name : 'nYear',
				column : {
					header : "<?php echo $this->lang->line('Año'); ?>",
					width : Ext.app.TAM_COLUMN_NUMBER,
					// id : 'descripcion',
					editor : new Ext.form.NumberField(),
					sortable : true
				},
				add : {
					xtype : 'numberfield',
					anchor : '95%',
					allowBlank : false
				}
			}, {
				name : 'nIdSerie',
				column : {
					header : "<?php echo $this->lang->line('Serie'); ?>",
					width : Ext.app.TAM_COLUMN_TEXT,
					editor : series,
					renderer : function(val) {
						return Ext.app.renderCombo(val, series);
					},
					sortable : true
				},
				add : Ext.app
						.combobox({
								url:"<?php echo site_url('serie/search');?>",
								id: 'nIdEOI', 
								label: "<?php echo $this->lang->line('Serie');?>",
								autoload: true, 
								allowBlank: false})
			}, {
				name : 'fIncremento',
				column : {
					header : "<?php echo $this->lang->line('Cliente'); ?>",
					width : Ext.app.TAM_COLUMN_ID,
					editor : new Ext.form.TextField(),
					sortable : true
				},
				add : new Ext.form.NumberField({
							name : 'nIdCliente',
							fieldLabel : "<?php echo $this->lang->line('Cliente');?>"
						})
			}];

	var stores = [{
				store : eois.store
			}];

	return Ext.app.createFormGrid({
				model : model,
				id : "<?php echo $id;?>",
				title : "<?php echo $title;?>",
				icon : "<?php echo $icon;?>",
				idfield : 'id',
				urlget : "<?php echo site_url('eoi/departamento/get_list');?>",
				urladd : "<?php echo site_url('eoi/departamento/add');?>",
				urlupd : "<?php echo site_url('eoi/departamento/upd');?>",
				urldel : "<?php echo site_url('eoi/departamento/del');?>",
				loadsstores : stores,
				groupField : 'nIdEOI',
				sortInfo : 'cDescripcion'
			});
})();