<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class LOFXParams {

    protected $xmlObject = null;
    protected $errors = array();
    public $warning = array();
    public $baseurl = '';
    protected $information = array();
    protected $specialParams = array();
    public $css = array();
    public $js = array();
    public $beforeUpdate = '';

    function __construct($module) {

        $filename = _PS_MODULE_DIR_ . $module->name . '/config/params.xml';

        if (file_exists($filename)) {
            $this->xmlObject = simplexml_load_file($filename);

            $this->getInfo();
            $this->module = $module->name;
            $this->moduleObj = $module;
            $this->root_url = __PS_BASE_URI__ . "modules/" . $this->module . "/config/";
            $this->root_dir = _PS_MODULE_DIR_ . $this->module . '/config/';


            //require defined standard field method :
            if (!class_exists('LOFParamsField')) {
                require_once $this->root_dir . 'params_fields.php';
            }
        } else {
            $this->setError('File ' . $filename . ' does not exist !');
        }
    }

    function getHeader() {
        $css = $this->getFilesFromFolder($this->root_dir . 'css/');
        $js = $this->getFilesFromFolder($this->root_dir . 'js/');
        $output = '';

        //push css files :
        if (count($css)) {
            foreach ($css as $css_filename) {
                $output .= '<link rel="stylesheet" href="' . $this->root_url . '/css/' . $css_filename . '" type="text/css" media="screen" charset="utf-8" />';
            }
        }

        //push javascript files :        
        if (count($js)) {
            foreach ($js as $js_filename) {
                $output .= '<script type="text/javascript" src="' . $this->root_url . '/js/' . $js_filename . '"></script>';
            }
        }

        return $output;
    }

    /**
     * 
     * convert simple xml object to array
     * @return Array 
     */
    function getInfo() {
        $info = array();
        $k = 0;
        foreach ($this->xmlObject->configuration->children() as $object) {
            $block = $this->getArrayAttributes($object);
            $info[$k]['label'] = $block['label'];
            foreach ($object->children() as $objTheme) {
                $theme = $this->getArrayAttributes($objTheme);

                if (count($objTheme->children())) {
                    $options = array();
                    foreach ($objTheme->children() as $objOption) {
                        $options[] = $this->getArrayAttributes($objOption);
                    }
                    $theme['options'] = $options;
                }

                $info[$k]['fields'][] = $theme;
            }
            $k++;
        }
        $this->information = $info;
        return $info;
    }

    /**
     *
     * display params form 
     * @return HTML
     */
    function displayForm() {
        $fieldsets = $this->getInfo();
        ob_start();
        require $this->root_dir . 'form.php';
        $form = ob_get_contents();
        ob_clean();
        $header = $this->getHeader();
        return $this->displayWarning() . $header . $form;
    }

    function displayWarning() {
        $hmtl .= '<ul class="warning_report">';
        foreach ($this->warning as $warn) {
            $html .= '<li>' . $warn . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    function getParamsName() {
        $params = array();
        $form = $this->getInfo();
        foreach ($form as $fieldsets) {
            foreach ($fieldsets['fields'] as $field) {
                $name = $this->module . '_' . $field['name'];
                $allowed_char = 32 - strlen($this->module);
                if (strlen($name) > 32) {
                    $this->warning[] = 'Param "' . $field['name'] . '" is too long name. Please make it less than ' . $allowed_char . ' characters';
                }
				$default = '';
				if(isset($field['default'])) {
					$default = $field['default'];
				}
                $params[$name] = $default;
            }
        }
        return $params;
    }

    function get($name, $default='', $prefix = true) {
        if ($prefix) {
            $name = $this->module . '_' . $name;
        }

        return Configuration::get($name) != '' ? htmlentities(Configuration::get($name), ENT_COMPAT, 'UTF-8') : $default;
    }

    function save($name) {
        $name = $this->module . '_' . $name;
        if (in_array($name, $this->specialParams)) {
            $value = $_POST[$name];
        } else {
            $value = Tools::getValue($name);
        }
        Configuration::updateValue($name, $value, true);
    }

    function updateWithSubfix($name, $subfixs=array()) {
        if (count($subfixs)) {
            foreach ($subfixs as $subname) {
                $name = $name . '_' . $subname;
                $this->save($name);
            }
        }
    }

    function checkName($name) {
        if (strlen($name) > 32) {
            $this->warning[] = $name . ' is too long name.';
        }
        return $name;
    }

    /**
     * ********** UPDATE CONFIGURATION ****************
     */
    function update() {

        // ============= Hook to update =================
        $this->callHook($this->beforeUpdate);

        foreach ($this->getParamsName() as $config => $key) {
            if (in_array($config, $this->specialParams)) {
                $value = $_POST[$config];
            } else {
                $value = Tools::getValue($config);
            }

            if (is_array($value))
                $value = implode(',', $value);
            Configuration::updateValue($config, $value, true);
        }
    }

    function getTemplateByName($name) {
        foreach ($this->information as $block) {
            foreach ($block['themes'] as $theme) {
                if (strtolower($theme['name']) == trim(strtolower($name))) {
                    return $theme;
                }
            }
        }
        return false;
    }

    //convert attributes to array :
    function getArrayAttributes($object) {
        $res = array();
        foreach ($object->attributes() as $key => $val) {
            $res[(string) $key] = (string) $val;
        }
        return $res;
    }

    function setError($error) {
        $this->errors[] = $error;
    }

    function hasError() {
        return count($this->errors);
    }

    function getErrorMsg() {
        return implode('<br />', $this->errors);
    }

    function callHook($name) {
        if ($name != '' && method_exists($this->moduleObj, $name)) {
            $this->moduleObj->{$name}();
        }
    }

    function getName($name) {
        return $this->module . '_' . $name;
    }

    public function hook($position, $name) {

        switch ($position) {
            case 'afterUpdate' :
                $this->afterUpdate = $name;
                break;
            case 'beforeUpdate' :
            default:
                $this->beforeUpdate = $name;
                break;
        }
    }

    function test($var) {
        echo '============================ START DEBUG ==========================<br />';
        if (is_string($var) || is_null($var)) {
            echo '====| ' . $var . ' |====<br />';
        } else {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        }

        echo '============================ END DEBUG ==========================<br />';
        die();
    }

    function toHtml($field) {

        $field['value'] = $this->get($field['name'], $field['default']);
        $field['name'] = $this->checkName($this->module . '_' . $field['name']);


        $html = '<label for="' . $name . '">' . $this->moduleObj->l($field['label']) .
                '<br /> <span class="params_desc">' . $field['description'] . '</span></label>';
        $objHtml = new LOFParamsField($this);
        $element = 'get' . ucfirst($field['type']) . 'Field';
        if (strlen($field['name'] < 32)) {
            $html .= $objHtml->{$element}($field);
        } else {
            $html .= 'Too long name - Please rename this params | current length : ' . strlen($field['name']);
        }

        return $html;
    }

    function getFilesFromFolder($path) {
        $items = array();
        $handle = opendir($path);
        if (!$handle) {
            return $items;
        }
        while (false !== ($file = readdir($handle))) {
            if ($this->isValidFile($file)) {
                $items[] = $file;
            }
        }
        return $items;
    }

    function isValidFile($file, $allowed=array('css', 'js', 'php'), $disallowed=array('.', '..', '.svn')) {
        if (!is_dir($file) && !in_array($file, $disallowed)) {
            $ext = preg_replace('/^.*\./', '', $file);
            if (in_array($ext, $allowed)) {
                return true;
            } else
                return false;
        } else {
            return false;
        }
    }

    function getValues() {
        $names = $this->getParamsName();

        
        $values = array();
        if (count($names)) {
            foreach ($names as $name => $default) {
                $friendlyName = str_replace($this->module . '_', '', $name);
                $values[$friendlyName] = Configuration::get($name) != '' ? htmlentities(Configuration::get($name), ENT_COMPAT, 'UTF-8') : $default;
            }
        }        
        return $values;
    }

}