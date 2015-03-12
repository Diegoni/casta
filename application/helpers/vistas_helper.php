<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**********************************************************************************
 **********************************************************************************
 * 
 * 				Botones
 * 
 * ********************************************************************************
 **********************************************************************************/

function single_button($texto=NULL, $id=NULL, $icono=NULL, $class=NULL, $type=NULL)
{
	if(is_array($class))
	{
		if(	in_array('primary', $class) || 
			in_array('danger', $class)  ||
			in_array('warning', $class) ||
			in_array('success', $class) )
		{
			$class_button = 'form-control btn btn-'.$class.' ';
		}
		else
		{
			$class_button = 'form-control btn btn-default ';	
		}
		
		foreach ($class as $key => $value) {
			$class_button .= $value.' ';
		}
	}
	else
	{
		if($class=='primary' || $class=='danger' || $class=='warning' || $class=='success')
		{
			$class_button='form-control btn btn-'.$class;
		}
		else 
		{
			$class_button='form-control btn btn-default '.$class;
		}	
	}
	
	if($type==NULL)
	{
		$type='button';
	}
	
	
	$button = "<button type='$type' class='$class_button' id='$id' name='$id' value='1'>";
	if($icono != NULL)
	{
		if(strpos($icono, 'fa-') !== false)
		{
			$button .= "<i class='fa $icono'></i> ";	
		}
		else
		if(strpos($icono, 'icon-') !== false)	 
		{
			$button .= "<span class='$icono'></span> ";	
		}
	}
	$button .= $texto."</button>";
	
	return $button;
} 
 

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
			$cadena['validacion']	= "";
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
					
	if(isset($type) )
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


function textarea_helper_horizontal($id, $value=NULL, $tamaño=NULL, $rows=NULL, $ckeditor=NULL, $required=NULL)
{
	if($ckeditor===NULL)
	{
		$ckeditor = "";
	}
	else 
	{
		$ckeditor = "ckeditor";	
	}
	
	return	"<div class='col-sm-".$tamaño."'>
				<textarea 
					class='form-control $ckeditor' 
					id='".$id."' 
					name='".$id."' 
					rows='".$rows."'
				>".$value."</textarea>
			 </div>";
}


function check_helper_horizontal($id, $value=NULL,$texto=NULL, $tamaño=NULL)
{
	if($tamaño===NULL){
		$tamaño	= 2;
	}
	
	if($value!=NULL && $value!=0)
	{
		$selected = "checked";
		$value = 1; 
	}
	else 
	{
		$selected = "";
		$value = 0;	
	}
	
	return	"<div class='col-sm-".$tamaño."'>
      			<div class='checkbox'>
        			<label>
          				<input
          					id		= '".$id."'
          					name	= '".$id."'
          					type	= 'checkbox'
          					value	= '".$value."'
          					onclick='
          					if(this.checked)
          					{
          						this.value = 1;
          					}
          					else 
          					{
          						this.value = 0;
							}
          					'
          					".$selected."
          				> ".$texto."
        			</label>
      			</div>
    		</div>";	
}


function select_helper_horizontal($id, $options=NULL, $value=NULL, $tamaño=NULL, $required=NULL)
{	
	if($tamaño===NULL)
	{
		$tamaño	= 10;
	}	

	$select = "<div class='col-sm-".$tamaño."'>";
		
	if(isset($required))
	{
		$select .= "<div class='input-group'>";	
	}
		
	$select	.= "<select class='form-control chosen-select' name='".$id."' id='".$id."'";
	
	if(isset($required))
	{
		$select .= "required >";	
	}
	else
	{
		$select .= ">";
	}
	
	$select	.=	"<option value=''></option>";
	foreach ($options as $row) {
		if(isset($row->descripcion)){
			$select .= "<option value='".$row->id_tabla."'";
			if($row->id_tabla==$value && $value!=NULL)
			{
				 $select .= "selected";
			}
			$select .=">";
			$select .= $row->descripcion."</option>";	
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


function autocomplete($array, $input, $valor, $id_array = NULL, $id_input = NULL)
{
	$id=0;
	$autocomplete =
	"
	<script>
	$(function() {
    var raw = [";
      	if(count($array)>0 && is_array($array)){		
			foreach ($array as $row) {
				$cadena = "{ value: ";	
				if(isset($row->id_customer))
				{
					$cadena .= $row->id_customer;//arreglar	
				}
				else
				{
					$cadena .= $row->id_product;//arreglar
				}
				
				$id = $id + 1;
				$cadena .= ", label: '";
				
				if(is_array($valor)){
					foreach ($valor as $key => $value) {
						$cadena .= $row->$value." ";	
					}		
				}
				else {
					$autocomplete .= '"'.$row->$valor.'",';	
				}
				
				$cadena .= "' },";
				
				$autocomplete .= $cadena;
			}
		}
		else
		{
			$autocomplete .= " ";
		}
		
	$autocomplete = trim($autocomplete, ',');
		
    $autocomplete .=
    "];
   var source  = [ ];
	var mapping = { };
	for(var i = 0; i < raw.length; ++i) {
	    source.push(raw[i].label);
	    mapping[raw[i].label] = raw[i].value;
	}
	
	$('#$input').autocomplete({
	    minLength: 1,
	    source: source,
	    select: function(event, ui) {
	    	$('#$id_input').val('');
	        $('#$id_input').val(mapping[ui.item.value]);
	    }
	});
	});
	</script>";
	
	return $autocomplete;
}


?>