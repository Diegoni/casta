USE `prestashop`;
DELIMITER $$
CREATE TRIGGER `tms_log_direccion_AINS` AFTER INSERT ON `tms_log_direccion` FOR 
EACH ROW
BEGIN 
	DECLARE id_ps_address_test INT;
	DECLARE address_test VARCHAR(255);
	DECLARE alias_test VARCHAR(255);
	DECLARE id_row_test INT;

	IF (NEW.action = 'insert') THEN 
		IF(NEW.system = 'dolibar') THEN
			IF (NEW.id_sin = 0) THEN
				
				IF (NEW.address IS NULL) THEN
					SET address_test = '-';
				ELSE 
					SET address_test = NEW.address;
				END IF;

				IF (NEW.alias IS NULL) THEN
					SET alias_test = '-';
				ELSE 
					SET alias_test = NEW.alias;
				END IF;

				SET id_row_test = (SELECT `id_ps_customer` FROM `tms_clientes_sin` WHERE `id_llx_societe` = NEW.id_cliente);
				
				INSERT INTO `ps_address`(
					`id_customer`,
					`id_sin`, 
					`firstname`, 
					`lastname`, 
					`address1`, 
					`postcode`, 
					`city`, 
					`phone`, 
					`phone_mobile`, 
					`date_add`, 
					`alias`, 
					`active` 
				)VALUES(
					id_row_test,
					NEW.id_row,
					NEW.firstname,
					NEW.lastname,
					address_test,
					NEW.postcode,
					NEW.city,
					NEW.phone,
					NEW.phone_mobile,
					NEW.date_add,
					alias_test,
					NEW.active
				);
				
				SET id_ps_address_test = last_insert_id();

				INSERT INTO `tms_direccion_sin` (
					`id_ps_address`,
					`id_llx_socpeople`
				)VALUES(
					id_ps_address_test,
					NEW.id_row
				);
			END IF;
		ELSE 
			IF (NEW.id_sin = 0) THEN

				SET id_row_test = (SELECT `id_llx_societe` FROM `tms_clientes_sin` WHERE `id_ps_customer` = NEW.id_cliente);
											
				INSERT INTO `llx_socpeople` (
					`fk_soc`,
					`id_sin`, 
					`firstname`, 
					`lastname`, 
					`address`, 
					`zip`, 
					`town`, 
					`phone`, 
					`phone_mobile`, 
					`datec`, 
					`poste`, 
					`statut`,
					`fk_user_creat`/* Mejorar esto*/
				) VALUES (
					id_row_test,
					NEW.id_row,
					NEW.firstname,
					NEW.lastname,
					NEW.address,
					NEW.postcode,
					NEW.city,
					NEW.phone,
					NEW.phone_mobile,
					NEW.date_add,
					NEW.alias,
					NEW.active,
					1
				);

				SET id_ps_address_test = last_insert_id();

				INSERT INTO `tms_direccion_sin` (
					`id_ps_address`,
					`id_llx_socpeople`
				)VALUES(
					NEW.id_row,
					id_ps_address_test
				);
			END IF;
		END IF;
	END IF;
END;