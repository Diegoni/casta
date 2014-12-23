<?php
$obj = get_instance(); 
$obj->load->library('Portlets');?>
(function(){
    try {
    
        var id = "<?php echo $id;?>";
        var title = "<?php echo $title;?>";
        var icon = "<?php echo $icon;?>";
        
        // create some portlet tools using built in Ext tool ids
        var general_tools = [{
            id: 'gear',
            handler: function(){
                Ext.Msg.alert('Message', 'The Settings tool was clicked.');
            }
        }, {
            id: 'close',
            handler: function(e, target, panel){
                panel.ownerCt.remove(panel, true);
            }
        }];
        
        
<?php echo $obj->portlets->get_portlets_js(); ?>		
		
        <?php echo $obj->portlets->get_portlets_user('form'); ?>;
		
        var panel = new Ext.Panel({			
            layout: 'border',
            title: title,
            id: id,
            iconCls: icon,
            region: 'center',
            closable: true,
            baseCls: 'x-plain',
            frame: true,
            items: [form]
        });
        
        return panel;
    } 
    catch (e) {
        console.dir(e);
    }
})();
