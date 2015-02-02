(function(){

    var store = new Ext.data.ArrayStore({
        fields: ['id', 'year'],
        data: [[1, _s('1 año')], [2, _s('2 años')], [3, _s('3+ años')]]
    });
    
    var store2 = new Ext.data.ArrayStore({
        fields: ['id', 'text'],
        data: [
            ['cTitulo', _s('cTitulo')], 
            ['cAutores', _s('cAutores')], 
            ['cSeccion', _s('Sección')], 
            ['cSeccion,cAutores', _s('Sección-Autor')], 
            ['cSeccion,cTitulo', _s('Sección-Título')],
            ['cProveedor,cSeccion,cTitulo', _s('Proveedor-Sección-Título')],
            ['cProveedor,cTitulo', _s('Proveedor-Título')],
		]
    });

    var tipo = new Ext.form.ComboBox({
        store: store,
        displayField: 'year',
        valueField: 'id',
        typeAhead: true,
        mode: 'local',
        forceSelection: true,
        triggerAction: 'all',
        emptyText: _s('Seleccione'),
        selectOnFocus: true,
        name: 'tipo',
        hiddenName: 'tipo',
        fieldLabel: _s('Antiguedad')
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
        fieldLabel: _s('Orden')
    });
    
    var seccion = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/seccion/search'),
        anchor: "90%",
        allowBlank: false,
        name: 'ids',
        label: _s('Seccion')
    }));
    
    var controls = [{
        fieldLabel: _s('Fecha'),
        value: new Date(),
        name: 'fecha',
        allowBlank: false,
        startDay: Ext.app.DATESTARTDAY,
        xtype: "datefield"
    }, seccion, tipo, orden];
    
    var url = site_url('oltp/oltp/desglose_antiguedad');
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        timeoout: false,
        title: _s('Desglose antiguedad'),
        url: url
    });
    seccion.store.load();
    form.show();
    return;
})();
