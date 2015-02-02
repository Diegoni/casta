Ext.ns('Ext.ux.form');

/**
 * Creates new DateTime
 * 
 * @constructor
 * @param {Object}
 *            config A config object
 */
Ext.ux.form.DateTimeText = Ext.extend(Ext.form.TextField, {
	initComponent : function() {
		// call parent initComponent
		Ext.ux.form.DateTimeText.superclass.initComponent.call(this);

	} // eo function initComponent
	}); // eo extend
// register xtype
Ext.reg('xdatetimetext', Ext.ux.form.DateTimeText);
