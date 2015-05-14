<?php /* Smarty version Smarty-3.1.19, created on 2015-05-14 09:43:35
         compiled from "C:\xampp2\htdocs\casta\pdf\\invoice.tpl" */ ?>
<?php /*%%SmartyHeaderCode:482655549877779af7-72368449%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8ff7b825735d8601c79d1575e59b67ccf2e7ba88' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\casta\\pdf\\\\invoice.tpl',
      1 => 1424703476,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '482655549877779af7-72368449',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'delivery_address' => 0,
    'invoice_address' => 0,
    'order' => 0,
    'order_invoice' => 0,
    'payment' => 0,
    'carrier' => 0,
    'product_reference_width' => 0,
    'tax_excluded_display' => 0,
    'order_details' => 0,
    'bgcolor' => 0,
    'order_detail' => 0,
    'customizationPerAddress' => 0,
    'customization' => 0,
    'customization_infos' => 0,
    'cart_rules' => 0,
    'cart_rule_colspan' => 0,
    'cart_rule' => 0,
    'tax_tab' => 0,
    'HOOK_DISPLAY_PDF' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_55549877b1b6d4_69647418',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55549877b1b6d4_69647418')) {function content_55549877b1b6d4_69647418($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include 'C:\\xampp2\\htdocs\\casta\\tools\\smarty\\plugins\\function.cycle.php';
?>
<div style="font-size: 8pt; color: #444">

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<!-- ADDRESSES -->
<table style="width: 100%">
	<tr>
		<td style="width: 17%"></td>
		<td style="width: 83%">
			<?php if (!empty($_smarty_tpl->tpl_vars['delivery_address']->value)) {?>
				<table style="width: 100%">
					<tr>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E"><?php echo smartyTranslate(array('s'=>'Delivery Address','pdf'=>'true'),$_smarty_tpl);?>
</span><br />
							 <?php echo $_smarty_tpl->tpl_vars['delivery_address']->value;?>

						</td>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E"><?php echo smartyTranslate(array('s'=>'Billing Address','pdf'=>'true'),$_smarty_tpl);?>
</span><br />
							 <?php echo $_smarty_tpl->tpl_vars['invoice_address']->value;?>

						</td>
					</tr>
				</table>
			<?php } else { ?>
				<table style="width: 100%">
					<tr>

						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E"><?php echo smartyTranslate(array('s'=>'Billing & Delivery Address.','pdf'=>'true'),$_smarty_tpl);?>
</span><br />
							 <?php echo $_smarty_tpl->tpl_vars['invoice_address']->value;?>

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

<div style="line-height: 1pt">&nbsp;</div>

<!-- PRODUCTS TAB -->
<table style="width: 100%">
	<tr>
		<td style="width: 17%; padding-right: 7px; text-align: right; vertical-align: top; font-size: 7pt;">
			<!-- CUSTOMER INFORMATION -->
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
					<td><?php echo smartyTranslate(array('s'=>'No payment','pdf'=>'true'),$_smarty_tpl);?>
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
			<!-- / CUSTOMER INFORMATION -->
		</td>
		<td style="width: 83%; text-align: right">
			<table style="width: 100%; font-size: 8pt;">
				<tr style="line-height:4px;">
					<?php $_smarty_tpl->tpl_vars['product_reference_width'] = new Smarty_variable(45, null, 0);?>
					<?php if (Configuration::get('PS_PDF_IMG_INVOICE')) {?>
						<?php $_smarty_tpl->tpl_vars['product_reference_width'] = new Smarty_variable($_smarty_tpl->tpl_vars['product_reference_width']->value-10, null, 0);?>
						<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 10%"><?php echo smartyTranslate(array('s'=>'Image','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<?php }?>
					<?php if (!$_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>
						<?php $_smarty_tpl->tpl_vars['product_reference_width'] = new Smarty_variable($_smarty_tpl->tpl_vars['product_reference_width']->value-10, null, 0);?>
					<?php }?>
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: <?php echo $_smarty_tpl->tpl_vars['product_reference_width']->value;?>
%"><?php echo smartyTranslate(array('s'=>'Product / Reference','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<!-- unit price tax excluded is mandatory -->
					<?php if (!$_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>
						<td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 20%"><?php echo smartyTranslate(array('s'=>'Unit Price','pdf'=>'true'),$_smarty_tpl);?>
 <br /><?php echo smartyTranslate(array('s'=>'(Tax Excl.)','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<?php }?>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 10%">
						<?php echo smartyTranslate(array('s'=>'Unit Price','pdf'=>'true'),$_smarty_tpl);?>

						<?php if ($_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>
							 <?php echo smartyTranslate(array('s'=>'(Tax Excl.)','pdf'=>'true'),$_smarty_tpl);?>

						<?php } else { ?>
							 <?php echo smartyTranslate(array('s'=>'(Tax Incl.)','pdf'=>'true'),$_smarty_tpl);?>

						<?php }?>
					</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 10%; white-space: nowrap;"><?php echo smartyTranslate(array('s'=>'Discount','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: center; font-weight: bold; width: 10%"><?php echo smartyTranslate(array('s'=>'Qty','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: <?php if (!$_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>15%<?php } else { ?>25%<?php }?>">
						<?php echo smartyTranslate(array('s'=>'Total','pdf'=>'true'),$_smarty_tpl);?>

						<?php if ($_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>
							<?php echo smartyTranslate(array('s'=>'(Tax Excl.)','pdf'=>'true'),$_smarty_tpl);?>

						<?php } else { ?>
							<?php echo smartyTranslate(array('s'=>'(Tax Incl.)','pdf'=>'true'),$_smarty_tpl);?>

						<?php }?>
					</td>
				</tr>
				<!-- PRODUCTS -->
				<?php  $_smarty_tpl->tpl_vars['order_detail'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['order_detail']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order_details']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['order_detail']->key => $_smarty_tpl->tpl_vars['order_detail']->value) {
$_smarty_tpl->tpl_vars['order_detail']->_loop = true;
?>
				<?php echo smarty_function_cycle(array('values'=>'#FFF,#DDD','assign'=>'bgcolor'),$_smarty_tpl);?>

				<tr style="line-height:6px;background-color:<?php echo $_smarty_tpl->tpl_vars['bgcolor']->value;?>
;" <?php if (Configuration::get('PS_PDF_IMG_INVOICE')&&isset($_smarty_tpl->tpl_vars['order_detail']->value['image'])&&$_smarty_tpl->tpl_vars['order_detail']->value['image']->id&&isset($_smarty_tpl->tpl_vars['order_detail']->value['image_size'])) {?>height="<?php echo $_smarty_tpl->tpl_vars['order_detail']->value['image_size'][1];?>
"<?php }?>>
					<?php if (Configuration::get('PS_PDF_IMG_INVOICE')) {?>
						<td style="text-align: left;"><?php if (isset($_smarty_tpl->tpl_vars['order_detail']->value['image'])&&$_smarty_tpl->tpl_vars['order_detail']->value['image']->id) {?><?php echo $_smarty_tpl->tpl_vars['order_detail']->value['image_tag'];?>
<?php }?></td>
					<?php }?>
					<td style="text-align: left; width: <?php echo $_smarty_tpl->tpl_vars['product_reference_width']->value;?>
%"><?php echo $_smarty_tpl->tpl_vars['order_detail']->value['product_name'];?>
<?php if (isset($_smarty_tpl->tpl_vars['order_detail']->value['product_reference'])&&!empty($_smarty_tpl->tpl_vars['order_detail']->value['product_reference'])) {?> (<?php echo smartyTranslate(array('s'=>'Reference:','pdf'=>'true'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['order_detail']->value['product_reference'];?>
)<?php }?></td>
					<!-- unit price tax excluded is mandatory -->
					<?php if (!$_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>
						<td style="text-align: right; width: 20%; white-space: nowrap;">
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_detail']->value['unit_price_tax_excl']),$_smarty_tpl);?>

						</td>
					<?php }?>
					<td style="text-align: right; width: 10%; white-space: nowrap;">
					<?php if ($_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_detail']->value['unit_price_tax_excl']),$_smarty_tpl);?>

					<?php } else { ?>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_detail']->value['unit_price_tax_incl']),$_smarty_tpl);?>

					<?php }?>
					</td>
					<td style="text-align: right; width: 10%">
					<?php if ((isset($_smarty_tpl->tpl_vars['order_detail']->value['reduction_amount'])&&$_smarty_tpl->tpl_vars['order_detail']->value['reduction_amount']>0)) {?>
						-<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_detail']->value['reduction_amount']),$_smarty_tpl);?>

					<?php } elseif ((isset($_smarty_tpl->tpl_vars['order_detail']->value['reduction_percent'])&&$_smarty_tpl->tpl_vars['order_detail']->value['reduction_percent']>0)) {?>
						-<?php echo $_smarty_tpl->tpl_vars['order_detail']->value['reduction_percent'];?>
%
					<?php } else { ?>
					--
					<?php }?>
					</td>
					<td style="text-align: center; width: 10%"><?php echo $_smarty_tpl->tpl_vars['order_detail']->value['product_quantity'];?>
</td>
					<td style="text-align: right;  width: <?php if (!$_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>15%<?php } else { ?>25%<?php }?>; white-space: nowrap;">
					<?php if ($_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_detail']->value['total_price_tax_excl']),$_smarty_tpl);?>

					<?php } else { ?>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_detail']->value['total_price_tax_incl']),$_smarty_tpl);?>

					<?php }?>
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
								<td style="line-height:3px; text-align: left; width: 45%; vertical-align: top">
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
								<?php if (!$_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>
									<td style="text-align: right;"></td>
								<?php }?>
								<td style="text-align: right; width: 10%"></td>
								<td style="text-align: center; width: 10%; vertical-align: top">(<?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity'];?>
)</td>
								<td style="width: 15%; text-align: right;"></td>
							</tr>
						<?php } ?>
					<?php } ?>
				<?php } ?>
				<!-- END PRODUCTS -->

				<!-- CART RULES -->
				<?php $_smarty_tpl->tpl_vars["shipping_discount_tax_incl"] = new Smarty_variable("0", null, 0);?>
				<?php  $_smarty_tpl->tpl_vars['cart_rule'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cart_rule']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cart_rules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cart_rule']->key => $_smarty_tpl->tpl_vars['cart_rule']->value) {
$_smarty_tpl->tpl_vars['cart_rule']->_loop = true;
?>
					<?php echo smarty_function_cycle(array('values'=>'#FFF,#DDD','assign'=>'bgcolor'),$_smarty_tpl);?>

					<tr style="line-height:6px;background-color:<?php echo $_smarty_tpl->tpl_vars['bgcolor']->value;?>
;text-align:left;">
						<?php $_smarty_tpl->tpl_vars['cart_rule_colspan'] = new Smarty_variable(2, null, 0);?>
						<?php if (Configuration::get('PS_PDF_IMG_INVOICE')) {?>
							<?php $_smarty_tpl->tpl_vars['cart_rule_colspan'] = new Smarty_variable($_smarty_tpl->tpl_vars['cart_rule_colspan']->value+1, null, 0);?>
						<?php }?>
						<td colspan="<?php echo $_smarty_tpl->tpl_vars['cart_rule_colspan']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['cart_rule']->value['name'];?>
</td>
						<td style="text-align: right">
							<?php if ($_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>
								- <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['cart_rule']->value['value_tax_excl']),$_smarty_tpl);?>

							<?php } else { ?>
								- <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['cart_rule']->value['value']),$_smarty_tpl);?>

							<?php }?>
						</td>
					</tr>
				<?php } ?>
				<!-- END CART RULES -->
			</table>

			<table style="width: 100%">
				<?php if ((($_smarty_tpl->tpl_vars['order_invoice']->value->total_paid_tax_incl-$_smarty_tpl->tpl_vars['order_invoice']->value->total_paid_tax_excl)>0)) {?>
				<tr style="line-height:5px;">
					<td style="width: 83%; text-align: right; font-weight: bold"><?php echo smartyTranslate(array('s'=>'Product Total (Tax Excl.)','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="width: 17%; text-align: right;"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_invoice']->value->total_products),$_smarty_tpl);?>
</td>
				</tr>

				<tr style="line-height:5px;">
					<td style="width: 83%; text-align: right; font-weight: bold"><?php echo smartyTranslate(array('s'=>'Product Total (Tax Incl.)','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="width: 17%; text-align: right;"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_invoice']->value->total_products_wt),$_smarty_tpl);?>
</td>
				</tr>
				<?php } else { ?>
				<tr style="line-height:5px;">
					<td style="width: 83%; text-align: right; font-weight: bold"><?php echo smartyTranslate(array('s'=>'Product Total','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="width: 17%; text-align: right;"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_invoice']->value->total_products),$_smarty_tpl);?>
</td>
				</tr>
				<?php }?>

				<?php if ($_smarty_tpl->tpl_vars['order_invoice']->value->total_wrapping_tax_incl>0) {?>
				<tr style="line-height:5px;">
					<?php if ($_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>
					<td style="text-align: right; font-weight: bold"><?php echo smartyTranslate(array('s'=>'Wrapping Cost (Tax Excl.)','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="width: 17%; text-align: right;"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_invoice']->value->total_wrapping_tax_excl),$_smarty_tpl);?>
</td>
					<?php } else { ?>
					<td style="text-align: right; font-weight: bold"><?php echo smartyTranslate(array('s'=>'Wrapping Cost (Tax Incl.)','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="width: 17%; text-align: right;"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_invoice']->value->total_wrapping_tax_incl),$_smarty_tpl);?>
</td>
					<?php }?>
				</tr>
				<?php }?>

				<?php if ($_smarty_tpl->tpl_vars['order_invoice']->value->total_shipping_tax_incl>0) {?>
				<tr style="line-height:5px;">
					<?php if ($_smarty_tpl->tpl_vars['tax_excluded_display']->value) {?>
					<td style="text-align: right; font-weight: bold"><?php echo smartyTranslate(array('s'=>'Shipping Cost (Tax Excl.)','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="width: 17%; text-align: right;"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_invoice']->value->total_shipping_tax_excl),$_smarty_tpl);?>
</td>
					<?php } else { ?>
					<td style="text-align: right; font-weight: bold"><?php echo smartyTranslate(array('s'=>'Shipping Cost (Tax Incl.)','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="width: 17%; text-align: right;"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_invoice']->value->total_shipping_tax_incl),$_smarty_tpl);?>
</td>
					<?php }?>
				</tr>
				<?php }?>

				<?php if ($_smarty_tpl->tpl_vars['order_invoice']->value->total_discount_tax_incl>0) {?>
				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold"><?php echo smartyTranslate(array('s'=>'Total Vouchers (Tax Incl.)','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="width: 17%; text-align: right;">-<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>($_smarty_tpl->tpl_vars['order_invoice']->value->total_discount_tax_incl)),$_smarty_tpl);?>
</td>
				</tr>
				<?php }?>

				<?php if (($_smarty_tpl->tpl_vars['order_invoice']->value->total_paid_tax_incl-$_smarty_tpl->tpl_vars['order_invoice']->value->total_paid_tax_excl)>0) {?>
				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold"><?php echo smartyTranslate(array('s'=>'Total Tax','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="width: 17%; text-align: right;"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>($_smarty_tpl->tpl_vars['order_invoice']->value->total_paid_tax_incl-$_smarty_tpl->tpl_vars['order_invoice']->value->total_paid_tax_excl)),$_smarty_tpl);?>
</td>
				</tr>
				<?php }?>

				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold"><?php echo smartyTranslate(array('s'=>'Total','pdf'=>'true'),$_smarty_tpl);?>
</td>
					<td style="width: 17%; text-align: right;"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['order_invoice']->value->total_paid_tax_incl),$_smarty_tpl);?>
</td>
				</tr>

			</table>

		</td>
	</tr>
</table>
<!-- / PRODUCTS TAB -->

<div style="line-height: 1pt">&nbsp;</div>

<?php echo $_smarty_tpl->tpl_vars['tax_tab']->value;?>


<?php if (isset($_smarty_tpl->tpl_vars['order_invoice']->value->note)&&$_smarty_tpl->tpl_vars['order_invoice']->value->note) {?>
<div style="line-height: 1pt">&nbsp;</div>
<table style="width: 100%">
	<tr>
		<td style="width: 17%"></td>
		<td style="width: 83%"><?php echo nl2br($_smarty_tpl->tpl_vars['order_invoice']->value->note);?>
</td>
	</tr>
</table>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['HOOK_DISPLAY_PDF']->value)) {?>
<div style="line-height: 1pt">&nbsp;</div>
<table style="width: 100%">
	<tr>
		<td style="width: 17%"></td>
		<td style="width: 83%"><?php echo $_smarty_tpl->tpl_vars['HOOK_DISPLAY_PDF']->value;?>
</td>
	</tr>
</table>
<?php }?>

</div>
<?php }} ?>
