<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

@error_reporting(E_ALL & ~E_NOTICE);
@session_start();
@date_default_timezone_set('PRC');
@header('Content-Type: text/html; charset=UTF-8');

function getspider($useragent=''){
	if(!$useragent){$useragent = $_SERVER['HTTP_USER_AGENT'];}
	$useragent=strtolower($useragent);
	if (strpos($useragent, 'baiduspider') !== false){return 'baiduspider';}
	if (strpos($useragent, 'googlebot') !== false){return 'googlebot';}
	if (strpos($useragent, 'soso') !== false){return 'soso';}
	if (strpos($useragent, 'bing') !== false){return 'bing';}
	if (strpos($useragent, 'yahoo') !== false){return 'yahoo';}
	if (strpos($useragent, 'sohu-search') !== false){return 'Sohubot';}
	if (strpos($useragent, 'sogou') !== false){return 'sogou';}
	if (strpos($useragent, 'youdaobot') !== false){return 'YoudaoBot';}
	if (strpos($useragent, 'yodaobot') !== false){return 'YodaoBot';}
	if (strpos($useragent, 'robozilla') !== false){return 'Robozilla';}
	if (strpos($useragent, 'msnbot') !== false){return 'msnbot';}
	if (strpos($useragent, 'lycos') !== false){return 'Lycos';}
	if (strpos($useragent, 'ia_archiver') !== false || strpos($useragent, 'iaarchiver') !== false){return 'alexa';}
	if (strpos($useragent, 'archive.org_bot') !== false){return 'Archive';} 
	if (strpos($useragent, 'robozilla') !== false){return 'Robozilla';} 
	if (strpos($useragent, 'sitebot') !== false){return 'SiteBot';} 
	if (strpos($useragent, 'mj12bot') !== false){return 'MJ12bot';} 
	if (strpos($useragent, 'gosospider') !== false){return 'gosospider';} 
	if (strpos($useragent, 'gigabot') !== false){return 'Gigabot';} 
	if (strpos($useragent, 'yrspider') !== false){return 'YRSpider';} 
	if (strpos($useragent, 'gigabot') !== false){return 'Gigabot';} 
	if (strpos($useragent, 'jikespider') !== false){return 'jikespider';} 
	if (strpos($useragent, 'addsugarspiderbot') !== false){return 'AddSugarSpiderBot';/*非常少*/} 
	if (strpos($useragent, 'testspider') !== false){return 'TestSpider';} 
	if (strpos($useragent, 'etaospider') !== false){return 'EtaoSpider';} 
	if (strpos($useragent, 'wangidspider') !== false){return 'WangIDSpider';} 
	if (strpos($useragent, 'foxspider') !== false){return 'FoxSpider';} 
	if (strpos($useragent, 'docomo') !== false){return 'DoCoMo';} 
	if (strpos($useragent, 'yandexbot') !== false){return 'YandexBot';} 
	if (strpos($useragent, 'ezooms') !== false){return 'Ezooms';/*个人*/} 
	if (strpos($useragent, 'sinaweibobot') !== false){return 'SinaWeiboBot';} 
	if (strpos($useragent, 'catchbot') !== false){return 'CatchBot';} 
	if (strpos($useragent, 'surveybot') !== false){return 'SurveyBot';} 
	if (strpos($useragent, 'dotbot') !== false){return 'DotBot';} 
	if (strpos($useragent, 'purebot') !== false){return 'Purebot';} 
	if (strpos($useragent, 'ccbot') !== false){return 'CCBot';} 
	if (strpos($useragent, 'mlbot') !== false){return 'MLBot';} 
	if (strpos($useragent, 'adsbot-google') !== false){return 'AdsBot-Google';}
	if (strpos($useragent, 'ahrefsbot') !== false){return 'AhrefsBot';}
	if (strpos($useragent, 'spbot') !== false){return 'spbot';}
	if (strpos($useragent, 'augustbot') !== false){return 'AugustBot';}
	return false;
}

if($_GET['rand'] && $_COOKIE['vmz_session']!=$_GET['rand']){
	@header("Location:");
}
if(!$_COOKIE['vmz_session']){
	if(!getspider()){
		$vmz_session=md5(uniqid().rand(1,1000));
		$_SESSION['vmz_session']=$vmz_session;
		setcookie("vmz_session",$vmz_session,time()+3600*24*14,'/');
		exit("<script language='javascript'>window.location.href='/?rand={$vmz_session}';</script>");
	}
}


// 检测PHP环境
if(version_compare(PHP_VERSION,'5.0.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',1);

// 定义应用目录
define('APP_PATH','./');
// 定义网站根目录目录
define('APP_ROOT',dirname(__FILE__));

// 判断是否安装
if(!file_exists(dirname(__FILE__).'/Common/Conf/db.php')){
	@header("Location:/install");
	exit();
}
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单