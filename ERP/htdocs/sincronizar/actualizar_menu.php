<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

/*----------------------------------------------------------------
		Cargamos las clases para hacer la sincronizacion
----------------------------------------------------------------*/

require_once DOL_DOCUMENT_ROOT.'/sincronizar/actualizar_clientes.php';
require_once DOL_DOCUMENT_ROOT.'/sincronizar/actualizar_direcciones.php';
require_once DOL_DOCUMENT_ROOT.'/sincronizar/actualizar_productos.php';
require_once DOL_DOCUMENT_ROOT.'/sincronizar/actualizar_pedidos.php';
require_once DOL_DOCUMENT_ROOT.'/sincronizar/actualizar_pedidos_detalle.php';


class Actualizar_menu extends CommonObject
{
	// Opciones de menú	
	var $productos		= 'products';
	var $terceros		= 'companies';
	var $comercial		= 'commercial';	
	
	// Tablas de menú	
	var $productos_mod	= 'tms_mod_productos';
	var $terceros_mod	= 'tms_mod_clientes';
	var $comercial_mod	= 'tms_mod_pedidos';
	
	function __construct($db)
	{
		$this->db = $db;
	}
	
	function buscar_actualizacion($mainmenu)
	{
		/*----------------------------------------------------------------
				Sincronización de PRODUCTOS
		----------------------------------------------------------------*/
		
		if($mainmenu == $this->productos)
		{
			$sql	= "SELECT * FROM `$this->productos_mod` WHERE id_row = 1";

			$resql	= $this->db->query($sql);
			$numr	= $this->db->num_rows($resql);
			$i		= 0;
					
			$productos = new Actualizar_productos($this->db);
					
			while ($i < $numr)
			{
				$objp = $this->db->fetch_object($resql);
					 
				if($objp->productos_dolibar > 0 || $objp->productos_prestashop > 0)
				{
					$productos->actualizar();
				} 
												
				$i++;
			}
		}
		
		/*----------------------------------------------------------------
				Sincronización de CLIENTES
		----------------------------------------------------------------*/
		
		else
		if($mainmenu == $this->terceros)	
		{
			$sql	= "SELECT * FROM `$this->terceros_mod` WHERE id_row = 1";
			
			$resql	= $this->db->query($sql);
			$numr	= $this->db->num_rows($resql);
			$i		= 0;
					
			$clientes = new Actualizar_clientes($this->db);
			$direcciones = new Actualizar_direcciones($this->db);
					
			while ($i < $numr)
			{
				$objp = $this->db->fetch_object($resql);
					 
				if($objp->clientes_dolibar > 0 || $objp->clientes_prestashop > 0)
				{
					$clientes->actualizar();
				} 
						
				if($objp->direcciones_dolibar > 0 || $objp->direcciones_prestashop > 0)
				{
					$direcciones->actualizar();
				}
						
				$i++;
			}				
		}
		
		/*----------------------------------------------------------------
				Sincronización de PEDIDOS
		----------------------------------------------------------------*/
				
		else
		if($mainmenu == $this->comercial)
		{
			$sql	= "SELECT * FROM `$this->comercial_mod` WHERE id_row = 1";

			$resql	= $this->db->query($sql);
			$numr	= $this->db->num_rows($resql);
			$i		= 0;
					
			$pedidos = new Actualizar_pedidos($this->db);
			$pedidos_detalle = new Actualizar_pedidos_detalle($this->db);
					
			while ($i < $numr)
			{
				$objp = $this->db->fetch_object($resql);
					 
				if($objp->pedidos_dolibar > 0 || $objp->pedidos_prestashop > 0)
				{
					$pedidos->actualizar();
				}
				
				if($objp->pedidos_detalle_dolibar > 0 || $objp->pedidos_detalle_prestashop > 0)
				{
					$pedidos_detalle->actualizar();
				}  
												
				$i++;
			}
		}	
	}
}