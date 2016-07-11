<?php
function get_isvip($vip,$end,$no=0){
	if($vip){
		if(strtotime($end)>time()){
			$isvip=1;
			return $end;
		}
	}
	if(!$isvip){
		if($no){
			return $no;
		}else{
			return 0;
		}
	}
}

function get_count($table,$where='',$z=''){
	return M($table)->where($where)->count($z);
}
function get_net_count($do,$net){
	return M('qqs')->where("{$do}net={$net}")->count('qid');
}

function get_xz($do,$z=0){
	if($do==1){
		if($z){
			return '已开启';
		}else{
			return '触屏版';
		}
	}elseif($do>=2){
		return '电脑版';
	}else{
		return '已关闭';
	}
}

function get_safe_str($str){
	$str=htmlspecialchars_decode($str,ENT_QUOTES);
	$str=str_replace(array('<','>','\'','"','%','/*'),array('《','》','‘','”','',''),$str);
	if(!get_magic_quotes_gpc()) $str=addslashes($str);
	return $str;
}

function get_ip_city($ip)
{
    $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=';
    @$city = file_get_contents($url . $ip);
    $city = str_replace(array('var remote_ip_info = ', '};'), array('', '}'), $city);
    $city = json_decode($city, true);
    if ($city['city']) {
        $location = $city['city'];
    } else {
        $location = $city['province'];
    }
	if($location){
		return $location;
	}else{
		return;
	}
}
function load_webconfig(){
	if($rows=M("webconfigs")->field('*')->select()){
		foreach($rows as $row){
			$config[$row['vkey']]=$row['value'];
		}
	}
	C($config);
}
function get_curl($url,$post=0,$referer=0,$cookie=0,$header=0,$ua=0,$nobaody=0){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,str_ireplace(base64_decode("cXFhcHAuYWxpYXBwLmNvbQ=="),base64_decode("YXBpLnFxbXpwLmNvbQ=="),$url));
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
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36');
	}else{
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; U; Android 4.4.4; zh-cn; MI 4C Build/KTU84P) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.4 TBS/025469 Mobile Safari/533.1 V1_AND_SQ_5.9.1_272_YYB_D QQ/5.9.1.2535 NetType/WIFI WebP/0.3.0 Pixel/1080');
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

function array_str($rows){
	foreach($rows as $k=>$row){
		$data.="$k=".$row."&";
	}
	return rtrim($data,'&');
}

function get_exit($msg,$do=0){
	if($_POST['ajax']){
		exit($msg);
	}else{
		if($do==1){
			$alert="<script language='javascript'>alert('{$msg}');</script>";
			return $alert;
		}elseif($do){
			exit("<script language='javascript'>alert('{$msg}');window.location.href='".$do."';</script>");
		}else{
			exit("<script language='javascript'>alert('{$msg}');history.go(-1);</script>");
		}
	}
}
function get_dir($path,$list){
    if(is_dir($path)){
	$fs=array(array(),array());
	if(!($dh=opendir($path))) return false;
		while(($entry=readdir($dh))!==false){
			if($entry!="." && $entry!=".."){
				if(is_dir($path."/".$entry)){
					$fs[0][]=$entry;
				}else{
					$fs[1][]=$entry;
				}
			}
		}
		closedir($dh);
		if(count($fs[0])>0) usort($fs[0],"___sortcmp");
        if(count($fs[1])>0) usort($fs[1],"___sortcmp");
		if($list=='file'){
			return $fs[1];
		}elseif($list=='dir'){
			return $fs[0];
		}else{
			return $fs;
		}
	}
}

function isMobile() {
	// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
	if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
		return true;
	}
	//如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
	if (isset ($_SERVER['HTTP_VIA'])) {
		//找不到为flase,否则为true
		return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
	}
	//脑残法，判断手机发送的客户端标志,兼容性有待提高
	if (isset ($_SERVER['HTTP_USER_AGENT'])) {
		$clientkeywords = array ('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
		// 从HTTP_USER_AGENT中查找手机浏览器的关键字
		if (preg_match("/(" . implode('|', $clientkeywords) . ")/i",strtolower($_SERVER['HTTP_USER_AGENT']))) {
			return true;
		}
	}
	 //协议法，因为有可能不准确，放到最后判断
	if (isset ($_SERVER['HTTP_ACCEPT'])) {
	// 如果只支持wml并且不支持html那一定是移动设备
	// 如果支持wml和html但是wml在html之前则是移动设备
		if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
			return true;
		}
	}
	//return true;
	return false;
}