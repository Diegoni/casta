Ext.namespace('app');
app.AlbaranForm = function(config) {
	Ext.applyIf(this, config);
	this.initUIComponents();
	app.AlbaranForm.superclass.constructor.call(this);
};
Ext.extend(app.AlbaranForm, Ext.Panel, {
	initUIComponents : function() {
		// BEGIN OF CODE GENERATION PARTS, DON'T DELETE CODE BELOW
		this.store1 = new Ext.data.Store({
			reader : new Ext.data.JsonReader({
				total : "total",
				id : "id",
				root : "root"
			}, [{
				name : "name",
				mapping : "name"
			}, {
				name : "age",
				mapping : "age",
				type : "int"
			}]),
			proxy : new Ext.data.HttpProxy({})
		});

		this.gridPanel1 = new Ext.grid.GridPanel({
			store : this.store1,
			layout : "fit",
			columns : [{
				hidden : false,
				sortable : true,
				dataIndex : "name",
				header : "name"
			}, {
				hidden : false,
				sortable : true,
				dataIndex : "age",
				header : "age"
			}],
			selModel : new Ext.grid.RowSelectionModel({}),
			autoHeight : false
		});

		Ext.apply(this, {
			region : "center",
			items : [{
				frame : "true",
				items : [{
					items : [{
						xtype : "textfield"
					}, {
						xtype : "textfield"
					}],
					layout : "form",
					columnWidth : ".5"
				}, {
					items : [{
						xtype : "textfield"
					}, {
						xtype : "textfield"
					}],
					layout : "form",
					columnWidth : ".5"
				}],
				layout : "column",
				xtype : "form"
			}, this.gridPanel1, {}]
		});
		// END OF CODE GENERATION PARTS, DON'T DELETE CODE ABOVE
	}
});
