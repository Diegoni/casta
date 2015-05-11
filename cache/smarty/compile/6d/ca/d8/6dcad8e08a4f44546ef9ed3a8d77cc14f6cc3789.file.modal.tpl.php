<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:40:21
         compiled from "/var/www/casta/admin/themes/default/template/helpers/modules_list/modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2066190995550beb054ab6c1-95574241%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6dcad8e08a4f44546ef9ed3a8d77cc14f6cc3789' => 
    array (
      0 => '/var/www/casta/admin/themes/default/template/helpers/modules_list/modal.tpl',
      1 => 1426844065,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2066190995550beb054ab6c1-95574241',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_550beb054b98f7_86998100',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550beb054b98f7_86998100')) {function content_550beb054b98f7_86998100($_smarty_tpl) {?><div class="modal fade" id="modules_list_container">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title"><?php echo smartyTranslate(array('s'=>'Recommended Modules'),$_smarty_tpl);?>
</h3>
			</div>
			<div class="modal-body">
				<div id="modules_list_container_tab_modal" style="display:none;"></div>
				<div id="modules_list_loader"><i class="icon-refresh icon-spin"></i></div>
			</div>
		</div>
	</div>
</div><?php }} ?>
