-- MySQL dump 10.11
--
-- Host: localhost    Database: pop3ml
-- ------------------------------------------------------
--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL auto_increment,
  `date` datetime,
  `state` set('sent','sentdigest','pending','queued'),
  `listname` varchar(128),
  `smtp` varchar(128),
  `mailfrom` text,
  `subject` text,
  `message` longtext,
  `header` longtext,
  `keyvalue` varchar(128),
  `rowlock` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

--
-- Table structure for table `mltable`
--

DROP TABLE IF EXISTS `mltable`;
CREATE TABLE `mltable` (
  `id` int(11) NOT NULL auto_increment,
  `typology` set('default'),
  `listname` varchar(128),
  `hostname` tinytext,
  `listaddr` tinytext,
  `listuser` tinytext,
  `listpoppass` tinytext,
  `listowneremail` tinytext,
  `parentlist` text,
  `msgsize` varchar(32),
  `smtpserver` longtext,
  `mltype` set('m','n'),
  `confirmsub` set('yes','no'),
  `confirmunsub` set('yes','no'),
  `moderatedlist` set('yes','no'),
  `subscriptionmod` set('yes','no'),
  `subscribersonly` set('yes','no'),
  `removeafterpop` set('yes','no'),
  `shutdown` set('yes','no'),
  `recipientlimit` int(11),
  `senddigest` varchar(32),
  `digestmaxsize` varchar(32),
  `digestmaxmsg` varchar(32),
  `sublist` longtext,
  `modsublist` longtext,
  `denysublist` longtext,
  `allowsublist` longtext,
  `digestsublist` longtext,
  `mailfilter` longtext,
  `headerchange` longtext,
  `trailerfile` longtext,
  `language` longtext,
  `rowlock` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `listname` (`listname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `queue`
--

DROP TABLE IF EXISTS `queue`;
CREATE TABLE `queue` (
  `id` int(11) NOT NULL auto_increment,
  `date` datetime,
  `smtp` varchar(128),
  `listname` varchar(64),
  `messageid` varchar(12),
  `addresses` longtext,
  `rowlock` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `subqueue`
--

DROP TABLE IF EXISTS `subqueue`;
CREATE TABLE `subqueue` (
  `code` varchar(128) NOT NULL,
  `request` set('subscription','unsubscription'),
  `keyvalue` varchar(128),
  `date` datetime,
  `rowlock` text,
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `subscribers`
--

DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL auto_increment,
  `emailaddress` varchar(128),
  `firstname` varchar(128),
  `lastname` varchar(128),
  `address` varchar(128),
  `city` varchar(128),
  `country` varchar(128),
  `telephone` varchar(64),
  `state` set('enabled','disabled','suspended') default 'enabled',
  `webpass` tinytext,
  `rowlock` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `emailaddress` (`emailaddress`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dump completed on 2008-12-12  8:35:09
