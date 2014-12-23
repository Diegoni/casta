/**
 * Fichero de textos
 */
var Ext = Ext || {};

Ext.lang = {
    'title': "Bibliopola"
	<?php if (isset($lang)): ?>
	<?php foreach($lang as $k => $v): ?>
	<?php $v = str_replace("'", "\\'", $v); ?>
	<?php echo ",'{$k}' : '{$v}'\n";?>
	<?php endforeach; ?>
	<?php endif; ?>
};

/**
 * Devuelve la traducción de un texto
 * @param {Object} text
 */
function _s(text)
{
	if (Ext.lang != null)
	{
		if (Ext.lang[text] != null)
			return Ext.lang[text];
	}
	return '*' + text + '*';
}
