<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

$mod='blank';
$nosecu=true;
require_once("../includes/common.php");
require_once(SYSTEM_ROOT."epay/epay.config.php");
require_once(SYSTEM_ROOT."epay/epay_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {//验证成功
	//商户订单号
	$out_trade_no = $_GET['out_trade_no'];

	//支付宝交易号
	$trade_no = $_GET['trade_no'];

	//交易状态
	$trade_status = $_GET['trade_status'];

	$srow=$DB->get_row("SELECT * FROM ".DBQZ."_pay WHERE orderid='{$out_trade_no}' limit 1");
	$uid=$srow['uid'];

	$sql="insert into `".DBQZ."_order` (`type`,`orderid`,`trade_no`,`time`,`name`,`money`,`status`) values ('alipay','".$out_trade_no."','".$trade_no."','".$date."','".$srow['name']."','".$srow['money']."','2')";
	$DB->query($sql);


    if($_GET['trade_status'] == 'TRADE_FINISHED') {
		//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
    }
    else if ($_GET['trade_status'] == 'TRADE_SUCCESS' && $srow['status']==0) {
		//付款完成后，支付宝系统发送该交易状态通知
		$DB->query("update `".DBQZ."_pay` set `status` ='1',`endtime` ='$date' where `orderid`='$out_trade_no'");
		$msg = 'OK';
		$row=$DB->get_row("select * from ".DBQZ."_user where userid='".$uid."' limit 1");
		if($row['vip']==1 && strtotime($row['vipdate'])>time() || $row['vip']==2){
			$isvip=1;
		}else{
			$isvip=0;
		}
		getshop($srow['shopid'],$srow['qq'],$msg);
    }

	echo "success";
}
else {
    //验证失败
    echo "fail";
}
?>