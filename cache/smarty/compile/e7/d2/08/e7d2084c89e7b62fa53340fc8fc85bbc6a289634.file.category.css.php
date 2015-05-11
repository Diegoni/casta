<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 13:29:53
         compiled from "C:\xampp2\htdocs\prestashop_1.6.0.12\prestashop\themes\default-bootstrap\css\category.css" */ ?>
<?php /*%%SmartyHeaderCode:5126550c12c13ec383-45328794%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e7d2084c89e7b62fa53340fc8fc85bbc6a289634' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\prestashop_1.6.0.12\\prestashop\\themes\\default-bootstrap\\css\\category.css',
      1 => 1424703476,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5126550c12c13ec383-45328794',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_550c12c1407905_60344350',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550c12c1407905_60344350')) {function content_550c12c1407905_60344350($_smarty_tpl) {?>/* ************************************************************************************************
								Categories Page Styles
************************************************************************************************ */
.content_scene_cat {
  border-top: 5px solid #333;
  color: #d7d7d7;
  line-height: 19px;
  margin: 0 0 26px 0; }
  .content_scene_cat .content_scene_cat_bg {
    padding: 18px 10px 10px 42px;
    background-color: #464646 !important; }
    @media (max-width: 1199px) {
      .content_scene_cat .content_scene_cat_bg {
        padding: 10px 10px 10px 15px; } }
  .content_scene_cat span.category-name {
    font: 600 42px/51px "Open Sans", sans-serif;
    color: #fff;
    margin-bottom: 12px; }
    @media (max-width: 1199px) {
      .content_scene_cat span.category-name {
        font-size: 25px;
        line-height: 30px; } }
  .content_scene_cat p {
    margin-bottom: 0; }
  .content_scene_cat a {
    color: #fff; }
    .content_scene_cat a:hover {
      text-decoration: underline; }
  .content_scene_cat .content_scene {
    color: #777; }
    .content_scene_cat .content_scene .cat_desc {
      padding-top: 20px; }
      .content_scene_cat .content_scene .cat_desc a {
        color: #777; }
        .content_scene_cat .content_scene .cat_desc a:hover {
          color: #515151; }

/* ************************************************************************************************
									Sub Categories Styles
************************************************************************************************ */
#subcategories {
  border-top: 1px solid #d6d4d4;
  padding: 15px 0 0px 0; }
  #subcategories p.subcategory-heading {
    font-weight: bold;
    color: #333;
    margin: 0 0 15px 0; }
  #subcategories ul {
    margin: 0 0 0 -20px; }
    #subcategories ul li {
      float: left;
      width: 145px;
      margin: 0 0 13px 33px;
      text-align: center;
      height: 202px; }
      #subcategories ul li .subcategory-image {
        padding: 0 0 8px 0; }
        #subcategories ul li .subcategory-image a {
          display: block;
          padding: 9px;
          border: 1px solid #d6d4d4; }
          #subcategories ul li .subcategory-image a img {
            max-width: 100%;
            vertical-align: top; }
      #subcategories ul li .subcategory-name {
        font: 600 18px/22px "Open Sans", sans-serif;
        color: #555454;
        text-transform: uppercase; }
        #subcategories ul li .subcategory-name:hover {
          color: #515151; }
      #subcategories ul li .cat_desc {
        display: none; }
      #subcategories ul li:hover .subcategory-image a {
        border: 5px solid #333;
        padding: 5px; }

/*# sourceMappingURL=category.css.map */
<?php }} ?>
