(function(){
    var controls = [new Ext.ux.form.Spinner({
                    fieldLabel: _s('Fila'),
                    name: 'row',
                    value: 1,
                    width: 60,
                    strategy: new Ext.ux.form.Spinner.NumberStrategy({minValue: 1})
                }),
            new Ext.ux.form.Spinner({
                    fieldLabel: _s('Columna'),
                    name: 'column',
                    value: 1,
                    width: 60,
                    strategy: new Ext.ux.form.Spinner.NumberStrategy({minValue: 1})
                })];

    var url = site_url('concursos/concurso/imprimir_teixells');

    var form = Ext.app.formStandarForm({
        controls : controls,
        title : _s('Imprimir etiqueta'),
        icon : 'iconoTeixellsTab',
        url : url
    }); 

    form.show();
    return;
    
})();
