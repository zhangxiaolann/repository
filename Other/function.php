<?php
function GetIP() {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP") , "unknown")) $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR") , "unknown")) $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR") , "unknown")) $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) $ip = $_SERVER['REMOTE_ADDR'];
    else $ip = "unknown";
    return ($ip);
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
function get_socket($url, $post = 0, $cookie = 0, $referer = 1) { //Author:消失的彩虹海
        $urlinfo = parse_url($url);
        $domain = $urlinfo['host'];
        $query = $urlinfo['path'] . '?' . $urlinfo['query'];
        $length = strlen($post);
        $fp = fsockopen($domain, 80, $errno, $errstr, 30);
        if (!$fp) {
            return false;
        } else {
            if ($post) $out = "POST {$query} HTTP/1.1\r\n";
            else $out = "GET {$query} HTTP/1.1\r\n";
            $out.= "Accept: application/json\r\n";
            $out.= "Accept-Language: zh-CN,zh;q=0.8\r\n";
            $out.= "X-Requested-With: XMLHttpRequest\r\n";
            if ($post) {
                $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
                $out.= "Content-Length: {$length}\r\n";
            }
            $out.= "Host: {$domain}\r\n";
            if ($referer) {
                if ($referer == 1) $out.= "Referer: http://m.qzone.com/infocenter?g_f=\r\n";
                else $out.= "Referer: {$referer}\r\n";
            }
            $out.= "User-Agent: Mozilla/5.0 (Linux; U; Android 2.3; en-us) AppleWebKit/999+ (KHTML, like Gecko) Safari/999.9\r\n";
            $out.= "Connection: close\r\n";
            $out.= "Cache-Control: no-cache\r\n";
            $out.= "Cookie: {$cookie}\r\n\r\n";
            if ($post) $out.= "{$post}";
            $str = '';
            fwrite($fp, $out);
            while (!feof($fp)) {
                $str.= fgets($fp, 2048);
            }
            fclose($fp);
        }
        $str = strstr($str, '{');
        return $str;
}
function getGTK($skey) {
        $len = strlen($skey);
        $hash = 5381;
        for ($i = 0; $i < $len; $i++) {
            $hash+= ($hash << 5) + ord($skey[$i]);
        }
        return $hash & 0x7fffffff; //计算g_tk
        
}
function getGTK2($skey) {
        //$skey = str_replace('@','',$skey);
        $salt = 5381;
        $md5key = 'tencentQQVIP123443safde&!%^%1282';
        $hash = array();
        $hash[] = ($salt << 5);
        $len = strlen($skey);
        for ($i = 0; $i < $len; $i++) {
            $ASCIICode = mb_convert_encoding($skey[$i], 'UTF-32BE', 'UTF-8');
            $ASCIICode = hexdec(bin2hex($ASCIICode));
            $hash[] = (($salt << 5) + $ASCIICode);
            $salt = $ASCIICode;
        }
        $md5str = md5(implode($hash) . $md5key);
        return $md5str;
}