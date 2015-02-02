(function(){
    var id = Ext.app.createId();
    var concursos = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('concursos/concurso2/search'),
        label: _s('Concurso'),
        name: 'concurso',
        anchor: '90%'
    }));
    
    var controls = [concursos];
    var form = Ext.app.formStandarForm({
        controls: controls,
        title: "Albaranes del concurso",
        url: site_url('concursos/albaran/get_albaranes')
    });
	
    concursos.getStore().load();
	
    form.show();
    return;
})();
