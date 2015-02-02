(function(){
    var seccion1 = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/seccion/search'),
        label: _s('Origen'),
        name: 'ido',
        anchor: '90%'
    }));

    var seccion2 = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/seccion/search'),
        label: _s('Destino'),
        name: 'idd',
        anchor: '90%'
    }));

    var controls = [seccion1, seccion2];

    var url = site_url('catalogo/movimiento/todo');
    
    seccion1.store.load();
    seccion2.store.load();
    
    var form = Ext.app.formStandarForm({
        icon: 'iconoSeccionMoverTab',
        controls: controls,
        timeout: false,
        title: _s('Mover TODOS los libros de una sección'),
        url: url
    });
    
    form.show();
    return;    
})();
