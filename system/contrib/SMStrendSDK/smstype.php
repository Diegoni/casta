<?php

define('SMSTYPE_GOLD','GS');
define('SMSTYPE_GOLD_PLUS','GP');
define('SMSTYPE_SILVER','SI');

function smstrend_sms_type_valid($smstype) {
	return 	$smstype === SMSTYPE_GOLD ||
			$smstype === SMSTYPE_GOLD_PLUS ||
			$smstype === SMSTYPE_SILVER;
}

function smstrend_sms_type_has_custom_tpoa($smstype) {
	return $smstype === SMSTYPE_GOLD_PLUS;
}
