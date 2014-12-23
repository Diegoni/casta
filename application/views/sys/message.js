(function() {
	try {
		Ext.app.msgInfo("<?php echo $title;?>", "<?php echo $message;?>");
	} catch (e) {
		console.dir(e);
	}
})();
