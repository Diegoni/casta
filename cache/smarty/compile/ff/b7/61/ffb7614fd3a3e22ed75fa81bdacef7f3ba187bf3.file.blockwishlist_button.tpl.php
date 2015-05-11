<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:40:12
         compiled from "/var/www/casta/themes/default-bootstrap/modules/blockwishlist/blockwishlist_button.tpl" */ ?>
<?php /*%%SmartyHeaderCode:609438997550beafccf56d3-35173667%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ffb7614fd3a3e22ed75fa81bdacef7f3ba187bf3' => 
    array (
      0 => '/var/www/casta/themes/default-bootstrap/modules/blockwishlist/blockwishlist_button.tpl',
      1 => 1426844232,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '609438997550beafccf56d3-35173667',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_550beafcd17bc9_92269947',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550beafcd17bc9_92269947')) {function content_550beafcd17bc9_92269947($_smarty_tpl) {?>

<div class="wishlist">
	<a class="addToWishlist wishlistProd_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
" href="#" rel="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
" onclick="WishlistCart('wishlist_block_list', 'add', '<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_product']);?>
', false, 1); return false;">
		<?php echo smartyTranslate(array('s'=>"Add to Wishlist",'mod'=>'blockwishlist'),$_smarty_tpl);?>

	</a>
</div><?php }} ?>
