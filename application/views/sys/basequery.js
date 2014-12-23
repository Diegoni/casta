(function(){

    Ext.app.callRemoteAsk({
        icon: '<?php echo $icon;?>',
        timeout: false, 
        params: <?php echo isset($params)?$params:'null';?>,
        url : '<?php echo $url;?>',
        title : '<?php echo $title;?>',
        askmessage : '<?php echo $text;?>'
    });

})();
