<?php
/*
 * @(#) $Header: /var/cvsroot/pop3ml/Attic/clean_pop3ml.php,v 1.1.2.13 2010/02/05 08:00:52 cvs Exp $
 */
/*  pop3ml - php Mailing list/Newsletter manager
    Copyright (C) 2009- Giuseppe Lucarelli <giu.lucarelli@gmail.it>

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

    require("config.php");
    if(isset($global_options['passwdfile'])) {
        require(INCLUDE_DIR_PATH.DS."functions.php");
    }
    require_once(CLASSES_DIR_PATH.DS.'class.pop3ml.php');

    function Connect(& $cls) {
        global $global_options;

        if(!$cls->dbconn=@mysql_connect($global_options['databaseHost'], $global_options['databaseUsername'], $global_options['databasePassword'])) {
            return false;
        }
        if(!@mysql_select_db($global_options['databaseName'],$cls->dbconn)) {
            return false;
        }
        return true;
    }

    function Init(& $cls, & $opt) {
        // change all key to lower to avoid malformed options
        $opt = array_change_key_case($opt, CASE_LOWER);
        $cls->cacheMessages = false;
        if(@$opt['cachemessages']) $cls->cacheMessages = $opt['cachemessages'];
        $cls->cachePath = false;
        if(@$opt['cachepath']) $cls->cachePath = $opt['cachepath'];
        $cls->expireCache = false;
        if(@$opt['expirecache']) $cls->expireCache = $opt['expirecache'];
        $cls->expireMsgMod = false;
        if(@$opt['expiremsgmod']) $cls->expireMsgMod = $opt['expiremsgmod'];
        $cls->expireMsgQueue = false;
        if(@$opt['expiremsgqueue']) $cls->expireMsgQueue = $opt['expiremsgqueue'];
        $cls->expireSubQueue = false;
        if(@$opt['expiresubqueue']) $cls->expireSubQueue = $opt['expiresubqueue'];
        if(CACHE_MESSAGES == false)
            $cls->cacheMessages = false;
        if(CACHE_PATH != false) {
            $cls->cachePath = CACHE_PATH;
        }
        if($cls->cachePath && strlen($cls->cachePath) > 1
        && $cls->cachePath[strlen($cls->cachePath)-1] != '/') {
            $cls->cachePath .= '/';
        }
        if(EXPIRE_CACHE != false) {
            $cls->expireCache = EXPIRE_CACHE;
        }
    }

    function CheckExpiredSubRequest(& $cls) {
        $query = 'delete from '.$cls->subqueue.' where time_to_sec(timediff(now(),date)) > '.($cls->expireSubQueue ? $cls->expireSubQueue : EXPIRE_SUB_QUEUE);
        @mysql_query($query,$cls->dbconn);
    }

    /**
     * this function remove all expired messages tagged as queued or waiting for moderator response
     */
    function RemoveExpiredMessages(& $cls) {

        $query = "select * from $cls->messages where listname = '".$cls->listName.
            "' and (state = 'queued' or state = 'pending') order by 1";
        if(!$result = @mysql_query($query,$cls->dbconn)) {
            return;
        }
        while($row = @mysql_fetch_object($result)) {
            if(!strcmp($row->state,'pending') && (strtotime('now') - strtotime($row->date)) > ($cls->expireMsgMod ? $cls->expireMsgMod : EXPIRE_MSG_MOD)
            || !strcmp($row->state,'queued') && (strtotime('now') - strtotime($row->date)) > ($cls->expireMsgQueue ? $cls->expireMsgQueue : EXPIRE_MSG_QUEUE)) {
                @mysql_query('delete from '.$cls->messages.' where id = '.$row->id,$cls->dbconn);
                echo 'removed msg# '.$row->id."; ";
            }
        }
        @mysql_free_result($result);
    }

    function RemoveCachedMessages(& $cls) {
        if(!$cls->cacheMessages || !file_exists($cls->cachePath)) {
            return;
        }
        $d = dir($cls->cachePath);
        while($msg=$d->read()) {
            if($msg[0] != '.' && !is_dir($cls->cachePath.$msg)) {
                if(!$stat = stat($cls->cachePath.$msg)) {
                    continue;
                }
                if((strtotime('now') - $stat['atime']) > $cls->expireCache) {
                    if(unlink($cls->cachePath.$msg)) {
                        echo "removed from cache msg[$msg];\n";
                    }
                }
            }
        }
    }



    $MyPop3ml = new pop3ml;
    $MyPop3ml->dbconn = &$global_options['dbconn'];
    $MyPop3ml->mltable = $global_options['mltable'];
    $MyPop3ml->messages = $global_options['messages'];
    $MyPop3ml->subqueue = $global_options['subqueue'];
    $MyPop3ml->subscribers = $global_options['subscribers'];
    $MyPop3ml->queue = $global_options['queue'];
    if(Connect($MyPop3ml) == false) {
        die('Connection to DB failed, quit; ');
    }

    foreach($mlinfo as $listname=>$opt) {
        $MyPop3ml->listName = $listname;
        if(!is_array($opt))
            $opt=array();
        Init($MyPop3ml,$opt);
        RemoveExpiredMessages($MyPop3ml);
        CheckExpiredSubRequest($MyPop3ml);
        RemoveCachedMessages($MyPop3ml);
    }
