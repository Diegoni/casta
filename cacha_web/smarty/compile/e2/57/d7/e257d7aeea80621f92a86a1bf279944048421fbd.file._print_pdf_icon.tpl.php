<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:40:15
         compiled from "/var/www/casta/admin/themes/default/template/controllers/outstanding/_print_pdf_icon.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1883724881550beaff7b5309-15610395%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e257d7aeea80621f92a86a1bf279944048421fbd' => 
    array (
      0 => '/var/www/casta/admin/themes/default/template/controllers/outstanding/_print_pdf_icon.tpl',
      1 => 1426844062,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1883724881550beaff7b5309-15610395',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'id_invoice' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_550beaff7c6ad2_27457354',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550beaff7c6ad2_27457354')) {function content_550beaff7c6ad2_27457354($_smarty_tpl) {?>


<?php if (Configuration::get('PS_INVOICE')) {?>
	<span style="width:20px; margin-right:5px;">
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), ENT_QUOTES, 'UTF-8', true);?>
&amp;submitAction=generateInvoicePDF&amp;id_order_invoice=<?php echo $_smarty_tpl->tpl_vars['id_invoice']->value;?>
"><img src="../img/admin/tab-invoice.gif" alt="invoice" /></a>
	</span>
<?php }?><?php }} ?>
