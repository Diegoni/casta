USE `prestashop`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` TRIGGER `ps_customer_AINS` AFTER INSERT ON `ps_customer` FOR
EACH ROW
BEGIN
	IF(NEW.id_sin = 0) THEN
		INSERT INTO `tms_log_clientes`(
			`id_row`,
			`id_sin`,
			`email`,
			`website`,
			`note`,
			`cuil`,
			`nombre`,
			`secure_key`,
			`active`,
			`date_upd`,
			`system`,
			`action`,
			`id_estado`
		)VALUES(
			NEW.id_customer,
			NEW.id_sin,
			NEW.email,
			NEW.website,
			NEW.note,
			NEW.cuil,
			NEW.firstname,
			NEW.secure_key,
			NEW.active,
			NEW.date_upd,
			'prestashop',
			'insert',
			0
		);
	END IF;
END