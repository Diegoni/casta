<?php 
/*
 * @(#) $Header: /var/cvsroot/viewmsg/class.viewmsg.php,v 1.28 2010/05/19 03:36:21 cvs Exp $
 */
/*
    A simple class to display a mail message from message buffer

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
/* WARNING! Be careful with messages! You can easily reach max size for a
 * php script (normally 10M) or (for example) max_allowed_packet for mysql.
 * enable row below (and adjust memory value) to enlarge 'php.ini' field value
 */
// ini_set('memory_limit','32M');
// error_reporting(E_ALL & ~E_NOTICE);

class ViewMsg
{
/* public */
    var $message='';
    var $subject='';
    var $from='';
    var $to='';
    var $body_result='';
    var $attachment='';
    var $content_type='';
    var $content_transfer_encoding='';
/* private */
    var $body_mime_parts='';
    var $header='';
    var $start_body='';
    var $debug=false;

    function GetPartData($start,$end) {
        $body_part=substr($this->message,$start,$end);
        if(!$body_part || strlen($body_part) < 32)
            return;
        $body_part = ltrim($body_part,"\n");
        $pos = strpos($body_part,"\n\n");
        $sub_header = substr($body_part,0,$pos);
        $body_part=substr($body_part,$pos+2);
        if(preg_match('/Content-Transfer-Encoding:.*\n/i',$sub_header,$matches)) {
            $content_transfer_encoding=
                trim(substr($matches[0],strlen('Content-Transfer-Encoding:'))," ;.\"'\n");
        }
        if(preg_match('/boundary=.*($|\n)/i',$sub_header,$matches)) {
            $boundary=
                trim(substr($matches[0],strlen('boundary='))," ;.\"'\n");
        }
        if(preg_match('/Content-Disposition:.*($|\n)/i',$sub_header,$matches)) {
            $content_disposition=
                trim(substr($matches[0],strlen('Content-Disposition:'))," ;.\"'\n");
        }
        //if (eregi("content-disposition: attachment;",$sub_header)
        //|| eregi("(content-|^)type: (message/rfc822|application/)",$sub_header)
        //|| eregi("(content-|^)type: image/.*;",$sub_header)) {
        if (preg_match("/content-disposition: attachment;/im",$sub_header)
        || preg_match("/(content-|^)type: (message\/rfc822|application\/)/im",$sub_header)
        || preg_match("/(content-|^)type: image\/.*;/im",$sub_header)) {
            if(!@$this->body_mime_parts['attach'] || !is_array($this->body_mime_parts['attach'])) {
                $i=0;
            } else {
                $i = sizeof($this->body_mime_parts['attach']);
            }
            $this->body_mime_parts['attach'][$i] = array();
            $pos = strpos($sub_header,'name=');
            if($pos) {
                $len = strpos(substr($sub_header,$pos+5),"\n");
                if(!$len) $len = strlen($sub_header) - $pos;
                $name = trim(substr($sub_header,$pos+5,$len),"\"\n");
            } else {
                $name = sprintf("message%d.eml",$i);
            }
            $this->body_mime_parts['attach'][$i]['header'] = $sub_header;
            $this->body_mime_parts['attach'][$i]['filename'] = $name;
            $this->body_mime_parts['attach'][$i]['buffer'] = $body_part;
            if(preg_match('/(content-|^)type:.*\n/i',$sub_header,$matches)) {
                $this->body_mime_parts['attach'][$i]['Content-Type'] =
                    trim(substr($matches[0],ViewMsg::Stripos($matches[0],':')+2)," <>;.\"'\n");
            }
            if(preg_match('/\nContent-ID:.*($|\n)/i',$sub_header,$matches)) {
                $this->body_mime_parts['attach'][$i]['Content-ID'] =
                    str_replace("$",'\$',trim(substr($matches[0],strlen(' Content-ID:'))," <>;.\"'\n"));
            }
            if(@$content_disposition) {
                $this->body_mime_parts['attach'][$i]['Content-Disposition'] = $content_disposition;
            }
            if(@$content_transfer_encoding) {
                $this->body_mime_parts['attach'][$i]['Content-Transfer-Encoding'] = $content_transfer_encoding;
            }
        //} else if(eregi("(content-|^)type: text/html",$sub_header)) {
        } else if(preg_match("/(content-|^)type: text\/html/im",$sub_header)) {
            $this->body_mime_parts['html'] = array();
            $this->body_mime_parts['html']['header'] = $sub_header;
            $this->body_mime_parts['html']['buffer'] = $body_part;
            if(@$content_transfer_encoding) {
                $this->body_mime_parts['html']['Content-Transfer-Encoding'] = $content_transfer_encoding;
            }
        //} else if(eregi("(content-|^)type: text/",$sub_header)) {
        } else if(preg_match("/(content-|^)type: text\//im",$sub_header)) {
            $this->body_mime_parts['text'] = array();
            $this->body_mime_parts['text']['header'] = $sub_header;
            $this->body_mime_parts['text']['buffer'] = $body_part;
            if(@$content_transfer_encoding) {
                $this->body_mime_parts['text']['Content-Transfer-Encoding'] = $content_transfer_encoding;
            }
/*
        //} else if(eregi("(content-|^)type: image/.*;",$sub_header)) {
        } else if(preg_match("/(content-|^)type: image\/.*;/im",$sub_header)) {
            if(!$this->body_mime_parts['image'] || !is_array($this->body_mime_parts['image'])) {
                $i=0;
            } else {
                $i = sizeof($this->body_mime_parts['image']);
            }
            $this->body_mime_parts['image'][$i] = array();
            $pos = strpos($sub_header,'name=');
            $len = strpos(substr($sub_header,$pos+5),"\n");
            if(!$len) $len = strlen($sub_header) - $pos;
            $name = trim(substr($sub_header,$pos+5,$len),"\"\n");
            $this->body_mime_parts['image'][$i]['header'] = $sub_header;
            $this->body_mime_parts['image'][$i]['name'] = $name;
            $this->body_mime_parts['image'][$i]['buffer'] = $body_part;
            if(preg_match('/(content-|^)type:.*\n/i',$sub_header,$matches)) {
                $this->body_mime_parts['image'][$i]['Content-Type'] = 
                    trim(substr($matches[0],ViewMsg::Stripos($matches[0],'image'))," <>;.\"'\n");
            }
            if(preg_match('/\nContent-ID:.*($|\n)/i',$sub_header,$matches)) {
                $this->body_mime_parts['image'][$i]['Content-ID'] = 
                    str_replace("$",'\$',trim(substr($matches[0],strlen(' Content-ID:'))," <>;.\"'\n"));
            }
            if(@$content_transfer_encoding) {
                $this->body_mime_parts['image'][$i]['Content-Transfer-Encoding'] = $content_transfer_encoding;
            }
            if(@$content_disposition) {
                $this->body_mime_parts['image'][$i]['Content-Disposition'] = $content_disposition;
            }
*/
        }
    }
    function InsertInlineImages() {
        if(!@$this->body_mime_parts['attach'] || sizeof($this->body_mime_parts['attach']) <= 0) {
            return;
        }
        //foreach($this->body_mime_parts['image'] as $img) {
        for($i=0; $i < sizeof($this->body_mime_parts['attach']); $i++) {
            //if(!eregi("image/",$this->body_mime_parts['attach'][$i]['Content-Type'])) {
            if(!preg_match("/image\//i",$this->body_mime_parts['attach'][$i]['Content-Type'])) {
                continue;
            }
            $img = & $this->body_mime_parts['attach'][$i];
            if(preg_match("/(\"|')(cid:\{.*\}\/|cid:.*|)".@$img['Content-ID']."[\"']/im",
                $this->body_result,$matches,PREG_OFFSET_CAPTURE)) {
                $end = strpos($img['buffer'],"\n\n");
                if(!$end) $end = strlen($img['buffer']);
                $this->body_result = substr($this->body_result,0,$matches[0][1]).
                    '"data:'.$img['Content-Type'].';'.$img['Content-Transfer-Encoding'].','.
                    substr($img['buffer'],0,$end).'"'.
                    substr($this->body_result,$matches[0][1]+strlen($matches[0][0]));
                //unset($this->body_mime_parts['image'][$i]['buffer']);
            } else if(preg_match("/=([[:space:]]+|)(\"|'|)".@$img['filename']."/i",
                $this->body_result,$matches,PREG_OFFSET_CAPTURE)) {
                $end = strpos($img['buffer'],"\n\n");
                if(!$end) $end = strlen($img['buffer']);
                $this->body_result = substr($this->body_result,0,$matches[0][1]).
                    '="data:'.$img['Content-Type'].';'.$img['Content-Transfer-Encoding'].','.
                    substr($img['buffer'],0,$end).'"'.
                    substr($this->body_result,$matches[0][1]+strlen($matches[0][0]));
                //unset($this->body_mime_parts['image'][$i]['buffer']);
            }
        }
    }
    
// TODO: for messages with content-type: text/plain and attachment insert <BR> for simple CR
    function & QuotedPrintableDecode(&$header,&$buffer) {
        //if(eregi("quoted-printable",$this->content_transfer_encoding)
        //|| eregi("Content-Transfer-Encoding: quoted-printable",$header)) {
        if(preg_match("/quoted-printable/i",@$this->content_transfer_encoding)
        || preg_match("/Content-Transfer-Encoding: quoted-printable/i",$header)) {
            // remove =\n first
            //$buffer=str_replace("=\n",'',$buffer);
            // remove =80 second (euro symbol)
            //$buffer=str_replace("=80","&euro;",$buffer);
            $buffer=preg_replace(
                array(
                    "/=\n/",
                    "/=80/",  // euro symbol
                    "/=92/"),  // &rsquo;
                array(
                    '', '&euro;', '&rsquo;'),
                $buffer);
            // decode others characters
            $buffer=quoted_printable_decode($buffer);
        } else if(eregi("Content-Transfer-Encoding: base64",$header)) {
            $buffer=base64_decode($buffer);
        }
        return $buffer;
    }

    function BuildBodyMessage() {
        // no content-type or text/plain only
        //if(!$this->content_type || eregi("text/plain",$this->content_type)) {
        if(!@$this->content_type || preg_match("/text\/plain/im",$this->content_type)) {
            $this->body_result = '<html><body><pre>'.
                preg_replace('/(\n|\r)/',"<br>",$this->QuotedPrintableDecode($this->header,
                    preg_replace(array('/</','/>/'),array('&lt;','&gt;'),
                        substr($this->message,$this->start_body)))).
                '</pre></body></html>';
        // html only
        //} else if(eregi("text/html",$this->content_type)) {
        } else if(preg_match("/text\/html/i",$this->content_type)) {
            $this->body_result = $this->QuotedPrintableDecode($this->header,substr($this->message,$this->start_body));
        // search for 'text/html' first, 'text/plain' after
        } else if(@$this->body_mime_parts['html']) {
            $this->body_result = $this->QuotedPrintableDecode($this->body_mime_parts['html']['header'],$this->body_mime_parts['html']['buffer']);
        } else if(@$this->body_mime_parts['text']) {
            $this->body_mime_parts['text']['buffer']=
                preg_replace(array('/</','/>/',"/(\n|\r)/"),array('&lt;','&gt;','<br>'),$this->body_mime_parts['text']['buffer']);
            $this->body_result = $this->QuotedPrintableDecode($this->body_mime_parts['text']['header'],
                $this->body_mime_parts['text']['buffer']);
        }
        //---------------------------------------------------------------------
        // DISABLED! converts urls and email address to clickable links
        /**
        $this->body_result=preg_replace(
            array(
            '/([^\'"])\b((http(s|)|ftp|file):\/\/[-A-Z0-9+&@#i\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])\b/i',
            '/\b(?:mailto:)?([A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4})\b/i'),
            array(
            '\1<a href="\2">\2</a>',
            '<a href="mailto:\1">\1</a>'),
            $this->body_result);
        */
        //---------------------------------------------------------------------
        $this->InsertInlineImages();
    }
    
    function BuildAttachList($type) {
        if(!@$this->body_mime_parts[$type] || !is_array($this->body_mime_parts[$type])) {
            return;
        }
        $this->attachment=array();
        for($i=0; $i < sizeof($this->body_mime_parts[$type]); $i++) {
            $this->attachment[$i] = & $this->body_mime_parts[$type][$i];
        }
    }
    
    function IsoDecode($data) {
        $retval='';

        if(!preg_match('/=\?.*\?([qb])\?.*\?=/im',$data)) {
            return $data;
        }
        $token = preg_split('/=\?/i',$data);
        foreach($token as $tok) {
            if(strlen($tok) <= 0) {
               continue;
            }
            if(!preg_match('/.*\?([qb])\?(.*)\?=(.*)/im',$tok,$matches,PREG_OFFSET_CAPTURE)) {
                $retval.=$tok;
            }
            if(!strcasecmp($matches[1][0],'b')) {
                $retval.=base64_decode($matches[2][0]).$matches[3][0];
            } else if(!strcasecmp($matches[1][0],'q')) {
                $retval.=str_replace('_',' ',quoted_printable_decode($matches[2][0])).$matches[3][0];
            }
        }
        return $retval;
    }

    function ParseHeader() {
        $pos=strpos($this->message,"\n\n");
        if($pos) {
            $this->start_body=$pos+1; // "2"
        } else {
            return false;
        }
        $this->header = substr($this->message,0,$pos);
        if(preg_match('/(\n|^)Content-Type:.*($|\n)/i',$this->header,$matches,PREG_OFFSET_CAPTURE)) {
            $this->content_type = substr($matches[0][0],strlen(' content_type:'));
            $this->content_type = trim(strtok($this->content_type,";\n")," ;,\"'");
            $pos = ViewMsg::Stripos(substr($this->header,$matches[0][1]),'boundary=');
            if($pos) {
                $this->boundary = substr(substr($this->header,$matches[0][1]),$pos+strlen('boundary='));
                $this->boundary = trim(preg_replace("/\n|\"|'/",'',strtok($this->boundary,"\n")));
            }
        }
        if(preg_match('/(\n|^)Content-Transfer-Encoding:.*(\n|$)/i',$this->header,$matches)) {
            $this->content_transfer_encoding = substr($matches[0],strlen(' Content-Transfer-Encoding:'));
            $this->content_transfer_encoding = trim(strtok($this->content_transfer_encoding,";\n")," ;,\"'");
        }
        if(preg_match('/(\n|^)subject:.*(\n|$)/i',$this->header,$matches)) {
            $this->subject = substr($matches[0],strlen(' subject:'));
            $this->subject = $this->IsoDecode(trim(strtok($this->subject,"\n")," ;,\"'"));
        }
        if(preg_match('/(\n|^)from:.*(\n|$)/i',$this->header,$matches)) {
            $this->from = substr($matches[0],strlen(' from:'));
            $this->from = $this->IsoDecode(trim(preg_replace("/\n/",'',strtok($this->from,"\n"))," ;,"));
        }
        if(preg_match('/(\n|^)to:.*(\n|$)/i',$this->header,$matches)) {
            $this->to = substr($matches[0],strlen(' to:'));
            $this->to = $this->IsoDecode(trim(preg_replace("/\n/",'',strtok($this->to,"\n"))," ;,"));
        }
    }

    function Stripos (& $haystack, $needle, $offset = 0) {
        if(function_exists('stripos')) {
            return stripos($haystack,$needle,$offset);
        }
        // PHP 4 doesn't define this function
        preg_match('/'.str_replace('/','\/',$needle).'/i',$haystack,$matches,PREG_OFFSET_CAPTURE,1);
        if(!$matches || !$matches[0][1]) {
            return false;
        }
        return $matches[0][1];
    }

    function strRightPos(&$buffer,&$needle) {
        if(version_compare(PHP_VERSION,'5','>=')) {
            $retval = strrpos($buffer,$needle);
            if(!$retval)
                $retval = strlen($buffer) - strlen($needle);
            return $retval;
        }
        // TODO: complete for PHP4
        // get last occurrence of needle
        // while
    }

    function TokenizeParts($start_part,$boundary) {
        $start=$start_part;
        $end=strpos($this->message,$boundary.'--',$start);
        if(!$end) {
            $end=$this->strRightPos($this->message,$boundary);
        }
        while($start=strpos($this->message,"\n--".$boundary."\n",$start)) {
            $start+=strlen($boundary)+3;
            if($pos=ViewMsg::Stripos(substr($this->message,$start,$end),"Content-Type:")) {
                $cnt=$start+$pos;
                //$start+=$pos;
                $pos = ViewMsg::Stripos(substr($this->message,$cnt,$end),'boundary=');
                if($pos) {
                    //$start += $pos+strlen('boundary=');
                    $start = $cnt+$pos+strlen('boundary=');
                    $crpos=strpos(substr($this->message,$start,$end),"\n");
                    $subboundary = substr($this->message,$start,$crpos);
                        //strpos(substr($this->message,$start,$end),"\n")));
                    $subboundary = trim($subboundary,"\"';\n\r ");
                    $start=strpos($this->message,"\n--".$subboundary,$start);
                    $this->TokenizeParts($start,$subboundary);
                } else {
                    $this->GetPartData($start,strpos(substr($this->message,$start,$end),'--'.$boundary));
                }
            } else {
                $this->GetPartData($start,strpos(substr($this->message,$start,$end),'--'.$boundary));
            }
        }
    }

    function Run() {
        if(!@$this->message || strlen($this->message) <= 0) {
            return -1;
        }
        $this->message=str_replace("\r",'',$this->message);
        $this->ParseHeader();
        $this->body_mime_parts=array();
            if(@$this->boundary && !strstr($this->content_type,'text/plain')) {
                $this->TokenizeParts($this->start_body,$this->boundary);
            } else {
                $this->GetPartData(0,strlen($this->message));
            }
        $this->BuildBodyMessage();
        if($this->debug) {
            var_dump(str_replace("<br>","\n",$this->body_result));
        }
        $this->BuildAttachList('attach');
    }
};
