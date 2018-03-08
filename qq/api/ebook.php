<?php
/*
 * 每月领取腾讯文学VIP包月体验卡并使用
 * 转载请注明出处 www.qqmiaozan.com
 *
 * Author 天涯 <45701103@qq.com>
 * Update 2016年6月18日
 */
error_reporting(0);
header("Content-Type: text/html; charset=UTF-8");

function get_curl($url, $referer = 0) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.5 Mobile Safari/533.1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}

/*
 * 使用体验卡
 * */
function useCard($uin, $skey) {
    $url = "http://ubook.3g.qq.com/8/user/useMonthCard?k1={$skey}&u1=o0{$uin}&ug=&b_f=400001&g_f=100013&tt=7456";
    $data = get_curl($url, 'http://ubook.qq.com/8/myaccount.html');
    $arr = json_decode($data, true);
    if ($arr['vipCard']['first'] == "0") {
        $msg = '成功开通腾讯图书VIP！';
    } else {
        if ($arr['vipCard']['first'] == "1") {
            $msg = "您已经是包月用户，不能使用包月体验卡";
        } else {
            if ($arr['vipCard']['first'] == "-20006") {
                $msg = "参数错误，请确认您账户信息";
            } else {
                if ($arr['vipCard']['first'] == "-20007") {
                    $msg = "体验卡过期，不能使用";
                } else {
                    // $msg = "网络超时，请稍后重试" . $arr['vipCard']['first'];
                    $msg = "领取失败，请更新此QQ后再来领取";
                }
            }
        }
    }
    return $msg;
}

$uin = is_numeric($_GET['uin']) ? $_GET['uin'] : exit('No uin!');
$skey = isset($_GET['skey']) ? urlencode($_GET['skey']) : exit('No skey!');

$url = "http://ubook.3g.qq.com/8/user/normalLevel?k1={$skey}&u1=o0{$uin}&ug=&b_f=400001&g_f=100013&tt=0208";
$data = get_curl($url, 'http://ubook.qq.com/8/mylevel.html?viplevel=1');
$arr = json_decode($data, true);
// 判断是否已经领取过每月礼包
if ($arr['monthGifts']['hasRecieved']) {
    // 领取过，查询剩余的包月卡
    $url = "http://ubook.3g.qq.com/8/user/account?k1={$skey}&u1=o0{$uin}&ug=&b_f=400001&g_f=100013&tt=9287";
    $data = get_curl($url, 'http://ubook.qq.com/8/myaccount.html');
    $arr = json_decode($data, true);
    // 判断是否有剩余的包月卡，如果有就使用
    if ($arr['baoyueCard']['first'] > 0) {
        $msg = useCard($uin, $skey);
    } else {
        $msg = '本月已经使用过了，下个月再来吧!';
    }
} else {
    // 本月未领取，先领取礼包
    $url = "http://ubook.3g.qq.com/8/user/recieveGifts?k1={$skey}&u1=o0{$uin}&ug=&b_f=400001&g_f=100013&tt=7486";
    $data = get_curl($url, 'http://ubook.qq.com/8/mylevel.html?viplevel=1');
    $arr = json_decode($data, true);
    if ($arr['code'] == 0 || $arr['code'] == -5) {
        if ($arr['vipType'] == 0) {
            // msg = "领取成功！";
            $msg = useCard($uin, $skey);
        } else {
            $msg = "本月已经领取过了~[2]";
        }
    } else {
        if ($arr['code'] == -2) {
            $msg = "今日礼包已发完，明天早点来领哦。";
        } else {
            $msg = "领取失败。";
        }
    }
}
$data=array("code"=>1,"msg"=>$msg);
echo json_encode($data);
