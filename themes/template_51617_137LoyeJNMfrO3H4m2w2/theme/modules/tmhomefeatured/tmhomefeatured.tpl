<!-- MODULE Home Featured Products -->
<div id="featured_products">
  <h4>{l s='Featured products' mod='tmhomefeatured'}</h4>
  {if isset($products) AND $products}
  <div class="block_content"> 
  	{assign var='liHeight' value=250}
    {assign var='nbItemsPerLine' value=4}
    {assign var='nbLi' value=$products|@count}
    {math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
    {math equation="nbLines*liHeight" nbLines=$nbLines|ceil liHeight=$liHeight assign=ulHeight}
    
    <ul>
      {foreach from=$products item=product name=homeFeaturedProducts}
      <li class="ajax_block_product  {if $smarty.foreach.homeFeaturedProducts.iteration is div by 4} omega{/if} num-{$smarty.foreach.homeFeaturedProducts.iteration}{if $smarty.foreach.homeFeaturedProducts.iteration == 1 or $smarty.foreach.homeFeaturedProducts.iteration == 5 or $smarty.foreach.homeFeaturedProducts.iteration == 9} alpha{/if}">
        <div class="featured_li"> 
        <a class="product_image" href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}"> 
            <img  src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home')}" alt="{$product.name|escape:html:'UTF-8'}" class="vky"/> 
                {foreach from=$addimages key=k item=v}
                    {if $k==$product.id_product} 
                    <img   src="{$link->getImageLink($product.link_rewrite,$product.id_product|cat:"-"|cat:$v, 'home')}"  alt="{$product.legend|escape:htmlall:'UTF-8'}" style="display:none;"  class="v" /> 
                    {/if}
                {/foreach} 
        </a> 
            <div>
                <h5><a class="product_link" href="{$product.link}" title="{$product.name|truncate:32:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:25:'...'|escape:'htmlall':'UTF-8'}</a></h5>
                <p class="product_desc"><a class="product_descr" href="{$product.link}" title="{l s='More' mod='tmhomefeatured'}">{$product.description_short|strip_tags|truncate:65:'...'}</a></p>            
                <p class=" mob"><a class="product_descr" href="{$product.link}" title="{l s='More' mod='tmhomefeatured'}">{$product.description_short|strip_tags|truncate:155:'...'}</a></p>           
                <p class=" mob-2"><a class="product_descr" href="{$product.link}" title="{l s='More' mod='tmhomefeatured'}">{$product.description_short|strip_tags|truncate:80:'...'}</a></p>      
                {if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
                <span class="price">
                {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                </span>{/if}  
					<div class="clear"></div>
                    
           	{if ($product.id_product_attribute == 0 OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product.available_for_order AND !isset($restricted_country_mode) 
            AND $product.minimal_quantity == 1 AND $product.customizable != 2 AND !$PS_CATALOG_MODE}
            {if ($product.quantity > 0 OR $product.allow_oosp)} 
                <a class="exclusive ajax_add_to_cart_button" rel="ajax_id_product_{$product.id_product}" href="{$link->getPageLink('cart.php')}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add" title="{l s='Add to cart' mod='tmhomefeatured'}">{l s='Add to cart' mod='tmhomefeatured'}
                </a> 
            {else} 
          	  <span class="exclusive">{l s='Add to cart' mod='tmhomefeatured'}</span> {/if}
            {/if} 
            	<a class="button" href="{$product.link}" title="{l s='View' mod='tmhomefeatured'}">{l s='View' mod='tmhomefeatured'}</a>
                
            </div>
        </div>
      </li>
      {/foreach}
    </ul>
  </div>
  {else}
 	 <p>{l s='No featured products' mod='tmhomefeatured'}</p>
  {/if} 
 </div>
<!-- /MODULE Home Featured Products -->