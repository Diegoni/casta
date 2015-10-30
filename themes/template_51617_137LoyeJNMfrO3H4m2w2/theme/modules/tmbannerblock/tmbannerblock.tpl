<!-- Module JM Slideshow -->
{if $page_name == "index"}
{literal}
<div id="banner-1" class="jms-slideshow">
    {/literal}
    {foreach from=$xml->item item=my_item name=loop}
        {literal}        
           <div class="step no-modile num-{/literal}{$smarty.foreach.loop.iteration}{literal}">   <a class="jms-link" href="{/literal}{$my_item->url}{literal}">  
            <div class="jms-content">
                    <h3>{/literal}{$my_item->$title|truncate:127:'...'}{literal}</h3>
                    <p>{/literal}{$my_item->$text|truncate:127:'...'}{literal}</p>
                   
                </div>
                <img src='{/literal}{$module_dir}{$my_item->img}{literal}' alt="" title="{/literal}{$my_item->$title|truncate:127:'...'}{literal}" /> </a>
            </div>
                        
        {/literal}
    {/foreach}{literal}
</div>

<div id="banner-2" class="jms-slideshow">
    {/literal}
    {foreach from=$xml->item item=my_item name=loop}
        {literal}        
           <div class="step modile-1 num-{/literal}{$smarty.foreach.loop.iteration}{literal}">   <a class="jms-link" href="{/literal}{$my_item->url}{literal}">  
            <div class="jms-content">
                    <h3>{/literal}{$my_item->$title|truncate:127:'...'}{literal}</h3>
                    <p>{/literal}{$my_item->$text|truncate:127:'...'}{literal}</p>
                </div>
                <img src='{/literal}{$module_dir}{$my_item->img}{literal}' width="246" height="113" alt="" title="{/literal}{$my_item->$title|truncate:127:'...'}{literal}" /> </a>
            </div>
                        
        {/literal}
    {/foreach}{literal}
</div>

<div id="banner-3" class="jms-slideshow">
    {/literal}
    {foreach from=$xml->item item=my_item name=loop}
        {literal}        
           <div class="step modile-2 num-{/literal}{$smarty.foreach.loop.iteration}{literal}">   <a class="jms-link" href="{/literal}{$my_item->url}{literal}">  
            <div class="jms-content">
                    <h3>{/literal}{$my_item->$title|truncate:127:'...'}{literal}</h3>
                    <p>{/literal}{$my_item->$text|truncate:127:'...'}{literal}</p>
                </div>
                <img src='{/literal}{$module_dir}{$my_item->img}{literal}' width="300" height="138" alt="" title="{/literal}{$my_item->$title|truncate:127:'...'}{literal}" /> </a>
            </div>
                        
        {/literal}
    {/foreach}{literal}
</div>
<div id="banner-4" class="jms-slideshow">
    {/literal}
    {foreach from=$xml->item item=my_item name=loop}
        {literal}        
           <div class="step no-modile num-{/literal}{$smarty.foreach.loop.iteration}{literal}">   <a class="jms-link" href="{/literal}{$my_item->url}{literal}">  
            <div class="jms-content">
                    <h3>{/literal}{$my_item->$title|truncate:127:'...'}{literal}</h3>
                    <p>{/literal}{$my_item->$text|truncate:127:'...'}{literal}</p>
                </div>
                <img src='{/literal}{$module_dir}{$my_item->img}{literal}' width="310" height="143" alt="" title="{/literal}{$my_item->$title|truncate:127:'...'}{literal}" /> </a>
            </div>
                        
        {/literal}
    {/foreach}{literal}
</div>

{/literal}
{/if}
<!-- /Module JM Slideshow -->