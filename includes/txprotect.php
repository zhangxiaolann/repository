<?php
/*
反腾讯网址安全监测系统
Description:屏蔽腾讯电脑管家网址安全检测、屏蔽QQ浏览器访问、屏蔽深圳&上海IP访问
Version:1.0
Author:消失的彩虹海
*/
if(preg_match("/qq-manager/", strtolower($_SERVER['HTTP_USER_AGENT']))) {
	exit('正在建设中！');
}
if(!isset($_SESSION['xxqq_once'])) {
	$xxqq_ualist = "/(qqbrowser|tencenttraveler|qq|micromessenger)/i";
	function xxqq_sysmsg($msg = '无法访问') {
    ?>  
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>站点提示信息</title>
        <style type="text/css">
html{background:#eee}body{background:#fff;color:#333;font-family:"微软雅黑","Microsoft YaHei",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:10px 10px 10px rgba(0,0,0,.13);box-shadow:10px 10px 10px rgba(0,0,0,.13);opacity:.8}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px "微软雅黑","Microsoft YaHei",,sans-serif;margin:30px 0 0 0;padding:0;padding-bottom:7px}#error-page{margin-top:50px}h3{text-align:center}#error-page p{font-size:9px;line-height:1.5;margin:25px 0 20px}#error-page code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px;font-size:9px}a{color:#21759B;text-decoration:none;margin-top:-10px}a:hover{color:#D54E21}.button{background:#f7f7f7;border:1px solid #ccc;color:#555;display:inline-block;text-decoration:none;font-size:9px;line-height:26px;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;-webkit-border-radius:3px;-webkit-appearance:none;border-radius:3px;white-space:nowrap;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);vertical-align:top}.button.button-large{height:29px;line-height:28px;padding:0 12px}.button:focus,.button:hover{background:#fafafa;border-color:#999;color:#222}.button:focus{-webkit-box-shadow:1px 1px 1px rgba(0,0,0,.2);box-shadow:1px 1px 1px rgba(0,0,0,.2)}.button:active{background:#eee;border-color:#999;color:#333;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5)}table{table-layout:auto;border:1px solid #333;empty-cells:show;border-collapse:collapse}th{padding:4px;border:1px solid #333;overflow:hidden;color:#333;background:#eee}td{padding:4px;border:1px solid #333;overflow:hidden;color:#333}
        </style>
    </head>
    <body id="error-page">
        <?php echo '<h3>站点提示信息</h3>';
        echo $msg; ?>
    </body>
    </html>
    <?php
     die;
	}
	function xxqq_ipcity($ip)
	{
    $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=';
    @$city = get_curl($url . $ip);
    $city = json_decode($city, true);
    if ($city['city']) {
        $location = $city['city'];
    } else {
        $location = $city['province'];
    }
	if($location){
		return $location;
	}else{
		return false;
	}
	}
	if(preg_match($xxqq_ualist, strtolower($_SERVER['HTTP_USER_AGENT']))) {
		xxqq_sysmsg('抱歉，本站暂不支持QQ浏览器、微信浏览器等腾讯旗下的浏览器访问！请使用其他浏览器访问本站，谢谢配合！');
	}
	$xxqq_city=xxqq_ipcity($_SERVER["REMOTE_ADDR"]);
	if($xxqq_city=='深圳'||$xxqq_city=='上海') {
		xxqq_sysmsg('抱歉，本站暂不支持'.$xxqq_city.'地区的用户访问！如需访问请使用代理，谢谢配合！<br/><br/><a href="http://www.xicidaili.com/nt/" target="_blank">最新代理服务器列表</a><br/><a href="http://jingyan.baidu.com/article/fd8044fa964b8b5031137ac1.html" target="_blank">电脑设置代理服务器教程</a><br/><a href="http://jingyan.baidu.com/article/fd8044faebfaa85030137a72.html" target="_blank">安卓手机设置代理服务器教程</a>');
	}
	$_SESSION['xxqq_once']=1;
	unset($xxqq_city);
	unset($xxqq_ualist);
}