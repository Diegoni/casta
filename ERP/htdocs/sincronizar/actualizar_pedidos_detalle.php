<?php
require_once DOL_DOCUMENT_ROOT.'/sincronizar/class_actualizar.php';

class Actualizar_pedidos_detalle extends Actualizar
{
	var $subject		= 'pedidos_detalle'; 
	
	// tablas en base de datos para PEDIDOS DETALLE
	var $table_log		= 'tms_log_pedidos_detalle'; //Guarda los cambios
	var $table_sin		= 'tms_pedidos_detalle_sin'; //Tabla de cruces 
	var $table_dol		= 'llx_commandedet';
	var $table_pre		= 'ps_order_detail';
	var $table_mod		= 'tms_mod_pedidos';
	
	var $table_sin_ped	= 'tms_pedidos_sin';
	var $table_sin_pro	= 'tms_productos_sin';
	var $table_tax		= 'ps_tax';
	
	// campos en tablas
	var $id_sin_dol		= 'id_llx_commandedet';
	var $id_sin_pre		= 'id_ps_order_detail';
	var $id_table_dol	= 'rowid';
	var $id_table_pre	= 'id_order_detail';
	
	function __construct($db)
	{
		$this->db = $db;
		
		parent::__construct(
			$db				= $this->db, 
			$table_log		= $this->table_log,
			$table_sin		= $this->table_sin,
			$id_sin_pre		= $this->id_sin_pre,
			$id_sin_dol		= $this->id_sin_dol,
			$table_mod		= $this->table_mod, 
			$subject		= $this->subject
		);
	}
		
	/*----------------------------------------------------------------
	------------------------------------------------------------------
	
	 		Funcion que permite actualizar pedidos
	 
	------------------------------------------------------------------
	----------------------------------------------------------------*/
	
	function actualizar()
	{
		$sql	= "SELECT * FROM `$this->table_log` WHERE id_estado = 0";
	
		$resql	= $this->db->query($sql);
		$numr	= $this->db->num_rows($resql);
		$i		= 0;
		
		if($numr > 0)
		{				
			while ($i < $numr)
			{
				$objp = $this->db->fetch_object($resql);
				
		/*----------------------------------------------------------------
				INSERT desde PRESTASHOP actualizo DOLIBAR
		----------------------------------------------------------------*/
	 			
				if($objp->action == $this->action_insert)
				{
					if($objp->system == $this->system_prestashop)
					{
						// 1 - Buscar en la tabla tms_pedidos_sin para ver que id corresponde
						
						$where = "`id_ps_orders` = $objp->id_order";
						
						$array_sin_pedido = $this->get_registros($this->table_sin_ped, $where);
						
						
						// 2 - Buscar en la tabla tms_productos_sin para ver que id corresponde
						
						$where = "`id_ps_product` = $objp->product_id";
						
						$array_sin_producto = $this->get_registros($this->table_sin_pro, $where);
						
						
						// 3 - Buscar en ps_tax el porcentaje que corresponde 
						
						$where = "`id_tax` = $objp->id_tax_rules_group";
						
						$array_tax = $this->get_registros($this->table_tax, $where);
						
						
						// 4 - Calculamos descuento
						
						if($objp->reduction_amount == 0)
						{
							$remise_percent = $objp->reduction_percent;				
						}
						else
						{
							$remise_percent = $objp->reduction_amount_tax_excl * 100 / $objp->product_price; //Controlar este calculo
						}
						
						
						// 5 - Calculo total del IVA
						
						$total_tva =  $objp->total_price_tax_incl - $objp->total_price_tax_excl;
												
						
						// Ingreso del registro
						
						$registro = array(
							'id_sin'		=> $objp->id_row,
							'fk_commande'	=> $array_sin_pedido['id_llx_commande'],
							'fk_product'	=> $array_sin_producto['id_llx_product'],
							'description'	=> "'".$objp->product_name."'",
							'qty'			=> $objp->product_quantity,
							'buy_price_ht'	=> $objp->purchase_supplier_price,
							'tva_tx'		=> $array_tax['rate'],
							'subprice'		=> $objp->product_price,
							'price'			=> $objp->unit_price_tax_excl,
							'total_tva'		=> $total_tva,
							'total_ht'		=> $objp->total_price_tax_incl,
							'total_ttc'		=> $objp->total_price_tax_excl,
							'remise_percent'=> $remise_percent,
							'remise'		=> $objp->reduction_amount,
						);
	
						$id_registro = $this->insert_registro($this->table_dol, $registro);
						
						$this->insert_sin($objp->id_row, $id_registro);
						
						$this->update_log($objp->id_log);
					}
					
		/*----------------------------------------------------------------
				INSERT desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
	 				
					else	
					if($objp->system == $this->system_dolibar)					
					{
						$where = "`id_llx_commande`	= $objp->id_order";
							
						$array_sin_pedido = $this->get_registros($this->table_sin_ped, $where);
						
						$registro = array(
							'id_sin'			=> $objp->id_row,
							'id_order'			=> $array_sin_pedido['id_ps_order'],
							'product_id'		=> "'".$objp->product_id."'",
							'product_quantity'	=> "'".$objp->product_quantity."'",
							'product_name'		=> "'".$objp->product_name."'",
							'product_price'		=> "'".$objp->product_price."'"
						);
							
						$id_registro = $this->insert_registro($this->table_pre, $registro);
						
						$this->insert_sin($id_registro, $objp->id_row);
						
						$this->update_log($objp->id_log);						
					}						
				}
				
		/*----------------------------------------------------------------
				UPDATE desde PRESTASHOP actualizo DOLIBAR
		----------------------------------------------------------------*/
	 
				else 
				if($objp->action == $this->action_update)	
				{
					if($objp->system == $this->system_prestashop)
					{
						$id_registro = $this->get_id_sin($objp->id_row, $this->system_dolibar);
						
						if($id_registro != 0)
						{
							$registro = array(
								'id_sin'		=> $objp->id_row,
								'fk_product'	=> "'".$objp->product_id."'",
								'qty'			=> "'".$objp->product_quantity."'",
								'description'	=> "'".$objp->product_name."'",
								'subprice'		=> "'".$objp->product_price."'",
								'total_ht'		=> "'".$objp->product_price."'"
							);
							
							$where = $this->id_table_dol." = ".$id_registro;
							
							$this->update_registro($this->table_dol, $registro, $where);
							
							$this->update_log($objp->id_log);
						}
						else
						{
							$this->log_error('no_sin' , $objp);
						} 	
					}
								
		/*----------------------------------------------------------------
				UPDATE desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
		
	 				else	
					if($objp->system == $this->system_dolibar)					
					{
						$id_registro = $this->get_id_sin($objp->id_row, $this->system_prestashop);
						
						if($id_registro > 0)
						{
							$registro = array(
								'id_sin'			=> $objp->id_row,
								'product_id'		=> "'".$objp->product_id."'",
								'product_quantity'	=> "'".$objp->product_quantity."'",
								'product_name'		=> "'".$objp->product_name."'",
								'product_price'		=> "'".$objp->product_price."'"
							);
							
							$where = $this->id_table_pre." = ".$id_registro; 
							
							$this->update_registro($this->table_pre, $registro, $where);
							
							$this->update_log($objp->id_log);
						}
						else
						{
							$this->log_error('no_sin' , $objp);
						} 	
					}
				}							
				 
				$i++;
			}
		}
	
		$this->delete_log();
			
		$this->reset_mod();
	}
		
}
