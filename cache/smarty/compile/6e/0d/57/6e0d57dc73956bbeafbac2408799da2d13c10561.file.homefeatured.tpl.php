<?php /* Smarty version Smarty-3.1.19, created on 2015-04-22 13:14:11
         compiled from "C:\xampp2\htdocs\prestashop\themes\Casta\modules\homefeatured\homefeatured.tpl" */ ?>
<?php /*%%SmartyHeaderCode:182205537c8d3f3b6d0-81183319%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6e0d57dc73956bbeafbac2408799da2d13c10561' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\prestashop\\themes\\Casta\\modules\\homefeatured\\homefeatured.tpl',
      1 => 1429712177,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '182205537c8d3f3b6d0-81183319',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'products' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5537c8d4053064_86976327',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5537c8d4053064_86976327')) {function content_5537c8d4053064_86976327($_smarty_tpl) {?>
<?php if (isset($_smarty_tpl->tpl_vars['products']->value)&&$_smarty_tpl->tpl_vars['products']->value) {?>
	<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./product-list.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('class'=>'homefeatured tab-pane','id'=>'homefeatured'), 0);?>

<?php } else { ?>
<ul id="homefeatured" class="homefeatured tab-pane">
	<li class="alert alert-info"><?php echo smartyTranslate(array('s'=>'No featured products at this time.','mod'=>'homefeatured'),$_smarty_tpl);?>
</li>
</ul>
<?php }?><?php }} ?>
