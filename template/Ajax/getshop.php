<?php
if(!defined('IN_CRONLITE'))exit();
if($islogin==1)
{
$act=isset($_GET['act'])?$_GET['act']:null;
switch($act) {
case 'get':
	$type=isset($_GET['type'])?daddslashes($_GET['type']):exit('No paytype!');
	$orderid=isset($_GET['orderid'])?daddslashes($_GET['orderid']):exit('No orderid!');
	$qq=isset($_GET['qq'])?daddslashes($_GET['qq']):null;

	if($type=='wxpay'&&$conf['wxpay_api']==1){
		require_once SYSTEM_ROOT."wxpay/WxPay.Api.php";
		$input = new WxPayOrderQuery();
		$input->SetOut_trade_no($orderid);
		$data=WxPayApi::orderQuery($input);
		if($data['return_code']=='SUCCESS' && $data['result_code']=='SUCCESS'){
			if($data['trade_state']=='SUCCESS'){
				$srow=$DB->get_row("SELECT * FROM ".DBQZ."_pay WHERE orderid='{$data['out_trade_no']}' limit 1");

				$sql="insert into `".DBQZ."_order` (`type`,`orderid`,`trade_no`,`time`,`name`,`money`,`status`) values ('wxpay','".$data['out_trade_no']."','".$data['transaction_id']."','".$date."','".$srow['name']."','".$srow['money']."','2')";
				$DB->query($sql);
				if($srow['status']==0){
					$DB->query("update `".DBQZ."_pay` set `status` ='1',`endtime` ='$date' where `orderid`='$orderid'");
					getshop($srow['shopid'],$qq,$msg);
					exit('{"code":1,"msg":"付款成功"}');
				}else{
					exit('{"code":1,"msg":"已经购买过"}');
				}
			}else{
				$msg='['.$data['trade_state'].']'.$data['trade_state_desc'];
				exit('{"code":-1,"msg":"'.$msg.'"}');
			}
		}else{
			$msg='['.$data['err_code'].']'.$data['err_code_des'];
			exit('{"code":-1,"msg":"'.$msg.'"}');
		}
	}else{
		$row=$DB->get_row("SELECT * FROM ".DBQZ."_pay WHERE orderid='{$orderid}' limit 1");
		if($row['status']==1){
			exit('{"code":1,"msg":"付款成功"}');
		}else{
			exit('{"code":-1,"msg":"未付款"}');
		}
	}
break;
case 'fill':
	$type=isset($_GET['type'])?daddslashes($_GET['type']):exit('No paytype!');
	$orderid=isset($_GET['orderid'])?daddslashes($_GET['orderid']):exit('No orderid!');
	$shopid=isset($_GET['shopid'])?daddslashes($_GET['shopid']):exit('No shopid!');
	$qq=isset($_GET['qq'])?daddslashes($_GET['qq']):null;
	$money=$buy_rules[$shopid];
	$srow=$DB->get_row("SELECT * FROM ".DBQZ."_order WHERE trade_no='{$orderid}' limit 1");
	if($srow['status']==1){
		if(number_format($money,2) != number_format($srow['money'],2))
			exit('{"code":-1,"msg":"付款金额与商品价格不符"}');
		$orderid=date("YmdHis").rand(111,999);
		$sql="insert into `".DBQZ."_pay` (`uid`,`qq`,`type`,`orderid`,`addtime`,`endtime`,`shopid`,`money`,`status`) values ('".$uid."','".$srow['qq']."','".$type."','".$orderid."','".$date."','".$date."','".$shopid."','".$money."','1')";
		$DB->query($sql);
		$DB->query("update `".DBQZ."_order` set `status` ='2',`orderid` ='$orderid' where `trade_no`='{$srow['trade_no']}'");
		$msg = 'OK';
		getshop($shopid,$qq,$msg);
		exit('{"code":1,"msg":"付款成功"}');
	}elseif($srow['status']==2){
		exit('{"code":2,"msg":"该交易号已经成功购买过，请返回刷新查看"}');
	}else{
		exit('{"code":-1,"msg":"交易号不存在"}');
	}
break;
}
}else{
	exit('{"code":-3,"msg":"登录失败，可能是密码错误或者身份失效了，请重新登录！"}');
}