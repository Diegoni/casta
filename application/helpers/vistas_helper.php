<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**********************************************************************************
 **********************************************************************************
 * 
 * 				Botones
 * 
 * ********************************************************************************
 **********************************************************************************/


function add_button($texto=NULL)
{
	if(!isset($texto)){
		$texto = 'Nuevo';
	}
	return "<button class='btn btn-default'>
				<i class='fa fa-plus-square'></i> ".$texto."
			</button>";
}

function save_button($texto=NULL)
{
	if(!isset($texto)){
		$texto = 'Guardar';
	}
	return "<button class='btn btn-default' type='submit' name='guardar' value='1'>
				<i class='fa fa-pencil-square-o'></i> ".$texto."
			</button>";
}

function refresh_button($texto=NULL)
{
	if(!isset($texto)){
		$texto = 'Refresh';
	}
	return "<button class='btn btn-default'>
				<i class='fa fa-refresh'></i> ".$texto."
			</button>";
}

function print_button($texto=NULL)
{
	if(!isset($texto)){
		$texto = 'Imprimir';
	}
	return "<div class='btn-group'>
				<button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
					<i class='fa fa-print'></i> ".$texto." <span class='caret'></span>
				</button>
				<ul class='dropdown-menu' role='menu'>
					<li><a href='#'><i class='fa fa-print'></i></span> Imprimir</a></li>
					<li><a href='#'><i class='fa fa-file-pdf-o'></i> PDF</a></li>
				</ul>
			</div>";
}

function action_button($texto=NULL)
{
	if(!isset($texto)){
		$texto = 'Acciones';
	}
	return "<div class='btn-group'>
				<button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false' disabled>
					<i class='fa fa-cogs'></i> ".$texto." <span class='caret'></span>
				</button>
				<ul class='dropdown-menu' role='menu'>
					<li><a href='#'>Action</a></li>
				</ul>
			</div>";
}


/**********************************************************************************
 **********************************************************************************
 * 
 * 				Formularios
 * 
 * ********************************************************************************
 **********************************************************************************/
 
 
function setTipo($type){
		//verifica el tipo, las validaciones se hacen en librerias/main/js/main.js -> Validación de formularios
		if($type=='email')
		{
			$cadena['input']		= $type;
			$cadena['validacion']	= "data-validate='$type'";
		}
		else if($type=='phone')
		{
			$cadena['input']	= 'text';
			$cadena['validacion']	= "data-validate='$type'";
		}
		else if($type=='number')
		{
			$cadena['input']	= $type;
			$cadena['validacion']	= "data-validate='$type'";
		}
		else if($type=='text')
		{
			$cadena['input']	= $type;
			$cadena['validacion']	= "data-validate='$type'";
		}
		else if(strstr($type, 'length '))
		{
			$cantidad	= substr($type, 7);
			$cadena['input']	= 'text';
			$cadena['validacion']	= "data-validate='length' data-length='$cantidad'";
		}
		
		return $cadena;	
} 
 
 
function input_helper_horizontal($id, $value=NULL, $tamaño=NULL, $placeholder=NULL, $type=NULL)
{
	if($type===NULL)
	{
		$type_input	= "text";
	}
	else 
	{
		$cadena = setTipo($type);
		
		$type_input = $cadena['input'];
		$validacion = $cadena['validacion'];		
	}
	
	if($tamaño===NULL)
	{
		$tamaño	= 10;
	}
	
	if($placeholder===NULL)
	{
		$placeholder	= '-';
	}
	
	$input = "<div class='col-sm-".$tamaño."'>";
	
	if(isset($type))
	{
		$input .=	"<div class='input-group' $validacion>";
	}
	
	$input	.=		"<input 
					type		= '".$type_input."' 
					class		= 'form-control' 
					id			= '".$id."' 
					name		= '".$id."'
					value		= '".$value."' 
					placeholder	= '".$placeholder."'";
					
	if(isset($type))
	{
		$input .= "required >
					<span class='input-group-addon danger'>
						<span class='glyphicon glyphicon-remove'>
						</span>
					</span>
					</div></div>";
	}
	else 
	{
		$input .= "></div>";	
	}
	
	return $input;
}


function label_helper_horizontal($texto=NULL, $tamaño=NULL)
{
	if($tamaño===NULL){
		$tamaño	= 2;
	}
	if($texto===NULL){
		$texto	= '';
	}
	return "<label class='col-sm-".$tamaño." control-label'>
    			".$texto."
    		</label>";
}


function textarea_helper_horizontal($id, $value=NULL, $tamaño=NULL, $rows=NULL)
{
	return	"<div class='col-sm-".$tamaño."'>
				<textarea 
					class='form-control' 
					id='".$id."' 
					name='".$id."' 
					rows='".$rows."'
				>
				".$value."
				</textarea>
			 </div>";
}


function check_helper_horizontal($id, $texto=NULL, $tamaño=NULL)
{
	if($tamaño===NULL){
		$tamaño	= 2;
	}
	
	return	"<div class='col-sm-".$tamaño."'>
      			<div class='checkbox'>
        			<label>
          				<input
          					id		= '".$id."'
          					name	= '".$id."'
          					type	= 'checkbox'
          					value	= 1
          				> ".$texto."
        			</label>
      			</div>
    		</div>";	
}


function select_helper_horizontal($id, $value=NULL, $tamaño=NULL, $required=NULL)
{	
	if($value===NULL)
	{
		
	}
	
	if($tamaño===NULL)
	{
		$tamaño	= 10;
	}	

	$select = "<div class='col-sm-".$tamaño."'>";
		
	if(isset($required))
	{
		$select .= "<div class='input-group'>";	
	}
		
	$select	.= "<select class='form-control chosen-select' name='".$id."'";
	
	if(isset($required))
	{
		$select .= "required >";	
	}
	else
	{
		$select .= ">";
	}
	
	$select	.=	"<option value=''></option>";
	foreach ($value as $row) {
		if(isset($row->descripcion)){
			$select .= "<option value='".$row->id_tabla."'>".$row->descripcion."</option>";	
		}
		
	}  
	$select	.= "</select>";
	
	if(isset($required))
	{
		$select .= "<span class='input-group-addon danger'>
						<span class='glyphicon glyphicon-remove'>
						</span>
					</span>
					</div>
					</div>";	
	}
	else
	{
		$select .= "</div>";
	}
	
	
	return $select;
}


/**********************************************************************************
 **********************************************************************************
 * 
 * 				Menús y sub menús
 * 
 * ********************************************************************************
 **********************************************************************************/

 
function sub_menu($datos)
{
	//Opciones del sub menu
	$opciones = array
		(
			'general'	=> "<li><a href='#general' class='sub-item' data-toggle='tab'><i class='fa fa-home'></i> ",
			'temas'		=> "<li><a href='#temas' class='sub-item' data-toggle='tab'><i class='fa fa-list-alt'></i> ",
			'notas'		=> "<li><a href='#notas' class='sub-item' data-toggle='tab'><i class='fa fa-file-text-o'></i> ",
			'usuarios'	=> "<li><a href='#usuarios' class='sub-item' data-toggle='tab'><i class='fa fa-users'></i> ",
			'historico'	=> "<li><a href='#historico' class='sub-item' data-toggle='tab'><i class='fa fa-history'></i> ",
			'perfiles'	=> "<li><a href='#perfiles' class='sub-item' data-toggle='tab'><i class='fa fa-book'></i> ",
			'busqueda'	=> "<li><a href='#busqueda' class='sub-item' data-toggle='tab'><i class='fa fa-search'></i> ",
		);
	
	//Armado del sub menu
	$mensaje = "<ul class='nav nav-tabs'>";
	
	foreach ($datos as $key => $value) {
		if(isset($opciones[$key])){
			$mensaje .= $opciones[$key].$value."</a></li>";	
		}
	}
	
	$mensaje .= "</ul>"; 
    			
	return $mensaje;
}


function autocomplete($array, $input, $valor)
{
	$autocomplete =
	"
	<script>
	$(function() {
    var clientes_array = [";
      	if(count($array)>0 && is_array($array)){		
			foreach ($array as $row) {
				if(is_array($valor)){
					$cadena = '"';
					foreach ($valor as $key => $value) {
						$cadena .= $row->$value." ";	
					}
					$cadena .=	'",';
									
					$autocomplete .=$cadena;		
				}
				else {
					$autocomplete .= '"'.$row->$valor.'",';	
				}
			}
		}
		else
		{
			$autocomplete .= " ";
		}
		
    $autocomplete .=
    "];
    $( '#$input' ).autocomplete({
      minLength: 2, 
      source: function(request, response) {
			var results = $.ui.autocomplete.filter(clientes_array, request.term);
			response(results.slice(0, 10));
    		}
    });
	});
	</script>";
	
	return $autocomplete;
}
?>