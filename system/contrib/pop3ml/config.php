<?php
// QUICK START
// insert all running mailinglists comma separated (ie. $mlinfo=array('list1'=>'','list2'=>'') and change all fields to
// fit your needs

$mlinfo=array(
    'mylist'=>'',
    // 'gmail'=>'',
    // 'yahoo'=>''
    );
$global_options = array
                  (
//-------------------------------------------------------------------
// usercode/password for accessing pages using .htaccess properties, decomment to enable user authentication
//-------------------------------------------------------------------
//    'passwdfile' => array(
//        'mladmin' => '1234',
//        'guest'   => 'letmein'),
//--------------------------------------------------------------------
    'databaseHost' => "localhost",
    'databaseName' => "pop3ml",
    'databaseUsername' => "root",
    'databasePassword' => "",
    'mltable' => "mltable",
    'subqueue' => "subqueue",
    'messages' => "messages",
    'queue' => "queue",
    'subscribers' => "subscribers",
// html style for display/log
    'logheader' =>  '',         // "<div style='white-space: nowrap'>",
    'logfooter' =>  '',         // "</div>",
    'logcr'     =>  ''           // "<br>",
);
//----------------------------------------------------------------------
// GETTING MORE COMPLEX
//----------------------------------------------------------------------
// set these values to your include/classes files pathname
define('INCLUDE_DIR_PATH','includes');    // ie. '/srv/www/htdocs/pop3ml/include'
define('CLASSES_DIR_PATH','includes');    // ie. '/srv/www/htdocs/pop3ml/classes'
define('SCRIPTS_DIR_PATH','includes');    // ie. '/srv/www/htdocs/php/path_to/some_extra/classes_or_scripts'
// it seems to be not necessary for portability, anyway uncomment it if you want to use the '\' directory separator
// under windows
//define('DS',DIRECTORY_SEPARATOR);
if(!defined('DS')) define('DS','/');

// set to 'true', if a ML has 'cacheMessages' (see below extra configuration section) set to 'true',
// all read messages will be saved into cache folder (in this form: 'mlname-date[-messageid].eml'),
// if 'false' all ML won't cache messages
define('CACHE_MESSAGES', true);

// if it contains a folder pathname, its value will override all ML setting of 'cachePath' value (see below extra
// configuration section)
// if 'false', script will use mailinglist 'cachePath' folder pathname to store all email messages
// WARNING, the directory has to be writeble by apache user
define('CACHE_PATH', false);         // '/tmp/cache/'

// it defines when cached messages will be removed from cache.
// if 'false', script will use all ML setting of 'expireCache' value (see below extra configuration section).
// if it contains a numeric value (in seconds), it will override all ML setting of 'expireCache' value
define('EXPIRE_CACHE', false);        // 2592000);    // 30 days
//------------------------------------------------------//

// expire ML locking time (in seconds); if ml is under modification or for some reasons script exits for error and doesn't
// unlock ml automatically, after this time script won't consider the ml locked, reading and sending emails
define('EXPIRE_LOCK',    3600);    // 60 minutes

// expire time for messages waiting moderators approval (in seconds)
define('EXPIRE_MSG_MOD', 86400);    // 24 hours

// expire time for queued messages (in seconds)
define('EXPIRE_MSG_QUEUE', 604800);    // 7 days

// expire time for (un)subscription requests sent to list (in seconds)
define('EXPIRE_SUB_QUEUE', 604800);    // 7 days

// minimum time to resend queued messages (in seconds)
// used only for first day. it will be multiplicated by 2 for second day, 3 for third,....
define('MIN_TIME_RESEND_MSG', 7200);    // 120 minutes

//-----------------DIGEST CONFIGURATION---------------------------------//
// send digest time: '[day:]hour:minutes' ie. 'mon:06:10' '12:10' or one of this values: 1,2,3,4,6,8,12
// 'mon:06:10' every monday at '06:10'
// '12:10' every dat at '12:10'
// '1' send every hour
// '2' send every 2 hours, first at '02:00', '04:00','06:00' and so on
// '3' send every 3 hours, first at '03:00', '06:00','09:00' and so on
// ...
// '12' twice a day, first at '12:00', second at '24:00'
// this value will be overridden by mailing list 'senddigest' 'mltable' field
define('SEND_DIGEST', '3');

// max size for digest message: '#[KM]' ie. '65536' (byte size), '64K' (Kbyte size), '1M' (Mbyte size)
// if false, script will use 'digestmaxsize' database field, otherwise it will use its value for every ML
// if both fields (define and database field) are unset, script will set max size to '64K'
define('DIGEST_MAX_SIZE', false);    // '1M'

// max number of messages per digest
// if false, script will use 'digestmaxmsg' database field, otherwise it will use its value for every ML
// if both fields (define and database field) are unset, script will set max size to '30'
define('DIGEST_MAX_MSG', false);    // '30'

//-----------------END DIGEST CONFIGURATION---------------------------------//
//-----------------SCHEDULING CONFIGURATION---------------------------------//
// you can delay email post to subscribers adding this header with associated value in this form:
// 'X-Scheduled: year-month-day hour:minutes'   ie. 'X-Scheduled: 2009-12-15 08:00'
// the script checks header's email to find this extra value. if you don't want tu use email header to delay messages,
// use subject instead, putting at the beginning of text the 'SCHEDULED_TIME' contents with the time value
// for example setting "SCHEDULED_TIME" define to "Mail scheduled for", an email with a subject like:
// "[Mail scheduled for: 2010-01-01 08:00] happy new year" will be scheduled for this day/time
define('SCHEDULED_TIME', 'X-Scheduled');
//------------------------------------------------------------------------------
// EXTRA CONFIGURATION VALUES
// (options are case insensitive, for example you can use both 'expireLock' and 'expirelock' values)
// 
// 'logSubject' => false                insert a number to write the message's subject to log file with char 'number' length
// 'forwardMailerTo' => false           insert one or more address (comma separated) to forward emails from robot.
//                                      if set to default value 'false' all 'mailer' emails will be simply ignored.
//                                      if set to "CACHE" value (case insensitive) all ignored emails from robots will be
//                                      cached (if not already done due to 'cacheMessages' setting).
//                                      if set to "LISTNAME" (case incensitive) or listaddress email address, all 'mailer'
//                                      emails will be delivered as normal messages (BE CAREFUL WITH THIS OPTION DUE TO
//                                      EMAIL LOOP!!)
// 'maxPop3MsgLimit' => false           if your ml's pop3 account could contain a large amount of emails and you don't want
//                                      to send every stored message each time script runs, put here a number you like
// 'expireLock' => '7200'               it will override 'EXPIRE_LOCK' default value '3600' for 60 minutes to this value
// 'cacheMessages'  => true             if set, all read messages will be saved into cache folder, but only if
//                                       'CACHE_MESSAGES' field value is set to 'true'
// 'cachePath'=> '/path/to/cache/'      if set, all read messages will be saved into this folder,
//                                      but only if 'CACHE_PATH' field value is set to 'false'
// 'expireCache'=> '1296000'            if set, all read messages will be removed from cache folder after this time,
//                                      but only if 'EXPIRE_CACHE' field value is set to 'false'
// 'expireMsgMod'=> '86400'             it will override 'EXPIRE_MSG_MOD' default value '172800'
//                                      for two days to this value
// 'expireMsgQueue'=> '86400'           it will override 'EXPIRE_MSG_QUEUE' default value '604800'
//                                      for seven days to this value
// 'expireSubQueue'=> '1296000'         it will override 'EXPIRE_MSG_QUEUE' default value '604800'
// 
// 'minTimeResendMsg'=> '3600'          it will override 'MIN_TIME_RESEND_MSG' default value '7200' for 120 minutes
// 
// 'scheduledTime'=>'X-Scheduled'       it will override 'SCHEDULED_TIME' default value
//
// for example:
// $mlinfo['mylist']=array('expireLock'=>'3600', 'cacheMessages'=>true,'cachePath'=>'/var/tmp/pop3ml');
//------------------------------------------------------------------------------
