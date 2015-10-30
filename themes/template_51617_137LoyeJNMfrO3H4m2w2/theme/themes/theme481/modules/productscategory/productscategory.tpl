{if count($categoryProducts) > 0 && $categoryProducts !== false}
<div class="clearfix blockproductscategory">
	<h3>{$categoryProducts|@count} {l s='other products in the same category:' mod='productscategory'}</h3>
    
    
       		<div class = 'container2'>
			
			<div class = 'iosSlider2'>
			
				<div class = 'slider2'>

			{foreach from=$categoryProducts item='categoryProduct' name=categoryProduct}
				{if count($categoryProducts) < 6}style="width: {math equation="width / nbImages" width=94 nbImages=$categoryProducts|@count}%"{/if}
			<div id='item2'>
				<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img" title="{$categoryProduct.name|htmlspecialchars}"><img src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'carusel')}" alt="{$categoryProduct.name|htmlspecialchars}" /></a>
                <div class="clear"></div>
				<p class="product_name">
					<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" title="{$categoryProduct.name|htmlspecialchars}">{$categoryProduct.name|truncate:22:'...'|escape:'htmlall':'UTF-8'}</a>
				</p>
                 <div class="clear"></div>
				{if $ProdDisplayPrice AND $categoryProduct.show_price == 1 AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
				<p class="price_display">
					<span class="price">{convertPrice price=$categoryProduct.displayed_price}</span>
				</p>
				{else}
				<br />
				{/if}
			</div>
			{/foreach}
    	</div>	
        
        </div>
        		<div class ='next'></div>
	<div class ='prev unselectable'></div>
        
        	</div>
    
    
	<script type="text/javascript">
				$(document).ready(function() {
				
				$('.iosSlider2').iosSlider({
					snapToChildren: true,
					desktopClickDrag: true,
					keyboardControls: true,
					infiniteSlider: true,
					onSliderLoaded: sliderTest,
					onSlideStart: sliderTest,
					onSlideComplete: slideComplete,
					navNextSelector: $('.next'),
				    navPrevSelector: $('.prev'),
				});
				
			});
			
			function sliderTest(args) {
				console.log(args.targetSliderOffset);
			}
			
			function slideComplete(args) {
			
				$('.next, .prev').removeClass('unselectable');
				
			    if(args.currentSlideNumber == 0) {
			
			        $('.prev').addClass('unselectable');
			
			    } else if(args.currentSliderOffset == args.data.sliderMax) {
			
			        $('.next').addClass('unselectable');
			
			    }
			
			}
	</script>
</div>
{/if}
