<?php
error_reporting(0);
@ini_set("short_open_tag", "on");
date_default_timezone_set('PRC');
$mysql=require("../Common/Conf/db.php");
$dbhost=$mysql['DB_HOST'].':'.$mysql['DB_PORT'];
$dbuser=$mysql['DB_USER'];
$dbpassword=$mysql['DB_PWD'];
$dbmysql=$mysql['DB_NAME'];
if($con = mysql_connect($dbhost,$dbuser,$dbpassword)){
	mysql_select_db($dbmysql, $con);
}else{
	exit('数据库链接失败！');
}
mysql_query("set names utf8"); 
$tableqz=$mysql['DB_PREFIX'];
$result=mysql_query("select * from {$tableqz}webconfigs");
while($row = mysql_fetch_array($result)){ 
	$config[$row['vkey']]=$row['value'];
}
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <title><?=$config['web_name']?>VIP会员购买，支付宝在线付款</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="<?=$config['web_name']?>VIP会员购买">

        <!-- CSS -->
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/style.css">

    </head>

    <body>

        <div class="header">
            <div class="container">
                <div class="row">
                    <div class="logo span4">
                        <h1><a href=""><?=$config['web_name']?><span class="red"></span></a></h1>
                    </div>
                    <div class="links span8">
                        <a class="home" href="/" rel="tooltip" data-placement="bottom" data-original-title="主页"></a>
                        <a class="blog" target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?=$config['web_qq']?>&site=qq&menu=yes" rel="tooltip" data-placement="bottom" data-original-title="QQ"></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="register-container container">
            <div class="row">
                <div class="iphone span5">
                    <img src="assets/img/iphone.png" alt="">
                </div>
                <div class="register span6">
                    <form action="https://shenghuo.alipay.com/send/payment/fill.htm" method="POST" target="_blank" accept-charset="GBK">
                        <h2><span class="red"><?=$config['web_name']?>VIP购买</span></h2>
						<h4 style="text-align: left;line-height: 30px;"><?=$anounce?><br />支付成功后请等待10分钟左右，若充值后长时间未到账请联系客服QQ：<?=$config['web_qq']?><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?=$config['web_qq']?>&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:<?=$config['web_qq']?>:51" alt="点击这里给我发消息" title="点击这里给我发消息"/></a></h4><br /><div style="border-bottom: 2px dotted #bbb;"></div><br />
                        <input name="optEmail" type="hidden" value="<?=$alipay?>">
<label for="money">购买物品：</label>
<select name="payAmount" style="width: 98%;">
<?=$project?>
</select>                        <label for="number">充值帐号：</label>
                        <input type="text" id="title" name="title" type="hidden" placeholder="请填写你的用户名或UID">
                        <input name="memo" type="hidden" value="开通秒赞网VIP，请勿修改以上信息，用户名若填错请联系客服：<?=$config['web_qq']?>">
                        <button type="submit">立即付款</button>
                    </form>
                </div>
            </div>
        </div>
		<div align="center">Collect from <a href="/" target="_blank" title="<?=$config['web_name']?>"><?=$config['web_name']?></a></div>
        <!-- Javascript -->
        <script src="assets/js/jquery-1.8.2.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.backstretch.min.js"></script>
        <script src="assets/js/scripts.js"></script>

    </body>

</html>

