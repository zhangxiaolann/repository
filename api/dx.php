<?php
// 此程序由微秒赞（洛绝尘）深度定制修改 <1031601644@qq.com>
// 底包为快乐是福1.81 <815856515@qq.com>
// 人不要脸，天下无敌。 儿子你要改版权爸爸也不拦你。
header('Content-Type: text/html; charset=UTF-8');
$sq=require(base64_decode("Li4vQ29tbW9uL0NvbmYvc3EucGhw"));
error_reporting(E_ALL & ~E_NOTICE);
session_start();
$uin = $_REQUEST['uin'];
$skey = $_REQUEST['skey'];
$json = $_SESSION['friendlist'][$uin];
$arr = json_decode($json, true);
if ($arr['code'] == - 1001) {
    exit('{"code":-1,"msg":"skey过期！"}');
}
$hycount = count($arr['data']['items']);
$dxrow['code'] = 0;$dxrow['msg'] = 'suc';
$i = 0;//记录已检测过的数量
$n = 0;//数组中的变量
$row=$arr['data']['items'];
$num=count($row);
while ($n <= $num-1){
	$touin = $row[$n]['uin'];//跳到n数组qq检测
	if (!isset($_SESSION["o" . $uin][$touin])) {//判断是否已检测过这个QQ
        $json = get_curl("http://api.qqmzp.com/jcdx3.php?uin=" . $uin . "&touin=" . $touin . "&skey=" . $skey  . "&url=".$sq['dm']."&v=".$sq['v']);
       //print_r($json);exit;
        $arr = json_decode($json, true);
        if ($arr) {
            $code = $arr['code'];
			$_SESSION['o' . $uin][$touin] = $code;
            /* if ($code == 0) {
                //$xfriend = $arr['xfriend'];
                $_SESSION['o' . $uin][$touin] = 1;
            } else {
                $_SESSION['o' . $uin][$touin] = 1;
            } */
            if ($code == -1) {
                $dxrow['dxrow'][] = $row[$n];
                $_SESSION['klsf_dxrow'][$uin][] = $row[$n];
            }
        }
        $i++;//已检测过的数量+1
    }
	$n++;
	if ($i >= 10) break;
}
if ($n >= $hycount) {
    $dxrow['finish'] = 1;
} else {
    $dxrow['finish'] = 0;
}
//print_r($_SESSION);exit;
$dxrow['count'] = $n;
$dxrow['dxcount'] = count($_SESSION['klsf_dxrow'][$uin]);
exit(json_encode($dxrow));
function get_curl($url, $post = 0, $referer = 0, $cookie = 0, $header = 0, $ua = 0, $nobaody = 0) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,str_ireplace(base64_decode("cXFhcHAuYWxpYXBwLmNvbQ=="),base64_decode("YXBpLnFxbXpwLmNvbQ=="),$url));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    if ($header) {
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
    }
    if ($cookie) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }
    if ($referer) {
        if ($referer == 1) {
            curl_setopt($ch, CURLOPT_REFERER, "http://v.qqmzp.com");
        } else {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
    }
    if ($ua) {
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36');
    } else {
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 4.4.4; zh-cn; MI 4C Build/KTU84P) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.4 TBS/025469 Mobile Safari/533.1 V1_AND_SQ_5.9.1_272_YYB_D QQ/5.9.1.2535 NetType/WIFI WebP/0.3.0 Pixel/1080');
    }
    if ($nobaody) {
        curl_setopt($ch, CURLOPT_NOBODY, 1);
    }
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}
