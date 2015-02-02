Ext.ns('Ext.ux.form');

Ext.ux.form.ISBN = function(config){
    Ext.ux.form.ISBN.superclass.constructor.call(this, config);
    this.addEvents('itemselect');
};

/**
 * @private hide from doc gen
 */
Ext.ux.form.ISBN = Ext.extend(Ext.form.TextField, {
    enableKeyEvents: true,
    selectOnFocus: true,
    ean_id: null,
    isbn_id: null,
    isbn10_id: null,
    prv_id: null,
    edit_id: null,
    next_id: null,
    _form: null,
    cls: 'isbncontrol',
    /**
     * Inicializa componente
     */
    initComponent: function(){
        // call parent initComponent
        Ext.ux.form.ISBN.superclass.initComponent.call(this);
    },
    
    /**
     * Inicializa eventos
     */
    initEvents: function(){
        var el = this.el;
        var me = this;
        
        el.on({
            keypress: function(e){
                if (e.getKey() == 13) {
                    if (me.next_id != null) 
                        Ext.getCmp(me.next_id).focus();
                }
            },
            
            change: this.doQuery,
            
            scope: this
        });
    },
    /**
     * Ejecuta la búsqueda
     */
    doQuery: function(){
		var me = this;
        if (me.form != null) {
            me.form.setDirty();
        }
        Ext.app.callRemote({
            url: site_url('catalogo/articulo/isbn'),
            params: {
                code: me.getValue()
            },
            fnok: function(res){
                me.suspendEvents();
                me.setValue();
                me.resumeEvents();
                try {
                    if (me.prv_id != null) {
                        var o = Ext.getCmp(me.prv_id);
                        o.setValue(res.nIdProveedor);
                        o.focus();
                    }
                    if (me.edit_id != null) {
                        var o = Ext.getCmp(me.edit_id);
                        o.setValue(res.nIdEditorial);
                        o.focus();
                    }
                    if (me.isbn_id != null) {
                        var o = Ext.getCmp(me.isbn_id);
                        o.setValue(res.isbn13);
                        o.focus();
                    }
                    if (me.isbn10_id != null) {
                        var o = Ext.getCmp(me.isbn10_id);
                        o.setValue(res.isbn10);
                        o.focus();
                    }
                    if (me.ean_id != null) {
                        var o = Ext.getCmp(me.ean_id);
                        o.setValue(res.ean);
                        o.focus();
                    }
                    me.focus();
                    if (me.next_id != null) 
                        Ext.getCmp(me.next_id).focus();
                } 
                catch (e) {
                    console.dir(e);
                }
            }
        });
    },
    /**
     * Evento de KeyPress
     * @param {Object} e
     * @param {Object} t
     */
    onKeyDownHandler: function(e, t){
        if (e.getKey() === e.ENTER) {
            //this.processCommand();
        }
    }
}); // eo extend
// register xtype
Ext.reg('isbnfield', Ext.ux.form.ISBN);
