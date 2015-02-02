/*
	todo beta 1
		- 
		
	todo
	- dropdowns with samples should have description (ie not Sample1,Sample2)
	- implement 'add new' button (pfff...)
	- make the errors more descriptive... dammit
	- use the domstorage (if available) to save json strings
	- add 'render as html' button...
	- enable [ctrl]-s for saving tree
	- implement jslint on json parse error (http://jslint.com/)
		- json must then at all times be printed like so: replace( find:"," replace:"\n," )
		- display line numbers with textarea?
	- settings tab with:
		- enable/disable autodetect typeOf
		- font/size of textarea's
		- 
	- make textarea's resizable
	
	BUGBUG
	   xml2json uses single quotes for json labels and values, this should be a double quote!!!!
*/
var BCJT = function(){
	return{
		info: {"version": "1.1", "www": "http://braincast.nl", "date": "april 2008", "description": "Braincast Json Tree object."},
		util: function(){
			function addLoadEvent(func){var oldonload = window.onload;if (typeof window.onload != 'function'){window.onload = func;}else{window.onload = function(){if(oldonload){oldonload();}func();};}}
			function addEvent(obj, type, fn){if (obj.attachEvent){obj['e'+type+fn] = fn;obj[type+fn] = function(){obj['e'+type+fn](window.event);};obj.attachEvent('on'+type, obj[type+fn]);}else{obj.addEventListener(type, fn, false);}}
			function getElementById(strid){return document.getElementById(strid);}
			return{addLoadEvent: addLoadEvent,addEvent: addEvent, $: getElementById};
		}(),
		tree: function(){
			var treeIndex = 0;
			var li = 0;
			function TreeUtil(){
				var oldA;
				function makeUrl(jsonpath, text, li, index, clickable){return (clickable) ? "<a id=\"a"+li+"\" onclick=\"BCJT.tree.forest["+index+"].getNodeValue('"+jsonpath+"', this);return false\">"+text+"</a>" : text;}
				function isTypeOf(thing){return (thing !== null) ? thing.constructor : thing;}
				function strIsTypeOf(con){switch(con){case Array: return 'array';case Object: return 'object';case Function: return 'function';case String: return 'string';case Number: return 'number';case Boolean: return 'boolean';case null: return 'null';default: return 'undeterimable type';}}
				function getParentLi(item){
					/* not used */
					return (item.nodeName == "LI") ? item.id : getParentLi(item.parentNode);
				}
				return{
					strIsTypeOf: strIsTypeOf,
					isTypeOf: isTypeOf,
					getNodeValue: function(jsonPath, aobj){
						if (aobj){
							if (isTypeOf(aobj) == String){
								aobj = document.getElementById(aobj);
							}
							if (oldA){oldA.className = "au";} aobj.className = "as"; oldA = aobj;
						}
						this.cp = "BCJT.tree.forest["+this.index+"]."+jsonPath;
						this.ca = aobj;
						this.cli = document.getElementById("li"+aobj.id.substr(1));
						var params = {"jsonPath":"", "jsonValue": "", "jsonType": null, "a":{}, li: {}};
						try{
							var jsval = eval("BCJT.tree.forest["+this.index+"]."+jsonPath);
							var typ = isTypeOf(jsval);
							var txt;			
							if (typ == Function){txt = (jsval.toSource) ? jsval.toSource() : txt = jsval;
							}else if (typ == String){txt = JSON.stringify(jsval).replace(/(^")|("$)/g, "");
							}else{
								txt = JSON.stringify(jsval);
							}
							params.jsonPath = jsonPath;
							params.jsonValue = txt;
							params.jsonType = strIsTypeOf(typ);					
							params.a = this.ca;
							params.li = this.cli;
							this.nodeclick(params);
						}catch(e){
							BCJT.tree.error = "Could not get value!<br />" + e;
						}
					},
					makeTree: function(content,dots,inside){
						var out = ""; var t;
						if(content === null){
						  	if (!inside){out += "<ul><li><s>null</s></li></ul>";}
						}else if (isTypeOf(content) == Array){
							for (var i=0; i<content.length; i++){
								dots += "["+i+"]";
								out += this.makeTree(content[i], dots, false);
								dots = dots.substr(0, dots.length - (""+i).length-2);
							}
						}else if(isTypeOf(content) == Object){
							out += "<ul>";
							for(var j in content){
								dots += "[\\'"+j+"\\']";
								t = this.makeTree(content[j], dots, true);				
								dots = dots.substr(0, dots.length - j.length - 6);
								li++;
								out+="<li id=\"li"+li+"\">"+makeUrl(dots+"[\\'"+j+"\\']", j, li, this.index, this.clickable)+" "+t;
							}
							out += "</ul>"; 
						}else if(isTypeOf(content) == String){out += "</li>";
						}else{out += "</li>";}
						return out; 
					},
					reloadTree: function(){
						li = 0;
						if (this.clickable){
							if (this.rootLink === ""){this.rootLink = "BCJT.tree.forest["+this.index+"].getNodeValue('json', this);return false";}						
							this.el.innerHTML = "<ul id=\"tree"+this.index+"\" class=\"mktree\"><li><a id=\"a0\" onclick=\""+this.rootLink+"\">"+this.rootNode+"</a><ul>"+this.makeTree(this.json, "json", false).substr(4)+"</ul></li></ul>";
						}else{this.el.innerHTML = "<ul id=\"tree"+this.index+"\" class=\"mktree\"><li>"+this.rootNode+"<ul>"+this.makeTree(this.json, "json", false).substr(4)+"</ul></li></ul>";}						
						if (this.mktree){ BCJT.mktree.processList( document.getElementById("tree"+this.index) );}
					}
				};
			}
			function Tree(json, div, params){
				if (!params){ params = {};}
				var options = {"json":json,"nodeclick":function(){}, "mktree": true, "clickable": true, "index": treeIndex, "el": document.getElementById(div), "cp":null, "ca":null,"cli":null, rootNode: "json", rootLink: "", "newtree": true};
				for (var key in options){this[key] = (params[key] !== undefined) ? params[key] : options[key];}
				if (this.newtree){
					if (this.clickable){
						if (this.rootLink === ""){this.rootLink = "BCJT.tree.forest["+this.index+"].getNodeValue('json', this);return false";}
						this.el.innerHTML = "<ul id=\"tree"+treeIndex+"\" class=\"mktree\"><li><a id=\"a0\" onclick=\""+this.rootLink+"\">"+this.rootNode+"</a><ul>"+this.makeTree(json, "json", false).substr(4)+"</ul></li></ul>";
					}else{this.el.innerHTML = "<ul id=\"tree"+treeIndex+"\" class=\"mktree\"><li>"+this.rootNode+"<ul>"+this.makeTree(json, "json", false).substr(4)+"</ul></li></ul>";}
					BCJT.tree.forest.push(this);
					treeIndex++;
				}else{
					if (this.clickable){
						if (this.rootLink === ""){this.rootLink = "BCJT.tree.forest["+this.index+"].getNodeValue('json', this);return false";}
						this.el.innerHTML = "<ul id=\"tree"+this.index+"\" class=\"mktree\"><li><a id=\"a0\" onclick=\""+this.rootLink+"\">"+this.rootNode+"</a><ul>"+this.makeTree(json, "json", false).substr(4)+"</ul></li></ul>";
					}else{this.el.innerHTML = "<ul id=\"tree"+this.index+"\" class=\"mktree\"><li>"+this.rootNode+"<ul>"+this.makeTree(json, "json", false).substr(4)+"</ul></li></ul>";}
					li = 0;
					BCJT.tree.forest[this.index] = this;
				}
				if (this.mktree){ BCJT.mktree.processList( document.getElementById("tree"+this.index) );}
				return this;
			}
			Tree.prototype = new TreeUtil();
			return{
				forest: [],
				_tree: Tree, /* expose the internal Tree object for prototype purposes */
				init: function(json, div, params){
					try{
						var j = (json.constructor === Object) ? json : eval('(' +json+ ')');
						new Tree(j, div, params);
						return true;
					}catch(e){
						BCJT.tree.error = "Build tree failed!<br />" + e;
						return false;
					}
				},
				error: ""
			};
		}(),
		mktree: function(){
			/* All below code was obtained from: http://www.javascripttoolbox.com/lib/mktree/ 
			   the autor is: Matt Kruse (http://www.mattkruse.com/)
			   (The code below was slightly modified!)
			*/
			var nodeClosedClass ="liClosed", nodeOpenClass = "liOpen", nodeBulletClass = "liBullet", nodeLinkClass = "bullet";
			
			/* the two below functions will prevent memory leaks in IE */
			function treeNodeOnclick(){this.parentNode.className = (this.parentNode.className==nodeOpenClass) ? nodeClosedClass : nodeOpenClass;return false;}	
			function retFalse(){return false;}		
			function processList(ul){
				if (!ul.childNodes || ul.childNodes.length===0){return;}
				var childNodesLength = ul.childNodes.length;
				for (var itemi=0;itemi<childNodesLength;itemi++){
					var item = ul.childNodes[itemi];
					if (item.nodeName == "LI"){
						var subLists = false;
						var itemChildNodesLength = item.childNodes.length;
						for (var sitemi=0;sitemi<itemChildNodesLength;sitemi++){
							var sitem = item.childNodes[sitemi];
							if (sitem.nodeName=="UL"){subLists = true; processList(sitem);}
						}
						var s = document.createElement("SPAN");
						var t = '\u00A0';
						s.className = nodeLinkClass;
						if (subLists){
							if (item.className===null || item.className===""){item.className = nodeClosedClass;}
							if (item.firstChild.nodeName=="#text") {t = t+item.firstChild.nodeValue; item.removeChild(item.firstChild);}
							s.onclick = treeNodeOnclick;
						}else{item.className = nodeBulletClass; s.onclick = retFalse;}
						s.appendChild(document.createTextNode(t));
						item.insertBefore(s,item.firstChild);
					}
				}
			}
			// Performs 3 functions:
			// a) Expand all nodes
			// b) Collapse all nodes
			// c) Expand all nodes to reach a certain ID
			function expandCollapseList(ul,nodeOpenClass,itemId){
				if (!ul.childNodes || ul.childNodes.length===0){return false;}
				for (var itemi=0;itemi<ul.childNodes.length;itemi++){
					var item = ul.childNodes[itemi];
					if (itemId!==null && item.id==itemId){return itemId;}
					if (item.nodeName == "LI"){
						var subLists = false;
						for (var sitemi=0;sitemi<item.childNodes.length;sitemi++){
							var sitem = item.childNodes[sitemi];
							if (sitem.nodeName=="UL"){
								subLists = true;
								var ret = expandCollapseList(sitem,nodeOpenClass,itemId);
								if (itemId!==null && ret){item.className=nodeOpenClass; return itemId;}
							}
						}
						if (subLists && itemId===null){item.className = nodeOpenClass;}
					}
				}
			}
			// Full expands a tree with a given ID
			function expandTree(treeId) {
			  var ul = document.getElementById(treeId);
			  if (ul === null) { return false; }
			  expandCollapseList(ul,nodeOpenClass);
			}
			
			// Fully collapses a tree with a given ID
			function collapseTree(treeId) {
			  var ul = document.getElementById(treeId);
			  if (ul === null) { return false; }
			  expandCollapseList(ul,nodeClosedClass);
			}
			
			// Expands enough nodes to expose an LI with a given ID
			function expandToItem(treeId,itemId) {
			  var ul = document.getElementById(treeId);
			  if (ul === null) { return false; }
			  var ret = expandCollapseList(ul,nodeOpenClass,itemId);			  
			  if (ret) {
			    var o = document.getElementById(itemId);
			    if (o.scrollIntoView) {
			      o.scrollIntoView(false);
			    }
			  }
			}
			return{
				processList: processList,
				expandCollapseList: expandCollapseList,
				expandTree: expandTree,
				collapseTree: collapseTree,
				expandToItem: expandToItem
			};
		}()
	};
}();
if ( typeof $ == "undefined" ) var $ = BCJT.util.$;
var addE = BCJT.util.addEvent;

var BCJTE = function(){
	if (!BCJT){
		throw new Error("BCJTE needs the BCJT object!");
	}
	var tp = BCJT.tree._tree;
	tp.prototype.deleteNode = function(){
		if (this.cp !== null){
			var del = this.cp.substring(this.cp.lastIndexOf("[")+2,this.cp.lastIndexOf("]")-1);
			var pp = this.cp.substring(0, this.cp.lastIndexOf("["));
			var parent = eval( pp );
			var no = {};
			for(var i in parent){if (i !== del){no[i] = parent[i];}}
			eval(pp +"="+ JSON.stringify(no));
			var pn = this.cli.parentNode.parentNode.id;
			this.cli.parentNode.removeChild(this.cli);
			this.reloadTree();
			/* expanding fails because the tree is being reindexed during reload... the old id probably doens't exist anymore */
			//BCJT.mktree.expandToItem(this.index, pn);
			//alert(this.index +"\n"+ pn);
			
		}
	};
	tp.prototype.save = function(nv,t){
		if (this.cp !== null){
			var str = "";
			if (t === undefined){ t = this.strIsTypeOf(this.isTypeOf(nv));}
			switch(t){
				case 'string': str = this.cp + "='" + nv + "'"; break;
				case 'object': case 'boolean': case 'function': case 'number': str = this.cp + "=" + nv; break;
				case 'array': str = this.cp +"="+ Array(nv); break;
				case 'null': str = this.cp +"=null"; break;
				default: return t;
			}
			try{
				eval(str);
				if (t == "object" || t == "array"){
					if (window.confirm("New value's have been saved.\nDo you want to rebuild the tree?")){
						this.reloadTree();
						if (this.mktree){
							var liid = BCJT.mktree.expandCollapseList(document.getElementById("tree"+this.index), this.cli.id);
							//document.getElementById("a"+liid.substr(2)).className = "as";
							this.ca = null;
							this.cp = null;
							this.cli = null;
						}
					}
				}						
			}catch(e){
				$("log").innerHTML = "There's an error in your value!<br />" + e;
				$("console").style.display = "block";
			}
		}
	};
	
	function searchJson(tree, keyword){
		var results = [];
		var li = 0;
		function searchKeyword(input,word){if (input===null){return -1;}return input.toString().search(new RegExp(word,"gi"));}		
		function searchTree(content,index,dots,inside,keyword){
			if (tree.isTypeOf(content) == Array){
				for (var i=0; i<content.length; i++){
					dots+="["+i+"]";
					searchTree(content[i], i, dots, false,keyword);
					dots=dots.substr(0, dots.length - (""+i).length-2);
				}
			}else if(tree.isTypeOf(content) == Object){
				for(var i in content){
					dots+="[\\'"+i+"\\']";
					searchTree(content[i], -1, dots,true,keyword);
					li++;
					if (searchKeyword(i,keyword) > -1 && tree.isTypeOf(i)!==Object){makeList(dots, i, li, "label");}
					if (searchKeyword(content[i],keyword) > -1 && tree.isTypeOf(content[i])!==Object){makeList(dots, i, li, "value");}					
					dots=dots.substr(0, dots.length - i.length - 6);	
				}
			}				
		}
		function makeList(dots, where, id, val){results.push({"a":"a"+id,"li":"li"+id,"path":dots,"value":where,"where":val});}		
		return function(){
			li =0;
			results.length = 0;
			searchTree(tree.json, -1, "json", false, keyword);
			return results;
		}();
	}
	tp.prototype.search = function(keyword){
		return searchJson(this, keyword);
	};
	return{
		samples: ['{"widget": {"debug": true,"window": {"title": "Sample Konfabulator Widget","name": "main_window","width": 500,"height": 500},"Pairs": [ {"src": "Images/Sun.png","name": "sun1"},{"hOffset": 250,"vOffset": 200},null,{"alignment": "center"}],"text": {"a very long item label here": "Click Here","size": 36,"style": null,"name": "text1","hOffset": 250,"vOffset": 100,"alignment": "center","onmouseover": function(){alert("Hello World");},"onMouseUp": "sun1.opacity = (sun1.opacity / 100) * 90;"}}}',
		'{employee:{gid:102, companyID:121, defaultActionID:444,names:{firstName:"Stive", middleInitial:"Jr",lastName:"Martin"},address:{city:"Albany",state:"NY",zipCode:"14410-585",addreess:"41 State Street"},job:{departmentID:102,jobTitleID:100,hireDate:"1/02/2000",terminationDate:"1/12/2007"},contact:{phoneHome:"12-123-2133", beeper:"5656",email1:"info@soft-amis.com",fax:"21-321-23223",phoneMobile:"32-434-3433",phoneOffice:"82-900-8993"},login:{employeeID:"eID102",password:"password",superUser:true,lastLoginDate:"1/12/2007",text:"text", regexp:/^mmm/, date: new Date() },comment:{PCDATA:"comment"},roles:[{role:102},{role:103}]}}',
		'{"members": [{"href": "1","entity": {"category": [{"term": "weblog", "label": "Weblog stuff"}],"updated": "2007-05-02T23:32:03Z","title": "This is the second post","author": {"uri": "http://dealmeida.net/","email": "roberto@dealmeida.net","name": "Rob de Almeida"},"summary": "Testing search","content": {"content": "This is my second post, to test the search.","type": "text"},"id": "1"}},{"href": "0","entity": {"category": [{"term": "weblog", "label": "Weblog stuff"},{"term": "json", "label": "JSON"}],"updated": "2007-05-02T23:25:59Z","title": "This is the second version of the first post","author": {"uri": "http://dealmeida.net/","email": "roberto@dealmeida.net","name": "Rob de Almeida"},"summary": "This is my first post here, after some modifications","content": {"content": "This is my first post, testing the jsonstore WSGI microapp PUT.","type": "html"},"id": "0"}}],"next": null}',
		'{"menu": {"header": "SVG Viewer","items": [{"id": "Open"},{"id": "OpenNew", "label": "Open New", "thing": "thing"},{"id": "ZoomIn", "label": "Zoom In"},{"id": "ZoomOut", "label": "Zoom Out"},{"id": "OriginalView", "label": "Original View"},null,{"id": "Quality"},{"id": "Pause"},{"id": "Mute"},null,{"id": "Find", "label": "Find..."},{"id": "FindAgain", "label": "Find Again"},{"id": "Copy"},{"id": "CopyAgain", "label": "Copy Again"},{"id": "CopySVG", "label": "Copy SVG"},{"id": "ViewSVG", "label": "View SVG"}]}}'],
		samplesxml: ['<animals><dog><name>Rufus</name><breed>labrador</breed></dog><dog><name>Marty</name><breed>whippet</breed></dog><cat name="Matilda"/></animals>',
		'<?xml version="1.0" encoding="ISO-8859-1"?><breakfast_menu><food><name>Belgian Waffles</name><price>$5.95</price><description>two of our famous Belgian Waffles with plenty of real maple syrup</description><calories>650</calories></food><food><name>Strawberry Belgian Waffles</name><price>$7.95</price><description>light Belgian waffles covered with strawberries and whipped cream</description><calories>900</calories></food><food><name>Berry-Berry Belgian Waffles</name><price>$8.95</price><description>light Belgian waffles covered with an assortment of fresh berries and whipped cream</description><calories>900</calories></food><food><name>French Toast</name><price>$4.50</price><description>thick slices made from our homemade sourdough bread</description><calories>600</calories></food><food><name>Homestyle Breakfast</name><price>$6.95</price><description>two eggs, bacon or sausage, toast, and our ever-popular hash browns</description><calories>950</calories></food></breakfast_menu>',
		'<xml id="DynNavXml" style="display:none;"><response sendername="" totalCount="1363"><dropdowns><dropdown name="countrycodes"><item key="be">Belgi�</item><item key="de">Duitsland</item><item key="fr">Frankrijk</item><item key="nl">Nederland</item><item key="int">Overig</item><item key="es">Spanje</item><item key="uk">Verenigd Koninkrijk</item><item key="us">Verenigde Staten</item></dropdown><dropdown name="newscategories"><item key="Column">Column</item><item key="News article">News article</item></dropdown><dropdown name="productcategories"><item key="Anual">Anual</item><item key="Basic plant material">Basic plant material</item><item key="Cut flower">Cut flower</item><item key="Pot plant">Pot plant</item></dropdown><dropdown name="targetaudiences"><item key="Consumer">Consumer</item><item key="Exporter">Exporter</item><item key="Florist">Florist</item><item key="Garden center/DIY">Garden center/DIY</item><item key="Grower">Grower</item><item key="Remainder">Remainder</item><item key="Supermarket">Supermarket</item><item key="Supplier basic plant material">Supplier basic plant material</item><item key="Wholesaler">Wholesaler</item></dropdown><dropdown name="remaindercategories"><item key="Flower Council">Flower Council</item></dropdown><dropdown name="period"><item key="2008">2008</item><item key="2007">2007</item><item key="2006">2006</item></dropdown></dropdowns></response></xml>'],
		addOptions: function(object, oValue, oText){object.options[object.length] = new Option(oText, oValue, false, false);},
		objectTypes: ['array','object','function','string','number','boolean','null','undeterimable type'],
		info: {"version": "1.2", "www": "http://braincast.nl", "date": "april 2008", "description": "Editor extension object for the Braincast Json Tree object."}
	};
}();


var BCJTEP = function(){
	if (!BCJT && !BCJTE){
		throw new Error("BCJTEP needs the BCJT object and the BCJTE object!");
	}
	function selectType(object, type){
		var l = object.options.length;
		for (var i=0;i<l;i++){
			if (object.options[i].text == type){
				object.selectedIndex= i;
				break;
			}
		}
	}
	function stripslashes(str){
	    str = str.replace(/\\'/g, '\'');
		str = str.replace(/\\"/g, '"');
		str = str.replace(/\\\\/g, '\\');
		//str = str.replace(/\\0/g, '\0');
		str = str.replace(/\\0/g, '0');
		return str;
	}
	function determineUserType(a){
		try{var y = eval(a);
		}catch(e){
			try{y = eval('('+a+')');
			}catch(f){return 'string';}
		}
		var x = typeof y;
		if (x == 'object'){
			try{
				x = y.constructor;
				if (x === Array){return 'array';}
				else if (x === Object){return 'object';}
			}catch(g){return 'null';}
		}else if (x == 'undefined'){return 'object';}
		else{return x;}
	}
	return{
		build: function(){
			var jsonstr = document.getElementById("jsonstr").value;
			if (jsonstr == "") return false;
			var r = BCJT.tree.init(jsonstr, "div1", {"rootNode": "json", "index": 0,"newtree":false, "nodeclick": function(p){
				if (p.jsonPath == "json"){
					$("jsonsamples").selectedIndex= 0;
					tabber1.show(1);
					$("jsonstr").value = p.jsonValue;
				}else{
					if ($("tab1").style.display == "block"){tabber1.show(2);}
					/*
					alert("type: " + p.jsonType + "\n" +
					  "value: " + p.jsonValue + "\n" +
					  "path: " + p.jsonPath + "\n" +
					  "li: " + p.li
					  );*/
					  $("jsonvalue").value = p.jsonValue;
					  $("jsonpath").innerHTML = "Path: " + p.jsonPath;
					  selectType($("jsontypes"),p.jsonType); 
				}
			}});
			if (r){return r;
			}else{
				$("log").innerHTML = BCJT.tree.error;
				$("console").style.display = "block";
				return false;
			}
		},	
		stripslashes: stripslashes,
		uType: determineUserType,
		selectType: selectType,
		writeResults: function(){
			var results = BCJT.tree.forest[0].search(document.getElementById("keyword").value);
			var res = $("results");
			res.innerHTML = "";
			var strtable = "<table width=\"100%\"><tbody>";
			for (var i=0; i< results.length; i++){
				strtable += "<tr><td width=\"15\"><img src=\"images/" + results[i].where + ".gif\" title=\"Result found in the "+results[i].where+"\" border=\"0\" /></td>";
				strtable += "<td><a href=\"#\" onclick=\"BCJT.mktree.expandToItem('tree0', '"+results[i].li+"');BCJT.tree.forest[0].getNodeValue('"+results[i].path+"', '"+results[i].a+"');return false\">" + results[i].value + "</td>";
				strtable += "<td class=\"path\">" + BCJTEP.stripslashes(String(results[i].path)) + "</td></tr>";
			}
			strtable += "</tbody></table>";
			res.innerHTML = strtable;
		},
		info: {"version": "1.3", "www": "http://braincast.nl", "date": "april 2008", "description": "Braincast Json Tree Presentation object."}
	};
}();

BCJTEP.prototype = function(){
	BCJT.util.addLoadEvent(function(){
		tabber1 = new Yetii({id: 'tab-container-1'});
		tabber2 = new Yetii({id: 'tab-container-2',tabclass: 'tabn'});
		addE($("buildbutton"), "click", function(){
			if (BCJTEP.build()){
				$("results").innerHTML = "&nbsp;";
				$("editortab").className = "show";
				$("searchtab").className = "show";
			}
		})
		
		var samples = $("jsonsamples");
		var j = BCJTE.samples.length;
		for (var i = 0; i<j; i++){BCJTE.addOptions(samples, "BCJTE.samples["+i+"]" ,"sample"+i);}
		addE(samples, "change", function(){
			if (this.options[this.selectedIndex].value != 0)
				$("jsonstr").value = eval(this.options[this.selectedIndex].value);
		});

		var samplesxml = $("xmlsamples");
		var j = BCJTE.samplesxml.length;
		for (var i = 0; i<j; i++){BCJTE.addOptions(samplesxml, "BCJTE.samplesxml["+i+"]" ,"sample"+i);}
		addE(samplesxml, "change", function(){
			if (this.options[this.selectedIndex].value != 0)
				$("xmlstr").value = eval(this.options[this.selectedIndex].value);
		});
		
		var jsontypes = $("jsontypes");
		var j = BCJTE.objectTypes.length;
		for (var i = 0; i<j; i++){BCJTE.addOptions(jsontypes, i ,BCJTE.objectTypes[i]);}
		
		addE($("savebutton"), "click", function(){
				if ($("autodetect").checked){
					BCJTEP.selectType( $("jsontypes"), BCJTEP.uType($("jsonvalue").value) );
				}
				var obj = BCJT.tree.forest[0];				
				var listtype = $("jsontypes").options[$("jsontypes").selectedIndex].text;
				obj.save($("jsonvalue").value,listtype);
			});
		/*
		addE($("jsonvalue"), "keydown", function(e){
			if (e.keyCode == 83 && e.ctrlKey){
				e.preventDefault();
				alert("Save");
			}
		});*/
		
		addE($("refresh"), "mousedown", function(){
			$("refresh").className = "button buttondown";
			$("refresh").style.backgroundPosition = "right bottom";
			if (BCJT.tree.forest[0]) BCJT.tree.forest[0].getNodeValue('json', 'a0');
		});
		addE($("refresh"), "mouseup", function(){
			$("refresh").className = "button";
			$("refresh").style.backgroundPosition = "center center";
		});

		addE($("convertbutton"), "click", function(){
			if ($("simple").checked){	
				try{
					$("jsonstr").value = xml2json.parser($("xmlstr").value,'','compact');
					tabber2.show(1);
				}catch(e){
					$("log").innerHTML = "Parse failed (try the complex version).<br />" + e;
					$("console").style.display = "block";
				}
			}else{
				try{
					var d = XMLObjectifier.textToXML($("xmlstr").value);
					var j = XMLObjectifier.xmlToJSON(d);
					$("jsonstr").value = JSON.stringify(j);
					tabber2.show(1);
				}catch(e){
					$("log").innerHTML = "Parse failed.<br />" + e;
					$("console").style.display = "block";
				}	
			}
		});	
		
		
		addE($("add"), "mousedown", function(){
			$("add").className = "button buttondown";
			$("add").style.backgroundPosition = "right bottom";
		});
		addE($("add"), "mouseup", function(){
			$("add").className = "button";
			$("add").style.backgroundPosition = "center center";
		});	
		
		addE($("delete"), "mousedown", function(){
			$("delete").className = "button buttondown";
			$("delete").style.backgroundPosition = "right bottom";
			if (confirm("Are you sure you want to delete this node?")) BCJT.tree.forest[0].deleteNode();
		});
		addE($("delete"), "mouseup", function(){
			$("delete").className = "button";
			$("delete").style.backgroundPosition = "center center";
		});
		
		addE($("search"), "click", function(){
			BCJTEP.writeResults();
		});
		addE($("keyword"), "keydown", function(e){
			if (e.keyCode == 13){
				BCJTEP.writeResults();
			}
		});
		addE($("consolebar"), "click", function(){
			$("console").style.display = "none";
			return false;
		});
		
	});
}();