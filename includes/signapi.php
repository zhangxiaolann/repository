<?php
if(!defined('IN_CRONLITE'))exit();

$qqapiid=$conf['qqapiid'];
$qqloginid=$conf['qqloginid'];
$apiserverid=$conf['apiserver'];
$mail_api=isset($conf['mail_api'])?$conf['mail_api']:0;
$getss=$conf['getss'];

if($apiserverid==1)
{
	$apiserver='http://sign.cccyun.cn/';
}
elseif($apiserverid==2)
{
	$apiserver='http://3600.sturgeon.mopaas.com/';
}
elseif($apiserverid==3)
{
	$apiserver='http://clouds.aliapp.com/';
}
else
{
	$apiserver='http://sign.cccyun.cn/';
}

$apiserver2='http://sign.cccyun.cn/';
$apiserver3='http://3600.sturgeon.mopaas.com/';

if($qqapiid==1)
{
	$qqapi_server='http://cloud.odata.cc/';
}
elseif($qqapiid==2)
{
	$qqapi_server='http://api.odata.cc/';
}
elseif($qqapiid==3)
{
	$qqapi_server=$conf['myqqapi'];
}
else
{
	$qqapi_server=$siteurl;
}


if($qqloginid==1)
{
	$qqloginapi='http://clouds.aliapp.com/addqq.php?baseurl='.urlencode($siteurl).'&sitename='.urlencode($conf['sitename']);
}
elseif($qqloginid==2)
{
	$qqloginapi='http://login.qqzzz.net/addqq.php?baseurl='.urlencode($siteurl).'&sitename='.urlencode($conf['sitename']);
}
elseif($qqloginid==3)
{
	$qqloginapi='http://cloud.sgwap.net/addqq.php?baseurl='.urlencode($siteurl).'&sitename='.urlencode($conf['sitename']);
}

$qqlogin=($qqloginid!=0)?$qqloginapi:'index.php?mod=addqq';


if($mail_api==1)
{
	$mail_api_url='http://m.cccyun.cn/mail/';
}
elseif($mail_api==2)
{
	$mail_api_url='http://cloud.sgwap.net/mail/';
}
?>