(function() {
	var form_id = Ext.app.createId();

	var id = Ext.app.createId();

	var cmpid = "<?php echo $this->input->get_post('cmpid');?>";
	var autor = "<?php echo $this->input->get_post('autor');?>";
	autor = ucwords(autor.toLowerCase());
	var c = Ext.getCmp(cmpid);

	var controls = [{
		xtype : 'compositefield',
		fieldLabel : _s('cNombre'),
		items : [{
			xtype : 'textfield',
			id : id + '_n',
			name : 'cNombre',
			value : autor,
			allowBlank : true
		}, {
			xtype : 'button',
			iconCls : 'icon-split',
			width : 30,
			value : '',
			handler : function() {
				var a = Ext.getCmp(id + '_a');
				var n = Ext.getCmp(id + '_n');
				part_names(n, a);
			}
		}]
	}, {
		xtype : 'textfield',
		name : 'cApellido',
		id : id + '_a',
		allowBlank : true,
		fieldLabel : _s('cApellido')
	}]

	var url = site_url('catalogo/autor/add');

	var form = Ext.app.formStandarForm({
		controls : controls,
		width : 400,
		icon : 'iconoAutoresTab',
		title : _s('Alta rápida autor'),
		fn_ok : function(res) {
			if(cmpid != '') {
				var c = Ext.getCmp(cmpid);
				if(c != null) {
					c.load(res);
				}
			}
		},
		url : url
	});
	form.show();
	return;

})();
