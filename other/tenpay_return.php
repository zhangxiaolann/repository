<?php

//---------------------------------------------------------
//财付通即时到帐支付页面回调示例，商户按照此文档进行开发即可
//---------------------------------------------------------
$mod='blank';
require_once("../includes/common.php");
require (SYSTEM_ROOT."tenpay/ResponseHandler.class.php");
require (SYSTEM_ROOT."tenpay/RequestHandler.class.php");
require (SYSTEM_ROOT."tenpay/client/ClientResponseHandler.class.php");
require (SYSTEM_ROOT."tenpay/client/TenpayHttpClient.class.php");


@header("Content-Type: text/html; charset=utf-8");

/* 创建支付应答对象 */
$resHandler = new ResponseHandler();
$resHandler->setKey($conf['tenpay_key']);

//判断签名
if($resHandler->isTenpaySign()) {
	
	//通知id
	$notify_id = $resHandler->getParameter("notify_id");
	
	//通过通知ID查询，确保通知来至财付通
	//创建查询请求
	$queryReq = new RequestHandler();
	$queryReq->init();
	$queryReq->setKey($conf['tenpay_key']);
	$queryReq->setGateUrl("https://gw.tenpay.com/gateway/verifynotifyid.xml");
	$queryReq->setParameter("partner", $conf['tenpay_pid']);
	$queryReq->setParameter("notify_id", $notify_id);
	
	//通信对象
	$httpClient = new TenpayHttpClient();
	$httpClient->setTimeOut(5);
	//设置请求内容
	$httpClient->setReqContent($queryReq->getRequestURL());
	
	//后台调用
	if($httpClient->call()) {
		//设置结果参数
		$queryRes = new ClientResponseHandler();
		$queryRes->setContent($httpClient->getResContent());
		$queryRes->setKey($conf['tenpay_key']);
		
		//判断签名及结果
		//只有签名正确,retcode为0，trade_state为0才是支付成功
		if($queryRes->isTenpaySign() && $queryRes->getParameter("retcode") == "0" && $queryRes->getParameter("trade_state") == "0" && $queryRes->getParameter("trade_mode") == "1" ) {
			//取结果参数做业务处理
			$out_trade_no = $queryRes->getParameter("out_trade_no");
			//财付通订单号
			$transaction_id = $queryRes->getParameter("transaction_id");
			//金额,以分为单位
			$total_fee = $queryRes->getParameter("total_fee");
			//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
			$discount = $queryRes->getParameter("discount");

			//------------------------------
			//处理业务开始
			//------------------------------

			$srow=$DB->get_row("SELECT * FROM ".DBQZ."_pay WHERE orderid='{$out_trade_no}' limit 1");

			$sql="insert into `".DBQZ."_order` (`type`,`orderid`,`trade_no`,`time`,`name`,`money`,`status`) values ('tenpay','".$out_trade_no."','".$trade_no."','".$date."','".$srow['name']."','".$srow['money']."','2')";
			$DB->query($sql);
			if($srow['status']==0){

				$DB->query("update `".DBQZ."_pay` set `status` ='1',`endtime` ='$date' where `orderid`='$out_trade_no'");

				$msg = 'OK';
				if(getshop($srow['shopid'],$srow['qq'],$msg)){
					exit('<script>window.location.href="../index.php?mod=shop";</script>');
				}else{
					exit('<script>window.location.href="../index.php?mod=shop";</script>');
				}
			}else{
				exit('<script>window.location.href="../index.php?mod=shop";</script>');
			}

		} else {
			//当做不成功处理
			exit('即时到帐支付失败！');
		}
	} else {
		exit('订单通知查询失败:' . $httpClient->getResponseCode() .',' . $httpClient->getErrInfo());
	}
} else {
	exit('认证签名失败！'.$resHandler->getDebugInfo());
}

?>