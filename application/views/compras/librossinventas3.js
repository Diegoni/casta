(function(){

    var store2 = new Ext.data.ArrayStore({
        fields: ['id', 'text'],
        data: [['cTitulo', _s('cTitulo')], ['cAutores', _s('cAutores')], ['s.cSeccion', _s('Sección')], 
		['s.cSeccion,cAutores', _s('Sección-Autor')], 
		['s.cSeccion,cTitulo', _s('Sección-Título')], 
		['cProveedor,s.cSeccion,cTitulo', _s('Proveedor-Sección-Título')], 
		['cProveedor,cTitulo', _s('Proveedor-Título')], ]
    });
    
    var orden = new Ext.form.ComboBox({
        store: store2,
        displayField: 'text',
        valueField: 'id',
        typeAhead: true,
        mode: 'local',
        forceSelection: true,
        triggerAction: 'all',
        emptyText: _s('Seleccione'),
        selectOnFocus: true,
        name: 'orden',
        hiddenName: 'orden',
        allowBlank: false,
        fieldLabel: _s('Orden')
    });
    
    var proveedores = new Ext.form.ComboBox(Ext.app.autocomplete({
        url: site_url('proveedores/proveedor/search'),
        name: 'idp',
        anchor: '100%',
        fieldLabel: _s('Proveedor')
    }));
    
    var materias = new Ext.form.ComboBox(Ext.app.autocomplete({
        url: site_url('catalogo/materia/search'),
        name: 'idm',
        anchor: '100%',
        fieldLabel: _s('Materia')
    }));
    
    var seccion = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/seccion/search'),
        anchor: "90%",
        allowBlank: false,
        name: 'ids',
        label: _s('Seccion')
    }));
    
    var controls = [seccion, proveedores, materias, orden];

    var url = site_url('compras/devolucion/libros_sin_venta3');
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        timeout: false,
        title: _s('Libros de más de 1 año, stock > 1'),
        url: url
    });
    seccion.store.load();
    form.show();
    return;
})();
