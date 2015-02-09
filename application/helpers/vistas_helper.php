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
				<button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
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
 
function input_helper_horizontal($id, $value=NULL, $tamaño=NULL, $placeholder=NULL, $type=NULL)
{
	if($type===NULL)
	{
		$type	= "text";
	}
	
	if($value===NULL)
	{
		
	}
	
	if($tamaño===NULL)
	{
		$tamaño	= 10;
	}
	
	if($placeholder===NULL)
	{
		$placeholder	= '-';
	}
	
	return "<div class='col-sm-".$tamaño."'>
				<input 
					type		= '".$type."' 
					class		= 'form-control' 
					id			= '".$id."' 
					name		= '".$id."'
					value		= '".$value."' 
					placeholder	= '".$placeholder."'
				>
			</div>";
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
          				> ".$texto."
        			</label>
      			</div>
    		</div>";	
}

function select_helper_horizontal($id, $value=NULL, $tamaño=NULL)
{	
	if($value===NULL)
	{
		
	}
	
	if($tamaño===NULL)
	{
		$tamaño	= 10;
	}	

	$select = "<div class='col-sm-".$tamaño."'>";		
	$select	.= "<select class='form-control chosen-select' name='".$id."'>
				<option value=''></option>";
	foreach ($value as $row) {
		if(isset($row->descripcion)){
			$select .= "<option value='".$row->id_tabla."'>".$row->descripcion."</option>";	
		}
		
	}  
	$select	.= "</select></div>";
	
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
			'general'	=> "<li role='.general.'><a href='#' class='sub-item'><i class='fa fa-home'></i> ",
			'temas'		=> "<li role='temas'><a href='#' class='sub-item'><i class='fa fa-list-alt'></i> ",
			'notas'		=> "<li role='notas'><a href='#' class='sub-item'><i class='fa fa-file-text-o'></i> ",
			'usuarios'	=> "<li role='usuarios'><a href='#' class='sub-item'><i class='fa fa-users'></i> ",
			'historico'	=> "<li role='historico'><a href='#' class='sub-item'><i class='fa fa-history'></i> ",
			'perfiles'	=> "<li role='perfiles'><a href='#' class='sub-item'><i class='fa fa-book'></i> ",
			'busqueda'	=> "<li role='busqueda'><a href='#' class='sub-item'><i class='fa fa-search'></i> ",
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



/**********************************************************************************
 **********************************************************************************
 * 
 * 				Mensajes
 * 
 * ********************************************************************************
 **********************************************************************************/

?>