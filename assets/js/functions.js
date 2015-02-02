/**
 * Función trim, limpieza de carácteres al final y al principio de un string
 */
String.prototype.trim = function() {
	return this.replace(/^[\s\n\r]+|[\s\n\r]+$/g, '')
}
/**
 * Comprueba si un elemento es null
 *
 * @param {}
 *            input
 * @param {}
 *            value
 * @return {}
 */
function is_null(input, value) {
	return (input === null) ? value : input;
}

/**
 * Trim
 * http://es.kioskea.net/faq/2540-javascript-la-funcion-trim
 * @param {Object} myString
 */
function trim(myString) {
	return myString.replace(/^\s+/g, '').replace(/\s+$/g, '')
}

/**
 * http://phpjs.org/functions/is_string:453
 * @param {Object} mixed_var
 */
function is_string(mixed_var) {
	// Returns true if variable is a Unicode or binary string
	//
	// version: 1103.1210
	// discuss at: http://phpjs.org/functions/is_string
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// *     example 1: is_string('23');
	// *     returns 1: true
	// *     example 2: is_string(23.5);
	// *     returns 2: false
	return ( typeof (mixed_var) == 'string');
}

/**
 * Determina si es un entero
 * http://www.andrewpeace.com/javascript-is-int.html
 * @param {Object} input
 */
function is_int(input) {
	return typeof (input) == 'number' && parseInt(input) == input;
}

/**
 * Determina si es un número
 * http://youropensource.com/projects/110-IsNumeric-and-IsNan-function-in-JS
 * @param {Object} val
 */
function is_numeric(val) {

	if(isNaN(parseFloat(val))) {

		return false;

	}
	return true
}

/**
 * Determina si es un array
 * http://phpjs.org/functions/is_array:437
 *
 * @param {}
 *            input
 * @return {}
 */
function is_array(mixed_var) {
	// Returns true if variable is an array
	//
	// version: 909.322
	// discuss at: http://phpjs.org/functions/is_array
	// + original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// + improved by: Legaev Andrey
	// + bugfixed by: Cord
	// + bugfixed by: Manish
	// + improved by: Onno Marsman
	// + improved by: Brett Zamir (http://brett-zamir.me)
	// + bugfixed by: Brett Zamir (http://brett-zamir.me)
	// % note 1: In php.js, javascript objects are like php associative arrays,
	// thus JavaScript objects will also
	// % note 1: return true in this function (except for objects which inherit
	// properties, being thus used as objects),
	// % note 1: unless you do ini_set('phpjs.objectsAsArrays', true), in which
	// case only genuine JavaScript arrays
	// % note 1: will return true
	// * example 1: is_array(['Kevin', 'van', 'Zonneveld']);
	// * returns 1: true
	// * example 2: is_array('Kevin van Zonneveld');
	// * returns 2: false
	// * example 3: is_array({0: 'Kevin', 1: 'van', 2: 'Zonneveld'});
	// * returns 3: true
	// * example 4: is_array(function tmp_a(){this.name = 'Kevin'});
	// * returns 4: false
	var key = '';
	var getFuncName = function(fn) {
		var name = (/\W*function\s+([\w\$]+)\s*\(/).exec(fn);
		if(!name) {
			return '(Anonymous)';
		}
		return name[1];
	};
	if(!mixed_var) {
		return false;
	}

	// BEGIN REDUNDANT
	this.php_js = this.php_js || {};
	this.php_js.ini = this.php_js.ini || {};
	// END REDUNDANT

	if( typeof mixed_var === 'object') {

		if(this.php_js.ini['phpjs.objectsAsArrays'] && // Strict checking for
		// being a JavaScript
		// array (only check
		// this way if call
		// ini_set('phpjs.objectsAsArrays',
		// 0) to disallow
		// objects as arrays)
		((this.php_js.ini['phpjs.objectsAsArrays'].local_value.toLowerCase && this.php_js.ini['phpjs.objectsAsArrays'].local_value.toLowerCase() === 'off') || parseInt(this.php_js.ini['phpjs.objectsAsArrays'].local_value, 10) === 0)) {
			return mixed_var.hasOwnProperty('length') && // Not
			// non-enumerable
			// because of being
			// on parent class
			!mixed_var.propertyIsEnumerable('length') && // Since is
			// own
			// property,
			// if not
			// enumerable,
			// it must
			// be a
			// built-in
			// function
			getFuncName(mixed_var.constructor) !== 'String';
			// exclude
			// String()
		}

		if(mixed_var.hasOwnProperty) {
			for(key in mixed_var) {
				// Checks whether the object has the specified property
				// if not, we figure it's not an object in the sense of a
				// php-associative-array.
				if(false === mixed_var.hasOwnProperty(key)) {
					return false;
				}
			}
		}

		// Read discussion at:
		// http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_is_array/
		return true;
	}

	return false;
}

/**
 * Convierte un timespam a un valor que se puede usar como date
 *
 * @param {Object}
 *            v
 */
function NumberToDate(v) {
	return parseInt(v) * 1000;
}

/**
 * Convierte un timespam a un valor que se puede usar como date
 *
 * @param {Object}
 *            v
 */
function DateToNumber(v) {
	return parseInt(v) / 1000;
}

function str_to_float(v) {
	v = new String(v);
	return parseFloat(v.replace(',', '.'));
}

function implode(glue, pieces) {
	// Joins array elements placing glue string between items and return one string
	//
	// version: 911.718
	// discuss at: http://phpjs.org/functions/implode
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Waldo Malqui Silva
	// +   improved by: Itsacon (http://www.itsacon.net/)
	// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	// *     example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
	// *     returns 1: 'Kevin van Zonneveld'
	// *     example 2: implode(' ', {first:'Kevin', last: 'van Zonneveld'});
	// *     returns 2: 'Kevin van Zonneveld'
	var i = '', retVal = '', tGlue = '';
	if(arguments.length === 1) {
		pieces = glue;
		glue = '';
	}
	if( typeof (pieces) === 'object') {
		if( pieces instanceof Array) {
			return pieces.join(glue);
		} else {
			for(i in pieces) {
				retVal += tGlue + pieces[i];
				tGlue = glue;
			}
			return retVal;
		}
	} else {
		return pieces;
	}
}

Number.prototype.decimal = function(num) {
	/*var v = parseFloat(this + 0.00001).toFixed(num);
	var v2 = parseFloat(v).toFixed(num);
	console.log('valor: ' + this + ' decimales: ' + num);
	console.log('pre:   ' + v);
	console.log('final: ' + v2)*/
	return parseFloat(parseFloat(this + 0.00001).toFixed(num));
}

var Ext = Ext || {}
if (Ext.ns) {
	/**
	 * @author schiesser
	 */
	Ext.ns('Extensive.grid');

	Extensive.grid.ItemDeleter = Ext.extend(Ext.grid.RowSelectionModel, {

		width : 20,

		sortable : false,
		dataIndex : 0, // this is needed, otherwise there will be an error
		menuDisabled : true,
		fixed : true,
		header : 'x',
		//id: 'deleter',
		_enabled : true,

		initEvents : function() {
			Extensive.grid.ItemDeleter.superclass.initEvents.call(this);
			var f = this;
			this.grid.on('cellclick', function(grid, rowIndex, columnIndex, e) {
				if(f._enabled && (columnIndex == grid.getColumnModel().getIndexById(f.id))) {
					try {
						var record = grid.getStore().getAt(rowIndex);
						grid.getStore().remove(record);
					} catch (e) {
						console.dir(e);
					}
				}
			});
		},
		enable : function() {
			this._enabled = true;
		},
		disable : function() {
			this._enabled = false;
		},
		renderer : function(v, p, record, rowIndex) {
			return '<span class="icon-delete-column" style="margin-left: -15px; align:left; width: 32; height: 16px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
		}
	});

	/**
	 * Advanced Data Validation Using ExtJs
	 * http://blog.adampresley.com/2008/advanced-data-validation-using-extjs/
	 * @param {Object} value
	 * @param {Object} field
	 */
	Ext.apply(Ext.form.VTypes, {
		password : function(value, field) {
			if(field.initialPasswordField) {
				var pwd = Ext.getCmp(field.initialPasswordField);
				this.passwordText = 'password_confirm_error';
				return (value == pwd.getValue());
			}

			return true;
			//this.passwordText = 'Passwords must be at least 5 characters, containing either a number, or a valid special character (!@#$%^&*()-_=+)';

			//var hasSpecial = value.match(/[0-9!@#\$%\^&\*\(\)\-_=\+]+/i);
			//var hasLength = (value.length >= 5);

			//return (hasSpecial && hasLength);
		},
		passwordText : 'Passwords must be at least 5 characters, containing either a number, or a valid special character (!@#$%^&*()-_=+)'
	});

	//http://blog.edseek.com/archives/2009/04/19/illuminate-required-fields-via-extjs/
	Ext.apply(Ext.layout.FormLayout.prototype, {
		originalRenderItem : Ext.layout.FormLayout.prototype.renderItem,
		renderItem : function(c, position, target) {
			if(c && !c.rendered && c.isFormField && c.fieldLabel && c.allowBlank === false) {
				c.fieldLabel = c.fieldLabel + " <span class=\"req\">*</span>";
			}
			this.originalRenderItem.apply(this, arguments);
		}
	});
}
/**
 * Calcula bases e impuestos a partir de un precio de venta al público
 * @param {Object} ct
 * @param {Object} dt
 * @param {Object} pr
 * @param {Object} iva
 * @param {Object} rec
 */
function ProcesarImportes(ct, dt, pr, iva, rec) {
	ct = parseInt(ct);
	dt = parseFloat(dt);
	iva = parseFloat(iva);
	rec = parseFloat(rec);

	var unitario = pr * (1 - dt / 100);
	//console.log('---------------');
	//console.log('unitario: ' + unitario);
	unitario = unitario.decimal(Ext.app.DECIMALS);
	//console.log('unitario2: ' + unitario);

	var total = unitario * ct;
	//console.log('total: ' + total);
	total = total.decimal(Ext.app.DECIMALS);
	//console.log('total2: ' + total);
	var base = QuitarIVA(total, iva);
	//console.log('base: ' + base);
	var recargo = ValorRecargo(base, rec)
	//console.log('recargo: ' + recargo);
	var iva1 = total - base;
	var iva2 = CalcularIVA(base, iva);
	//console.log('iva que tiene que ser: ' + iva2);
	//console.log('iva: ' + iva1);

	iva1 = iva1.decimal(Ext.app.DECIMALS);
	//console.log('iva2: ' + iva1);
	total += recargo;
	//console.log('total rec: ' + total);
	var t2 = base + iva2;
	t2 = t2.decimal(Ext.app.DECIMALS)
	if (t2 != total)
	{
		//console.log('Descuadre: ' + base + ' + ' + iva2 + ' != ' + total);
		unitario -= 0.01;
		base = QuitarIVA(unitario, iva) * ct;
		iva1 = CalcularIVA(base, iva);
		total = base + iva1;
	}
	return {
		base : base,
		iva : iva1,
		recargo : recargo,
		unitario : unitario,
		total : total
	};
}

/**
 * Calcula el valor del IVA de un precio
 * @param {Object} pr
 * @param {Object} iva
 */
function CalcularIVA(pr, iva) {
	var v = parseFloat(pr) * (parseFloat(iva) / 100);
	return parseFloat(v.decimal(Ext.app.DECIMALS));
}

/**
 * Aplica el IVA a un precio
 * @param {Object} pr
 * @param {Object} iva
 */
function AplicarIVA(pr, iva) {
	var v = parseFloat(pr) * (1 + (parseFloat(iva) / 100));
	return parseFloat(v.decimal(Ext.app.DECIMALS));
}

/**
 * Calcula el valor del recargo de equivalencia
 * @param {Object} pr
 * @param {Object} rec
 */
function ValorRecargo(pr, rec) {
	var v = parseFloat(pr) * (parseFloat(rec) / 100);
	return parseFloat(v.decimal(Ext.app.DECIMALS));
}

/**
 * Quita el IVA a un PVP
 * @param {Object} pr
 * @param {Object} iva
 */
function QuitarIVA(pr, iva) {
	var v = parseFloat(pr) / (1 + (parseFloat(iva) / 100));
	return parseFloat(v.decimal(Ext.app.DECIMALS));
}

/**
 * Muestra texto de información
 * @param {Object} text
 */
function tooltip_error(cmp, title, text) {
	//console.dir(cmp.getEl());
	var t = new Ext.ToolTip({
		title : '<b>' + title + '</b>',
		id : 'content-anchor-tip',
		target : cmp.getEl(),
		anchor : 'top',
		html : '<div id="tooltip-error">' + text + '</div>',
		width : 200,
		autoHide : false,
		closable : true
	});
	t.show();
}

/**
 * Calcula el margen de un precio de venta
 * @param {Object} venta
 * @param {Object} coste
 */
function Margen(venta, coste) {
	venta = parseFloat(venta);
	coste = parseFloat(coste);
	//console.log(venta);
	//console.log(coste);
	if(venta < 0)
		venta = -venta;
	if(coste < 0)
		coste = -coste;
	//var m = venta - coste;
	var m = (venta == 0) ? 0 : (1 - (coste / venta));
	m = m.decimal(Ext.app.DECIMALS) * 100;
	//console.log('Margen ' + venta + ', ' + coste + ' -> ' + m);
	return m;
}

/**
 * Calcula el margen de un precio de venta
 * @param {Object} venta
 * @param {Object} coste
 */
function DescuentoMaximo(precio, coste, margen, acinco) {
	precio = parseFloat(precio);
	coste = parseFloat(coste);
	margen = parseFloat(margen);
	if(precio < 0)
		precio = -precio;
	if(coste < 0)
		coste = -coste;
	if(margen < 0)
		margen = -margen;
	margen = margen / 100.0;
	/*console.log('Precio: ' + precio);
	 console.log('Coste: ' + coste);
	 console.log('Margen: ' + margen);*/
	var f = ((1.0 - margen) * precio);
	var dto = (f == 0) ? 0 : (1 - (coste / f));
	dto = dto.decimal(Ext.app.DECIMALS) * 100;
	if(acinco === true) {
		var r = (dto % 5);
		dto -= r;
	}
	return dto;
}

function FormatNumeroFactura(numero, serie) {
	//return ceros(numero, Ext.app.NUM_CEROS_DOCUMENTOS) + '-' + serie;
	return numero + '-' + serie;
}

function ceros(v, num) {
	var numCeros = '000000000000';
	numCeros = numCeros.substring(0, num);
	return numCeros.substring(0, numCeros.length - v.length) + valor;
}

/**
 * http://phpjs.org/functions/in_array:432
 * @param {Object} needle
 * @param {Object} haystack
 * @param {Object} argStrict
 */
function in_array(needle, haystack, argStrict) {
	// http://kevin.vanzonneveld.net
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: vlado houba
	// +   input by: Billy
	// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	// *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
	// *     returns 1: true
	// *     example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
	// *     returns 2: false
	// *     example 3: in_array(1, ['1', '2', '3']);
	// *     returns 3: true
	// *     example 3: in_array(1, ['1', '2', '3'], false);
	// *     returns 3: true
	// *     example 4: in_array(1, ['1', '2', '3'], true);
	// *     returns 4: false

	var key = '', strict = !!argStrict;

	if(strict) {
		for(key in haystack) {
			if(haystack[key] === needle) {
				return true;
			}
		}
	} else {
		for(key in haystack) {
			if(haystack[key] == needle) {
				return true;
			}
		}
	}

	return false;
}

if (Ext.extend) {
	Ext.IframeWindow = Ext.extend(Ext.Window, {
		onRender : function() {
			this.bodyCfg = {
				tag : 'iframe',
				src : this.src,
				cls : this.bodyCls,
				style : {
					border : '0px none'
				}
			};
			Ext.IframeWindow.superclass.onRender.apply(this, arguments);
		}
	});
}
//http://www.mojavelinux.com/articles/javascript_hashes.html
function Hash() {
	this.length = 0;
	this.items = new Array();
	for(var i = 0; i < arguments.length; i += 2) {
		if( typeof (arguments[i + 1]) != 'undefined') {
			this.items[arguments[i]] = arguments[i + 1];
			this.length++;
		}
	}

	this.removeItem = function(in_key) {
		var tmp_previous;
		if( typeof (this.items[in_key]) != 'undefined') {
			this.length--;
			var tmp_previous = this.items[in_key];
			delete this.items[in_key];
		}

		return tmp_previous;
	}

	this.getItem = function(in_key) {
		return this.items[in_key];
	}

	this.setItem = function(in_key, in_value) {
		var tmp_previous;
		if( typeof (in_value) != 'undefined') {
			if( typeof (this.items[in_key]) == 'undefined') {
				this.length++;
			} else {
				tmp_previous = this.items[in_key];
			}

			this.items[in_key] = in_value;
		}

		return tmp_previous;
	}

	this.hasItem = function(in_key) {
		return typeof (this.items[in_key]) != 'undefined';
	}

	this.clear = function() {
		for(var i in this.items) {
			delete this.items[i];
		}

		this.length = 0;
	}
}

//http://phpjs.org/functions/printf:494
function printf() {
	// http://kevin.vanzonneveld.net
	// +   original by: Ash Searle (http://hexmen.com/blog/)
	// +   improved by: Michael White (http://getsprink.com)
	// +   improved by: Brett Zamir (http://brett-zamir.me)
	// -    depends on: sprintf
	// *     example 1: printf("%01.2f", 123.1);
	// *     returns 1: 6

	var body, elmt, d = this.window.document;
	var ret = '';

	var HTMLNS = 'http://www.w3.org/1999/xhtml';
	body = d.getElementsByTagNameNS ? (d.getElementsByTagNameNS(HTMLNS, 'body')[0] ? d.getElementsByTagNameNS(HTMLNS, 'body')[0] : d.documentElement.lastChild) : d.getElementsByTagName('body')[0];

	if(!body) {
		return false;
	}
	ret = this.sprintf.apply(this, arguments);
	elmt = d.createTextNode(ret);
	body.appendChild(elmt);

	return ret.length;
}

//http://phpjs.org/functions/sprintf:522
function sprintf() {
	// http://kevin.vanzonneveld.net
	// +   original by: Ash Searle (http://hexmen.com/blog/)
	// + namespaced by: Michael White (http://getsprink.com)
	// +    tweaked by: Jack
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +      input by: Paulo Freitas
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +      input by: Brett Zamir (http://brett-zamir.me)
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// *     example 1: sprintf("%01.2f", 123.1);
	// *     returns 1: 123.10
	// *     example 2: sprintf("[%10s]", 'monkey');
	// *     returns 2: '[    monkey]'
	// *     example 3: sprintf("[%'#10s]", 'monkey');
	// *     returns 3: '[####monkey]'

	var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g;
	var a = arguments, i = 0, format = a[i++];

	// pad()
	var pad = function(str, len, chr, leftJustify) {
		if(!chr) {
			chr = ' ';
		}
		var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
		return leftJustify ? str + padding : padding + str;
	};
	// justify()
	var justify = function(value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
		var diff = minWidth - value.length;
		if(diff > 0) {
			if(leftJustify || !zeroPad) {
				value = pad(value, minWidth, customPadChar, leftJustify);
			} else {
				value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
			}
		}
		return value;
	};
	// formatBaseX()
	var formatBaseX = function(value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
		// Note: casts negative numbers to positive ones
		var number = value >>> 0;
		prefix = prefix && number && {
		'2': '0b',
		'8': '0',
		'16': '0x'
		}[base] || '';
		value = prefix + pad(number.toString(base), precision || 0, '0', false);
		return justify(value, prefix, leftJustify, minWidth, zeroPad);
	};
	// formatString()
	var formatString = function(value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
		if(precision != null) {
			value = value.slice(0, precision);
		}
		return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
	};
	// doFormat()
	var doFormat = function(substring, valueIndex, flags, minWidth, _, precision, type) {
		var number;
		var prefix;
		var method;
		var textTransform;
		var value;

		if(substring == '%%') {
			return '%';
		}

		// parse flags
		var leftJustify = false, positivePrefix = '', zeroPad = false, prefixBaseX = false, customPadChar = ' ';
		var flagsl = flags.length;
		for(var j = 0; flags && j < flagsl; j++) {
			switch (flags.charAt(j)) {
				case ' ':
					positivePrefix = ' ';
					break;
				case '+':
					positivePrefix = '+';
					break;
				case '-':
					leftJustify = true;
					break;
				case "'":
					customPadChar = flags.charAt(j + 1);
					break;
				case '0':
					zeroPad = true;
					break;
				case '#':
					prefixBaseX = true;
					break;
			}
		}

		// parameters may be null, undefined, empty-string or real valued
		// we want to ignore null, undefined and empty-string values
		if(!minWidth) {
			minWidth = 0;
		} else if(minWidth == '*') {
			minWidth = +a[i++];
		} else if(minWidth.charAt(0) == '*') {
			minWidth = +a[minWidth.slice(1, -1)];
		} else {
			minWidth = +minWidth;
		}

		// Note: undocumented perl feature:
		if(minWidth < 0) {
			minWidth = -minWidth;
			leftJustify = true;
		}

		if(!isFinite(minWidth)) {
			throw new Error('sprintf: (minimum-)width must be finite');
		}

		if(!precision) {
			precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0 : undefined;
		} else if(precision == '*') {
			precision = +a[i++];
		} else if(precision.charAt(0) == '*') {
			precision = +a[precision.slice(1, -1)];
		} else {
			precision = +precision;
		}

		// grab value using valueIndex if required?
		value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

		switch (type) {
			case 's':
				return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
			case 'c':
				return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
			case 'b':
				return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
			case 'o':
				return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
			case 'x':
				return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
			case 'X':
				return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
			case 'u':
				return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
			case 'i':
			case 'd':
				number = parseInt(+value, 10);
				prefix = number < 0 ? '-' : positivePrefix;
				value = prefix + pad(String(Math.abs(number)), precision, '0', false);
				return justify(value, prefix, leftJustify, minWidth, zeroPad);
			case 'e':
			case 'E':
			case 'f':
			case 'F':
			case 'g':
			case 'G':
				number = +value;
				prefix = number < 0 ? '-' : positivePrefix;
				method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
				textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
				value = prefix + Math.abs(number)[method](precision);
				return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
			default:
				return substring;
		}
	};
	return format.replace(regex, doFormat);
}

//http://www.thegrubbsian.com/2009/01/25/useful-javascript-extensions/
this.ObjUtil = {};

// Methods to determine the type of an object.
ObjUtil.isObject = function(obj) {
	return Object.prototype.toString.call(obj) === "[object Object]";
};
ObjUtil.isString = function(obj) {
	return Object.prototype.toString.call(obj) === "[object String]";
};
ObjUtil.isNumber = function(obj) {
	return Object.prototype.toString.call(obj) === "[object Number]";
};
ObjUtil.isBoolean = function(obj) {
	return Object.prototype.toString.call(obj) === "[object Boolean]";
};
ObjUtil.isArray = function(obj) {
	return Object.prototype.toString.call(obj) === "[object Array]";
};
ObjUtil.isFunction = function(obj) {
	return Object.prototype.toString.call(obj) === "[object Function]";
};
ObjUtil.isDate = function(obj) {
	return Object.prototype.toString.call(obj) === "[object Date]";
};
ObjUtil.isRegExp = function(obj) {
	return Object.prototype.toString.call(obj) === "[object RegExp]";
};
// Allows for creating method overloads, beware of a slight overhead during method call.
// Also note that the only differentiator between functions is their numer of arguments.
// Thanks to John Resig for the inspiration for this method.
ObjUtil.addOverload = function(obj, name, func) {
	var old = obj[name];
	obj[name] = function() {
		if(func.length == arguments.length) {
			return func.apply(obj, arguments);
		} else if( typeof old == "function") {
			return old.apply(obj, arguments);
		}
	};
};
// A generic comparison operator, to maintain lexicographical order strings are cast
// to lower case.
ObjUtil.compareTo = function(a, b) {
	if(ObjUtil.isString(a)) {
		a = a.toLowerCase();
	}
	if(ObjUtil.isString(b)) {
		b = b.toLowerCase();
	}
	if(a < b) {
		return -1;
	}
	if(a > b) {
		return 1;
	}
	return 0;
};
// A generic memoizer function to help with heavy recursion.
// Ex: var fibonacci = ObjUtil.memo(function (recur, n) {
// return recur(n - 1) + recur(n - 2); }, {"0":0, "1":1});
ObjUtil.memo = function(func, seed) {
	var cache = seed || {};
	var shell = function(arg) {
		if(!( arg in cache)) {
			cache[arg] = func(shell, arg);
		}
		return cache[arg];
	};
	return shell;
};
// Shameless theft of the jQuery.extend method, but it's useful to have it here as well so as
// not to incur too many dependencies (i.e. things relying on jQuery and extensions.js)
ObjUtil.merge = function() {
	var target = arguments[0] || {}, i = 1, length = arguments.length, deep = false, options;

	if( typeof target === "boolean") {
		deep = target;
		target = arguments[1] || {};
		i = 2;
	}

	if( typeof target !== "object" && !jQuery.isFunction(target)) {
		target = {};
	}

	if(length == i) {
		target = this; --i;
	}

	for(; i < length; i++)
	if(( options = arguments[i]) != null)
		for(var name in options) {
			var src = target[name], copy = options[name];

			if(target === copy) {
				continue;
			}

			if(deep && copy && typeof copy === "object" && !copy.nodeType) {
				target[name] = jQuery.extend(deep, src || (copy.length != null ? [] : {}), copy);
			} else if(copy !== undefined) {
				target[name] = copy;
			}
		}

	return target;
};
// Wraps a for loop around an array and passes each item in the loop to the
// supplied function and calls it.
if(!Array.forEach) {
	Array.prototype.forEach = function(func) {
		for(var i in this) {
			if(this.hasOwnProperty(i)) {
				func(this[i]);
			}
		}
	};
}

// Returns the object matching the criteria function.  Note that the 'func' parameter
// here must be a function that returns a boolean.
/*if (!Array.find) {
Array.prototype.find = function(func){
for (var i in this) {
if (func(this[i])) {
return this[i];
}
}
return null;
};
}*/
// Returns an array of objects matching the criteria function.  Note that the 'func'
// parameter here must be a function that returns a boolean.
if(!Array.findAll) {
	Array.prototype.findAll = function(func) {
		var items = [];
		this.forEach(function(i) {
			if(!ObjUtil.isFunction(i)) {
				if(func(i)) {
					items.push(i);
				}
			}
		});
		return items;
	};
}

// http://www.thegrubbsian.com/2009/01/28/custom-javascript-events-with-the-observer-pattern/
var BPObserver = function() {
	this.observations = [];
};
var BPObservation = function(name, func) {
	this.name = name;
	this.func = func;
};

BPObserver.prototype = {
	observe : function(name, func) {
		var exists = this.observations.findAll(function(i) {
			return i.name == name && i.func == func;
		}).length > 0;
		if(!exists) {
			this.observations.push(new BPObservation(name, func));
		}
	},
	unobserve : function(name, func) {
		this.observations.remove(function(i) {
			return i.name == name && i.func == func;
		});
	},
	fire : function(name, data, scope) {
		//console.log('fire');
		var funcs = this.observations.findAll(function(i) {
			return i.name == name;
		});
		//console.dir(funcs);
		funcs.forEach(function(i) {
			//console.dir(i);
			i.func.call(scope || window, data);
		});
	}
};

if (Ext.override) {
	//http://www.sencha.com/forum/showthread.php?55690-Tooltip-to-quot-capture-quot-the-target-element-which-triggered-it-s-showing.&p=265259#post265259
	Ext.override(Ext.ToolTip, {
		onTargetOver : function(e) {
			if(this.disabled || e.within(this.target.dom, true)) {
				return;
			}
			var t = e.getTarget(this.delegate);
			if(t) {
				this.triggerElement = t;
				this.clearTimer('hide');
				this.targetXY = e.getXY();
				this.delayShow();
			}
		},
		onMouseMove : function(e) {
			var t = e.getTarget(this.delegate);
			if(t) {
				this.targetXY = e.getXY();
				if(t === this.triggerElement) {
					if(!this.hidden && this.trackMouse) {
						this.setPagePosition(this.getTargetXY());
					}
				} else {
					this.hide();
					this.lastActive = new Date(0);
					this.onTargetOver(e);
				}
			} else if(!this.closable && this.isVisible()) {
				this.hide();
			}
		},
		hide : function() {
			this.clearTimer('dismiss');
			this.lastActive = new Date();
			delete this.triggerElement;
			Ext.ToolTip.superclass.hide.call(this);
		}
	});
}
if(!window['console']) {
	// Enable console
	if(window['loadFirebugConsole']) {
		window.loadFirebugConsole();
	} else {
		// No console, use Firebug Lite
		var firebugLite = function(F, i, r, e, b, u, g, L, I, T, E) {
			if(F.getElementById(b))
				return;
			E = F[i + 'NS'] && F.documentElement.namespaceURI;
			E = E ? F[i+'NS'](E, 'script') : F[i]('script');
			E[r]('id', b);
			E[r]('src', I + g + T);
			E[r](b, u);
			(F[e]('head')[0] || F[e]('body')[0]).appendChild(E);
			E = new Image;
			E[r]('src', I + L);
		};
		firebugLite(document, 'createElement', 'setAttribute', 'getElementsByTagName', 'FirebugLite', '4', 'firebug-lite.js', 'releases/lite/latest/skin/xp/sprite.png', 'https://getfirebug.com/', '#startOpened');
	}
} else {
	// console is already available, no action needed.
}

if(!window.console)
	console = {};
console.log = console.log ||
function() {
};

console.warn = console.warn ||
function() {
};

console.error = console.error ||
function() {
};

console.info = console.info ||
function() {
};

console.dir = console.dir ||
function() {
	console.log('NOP dir');
};

/**
 * Selección del precio según la tarifa.
 * @param {Object} pvp
 * @param {Object} iva
 * @param {Object} tipo
 * @param {Object} articulo_tarifas
 * @param {Object} tarifas
 * @param {Object} tarifas_general
 */
function getTarifa(pvp, iva, tipo, articulo_tarifas, tarifas, tarifas_general) {
	/*console.log('Calculando tarifas');
	console.dir({
	'PVP': pvp,
	'IVA': iva,
	'tipo': tipo,
	'articulo_tarifas': articulo_tarifas,
	'tarifas': tarifas,
	'tarifas_general': tarifas_general
	});*/
	// Elige tarifa según tipo de libro
	var tarifa = null;
	Ext.each(tarifas, function(item) {
		if(item.nIdTipoLibro == tipo) {
			tarifa = item.nIdTipoTarifa;
			return false;
		}
	});
	// Tarifa general?
	if(tarifa == null)
		tarifa = tarifas_general;

	// No hay tarifa
	if(tarifa == null)
		return pvp;
	var precio = null;
	Ext.each(articulo_tarifas, function(item) {
		if(item.nIdTipoTarifa == tarifa) {
			precio = item.fPrecio;
			return false;
		}
	});
	if(precio == null)
		return pvp;
	return AplicarIVA(precio, iva);
}

/**
 *
 * @param {Object} texto
 * @param {Object} url
 * @param {Object} style
 */
function format_enlace_cmd(texto, url, style) {
	if(style == null)
		style = 'cmd-link';
	return "<span class='" + style + "'><a href=\"javascript:parent.Ext.app.execCmd({url: '" + url + "'});\">" + texto + "</a></span>";
}

// https://github.com/kvz/phpjs/raw/master/functions/url/urldecode.js
function urldecode(str) {
	// http://kevin.vanzonneveld.net
	// +   original by: Philip Peterson
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +      input by: AJ
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Brett Zamir (http://brett-zamir.me)
	// +      input by: travc
	// +      input by: Brett Zamir (http://brett-zamir.me)
	// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Lars Fischer
	// +      input by: Ratheous
	// +   improved by: Orlando
	// +      reimplemented by: Brett Zamir (http://brett-zamir.me)
	// +      bugfixed by: Rob
	// +      input by: e-mike
	// +   improved by: Brett Zamir (http://brett-zamir.me)
	// %        note 1: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
	// %        note 2: Please be aware that this function expects to decode from UTF-8 encoded strings, as found on
	// %        note 2: pages served as UTF-8
	// *     example 1: urldecode('Kevin+van+Zonneveld%21');
	// *     returns 1: 'Kevin van Zonneveld!'
	// *     example 2: urldecode('http%3A%2F%2Fkevin.vanzonneveld.net%2F');
	// *     returns 2: 'http://kevin.vanzonneveld.net/'
	// *     example 3: urldecode('http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a');
	// *     returns 3: 'http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'
	return decodeURIComponent((str + '').replace(/\+/g, '%20'));
}

// http://phpjs.org/functions/parse_str:484
function parse_str(str, array) {
	// http://kevin.vanzonneveld.net
	// +   original by: Cagri Ekin
	// +   improved by: Michael White (http://getsprink.com)
	// +    tweaked by: Jack
	// +   bugfixed by: Onno Marsman
	// +   reimplemented by: stag019
	// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	// +   bugfixed by: stag019
	// -    depends on: urldecode
	// +   input by: Dreamer
	// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	// %        note 1: When no argument is specified, will put variables in global scope.
	// *     example 1: var arr = {};
	// *     example 1: parse_str('first=foo&second=bar', arr);
	// *     results 1: arr == { first: 'foo', second: 'bar' }
	// *     example 2: var arr = {};
	// *     example 2: parse_str('str_a=Jack+and+Jill+didn%27t+see+the+well.', arr);
	// *     results 2: arr == { str_a: "Jack and Jill didn't see the well." }
	var glue1 = '=', glue2 = '&', array2 = String(str).replace(/^&?([\s\S]*?)&?$/, '$1').split(glue2), i, j, chr, tmp, key, value, bracket, keys, evalStr, that = this, fixStr = function(str) {
		return that.urldecode(str).replace(/([\\"'])/g, '\\$1').replace(/\n/g, '\\n').replace(/\r/g, '\\r');
	};
	if(!array) {
		array = this.window;
	}

	for( i = 0; i < array2.length; i++) {
		tmp = array2[i].split(glue1);
		if(tmp.length < 2) {
			tmp = [tmp, ''];
		}
		key = fixStr(tmp[0]);
		value = fixStr(tmp[1]);
		while(key.charAt(0) === ' ') {
			key = key.substr(1);
		}
		if(key.indexOf('\0') !== -1) {
			key = key.substr(0, key.indexOf('\0'));
		}
		if(key && key.charAt(0) !== '[') {
			keys = [];
			bracket = 0;
			for( j = 0; j < key.length; j++) {
				if(key.charAt(j) === '[' && !bracket) {
					bracket = j + 1;
				} else if(key.charAt(j) === ']') {
					if(bracket) {
						if(!keys.length) {
							keys.push(key.substr(0, bracket - 1));
						}
						keys.push(key.substr(bracket, j - bracket));
						bracket = 0;
						if(key.charAt(j + 1) !== '[') {
							break;
						}
					}
				}
			}
			if(!keys.length) {
				keys = [key];
			}
			for( j = 0; j < keys[0].length; j++) {
				chr = keys[0].charAt(j);
				if(chr === ' ' || chr === '.' || chr === '[') {
					keys[0] = keys[0].substr(0, j) + '_' + keys[0].substr(j + 1);
				}
				if(chr === '[') {
					break;
				}
			}
			evalStr = 'array';
			for( j = 0; j < keys.length; j++) {
				key = keys[j];
				if((key !== '' && key !== ' ') || j === 0) {
					key = "'" + key + "'";
				} else {
					key = eval(evalStr + '.push([]);') - 1;
				}
				evalStr += '[' + key + ']';
				if(j !== keys.length - 1 && eval('typeof ' + evalStr) === 'undefined') {
					eval(evalStr + ' = [];');
				}
			}
			evalStr += " = '" + value + "';\n";
			eval(evalStr);
		}
	}
}

var renderInfo = function(val, x, r, row, col) {
	if(r != null) {
		if(x != null)
			x.css = 'icon-status-text-' + r.data.nIdInformacion;
		return (val != null) ? ('&nbsp;&nbsp;' + val) : '';
	}
	return val;
}
var renderInfoCliente = function(val, x, r, row, col) {
	if(r != null) {
		if(x != null)
			x.css = 'icon-status-text-' + r.data.nIdTipoInformacion;
		return (val != null) ? ('&nbsp;&nbsp;' + val) : '';
	}
	return val;
}

//http://www.tecnoretales.com/programacion/serializar-array-con-javascript/
function serialize(arr) {
	if(arr == null)
		return '';
	var res = 'a:' + arr.length + ':{';
	for( i = 0; i < arr.length; i++) {
		res += 'i:' + i + ';s:' + arr[i].length + ':"' + arr[i] + '";';
	}
	res += '}';

	return res;
}

//http://phpjs.org/functions/utf8_encode:577
function utf8_encode(argString) {
	// http://kevin.vanzonneveld.net
	// +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: sowberry
	// +    tweaked by: Jack
	// +   bugfixed by: Onno Marsman
	// +   improved by: Yves Sucaet
	// +   bugfixed by: Onno Marsman
	// +   bugfixed by: Ulrich
	// +   bugfixed by: Rafal Kukawski
	// *     example 1: utf8_encode('Kevin van Zonneveld');
	// *     returns 1: 'Kevin van Zonneveld'

	if(argString === null || typeof argString === "undefined") {
		return "";
	}

	var string = (argString + '');
	// .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
	var utftext = "", start, end, stringl = 0;
	start = end = 0;
	stringl = string.length;
	for(var n = 0; n < stringl; n++) {
		var c1 = string.charCodeAt(n);
		var enc = null;

		if(c1 < 128) {
			end++;
		} else if(c1 > 127 && c1 < 2048) {
			enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
		} else {
			enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
		}
		if(enc !== null) {
			if(end > start) {
				utftext += string.slice(start, end);
			}
			utftext += enc;
			start = end = n + 1;
		}
	}

	if(end > start) {
		utftext += string.slice(start, stringl);
	}

	return utftext;
}

//http://phpjs.org/functions/unserialize:571
function unserialize(data) {
	// http://kevin.vanzonneveld.net
	// +     original by: Arpad Ray (mailto:arpad@php.net)
	// +     improved by: Pedro Tainha (http://www.pedrotainha.com)
	// +     bugfixed by: dptr1988
	// +      revised by: d3x
	// +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +        input by: Brett Zamir (http://brett-zamir.me)
	// +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +     improved by: Chris
	// +     improved by: James
	// +        input by: Martin (http://www.erlenwiese.de/)
	// +     bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +     improved by: Le Torbi
	// +     input by: kilops
	// +     bugfixed by: Brett Zamir (http://brett-zamir.me)
	// -      depends on: utf8_decode
	// %            note: We feel the main purpose of this function should be to ease the transport of data between php & js
	// %            note: Aiming for PHP-compatibility, we have to translate objects to arrays
	// *       example 1: unserialize('a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}');
	// *       returns 1: ['Kevin', 'van', 'Zonneveld']
	// *       example 2: unserialize('a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}');
	// *       returns 2: {firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'}
	var that = this;
	var utf8Overhead = function(chr) {
		// http://phpjs.org/functions/unserialize:571#comment_95906
		var code = chr.charCodeAt(0);
		if(code < 0x0080) {
			return 0;
		}
		if(code < 0x0800) {
			return 1;
		}
		return 2;
	};
	var error = function(type, msg, filename, line) {
		throw new that.window[type](msg, filename, line);
	};
	var read_until = function(data, offset, stopchr) {
		var buf = [];
		var chr = data.slice(offset, offset + 1);
		var i = 2;
		while(chr != stopchr) {
			if((i + offset) > data.length) {
				error('Error', 'Invalid');
			}
			buf.push(chr);
			chr = data.slice(offset + (i - 1), offset + i);
			i += 1;
		}
		return [buf.length, buf.join('')];
	};
	var read_chrs = function(data, offset, length) {
		var buf;
		buf = [];
		for(var i = 0; i < length; i++) {
			var chr = data.slice(offset + (i - 1), offset + i);
			buf.push(chr);
			length -= utf8Overhead(chr);
		}
		return [buf.length, buf.join('')];
	};
	var _unserialize = function(data, offset) {
		var readdata;
		var readData;
		var chrs = 0;
		var ccount;
		var stringlength;
		var keyandchrs;
		var keys;

		if(!offset) {
			offset = 0;
		}
		var dtype = (data.slice(offset, offset + 1)).toLowerCase();

		var dataoffset = offset + 2;
		var typeconvert = function(x) {
			return x;
		};
		switch (dtype) {
			case 'i':
				typeconvert = function(x) {
					return parseInt(x, 10);
				};
				readData = read_until(data, dataoffset, ';');
				chrs = readData[0];
				readdata = readData[1];
				dataoffset += chrs + 1;
				break;
			case 'b':
				typeconvert = function(x) {
					return parseInt(x, 10) !== 0;
				};
				readData = read_until(data, dataoffset, ';');
				chrs = readData[0];
				readdata = readData[1];
				dataoffset += chrs + 1;
				break;
			case 'd':
				typeconvert = function(x) {
					return parseFloat(x);
				};
				readData = read_until(data, dataoffset, ';');
				chrs = readData[0];
				readdata = readData[1];
				dataoffset += chrs + 1;
				break;
			case 'n':
				readdata = null;
				break;
			case 's':
				ccount = read_until(data, dataoffset, ':');
				chrs = ccount[0];
				stringlength = ccount[1];
				dataoffset += chrs + 2;
				readData = read_chrs(data, dataoffset + 1, parseInt(stringlength, 10));
				chrs = readData[0];
				readdata = readData[1];
				dataoffset += chrs + 2;
				if(chrs != parseInt(stringlength, 10) && chrs != readdata.length) {
					error('SyntaxError', 'String length mismatch');
				}

				// Length was calculated on an utf-8 encoded string
				// so wait with decoding
				readdata = that.utf8_decode(readdata);
				break;
			case 'a':
				readdata = {};
				keyandchrs = read_until(data, dataoffset, ':');
				chrs = keyandchrs[0];
				keys = keyandchrs[1];
				dataoffset += chrs + 2;

				for(var i = 0; i < parseInt(keys, 10); i++) {
					var kprops = _unserialize(data, dataoffset);
					var kchrs = kprops[1];
					var key = kprops[2];
					dataoffset += kchrs;

					var vprops = _unserialize(data, dataoffset);
					var vchrs = vprops[1];
					var value = vprops[2];
					dataoffset += vchrs;

					readdata[key] = value;
				}
				dataoffset += 1;
				break;
			default:
				error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);
				break;
		}
		return [dtype, dataoffset - offset, typeconvert(readdata)];
	};
	return _unserialize((data + ''), 0)[2];
}

// http://phpjs.org/functions/serialize:508
function serialize(mixed_value) {
	// http://kevin.vanzonneveld.net
	// +   original by: Arpad Ray (mailto:arpad@php.net)
	// +   improved by: Dino
	// +   bugfixed by: Andrej Pavlovic
	// +   bugfixed by: Garagoth
	// +      input by: DtTvB (http://dt.in.th/2008-09-16.string-length-in-bytes.html)
	// +   bugfixed by: Russell Walker (http://www.nbill.co.uk/)
	// +   bugfixed by: Jamie Beck (http://www.terabit.ca/)
	// +      input by: Martin (http://www.erlenwiese.de/)
	// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
	// +   improved by: Le Torbi (http://www.letorbi.de/)
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
	// +   bugfixed by: Ben (http://benblume.co.uk/)
	// -    depends on: utf8_encode
	// %          note: We feel the main purpose of this function should be to ease the transport of data between php & js
	// %          note: Aiming for PHP-compatibility, we have to translate objects to arrays
	// *     example 1: serialize(['Kevin', 'van', 'Zonneveld']);
	// *     returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
	// *     example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
	// *     returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'
	var _utf8Size = function(str) {
		var size = 0, i = 0, l = str.length, code = '';
		for( i = 0; i < l; i++) {
			code = str.charCodeAt(i);
			if(code < 0x0080) {
				size += 1;
			} else if(code < 0x0800) {
				size += 2;
			} else {
				size += 3;
			}
		}
		return size;
	};
	var _getType = function(inp) {
		var type = typeof inp, match;
		var key;

		if(type === 'object' && !inp) {
			return 'null';
		}
		if(type === "object") {
			if(!inp.constructor) {
				return 'object';
			}
			var cons = inp.constructor.toString();
			match = cons.match(/(\w+)\(/);
			if(match) {
				cons = match[1].toLowerCase();
			}
			var types = ["boolean", "number", "string", "array"];
			for(key in types) {
				if(cons == types[key]) {
					type = types[key];
					break;
				}
			}
		}
		return type;
	};
	var type = _getType(mixed_value);
	var val, ktype = '';

	switch (type) {
		case "function":
			val = "";
			break;
		case "boolean":
			val = "b:" + ( mixed_value ? "1" : "0");
			break;
		case "number":
			val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
			break;
		case "string":
			val = "s:" + _utf8Size(mixed_value) + ":\"" + mixed_value + "\"";
			break;
		case "array":
		case "object":
			val = "a";
			/*
			 if (type == "object") {
			 var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);
			 if (objname == undefined) {
			 return;
			 }
			 objname[1] = this.serialize(objname[1]);
			 val = "O" + objname[1].substring(1, objname[1].length - 1);
			 }
			 */
			var count = 0;
			var vals = "";
			var okey;
			var key;
			for(key in mixed_value) {
				if(mixed_value.hasOwnProperty(key)) {
					ktype = _getType(mixed_value[key]);
					if(ktype === "function") {
						continue;
					}
					okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
					vals += this.serialize(okey) + this.serialize(mixed_value[key]);
					count++;
				}
			}
			val += ":" + count + ":{" + vals + "}";
			break;
		case "undefined":
		// Fall-through
		default:
			// if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
			val = "N";
			break;
	}
	if(type !== "object" && type !== "array") {
		val += ";";
	}
	return val;
}

// http://phpjs.org/functions/utf8_decode:576
function utf8_decode(str_data) {
	// http://kevin.vanzonneveld.net
	// +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
	// +      input by: Aman Gupta
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Norman "zEh" Fuchs
	// +   bugfixed by: hitwork
	// +   bugfixed by: Onno Marsman
	// +      input by: Brett Zamir (http://brett-zamir.me)
	// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// *     example 1: utf8_decode('Kevin van Zonneveld');
	// *     returns 1: 'Kevin van Zonneveld'
	var tmp_arr = [], i = 0, ac = 0, c1 = 0, c2 = 0, c3 = 0;
	str_data += '';

	while(i < str_data.length) {
		c1 = str_data.charCodeAt(i);
		if(c1 < 128) {
			tmp_arr[ac++] = String.fromCharCode(c1);
			i++;
		} else if(c1 > 191 && c1 < 224) {
			c2 = str_data.charCodeAt(i + 1);
			tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
			i += 2;
		} else {
			c2 = str_data.charCodeAt(i + 1);
			c3 = str_data.charCodeAt(i + 2);
			tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			i += 3;
		}
	}

	return tmp_arr.join('');
}

//http://phpjs.org/functions/ucfirst:568
function ucfirst(str) {
	// http://kevin.vanzonneveld.net
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   bugfixed by: Onno Marsman
	// +   improved by: Brett Zamir (http://brett-zamir.me)
	// *     example 1: ucfirst('kevin van zonneveld');
	// *     returns 1: 'Kevin van zonneveld'
	str += '';
	var f = str.charAt(0).toUpperCase();
	return f + str.substr(1);
}

// http://phpjs.org/functions/ucwords:569
function ucwords(str) {
	// http://kevin.vanzonneveld.net
	// +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	// +   improved by: Waldo Malqui Silva
	// +   bugfixed by: Onno Marsman
	// +   improved by: Robin
	// +      input by: James (http://www.james-bell.co.uk/)
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// *     example 1: ucwords('kevin van  zonneveld');
	// *     returns 1: 'Kevin Van  Zonneveld'
	// *     example 2: ucwords('HELLO WORLD');
	// *     returns 2: 'HELLO WORLD'
	return (str + '').replace(/^([a-z])|\s+([a-z])/g, function($1) {
		return $1.toUpperCase();
	});
}

function part_names(n, a) {
	a.setValue(ucwords(a.getValue().toLowerCase()));
	n.setValue(ucwords(n.getValue().toLowerCase()));
	var an = n.getValue().split(' ');
	if(an.length == 0 || n.getValue() == '') {
		n.setValue(a.getValue());
		a.setValue('');
	} else {
		var a2 = an[an.length - 1] + ' ' + a.getValue();
		a.setValue(a2.trim());
		an[an.length - 1] = '';
		var n2 = implode(' ', an);
		n.setValue(n2.trim());
	}
}

function limpiar_titulo(titulo) {
	var t = titulo.getValue().toLowerCase();
	t = ucfirst(t);
	titulo.setValue(t);
}

//http://www.zparacha.com/validate-email-address-using-javascript-regular-expression/
function validateEmail(elementValue) {
	var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
	return emailPattern.test(elementValue);
}

// http://stackoverflow.com/questions/18082/validate-numbers-in-javascript-isnumeric
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}