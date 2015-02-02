<?php $this->load->helper('asset');?>
<table summary="<?php echo $title;?>"
	class="sortable-onload-0 rowstyle-alt colstyle-alt no-arrow"
	width="100%" id="tab_resumen">
	<caption><?php echo $title;?></caption>
	<thead>
		<tr>
			<th colspan="7">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($messages as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php if ($m['type']=='error') echo image_asset('fugue/exclamation-red.png');?>
			<?php if ($m['type']=='warning') echo image_asset('fugue/exclamation.png');?></td>
			<?php echo str_repeat('<td>&nbsp;</td>', $m['level']);?>
			
			<td colspan="<?php echo 5 - $m['level'];?>"><?php echo $m['message'];?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
