<?php



class tmbannerblock extends Module
{
	protected $maxImageSize = 307200;
	protected $imageDir = 'slides/';

	protected $_defaultLanguage;
	protected $_languages;
	protected $_xml;

	public function __construct()
	{
		$this->name = 'tmbannerblock';
		$this->tab = 'front_office_features';
		$this->version = '1.0';

		parent::__construct();

		$this->page = basename(__FILE__,'.php');
		$this->displayName = $this->l('TM Banner Block');
		$this->description = $this->l('Add a awesome banner to your homepage.');

		/* initiate values for translation */
		$this->_defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$this->_languages = Language::getLanguages();
		/* put xml in cache */
		$this->_xml = $this->_getXml();
	}

	public function install()
	{
		if (!parent::install()
				OR !$this->registerHook('top')
				OR !$this->registerHook('header')
			)
			return false;
		return true;
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.' - '.$this->l('version').' '.$this->version.'</h2>';
		$this->_html .= $this->_postProcess();
		$this->_html .= $this->_displayForm();
		return $this->_html;
	}

	protected function putContent($xml_data, $key, $field)
	{
		$field = stripslashes(htmlspecialchars($field,ENT_QUOTES,"UTF-8"));
		if (!$field)
			return 0;
		return ("\n\t\t<".$key.">".$field."</".$key.">");
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('submitUpdate'))
		{
			$newXml = '<'.'?'.'xml version="1.0" encoding="utf-8" '.'?'.'>';
			$newXml .= "\n<items>";
			$i = 0;
			foreach (Tools::getValue('item') AS $item)
			{
				$newXml .= "\n\t<item>";
				foreach ($item AS $key => $field)
				{
					if ($line = $this->putContent($newXml, $key, $field))
						$newXml .= $line;
				}
				if (isset($_FILES['item_'.$i.'_img']) AND isset($_FILES['item_'.$i.'_img']['tmp_name']) AND !empty($_FILES['item_'.$i.'_img']['tmp_name']))
				{
					Configuration::set('JMPRESS_GENERATION_METHOD', 1);
					if ($error = ImageManager::validateUpload($_FILES['item_'.$i.'_img'], $this->maxImageSize))
					//if ($error = checkImage($_FILES['item_'.$i.'_img'], $this->maxImageSize))
						return $error;
					elseif (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($_FILES['item_'.$i.'_img']['tmp_name'], $tmpName))
						return false;
					//	if ($error = !ImageManager::validateUpload($_FILES['item_'.$i.'_img'], $this->maxImageSize))
					else if (!ImageManager::resize($tmpName, dirname(__FILE__).'/'.$this->imageDir.'slide'.$i.'.jpg'))
					//elseif (!imageResize($tmpName, dirname(__FILE__).'/'.$this->imageDir.'slide'.$i.'.jpg'))
						return $this->displayError($this->l('An error occurred during the image upload.'));
					unlink($tmpName);
				}
				if ($line = $this->putContent($newXml, 'img', $this->imageDir.'slide'.$i.'.jpg'))
					$newXml .= $line;
				$newXml .= "\n\t</item>\n";
				$i++;
			}
			$newXml .= "\n</items>\n";

			if ($fd = @fopen(dirname(__FILE__).'/'.$this->getXmlFilename(), 'w'))
			{
				if (!@fwrite($fd, $newXml))
					return $this->displayError($this->l('Unable to write to the editor file.'));
				if (!@fclose($fd))
					return $this->displayError($this->l('Can\'t close the editor file.'));
			}
			else
				return $this->displayError($this->l('Unable to update the editor file. Please check the editor file\'s writing permissions.'));

			/* refresh XML */
			$this->_xml = $this->_getXml();
			return $this->displayConfirmation($this->l('Items updated.'));
		}
	}

	static private function getXmlFilename()
	{
		return 'data.xml';
	}

	private function _getXml()
	{
		$file = dirname(__FILE__).'/'.$this->getXmlFilename();
		if (file_exists($file))
		{

			if ($xml = @simplexml_load_file($file))
			{
				return $xml;
			}
		}
		return false;
	}

	public function _getFormItem($i, $last)
	{
		$divLangName = 'title'.$i.'&curren;cpara'.$i.'&curren;btnb'.$i;
		$defaultLanguage = (int)Configuration::get('PS_LANG_DEFAULT');		
		$output = '
			<div style="border-bottom:1px solid #CCCED7!important;padding-bottom:20px!important;margin-bottom:20px!important;" class="item" id="item'.$i.'">
				<h3>'.$this->l('Banner №').($i+1).'</h3>
				<label>'.$this->l('Banner Title ').'</label>
				<div class="margin-form">';

		foreach ($this->_languages as $language)
		{
			$output .= '
					<div id="title'.$i.'_'.$language['id_lang'].'" style="display:'.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<input type="text" name="item['.$i.'][title_'.$language['id_lang'].']" id="item_title_'.$i.'_'.$language['id_lang'].'" size="64" value="'.(isset($this->_xml->item[$i]->{'title_'.$language['id_lang']}) ? stripslashes(htmlspecialchars($this->_xml->item[$i]->{'title_'.$language['id_lang']})) : '').'" />
					</div>';
		}
		$output .= $this->displayFlags($this->_languages, $defaultLanguage, $divLangName , 'title'.$i, true);
		
		
		
		$output .= '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Banner button').'</label>
				<div class="margin-form">';

		foreach ($this->_languages as $language)
		{
			$output .= '
					<div id="btnb'.$i.'_'.$language['id_lang'].'" style="display:'.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<input type="text" name="item['.$i.'][btn_'.$language['id_lang'].']" id="item_btn_'.$i.'_'.$language['id_lang'].'" size="64" value="'.(isset($this->_xml->item[$i]->{'btn_'.$language['id_lang']}) ? stripslashes(htmlspecialchars($this->_xml->item[$i]->{'btn_'.$language['id_lang']})) : '').'" />
					</div>';
		}
		$output .= $this->displayFlags($this->_languages, $defaultLanguage, $divLangName , 'btnb'.$i, true);

		

		$output .= '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Banner Text').'</label>
				<div class="margin-form">';

		foreach ($this->_languages as $language)
		{
			$output .= '
					<div id="cpara'.$i.'_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<textarea cols="64" rows="3" name="item['.$i.'][text_'.$language['id_lang'].']" id="item_text_'.$i.'_'.$language['id_lang'].'">'.(isset($this->_xml->item[$i]-> {'text_'.$language['id_lang']}) ? stripslashes(htmlspecialchars($this->_xml->item[$i]-> {'text_'.$language['id_lang']})) : '').'</textarea>
					</div>';
		}
		$output .= $this->displayFlags($this->_languages, $defaultLanguage, $divLangName , 'cpara'.$i, true);

		$output .= '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Banner Picture').'</label>
				<div class="margin-form">
					<img src="'.$this->_path.(isset($this->_xml->item->$i->img) ? stripslashes(htmlspecialchars($this->_xml->item->$i->img)) : '').'" alt="" title="" style="width:250px; height:auto;border:1px solid #ccc!important; margin-bottom:20px;background:#fff;" />
					<br /><input type="file" name="item_'.$i.'_img" />
					<p style="clear: both"></p>
				</div>
				<label>'.$this->l('Banner Link').'</label>
				<div class="margin-form">
					<input type="text" name="item['.$i.'][url]" size="64" value="'.(isset($this->_xml->item[$i]->url) ? stripslashes(htmlspecialchars($this->_xml->item[$i]->url)) : '').'" />
					<p style="clear: both"></p>
				</div>
				<div class="clear pspace"></div>
				'.($i > 0 ? '<a href="javascript:{}" onclick="removeDiv(\'item'.$i.'\')" style="color:#EA2E30"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="'.$this->l('delete').'" />'.$this->l('Delete this item').'</a>' : '').'
				<hr/>
				'.($last ? '<a id="clone'.$i.'" href="javascript:cloneIt(\'clone'.$i.'\')" style="color:#488E41"><img src="'._PS_ADMIN_IMG_.'add.gif" alt="'.$this->l('add').'" /><b>'.$this->l('Add a new item').'</b></a>' : '').'
			</div>';
		return $output;
	}

	public function _displayForm()
	{

		$output = '';

		$xml = false;
		if (!$xml = $this->_xml)
			$output .= $this->displayError($this->l('Your data file is empty.'));

		$output .= '
		<script type="text/javascript">
		function removeDiv(id)
		{
			$("#"+id).fadeOut("slow");
			$("#"+id).remove();
		}
		function cloneIt(cloneId) {
			var currentDiv = $("#"+cloneId).parent(".item");
			var id = $(currentDiv).attr("id").match(/[0-9]+/gi);
			var nextId = parseInt(id) + 1;
			$.get("'._MODULE_DIR_.$this->name.'/ajax.php?id="+nextId, function(data) {
				$(currentDiv).after(data);
			});
			$("#"+cloneId).remove();
		}
		</script>
		<form method="post" action="'.$_SERVER['REQUEST_URI'].'" enctype="multipart/form-data">
			<fieldset class="banner-block" style="width: 900px;">
				<legend>'.$this->displayName.'</legend>';

		$i = 0;
		foreach ($xml->item as $item)
		{
			$last = ($i == (count($xml->item)-1) ? true : false);
			$output .= $this->_getFormItem($i, $last);
			$i++;
		}
		$output .= '
				<div class="margin-form clear">
					<input type="submit" name="submitUpdate" value="'.$this->l('Save').'" class="button" />
				</div>
			</fieldset>
		</form>';
		return $output;
	}

	function hookHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'css/style.css');
	}

	function hookTop($params)
	{
		global $cookie;


		if ($xml = $this->_xml)
		{
			$this->smarty->assign(array(
				'xml' => $xml,
				'title' => 'title_'.$cookie->id_lang,
				'btn' => 'btn_'.$cookie->id_lang,
				'text' => 'text_'.$cookie->id_lang,
				'effect' => $effects
			));
			return $this->display(__FILE__, $this->name.'.tpl');
		}
		return false;
	}

	public function hookDisplayHome($params) {
		return $this->hookTop($params);
	}

}

?>