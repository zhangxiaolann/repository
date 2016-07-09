<?php
error_reporting(0);
define('IN_CRONLITE', true);
define('VERSION', '5120');
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');
define('TIMESTAMP', time());

session_start();

date_default_timezone_set("PRC");
$date = date("Y-m-d H:i:s");

$scriptpath=str_replace('\\','/',$_SERVER['SCRIPT_NAME']);
$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$siteurl = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$sitepath.'/';

if(preg_match("/qq-manager/", strtolower($_SERVER['HTTP_USER_AGENT']))) {
	exit('正在建设中！'); //屏蔽电脑管家网站安全检测
}

if(is_file(ROOT.'includes/360safe/360webscan.php')){//360网站卫士
    require_once(ROOT.'includes/360safe/360webscan.php');
}

if(defined("SAE_ACCESSKEY"))
include_once ROOT.'includes/sae.php';
else
include_once ROOT.'config.php';

if(!defined('SQLITE') && (!$user||!$pwd||!$dbname))//检测安装
{
header('Content-type:text/html;charset=utf-8');
echo '你还没安装！<a href="install/">点此安装</a>';
exit();
}

if(!isset($port))$port='3306';
//连接数据库
include_once("db.class.php");
if(defined('SQLITE'))$DB=new DB($db_file);
else $DB=new DB($host,$user,$pwd,$dbname,$port);

if($DB->query("select * from wjob_config where id='1'")==FALSE)//检测安装2
{
header('Content-type:text/html;charset=utf-8');
echo '<div class="row">你还没安装！<a href="install/">点此安装</a></div>';
exit();
}

$conf=$DB->get_row("SELECT * FROM wjob_config WHERE 1 limit 1");//获取系统配置

include_once(ROOT."includes/set.php");
include_once(ROOT."includes/signapi.php");
include_once(ROOT."includes/function.php");
include_once(ROOT."includes/qq.func.php");

if (!file_exists(ROOT.'install/job.lock') && file_exists(ROOT.'install/index.php')) {
	sysmsg('<h2>检测到无 job.lock 文件</h2><ul><li><font size="4">如果您尚未安装本程序，请<a href="./install/">前往安装</a></font></li><li><font size="4">如果您已经安装本程序，请手动放置一个空的 job.lock 文件到 /install 文件夹下，<b>为了您站点安全，在您完成它之前我们不会工作。</b></font></li></ul><br/><h4>为什么必须建立 job.lock 文件？</h4>它是彩虹云任务的保护文件，如果检测不到它，就会认为站点还没安装，此时任何人都可以安装/重装彩虹云任务。<br/><br/>',true);
}

$txprotect_domain=explode(",",$conf['txprotect_domain']);
if($conf['txprotect']==1)include_once(ROOT."includes/txprotect.php");
elseif($conf['txprotect']==2 && in_array($_SERVER['HTTP_HOST'],$txprotect_domain))include_once(ROOT."includes/txprotect.php");

//界面样式
if((!checkpc() || !file_exists(ROOT.'template/index.html')) && $mod=='home')$mod='index';

if(checkmobile()==true)
$theme=isset($_COOKIE["uachar"])?$_COOKIE["uachar"]:'mobile';

if(!isset($theme))
$theme=isset($_COOKIE["uachar"])?$_COOKIE["uachar"]:'default';
if($mod=='head')$theme='mobile';
if($conf['css2']==0)$theme='mobile';

define('TEMPLATE_ROOT', ROOT.'/template/'.$theme.'/');
define('PUBLIC_ROOT', ROOT.'/template/public/');

if($conf['version']<='5090')//检测更新
{
header('Content-type:text/html;charset=utf-8');
echo '<div class="row">新版本已准备就绪！<a href="install/update2.php">点此更新</a></div>';
exit();
}

$info=$DB->get_row("SELECT * FROM wjob_info WHERE sysid='0' limit 1");//获取任务运行信息

$sysname=array("0","①","②","③","④","⑤","⑥","⑦","⑧","⑨","⑩","⑪","⑫","⑬","⑭","⑮","⑯","⑰","⑱","⑲","⑳");

include_once(TEMPLATE_ROOT.'main.php');
include_once(ROOT."includes/member.php");


if($mod=='blank'){}
elseif($mod=='head')
	include ROOT.'/template/mobile/head.php';
elseif(file_exists(TEMPLATE_ROOT.$mod.'.php'))
	include TEMPLATE_ROOT.$mod.'.php';
elseif(file_exists(PUBLIC_ROOT.$mod.'.php'))
	include PUBLIC_ROOT.$mod.'.php';
else
	die('Template file not found');
?>