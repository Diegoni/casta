<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Helpers
 * @category	Heleprs
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Carga un árbol de comandos ext desde un archivo XML
 *
 * @param string $uri URI del archivo
 * @return array
 */
function extjs_load_tree_xml($uri)
{
	$xml = new SimpleXMLElement($uri, null, TRUE);

	if (!isset($xml->node)) return null;

	$nodes = array();
	foreach ($xml->node as $node)
	{
		$nodes[] = extjs_load_tree_node_xml($node);
	}
	return $nodes;
}

/**
 * Lee un nodo de un fichero XML y lo convierte a un nodo de comandos
 *
 * @param SimpleXMLElement $node Nodo
 * @return array
 */
function extjs_load_tree_node_xml($node)
{
	$n = array();
	$obj =& get_instance();

	if (isset($node->text)) $n['text'] = $obj->lang->line((string)$node->text);
	if (isset($node->iconCls)) $n['iconCls'] = (string)$node->iconCls;
	if (isset($node->qtip)) $n['qtip'] = (string)$node->qtip;
	if (isset($node->href)) $n['href'] = (string)$node->href;
	if (isset($node->id)) $n['id'] = (string)$node->id;
	if (isset($node->leaf)) $n['leaf'] = (bool)$node->leaf;
	if (isset($node->children))
	{
		foreach ($node->children->node as $subnode)
		{
			$n['children'][] = extjs_load_tree_node_xml($subnode);
		}
	}
	return $n;
}


/**
 * Crea un reader con los parámetros indicados
 * @param array $data Datos
 * @param bool $model true: crea el modelo de datos
 * @return string
 */
function extjs_createjsonreader($data, $model = false, $groupField = null, $sortInfo = null)
{
	$fields = '';
	foreach($data['fields'] as $field)
	{
		$fields .= ",{name:'{$field['name']}'";
		if (isset($field['type'])) $fields .= ",type:'{$field['type']}'";
		if (isset($field['mapping'])) $fields .= ",mapping:'{$field['mapping']}'";
		$fields .= '}';
	}
	$sort = isset($data['remotesort'])?$data['remotesort']:'true';
	if (strlen($fields)>0) $fields = substr($fields,1);

	$t_store = isset($groupField)?'GroupingStore':'Store';
	$groupField = isset($groupField)?"groupField:'{$groupField}', sortInfo:{field: '{$sortInfo}', direction: 'ASC'},":'';
	$params = (isset($data['params']))?'params:{'. $data['params'] . '},':'';
	$reader = "var {$data['name']} = new Ext.data.{$t_store}({
		remoteSort : {$sort},
		autoload : true,
		{$groupField}
		proxy : new Ext.data.HttpProxy({
		url : '{$data['url']}'}),
		reader : new Ext.data.JsonReader({
		root : 'value_data',
		totalProperty : 'total_data',
		idProperty : '{$data['id']}',
		remoteSort : true,
		{$params}
		autoload : true}, [{$fields}])});\n";

		$dir = (isset($data['dir']))?$data['dir']:'asc';
		if (isset($data['sort'])) $reader .= "{$data['name']}.setDefaultSort('{$data['sort']}', '{$dir}');\n";

		if ($model)
		{
			$data['name'] .= '_model';
			$reader .= extjs_createrecordstore($data);
		}
		return $reader;
}

/**
 * Crea un record con los parámetros indicados
 * @param array $data Datos
 * @return string
 */
function extjs_createrecordstore($data)
{
	$fields = '';
	foreach($data['fields'] as $field)
	{
		$fields .= ",{name:'{$field['name']}'";
		if (isset($field['type'])) $fields .= ",type:'{$field['type']}'";
		//if (isset($field['mapping'])) $fields .= ",mapping:'{$field['mapping']}'";
		$fields .= '}';
	}
	if (strlen($fields)>0) $fields = substr($fields,1);

	$reader = "var {$data['name']} = new Ext.data.Record.create(
		[{$fields}]);\n";

	return $reader;
}

/**
 * Crea un formulario grid para gestionar los datos de un modelo de datos
 * @param array $model Mode de datos
 * @param string $id Id del formulario
 * @param string $title Título del formulario
 * @param string $icon Icono del formulario
 * @param string $cmd Controlador de los datos del modelo
 * @param string $field_id Campo identificador
 *
 * @return string
 */
function extjs_createmodel($model, $id, $title, $icon, $cmd, $field_id)
{
	//CI
	$obj =& get_instance();

	$combos = array();
	$fields = '{
			name : \'id\',
			column : {
				header : "' . $obj->lang->line('Id'). '",
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : \'id\',
				sortable : true
			}
		}, {
			name : \''. $field_id .'\'
		}, ';

	$count = 0;
	$default_ok = false;
	foreach($model as $m)
	{
		//Cabecara
		$header = $obj->lang->line(isset($m[DATA_MODEL_DESCRIPTION])?$m[DATA_MODEL_DESCRIPTION]:$m[DATA_MODEL_FIELD]);

		//Ancho y editor según tipo
		$type = null;
		$renderer='';
		$extra = '';
		switch ($m[DATA_MODEL_TYPE])
		{
			case DATA_MODEL_TYPE_INT:
				$width = 'Ext.app.TAM_COLUMN_NUMBER';
				$editor = 'new Ext.form.NumberField()';
				$xtype = 'numberfield';
				break;
			case DATA_MODEL_TYPE_DATETIME:
			case DATA_MODEL_TYPE_DATE:
				$width = 'Ext.app.TAM_COLUMN_DATE';
				$editor = 'new Ext.form.DateField({format: "d/m/Y", startDay: Ext.app.DATESTARTDAY})';
				$extra = ', startDay: Ext.app.DATESTARTDAY';
				$xtype = 'datefield';
				$type = 'date';
				break;
			case DATA_MODEL_TYPE_STRING:
				$width = 'Ext.app.TAM_COLUMN_TEXT';
				$editor = 'new Ext.form.TextField()';
				$xtype = 'textfield';
				break;
			case DATA_MODEL_TYPE_BOOLEAN:
				$width = 'Ext.app.TAM_COLUMN_BOOL';
				$editor = 'new Ext.form.Checkbox()';
				$xtype = 'checkbox';
				$renderer = 'renderer: Ext.app.renderCheck,';
				break;
			case DATA_MODEL_TYPE_DOUBLE:
				$width = 'Ext.app.TAM_COLUMN_NUMBER';
				$editor = 'new Ext.form.NumberField()';
				$xtype = 'numberfield';
			case DATA_MODEL_TYPE_MONEY:
				$width = 'Ext.app.TAM_COLUMN_NUMBER';
				$editor = 'new Ext.form.NumberField()';
				$renderer = 'renderer: Ext.app.euroFormatter,';
				$xtype = 'numberfield';
				break;
		}

		$allowblank = ($m[DATA_MODEL_REQUIRED] === TRUE) ? 'false' : 'true';
		$add = "{xtype: '{$xtype}', allowBlank: {$allowblank} {$extra}}";

		//Si se indica tipo de editor, se crea
		if (isset($m[DATA_MODEL_EDITOR]))
		{
			$editortype = $m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_TYPE];
			switch ($editortype)
			{
				case DATA_MODEL_EDITOR_COMBO:
					//Es un combo box
					//Parámetro 1 es la URL
					$name = str_replace('/', '_', $m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM1]);
					//Crea un combo necesario
					$combos[$name] = $m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM1];
					$editor = $name;
					$renderer = "renderer: function(val) {
					return Ext.app.renderCombo(val, {$name});
				},";
					$add = "Ext.app.combobox({url: \"" . site_url($m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM1]) ."\",
					id: '{$m[DATA_MODEL_FIELD]}', 
					fieldLabel: \"{$m[DATA_MODEL_FIELD]}\", autoload: {$allowblank} })";
					break;
			}
		}

		$default ='';
		if (((isset($m[DATA_MODEL_DEFAULT]) && ($m[DATA_MODEL_DEFAULT] === TRUE))|| (count($model) == $count+1)) && (!$default_ok))
		{
			$default = 'id :\'descripcion\',';
			$default_ok = TRUE;
		}

		$column = "{
	header : \"{$header}\",
	width : {$width},
	{$renderer}
	{$default}
	editor : {$editor},
	sortable : true}";

	$type = (isset($type))?"type: '{$type}',": '';
	$field = "{name: '{$m[DATA_MODEL_FIELD]}',$type column:{$column}, add:{$add}}";
	$fields .= $field . ',';
	$count++;
	}
	//Crea los combos;
	$str_c = '';
	$stores = '';
	foreach($combos as $name => $url)
	{
		$url = site_url($url);
		$str_c .= "var {$name} = new Ext.form.ComboBox(Ext.app.combobox({ url: \"{$url}\" }));";
		$stores .= "{store: {$name}.store},";
	}
	$stores = 'var stores = [ ' .substr($stores, 0, strlen($stores)-1) . '];';
	$fields = 'var model = [' . substr($fields, 0, strlen($fields) - 1) . '];';

	$cmd = str_replace('.', '/', $cmd);
	$function = "return Ext.app.createFormGrid({model: model, id: \"{$id}\",
title: \"{$title}\", icon: \"{$icon}\", idfield: 'id',
urlget: \"" . site_url($cmd . '/get_list') ."\",
urladd: \"" . site_url($cmd . '/add') . "\",
urlupd: \"" . site_url($cmd . '/upd') . "\",
urldel: \"" . site_url($cmd . '/del') ."\", loadstores: stores});";

	$end = "(function() { {$str_c} {$fields} {$stores} {$function} })();";
	return $end;
}

/**
 * Crea un formulario grid para gestionar los datos de un modelo de datos
 * @param array $model Mode de datos
 * @param string $id Id del formulario
 * @param string $title Título del formulario
 * @param string $icon Icono del formulario
 * @param string $cmd Controlador de los datos del modelo
 * @param string $field_id Campo identificador
 * @param string $fn_pre Función a la que llamar antes del render
 * @param bool $load Cargar el store al crear
 *
 * @return string
 */
function extjs_creategrid($model, $id, $title, $icon, $cmd, $field_id, $fn_pre = null, $load = TRUE, $fn_add = null, $plus = null, $submenu = null, $show_filter = FALSE, $extra_fields = null)
{
	//CI
	$obj =& get_instance();

	$combos = array();
	$fields = '{
			name : \'id\',
			column : {
				header : "' . $obj->lang->line('Id'). '",
				width : Ext.app.TAM_COLUMN_ID,
				dataIndex : \'id\',
				sortable : true
			}
		}, {
			name : \''. $field_id .'\'
		}, ';
	if (isset($extra_fields) && is_array($extra_fields))
	{
		foreach ($extra_fields as $value) 
		{
			$fields .= "{name: '{$value}'},\n";
		}
	}

	$count = 0;
	$default_ok = false;

	foreach($model as $k => $m)
	{
		if (!isset($m[DATA_MODEL_SEARCH])) $m[DATA_MODEL_SEARCH] = FALSE;

		//Cabecera
		$header = $obj->lang->line(isset($m[DATA_MODEL_DESCRIPTION])?$m[DATA_MODEL_DESCRIPTION]:$m[DATA_MODEL_FIELD]);

		//Ancho y editor según tipo
		$type = $renderer =	$extras = $add = $readonly = null;
		$extraadd	= '';
		$column = '';
		if (!(isset($m[DATA_MODEL_NO_GRID]) && $m[DATA_MODEL_NO_GRID] == TRUE && $m[DATA_MODEL_SEARCH] !== TRUE))
		{
			switch ($m[DATA_MODEL_TYPE])
			{
				case DATA_MODEL_TYPE_INT:
					$width = 'Ext.app.TAM_COLUMN_NUMBER';
					$editor = 'new Ext.form.NumberField({selectOnFocus:true})';
					$xtype = 'numberfield';
					break;
				case DATA_MODEL_TYPE_DATETIME:
					$width = 'Ext.app.TAM_COLUMN_DATE';
					$editor = 'new Ext.form.DateField({format: "d/m/Y", startDay: Ext.app.DATESTARTDAY})';
					$xtype = 'datefield';
					$type = 'date';
					$extras = "dateFormat: 'timestamp', startDay: Ext.app.DATESTARTDAY";
					$extraadd = ', startDay: Ext.app.DATESTARTDAY';
					$renderer = 'renderer: Ext.app.renderDate';
					break;
				case DATA_MODEL_TYPE_TIME:
					$width = 'Ext.app.TAM_COLUMN_DATE';
					$editor = 'new Ext.form.DateField({startDay: Ext.app.DATESTARTDAY,format: "d/m/Y"})';
					$xtype = 'datefield';
					$type = 'date';
					$extras = "dateFormat: 'timestamp', startDay: Ext.app.DATESTARTDAY";
					$extraadd = ', startDay: Ext.app.DATESTARTDAY';
					$renderer = 'renderer: Ext.app.renderDate';
					break;
				case DATA_MODEL_TYPE_DATE:
					$width = 'Ext.app.TAM_COLUMN_DATE';
					$editor = 'new Ext.form.DateField({startDay: Ext.app.DATESTARTDAY,format: "d/m/Y"})';
					$xtype = 'datefield';
					$type = 'date';
					$extras = "dateFormat: 'timestamp', startDay: Ext.app.DATESTARTDAY";
					$extraadd = ', startDay: Ext.app.DATESTARTDAY';
					$renderer = 'renderer: Ext.app.renderDateShort';
					break;
				case DATA_MODEL_TYPE_STRING:
					$width = 'Ext.app.TAM_COLUMN_TEXT';
					$extras = "anchor: '95%'";
					$editor = 'new Ext.form.TextField({selectOnFocus:true})';
					$xtype = 'textfield';
					break;
				case DATA_MODEL_TYPE_ALIAS:
					$width = 'Ext.app.TAM_COLUMN_TEXT';
					$extras = "anchor: '95%'";
					if (isset($m[DATA_MODEL_NO_GRID]) && $m[DATA_MODEL_NO_GRID] == TRUE) 
						$renderer = 'hidden: true, hideable: false';
					$editor = 'new Ext.form.TextField({selectOnFocus:true})';
					$xtype = 'textfield';
					break;
				case DATA_MODEL_TYPE_BOOLEAN:
					$width = 'Ext.app.TAM_COLUMN_BOOL';
					$editor = 'new Ext.form.Checkbox()';
					$xtype = 'checkbox';
					$renderer = 'renderer: Ext.app.renderCheck';
					break;
				case DATA_MODEL_TYPE_DOUBLE:
				case DATA_MODEL_TYPE_FLOAT:
					$width = 'Ext.app.TAM_COLUMN_NUMBER';
					$editor = 'new Ext.form.NumberField({selectOnFocus:true})';
					$xtype = 'numberfield';
					break;
				case DATA_MODEL_TYPE_MONEY:
					$width = 'Ext.app.TAM_COLUMN_NUMBER';
					$editor = 'new Ext.form.NumberField()';
					$renderer = "renderer: Ext.app.euroFormatter,align: 'right'";
					$xtype = 'numberfield';
					break;
			}

			// Permite blancos?
			$allowblank = $m[DATA_MODEL_REQUIRED]?'false':'true';
			$add = "{xtype: '{$xtype}', allowBlank: {$allowblank} {$extraadd}}";
			$readonly = (isset($m[DATA_MODEL_READONLY])) ? ($m[DATA_MODEL_READONLY] ? 'true' : 'false') : 'false';

			//Si se indica tipo de editor, se crea
			if (isset($m[DATA_MODEL_EDITOR]))
			{
				$editortype = $m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_TYPE];
				switch ($editortype)
				{
					case DATA_MODEL_EDITOR_COMBO:
						//Es un combo box
						//Parámetro 1 es la URL
						$name = str_replace('/', '_', $m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM1]);
						//Crea un combo necesario
						$combos[$name] = $m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM1];
						$editor = $name;

						if (isset($m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM2]))
						{
							$renderer = "renderer: function(val, x, r) { return r.data.{$m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM2]};}";
							$field = "{name: '{$m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM2]}'}";
							$fields .= $field . ',';
						}
						else
						{
							$renderer = "renderer: function(val) {return Ext.app.renderCombo(val, {$name});}";
						}

						$add = "Ext.app.combobox({ url: \"" . site_url($m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM1]) ."\",
						id: '{$m[DATA_MODEL_FIELD]}', 
						anchor: '100%',
						fieldLabel: _s(\"{$m[DATA_MODEL_FIELD]}\"), autoload: true, allowBlank : {$allowblank} } )";
						break;
					case DATA_MODEL_EDITOR_SEARCH:
						//Es un combo box
						//Parámetro 1 es la URL
						$name = str_replace('/', '_', $m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM1]);
						$readonly = 'true';
						$fieldname = (isset($m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM2]))?$m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM2]:$m[DATA_MODEL_FIELD];
						//Crea un combo necesario
						//$combos[$name] = $m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM1];
						$renderer = "renderer: function(val, x, r) { return r.data.{$fieldname};}";
						$add2 = "Ext.app.autocomplete2({ url: \"" . site_url($m[DATA_MODEL_EDITOR][DATA_MODEL_EDITOR_PARAM1]) ."\",
						hiddenName: '{$m[DATA_MODEL_FIELD]}', name: '{$name}', 
						fieldLabel: _s(\"{$m[DATA_MODEL_FIELD]}\"), allowBlank : {$allowblank} } )";
						//$editor = $add;
						$field = "{name: '{$fieldname}', add:{$add2}, ro: false}";
						$fields .= $field . ',';
						break;
				}
			}

			// Es el default
			$default ='';
			if (((isset($m[DATA_MODEL_DEFAULT]) && ($m[DATA_MODEL_DEFAULT] === TRUE))|| (count($model) == $count+1)) && (!$default_ok))
			{
				$default = 'id :\'descripcion\',';
				$default_ok = TRUE;
			}

			if (isset($renderer)) $renderer .= ',';
			$column = "{
			header : \"{$header}\",
			width : {$width},
			{$renderer}
			{$default}
			editor : {$editor},
			sortable : true}";

			$type = (isset($type))?"type: '{$type}',": '';
			$extras = isset($extras)?$extras = ',extras: {'. $extras. '}' : '';
		}
		if (!empty($column)) $column = "column:{$column},";
		if (!empty($add)) $add = "add:{$add},";
		if (!empty($readonly)) $readonly = "ro:{$readonly}";
		$field = "{name: '{$m[DATA_MODEL_FIELD]}'{$extras}, {$type} {$column} {$add}  {$readonly}}";
		$fields .= $field . ',';
		$count++;
	}
	#echo '<pre>'; print_r($fields); die();
	//Crea los combos;
	$str_c = '';
	$stores = '';
	foreach($combos as $name => $url)
	{
		$url = site_url($url);
		$str_c .= "var {$name} = new Ext.form.ComboBox(Ext.app.combobox({url: \"{$url}\" }));";
		$stores .= "{store: {$name}.store},";
	}
	$stores = 'var stores = [ ' .substr($stores, 0, strlen($stores)-1) . '];';
	$fields = 'var model = [' . substr($fields, 0, strlen($fields) - 1) . '];';

	$cmd = str_replace('.', '/', $cmd);
	$load = $load ? 'true' : 'false';
	if (!isset($fn_pre)) $fn_pre = 'null';
	if (!isset($fn_add)) $fn_add = 'null';
	if (isset($plus)) $plus = ',' . $plus;
	$show_filter = ($show_filter)?'true':'false';
	$function = "var form_id= Ext.app.createId(); var panel =  Ext.app.createFormGrid({model: model, id: \"{$id}\",
		title: \"{$title}\", icon: \"{$icon}\", idfield: 'id',
		urlget: \"" . site_url($cmd . '/get_list') ."\",
		show_filter: {$show_filter},
		urladd: \"" . site_url($cmd . '/add') . "\",
		urlupd: \"" . site_url($cmd . '/upd') . "\",
		urldel: \"" . site_url($cmd . '/del') ."\", loadstores: stores, 
		fn_pre:{$fn_pre}, 
		fn_add:{$fn_add}, 
		load:{$load} {$plus} }); var grid = Ext.getCmp(\"{$id}_grid\");";
	if (isset($submenu))
	{
		$form = $obj->load->view($submenu, null, TRUE);
		$function .= $form;
	}
	$function .= 'return panel;';

	$end = $str_c.' '. $fields . ' ' . $stores . ' ' . $function;
	return "(function() { {$end} })();";
}

/**
 * Crea un enlace que ejecuta un comando de la aplicación
 * @param string $command Comando a ejecutar
 * @param string $text Texto a mostrar en el enlace
 * @return string
 */
function extjs_command($command, $text)
{
	return format_enlace_cmd($text, site_url($command));
}

/* End of file extjs_helper.php */
/* Location: ./system/helpers/extjs_helper.php */