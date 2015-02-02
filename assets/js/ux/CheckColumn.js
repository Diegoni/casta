Ext.ns('Ext.ux.grid');

Ext.ux.grid.CheckColumn = function(config) {
	Ext.apply(this, config);
	if (!this.id) {
		this.id = Ext.id();
	}

	// call parent
	Ext.ux.grid.CheckColumn.superclass.constructor.call(this);
};

Ext.extend(Ext.ux.grid.CheckColumn, Ext.util.Observable, {
	/**
	 * @cfg {String} actionEvent Event to trigger actions, e.g. click, dblclick,
	 *      mouseover (defaults to 'click')
	 */
	actionEvent : 'dblclick',
	init : function(grid) {
		this.grid = grid;

		// the actions column must have an id for Ext 3.x
		this.id = this.id || Ext.id();
		var lookup = grid.getColumnModel().lookup;
		delete (lookup[undefined]);
		lookup[this.id] = this;

		var view = grid.getView();
		var cfg = {
			scope : this
		};

		// setup renderer
		if (!this.renderer) {
			this.renderer = this.renderer.createDelegate(this);
		}

		this.grid.on('render', function() {
			var view = this.grid.getView();
			view.mainBody.on(this.actionEvent, this.onClick, this);
		}, this);
	},
	onClick : function(e, t) {
		if (t.className && t.className.indexOf('x-grid3-cc-' + this.id) != -1) {
			e.stopEvent();
			var index = this.grid.getView().findRowIndex(t);
			var record = this.grid.store.getAt(index);
			var cm = this.grid.getColumnModel();
			// if (cm.isCellEditable(cm.getIndexById(this.id), index) !== false)
			// {
			record.set(this.dataIndex, !record.data[this.dataIndex]);
			this.grid.fireEvent('afteredit', {
				grid : this.grid,
				record : record,
				value : record.data[this.dataIndex],
				orignalValue : !record.data[this.dataIndex],
				field: this.dataIndex
			});
			// }
		}
	},

	renderer : function(v, p, record) {
		if (p!=null)
			p.css += ' x-grid3-check-col-td';
		return '<div class="x-grid3-check-col' + (v ? '-on' : '') + ' x-grid3-cc-' + this.id + '">' 
		+ (v && (p==null)?'[x]': '&#160') + '</div>';			
	}

});

Ext.reg('checkcolumn', Ext.ux.grid.CheckColumn);
