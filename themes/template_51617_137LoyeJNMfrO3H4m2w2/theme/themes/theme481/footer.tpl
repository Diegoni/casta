
    {if !$content_only}
	   
       	
         </div>
   
					{if $page_name !== 'index'}
            			<aside  class="grid_6">
        					<div id="right_column" class="column">
								{$HOOK_RIGHT_COLUMN}
                    		</div>
						</aside>
            		{/if}
                        
       	</div>
         	</div>

                    <footer id="footer">
                            <div class="container_24 clearfix">
                                <div class="grid_24">
                                        <div class="footer-inner">
                                        {$HOOK_FOOTER}
                                        <div class="clear"></div>	
                                        <p class="row-footer">&copy; {$smarty.now|date_format:"%Y"} {l s='Powered by'  mod='blockcms'} <a href="http://www.prestashop.com">PrestaShop</a>&trade;. All rights reserved</p>
                                        </div>
                                </div>
                            </div>
        
                    </footer>            
                        
	   </div>
    {/if}         	
	</body>
</html>
