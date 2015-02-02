(function(){
    var controls = [new Ext.app.autocomplete2({
        allowBlank: false,
        url: site_url('clientes/cliente/search'),
        fieldLabel: _s('Cliente'),
        name: 'cliente',
        anchor: '90%'
    }), {
        xtype: 'fileuploadfield',
        id: 'excelfile',
        emptyText: _s('Fichero EXCEL'),
        fieldLabel: _s('Fichero'),
        anchor: '90%',
        name: 'excelfile',
        buttonCfg: {
            text: '',
            iconCls: 'upload-icon'
        }
    }, {
		xtype: 'textfield',
		allowBlank: true,
        fieldLabel: _s('Filtro'),
        anchor: '40%',
		name: 'filtro'
	}];
    var url = site_url('concursos/importar/excel');
    var fnok = function(){
		//form.hide();
        Ext.app.msgInfo(_s('Importar EXCEL'), _s('concursos_importado_ok'));		
    }
    var form = Ext.app.formStandarForm({
        controls: controls,
        title: _s('Importar EXCEL'),
        upload: true,
		fly: true,
        url: url,
        fn_ok: fnok
    });
    
    form.show();
    return;
})();

