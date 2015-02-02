(function(){
	Ext.app.formSelectReport({
		list: site_url('mailing/boletin/report_list'),
		action: site_url('mailing/boletin/printer'),
		id: '<?php echo $id;?>' 
	});
    return;
})();
