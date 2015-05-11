<?php /* Smarty version Smarty-3.1.19, created on 2015-04-22 10:58:16
         compiled from "C:\xampp2\htdocs\prestashop\themes\TMS\modules\blockcontact\nav.tpl" */ ?>
<?php /*%%SmartyHeaderCode:100175537a8f8867d38-25053103%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8386a434818a692d284aa9cd14e8b9402fa25011' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\prestashop\\themes\\TMS\\modules\\blockcontact\\nav.tpl',
      1 => 1429704543,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '100175537a8f8867d38-25053103',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'telnumber' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5537a8f8913b57_35408832',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5537a8f8913b57_35408832')) {function content_5537a8f8913b57_35408832($_smarty_tpl) {?>
<div id="contact-link">
	<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Contact us','mod'=>'blockcontact'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Contact us','mod'=>'blockcontact'),$_smarty_tpl);?>
</a>
</div>
<?php if ($_smarty_tpl->tpl_vars['telnumber']->value) {?>
	<span class="shop-phone">
		<i class="icon-phone"></i><?php echo smartyTranslate(array('s'=>'Call us now:','mod'=>'blockcontact'),$_smarty_tpl);?>
 <strong><?php echo $_smarty_tpl->tpl_vars['telnumber']->value;?>
</strong>
	</span>
<?php }?><?php }} ?>
