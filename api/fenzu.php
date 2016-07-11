<?php
// 此程序由微秒赞（洛绝尘）深度定制修改 <1031601644@qq.com>
// 底包为快乐是福1.81 <815856515@qq.com>
// 人不要脸，天下无敌。 儿子你要改版权爸爸也不拦你。
header('Content-type: application/json');
$uin=$_POST['uin']?$_POST['uin']:exit('{"code":-10,"msg":"No Uin！"}');
$skey=$_POST['skey']?$_POST['skey']:exit('{"code":-1,"msg":"No Skey！"}');
$pskey=$_POST['pskey']?$_POST['pskey']:exit('{"code":-1,"msg":"No P_skey！"}');
$gpid=$_POST['gpid']?$_POST['gpid']:0;
$uins=$_POST['uins']?$_POST['uins']:exit('{"code":-1,"msg":"No uins"}');
$uinarr=explode(',',$uins);
$uinarr=array_filter($uinarr);
//print_r($uinarr);exit();
$gtk=getGTK($pskey);
$cookie='pt2gguin=o0'.$uin.'; uin=o0'.$uin.'; skey='.$skey.'; p_skey='.$pskey.'; p_uin=o0'.$uin.';';
$url="http://w.qzone.qq.com/cgi-bin/tfriend/friend_chggroupid.cgi?g_tk=".$gtk;
$suc=0;$err=0;
foreach($uinarr as $key => $ouin){
	$post="qzreferrer=".urlencode("http://ctc.qzs.qq.com/qzone/v8/pages/friends/friend_msg_setting.html?mode=pass1&ouin=".$ouin."&id=0&flag=100&key=".$ouin."&time=")."&gpid=".$gpid."&ifuin=".$ouin."&uin=".$uin."&flag=102&key=".$ouin."&rd=0.08129".time()."&remark=&fupdate=1";
	$data=get_curl($url,$post,"http://ctc.qzs.qq.com/qzone/v8/pages/friends/friend_msg_setting.html?mode=pass1&ouin=".$ouin."&id=&flag=102&key=0&time=",$cookie);
	$json=getSubstr($data,"callback(",");");
	$arr=json_decode($json,true);
	if(array_key_exists('code',$arr) && $arr['code']==0){
		$suc++;
	}elseif($arr['code']==-3000){
		exit('{"code":-1,"msg":"SKEY已失效！"}');
	}else{
		$err++;
	}
}
exit('{"code":0,"suc":'.$suc.',"err":'.$err.'}');
function get_curl($url,$post=0,$referer=0,$cookie=0,$header=0,$ua=0,$nobaody=0){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
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
			curl_setopt($ch, CURLOPT_REFERER, "http://m.qzone.com/infocenter?g_f=");
		}else{
			curl_setopt($ch, CURLOPT_REFERER, $referer);
		}
	}
	if($ua){
		curl_setopt($ch, CURLOPT_USERAGENT,$ua);
	}else{
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; U; Android 4.0.4; es-mx; HTC_One_X Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0');
	}
	if($nobaody){
		curl_setopt($ch, CURLOPT_NOBODY,1);//主要头部
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,0);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}
function getGTK($skey){
    $len = strlen($skey);
    $hash = 5381;
    for($i = 0; $i < $len; $i++){
        //改下面两行
        $hash += ((($hash << 5) & 0x7fffffff) + ord($skey[$i])) & 0x7fffffff;
        $hash&=0x7fffffff;
    }
    return $hash & 0x7fffffff;//计算g_tk
}
function getSubstr($str, $leftStr, $rightStr){
		$left = strpos($str, $leftStr);
		//echo '左边:'.$left;
		$right = strpos($str, $rightStr,$left);
		//echo '<br>右边:'.$right;
		if($left < 0 or $right < $left) return '';
		return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
}