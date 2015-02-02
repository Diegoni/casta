<?php
/*
 * @(#) $Header: /var/cvsroot/pop3ml/Attic/test_pop3ml.php,v 1.1.2.38 2010/02/16 13:42:11 cvs Exp $
 */
/*  
    Copyright (C) 2009- Giuseppe Lucarelli <giu.lucarelli@gmail.com>

    This program is free software; you can redistribute it and/or modify
    it under the terms of version 2 of the GNU General Public License as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
    ini_set("post_max_size","128M");
    require("config.php");
    if(isset($global_options['passwdfile']) && !$_SERVER['PHP_AUTH_USER']) {
        header("WWW-Authenticate: Basic realm=\"Pop3ml\"");
        header("HTTP/1.0 401 Unauthorized");
        exit;
    }
    require(CLASSES_DIR_PATH.DS.'class.viewmsg.php');
    require(CLASSES_DIR_PATH.DS.'class.pop3ml.php');

    $lang = array(
        'en-gb' => array('listname'=>'listname','send mail'=>'send mail','smtp mail from'=>'smtp "MAIL FROM"','subject'=>'subject','header'=>'header','body'=>'body','message output'=>'message output','test result'=>'test result','send'=>'send','reset'=>'reset'),
        'it-it' => array('listname'=>'nome lista','send mail'=>'invia mail','smtp mail from'=>'smtp "MAIL FROM"','subject'=>'oggetto','header'=>'header','body'=>'corpo','message output'=>'messaggio','test result'=>'risultato invio','send'=>'invia','reset'=>'azzera'),
    );

    $MyPop3ml = new pop3ml;
    $MyPop3ml->now = strtotime('now');
    $MyPop3ml->dbconn = &$global_options['dbconn'];
    $MyPop3ml->mltable = $global_options['mltable'];
    $MyPop3ml->messages = $global_options['messages'];
    $MyPop3ml->subqueue = $global_options['subqueue'];
    $MyPop3ml->queue = $global_options['queue'];
    $MyPop3ml->subscribers = $global_options['subscribers'];
    $MyPop3ml->logheader = $global_options['logheader'];
    $MyPop3ml->logfooter = $global_options['logfooter'];
    $MyPop3ml->logcr = $global_options['logcr'];
    $MyPop3ml->language = false;

    if(empty($_POST['mlname'])) $_POST['mlname'] = '';
    if(empty($_POST['mailfrom'])) $_POST['mailfrom'] = '';
    if(empty($_POST['subject'])) $_POST['subject'] = '';
    if(empty($_POST['smtp'])) $_POST['smtp'] = '';
    if(empty($_POST['header'])) $_POST['header'] = '';
    if(empty($_POST['body'])) $_POST['body'] = '';
    if(empty($_POST['smtpdebug'])) $_POST['smtpdebug'] = '';

    if(!$MyPop3ml->dbconn=@mysql_connect($global_options['databaseHost'], $global_options['databaseUsername'], $global_options['databasePassword'])) {
        die("\ndocument.getElementById('result').value+=unescape('".
            rawurlencode('database connection failed for ['.mysql_error()."]\n").
            "');\n</script>\n");
    }
    if(!@mysql_select_db($global_options['databaseName'],$MyPop3ml->dbconn)) {
        die("\ndocument.getElementById('result').value+=unescape('".
            rawurlencode('select database function failed for ['.mysql_error()."]\n").
            "');\n</script>\n");
    }
    // now test if it's a registered user to grant access
    if(isset($global_options['passwdfile'])) {
        if (!isset($global_options['passwdfile'][$_SERVER['PHP_AUTH_USER']])
        || $_SERVER['PHP_AUTH_PW'] != $global_options['passwdfile'][$_SERVER['PHP_AUTH_USER']]) {
            $global_options['username'] = '';
        } else {
            $global_options['username'] = $_SERVER['PHP_AUTH_USER'];
            $global_options['password'] = $global_options['passwdfile'][$global_options['username']];
        }
        if(!strcmp($global_options['username'],'')) {
            $query = "select *,password('".$_SERVER['PHP_AUTH_PW'].
                "') as oldpw from ".$MyPop3ml->subscribers." where emailaddress = '".$_SERVER['PHP_AUTH_USER']."'";
            $result = mysql_query($query,$MyPop3ml->dbconn);
            if(!$row = mysql_fetch_object($result)) {
                die('Sorry, you are not authorized to access this page');
            }
            if(strcmp($row->webpass,$row->oldpw)) {
                die('Sorry; you are not authorized to access this page');
            }
            $MyPop3ml->userRequest = true;
            $global_options['username'] = $_SERVER['PHP_AUTH_USER'];
        }
    }

    function BuildMlList() {
        global $MyPop3ml, $global_options, $lang;
        $retval = '';
        $user = '';

        if(!empty($MyPop3ml->userRequest) && $MyPop3ml->userRequest === true) {
            $user = $global_options['username'];
            $query = "select listname,sublist,language from ".$MyPop3ml->mltable.
               " where (sublist regexp '(^|\\n)$user(\\n|$)' or digestsublist regexp '(^|\\n)$user(\\n|$)' or allowsublist regexp '(^|\\n)$user(\\n|$)' or modsublist regexp '(^|\\n)$user(\\n|$)') and (denysublist not regexp '(^|\\n)$user(\\n|$)')";
        } else {
            $query = "select listname,language from ".$MyPop3ml->mltable;
        }
        $result = @mysql_query($query,$MyPop3ml->dbconn);
        while($row = mysql_fetch_object($result)) {
            if((strlen($_POST['mlname']) <= 0 && $MyPop3ml->language === false)
            || !strcmp($_POST['mlname'],$row->listname)) {
                $MyPop3ml->dbrow->language = &$row->language;
                $MyPop3ml->language = $lang[strtolower($MyPop3ml->GetText('LANG',false))];
            }
            $retval .= $row->listname.',';
        }
        @mysql_free_result($result);
        return rtrim($retval,' ,');
    }

?>
<html>
<head>
<title>TEST_POP3ML</title>
<script language='javascript'>
    var mllist='<?php echo BuildMlList()?>';
    var sellist='<?php echo $_POST['mlname']; ?>';

    function changeSmtpStatus(checkitem) {
        document.test.smtpdebug.value=(checkitem.checked?'true':'false');
/*
 *      if(checkitem.checked) {
 *          document.getElementById('divmailfrom').style.visibility = 'visible';
 *          document.test.mailfrom.value='<?php echo $_POST['mailfrom'];?>';
 *      } else {
 *          document.getElementById('divmailfrom').style.visibility = 'hidden';
 *          document.test.mailfrom.value='';
 *      }
 */
    }

    function TrimHeader() {
        dest = document.getElementById('header');
        dest.value = dest.value.replace(eval("/\\r/ig"),'');
        dest.value = dest.value.replace(eval("/\\n\\n/ig"),"\n");
        dest.value = dest.value.replace(eval("/^\\n/"),'');
    }

    function SetHeader(key,widget,replace) {
        //alert(key+": "+widget.value+": "+replace);
        dest = document.getElementById('header');
        if(dest.value.match(eval("/(^|\\n)"+key+': /i')) == null) {
            dest.value = key+': '+widget.value+"\n"+dest.value;
        } else if(replace == true) {
            if(widget.value.length > 0) {
                dest.value = dest.value.replace(eval("/(^|\\n)"+key+":(.*)/ig"),'$1'+key+': '+widget.value);
            } else {
                dest.value = dest.value.replace(eval("/(^|\\n)("+key+":)(.*)/ig"),'');
            }
        }
    }

    function SubmitForm() {
        TrimHeader();
        document.test.target='_self';
        document.test.submit();
    }

</script>
</head>
<body>
<div style='width:100%; height: 100%; font-size: 12px'>
 <form name='test' id='test' method=POST action='<?php echo $_SERVER['PHP_SELF']; ?>'>
  <input type=hidden name='id'>
  <input type=hidden name='attachment'>
  <input type=hidden name='smtpdebug'>
  <div style="float: left; width: 38%; height: 100%;">
   <div style="float: left; width: 22%; white-space: nowrap">
   <span style='font-weight: bold; white-space: nowrap'><?php echo $MyPop3ml->language['listname']; ?></span>&nbsp;
   </div>
   <div style="float: left; width: 20%; white-space: nowrap">
     <select name='mlname' id='mlname' align=left onChange='javascript:SubmitForm()'>&nbsp;
     <script>
         var token=mllist.split(',');
         for(var i=0; i < token.length; i++) {
             document.writeln("<option value='"+token[i]+"' id='"+token[i]+"'>"+token[i]);
         }
     </script>
     </select>
   </div>
   <div style="float: right">
   <span style='font-weight: bold;'><?php echo $MyPop3ml->language['send mail']; ?></span>&nbsp;
   <input type=checkbox name='smtp' id='smtp'" onClick="javascript:changeSmtpStatus(this)">
   </div>
   <br clear = 'all'>
   <div id='divmailfrom' style='visibility: visible'>
    <span style='font-weight: bold;'><?php echo $MyPop3ml->language['smtp mail from']; ?></span>&nbsp;
    <input style='width: 240px;' type=text name='mailfrom' id='mailfrom' onChange="SetHeader('from',this,false)">
    <br>
    <span style='font-weight: bold;'><?php echo $MyPop3ml->language['subject']; ?></span>&nbsp;
    <input style='width: 310px;' type=text name='subject' id='subject' onChange="SetHeader('subject',this,true)">
   </div>
   <script>
       document.getElementById('mlname').value = '<?php echo $_POST['mlname']; ?>';
       document.getElementById('smtp').checked = '<?php echo $_POST['smtp']; ?>';
       document.getElementById('mailfrom').value = '<?php echo $_POST['mailfrom']; ?>';
       document.getElementById('subject').value = '<?php echo $_POST['subject']; ?>';
       changeSmtpStatus(document.getElementById('smtp'));
   </script>
   <div style='float: left; width: 100%; height: 180px'>
    <div style='float: left; width: 100%'>
     <span style='font-weight: bold;'><?php echo $MyPop3ml->language['header']; ?></span><br>
     <textarea style='width: 100%; height: 50px' name='header' id='header'>
<?php if($_POST['header']) { $_POST['header'] = stripslashes($_POST['header']); echo $_POST['header']; } ?></textarea><br>
    </div>
    <br clear='all'>
    <div style='float: left; width: 100%'>
     <span style='font-weight: bold;'><?php echo $MyPop3ml->language['body']; ?></span><br>
     <textarea style='width: 100%; height: 80px' name='body' id='body'>
<?php if($_POST['body']) { $_POST['body'] = stripslashes($_POST['body']); echo $_POST['body']; } ?></textarea><br>
    </div>
    <br clear='all'>
    <div>
    <input onClick='TrimHeader()' type=submit value='<?php echo $MyPop3ml->language['send']; ?>'><input type=reset value='<?php echo $MyPop3ml->language['reset']; ?>'>
    </div>
   </div>
   <br clear="all">
   <br clear="all">
   <span style='font-weight: bold'><?php echo $MyPop3ml->language['message output']; ?></span>
   <div style='float: left; width: 100%; height: 270px; border: 1px solid black; padding: 2px' id='body'>
    <div style='float: left; width: 100%; height: 100%; border: 0px; padding: 0px; overflow: scroll' id='popBody'>
    </div>
   </div>
  </div>
  <div style='float: right; width: 60%'>
   <span style='font-weight: bold'><?php echo $MyPop3ml->language['test result']; ?></span>
   <textarea style='float: right; width: 100%; height: 515px; border: 1px solid black; padding: 2px' name='result' id='result'>
   </textarea><br>
  </div>
 </form>
</div>
<script>
//var pop=window.open('','popBody','toolbar=no,menubar=no,resizable=yes,top=0,left=0,width=100%,height=100%');
<?php
    if(is_null($_POST['header']) && is_null($_POST['mlname']) <= 0)
        die;
    $_POST['header'] = trim(str_replace("\n\n","\n",str_replace("\r",'',$_POST['header'])));

    $message=trim(rawurldecode($_POST['header'])."\r\n\r\n".$_POST['body']);
    $vm = new ViewMsg;
    $vm->message=&$message;
    $vm->run();
    if(strlen($_POST['mailfrom']) > 0) {
        $vm->from = $_POST['mailfrom'];
    }
    echo "document.getElementById('result').value='';\n";
    echo "document.getElementById('result').value+=unescape('".rawurlencode("\nEMAIL DATA:\nfrom: ".$vm->from.
        "\nto: ".$vm->to."\nsubject: ".$vm->subject.(!empty($attach_list) ? "\nattachment:\n$attach_list\n" : "\n"))."');\n";
    if(!empty($vm->attachment)) {
        for($i=0; $i < sizeof($vm->attachment); $i++) {
            $filename=$vm->isoDecode($vm->attachment[$i]['filename']);
            echo 'document.getElementById(\'result\').value+='.
                "unescape('".rawurlencode("\nATTACHMENT FILE NAME: $filename\n")."');\n";
            //$attach_list.='&nbsp;<a href="javascript:getAttach('.
            //$i.",'$filename')\">$filename</a><br>";
            //echo "attachList[$i] = '".rawurlencode(
                //(eregi("base64",$vm->attachment[$i]['Content-Transfer-Encoding']) ?
                    //base64_decode($vm->attachment[$i]['buffer']) :
                    //$vm->attachment[$i]['buffer'])).
                //"';\n";
        }
    }
    //echo "pop.document.writeln(unescape('".rawurlencode($vm->body_result)."'));\n";
    echo "document.getElementById('popBody').innerHTML=unescape('".rawurlencode($vm->body_result)."');\n";
    echo "document.getElementById('result').value+=unescape('".rawurlencode("\n")."');\n";

    // now test ML

    if(!strcasecmp($_POST['smtpdebug'],'true')) {
        $MyPop3ml->debug = false;
    } else {
        $MyPop3ml->debug = true;
    }

    $MyPop3ml->listName = $_POST['mlname'];
    // change all key to lower to avoid malformed options
    if($MyPop3ml->listName && is_array($mlinfo[$MyPop3ml->listName])) {
        $mlinfo[$MyPop3ml->listName] = array_change_key_case($mlinfo[$MyPop3ml->listName], CASE_LOWER);
    }
    if(!empty($mlinfo[$MyPop3ml->listName]['forwardmailerto'])) $MyPop3ml->forwardMailerTo = $mlinfo[$MyPop3ml->listName]['forwardmailerto'];
    if(!empty($mlinfo[$MyPop3ml->listName]['expirelock'])) $MyPop3ml->expireLock = $mlinfo[$MyPop3ml->listName]['expirelock'];
    if(!empty($mlinfo[$MyPop3ml->listName]['cachemessages'])) $MyPop3ml->cacheMessages = $mlinfo[$MyPop3ml->listName]['cachemessages'];
    if(!empty($mlinfo[$MyPop3ml->listName]['cachepath'])) $MyPop3ml->cachePath = $mlinfo[$MyPop3ml->listName]['cachepath'];
    if(!empty($mlinfo[$MyPop3ml->listName]['mintimeresendmsg'])) $MyPop3ml->minTimeResendMsg = $mlinfo[$MyPop3ml->listName]['mintimeresendmsg'];
    if(!empty($mlinfo[$MyPop3ml->listName]['scheduledtime'])) $MyPop3ml->scheduledTime = $mlinfo[$MyPop3ml->listName]['scheduledtime'];
    if($MyPop3ml->Init() != true) {
        die("\ndocument.getElementById('result').value+=unescape('".rawurlencode($MyPop3ml->debugOutput."\n").
            "');\n</scr"."ipt>\n");
    }
    $MyPop3ml->InitSublistFields();
    $parameters=array(
        'Data'=>trim($_POST['header'])."\r\n\r\n".$_POST['body'] //, 'SaveBody'=>1,
        );
    $mime=new mime_parser_class;
    $mime->decode_bodies = 0;
    $success=$mime->Decode($parameters, $MyPop3ml->decoded);
    if(strpos($vm->from,'<'))
        $vm->from = preg_replace('/^.*\<(.*)\>/','\1',$vm->from);

    echo "\n/*\n";
    // do some ml tests
    $query = "select count(*) from ".addslashes($MyPop3ml->mltable)." where listname = '".$MyPop3ml->listName."'";
    $command = mysql_query($query,$MyPop3ml->dbconn);
    if(!$result = mysql_fetch_row($command)) {
        $MyPop3ml->debugOutput .= "no ML found: quit; ";
        die("\ndocument.getElementById('result').value+=\"No ML found: quit;\n</scr"."ipt>\";\n");
    }
    if($result[0] > 1) {
        $MyPop3ml->debugOutput .= "\nOops, there is more then one ML with this name (".$result[0]."), fix it as soon as possible!\n";
    }
    // now run class script functions for messages management
    $result = $MyPop3ml->CheckSender($vm->from);
    $MyPop3ml->debugOutput .= "\nCHECK SENDER RESULT: $result\n";
    if(!@$MyPop3ml->decoded[0]['ExtractedAddresses']['from:']) {
        $MyPop3ml->debugOutput .= "\nBAD ADDRESS FOR [".
            $MyPop3ml->GetReturnPath('')."]\n";
    } else {
        if($MyPop3ml->CheckMlCommand($vm->from, $result) == true) {
            // continue;
        } else {
            if(strstr($result,'mailer')) {
                if($MyPop3ml->forwardMailerTo && strcmp($MyPop3ml->forwardMailerTo, $MyPop3ml->listAddress)) {
                    $mailsubject = "dropped message from Mailer [$vm->from]";
                    $recipient = str_replace(',',"\n",str_replace(' ','',$MyPop3ml->forwardMailerTo));
                    $MyPop3ml->NotifyUser($recipient,false,$mailsubject,$MyPop3ml->decoded[0]['Body']);
                }
            } else if (strstr($result,'deny')) {
                $MyPop3ml->SendSubscriptionError(
                    $MyPop3ml->GetReturnPath($vm->from),false,'UNSUBSCRIPTION ERROR STATE');
            } else {
                if($MyPop3ml->MailFilter($vm->from) == true) {
                    $MyPop3ml->SendMessage($vm->from,strlen(@$MyPop3ml->decoded[0]['Body']));
                }
            }
        }
    }
    echo "\n*/\n";
    echo "document.getElementById('result').value+=unescape('".rawurlencode($MyPop3ml->debugOutput."\n").'\');'."\n";
?>
</script>
</body>
</html>
