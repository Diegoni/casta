<?php

define('SMSTREND_SEND_SMS_REQUEST','/Trend/SENDSMS');
define('SMSTREND_MSG_STATUS_REQUEST','/Trend/SMSSTATUS');
define('SMSTREND_HISTORY_REQUEST','/Trend/SMSHISTORY');
define('SMSTREND_CREDITS_REQUEST','/Trend/CREDITS');

define('SMSTREND_DATE_TIME_FORMAT','%Y%m%d%H%M%S');

class SMStrend_POST {
	var $params;
	var $result;

	function SMStrend_POST($username, $password) 
	{
		$this->params['login'] = $username;
		$this->params['password'] = $password;
	}

	function add_param($name, $value) 
	{
		$this->params[$name] = $value;
	}

	function do_post($request) 
	{
		$request_url = 'http://'.SMSTREND_HOSTNAME;
		if (SMSTREND_DEFAULT_PORT != 80) {
			$request_url = $request_url.':'.SMSTREND_DEFAULT_PORT;
		}
		$request_url = $request_url.$request;
		$postdata = http_build_query($this->params);
		$request_url .= '?' . str_replace('&amp;', '&', $postdata);
		$opts = array('http' =>
    		array(
        		'method'  => 'POST',
		        'header'  => 'Content-type: application/x-www-form-urlencoded',
		        'content' => $postdata
		    )
		);
		$context  = stream_context_create($opts);
		$this->result = file_get_contents($request_url, false, $context);
		list($version,$status_code,$msg) = explode(' ',$http_response_header[0], 3);
		switch($status_code) {
			case 200: return new SMStrend_response_parser($this->result);
			// maybe we could implement better error handling?
			default: return null;
		}
	}

}

define('SMSTREND_SEPARATOR','|');
define('SMSTREND_NEWLINE',';');
class SMStrend_response_parser {
	var $cursor;
	var $response;
	var $isok;
	var $errcode;
	var $errmsg;
	
	function SMStrend_response_parser($response) {
		$this->response = $response;
		$this->cursor = 0;
		if (strlen($response) >= 2) {
			$code = $this->next_string();
			if ('OK' == $code) {
				$this->isok = true;
			}
			if ('KO' == $code) {
				$this->isok = false;
				$this->errcode = $this->next_int();
				$this->errmsg = $this->next_string();
			}
		}
	}

	function next_string() {
		$nstr = '';
//		echo 'cursor:|'.$this->cursor.'|';
//		echo 'nstr:|'.$nstr.'|';
		while (($this->response[$this->cursor] != SMSTREND_SEPARATOR) &&
			($this->response[$this->cursor] != SMSTREND_NEWLINE)) {
//		echo 'Cnstr:|'.$nstr.'|';
			$nstr = $nstr.$this->response[$this->cursor++];
			if ($this->cursor >= strlen($this->response))
				break;
		}
//		echo 'Enstr:|'.$nstr.'|';
		if ($this->cursor < strlen($this->response) && $this->response[$this->cursor] != SMSTREND_NEWLINE) {
			$this->cursor++;
		}
		return urldecode($nstr);
	}
	function next_int() {
		return (int)$this->next_string();
	}

	function go_next_line() {
		while ($this->response[$this->cursor++] != SMSTREND_NEWLINE) {
			if ($this->cursor > strlen($this->response)) {
				return false;
			}
		}
		return strlen($this->response) != $this->cursor;
	}

	function get_result_array() {
		return array('ok' => $this->isok, 'errcode' => $this->errcode, 'errmsg' => $this->errmsg);
	}
}
