#!/bin/bash
/usr/bin/wget --user=username --password=userpass http://yourserver.com/pop3ml/mlsend.php -O - -t 1 >>/var/log/pop3ml.log
