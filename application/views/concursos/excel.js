(function() {

	try {
		var form_id = Ext.app.createId();
		var prv = "<?php echo (isset($prv)&&$prv)?1:0?>";
		var sec = "<?php echo (isset($seccion)?(($seccion)?1:0):1)?>";
		var url = "<?php echo (isset($url))?$url:'concursos/importar/excel'?>";

		if(sec == '1') {
			var seccion = new Ext.form.ComboBox(Ext.app.combobox({
				url : site_url('generico/seccion/search'),
				anchor : "90%",
				allowBlank : true,
				name : 'seccion',
				label : _s('Seccion')
			}));
		}
		var controls = [new Ext.app.autocomplete2({
			allowBlank : true,
			url : site_url((prv == '1') ? 'proveedores/proveedor/search' : 'clientes/cliente/search'),
			fieldLabel : _s((prv == '1') ? 'Proveedor' : 'Cliente'),
			name : (prv == '1') ? 'proveedor' : 'cliente',
			anchor : '90%'
		}), {
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
		}, {
			xtype : 'textfield',
			allowBlank : true,
			fieldLabel : _s('Rango'),
			anchor : '40%',
			name : 'rango'
		}, {
			xtype : 'checkbox',
			allowBlank : true,
			checked : false,
			fieldLabel : _s('Crear documento'),
			anchor : '90%',
			name : 'crear'
		}, {
			xtype : 'checkbox',
			allowBlank : true,
			checked : false,
			fieldLabel : _s('Crear libros'),
			anchor : '90%',
			name : 'crear_libros'
		}];

		if(sec == '1')
			controls[controls.length] = seccion;

		controls[controls.length] = {
			xtype : 'textfield',
			allowBlank : true,
			fieldLabel : _s('Descuento'),
			anchor : '30%',
			name : 'dto'
		};

		controls[controls.length] = {
			xtype : 'textfield',
			allowBlank : true,
			fieldLabel : _s('Referencia'),
			anchor : '90%',
			name : 'ref'
		};

		var url = site_url(url);
		var form = Ext.app.formStandarForm({
			controls : controls,
			timeout : false,
			title : _s('Importar EXCEL'),
			icon : 'icon-excel',
			url : url
		});

		if (sec==1)seccion.store.load();

		form.show();
		return;
	} catch (e) {
		console.dir(e);
	}
	return;
})();
