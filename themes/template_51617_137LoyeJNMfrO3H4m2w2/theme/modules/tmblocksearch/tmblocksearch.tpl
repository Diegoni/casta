

<!-- Block search module -->
<div id="search_block_left" class="block exclusive">
	<h4>{l s='Search' mod='tmblocksearch'}</h4>
	<form method="get" action="{$link->getPageLink('search', true)}" id="searchbox">
		<p class="block_content">
			<label for="search_query_block">{l s='Enter a product name' mod='tmblocksearch'}</label>
			<input type="hidden" name="orderby" value="position" />
			<input type="hidden" name="controller" value="search" />
			<input type="hidden" name="orderway" value="desc" />
			<input class="search_query" type="text" id="search_query_block" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|htmlentities:$ENT_QUOTES:'utf-8'|stripslashes}{/if}" />
			<input type="submit" id="search_button" class="button_mini" value="{l s='go' mod='tmblocksearch'}" />
		</p>
	</form>
</div>
{include file="$self/tmblocksearch-instantsearch.tpl"}
<!-- /Block search module -->
