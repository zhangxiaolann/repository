<div class="modal fade" align="left" id="shop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="clearInterval(interval1)"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">在线支付</h4>
      </div>
      <div class="modal-body">
<?php
if(!defined('IN_CRONLITE'))exit();

$act=isset($_GET['act'])?$_GET['act']:null;

if($islogin==1)
{
switch($act) {
case 'pay':
	$type=isset($_GET['type'])?daddslashes($_GET['type']):exit('No paytype!');
	$shopid=isset($_GET['shopid'])?daddslashes($_GET['shopid']):exit('No shopid!');
	$qq=isset($_GET['qq'])?daddslashes($_GET['qq']):null;
	$money=$buy_rules[$shopid];
	if($shopid>=10&&$shopid<=11)$money=$qq;
	elseif($shopid==12)$money=$daili_rules[10];
	if($shopid==0)$name='1天试用VIP会员';elseif($shopid==1)$name='1个月VIP会员';elseif($shopid==2)$name='3个月VIP会员';elseif($shopid==3)$name='6个月VIP会员';elseif($shopid==4)$name='12个月VIP会员';elseif($shopid==5)$name='永久VIP会员';elseif($shopid==6)$name='1个QQ配额';elseif($shopid==7)$name='3个QQ配额';elseif($shopid==8)$name='5个QQ配额';elseif($shopid==9)$name='10个QQ配额';elseif($shopid==10)$name=($qq*$rules[0]).$conf['coin_name'];elseif($shopid==11)$name='代理余额'.$qq.'元';elseif($shopid==12)$name='开通代理商';
	$name.='-UID:'.$uid;

	if($money<=0)exit('该商品未出售！');
?>
<script>
    // 检查是否支付完成
	var interval1;
    function loadmsg(orderid) {
		$(".getshop").html('请稍候');
		if(orderid){
			var act = 'get';
			var text = '检测是否已付款';
		}else{
			var act = 'fill';
			var text = '确定';
			orderid = $("#orderid").val();
			if(orderid==''){
				alert('交易号不能为空！');
				$(".getshop").html('确定');
				return false;
			}
		}
        $.ajax({
            type: "GET",
            dataType: "json",
			cache: false,
            url: "ajax.php?mod=getshop&act="+act+"&type=<?php echo $type?>&shopid=<?php echo $shopid?>&qq=<?php echo $qq?>&orderid="+orderid,
            success: function (data, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (data.code == "1") {
					clearInterval(interval1);
                    // 这个脚本是 ie6和ie7 通用的脚本
                    if (confirm("您已支付完成，是否刷新页面？")) {
                        window.location.href="index.php?mod=shop";
                    } else {
                        // 用户取消
                    }
                } else {
					if(text == '确定')
						alert(data.msg);
                    $(".getshop").html(text);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus == "timeout") {
                    //loadmsg();
                } else { //异常
                    alert('创建连接失败');
                }
            }
        });
    }
</script>
<?php
	if($type=='alipay'){
		if($conf['alipay_api']==1){
			$orderid=date("YmdHis").rand(111,999);
			$sql="insert into `".DBQZ."_pay` (`uid`,`qq`,`type`,`orderid`,`addtime`,`shopid`,`name`,`money`,`status`) values ('".$uid."','".$qq."','".$type."','".$orderid."','".$date."','".$shopid."','".$name."','".$money."','0')";
			if($DB->query($sql))
				echo '<table class="table"><tobdy><tr><td>已经成功生成订单！</td></tr><tr><td>订单号：<u>'.$orderid.'</u></td></tr><tr><td>商品名称：<u>'.$name.'('.$money.'元)</u></td></tr><tr><td>支付方式：<u>支付宝</u></td></tr><tr><td><a href="index.php?mod=pay&type=alipay&orderid='.$orderid.'" class="btn btn-success btn-block" target="_blank">立即支付</a></td></tr></tbody></table><script>$(document).ready(function(){interval1=setInterval("loadmsg(\''.$orderid.'\')", 3000);});</script>';
			else
				echo '<table class="table"><tobdy><tr><td>生成订单失败！</td></tr><tr><td>原因：'.$DB->error().'</td></tr></tbody></table>';
		}elseif($conf['alipay_api']==2){
			$orderid=date("YmdHis").rand(111,999);
			$sql="insert into `".DBQZ."_pay` (`uid`,`qq`,`type`,`orderid`,`addtime`,`shopid`,`name`,`money`,`status`) values ('".$uid."','".$qq."','".$type."','".$orderid."','".$date."','".$shopid."','".$name."','".$money."','0')";
			if($DB->query($sql))
				echo '<table class="table"><tobdy><tr><td>已经成功生成订单！</td></tr><tr><td>订单号：<u>'.$orderid.'</u></td></tr><tr><td>商品名称：<u>'.$name.'('.$money.'元)</u></td></tr><tr><td>支付方式：<u>支付宝</u></td></tr><tr><td><a href="index.php?mod=pay&type=epay&type2=alipay&orderid='.$orderid.'" class="btn btn-success btn-block" target="_blank">立即支付</a></td></tr></tbody></table><script>$(document).ready(function(){interval1=setInterval("loadmsg(\''.$orderid.'\')", 3000);});</script>';
			else
				echo '<table class="table"><tobdy><tr><td>生成订单失败！</td></tr><tr><td>原因：'.$DB->error().'</td></tr></tbody></table>';
		}
	}elseif($type=='tenpay'){
		if($conf['tenpay_api']==1){
			$orderid=date("YmdHis").rand(111,999);
			$sql="insert into `".DBQZ."_pay` (`uid`,`qq`,`type`,`orderid`,`addtime`,`shopid`,`name`,`money`,`status`) values ('".$uid."','".$qq."','".$type."','".$orderid."','".$date."','".$shopid."','".$name."','".$money."','0')";
			if($DB->query($sql))
				echo '<table class="table"><tobdy><tr><td>已经成功生成订单！</td></tr><tr><td>订单号：<u>'.$orderid.'</u></td></tr><tr><td>商品名称：<u>'.$name.'('.$money.'元)</u></td><tr><td>支付方式：<u>财付通</u></td></tr><tr><td><a href="index.php?mod=pay&type=tenpay&orderid='.$orderid.'" class="btn btn-success btn-block" target="_blank">立即支付</a></td></tr></tbody></table><script>$(document).ready(function(){interval1=setInterval("loadmsg(\''.$orderid.'\')", 3000);});</script>';
			else
				echo '<table class="table"><tobdy><tr><td>生成订单失败！</td></tr><tr><td>原因：'.$DB->error().'</td></tr></tbody></table>';
		}elseif($conf['tenpay_api']==2){
			$orderid=date("YmdHis").rand(111,999);
			$sql="insert into `".DBQZ."_pay` (`uid`,`qq`,`type`,`orderid`,`addtime`,`shopid`,`name`,`money`,`status`) values ('".$uid."','".$qq."','".$type."','".$orderid."','".$date."','".$shopid."','".$name."','".$money."','0')";
			if($DB->query($sql))
				echo '<table class="table"><tobdy><tr><td>已经成功生成订单！</td></tr><tr><td>订单号：<u>'.$orderid.'</u></td></tr><tr><td>商品名称：<u>'.$name.'('.$money.'元)</u></td><tr><td>支付方式：<u>财付通</u></td></tr><tr><td><a href="index.php?mod=pay&type=epay&type2=tenpay&orderid='.$orderid.'" class="btn btn-success btn-block" target="_blank">立即支付</a></td></tr></tbody></table><script>$(document).ready(function(){interval1=setInterval("loadmsg(\''.$orderid.'\')", 3000);});</script>';
			else
				echo '<table class="table"><tobdy><tr><td>生成订单失败！</td></tr><tr><td>原因：'.$DB->error().'</td></tr></tbody></table>';
		}
	}elseif($type=='wxpay'){
		if($conf['wxpay_api']==1){
			$orderid=date("YmdHis").rand(111,999);
			$sql="insert into `".DBQZ."_pay` (`uid`,`qq`,`type`,`orderid`,`addtime`,`shopid`,`name`,`money`,`status`) values ('".$uid."','".$qq."','".$type."','".$orderid."','".$date."','".$shopid."','".$name."','".$money."','0')";
			if($DB->query($sql)){
				require_once SYSTEM_ROOT."wxpay/WxPay.Api.php";
				require_once SYSTEM_ROOT."wxpay/WxPay.NativePay.php";
				$notify = new NativePay();
				$input = new WxPayUnifiedOrder();
				$input->SetBody($name);
				$input->SetOut_trade_no($orderid);
				$input->SetTotal_fee($money*100);
				$input->SetSpbill_create_ip($clientip);
				$input->SetTime_start(date("YmdHis"));
				$input->SetTime_expire(date("YmdHis", time() + 600));
				$input->SetGoods_tag("test");
				$input->SetNotify_url($siteurl."other/wxpay_notify.php");
				$input->SetTrade_type("NATIVE");
				$result = $notify->GetPayUrl($input);
				if($result["result_code"]=='SUCCESS'){

				echo '<table class="table"><tobdy><tr><td>订单号：<u>'.$orderid.'</u></td></tr><tr><td>商品名称：<u>'.$name.'('.$money.'元)</u></td><tr><td>支付方式：<u>微信扫码支付</u></td></tr><tr><td><img alt="用微信扫码二维码" src="http://qr.topscan.com/api.php?text='.urlencode($result["code_url"]).'" style="width:150px;height:150px;"/><br/>请使用微信扫一扫 扫描二维码完成支付。<font color="red">支付期间请勿关闭此窗口，否则无法自动到账！</font></td></tr></tbody></table><script>$(document).ready(function(){interval1=setInterval("loadmsg(\''.$orderid.'\')", 4000);});</script>';
				}else{
					echo '<table class="table"><tobdy><tr><td>微信支付下单失败！</td></tr><tr><td>['.$result["err_code"].'] '.$result["err_code_des"].'</td></tr></tbody></table>';
				}
			}else
				echo '<table class="table"><tobdy><tr><td>生成订单失败！</td></tr><tr><td>原因：'.$DB->error().'</td></tr></tbody></table>';
		}elseif($conf['wxpay_api']==2){
			$orderid=date("YmdHis").rand(111,999);
			$sql="insert into `".DBQZ."_pay` (`uid`,`qq`,`type`,`orderid`,`addtime`,`shopid`,`name`,`money`,`status`) values ('".$uid."','".$qq."','".$type."','".$orderid."','".$date."','".$shopid."','".$name."','".$money."','0')";
			if($DB->query($sql))
				echo '<table class="table"><tobdy><tr><td>已经成功生成订单！</td></tr><tr><td>订单号：<u>'.$orderid.'</u></td></tr><tr><td>商品名称：<u>'.$name.'('.$money.'元)</u></td><tr><td>支付方式：<u>微信支付</u></td></tr><tr><td><a href="index.php?mod=pay&type=epay&type2=wxpay&orderid='.$orderid.'" class="btn btn-success btn-block" target="_blank">立即支付</a></td></tr></tbody></table><script>$(document).ready(function(){interval1=setInterval("loadmsg(\''.$orderid.'\')", 3000);});</script>';
			else
				echo '<table class="table"><tobdy><tr><td>生成订单失败！</td></tr><tr><td>原因：'.$DB->error().'</td></tr></tbody></table>';
		}
	}elseif($type=='qqpay'){
		if($conf['qqpay_api']==1){
			$orderid=date("YmdHis").rand(111,999);
			$sql="insert into `".DBQZ."_pay` (`uid`,`qq`,`type`,`orderid`,`addtime`,`shopid`,`name`,`money`,`status`) values ('".$uid."','".$qq."','".$type."','".$orderid."','".$date."','".$shopid."','".$name."','".$money."','0')";
			if($DB->query($sql)){
				require_once(SYSTEM_ROOT."tenpay/RequestHandler.class.php");

				/* 创建支付请求对象 */
				$reqHandler = new RequestHandler();
				$reqHandler->init();
				$reqHandler->setKey($conf['qqpay_key']);
				$reqHandler->setGateUrl("https://myun.tenpay.com/cgi-bin/wappayv2.0/wappay_init.cgi");

				//----------------------------------------
				//设置支付参数 
				//----------------------------------------
				$reqHandler->setParameter("ver", "2.0"); //版本号，ver默认值是1.0
				$reqHandler->setParameter("charset", "1"); //1 UTF-8, 2 GB2312
				$reqHandler->setParameter("bank_type", "0"); //银行类型
				$reqHandler->setParameter("desc", $name); //商品描述，32个字符以内
				$reqHandler->setParameter("pay_channel", "1"); //描述支付渠道
				$reqHandler->setParameter("bargainor_id", trim($conf['qqpay_pid']));
				$reqHandler->setParameter("sp_billno", $orderid);
				$reqHandler->setParameter("total_fee", $money*100);  //总金额
				$reqHandler->setParameter("fee_type", "1");               //币种
				$reqHandler->setParameter("notify_url", $siteurl.'other/qqpay_notify.php');

				//请求的URL
				$reqUrl = $reqHandler->getRequestURL();
				$data = get_curl($reqUrl);
				if(preg_match("!<token_id>(.*?)</token_id>!",$data,$match)){
					$code_url='https://myun.tenpay.com/mqq/pay/qrcode.html?_wv=1027&_bid=2183&t='.$match[1];

					echo '<table class="table"><tobdy><tr><td>订单号：<u>'.$orderid.'</u></td></tr><tr><td>商品名称：<u>'.$name.'('.$money.'元)</u></td><tr><td>支付方式：<u>手机QQ扫码支付</u></td></tr><tr><td><img alt="用手机QQ扫码二维码" src="http://qr.topscan.com/api.php?text='.urlencode($code_url).'" style="width:150px;height:150px;"/><br/>请使用手机QQ扫一扫 扫描二维码完成支付。<font color="red">支付期间请勿关闭此窗口，否则无法自动到账！</font></td></tr></tbody></table><iframe frameborder="0" style="display:none" src="mqqapi://forward/url?src_type=web&style=default&=1&version=1&url_prefix='.urlencode(base64_encode($code_url)).'"></iframe><script>$(document).ready(function(){interval1=setInterval("loadmsg(\''.$orderid.'\')", 4000);});</script>';
				}else{
					preg_match("!<err_info>(.*?)</err_info>!",$data,$match);
					echo '<table class="table"><tobdy><tr><td>手机QQ支付下单失败！</td></tr><tr><td>'.$match[1].'</td></tr></tbody></table>';
				}
			}else
				echo '<table class="table"><tobdy><tr><td>生成订单失败！</td></tr><tr><td>原因：'.$DB->error().'</td></tr></tbody></table>';
		}elseif($conf['qqpay_api']==2){
			$orderid=date("YmdHis").rand(111,999);
			$sql="insert into `".DBQZ."_pay` (`uid`,`qq`,`type`,`orderid`,`addtime`,`shopid`,`name`,`money`,`status`) values ('".$uid."','".$qq."','".$type."','".$orderid."','".$date."','".$shopid."','".$name."','".$money."','0')";
			if($DB->query($sql))
				echo '<table class="table"><tobdy><tr><td>已经成功生成订单！</td></tr><tr><td>订单号：<u>'.$orderid.'</u></td></tr><tr><td>商品名称：<u>'.$name.'('.$money.'元)</u></td><tr><td>支付方式：<u>手机QQ扫码支付</u></td></tr><tr><td><a href="index.php?mod=pay&type=epay&type2=qqpay&orderid='.$orderid.'" class="btn btn-success btn-block" target="_blank">立即支付</a></td></tr></tbody></table><script>$(document).ready(function(){interval1=setInterval("loadmsg(\''.$orderid.'\')", 3000);});</script>';
			else
				echo '<table class="table"><tobdy><tr><td>生成订单失败！</td></tr><tr><td>原因：'.$DB->error().'</td></tr></tbody></table>';
		}
	}
break;
}

}else{
	exit('登录失败，可能是密码错误或者身份失效了，请重新登录！');
}
?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="clearInterval(interval1)">Close</button>
      </div>
    </div>
  </div>
</div>