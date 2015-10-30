<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class LofsimpleslideParams extends LOFXParams {
    
    public function displayFlags($id='', $use_vars_instead_of_ids = false) {        
        global $cookie;
        $defaultLang = intval($cookie->id_lang);
        if($id=='') $id = $defaultLang;
        $languages = Language::getLanguages(true);
        if (sizeof($languages) == 1)
            return false;
        $output = '<div class="lof_flag" style="position:relative; float:left; width: 1px;">
		<div class="displayed_flag">
			<img src="../img/l/' . $defaultLang . '.jpg" class="pointer" id="language_current_' . $id . '" onclick="toggleLanguageFlags(this);" alt="" />
		</div>
		<div id="languages_' . $id . '" class="language_flags">';
        foreach ($languages as $language) {
            $output .= '<img src="../img/l/' . (int) ($language['id_lang']) . '.jpg" class="pointer" alt="' . $language['name'] . '" title="' . $language['name'] . '" onClick="changeToLanguage(\''.$language['id_lang'].'\'); " />';
        }
                
        $output .= '</div></div>';

        return $output;
    }    

    function getImages($path='') {
        $items = array();
        if($path) {
            $handle = opendir($path);
        } else {
            $handle = opendir(LOF_SIMPLE_SLIDE_IMAGES_THUMB);
        }
        
        if (!$handle) {
            return $items;
        }
        while (false !== ($file = readdir($handle))) {
            if ($this->isItemImage($file)) {
                $items[] = $file;
            }
        }
        return $items;
    }

    function isItemImage($file, $disallowed=array('.', '..', '.svn')) {
        if (!is_dir($file) && !in_array($file, $disallowed)) {
            $ext = strtolower(preg_replace('/^.*\./', '', $file));
            if (in_array($ext, $this->moduleObj->allowedUpload)) {
                return true;
            } else
                return false;
        } else {
            return false;
        }
    }

}