<?php
require_once DOL_DOCUMENT_ROOT.'/sincronizar/class_actualizar.php';

class Actualizar_pedidos extends Actualizar
{
	var $subject		= 'pedidos'; 
	
	// tablas en base de datos para PEDIDOS
	var $table_log		= 'tms_log_pedidos'; //Guarda los cambios
	var $table_sin		= 'tms_pedidos_sin'; //Tabla de cruces 
	var $table_dol		= 'llx_commande';
	var $table_pre		= 'ps_orders';
	var $table_mod		= 'tms_mod_pedidos';
	
	var $table_sin_cli	= 'tms_clientes_sin';
	var $table_payment	= 'tms_payment';
	var $table_carrier	= 'ps_order_carrier';
	var $table_dol_det	= 'llx_commandedet';
	
	// campos en tablas
	var $id_sin_dol		= 'id_llx_commande';
	var $id_sin_pre		= 'id_ps_orders';
	var $id_table_dol	= 'rowid';
	var $id_table_pre	= 'id_order';
	
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
						// 1 - Buscamos el id correspondiente del cliente
						
						$where = "`id_ps_customer`	= $objp->id_cliente";
							
						$array_sin_cliente = $this->get_registros($this->table_sin_cli, $where);
						
						
						// 2 - Buscamos los valores de configuración para Condición de Pago y Origen del pedido
							
						$array_config_s = $this->get_registros($this->table_config_s);
						
						
						// 3 - Buscamos Modo de pago
						
						$where = "`ps_order_payment` = '$objp->payment'";
							
						$array_forma = $this->get_registros($this->table_payment, $where);
						
						// 4 - Calculo impuesto
						
						
						$impuesto = $objp->total_ttc - $objp->total_ht;
						
						
						// 5 - Ingreso del pedido											
												
						$registro = array(
							'id_sin'				=> $objp->id_row,
							'fk_soc'				=> $array_sin_cliente['id_llx_societe'],
							'ref'					=> "'".$objp->reference."'",
							'tva'					=> "'".$impuesto."'",
							'total_ht'				=> "'".$objp->total_ht."'",
							'total_ttc'				=> "'".$objp->total_ttc."'",
							'fk_mode_reglement'		=> $array_forma['llx_c_paiement_id'],
							'fk_input_reason'		=> $array_config_s['id_llx_c_input_reason'],
							'fk_cond_reglement'		=> $array_config_s['id_llx_c_payment_term'],
							'date_creation'			=> "'".$objp->date_upd."'",
							'date_commande'			=> "'".date('Y-m-d', strtotime($objp->date_upd))."'"
						);
	
						$id_registro = $this->insert_registro($this->table_dol, $registro);
						
						$this->insert_sin($objp->id_row, $id_registro);
						
						
						// 6 - Ingreso del cargo de transporte
						
						$where = "`id_order` = '$objp->id_row'";
							
						$array_carrier = $this->get_registros($this->table_carrier, $where);
						
						if(is_array($array_carrier))
						{
							$iva = $array_carrier['shipping_cost_tax_incl'] - $array_carrier['shipping_cost_tax_excl'];
						
							$iva = $iva * 100 / $array_carrier['shipping_cost_tax_excl'];
							
							$registro = array(
								//'id_sin'		=> $objp->id_row,
								'fk_commande'	=> $id_registro,
								'fk_product'	=> $array_config_s['id_servicio_envio'],
								//'description'	=> "'".$objp->product_name."'",
								'qty'			=> 1,
								//'buy_price_ht'	=> $objp->purchase_supplier_price,
								'tva_tx'		=> $iva,
								'subprice'		=> $array_carrier['shipping_cost_tax_excl'],
								'price'			=> $array_carrier['shipping_cost_tax_incl'],
								/*'total_tva'		=> $total_tva,*/
								'total_ht'		=> $array_carrier['shipping_cost_tax_incl'], // Ver 
								'total_ttc'		=> $array_carrier['shipping_cost_tax_incl'],
								/*'remise_percent'=> $remise_percent,
								'remise'		=> $objp->reduction_amount,*/
							);
							
							$this->insert_registro($this->table_dol_det, $registro);						
						}
							
						$this->update_log($objp->id_log);
					}
					
		/*----------------------------------------------------------------
				INSERT desde DOLIBAR actualizo PRESTASHOP
		----------------------------------------------------------------*/
	 				
					else	
					if($objp->system == $this->system_dolibar)					
					{
						$where = "`id_llx_societe`	= $objp->id_cliente";
							
						$array_sin_cliente = $this->get_registros($this->table_sin_cli, $where);
						
						$registro = array(
							'id_sin'				=> $objp->id_row,
							'id_customer'			=> $array_sin_cliente['id_ps_customer'],
							'reference'				=> "'".$objp->reference."'",
							'total_paid'			=> "'".$objp->total_paid."'",
							'date_add'				=> "'".$objp->date_upd."'"
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
							// 1 - Buscamos el id correspondiente del cliente
						
							$where = "`id_ps_customer`	= $objp->id_cliente";
								
							$array_sin_cliente = $this->get_registros($this->table_sin_cli, $where);
							
							// 2 - Buscamos los valores de configuración para Condición de Pago y Origen del pedido
								
							$array_config_s = $this->get_registros($this->table_config_s);
							
							// 3 - Buscamos Modo de pago
							
							$where = "`ps_order_payment` = '$objp->payment'";
								
							$array_forma = $this->get_registros($this->table_payment, $where);
							
							// 4 - Calculo impuesto
							
							$impuesto = $objp->total_ttc - $objp->total_ht;
												
													
							$registro = array(
								'id_sin'				=> $objp->id_row,
								'fk_soc'				=> $array_sin_cliente['id_llx_societe'],
								'ref'					=> "'".$objp->reference."'",
								'tva'					=> "'".$impuesto."'",
								'total_ht'				=> "'".$objp->total_ht."'",
								'total_ttc'				=> "'".$objp->total_ttc."'",
								'fk_mode_reglement'		=> $array_forma['llx_c_paiement_id'],
								'fk_input_reason'		=> $array_config_s['id_llx_c_input_reason'],
								'fk_cond_reglement'		=> $array_config_s['id_llx_c_payment_term'],
								'date_creation'			=> "'".$objp->date_upd."'",
								'date_commande'			=> "'".date('Y-m-d', strtotime($objp->date_upd))."'"
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
							$registro =  array(
								'id_sin'				=> $objp->id_row,
								'reference'				=> "'".$objp->reference."'",
								'total_paid'			=> "'".$objp->total_paid."'",
								'date_add'				=> "'".$objp->date_upd."'"
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
