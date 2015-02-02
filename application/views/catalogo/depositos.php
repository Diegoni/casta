<?php $this->load->helper('asset');?>
<?php echo css_asset('icons.css', 'main');?>
<?php echo css_asset('jquery-ui-1.9.2.custom.css');?>
<h1><?php echo $this->lang->line('Depósitos');?></h1>

                <div class="ui-widget">
                    <input class="auto"/>
                </div>

<span id="msg"></span>

<table class="table table-striped table-bordered sortable">
	<thead>
	<tr>
		<th><?php echo $this->lang->line('Sección');?></th>
		<th><?php echo $this->lang->line('ISBN');?></th>
		<th><?php echo $this->lang->line('Título');?></th>
		<th><?php echo $this->lang->line('nStockDeposito');?></th>
        <th><?php echo $this->lang->line('Proveedor');?></th>
		<th><?php echo $this->lang->line('Albarán');?></th>
        <th><?php echo $this->lang->line('Fecha');?></th>
        <th><?php echo $this->lang->line('dVencimiento');?></th>
        <th><?php echo $this->lang->line('Cantidad');?></th>
	</tr>
</thead>
<tbody id="datos">
</tbody>
</table>
<p>
<small><span id="count">0</span> <?php echo $this->lang->line('registros encontrados');?></small>
</p>
<?php echo js_asset('jQuery/jquery.min.js');?>
<?php echo js_asset('jQuery/jquery-ui-1.9.2.custom.min.js');?>
<?php echo js_asset('sorttable.js');?>

<script type="text/javascript">
function enlace_js(texto, js, style){
    if (style == null) 
        style = 'cmd-link';
    return "<span class='" + style + "'><a href=\"javascript:" + js + ";\">" + texto + "</a></span>";
}
var enlace_cmd = function (texto, url, style){
    url = parent.site_url(url);
    var js = "parent.Ext.app.execCmd({url: '" + url + "'});";
    return enlace_js(texto, js, style);
}

	$(function() {

        var cache = {};
        var count = 0;
        jQuery( ".auto" ).autocomplete({
            minLength: 4,
            select: function( event, ui ) {
                var id = ui.item.id;
                var all = '';
                $('#msg').html('<div class="alert alert-block">' + parent._s('Cargando') + '</div>');
                $.getJSON( "<?php echo site_url('catalogo/articulo/depositos');?>", {id: id}, function( data, status, xhr ) {
                    $.each(data.value_data, function (index, item) {
                        var t = "<tr id='" + item.nIdLibro + '_' + item.nIdSeccion + "'>" +                
                        "<td class='alert'>" + item.cNombre + "</td>" + 
                        "<td>" + item.cISBN + "</td>" + 
                        "<td>" + enlace_cmd(item.cTitulo, 'catalogo/articulo/index/' + item.nIdLibro) + "</td>" + 
                        "<td><span class='badge badge-success'>" + item.nStockDeposito + "</span></td>" + 
                        "<td class='alert alert-info'>" + item.cProveedor + "</td>" + 
                        "<td>" + enlace_cmd(item.nIdAlbaran, 'compras/albaranentrada/index/' + item.nIdAlbaran) + "</td>" + 
                        "<td>" + item.dFecha + "</td>" + 
                        "<td>" + item.dVencimiento + "</td>" + 
                        "<td>" + item.nCantidad + "</td>" + 
                        "<td align=\"center\">" + 
                        "<button class=\"btn btn-danger acc\" rel=\"" + item.nIdLibro + '_' + item.nIdSeccion + "\"><?php echo $this->lang->line('Firme');?></button>" +
                        "</td>" + 
                        "</tr>";
                        all += t;
                    });                
                    $('#datos').html(all);
                    $('#count').html(data.total_data);
                    $('#msg').html('');
                    count = parseInt(data.total_data);

                    jQuery('.acc').bind('click', function(item) {
                        item.preventDefault();
                        $(this).prop('disabled', true);
                        var id = item.currentTarget.attributes.getNamedItem('rel').value;
                        var v = id.split('_');
                        parent.Ext.app.callRemote({
                            url: "<?php echo site_url('catalogo/articulo/firme');?>",
                            params: {
                                id: parseInt(v[0]),
                                ids: parseInt(v[1])
                            },
                            fnok: function(res)
                            {
                                jQuery('#' + id).fadeOut('slow');
                                --count;
                                $('#count').html(count);
                            }
                        });
                        return;
                    });
                });
            },
            source: function( request, response ) {
                var term = request.term;
                if ( term in cache ) {
                    response( cache[ term ] );
                    return;
                }
 
                $.getJSON( "<?php echo site_url('generico/seccion/search');?>", {query: request.term}, function( data, status, xhr ) {
                    var d = [];
                    $.each(data.value_data, function (index, item) {
                        d[d.length] = {id: item.id, label: item.text};
                    });
                    cache[ term ] = d;
                    response( d );
                });
            }
        });

	});
</script>
