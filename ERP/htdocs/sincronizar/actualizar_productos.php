<?php
require_once DOL_DOCUMENT_ROOT.'/sincronizar/class_actualizar.php';

class Actualizar_productos extends Actualizar
{
	var $subject		= 'productos'; 
	
	// tablas en base de datos para PRODUCTOS
	var $table_log		= 'tms_log_productos'; //Guarda los cambios
	var $table_sin		= 'tms_productos_sin'; //Tabla de cruces 
	var $table_dol		= 'llx_product';
	var $table_pre		= 'ps_product';
	var $table_mod		= 'tms_mod_productos';
	
	var $table_lag		= 'ps_product_lang';
	var $table_shop		= 'ps_product_shop';
	
	// campos en tablas
	var $id_sin_dol		= 'id_llx_product';
	var $id_sin_pre		= 'id_ps_product';
	var $id_table_dol	= 'rowid';
	var $id_table_pre	= 'id_product';
	
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
	
	 		Funcion que permite actualizar productos
	 
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
						$length		= $objp->width;
						$surface	= $objp->height * $length;
						$volume		= $objp->depth * $surface;
						
						$registro = array(
							'id_sin'				=> $objp->id_row,
							'ref'					=> "'".$objp->ref."'",
							'label'					=> "'".$objp->name."'",
							'description'			=> "'".$objp->description_short."'",
							'price'					=> "'".$objp->price."'",
							'price_min'				=> "'".$objp->price_min."'",
							'accountancy_code_sell'	=> "'".$objp->code_sell."'",
							'accountancy_code_buy'	=> "'".$objp->code_buy."'",
							'barcode'				=> "'".$objp->barcode."'",
							'weight'				=> "'".$objp->weight."'",
							'length'				=> "'".$length."'",
							'surface'				=> "'".$surface."'",
							'volume'				=> "'".$volume."'",
							'tosell'				=> "'".$objp->active."'",
							'tva_tx'				=> "'".$objp->tva."'",
							'datec'					=> "'".$objp->date_upd."'",
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
						// 1 - Hacemos insert del producto en la tabla ps_product
						
						$length		= $objp->width;
						$height		= $objp->height / $length ;
						$depth		= $objp->depth / $height;
						
						$registro = array(
							'id_sin'				=> $objp->id_row,
							'id_supplier'			=> 1,
							'id_manufacturer'		=> 1,
							'id_category_default'	=> 1,
							'reference'				=> "'".$objp->ref."'",
							'price'					=> "'".$objp->price."'",
							'wholesale_price'		=> "'".$objp->price_min."'",
							'ean13'					=> "'".$objp->code_sell."'",
							'upc'					=> "'".$objp->barcode."'",
							'weight'				=> "'".$objp->weight."'",
							'width'					=> "'".$length."'",
							'height'				=> "'".$width."'",
							'depth'					=> "'".$depth."'",
							'active'				=> "'".$objp->active."'",
							'id_tax_rules_group'	=> "'".$objp->tva."'",
							'date_add'				=> "'".$objp->date_upd."'",
							'date_upd'				=> "'".$objp->date_upd."'",
						);
							
						$id_registro = $this->insert_registro($this->table_pre, $registro);
						
						$this->insert_sin($id_registro, $objp->id_row);
						
						// 2 - Hacemos insert de las cadenas del producto en la tabla ps_product_lang
												
						for ($i = 1; $i < 3; $i++)
						{ 
							$registro = array(
								'id_product'		=> $id_registro,
								'id_shop'			=> 1,
								'id_lang'			=> $i,
								'name'				=> "'".$objp->name."'",
								'description_short'	=> "'".$objp->description_short."'"
							);
							
							$this->insert_registro($this->table_lag, $registro);
						}
						
						// 3 - Hacemos insert de los precios del producto en la tabla ps_product_shop
						
						$registro = array(
							'id_product' 			=> $id_registro,
							'id_shop' 				=> 1,
							'id_category_default'	=> 1,
							'id_tax_rules_group'	=> 1,
							'active'				=> 0,
							'redirect_type'			=> "'404'"
						);
						
						$this->insert_registro($this->table_shop, $registro);
						
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
							// 1 - Obtenemos cadenas, tabla ps_product_lag
							
							$where = 
							"`id_product`	= $id_registro AND
							 `id_shop`		= 1 AND
							 `id_lang`		= 1";
							
							$array_lag = $this->get_registros($this->table_lag, $where);
							
							// 2 - Obtenemos precios, tabla ps_product_shop
							
							$where = 
							"`id_product`	= $id_registro AND
							 `id_shop`		= 1";
							
							$array_shop = $this->get_registros($this->table_shop, $where);
							
							// 3 - Hacemos el update de la tabla llx_product
							
							$length		= $objp->width;
							$surface	= $objp->height * $length;
							$volume		= $objp->depth * $surface;
							
							$registro =  array(
								'id_sin'		=> $objp->id_row,
								'ref'			=> "'".$objp->ref."'",
								'label'			=> "'".$array_lag['name']."'",
								'description'	=> "'".$array_lag['description_short']."'",
								'price'			=> "'".$array_shop['price']."'",
								'price_min'		=> "'".$array_shop['price_min']."'",
								'accountancy_code_sell' => "'".$objp->code_sell."'",
								'accountancy_code_buy'	=> "'".$objp->code_buy."'",
								'barcode'		=> "'".$objp->barcode."'",
								'weight'		=> "'".$objp->weight."'",
								'length'		=> "'".$length."'",
								'surface'		=> "'".$surface."'",
								'volume'		=> "'".$volume."'",
								//'tosell'		=> "'".$objp->active."'",
								'tva_tx'		=> "'".$objp->tva."'",
								'datec'			=> "'".$objp->date_upd."'"
							);
							
							$where = $this->id_table_dol." = ".$id_registro;
							
							$this->update_registro($this->table_dol, $registro, $where);
							
							$this->update_log($objp->id_log);
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
							// 1 - Update productos, tabla ps_product
							
							$length		= $objp->width;
							$height		= $objp->height / $length ;
							$depth		= $objp->depth / $height;
							
							$registro =  array(
								'id_sin'			=> $objp->id_row,
								'reference'			=> "'".$objp->ref."'",
								'price'				=> "'".$objp->price."'",
								'wholesale_price'	=> "'".$objp->price_min."'",
								'ean13'				=> "'".$objp->code_sell."'",
								'upc'				=> "'".$objp->barcode."'",
								'weight'			=> "'".$objp->weight."'",
								'width'				=> "'".$length."'",
								'height'			=> "'".$height."'",
								'depth'				=> "'".$depth."'",
								'active'			=> "'".$objp->active."'",
								'id_tax_rules_group'=> "'".$objp->tva."'",
								'date_upd'			=> "'".$objp->date_upd."'" 
							);	
							
							$where = $this->id_table_pre." = ".$id_registro; 
							
							$this->update_registro($this->table_pre, $registro, $where);
							
							// 2 - Update cadenas, tabla ps_product_lang
														
							$registro =  array(
								'name'				=> "'".$objp->name."'",
								'description_short'	=> "'".$objp->description_short."'"
							);
							
							$where = "id_product = ".$id_registro;
							$where .= " AND id_shop = 1";
							$where .= " AND id_lang = 1";
							
							$this->update_registro($this->table_lag, $registro, $where);
							
							// 3 - Update precios, tabla ps_product_shop
							
							$registro = array(
								'id_product' 			=> $id_registro,
								'id_shop' 				=> 1,
								'id_category_default'	=> 2,
								'id_tax_rules_group'	=> 1,
								'active'				=> 0,
								'redirect_type'			=> "'404'",
								'price'					=> "'".$objp->price."'",
								'wholesale_price'		=> "'".$objp->price_min."'"
							);
							
							$where = "id_product = ".$id_registro;
							$where .= " AND id_shop = 1";
							
							$this->update_registro($this->table_shop, $registro, $where);
							
							$this->update_log($objp->id_log);
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
