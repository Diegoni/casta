USE `prestashop`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` TRIGGER `ps_address_AINS` AFTER INSERT ON `ps_address` FOR 
EACH ROW
BEGIN 
	DECLARE id_address_test INT;

	IF(NEW.id_sin = 0) THEN
		SET id_address_test = (SELECT `id_ps_address` FROM `tms_clientes_sin` WHERE `id_ps_customer` = NEW.id_customer);

		IF (id_address_test = 0) THEN 
			UPDATE `tms_clientes_sin` 
				SET `id_ps_address` = NEW.id_address 
			WHERE `id_ps_customer` = NEW.id_customer ;/*Falta el where*/

		/*
			UPDATE `llx_societe`
			SET 
				`address`	= NEW.address1,
				`zip`		= NEW.postcode,
				`town`		= NEW.city,
				`phone`		= NEW.phone
			WHERE 
				`rowid` 		= id_societe_test;
		*/
		ELSE 
			INSERT INTO `tms_log_direccion`(
				`id_row`,
				`id_sin`,
				`id_cliente`,
				`firstname`,
				`lastname`,
				`address`,
				`postcode`,
				`city`,
				`phone`,
				`phone_mobile`,
				`date_add`,
				`alias`,
				`active`,
				`system`,
				`action`,
				`id_estado`
			)VALUES(
				NEW.id_address,
				NEW.id_sin,
				NEW.id_customer,
				NEW.firstname, 
				NEW.lastname, 
				NEW.address1, 
				NEW.postcode, 
				NEW.city, 
				NEW.phone, 
				NEW.phone_mobile, 
				NEW.date_add, 
				NEW.alias, 
				NEW.active, 
				'prestashop',
				'insert',
				0
			);
		END IF;
	END IF;
END
