<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'util.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'smstype.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'http_post.php');

function smstrend_get_message_status($order_id, $username, $password)
{
	$post = new SMStrend_POST($username, $password);
	$post->add_param('order_id',$order_id);
	$rp = $post->do_post(SMSTREND_MSG_STATUS_REQUEST);
	$res = $rp->get_result_array();
	$count = 0;
	if ($rp->isok) 
	{
		while ($rp->go_next_line()) 
		{
			$res[] = new SMStrend_SMS_STATUS($rp->next_string(), $rp->next_string(), $rp->next_string());
			$count++;
		}
	}
	$res['count'] = $count;
	return $res;
}

class SMStrend_SMS_STATUS {
	var $recipient;
	var $status;
	var $dt_received;

	function SMStrend_SMS_STATUS($recipient, $status, $dt_received) {
		$this->recipient = $recipient;
		$this->status = $status;
		$this->dt_received = $dt_received;
	}
	
	function str_status() {
		switch ($this->status) {
			case 'SCHEDULED': return 'postponed, not jet arrived';
			case 'SENT': return 'sent, wait for delivery notification (depending on message type)';
			case 'DLVRD': return 'the sms has been correctly delivered to the mobile phone';
			case 'ERROR': return 'error sending sms';
			case 'TIMEOUT': return 'cannot deliver sms to the mobile in 48 hours';
			case 'TOOM4NUM': return 'too many messages sent to this number (spam warning)';
			case 'TOOM4USER': return 'too many messages sent by this user';
			case 'UNKNPFX': return 'unknown/unparsable mobile phone prefix';
			case 'UNKNRCPT': return 'unknown recipient';
			case 'WAIT4DLVR': return 'message sent, waiting for delivery notification';
			case 'WAITING': return 'not yet sent (still active)';
			default: return 'received an unknown status code from server (this should never happen!)';
		}
	}

	function is_error() {
		switch ($this->status) {
			case 'ERROR':
			case 'TIMEOUT':
			case 'TOOM4NUM':
			case 'TOOM4USER':
			case 'UNKNPFX':
			case 'UNKNRCPT': return true;
		}
		return false;
	}

	function get_sms_received_timestamp() {
		if ($this->status == 'DLVRD') {
			return smstrend_date_to_unix_timestamp($this->dt_received);
		} else {
			return null;
		}
	}
}

?>
