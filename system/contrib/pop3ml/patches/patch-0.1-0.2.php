<?php
/*
 * @(#) $Header: /var/cvsroot/pop3ml/patches/Attic/patch-0.1-0.2.php,v 1.1.2.12 2010/01/29 10:46:29 cvs Exp $
 */
	require("../config.php");
	if(isset($global_options['passwdfile'])) {
		require(INCLUDE_DIR_PATH.DS."functions.php");
	}
	require("sqlchanges.php");	// check it out before run this script
	if(!$global_options['subscribers'])
		$global_options['subscribers'] = 'subscribers';

	echo "updating ".$global_options['databaseHost']."<br>\n";
	if(!$dbconn=@mysql_connect($global_options['databaseHost'], $global_options['databaseUsername'], $global_options['databasePassword'])) {
		die("no database");
	}
	@mysql_select_db($global_options['databaseName'],$dbconn);

	// MLTABLE
	$query = "alter table ".addslashes($global_options['mltable'])." change listname listname varchar(128) unique";
	echo "<br>\n$query<br>\n";
	$command = mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." change msgsize msgsize varchar(32)";
	echo "<br>\n$query<br>\n";
	$command = mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." change hostname hostname tinytext";
	echo "<br>\n$query<br>\n";
	$command = mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." change listaddr listaddr tinytext";
	echo "<br>\n$query<br>\n";
	$command = mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." change listuser listuser tinytext";
	echo "<br>\n$query<br>\n";
	$command = mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." change listpoppass listpoppass tinytext";
	echo "<br>\n$query<br>\n";
	$command = mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." change listowneremail listowneremail tinytext";
	echo "<br>\n$query<br>\n";
	$command = mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." add recipientlimit int(11) after shutdown";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." add senddigest varchar(32) after shutdown";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." add digestmaxsize varchar(32) after senddigest";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." add digestmaxmsg varchar(32) after digestmaxsize";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." change mimeremove mailfilter longtext";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." drop column submsg";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." drop column unsubmsg";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." drop column welcome";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." drop column goodbye";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['mltable'])." add language longtext after trailerfile";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "update ".addslashes($global_options['mltable']).
		" set language = '".str_replace("'","\\'",(LANG == 'it-IT' ? LANGUAGE_VALUE_IT : LANGUAGE_VALUE_EN))."', ".
		"headerchange = '".str_replace("'","\\'",HEADERCHANGE)."', ".
		"mailfilter = '".str_replace("'","\\'",MAILFILTER)."', ".
		"recipientlimit = '".RECIPIENTLIMIT."', ".
		"senddigest = '".SENDDIGEST."', ".
		"digestmaxsize = '".DIGESTMAXSIZE."', ".
		"digestmaxmsg = '".DIGESTMAXMSG."'";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	// QUEUE
	$query = "alter table ".addslashes($global_options['queue'])." change mldate date datetime";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['queue'])." change mltable listname varchar(128)";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	// SUBQUEUE
	$query = "alter table ".addslashes($global_options['subqueue'])." add date datetime after keyvalue";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	// MESSAGES
	$query = "alter table ".addslashes($global_options['messages'])." change state state set('sent','sentdigest','pending','queued')";
	echo "<br>\n$query<br>\n";
	$command = mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['messages'])." add keyvalue varchar(128) after header";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['queue'])." change addresses addresses longtext";
	echo "<br>\n$query<br>\n";
	$command = mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['messages'])." change mltable listname varchar(128)";
	echo "<br>\n$query<br>\n";
	$command = mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['messages'])." change mldate date datetime";
	echo "<br>\n$query<br>\n";
	$command = mysql_query($query,$dbconn);
	echo mysql_error();

	// SUBSCRIBERS
	$query = "alter table ".addslashes($global_options['subscribers'])." change emailaddress emailaddress varchar(128) unique";
	echo "<br>\n$query<br>\n";
	$command = mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['subscribers'])." add state set('enabled','disabled','suspended') default 'enabled' after emailaddress";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "update ".addslashes($global_options['subscribers'])." set state = 'enabled'";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

	$query = "alter table ".addslashes($global_options['subscribers'])." add webpass tinytext after state";
	echo "<br>\n$query<br>\n";
	$command = @mysql_query($query,$dbconn);
	echo mysql_error();

?>
