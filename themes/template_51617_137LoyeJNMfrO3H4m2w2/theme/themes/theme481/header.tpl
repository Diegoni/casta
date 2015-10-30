<!doctype html>
<!--[if lt IE 7 ]> <html class="ie ie6 no-js" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie ie7 no-js" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie ie8 no-js" lang="en"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie ie9 no-js" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" lang="{$lang_iso}"><!--<![endif]--><head>
	<meta charset="utf-8">
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">	
                    <title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
                        
			
             		  				<!--  META   -->  
                                                    
		{if isset($meta_description) AND $meta_description}
			
		<meta name="description" content="{$meta_description|escape:html:'UTF-8'}" />
		{/if}
		{if isset($meta_keywords) AND $meta_keywords}
			<meta name="keywords" content="{$meta_keywords|escape:html:'UTF-8'}" />
		{/if}
        
	<meta name="generator" content="PrestaShop" />
    <meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
	<meta name="author" content="Prestashop 1.5">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 
    <meta name="apple-mobile-web-app-capable" content="yes" /> 
         
							 <!-- LINK - STYLE - GOGLEFONT -->  

                                                                          
        <link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />
		<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
        <link href='http://fonts.googleapis.com/css?family=Playfair+Display' rel='stylesheet' type='text/css' />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css' />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
        <link href="{$css_dir}normalize.css" rel="stylesheet" type="text/css" media="{$media}" />


       					      <!-- FOREACH JS - STYLE -->

                                  
		<script type="text/javascript">
			var baseDir = '{$content_dir}';
			var baseUri = '{$base_uri}';
			var static_token = '{$static_token}';
			var token = '{$token}';
			var priceDisplayPrecision = {$priceDisplayPrecision*$currency->decimals};
			var priceDisplayMethod = {$priceDisplay};
			var roundMode = {$roundMode};
		</script>    

{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}
	<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
	{/foreach}
{/if}
{if isset($js_files)}
	{foreach from=$js_files item=js_uri}
	<script type="text/javascript" src="{$js_uri}"></script>
	{/foreach}
{/if}       		


 


 		
       							<!-- MAIN-STYLE-PRESTASHOP -->
                                
        <link href="{$css_dir}320.css" rel="stylesheet" type="text/css" media="all" />
        <link href="{$css_dir}480.css" rel="stylesheet" type="text/css" media="all" />
        <link href="{$css_dir}768.css" rel="stylesheet" type="text/css" media="all" />
		<link href="{$css_dir}grid_prestashop.css" rel="stylesheet" type="text/css" media="all" />	
        		    
        <script type="text/javascript" src="{$js_dir}jquery.easing.1.3.js"></script> 
        <script type="text/javascript" src="{$js_dir}cookies.js"></script> 
        <script type="text/javascript" src="{$js_dir}plugintheme.js"></script>
        <script type="text/javascript" src="{$js_dir}jquery.iosslider.min.js"></script> 
		<script type="text/javascript" src="{$js_dir}script.js"></script>        

		<script type="text/javascript" src="{$js_dir}modernizr-2.5.3.min.js"></script> 	
        <!--[if (gt IE 9)|!(IE)]><!-->
		<script type="text/javascript" src="{$js_dir}jquery.mobile.customized.min.js"></script>
		<!--<![endif]-->

        
        
        					                  	
		<!--[if lt IE 8]>
			<div style=' clear: both; text-align:center; position: relative;'>
  				<a href="http://www.microsoft.com/windows/internet-explorer/default.aspx?ocid=ie6_countdown_bannercode">
                	<img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" alt="" />
                </a>
			</div>
		<![endif]-->  
<!--[if IE]> 
<style>
.checkbox input,
.radio input,
input[type="checkbox"],
input[type="radio"] { border:none!important; border-radius:2px; width:13px; height:13px;}
</style>
<![endif]-->
                      {$HOOK_HEADER}
	</head>
	
	<body {if isset($page_name)}id="{$page_name|escape:'htmlall':'UTF-8'}"{/if} class="{if $hide_left_column}hide-left-column{/if} {if $hide_right_column}hide-right-column{/if}">
	{if !$content_only}
		{if isset($restricted_country_mode) && $restricted_country_mode}
		<div id="restricted-country">
			<p>{l s='You cannot place a new order from your country.'} <span class="bold">{$geolocation_country}</span></p>
		</div>
		{/if}

                       
         		 <div id="main-wrap"> 
                        
                        

                        <header id="header"> 
                            <div class="container_24 clearfix">
                                <div class="grid_24">
                                    <div id="header-inner">
                                        <a id="header_logo" href="{$base_dir}" title="{$shop_name|escape:'htmlall':'UTF-8'}"><img class="logo" src="{$img_ps_dir}logo.jpg?{$img_update_time}" alt="{$shop_name|escape:'htmlall':'UTF-8'}" /></a>{$HOOK_TOP}</div></div>
                            </div>
                       </header>

              	<!-- ROW-CONTANT  --> 
							<div class="bg-content">
								<div id="columns" class="container_24 primary_content_wrap clearfix">

 													{if $page_name == 'index'}
   														 <div id="center_column" class="center_column grid_24">
													{/if}
              									  	<!-- Left -->
			
                                                  	<!-- ALLPAGE-CENTER_COLUMN -->
   			    									{if $page_name !== 'index'}
														<div id="center_column" class="center_column grid_18">
													{/if}   
                                                       
	{/if}
