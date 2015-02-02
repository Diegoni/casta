<?php
function sub_menu($menu)
{
	$items = array();
	foreach ($menu as $m)
	{
		$submenu = (isset($m['children']))?sub_menu($m['children']):'null';
		if ($m['text'] != '-')
		{
			$id = (isset($m['id']))?$m['id']:'null';
			$cmd = isset($m['id'])?"Ext.app.execCmd({
                                        id: \"{$m['id']}\",
                                        timeout: false,
                                        title: \"{$m['text']}\",
                                        icon: \"{$m['iconCls']}Tab\"
                                    });":'';
			$item = "{
			//xtype: 'tbbutton',
			text: \"{$m['text']}\",
            iconCls: \"{$m['iconCls']}\",
            handler: function() {{$cmd}},             
            menu: {$submenu}
	        }";
		}
		else
		{
			$item = '\'-\'';
		}

		$items[] = $item;
	}
	return '[' . implode(', ', $items) . ']';
}

echo "var createMenuFunction = function(startMenu){\n";

foreach($menu as $m)
{
	$submenu = (isset($m['children']))?sub_menu($m['children']):'null';
	echo ($m['text'] != '-')?"startMenu.add({
		text: \"{$m['text']}\", 
		iconCls: \"{$m['iconCls']}\",
		menu: {$submenu},
		scope: this 
		});\n":"startMenu.add('-');\n";
}

echo "}\n";
