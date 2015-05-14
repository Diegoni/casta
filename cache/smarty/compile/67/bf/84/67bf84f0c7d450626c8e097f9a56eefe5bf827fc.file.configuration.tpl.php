<?php /* Smarty version Smarty-3.1.19, created on 2015-05-13 09:35:48
         compiled from "C:\xampp2\htdocs\casta\modules\authorizeaim\views\templates\admin\configuration.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6345553452463b473-99150410%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '67bf84f0c7d450626c8e097f9a56eefe5bf827fc' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\casta\\modules\\authorizeaim\\views\\templates\\admin\\configuration.tpl',
      1 => 1431520527,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6345553452463b473-99150410',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'module_dir' => 0,
    'currencies' => 0,
    'currency' => 0,
    'available_currencies' => 0,
    'configuration_id_name' => 0,
    '($_smarty_tpl->tpl_vars[\'configuration_id_name\']->value)' => 0,
    'configuration_key_name' => 0,
    '($_smarty_tpl->tpl_vars[\'configuration_key_name\']->value)' => 0,
    'AUTHORIZE_AIM_SANDBOX' => 0,
    'AUTHORIZE_AIM_TEST_MODE' => 0,
    'AUTHORIZE_AIM_CARD_VISA' => 0,
    'AUTHORIZE_AIM_CARD_MASTERCARD' => 0,
    'AUTHORIZE_AIM_CARD_DISCOVER' => 0,
    'AUTHORIZE_AIM_CARD_AX' => 0,
    'order_states' => 0,
    'os' => 0,
    'AUTHORIZE_AIM_HOLD_REVIEW_OS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_555345248100d5_47461541',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_555345248100d5_47461541')) {function content_555345248100d5_47461541($_smarty_tpl) {?><div class="authorizeaim-wrapper">
<a href="http://reseller.authorize.net/application/prestashop/" class="authorizeaim-logo" target="_blank"><img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
img/logo_authorize.png" alt="Authorize.net" border="0" /></a>
<p class="authorizeaim-intro"><?php echo smartyTranslate(array('s'=>'Start accepting payments through your PrestaShop store with Authorize.Net, the pioneering provider of ecommerce payment services.  Authorize.Net makes accepting payments safe, easy and affordable.','mod'=>'authorizeaim'),$_smarty_tpl);?>
</p>
<p class="authorizeaim-sign-up"><?php echo smartyTranslate(array('s'=>'Do you require a payment gateway account? ','mod'=>'authorizeaim'),$_smarty_tpl);?>
<a href="http://reseller.authorize.net/application/prestashop/" target="_blank"><?php echo smartyTranslate(array('s'=>'Sign Up Now','mod'=>'authorizeaim'),$_smarty_tpl);?>
</a></p>
<div class="authorizeaim-content">
	<div class="authorizeaim-leftCol">
		<h3><?php echo smartyTranslate(array('s'=>'Why Choose Authorize.Net?','mod'=>'authorizeaim'),$_smarty_tpl);?>
</h3>
		<ul>
			<li><?php echo smartyTranslate(array('s'=>'Leading payment gateway since 1996 with 400,000+ active merchants','mod'=>'authorizeaim'),$_smarty_tpl);?>
</li>
			<li><?php echo smartyTranslate(array('s'=>'Multiple currency acceptance','mod'=>'authorizeaim'),$_smarty_tpl);?>
</li>
			<li><?php echo smartyTranslate(array('s'=>'FREE award-winning customer support via telephone, email and online chat','mod'=>'authorizeaim'),$_smarty_tpl);?>
</li>
			<li><?php echo smartyTranslate(array('s'=>'FREE Virtual Terminal for mail order/telephone order transactions','mod'=>'authorizeaim'),$_smarty_tpl);?>
</li>
			<li><?php echo smartyTranslate(array('s'=>'No Contracts or long term commitments ','mod'=>'authorizeaim'),$_smarty_tpl);?>
</li>
			<li><?php echo smartyTranslate(array('s'=>'Additional services include: ','mod'=>'authorizeaim'),$_smarty_tpl);?>

				<ul class="none">
					<li><?php echo smartyTranslate(array('s'=>'- Advanced Fraud Detection Suite™','mod'=>'authorizeaim'),$_smarty_tpl);?>
</li>
					<li><?php echo smartyTranslate(array('s'=>'- Automated Recurring Billing ™','mod'=>'authorizeaim'),$_smarty_tpl);?>
</li>
					<li><?php echo smartyTranslate(array('s'=>'- Customer Information Manager','mod'=>'authorizeaim'),$_smarty_tpl);?>
</li>
				</ul>
			</li>
			<li><?php echo smartyTranslate(array('s'=>'Gateway and merchant account set up available','mod'=>'authorizeaim'),$_smarty_tpl);?>
</li>
			<li><?php echo smartyTranslate(array('s'=>'Simple setup process','mod'=>'authorizeaim'),$_smarty_tpl);?>

		</li>
		</ul>
		<ul class="none" style = "display: inline; font-size: 13px;">
			<li><a href="http://reseller.authorize.net/application/prestashop/" target="_blank" class="authorizeaim-link"><?php echo smartyTranslate(array('s'=>'Sign up Now','mod'=>'authorizeaim'),$_smarty_tpl);?>
</a></li>
		</ul>
	</div>
	<div class="authorizeaim-video">
		<p><?php echo smartyTranslate(array('s'=>'Have you ever wondered how credit card payments work? Connecting a payment application to the credit card processing networks is difficult, expensive and beyond the resources of most businesses. Authorize.Net provides the complex infrastructure and security necessary to ensure secure, fast and reliable transactions. See How:','mod'=>'authorizeaim'),$_smarty_tpl);?>
</p>
		<a href="http://www.youtube.com/watch?v=8SQ3qst0_Pk" class="authorizeaim-video-btn">
			<img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
img/video-screen.jpg" alt="Merchant Warehouse screencast" />
			<img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
img/btn-video.png" alt="" class="video-icon" />
		</a>
	</div>
</div>

<form action="<?php echo mb_convert_encoding(htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" method="post">
	<fieldset>
		<legend><?php echo smartyTranslate(array('s'=>'Configure your existing Authorize.Net Accounts','mod'=>'authorizeaim'),$_smarty_tpl);?>
</legend>

		
		<?php  $_smarty_tpl->tpl_vars['currency'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['currency']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['currency']->key => $_smarty_tpl->tpl_vars['currency']->value) {
$_smarty_tpl->tpl_vars['currency']->_loop = true;
?>
			<?php if ((in_array($_smarty_tpl->tpl_vars['currency']->value['iso_code'],$_smarty_tpl->tpl_vars['available_currencies']->value))) {?>
				<?php $_smarty_tpl->tpl_vars['configuration_id_name'] = new Smarty_variable(("AUTHORIZE_AIM_LOGIN_ID_").($_smarty_tpl->tpl_vars['currency']->value['iso_code']), null, 0);?>
				<?php $_smarty_tpl->tpl_vars['configuration_key_name'] = new Smarty_variable(("AUTHORIZE_AIM_KEY_").($_smarty_tpl->tpl_vars['currency']->value['iso_code']), null, 0);?>
				<table>
					<tr>
						<td>
							<p><?php echo smartyTranslate(array('s'=>'Credentials for','mod'=>'authorizeaim'),$_smarty_tpl);?>
<b> <?php echo $_smarty_tpl->tpl_vars['currency']->value['iso_code'];?>
</b> <?php echo smartyTranslate(array('s'=>'currency','mod'=>'authorizeaim'),$_smarty_tpl);?>
</p>
							<label for="authorizeaim_login_id"><?php echo smartyTranslate(array('s'=>'Login ID','mod'=>'authorizeaim'),$_smarty_tpl);?>
:</label>
							<div class="margin-form" style="margin-bottom: 0px;"><input type="text" size="20" id="authorizeaim_login_id_<?php echo $_smarty_tpl->tpl_vars['currency']->value['iso_code'];?>
" name="authorizeaim_login_id_<?php echo $_smarty_tpl->tpl_vars['currency']->value['iso_code'];?>
" value="<?php echo $_smarty_tpl->tpl_vars[($_smarty_tpl->tpl_vars['configuration_id_name']->value)]->value;?>
" /></div>
							<label for="authorizeaim_key"><?php echo smartyTranslate(array('s'=>'Key','mod'=>'authorizeaim'),$_smarty_tpl);?>
:</label>
							<div class="margin-form" style="margin-bottom: 0px;"><input type="text" size="20" id="authorizeaim_key_<?php echo $_smarty_tpl->tpl_vars['currency']->value['iso_code'];?>
" name="authorizeaim_key_<?php echo $_smarty_tpl->tpl_vars['currency']->value['iso_code'];?>
" value="<?php echo $_smarty_tpl->tpl_vars[($_smarty_tpl->tpl_vars['configuration_key_name']->value)]->value;?>
" /></div>
						</td>
					</tr>
				</table><br />
				<hr size="1" style="background: #BBB; margin: 0; height: 1px;" noshade /><br />
			<?php }?>
		<?php } ?>

		<label for="authorizeaim_mode"><a class="authorizeaim-sign-up" target="_blank" href="https://developer.authorize.net/guides/AIM/wwhelp/wwhimpl/js/html/wwhelp.htm"><img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
img/help.png" alt="" /></a> <?php echo smartyTranslate(array('s'=>'Environment:','mod'=>'authorizeaim'),$_smarty_tpl);?>
</label>
		<div class="margin-form" id="authorizeaim_mode">
			<input type="radio" name="authorizeaim_mode" value="0" style="vertical-align: middle;" <?php if (!$_smarty_tpl->tpl_vars['AUTHORIZE_AIM_SANDBOX']->value&&!$_smarty_tpl->tpl_vars['AUTHORIZE_AIM_TEST_MODE']->value) {?>checked="checked"<?php }?> />
			<span><?php echo smartyTranslate(array('s'=>'Live mode','mod'=>'authorizeaim'),$_smarty_tpl);?>
</span><br/>
			<input type="radio" name="authorizeaim_mode" value="1" style="vertical-align: middle;" <?php if (!$_smarty_tpl->tpl_vars['AUTHORIZE_AIM_SANDBOX']->value&&$_smarty_tpl->tpl_vars['AUTHORIZE_AIM_TEST_MODE']->value) {?>checked="checked"<?php }?> />
			<span><?php echo smartyTranslate(array('s'=>'Test mode (in production server)','mod'=>'authorizeaim'),$_smarty_tpl);?>
</span><br/>
			<input type="radio" name="authorizeaim_mode" value="2" style="vertical-align: middle;" <?php if ($_smarty_tpl->tpl_vars['AUTHORIZE_AIM_SANDBOX']->value) {?>checked="checked"<?php }?> />
			<span><?php echo smartyTranslate(array('s'=>'Test mode','mod'=>'authorizeaim'),$_smarty_tpl);?>
</span><br/>
		</div>
		<label for="authorizeaim_cards"><?php echo smartyTranslate(array('s'=>'Cards* :','mod'=>'authorizeaim'),$_smarty_tpl);?>
</label>
		<div class="margin-form" id="authorizeaim_cards">
			<input type="checkbox" name="authorizeaim_card_visa" <?php if ($_smarty_tpl->tpl_vars['AUTHORIZE_AIM_CARD_VISA']->value) {?>checked="checked"<?php }?> />
				<img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
/cards/visa.gif" alt="visa" />
			<input type="checkbox" name="authorizeaim_card_mastercard" <?php if ($_smarty_tpl->tpl_vars['AUTHORIZE_AIM_CARD_MASTERCARD']->value) {?>checked="checked"<?php }?> />
				<img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
/cards/mastercard.gif" alt="visa" />
			<input type="checkbox" name="authorizeaim_card_discover" <?php if ($_smarty_tpl->tpl_vars['AUTHORIZE_AIM_CARD_DISCOVER']->value) {?>checked="checked"<?php }?> />
				<img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
/cards/discover.gif" alt="visa" />
			<input type="checkbox" name="authorizeaim_card_ax" <?php if ($_smarty_tpl->tpl_vars['AUTHORIZE_AIM_CARD_AX']->value) {?>checked="checked"<?php }?> />
				<img src="<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
/cards/ax.gif" alt="visa" />
		</div>

		<label for="authorizeaim_hold_review_os"><?php echo smartyTranslate(array('s'=>'Order status:  "Hold for Review" ','mod'=>'authorizeaim'),$_smarty_tpl);?>
</label>
		<div class="margin-form">
			<select id="authorizeaim_hold_review_os" name="authorizeaim_hold_review_os">';
				// Hold for Review order state selection
				<?php  $_smarty_tpl->tpl_vars['os'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['os']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order_states']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['os']->key => $_smarty_tpl->tpl_vars['os']->value) {
$_smarty_tpl->tpl_vars['os']->_loop = true;
?>
					<option value="<?php if (intval($_smarty_tpl->tpl_vars['os']->value['id_order_state'])) {?>" <?php echo ((int)$_smarty_tpl->tpl_vars['os']->value['id_order_state']==$_smarty_tpl->tpl_vars['AUTHORIZE_AIM_HOLD_REVIEW_OS']->value);?>
 selected<?php }?>>
						<?php echo stripslashes($_smarty_tpl->tpl_vars['os']->value['name']);?>

					</option>
				<?php } ?>
			</select>
		</div>
		<br />
		<center>
			<input type="submit" name="submitModule" value="<?php echo smartyTranslate(array('s'=>'Update settings','mod'=>'authorizeaim'),$_smarty_tpl);?>
" class="button" />
		</center>
		<sub><?php echo smartyTranslate(array('s'=>'* Subject to region','mod'=>'authorizeaim'),$_smarty_tpl);?>
</sub>
	</fieldset>
</form>
</div>
<?php }} ?>
