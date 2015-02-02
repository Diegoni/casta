(function(){
    var controls = [{
        fieldLabel: "<?php echo $label;?>",
		name: 'ids',
        xtype: "textarea",
		anchor: '100% 100%',
		grow: true		
    }];
    var url = "<?php echo $url;?>";
    
    var form = Ext.app.formStandarForm({
        controls: controls,
		timeout: false,
        icon: "<?php echo $icon;?>",
        title: "<?php echo $title;?>",
        url: url
    });
    
    form.show();
    return;
})();
