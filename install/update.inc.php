<?php
error_reporting(0);
define('IN_CRONLITE', true);
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');

date_default_timezone_set("PRC");
$date = date("Y-m-j H:i:s ");


if(defined("SAE_ACCESSKEY"))
include_once ROOT.'includes/sae.php';
else
include_once ROOT.'config.php';

if(!defined('SQLITE') && (!$user||!$pwd||!$dbname))//检测安装
{
header('Content-type:text/html;charset=utf-8');
echo '你还没安装！<a href="index.php">点此安装</a>';
exit();
}

$scriptpath=str_replace('\\','/',$_SERVER['SCRIPT_NAME']);
$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/install/'));
$siteurl = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$sitepath.'/';

if(!isset($port))$port='3306';
//连接数据库
include_once(ROOT."includes/db.class.php");
if(defined('SQLITE'))$DB = new DB($db_file);
else $DB = new DB($host,$user,$pwd,$dbname,$port);

$conf=$DB->get_row("SELECT * FROM wjob_config WHERE 1 limit 1");//获取系统配置

?>