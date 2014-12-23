(function(){
    var propsGrid = new Ext.grid.PropertyGrid({
        anchor: '100% 100%',
		//height:500;
        //autoHeight: true,
        source: {              <?php echo $items;?>
        },
        viewConfig: {
            forceFit: true,
            scrollOffset: 2 // the grid will never have scrollbars
        }
    });
    var controls = [propsGrid];
    var url = site_url('<?php echo $url;?>');
    var old_vars = [];
    
    var form = Ext.app.formStandarForm({
        controls: controls,
        icon: 'iconoConfiguracionTab',
		height: 500,
        timeout: false,
        title: _s('<?php echo $title;?>'),
        //url: url,
        show: function(){
            old_vars = propsGrid.getSource();
        },
        fn_ok: function(res){
            var new_vars = propsGrid.getSource();
            Ext.app.callRemote({
                url: url,
                params: new_vars
            });
        }
    });
    
    form.show();
    return;
    
})();
