<?php
/*
 *WEBQQ功能类
 *Author：消失的彩虹海
*/
class webqq{
	public $msg;
	public $cookie;
	public $psessionid;
	public $vfwebqq;
	public function __construct($uin,$skey){
		$this->uin=$uin;
		$this->skey=$skey;
		$this->ptwebqq=md5($uin).md5($uin);
		$this->gtk=$this->getGTK($skey);
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
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
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
        for($i = 0; $i < $len; $i++){
            $hash += (($hash << 5) & 0xffffffff) + ord($skey[$i]);
        }
        return $hash & 0x7fffffff;//计算g_tk
    }
	private function getToken($token){
		$len = strlen($token);
		$hash = 0;
		for ($i = 0; $i < $len; $i++) {
			$hash = fmod($hash * 33 + ord($token[$i]), 4294967296);
		}
        return $hash;
    }
	private function getHash($uin,$ptvfwebqq){
		if(intval($uin)==2147483647)return $this->get_curl('http://music.api.cccyun.cc/gettoken.php?uin='.$uin.'&ptvfwebqq='.$ptvfwebqq);
		$ptb = array();
		for($i=0;$i<strlen($ptvfwebqq);$i++){
			$ptb[$i%4] ^= ord($ptvfwebqq[$i]);
		}
		$salt = array('EC','OK');
		$uinByte = array();
		$uinByte[0] = ((($uin >> 24) & 0xFF) ^ ord($salt[0][0]));
		$uinByte[1] = ((($uin >> 16) & 0xFF) ^ ord($salt[0][1]));
		$uinByte[2] = ((($uin >> 8) & 0xFF) ^ ord($salt[1][0]));
		$uinByte[3] = (($uin & 0xFF) ^ ord($salt[1][1]));
		$result = array();
		for ($i=0;$i<8;$i++){
			if ($i%2 == 0)
				$result[$i] = $ptb[$i>>1];
			else
				$result[$i] = $uinByte[$i>>1];
		}
        $hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$buf = '';
		for($i=0;$i<count($result);$i++){
			$buf .= ($hex[($result[$i]>>4) & 0xF]);
			$buf .= ($hex[$result[$i] & 0xF]);
		}
        return $buf;
    }

	//获取vfwebqq
	private function getvfwebqq(){
		$url = 'http://s.web2.qq.com/api/getvfwebqq?ptwebqq='.$this->ptwebqq.'&clientid=53999199&psessionid=&t='.time().'128';
		$data = $this->get_curl($url,0,'http://d1.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			return $arr['result']['vfwebqq'];
		}else{
			return false;
		}
	}

	//首次登录WEBQQ
	public function login($superkey){
		$supertoken=(string)$this->getToken($superkey);
		$url = "http://ptlogin.qq.com/pt4_auth?daid=164&appid=501004106&auth_token=".$this->getToken($supertoken);
		$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login','superuin=o0'.$this->uin.'; superkey='.$superkey.'; supertoken='.$supertoken.';');
		if(preg_match('/ptsigx=(.*?)&/',$data,$match)){
			$url='http://ptlogin4.web2.qq.com/check_sig?pttype=1&uin='.$this->uin.'&service=ptqrlogin&nodirect=0&ptsigx='.$match[1].'&s_url=http%3A%2F%2Fw.qq.com%2Fproxy.html%3Flogin2qq%3D1%26webqq_type%3D10&f_url=&ptlang=2052&ptredirect=100&aid=501004106&daid=164&j_later=0&low_login_hour=0&regmaster=0&pt_login_type=3&pt_aid=0&pt_aaid=16&pt_light=0&pt_3rd_aid=0';
			$data = $this->get_curl($url,0,'http://ui.ptlogin2.qq.com/cgi-bin/login',0,1);
			preg_match_all('/Set-Cookie: (.*);/iU',$data,$matchs);
			$cookie='';
			foreach ($matchs[1] as $val) {
				if(substr($val,-1)=='=')continue;
				$cookie.=$val.'; ';
			}
			$this->cookie=$cookie.'ptwebqq='.$this->ptwebqq.';';
			return $this->cookie;
		}else{
			$this->msg[]='WEBQQ登录失败！superkey已失效';
			return false;
		}
	}

	//第二次登录
	public function login2(){
		$url = 'http://d1.web2.qq.com/channel/login2';
		$post = 'r=%7B%22ptwebqq%22%3A%22'.$this->ptwebqq.'%22%2C%22clientid%22%3A53999199%2C%22psessionid%22%3A%22%22%2C%22status%22%3A%22online%22%7D';
		$data = $this->get_curl($url,$post,'http://d1.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			$this->psessionid=$arr['result']['psessionid'];
			if($vfwebqq=$this->getvfwebqq()){
				$this->vfwebqq=$vfwebqq;
			}else{
				$this->vfwebqq=$arr['result']['vfwebqq'];
			}
			$this->msg[]=$this->uin.' 登录WEBQQ成功！';
			return true;
		} elseif (array_key_exists('retcode', $arr) && $arr['retcode'] == 108) {
			$this->msg[]=$this->uin.' 登录WEBQQ失败，请在安全中心检查QQ是否开启了登录限制';
			return false;
		} elseif (array_key_exists('retcode', $arr)) {
			$this->msg[]=$this->uin.' 登录WEBQQ失败，错误信息:' . $arr['retcode'] . $arr['errmsg'];
			return false;
		} else {
			$this->msg[]=$this->uin.' 登录WEBQQ失败，接口请求错误';
			return false;
		}
	}

	//WEBQQ上线
	public function online(){
		$url = 'http://d1.web2.qq.com/channel/get_online_buddies2?vfwebqq='.$this->vfwebqq.'&clientid=53999199&psessionid='.$this->psessionid.'&t='.time().'553';
		$data = $this->get_curl($url,0,'http://d1.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			return true;
		}else{
			return false;
		}
	}

	//心跳包
	public function poll(){
		$url = 'http://d1.web2.qq.com/channel/poll2';
		$post = 'r=%7B%22ptwebqq%22%3A%22'.$this->ptwebqq.'%22%2C%22clientid%22%3A53999199%2C%22psessionid%22%3A%22'.$this->psessionid.'%22%2C%22key%22%3A%22%22%7D';
		$data = $this->get_curl($url,$post,'http://d1.web2.qq.com/proxy.html',$this->cookie);
		return json_decode($data, true);
	}

	//改变在线状态
	public function change_status($newstatus){
		//online、callme、away、busy、silent、hidden、offline
		$url = 'http://d1.web2.qq.com/channel/change_status2?newstatus='.$newstatus.'&clientid=53999199&psessionid='.$this->psessionid.'&t='.time().'1542';
		$data = $this->get_curl($url,0,'http://d1.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			$this->msg[]='改变在线状态成功，当前状态：'.$newstatus;
			return true;
		}else{
			$this->msg[]='改变在线状态失败。'.$arr['errmsg'];
			return false;
		}
	}

	//获取最近联系人/群的请求信息
	public function get_recent_list(){
		$url = 'http://d1.web2.qq.com/channel/get_recent_list2';
		$post = 'r=%7B%22vfwebqq%22%3A%22'.$this->vfwebqq.'%22%2C%22clientid%22%3A53999199%2C%22psessionid%22%3A%22'.$this->psessionid.'%22%7D';
		$data = $this->get_curl($url,$post,'http://d1.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			return $arr['result'];
		}else{
			$this->msg[]='获取最近联系人/群的请求信息失败。'.$arr['errmsg'];
			return false;
		}
	}

	//获取自己的资料
	public function get_self_info(){
		$url = 'http://s.web2.qq.com/api/get_self_info2?t='.time();
		$data = $this->get_curl($url,0,'http://s.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			return $arr['result'];
		}else{
			$this->msg[]='获取自己的资料失败。'.$arr['errmsg'];
			return false;
		}
	}

	//发送群消息
	public function send_qun_msg($group_uin, $content, $msgid){
		$url = 'http://d1.web2.qq.com/channel/send_qun_msg2';
		$post = 'r=%7B%22group_uin%22%3A'.$group_uin.'%2C%22content%22%3A%22%5B%5C%22'.$content.'%5C%22%2C%5B%5C%22font%5C%22%2C%7B%5C%22name%5C%22%3A%5C%22%E5%AE%8B%E4%BD%93%5C%22%2C%5C%22size%5C%22%3A10%2C%5C%22style%5C%22%3A%5B0%2C0%2C0%5D%2C%5C%22color%5C%22%3A%5C%22000000%5C%22%7D%5D%5D%22%2C%22face%22%3A522%2C%22clientid%22%3A53999199%2C%22msg_id%22%3A'.$msgid.'%2C%22psessionid%22%3A%22'.$this->psessionid.'%22%7D';
		$data = $this->get_curl($url,$post,'http://d1.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0 || array_key_exists('errCode', $arr) && $arr['errCode'] == 0 || $arr['retcode'] == 100100) {
			$this->msg[]='群消息回复成功:'.$content;
			return true;
		}else{
			$this->msg[]='群消息回复失败:'.$content.$arr['errmsg'];
			return false;
		}
	}

	//发送私人消息
	public function send_buddy_msg($to, $content, $msgid){
		$url = 'http://d1.web2.qq.com/channel/send_buddy_msg2';
		$post = 'r=%7B%22to%22%3A'.$to.'%2C%22content%22%3A%22%5B%5C%22'.$content.'%5C%22%2C%5B%5C%22font%5C%22%2C%7B%5C%22name%5C%22%3A%5C%22%E5%AE%8B%E4%BD%93%5C%22%2C%5C%22size%5C%22%3A10%2C%5C%22style%5C%22%3A%5B0%2C0%2C0%5D%2C%5C%22color%5C%22%3A%5C%22000000%5C%22%7D%5D%5D%22%2C%22face%22%3A540%2C%22clientid%22%3A53999199%2C%22msg_id%22%3A'.$msgid.'%2C%22psessionid%22%3A%22'.$this->psessionid.'%22%7D';
		$data = $this->get_curl($url,$post,'http://d1.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0 || array_key_exists('errCode', $arr) && $arr['errCode'] == 0 || $arr['retcode'] == 100100) {
			$this->msg[]='私人消息回复成功:'.$content;
			return true;
		}else{
			$this->msg[]='私人消息回复失败:'.$content.$arr['errmsg'];
			return false;
		}
	}

	//获取陌生人资料
	public function get_stranger_info($send_uin){
		$url = "http://s.web2.qq.com/api/get_stranger_info2?tuin=" . $send_uin . "&verifysession=&gid=0&code=&vfwebqq=" . $this->vfwebqq . "&t=" . time() . "559";
		$data = $this->get_curl($url,0,'http://s.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			return $arr['result'];
		}else{
			$this->msg[]='获取陌生人信息失败。'.$arr['errmsg'];
			return false;
		}
	}
	//获取群资料
	public function get_group_public_info($gcode){
		$url = 'http://s.web2.qq.com/api/get_group_public_info2';
		$post = "gcode=" . $gcode . "&vfwebqq=" . $this->vfwebqq . "&t=" . time() . "559";
		$data = $this->get_curl($url,$post,'http://s.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			return $arr['result'];
		}else{
			$this->msg[]='获取群资料失败。'.$arr['errmsg'];
			return false;
		}
	}
	//处理加群请求
	public function op_group_join_req($group_uin, $req_uin, $is_agree = true){
		$op_type = $is_agree ? 2 : 3;
		$url = "http://d1.web2.qq.com/channel/op_group_join_req?group_uin=" . $group_uin . "&req_uin=" . $req_uin . "&msg=&op_type=" . $op_type . "&clientid=53999199&psessionid=" . $this->psessionid . "&t=" . time() . "559";
		$data = $this->get_curl($url,0,'http://d1.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			$this->msg[]='处理 '.$group_uin.' 群的 '.$req_uin.' QQ的加群请求成功';
			return true;
		}else{
			$this->msg[]='处理 '.$group_uin.' 群的 '.$req_uin.' QQ的加群请求失败'.$arr['errmsg'];
			return false;
		}
	}
	//获取好友列表
	public function get_user_friends(){
		$hash = $this->getHash($this->uin,$this->ptwebqq);
		$url = "http://s.web2.qq.com/api/get_user_friends2";
		$post = 'r=%7B%22vfwebqq%22%3A%22'.$this->vfwebqq.'%22%2C%22hash%22%3A%22'.$hash.'%22%7D';
		$data = $this->get_curl($url,$post,'http://s.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			return $arr['result'];
		}else{
			return false;
		}
	}
	//获取群列表
	public function get_group_name_list(){
		$hash = $this->getHash($this->uin,$this->ptwebqq);
		$url = "http://s.web2.qq.com/api/get_group_name_list_mask2";
		$post = 'r=%7B%22vfwebqq%22%3A%22'.$this->vfwebqq.'%22%2C%22hash%22%3A%22'.$hash.'%22%7D';
		$data = $this->get_curl($url,$post,'http://s.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			return $arr['result'];
		}else{
			return false;
		}
	}
	//获取好友资料
	public function get_friend_info($send_uin){
		$url = "http://s.web2.qq.com/api/get_friend_info2?tuin=" . $send_uin . "&vfwebqq=" . $this->vfwebqq . "&clientid=53999199&psessionid=" . $this->psessionid . "&t=" . time() . "905";
		$data = $this->get_curl($url,0,'http://s.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			return $arr['result'];
		}else{
			//$this->msg[]='获取好友'.$send_uin.'的资料失败。'.$arr['errmsg'];
			return false;
		}
	}
	//获取好友昵称
	public function get_friend_nick($send_uin){
		$data=$this->get_friend_info($send_uin);
		return $data['nick'];
	}
	//获取好友QQ
	public function get_friend_uin($send_uin){
		$url = "http://s.web2.qq.com/api/get_friend_uin2?tuin=" . $send_uin . "&type=1&vfwebqq=" . $this->vfwebqq . "&t=" . time() . "239";
		$data = $this->get_curl($url,0,'http://s.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			return $arr['result']['account'];
		}else{
			$this->msg[]='获取好友'.$send_uin.'的QQ失败。'.$arr['errmsg'];
			return false;
		}
	}
	//获取好友长昵称
	public function get_single_long_nick($send_uin){
		$url = "http://s.web2.qq.com/api/get_single_long_nick2?tuin=" . $send_uin . "&vfwebqq=" . $this->vfwebqq . "&t=" . time() . "239";
		$data = $this->get_curl($url,0,'http://s.web2.qq.com/proxy.html',$this->cookie);
		$arr = json_decode($data, true);
		if (array_key_exists('retcode', $arr) && $arr['retcode'] == 0) {
			return $arr['result'][0]['lnick'];
		}else{
			$this->msg[]='获取好友'.$send_uin.'的昵称失败。'.$arr['errmsg'];
			return false;
		}
	}

	//禁止某个人发言
	public function set_group_shutup($group_uin, $member_uin, $time){
		$url = "http://qinfo.clt.qq.com/cgi-bin/qun_info/set_group_shutup";
		$post = "gc=" . $group_uin . "&shutup_list=%5B%7B%22uin%22%3A" . $member_uin . "%2C%22t%22%3A" . $time . "%7D%5D&bkn=" . $this->gtk . "&src=qinfo_v2";
		$data = $this->get_curl($url,$post,'http://qun.qq.com/member.html',$this->cookie);
		$arr = json_decode($data);
		return $arr->ec == 0 ? true : false;
	}
	//修改群名片
	public function set_group_card($group_uin, $member_uin, $name){
		$url = "http://qun.qq.com/cgi-bin/qun_mgr/set_group_card";
		$post = "gc=" . $group_uin . "&u=" . $member_uin . "&name=" . urlencode($name) . "&bkn=" . $this->gtk;
		$data = $this->get_curl($url,$post,'http://qun.qq.com/member.html',$this->cookie);
		$arr = json_decode($data);
		return $arr->ec == 0 ? true : false;
	}
	//移除群成员
	public function delete_group_member($group_uin, $member_uin){
		$url = "http://qun.qq.com/cgi-bin/qun_mgr/delete_group_member";
		$post = "gc=" . $group_uin . "&ul=" . $member_uin . "&flag=0&bkn=" . $this->gtk;
		$data = $this->get_curl($url,$post,'http://qun.qq.com/member.html',$this->cookie);
		$arr = json_decode($data);
		return $arr->ec == 0 ? true : false;
	}
	//获取群列表
	public function get_group_list(){
		$url = "http://qun.qq.com/cgi-bin/qun_mgr/get_group_list";
		$post = "bkn=" . $this->gtk;
		$data = $this->get_curl($url,$post,'http://qun.qq.com/member.html',$this->cookie);
		$arr = json_decode($data,true);
		return $arr;
	}
	//获取好友列表
	public function get_friend_list(){
		$url = "http://qun.qq.com/cgi-bin/qun_mgr/get_friend_list";
		$post = "bkn=" . $this->gtk;
		$data = $this->get_curl($url,$post,'http://qun.qq.com/member.html',$this->cookie);
		$arr = json_decode($data,true);
		return $arr;
	}
	//查询群成员的信息
	public function search_group_members($group_uin, $start, $end, $sort, $key = ""){
		$url = "http://qun.qq.com/cgi-bin/qun_mgr/search_group_members";
		$key = empty($key) ? "" : ("&key=" . $key);
		$post = "gc=" . $group_uin . "&st=" . $start . "&end=" . $end . "&sort=" . $sort . $key . "&bkn=" . $this->gtk;
		$data = $this->get_curl($url,$post,'http://qun.qq.com/member.html',$this->cookie);
		$arr = json_decode($data,true);
		return $arr;
	}
	//邀请别人加入QQ群，被邀请的人必须是自己的好友
	public function add_group_member($group_uin, $member_uin){
		$url = "http://qun.qq.com/cgi-bin/qun_mgr/add_group_member";
		$post = "gc=" . $group_uin . "&ul=" . $member_uin . "&bkn=" . $this->gtk;
		$data = $this->get_curl($url,$post,'http://qun.qq.com/member.html',$this->cookie);
		$arr = json_decode($data);
		return $arr->ec == 0 ? true : false;
	}
	//添加/取消群管理员，需要群主权限
	public function set_group_admin($group_uin, $member_uin, $option){
		$url = "http://qun.qq.com/cgi-bin/qun_mgr/set_group_admin";
		$post = "gc=" . $group_uin . "&ul=" . $member_uin . "&op=" . $option . "&bkn=" . $this->gtk;
		$data = $this->get_curl($url,$post,'http://qun.qq.com/member.html',$this->cookie);
		$arr = json_decode($data);
		return $arr->ec == 0 ? true : false;
	}

	//调用茉莉机器人接口
	public function robotapi_itpk($content,$from_uin,$nick){
		global $robot,$apikey,$apisecret;
		if($robot==2)$addstr='&api_key='.$apikey.'&api_secret='.$apisecret;
		$cqname = $nick?$nick:'QQ智能机器人';
		$url = 'http://i.itpk.cn/api.php?question='.urlencode($content).$addstr;
		$data = $this->get_curl($url);
		$data = str_replace("[cqname]",$cqname,$data);
		if(strpos($data,'[name]')!==false)
		$data = str_replace("[name]",$this->get_friend_nick($from_uin),$data);
		return $data;
	}

	public function process_message($poll,$content,$method,$nick) {
		foreach ($poll['result'] as $news) {
			if ($news['poll_type'] == 'message') {
				//好友消息
				if($method==2||$method==4){
					$from_uin = $news['value']['from_uin'];
					$msgid = rand(5000000, 5999999);
					if($content=='robot')
						$content=$this->robotapi_itpk($news['value']['content'][1],$from_uin,$nick);
					$this->send_buddy_msg($from_uin, $content, $msgid);
				}
			} elseif ($news['poll_type'] == 'group_message') {
				//群消息
				if($method==3||$method==4){
					$group_uin = $news['value']['from_uin'];
					$send_uin = $news['value']['send_uin'];
					$msgid = rand(5000000, 5999999);
					if($content=='robot')
						$content=$this->robotapi_itpk($news['value']['content'][1],$from_uin,$nick);
					$this->send_qun_msg($group_uin, $content, $msgid);
				}
			} elseif ($news['poll_type'] == 'sys_g_msg') {
				//加群验证消息
			} elseif ($news['poll_type'] == 'sess_message') {
				//临时会话消息
			} elseif ($news['poll_type'] == 'discu_message') {
				//讨论组消息
			} elseif ($news['poll_type'] == 'buddies_status_change') {
				//好友状态改变提醒
			}
		}
	}
}