<?php 
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
<?php foreach($js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
</head>
<body>
	<div class='container'>  
	    <div class="col-md-12">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	  				<?php echo $titulo ?>
	  			</div>
	  			<div class="panel-body">
					<?php echo $output; ?>
				</div>
		    </div>
	    </div>
    </div>
</body>
</html>
