<?php /* Smarty version Smarty-3.1.19, created on 2015-05-12 15:57:22
         compiled from "C:\xampp2\htdocs\casta\themes\default-bootstrap\product-list-colors.tpl" */ ?>
<?php /*%%SmartyHeaderCode:869755524d12a25ee7-89735615%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8b97c5bb22a318600051eb0ad3da5a605defc804' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\casta\\themes\\default-bootstrap\\product-list-colors.tpl',
      1 => 1424703478,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '869755524d12a25ee7-89735615',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'colors_list' => 0,
    'col_img_dir' => 0,
    'color' => 0,
    'link' => 0,
    'img_color_exists' => 0,
    'img_col_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_55524d12a83af9_26794188',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55524d12a83af9_26794188')) {function content_55524d12a83af9_26794188($_smarty_tpl) {?>
<ul class="color_to_pick_list clearfix">
	<?php  $_smarty_tpl->tpl_vars['color'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['color']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['colors_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['color']->key => $_smarty_tpl->tpl_vars['color']->value) {
$_smarty_tpl->tpl_vars['color']->_loop = true;
?>
		<?php $_smarty_tpl->tpl_vars['img_color_exists'] = new Smarty_variable(file_exists((($_smarty_tpl->tpl_vars['col_img_dir']->value).($_smarty_tpl->tpl_vars['color']->value['id_attribute'])).('.jpg')), null, 0);?>
		<li>
			<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getProductLink($_smarty_tpl->tpl_vars['color']->value['id_product'],null,null,null,null,null,$_smarty_tpl->tpl_vars['color']->value['id_product_attribute']), ENT_QUOTES, 'UTF-8', true);?>
" id="color_<?php echo intval($_smarty_tpl->tpl_vars['color']->value['id_product_attribute']);?>
" class="color_pick"<?php if (!$_smarty_tpl->tpl_vars['img_color_exists']->value&&isset($_smarty_tpl->tpl_vars['color']->value['color'])&&$_smarty_tpl->tpl_vars['color']->value['color']) {?> style="background:<?php echo $_smarty_tpl->tpl_vars['color']->value['color'];?>
;"<?php }?>>
				<?php if ($_smarty_tpl->tpl_vars['img_color_exists']->value) {?>
					<img src="<?php echo $_smarty_tpl->tpl_vars['img_col_dir']->value;?>
<?php echo intval($_smarty_tpl->tpl_vars['color']->value['id_attribute']);?>
.jpg" alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['color']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['color']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
" width="20" height="20" />
				<?php }?>
			</a>
		</li>
	<?php } ?>
</ul><?php }} ?>