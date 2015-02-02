(function(){
	try {
		
	var data = [
	<?php foreach ($reports as $report):?>
	['<?php echo $report['id'];?>', '<?php echo $report ['text'];?>'],
	<?php endforeach; ?> 
	];
	
	
	Ext.app.formSelectReport2({
		action: '<?php echo $action;?>',
		title: '<?php echo $title;?>',
		lang: '<?php echo $lang;?>',
		list: data,
		id: '<?php echo $id;?>' 
	});
	//console.log('OK');
	}
	 catch (e)
	 {
	 	console.dir(e);
	 }
    return;
	
	
})();
