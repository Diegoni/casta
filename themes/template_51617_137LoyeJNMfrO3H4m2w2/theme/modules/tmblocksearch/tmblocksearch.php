<?php
if (!defined('_PS_VERSION_'))
	exit;

class TmBlockSearch extends Module
{
	public function __construct()
	{
		$this->name = 'tmblocksearch';
		$this->tab = 'front_office_features';
		$this->version = 1.2;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('TM Quick Search block');
		$this->description = $this->l('Adds a block with a quick search field.');
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('top') || !$this->registerHook('header'))
			return false;
		return true;
	}
	
	public function hookHeader($params)
	{
		if (Configuration::get('PS_SEARCH_AJAX'))
			$this->context->controller->addJqueryPlugin('autocomplete');
		$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookRightColumn($params)
	{
		$this->calculHookCommon($params);
		$this->smarty->assign('blocksearch_type', 'block');
		return $this->display(__FILE__, 'tmblocksearch.tpl');
	}

	public function hookTop($params)
	{
		$this->calculHookCommon($params);
		$this->smarty->assign('blocksearch_type', 'top');
		return $this->display(__FILE__, 'tmblocksearch-top.tpl');
	}

	/**
	 * _hookAll has to be called in each hookXXX methods. This is made to avoid code duplication.
	 *
	 * @param mixed $params
	 * @return void
	 */
	private function calculHookCommon($params)
	{
		$this->smarty->assign(array(
			'ENT_QUOTES' =>		ENT_QUOTES,
			'search_ssl' =>		Tools::usingSecureMode(),
			'ajaxsearch' =>		Configuration::get('PS_SEARCH_AJAX'),
			'instantsearch' =>	Configuration::get('PS_INSTANT_SEARCH'),
			'self' =>			dirname(__FILE__),
		));

		return true;
	}
}

