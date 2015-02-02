(function(){

    var concurso = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('concursos/concurso/search'),
        label: _s('Concursos'),
        name: 'concurso',
        anchor: '90%'
    }));
            
    concurso.store.load({
			callback: function() {
				var v = Ext.app.get_config('bp.albaranentrada.concurso.default', 'user');
				if (v != null && v != '')
					concurso.setValue(parseInt(v));
			}
		});
    var url = '<?php echo $url;?>';
    
    var form = Ext.app.formStandarForm({
        controls: [concurso],
        timeout: false,
        icon: 'iconoReportTab',
        title: _s('Selección de concurso'),
        url: url,
		fn_pre : function() {
			Ext.app.set_config('bp.albaranentrada.concurso.default', concurso.getValue(), 'user');
		}
    });
            
    form.show();
    return;
    
})();
