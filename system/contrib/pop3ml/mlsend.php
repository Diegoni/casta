<?php
/*
 * @(#) $Header: /var/cvsroot/pop3ml/mlsend.php,v 1.4.2.20 2010/02/05 08:00:52 cvs Exp $
 */

    require('config.php');
    if(isset($global_options['passwdfile'])) {
        require(INCLUDE_DIR_PATH.DS."functions.php");
    }
    require_once(CLASSES_DIR_PATH.DS.'class.pop3ml.php');

    $MyPop3ml = new pop3ml;
    $MyPop3ml->dbconn = &$global_options['dbconn'];
    $MyPop3ml->mltable = $global_options['mltable'];
    $MyPop3ml->messages = $global_options['messages'];
    $MyPop3ml->subqueue = $global_options['subqueue'];
    $MyPop3ml->subscribers = $global_options['subscribers'];
    $MyPop3ml->queue = $global_options['queue'];
    $MyPop3ml->logheader = $global_options['logheader'];
    $MyPop3ml->logfooter = $global_options['logfooter'];
    $MyPop3ml->logcr = $global_options['logcr'];
    if(!$MyPop3ml->dbconn=@mysql_connect($global_options['databaseHost'], $global_options['databaseUsername'], $global_options['databasePassword'])) {
        die('database connection failed for ['.mysql_error()."]\n");
    }
    if(!@mysql_select_db($global_options['databaseName'],$MyPop3ml->dbconn)) {
        die('select database function failed for ['.mysql_error()."]\n");
    }

    foreach($mlinfo as $listname=>$opt) {
        $MyPop3ml->listName = $listname;
        if(is_array($opt)) {
            // change all key to lower to avoid malformed options
            $opt = array_change_key_case($opt, CASE_LOWER);
            $MyPop3ml->logSubject = false;
            if(@$opt['logsubject']) $MyPop3ml->logSubject = $opt['logsubject'];
            $MyPop3ml->forwardMailerTo = false;
            if(@$opt['forwardmailerto']) $MyPop3ml->forwardMailerTo = $opt['forwardmailerto'];
            $MyPop3ml->maxPop3MsgLimit = false;
            if(@$opt['maxpop3msglimit']) $MyPop3ml->maxPop3MsgLimit = $opt['maxpop3msglimit'];
            $MyPop3ml->expireLock = false;
            if(@$opt['expirelock']) $MyPop3ml->expireLock = $opt['expirelock'];
            $MyPop3ml->cacheMessages = false;
            if(@$opt['cachemessages']) $MyPop3ml->cacheMessages = $opt['cachemessages'];
            $MyPop3ml->cachePath = false;
            if(@$opt['cachepath']) $MyPop3ml->cachePath = $opt['cachepath'];
            $MyPop3ml->minTimeResendMsg = false;
            if(@$opt['mintimeresendmsg']) $MyPop3ml->minTimeResendMsg = $opt['mintimeresendmsg'];
            $MyPop3ml->scheduledTime = false;
            if(@$opt['scheduledtime']) $MyPop3ml->scheduledTime = $opt['scheduledtime'];
        }
        $MyPop3ml->run();
    }
