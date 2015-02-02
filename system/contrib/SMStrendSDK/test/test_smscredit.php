<?php

require('../credits.php');

$credits = smstrend_get_credits();
if ($credits['ok']) {
	for ($i=0;$i<$credits['count'];$i++) {
		if (!$credits[$i]->is_international()) {
			echo 'You can send '.$credits[$i]->availability.' smss';
			echo ' of type '.$credits[$i]->credit_type;
			echo ' in '.$credits[$i]->nation.' </br>';
		} else {
			if ($credits[$i]->credit_type == 'EE') {
				echo 'You can send '.$credits[$i]->availability;
				echo ' smss in foreign countries </br>';
			}
			if ($credits[$i]->credit_type == 'NL') {
				echo 'You can perform '.$credits[$i]->availability;
				echo ' lookup requests </br>';
			}
		}
	}
} else {
	echo 'Request failed: '.$status['errcode'].' - '.$status['errmsg'];
}


?>
