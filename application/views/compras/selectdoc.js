(function(){
    var controls = [new Ext.app.autocomplete2({
            allowBlank: false,
            url: "<?php echo $url_search;?>",
            fieldLabel: "<?php echo $label;?>",
            name: 'idd',
            anchor: '90%'
        })];
    var url = "<?php echo $url_action;?>";
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        title: "<?php echo $title;?>",
        url: url
    });
    
    form.show();
    return;
})();
