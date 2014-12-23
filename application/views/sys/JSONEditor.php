<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0039)http://braincast.nl/samples/jsoneditor/ -->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>JSON Editor</title>

<?php echo js_asset('json.js', 'app');?>
<?php echo js_asset('xml2json.js', 'app');?>
<?php echo js_asset('saXMLUtils.pack.js', 'app');?>
<?php echo js_asset('yetii-min.js', 'app');?>
<?php echo js_asset('main.js', 'app');?>
<?php echo css_asset('jsoneditor.css', 'app');?>
</head>
<body>
<div id="treecontainer">
<div id="div1"></div>
</div>
<div id="tabcontainer">
<div id="tab-container-1" class="tablayout">
<ul id="tab-container-1-nav" class="tablayout">
	<li><a href="#tab1">source</a></li>
	<li id="editortab" class="noshow"><a href="#tab2">editor</a></li>
	<li id="searchtab" class="noshow"><a href="#tab3">search</a></li>
</ul>
<div class="tab" id="tab1">
<div id="tab-container-2" class="tablayout nested">
<ul id="tab-container-2-nav" class="tablayout">
	<li><a href="#tab1a">json</a></li>
	<li><a href="#tab2b">xml</a></li>
</ul>
<div class="tabn" id="tab1a">
<table class="icanhastable">
	<tr>
		<td>
		<div class="button" id="refresh" title="Reload below json from tree"></div>
		</td>
		<td>sample json: <select id="jsonsamples">
			<option value="0">Samples</option>
		</select></td>
	</tr>
	<tr>
		<td colspan="2"><textarea wrap="virtual" id="jsonstr" cols="55"
			rows="10" class=""><?php if (isset($json)):?><?php echo $json;?><?php endif;?></textarea></td>
	</tr>
	<tr>
		<td colspan="2"><input type="button" id="buildbutton"
			value="build tree" /></td>
	</tr>
</table>
</div>
<div class="tab" id="tab3">
<table class="icanhastable">
	<tr>
		<td>search: <input type="text" id="keyword" /><input type="button"
			id="search" value="search" /></td>
	</tr>
	<tr>
		<td>
		<div id="results">&nbsp;</div>
		</td>
	</tr>
</table>
</div>
</div>
<div id="console">
<div id="bar"><a id="consolebar">Error console</a></div>
<div id="log"></div>
</div>
</div>
</body>
</html>
