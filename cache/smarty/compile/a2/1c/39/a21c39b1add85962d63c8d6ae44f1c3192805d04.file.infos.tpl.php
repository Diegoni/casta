<?php /* Smarty version Smarty-3.1.19, created on 2015-04-15 11:11:08
         compiled from "C:\xampp2\htdocs\prestashop\modules\bankwire\views\templates\hook\infos.tpl" */ ?>
<?php /*%%SmartyHeaderCode:27575552e717c997fb5-37066419%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a21c39b1add85962d63c8d6ae44f1c3192805d04' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\prestashop\\modules\\bankwire\\views\\templates\\hook\\infos.tpl',
      1 => 1424703572,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '27575552e717c997fb5-37066419',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_552e717c9de4c9_66903840',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_552e717c9de4c9_66903840')) {function content_552e717c9de4c9_66903840($_smarty_tpl) {?>

<div class="alert alert-info">
<img src="../modules/bankwire/bankwire.jpg" style="float:left; margin-right:15px;" width="86" height="49">
<p><strong><?php echo smartyTranslate(array('s'=>"This module allows you to accept secure payments by bank wire.",'mod'=>'bankwire'),$_smarty_tpl);?>
</strong></p>
<p><?php echo smartyTranslate(array('s'=>"If the client chooses to pay by bank wire, the order's status will change to 'Waiting for Payment.'",'mod'=>'bankwire'),$_smarty_tpl);?>
</p>
<p><?php echo smartyTranslate(array('s'=>"That said, you must manually confirm the order upon receiving the bank wire.",'mod'=>'bankwire'),$_smarty_tpl);?>
</p>
</div>
<?php }} ?>
