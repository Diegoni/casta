<?php $this->load->helper('asset');?>
<?php echo css_asset('watable.css');?>
<?php echo css_asset('DT_bootstrap.css', 'beta2'); ?>
<?php #echo css_asset('fuelux-responsive.min.css', 'beta2'); ?>
 <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
.typeahead {
max-height: 200px;
overflow-y: auto;
overflow-x: hidden;
}
</style>
<div id="form_actions">
    <span class='add-on' style="margin-left: 10px;"><?php echo $this->lang->line('Materia');?></span>
    <input type="text" style="margin-bottom: 0;" id="materia" />
    <a id="asignar" class="btn btn-danger" href="#"><i class="icon-check"></i><?php echo $this->lang->line('Asignar');?></a>
</div>

 <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <div class="nav-collapse collapse">
            <div class='input-prepend' style="margin-top: 7px;">
                <span class='add-on'><?php echo $this->lang->line('Sección');?></span>
                <input type="text" style="margin-bottom: 0;" id="seccion" />
                <a id="actualizar" class="btn" href="#"><i class="icon-search"></i>Buscar</a>                
                <span class='add-on' style="margin-left: 10px;margin-right: 10px;"><?php echo $this->lang->line('Con ventas');?>
                <input type="checkbox" style="margin-bottom: 0;" id="conventas" checked /></span>
            </div>            
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
 <div class="container">

<div class="row-fluid">
    <div class="span12">

<div class="alert alert-info" id="help">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php echo $this->lang->line('sinmateria-help'); ?>
</div>  
<div id="temp"></div>

<div id = "alert_placeholder"></div>      
        <table class="table table-bordered table-striped table_vam" id="dt_gal">
            <thead>
                <tr>
                    <!--<th class="table_checkbox"><input type="checkbox" name="select_rows" class="select_rows" data-tableid="dt_gal" /></th>-->
                    <th><i class="icon-picture"></i></th>
                    <th><?php echo $this->lang->line('Id');?></th>
                    <th><?php echo $this->lang->line('cTitulo');?></th>
                    <th><?php echo $this->lang->line('cAutores');?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        
    </div>
</div>

</div>
<?php echo js_asset('underscore.min.js');?>
<?php echo js_asset('jquery.watable.js');?>
<?php #echo js_asset('jquery.activity-indicator-1.0.0.min.js');?>
<?php echo js_asset('jquery.dataTables.js', 'beta2'); ?>
<?php echo js_asset('DT_bootstrap.js', 'beta2'); ?>
<?php #echo js_asset('combobox.js', 'beta2'); ?>

<script type="text/javascript">
	$(function() {

        $('#form_actions').hide();

        var bootstrap_alert = new function() {
            var createAutoClosingAlert = function (selector, delay) {
               var alert = selector.alert();
                    window.setTimeout(function() {
                        selector.fadeTo(500, 0).slideUp(500, function(){
                            $(this).remove(); 
                        });
                    }, delay);
               //window.setTimeout(function() { alert.alert('close') }, delay);
            }
            var msg = function(clase, message, delay) {
                    var div = $('<div/>', {
                        'class': clase,
                        html: '<button type="button" class="close" data-dismiss="alert">&times;</button><span>' + message +'</span>'
                    }).appendTo('#alert_placeholder');
                    if (delay > 0)
                        createAutoClosingAlert(div, delay);
                }

            return {
                warning : function(message, delay) {
                    msg('alert', message, delay);
                },
                error : function(message, delay) {
                    msg('alert alert-error', message, delay);
                },
                success : function(message, delay) {
                    msg('alert alert-success', message, delay);
                },
                info : function(message, delay) {
                    msg('alert alert-info', message, delay);
                },
            }
        }

        var controlBox = function (id, url) {
            this.sel_id = null;
            this.search = _.debounce(function( query, process, obj ){
                if(obj.finished != null && !obj.finished) {
                    return;
                }
                obj.finished = false;
                parent.Ext.app.callRemote({
                    url: url,
                    params: {
                        query: query
                    },
                    fnok: function(res) {
                        //console.log('Datos leídos ' + res.value_data.length);
                        var data = new Array();                     
                        jQuery.each(res.value_data, function(index, response) {
                            data.push(response.id + '###' + response.text);
                        });
                        //console.dir(data);
                        process(data);
                        obj.finished = true;
                    }
                });
            }, 300);
            var me = this;
            $(id).typeahead({
                items: 9999,
                source: function(query, process) {
                    me.search(query, process, this);
                },
                matcher: function(item) {
                    return true;
                },
                highlighter: function(item) {
                    //console.log('highlighter ' + item);
                    var parts = item.split('###');
                    parts.shift();
                    return parts.join('###');
                },
                updater: function(item) {
                    var parts = item.split('###');
                    me.sel_id = parts.shift();
                    //console.log('Seleccionado: ' + me.sel_id);
                    //jQuery('#seccion_id').val(parts.shift());
                    return parts.join('###');
                },
                minLength: 1
            });

            $(id).on('focus', $(id).typeahead.bind($(id), 'lookup'));

            this.getId = function () {
                return me.sel_id;
            }
        }

        var objSc = new controlBox('#seccion', parent.site_url('generico/seccion/search'));
        var objMt = new controlBox('#materia', parent.site_url('catalogo/materia/search'));

        var fnGetKey = function( aoData, sKey ) {
            for ( var i=0, iLen=aoData.length ; i<iLen ; i++ )
            {
                if ( aoData[i].name == sKey )
                {
                    return aoData[i].value;
                }
            }
            return null;
        }

        var pageAct = 0;
        var fnDataTablesPipeline = function( sSource, aoData, fnCallback ) {
            var sEcho = fnGetKey(aoData, "sEcho");
            var iRequestStart = fnGetKey(aoData, "iDisplayStart");
            var iRequestLength = fnGetKey(aoData, "iDisplayLength");
            var iRequestEnd = iRequestStart + iRequestLength;
            var conventas = $('#conventas').is(":checked");

            pageAct = (iRequestStart>0)?((iRequestStart / iRequestLength)):0;
            var id = objSc.getId();
            if (id > 0) {
                $('#actualizar').toggleClass('disabled');
                $('#form_actions').hide();
                $("#temp").prepend($('#form_actions'));
                $("#dt_gal").hide();

                parent.Ext.app.callRemote({
                    url: parent.site_url('catalogo/articulo/sinmateria'),
                    params: {
                        id: parseInt(id),
                        conventas: (conventas == 'on'),
                        start: iRequestStart,
                        limit: iRequestLength
                    },
                    fnok: function(res) {
                        //$('#dt_gal').activity(false);
                        $('#actualizar').toggleClass('disabled', false);
                        var data = new Array();                     
                        jQuery.each(res.value_data, function(index, response) {
                            var img = parent.Ext.app.getPortada(50).replace('{id}', response.nIdLibro);
                            //var img = '<img src="' + parent.Ext.app.getPortada(50).replace('{id}', response.nIdLibro) 
                            //+ '" class="img-polaroid" alt="" title="' + response.nIdLibro + '"/>';
                            //console.log(img);
                            data.push([/*'<input type="checkbox" />', */
                                img,
                                response.nIdLibro, 
                                response.cTitulo, 
                                response.cAutores]);
                        });

                        var json = {
                            //"sEcho": 3,
                            "iTotalRecords": res.total_data,
                            "iTotalDisplayRecords": res.total_data,
                            "aaData": data
                        };
                        fnCallback(json);
                        $("#dt_gal").show();
                        acciones();
                    }
                });
            } else {
                var json = {
                    "sEcho":0,
                    "iTotalRecords": 0,
                    "iTotalDisplayRecords": 0,
                    "aaData": []
                };
                fnCallback(json)
            }
        }

       var oTable = $('#dt_gal').dataTable({
            "sDom": "<'row'<'span6'<'dt_actions'>l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
            "sPaginationType": "bootstrap",
            "bProcessing": true,
            "bServerSide": true,
            "bFilter": false,
            "bSort": false,
            "iDisplayLength": 50,
            "bLengthChange": false,
            "sAjaxSource": parent.site_url('catalogo/articulo/sinmateria'),
            "fnServerData": fnDataTablesPipeline,
            //"aaSorting": [[ 2, "asc" ]],
            "aoColumns": [
                /*{ "bSortable": false, sWidth: '16px' },*/
                { "bSortable": false, sWidth: '50px' },
                { "bSortable": true, sWidth: '30px' },
                { "sType": "string" },
                { "sType": "string" }
            ],
            "oLanguage": {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        });        

        /*$("#dt_gal input:checkbox").click(function() {
            var checkedStatus = this.checked;
            $("#dt_gal tbody tr td:first-child input:checkbox").each(function() {
                this.checked = checkedStatus;
                if (checkedStatus == this.checked) {
                    $(this).closest('.checker > span').removeClass('checked');
                }
                if (this.checked) {
                    $(this).closest('.checker > span').addClass('checked');
                }
            });
        }); */

        // Botón de actualizar
        $('#actualizar').bind('click', function(item) {
            item.preventDefault();
            $('#form_actions').hide();
            $("#temp").prepend($('#form_actions'));
            oTable.fnPageChange( 'first' );

            return;
        });

        var last_id = null;
        var last_tr = null;
        // Botón de actualizar
        $('#asignar').bind('click', function(item) {
            item.preventDefault();
            var id = objMt.getId();
            if (id < 1) {
                $('#materia').focus();
            }

            /*var ids = '';

            $("#dt_gal tbody tr td:first-child input:checkbox").each(function() {
                var tr = $(this).parents('tr')[0];
                var d = oTable.fnGetData(tr);
                if (this.checked) {
                    ids += ';' + d[1];
                    //console.log('checked ' + d[1])
                }
            });*/
            if (last_id != null) {
                /*var ids = [last_id];
                if (ids.length > 0) {*/
                    $('#asignar').toggleClass('disabled');
                    //$('#dt_gal').activity();
                    parent.Ext.app.callRemote({
                        nomsg: true,
                        url: parent.site_url('catalogo/articulo/asignar_materia'),
                        params: {
                            mat: parseInt(id),
                            ids: /*ids*/ last_id
                        },
                        fnok: function(res) {
                            bootstrap_alert.success(res.message, 5000);
                            //$('#dt_gal').activity(false);
                            $('#asignar').toggleClass('disabled', false);
                            //last_tr.fadeOut('slow');
                            $(last_tr).fadeOut('slow');
                            //oTable.fnPageChange( pageAct );
                        },
                        fnnok: function(res) {
                            bootstrap_alert.error(res.message, 5000);
                            //$('#dt_gal').activity(false);
                            $('#asignar').toggleClass('disabled', false);                        
                        }
                    });
                /*}*/
            }

            return;
        });

        var acciones = function() {

            $("#dt_gal tbody tr").on("mouseenter",function(){
                var d = oTable.fnGetData(this);
                last_id  = d[1];
                last_tr = this;
                $('#form_actions').show();
                $(this).find("td:last-child").prepend($('#form_actions'))
                //$(this).find("td:last-child").html('<a href="javascript:void(0);" onClick="editrow('+$(this).attr("id")+')">Edit</a>&nbsp;&nbsp;<a href="javascript:void(0);" onClick="deleterow('+$(this).attr("id")+')">Delete</a>');    
            });//

            // remove button on tr mouseleave
            $("#dt_gal tbody tr").on("mouseleave",function(){
                $('#form_actions').hide();
                last_id = null;
                //$(this).find("td:last-child").html("&nbsp;");    
            });

            // TD click event
            $("#dt_gal tbody tr").on("dblclick",function(event){

                var d = oTable.fnGetData(this);
                var url = parent.site_url('catalogo/articulo/index/' + d[1]);
                parent.Ext.app.execCmd({url: url});
            });
        }

        //$('#help').alert();
    
        /*$('#seccion').popover({
            trigger: 'over',
            //title: 'Sección',
            placement: 'bottom',
            //content: 'Selecciona la seccion'
            html: 'true',
            title : '<span class="text-info"><strong>title</strong></span> <button     type="button" id="close" class="close">&times;</button></span>',
            content : "Porque mierdas no funciona esto?"
        });*/

	});
</script>
