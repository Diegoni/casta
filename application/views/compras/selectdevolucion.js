(function(){
    var controls = [{
				fieldLabel : _s('Devolución'),
				name : "idd",
				xtype : "textfield"
			}];
    var url = "<?php echo $url;?>";
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        title: "<?php echo $title;?>",
        url: url
    });
    
    form.show();
    return;
})();
