<?php /* Smarty version Smarty-3.1.19, created on 2015-05-08 10:51:49
         compiled from "C:\xampp2\htdocs\prestashop\modules\blockcategories\views\blockcategories_admin.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16424554cbf756864d6-40736209%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '76ec5b5197f7cc507b9245f7ea852b85422b5204' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\prestashop\\modules\\blockcategories\\views\\blockcategories_admin.tpl',
      1 => 1429721454,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16424554cbf756864d6-40736209',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'helper' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_554cbf756b9164_48747932',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_554cbf756b9164_48747932')) {function content_554cbf756b9164_48747932($_smarty_tpl) {?>
<div class="form-group">
	<label class="control-label col-lg-3">
		<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="<?php echo smartyTranslate(array('s'=>'You can upload a maximum of 3 images.','mod'=>'blockcategories'),$_smarty_tpl);?>
">
			<?php echo smartyTranslate(array('s'=>'Thumbnails','mod'=>'blockcategories'),$_smarty_tpl);?>

		</span>
	</label>
	<div class="col-lg-4">
		<?php echo $_smarty_tpl->tpl_vars['helper']->value;?>

	</div>
</div>
<?php }} ?>
