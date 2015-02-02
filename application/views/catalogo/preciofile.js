(function() {

	var form_id = Ext.app.createId();

	var url = site_url('catalogo/articulo/precios');
	var controls = [{
		xtype: 'hidden',
		id: form_id + '_files',
		value: '',
		name: 'file'
	},{
		xtype:'awesomeuploader',
		gridHeight:100,
		gridWidth: 440,
		height: 160,
		width: 460,
		supressPopups: true,
		//frame: true,
		flashSwfUploadPath: slash_item() + "/assets/js/ux/swfupload.swf",
		flashButtonSprite: slash_item() + "/assets/images/swfupload_browse_button_trans_56x22.png",
		flashUploadUrl: site_url('sys/upload/file'),
		standardUploadUrl: site_url('sys/upload/file'),
		xhrUploadUrl: site_url('sys/upload/file'),
		xhrFilePostName: 'file',
		flashUploadFilePostName: 'file',
		standardUploadFilePostName: 'file',
		flashSwfUploadFileTypes: '*.xls;*.xlsx;*.csv',
		flashSwfUploadFileTypesDescription: _s('Ficheros EXCEL'),
		awesomeUploaderRoot:slash_item() + '/assets/images/',
		maxFileSizeBytes: 15 * 1024 * 1024, // 15 M
		listeners: {
			scope:this,
			fileupload: function(uploader, success, result) {
				if(success) {
					var c = Ext.getCmp(form_id + '_files');
					var v = c.getValue();
					//console.log(v);
					if (v != null && v != '')
						v += ';' + result.message;
					else
						v=result.message;
					//console.log(v);
					c.setValue(v);
				}
			}
		}
	}];

	var form = Ext.app.formStandarForm({
		controls: controls,
		timeout: false,
		title: _s('Actualizar precios'),
		icon: 'icon-precio',
		url: url
	});

	form.show();
	return;

})();