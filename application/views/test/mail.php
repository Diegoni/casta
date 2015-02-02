<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Ext.ux.TinyMCE</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" xmlns="">
	
	<link rel="stylesheet" type="text/css" href="lib/ext/resources/css/ext-all.css"></link>
		
<?php echo js_asset('ext-base.js');?>
<?php echo js_asset('ext-all.js');?>
<?php echo js_asset('ux/miframe-min.js');?>
<?php echo js_asset('tiny_mce/tiny_mce.js');?>
<?php echo js_asset('Ext.ux.TinyMCE.js');?>
<?php echo js_asset('locale/ext-lang-es.js');?>
	
	<script type="text/javascript">
	
	// You have to init TinyMCE manually if you plan to render Ext.ux.TinyMCE in the Ext.onReady handler.
	Ext.ux.TinyMCE.initTinyMCE();
	
	Ext.onReady( function() {
mydiv = Ext.DomHelper.insertFirst(document.body, {
			id : 'form'
		}, true);

	
		var frm = new Ext.form.FormPanel({	
			title: "Form with TinyMCE editor",
			applyTo: "form",
			autoHeight: true,
			width: 750,
			frame: true,
			buttons: [
			{
				text: "Submit",
				handler: function() {
					// Sync value
					tinyMCE.triggerSave();
					// Submit the form
					frm.getForm().submit({
						url: "/",
						method: "GET"
					});
				}
			},
			{
				text: "Submit 2",
				handler: function() {
					// Sync value for specific editor
					Ext.getCmp( "richText" ).syncValue();
					// Submit the form
					frm.getForm().submit({
						url: "/",
						method: "GET"
					});
				}
			}
			],
			items: [
			{
				xtype: "tinymce",
				fieldLabel: "Rich text",
				id: "richText",
				name: "richText",
				width: 600,
				height: 400,
				tinymceSettings: {
					theme : "advanced",
					plugins: "safari,pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
					theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
					theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
					theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|",
					theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : false,
					extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
					template_external_list_url : "example_template_list.js"
				},
				value: "<h1>Demo</h1><p>Ext.ux.TinyMCE works...</p>"
			}					
			]
		});
	});
	</script>
	
</head>
<body>
</body>
</html>