USE `prestashop`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` TRIGGER `llx_societe_AINS` AFTER INSERT ON `llx_societe` FOR 
EACH ROW
BEGIN
	IF(NEW.id_sin = -1) THEN
		INSERT INTO `tms_log_clientes`(
			`id_row`,
			`id_sin`,
			`nombre`,
			`email`,
			`website`,
			`note`,
			`cuil`,
			`address`,
			`postcode`,
			`city`,
			`phone`,
			`secure_key`,
			`active`,
			`date_upd`,
			`system`,
			`action`,
			`id_estado`
		)VALUES(
			NEW.rowid,
			NEW.id_sin,
			NEW.nom,
			NEW.email,
			NEW.url,
			NEW.note_private,
			NEW.siren,
			NEW.address,
			NEW.zip,
			NEW.town,
			NEW.phone,
			NEW.secure_key,
			NEW.status,
			NEW.datec,
			'dolibar',
			'insert',
			0
		);
	END IF;
END