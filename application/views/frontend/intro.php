<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<title>Hotel</title>
		
		<script src="<?php echo base_url().'librerias/jquery.js'?>" type="text/javascript"></script>
		<script src="<?php echo base_url().'librerias/main/js/intro_function.js'?>"></script>
		<script type="text/javascript" src="<?php echo base_url().'librerias/main/js/intro.js'?>"></script>
		
		<link href='http://fonts.googleapis.com/css?family=Raleway:400,800,300' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url().'librerias/intro/css/normalize.css'?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url().'librerias/intro/css/demo.css'?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url().'librerias/intro/css/component.css'?>" />

	</head>
	<body style="visibility:hidden; background-color:white; background-image:none">
		<?php 
		$efectos=array(
						'effect-lily', 
						'effect-sadie', 
						'effect-layla', 
						'effect-oscar', 
						'effect-marley', 
						'effect-ruby',
						'effect-chico',
						'effect-milo',
						'effect-sarah',
						'effect-dexter',
						'effect-romeo',
						'effect-bubba',
						'effect-roxy'
						);
		shuffle($efectos);
		$i=0;
		?>
		<div class="container">
			<!-- Top Navigation -->
			<header class="codrops-header">
				<h1>Hoteles Gold <span>Mensaje de la empresa</span></h1>	
			</header>
			<div class="grid">
			<?php foreach ($hoteles as $hotel) { ?> 
				<a href="<?php echo base_url().'index.php/inicio/hotel/'.$hotel->id_hotel; ?>">
					<figure class="<?php echo $efectos[$i]?>">
						<img src="<?php echo base_url().'assets/uploads/logos/'.$hotel->fondo_intro?>" alt="img01"/>
						<figcaption>
							<h2>Hotel <span><?php echo $hotel->hotel ?></span></h2>
							<p><?php echo $hotel->descripcion ?></p>
						</figcaption>			
					</figure>
				</a>
				<?php 	$i=$i+1; 
						if($i>count($efectos)){
							$i=0;
						}
				?>
			<?php } ?>
			</div>
		</div><!-- /container -->
	</body>
</html>