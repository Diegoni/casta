<?php /* Smarty version Smarty-3.1.19, created on 2015-05-14 09:44:20
         compiled from "C:\xampp2\htdocs\casta\pdf\\delivery-slip.tpl" */ ?>
<?php /*%%SmartyHeaderCode:25831555498a4de4a55-43120782%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0c63f1fbea4a8f81c1fa9e66f3ae1a300cf871dc' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\casta\\pdf\\\\delivery-slip.tpl',
      1 => 1424703476,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25831555498a4de4a55-43120782',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'invoice_address' => 0,
    'delivery_address' => 0,
    'order' => 0,
    'order_invoice' => 0,
    'payment' => 0,
    'carrier' => 0,
    'order_details' => 0,
    'bgcolor' => 0,
    'order_detail' => 0,
    'customizationPerAddress' => 0,
    'customization' => 0,
    'customization_infos' => 0,
    'HOOK_DISPLAY_PDF' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_555498a50addd1_34681030',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_555498a50addd1_34681030')) {function content_555498a50addd1_34681030($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include 'C:\\xampp2\\htdocs\\casta\\tools\\smarty\\plugins\\function.cycle.php';
?>
<div style="font-size: 9pt; color: #444">

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<!-- ADDRESSES -->
<table style="width: 100%">
	<tr>
		<td style="width: 20%"></td>
		<td style="width: 80%">
			<?php if (!empty($_smarty_tpl->tpl_vars['invoice_address']->value)) {?>
				<table style="width: 100%">
					<tr>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 11pt; color: #9E9F9E"><?php echo smartyTranslate(array('s'=>'Delivery Address','pdf'=>'true'),$_smarty_tpl);?>
</span><br />
							 <?php echo $_smarty_tpl->tpl_vars['delivery_address']->value;?>

						</td>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 11pt; color: #9E9F9E"><?php echo smartyTranslate(array('s'=>'Billing Address','pdf'=>'true'),$_smarty_tpl);?>
</span><br />
							 <?php echo $_smarty_tpl->tpl_vars['invoice_address']->value;?>

						</td>
					</tr>
				</table>
			<?php } else { ?>
				<table style="width: 100%">
					<tr>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 11pt; color: #9E9F9E"><?php echo smartyTranslate(array('s'=>'Billing & Delivery Address','pdf'=>'true'),$_smarty_tpl);?>
</span><br />
							 <?php echo $_smarty_tpl->tpl_vars['delivery_address']->value;?>

						</td>
						<td style="width: 50%">

						</td>
					</tr>
				</table>
			<?php }?>
		</td>
	</tr>
</table>
<!-- / ADDRESSES -->

<table>
	<tr><td style="line-height: 8px">&nbsp;</td></tr>
</table>

<!-- PRODUCTS TAB -->
<table style="width: 100%">
	<tr>
		<td style="width: 22%; padding-right: 7px; text-align: right; vertical-align: top">
			<!-- CUSTOMER INFORMATIONS -->
			<b><?php echo smartyTranslate(array('s'=>'Order Number:','pdf'=>'true'),$_smarty_tpl);?>
</b><br />
			<?php echo $_smarty_tpl->tpl_vars['order']->value->getUniqReference();?>
<br />
			<br />
			<b><?php echo smartyTranslate(array('s'=>'Order Date:','pdf'=>'true'),$_smarty_tpl);?>
</b><br />
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['order']->value->date_add,'full'=>0),$_smarty_tpl);?>
<br />
			<br />
			<b><?php echo smartyTranslate(array('s'=>'Payment Method:','pdf'=>'true'),$_smarty_tpl);?>
</b><br />
			<table style="width: 100%;">
			<?php  $_smarty_tpl->tpl_vars['payment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['payment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order_invoice']->value->getOrderPaymentCollection(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['payment']->key => $_smarty_tpl->tpl_vars['payment']->value) {
$_smarty_tpl->tpl_vars['payment']->_loop = true;
?>
				<tr>
					<td style="width: 50%"><?php echo $_smarty_tpl->tpl_vars['payment']->value->payment_method;?>
</td>
					<td style="width: 50%"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['payment']->value->amount,'currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency),$_smarty_tpl);?>
</td>
				</tr>
			<?php }
if (!$_smarty_tpl->tpl_vars['payment']->_loop) {
?>
				<tr>
					<td><?php echo smartyTranslate(array('s'=>'No payment'),$_smarty_tpl);?>
</td>
				</tr>
			<?php } ?>
			</table>
			<br />
			<?php if (isset($_smarty_tpl->tpl_vars['carrier']->value)) {?>
			<b><?php echo smartyTranslate(array('s'=>'Carrier:','pdf'=>'true'),$_smarty_tpl);?>
</b><br />
			<?php echo $_smarty_tpl->tpl_vars['carrier']->value->name;?>
<br />
			<br />
			<?php }?>			
			<!-- / CUSTOMER INFORMATIONS -->
		</td>
		<td style="width: 78%; text-align: right">
			<table style="width: 100%">
				<tr style="line-height:6px;">
					<?php if (Configuration::get('PS_PDF_IMG_DELIVERY')) {?>
						<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 10%"><?php echo smartyTranslate(array('s'=>'IMAGE','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<?php }?>
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 60%"><?php echo smartyTranslate(array('s'=>'ITEMS TO BE DELIVERED','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: left; font-weight: bold; width: 20%"><?php echo smartyTranslate(array('s'=>'REFERENCE','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: center; font-weight: bold; width: 20%"><?php echo smartyTranslate(array('s'=>'QTY','pdf'=>'true'),$_smarty_tpl);?>
</td>
				</tr>
				<?php  $_smarty_tpl->tpl_vars['order_detail'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['order_detail']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order_details']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['order_detail']->key => $_smarty_tpl->tpl_vars['order_detail']->value) {
$_smarty_tpl->tpl_vars['order_detail']->_loop = true;
?>
				<?php echo smarty_function_cycle(array('values'=>'#FFF,#DDD','assign'=>'bgcolor'),$_smarty_tpl);?>

				<tr style="line-height:6px;background-color:<?php echo $_smarty_tpl->tpl_vars['bgcolor']->value;?>
;" <?php if (Configuration::get('PS_PDF_IMG_DELIVERY')&&isset($_smarty_tpl->tpl_vars['order_detail']->value['image'])&&$_smarty_tpl->tpl_vars['order_detail']->value['image']->id&&isset($_smarty_tpl->tpl_vars['order_detail']->value['image_size'])) {?>height="<?php echo $_smarty_tpl->tpl_vars['order_detail']->value['image_size'][1];?>
"<?php }?>>
					<?php if (Configuration::get('PS_PDF_IMG_DELIVERY')) {?>
						<td style="text-align: left;"><?php if (isset($_smarty_tpl->tpl_vars['order_detail']->value['image'])&&$_smarty_tpl->tpl_vars['order_detail']->value['image']->id) {?><?php echo $_smarty_tpl->tpl_vars['order_detail']->value['image_tag'];?>
<?php }?></td>
					<?php }?>
					<td style="text-align: left; width: 60%"><?php echo $_smarty_tpl->tpl_vars['order_detail']->value['product_name'];?>
</td>
					<td style="text-align: left; width: 20%">
						<?php if (empty($_smarty_tpl->tpl_vars['order_detail']->value['product_reference'])) {?>
							---
						<?php } else { ?>
							<?php echo $_smarty_tpl->tpl_vars['order_detail']->value['product_reference'];?>

						<?php }?>
					</td>
					<td style="text-align: center; width: 20%"><?php echo $_smarty_tpl->tpl_vars['order_detail']->value['product_quantity'];?>
</td>
				</tr>
					<?php  $_smarty_tpl->tpl_vars['customizationPerAddress'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customizationPerAddress']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order_detail']->value['customizedDatas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customizationPerAddress']->key => $_smarty_tpl->tpl_vars['customizationPerAddress']->value) {
$_smarty_tpl->tpl_vars['customizationPerAddress']->_loop = true;
?>
						<?php  $_smarty_tpl->tpl_vars['customization'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customization']->_loop = false;
 $_smarty_tpl->tpl_vars['customizationId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customizationPerAddress']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customization']->key => $_smarty_tpl->tpl_vars['customization']->value) {
$_smarty_tpl->tpl_vars['customization']->_loop = true;
 $_smarty_tpl->tpl_vars['customizationId']->value = $_smarty_tpl->tpl_vars['customization']->key;
?>
							<tr style="line-height:6px;background-color:<?php echo $_smarty_tpl->tpl_vars['bgcolor']->value;?>
;">
								<td style="line-height:3px; text-align: left; width: 60%; vertical-align: top">
										<blockquote>
											<?php if (isset($_smarty_tpl->tpl_vars['customization']->value['datas'][@constant('_CUSTOMIZE_TEXTFIELD_')])&&count($_smarty_tpl->tpl_vars['customization']->value['datas'][@constant('_CUSTOMIZE_TEXTFIELD_')])>0) {?>
												<?php  $_smarty_tpl->tpl_vars['customization_infos'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customization_infos']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['customization']->value['datas'][@constant('_CUSTOMIZE_TEXTFIELD_')]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customization_infos']->key => $_smarty_tpl->tpl_vars['customization_infos']->value) {
$_smarty_tpl->tpl_vars['customization_infos']->_loop = true;
?>
													<?php echo $_smarty_tpl->tpl_vars['customization_infos']->value['name'];?>
: <?php echo $_smarty_tpl->tpl_vars['customization_infos']->value['value'];?>

													<?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['custo_foreach']['last']) {?><br />
													<?php } else { ?>
													<div style="line-height:0.4pt">&nbsp;</div>
													<?php }?>
												<?php } ?>
											<?php }?>

											<?php if (isset($_smarty_tpl->tpl_vars['customization']->value['datas'][@constant('_CUSTOMIZE_FILE_')])&&count($_smarty_tpl->tpl_vars['customization']->value['datas'][@constant('_CUSTOMIZE_FILE_')])>0) {?>
												<?php echo count($_smarty_tpl->tpl_vars['customization']->value['datas'][@constant('_CUSTOMIZE_FILE_')]);?>
 <?php echo smartyTranslate(array('s'=>'image(s)','pdf'=>'true'),$_smarty_tpl);?>

											<?php }?>
										</blockquote>
								</td>
								<td style="text-align: right; width: 20%"></td>
								<td style="text-align: center; width: 20%; vertical-align: top">(<?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity'];?>
)</td>
							</tr>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			</table>
		</td>
	</tr>
</table>
<!-- / PRODUCTS TAB -->

<table>
	<tr><td style="line-height: 8px">&nbsp;</td></tr>
</table>

<?php if (isset($_smarty_tpl->tpl_vars['HOOK_DISPLAY_PDF']->value)) {?>
	<div style="line-height: 1pt">&nbsp;</div>
	<table style="width: 100%">
		<tr>
			<td style="width: 15%"></td>
			<td style="width: 85%">
				<?php echo $_smarty_tpl->tpl_vars['HOOK_DISPLAY_PDF']->value;?>

			</td>
		</tr>
	</table>
<?php }?>

</div>

<?php }} ?>
