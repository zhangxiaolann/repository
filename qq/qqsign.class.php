<?php
/*
 *腾讯签到功能类
 *Author：消失的彩虹海 & 云上的影子
*/
class qqsign{
	public $msg;
	public function __construct($uin,$sid=null,$skey=null,$pskey=null){
		$this->uin=$uin;
		$this->sid=$sid;
		$this->skey=$skey;
		$this->pskey=$pskey;
		$this->gtk=$this->getGTK($skey);
		$this->gtk2=$this->getGTK2($skey);
		if($pskey==null)
			$this->cookie='pt2gguin=o0'.$uin.'; uin=o0'.$uin.'; skey='.$skey.';';
		else
			$this->cookie='pt2gguin=o0'.$uin.'; uin=o0'.$uin.'; skey='.$skey.'; p_skey='.$pskey.'; p_uin=o0'.$uin.';';
	}
	public function get_curl($url,$post=0,$referer=1,$cookie=0,$header=0,$ua=0,$nobaody=0,$json=0){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$httpheader[] = "Accept:application/json";
		$httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
		$httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
		$httpheader[] = "Connection:close";
		if($json){
			$httpheader[] = "Content-Type:application/json; charset=utf-8";
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
		if($post){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		if($header){
			curl_setopt($ch, CURLOPT_HEADER, TRUE);
		}
		if($cookie){
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		if($referer){
			if($referer==1){
				curl_setopt($ch, CURLOPT_REFERER, 'http://m.qzone.com/infocenter?g_f=');
			}else{
				curl_setopt($ch, CURLOPT_REFERER, $referer);
			}
		}
		if($ua){
			curl_setopt($ch, CURLOPT_USERAGENT,$ua);
		}else{
			curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.5 Mobile Safari/533.1');
		}
		if($nobaody){
			curl_setopt($ch, CURLOPT_NOBODY,1);
		}
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$ret = curl_exec($ch);
		curl_close($ch);
		//$ret=mb_convert_encoding($ret, "UTF-8", "UTF-8");
		return $ret;
	}
	private function getGTK($skey){
        $len = strlen($skey);
        $hash = 5381;
        for ($i = 0; $i < $len; $i++) {
            $hash += ($hash << 5 & 2147483647) + ord($skey[$i]) & 2147483647;
            $hash &= 2147483647;
        }
        return $hash & 2147483647;
    }
	private function getGTK2($skey){
		$salt = 5381;
		$md5key = 'tencentQQVIP123443safde&!%^%1282';
		$hash = array();
		$hash[] = ($salt << 5);
		for($i = 0; $i < strlen($skey); $i ++)
		{
			$ASCIICode = mb_convert_encoding($skey[$i], 'UTF-32BE', 'UTF-8');
			$ASCIICode = hexdec(bin2hex($ASCIICode));
			$hash[] = (($salt << 5) + $ASCIICode);
			$salt = $ASCIICode;
		}
		$md5str = md5(implode($hash) . $md5key);
		return $md5str;
	}
	private function getGTK3($skey){
		$salt = 108;
		$md5key = 'tencent.mobile.qq.csrfauth';
		$hash = array();
		$hash[] = ($salt << 5);
		for($i = 0; $i < strlen($skey); $i ++)
		{
			$ASCIICode = mb_convert_encoding($skey[$i], 'UTF-32BE', 'UTF-8');
			$ASCIICode = hexdec(bin2hex($ASCIICode));
			$hash[] = (($salt << 5) + $ASCIICode);
			$salt = $ASCIICode;
		}
		$md5str = md5(implode($hash) . $md5key);
		return $md5str;
	}
	private function getToken($token){
		$len = strlen($token);
		$hash = 0;
		for ($i = 0; $i < $len; $i++) {
			$hash = fmod($hash * 33 + ord($token[$i]), 4294967296);
		}
        return $hash;
    }
	public function vipqd()
	{
		$data=$this->get_curl("http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=79968&format=json&g_tk=" . $this->gtk2 ."&cachetime=".time(),0,'http://vip.qq.com/',$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0)
			$this->msg[] = $this->uin.' 会员面板签到成功！';
		elseif($arr['ret']==10601)
			$this->msg[] = $this->uin.' 会员面板今天已经签到！';
		elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[] = $this->uin.' 会员面板签到失败！SKEY过期';
		}elseif($arr['ret']==20101)
			$this->msg[] = $this->uin.' 会员面板签到失败！不是QQ会员！';
		else
			$this->msg[] = $this->uin.' 会员面板签到失败！'.$arr['msg'];

		//$url='http://vipfunc.qq.com/act/client_oz.php?action=client&g_tk='.$this->gtk2;
		//$data=$this->get_curl($url,0,$url,$this->cookie);

		//$url='http://vipfunc.qq.com/growtask/sign.php?cb=vipsign.signCb&action=daysign&actId=16&fotmat=json&t='.time().'141&g_tk='.$this->gtk2;
		//$data=$this->get_curl($url,0,$url,$this->cookie);
		$data=$this->get_curl("http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=23314&format=json&g_tk=" . $this->gtk2 ."&cachetime=".time(),0,'http://vip.qq.com/',$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0)
			$this->msg[] = $this->uin.' 会员网页版签到成功！';
		elseif($arr['ret']==10601)
			$this->msg[] = $this->uin.' 会员网页版今天已经签到！';
		elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[] = $this->uin.' 会员网页版签到失败！SKEY过期';
		}elseif($arr['ret']==20101)
			$this->msg[] = $this->uin.' 会员网页版签到失败！不是QQ会员！';
		else
			$this->msg[] = $this->uin.' 会员网页版签到失败！'.$arr['msg'];

		$data=$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?actid=52002&rand=0.27489888'.time().'&g_tk='.$this->gtk2.'&format=json',0,'http://vip.qq.com/',$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0)
			$this->msg[] = $this->uin.' 会员手机端签到成功！';
		elseif($arr['ret']==10601)
			$this->msg[] = $this->uin.' 会员手机端今天已经签到！';
		elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[] = $this->uin.' 会员手机端签到失败！SKEY过期';
		}else
			$this->msg[] = $this->uin.' 会员手机端签到失败！'.$arr['msg'];

		$data=$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=54963&isLoadUserInfo=1&format=json&g_tk='.$this->gtk2,0,'http://vip.qq.com/',$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0)
			$this->msg[] = $this->uin.' 会员积分签到成功！';
		elseif($arr['ret']==10601)
			$this->msg[] = $this->uin.' 会员积分今天已经签到！';
		elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[] = $this->uin.' 会员积分签到失败！SKEY过期';
		}else
			$this->msg[] = $this->uin.' 会员积分签到失败！'.$arr['msg'];

		$data=$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=23074&format=json&g_tk='.$this->gtk2,0,'http://vip.qq.com/',$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0)
			$this->msg[] = $this->uin.' 会员积分手机端签到成功！';
		elseif($arr['ret']==10601)
			$this->msg[] = $this->uin.' 会员积分手机端今天已经签到！';
		elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[] = $this->uin.' 会员积分手机端签到失败！SKEY过期';
		}else
			$this->msg[] = $this->uin.' 会员积分手机端签到失败！'.$arr['msg'];

		$data=$this->get_curl('http://pay.qun.qq.com/cgi-bin/group_pay/good_feeds/gain_give_stock?gain=1&bkn='.$this->gtk,0,'http://m.vip.qq.com/act/qun/jindou.html',$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('ec',$arr) && $arr['ec']==0)
			$this->msg[] = $this->uin.' 免费领金豆成功！';
		elseif($arr['ec']==1010)
			$this->msg[] = $this->uin.' 今天已经领取过金豆了！';
		else
			$this->msg[] = $this->uin.' 领金豆失败！'.$arr['em'];

		$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?g_tk='.$this->gtk2.'&actid=27754&_='.time(),0,'http://vip.qq.com/',$this->cookie);//超级会员每月成长值
		$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?g_tk='.$this->gtk2.'&actid=27755&_c=page&_='.time(),0,'http://vip.qq.com/',$this->cookie);//超级会员每月积分
		$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?g_tk='.$this->gtk2.'&actid=22894&_c=page&_='.time(),0,'http://vip.qq.com/',$this->cookie);//每月分享积分
		$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?g_tk='.$this->gtk2.'&actid=22249&_c=page&format=json&_='.time(),0,'http://vip.qq.com/',$this->cookie);//每周薪水积分
		$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?g_tk='.$this->gtk2.'&actid=22887&_c=page&format=json&_='.time(),0,'http://vip.qq.com/',$this->cookie);//每周邀请好友积分
		$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?g_tk='.$this->gtk2.'&actid=202041&_c=page&format=json&_='.time(),0,'http://vip.qq.com/',$this->cookie);//手Q每日签到
		$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?g_tk='.$this->gtk2.'&actid=202049&_c=page&format=json&_='.time(),0,'http://vip.qq.com/',$this->cookie);//手Q每日SVIP签到
	}
	public function lzqd(){
		$this->msg[]='绿钻签到活动已结束！';
		
	}
	public function pqd(){
		$url="http://iyouxi.vip.qq.com/ams3.0.php?g_tk=".$this->gtk2."&pvsrc=102&ozid=511022&vipid=&actid=32961&format=json&t=".time()."8777&cache=3654";
		$data = $this->get_curl($url,0,'http://youxi.vip.qq.com/m/wallet/activeday/index.html?_wv=3&pvsrc=102',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='钱包签到成功！';
		}elseif($arr['ret']==37206){
			$this->msg[]='钱包签到失败！你没有绑定银行卡';
		}elseif($arr['ret']==10601){
			$this->msg[]='你今天已钱包签到！';
		}elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[]='钱包签到失败！SKEY已失效';
		}else{
			$this->msg[]='钱包签到失败！'.$arr['msg'];
		}

		$url="http://proxy.vac.qq.com/cgi-bin/srfentry.fcgi?ts=".time()."9813&g_tk=".$this->gtk."&data={%2210752%22:{%22giveOpt%22:0}}&pt4_token=";
		$data = $this->get_curl($url,0,'https://i.qianbao.qq.com/wallet/recharge/dist/m/index_v4.html?_wv=1031&noTab=1&tab=fee&payChannel=task_activity&source=sng_308803&taskPlugin=1&pvsrc=311&bottom=50',$this->cookie);
		$arr = json_decode($data, true);
		$arr = $arr['10752'];
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='钱包签到2成功！';
		}elseif($arr['ret']==-4){
			$this->msg[]='钱包签到2失败！你没有绑定银行卡';
		}elseif($arr['ret']==-5){
			$this->msg[]='你今天已钱包签到2！';
		}else{
			$this->msg[]='钱包签到2失败！'.$arr['msg'];
		}

		$url="http://proxy.vac.qq.com/cgi-bin/srfentry.fcgi?ts=".time()."9813&g_tk=".$this->gtk."&data={%2210975%22:{%22sIn%22:{%22uin%22:0}}}&pt4_token=";
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		$arr = $arr['10975'];
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='钱包签到3成功！积分+5，连续签到'.$arr['data']['sOut']['continueDays'].'天';
		}elseif($arr['ret']==1){
			$this->msg[]='你今天已钱包签到3！';
		}else{
			$this->msg[]='钱包签到3失败！'.$arr['msg'];
		}

		$url="http://iyouxi3.vip.qq.com/ams3.0.php?g_tk=".$this->gtk2."&pvsrc=102&s_p=1%7Chttp%7C&s_v=0&ozid=511022&vipid=&actid=133339&sid=&format=json&t=".time()."8777&cache=3654";
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='钱包消费领取成长值成功！';
		}elseif($arr['ret']==37206){
			$this->msg[]='钱包消费领取成长值失败！你没有绑定银行卡';
		}elseif($arr['ret']==70051){
			$this->msg[]='您今天还未消费！建议：向自己的QQ小号发送1分钱红包即可！';
		}elseif($arr['ret']==10601){
			$this->msg[]='钱包消费领取成长值已完成！';
		}elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[]='钱包签到失败！SKEY已失效';
		}else{
			$this->msg[]='钱包消费领取成长值失败！'.$arr['msg'];
		}
	}
	public function yqd(){
		$url = 'http://vip.qzone.qq.com/fcg-bin/v2/fcg_mobile_vip_site_checkin?t=0.89457'.time().'&g_tk='.$this->gtk.'&qzonetoken=423659183';
		$post = 'uin='.$this->uin.'&format=json';
		$referer='http://h5.qzone.qq.com/vipinfo/index?plg_nld=1&source=qqmail&plg_auth=1&plg_uin=1&_wv=3&plg_dev=1&plg_nld=1&aid=jh&_bid=368&plg_usr=1&plg_vkey=1&pt_qzone_sig=1';
		$data = $this->get_curl($url,$post,$referer,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='黄钻签到成功！';
		}elseif(array_key_exists('code',$arr) && $arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='黄钻签到失败！SKEY已失效';
		}elseif(array_key_exists('code',$arr)){
			$this->msg[]='黄钻签到失败！'.$arr['message'];
		}else{
			$this->msg[]='黄钻签到失败！'.$data;
		}

		$url = 'http://activity.qzone.qq.com/fcg-bin/fcg_huangzuan_daily_signing?t=0.'.time().'906035&g_tk='.$this->gtk.'&qzonetoken=-1';
		$post = 'option=sign&uin='.$this->uin.'&format=json';
		$data = $this->get_curl($url,$post,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='黄钻公众号签到成功！';
		}elseif(array_key_exists('code',$arr) && $arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='黄钻公众号签到失败！SKEY已失效';
		}elseif($arr['code']==-90002){
			$this->msg[]='黄钻公众号签到失败！非黄钻用户无法签到';
		}elseif(array_key_exists('code',$arr)){
			$this->msg[]='黄钻公众号签到失败！'.$arr['message'];
		}else{
			$this->msg[]='黄钻公众号签到失败！'.$data;
		}
	}
	public function mqqd(){
		$url = 'http://mq.qq.com/index_userSignIn.shtml?r=0.'.time().'2899';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('iRet',$arr) && $arr['iRet']==0){
			$this->msg[]='超级ＱＱ签到成功！';
		}elseif($arr['iRet']==-10){
			$this->skeyzt=1;
			$this->msg[]='超级ＱＱ签到失败！SKEY已失效';
		}elseif($arr['iRet']==-11){
			$this->msg[]='超级ＱＱ已签到！';
		}else{
			$this->msg[]='超级ＱＱ签到失败！'.$data;
		}
		$url = 'http://mq.qq.com/activity/badgepk10_qiandao.shtml?r=0.'.time().'2796';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$data = mb_convert_encoding($data, "UTF-8", "GB2312"); 
		if(strpos($data,'签到成功')!==false){
			$this->msg[]='勋章馆签到成功！';
		}elseif(strpos($data,'感谢参与')!==false){
			$this->msg[]='勋章馆今天已签到！';
		}elseif(strpos($data,'领取失败')!==false){
			$this->msg[]='勋章馆签到失败！';
		}
	}
	public function qunqd($forbid,$poi=null,$template_id='2',$content='签到'){
		$filename = ROOT.'qq/temp/qun_'.md5($this->uin.date("Ymd")).'.txt';
		if(file_exists($filename)){
			$start=file_get_contents($filename);
			if($start=='-1'){
				$this->msg[] = '今日群签到已完成！';
				return true;
			}
		}else{
			$start=0;
		}
		$url = 'http://qun.qzone.qq.com/cgi-bin/get_group_list?groupcount=4&count=4&format=json&callbackFun=_GetGroupPortal&uin='.$this->uin.'&g_tk='.$this->gtk.'&ua=Mozilla%2F5.0%20(Windows%20NT%206.1%3B%20WOW64)%20AppleWebKit%2F537.36%20(KHTML%2C%20like%20Gecko)%20Chrome%2F31.0.1650.63%20Safari%2F537.36';
		$url2 = 'http://qun.qq.com/cgi-bin/qiandao/sign/publish';
		$data = $this->get_curl($url,0,'http://qun.qzone.qq.com/group',$this->cookie);
		preg_match('/_GetGroupPortal_Callback\((.*?)\)\;/is',$data,$json);
		$arr = json_decode($json[1],true);
		//print_r($arr);exit;
		if(@array_key_exists('code',$arr) && $arr['code']==0) {
			if($poi){
				$addstr='&poi='.urlencode($poi).'&lat=1&lgt=1';
			}
			$end=$start+5;
			for($i=$start;$i<$start+5;$i++){
				$row=$arr['data']['group'][$i];
				if(!$row){
					$end=-1;break;
				}
				if(in_array($row['groupid'],$forbid))continue;
				$post = 'bkn='.$this->gtk.'&gc='.$row['groupid'].'&client=1&pic_id=&text='.urlencode($content).'&template_data=&template_id='.$template_id.$addstr;
				$data = $this->get_curl($url2,$post,$url2,$this->cookie);
				$arrs=json_decode($data,true);
				if(array_key_exists('retcode',$arrs) && $arrs['retcode']==0) {
					$this->msg[] = $row['groupname'].' 签到成功！';
				} elseif($arrs['retcode']==10013) {
					$this->msg[] = $row['groupname'].' 禁言已跳过';
				} elseif($arrs['retcode']==10016) {
					$this->msg[] = '群签到一次性只能签到5个群，请10分钟后再试！';
					$end=$i;
					break;
				} elseif($arrs['code']==-3000) {
					$this->skeyzt=1;
					$this->msg[] = '群：'.$row['groupid'].'签到失败！原因：SKEY失效！';
					$end=$i;
					break;
				} else {
					$this->msg[] = '群：'.$row['groupid'].'签到失败！原因：'.$arrs['msg'].$arrs['message'];
					$end=$i;
					break;
				}
			}
			file_put_contents($filename,$end);
		} elseif($arr['code']==-3000) {
			$this->msg[]='群签到失败！原因：SKEY已失效。';
		} else {
			$this->msg[]='群签到失败！原因：'.$arr['message'];
		}
	}
	public function wenwen($superkey,$forbid,$do=0){
		$supertoken=(string)$this->getToken($superkey);
		$url = "http://ptlogin.qq.com/pt4_auth?daid=210&appid=6000201&auth_token=".$this->getToken($supertoken);
		$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login','superuin=o0'.$this->uin.'; superkey='.$superkey.'; supertoken='.$supertoken.';');
		if(preg_match('/ptsigx=(.*?)&/',$data,$match)){
			$url='http://ptlogin4.wenwen.sogou.com/check_sig?uin='.$this->uin.'&ptsigx='.$match[1].'&daid=210&pt_login_type=4&service=pt4_auth&pttype=2&regmaster=&aid=6000201&s_url=http%3A%2F%2Fwenwen.sogou.com%2Fqunapp%2Ffriends%2F%3FgroupUin%3D514575135&low_login_enable=1&low_login_hour=720';
			$data = $this->get_curl($url,0,'http://xui.ptlogin2.qq.com/cgi-bin/login',0,1);
			$cookie='ssuid='.time().'; ';
			preg_match_all('/Set-Cookie: (.*);/iU',$data,$matchs);
			foreach ($matchs[1] as $val) {
				$cookie.=$val.'; ';
			}
			$url='http://wenwen.sogou.com/login/popLogin';
			$data = $this->get_curl($url,0,'http://xui.ptlogin2.qq.com/cgi-bin/xlogin',$cookie,1);
			preg_match_all('/Set-Cookie: (.*);/iU',$data,$matchs);
			$cookie2='';
			foreach ($matchs[1] as $val) {
				$cookie2.=$val.'; ';
			}
			

		$url = 'http://wenwen.sogou.com/submit/ms/signin?groupUin=undefined';
		$post = '{"orig":253,"userId":"'.$this->uin.'"}';
		$data = $this->get_curl($url,$post,'http://wenwen.sogou.com/cate/home',$cookie.$cookie2,0,0,0,1);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='搜索问问签到成功！';
		}elseif($arr['code']==-1){
			$this->msg[]='搜索问问今天已签到！';
		}else{
			$this->msg[]='搜索问问签到失败！'.$arr['message'];
		}
		
		if($do==0)return true;
		$url = 'http://qun.qzone.qq.com/cgi-bin/get_group_list?groupcount=4&count=4&format=json&callbackFun=_GetGroupPortal&uin='.$this->uin.'&g_tk='.$this->gtk.'&ua=Mozilla%2F5.0%20(Windows%20NT%206.1%3B%20WOW64)%20AppleWebKit%2F537.36%20(KHTML%2C%20like%20Gecko)%20Chrome%2F31.0.1650.63%20Safari%2F537.36';
		$data = $this->get_curl($url,0,'http://qun.qzone.qq.com/group',$this->cookie);
		preg_match('/_GetGroupPortal_Callback\((.*?)\)\;/is',$data,$json);
		$arr = json_decode($json[1],true);
		//print_r($arr);exit;
		if(@array_key_exists('code',$arr) && $arr['code']==0) {
			foreach($arr['data']['group'] as $row){
				if(in_array($row['groupid'],$forbid))continue;
				$url='http://wenwen.sogou.com/wapi/qun/red-dot?groupUin='.$row['groupid'].'&lds=0&_='.time().'1573';
				$data = $this->get_curl($url,0,'http://wenwen.sogou.com/mobile/found/?groupUin='.$row['groupid'],$cookie,1,0,1);
				$addcookie='';
				preg_match_all('/Set-Cookie:(.*);/iU',$data,$matchs);
				foreach ($matchs[1] as $val) {
					if(strpos($val,'qun_tl')!==false)continue;
					$addcookie.=trim($val).'; ';
				}
				$url = 'http://wenwen.sogou.com/submit/qun/signin?groupUin='.$row['groupid'].'&_=0.4945279657840729';
				$post = 'groupUin='.$row['groupid'].'&orig=253&tagId=213821&qid=10264304';
				$data = $this->get_curl($url,$post,'http://wenwen.sogou.com/mobile/found/?groupUin='.$row['groupid'],$cookie.$addcookie);
				$arr=json_decode($data,true);
				if(array_key_exists('resultCode',$arr) && $arr['resultCode']==0) {
					$this->msg[] = $row['groupid'].' 群问问签到成功！已连续签到'.$arr['days'].'天';
				} elseif($arr['resultCode']==1) {
					$this->msg[] = $row['groupid'].' 群问问已签到！';
				} elseif($arr['resultCode']==-2) {
					$this->msg[] = '签到时间未到！';
					break;
				} else {
					$this->msg[] = $row['groupid'].' 群问问签到失败！原因：'.$arr['msg'];
				}
			}
		} elseif($arr['code']==-3000) {
			$this->skeyzt=1;
			$this->msg[]='群问问签到失败！原因：SKEY已失效。';
		} else {
			$this->msg[]='群问问签到失败！原因：'.$arr['message'];
		}

		}else{
			$this->msg[]='群问问签到失败！superkey已失效';
		}
	}
	public function buluo($superkey){
		$supertoken=(string)$this->getToken($superkey);
		$url = "http://ptlogin.qq.com/pt4_auth?daid=371&appid=715030901&auth_token=".$this->getToken($supertoken);
		$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login','superuin=o0'.$this->uin.'; superkey='.$superkey.'; supertoken='.$supertoken.';');
		if(preg_match('/ptsigx=(.*?)&/',$data,$match)){
			$url='http://ptlogin4.buluo.qq.com/check_sig?uin='.$this->uin.'&ptsigx='.$match[1].'&daid=371&pt_login_type=4&service=pt4_auth&pttype=2&regmaster=&aid=715030901&s_url=http%3A%2F%2Fbuluo.qq.com%2Fp%2Fbarindex.html';
			$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login',0,1);
			preg_match_all('/Set-Cookie: (.*);/iU',$data,$matchs);
			$cookie='';
			foreach ($matchs[1] as $val) {
				if(substr($val,-1)=='=')continue;
				$cookie.=$val.'; ';
			}
			preg_match("/skey=(.*?);/", $cookie, $match);
			$skey=$match[1];
			$gtk=$this->getGTK($skey);

		$url='http://buluo.qq.com/cgi-bin/bar/login_present_heart';
		$post='bkn='.$gtk;
		$data=$this->get_curl($url,$post,'http://buluo.qq.com/mobile/my_heart.html',$cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('retcode',$arr) && $arr['retcode']==0){
			if($arr['result']['add_hearts']==0)
				$this->msg[]='今日已领取爱心';
			else
				$this->msg[]='成功领取爱心 +'.$arr['result']['add_hearts'];
		}elseif($arr['retcode']==100000){
			$this->skeyzt=1;
			$this->msg[]='领取爱心失败！SKEY已失效。';
		}else{
			$this->msg[]='领取爱心失败！'.$data;
		}

		$url='http://buluo.qq.com/cgi-bin/bar/card/bar_list_by_page?uin='.$this->uin.'&neednum=30&startnum=0&r=0.98389'.time();
		$url2='http://buluo.qq.com/cgi-bin/bar/user/sign';
		$data=$this->get_curl($url,0,'http://buluo.qq.com/mobile/personal.html',$cookie);
		$arr=json_decode($data,true);
		//print_r($arr);exit;
		if(array_key_exists('retcode',$arr) && $arr['retcode']==0){
			$this->msg[]=$this->uin.'获取兴趣部落列表成功！';
			$arr=$arr['result']['followbars'];
			foreach($arr as $row) {
				$post='bid='.$row['bid'].'&bkn='.$gtk.'&r=0.84746'.time();
				$data=$this->get_curl($url2,$post,'http://buluo.qq.com/mobile/personal.html',$cookie);
				$arrs=json_decode($data,true);
				if(array_key_exists('retcode',$arrs) && $arrs['retcode']==0){
					if($arrs['result']['sign']==1)
						$this->msg[]=$row['name'].' 部落已签到！';
					else
						$this->msg[]=$row['name'].' 部落签到成功！';
				}elseif($arrs['retcode']==100000){
					$this->skeyzt=1;
					$this->msg[]=$row['name'].' 部落签到失败！SKEY已失效。';
				}else{
					$this->msg[]=$row['name'].' 部落签到失败！'.$data;
				}
			}
		}elseif($arr['retcode']==100000){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.'兴趣部落签到失败！SKEY已失效。';
		}else{
			$this->msg[]=$this->uin.'兴趣部落签到失败！'.$data;
		}

		}else{
			$this->msg[]='部落签到失败！superkey已失效';
		}
	}
	public function gameqd(){
		$url = 'http://social.minigame.qq.com/cgi-bin/social/welcome_panel_operate?format=json&cmd=2&uin='.$this->uin.'&g_tk='.$this->gtk;
		$data = $this->get_curl($url,0,'http://minigame.qq.com/appdir/social/cloudHall/src/index/welcome.html',$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('result',$arr) && $arr['result']==0) {
			if($arr['do_ret']==11)
				$this->msg[]='游戏大厅今天已签到！';
			else
				$this->msg[]='游戏大厅签到成功！';
		}elseif($arr['result']==1000005){
			$this->skeyzt=1;
			$this->msg[]='游戏大厅签到失败！SKEY已失效。';
		}else{
			$this->msg[]='游戏大厅签到失败！'.$arr['resultstr'];
		}

		$url = 'http://social.minigame.qq.com/cgi-bin/social/welcome_panel_operate?format=json&cmd=1&uin='.$this->uin.'&g_tk='.$this->gtk;
		$data = $this->get_curl($url,0,'http://minigame.qq.com/appdir/social/cloudHall/src/index/welcome.html',$this->cookie);

		$url = 'http://social.minigame.qq.com/cgi-bin/social/CheckInPanel_Operate?Cmd=CheckIn_Operate&g_tk='.$this->gtk;
		$data = $this->get_curl($url,0,'http://minigame.qq.com/appdir/social/cloudHall/src/index/welcome.html',$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('result',$arr) && $arr['result']==0) {
			if($arr['do_ret']==11)
				$this->msg[]='游戏大厅2今天已签到！';
			else
				$this->msg[]='游戏大厅2签到成功！';
		}elseif($arr['result']==1000005){
			$this->skeyzt=1;
			$this->msg[]='游戏大厅2签到失败！SKEY已失效。';
		}else{
			$this->msg[]='游戏大厅2签到失败！'.$arr['resultstr'];
		}

		$url = 'http://info.gamecenter.qq.com/cgi-bin/gc_my_tab_async_fcgi?merge=1&ver=0&st='.time().'746&sid=&uin='.$this->uin.'&number=0&path=489&plat=qq&gamecenter=1&_wv=1031&_proxy=1&gc_version=2&ADTAG=gamecenter&notShowPub=1&param=%7B%220%22%3A%7B%22param%22%3A%7B%22platform%22%3A1%2C%22tt%22%3A1%7D%2C%22module%22%3A%22gc_my_tab%22%2C%22method%22%3A%22sign_in%22%7D%7D&g_tk='.$this->gtk;
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('ecode',$arr) && $arr['ecode']==0) {
			$arr=$arr['data']['0'];
			if(array_key_exists('retCode',$arr) && $arr['retCode']==0) {
				$this->msg[]='手Q游戏中心签到成功！已连续签到'.$arr['retBody']['data']['cur_continue_sign'].'天';
			}else{
				$this->msg[]='手Q游戏中心签到失败！'.$arr['retBody']['message'];
			}
		}elseif($arr['ecode']==-120000){
			$this->skeyzt=1;
			$this->msg[]='手Q游戏中心签到失败！SKEY已失效。';
		}else{
			$this->msg[]='手Q游戏中心签到失败！'.$arr['data']['0']['retBody']['message'];
		}
	}
	public function dldqd(){
		$url = 'http://fight.pet.qq.com/cgi-bin/petpk?cmd=award&op=1&type=0';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$data = mb_convert_encoding($data, "UTF-8", "GB2312");
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]=$arr['ContinueLogin'].' + '.$arr['DailyAward'];
		}elseif($arr['ret']==-1){
			$this->msg[]=$arr['ContinueLogin'];
			$this->msg[]=$arr['DailyAward'];
		}elseif($arr['result']==-5){
			$this->skeyzt=1;
			$this->msg[]='大乐斗领礼包失败！SKEY已失效';
		}else{
			$this->msg[]='大乐斗领礼包失败！'.$arr['msg'];
		}
	}
	public function weiyun(){
		$url = 'http://h5.weiyun.com/sign_in';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		preg_match('!index.get\(\'./signin\'\).init\((.*?)\)\;!is',$data,$match);
		$arr = json_decode($match[1], true);
		if(array_key_exists('signInInfo',$arr)){
			$this->msg[]='微云签到成功！已连续签到'.$arr['signInInfo']['sign_in_count'].'天，积分增加 '.$arr['signInInfo']['add_point'].'积分，当前积分 '.$arr['signInInfo']['total_point'];
		}else{
			$this->msg[]='微云签到失败！';
		}
	}
	public function fzqd(){
		$url='http://x.pet.qq.com/vip_platform?cmd=set_sign_info&format=json&_='.time().'9008';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('result',$arr) && $arr['result']==0){
			$this->msg[]=$this->uin.' 粉钻签到成功！';
		}elseif($arr['result']==-101){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 粉钻签到失败！SKEY已失效';
		}else{
			$this->msg[]=$this->uin.' 粉钻签到失败！'.$arr['msg'];
		}
	}
	public function video(){
		$url='http://pay.video.qq.com/fcgi-bin/sign?low_login=1&uin='.$this->uin.'&otype=json&_t=2&g_tk='.$this->gtk2.'&_='.time().'8906';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		preg_match('/QZOutputJson=(.*?)\;/is',$data,$json);
		$arr = json_decode($json[1], true);
		$arr = $arr['result'];
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]=$this->uin.' 好莱坞会员签到成功！';
		}elseif($arr['code']==-11){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 好莱坞会员签到失败！SKEY已失效';
		}elseif($arr['code']==500){
			$this->msg[]=$this->uin.' 你不是好莱坞会员，无法签到';
		}else{
			$this->msg[]=$this->uin.' 好莱坞会员签到失败！'.$arr['msg'];
		}
	}
	public function videos(){
		$url='http://growth.video.qq.com/fcgi-bin/sync_task?callback=&otype=json&taskid=27&platform=2&_='.time().'8906';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		preg_match('/QZOutputJson=(.*?)\;/is',$data,$json);
		$arr = json_decode($json[1], true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]=$this->uin.' 腾讯视频网页端签到成功！';
		}elseif($arr['ret']==-13004){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 腾讯视频网页端签到失败！SKEY已失效';
		}else{
			$this->msg[]=$this->uin.' 腾讯视频网页端签到失败！'.$arr['errmsg'];
		}

		$url='http://growth.video.qq.com/fcgi-bin/sync_task?callback=&otype=json&taskid=22&platform=1&_='.time().'8906';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		preg_match('/QZOutputJson=(.*?)\;/is',$data,$json);
		$arr = json_decode($json[1], true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]=$this->uin.' 腾讯视频PC端签到成功！';
		}elseif($arr['ret']==-13004){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 腾讯视频PC端签到失败！SKEY已失效';
		}else{
			$this->msg[]=$this->uin.' 腾讯视频PC端签到失败！'.$arr['errmsg'];
		}

		$url='http://growth.video.qq.com/fcgi-bin/sync_task?callback=&flag=1&otype=json&taskid=24&platform=3&_='.time().'8906';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		preg_match('/QZOutputJson=(.*?)\;/is',$data,$json);
		$arr = json_decode($json[1], true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]=$this->uin.' 腾讯视频手机端签到成功！';
		}elseif($arr['ret']==-13004){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 腾讯视频手机端签到失败！SKEY已失效';
		}else{
			$this->msg[]=$this->uin.' 腾讯视频手机端签到失败！'.$arr['errmsg'];
		}
	}
	public function king(){
		$url='http://pf.vip.qq.com/common/vframe1.1.php?&id=5000013&g_tk='.$this->gtk2.'&d='.time().'8799&_ACTID_=20121005';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode(str_replace('VF_RET= ','',$data), true);
		//print_r($arr);exit;
		if(array_key_exists('TASK',$arr) && $arr['TASK']['RET']==0){
			$this->msg[]=$this->uin.' 任意星钻专属礼包抽奖成功！';
		}elseif($arr['TASK']['RET']==10002){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 年费会员抽奖失败！SKEY已失效';
		}elseif($arr['TASK']['RET']==-10001){
			$this->msg[]=$this->uin.' 你不是钻皇，无法抽奖！';
		}else{
			$this->msg[]=$this->uin.' 任意星钻专属礼包抽奖失败！'.$arr['msg'];
		}
	}
	public function qqmgr(){
		$url='http://p.guanjia.qq.com/bin/user/qrycheckin.php?op=checkin&emotionId=75&Uin='.$this->uin.'&skey='.$this->skey.'&gjtk='.$this->gtk.'&_='.time().'051';
		$data = $this->get_curl($url,0,'http://s.pcmgr.qq.com/user_v2/inc/sign.html',$this->cookie);
		preg_match('/jsonpCallback\((.*?)\)/is',$data,$json);
		$arr = json_decode($json[1], true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='腾讯电脑管家签到成功！';
		}elseif($arr['code']==8){
			$this->skeyzt=1;
			$this->msg[]='腾讯电脑管家签到失败！SKEY已失效';
		}elseif(array_key_exists('code',$arr)){
			$this->msg[]='腾讯电脑管家签到失败！'.$arr['msg'];
		}else{
			$this->msg[]='腾讯电脑管家签到失败！'.$data;
		}
	}
	public function qd3366(){
		$url = 'http://fcg.3366.com/fcg-bin/growinfo/mgp_growinfo_signin?&_r='.time().'7197&sCSRFToken='.$this->gtk;
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$data = mb_convert_encoding($data, "UTF-8", "GB2312");
		preg_match('/gGrowInfoSignInResult = (.*?);/is',$data,$json);
		$arr = json_decode($json[1], true);
		if(array_key_exists('result',$arr) && $arr['result']==0){
			$this->msg[]='3366签到成功！';
		}elseif($arr['result']==16001){
			$this->msg[]='3366今天已签到！';
		}elseif($arr['result']==10002){
			$this->skeyzt=1;
			$this->msg[]='3366签到失败！SKEY已失效';
		}elseif(array_key_exists('result',$arr)){
			$this->msg[]='3366签到失败！'.$arr['resultstr'];
		}else{
			$this->msg[]='3366签到失败！'.$data;
		}
	}
	public function dnfjf(){
		$url = 'http://apps.game.qq.com/cms/index.php?serviceType=dnf&actId=2&sAction=duv&sModel=Data&retType=json';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('iRet',$arr) && $arr['iRet']==0){
			if($arr['jData']['iLotteryRet']==100002){
				$this->msg[]='DNF社区积分已领取！';
			}else{
				$this->msg[]='DNF社区积分领取成功！';
			}
		}else{
			$this->msg[]='DNF社区积分领取失败！'.$arr['sMsg'];
		}
	}
	public function liuliang(){
		$url = 'http://iyouxi.vip.qq.com/ams3.0.php?g_tk='.$this->gtk2.'&pvsrc=101&ozid=511022&vipid=&actid=68391&format=json&cache=3654';
		$data = $this->get_curl($url,0,'http://youxi.vip.qq.com/m/wallet/activeday/index.html?_wv=3&pvsrc=101',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='领取成功！流量豆 +1';
		}elseif($arr['ret']==10601){
			$this->msg[]='今天已经领取过流量豆了！';
		}elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[]='领取流量豆失败！SKEY已失效';
		}else{
			$this->msg[]='领取流量豆失败！'.$arr['msg'];
		}

		$url = 'http://proxy.vac.qq.com/cgi-bin/srfentry.fcgi?ts='.time().'7087&g_tk='.$this->gtk.'&data={%2210140%22:{%22req%22:{%22platId%22:1,%22taskId%22:10,%22taskStatus%22:10}}}';
		$data = $this->get_curl($url,0,'http://vac.qq.com/wifi/v2/integral.html?_wv=1',$this->cookie);
		$arr = json_decode($data, true);
		$arr = $arr['10140'];
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='完成签到送积分！流量豆 +5';
		}elseif($arr['ret']==10001){
			$this->msg[]='今天已经领取过积分了！';
		}elseif($arr['ret']==-500000){
			$this->skeyzt=1;
			$this->msg[]='领取积分失败！SKEY已失效';
		}else{
			$this->msg[]='领取积分失败！'.$arr['msg'];
		}
	}
	public function qqpet(){
		$url = 'http://x.pet.qq.com/petgrow?cmd=Random&format=json&_='.time().'6346';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		$this->msg[]=$arr['msg'];

		$url = 'http://x.pet.qq.com/toolbarsign?cmd=Sign&format=json&_='.time().'9718';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		$this->msg[]=$arr['msg'];

		$url = 'http://x.pet.qq.com/toolbarsign?cmd=Gift&format=json&_='.time().'9718';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		$this->msg[]=$arr['msg'];

	}

	public function sweet_send($id=1){
		$luin = $this->sweet_sign();
		if(!$luin)return false;

		$url = 'http://sweet.snsapp.qq.com/v2/cgi-bin/sweet_chat_sendmsg?g_tk='.$this->gtk;
		$post = 'luin='.$luin.'&opuin='.$this->uin.'&type=8&rid=&content=&richval=%5B%7B%22id%22%3A'.$id.'%2C%22subid%22%3A1%2C%22type%22%3A%22interect%22%7D%5D&src=1&uin='.$this->uin.'&plat=0&outputformat=4';
		$json=$this->get_curl($url,$post,$url,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='发送密语成功！今日已获得积分:'.$arr['expDetail']['totalExp'].'，总共积分:'.($arr['exp']/10);
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='发送密语失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='发送密语失败！'.$arr['message'];
		}
	}
	public function sweet_tree(){
		$luin = $this->sweet_sign();
		if(!$luin)return false;

		$url = 'http://sweet.snsapp.qq.com/v2/cgi-bin/sweet_tree_index?g_tk='.$this->gtk.'&luin='.$luin.'&outputformat=4&opuin='.$this->uin.'&src=1&uin='.$this->uin;
		$json=$this->get_curl($url,0,'http://sweet.snsapp.qq.com/',$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='登录情侣树成功！当前经验值:'.$arr['exp'];

		$url = 'http://sweet.snsapp.qq.com/v2/cgi-bin/sweet_tree_operate?g_tk='.$this->gtk;
		$post = 'opuin='.$this->uin.'&luin='.$luin.'&outputformat=4&uin='.$this->uin.'&src=1&cmd=1';
		$json=$this->get_curl($url,$post,$url,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='情侣树浇水成功！获得经验值:'.$arr['exp'].'，总经验值:'.$arr['exp_total'];
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='情侣树浇水失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='情侣树浇水失败！'.$arr['message'];
		}
		$post = 'opuin='.$this->uin.'&luin='.$luin.'&outputformat=4&uin='.$this->uin.'&src=1&cmd=2';
		$json=$this->get_curl($url,$post,$url,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='情侣树施肥成功！获得经验值:'.$arr['exp'].'，总经验值:'.$arr['exp_total'];
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='情侣树施肥失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='情侣树施肥失败！'.$arr['message'];
		}
		$post = 'opuin='.$this->uin.'&luin='.$luin.'&outputformat=4&uin='.$this->uin.'&src=1&cmd=3';
		$json=$this->get_curl($url,$post,$url,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='情侣树阳光成功！获得经验值:'.$arr['exp'].'，总经验值:'.$arr['exp_total'];
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='情侣树阳光失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='情侣树阳光失败！'.$arr['message'];
		}

		$url = 'http://sweet.snsapp.qq.com/v2/cgi-bin/sweet_tree_gain?g_tk='.$this->gtk;
		$post = 'opuin='.$this->uin.'&luin='.$luin.'&trunk_id=1&outputformat=4&uin='.$this->uin.'&src=1';
		$json=$this->get_curl($url,$post,$url,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='情侣树收获果实成功！获得经验值:'.$arr['exp'].'，总经验值:'.$arr['exp_total'];
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='情侣树收获果实失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='情侣树收获果实失败！'.$arr['message'];
		}

		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='登录情侣树失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='登录情侣树失败！'.$arr['message'];
		}
		
	}
	public function sweet_sign(){
		$url = 'http://sweet.snsapp.qq.com/v2/cgi-bin/sweet_signlove_get?cmd=0&startts=1453564800&endts=1457280000&opuin='.$this->uin.'&uin='.$this->uin.'&plat=0&outputformat=4&g_tk='.$this->gtk;
		$data = $this->get_curl($url,0,'http://sweet.snsapp.qq.com/',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='情侣空间签到成功！';
			return $arr['data']['lover']['uin'];
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='情侣空间签到失败！原因:SKEY已过期！';
			return false;
		}else{
			$this->msg[]='情侣空间签到失败！'.$arr['message'];
			return false;
		}
	}
	public function bookqd(){
		$url = "http://ubook.3g.qq.com/8/user/myMission?k1={$this->skey}&u1=o0{$this->uin}";
        $data = $this->get_curl($url, 0, 'http://ubook.qq.com/8/mymission.html');
        $arr = json_decode($data, true);
        if ($arr['isLogin'] == 'true' && $arr['signMap']['code'] == 0) {
            $this->msg[] = '图书签到成功！';
        } elseif ($arr['signMap']['code'] == -2) {
            $this->msg[] = '图书今日已经签到！';
        } elseif ($arr['isLogin'] == 'false') {
			$this->skeyzt=1;
            $this->msg[] = '图书签到失败！SKEY过期！';
        } else {
            $this->msg[] = '图书签到失败！数据异常';
        }

		$url = "http://novelsns.html5.qq.com/ajax?m=task&type=sign&aid=20&t=".time()."586";
		$data = $this->get_curl($url,0,'https://bookshelf.html5.qq.com/discovery.html','Q-H5-ACCOUNT='.$this->uin.'; Q-H5-SKEY='.$this->skey.'; luin='.$this->uin.'; Q-H5-USERTYPE=1; Q-H5-GUID=8d9906176047b05655b3cdd050808994;');
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='小说书架签到成功！已连续签到'.$arr['continuousDays'].'天,获得书豆'.$arr['beans'];
		}elseif($arr['ret']==-2){
			$this->msg[]='小说书架今天已签到！已连续签到'.$arr['continuousDays'].'天,获得书豆'.$arr['beans'];
		}else{
			$this->msg[]='小说书架签到失败！'.$arr['msg'];
		}

		$url = "http://novelsns.html5.qq.com/ajax?m=shareSignPageObtainBeans&aid=20&t=".time()."586";
		$data = $this->get_curl($url,0,'https://bookshelf.html5.qq.com/discovery.html','Q-H5-ACCOUNT='.$this->uin.'; Q-H5-SKEY='.$this->skey.'; luin='.$this->uin.'; Q-H5-USERTYPE=1; Q-H5-GUID=8d9906176047b05655b3cdd050808994;');
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='小说书架分享成功！获得书豆'.$arr['beans'];
		}else{
			$this->msg[]='小说书架分享失败！'.$arr['msg'];
		}

		$url = 'http://reader.sh.vip.qq.com/cgi-bin/reader_page_csrf_cgi?merge=2&ditch=100020&cfrom=account&current=sign_index&tf=2&sid='.$this->uin.'&client=1&version=qqreader_1.0.669.0001_android_qqplugin&channel=00000&_bid=2036&ChannelID=100020&plat=1&qqVersion=0&_from=sign_index&_='.time().'017&g_tk='.$this->gtk.'&p_tk=&sequence='.time().'755';
		$post = 'param=%7B%220%22%3A%7B%22param%22%3A%7B%22tt%22%3A0%7D%2C%22module%22%3A%22reader_sign_manage_svr%22%2C%22method%22%3A%22UserTodaySign%22%7D%2C%221%22%3A%7B%22param%22%3A%7B%22tt%22%3A0%7D%2C%22module%22%3A%22reader_sign_manage_svr%22%2C%22method%22%3A%22GetSignGifts%22%7D%7D';
		$data = $this->get_curl($url,$post,$url,$this->cookie);
		$arr = json_decode($data, true);
		if($arr = $arr['data']['0']['retBody']){
			if(array_key_exists('result',$arr) && $arr['result']==0){
				$this->msg[]='手机QQ阅读签到成功！获得书券'.$arr['data']['awards'][0]['awardNum'].',已连续签到'.$arr['data']['lastDays'].'天';
			}else{
				$this->msg[]='手机QQ阅读签到失败！'.$arr['message'];
			}
		}else{
			$this->msg[]='手机QQ阅读签到失败！'.$data;
		}

		$url = 'http://reader.sh.vip.qq.com/cgi-bin/reader_page_csrf_cgi?merge=1&ditch=100020&cfrom=account&current=sign_index&tf=2&sid='.$this->uin.'&client=1&version=qqreader_1.0.669.0001_android_qqplugin&channel=00000&_bid=2036&ChannelID=100020&plat=1&qqVersion=0&_from=sign_index&_='.time().'017&g_tk='.$this->gtk.'&p_tk=&sequence='.time().'755';
		$post = 'param=%7B%220%22%3A%7B%22param%22%3A%7B%22tt%22%3A0%7D%2C%22module%22%3A%22reader_sign_manage_svr%22%2C%22method%22%3A%22GrantBigGift%22%7D%7D';
		$data = $this->get_curl($url,$post,$url,$this->cookie);
		$arr = json_decode($data, true);
		if($arr = $arr['data']['0']['retBody']){
			if(array_key_exists('result',$arr) && $arr['result']==0){
				$this->msg[]='手机QQ阅读抽奖成功！获得奖品'.$arr['data']['newlyGift']['giftName'];
			}elseif($arr['result']==1004){
				$this->msg[]='手机QQ阅读抽奖：需要连续签到5天才可以抽奖';
			}else{
				$this->msg[]='手机QQ阅读抽奖失败！'.$arr['message'];
			}
		}else{
			$this->msg[]='手机QQ阅读抽奖失败！'.$data;
		}
	}
	public function daoju(){
		$url = "http://apps.game.qq.com/ams/ame/ame.php?ameVersion=0.3&sServiceType=dj&iActivityId=11117&sServiceDepartment=djc&set_info=djc";
		$post = "iActivityId=11117&iFlowId=96939&g_tk=".$this->gtk."&e_code=0&g_code=0&sServiceDepartment=djc&sServiceType=dj";
		$data = $this->get_curl($url,$post,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr['modRet']) && $arr['modRet']['ret']==0){
			$this->msg[]='道聚城签到成功！';
		}elseif($arr['modRet']['ret']==600){
			$this->msg[]='道聚城今天已签到！';
		}else{
			$this->msg[]='道聚城签到失败！'.$arr['modRet']['msg'];
		}

		$post = "gameId=&sArea=&iSex=&sRoleId=&iGender=&sServiceType=dj&objCustomMsg=&areaname=&roleid=&rolelevel=&rolename=&areaid=&iActivityId=11117&iFlowId=96910&g_tk=".$this->gtk."&e_code=0&g_code=0&sServiceDepartment=djc";
		$data = $this->get_curl($url,$post,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('iRet',$arr['modRet']) && $arr['modRet']['iRet']==0){
			$this->msg[]=$arr['modRet']['sMsg'];
		}elseif($arr['ret']==600){
			$this->msg[]=$arr['msg'];
		}else{
			$this->msg[]=$arr['msg'];
		}
	}
	public function xinyue(){
		$url = "http://apps.game.qq.com/ams/ame/ame.php?ameVersion=0.3&sServiceType=tgclub&iActivityId=21547&sServiceDepartment=xinyue&set_info=xinyue";
		$post = "iActivityId=21547&iFlowId=149694&g_tk=".$this->gtk."&e_code=0&g_code=0&sServiceDepartment=xinyue&sServiceType=tgclub";
		$data = $this->get_curl($url,$post,0,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='心锐VIP签到成功！';
		}elseif($arr['ret']==600){
			$this->msg[]='心锐VIP今天已签到！';
		}else{
			$this->msg[]='心锐VIP签到失败！'.$arr['msg'];
		}
	}
	public function jpgame(){
		$url = "http://1.game.qq.com/app/sign?start=".date("Y-m")."&g_tk=".$this->gtk."&_t=0.6780016267291531";
		$data = $this->get_curl($url,0,$url,$this->cookie);
		preg_match('/var sign_index = (.*?);/is', $data, $json);
		$arr = json_decode($json[1], true);
		$arr = $arr['jData']['signInfo'];
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='精品页游签到成功！';
		}elseif($arr['ret']==1){
			$this->msg[]='精品页游今天已签到！';
		}else{
			$this->msg[]='精品页游签到失败！'.$arr['msg'];
		}
	}
	public function addbuluo($superkey,$bid){
		$supertoken=(string)$this->getToken($superkey);
		$url = "http://ptlogin.qq.com/pt4_auth?daid=371&appid=715030901&auth_token=".$this->getToken($supertoken);
		$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login','superuin=o0'.$this->uin.'; superkey='.$superkey.'; supertoken='.$supertoken.';');
		if(preg_match('/ptsigx=(.*?)&/',$data,$match)){
			$url='http://ptlogin4.buluo.qq.com/check_sig?uin='.$this->uin.'&ptsigx='.$match[1].'&daid=371&pt_login_type=4&service=pt4_auth&pttype=2&regmaster=&aid=715030901&s_url=http%3A%2F%2Fbuluo.qq.com%2Fp%2Fbarindex.html';
			$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login',0,1);
			preg_match_all('/Set-Cookie: (.*);/iU',$data,$matchs);
			$cookie='';
			foreach ($matchs[1] as $val) {
				if(substr($val,-1)=='=')continue;
				$cookie.=$val.'; ';
			}
			$url = 'http://buluo.qq.com/cgi-bin/bar/user/fbar';
			$post = 'bid='.$bid.'&op=1&bkn='.$this->gtk.'&r=0.395212'.time();
			$data = $this->get_curl($url,$post,'http://buluo.qq.com/mobile/index.html?_lv='.$bid.'&_wv=257289&_bid=128',$cookie);
			$arr = json_decode($data, true);
			if(array_key_exists('retcode',$arr) && $arr['retcode']==0){
				$this->msg[]='部落关注成功！';
			}elseif($arr['result']==100006){
				$this->msg[]='部落关注失败！p_skey已失效';
			}else{
				$this->msg[]='部落关注失败！'.$data;
			}
		}else{
			$this->msg[]='部落关注失败！superkey已失效';
		}
	}
	public function qqqun($superkey){
		$supertoken=(string)$this->getToken($superkey);
		$url = "http://ptlogin.qq.com/pt4_auth?daid=73&appid=715030901&auth_token=".$this->getToken($supertoken);
		$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login','superuin=o0'.$this->uin.'; superkey='.$superkey.'; supertoken='.$supertoken.';');
		if(preg_match('/ptsigx=(.*?)&/',$data,$match)){
			$url='http://ptlogin2.qun.qq.com/check_sig?pttype=2&uin='.$this->uin.'&service=jump&nodirect=0&ptsigx='.$match[1].'&s_url=http%3A%2F%2Fqun.qq.com%2Fmember.html&f_url=&ptlang=2052&ptredirect=100&aid=1000101&daid=73&j_later=0&low_login_hour=0&regmaster=0&pt_login_type=2&pt_aid=715030901&pt_aaid=0&pt_light=0&pt_3rd_aid=0';
			$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login',0,1);
			preg_match_all('/Set-Cookie: (.*);/iU',$data,$matchs);
			$cookie='';
			foreach ($matchs[1] as $val) {
				if(substr($val,-1)=='=')continue;
				$cookie.=$val.'; ';
			}
			return $cookie;
		}else{
			$this->msg[]='获取群成员列表失败！superkey已失效';
			return false;
		}
	}
	public function qqweibo($superkey){
		$supertoken=(string)$this->getToken($superkey);
		$url = "http://ptlogin.qq.com/pt4_auth?daid=6&appid=46000101&auth_token=".$this->getToken($supertoken);
		$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login','superuin=o0'.$this->uin.'; superkey='.$superkey.'; supertoken='.$supertoken.';');
		if(preg_match('/ptsigx=(.*?)&/',$data,$match)){
			$url='http://ptlogin4.t.qq.com/check_sig?uin='.$this->uin.'&ptsigx='.$match[1].'&daid=6&pt_login_type=4&service=pt4_auth&pttype=2&regmaster=0&regmaster=&aid=46000101&s_url=http%3A%2F%2Ft.qq.com&low_login_enable=1&low_login_hour=720&has_onekey=1';
			$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login',0,1);
			preg_match_all('/Set-Cookie: (.*);/iU',$data,$matchs);
			$cookie='';
			foreach ($matchs[1] as $val) {
				if(substr($val,-1)=='=')continue;
				$cookie.=$val.'; ';
			}
			return $cookie;
		}else{
			$this->msg[]='登录腾讯微博失败！superkey已失效';
			return false;
		}
	}
	public function gamevip($superkey){
		$supertoken=(string)$this->getToken($superkey);
		$url = "http://ptlogin.qq.com/pt4_auth?daid=176&appid=21000110&auth_token=".$this->getToken($supertoken);
		$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login','superuin=o0'.$this->uin.'; superkey='.$superkey.'; supertoken='.$supertoken.';');
		if(preg_match('/ptsigx=(.*?)&/',$data,$match)){
			$url='http://ptlogin4.gamevip.qq.com/check_sig?uin='.$this->uin.'&ptsigx='.$match[1].'&daid=176&pt_login_type=4&service=pt4_auth&pttype=2&regmaster=&aid=21000110&s_url=http%3A%2F%2Fgamevip.qq.com%2F';
			$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login',0,1);
			preg_match("/skey=(.*?);/", $data, $match);
			$skey=$match[1];
			preg_match("/p_skey=(.*?);/", $data, $match);
			$pskey=$match[1];
			$cookie='pt2gguin=o0'.$this->uin.'; uin=o0'.$this->uin.'; skey='.$skey.'; p_uin=o0'.$this->uin.'; p_skey='.$pskey.'; DomainID=176;';

			$url='http://app.gamevip.qq.com/cgi-bin/gamevip_sign/GameVip_SignIn?format=json&g_tk='.$this->gtk.'&_='.time().'0334';
			$data = $this->get_curl($url,0,'http://gamevip.qq.com/sign_pop/sign_pop_v2.html',$cookie);
			$arr = json_decode($data, true);
			if(array_key_exists('result',$arr) && $arr['result']==0){
				$this->msg[]='蓝钻签到成功！当前签到积分'.$arr['SignScore'].'点';
			}elseif($arr['result']==1000005){
				$this->msg[]='蓝钻签到失败！P_skey已失效';
			}else{
				$this->msg[]='蓝钻签到失败！'.$arr['resultstr'];
			}

			$url='http://app.gamevip.qq.com/cgi-bin/gamevip_sign/GameVip_Lottery?format=json&g_tk='.$this->gtk.'&_='.time().'0334';
			$data = $this->get_curl($url,0,$url,$cookie);
			$data = mb_convert_encoding($data, "UTF-8", "GB2312");
			$arr = json_decode($data, true);
			if(array_key_exists('result',$arr) && $arr['result']==0){
				$this->msg[]='蓝钻抽奖成功！';
			}elseif($arr['result']==1000005){
				$this->msg[]='蓝钻抽奖失败！P_skey已失效';
			}elseif($arr['result']==102){
				$this->msg[]='蓝钻抽奖次数已用完';
			}else{
				$this->msg[]='蓝钻抽奖失败！'.$arr['resultstr'];
			}

			$url='http://app.gamevip.qq.com/cgi-bin/gamevip_m_sign/GameVip_m_SignIn';
			$data = $this->get_curl($url,0,$url,$cookie);
			$data = mb_convert_encoding($data, "UTF-8", "GB2312");
			$arr = json_decode($data, true);
			if(array_key_exists('result',$arr) && $arr['result']==0){
				$this->msg[]='蓝钻手机签到成功！奖励魔法卡片'.$arr['MagicCard'].'张，星星'.$arr['McStar'].'颗';
			}elseif($arr['result']==1000005){
				$this->msg[]='蓝钻手机签到失败！P_skey已失效';
			}else{
				$this->msg[]='蓝钻手机签到失败！'.$arr['resultstr'];
			}
		}else{
			$this->msg[]='蓝钻签到失败！superkey已失效';
		}

		$url = "http://apps.game.qq.com/ams/ame/ame.php?ameVersion=0.3&sServiceType=qqgame&iActivityId=54614&sServiceDepartment=newterminals&set_info=newterminals";
		$post = "iActivityId=54614&iFlowId=279055&g_tk=".$this->gtk."&e_code=0&g_code=0&eas_url=http%253A%252F%252Flz.qq.com%252Fact%252Fa20160712sign%252F&eas_refer=&sServiceDepartment=group_h&sServiceType=qqgame";
		$data = $this->get_curl($url,$post,0,$this->cookie);
		$arr = json_decode($data, true);
		$arr = $arr['modRet'];
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='蓝钻微信公众号签到成功！';
		}elseif($arr['ret']==600){
			$this->msg[]='蓝钻微信公众号今天已签到！';
		}else{
			$this->msg[]='蓝钻微信公众号签到失败！'.$arr['msg'];
		}
	}
	public function zhongzhuan($superkey){
		$supertoken=(string)$this->getToken($superkey);
		$url = "http://ptlogin.qq.com/pt4_auth?daid=4&appid=522005705&auth_token=".$this->getToken($supertoken);
		$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login','superuin=o0'.$this->uin.'; superkey='.$superkey.'; supertoken='.$supertoken.';');
		if(preg_match('/ptsigx=(.*?)&/',$data,$match)){
			$url='http://ptlogin4.mail.qq.com/check_sig?uin='.$this->uin.'&ptsigx='.$match[1].'&daid=4&pt_login_type=4&service=pt4_auth&pttype=2&regmaster=&aid=522005705&s_url=http%3A%2F%2Fmail.qq.com%2Fcgi-bin%2Flogin%3Fvt%3Dpassport%26vm%3Dwpt%26ft%3Dloginpage%26target%3D';
			$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login',0,1);
			preg_match_all('/Set-Cookie: (.*);/iU',$data,$matchs);
			$cookie='';
			foreach ($matchs[1] as $val) {
				if(substr($val,-1)=='=')continue;
				$cookie.=$val.'; ';
			}
			preg_match("/Location: (.*?)\r\n/", $data, $match);
			$data = $this->get_curl($match[1],0,'http://ui.ptlogin2.qq.com/cgi-bin/login',$cookie,1);
			preg_match_all('/Set-Cookie: (.*);/iU',$data,$matchs);
			$cookie='';
			foreach ($matchs[1] as $val) {
				if(substr($val,-1)=='=')continue;
				$cookie.=$val.'; ';
			}
			preg_match("/frame_html\?sid=(.*?)&/", $data, $match);
			$sid=$match[1];

			$url = 'http://set2.mail.qq.com/cgi-bin/ftnExs_files?sid='.$sid.'&t=ftn.json&s=list&ef=js&listtype=self&up=down&sorttype=createtime&page=0&pagesize=50&pagemode=more&pagecount=2&ftnpreload=true&sid='.$sid;
			$data = $this->get_curl($url,0,'http://set2.mail.qq.com/',$cookie);
			$data = mb_convert_encoding($data, "UTF-8", "GB2312");
			if(preg_match_all('/{sFileId : \"(.*)\"/iU',$data,$matchs)){
				$post='';$i=0;
				foreach ($matchs[1] as $val) {
					$post.='&fid='.$val;$i++;
				}
				$url = 'http://set2.mail.qq.com/cgi-bin/ftnExtendfile?sid='.$sid.'&t=ftn.json&s=oper&ef=js&keytext=';
				$data = $this->get_curl($url,$post,'http://set2.mail.qq.com/',$cookie);
				if(strpos($data,'errcode : "0"')){
					$this->msg[]='QQ邮箱中转站一键续期成功！共续期了'.$i.'个文件';
				}else{
					$this->msg[]='QQ邮箱中转站一键续期失败！'.$data;
				}
			}else{
				$this->msg[]='QQ邮箱中转站没有要续期的文件！';
			}
		}else{
			$this->msg[]='QQ邮箱中转站一键续期失败！superkey已失效';
		}
	}
	public function mqq(){
		$url = 'http://cgi.vip.qq.com/online/set?p_tk=&g_tk_type=1&g_tk='.$this->gtk.'&sid=&beg=0&end=24&type=Y&format=json';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]=$this->uin.' 手机QQ在线6小时一键加速成功！';
		}elseif($arr['ret']==-7){
			$this->skeyzt=1;
			$this->msg[]='手机QQ等级加速失败！原因：SKEY已失效';
		}else{
			$this->msg[]='手机QQ等级加速失败！原因：'.$arr['msg'];
			$this->msg[]='提示：必须是QQ会员，且<a href="http://bd.qq.com/" target="_blank">绑定手机</a>才能使用此功能。';
		}
	}
	public function qqllq(){
		$url = 'http://i.browser.qq.com/all_data_query?guid=F27FE57A437658E7D11BC0D85ECBCC70&g_tk='.$this->gtk2;
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if($task_list=$arr['static_data']['task_list']){
			foreach($task_list as $row){
				if($row['interface']=='50619'||$row['interface']=='54000'||$row['interface']=='53998')continue;
				$this->qqllq_task($row['interface'],$row['title'],$row['score']);
			}
		}elseif($arr['ret']==-2){
			$this->skeyzt=1;
			$this->msg[]='获取活动列表失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='获取活动列表失败！'.$arr['msg'];
		}
	}
	private function qqllq_task($actid,$title,$score){
		$url = 'http://iyouxi.vip.qq.com/ams3.0.php?g_tk='.$this->gtk2.'&actid='.$actid.'&guid=F27FE57A437658E7D11BC0D85ECBCC70&fromat=json&_='.time().'7071';
		$data = $this->get_curl($url,0,'http://i.browser.qq.com/',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='QQ浏览器 '.$title.' 成功！积分+'.$score;
		}elseif($arr['ret']==10601){
			$this->msg[]='QQ浏览器 '.$title.' 今天已完成！';
		}elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[]='QQ浏览器 '.$title.' 失败！SKEY过期';
		}else{
			$this->msg[]='QQ浏览器 '.$title.' 失败！'.$arr['msg'];
		}
	}
	public function xing(){
		$url = 'http://starvip.qq.com/fcg-bin/v2/fcg_mobile_starvip_site_checkin?g_tk='.$this->gtk.'&r=0.06027948'.time();
		$post='format=json&uin='.$this->uin;
		$data = $this->get_curl($url,$post,'http://xing.qq.com/',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='星钻签到成功！成长值+'.$arr['data']['add'];
		}elseif($arr['code']==-10000){
			$this->msg[]='每天只需要签到一次哦！';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='星钻签到失败！SKEY过期';
		}else{
			$this->msg[]='星钻签到失败！'.$arr['message'];
		}

		$url = 'http://starvip.qq.com/fcg-bin/v2/fcg_qzact_lottery?g_tk='.$this->gtk.'&r=0.00463036'.time();
		$post='actid=369&ruleid=2048&format=json&uin='.$this->uin;
		$data = $this->get_curl($url,$post,'http://xing.qq.com/',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='星钻抽奖成功！抽奖结果：'.$arr['data'][0]['name'].$arr['data'][0]['cdkey'];
		}elseif($arr['code']==-10000){
			$this->msg[]='您已经用完了所有的抽奖机会！';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='星钻抽奖失败！SKEY过期';
		}else{
			$this->msg[]='星钻抽奖失败！'.$arr['message'];
		}
	}
	public function dongman(){
		$url = 'http://comic.vip.qq.com/cgi-bin/coupon_coin?merge=1&pageVersion=288192_online&platId=109&version=1&_='.time().'516&g_tk='.$this->gtk.'&p_tk=&sequence='.time().'431';
		$post = 'param=%7B%220%22%3A%7B%22param%22%3A%7B%22tt%22%3A0%7D%2C%22module%22%3A%22comic_sign_in_svr%22%2C%22method%22%3A%22SignIn%22%2C%22timestamp%22%3A'.time().'424%7D%7D';
		$data = $this->get_curl($url,$post,$url,$this->cookie);
		$arr = json_decode($data, true);
		if($arr = $arr['data']['0']['retBody']){
			if(array_key_exists('result',$arr) && $arr['result']==0){
				$this->msg[]='手机QQ动漫签到成功！萌点+2，本月已累计签到'.$arr['data']['singedDayOfMonth'].'天';
			}elseif($arr['result']==-120000){
				$this->skeyzt=1;
				$this->msg[]='手机QQ动漫签到失败！SKEY已失效';
			}else{
				$this->msg[]='手机QQ动漫签到失败！'.$arr['message'];
			}
		}else{
			$this->msg[]='手机QQ动漫签到失败！'.$data;
		}

		$url = 'http://iyouxi.vip.qq.com/ams3.0.php?g_tk='.$this->gtk2.'&actid=105321&merge=1&plat=1&qqVersion=6.6.9.3060&_='.time().'749';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='手机QQ动漫领取奖励成功！';
		}elseif($arr['ret']==20226){
			$this->msg[]='累计签到天数不足，无法领取额外奖励';
		}elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[]='手机QQ动漫领取奖励失败！SKEY过期';
		}else{
			$this->msg[]='手机QQ动漫领取奖励失败！'.$arr['msg'];
		}
	}
	public function webface(){
		$url='http://face.qq.com/client/webface.php';
		$post='cmd=get_used_items&g_tk='.$this->gtk2.'&callback=callback';
		$data=$this->get_curl($url,$post,$url,$this->cookie);
		preg_match('/callback\((.*?)\);/is', $data, $json);
		$arr=json_decode($json[1],true);
		if(@array_key_exists('result',$arr) && $arr['result']==0){
			$faces=array();
			foreach($arr['data'] as $row){
				if($row['hash'])$faces[]=$row['hash'];
			}
			$hash=$faces[array_rand($faces,1)];
			$url='http://face.qq.com/client/webface_share.php';
			$post='hash='.$hash.'&type=0&cmd=set_used_face&g_tk='.$this->gtk2.'&callback=callback';
			$data=$this->get_curl($url,$post,$url,$this->cookie);
			$this->msg[]='更换头像成功！更换头像的范围在你所使用过的头像中，修改头像种类请<a href="http://ptlogin2.qq.com/jump?uin='.$this->uin.'&skey='.$this->skey.'&u1=http%3A%2F%2Fstyle.qq.com%2Fface%2Fmanage.html" target="_blank" rel="noreferrer">点此进入</a>';
		}elseif($arr['result']==1001){
			$this->skeyzt=1;
			$this->msg[]='更换头像失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='更换头像失败！'.$data;
		}
	}
	public function qcloud(){
		$url = 'https://cloud.tencent.com/act/campus/ajax/index?uin='.$this->uin.'&csrfCode='.$this->gtk;
		$post = 'action=getLongPackageVoucher&actId=1948';
		$data = $this->get_curl($url,$post,'https://cloud.tencent.com/act/campus',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']=='0'){
			$this->msg[]='领取腾讯云学生机代金卷成功！';
		}elseif($arr['code']==11035){
			$this->msg[]='本月您已领取优惠代金券，请下个月再领取。';
		}elseif($arr['code']=='NOT-LOGINED'){
			$this->skeyzt=1;
			$this->msg[]='登录态验证失败，请重新登录';
		}else{
			$this->msg[]=$arr['msg'];
		}
	}
	public function social(){
		$url = 'http://play.mobile.qq.com/pansocial/cgi/checkin/checkInAction?actionType=0&token='.$this->getGTK3($this->skey).'&packageId=0&_='.time().'2588&callback=';
		$data = $this->get_curl($url,0,'http://play.mobile.qq.com/play/mqqplay/keepsign/index.html',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('errCode',$arr) && $arr['errCode']==0){
			$this->msg[]='QQ附近签到成功！魅力值+'.$arr['uint32_added_charm'];
		}elseif($arr['errCode']==1){
			$this->msg[]='QQ附近签到今日已签';
		}elseif($arr['errCode']==100000){
			$this->skeyzt=1;
			$this->msg[]='QQ附近签到失败！SKEY过期';
		}else{
			$this->msg[]='QQ附近签到失败！'.$data;
		}
	}
	public function farm(){
		$url = 'http://nc.qzone.qq.com/cgi-bin/cgi_farm_month_signin_day?g_tk='.$this->gtk;
		$post = 'uinY='.$this->uin;
		$data = $this->get_curl($url,$post,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ecode',$arr) && $arr['ecode']==0){
			$this->msg[]='QQ农场签到成功！';
		}elseif($arr['ecode']==-102){
			$this->msg[]='QQ农场今日已经签到';
		}elseif($arr['ecode']==-10004){
			$this->skeyzt=1;
			$this->msg[]='QQ农场签到失败！SKEY过期';
		}else{
			$this->msg[]='QQ农场签到失败！'.$arr['errorContent'];
		}
	}
	public function qqgame(){
		$url = 'http://reader.sh.vip.qq.com/cgi-bin/common_async_cgi?g_tk='.$this->gtk.'&plat=1&version=6.6.6&param=%7B%22key0%22%3A%7B%22param%22%3A%7B%22bid%22%3A13792605%7D%2C%22module%22%3A%22reader_comment_read_svr%22%2C%22method%22%3A%22GetReadAllEndPageMsg%22%7D%7D';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ecode',$arr) && $arr['ecode']==0){
			$this->msg[]='QQ手游加速0.2天成功！';
		}else{
			$this->msg[]='QQ手游加速失败！'.$data;
		}
	}
	public function checkin(){
		$url = 'http://ti.qq.com/cgi-node/signin/pickup';
		$data = $this->get_curl($url,0,'http://ti.qq.com/signin/public/index.html?_wv=1090532257&_wwv=13',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='QQ每日打卡成功！已打卡'.$arr['data']['day'].'天，本次获得'.$arr['data']['prizeList'][0]['prizeName'];
		}elseif($arr['code']==-200){
			$this->msg[]='打卡失败，您可能未获得测试资格';
		}elseif($arr['code']==100000){
			$this->skeyzt=1;
			$this->msg[]='打卡失败，SKEY已失效';
		}else{
			$this->msg[]='打卡失败！'.$arr['msg'];
		}
		$this->msg[]='----------';
	}
	public function ncapp(){
		$url = 'http://mcapp.z.qq.com/nc/cgi-bin/wap_farm_index?sid=c&g_ut=3&signin=1';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		if(preg_match("/欢迎来到QQ农场/",$data)){
			$this->msg[]='QQ农场开通';
			$data = $this->get_curl($url,0,$url,$this->cookie);
			if(preg_match("!<p class=\"txt-warning2\">(.*?)</p>!is",$data,$match)){
				$this->msg[]='QQ农场'.$match[1];
			}elseif(preg_match("/签到成功/",$data)){
				$this->msg[]='QQ农场签到成功！';
			}elseif(preg_match("/我的土地/",$data)){
				$this->msg[]='QQ农场已签到！';
			}else{
				$this->msg[]='QQ农场签到结果获取失败！';
			}
		}elseif(preg_match("!<p class=\"txt-warning2\">(.*?)</p>!is",$data,$match)){
			$this->msg[]='QQ农场'.$match[1];
		}elseif(preg_match("/签到成功/",$data)){
			$this->msg[]='QQ农场签到成功！';
		}elseif(preg_match("/我的土地/",$data)){
			$this->msg[]='QQ农场已签到！';
		}else{
			$this->msg[]='QQ农场签到结果获取失败！';
		}
		if(preg_match("!一键.*?place=(.*?)&amp.*?收获</a>!is",$data,$match)){
			$url = 'http://mcapp.z.qq.com/nc/cgi-bin/wap_farm_harvest?sid=c&B_UID=0&place='.$match[1].'&g_ut=3&time=-2147483648';
			$data = $this->get_curl($url,0,$url,$this->cookie);
			if(preg_match("!<p class=\"txt-warning2\">(.*?)</p>!is",$data,$match)){
				$this->msg[]=strip_tags($match[1]);
			}elseif(preg_match("/一键操作对以下用户开放/",$data)){
				$this->msg[]='黄钻用户才可以使用一键收获！';
			}elseif(preg_match("/我的农场/",$data)){
				$this->msg[]='QQ农场一键收获已完成！';
			}else{
				$this->msg[]='QQ农场一键收获结果获取失败！';
			}
		}

		$url = 'http://mcapp.z.qq.com/nc/cgi-bin/wap_farm_freegift_recv?sid=c&g_ut=3&fg_recv=1';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		if(preg_match("!<div class=\"top-info\">(.*?)</div>!is",$data,$match)){
			$this->msg[]=$match[1];
		}elseif(preg_match("/我的农场/",$data)){
			$this->msg[]='QQ农场一键收礼已完成！';
		}else{
			$this->msg[]='QQ农场收礼结果获取失败！';
		}
	}

	public function mcapp(){
		$url = 'http://mcapp.z.qq.com/mc/cgi-bin/wap_pasture_index?sid=c&g_ut=3&signin=1&yellow=1&optflag=2&pid=0&v=1';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		if(preg_match("/欢迎来到QQ牧场/",$data)){
			$this->msg[]='QQ牧场开通';
			$data = $this->get_curl($url,0,$url,$this->cookie);
			if(preg_match("!<p class=\"txt-warning2\">(.*?)</p>!is",$data,$match)){
				$this->msg[]='QQ牧场'.$match[1];
			}elseif(preg_match("/签到成功/",$data)){
				$this->msg[]='QQ牧场签到成功！';
			}elseif(preg_match("/动物及产品/",$data)){
				$this->msg[]='QQ牧场已签到！';
			}else{
				$this->msg[]='QQ牧场签到结果获取失败！';
			}
		}elseif(preg_match("!<p class=\"txt-warning2\">(.*?)</p>!is",$data,$match)){
			$this->msg[]='QQ牧场'.$match[1];
		}elseif(preg_match("/签到成功/",$data)){
			$this->msg[]='QQ牧场签到成功！';
		}elseif(preg_match("/动物及产品/",$data)){
			$this->msg[]='QQ牧场已签到！';
		}else{
			$this->msg[]='QQ牧场签到结果获取失败！';
		}

		if (preg_match("/清扫/",$data)) { 
	 		preg_match_all('/num=*(.*)&type=2&pos=0">清扫*/i',$data,$bb);
	 		$bb = $bb[1][0];
	 		$this->get_curl("http://mcapp.z.qq.com/mc/cgi-bin/wap_pasture_help?sid=c&g_ut=3&B_UID=0&num={$bb}&type=2&pos=0");
		}
		if (preg_match("/饥饿/",$data)) { 
			$this->get_curl("http://mcapp.z.qq.com/mc/cgi-bin/wap_pasture_feed_food?sid=c&g_ut=3&num=999");
		}
		if (preg_match("/收获期/",$data)) {
			preg_match_all('/期 <a[\s]+[^>]*href="*(.*)">收获<*/i',$data,$sh);
			$shnum = count($sh[1]);
			if ($shnum > 2) $shnum = 2;
			for ($i = 0; $i < $shnum; $i++) $this->get_curl(htmlspecialchars_decode($sh[1][$i]));
		}
		if (preg_match("/生产期/",$data)) {
			preg_match_all('/<a[\s]+[^>]*href="*(.*)">生产<*/i',$data,$sh);
			$shnum = count($sh[1]);
			if ($shnum > 2) $shnum = 2;
			for ($i = 0; $i < $shnum; $i++) $this->get_curl(htmlspecialchars_decode($sh[1][$i]));
		}
		if (preg_match("/剩余/",$data)) {
			preg_match_all('/<a[\s]+[^>]*href="*(.*)">收获<*/i',$data,$sh);
			$shnum = count($sh[1]);
			if ($shnum > 2) $shnum = 2;
			for ($i = 0; $i < $shnum; $i++) $this->get_curl(htmlspecialchars_decode($sh[1][$i]));
		}

		$url = 'http://mcapp.z.qq.com/mc/cgi-bin/wap_pasture_harvest?sid=c&g_ut=3&serial=-1&htype=3';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		if(preg_match("!<p class=\"txt-warning2\">(.*?)</p>!is",$data,$match)){
			$this->msg[]=strip_tags($match[1]);
		}elseif(preg_match("/动物及产品/",$data)){
			$this->msg[]='QQ牧场一键收获已完成！';
		}else{
			$this->msg[]='QQ牧场一键收获结果获取失败！';
		}
	}
}