(function(){
    var controls = [{
				fieldLabel : _s('Año'),
				name : "year",
				xtype : "textfield"
			}];
    var url = site_url('oltp/oltp/ventas_series');
    
    var form = Ext.app.formStandarForm({
    	icon: 'iconoReportTab',
        controls: controls,
		timeout: false,
        title: _s('Ventas por series y meses'),
        url: url
    });
    
    form.show();
    return;
})();
