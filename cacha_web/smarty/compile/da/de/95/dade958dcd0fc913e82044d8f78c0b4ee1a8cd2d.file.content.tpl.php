<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:40:17
         compiled from "/var/www/casta/admin/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:315792284550beb012f4910-41710268%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dade958dcd0fc913e82044d8f78c0b4ee1a8cd2d' => 
    array (
      0 => '/var/www/casta/admin/themes/default/template/content.tpl',
      1 => 1426844057,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '315792284550beb012f4910-41710268',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_550beb0131ff39_55752185',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550beb0131ff39_55752185')) {function content_550beb0131ff39_55752185($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div><?php }} ?>
