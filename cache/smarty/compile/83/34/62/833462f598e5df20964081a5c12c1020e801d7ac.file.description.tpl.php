<?php /* Smarty version Smarty-3.1.19, created on 2015-04-29 12:57:27
         compiled from "C:\xampp2\htdocs\prestashop\modules\erpillicopresta\views\templates\admin\configuration\description.tpl" */ ?>
<?php /*%%SmartyHeaderCode:70645540ff6749db75-02583126%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '833462f598e5df20964081a5c12c1020e801d7ac' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\prestashop\\modules\\erpillicopresta\\views\\templates\\admin\\configuration\\description.tpl',
      1 => 1430323017,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '70645540ff6749db75-02583126',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'erp_iso_code' => 0,
    'blockLicence' => 0,
    'isDevelopper' => 0,
    'forms' => 0,
    'unique_form' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5540ff67666c61_84016765',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5540ff67666c61_84016765')) {function content_5540ff67666c61_84016765($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_replace')) include 'C:\\xampp2\\htdocs\\prestashop\\tools\\smarty\\plugins\\modifier.replace.php';
?>


<!-- DESCRIPTION BLOC -->

<div class="erp-configuration-page prestashop_<?php echo smarty_modifier_replace(substr(@constant('_PS_VERSION_'),0,3),'.','_');?>
">

    <div class="row header">
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" id="logoLeft">
            <img width="100%" src="../modules/erpillicopresta/img/1click_en.png" alt="<?php echo smartyTranslate(array('s'=>'1 Click ERP','mod'=>'erpillicopresta'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'1 Click ERP','mod'=>'erpillicopresta'),$_smarty_tpl);?>
" />
        </div>

        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
            <h1> <?php echo smartyTranslate(array('s'=>'The first advanced-version Prestashop ERP available for free.','mod'=>'erpillicopresta'),$_smarty_tpl);?>
  </h1>
            <h2><?php echo smartyTranslate(array('s'=>'Your personalised store management accelerator.','mod'=>'erpillicopresta'),$_smarty_tpl);?>
 </h2> 
        </div>

        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12" id="logoRight">            
            <img src="../modules/erpillicopresta/img/certified.png" alt="<?php echo smartyTranslate(array('s'=>'Certified by prestashop','mod'=>'erpillicopresta'),$_smarty_tpl);?>
" title="<?php echo smartyTranslate(array('s'=>'Certified by prestashop','mod'=>'erpillicopresta'),$_smarty_tpl);?>
" />
        </div>
    </div>

    <br />

    <div class="row bloks">

        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">

            <h2 class="configuration_heading"><?php echo smartyTranslate(array('s'=>'Time-saving module','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</h2>

            <div class="blok_content first">

                <p>
                    <span class="bold"><?php echo smartyTranslate(array('s'=>'Save up to 2h per day*','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</span>
                </p>
           
                <h4 class="red">
                    <?php echo smartyTranslate(array('s'=>'6 functional areas','mod'=>'erpillicopresta'),$_smarty_tpl);?>
 <br/>
                    <?php echo smartyTranslate(array('s'=>'30 features','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                </h4>
            
                <div class="row">
                    
                        <ul class="features_list col-lg-9 col-lg-offset-3 col-md-8 col-md-offset-4 col-sm-9 col-sm-offset-3 col-xs-11 col-xs-offset-1">
                            <li>
                                - <?php echo smartyTranslate(array('s'=>'Optimise the management of','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                                <span class="bold"><?php echo smartyTranslate(array('s'=>'client orders','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</span>
                            </li>
                            <li>
                                - <?php echo smartyTranslate(array('s'=>'Improve','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                                <span class="bold"><?php echo smartyTranslate(array('s'=>'supplier sheet data','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</span>
                            </li>
                            <li>
                                - <?php echo smartyTranslate(array('s'=>'Facilitate','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                                <span class="bold"><?php echo smartyTranslate(array('s'=>'ordering from suppliers','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</span>
                            </li>
                            <li>
                                - <?php echo smartyTranslate(array('s'=>'Manage your','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                                <span class="bold"><?php echo smartyTranslate(array('s'=>'stock','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</span>
                                <?php echo smartyTranslate(array('s'=>'efficiently','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                            </li>
                            <li>
                                - <?php echo smartyTranslate(array('s'=>'Manage','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                                <span class="bold"><?php echo smartyTranslate(array('s'=>'inventories online and offline','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</span>
                            </li>
                            <li>
                                - <?php echo smartyTranslate(array('s'=>'Restock','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                                <span class="bold"><?php echo smartyTranslate(array('s'=>'automatically','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</span>
                                <?php echo smartyTranslate(array('s'=>' ','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                            </li>
                        </ul>
                  
                </div>

                <br/>
                
                <p><?php echo smartyTranslate(array('s'=>'Compatible with Prestashop 1.5 et 1.6','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</p>

            </div>

        </div>

        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">

            <h2 class="configuration_heading"><?php echo smartyTranslate(array('s'=>'A personalised module','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</h2>

            <div class="row blok_content">
                <div>
                    
                    <h4 class="red">
                        <?php echo smartyTranslate(array('s'=>'The 1ST free, adaptable ERP','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                    </h4>

                    <div class="version">   
                        <p>
                            <span class="bold">
                                <?php echo smartyTranslate(array('s'=>'Free version','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                            </span>
                            <?php echo smartyTranslate(array('s'=>' > Activate 1-Click ERP','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                        </p>
                        
                        <br/>
                        
                        <p class="row">
                            <span class="bold col-lg-2">
                                <?php echo smartyTranslate(array('s'=>'Advanced version','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                            </span>
                            <span class="col-lg-10">
                                <?php echo smartyTranslate(array('s'=>' > Available for purchase or as a subscription**','mod'=>'erpillicopresta'),$_smarty_tpl);?>
   <br/>
                                <?php echo smartyTranslate(array('s'=>' > A number of packs are available to adapt the module to your needs','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                            </span>
                        </p>
                        
                        <p></p>
                    </div>
                    
                </div>
                            
                <div>
                    <a href="<?php if ($_smarty_tpl->tpl_vars['erp_iso_code']->value=='fr') {?> <?php echo @constant('ERP_URL_VIDEO_FR');?>
 <?php } else { ?> <?php echo @constant('ERP_URL_VIDEO_EN');?>
 <?php }?>" target="_blank" id="video_link">
                        <?php echo smartyTranslate(array('s'=>'Read more','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                    </a>
                </div>
                
                <div class="row blok_button">
                    <div class="col-lg-4 col-lg-offset-2 button1 col-md-3 col-md-offset-3 col-sm-4 col-sm-offset-2 col-xs-12">
                        <a href="<?php if ($_smarty_tpl->tpl_vars['erp_iso_code']->value=='fr') {?> <?php echo @constant('ERP_URL_DOC_FR');?>
 <?php } else { ?> <?php echo @constant('ERP_URL_DOC_EN');?>
 <?php }?>" target="_blank"><?php echo smartyTranslate(array('s'=>'Download documentation','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</a>
                    </div>
                    
                    <div class="visible-xs"> &nbsp; </div>
                    
                    <div class="col-lg-4 button2 col-md-3 col-sm-4 col-xs-12">
                       <a href="<?php if ($_smarty_tpl->tpl_vars['erp_iso_code']->value=='fr') {?> <?php echo @constant('ERP_URL_CONTACT_FR');?>
 <?php } else { ?> <?php echo @constant('ERP_URL_CONTACT_EN');?>
 <?php }?>" target="_blank"><?php echo smartyTranslate(array('s'=>'Contact us','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</a>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <br/>
    
    <p>
        <?php echo smartyTranslate(array('s'=>'*Amount of time saved is an estimate by the merchants who use 1-Click ERP.','mod'=>'erpillicopresta'),$_smarty_tpl);?>

        <?php echo smartyTranslate(array('s'=>'**Subscription without any duration obligation. Cancellation is possible at any time.','mod'=>'erpillicopresta'),$_smarty_tpl);?>

    </p>
    <br/>

    
    <?php if (Configuration::get('ERP_LICENCE_INSTALL_ERROR')=='0'||$_smarty_tpl->tpl_vars['blockLicence']->value==false) {?>
    
        <div class="row bloks blok_licence">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h2 class="configuration_heading red">

                    <?php if (Configuration::get('ERP_LICENCE_VALIDITY')=='0') {?>
                        <?php echo smartyTranslate(array('s'=>'Activation form','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                        <span><?php echo smartyTranslate(array('s'=>'For any free or paid activation, you must first fill out the form below','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</span>
                    <?php } else { ?>
                        <?php echo smartyTranslate(array('s'=>'Activation information','mod'=>'erpillicopresta'),$_smarty_tpl);?>

                    <?php }?>
                </h2>
            </div>
        </div>
        
        
        <?php if ($_smarty_tpl->tpl_vars['isDevelopper']->value&&Configuration::get('ERP_LICENCE_VALIDITY')=='0'&&Configuration::get('ERP_NEW_LICENCE')!='') {?>
            
            <div class="<?php ob_start();?><?php echo smarty_modifier_replace(substr(@constant('_PS_VERSION_'),0,3),'.','_');?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1=='1_6') {?>alert alert-info<?php } else { ?>hint clear<?php }?>" style="display: block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <ul id="infos_block" class="list-unstyled">
                    <li><b><?php echo smartyTranslate(array('s'=>'Carefull your license would be attached to non routable IP','mod'=>'erpillicopresta'),$_smarty_tpl);?>
 <?php echo Configuration::get('PS_SHOP_DOMAIN');?>
</b>.<br></li>
                    <li><b><?php echo smartyTranslate(array('s'=>'When your store would be launched in production, you will have to process to a free migration of your license.','mod'=>'erpillicopresta'),$_smarty_tpl);?>
</b><br></li>
                </ul>
            </div>
        
            <br/>
        
        <?php }?>
    
       
        <?php if (isset($_smarty_tpl->tpl_vars['forms']->value)&&!empty($_smarty_tpl->tpl_vars['forms']->value)) {?>
            <?php  $_smarty_tpl->tpl_vars['unique_form'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['unique_form']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['forms']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['unique_form']->key => $_smarty_tpl->tpl_vars['unique_form']->value) {
$_smarty_tpl->tpl_vars['unique_form']->_loop = true;
?>
                <?php echo $_smarty_tpl->tpl_vars['unique_form']->value;?>

            <?php } ?>
        <?php }?>
    <?php }?>

</div>

<!-- END DESCRIPTION BLOC --><?php }} ?>
