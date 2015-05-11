<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:52:34
         compiled from "/var/www/casta/admin007anrqcy/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1918542265550bede218fe14-46123516%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5d6545868b0112b9deb199c5e34487da3bf93a0c' => 
    array (
      0 => '/var/www/casta/admin007anrqcy/themes/default/template/content.tpl',
      1 => 1426844057,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1918542265550bede218fe14-46123516',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_550bede219bbf1_76286576',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550bede219bbf1_76286576')) {function content_550bede219bbf1_76286576($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div><?php }} ?>
