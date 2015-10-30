<?php

/*
 * 2011 LandOfCoder
 *
 *  @author LandOfCoder 
 *  @copyright  2011 LandOfCoder
 *  @version  Release: $Revision: 1.0 $
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_'))
    exit;
require_once(_PS_MODULE_DIR_ . "lofcamera/defined.php");
if (!class_exists('LOFXParams')) {
    require LOF_SIMPLE_SLIDE_ROOT . 'config/params.php';
}
require LOF_SIMPLE_SLIDE_ROOT . 'config/lofcamera.php';


if (!class_exists('PhpThumbFactory')) {
    require LOF_SIMPLE_SLIDE_LIB . 'phpthumb/ThumbLib.inc.php';
}

class lofcamera extends Module {
    /* @var boolean error */

    protected $error = false;
	
    private $_postErrors = array();
    public $allowedUpload = array("jpg", "bmp", "gif", "png");

    public function __construct() {
        $this->name = 'lofcamera';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'TM';
        $this->need_instance = 0;
        $this->params = new LofsimpleslideParams($this);

        parent::__construct();
        $this->displayName = $this->l('Camera Slideshow');
        $this->description = $this->l('Simply - Useful - smoothly, It is slideshow ');
        $this->confirmUninstall = $this->l('Do you want to uninstall Lof simple slideshow');
    }

    public function install() {
        if (parent::install() == false
                OR !$this->registerHook('header')
                OR !$this->registerHook('top')
        )
            return false;
        return true;
    }

    public function uninstall() {
        if (!parent::uninstall())
            return false;
        return true;
    }

    function hookHeader() {

        $theme_assets = LOF_SIMPLE_SLIDE_URI_THEMES.$this->params->get('template', 'default').'/assets/';
        if (_PS_VERSION_ <= "1.4") {
            $header .= $this->linkMedia(LOF_SIMPLE_SLIDE_URI_CSS . 'lofsimpleslide.css');
            $header .= $this->linkMedia(LOF_SIMPLE_SLIDE_URI_CSS . 'slide.camera.css');
            $header .= $this->linkMedia(LOF_SIMPLE_SLIDE_URI_CSS . 'slide.camera.js', 'js');
            $header .= $this->linkMedia($theme_assets.'styles.css');

            return $header;
        } else {
            Tools::addCSS(LOF_SIMPLE_SLIDE_URI_CSS . 'lofsimpleslide.css');
            Tools::addCSS(LOF_SIMPLE_SLIDE_URI_CSS . 'slide.camera.css');
            Tools::addCSS($theme_assets.'styles.css');
            Tools::addJS(LOF_SIMPLE_SLIDE_URI_JS . 'slide.camera.js');
        }
    }

    function hookhome($params) {
        return $this->processHook($params, 'home');
    }

    function hookTop($params) {
        return $this->processHook($params, 'top');
    }

    function hookleftColumn($params) {
        return $this->processHook($params, 'left');
    }

    function hookrightColumn($params) {
        return $this->processHook($params, 'right');
    }

    function processHook($params = array(), $hook = '') {

        $this->hookname = 'lofcamera_' . $hook;

        ob_start();
        require LOF_SIMPLE_SLIDE_ROOT . 'initjs.php';
        $slideSettings = ob_get_contents();
        ob_clean();

        //render slide configuration :
        //$this->renderSlideConfig();

        global $smarty, $cookie;

        $images = $this->params->getImages(LOF_SIMPLE_SLIDE_IMAGES_PRIMARY);
        $params = $this->params->getValues();

        //create images information :
        foreach ($images as $k => $img) {
            $basename = 'slide' . $cookie->id_lang . '_' . $k . '_';
            $image = array(
                'title' => $this->params->get($basename . 'title'),
                'desc' => $this->params->get($basename . 'desc'),
				'desc2' => $this->params->get($basename . 'desc2'),
			    'buttonlink' => $this->params->get($basename . 'buttonlink'),
				'link' => $this->params->get($basename . 'link'),
                'price' => $this->params->get($basename . 'price'),
                'name' => $img,
            );
            $images[$k] = $image;
        }

        $smarty->assign(array(
            'images' => $images,
            'image_uri' => LOF_SIMPLE_SLIDE_URI_IMAGES_PRIMARY,
            'thumb_uri' => LOF_SIMPLE_SLIDE_URI_IMAGES_THUMB,
            'params' => $params,
            'hookname' => $this->hookname
        ));

        return $this->display(__FILE__, $this->getLayoutPath()) . $slideSettings;
    }

    public function getLayoutPath() {
        $theme = $this->params->get('template', 'default');
        $layout = 'themes/' . $theme . '/default.tpl';
        if (!file_exists(__FILE__ . "/" . $layout)) {
            return $layout;
        }
    }

    /**
     * Render processing form && process saving data.
     */
    public function getContent() {
        $html = "";
        if (Tools::isSubmit('submit')) {
            $this->_postValidation();
            if (is_array($this->_postErrors) && !count($this->_postErrors)) {
                $this->params->hook('beforeUpdate', 'firstUpdate');
                $this->params->update();
                $html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
            }
        }
        if ($this->params->hasError())
            die($this->params->getErrorMsg());

        return $html . $this->params->displayForm();
    }

    /**
     * Process vadiation before saving data 
     */
    private function _postValidation() {
        
    }

    function firstUpdate() {
        $this->uploadImages();
        $this->updateImagesInfo();
        $this->removeSelectedImages();
    }

    function uploadImages() {

        $files = $_FILES[$this->params->getName('img_uploader')];
        if (is_array($files['name']) && count($files['name']) && $files['name'][0] != '') {

            $imageWidth = $this->params->get('image_width', 600);
            $imageHeight = $this->params->get('image_height', 200);
            $thumbWidth = $this->params->get('thumb_width', 150);
            $thumbHeight = $this->params->get('thumb_height', 100);

            for ($i = 0; $i < count($files['name']); $i++) {
                $file = $files['name'][$i];
                $file_tmp = $files['tmp_name'][$i];

                if (isset($file) && $file != NULL) {
                    $ext = strtolower(substr($file, strrpos($file, '.') + 1));
                    $filename = LOF_SIMPLE_SLIDE_IMAGES_ORIGIN . $file;
                    if (in_array($ext, $this->allowedUpload)) {
                        if (move_uploaded_file($file_tmp, $filename)) {

                            $primayname = LOF_SIMPLE_SLIDE_IMAGES_PRIMARY . $file;
                            //create thumbnail if not exist :                        
                            if (!file_exists($primayname) && file_exists($filename)) {
                                $this->createThumb($filename, $primayname, $imageWidth, $imageHeight);
                            }

                            $thumbname = LOF_SIMPLE_SLIDE_IMAGES_THUMB . $file;
                            //create thumbnail if not exist :                        
                            if (!file_exists($thumbname) && file_exists($filename)) {
                                $this->createThumb($filename, $thumbname, $thumbWidth, $thumbHeight);
                            }

                            //delete origin image :
                            if (file_exists($filename)) {
                                @unlink($filename);
                            }
                        }
                    }
                }
            }
        }
    }

    function updateImagesInfo() {
        $images = $this->params->getImages();
        $languages = Language::getLanguages(true);

        foreach ($languages as $lang) {
            foreach ($images as $k => $img) {
                $basename = 'slide' . $lang['id_lang'] . '_' . $k;
                $this->params->save($basename . '_title');
                $this->params->save($basename . '_desc');
				$this->params->save($basename . '_desc2');
				$this->params->save($basename . '_buttonlink');
                $this->params->save($basename . '_price');
                $this->params->save($basename . '_link');
                $this->params->save($basename . '_image');
            }
        }
    }

    function createThumb($imagePath, $thumbname, $width=100, $height=100) {
        $thumb = PhpThumbFactory::create($imagePath);
        $thumb->adaptiveResize($width, $height);
        $thumb->save($thumbname);
        return true;
    }

    function removeSelectedImages() {
        $images = $_POST['remove_images'];
        if (is_array($images) && count($images)) {
            foreach ($images as $imageName) {
                //remove origin image :
                $filename = LOF_SIMPLE_SLIDE_IMAGES_ORIGIN . $imageName;
                if (file_exists($filename)) {
                    @unlink($filename);
                }

                //remove primay image :
                $imagename = LOF_SIMPLE_SLIDE_IMAGES_PRIMARY . $imageName;
                if (file_exists($imagename)) {
                    @unlink($imagename);
                }

                //remove thumb image :
                $thumbname = LOF_SIMPLE_SLIDE_IMAGES_THUMB . $imageName;
                if (file_exists($thumbname)) {
                    @unlink($thumbname);
                }
            }
        }
    }

    function linkMedia($src, $type='css') {
        if ($type == 'css') {
            return '<link type="text/css" rel="stylesheet" href="' . $src . '" />';
        } else {
            return '<script type="text/javascript" src="' . $src . '"></script>';
        }
    }

}
