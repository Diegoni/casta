(function(){
    var id = Ext.app.createId();
    var controls = [{
        fieldLabel: _s('Emails'),
		name: 'emails',
        xtype: "textarea",
		anchor: '100% 100%',
		grow: true		
    }];
    var url = site_url('mailing/mailing/delete');
    
    var form = Ext.app.formStandarForm({
		icon: 'iconoEliminarEmailsTab',
        controls: controls,
        title: _s('mailing-delete-emails'),
        url: url
    });
    
    form.show();
    return;
})();
