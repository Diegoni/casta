            <h1><?php echo $this->lang->line('Albaranes sin facturar');?></h1>
          <p>
            <table class="table table-striped table-bordered">
            	<thead>
            	<tr>
            		<th>#</th>
            		<th><?php echo $this->lang->line('Fecha');?></th>
            		<th><?php echo $this->lang->line('Cliente');?></th>
            		<th><?php echo $this->lang->line('Artículos');?></th>
            		<th><?php echo $this->lang->line('Importe');?></th>
            		<th><?php echo $this->lang->line('cCUser');?></th>
            	</tr>
            </thead>
            <tbody>
            <?php foreach ($albaranes as $albaran):?>
            	<tr id="alb_<?php echo $albaran['nIdAlbaran'];?>" class="albaran">
					<td>
						<a rel="<?php echo $albaran['nIdAlbaran'];?>" class="btn btn-mini" href="#"><i class="icon-star"></i>&nbsp;</a>&nbsp;<?php echo format_enlace_cmd($albaran['nIdAlbaran'], site_url('ventas/albaransalida/index/' . $albaran['nIdAlbaran']));?>
					</td>
            		<td><?php echo format_date($albaran['dCreacion']);?></td>
					<td><?php echo format_enlace_cmd(format_name($albaran['cNombre'], $albaran['cApellido'], $albaran['cEmpresa']), site_url('ventas/albaransalida/index/' . $albaran['nIdCliente']));?><br/><?php echo $albaran['nIdCliente']; ?></td>
            		<td style="text-align: center;"><span class="label <?php echo ($albaran['nLibros']<0)?'label-important':'label-info';?>"><?php echo format_number($albaran['nLibros']);?></span></td>
            		<td style="text-align: right;"><span class="label <?php echo ($albaran['fTotal']<0)?'label-inverse':'';?>"><?php echo format_price($albaran['fTotal']);?></span></td>
            		<td><?php echo $albaran['cCUser'];?></td>
            	</tr>
            <?php endforeach; ?>

        </tbody>
            </table>
        </p>
        <p>
		<small><?php echo format_number(count($albaranes));?> <?php echo $this->lang->line('registros encontrados');?></small>
        </p>

<script type="text/javascript">
	$(function() {
		jQuery('.albaran > td > a').bind('click', function(item) {
			item.preventDefault();
			var v = item.currentTarget.attributes.getNamedItem('rel').value;
			parent.Ext.app.callRemote({
				url: "<?php echo site_url('ventas/albaransalida/nofacturable');?>",
				params: {
					id: parseInt(v)
				},
				fnok: function(res)
				{
					if (res.facturable)
						jQuery('#alb_' + v).removeClass('error');
					else
						jQuery('#alb_' + v).addClass('error');
				}
			});
			return;
		});
	});
</script>
