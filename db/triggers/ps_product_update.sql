USE `prestashop`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` TRIGGER `ps_product_AUPD` AFTER UPDATE ON `ps_product` FOR 
EACH ROW
BEGIN
	DECLARE nombre varchar(128);
	DECLARE description_short text;
	DECLARE impuesto float;
	DECLARE superficie float;
	DECLARE volumen float;
	DECLARE iva float;
		
	SET nombre = (SELECT `name` FROM `ps_product_lang` WHERE id_product = NEW.id_product AND id_lang = 1);
	SET description_short = (SELECT `description_short` FROM `ps_product_lang` WHERE id_product = NEW.id_product AND id_lang = 1);
	SET iva = (SELECT `rate` FROM `ps_tax` WHERE id_tax = NEW.id_tax_rules_group);
	SET impuesto = 1 + iva / 100;
	SET superficie = NEW.weight * NEW.height;
	SET volumen = NEW.weight * NEW.height * NEW.depth;
	
	UPDATE dolibarr.llx_product
	SET
	`label` = nombre,
	`description` = description_short,
	`price` = NEW.price * impuesto,
	`accountancy_code_sell` = NEW.upc,
	`accountancy_code_buy` = NEW.upc,
	`barcode` = NEW.upc,
	`weight` = NEW.weight,
	`weight_units` = 0, /*kg*/
	`length` = NEW.width,
	`length_units` = -2, /*cm*/
	`surface` = superficie,
	`surface_units` = -4, /*cm2*/
	`volume` = volumen,
	`volume_units` = -6, /*cm3*/
	`tosell` = NEW.active,
	`tva_tx` = iva,
	`price_min` = NEW.wholesale_price
	WHERE `ref` = NEW.reference;
END