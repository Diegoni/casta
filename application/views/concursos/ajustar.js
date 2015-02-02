(function(){

    var concurso = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('concursos/biblioteca/search'),
        label: _s('Bibliotecas'),
        name: 'biblioteca',
        anchor: '90%'
    }));
            
    concurso.store.load();
    var url = site_url('concursos/concurso/ajustar');
    
    var form = Ext.app.formStandarForm({
        controls: [concurso, { 
            xtype: 'numberfield',
            fieldLabel: _s('fImporte'),
            name: 'importe'
        }],
        timeout: false,
        icon: 'iconoAjustarTab',
        title: _s('Ajustar'),
        url: url
    });
            
    form.show();
    return;
    
})();
