ALTER TABLE `ps_product` ADD `descripcion` VARCHAR(128) NOT NULL AFTER `id_product`;
ALTER TABLE `ps_category` ADD `descripcion` VARCHAR(128) NOT NULL AFTER `id_category`;
ALTER TABLE `ps_gender` ADD `descripcion` VARCHAR(128) NOT NULL AFTER `id_gender`;
ALTER TABLE `ps_risk` ADD `descripcion` VARCHAR(128) NOT NULL AFTER `id_risk`;
ALTER TABLE `ps_group` ADD `descripcion` VARCHAR(128) NOT NULL AFTER `id_group`;
ALTER TABLE `ps_feature` ADD `descripcion` VARCHAR(128) NOT NULL AFTER `id_feature`;
ALTER TABLE `ps_feature_value` ADD `descripcion` VARCHAR(128) NOT NULL AFTER `id_feature_value`;