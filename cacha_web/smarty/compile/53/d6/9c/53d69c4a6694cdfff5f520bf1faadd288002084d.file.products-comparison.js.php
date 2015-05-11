<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 13:29:59
         compiled from "C:\xampp2\htdocs\prestashop_1.6.0.12\prestashop\themes\default-bootstrap\js\modules\productcomments\js\products-comparison.js" */ ?>
<?php /*%%SmartyHeaderCode:25416550c12c7047ea9-27070040%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '53d69c4a6694cdfff5f520bf1faadd288002084d' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\prestashop_1.6.0.12\\prestashop\\themes\\default-bootstrap\\js\\modules\\productcomments\\js\\products-comparison.js',
      1 => 1424703476,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25416550c12c7047ea9-27070040',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_550c12c70578a2_55776644',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550c12c70578a2_55776644')) {function content_550c12c70578a2_55776644($_smarty_tpl) {?>/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$(document).ready(function() 
{
	$('input.star').rating();
	$('.auto-submit-star').rating();
	$('a.cluetip').cluetip({
		local:true,
		cursor: 'pointer',
		cluetipClass: 'comparison_comments',
		dropShadow: false,
		dropShadowSteps: 0,
		showTitle: false,
		tracking: true,
		sticky: false,
		mouseOutClose: true,
	    width: 450,
		fx: {             
	    	open:       'fadeIn',
	    	openSpeed:  'fast'
		}
	}).css('opacity', 0.8);

	$('.comparison_infos a').each(function(){
		var id_product_comment = parseInt($(this).data('id-product-comment'));
		if (id_product_comment)
		{
			$(this).click(function(e){
				e.preventDefault();
			});
			var htmlContent = $('#comments_' + id_product_comment).html();
			$(this).popover({
				placement : 'bottom', //placement of the popover. also can use top, bottom, left or right
				title : false, //this is the top title bar of the popover. add some basic css
				html: 'true', //needed to show html of course
				content : htmlContent  //this is the content of the html box. add the image here or anything you want really.
			});
		}
	});
});

function closeCommentForm()
{
	$('#sendComment').slideUp('fast');
	$('input#addCommentButton').fadeIn('slow');
}<?php }} ?>
