<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <title><?php echo $title;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="assets/css/docs.css" rel="stylesheet">
   <script src="http://code.jquery.com/jquery-latest.js"></script>

    <style type="text/css">
    #printable { display: none; }
    @media print
    {
        #non-printable { display: none; }
        #printable { display: block; }
    }  
    </style>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

  </head>

  <body>

    <div class="container-fluid">
      <div class="row-fluid">

      </div><!--/row-->

      <?php echo $body; ?>


      <hr>

      <footer>
        <p class="muted"><small><?php echo $this->lang->line('Creado')?> <?php echo date('d-m-Y G:i:s');?></small></p>
      </footer>

    </div><!--/.fluid-container-->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!--<script src="assets/js/application.js"></script>-->
  </body>
</html>
