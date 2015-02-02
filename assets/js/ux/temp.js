Ext.ns('Ext.ux.form');

Ext.ux.form.TextID = function(config){
    Ext.ux.form.TextID.superclass.constructor.call(this, config);
    this.addEvents('itemselect');
};

/**
 * @private hide from doc gen
 */
Ext.ux.form.TextID = Ext.extend(Ext.form.TextField, {
    cantidadField: null,
    descuentoField: null,
    importeField: null,
    referenciaField: null,
    seccionField: null,
    articuloField: null,
    infoField: null,
    url_search: null,
    url_load: null,
    field: null,
    fn_get_seccion: null,
    aplicar_revargo: false,
    _tip: null,
    _regex: [],
    /**
     * Inicializa componente
     */
    initComponent: function(){
        var me = this;
        me._regex = [{
            // Patrón cantidad
            pattern: /^([\+|\-|c|q])\s?(\-?\d+)/,
            fn: function(m, c){
                var v = (m[1] == '-') ? parseInt(-m[2]) : parseInt(m[2]);
                if (c.cantidadField != null) {
                    c.cantidadField.setValue(v);
                    c.subtotal();
                    return true;
                }
            }
        }, {
            // Patrón descuento
            pattern: /^[\*|d]\s?(\d+)/,
            fn: function(m, c){
                var v = parseInt(m[1]);
                if (c.descuentoField != null) {
                    if ((v >= 0) && (v <= 100)) {
                        c.descuentoField.setValue(v);
                        c.subtotal();
                    }
                    else 
                        c.info(_s('linea-descuento-error'));
                    return true;
                }
            }
        }, {
            // Patrón referencia
            pattern: /^ref\s?(*+)/,
            fn: function(m, c){
                var v = parseInt(m[1]);
                if (c.referenciaField != null) {
                    c.referenciaField.setValue(v);
                    return true;
                }
            }
        }, {
            // Patrón importe
            pattern: /^\/\s?\-?([\d\\.\,]+)/,
            fn: function(m, c){
                var v = parseFloat(m[1]);
                if (c.importeField != null) {
                    if (v >= 0) {
                        v = v.decimal(Ext.app.DECIMALS);
                        c.importeField.setValue(v);
                        c.subtotal();
                    }
                    else 
                        c.info(_s('linea-importe-error'));
                    return true;
                }
            }
        }];
        
        // call parent initComponent
        Ext.ux.form.DateTimeText.superclass.initComponent.call(this);
    },
    
    /**
     * Inicializa eventos
     */
    initEvents: function(){
        var el = this.el;
        
        el.on({
            keydown: this.onKeyDownHandler,
            focus: this.onFocus,
            scope: this
        });
        var me = this;
        var fn = function(){
            var id = me.seccionField.getValue();
            for (var i = 0; i < me.field.secciones.length; i++) {
                if (me.field.secciones[i].nIdSeccion == id) {
                    me.field['seccion'] = me.field.secciones[i];
                    break;
                }
            }
            me.fireEvent('itemselect', me, me.field);
            me.clear();
            me.setValue('');
            try {
                el.focus();
                me.focus();
            } 
            catch (e) {
                console.dir(e);
            }
            
        }
        if (this.seccionField != null) {
            this.seccionField.on('select', function(){
                fn();
            });
            this.seccionField.on('keypress', function(t, e){
                if (e.getKey() === e.ENTER) {
                    fn();
                }
            });
        }
    },
    /**
     * Añade un nuevo comando al control
     * @param {Object} pattern
     * @param {Object} fn
     */
    addPattern: function(pattern, fn){
        this._regex[this._regex.length] = {
            pattern: pattern,
            fn: fn
        };
    },
    /**
     * Focus
     */
    onFocus: function(){
        var v = this.getValue();
        this.selectText(0, v.length);
    },
    
    /**
     * Muestra texto de información
     * @param {Object} text
     */
    info: function(text){
        this._tip = new Ext.ToolTip({
            target: this.el,
            anchor: 'top',
            anchorOffset: 85, // center the anchor on the tooltip
            html: text
        });
        this._tip.show();
    },
    
    /**
     * Calcula el subtotal de la línea de venta
     */
    subtotal: function(){
        if (this.infoField != null) {
            var data = this.valores()
        }
        //console.dir(data);
        this.infoField.setText(Ext.app.currencyFormatter(data.unitario));
    },
    
    /**
     * Procesa los comandos de la casilla de texto
     */
    processCommand: function(){
        var me = this;
        var value = this.getValue();
        var stop = false;
        //console.log('Patrones:');
        //console.dir(me._regex);
        Ext.each(me._regex, function(p){
            try {
                if (!stop) {
                    //console.log(p.pattern);
                    var re = new RegExp(p.pattern);
                    var m = re.exec(value);
                    //console.dir(m);
                    if (m != null) 
                        if (p.fn(m, me)) {
                            stop = true;
                        }
                }
            } 
            catch (e) {
                console.dir(e);
            }
        });
        if (!stop) {
            me.load_articulo(value);
        }
        else {
            this.setValue('');
        }
    },
    
    /**
     * Calcula el importe a partir de un precio
     * @param {Object} precio
     */
    valores: function(pr, iva, recargo){
        var me = this;
        if (pr == null) 
            pr = (me.importeField.getValue());
        //pr = precio;//(precio != null) ? precio : ((pr != '') ? parseFloat(pr) : 0);
        var ct = (me.cantidadField.getValue());
        ct = (ct != '') ? parseInt(ct) : 1;
        var dt = (me.descuentoField.getValue());
        dt = (dt != '') ? parseFloat(dt) : 0;
        
        var totales = ProcesarImportes(ct, dt, pr, iva, recargo);
        var field = {
            'precio': pr,
            'cantidad': ct,
            'descuento': dt,
            'unitario': totales.unitario,
            'base': totales.base,
            'iva': totales.iva,
            'recargo': totales.recargo,
            'total': totales.total
        }
        
        return field;
    },
    
    /**
     * Limpia los campos
     * @param {Object} all TRUE: limpia también el descuento
     */
    clear: function(all){
        if (this.articuloField != null) 
            this.articuloField.setValue('');
        
        if (this.importeField != null) 
            this.importeField.setValue('');
        
        if (this.cantidadField != null) 
            this.cantidadField.setValue('');
        
        if (this.seccionField != null) {
            this.seccionField.getStore().removeAll();
            this.seccionField.setValue('');
        }
        
        if (all === true) {
            if (this.descuentoField != null) 
                this.descuentoField.setValue('');
        }
        
        if (this.infoField != null) 
            this.infoField.setText('');
        
        if (this.referenciaField != null) 
            this.referenciaField.setText('');
        
        this.field = null;
        this.setValue('');
    },
    
    /**
     * Carga los datos
     * @param {Object} data
     */
    load: function(data){
        var me = this;
        Ext.app.callRemote({
            url: this.url_load,
            params: {
                id: data.id,
                relation: 'secciones'
            },
            fnok: function(v){
                try {
                
                    v.value_data.secciones;
                    if (v.value_data.secciones.length == 0) {
                        me.info(_s('linea-seccion-error'));
                        me.focus();
                        return;
                    }
                    if (me.articuloField != null) {
                        me.articuloField.setValue(data.text);
                    }
                    var pr = (me.importeField.getValue());
                    if (pr == '') 
                        pr = v.value_data.fPVP;
                    var recargo = me.aplicar_recargo ? v.value_data.fRecargo : 0;
                    var valores = me.valores(pr, v.value_data.fIVA, recargo);
                    var coste = (valores.cantidad < 0) ? -v.value_data.fPrecioCompra : v.value_data.fPrecioCompra;
                    var referencia = (me.referenciaField != null) ? me.referenciaField.getValue() : null;
                    var field = {
                        'id': data.id,
                        'nCantidad': valores.cantidad,
                        'cTitulo': v.value_data.cTitulo,
                        'fDescuento': valores.descuento,
                        'fIVA': v.value_data.fIVA,
                        'fPVP': valores.precio,
                        'fImporte': valores.unitario,
                        'cReferencia': referencia,
                        'fIVAImporte': valores.iva,
                        'fBase': valores.base,
                        'fCoste': coste,
                        'fRecargo': recargo,
                        'fRecargoImporte': valores.recargo,
                        'fMargen': Margen(valores.base, valores.cantidad * v.value_data.fPrecioCompra),
                        'fTotal': valores.total
                    }
                    me.field = field;
                    if (me.fn_get_seccion != null) {
                        var sec = me.fn_get_seccion(v.value_data.secciones);
                        if (sec.select != null) {
                            field['seccion'] = sec.select;
                            field['secciones'] = sec.secciones;
                            me.fireEvent('itemselect', me, field);
                            me.clear();
                            //me.setValue('');
                            me.focus();
                            return;
                        }
                    }
                    var st = me.seccionField.getStore();
                    st.removeAll();
                    st.loadData({
                        total_data: sec.secciones.length,
                        value_data: sec.secciones
                    });
                    if (st.getTotalCount() > 0) {
                        me.seccionField.setValue(st.getAt(0).data.id);
                    }
                    field['secciones'] = sec.secciones;
                    me.field = field;
                    me.seccionField.focus();
                } 
                catch (e) {
                    console.dir(e);
                }
            }
        });
        
    },
    
    /**
     * Busca un artículo
     * @param {Object} title
     */
    load_articulo: function(title){
        var me = this;
        me.onFocus();
        if (me.url_search != null && title.trim() != '') {
            var store = Ext.app.getStore(me.url_search, ['id', 'text'], false, true);
            store.baseParams = {
                start: 0,
                limit: Ext.app.AUTOCOMPLETELISTSIZE
            }
            var fn = function(){
                if ((parseInt(store.getTotalCount()) > 1)) {
                    var listView = new Ext.list.ListView({
                        store: store,
                        columnSort: true,
                        singleSelect: true,
                        height: 250,
                        reserveScrollOffset: true,
                        columns: [{
                            header: _s('Id'),
                            width: .10,
                            dataIndex: 'id'
                        }, {
                            header: _s('cDescripcion'),
                            width: .90,
                            dataIndex: 'text'
                        }]
                    });
                    
                    var fn_ok = function(){
                        var v = listView.getSelectedIndexes();
                        v = v[0];
                        v = store.getAt(v);
                        
                        me.load(v.data);
                    };
                    
                    var form = Ext.app.formStandarForm({
                        controls: [listView],
                        fn_ok: fn_ok
                    });
                    
                    listView.on('dblclick', function(view, index){
                        var v = store.getAt(index);
                        form.close();
                        me.load(v.data);
                    });
                    form.show();
                }
                else {
                    if (store.getTotalCount() > 0) {
                        var v = store.getAt(0);
                        me.load(v.data);
                    }
                    else {
                        me.info(_s('linea-articulo-no-encontrado'));
                    }
                }
            }
            store.load({
                params: {
                    query: title,
                    start: 0,
                    limit: Ext.app.AUTOCOMPLETELISTSIZE
                },
                callback: fn
            });
        }
    },
    /**
     * Evento de KeyPress
     * @param {Object} e
     * @param {Object} t
     */
    onKeyDownHandler: function(e, t){
        if (e.getKey() === e.ENTER) {
            this.processCommand();
        }
    },
    
    /**
     * Aplicar recargo de equivalencia a las líneas
     * @param {Object} aplicar
     */
    aplicarRecargo: function(aplicar){
        this.applicar_recargo = aplicar;
    }
}); // eo extend
// register xtype
Ext.reg('xtextid', Ext.ux.form.TextID);
