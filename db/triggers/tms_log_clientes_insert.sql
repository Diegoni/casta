USE `prestashop`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` TRIGGER `tms_log_clientes_AINS` AFTER INSERT ON `tms_log_clientes` FOR 
EACH ROW
BEGIN 
	DECLARE email_test VARCHAR (128);
	DECLARE cuil_test VARCHAR (128);
	DECLARE ciudad VARCHAR (128);
	DECLARE id_test INT;
	DECLARE id_customer_test INT;
	DECLARE id_address_test INT;

	IF (NEW.action = 'insert') THEN 
		IF(NEW.system = 'dolibar') THEN
			IF (NEW.id_sin = -1) THEN
				IF(NEW.email IS NULL) THEN 
					SET email_test = 'diego.nieto@tmsgroup.com.ar';
				ELSE 
					SET email_test = NEW.email;
				END IF; 

				IF(NEW.cuil IS NULL) THEN 
					SET cuil_test = '23-31246501-9';
				ELSE 
					SET cuil_test = NEW.cuil;
				END IF; 

				INSERT INTO `ps_customer`(
					`email`,
					`website`,
					`note`,
					`cuil`,
					`firstname`,
					`secure_key`,
					`date_add`,
					`date_upd`,
					`active`,
					`id_sin`
				)VALUES(
					email_test,
					NEW.website,
					NEW.note,
					cuil_test,
					NEW.nombre,
					NEW.secure_key,
					NEW.date_upd,
					NEW.date_upd,
					NEW.active,
					NEW.id_row
				);
				
				SET id_customer_test = last_insert_id();
				
				IF(NEW.address IS NULL) THEN 
					SET id_address_test = 0;
				ELSE
					IF (NEW.city IS NULL) THEN
						SET ciudad = 'Mendoza';
					ELSE
						SET ciudad = NEW.city;
					END IF;

					INSERT INTO `ps_address` (
						`id_country`, 
						`id_state`, 
						`id_customer`, 
						`address1`, 
						`postcode`, 
						`city`, 
						`phone`, 
						`date_add`, 
						`date_upd`, 
						`active`, 
						`deleted`
					) VALUES (
						44, 
						111, 
						id_customer_test, 
						NEW.address,
						NEW.postcode,
						ciudad,
						NEW.phone,
						NEW.date_upd,
						NEW.date_upd,
						1,
						0
					);
					
					SET id_address_test = last_insert_id();
			
				END IF;			
			
				INSERT INTO `tms_clientes_sin` (
					`id_ps_customer`,
					`id_ps_address`,
					`id_llx_societe`
				)VALUES(
					id_customer_test,
					id_address_test,
					NEW.id_row
				);
			END IF;
		ELSE 
			IF (NEW.id_sin = 0) THEN
				INSERT INTO `llx_societe`(  
					`email`,
					`url`,
					`note_private`,
					`siren`,
					`nom`,
					`datec`,
					`status`,
					`client`,
					`id_sin`
				)VALUES	(
					NEW.email,
					NEW.website,
					NEW.note,
					NEW.cuil,
					NEW.nombre,
					NEW.date_upd,
					NEW.active,
					1,
					NEW.id_row
				);

				INSERT INTO `tms_clientes_sin` (
					`id_ps_customer`,
					`id_llx_societe`
				)VALUES(
					NEW.id_row,
					last_insert_id()
				);
				
			END IF;
		END IF;
	ELSE 
		IF(NEW.system = 'dolibar') THEN
			SET id_test = (SELECT `id_ps_customer` FROM `tms_clientes_sin` WHERE `id_llx_societe` = NEW.id_row);
			
			IF(NEW.address IS NULL) THEN 
				SET id_address_test = 0;
			ELSE
				SET id_address_test = (SELECT `id_ps_address` FROM `tms_clientes_sin` WHERE `id_llx_societe` = NEW.id_row);
				
				IF (NEW.city IS NULL) THEN
					SET ciudad = 'Mendoza';
				ELSE
					SET ciudad = NEW.city;
				END IF;

				IF(id_address_test = 0) THEN 
					INSERT INTO `ps_address` (
						`id_country`, 
						`id_state`, 
						`id_customer`, 
						`alias`, 
						`company`,
						`firstname`,
						`lastname`,
						`address1`, 
						`postcode`, 
						`city`, 
						`phone`, 
						`phone_mobile`, 
						`date_add`, 
						`date_upd`, 
						`active`, 
						`deleted`,
						`id_sin`
					) VALUES (
						44, 
						111, 
						id_test,
						'Dirección Dolibar',
						'-',
						'-',
						'-',
						NEW.address,
						NEW.postcode,
						ciudad,
						NEW.phone,
						'-',
						NEW.date_upd,
						NEW.date_upd,
						1,
						0,
						NEW.id_row
					);
					SET id_address_test = last_insert_id();

					UPDATE `tms_clientes_sin` SET 
						`id_ps_address` = id_address_test
					WHERE 
						`id_ps_customer` = id_test;
				ELSE 
					UPDATE `ps_address` SET
						`address1`	= NEW.address,
						`postcode`	= NEW.postcode,
						`city`		= NEW.city,
						`phone` 	= NEW.phone,
						`id_sin`	= -1,
						`date_upd`	= NEW.date_upd
					WHERE 
						`id_address` = id_address_test;
				END IF;	
			END IF;	

			UPDATE `ps_customer` SET 
				`email` 	= NEW.email ,
				`website` 	= NEW.website,
				`note` 		= NEW.note,
				`cuil` 		= NEW.cuil,
				`firstname` = NEW.nombre,
				`date_upd` 	= NEW.date_upd,
				`active` 	= NEW.active,
				`id_sin` 	= NEW.id_row
			WHERE `id_customer` = id_test;
		ELSE
			SET id_test = (SELECT `id_llx_societe` FROM `tms_clientes_sin` WHERE `id_ps_customer` = NEW.id_row);

			UPDATE `llx_societe` SET 
				`email` 	= NEW.email ,
				`url` 		= NEW.website,
				`note_private` 	= NEW.note,
				`siren` 	= NEW.cuil,
				`nom`		= NEW.nombre,
				`datec` 	= NEW.date_upd,
				`status` 	= NEW.active,
				`id_sin` 	= NEW.id_row
			WHERE `rowid` = id_test;
		END IF;
	END IF;
 
END