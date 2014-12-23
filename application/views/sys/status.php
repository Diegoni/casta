<html>
<head>
<title><?php echo $this->lang->line('Estado servidor');?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF8" />
<style type="text/css">
body {
	background-color: #fff;
	margin: 40px;
	font-family: Lucida Grande, Verdana, Sans-serif;
	font-size: 12px;
	color: #000;
}

#content {
	border: #999 1px solid;
	background-color: #fff;
	padding: 20px 20px 12px 20px;
}

h1 {
	font-weight: normal;
	font-size: 14px;
	color: #990000;
	margin: 0 0 4px 0;
}

pre {
	padding-top: 0 .5em;
	padding-right: 0 pt;
	padding-bottom: 0 .5em;
	padding-left: 1 em;
	background-color: #222222;
	overflow-x: auto;
	overflow-y: auto;
	line-height: 17 px;
	font-size: 13 px;
	color: #ffffff;
	font-family: " Bitstream Vera Sans Mono ", monospace;
}
</style>
</head>
<body>
<div id="content"><?php foreach($process as $p):?>
<h1><?php echo $p[1];?></h1>
<pre><?php echo string_encode(implode("\n", $p[2]));?></pre> <?php endforeach;?></div>
</body>
</html>
