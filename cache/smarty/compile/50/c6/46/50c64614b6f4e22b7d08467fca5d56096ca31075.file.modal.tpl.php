<?php /* Smarty version Smarty-3.1.19, created on 2015-11-12 09:21:04
         compiled from "C:\xampp2\htdocs\casta\admin1978\themes\default\template\helpers\modules_list\modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:528956448430dca611-16119238%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '50c64614b6f4e22b7d08467fca5d56096ca31075' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\casta\\admin1978\\themes\\default\\template\\helpers\\modules_list\\modal.tpl',
      1 => 1424703476,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '528956448430dca611-16119238',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56448430dce497_26791001',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56448430dce497_26791001')) {function content_56448430dce497_26791001($_smarty_tpl) {?><div class="modal fade" id="modules_list_container">
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
