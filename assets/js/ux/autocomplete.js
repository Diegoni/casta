Ext.namespace('Ext.ux.form');
Ext.ux.form.Autocomplete = function(config){
    Ext.ux.form.Autocomplete.superclass.constructor.call(this, config);
};

/**
 * @private hide from doc gen
 */
Ext.ux.form.Autocomplete = Ext.extend(Ext.ux.form.Autocomplete, Ext.form.ComboBox, {

    /**
     * @cfg {String} url URL del store
     */
    url: null,
    fnselect: null,
    label: null,
    loading: false,
    value_id: null,
	dirty: false,
    
    initComponent: function(){
    
        var store = Ext.app.getStore(this.url, ['id', 'text'], false, true);
        
        var my_select = function(f, id, text){
            try {
				console.log('select ' + id + ' ' + text);
				f.setValue(id);
				f.dirty = true;
				f.fireEvent('change');
                /*f.value = text;
                f.value_id = id;*/
                f.selectText(0, 1000);
                if (this.fnselect) {
                    this.fnselect(id);
                }
            } 
            catch (e) {
                console.dir(e);
            }
        }
        
        Ext.apply(this, {
            //mode: 'remote',
            fieldLabel: this.label,
            displayField: 'text',
            valueField: 'id',
            cls: 'searchcontrol',
            forceSelection: true,
            loadingText: _s('cargando'),
            emptyText: _s('seleccione'),
            typeAhead: true,
            hideTrigger: true,
            triggerAction: 'all',
            selectOnFocus: true,
            enableKeyEvents: true,
            minChars: 2,
            listClass: 'x-combo-list-small',
            store: store,
            listeners: {
                focus: function(f){
                    f.selectText(0, 1000);
                },
                keypress: function(f, e){
                    if (e.getKey() == 13) {
                        var q = f.getRawValue();
                        try {
                            f.doQuery(q, true);
                        } 
                        catch (e) {
                        }
                    }
                },
                select: function(f, r, i){
                    my_select(f, new String(r.data.id), r.data.text);
                }
            }
        
        });
        
        var combo = this;
        store.on('load', function(r, o){
            console.log('store load');
            var fn = function(combo, data){
                combo.suspendEvents(false);
                /*this.loading = true;
                combo.value = data.text;
                combo.value_id = data.id;
                this.loading = false;*/
				//Ext.ux.form.Autocomplete.superclass.setValue.call(this, data.id);
				//console.dir(combo);
				//combo.setDirty(true);
                my_select(combo, parseInt(data.id), data.text);
                store.removeAll();
                combo.resumeEvents();
                combo.collapse();
            };
            try {
                if (parseInt(store.getTotalCount()) == 1) {
                    fn(combo, store.getAt(0).data);
                }
            } 
            catch (e) {
                console.dir(e);
            }
        });
        
        Ext.ux.form.Autocomplete.superclass.initComponent.call(this);
    },
    
    initEvents: function(){
        console.log('Autocomplete - initEvents');
        Ext.ux.form.Autocomplete.superclass.initEvents.call(this);
    },
    
    getValue: function(){
        return this.value_id;
    }/*,
    
    setValue: function(value){
        console.log('Autocomplete - setValue ' + value);
        /*if (!this.rendered) {
            this.value = value;
            return;
        }
        console.log('Autocomplete - setValue rendered ' + value);
        this.doQuery(value, true);*//*
        Ext.ux.form.Autocomplete.superclass.setValue.call(this, value);
    }*/
    
});


Ext.reg('autocomplete', Ext.ux.form.Autocomplete);



Ext.ux.form.Autocomplete2 = function(config){
    Ext.ux.form.Autocomplete2.superclass.constructor.call(this, config);
};

/**
 * @private hide from doc gen
 */
Ext.ux.form.Autocomplete2 = Ext.extend(Ext.ux.form.Autocomplete2, Ext.form.ComboBox, {

    /**
     * @cfg {String} url URL del store
     */
    url: null,
    fnselect: null,
    label: null,
    loading: false,
    
    initComponent: function(){
    
        var store = Ext.app.getStore(this.url, ['id', 'text'], false, true);
        
        Ext.apply(this, {
            mode: 'remote',
            fieldLabel: this.label,
            displayField: 'text',
            valueField: 'id',
            cls: 'searchcontrol',
            forceSelection: true,
            loadingText: _s('cargando'),
            emptyText: _s('seleccione'),
            typeAhead: true,
            hideTrigger: true,
            triggerAction: 'all',
            selectOnFocus: true,
            minChars: 100,
            listClass: 'x-combo-list-small',
            store: store
        });
        this.store.on('load', this.onLoadStore, this);
        
        Ext.ux.form.Autocomplete2.superclass.initComponent.call(this);
    },
    
    initEvents: function(){
        console.log('Autocomplete - initEvents');
        
        Ext.ux.form.Autocomplete2.superclass.initEvents.call(this);
        var el = this.el;
        
        el.on({
            keypress: this.onKeyPress,
            scope: this
        });
        
        this.on({
            select: this.onSelect,
            focus: this.onFocus,
            //keypress: this.onKeyPress,
            scope: this
        });
    },
    
    onLoadStore: function(store, r, o){
        console.log('Autocomplete - onLoadStore');
        //console.dir(this);
        console.log(store.getTotalCount());
        if (parseInt(store.getTotalCount()) == 1) {
            this.suspendEvents(false);
            var data = store.getAt(0).data;
            this.setValue(data.text);
            this.selectText(0, 1000);
            if (this.fnselect) {
                this.fnselect(parseInt(data.id));
            }
            this.store.removeAll();
            this.resumeEvents();
            this.collapse();
            this.loading = false;
        }
        /*else {
         console.log('Multi');
         this.expand();
         }*/
        if (this.store.getCount() === 0 && this.isExpanded()) {
            this.collapse();
        }
    },
    
    onKeyPress: function(e){
        console.log('loading ' + this.loading);
        if (e.getKey() == 13 /*&& !this.loading*/) {
            console.log('enter');
            var q = this.getRawValue();
            try {
                this.store.removeAll();
                this.loading = true;
                this.doQuery(q, true);
            } 
            catch (e) {
                console.dir(e);
            }
        }
    },
    
    onFocus: function(){
        console.log('Autocomplete - onFocus');
        this.selectText(0, 1000);
        Ext.ux.form.SuperBoxSelect.superclass.onFocus.call(this);
    },
    onSelect: function(record, index){
        //this.suspendEvents(false);
        //this.value = record.id;
        console.log('Select ' + record.id);
        if (this.fnselect) {
            this.fnselect(parseInt(record.id));
        }
        this.collapse();
        this.loading = false;
        //this.resumeEvents();
        //return false;
    }
    /*,
     setValue: function(value){
     if (!this.rendered) {
     this.value = value;
     return;
     }
     Ext.ux.form.SuperBoxSelect.superclass.setValue.call(this);
     }*/
});


Ext.reg('autocomplete2', Ext.ux.form.Autocomplete2);
