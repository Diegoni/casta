<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/sincronizar/actualizar_clientes.php';
require_once DOL_DOCUMENT_ROOT.'/sincronizar/actualizar_direcciones.php';
require_once DOL_DOCUMENT_ROOT.'/sincronizar/actualizar_productos.php';


class Actualizar_menu extends CommonObject
{
	// Opciones de menú	
	var $productos	= 'products';
	var $terceros	= 'companies';
	
	function __construct($db)
	{
		$this->db = $db;
	}
	
	function buscar_actualizacion($mainmenu)
	{
		if($mainmenu == $this->productos)
		{
			echo $mainmenu;
		}
		else
		if($mainmenu == $this->terceros)	
		{
			echo $mainmenu;
		}
			
	}
}