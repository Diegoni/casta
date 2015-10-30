<!-- Block myaccount module -->
	<div id="tmfooterlinks">
	<div>
    <h4><a href="{$link->getPageLink('my-account.php', true)}">{l s='My account' mod='blockmyaccountfooter'}</a></h4>
	
		<ul class="bullet">
			<li><a href="{$link->getPageLink('history.php', true)}" title="">{l s='My orders' mod='blockmyaccountfooter'}</a></li>
			{if $returnAllowed}<li><a href="{$link->getPageLink('order-follow.php', true)}" title="">{l s='My merchandise returns' mod='blockmyaccountfooter'}</a></li>{/if}
			<li><a href="{$link->getPageLink('order-slip.php', true)}" title="">{l s='My credit slips' mod='blockmyaccountfooter'}</a></li>
			<li><a href="{$link->getPageLink('addresses.php', true)}" title="">{l s='My addresses' mod='blockmyaccountfooter'}</a></li>
			<li><a href="{$link->getPageLink('identity.php', true)}" title="">{l s='My personal info' mod='blockmyaccountfooter'}</a></li>
			{if $voucherAllowed}<li><a href="{$link->getPageLink('discount.php', true)}" title="">{l s='My vouchers' mod='blockmyaccountfooter'}</a></li>{/if}
			{$HOOK_BLOCK_MY_ACCOUNT}
		</ul>

	</div>
</div>
<!-- /Block myaccount module -->
