<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:40:16
         compiled from "/var/www/casta/admin/themes/default/template/controllers/stats/calendar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1776712647550beb0097ff59-97046204%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1cd9ba7b751bc58bc57eb0ae0f1bdaa92840c34e' => 
    array (
      0 => '/var/www/casta/admin/themes/default/template/controllers/stats/calendar.tpl',
      1 => 1426844063,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1776712647550beb0097ff59-97046204',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_550beb009c0553_93759474',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550beb009c0553_93759474')) {function content_550beb009c0553_93759474($_smarty_tpl) {?>

<div id="statsContainer" class="col-md-9">
	<?php echo $_smarty_tpl->getSubTemplate ("../../form_date_range_picker.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
<?php }} ?>
