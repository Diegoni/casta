// vim: sw=4:ts=4:nu:nospell:fdc=4
/**
 * An Application
 *
 * @author    Ing. Jozef Sakáloš
 * @copyright (c) 2008, by Ing. Jozef Sakáloš
 * @date      2. April 2008
 * @version   $Id: filetree.js 113 2009-02-02 02:27:23Z jozo $
 *
 * @license application.js is licensed under the terms of the Open Source
 * LGPL 3.0 license. Commercial use is permitted to the extent that the 
 * code/component(s) do NOT become part of another Open Source or Commercially
 * licensed development library or toolkit without explicit permission.
 * 
 * License details: http://www.gnu.org/licenses/lgpl.html
 */
 
/*global Ext, WebPage, window */

Ext.BLANK_IMAGE_URL = '/extjs/resources/images/default/s.gif';

Ext.onReady(function() {
    Ext.QuickTips.init();
    Ext.form.Field.prototype.msgTarget = 'side';

	var treepanel = new Ext.ux.FileTreePanel({
		 height:600,
		width: 400
		,id:'ftp'
		,title:'FileTreePanel'
		,renderTo:Ext.getBody()
		,rootPath:'root'
		,topMenu:true
		,autoScroll:true
		,enableProgress:false,
		url: "/fileserver/doUpload"
//		,baseParams:{additional:'haha'}
//		,singleUpload:true
	});

});

// eof
