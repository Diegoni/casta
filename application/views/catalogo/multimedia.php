<?php $this->load->helper('extjs');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $this->lang->line('Creados');?>">
	<caption>
		<?php echo $this->lang->line('Creados');?>
	</caption>
	<thead>
		<tr>
			<th><?php echo $this->lang->line('Creados');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($elm as $m):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $m;?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="9" scope="row"><?php echo count($elm);?> <?php echo $this->lang->line('registros encontrados');?></td>
		</tr>
	</tfoot>
</table>

<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $this->lang->line('Erróneos');?>">
	<caption>
		<?php echo $this->lang->line('Erróneos');?>
	</caption>
	<thead>
		<tr>
			<th><?php echo $this->lang->line('Erróneos');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($error as $m):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $m;?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="9" scope="row"><?php echo count($error);?> <?php echo $this->lang->line('registros encontrados');?></td>
		</tr>
	</tfoot>
</table>

<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $this->lang->line('No encontrados');?>">
	<caption>
		<?php echo $this->lang->line('No encontrados');?>
	</caption>
	<thead>
		<tr>
			<th><?php echo $this->lang->line('No encontrados');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($no as $m):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $m;?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="9" scope="row"><?php echo count($no);?> <?php echo $this->lang->line('registros encontrados');?></td>
		</tr>
	</tfoot>
</table>
