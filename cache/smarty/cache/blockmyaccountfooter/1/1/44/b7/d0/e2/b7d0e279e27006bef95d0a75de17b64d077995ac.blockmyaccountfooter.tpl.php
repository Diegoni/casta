<?php /*%%SmartyHeaderCode:743055524cd6984146-62876309%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b7d0e279e27006bef95d0a75de17b64d077995ac' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\casta\\themes\\default-bootstrap\\modules\\blockmyaccountfooter\\blockmyaccountfooter.tpl',
      1 => 1424703478,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '743055524cd6984146-62876309',
  'variables' => 
  array (
    'link' => 0,
    'returnAllowed' => 0,
    'voucherAllowed' => 0,
    'HOOK_BLOCK_MY_ACCOUNT' => 0,
    'is_logged' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_55524cd6ae7916_52033825',
  'cache_lifetime' => 31536000,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55524cd6ae7916_52033825')) {function content_55524cd6ae7916_52033825($_smarty_tpl) {?>
<!-- Block myaccount module -->
<section class="footer-block col-xs-12 col-sm-4">
	<h4><a href="http://localhost/casta/es/mi-cuenta" title="Administrar mi cuenta de cliente" rel="nofollow">Mi cuenta</a></h4>
	<div class="block_content toggle-footer">
		<ul class="bullet">
			<li><a href="http://localhost/casta/es/historial-compra" title="Mis compras" rel="nofollow">Mis compras</a></li>
						<li><a href="http://localhost/casta/es/albaran" title="Mis vales descuento" rel="nofollow">Mis vales descuento</a></li>
			<li><a href="http://localhost/casta/es/direcciones" title="Mis direcciones" rel="nofollow">Mis direcciones</a></li>
			<li><a href="http://localhost/casta/es/datos-personales" title="Administrar mi información personal" rel="nofollow">Mis datos personales</a></li>
						
            <li><a href="http://localhost/casta/es/?mylogout" title="Cerrar sesión" rel="nofollow">Cerrar sesión</a></li>		</ul>
	</div>
</section>
<!-- /Block myaccount module -->
<?php }} ?>
