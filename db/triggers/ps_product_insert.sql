USE `prestashop`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` TRIGGER `ps_product_AINS` AFTER INSERT ON `ps_product` FOR 
EACH ROW
BEGIN
  INSERT INTO dolibarr.llx_product
	(
  `ref`,
  `label`,
  `price_ttc`,
  `accountancy_code_sell`,
  `accountancy_code_buy`,
  `barcode`,
  `weight`,
  `length`,
  `datec`
	)
	VALUES	(
	NEW.reference,
	NEW.descripcion,
	NEW.price,
	NEW.upc,
	NEW.upc,
	NEW.upc,
	NEW.weight,
	NEW.width,
	NEW.date_add
);
END