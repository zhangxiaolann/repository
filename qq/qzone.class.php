<?php
/*
 *QQ空间多功能操作类
 *Author：消失的彩虹海 & 快乐是福 & 云上的影子
*/
class qzone{
	public $msg;
	public $delend;
	private $qzonetoken = null;
	public function __construct($uin,$sid=null,$skey=null,$pskey=null,$superkey=null){
		$this->uin=$uin;
		$this->sid=$sid;
		$this->pskey=$pskey;
		$this->gtk=$this->getGTK($skey);
		$this->gtk2=$this->getGTK($pskey);
		if($pskey==null)
			$this->cookie='pt2gguin=o0'.$uin.'; uin=o0'.$uin.'; skey='.$skey.';';
		else{
			$this->cookie='pt2gguin=o0'.$uin.'; uin=o0'.$uin.'; skey='.$skey.'; p_skey='.$pskey.'; p_uin=o0'.$uin.';';
			$this->cookie2='pt2gguin=o0'.$uin.'; uin=o0'.$uin.'; skey='.$skey.';';
		}
	}
	function gift_pc($uin,$con){
		$url = "http://drift.qzone.qq.com/cgi-bin/sendgift?g_tk=".$this->gtk2;
		$post="qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fgift%2Fsend_list.html%23uin%3D%26type%3D%26itemid%3D%26birthday%3D%26birthdaytab%3D0%26lunarFlag%3D0%26source%3D%26nick%3D%26giveback%3D%26popupsrc%3D301%26open%3D%26is_fest_opt%3D0%26festId%3D%26html%3Dsend_list%26startTime%3D1441885072018%26timeoutId%3D2086&fupdate=1&random=0.06927570731103372&charset=utf-8&uin=".$this->uin."&targetuin={$uin}&source=0&giftid=106777&private=0&giveback=&qzoneflag=1&time=&timeflag=0&giftmessage=".urlencode($con)."&gifttype=0&gifttitle=%E8%AE%B8%E6%84%BF%E4%BA%91%E6%8A%B1%E6%9E%95+&traceid=&newadmin=1&birthdaytab=0&answerid=&arch=0&clicksrc=&click_src_v2=01%7C01%7C301%7C0556%7C0000%7C01";
		$json=$this->get_curl($url,$post,0,$this->cookie);
		preg_match('/frameElement\.callback\((.*?)\)\;/is',$json,$jsons);
		$json = $jsons[1];
		$arr = json_decode($json, true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]=$this->uin." 送礼物成功！";
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]=$this->uin." 未登录";
		}elseif($arr['code']==6){
			$this->msg[]=$this->uin." 收礼人设置了权限";
		}else{
			$this->msg[]=$this->uin." 送礼物失败！";
		}
	}
	function gift($uin, $con) {
        $url = "http://mobile.qzone.qq.com/gift/giftweb?g_tk=".$this->gtk2;
        $post = "action=3&itemid=108517&struin={$uin}&content=" . urlencode($con) . "&format=json&isprivate=0";
        $json = $this->get_curl($url, $post,1,$this->cookie);
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
	function liuyan_pc($uin,$con){
		$url = "http://m.qzone.qq.com/cgi-bin/new/add_msgb?g_tk=".$this->gtk2;
		$post = 'qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fmsgboard%2Fmsgbcanvas.html%23page%3D1&content='.urlencode($con).'&hostUin='.$uin.'&uin='.$this->uin.'&format=json&inCharset=utf-8&outCharset=utf-8&iNotice=1&ref=qzone&json=1&g_tk='.$this->gtk;
		$json=$this->get_curl($url,$post,'http://cnc.qzs.qq.com/qzone/msgboard/msgbcanvas.html',$this->cookie);
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
		$url = "http://mobile.qzone.qq.com/msgb/fcg_add_msg?g_tk=".$this->gtk2;
		$post = "res_uin={$uin}&format=json&content=".urlencode($con)."&opr_type=add_comment";
		$json=$this->get_curl($url,$post,1,$this->cookie);
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
	public function zyzan($uin){

		$randuin=rand(111111111,999999999);
		$url='http://w.cnc.qzone.qq.com/cgi-bin/likes/internal_dolike_app?g_tk='.$this->gtk2;
		$post='qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F'.$randuin.'&appid=7030&face=0&fupdate=1&from=1&query_count=200&format=json&opuin='.$this->uin.'&unikey=http%3A%2F%2Fuser.qzone.qq.com%2F'.$randuin.'&curkey=http%3A%2F%2Fuser.qzone.qq.com%2F'.$uin.'&zb_url=http%3A%2F%2Fi.gtimg.cn%2Fqzone%2Fspace_item%2Fpre%2F10%2F'.rand(10000,99999).'_1.gif';
		$json=$this->get_curl($url,$post,'http://user.qzone.qq.com/'.$uin,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]=$this->uin.' 赞 '.$uin.' 主页成功[PC]';
		}elseif($arr[code]==-3000){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 赞 '.$uin.' 主页失败[PC]！原因:'.$arr['message'];
		}elseif(array_key_exists('code',$arr)){
			$this->msg[]=$this->uin.' 赞 '.$uin.' 主页失败[PC]！原因:'.$arr['message'];
		}else{
			$this->msg[]=$this->uin.' 赞 '.$uin.' 主页失败[PC]！请10秒后再试';
		}
	}
	public function quantu(){
		if($shuos=$this->getnew()){
			$i=0;
			foreach($shuos as $shuo){
				$albumid='';$lloc='';
				if($shuo['original']['cell_pic']){
					$albumid=$shuo['original']['cell_pic']['albumid'];
					$lloc=$shuo['original']['cell_pic']['picdata'][0]['lloc'];
				}
				if($shuo['pic']){
					$albumid=$shuo['pic']['albumid'];
					$lloc=$shuo['pic']['picdata'][0]['lloc'];
				}
				if(!empty($albumid)) {
					$touin=$shuo['userinfo']['user']['uin'];
					$this->quantu_do($touin,$albumid,$lloc);
					if($this->skeyzt) break;
					++$i;if($i>=6)break;
					usleep(100000);
				}
			}
		}
	}
	public function quantu_do($touin,$albumid,$lloc){
		$url="http://app.photo.qq.com/cgi-bin/app/cgi_annotate_face?g_tk=".$this->gtk2;
		$post="format=json&uin={$this->uin}&hostUin=$touin&faUin={$this->uin}&faceid=&oper=0&albumid=$albumid&lloc=$lloc&facerect=10_10_50_50&extdata=&inCharset=GBK&outCharset=GBK&source=qzone&plat=qzone&facefrom=moodfloat&faceuin={$this->uin}&writeuin={$this->uin}&facealbumpage=quanren&qzreferrer=http://user.qzone.qq.com/{$this->uin}/infocenter?via=toolbar";
		$json=$this->get_curl($url,$post,'http://user.qzone.qq.com/'.$uin.'/infocenter?via=toolbar',$this->cookie);
		$json=mb_convert_encoding($json, "UTF-8", "gb2312");
		$arr=json_decode($json,true);
		if(!array_key_exists('code',$arr)){
			$this->msg[]="圈{$touin}的图{$albumid}失败，原因：获取结果失败！";
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]="圈{$touin}的图{$albumid}失败，原因：SKEY已失效！";
		}elseif($arr['code']==0){
			$this->msg[]="圈{$touin}的图{$albumid}成功";
		}else{
			$this->msg[]="圈{$touin}的图{$albumid}失败，原因：".$arr['message'];
		}
	}
	public function getll(){
		$url='http://mobile.qzone.qq.com/list?g_tk='.$this->gtk2.'&res_attach=&format=json&list_type=msg&action=0&res_uin='.$this->uin.'&count=20';
		$get=$this->get_curl($url,0,1,$this->cookie);
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
		$url='http://m.qzone.qq.com/cgi-bin/new/get_msgb?uin='.$this->uin.'&hostUin='.$this->uin.'&start=0&s=0.935081'.time().'&format=json&num=10&inCharset=utf-8&outCharset=utf-8&g_tk='.$this->gtk2;
		$json=$this->get_curl($url,0,'http://user.qzone.qq.com/',$this->cookie,0,$ua);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='获取留言列表成功！';
			return $arr['data']['commentList'];
		}else{
			$this->msg[]='获取留言列表失败！';
		}
	}
	public function PCdelly($idList,$uinList){
		$url = "http://m.qzone.qq.com/cgi-bin/new/del_msgb?g_tk=".$this->gtk2;
		$post="qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fmsgboard%2Fmsgbcanvas.html%23page%3D1&hostUin=".$this->uin."&idList=".urlencode($idList)."&uinList=".urlencode($uinList)."&format=json&iNotice=1&inCharset=utf-8&outCharset=utf-8&ref=qzone&json=1&g_tk=".$this->gtk2;
		$data = $this->get_curl($url,$post,'http://ctc.qzs.qq.com/qzone/msgboard/msgbcanvas.html',$this->cookie);
		$arr=json_decode($data,true);
		if($arr){
			if(array_key_exists('code',$arr) && $arr['code']==0){
				$this->msg[]= '删除 '.$uinList.' 留言成功！'.$arr['message'];
			}elseif($arr['code']==-3000){
				$this->skeyzt=1;
				$this->msg[]='删除 '.$uinList.' 留言失败！原因:'.$arr['message'];
			}elseif(array_key_exists('code',$arr)){
				$this->msg[]= '删除 '.$uinList.' 留言失败！'.$arr['message'];
			}
		}else{
			$this->msg[]=  "未知错误，删除失败！";
		}
	}
	public function cpdelly($id,$uin){
		$url = 'http://mobile.qzone.qq.com/operation/operation_add?g_tk='.$this->gtk2;
		$post='opr_type=delugc&res_type=334&res_id='.$id.'&real_del=0&res_uin='.$this->uin.'&format=json';
		$data = $this->get_curl($url,$post,1,$this->cookie);
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
	public function delly($do=0){
		if($liuyans=$this->getliuyan()){
			$idList='';
			$uinList='';
			foreach($liuyans as $row) {
				$idList.=$row['id'].',';
				$uinList.=$row['uin'].',';
				$exists=true;
			}
			if($exists){
				$idList = trim($idList, ',');
				$uinList = trim($uinList, ',');
				$this->PCdelly($idList,$uinList);
			}else{
				$this->delend = 1;
			}
		}
	}
	public function pczhuanfa($con,$touin,$tid){
		$url='http://taotao.qzone.qq.com/cgi-bin/emotion_cgi_forward_v6?g_tk='.$this->gtk2.'&qzonetoken='.$this->getToken('https://user.qzone.qq.com/'.$this->uin.'/311',1);
		$post='tid='.$tid.'&t1_source=1&t1_uin='.$touin.'&signin=0&con='.urlencode($con).'&with_cmt=0&fwdToWeibo=0&forward_source=2&code_version=1&format=json&out_charset=UTF-8&hostuin='.$this->uin.'&qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F'.$this->uin.'%2Finfocenter';
		$json=$this->get_curl($url,$post,'http://user.qzone.qq.com/'.$this->uin.'/infocenter',$this->cookie);
		if($json){
			$arr=json_decode($json,true);
			if(array_key_exists('code',$arr) && $arr['code']==0){
				$this->shuotid=$arr[tid];
				$this->msg[]=$this->uin.'转发 '.$touin.' 说说成功[PC]';
			}elseif($arr[code]==-3000){
				$this->skeyzt=1;
				$this->msg[]=$this->uin.'转发 '.$touin.' 说说失败[PC]！原因:'.$arr['message'];
			}elseif(array_key_exists('code',$arr)){
				$this->msg[]=$this->uin.'转发 '.$touin.' 说说失败[PC]！原因:'.$arr['message'];
			}else{
				$this->msg[]=$this->uin.'转发 '.$touin.' 说说失败[PC]！原因'.$json;
			}
		}else{
			$this->msg[]=$this->uin.'获取转发 '.$touin.' 说说结果失败[PC]';
		}
	}
	public function cpzhuanfa($con,$touin,$tid){
		$url='http://mobile.qzone.qq.com/operation/operation_add?g_tk='.$this->gtk2.'&qzonetoken='.$this->getToken();
		$post='res_id='.$tid.'&res_uin='.$touin.'&format=json&reason='.urlencode($con).'&res_type=311&opr_type=forward&operate=1';
		$json=$this->get_curl($url,$post,1,$this->cookie);
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
	public function zhuanfa($do=0,$uins=array(),$con=null){
		if(count($uins)==1 && $uins[0]!='') {
			$uin = $uins[0];
			if($shuos=$this->getmynew($uin)){
				$i=0;
				foreach($shuos as $shuo){
					$cellid=$shuo['tid'];
					if($do){
						$this->pczhuanfa($con,$uin,$cellid);
						if($this->skeyzt) break;
					}else{
						$this->cpzhuanfa($con,$uin,$cellid);
						if($this->skeyzt) break;
					}
					++$i;if($i>=3)break;
					usleep(100000);
				}
			}
		} elseif(count($uins)>1) {
			if($shuos=$this->getnew()){
				foreach($shuos as $shuo){
					$uin = $shuo['userinfo']['user']['uin'];
					if(in_array($uin,$uins)) {
						$cellid=$shuo['id']['cellid'];
						if($do){
							$this->pczhuanfa($con,$uin,$cellid);
							if($this->skeyzt) break;
						}else{
							$this->cpzhuanfa($con,$uin,$cellid);
							if($this->skeyzt) break;
						}
					}
				}
			}
		} else {
			if($shuos=$this->getnew()){			
				foreach($shuos as $shuo){
					$uin = $shuo['userinfo']['user']['uin'];
					$cellid=$shuo['id']['cellid'];
					if($do){
						$this->pczhuanfa($con,$uin,$cellid);
						if($this->skeyzt) break;
					}else{
						$this->cpzhuanfa($con,$uin,$cellid);
						if($this->skeyzt) break;
					}
				}
			}
		}
	}
	public function timeshuo($content='',$time,$richval=''){
		$url='http://taotao.qq.com/cgi-bin/emotion_cgi_publish_timershuoshuo_v6?g_tk='.$this->gtk2;		$post='syn_tweet_verson=1&paramstr=1&pic_template=';
		if($richval){
			$post.='&richtype=1&richval=,'.$richval.'&pic_bo=bgBuAAAAAAADACU! bgBuAAAAAAADACU!';
		}
		$post.='&special_url=&subrichtype=1&con='.$content.'&feedversion=1&ver=1&ugc_right=1&to_tweet=0&to_sign=0&time='.$time.'&hostuin='.$this->uin.'&code_version=1&format=json';
		
		$json=$this->get_curl($url,$post,0,$this->cookie);
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
		$sendurl='http://taotao.qq.com/cgi-bin/emotion_cgi_pubnow_timershuoshuo_v6?g_tk='.$this->gtk2;
		$url='http://user.qzone.qq.com/q/taotao/cgi-bin/emotion_cgi_del_timershuoshuo_v6?g_tk='.$this->gtk2;
		$post='hostuin='.$this->uin.'&tid=1&time=1426176000&code_version=1&format=json&qzreferrer=http://user.qzone.qq.com/'.$this->uin.'/311';
	}
	public function cpqd($content='签到',$sealid='50457'){
		$url='http://h5.qzone.qq.com/webapp/json/publishDiyMood/publishmood?g_tk='.$this->gtk2.'&qzonetoken='.$this->getToken('http://h5s.qzone.qq.com/checkin/index');
		$post='seal_id='.$sealid.'&frames=10&uin='.$this->uin.'&content='.urlencode($content).'&isWinPhone=2&lock_days=1&richval=&richtype=0&issynctoweibo=0&extend_info.activity_report=%23signin%23'.$sealid.'&source.subtype=33&format=json';
		$json=$this->get_curl($url,$post,0,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]=$this->uin.' 签到成功[CP]';
		}elseif($arr['ret']==-3000){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 签到失败[CP]！原因:SID已失效，请更新SID';
		}elseif($arr['code']==-3001){
			$this->msg[]=$this->uin.' 签到失败[CP]！原因:需要验证码';
		}elseif(@array_key_exists('ret',$arr)){
			$this->msg[]=$this->uin.' 签到失败[CP]！原因:'.$arr['msg'];
		}else{
			$this->msg[]=$this->uin.' 签到失败[CP]！原因:'.$json;
		}
	}
	public function pcqd($content='',$sealid='50001'){
		$url='http://snsapp.qzone.qq.com/cgi-bin/signin/checkin_cgi_publish?g_tk='.$this->gtk2;
		$post='qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fapp%2Fcheckin_v4%2Fhtml%2Fcheckin.html&plattype=1&hostuin='.$this->uin.'&seal_proxy=&ttype=1&termtype=1&content='.urlencode($content).'&seal_id='.$sealid.'&uin='.$this->uin.'&time_for_qq_tips='.time().'&paramstr=1';
		$get=$this->get_curl($url,$post,'http://user.qzone.qq.com/'.$this->uin.'/311',$this->cookie);
		preg_match('/callback\((.*?)\)\; <\/script>/is',$get,$json);
		if($json=$json[1]){
			$arr=json_decode($json,true);
			$arr['feedinfo']='';
			if(@array_key_exists('code',$arr) && $arr['code']==0){
				$this->msg[]=$this->uin.' 签到成功[PC]';
			}elseif($arr['code']==-3000){
				$this->skeyzt=1;
				$this->msg[]=$this->uin.' 签到失败[PC]！原因:SID已失效，请更新SID';
			}elseif(@array_key_exists('code',$arr)){
				$this->msg[]=$this->uin.' 签到失败[PC]！原因:'.$arr['message'];
			}else{
				$this->msg[]=$this->uin.' 签到失败[PC]！原因:'.$json;
			}
		}else{
			$this->msg[]=$this->uin.' 获取签到结果失败[PC]';
		}
		
	}
	public function qiandao($do=0,$content='签到',$sealid=50001){
		if($do){
			$this->pcqd($content,$sealid);
		}else{
			$this->cpqd($content,$sealid);
		}
	}

	public function cpshuo($content,$richval='',$sname='',$lon='',$lat=''){
		$url='http://mobile.qzone.qq.com/mood/publish_mood?qzonetoken='.$this->getToken().'&g_tk='.$this->gtk2;
		$post='opr_type=publish_shuoshuo&res_uin='.$this->uin.'&content='.urlencode($content).'&richval='.$richval.'&lat='.$lat.'&lon='.$lon.'&lbsid=&issyncweibo=0&is_winphone=2&format=json&source_name='.$sname;
		$result=$this->get_curl($url,$post,1,$this->cookie);
		$arr=json_decode($result,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]=$this->uin.' 发布说说成功[CP]';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]=$this->uin.' 发布说说失败[CP]！原因:SID已失效，请更新SID';
		}elseif($arr['code']==-3001){
			$this->msg[]=$this->uin.' 发布说说失败[CP]！原因:需要验证码';
		}elseif(@array_key_exists('code',$json)){
			$this->msg[]=$this->uin.' 发布说说失败[CP]！原因:'.$arr['message'];
		}else{
			$this->msg[]=$this->uin.' 发布说说失败[CP]！原因:'.$result;
		}
	}
	public function pcshuo($content,$richval=0){
		$url='http://taotao.qq.com/cgi-bin/emotion_cgi_publish_v6?g_tk='.$this->gtk2.'&qzonetoken='.$this->getToken('https://user.qzone.qq.com/'.$this->uin.'/311',1);
		$post='syn_tweet_verson=1&paramstr=1&pic_template=';
		if($richval){
			$post.="&richtype=1&richval=".$this->uin.",{$richval}&special_url=&subrichtype=1&pic_bo=uAE6AQAAAAABAKU!%09uAE6AQAAAAABAKU!";
		}else{
			$post.="&richtype=&richval=&special_url=";
		}
		$post.="&subrichtype=&con=".urlencode($content)."&feedversion=1&ver=1&ugc_right=1&to_tweet=0&to_sign=0&hostuin=".$this->uin."&code_version=1&format=json&qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F".$this->uin."%2F311";
		$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0";
		$json=$this->get_curl($url,$post,'http://user.qzone.qq.com/'.$this->uin.'/311',$this->cookie);
		if($json){
			$arr=json_decode($json,true);
			$arr['feedinfo']='';
			if(@array_key_exists('code',$arr) && $arr['code']==0){
				$this->msg[]=$this->uin.' 发布说说成功[PC]';
			}elseif($arr['code']==-3000){
				$this->skeyzt=1;
				$this->msg[]=$this->uin.' 发布说说失败[PC]！原因:SID已失效，请更新SID';
			}elseif($arr['code']==-3001){
				$this->msg[]=$this->uin.' 发布说说失败[PC]！原因:需要验证码';
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
	public function shuo($do=0,$content,$image=null,$sname='',$delete=false){
		if($delete){
			$url='http://sh.taotao.qq.com/cgi-bin/emotion_cgi_feedlist_v6?hostUin='.$this->uin.'&ftype=0&sort=0&pos=0&num=1&replynum=0&code_version=1&format=json&need_private_comment=1&g_tk='.$this->gtk;
			$json=$this->get_curl($url,0,0,$this->cookie);
			$arr=json_decode($json,true);
			//Print_r($arr);exit;
			if(@array_key_exists('code',$arr) && $arr['code']==0){
				$cellid=$arr['msglist'][0]['tid'];
				$this->pcdel($cellid);
			}else{
				$this->msg[]='删除上一条说说失败！原因:'.$arr['message'];
			}
		}
		if(!empty($image)){
			if($pic=$this->openu($image)){
				$image_size=getimagesize($image);
				$richval=$this->uploadimg($pic,$image_size);
			}
		}else{
			$richval=null;
		}
		if($do){
			$this->pcshuo($content,$richval);
		}else{
			$this->cpshuo($content,$richval,$sname);
		}
	}

	public function pcdel($cellid){
		if(strlen($cellid)==10){
			$url='https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzsharedelete?g_tk='.$this->gtk2;
			$post='notice=1&fupdate=1&platform=qzone&format=json&token='.$this->gtk2.'&owneruin='.$this->uin.'&itemid='.$cellid.'&entryuin='.$this->uin.'&ugcPlatform=300&qzreferrer=https%3A%2F%2Fsns.qzone.qq.com%2Fcgi-bin%2Fqzshare%2Fcgi_qzsharegetmylistbytype%3Fuin%3D'.$this->uin;
		}else{
			$url='http://taotao.qq.com/cgi-bin/emotion_cgi_delete_v6?g_tk='.$this->gtk2;
			$post='hostuin='.$this->uin.'&tid='.$cellid.'&t1_source=1&code_version=1&format=json&qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F'.$this->uin.'%2F311';
		}
		$json=$this->get_curl($url,$post,'http://user.qzone.qq.com/'.$this->uin.'/311',$this->cookie);
		if($json){
			$arr=json_decode($json,true);
			if(@array_key_exists('code',$arr) && $arr['code']==0){
				$this->msg[]='删除说说'.$cellid.'成功[PC]';
			}elseif($arr['code']==-3000){
				$this->skeyzt=1;
				$this->msg[]='删除说说'.$cellid.'失败[PC]！原因:SKEY已失效';
			}elseif(@array_key_exists('code',$json)){
				$this->msg[]='删除说说'.$cellid.'失败[PC]！原因:'.$arr['message'];
			}else{
				$this->msg[]='删除说说'.$cellid.'失败[PC]！原因:'.$json;
			}
		}else{
			$this->msg[]=$this->uin.'获取删除结果失败[PC]';
		}
	}
	public function cpdel($cellid,$appid){
		$url='http://mobile.qzone.qq.com/operation/operation_add?g_tk='.$this->gtk2;
		$post='opr_type=delugc&res_type='.$appid.'&res_id='.$cellid.'&real_del=0&res_uin='.$this->uin.'&format=json';
		$json=$this->get_curl($url,$post,1,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='删除说说'.$cellid.'成功[CP]';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='删除说说'.$cellid.'失败[CP]！原因:SID已失效，请更新SID';
		}elseif(@array_key_exists('code',$json)){
			$this->msg[]='删除说说'.$cellid.'失败[CP]！原因:'.$arr['message'];
		}else{
			$this->msg[]='删除说说'.$cellid.'失败[CP]！原因:'.$json;
		}
	}
	public function shuodel($do=0){
		if($shuos=$this->getnew('my')){
			//print_r($shuos);exit;
			$i=0;
			foreach($shuos as $shuo){
				$cellid=$shuo['id']['cellid'];
				$this->pcdel($cellid);
				if($this->skeyzt) break;
				++$i;if($i>=10)break;
			}
		}
	}
	public function cpreply($content,$uin,$cellid,$type,$param){
		$post='res_id='.$cellid.'&res_uin='.$uin.'&format=json&res_type='.$type.'&content='.urlencode($content).'&busi_param='.$param.'&opr_type=addcomment';
		$url='http://mobile.qzone.qq.com/operation/publish_addcomment?qzonetoken='.$this->getToken().'&g_tk='.$this->gtk2;
		$json=$this->get_curl($url,$post,1,$this->cookie);
		if($json){
			$arr=json_decode($json,true);
			if(@array_key_exists('code',$arr) && $arr['code']==0){
				$this->msg[]='评论 '.$uin.' 的说说成功[CP]';
			}elseif($arr['code']==-3000){
				$this->skeyzts=1;
				$this->msg[]='评论 '.$uin.' 的说说失败[CP]！原因:SID已失效，请更新SID';
			}elseif(@array_key_exists('message',$arr)){
				$this->msg[]='评论 '.$uin.' 的说说失败[CP]！原因:'.$arr['message'];
			}else{
				$this->msg[]='评论 '.$uin.' 的说说失败[CP]！原因:'.$json;
			}
		}else{
			$this->msg[]='获取评论'.$uin.'的说说结果失败[CP]！';
		}
	}
	public function pcreply($content,$uin,$cellid,$from,$richval=null){
		$post='topicId='.$uin.'_'.$cellid.'__'.$from.'&feedsType=100&inCharset=utf-8&outCharset=utf-8&plat=qzone&source=ic&hostUin='.$uin.'&isSignIn=&platformid=52&uin='.$this->uin.'&format=json&ref=feeds&content='.urlencode($content);
		if($richval){
			$post.='&richval='.urlencode($richval).'&richtype=1';
		}else{
			$post.='&richval=&richtype=';
		}
		$post.='&private=0&paramstr=1&qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F'.$this->uin;
		$url='http://h5.qzone.qq.com/proxy/domain/taotao.qzone.qq.com/cgi-bin/emotion_cgi_re_feeds?qzonetoken='.$this->getToken('https://user.qzone.qq.com/'.$this->uin.'/311',1).'&g_tk='.$this->gtk2;
		$json=$this->get_curl($url,$post,'http://user.qzone.qq.com/'.$this->uin,$this->cookie);
		if($json){
			$arr=json_decode($json,true);
			$arr['data']['feeds']='';
			if(@array_key_exists('code',$arr) && $arr['code']==0){
				$this->msg[]='评论 '.$uin.' 的说说成功[PC]';
			}elseif($arr['code']==-3000){
				$this->skeyzts=1;
				$this->msg[]='评论 '.$uin.' 的说说失败[PC]！原因:SID已失效，请更新SID';
			}elseif($arr['code']==-3001){
				$this->msg[]='评论 '.$uin.' 的说说失败[PC]！原因:需要验证码';
			}elseif(@array_key_exists('message',$arr)){
				$this->msg[]='评论 '.$uin.' 的说说失败[PC]！原因:'.$arr['message'];
			}else{
				$this->msg[]='评论 '.$uin.' 的说说失败[PC]！原因'.$json;
			}
		}else{
			$this->msg[]='获取评论结果失败[PC]';
		}
	}
	public function reply($do=0,$contents=array(),$forbid=array(),$only=array(),$sleep=0,$image=null){
		if($shuos=$this->getnew()){
			$i=0;$e=0;
			foreach($shuos as $shuo){
				$uin=$shuo['userinfo']['user']['uin'];
				if($this->is_comment($this->uin,$shuo['comment']['comments']) && !in_array($uin,$forbid)){
					$appid=$shuo['comm']['appid'];
					if($appid!=311)continue;
					$typeid=$shuo['comm']['feedstype'];
					$curkey=urlencode($shuo['comm']['curlikekey']);
					$uinkey=urlencode($shuo['comm']['orglikekey']);
					$from=$shuo['userinfo']['user']['from'];
					$cellid=$shuo['id']['cellid'];
					if($only[0]!='' && !in_array($uin,$only))continue;
					$content=$contents[array_rand($contents,1)];
					if($do){
						$this->pcreply($content,$uin,$cellid,$from,$image);

					}else{
						$param=$this->array_str($shuo['operation']['busi_param']);
						$this->cpreply($content,$uin,$cellid,$appid,$param);
					}
					if($this->skeyzts) ++$e;
					$this->skeyzts=false;
					++$i;if($i>=6)break;
					if($sleep)sleep($sleep);
					else usleep(100000);
				}
			}
			if($e>1 && $e==$i)$this->skeyzt=1;
			if($i==0)$this->msg[] = '没有要评论的说说';
		}
	}
	public function cplike($uin,$appid,$unikey,$curkey){
		$post='opuin='.$uin.'&unikey='.$unikey.'&curkey='.$curkey.'&appid='.$appid.'&opr_type=like&format=purejson';
		$url='http://h5.qzone.qq.com/proxy/domain/w.qzone.qq.com/cgi-bin/likes/internal_dolike_app?g_tk='.$this->gtk2;
		$json=$this->get_curl($url,$post,1,$this->cookie);
		if($json){
			$arr=json_decode($json,true);
			if(@array_key_exists('ret',$arr) && $arr['ret']==0){
				$this->msg[]='赞 '.$uin.' 的说说成功[CP]';
			}elseif($arr['ret']==-3000){
				$this->skeyzts=1;
				$this->msg[]='赞'.$uin.'的说说失败[CP]！原因:SKEY已失效';
			}elseif(@array_key_exists('msg',$arr)){
				$this->msg[]='赞 '.$uin.' 的说说失败[CP]！原因:'.$arr['msg'];
			}else{
				$this->msg[]='赞 '.$uin.' 的说说失败[CP]！原因:'.$json;
			}
		}else{
			$this->msg[]='获取赞'.$uin.'的说说结果失败[CP]！';
		}
	}
	public function pclike($uin,$curkey,$unikey,$from,$appid,$typeid,$abstime,$fid){
		$post='qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F'.$this->uin.'&opuin='.$this->uin.'&unikey='.$unikey.'&curkey='.$curkey.'&from='.$from.'&appid='.$appid.'&typeid='.$typeid.'&abstime='.$abstime.'&fid='.$fid.'&active=0&fupdate=1';
		$randserver = array('w.cnc.','w.');
		$url='http://'.$randserver[rand(0,1)].'qzone.qq.com/cgi-bin/likes/internal_dolike_app?g_tk='.$this->gtk2;
		$ua='Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.69 Safari/537.36 QQBrowser/9.1.4060.400';
		$get=$this->get_curl($url,$post,'http://user.qzone.qq.com/'.$this->uin,$this->cookie,0,$ua);
		preg_match('/callback\((.*?)\)\;/is',$get,$json);
		if($json=$json[1]){
			$arr=json_decode($json,true);
			if($arr['message']=='succ' || $arr['msg']=='succ'){
				$this->msg[]='赞 '.$uin.' 的说说成功[PC]';
			}elseif($arr['code']==-3000){
				$this->skeyzts=1;
				$this->msg[]='赞 '.$uin.' 的说说失败[PC]！原因:SKEY已失效';
			}elseif(@array_key_exists('message',$arr)){
				$this->msg[]='赞 '.$uin.' 的说说失败[PC]！原因:'.$arr['message'];
			}else{
				$this->msg[]='赞 '.$uin.' 的说说失败[PC]！原因:'.$json;
			}
		}else{
			$this->msg[]='获取赞'.$uin.'的说说结果失败[PC]';
		}
	}
	public function newpclike($forbid=array(),$sleep=0)
	{
		$randserver=array('s1','s2','s5','s6','s7','s8','s11','s12');
		$url = 'http://ic2.'.$randserver[rand(0,7)].'.qzone.qq.com/cgi-bin/feeds/feeds3_html_more?format=json&begintime=' . time() . '&count=20&uin=' . $this->uin . '&g_tk=' . $this->gtk2;
		$json = $this->get_curl($url, 0, 'http://user.qzone.qq.com/'.$this->uin, $this->cookie);
		$arr = json_decode($json, true);

		if ($arr[code] == -3000) {
			$this->skeyzt = 1;
			$this->msg[] = $this->uin . '获取说说列表失败，原因:SKEY过期！[PC]';
		}
		elseif(strpos($json,'"code":0')) {
			$this->msg[] = $this->uin . '获取说说列表成功[PC]';
			$json = str_replace(array("\\x22", "\\x3C", "\/"), array('"', '<', '/'), $json);
			$i=0;

			if(preg_match_all('/appid:\'(\d+)\',typeid:\'(\d+)\',key:\'([0-9A-Za-z]+)\'.*?,abstime:\'(\d+)\'.*?,uin:\'(\d+)\'.*?,html:\'(.*?)\'/i', $json, $arr)) {
				foreach ($arr[1] as $k => $row ) {
					if(preg_match('/data\-unikey="([0-9A-Za-z\.\-\_\/\:]+)" data\-curkey="([0-9A-Za-z\.\-\_\/\:]+)" data\-clicklog="like" href="javascript\:\;"><i class="fui\-icon icon\-op\-praise"><\/i>/i',$arr[6][$k],$match)){
						$appid = $arr[1][$k];
						$typeid = $arr[2][$k];
						$fid = $arr[3][$k];
						$abstime = $arr[4][$k];
						$touin = $arr[5][$k];
						$unikey = urlencode($match[1]);
						$curkey = urlencode($match[2]);

						if(!in_array($touin,$forbid))
						$this->pclike($touin, $curkey, $unikey, 1, $appid, $typeid, $abstime, $fid);
						++$i;if($i>=8)break;

						if ($this->skeyzt) break;
						if($sleep)sleep($sleep);
						else usleep(100000);
					}
				}
			}
			else {
				$this->msg[] = $this->uin . '没有要赞的说说[PC]';
			}
		}else{
			//$this->msg[] = $this->uin . '没有要赞的说说[PC]';
			$this->like(1,$forbid,$sleep);
		}
	}
	public function like($do=0,$forbid=array(),$sleep=0){
		if ($do==2) {
			$this->newpclike($forbid,$sleep);
		}
		elseif($shuos=$this->getnew()){
			$i=0;$e=0;
			foreach($shuos as $shuo){
				$like=$shuo['like']['isliked'];
				if($like==0 && !in_array($shuo['userinfo']['user']['uin'],$forbid)){
					$appid=$shuo['comm']['appid'];
					$typeid=$shuo['comm']['feedstype'];
					$curkey=urlencode($shuo['comm']['curlikekey']);
					$uinkey=urlencode($shuo['comm']['orglikekey']);
					$uin=$shuo['userinfo']['user']['uin'];
					$from=$shuo['userinfo']['user']['from'];
					$abstime=$shuo['comm']['time'];
					$cellid=$shuo['id']['cellid'];
					if($do){

						$this->pclike($uin,$curkey,$uinkey,$from,$appid,$typeid,$abstime,$cellid);
					}else{
						$this->cplike($uin,$appid,$uinkey,$curkey);
					}
					if($this->skeyzts) ++$e;
					$this->skeyzts=false;
					++$i;if($i>=8)break;
					if($sleep)sleep($sleep);
					else usleep(100000);
				}
			}
			if($e>1 && $e==$i)$this->skeyzt=1;
			if($i==0)$this->msg[] = '没有要赞的说说';
		}
	}



	public function getnew($do='',$time=0){
		if($do=='my'){
			$url="http://mobile.qzone.qq.com/list?g_tk=".$this->gtk2."&res_attach=&format=json&list_type=shuoshuo&action=0&res_uin=".$this->uin."&count=20";
			$json=$this->get_curl($url,0,1,$this->cookie);
		}else{
			$url="http://h5.qzone.qq.com/webapp/json/mqzone_feeds/getActiveFeeds?g_tk=".$this->gtk2;
			$post='res_type=0&res_attach=&refresh_type=2&format=json&attach_info=';
			$json=$this->get_curl($url,$post,1,$this->cookie);
		}
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='获取说说列表成功！';
			if(isset($arr['data']['vFeeds']))
				return $arr['data']['vFeeds'];
			else
				return $arr['data']['feeds']['vFeeds'];
		}elseif(strpos($arr['message'],'务器繁忙')){
			$this->msg[]='获取最新说说失败！原因:'.$arr['message'];
			return false;
		}elseif(strpos($arr['message'],'登录') || strpos($arr['message'],'统繁忙')){
			if($time==0){
				return $this->getnew($do,1);
			}else{
			$this->skeyzt=1;
			$this->msg[]='获取最新说说失败！原因:SKEY已失效';
			return false;
			}
		}else{
			$this->msg[]='获取最新说说失败！原因:'.$arr['message'];
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

	public function flower(){
		$url = 'http://flower.qzone.qq.com/fcg-bin/cgi_plant?g_tk='.$this->gtk2;
		$post = 'fl=1&fupdate=1&act=rain&uin='.$this->uin.'&newflower=1&outCharset=utf%2D8&g%5Ftk='.$this->gtk2.'&format=json';
		$json=$this->get_curl($url,$post,$url,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='浇水成功！';
		}elseif($arr['code']==-6002){
			$this->msg[]='今天浇过水啦！';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='浇水失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='浇水失败！'.$arr['message'];
		}

		$post = 'fl=1&fupdate=1&act=love&uin='.$this->uin.'&newflower=1&outCharset=utf%2D8&g%5Ftk='.$this->gtk2.'&format=json';
		$json=$this->get_curl($url,$post,$url,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='修剪成功！';
		}elseif($arr['code']==-6002){
			$this->msg[]='今天修剪过啦！';
		}elseif($arr['code']==-6007){
			$this->msg[]='您的爱心值今天已达到上限！';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='修剪失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='修剪失败！'.$arr['message'];
		}

		$post = 'fl=1&fupdate=1&act=sun&uin='.$this->uin.'&newflower=1&outCharset=utf%2D8&g%5Ftk='.$this->gtk2.'&format=json';
		$json=$this->get_curl($url,$post,$url,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='光照成功！';
		}elseif($arr['code']==-6002){
			$this->msg[]='今天日照过啦！';
		}elseif($arr['code']==-6007){
			$this->msg[]='您的阳光值今天已达到上限！';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='光照失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='光照失败！'.$arr['message'];
		}

		$post = 'fl=1&fupdate=1&act=nutri&uin='.$this->uin.'&newflower=1&outCharset=utf%2D8&g%5Ftk='.$this->gtk2.'&format=json';
		$json=$this->get_curl($url,$post,$url,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='施肥成功！';
		}elseif($arr['code']==-6005){
			$this->msg[]='暂不能施肥！';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='施肥失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='施肥失败！'.$arr['message'];
		}

		$url = 'http://flower.qzone.qq.com/cgi-bin/cgi_pickup_oldfruit?g_tk='.$this->gtk2;
		$post = 'mode=1&g%5Ftk='.$this->gtk2.'&outCharset=utf%2D8&fupdate=1&format=json';
		$json=$this->get_curl($url,$post,$url,$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='兑换神奇肥料成功！';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='兑换神奇肥料失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='兑换神奇肥料失败！'.$arr['message'];
		}

		$url = 'http://flower.qzone.qq.com/cgi-bin/cgi_show_userprop?p=0.37835920085705778&fupdate=1&format=json&g_tk='.$this->gtk2;
		$json=$this->get_curl($url,0,$url,$this->cookie);
		$json=mb_convert_encoding($json, "UTF-8", "gb2312");
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			foreach($arr['data']['prop'] as $row){
				$url = 'http://flower.qzone.qq.com/cgi-bin/cgi_exchange_prop?g_tk='.$this->gtk2;
				$post = 'op_uin='.$this->uin.'&propid='.$row['propid'].'&num=1&p=0.'.time().'74383277&qzreferrer=http%3A%2F%2Frc.qzone.qq.com%2Fappstore%2Fdailycoupon%3Ffrom%3Dappstore.myInfoBoxBtn&fupdate=1&format=json';
				$this->get_curl($url,$post,'http://ctc.qzs.qq.com/qzone/client/photo/swf/RareFlower/FlowerVineLite.swf',$this->cookie);

				$url = 'http://flower.qzone.qq.com/cgi-bin/cgi_use_mallprop?g_tk='.$this->gtk2;
				$post = 'qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fflower%2Ftool.html%23&propid='.$row['propid'].'&op_uin='.$this->uin.'&p=0.'.time().'84094803&format=json';
				$json=$this->get_curl($url,$post,'http://ctc.qzs.qq.com/qzone/client/photo/swf/RareFlower/FlowerVineLite.swf',$this->cookie);
				$json=mb_convert_encoding($json, "UTF-8", "gb2312");
			}
		}

		$url = 'http://flower.qzone.qq.com/cgi-bin/fg_get_giftpkg?g_tk='.$this->gtk2;
		$post = 'outCharset=utf-8&format=json';
		$json=$this->get_curl($url,$post,'http://ctc.qzs.qq.com/qzone/client/photo/swf/RareFlower/FlowerVineLite.swf',$this->cookie);
		$arr=json_decode($json,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			if($arr['data']['vDailyGiftpkg'][0]['caption']){
				$this->msg[]=$arr['data']['vDailyGiftpkg'][0]['caption'].':'.$arr['data']['vDailyGiftpkg'][0]['content'];
				$giftpkgid=$arr['data']['vDailyGiftpkg'][0]['id'];
				$granttime=$arr['data']['vDailyGiftpkg'][0]['granttime'];
				$url = 'http://flower.qzone.qq.com/cgi-bin/fg_use_giftpkg?g_tk='.$this->gtk2;
				$post = 'giftpkgid='.$giftpkgid.'&outCharset=utf%2D8&granttime='.$granttime.'&format=json';
				$this->get_curl($url,$post,'http://ctc.qzs.qq.com/qzone/client/photo/swf/RareFlower/FlowerVineLite.swf',$this->cookie);
			}else
				$this->msg[]='领取每日登录礼包成功！';
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='领取每日登录礼包失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='领取每日登录礼包失败！'.$arr['message'];
		}

		$url = 'http://flower.qzone.qq.com/cgi-bin/cgi_get_giftpkg?uin='.$this->uin.'&t=0.8578207577979139&fupdate=1&g_tk='.$this->gtk2;
		$json=$this->get_curl($url,0,'http://ctc.qzs.qq.com/qzone/client/photo/swf/RareFlower/FlowerVineLite.swf',$this->cookie);
	}
	public function sweet_sign(){
		$url = 'http://h5.qzone.qq.com/mood/lover?_wv=3';
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$this->msg[]='情侣空间登录成功！';
		
		$url = 'http://h5.qzone.qq.com/webapp/json/love_ranklist/AddDownloadAppScore?t=0.09518297787128992&g_tk='.$this->gtk2;
		$data = $this->get_curl($url,0,$url,$this->cookie);
		$arr = json_decode($data, true);
		if(array_key_exists('ret',$arr) && $arr['ret']==0){
			$this->msg[]='情侣空间下载客户端成功！';
		}else{
			$this->msg[]='情侣空间签到失败！'.$arr['msg'];
		}

		$url = 'http://h5.qzone.qq.com/proxy/domain/sweet.snsapp.qq.com/v2/cgi-bin/sweet_share_write?version=v2&t=0.0531703351366811&g_tk='.$this->gtk2;
		$post = 'type=501&uin='.$this->uin.'&plat=1&opuin='.$this->uin.'&content=%E5%AE%9D%E8%B4%9D%E4%BD%A0%E5%B0%B1%E6%98%AF%E6%88%91%E5%94%AF%E4%B8%80%EF%BC%81&sync=0&lbs=&appicnum=0&format=html&inCharset=utf-8&outCharset=utf-8';
		$data = $this->get_curl($url,$post,$url,$this->cookie);
		preg_match('/callback\((.*?)\n\);/',$data,$json);
		$arr = json_decode($json[1], true);
		if(array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='情侣空间发表状态成功！';
		}else{
			$this->msg[]='情侣空间发表状态失败！'.$arr['message'];
		}
	}
	private function getToken($url = 'https://h5.qzone.qq.com/mqzone/index', $pc = false){
		if($this->qzonetoken)return $this->qzonetoken;
		$filename = ROOT.'qq/temp/'.md5($this->uin.$this->pskey.$url).'.txt';
		if(file_exists($filename)){
			$result=file_get_contents($filename);
			$this->qzonetoken=$result;
			return $this->qzonetoken;
		}
		if($pc){
			$ua='Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
		}else{
			$ua='Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.5 Mobile Safari/533.1';
		}
		$json=$this->get_curl($url,0,0,$this->cookie,0,$ua);
		preg_match('/\(function\(\){ try{*.return (.*?);} catch\(e\)/i',$json,$match);
		if($data=$match[1]){
			$word=array('([]+[][(![]+[])[!+[]+!![]+!![]]+([]+{})[+!![]]+(!![]+[])[+!![]]+(!![]+[])[+[]]][([]+{})[!+[]+!![]+!![]+!![]+!![]]+([]+{})[+!![]]+([][[]]+[])[+!![]]+(![]+[])[!+[]+!![]+!![]]+(!![]+[])[+[]]+(!![]+[])[+!![]]+([][[]]+[])[+[]]+([]+{})[!+[]+!![]+!![]+!![]+!![]]+(!![]+[])[+[]]+([]+{})[+!![]]+(!![]+[])[+!![]]]((!![]+[])[+!![]]+([][[]]+[])[!+[]+!![]+!![]]+(!![]+[])[+[]]+([][[]]+[])[+[]]+(!![]+[])[+!![]]+([][[]]+[])[+!![]]+([]+{})[!+[]+!![]+!![]+!![]+!![]+!![]+!![]]+(![]+[])[!+[]+!![]]+([]+{})[+!![]]+([]+{})[!+[]+!![]+!![]+!![]+!![]]+(+{}+[])[+!![]]+(!![]+[])[+[]]+([][[]]+[])[!+[]+!![]+!![]+!![]+!![]]+([]+{})[+!![]]+([][[]]+[])[+!![]])())'=>'https','([][[]]+[])'=>'undefined','([]+{})'=>'[object Object]','(+{}+[])'=>'NaN','(![]+[])'=>'false','(!![]+[])'=>'true');
			$words=array();$i=0;
			foreach($word as $k=>$v){
				$words[$i]=$v;
				$data=str_replace($k,'$words['.$i.']',$data);
				$i++;
			}
			$data=str_replace(array('!+[]','+!![]','+[]'),array('+1','+1','+0'),$data);
			$data=str_replace(array('+(','+$'),array('.(','.$'),$data);
			eval('$result='.$data.';');
			if(!$result){
				$this->msg[]='计算qzonetoken失败！';
				return false;
			}
			file_put_contents($filename,$result);
			$this->qzonetoken=$result;
			return $this->qzonetoken;
		}else{
			$this->msg[]='获取qzonetoken失败！';
			return false;
		}
    }
	public function get_curl($url,$post=0,$referer=1,$cookie=0,$header=0,$ua=0,$nobaody=0){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$httpheader[] = "Accept:application/json";
		$httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
		$httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
		$httpheader[] = "Connection:close";
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
				curl_setopt($ch, CURLOPT_REFERER, 'http://h5.qzone.qq.com/mqzone/index');
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
		curl_setopt($ch,CURLOPT_TIMEOUT,10);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;
	}
	public function openu($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$httpheader[] = "Accept:*/*";
		$httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
		$httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
		$httpheader[] = "Connection:close";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36');
		curl_setopt($ch,CURLOPT_TIMEOUT,10);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$ret = curl_exec($ch);
		curl_close($ch);
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
		$len = strlen($skey);
		for($i = 0; $i < $len; $i ++)
		{
			$ASCIICode = mb_convert_encoding($skey[$i], 'UTF-32BE', 'UTF-8');
			$ASCIICode = hexdec(bin2hex($ASCIICode));
			$hash[] = (($salt << 5) + $ASCIICode);
			$salt = $ASCIICode;
		}
		$md5str = md5(implode($hash) . $md5key);
		return $md5str;
	}
	private function is_comment($uin,$arrs){
        if($arrs){
	    	foreach($arrs as $arr){
    	   		if($arr['user']['uin'] == $uin){
        			return false;
            		break;
        		}
    		}
        	return true; 
        }else{
        	return true; 
        }
	}
	private function array_str($array){
    	$str='';
        if($array[-100]){
	        $array100=explode(' ',trim($array[-100]));
    	    $new100=implode('+',$array100);
            $array[-100]=$new100;
        }
 		foreach($array as $k=>$v){
            if($k!='-100'){
	    		$str=$str.$k.'='.$v.'&';
            }
 		}
        $str=urlencode($str.'-100=').$array[-100].'+';
        $str=str_replace(':','%3A',$str);
    	return $str;
    }
	public function setnick($nick){
		$url="http://w.qzone.qq.com/cgi-bin/user/cgi_apply_updateuserinfo_new?g_tk=".$this->gtk2;
		$data="qzreferrer=http%3A%2F%2Fctc.qzs.qq.com%2Fqzone%2Fv6%2Fsetting%2Fprofile%2Fprofile.html%3Ftab%3Dbase&nickname=".urlencode($nick)."&emoji=&sex=1&birthday=2015-01-01&province=0&city=PAR&country=FRA&marriage=6&bloodtype=5&hp=0&hc=PAR&hco=FRA&career=&company=&cp=0&cc=0&cb=&cco=0&lover=&islunar=0&mb=1&uin=".$this->uin."&pageindex=1&nofeeds=1&fupdate=1&format=json";
		$return=$this->get_curl($url,$data,$url,$this->cookie);
		$arr=json_decode($return,true);
		if(@array_key_exists('code',$arr) && $arr['code']==0){
			$this->msg[]='修改昵称成功！当前昵称：'.$nick;
		}elseif($arr['code']==-3000){
			$this->skeyzt=1;
			$this->msg[]='修改昵称失败！原因:SKEY已过期！';
		}else{
			$this->msg[]='修改昵称失败！'.$arr['message'];
		}
	}
	public function getgroupinfo(){
		$ua='Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36';
		$url='http://r.qzone.qq.com/cgi-bin/tfriend/friend_getgroupinfo.cgi?uin='.$this->uin.'&fuin=&rd=0.808466'.time().'&fupdate=1&format=json&g_tk='.$this->gtk2.'&qzonetoken='.$this->getToken('https://user.qzone.qq.com/'.$this->uin.'/311',1);
		$return=$this->get_curl($url,0,'http://user.qzone.qq.com/'.$this->uin.'/myhome/friends/ofpmd',$this->cookie,0,$ua);
		$arr=json_decode($return,true);
		return $arr;
	}
	public function addfriend($touin, $groupid=0){
		$ua='Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36';
		$url='http://w.qzone.qq.com/cgi-bin/tfriend/friend_addfriend.cgi?g_tk='.$this->gtk2.'&qzonetoken='.$this->getToken('https://user.qzone.qq.com/'.$this->uin.'/311',1);
		$post='sid=0&ouin='.$touin.'&uin='.$this->uin.'&fupdate=1&rd=0.017492896'.time().'&fuin='.$touin.'&groupId='.$groupid.'&realname=&flag=&chat=&key=&im=0&from=9&from_source=11&format=json&qzreferrer=http://user.qzone.qq.com/'.$this->uin.'/myhome/friends/ofpmd';
		$return=$this->get_curl($url,$post,'http://user.qzone.qq.com/'.$this->uin.'/myhome/friends/ofpmd',$this->cookie,0,$ua);
		$arr=json_decode($return,true);
		return $arr;
	}
	public function uploadimg($image,$image_size=array()){
		$url='http://mobile.qzone.qq.com/up/cgi-bin/upload/cgi_upload_pic_v2?g_tk='.$this->gtk2;
        $post='picture='.urlencode(base64_encode($image)).'&base64=1&hd_height='.$image_size[1].'&hd_width='.$image_size[0].'&hd_quality=90&output_type=json&preupload=1&charset=utf-8&output_charset=utf-8&logintype=sid&Exif_CameraMaker=&Exif_CameraModel=&Exif_Time=&uin='.$this->uin;
        $data=preg_replace("/\s/","",$this->get_curl($url,$post,1,$this->cookie,0,1));
		preg_match('/_Callback\((.*)\);/',$data,$arr);
		$data=json_decode($arr[1],true);
        if($data && array_key_exists('filemd5',$data)){
			$this->msg[]='图片上传成功！';
			$post='output_type=json&preupload=2&md5='.$data['filemd5'].'&filelen='.$data['filelen'].'&batchid='.time().rand(100000,999999).'&currnum=0&uploadNum=1&uploadtime='.time().'&uploadtype=1&upload_hd=0&albumtype=7&big_style=1&op_src=15003&charset=utf-8&output_charset=utf-8&uin='.$this->uin.'&logintype=sid&refer=shuoshuo';
			$img=preg_replace("/\s/","",$this->get_curl($url,$post,1,$this->cookie,0,1));
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
}