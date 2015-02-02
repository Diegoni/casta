<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->page_size('a4'); ?>
<?php echo get_instance()->htmlfile->margins(0,0,0,0); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Labels</title>
    <style>
    body {
        width: 21cm;
        margin: <?php echo str_replace(',', '.', $config['fTop']/10);?>cm <?php echo str_replace(',', '.', $config['fLeft']/10);?>cm;
        }
    .label{
        width: <?php echo str_replace(',', '.', ($config['fWidth'])/10);?>cm; 
        height: <?php echo str_replace(',', '.', ($config['fHeight'])/10);?>cm; 
        /*margin-left: <?php echo str_replace(',', '.', $config['fPaddingLeft']/10);?>cm; /* the gutter */
        /*margin-top: <?php echo str_replace(',', '.', $config['fPaddingTop']/10);?>cm; /* the gutter */

        float: left;

        /*text-align: center;*/
        overflow: hidden;

        /*outline: 1px dotted; /* outline doesn't occupy space like border does */
        }
    .insider{
        padding: <?php echo str_replace(',', '.', $config['fHorizontal']/10);?>cm <?php echo str_replace(',', '.', $config['fVertical']/10);?>cm 0;
        /*top:50%;
        position:absolute;
        margin-top:-5em;*/
        }
    .page-break  {
        clear: left;
        display:block;
        page-break-after:always;
        }
    </style>

</head> <body> <?php  $ini = ($row - 1) * $config['nColumns'] + $column - 1;
echo str_repeat('<div class="label">&nbsp;</div>', $ini); $count = $ini;  ?>
<?php foreach ($etiquetas as $value): ?> <div class="label"><div class="insider"><?php echo
$value;?></div></div> <?php ++$count; ?> <?php if ($count == $config['nRows'] *
$config['nColumns']):?> <div class="page-break"></div> <?php $count = 0; ?>
<?php endif; ?> <?php endforeach; ?> </body> </html>
