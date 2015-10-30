<div id="slide_wrap"  >
{if $params.showTitle}
    <h4>{$params.title}</h4>
{/if}  
<div class="camera_wrap {$params.skin}" id="{$hookname}"  >
    {foreach from=$images item=img name=images}
       <div data-thumb="{$thumb_uri}{$img.name}" data-src="{$image_uri}{$img.name}" data-link="{$img.link}">
            {if $img.title}                         
                <div class="camera_caption fadeFromBottom num-{$smarty.foreach.images.iteration}" >  
               
                   
                   
                    <div class="lof_camera_title " >
                        {$img.title}
                    </div>
                    <div class="lof_camara_desc">
 {if $img.price}
                        <div class="des2 ">{$img.price}</div>
                        {/if}
                        <div class="des1">  {$img.desc}<em class="item-text">&nbsp;</em></div> 
                    </div>

               
               </div>
            {/if}
        </div>
    {/foreach}    
</div>
</div>