<?php /* Smarty version Smarty-3.1.19, created on 2015-04-22 11:23:56
         compiled from "C:\xampp2\htdocs\prestashop\themes\Casta\modules\homefeatured\homefeatured.tpl" */ ?>
<?php /*%%SmartyHeaderCode:167025537aefc542970-02576923%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
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
  'nocache_hash' => '167025537aefc542970-02576923',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'products' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5537aefc55df06_68696768',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5537aefc55df06_68696768')) {function content_5537aefc55df06_68696768($_smarty_tpl) {?>
<?php if (isset($_smarty_tpl->tpl_vars['products']->value)&&$_smarty_tpl->tpl_vars['products']->value) {?>
	<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./product-list.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array('class'=>'homefeatured tab-pane','id'=>'homefeatured'), 0);?>

<?php } else { ?>
<ul id="homefeatured" class="homefeatured tab-pane">
	<li class="alert alert-info"><?php echo smartyTranslate(array('s'=>'No featured products at this time.','mod'=>'homefeatured'),$_smarty_tpl);?>
</li>
</ul>
<?php }?><?php }} ?>
