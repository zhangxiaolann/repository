<?php
/*
 *QQ空间多功能操作类
 *Author：消失的彩虹海 & 快乐是福 & 云上的影子 & 微秒赞(洛绝尘)
*/
// 此程序由微秒赞（洛绝尘）深度定制修改 <1031601644@qq.com>
// 底包为快乐是福1.81 <815856515@qq.com>
class qzone {
    public $msg;
    public function __construct($uin, $sid = 0, $skey = 0, $pc_p_skey = 0, $cp_p_skey = 0) {
        $this->uin			= $uin;
        $this->sid			= $sid;
		$this->skey			= $skey;
		$this->pc_p_skey 	= $pc_p_skey;
		$this->cp_p_skey 	= $cp_p_skey;
        $this->gtk			= $this->getGTK($skey);
		$this->gtk2			= $this->getGTK2($skey);
		$this->pc_gtk		= $this->getGTK($pc_p_skey);
		$this->cp_gtk		= $this->getGTK($cp_p_skey);
		$this->cookie		= 'pt2gguin=o' . $uin . '; uin=o' . $uin . '; skey=' . $skey . ';';
        $this->pc_cookie	= 'pt2gguin=o' . $uin . '; uin=o' . $uin . '; skey=' . $skey . '; p_uin=o'. $uin .'; p_skey='.$pc_p_skey.';';
		$this->cp_cookie	= 'pt2gguin=o' . $uin . '; uin=o' . $uin . '; skey=' . $skey . '; p_uin=o'. $uin .'; p_skey='.$cp_p_skey.';';
        if (defined("SAE_ACCESSKEY")) $this->cookiefile = SAE_TMP_PATH . $uin . '.txt';
        else $this->cookiefile = './cookie/' . $uin . '.txt';
    }
	public function lld(){
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
	//get_curl($url, $post = 0, $referer = 1, $cookie = 0, $header = 0, $ua = 0, $nobaody = 0)
    public function pcqunqd($qun,$qunqdcon) {//Author:微秒赞(洛绝尘)
        $url = "http://qiandao.qun.qq.com/cgi-bin/sign";
		$rf="http://qiandao.qun.qq.com/qqweb/m/qun/checkin/index.html?_lv=32348&_wv=1027&_bid=2166&gc=". $qun."&sid=Abq5hfff7wI3sHgOWYTY3STq";
        $post = "&gc=" . $qun . "&is_sign=0&from=1&bkn=" . $this->gtk."";
        $json = $this->get_curl($url, $post, $rf, $this->cookie,0,1);
		$arr = json_decode($json, true);
		if($arr[ec]==0)
			$this->msg[] = $this->uin .'在群号为'. $qun.'签到成功!您已累计签到'.$arr[conti_count].'天,签到排名为'.$arr[rank].'名,今天已有'.$arr[today_count].'人签到!';
		else
			$this->error[] = $this->uin .'在群号为'. $qun.'签到失败，原因：'.$arr[em];
    }
    public function qunqd($qunqdcon) {//Author:微秒赞(洛绝尘)
        $arr = $this->getqun();
		//print_r($arr);exit;
		$num=count($arr)-1;
		$i = 0;
		while ($i <= $num) {
           $this->pcqunqd($arr[$i],$qunqdcon);
           $i++;
        }
    }
    public function getqun() {//Author:微秒赞(洛绝尘)
        $url = "http://qun.qq.com/cgi-bin/qun_mgr/get_group_list";
        $post = "bkn=" . $this->gtk;
        $json = $this->get_curl($url, $post, 1, $this->cookie);
		//print_r($json);exit;
		$arr = json_decode($json, true);
		
		if (array_key_exists('ec', $arr) && $arr['ec'] == 1) {
			 $this->msg[] = $this->uin . " SKEY过期";
			 $this->skeyzt = 1;
		}
		$create = count($arr[create]);
        $ec = count($arr[ec]);
        $join = count($arr[join]);
        $manage = count($arr[manage]);
        $i = 0;
		if($arr[create]!="0"){
			while ($i <= $create -1) {
				$qunarr[] = $arr[create][$i][gc];
				$i++;
			}
		}
        $i = 0;
		if($arr[ec]!="0"){
			while ($i <= $ec-1 ) {
				$qunarr[] = $arr[ec][$i][gc];
				$i++;
			}
		}
        $i = 0;
		if($arr[join]!="0"){
			while ($i <= $join-1 ) {
				$qunarr[] = $arr[join][$i][gc];
				$i++;
			}
		}
        $i = 0;
		if($arr[manage]!="0"){
			while ($i <= $manage-1) {
				$qunarr[] = $arr[manage][$i][gc];
				$i++;
			}
		}
		return $qunarr;
    }
    function gift($uin, $con) {
        $url = "http://mobile.qzone.qq.com/gift/giftweb?g_tk=".$this->cp_gtk;
        $post = "action=3&itemid=108517&struin={$uin}&content=" . urlencode($con) . "&format=json&isprivate=0";
        $json = $this->get_curl($url, $post,1,$this->cp_cookie);
        $arr = json_decode($json, true);
        if (array_key_exists('code', $arr) && $arr['code'] == 0) {
            $this->msg[] = $this->uin . " 送礼物成功！";
        } elseif ($arr['code'] == - 3000) {
            $this->skeyzt = 1;
            $this->msg[] = $this->uin . " 未登录";
        } elseif ($arr['code'] == - 10000) {
            $this->msg[] = $this->uin . " 收礼人设置了权限";
        } else {
            $this->msg[] = $this->uin . " 送礼物失败！";
        }
    }
	function pc_liuyan($uin,$con){
		$url = "http://m.qzone.qq.com/cgi-bin/new/add_msgb?g_tk=".$this->pc_gtk;
		$post = 'qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fmsgboard%2Fmsgbcanvas.html%23page%3D1&content='.urlencode($con).'&hostUin='.$uin.'&uin='.$this->uin.'&format=json&inCharset=utf-8&outCharset=utf-8&iNotice=1&ref=qzone&json=1&g_tk='.$this->gtk;
		$json=$this->get_curl($url,$post,'http://cnc.qzs.qq.com/qzone/msgboard/msgbcanvas.html',$this->pc_cookie);
		$arr = json_decode($json, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]=$this->uin.' 为 '.$uin.' 刷留言成功[CP]';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 为 '.$uin.' 刷留言失败[CP]！原因:'.$arr['message'];
		}else{
			$this->msg[]=$this->uin.' 为 '.$uin.' 刷留言失败[CP]！原因:'.$arr['message'];
		}
	}
	function liuyan($uin,$con){
		$url = "http://mobile.qzone.qq.com/msgb/fcg_add_msg?g_tk=".$this->cp_gtk;
		$post = "res_uin={$uin}&format=json&content=".urlencode($con)."&opr_type=add_comment";
		$json=$this->get_curl($url,$post,1,$this->cp_cookie);
		$arr = json_decode($json, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]=$this->uin.' 为 '.$uin.' 刷留言成功[CP]';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 为 '.$uin.' 刷留言失败[CP]！原因:'.$arr['message'];
		}elseif($arr['code']==-4017){
			$this->msg[]=$this->uin.' 为 '.$uin.' 刷留言成功[CP]，留言内容将在你审核后展示';
		}else{
			$this->msg[]=$this->uin.' 为 '.$uin.' 刷留言失败[CP]！原因:'.$arr['message'];
		}
	}
    public function zyzan($uin) {
		$num=rand(11000,2000000000);
        $url = 'http://w.qzone.qq.com/cgi-bin/likes/internal_dolike_app?g_tk=' . $this->pc_gtk;
        $post = 'qzreferrer=http://user.qzone.qq.com/' . $num . '/main&appid=7030&face=0&fupdate=1&from=1&query_count=200&opuin=' . $this->uin . '&unikey=http://user.qzone.qq.com/' . $num . '&curkey=http://user.qzone.qq.com/' . $uin . '&zb_url=http://i.gtimg.cn/qzone/space_item/pre/3/72019_1.gif';
        $json = $this->get_curl($url, $post, 'http://user.qzone.qq.com/' . $uin, $this->pc_cookie);
		$json = $this->getSubstr($json,"frameElement.callback(",");");
        $arr = json_decode($json, true);
		//$this->msg[]=$json;
		//$this->msg[]=$this->cookie;
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $this->msg[] = $this->uin . '模拟'.$num.' 赞 ' . $uin . ' 主页成功[PC]';
        } elseif ($arr[code] == - 3000) {
            $this->skeyzt = 1;
            $this->error[] = $this->uin . '模拟'.$num.' 赞 ' . $uin . ' 主页失败[PC]！原因:' . $arr[message];
        } else {
            $this->error[] = $this->uin . '模拟'.$num.' 赞 ' . $uin . ' 主页失败[PC]！原因:' . $arr['message'];
        }
    }
    public function quantu() {
        if ($shuos = $this->getnew()) {
            foreach ($shuos as $shuo) {
                $albumid = '';
                $lloc = '';
                if ($shuo['original']) {
                    $albumid = $shuo['original']['cell_pic']['albumid'];
                    $lloc = $shuo['original']['cell_pic']['picdata'][0]['lloc'];
                }
                if ($shuo['pic']) {
                    $albumid = $shuo['pic']['albumid'];
                    $lloc = $shuo['pic']['picdata'][0]['lloc'];
                }
                if (!empty($albumid)) {
                    $touin = $shuo['userinfo']['user']['uin'];
                    $this->quantu_do($touin, $albumid, $lloc);
                    if ($this->skeyzt) break;
                }
            }
        }
    }
    public function quantu_do($touin, $albumid, $lloc) {
        $url = "http://app.photo.qq.com/cgi-bin/app/cgi_annotate_face?g_tk=" . $this->pc_gtk;
        $post = "format=json&uin={$this->uin}&hostUin=$touin&faUin={$this->uin}&faceid=&oper=0&albumid=$albumid&lloc=$lloc&facerect=10_10_50_50&extdata=&inCharset=GBK&outCharset=GBK&source=qzone&plat=qzone&facefrom=moodfloat&faceuin={$this->uin}&writeuin={$this->uin}&facealbumpage=quanren&qzreferrer=http://user.qzone.qq.com/$uin/infocenter?via=toolbar";
        $json = $this->get_curl($url, $post, 'http://user.qzone.qq.com/' . $uin . '/infocenter?via=toolbar', $this->pc_cookie);
        $json = mb_convert_encoding($json, "UTF-8", "gb2312");
        $arr = json_decode($json, true);
        if (!array_key_exists('code', $arr)) {
            $this->error[] = "圈{$touin}的图{$albumid}失败，原因：获取结果失败！";
        } elseif ($arr['code'] == - 3000) {
            $this->skeyzt = 1;
            $this->error[] = "圈{$touin}的图{$albumid}失败，原因：SKEY已失效！";
        } elseif ($arr['code'] == 0) {
            $this->msg[] = "圈{$touin}的图{$albumid}成功";
        } else {
            $this->error[] = "圈{$touin}的图{$albumid}失败，原因：" . $arr['message'];
        }
    }
    public function getll(){
		$url='http://mobile.qzone.qq.com/list?g_tk='.$this->cp_gtk.'&res_attach=&format=json&list_type=msg&action=0&res_uin='.$this->uin.'&count=20';
		$get=$this->get_curl($url,0,1,$this->cp_cookie);
		$arr = json_decode($get,true);
		if(!array_key_exists('code',$arr)){
			$this->msg[]="获取留言列表失败";
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]="获取留言列表失败，原因：SKEY已失效！";
		}elseif($arr['code']==0 && $arr['data']['vFeeds']){
			if($arr['data']['vFeeds'])
				return $arr['data']['vFeeds'];
			else
				$this->msg[]='没有留言！';
		}else{
			$this->msg[]="获取留言列表失败，原因：".$arr['message'];
		}
		
	}
	public function getliuyan(){
		$ua='Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0';
		$url='http://m.qzone.qq.com/cgi-bin/new/get_msgb?uin='.$this->uin.'&hostUin='.$this->uin.'&start=0&s=0.935081'.time().'&format=json&num=20&inCharset=utf-8&outCharset=utf-8&g_tk='.$this->cp_gtk;
		$json=$this->get_curl($url,0,'http://user.qzone.qq.com/',$this->cp_cookie,0,$ua);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='获取留言列表成功！';
			return $arr['data']['commentList'];
		}else{
			$this->msg[]='获取留言列表失败！';
		}
	}
	public function PCdelll($id,$uin){
		$url = "http://m.qzone.qq.com/cgi-bin/new/del_msgb?g_tk=".$this->pc_gtk;
		$post="qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fmsgboard%2Fmsgbcanvas.html%23page%3D1&hostUin=".$this->uin."&idList=".$id."&uinList=".$uin."&format=json&iNotice=1&inCharset=utf-8&outCharset=utf-8&ref=qzone&json=1&g_tk=".$this->gtk2;
		$data = $this->get_curl($url,$post,'http://ctc.qzs.qq.com/qzone/msgboard/msgbcanvas.html',$this->pc_cookie);
		$arr=json_decode($data,true);
		if($arr){
			if(array_key_exists('code',$arr) && $arr['code']==0){
				$this->msg[]= '删除 '.$uin.' 留言成功！';
			}elseif($arr['code']==-3000){
				$this->skeyzt=1;
				$this->msg[]='删除 '.$uin.' 留言失败！原因:'.$arr['message'];
			}elseif(array_key_exists('code',$arr)){
				$this->msg[]= '删除 '.$uin.' 留言失败！'.$arr['message'];
			}
		}else{
			$this->msg[]=  "未知错误，删除失败！";
		}
	}
	public function cpdelll($id,$uin){
		$url = 'http://mobile.qzone.qq.com/operation/operation_add?g_tk='.$this->cp_gtk;
		$post='opr_type=delugc&res_type=334&res_id='.$id.'&real_del=0&res_uin='.$this->uin.'&format=json';
		$data = $this->get_curl($url,$post,1,$this->cp_cookie);
		$arr=json_decode($data,true);
		if($arr){
			if(array_key_exists('code',$arr) && $arr['code']==0){
				$this->msg[]='删除 '.$uin.' 留言成功！';
			}elseif($arr['code']==-3000){
				$this->skeyzt=1;
				$this->msg[]='删除 '.$uin.' 留言失败！原因:'.$arr['message'];
			}else{
				$this->msg[]='删除 '.$uin.' 留言失败！原因:'.$arr['message'];
			}
		}else{
			$this->msg[]='未知错误，删除失败！可能留言已删尽';
		}
	}
	public function delll($do=0){
		if($do){
			if($liuyans=$this->getliuyan()){
				foreach($liuyans as $row) {
					$cellid=$row['id'];
					$uin=$row['uin'];
					if($cellid){
						$this->PCdelll($cellid,$uin);
					}
				}
			}
		}else{
			if($liuyans=$this->getll()){
				foreach($liuyans as $row) {
					$cellid=$row['id']['cellid'];
					$uin=$row['userinfo']['user']['uin'];
					if($cellid){
						$this->cpdelll($cellid,$uin);
					}
				}
			}
		}
	}
    public function pczhuanfa($con, $touin, $tid) {
        $url = 'http://taotao.qzone.qq.com/cgi-bin/emotion_cgi_forward_v6?g_tk=' . $this->pc_gtk;
        $post = 'qzreferrer=http://user.qzone.qq.com/' . $this->uin . '/infocenter&tid=' . $tid . '&t1_source=1&t1_uin=' . $touin . '&signin=0&con=' . urlencode($con) . '&with_cmt=0&fwdToWeibo=0&forward_source=2&code_version=1&format=fs&out_charset=UTF-8&hostuin='. $this->uin;
        $json = $this->get_curl($url, $post, 'http://user.qzone.qq.com/' . $this->uin . '/infocenter', $this->pc_cookie, 0, 1);
        $json = $this->getSubstr($json,"frameElement.callback(",");");
		if ($json) {
            $arr = json_decode($json, true);
            if (array_key_exists('code', $arr) && $arr['code'] == 0) {
                $this->shuotid = $arr[tid];
                $this->msg[] = $this->uin . '转发 ' . $touin . ' 说说成功[PC]';
            } elseif ($arr[code] == - 3000) {
                $this->skeyzt = 1;
                $this->error[] = $this->uin . '转发 ' . $touin . ' 说说失败[PC]！原因:' . $arr['message'];
            } elseif (array_key_exists('code', $arr)) {
                $this->error[] = $this->uin . '转发 ' . $touin . ' 说说失败[PC]！原因:' . $arr['message'];
            } else {
                $this->error[] = $this->uin . '转发 ' . $touin . ' 说说失败[PC]！原因' . $json;
            }
        } else {
            $this->error[] = $this->uin . '获取转发 ' . $touin . ' 说说结果失败[PC]';
        }
    }
    public function cpzhuanfa($con,$touin,$tid){
		$url='http://mobile.qzone.qq.com/operation/operation_add?g_tk='.$this->cp_gtk;
		$post='res_id='.$tid.'&res_uin='.$touin.'&format=json&reason='.urlencode($con).'&res_type=311&opr_type=forward&operate=1';
		$json=$this->get_curl($url,$post,1,$this->cp_cookie);
		if($json){
			$arr=json_decode($json,true);
			if(array_key_exists('code',$arr) && $arr['code']==0){
				$this->msg[]=$this->uin.'转发 '.$touin.' 说说成功[CP]';
			}elseif($arr['code']==-3000){
				$this->skeyzt=1;
				$this->msg[]=$this->uin.'转发 '.$touin.' 说说失败[CP]！原因:'.$arr['message'];
			}else{
				$this->msg[]=$this->uin.'转发 '.$touin.' 说说失败[CP]！原因:'.$arr['message'];
			}
		}else{
			$this->msg[]=$this->uin.'获取转发 '.$touin.' 说说结果失败[CP]';
		}
	}
    public function zhuanfa($do = 0, $uins = array() , $con = null) {
        if (count($uins) <= 1) {
            $uin = $uins[0];
            if ($shuos = $this->getmynew($uin)) {
                foreach ($shuos as $shuo) {
                    $cellid = $shuo['tid'];
                    if ($do) {
                        $this->pczhuanfa($con, $uin, $cellid);
                        if ($this->skeyzt) break;
                    } else {
                        $this->cpzhuanfa($con, $uin, $cellid);
                        if ($this->skeyzt) break;
                    }
                }
            }
        } else {
            global $getss;
            if ($shuos = $this->getnew($getss)) {
                foreach ($shuos as $shuo) {
                    $uin = $shuo['userinfo']['user']['uin'];
                    if (in_array($uin, $uins)) {
                        $cellid = $shuo['id']['cellid'];
                        if ($do) {
                            $this->pczhuanfa($con, $uin, $cellid);
                            if ($this->skeyzt) break;
                        } else {
                            $this->cpzhuanfa($con, $uin, $cellid);
                            if ($this->skeyzt) break;
                        }
                    }
                }
            }
        }
    }
    public function timeshuo($content='',$time,$richval=''){
		$url='http://taotao.qq.com/cgi-bin/emotion_cgi_publish_timershuoshuo_v6?g_tk='.$this->pc_gtk;
		$post='syn_tweet_verson=1&paramstr=1&pic_template=';
		if($richval){
			$post.='&richtype=1&richval=,'.$richval.'&pic_bo=bgBuAAAAAAADACU! bgBuAAAAAAADACU!';
		}
		$post.='&special_url=&subrichtype=1&con='.$content.'&feedversion=1&ver=1&ugc_right=1&to_tweet=0&to_sign=0&time='.$time.'&hostuin='.$this->uin.'&code_version=1&format=json';
		
		$json=$this->get_curl($url,$post,0,$this->pc_cookie);
		if($json){
			$arr=json_decode($json,true);
			if(@array_key_exists('code',$arr) && $arr['code']==0){
				$this->shuotid=$arr['tid'];
				$this->msg[]=$this->uin.' 发布定时说说成功[PC]';
			}elseif($arr['code']==-3000){
				$this->skeyzt=1;
				$this->msg[]=$this->uin.' 发布定时说说失败[PC]！原因:SID已失效，请更新SID';
			}else{
				$this->msg[]=$this->uin.' 发布定时说说失败[PC]！原因'.$json;
			}
		}else{
			$this->msg[]=$this->uin.' 获取发布定时说说结果失败[PC]';
		}
	}
	public function timedel(){
		$sendurl='http://taotao.qq.com/cgi-bin/emotion_cgi_pubnow_timershuoshuo_v6?g_tk='.$this->pc_gtk;
		$url='http://user.qzone.qq.com/q/taotao/cgi-bin/emotion_cgi_del_timershuoshuo_v6?g_tk='.$this->pc_gtk;
		$post='hostuin='.$this->uin.'&tid=1&time=1426176000&code_version=1&format=json&qzreferrer=http://user.qzone.qq.com/'.$this->uin.'/311';
	}
    public function cpqd($content='签到',$sealid='50001'){
		$url='http://mobile.qzone.qq.com/mood/publish_signin?g_tk='.$this->cp_gtk;
		$post='opr_type=publish_signin&res_uin='.$this->uin.'&content='.urlencode($content).'&lat=0&lon=0&lbsid=&seal_id='.$sealid.'&seal_proxy=&is_winphone=0&source_name=&format=json';
		$json=$this->get_curl($url,$post,0,$this->cp_cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]=$this->uin.' 签到成功[CP]';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 签到失败[CP]！原因:SKEY已失效，请更新SKEY';
		}elseif($arr['code']==-11210){
			$this->msg[]=$this->uin.' 签到失败[CP]！原因:'.$arr['message'];
		}elseif(@array_key_exists('code',$arr)){
			$this->msg[]=$this->uin.' 签到失败[CP]！原因:'.$arr['message'];
		}else{
			$this->msg[]=$this->uin.' 签到失败[CP]！原因:'.$json;
		}
	
	}
    public function pcqd($content = '', $sealid = '10761') {
        $url = 'http://snsapp.qzone.qq.com/cgi-bin/signin/checkin_cgi_publish?g_tk=' . $this->pc_gtk;
        $post = 'qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fapp%2Fcheckin_v4%2Fhtml%2Fcheckin.html&plattype=1&hostuin=' . $this->uin . '&seal_proxy=0a0255fd19940b1a0255fc35000b2a0a0c0b1c0b3800120129e31a0c1c2001300f4c0b0129e41a0c1c2001300f4c0b0129e51a0c1c2001300f4c0b0129e61a0c1c2001300f4c0b0129e71a0c1c2001300f4c0b0129e81a0c1c2001300f4c0b0129e91a0c1c2001300f4c0b012a031a0c1c2001300f4c0b012a041a0c1c2001300f4c0b012a051a0c1c2001300f4c0b012a061a0c1c2001300f4c0b012a071a0c1c2001300f4c0b012a081a0c1c2001300f4c0b012a091a0c1c2001300f4c0b012a1a1a0c1c200130074c0b012a1b1a0c1c200130074c0b012a1c1a0c1c200130074c0b012a1d1a0c1c200130074c0b&ttype=1&termtype=1&content=' . urlencode($content) . '&seal_id=' . $sealid . '&uin=' . $this->uin . '&time_for_qq_tips=' . time() . '&paramstr=1';
		//$post = 'qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fapp%2Fcheckin_v4%2Fhtml%2Fcheckin.html&plattype=1&hostuin=' . $this->uin . '&seal_proxy=&ttype=1&termtype=1&content=' . $content . '&seal_id=' . $sealid . '&uin=' . $this->uin . '&time_for_qq_tips='.time().'&paramstr=1';
		$get = $this->get_curl($url, $post, 'http://cnc.qzs.qq.com/qzone/app/checkin_v4/html/checkin.html', $this->pc_cookie);
		preg_match('/callback\((.*?)\)\; <\/script>/is',$get,$json);
        if ($json = $json[1]) {
            $arr = json_decode($json, true);
            $arr['feedinfo'] = '';
            if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
                $this->msg[] = $this->uin . ' 签到成功[PC]';
            } elseif ($arr['code'] == - 3000) {
                $this->skeyzt = 1;
                $this->error[] = $this->uin . ' 签到失败[PC]！原因:SKEY已失效，请更新SKEY';
            } elseif (@array_key_exists('code', $arr)) {
                $this->error[] = $this->uin . ' 签到失败[PC]！原因:' . $arr['message'];
            } else {
                $this->error[] = $this->uin . ' 签到失败[PC]！原因:' . $json;
            }
        } else {
            $this->error[] = $this->uin . ' 获取签到结果失败[PC]';
        }
    }
    public function qiandao($do = 0, $content = '签到', $sealid = 10319) {
        if ($do) {
            $this->pcqd($content, $sealid);
        } else {
            $this->cpqd($content, $sealid);
        }
    }
	public function cpshuo($content,$richval='',$sname='',$lon='',$lat=''){
		$url='http://mobile.qzone.qq.com/mood/publish_mood?g_tk='.$this->cp_gtk;
		$post='opr_type=publish_shuoshuo&res_uin='.$this->uin.'&content='.urlencode($content).'&richval='.$richval.'&lat='.$lat.'&lon='.$lon.'&lbsid=&issyncweibo=0&is_winphone=2&format=json&source_name='.$sname;
		$result=$this->get_curl($url,$post,1,$this->cp_cookie);
		$arr=json_decode($result,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]=$this->uin.' 发布说说成功[CP]';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 发布说说失败[CP]！原因:SID已失效，请更新SID';
		}elseif(@array_key_exists('code',$json)){
			$this->msg[]=$this->uin.' 发布说说失败[CP]！原因:'.$arr['message'];
		}else{
			$this->msg[]=$this->uin.' 发布说说失败[CP]！原因:'.$result;
		}
	}
	public function pcshuo($content,$richval=0){
		$url='http://taotao.qq.com/cgi-bin/emotion_cgi_publish_v6?g_tk='.$this->pc_gtk;
		$post='syn_tweet_verson=1&paramstr=1&pic_template=';
		if($richval){
			$post.="&richtype=1&richval=".$this->uin.",{$richval}&special_url=&subrichtype=1&pic_bo=uAE6AQAAAAABAKU!%09uAE6AQAAAAABAKU!";
		}else{
			$post.="&richtype=&richval=&special_url=";
		}
		$post.="&subrichtype=&con=".urlencode($content)."&feedversion=1&ver=1&ugc_right=1&to_tweet=0&to_sign=0&hostuin=".$this->uin."&code_version=1&format=json&qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F".$this->uin."%2F311";
		$json=$this->get_curl($url,$post,'http://user.qzone.qq.com/'.$this->uin.'/311',$this->pc_cookie);
		if($json){
			$arr=json_decode($json,true);
			$arr['feedinfo']='';
			if(@array_key_exists('code',$arr) && $arr['code']==0){
				$this->msg[]=$this->uin.' 发布说说成功[PC]';
			}elseif($arr['code']==-3000){
				$this->skeyzt=1;
				$this->msg[]=$this->uin.' 发布说说失败[PC]！原因:SKEY已失效，请更新SID';
			}elseif($arr['code']==-10045){
				$this->msg[]=$this->uin.' 发布说说失败[PC]！原因:'.$arr['message'];
			}elseif(@array_key_exists('code',$arr)){
				$this->msg[]=$this->uin.' 发布说说失败[PC]！原因:'.$arr['message'];
			}else{
				$this->msg[]=$this->uin.' 发布说说失败[PC]！原因'.$json;
			}
		}else{
			$this->msg[]=$this->uin.' 获取发布说说结果失败[PC]';
		}
	}
	public function shuo($do=0,$content,$image=0,$type=0,$sname=''){
		if(!$type && $image){
			if($pic=$this->get_curl($image)){
				$richval=$this->uploadimg($pic);
			}
		}else{
			$richval=$image;
		}
		if($do){
			return $this->pcshuo($content,$richval,$sname);
		}else{
			return $this->cpshuo($content,$richval,$sname);
		}
	
	}
    public function pcdel($cellid) {
        $url = 'http://taotao.qzone.qq.com/cgi-bin/emotion_cgi_delete_v6?g_tk=' . $this->gtk;
        $post = 'hostuin=' . $this->uin . '&tid=' . $cellid . '&t1_source=1&code_version=1&format=fs&qzreferrer=http://user.qzone.qq.com/' . $this->uin . '/311';
		$json = $this->getSubstr($json,"frameElement.callback(",");");
        $json = $this->get_curl($url, $post, 'http://user.qzone.qq.com/' . $this->uin . '/311', $this->cookie);
        if ($json) {
            $arr = json_decode($json, true);
            if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
                $this->msg[] = '删除说说' . $cellid . '成功[PC]';
            } elseif ($arr['code'] == - 3000) {
                $this->skeyzt = 1;
                $this->error[] = '删除说说' . $cellid . '失败[PC]！原因:SKEY已失效，请更新SKEY';
            } elseif (@array_key_exists('code', $json)) {
                $this->error[] = '删除说说' . $cellid . '失败[PC]！原因:' . $arr['message'];
            } else {
                $this->error[] = '删除说说' . $cellid . '失败[PC]！原因:' . $json;
            }
        } else {
            $this->error[] = $this->uin . '获取删除结果失败[PC]';
        }
    }
    public function cpdel($cellid) {
        $url = 'http://m.qzone.com/operation/operation_add?g_tk=' . $this->gtk;
        $post='format=json&opr_type=delugc&real_del=0&res_id='.$cellid.'&res_type=311&res_uin='.$this->uin;
        $json = $this->get_curl($url, $post, 'http://m.qzone.com/infocenter?g_ut=3&g_f=6676', $this->cp_cookie);
        $arr = json_decode($json, true);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $this->msg[] = '删除说说' . $cellid . '成功[CP]';
        } elseif ($arr['code'] == - 3000) {
            $this->skeyzt = 1;
            $this->error[] = '删除说说' . $cellid . '失败[CP]！原因:SKEY已失效，请更新SKEY';
        } elseif (@array_key_exists('code', $json)) {
            $this->error[] = '删除说说' . $cellid . '失败[CP]！原因:' . $arr['message'];
        } else {
            $this->error[] = '删除说说' . $cellid . '失败[CP]！原因:' . $json;
        }
    }
    public function shuodel($do = 0) {
        if ($shuos = $this->getmynew()) {
            //print_r($shuos);exit;
            foreach ($shuos as $shuo) {
                $cellid = $shuo['tid'];
                if ($do) {
                    $this->pcdel($cellid);
                    if ($this->skeyzt) break;
                } else {
                    $this->cpdel($cellid);
                }
            }
        }
    }
    public function cpReply($content,$touin,$tid) {
		$url="http://m.qzone.com/operation/publish_addcomment?g_tk=".$this->gtk;
		$post="res_id=".$tid."&res_uin=".$touin."&format=json&res_type=311&content=test&busi_param=4%3D%265%3Dhttp%253A%252F%252Fuser.qzone.qq.com%252F".$touin."%252Fmood%252F".$tid."%266%3Dhttp%253A%252F%252Fuser.qzone.qq.com%252F".$touin."%252Fmood%252F".$tid."%2623%3D2%2630%3D%2648%3D0%2652%3D%26-100%3Dappid%253A311%2Btypeid%253A0%2Bfeedtype%253A0%2Bhostuin%253A".$this->uin."%2Bfeedskey%253A".$tid."%2B&opr_type=addcomment";
        $json = $this->get_curl($url, $post, 1, $this->cookie);
        if ($json) {
            $arr = json_decode($json, true);
            if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
                $this->msg[] = '评论 ' . $uin . ' 的说说成功[CP]';
            } elseif ($arr['code'] == - 3000) {
                $this->skeyzt = 1;
                $this->error[] = '评论 ' . $uin . ' 的说说失败[CP]！原因:SKEY已失效，请更新SKEY';
            } elseif (@array_key_exists('message', $arr)) {
                $this->error[] = '评论 ' . $uin . ' 的说说失败[CP]！原因:' . $arr['message'];
            } else {
                $this->error[] = '评论 ' . $uin . ' 的说说失败[CP]！原因:' . $json;
            }
        } else {
            $this->error[] = '获取评论' . $uin . '的说说结果失败[CP]！';
        }
    }
    public function pcReply($content,$touin,$tid) {
        $url = 'http://taotao.qzone.qq.com/cgi-bin/emotion_cgi_addcomment_ugc?g_tk=' . $this->gtk;
		$post="qzreferrer=http%3a%2f%2fctc.qzs.qq.com%2fqzone%2fapp%2fmood_v6%2fhtml%2findex.html%23mood%26uin%3d".$this->uin."%26pfid%3d2%26qz_ver%3d8%26appcanvas%3d0%26qz_style%3d2%26params%3d%26entertime%3d".time()."%26canvastype%3d&uin=".$this->uin."&hostUin=".$this->uin."&topicId=".$ouin."_".$id."&commentUin=".$this->uin."&content=".$content."&richval=&richtype=&inCharset=&outCharset=&ref=&private=0&with_fwd=0&to_tweet=0&hostuin=".$this->uin."&code_version=1&format=fs";
        $json = $this->get_curl($url, $post,"http://ctc.qzs.qq.com/qzone/app/mood_v6/html/index.html", $this->cookie);
        if ($json) {
            $arr = json_decode($json, true);
            $arr['data']['feeds'] = '';
            if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
                $this->msg[] = '评论 ' . $uin . ' 的说说成功[PC]';
            } elseif ($arr['code'] == - 3000) {
                $this->skeyzt = 1;
                $this->error[] = '评论 ' . $uin . ' 的说说失败[PC]！原因:SKEY已失效，请更新SKEY';
            } elseif ($arr['code'] == - 10052 || $arr['code'] == - 10025) {
                $this->error[] = '评论 ' . $uin . ' 的说说失败[PC]！原因:' . $arr['message'];
            } elseif (@array_key_exists('message', $arr)) {
                $this->error[] = '评论 ' . $uin . ' 的说说失败[PC]！原因:' . $arr['message'];
            } else {
                $this->error[] = '评论 ' . $uin . ' 的说说失败[PC]！原因' . $json;
            }
        } else {
            $this->error[] = '获取评论结果失败[PC]';
        }
    }
    public function reply($do='pc',$content='') {
		if($shuos=$this->GetNewShuoList()){
			foreach($shuos as $shuo){
				if($shuo['iscomment']=false){
					if($do=='pc'){
						$this->pcReply($content,$shuo['touin'],$shuo['tid']);
					}elseif($do=='cp'){
						$this->cpReply($content,$shuo['touin'],$shuo['tid']);
					}
				}
			}
		}
    }
	public function GetNewShuoList($method=0,$ouin=0){
		//$method  0:pc 1:cp 2:mypc 3:mycp 4:toupc 5:toucp
		switch($method){
			case 0;
				$url="http://taotao.qq.com/cgi-bin/emotion_cgi_get_mix_v6?uin=".$this->uin."&inCharset=utf-8&outCharset=utf-8&hostUin=".$this->uin."&notice=0&sort=0&pos=0&num=20&cgi_host=http%3A%2F%2Ftaotao.qq.com%2Fcgi-bin%2Femotion_cgi_get_mix_v6&code_version=1&format=json&need_private_comment=1&g_tk=".$gtk;
				$json=get_curl($url,0,0,$cookie);
				$arr=json_decode($json,true);
				$arr_msglist=$arr['msglist'];
				$arr_comment=$arr['comment'];
				foreach($arr_msglist as $arr_msglist_key => $arr_msglist_value){
					$touin	=$arr_msglist_value['uin'];
					$tid	=$arr_msglist_value['tid'];
					$tid_tid='tid_'.$tid;
					if(array_key_exists($tid_tid, $arr_comment)){
						$arr_comment_tid=$arr_comment[$tid_tid];
						if(is_array($arr_comment_tid)){
							foreach($arr_comment_tid as $arr_comment_tid_key => $arr_comment_tid_value){
								if(in_array($this->uinn,$arr_comment_tid_value)){
									$iscomment=true;
								}else{
									$iscomment=false;
								}
							}
						}
					}
					$arr_list[$arr_msglist_key]['touin']=$touin;
					$arr_list[$arr_msglist_key]['tid']=$tid;
					$arr_list[$arr_msglist_key]['iscomment']=$iscomment;
				}
			break;
		}
		return $arr_list;
	}
    public function cplike($uin, $type, $uinkey, $curkey) {
        $post = 'opr_type=like&action=0&res_uin=' . $uin . '&res_type=' . $type . '&uin_key=' . $uinkey . '&cur_key=' . $curkey . '&format=json&sid=' . $this->sid;
        $url = 'http://wap.m.qzone.com/praise/like?g_tk=' . $this->gtk;
        $json = $this->get_curl($url, $post, 1, $this->cp_cookie);
        if ($json) {
            $arr = json_decode($json, true);
            if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
                $this->msg[] = '赞 ' . $uin . ' 的说说成功[CP]';
            } elseif ($arr[code] == - 3000) {
                $this->sidzt = 1;
                $this->error[] = '赞' . $uin . '的说说失败[CP]！原因:SID已失效，请更新SID';
            } elseif ($arr['code'] == - 11210) {
                $this->error[] = '赞 ' . $uin . ' 的说说失败[CP]！原因:' . $arr['message'];
            } elseif (@array_key_exists('message', $arr)) {
                $this->error[] = '赞 ' . $uin . ' 的说说失败[CP]！原因:' . $arr['message'];
            } else {
                $this->error[] = '赞 ' . $uin . ' 的说说失败[CP]！原因:' . $json;
            }
        } else {
            $this->error[] = '获取赞' . $uin . '的说说结果失败[CP]！';
        }
    }
    public function pclike($uin, $curkey, $uinkey, $from, $appid, $typeid, $abstime, $fid) {
        $post = 'qzreferrer=http://user.qzone.qq.com/' . $this->uin . '&opuin=' . $this->uin . '&unikey=' . $uinkey . '&curkey=' . $curkey . '&from=' . $from . '&appid=' . $appid . '&typeid=' . $typeid . '&abstime=' . $abstime . '&fid=' . $fid . '&active=0&fupdate=1';
		$url = 'http://w.cnc.qzone.qq.com/cgi-bin/likes/internal_dolike_app?g_tk=' . $this->pc_gtk;
        $get = $this->get_curl($url, $post, 'http://user.qzone.qq.com/' . $this->uin, $this->pc_cookie);
        preg_match('/callback\((.*?)\)\;/is', $get, $json);
        if ($json = $json[1]) {
            $arr = json_decode($json, true);
			//$this->msg[] =$arr;
            if ($arr['message'] == 'succ' || $arr['msg'] == 'succ') {
                $this->msg[] = '赞 ' . $uin . ' 的说说成功[PC]';
            } elseif ($arr['code'] == - 3000) {
                $this->skeyzt = 1;
                $this->error[] = '赞 ' . $uin . ' 的说说失败[PC]！原因:SKEY已失效，请更新SKEY';
            } elseif (@array_key_exists('message', $arr)) {
                $this->error[] = '赞 ' . $uin . ' 的说说失败[PC]！原因:' . $arr['message'];
            } else {
                $this->error[] = '赞 ' . $uin . ' 的说说失败[PC]！原因:' . $json;
            }
        } else {
			
            $this->error[] = $uin . ' 获取赞结果失败[PC]';
        }
    }
	public function pclike2($uin, $curkey, $uinkey, $from, $appid, $typeid, $abstime, $fid) {
        $post = 'qzreferrer=http://user.qzone.qq.com/' . $this->uin . '&opuin=' . $this->uin . '&unikey=' . $uinkey . '&curkey=' . $curkey . '&from=' . $from . '&appid=' . $appid . '&typeid=' . $typeid . '&abstime=' . $abstime . '&fid=' . $fid . '&active=0&fupdate=1';
        $url = 'http://w.qzone.qq.com/cgi-bin/likes/internal_dolike_app?g_tk=' . $this->pc_gtk;
        $get = $this->get_curl($url, $post, 'http://user.qzone.qq.com/' . $this->uin, $this->pc_cookie);
        preg_match('/callback\((.*?)\)\;/is', $get, $json);
        if ($json = $json[1]) {
            $arr = json_decode($json, true);
			//$this->msg[] =$arr;
            if ($arr['message'] == 'succ' || $arr['msg'] == 'succ') {
                $this->msg[] = '赞 ' . $uin . ' 的说说成功[PC]';
            } elseif ($arr['code'] == - 3000) {
                $this->skeyzt = 1;
                $this->error[] = '赞 ' . $uin . ' 的说说失败[PC]！原因:SKEY已失效，请更新SKEY';
            } elseif (@array_key_exists('message', $arr)) {
                $this->error[] = '赞 ' . $uin . ' 的说说失败[PC]！原因:' . $arr['message'];
            } else {
                $this->error[] = '赞 ' . $uin . ' 的说说失败[PC]！原因:' . $json;
            }
        } else {
            $this->msg[] = $uin . ' 获取赞结果失败[PC]';
        }
    }
    public function newpclike2($forbid = array()) {
        $url = 'http://ic2.s51.qzone.qq.com/cgi-bin/feeds/feeds3_html_more?format=json&begintime=' . time() . '&count=20&uin=' . $this->uin . '&g_tk=' . $this->gtk;
        $json = $this->get_curl($url, 0, 0, $this->pc_cookie);
        $arr = json_decode($json, true);
        if ($arr[code] == - 3000) {
            $this->skeyzt = 1;
            $this->error[] = $this->uin . '获取说说列表失败，原因:SKEY过期！[PC]';
        } else {
            $this->msg[] = $this->uin . '获取说说列表成功[PC]';
            $json = str_replace(array(
                "\\x22",
                "\\x3C",
                "\/"
            ) , array(
                '"',
                '<',
                '/'
            ) , $json);
            if (preg_match_all('/data\-unikey="([0-9A-Za-z\.\-\_\/\:]+)" data\-curkey="([0-9A-Za-z\.\-\_\/\:]+\/([0-9A-Za-z]+))" data\-clicklog="like" href="javascript\:\;"><i class="ui\-icon icon\-praise"><\/i>赞/iUs', $json, $arr)) {
                foreach ($arr[1] as $k => $row) {
                    preg_match('/\/(\d+)\//', $row, $match);
                    $touin = $match[1];
                    $type = 0;
                    $key = $arr[2][$k];
                    $fid = $arr[3][$k];
                    if ($row != $key) {
                        $type = 5;
                    }
                    if (!in_array($touin, $forbid)) $this->pclike($touin, $key, $row, 1, '311', $type, time() , $fid);
                    if ($this->skeyzt) break;
                }
            } else {
                $this->msg[] = $this->uin . '没有要赞的说说[PC]';
            }
        }
    }
    public function newpclike($forbid = array()) {
        $url = 'http://taotao.qq.com/cgi-bin/emotion_cgi_get_mix_v6?uin=' . $this->uin . '&inCharset=utf-8&outCharset=utf-8&hostUin=' . $this->uin . '&notice=0&sort=0&pos=0&num=20&code_version=1&format=json&need_private_comment=1&g_tk=' . $this->gtk;
        $json = $this->get_curl($url, 0, 1, $this->pc_cookie, 0, 1);
        if ($json) {
            $arr = json_decode($json, true);
            //print_r($arr);exit;
            if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
                $this->msg[] = '获取说说列表成功[PC]';
                foreach ($arr['msglist'] as $k => $row) {
                    $touin = $row['uin'];
                    $type = 0;
                    $key = 'http://user.qzone.qq.com/' . $touin . '/mood/' . $row['tid'];
                    $fid = $row['tid'];
                    if (!in_array($touin, $forbid)) $this->pclike($touin, $key, $key, 1, '311', $type, time() , $fid);
                    if ($this->skeyzt) break;
                }
            } elseif ($arr[code] == - 3000) {
                $this->skeyzt = 1;
                $this->error[] = '获取最新说说失败！[PC]原因:SKEY过期！';
            } elseif (@array_key_exists('message', $arr)) {
                $this->error[] = '获取最新说说失败！[PC]原因:' . $arr['message'];
            } else {
                $this->error[] = '获取最新说说失败！[PC]原因:' . $json;
            }
        } else {
            $this->error[] = '获取最新说说失败！[PC]';
        }
    }
    public function like($do = 0, $forbid = array()) {
        global $getss;
        if ($shuos = $this->getnew($getss)) {
			//print_r($shuos);exit();
			 //$this->msg[] =$getss;
            foreach ($shuos as $shuo) {
                $like = $shuo['like']['isliked'];
                if ($like == 0 && !in_array($shuo['userinfo']['user']['uin'], $forbid)) {
                    $appid = $shuo['comm']['appid'];
                    $typeid = $shuo['comm']['feedstype'];
                    $curkey = urlencode($shuo['comm']['curlikekey']);
                    $uinkey = urlencode($shuo['comm']['orglikekey']);
                    $uin = $shuo['userinfo']['user']['uin'];
                    $from = $shuo['userinfo']['user']['from'];
                    $abstime = $shuo['comm']['time'];
                    $cellid = $shuo['id']['cellid'];
                    if ($do==2) {
                        $this->pclike($uin, $curkey, $uinkey, $from, $appid, $typeid, $abstime, $cellid);
                        if ($this->skeyzt) break;
                    }elseif ($do==3) {
                        $this->pclike2($uin, $curkey, $uinkey, $from, $appid, $typeid, $abstime, $cellid);
                        if ($this->skeyzt) break;
                    }else {
                        $this->cplike($uin, $appid, $uinkey, $curkey);
                    }
                }
            }
        }
    }
	public function getnew() { //获取好友动态说说列表
		$url = "https://h5.qzone.qq.com/webapp/json/mqzone_feeds/getActiveFeeds?g_tk=" . $this->cp_gtk;
		$data = $this->get_curl($url, 0, 1, $this->cp_cookie);//触屏空间加载下一页接口无post
		$arr = json_decode($data, true);
        if (@array_key_exists('code', $arr) && $arr['code'] == 0) {
            $this->msg[] = '获取说说列表成功！';
            if (isset($arr['data']['vFeeds'])) return $arr['data']['vFeeds'];
            else return $arr['data']['feeds']['vFeeds'];
        } elseif (strpos($arr['message'], '务器繁忙')) {
            $this->error[] = '获取最新说说失败！原因:' . $arr['message'];
            return false;
        } elseif (strpos($arr['message'], '登录')) {
            $this->skeyzt = 1;
            $this->error[] = '获取最新说说失败！原因:' . $arr['message'];
            return false;
        } else {
            $this->error[] = '获取最新说说失败！原因:' . $arr['message'];
            return false;
        }
    }
    public function getmynew($uin=null){
		if(empty($uin))$uin=$this->uin;
		$url='http://sh.taotao.qq.com/cgi-bin/emotion_cgi_feedlist_v6?hostUin='.$uin.'&ftype=0&sort=0&pos=0&num=10&replynum=0&code_version=1&format=json&need_private_comment=1&g_tk='.$this->gtk;
		$json=$this->get_curl($url,0,0,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='获取说说列表成功！';
			return $arr['msglist'];
		}else{
			$this->msg[]='获取最新说说失败！原因:'.$arr['message'];
			return false;
		}
	}
    public function cpflower() {
        $url = 'http://m.qzone.com/flower/rattan/cgi-bin/touch_cgi_plant?t=0.90354' . time();
        //水滴
        $post = "act=rain&uin=" . $this->uin . "&newflower=2&fl=1&fupdate=1&format=json&sid=" . $this->sid;
        $this->get_curl($url, $post);
        //爱心
        $post = "act=love&uin=" . $this->uin . "&newflower=2&fl=1&fupdate=1&format=json&sid=" . $this->sid;
        $this->get_curl($url, $post);
        //阳光
        $post = "act=sun&uin=" . $this->uin . "&newflower=2&fl=1&fupdate=1&format=json&sid=" . $this->sid;
        $this->get_curl($url, $post);
        //树叶
        $post = "act=nutri&uin=" . $this->uin . "&newflower=2&fl=1&fupdate=1&format=json&sid=" . $this->sid;
        $this->get_curl($url, $post);
    }
	public function pcflower() {
        $url = 'http://flower.qzone.qq.com/fcg-bin/cgi_plant?g_tk=' . $this->gtk;
		
        //水滴
        $post = "outCharset=utf-8&fl=1&act=rain&g_tk=".$this->gtk."&newflower=1&fupdate=1&uin=".$this->uin."&format=json";
        $json=$this->get_curl($url, $post,0,$this->cookie);
		$arr = json_decode($json, true);
		$this->msg[] = '水滴：'.$arr[message].'[PC]';
		
        //爱心
        $post = "outCharset=utf-8&fl=1&act=love&g_tk=".$this->gtk."&newflower=1&fupdate=1&uin=".$this->uin."&format=json";
        $json=$this->get_curl($url, $post,0,$this->cookie);
		$arr = json_decode($json, true);
		$this->msg[] = '爱心:'.$arr[message].'[PC]';
		
        //阳光
        $post = "outCharset=utf-8&fl=1&act=sun&g_tk=".$this->gtk."&newflower=1&fupdate=1&uin=".$this->uin."&format=json";
        $json=$this->get_curl($url, $post,0,$this->cookie);
		$arr = json_decode($json, true);
		$this->msg[] = '阳光:'.$arr[message].'[PC]';
		
        //树叶
        $post = "outCharset=utf-8&fl=1&act=nutri&g_tk=".$this->gtk."&newflower=1&fupdate=1&uin=".$this->uin."&format=json";
        $json=$this->get_curl($url, $post,0,$this->cookie);
		$arr = json_decode($json, true);
		$this->msg[] = '树叶:'.$arr[message].'[PC]';
		
		$url = "http://flower.qzone.qq.com/cgi-bin/cgi_pickup_oldfruit?g_tk=" . $this->gtk;
		$post = "outCharset=utf-8&fupdate=1&g_tk=".$this->gtk."&mode=1&format=json";
        $json=$this->get_curl($url, $post,0,$this->cookie);
		$arr = json_decode($json, true);
		$this->msg[] = $arr[data][msg].':'.$arr['data']['count'].'[PC]';
    }
    public function get_curl($url, $post = 0, $referer = 1, $cookie = 0, $header = 0, $ua = 0, $nobaody = 0) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept:application/json";
        $httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
        $httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
        $httpheader[] = "Connection:close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
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
                curl_setopt($ch, CURLOPT_REFERER, 'http://m.qzone.com/infocenter?g_f=');
            } else {
                curl_setopt($ch, CURLOPT_REFERER, $referer);
            }
        }
        if ($ua) {
            curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36');
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.5 Mobile Safari/533.1');
        }
        if ($nobaody) {
            curl_setopt($ch, CURLOPT_NOBODY, 1);
        }
        //curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
        //curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiefile);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
    public function get_socket($url, $post = 0, $cookie = 0, $referer = 1) { //Author:消失的彩虹海
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

	private function getGTK($skey){
		$len = strlen($skey);
		$hash = 5381;
		for($i = 0; $i < $len; $i++){
			$hash += ((($hash << 5) & 0x7fffffff) + ord($skey[$i])) & 0x7fffffff;
			$hash&=0x7fffffff;
		}
		return $hash & 0x7fffffff;//计算g_tk
	}
    private function getGTK2($skey) {
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
	
    private function is_comment($uin, $arrs) {
        if ($arrs) {
            foreach ($arrs as $arr) {
                if ($arr['user']['uin'] == $uin) {
                    return false;
                    break;
                }
            }
            return true;
        } else {
            return true;
        }
    }
    private function array_str($array) {
        $str = '';
        if ($array[-100]) {
            $array100 = explode(' ', trim($array[-100]));
            $new100 = implode('+', $array100);
            $array[-100] = $new100;
        }
        foreach ($array as $k => $v) {
            if ($k != '-100') {
                $str = $str . $k . '=' . $v . '&';
            }
        }
        $str = urlencode($str . '-100=') . $array[-100] . '+';
        $str = str_replace(':', '%3A', $str);
        return $str;
    }
    public function uploadimg($image,$image_size=array()){
		$url='http://mobile.qzone.qq.com/up/cgi-bin/upload/cgi_upload_pic_v2?g_tk='.$this->cp_gtk;
        $post='picture='.urlencode(base64_encode($image)).'&base64=1&hd_height='.$image_size[1].'&hd_width='.$image_size[0].'&hd_quality=90&output_type=json&preupload=1&charset=utf-8&output_charset=utf-8&logintype=sid&Exif_CameraMaker=&Exif_CameraModel=&Exif_Time=&uin='.$this->uin;
        $data=preg_replace("/\s/","",$this->get_curl($url,$post,1,$this->cp_cookie,0,1));
		preg_match('/_Callback\((.*)\);/',$data,$arr);
		$data=json_decode($arr[1],true);
        if($data && array_key_exists('filemd5',$data)){
			$this->msg[]='图片上传成功！';
			$post='output_type=json&preupload=2&md5='.$data['filemd5'].'&filelen='.$data['filelen'].'&batchid='.time().rand(100000,999999).'&currnum=0&uploadNum=1&uploadtime='.time().'&uploadtype=1&upload_hd=0&albumtype=7&big_style=1&op_src=15003&charset=utf-8&output_charset=utf-8&uin='.$this->uin.'&logintype=sid&refer=shuoshuo';
			$img=preg_replace("/\s/","",$this->get_curl($url,$post,1,$this->cp_cookie,0,1));
			preg_match('/_Callback\(\[(.*)\]\);/',$img,$arr);
			$data=json_decode($arr[1],true);
            if($data && array_key_exists('picinfo',$data)){
				if($data[picinfo][albumid]!=""){
					$this->msg[]='图片信息获取成功！';
					return ''.$data['picinfo']['albumid'].','.$data['picinfo']['lloc'].','.$data['picinfo']['sloc'].','.$data['picinfo']['type'].','.$data['picinfo']['height'].','.$data['picinfo']['width'].',,,';
				}else{
					$this->msg[]='图片信息获取失败！';
					return;
				}
            }else{
                $this->msg[]='图片信息获取失败！';
                return;
            }
		}else{
			$this->msg[]='图片上传失败！原因：'.$data['msg'];
            return;
        }
	}
    public function scqd() {
        $url = "http://ebook.3g.qq.com/user/v3/normalLevel/sign?sid=" . $this->sid . "&g_ut=2";
        $this->get_curl($url);
        $this->msg[] = "书城签到成功~[3G]";
    }
    public function cpscqd() {//触屏书城签到	Author:微秒赞(洛绝尘)
		//$ua=''
        $url = "http://ubook.3g.qq.com/7/user/myMission?k1=" . $this->skey . "&u1=o". $this->uin;
        $json = $this->get_curl($url);
		$arr = json_decode($json, true);
		if ($arr['isLogin'] == true){
			if (array_key_exists('signMap', $arr) && $arr[signMap]['code'] == 0) $this->msg[] = $this->uin . ' 书城签到成功！这个月已经连续签到'.$arr[signMap]['continuousDays'].'天，得到了'.$arr[signMap]['growthVal'].'点成长值，'.$arr[signMap]['vipExp'].'点VIP经验，'.$arr[signMap]['growthValByVip'].'点VIP成长值[CP]';
			elseif ($arr[signMap]['code'] == -2) $this->msg[] = $this->uin . ' 书城今天已经签到过了！并且这个月已经连续签到'.$arr[signMap]['continuousDays'].'天，得到了'.$arr[signMap]['growthVal'].'点成长值，'.$arr[signMap]['vipExp'].'点VIP经验，'.$arr[signMap]['growthValByVip'].'点VIP成长值[CP]';
			else $this->error[] = $this->uin.'true似乎签到失败了！签到的返回信息是：'.$arr[signMap];
		}elseif($arr['isLogin'] == false) $this->msg[] = $this->uin.'没有登陆';
		else $this->error[] = $this->uin.'false似乎签到失败了！签到的返回信息是：'.$json;
    }
    public function gcw() //挂QQ宠物   Author:微秒赞(洛绝尘)
    {
        $url = 'http://qqpet.wapsns.3g.qq.com/qqpet/fcgi-bin/phone_pet?petid=0&cmd=1&g_f=16&B_UID=' . $this->uin . '&sid=' . $this->sid;
        $this->get_curl($url);
        $this->msg[] = $this->uin . '挂QQ宠物成功~[3G]';
    }
    public function cqqd() //超Q签到   Author:微秒赞(洛绝尘)
    {
        $url = 'http://sqq2.3g.qq.com/s?aid=bizp&pt=page&pc=signin&&sid=' . $this->sid;
        $this->get_curl($url);
        $this->msg[] = $this->uin . '超Q签到成功~[3G]';
    }
	public function cpcqqd(){
		$url = 'http://mq.qq.com/index_userSignIn.shtml?r=0.'.time().'2899';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('iRet',$arr) && $arr['iRet']==0){
			$this->msg[]='超级ＱＱ签到成功！';
		}elseif($arr['iRet']==-10){
			$this->skeyzt=1;
			$this->error[]='超级ＱＱ签到失败！SKEY已失效';
		}elseif($arr['iRet']==-11){
			$this->msg[]='超级ＱＱ已签到！';
		}else{
			$this->error[]='超级ＱＱ签到失败！'.$data;
		}
		$url = 'http://mq.qq.com/activity/badgepk10_qiandao.shtml?r=0.'.time().'2796';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$data = mb_convert_encoding($data, "UTF-8", "GB2312"); 
		if(strpos($data,'签到成功')!==false){
			$this->msg[]='勋章馆签到成功！';
		}elseif(strpos($data,'感谢参与')!==false){
			$this->msg[]='勋章馆今天已签到！';
		}elseif(strpos($data,'领取失败')!==false){
			$this->error[]='勋章馆签到失败！';
		}
	}
    public function gq() //挂QQ
    {
        $this->get_curl('http://pt.3g.qq.com/s?aid=nLogin3gqqbysid&3gqqsid=' . $this->sid);
        $this->get_curl('http://q32.3g.qq.com/g/s?sid=' . $this->sid . '&s=10&aid=chgStatus');
        $this->msg[] = $this->uin . '挂QQ成功~[3G]';
    }
    public function mcqd() //牧场签到   Author:微秒赞(洛绝尘)
    {
        $this->get_curl('http://mcapp.z.qq.com/mc/cgi-bin/wap_pasture_index?sid=' . $this->sid . '&uin=' . $this->uin . '&g_ut=2&source=qzone');
        $this->get_curl('http://mcapp.z.qq.com/mc/cgi-bin/wap_pasture_index?sid=' . $this->sid . '&uin=' . $this->uin . '&g_ut=1&signin=1&yellow=1&optflag=2&pid=0&v=1');
        $this->msg[] = $this->uin . '牧场签到成功~[3G]';
    }
    public function gamevipqd() //蓝钻签到   Author:微秒赞(洛绝尘)
    {
        $this->get_curl('http://mz.3g.qq.com/compaign/sign/signresult.jsp?fm=1&sid=' . $this->sid);
        $this->msg[] = $this->uin . '蓝钻签到成功~[3G]';
    }
    public function pcgamevipqd() //PC蓝钻签到   Author:微秒赞(洛绝尘)
    {
        $data = $this->get_curl('http://app.gamevip.qq.com/cgi-bin/gamevip_sign/GameVip_SignIn?g_tk=' . $this->gtk . '&_=' . time() , 0, 'http://gamevip.qq.com/sign_pop/sign_pop_v2.html?ADTAG=GW.XSY.TOP.SIGN&refer=', $this->cookie,0,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36');
        $arr = json_decode($data, true);
        if ($arr['FirstSignIn'] == 1) $this->msg[] = $this->uin . ' 蓝钻签到成功！[PC]您目前有' . $arr[LotteryTime0] . '抽奖机会和' . $arr[SignScore] . '积分！';
        elseif ($arr['FirstSignIn'] == 0) $this->msg[] = $this->uin . ' 您今天已经签到过了！[PC]您目前有' . $arr[LotteryTime0] . '抽奖机会和' . $arr[SignScore] . '积分！';
        elseif ($arr['result'] == 1000005) $this->msg[] = $this->uin . ' 您还没有登录！[PC]';
        else $this->error[] = $this->uin . ' 蓝钻签到签到失败！[PC]' . $arr['resultstr'];
        $data = $this->get_curl('http://app.gamevip.qq.com/cgi-bin/gamevip_sign/GameVip_Lottery?g_tk=' . $this->gtk . '&_=' . time() , 0, 'http://gamevip.qq.com/sign_pop/sign_pop_v2.html?ADTAG=GW.XSY.TOP.SIGN&refer=', $this->cookie.'DomainID=176;ts_uid=2275137330;gv_pvid=3105804530;ts_refer=ADTAGGW.XSY.TOP.SIGN;ts_last=gamevip.qq.com/sign_pop/sign_pop_v2.html;pt4_token=KXrGPm7s8qur05FYdlsXhw__;blue_show_once_1031601644=1;',0,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36');
        $arr = json_decode($data, true);
        if ($arr['itemName']) $this->msg[] = $this->uin . ' 恭喜您！蓝钻抽奖成功！[PC]奖品为：' . $arr['itemName'] . ',其cdkey是' . $arr[itemcdkey] . ',您还有' . $arr['LeftLotteryTime'] . '次抽奖机会！';
        elseif ($arr['result'] == 102) $this->msg[] = $this->uin . ' 您的免费蓝钻抽奖机会已用完！[PC]';
        elseif ($arr['result'] == 1000005) $this->msg[] = $this->uin . ' 您还没有登录！[PC]';
        else $this->error[] = $this->uin . ' 蓝钻抽奖失败！[PC]原因为' . $arr['resultstr'];
    }
    public function vipqd()
	{
		$url='http://vipfunc.qq.com/act/client_oz.php?action=client&g_tk='.$this->gtk2;
		$data=$this->get_curl($url,0,$url,$this->cookie);
		if($data=='{ret:0}')
			$this->msg[] = $this->uin.' 会员面板签到成功！';
		else
			$this->msg[] = $this->uin.' 会员面板签到失败！'.$json;

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

		$data=$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=56247&g_tk='.$this->gtk2.'&pvsrc=&fotmat=json&cache=0',0,'http://vip.qq.com/',$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0)
			$this->msg[] = $this->uin.' 三国之刃会员每周签到礼包领取成功！';
		elseif($arr['ret']==10601)
			$this->msg[] = $this->uin.' 三国之刃会员每周签到礼包已领取！';
		elseif($arr['ret']==40039)
			$this->msg[] = $this->uin.' 三国之刃会员每周签到礼包 不符合领取条件';
		elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[] = $this->uin.' 三国之刃会员每周签到礼包领取失败！SKEY过期';
		}else
			$this->msg[] = $this->uin.' 三国之刃会员每周签到礼包领取失败！'.$arr['msg'];

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

		$data=$this->get_curl('http://iyouxi.vip.qq.com/ams2.02.php?actid=23074&g_tk_type=1sid=&rand=0.8656469448520889&format=json&g_tk='.$this->gtk2,0,'http://vip.qq.com/',$this->cookie);
		$arr=json_decode($data,true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0)
			$this->msg[] = $this->uin.' 会员积分2签到成功！';
		elseif($arr['ret']==10601)
			$this->msg[] = $this->uin.' 会员积分2今天已经签到！';
		elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[] = $this->uin.' 会员积分2签到失败！SKEY过期';
		}else
			$this->msg[] = $this->uin.' 会员积分2签到失败！'.$arr['msg'];

		$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=27754&g_tk='.$this->gtk2.'&pvsrc=undefined&ozid=509656&vipid=MA20131223091753081&format=json&_='.time(),0,'http://vip.qq.com/',$this->cookie);//超级会员每月成长值
		$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=27755&g_tk='.$this->gtk2.'&pvsrc=undefined&ozid=509656&vipid=MA20131223091753081&format=json&_='.time(),0,'http://vip.qq.com/',$this->cookie);//超级会员每月积分
		$this->get_curl('http://iyouxi.vip.qq.com/ams3.0.php?g_tk='.$this->gtk2.'&actid=22249&_c=page&format=json&_='.time(),0,'http://vip.qq.com/',$this->cookie);//每周薪水积分
		$this->get_curl("http://iyouxi.vip.qq.com/jsonp.php?_c=page&actid=5474&isLoadUserInfo=1&format=json&g_tk=".$this->gtk2."&_=".time(),0,0,$this->cookie);
	}
    public function lzqd(){
		$url='http://share.music.qq.com/fcgi-bin/dmrp_activity/fcg_feedback_send_lottery.fcg?activeid=110&rnd='.time().'157&g_tk='.$this->gtk.'&uin='.$this->uin.'&hostUin=0&format=json&inCharset=UTF-8&outCharset=UTF-8&notice=0&platform=activity&needNewCode=1';
		$data = $this->get_curl($url,0,'http://y.qq.com/vip/fuliwo/index.html',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			if($arr['data']['alreadysend']==1)
				$this->msg[]='您今天已经签到过了！';
			else
				$this->msg[]='绿钻签到成功！';
		}elseif($arr['code']==-200017){
			$this->msg[]='你不是绿钻无法签到！';
		}else{
			$this->msg[]='绿钻签到失败！';
		}

		$url='http://share.music.qq.com/fcgi-bin/dmrp_activity/fcg_dmrp_get_present.fcg?activeid=73&rnd='.time().'029&g_tk='.$this->gtk.'&uin='.$this->uin.'&hostUin=0&format=json&inCharset=GB2312&outCharset=gb2312&notice=0&platform=activity&needNewCode=1';
		$data = $this->get_curl($url,0,'http://y.qq.com/vip/Installment_lv8/index.html',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='绿钻签到2成功！';
		}elseif($arr['code']==-200006){
			$this->msg[]='绿钻签到2今天已签到！';
		}else{
			$this->msg[]='绿钻签到2失败！';
		}

		$url='http://share.music.qq.com/fcgi-bin/dmrp_activity/fcg_dmrp_get_present.fcg?activeid=128&rnd='.time().'029&g_tk='.$this->gtk.'&uin='.$this->uin.'&hostUin=0&format=json&inCharset=GB2312&outCharset=gb2312&notice=0&platform=activity&needNewCode=1';
		$data = $this->get_curl($url,0,'http://y.qq.com/vip/Installment_lv8/index.html',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='绿钻签到3成功！';
		}elseif($arr['code']==-200006){
			$this->msg[]='绿钻签到3今天已签到！';
		}elseif($arr['code']==200004){
			$this->msg[]='你不是绿钻无法签到！';
		}else{
			$this->msg[]='绿钻签到3失败！';
		}

		$url='http://share.music.qq.com/fcgi-bin/dmrp_activity/fcg_dmrp_get_present.fcg?activeid=130&rnd='.time().'029&g_tk='.$this->gtk.'&uin='.$this->uin.'&hostUin=0&format=json&inCharset=GB2312&outCharset=gb2312&notice=0&platform=activity&needNewCode=1';
		$data = $this->get_curl($url,0,'http://y.qq.com/vip/Installment_lv8/index.html',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='绿钻签到4成功！';
		}elseif($arr['code']==-200006){
			$this->msg[]='绿钻签到4今天已签到！';
		}elseif($arr['code']==200004){
			$this->msg[]='你不是绿钻无法签到！';
		}else{
			$this->msg[]='绿钻签到4失败！';
		}

		$url='http://share.music.qq.com/fcgi-bin/dmrp_activity/fcg_dmrp_get_present.fcg?activeid=138&rnd='.time().'029&g_tk='.$this->gtk.'&uin='.$this->uin.'&hostUin=0&format=json&inCharset=GB2312&outCharset=gb2312&notice=0&platform=activity&needNewCode=1';
		$data = $this->get_curl($url,0,'http://y.qq.com/vip/Installment_lv8/index.html',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='绿钻签到5成功！';
		}elseif($arr['code']==-200006){
			$this->msg[]='绿钻签到5今天已签到！';
		}elseif($arr['code']==200004){
			$this->msg[]='你不是绿钻无法签到！';
		}else{
			$this->msg[]='绿钻签到5失败！';
		}

		$url='http://share.music.qq.com/fcgi-bin/dmrp_activity/fcg_dmrp_draw_lottery.fcg?activeid=159&rnd='.time().'482&g_tk='.$this->gtk.'&uin='.$this->uin.'&hostUin=0&format=json&inCharset=UTF-8&outCharset=UTF-8&notice=0&platform=activity&needNewCode=1';
		$data = $this->get_curl($url,0,'http://y.qq.com/vip/fuliwo/index.html',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='绿钻抽奖成功！';
		}elseif($arr['code']==200008){
			$this->msg[]='您没有抽奖机会！';
		}else{
			$this->msg[]='绿钻抽奖失败！';
		}
		
	}
    public function pqd(){
		$url="http://iyouxi.vip.qq.com/ams3.0.php?g_tk=".$this->gtk2."&pvsrc=102&ozid=511022&vipid=&actid=32961&format=json".time()."8777&cache=3654";
		$data = $this->get_curl($url,0,'http://youxi.vip.qq.com/m/wallet/activeday/index.html?_wv=3&pvsrc=102',$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='钱包签到成功！';
		}elseif($arr['ret']==37206){
			$this->msg[]='钱包签到失败！你没有绑定银行卡';
		}elseif($arr['ret']==10601){
			$this->msg[]='你今天已钱包签到！';
		}else{
			$this->msg[]='钱包签到失败！'.$arr['msg'];
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
			$this->error[]='黄钻签到失败！SKEY已失效';
		}elseif(array_key_exists('code',$arr)){
			$this->error[]='黄钻签到失败！'.$arr['message'];
		}else{
			$this->error[]='黄钻签到失败！'.$data;
		}

		$url = 'http://activity.qzone.qq.com/fcg-bin/fcg_qzact_count?g_tk='.$this->gtk.'&format=json&actid=101&uin='.$this->uin.'&_='.time().'3333';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$data = mb_convert_encoding($data, "UTF-8", "GB2312");
		$arr = json_decode($data, true);
		$count = $arr['data']['rule']['1001']['count'][1]['left'];
		while($count>0) {
			$url = 'http://activity.qzone.qq.com/fcg-bin/fcg_qzact_lottery?g_tk='.$this->gtk;
			$post = 'actid=101&ruleid=1001&format=json&uin='.$this->uin.'&g_tk='.$this->gtk.'&qzreferrer=http%3A%2F%2Fqzs.qq.com%2Fqzone%2Fqzact%2Fact%2Fhkhd%2Findex.html';
			$referer='http://qzs.qq.com/qzone/qzact/act/hkhd/index.html';
			$data = $this->get_curl($url,$post,$referer,$this->cookie);
			$arr = json_decode($data, true);
			if(array_key_exists('code',$arr) && $arr['code']==0){
				$this->msg[]='黄钻抽奖成功！';
			}elseif($arr['code']==-3000){
				$this->skeyzt=1;
				$this->error[]='黄钻抽奖失败！SKEY已失效';
			}elseif($arr['code']==-5004){
				$this->error[]='黄钻抽奖失败！抽奖机会已用完';
			}elseif(array_key_exists('code',$arr)){
				$this->error[]='黄钻抽奖失败！'.$arr['message'];
			}else{
				$this->error[]='黄钻抽奖失败！'.$data;
			}
			--$count;
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
			$this->error[]='大乐斗领礼包失败！SKEY已失效';
		}else{
			$this->error[]='大乐斗领礼包失败！'.$arr['msg'];
		}
	}
	public function weiyunqd(){
		$data = $this->get_curl('http://web2.cgi.weiyun.com/weiyun_activity.fcg?cmd%20=17004&g_tk='.$this->gtk.'&data=%7B%22req_header%22%3A%7B%22cmd%22%3A17004%2C%22appid%22%3A30013%2C%22version%22%3A2%2C%22major_version%22%3A2%7D%2C%22req_body%22%3A%7B%22ReqMsg_body%22%3A%7B%22weiyun.WeiyunDailySignInMsgReq_body%22%3A%7B%7D%7D%7D%7D&format=json',0,'http://www.weiyun.com/',$this->cookie);
		$json = json_decode($data, true);
		$arr = $json['rsp_header'];
		if(array_key_exists('retcode',$arr) && $arr['retcode']==0){
			$this->msg[]='微云签到成功！空间增加 '.$json['rsp_body']['RspMsg_body']['weiyun.WeiyunDailySignInMsgRsp_body']['add_space'].'MB';
		}elseif($arr['retcode']==190051){
			$this->skeyzt=1;
			$this->error[]='微云签到失败！SKEY已失效';
		}elseif(array_key_exists('retcode',$arr)){
			$this->error[]='微云签到失败！'.$arr['retmsg'];
		}else{
			$this->error[]='微云签到失败！'.$data;
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
			$this->error[]=$this->uin.' 粉钻签到失败！SKEY已失效';
		}else{
			$this->error[]=$this->uin.' 粉钻签到失败！'.$arr['msg'];
		}
	}
	public function videoqd(){
		$url='http://pay.video.qq.com/fcgi-bin/sign?low_login=1&uin='.$this->uin.'&otype=json&_t=2&g_tk='.$this->gtk.'&_='.time().'8906';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		preg_match('/QZOutputJson=(.*?)\;/is',$data,$json);
		$arr = json_decode($json[1], true);
		$arr = $arr['result'];
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]=$this->uin.' 好莱坞会员签到成功！';
		}elseif($arr['code']==-11){
			$this->skeyzt=1;
			$this->error[]=$this->uin.' 好莱坞会员签到失败！SKEY已失效';
		}elseif($arr['code']==500){
			$this->msg[]=$this->uin.' 你不是好莱坞会员，无法签到';
		}else{
			$this->error[]=$this->uin.' 好莱坞会员签到失败！'.$arr['msg'];
		}
	}
	public function nianvipcj(){
		$url='http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=28481&_record_gift_flow=1&g_tk='.$this->gtk2.'&pvsrc=undefined&ozid=&vipid=-&format=json&_='.time().'1516';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]=$this->uin.' 年费会员抽奖.类型:'.$arr['data']['actname'].' 结果:'.$arr['data']['op']['name'];
		}elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 年费会员抽奖失败！SKEY已失效';
		}elseif($arr['ret']==20102){
			$this->msg[]=$this->uin.' 你不是年费会员，无法抽奖';
		}elseif($arr['ret']==10601){
			$this->msg[]=$this->uin.' 你今天已经抽奖过了！类型:'.$arr['data']['actname'];
		}else{
			$this->msg[]=$this->uin.' 年费会员抽奖失败！'.$arr['msg'];
		}

		$url='http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=28311&_record_gift_flow=1&g_tk='.$this->gtk2.'&pvsrc=undefined&ozid=&vipid=-&format=json&_='.time().'6721';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]=$this->uin.' 年费会员抽奖.类型:'.$arr['data']['actname'].' 结果:'.$arr['data']['op']['name'];
		}elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 年费会员抽奖失败！SKEY已失效';
		}elseif($arr['ret']==20102){
			$this->msg[]=$this->uin.' 你不是年费会员，无法抽奖';
		}elseif($arr['ret']==10601){
			$this->msg[]=$this->uin.' 你今天已经抽奖过了！类型:'.$arr['data']['actname'];
		}else{
			$this->msg[]=$this->uin.' 年费会员抽奖失败！'.$arr['msg'];
		}

		$url='http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=28479&_record_gift_flow=1&g_tk='.$this->gtk2.'&pvsrc=undefined&ozid=&vipid=-&format=json&_='.time().'6721';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]=$this->uin.' 年费会员抽奖.类型:'.$arr['data']['actname'].' 结果:'.$arr['data']['op']['name'];
		}elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 年费会员抽奖失败！SKEY已失效';
		}elseif($arr['ret']==20102){
			$this->msg[]=$this->uin.' 你不是年费会员，无法抽奖';
		}elseif($arr['ret']==10601){
			$this->msg[]=$this->uin.' 你今天已经抽奖过了！类型:'.$arr['data']['actname'];
		}else{
			$this->msg[]=$this->uin.' 年费会员抽奖失败！'.$arr['msg'];
		}

		$url='http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=28485&_record_gift_flow=1&g_tk='.$this->gtk2.'&pvsrc=undefined&ozid=&vipid=-&format=json&_='.time().'6721';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]=$this->uin.' 年费会员抽奖.类型:'.$arr['data']['actname'].' 结果:'.$arr['data']['op']['name'];
		}elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 年费会员抽奖失败！SKEY已失效';
		}elseif($arr['ret']==20102){
			$this->msg[]=$this->uin.' 你不是年费会员，无法抽奖';
		}elseif($arr['ret']==10601){
			$this->msg[]=$this->uin.' 你今天已经抽奖过了！类型:'.$arr['data']['actname'];
		}else{
			$this->msg[]=$this->uin.' 年费会员抽奖失败！'.$arr['msg'];
		}

		$url='http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=28484&_record_gift_flow=1&g_tk='.$this->gtk2.'&pvsrc=undefined&ozid=&vipid=-&format=json&_='.time().'6721';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]=$this->uin.' 年费会员抽奖.类型:'.$arr['data']['actname'].' 结果:'.$arr['data']['op']['name'];
		}elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 年费会员抽奖失败！SKEY已失效';
		}elseif($arr['ret']==20102){
			$this->msg[]=$this->uin.' 你不是年费会员，无法抽奖';
		}elseif($arr['ret']==10601){
			$this->msg[]=$this->uin.' 你今天已经抽奖过了！类型:'.$arr['data']['actname'];
		}else{
			$this->msg[]=$this->uin.' 年费会员抽奖失败！'.$arr['msg'];
		}

		$url='http://iyouxi.vip.qq.com/ams3.0.php?_c=page&actid=28476&_record_gift_flow=1&g_tk='.$this->gtk2.'&pvsrc=undefined&ozid=&vipid=-&format=json&_='.time().'6721';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]=$this->uin.' 年费会员抽奖.类型:'.$arr['data']['actname'].' 结果:'.$arr['data']['op']['name'];
		}elseif($arr['ret']==10002){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 年费会员抽奖失败！SKEY已失效';
		}elseif($arr['ret']==20102){
			$this->msg[]=$this->uin.' 你不是年费会员，无法抽奖';
		}elseif($arr['ret']==10601){
			$this->msg[]=$this->uin.' 你今天已经抽奖过了！类型:'.$arr['data']['actname'];
		}else{
			$this->msg[]=$this->uin.' 年费会员抽奖失败！'.$arr['msg'];
		}
	}
	public function qqgjqd(){
		$url='http://c.pc.qq.com/fcgi-bin/signin?format=json&mood_id=129&checkin_date='.date("Y-m-d").'&remark=%E6%88%91%E5%B0%B1%E6%98%AF%E6%9D%A5%E7%AD%BE%E5%88%B0%E7%9A%84';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$data = mb_convert_encoding($data, "UTF-8", "GB2312");
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']=='suc'){
			if($arr['result']==2)
				$this->msg[]='QQ管家今天已签到！';
			else
				$this->msg[]='QQ管家签到成功！';
		}elseif(strpos($arr['ret'],'登录失败')!==false){
			$this->skeyzt=1;
			$this->error[]='QQ管家签到失败！SKEY已失效';
		}elseif(array_key_exists('ret',$arr)){
			$this->error[]='QQ管家签到失败！'.$arr['ret'];
		}else{
			$this->error[]='QQ管家签到失败！'.$data;
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
			$this->error[]='3366签到失败！SKEY已失效';
		}elseif(array_key_exists('result',$arr)){
			$this->error[]='3366签到失败！'.$arr['resultstr'];
		}else{
			$this->error[]='3366签到失败！'.$data;
		}
	}
	public function getSubstr($str, $leftStr, $rightStr){
		$left = strpos($str, $leftStr);
		//echo '左边:'.$left;
		$right = strpos($str, $rightStr,$left);
		//echo '<br>右边:'.$right;
		if($left < 0 or $right < $left) return '';
		return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
	}
}

