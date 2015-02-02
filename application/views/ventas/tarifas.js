(function(){

    var divisa = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('generico/divisa/search'),
        name: 'divisa',
        label: _s('Divisa Compra')
    }));
    
    var tipos = new Ext.form.ComboBox(Ext.app.combobox({
        url: site_url('catalogo/tipolibro/search'),
        name: 'tipo',
        label: _s('Tipo artículo')
    }));
    
    var controls = [{
        xtype: 'textfield',
        name: 'precio',
        allowBlank: false,
        anchor: '50%',
        fieldLabel: _s('Importe')
    }, divisa, {
        xtype: 'textfield',
        name: 'portes',
        value: "<?php echo $this->config->item('bp.divisa.portes');?>",
        allowBlank: false,
        anchor: '50%',
        fieldLabel: _s('Portes')
    }, {
        xtype: 'textfield',
        name: 'dto',
        value: "<?php echo $this->config->item('bp.divisa.dtoprv');?>",
        allowBlank: false,
        anchor: '50%',
        fieldLabel: _s('Dto. Proveedor')
    }, tipos];
    
    var url = site_url('ventas/cambiodivisa/tarifas');
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        icon: 'iconoCalculoTarifasTab',
        title: _s('Cálculo Tarifas'),
        labelWidth: 100,
        url: url
    });
    
    Ext.app.loadStores([{
        store: divisa.store
    }, {
        store: tipos.store
    }], function(){
        tipos.setValue("<?php echo $this->config->item('bp.divisa.tipodefault');?>");
        divisa.setValue("<?php echo $this->config->item('bp.divisa.default');?>");
    });
    form.show();
    return;
})();
