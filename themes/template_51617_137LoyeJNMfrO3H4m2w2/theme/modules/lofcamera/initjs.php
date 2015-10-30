
<script type="text/javascript">
    // <![CDATA[    
    $(document).ready(function() {                    
        jQuery(function(){			
            jQuery('#<?php echo $this->hookname; ?>').camera({
                thumbnails: <?php echo  $this->params->get('showSthumb', 'true');  ?>,
                autoAdvance: <?php echo  $this->params->get('auto', 'true');  ?>,
                barDirection: '<?php echo  $this->params->get('barDrirection', 'leftToRight');  ?>',
                fx: '<?php echo  $this->params->get('fx', 'stampede');  ?>',
                navigationHover: <?php echo  $this->params->get('navHover', 'false');  ?>,
                pagination: <?php echo  $this->params->get('showPagi', 'true')  ?>,
                playPause: <?php echo  $this->params->get('playBtn', 'true');  ?>,
                time: <?php echo  $this->params->get('time', 'true');  ?>,
                transPeriod: <?php echo  $this->params->get('transTime', 'true');  ?>,
                hover: <?php echo  $this->params->get('auto', 'true');  ?>,
                height: '<?php echo $this->params->get('slideHeight', '492px'); ?>'
            });
        });    
    });
    // ]]>
</script>
