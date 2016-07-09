DROP TABLE IF EXISTS `wjob_user`;
create table `wjob_user` (
`userid` int(11) NOT NULL auto_increment,
`pass` varchar(150) NOT NULL,
`user` varchar(150) NOT NULL,
`num` varchar(100) NOT NULL default '0',
`qqnum` INT(150) NOT NULL default '0',
`date` datetime NOT NULL,
`last` datetime NOT NULL,
`zcip` VARCHAR( 15 ) DEFAULT NULL,
`dlip` VARCHAR( 15 ) DEFAULT NULL,
`email` varchar(150) NULL,
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wjob_chat`;
CREATE TABLE `wjob_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(150) DEFAULT NULL,
  `sj` varchar(150) DEFAULT NULL,
  `nr` varchar(500) DEFAULT NULL,
  `to` varchar(150) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wjob_info`;
create table `wjob_info` (
`sysid` int(11) NOT NULL,
`last` datetime NULL,
`times` int(150) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sysid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `wjob_info`(`sysid`, `times`) VALUES
('0', '0'),
('1', '0'),
('2', '0'),
('3', '0'),
('4', '0'),
('5', '0'),
('6', '0'),
('7', '0'),
('8', '0'),
('3001', '0');

DROP TABLE IF EXISTS `wjob_job`;
create table `wjob_job` (
`jobid` int(11) NOT NULL auto_increment,
`sysid` INT( 150 ) NOT NULL,
`type` INT( 4 ) NOT NULL default '0',
`url` text NOT NULL,
`lx` varchar(150) NOT NULL default '0',
`mc` VARCHAR( 255 ) NOT NULL default '网址挂刷任务',
`usep` int(1) NULL,
`proxy` varchar(30) NULL,
`referer` varchar(250) NULL,
`useragent` varchar(250) NULL,
`start` int(2) NULL,
`stop` int(2) NULL,
`zt` int(1) NOT NULL default '0',
`post` int(1) NULL,
`postfields` text NULL,
`cookie` text NULL,
`timea` datetime NOT NULL,
`timeb` datetime NOT NULL,
`times` varchar(250) NOT NULL default '0',
`server` varchar(250) NOT NULL default '1',
`pl` INT(150) NOT NULL DEFAULT '0',
`time` INT(150) NOT NULL DEFAULT '0',
 PRIMARY KEY (`jobid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wjob_qq`;
create table `wjob_qq` (
`id` int(11) NOT NULL auto_increment,
`lx` varchar(150) NOT NULL default '0',
`qq` varchar(20) NOT NULL,
`pw` varchar(150) NULL,
`sid` varchar(150) NULL,
`skey` varchar(150) NULL,
`ptsig` VARCHAR(150) NULL,
`pskey` VARCHAR(150) NULL,
`pskey2` VARCHAR(150) NULL,
`apiid` INT(4) NOT NULL default '0',
`status` INT(1) NOT NULL default '0',
`status2` int(4) NOT NULL default '0',
`time` datetime NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wjob_config`;
create table `wjob_config` (
`id` int(1) NOT NULL auto_increment,
`zc` int(1) NOT NULL default '1',
`max` int(10) NOT NULL,
`sjyl` int(1) NOT NULL,
`pagesize` int(10) NOT NULL,
`sitename` text NOT NULL,
`sitetitle` text NULL,
`build` datetime NULL,
`gg` text NOT NULL,
`guang` text NOT NULL,
`bottom` text NOT NULL,
`footer` TEXT NULL,
`times` int(11) NOT NULL default '0',
`interval` int(10) NOT NULL DEFAULT '0',
`version` int(4) NOT NULL,
`switch` int(1) NOT NULL DEFAULT '1',
`css` int(1) NOT NULL DEFAULT '1',
`css2` int(1) NOT NULL DEFAULT '1',
`sysnum` int(2) NOT NULL,
`bulk` INT( 10 ) NOT NULL DEFAULT  '10',
`adminid` INT( 10 ) NOT NULL,
`seconds` VARCHAR( 150 ) NOT NULL DEFAULT  '0-0-0-0-0-0-0-0',
`show` TEXT NOT NULL,
`block` TEXT NULL,
`banned` TEXT NULL,
`apiserver` INT(2) NOT NULL,
`multi` VARCHAR( 150 ) NOT NULL DEFAULT '0-0-0-0-0-0-0-0',
`loop` VARCHAR(150) NOT NULL DEFAULT '0-0-0-0-0-0-0-0',
`jifen` INT(1) NOT NULL DEFAULT  '0',
`rules` TEXT NULL,
`cronkey` VARCHAR(150) DEFAULT NULL,

`qqapiid` INT(4) NOT NULL DEFAULT 0,
`qqloginid` INT(4) NOT NULL DEFAULT 1,
`mail_name` VARCHAR(150) NULL,
`mail_pwd` VARCHAR(150) NULL,
`mail_stmp` VARCHAR(150) NULL,
`mail_port` VARCHAR(150) NULL,
`mail_api` int(1) NOT NULL DEFAULT 0,
`siteurl` VARCHAR(150) NULL,
`kfqq` VARCHAR(150) NULL,

`qqblock` text NULL,
`txprotect` int(1) NOT NULL DEFAULT 0,
`txprotect_domain` text NULL,
`getss` int(1) NOT NULL DEFAULT 0,
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `wjob_config` (`id`, `zc`, `max`, `sjyl`, `pagesize`, `sitename`, `gg`, `guang`, `bottom`, `footer`, `times`, `interval`, `version`, `switch`, `css`, `sysnum`, `bulk`, `adminid`, `seconds`, `show`, `apiserver`) VALUES
(1, 1, 120, 0, 30, '网站监控', '<font color=green>★免费网址监控，且用且珍惜★萌萌逼阉割版彩虹5.12-QQ:1094908379</font>', '>><a href=http://xmbk.xsscp.com>萌萌逼博客</a><br><a href=http://xmbk.xsscp.com>萌萌逼博客</a>|<a href=http://xmbk.xsscp.com>萌萌逼博客</a>', '域名:<a href="/">xmbk.xsscp.com</a><br/>[QQ]1094908379<a href="./qq/api/joingroup.php?qun=">[加入]</a>', '模板设计:<a href="http://zhizhe8.net/" target="_blank">Kenvix</a>', 1, 50, 5120, 1, 1, 4, 10, 1, '0-0-0-0-0-0-0-0', '1分钟|1分钟|1分钟|5分钟|5分钟|6小时|6小时|12小时', 1)
