(function(){

    var biblioteca = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('concursos/biblioteca/search'),
        label: _s('Bibliotecas'),
        name: 'biblioteca',
        anchor: '90%'
    }));
            
    biblioteca.store.load();

    var url = '<?php echo $url;?>';
    
    var form = Ext.app.formStandarForm({
        controls: [biblioteca],
        timeout: false,
        icon: 'iconoReportTab',
        title: _s('Selección de biblioteca'),
        url: url
    });
            
    form.show();
    return;
    
})();
