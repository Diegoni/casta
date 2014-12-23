<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <title>Traspasos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="assets/css/docs.css" rel="stylesheet">

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
      <div class="span3 bs-docs-sidebar non-printable">
        <ul class="nav nav-list bs-docs-sidenav">
          <li><a href="#resumen"><i class="icon-chevron-right"></i> Resumen</a></li>
          <?php if (count($errores) > 0): ?>
          <li><a href="#errores"><i class="icon-chevron-right"></i> Errores <span class="badge badge-important"><?php echo count($errores);?></span></a></li>
          <?php endif; ?>
          <?php if (count($warnings) > 0): ?>
          <li><a href="#warnings"><i class="icon-chevron-right"></i> Incidencias <span class="badge badge-warning"><?php echo count($warnings);?></span></a></li>
          <?php endif; ?>
          <?php if (count($asientos) > 0): ?>
          <li><a href="#asientos"><i class="icon-chevron-right"></i> Asientos <span class="badge badge-inverse"><?php echo count($asientos);?></span></a></li>
          <?php endif; ?>
          <?php if (count($log) > 0): ?>
          <li><a href="#log"><i class="icon-chevron-right"></i> Log</a></li>
          <?php endif; ?>
        </ul>
      </div>        
      <div class="span9">
 <!-- Overview
        ================================================== -->
        <section id="resumen">
            <h1>Resumen</h1>
          <p>
            <table class="table table-bordered">
              <tr>
                <td><strong>Iniciador</strong></td>
                <td><?php echo $iniciador;?><?php if ($test): ?> <span class="label label-info">Modo prueba</span><?php endif?></td>
              </tr>
              <tr>
                <td><strong>Asientos</strong></td>
                <td><span class="badge badge-inverse"><?php echo count($asientos);?></span></td>
              </tr>
              <?php if (count($errores) > 0): ?>
              <tr>
                <td><strong>Errores</strong></td>
                <td><span class="badge badge-important"><?php echo count($errores);?></span></td>
              </tr>
              <?php endif; ?>
              <?php if (count($warnings) > 0): ?>
              <tr>
                <td><strong>Incidencias</strong></td>
                <td><span class="badge badge-warning"><?php echo count($warnings);?></span></td>
              </tr>
              <?php endif; ?>
              <tr>
                <td><strong>Fecha</strong></td>
                <td><?php echo date('d-m-Y G:i:s');?></td>
              </tr>
              <tr>
                <td><strong>Fichero</strong></td>
                <td><?php echo $access;?><?php if (!empty($id) && !$test): ?>
              <?php echo format_enlace_cmd('<i class="icon-download-alt"></i> ', site_url('sys/bp2lc/download/' . $id), 'btn btn-mini');?>
              <?php endif; ?></td>
              </tr>
            </table>
          </p>
        </section> 
        </div>
      </div>
        <div class="row-fluid">
        <div class="span12">
        <?php if (count($errores) > 0): ?>
        <section id="errores">
            <h1>Errores</h1>
          <p>
            <table class="table table-striped table-bordered">
              <tr>
                <th>Fecha</th>
                <th>Factura</th>
                <th>Id</th>
                <th>Error</th>
              </tr>
            <?php foreach ($errores as $error):?>
            <tr class="error">
              <td><?php echo date('d-m-Y', $error['dFecha']);?></td>
              <td><strong><?php echo $error['cNumero']; ?></strong></td>
              <td><?php echo format_enlace_cmd($error['nIdFactura'], site_url('ventas/factura/index/' . $error['nIdFactura']));?></td>
              <td><span class="label label-important"><?php echo $error['error']; ?></span>
              <?php if (!empty($error['cmd'])):?>
              <?php echo format_enlace_cmd('<i class="icon-star"></i> ', site_url($error['cmd'] . '/' . $error['nIdFactura']), 'btn btn-mini');?>
              <?php else: ?>
              <?php echo format_enlace_cmd('<i class="icon-ok"></i> ', site_url('ventas/factura/contabilizar/' . $error['nIdFactura']), 'btn btn-mini');?>
              <?php endif; ?>
              </td>
            </tr>
            <?php endforeach;?>
          </table>
          </p>
        </section>
      <?php endif; ?>
        <?php if (count($warnings) > 0): ?>
        <section id="warnings">
            <h1>Incidencias</h1>
          <p>
            <table class="table table-striped table-bordered">
              <tr>
                <th>Fecha</th>
                <th>Factura</th>
                <th>Id</th>
                <th>Mensaje</th>
              </tr>
            <?php $war = array(); ?>
            <?php foreach ($warnings as $error):?>
            <?php 
            $style = (strpos($error['warning'], 'L') === 0)?'label-success':
              ((strpos($error['warning'], 'CL') === 0)?'label-inverse':
              ((strpos($error['warning'], 'F') === 0)?' ':
              ((strpos($error['warning'], 'EXCLUIDO') > 0)?'label-warning':
              ((strpos($error['warning'], 'No se ha') > 0)?'label-info':
              'label-warning'))));
              $war[$style][] = $error;
              ?>
            <?php endforeach;?>  
            <?php foreach ($war as $style => $data):?>
              <?php foreach ($data as $error):?>
              <tr class="warning">
                <td><?php echo date('d-m-Y', $error['dFecha']);?></td>
                <td><strong><?php echo $error['cNumero']; ?></strong></td>
                <td><?php echo format_enlace_cmd($error['nIdFactura'], site_url('ventas/factura/index/' . $error['nIdFactura']));?></td>
                <td><span class="label <?php echo $style;?>"><?php echo $error['warning']; ?></span></td>
              </tr>
              <?php endforeach;?>
            <?php endforeach;?>
          </table>
        </p>
        </section>
      <?php endif; ?>
        <?php if (count($asientos) > 0): ?>
        <section id="asientos">
            <h1>Asientos</h1>
            <?php foreach ($asientos as $asiento):?>
          <p>
            <table class="table table-striped table-bordered">
              <tr>
                <th colspan="7"><span class="badge badge-inverse"><?php echo $asiento['num']?></span> <?php echo $asiento['dia']?> <?php echo $asiento['desc']?></th>
              </tr>
              <tr>
                <th>#</th>
                <th>D/H</th>
                <th>CUENTA</th>
                <th>VALOR</th>
                <th>DESCRIPCION</th>
                <th>DOC</th>
              </tr>
              <?php $ct = 0;?>
            <?php foreach ($asiento['apuntes'] as $apunte):?>
              <tr>
                <td><span class="badge"><?php echo ++$ct; ?></span></td>
                <td><span style="color: green;"><?php echo $apunte['dh'];?></span></td>
                <td><strong><?php echo $apunte['cc'];?></strong></td>
                <td><span style="color: orange;"><?php echo $apunte['valor'];?></span></td>
                <td><span style="color: blue;"><?php echo $apunte['desc'];?></span></td>
                <td><?php echo $apunte['doc'];?></td>
              </tr>
            <?php endforeach;?>
          </table>
        </p>
            <?php endforeach;?>
        </section>
      <?php endif; ?>
        <?php if (count($log) > 0): ?>
        <section id="log">
            <h1>Log</h1>
          <p>
            <pre><?php foreach ($log as $log):?><?php echo $log . "\n"; ?><?php endforeach;?></pre>
        </p>
        </section>
      <?php endif; ?>
      </div><!--/span-->

      </div><!--/row-->


      <hr>

      <footer>
        <p>Creado <?php echo date('d-m-Y G:i:s');?></p>
      </footer>

    </div><!--/.fluid-container-->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
   <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/application.js"></script>
  </body>
</html>
