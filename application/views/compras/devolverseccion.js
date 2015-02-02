(function(){

    var seccion = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/seccion/search'),
		anchor: "90%",
		allowBlank: false,
		name: 'ids',
        label: _s('Seccion')
    }));
    
    //var id = Ext.app.createId();
    var controls = [seccion, {
        fieldLabel: _s('Crear devoluciones'),
        xtype: 'checkbox',
        name: 'crear',
        value: false
    },{
        fieldLabel: _s('Usar habitual'),
        xtype: 'checkbox',
        name: 'habitual',
        value: false
    }];
    var url = site_url('compras/devolucion/devolver_seccion');
    
    var form = Ext.app.formStandarForm({
        controls: controls,
		timeout: false,
		icon: 'iconoDevolverLibroSeccionTab',
        title: _s('Devolver Libros Sección'),
        url: url
    });
    seccion.store.load();
    form.show();
    return;
})();
