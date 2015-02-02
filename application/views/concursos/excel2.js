(function() {

	try {
		var form_id = Ext.app.createId();

	    var bibliotecas = new Ext.form.ComboBox(Ext.app.combobox({
	        url : site_url('concursos/biblioteca/search'),
	        name : 'biblioteca',
	        anchor : '100%',
	        label : _s('nIdBiblioteca')
	    }));

	    var salas = new Ext.form.ComboBox(Ext.app.combobox({
	        url : site_url('concursos/sala/search'),
	        name : 'sala',
	        anchor : '100%',
	        label : _s('nIdSala')
	    }));

		var controls = [bibliotecas, salas, {
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
			flashSwfUploadFileTypes : '*.xls;*.csv;*.xlsx',
			flashSwfUploadFileTypesDescription : _s('Ficheros EXCEL'),
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

		var url = site_url('concursos/biblioteca/importar_excel');
		var form = Ext.app.formStandarForm({
			controls : controls,
			timeout : false,
			title : _s('Importar EXCEL'),
			icon : 'icon-excel',
			url : url
		});

		bibliotecas.store.load();
		salas.store.load();

		form.show();
		return;
	} catch (e) {
		console.dir(e);
	}
	return;
})();
