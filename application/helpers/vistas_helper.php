<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**********************************************************************************
 **********************************************************************************
 * 
 * 				Botones
 * 
 * ********************************************************************************
 **********************************************************************************/


function add_button()
{
	return "<button class='btn btn-default'>
				<i class='fa fa-plus-square'></i> Nuevo
			</button>";
}

function edit_button()
{
	return "<button class='btn btn-default'>
				<i class='fa fa-pencil-square-o'></i> Guardar
			</button>";
}

function refresh_button()
{
	return "<button class='btn btn-default'>
				<i class='fa fa-refresh'></i> Refrescar
			</button>";
}

function print_button()
{
	return "<div class='btn-group'>
				<button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
					<i class='fa fa-print'></i> Imprimir <span class='caret'></span>
				</button>
				<ul class='dropdown-menu' role='menu'>
					<li><a href='#'><i class='fa fa-print'></i></span> Imprimir</a></li>
					<li><a href='#'><i class='fa fa-file-pdf-o'></i> PDF</a></li>
				</ul>
			</div>";
}

function action_button()
{
	return "<div class='btn-group'>
				<button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
					<i class='fa fa-cogs'></i></span> Acciones <span class='caret'></span>
				</button>
				<ul class='dropdown-menu' role='menu'>
					<li><a href='#'>Action</a></li>
				</ul>
			</div>";
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
			'general'	=> "<li role='.general.'><a href='#'>General</a></li>",
			'temas'		=> "<li role='temas'><a href='#'>Temas</a></li>",
			'notas'		=> "<li role='notas'><a href='#'>Notas</a></li>",
			'usuarios'	=> "<li role='usuarios'><a href='#'>Usuarios</a></li>",
			'historico'	=> "<li role='historico'><a href='#'>Historico</a></li>",
			'perfiles'	=> "<li role='perfiles'><a href='#'>Perfiles</a></li>",
			'busqueda'	=> "<li role='busqueda'><a href='#'>Búsqueda</a></li>",
		);
	
	//Armado del sub menu
	$mensaje = "<ul class='nav nav-tabs'>";
	
	foreach ($datos as $key => $value) {
		if(isset($opciones[$value])){
			$mensaje .= $opciones[$value];	
		}
	}
	
	$mensaje .= "</ul>"; 
    			
	return $mensaje;
}

?>