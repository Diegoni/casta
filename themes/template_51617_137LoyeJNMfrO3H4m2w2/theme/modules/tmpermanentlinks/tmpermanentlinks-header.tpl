<!-- Block permanent links module HEADER -->
<ul id="tmheaderlinks">
	<li><a href="{$link->getPageLink('index.php')}"{if $page_name == 'index'} class="active"{/if}>{l s='home' mod='tmpermanentlinks'}</a></li>
	<li><a href="{$link->getPageLink('prices-drop')}"{if $page_name == 'prices-drop'} class="active"{/if}>{l s='specials' mod='tmpermanentlinks'}</a></li>
    <li><a href="{$link->getPageLink('new-products')}" {if $page_name == 'new-products'} class="active"{/if}>{l s='New products' mod='tmpermanentlinks'}</a></li>
    <li><a href="{$link->getPageLink('my-account', true)}" {if $page_name == 'my-account'} class="active"{/if}>{l s='Your Account' mod='tmpermanentlinks'}</a></li>
	<li><a href="{$link->getPageLink('contact', true)}" {if $page_name == 'contact'} class="active"{/if}>{l s='contact' mod='tmpermanentlinks'}</a></li>
	<li><a href="{$link->getPageLink('sitemap')}" {if $page_name == 'sitemap'} class="active"{/if}>{l s='sitemap' mod='tmpermanentlinks'}</a></li>
</ul>
<div class="mobile-link-top">
<h4><span>MENU</span> <span class="open-menu"></span></h4>
<ul id="mobilelink">
	<li {if $page_name == 'index'} class="active"{/if}><a href="{$link->getPageLink('index.php')}"{if $page_name == 'index'} class="active"{/if}>{l s='home' mod='tmpermanentlinks'}</a></li>
	<li {if $page_name == 'prices-drop'} class="active"{/if}><a href="{$link->getPageLink('prices-drop')}"{if $page_name == 'prices-drop'} class="active"{/if}>{l s='specials' mod='tmpermanentlinks'}</a></li>
    <li {if $page_name == 'new-products'} class="active"{/if}><a href="{$link->getPageLink('new-products')}" {if $page_name == 'new-products'} class="active"{/if}>{l s='New products' mod='tmpermanentlinks'}</a></li>
    <li {if $page_name == 'my-account'} class="active"{/if}><a href="{$link->getPageLink('my-account', true)}" {if $page_name == 'my-account'} class="active"{/if}>{l s='Your Account' mod='tmpermanentlinks'}</a></li>
	<li {if $page_name == 'contact-form'} class="active"{/if}><a href="{$link->getPageLink('contact', true)}" {if $page_name == 'contact'} class="active"{/if}>{l s='contact' mod='tmpermanentlinks'}</a></li>
	<li {if $page_name == 'sitemap'} class="active"{/if}><a href="{$link->getPageLink('sitemap')}" {if $page_name == 'sitemap'} class="active"{/if}>{l s='sitemap' mod='tmpermanentlinks'}</a></li>
</ul>
</div>
<!-- /Block permanent links module HEADER -->
