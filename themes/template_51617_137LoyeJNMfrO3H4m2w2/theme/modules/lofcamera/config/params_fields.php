<?php

/**
 * Defined fields for LOFXParams 
 * 
 * ** For define custom field :
 * Create function with name follow this rule : get<Element>Field with param $field e.g: function getCategoriesField($field) {}
 * $field : is an array and contain element same with xml, for example :
 * 
 * if xml field is : <field type="text" name="test" default="1" label="This is test" class="this-is-test" />
 * we'll get $field information like this : array('type' => "text", 'name' => 'test' .... and so on...
 * 
 * * If you want to create custom style then you just create a css file and push to /config/css folder, do same with javascript file.
 * * No need to hard code because every media file in css and js folder will be call from system.
 */
class LOFParamsField {

    function __construct($obj = null) {
        $this->config = $obj;
    }

    /*     * ************************************CUSTOM FIELDS ************************************************* */

    function getImageuploadField($field) {
        if ($field['multi']) {
            $name = $field['name'] . '[]';
            $multi = 'multiple="1"';
        }

        $html = '<input class="' . $field['class'] . '" onChange="' . $field['change'] . '" type="file" name="' . $name . '" id="' . $field['name'] . '" ' . $multi . ' />';
        $html .= '<fieldset>
                    <legend>Files Selected</legend>
                    <ul id="upload_list"><li><span>Please select some image ("Ctrl + left click" on image to select multiple image)</span></li></ul>    
                </fieldset>';

        return $html;
    }

    function getImageeditField($field) {
        
        $images = $this->config->getImages();  
        $html = 'No image found !';
        $languages = Language::getLanguages();

        if (count($languages)) {
                $html = '';
                global $cookie;
                
                $html .= $this->config->displayFlags();
                
            foreach ($languages as $lang) {
                $html .= '<div class="info_lang" id="lang_'.$lang['id_lang'].'" >';
                                
                if (count($images)) {
                    $properties = explode(',', $field['properties']);
                    $labels = explode(',', $field['labels']);                    
                    foreach ($images as $k => $imgname) {
                        if (count($properties)) {
                            $html .= '<div class="image-edit-field">';
                            $html .= '<div class="edit-field-left" >
                                <img src="' . LOF_SIMPLE_SLIDE_URI_IMAGES_THUMB . $imgname . '" /> 
                                    <div class="check_remove"><input onClick="selectImage(this);" type="checkbox" name="remove_images[]" value="' . $imgname . '" />Delete</div>
                              </div>';
                            $html .= '<div class="edit-field-right">';
                            foreach ($properties as $i => $property) {
                                $labeltext = trim($labels[$i]) ? trim($labels[$i]) : ucfirst(trim($property));
                                $fullname = $this->config->checkName($field['name'] .$lang['id_lang'].'_' .$k . '_' . trim($property));
                                if (trim($property) != 'image') {
                                    $html .= '<label for="' . $title_name . '">' . $labeltext . '</label>';
                                    if (trim($property) == 'desc') {
                                        $html .= '<textarea name="' . $fullname . '" cols="70" rows="5" >' . $this->config->get($fullname, '', false) . '</textarea>';
                                    } else {
                                        $html .= '<input type="text" name="' . $fullname . '" value="' . $this->config->get($fullname, '', false) . '" />';
                                    }

                                    $html .= '<div class="clearfix"></div>';
                                } else {
                                    $html .= '<input type="hidden" name="' . $fullname . '" value="' . $imgname . '" />';
                                }
                            }
                            $html .= '</div>';
                            $html .= '</div>';
                        }
                    }
                }
                $html .= '</div>';
            }
            $html .= '<script type="text/javascript">changeToLanguage("'.$cookie->id_lang.'");</script>';
        }

        return $html;
    }

    /*     * *********************************** BASIC FIELDS *************************************************** */

    function getTextField($field) {
        return '<input size="40" id="input_' . $field['name'] . '" ' . $field['attr'] . ' name="' . $field['name'] . '" value="' . $field['value'] . '" />';
    }

    function getTextareaField($field) {
        return '<textarea ' . $field['attr'] . ' cols="' . $field['cols'] . '" rows="' . $field['rows'] . '" id="' . $field['name'] . '" name="' . $field['name'] . '">' . $field['value'] . '</textarea>';
    }

    function getListField($field) {
        $html = '<select name="' . $field['name'] . '" ' . $field['attr'] . ' >';
        foreach ($field['options'] as $opt) {
            $selected = $opt['value'] == $field['value'] ? 'selected="selected"' : '';
            $html .= '<option value="' . $opt['value'] . '" ' . $selected . ' >' . $opt['text'] . '</option>';
        }
        $html .= '</select>';

        return $html;
    }

    function getThemeField($field) {
        $path = _PS_ROOT_DIR_ . '/modules/' . $this->config->module . '/themes/';
        $folders = $this->getFolderList($path);
        $options = array();
        foreach ($folders as $folder) {
            $options[] = array('text' => ucfirst($folder), 'value' => $folder);
        }
        $field['options'] = $options;
        return $this->getListField($field);
    }

    private function getFolderList($path) {
        $items = array();
        $handle = opendir($path);
        if (!$handle) {
            return $items;
        }
        while (false !== ($file = readdir($handle))) {
            if (is_dir($path . $file))
                $items[$file] = $file;
        }
        unset($items['.'], $items['..'], $items['.svn']);
        return $items;
    }

}
