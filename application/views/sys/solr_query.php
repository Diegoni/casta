<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $this->lang->line('Búsqueda');?></title>
</head>
<body>
<form accept-charset="utf-8" method="get"><label for="query"><?php echo $this->lang->line('Buscar');?>:</label>
<input id="query" name="query" type="text"
	value="<?php echo $query; ?>" />
<input type="submit" /></form>
<?php if (isset($results)):?>
<pre><?php #print_r($results)?></pre>
<?php
$total = $results['response']['numFound'];
$start = min(1, $total);
$end = $start + count($results['response']['docs']) - 1;
?>
<div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
<ol>
<?php foreach ($results['response']['docs'] as $doc): ?>
	<li>
	<table style="border: 1px solid black; text-align: left">
	<?php foreach ($doc as $field => $value):?>
		<tr>
			<th><?php echo $field; ?></th>
			<td><?php echo $value; ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	</li>
	<?php endforeach; ?>
</ol>
	<?php endif; ?>
</body>
</html>
