(function() {

	try {
		var form_id = Ext.app.createId();

		var controls = [{
			xtype : 'hidden',
			id : form_id + '_files',
			value : '',
			name : 'file'
		}, {
			xtype : 'awesomeuploader',
			gridHeight : 100,
			gridWidth : 440,
			height : 160,
			width : 460,
			supressPopups : true,
			//frame: true,
			flashSwfUploadPath : slash_item() + "/assets/js/ux/swfupload.swf",
			flashButtonSprite : slash_item() + "/assets/images/swfupload_browse_button_trans_56x22.png",
			flashUploadUrl : site_url('sys/upload/file'),
			standardUploadUrl : site_url('sys/upload/file'),
			xhrUploadUrl : site_url('sys/upload/file'),
			xhrFilePostName : 'file',
			flashUploadFilePostName : 'file',
			standardUploadFilePostName : 'file',
			flashSwfUploadFileTypes : "<?php isset($ext)?$ext:'*.*';?>",
			maxFileSizeBytes: 62914560, // 60 * 1024 * 1024
			flashSwfUploadFileTypesDescription : _s("<?php isset($desc)?$desc:'Todos los ficheros';?>"),
			awesomeUploaderRoot : slash_item() + '/assets/images/',
			listeners : {
				scope : this,
				fileupload : function(uploader, success, result) {
					if(success) {
						var c = Ext.getCmp(form_id + '_files');
						var v = c.getValue();
						console.log(v);
						if(v != null && v != '')
							v += ';' + result.message;
						else
							v = result.message;
						console.log(v);
						c.setValue(v);
					}
				}
			}
		}];

		var url = site_url("<?php echo isset($url)?$url:'sys/import/file';?>");
		var form = Ext.app.formStandarForm({
			controls : controls,
			timeout : false,
			title : _s('Importar desde fichero'),
			icon : 'iconoImportarFicheroTab',
			url : url
		});

		form.show();
		return;
	} catch (e) {
		console.dir(e);
	}
	return;
})();
