<?php
if (!defined('_PS_VERSION_'))
	exit;

class TmHomeFeatured extends Module
{
	private $_html = '';
	private $_postErrors = array();

	function __construct()
	{
		$this->name = 'tmhomefeatured';
		$this->tab = 'front_office_features';
		$this->version = '0.9';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('TM Featured Products on the homepage');
		$this->description = $this->l('Displays Featured Products in the middle of your homepage.');
	}

	function install()
	{
		if (!Configuration::updateValue('HOME_FEATURED_NBR', 8) || !parent::install() || !$this->registerHook('displayHome') || !$this->registerHook('displayHeader'))
			return false;
		return true;
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitTmHomeFeatured'))
		{
			$nbr = (int)(Tools::getValue('nbr'));
			if (!$nbr OR $nbr <= 0 OR !Validate::isInt($nbr))
				$errors[] = $this->l('Invalid number of products');
			else
				Configuration::updateValue('HOME_FEATURED_NBR', (int)($nbr));
			if (isset($errors) AND sizeof($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<p>'.$this->l('In order to add products to your homepage, just add them to the "home" category.').'</p><br />
				<label>'.$this->l('Number of products displayed').'</label>
				<div class="margin-form">
					<input type="text" size="5" name="nbr" value="'.Tools::safeOutput(Tools::getValue('nbr', (int)(Configuration::get('HOME_FEATURED_NBR')))).'" />
					<p class="clear">'.$this->l('The number of products displayed on homepage (default: 10).').'</p>

				</div>
				<center><input type="submit" name="submitTmHomeFeatured" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}


	public function hookDisplayHome($params)
	{
		$category = new Category(Context::getContext()->shop->getCategory(), Configuration::get('PS_LANG_DEFAULT'));
		$nb = (int)(Configuration::get('HOME_FEATURED_NBR'));


$products = $category->getProducts($params['cookie']->id_lang, 1, ($nb ? $nb : 10));
$p=array();
for($i=0;$i<count($products);$i++){
$product=$products[$i];
unset ($pid);
$pid=$product['id_product'];
$sql= "SELECT * FROM `"._DB_PREFIX_."image` WHERE `id_product` = '{$pid}' AND cover = '0' ORDER BY `id_image` ";
$results = Db::getInstance()->ExecuteS($sql);
$result=$results[0];
$p[$pid]=$result['id_image'];
}
$this->smarty->assign('addimages',$p);

		$this->smarty->assign(array(
			'products' => $products,
			'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			'homeSize' => Image::getSize('home'),
		));

		return $this->display(__FILE__, 'tmhomefeatured.tpl');
	}
}

