<?php
// 此程序由微秒赞（洛绝尘）深度定制修改 <1031601644@qq.com>
// 底包为快乐是福1.81 <815856515@qq.com>
// 人不要脸，天下无敌。 儿子你要改版权爸爸也不拦你，尤其是龙魂儿子

namespace Mz\Controller;
use Think\Controller;
class IndexController extends Controller {
	private $user;
	
	public function dama(){
		$this->display();
	}
	public function chat(){
		if($_POST['do']=='look'){
			$kn=is_numeric($_POST['id'])?$_POST['id']:'1';
			if($kn==1){
				exit('{"code":-1,"msg":"没有更多聊天内容了！"}');
			}
			$hdeb=M("chats")->field(array(C("DB_PREFIX").'chats.*',C("DB_PREFIX").'users.user'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."chats.uid")->where('id<'.$kn)->limit(15)->order('id desc')->select();
			$ifloe['code']=0;
			$ifloe['data']=$hdeb;
			exit(json_encode($ifloe));
		}elseif($_POST['do']=='new'){
			$kn=is_numeric($_POST['id'])?$_POST['id']:'0';
			$hdeb=M("chats")->field(array(C("DB_PREFIX").'chats.*',C("DB_PREFIX").'users.user'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."chats.uid")->where('id>'.$kn)->order('id asc')->select();
			$ifloe['code']=0;
			$ifloe['data']=$hdeb;
			exit(json_encode($ifloe));
		}elseif($_POST['do']=='send'){
			$kn=is_numeric($_POST['id'])?$_POST['id']:'0';
			$con=I('post.content','','get_safe_str');
			/*
			if(!$kn){
				exit('{"code":-1,"msg":"LastID Error！"}');
			}
			*/
			if(!$con){
				exit('{"code":-2,"msg":"聊天内容不能为空！"}');
			}
			if(!get_isvip($this->user['vip'],$this->user['vipend'])){
				exit('{"code":-3,"msg":"对不起，仅VIP能发送聊天信息！"}');
			}
			$data['uid']=$this->user['uid'];
			$data['content']=$con;
			$data['addtime']=date("Y-m-d H:i:s");
			M("chats")->data($data)->add();
			$hdeb=M("chats")->field(array(C("DB_PREFIX").'chats.*',C("DB_PREFIX").'users.user'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."chats.uid")->where('id>'.$kn)->order('id asc')->select();
			$ifloe['code']=0;
			$ifloe['data']=$hdeb;
			exit(json_encode($ifloe));
		}
		$p=is_numeric($_GET['p'])?$_GET['p']:'1';
		$mvrjt=15*($p-1);
		$tkes=$uxg;
		$fbsng="$mvrjt,15";
		$xysan=M("chats")->count('id');
		$gnyce=ceil($qqyqxcni);
		$hdeb=M("chats")->field(array(C("DB_PREFIX").'chats.*',C("DB_PREFIX").'users.user'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."chats.uid")->limit($fbsng)->order('id desc')->select();
		if(!$thqejoqq=$hdeb[0]['id']) $thqejoqq=0;
		$this->assign('lastchat',$thqejoqq);
		$hdeb=array_reverse($hdeb);
		if(!$issfaiygx=$hdeb[0]['id']) $issfaiygx=1;
		$this->assign('startchat',$issfaiygx);
		$this->assign('page',$p);
		$this->assign('pages',$gnyce);
		$this->assign('count',$xysan);
		$this->assign('list',$hdeb);
		$this->display();
	
	}
	public function dxjc(){
		$this->islogin();
		if(!get_isvip($this->user['vip'],$this->user['vipend'])){
			get_exit("对不起，此功能仅VIP能使用");
		}
		$qid=is_numeric($_GET['qid'])?$_GET['qid']:'0';
		$row=$this->qqcheck($qid);

		if($_GET['do']=='clear'){
			session_unset();
			get_exit('检测数据已清楚！',U('dxjc','qid='.$qid));
		}
		$json = get_curl("http://api.qqmzp.com/getfriendlist.php?uin=" . $row[qq] . "&skey=" . $row[skey] ."&pc_p_skey=" . $row[pc_p_skey] . "&url=".C('dm')."&v=".C('v'));
		$json=mb_convert_encoding($json, "UTF-8", "UTF-8");
		$arr=json_decode($json,true);
		if(!@array_key_exists('code',$arr)){
			get_exit("获取好友列表失败，请稍候重试！");
		}elseif($arr['code']==-3000){
			get_exit("SKEY已过期，请更新后再检测！");
		}
		$_SESSION[friendlist]["$row[qq]"]=$json;
		$this->assign('json',$json);
		$this->assign('qqrow',$row);
		$this->assign('dxrow',$_SESSION['vmsf_dxrow']["$row[qq]"]);
		$this->assign('arr',$arr['data']['items']);
		$this->display();
	
	}

	public function qzmusic(){
		$this->islogin();
		if(!get_isvip($this->user['vip'],$this->user['vipend'])){
			get_exit("对不起，此功能仅VIP能使用");
		}

		$qid=is_numeric($_GET['qid'])?$_GET['qid']:'0';
		$row=$this->qqcheck($qid);

		
		$url = get_curl("http://qzone-music.qq.com/fcg-bin/cgi_playlist_xml.fcg?json=1&uin={$uin}&g_tk=5381");
		$url = mb_convert_encoding($url, "UTF-8", "GB2312");
		//print_r($arr);
		preg_match_all('@{xqusic_id\:.*xsong_name\:\"(.*)\".*qqmusic.qq.com/(.*)\'@Ui',$url,$arr);
		$this->assign('name',$arr);
		$this->assign('uin',$uin);
		$this->assign('qqrow',$row);
		$this->display();
	}
	
	public function mzjc(){
		$this->islogin();
		if(!get_isvip($this->user['vip'],$this->user['vipend'])){
			get_exit("对不起，此功能仅VIP能使用");
		}
		$qid=is_numeric($_GET['qid'])?$_GET['qid']:'0';
		$row=$this->qqcheck($qid);
		$json=get_curl("http://api.qqmzp.com/mzjc.php?uin={$row['qq']}&skey={$row['skey']}". "&url=".C('dm')."&v=".C('v'));
		$arr=json_decode($json,true);
		if(!@array_key_exists('code',$arr)){
			get_exit("获取秒赞好友失败，请稍候重试！");
		}elseif($arr['code']==-3000){
			get_exit("SKEY已过期，请更新后再检测！",U('add',"uin=".$row['qq']));
		}
		$this->assign('mzcount',$arr['mzcount']);
		$this->assign('gpcount',$arr['gpcount']);
		$this->assign('fdcount',$arr['fdcount']);
		$this->assign('gpnames',$arr['gpnames']);
		$this->assign('friends',$arr['friends']);
		$this->assign('qqrow',$row);
		$this->display();
	}
	
    public function index(){
		if(isMobile()){
			$eu=C('web_wap_indexmb');
		}else{
			$eu=C('web_web_indexmb');
		}
		$this->display($eu);

	}

	public function user(){
		$this->islogin();
		if($_GET['do']=='del'){
			$qid=is_numeric($_GET['qid'])?$_GET['qid']:'0';
			$this->qqcheck($qid);
			M("qqs")->where("qid='$qid'")->delete();
		}
		$user=M("users")->field(array("*"))->where("sid='".$_COOKIE['vmz_sid']."'")->find();
		$qlist=M('qqs')->field(array('*'))->where("uid='".$user['uid']."'")->select();
		foreach($qlist as $key => $value){
			$nick=$this->get_qqnick($value['qq']);
			$list[$key]['nick']=$nick;
			$list[$key]['qid']=$value['qid'];
			$list[$key]['uin']=$value['qq'];
			$list[$key]['skeyzt']=$value['skeyzt'];
			$list[$key]['sidzt']=$value['sidzt'];
		}
		$this->assign('qlist',$list);
		$this->display();
    }
	public function qq(){
		$this->islogin();
		$qid=is_numeric($_GET['qid'])?$_GET['qid']:'0';
		$row=$this->qqcheck($qid);
		//dump($row);exit;
		if($row['sidzt']!=0 or $row['skeyzt']!=0 or empty($row['pc_p_skey']) or empty($row['cp_p_skey'])){
			get_exit("SKEY失效，请更新！",U('add','uin='.$row['qq']));
		}
		if($do=$_GET['change']){
			if(!get_isvip($this->user['vip'],$this->user['vipend'])){
				get_exit("对不起，此功能仅VIP能使用");
			}
			if(array_key_exists("is$do",$row)){
				if($row["is$do"]){
					M('qqs')->where("qid='$qid'")->setField("is$do",'0');
				}else{
					if($do=='vipqd'||$do=='lz'||$do=='zyzan'||$do=='qt'){
						M('qqs')->where("qid='$qid'")->setField("is$do",'2');
					}else{
						M('qqs')->where("qid='$qid'")->setField("is$do",'1');	
					}
				}
				$row=$this->qqcheck($qid);
			}
		}
		
		if($do=$_GET['do']){
			if(!get_isvip($this->user['vip'],$this->user['vipend'])){
				get_exit("对不起，此功能仅VIP能使用");
			}
			
			if($do=='qqz'){
				if(strlen(C('lqqurl'))){
					$data=get_curl(C('lqqurl').$row['qq']);
					get_exit($data);
				}else{
					get_exit("请通知站长前往后台设置拉圈API地址");
				}
			}
			
			if($do=='caipiao'){
				$json=get_curl("http://api.qqmzp.com/caizi.php?uin=".$row['qq']."&skey=".$row['skey']. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(array_key_exists('code',$arr)){
					get_exit($arr['msg']);
				}else{
					get_exit("Api系统繁忙。");
				}
			}
			
			if($do=='tushu'){
				$json=get_curl("http://api.qqmzp.com/book.php?uin=".$row['qq']."&skey=".$row['skey']. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(array_key_exists('code',$arr)){
					get_exit($arr['msg']);
				}else{
					get_exit("Api系统繁忙。");
				}
			}
			if($do=='xuanfeng'){
				$json=get_curl("http://api.qqmzp.com/xuanfeng.php?uin=".$row['qq']."&skey=".$row['skey']. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(array_key_exists('code',$arr)){
					get_exit($arr['msg']);
				}else{
					get_exit("Api系统繁忙。");
				}
			}
			if($do=='qzjf'){
				$data=get_curl("http://api.qqmzp.com/qzonejf.php?uin=".$row['qq']."&skey=".$row['skey']. "&url=".C('dm')."&v=".C('v'));
				if($data){
					get_exit($data);
				}
			}
		}

		$this->assign('qqrow',$row);
        $this->display();
    }
	public function qqset(){
		$this->islogin();
		$qid=is_numeric($_GET['qid'])?$_GET['qid']:'0';
		$row=$this->qqcheck($qid);
		if($_GET['do']=='delzf'){
			if(!get_isvip($this->user['vip'],$this->user['vipend'])){
				get_exit("对不起，此功能仅VIP能使用");
			}
			$kn=is_numeric($_GET['id'])?$_GET['id']:'0';
			M('zfdates')->where("uin='".$row['qq']."' and id='$kn'")->delete();
		}
		if($do=$_POST['do']){
			$is=is_numeric($_POST['is'])?$_POST['is']:'0';
			if($do=='zan'){
				$data['iszan']=$is;
				$fwq=0;
				$rate=is_numeric($_POST['rate'])?$_POST['rate']:'90';
				if($is){
					$fwq=is_numeric($_POST['net'])?$_POST['net']:'0';
					if(!$fwq){
						get_exit('请选择个合适的服务器');
					}elseif($fwq>C('freezan') && !get_isvip($this->user['vip'],$this->user['vipend'])){
						get_exit("对不起，此服务器仅VIP能使用！");
					}elseif($fwq && $row['zannet']!=$fwq && get_net_count('zan',$fwq)>=C('netnum')){
						get_exit("{$fwq}号服务器已满");
					}
				}
				$data['zannet']=$fwq;
				$data['zanrate']=$rate;
				M("qqs")->where("qid='$qid'")->save($data);
				$this->assign('alert',get_exit('秒赞设置成功！',1));
				$row=M("qqs")->field('*')->where("qid='$qid'")->find();
			}elseif($do=='reply'){
				if(!get_isvip($this->user['vip'],$this->user['vipend'])){
					get_exit("对不起，此功能仅VIP能使用");
				}
				$data['isreply']=$is;
				$con=I('post.content','','get_safe_str');
				$data['replycon']=$con;
				M("qqs")->where("qid='$qid'")->save($data);
				$this->assign('alert',get_exit('秒评设置成功！',1));
				$row=M("qqs")->field('*')->where("qid='$qid'")->find();
			}elseif($do=='zf'){
				if(!get_isvip($this->user['vip'],$this->user['vipend'])){
					get_exit("对不起，此功能仅VIP能使用");
				}
				$data['iszf']=$is;
				$con=I('post.content','','get_safe_str');
				$sgvp=I('post.zfok','','get_safe_str');
				
				$data['zfcon']=$con;
				M("qqs")->where("qid='$qid'")->save($data);
				$this->assign('alert',get_exit('转发设置成功！',1));
				$row=M("qqs")->field('*')->where("qid='$qid'")->find();
			}elseif($do=='zfupdate'){
				if(!get_isvip($this->user['vip'],$this->user['vipend'])){
					get_exit("对不起，此功能仅VIP能使用");
				}
				$kn=is_numeric($_POST['id'])?$_POST['id']:'0';
				$data['zfdate']=I('post.date','','get_safe_str');
				M("zfdates")->where("id='$kn'")->save($data);
			}elseif($do=='zfadd'){
				if(!get_isvip($this->user['vip'],$this->user['vipend'])){
					get_exit("对不起，此功能仅VIP能使用");
				}
				$data['uin']=$row['qq'];
				$data['zfuin']=I('post.uin','','get_safe_str');
				$data['zfdate']=I('post.date','','get_safe_str');
				M("zfdates")->add($data);
			}elseif($do=='shuo'){
				if(!get_isvip($this->user['vip'],$this->user['vipend'])){
					get_exit("对不起，此功能仅VIP能使用");
				}
				$data['isshuo']=$is;
				$rate=is_numeric($_POST['rate'])?$_POST['rate']:'90';
				$con=I('post.content','','get_safe_str');
				$shuocon=I('post.shuocon','','get_safe_str');
				$shuophone=I('post.shuophone','','get_safe_str');
				$shuopic=I('post.shuopic','','get_safe_str');
				
				$data['shuopic']=$shuopic;
				$data['shuoshuo']=$con;
				$data['shuocon']=$shuocon;
				$data['shuophone']=$shuophone;
				$data['shuorate']=$rate;
				M("qqs")->where("qid='$qid'")->save($data);
				$this->assign('alert',get_exit('自动说说设置成功！',1));
				$row=M("qqs")->field('*')->where("qid='$qid'")->find();
			}elseif($do=='qd'){
				if(!get_isvip($this->user['vip'],$this->user['vipend'])){
					get_exit("对不起，此功能仅VIP能使用");
				}
				$data['isqd']=$is;
				$con=I('post.content','','get_safe_str');
				$data['qdcon']=$con;
				M("qqs")->where("qid='$qid'")->save($data);
				$this->assign('alert',get_exit('空间签到设置成功！',1));
				$row=M("qqs")->field('*')->where("qid='$qid'")->find();
			}elseif($do=='delshuo'){
				if(!get_isvip($this->user['vip'],$this->user['vipend'])){
					get_exit("对不起，此功能仅VIP能使用");
				}
				$data['isdelshuo']=$is;
				M("qqs")->where("qid='$qid'")->save($data);
				$this->assign('alert',get_exit('删除说说设置成功！',1));
				$row=M("qqs")->field('*')->where("qid='$qid'")->find();
			}elseif($do=='qunqd'){
				if(!get_isvip($this->user['vip'],$this->user['vipend'])){
					get_exit("对不起，此功能仅VIP能使用");
				}
				$data['isqunqd']=$is;
				$con=I('post.content','','get_safe_str');
				$data['qunqdcon']=$con;
				M("qqs")->where("qid='$qid'")->save($data);
				$this->assign('alert',get_exit('扣群签到设置成功！',1));
				$row=M("qqs")->field('*')->where("qid='$qid'")->find();
			}elseif($do=='ly'){
				if(!get_isvip($this->user['vip'],$this->user['vipend'])){
					get_exit("对不起，此功能仅VIP能使用");
				}
				$data['isly']=$is;
				$con=I('post.content','','get_safe_str');
				$data['lycon']=$con;
				M("qqs")->where("qid='$qid'")->save($data);
				$this->assign('alert',get_exit('互刷留言设置成功！',1));
				$row=M("qqs")->field('*')->where("qid='$qid'")->find();
			}elseif($do=='ht'){
				if(!get_isvip($this->user['vip'],$this->user['vipend'])){
					get_exit("对不起，此功能仅VIP能使用");
				}
				$data['isht']=$is;
				M("qqs")->where("qid='$qid'")->save($data);
				$this->assign('alert',get_exit('花藤服务设置成功！',1));
				$row=M("qqs")->field('*')->where("qid='$qid'")->find();
			}	
		}

		$this->assign('qqrow',$row);
		$this->display();
	}
	public function djdg(){
		$this->islogin();
		$qid=is_numeric($_GET['qid'])?$_GET['qid']:'0';
		$row=$this->qqcheck($qid);
		
		if($_POST['do']=='djdg'){
			if($_POST['xunzhang']){
				$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&control=1&qq='.$row['qq'].'&id=xunzhang&sw=0'. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(@array_key_exists('control_code',$arr['vmz']) && $arr['vmz']['control_code']==0){
					$exit.='勋章墙代挂加速'.$arr['vmz']['control_error'].'\\n';
				}else{
					$exit.='勋章墙代挂加速操作失败！原因'.$arr['vmz']['control_error'].'\\n';
				}
			}else{
				$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&control=1&qq='.$row['qq'].'&id=xunzhang&sw=1'. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(@array_key_exists('control_code',$arr['vmz']) && $arr['vmz']['control_code']==0){
					$exit.='勋章墙代挂加速'.$arr['vmz']['control_error'].'\\n';
				}else{
					$exit.='勋章墙代挂加速操作失败！原因'.$arr['vmz']['control_error'].'\\n';
				}
			}
			if($_POST['pcqq']){
				$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&control=1&qq='.$row['qq'].'&id=pcqq&sw=0'. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(@array_key_exists('control_code',$arr['vmz']) && $arr['vmz']['control_code']==0){
					$exit.='电脑QQ代挂加速 '.$arr['vmz']['control_error'].'\\n';
				}else{
					$exit.='电脑QQ代挂加速操作失败！原因'.$arr['vmz']['control_error'].'\\n';
				}
			}else{
				$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&control=1&qq='.$row['qq'].'&id=pcqq&sw=1'. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(@array_key_exists('control_code',$arr['vmz']) && $arr['vmz']['control_code']==0){
					$exit.='电脑QQ代挂加速'.$arr['vmz']['control_error'].'\\n';
				}else{
					$exit.='电脑QQ代挂加速操作失败！原因'.$arr['vmz']['control_error'].'\\n';
				}
			}
			if($_POST['mqq']){
				$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&control=1&qq='.$row['qq'].'&id=mqq&sw=0'. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(@array_key_exists('control_code',$arr['vmz']) && $arr['vmz']['control_code']==0){
					$exit.='手机QQ代挂加速'.$arr['vmz']['control_error'].'\\n';
				}else{
					$exit.='手机QQ代挂加速操作失败！原因'.$arr['vmz']['control_error'].'\\n';
				}
			}else{
				$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&control=1&qq='.$row['qq'].'&id=mqq&sw=1'. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(@array_key_exists('control_code',$arr['vmz']) && $arr['vmz']['control_code']==0){
					$exit.='手机QQ代挂加速'.$arr['vmz']['control_error'].'\\n';
				}else{
					$exit.='手机QQ代挂加速操作失败！原因'.$arr['vmz']['control_error'].'\\n';
				}
			}
			if($_POST['game']){
				$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&control=1&qq='.$row['qq'].'&id=game&sw=0'. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(@array_key_exists('control_code',$arr['vmz']) && $arr['vmz']['control_code']==0){
					$exit.='腾讯手游代挂加速'.$arr['vmz']['control_error'].'\\n';
				}else{
					$exit.='腾讯手游代挂加速操作失败！原因'.$arr['vmz']['control_error'].'\\n';
				}
			}else{
				$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&control=1&qq='.$row['qq'].'&id=game&sw=1'. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(@array_key_exists('control_code',$arr['vmz']) && $arr['vmz']['control_code']==0){
					$exit.='腾讯手游代挂加速'.$arr['vmz']['control_error'].'\\n';
				}else{
					$exit.='腾讯手游代挂加速操作失败！原因'.$arr['vmz']['control_error'].'\\n';
				}
			}
			if($_POST['guanjia']){
				$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&control=1&qq='.$row['qq'].'&id=guanjia&sw=0'. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(@array_key_exists('control_code',$arr['vmz']) && $arr['vmz']['control_code']==0){
					$exit.='电脑管家代挂加速'.$arr['vmz']['control_error'].'\\n';
				}else{
					$exit.='电脑管家代挂加速操作失败！原因'.$arr['vmz']['control_error'].'\\n';
				}
			}else{
				$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&control=1&qq='.$row['qq'].'&id=guanjia&sw=1'. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(@array_key_exists('control_code',$arr['vmz']) && $arr['vmz']['control_code']==0){
					$exit.='电脑管家代挂加速'.$arr['vmz']['control_error'].'\\n';
				}else{
					$exit.='电脑管家代挂加速操作失败！原因'.$arr['vmz']['control_error'].'\\n';
				}
			}
			if($_POST['qqmusic']){
				$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&control=1&qq='.$row['qq'].'&id=qqmusic&sw=0'. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(@array_key_exists('control_code',$arr['vmz']) && $arr['vmz']['control_code']==0){
					$exit.='QQ音乐代挂加速'.$arr['vmz']['control_error'].'\\n';
				}else{
					$exit.='QQ音乐代挂加速操作失败！原因'.$arr['vmz']['control_error'].'\\n';
				}
			}else{
				$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&control=1&qq='.$row['qq'].'&id=qqmusic&sw=1'. "&url=".C('dm')."&v=".C('v'));
				$arr=json_decode($json,true);
				if(@array_key_exists('control_code',$arr['vmz']) && $arr['vmz']['control_code']==0){
					$exit.='QQ音乐代挂加速'.$arr['vmz']['control_error'].'\\n';
				}else{
					$exit.='QQ音乐代挂加速操作失败！原因'.$arr['vmz']['control_error'].'\\n';
				}
			}
			get_exit($exit);
		}elseif($_POST['do']=='upqqpwd'){
			$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&upqqpwd=1&qq='.$row['qq'].'&qqpwd='.$_POST['qqpwd']. "&url=".C('dm')."&v=".C('v'));
			$arr=json_decode($json,true);
			if(@array_key_exists('up_code',$arr['vmz']['up']) && $arr['vmz']['up']['up_code']==0){
				$exit=$arr['vmz']['up']['up_error'].'\\n';
			}else{
				$exit='QQ密码更新失败！原因'.$arr['vmz']['up']['up_error'].'\\n';
			}
			get_exit($exit);
		}elseif($_POST['do']=='bg'){
			$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&bg=1&qq='.$row['qq'].'&bgid='.implode(',',$_POST['bgid']). "&url=".C('dm')."&v=".C('v'));
			$arr=json_decode($json,true);
			if(@array_key_exists('bg_code',$arr['vmz']['bg']) && $arr['vmz']['bg']['bg_code']==0){
				$exit=$arr['vmz']['bg']['bg_error'].'\\n';
			}else{
				$exit=$arr['vmz']['bg']['bg_error'].'\\n';
			}
			get_exit($exit);
		}elseif($_POST['do']=='renew'){
			$json=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&renew=1&qq='.$row['qq'].'&cami='.$_POST['cami']. "&url=".C('dm')."&v=".C('v'));
			$arr=json_decode($json,true);
			if(@array_key_exists('renew_code',$arr['vmz']['renew']) && $arr['vmz']['renew']['renew_code']==0){
				$exit=$arr['vmz']['renew']['renew_error'].'\\n';
			}else{
				$exit=$arr['vmz']['renew']['renew_error'].'\\n';
			}
			get_exit($exit);
		}
		$dg_info=get_curl('http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&dg_info=1&qq='.$row['qq']. "&url=".C('dm')."&v=".C('v'));
		$arr=json_decode($dg_info,true);
		if(@array_key_exists('code',$arr['vmz']) && $arr['vmz']['code']==0){
			$this->assign('dg_info',json_decode($dg_info,true));
		}elseif(@array_key_exists('code',$arr['vmz']) && $arr['vmz']['code']==1 && $arr['vmz']['error']=='验证失败,QQ不存在'){
			get_exit('您的QQ没有开通代挂权限，请联系客服进行购买开通\\n全套等级代挂加速一月每月只需3元\\n客服QQ：'.C('web_qq'),'../../shop.html');
		}else{
			get_exit($arr['vmz']['error']);
		}
		$this->assign('qqrow',$row);
		$this->display();
	}
	public function shop(){
		$this->islogin();
        if($do=$_POST['do']){

            if($do=='km'){
                $vipkm=I('post.vipkm','','get_safe_str');
                $pekm=I('post.pekm','','get_safe_str');
				//echo($vipkm.".".$pekm);
                if(!empty($vipkm) or !empty($pekm)){
                    if(!empty($vipkm)){
                        if(!$viprow=M("kms")->field('*')->where("km='$vipkm' and (kind=0 or kind=2)")->find()){
                            get_exit("会员卡卡密不存在！");
                        }else{
                            if($viprow['isuse']){
                                get_exit("此会员卡卡密已经被使用！");
                            }elseif(get_isvip($this->user['vip'],$this->user['vipend']) && $viprow['kind']==2){
                                get_exit("对不起，你已经是会员，不能再使用试用卡！");
                            }else{
                                $data['isuse']=1;
                                $data['uid']=$this->user['uid'];
                                $data['usetime']=date("Y-m-d H:i:s");
                                M("kms")->where("kid='$viprow[kid]'")->save($data);
                                if($viprow['kind']==2){
                                    $jhvip['vip']=1;
                                    $jhvip['vipstart']=date("Y-m-d");
                                    $jhvip['vipend']=date("Y-m-d",strtotime("+ $viprow[ms] days"));
                                    M("users")->where("uid='".$this->user['uid']."'")->save($jhvip);
                                    $lpz="成功开通{$viprow['ms']}天VIP，你的VIP到期日期:{$jhvip['vipend']}";
                                    $this->assign('alert',get_exit($lpz,1));
                                }elseif(get_isvip($this->user['vip'],$this->user['vipend'])){
                                    $dehxcy=date("Y-m-d",strtotime("+ $viprow[ms] months",strtotime($this->user['vipend'])));
                                    M("users")-> where("uid='".$this->user['uid']."'")->setField('vipend',$dehxcy);
                                    $lpz='成功续费'.$viprow['ms'].'个月VIP，你的VIP到期日期:'.$dehxcy;
                                    $this->assign('alert',get_exit($lpz,1));
                                }else{
                                    $jhvip['vip']=1;
                                    $jhvip['vipstart']=date("Y-m-d");
                                    $jhvip['vipend']=date("Y-m-d",strtotime("+ $viprow[ms] months"));
                                    M("users")->where("uid='".$this->user['uid']."'")->save($jhvip);
                                    $lpz="成功开通{$viprow['ms']}个月VIP，你的VIP到期日期:{$jhvip['vipend']}";
                                    $this->assign('alert',get_exit($lpz,1));
                                }
                            }

                        }
                    }
                    if(!empty($pekm)){
                        if(!$perow=M("kms")->field('*')->where("km='$pekm' and kind=1")->find()){
                            get_exit("配额卡卡密不存在！");
                        }else{
                            if($perow['isuse']){
                                get_exit("此增加配额卡密已经被使用！");
                            }else{
                                $data['isuse']=1;
                                $data['uid']=$this->user['uid'];
                                $data['usetime']=date("Y-m-d H:i:s");
                                M("kms")->where("kid='$perow[kid]'")->save($data);
                                M("users")->where("uid='".$this->user['uid']."'")->setInc('peie',$perow['ms']);
                                $lpz="成功增加{$perow['ms']}个配额";
                                $this->assign('alert',get_exit($lpz,1));
                            }
                        }
                    }
                }else{
                    get_exit("两者之间，最少填一个！");
                }
            }elseif($do=='djdg'){
                $uin=I('post.uin','','get_safe_str');
                $dgkm=I('post.dgkm','','get_safe_str');
				if(empty($uin)){
					get_exit("请选择需要代挂的QQ！");
				}elseif(empty($dgkm)){
					get_exit("请输入代挂卡密！");
				}elseif(empty($_POST['qqpwd'])){
					get_exit("请输入QQ密码！");
				}else{
					$url='http://api.qqmzp.com/yefdgapi.php?pid='.C('yefdjdg_pid').'&skey='.C('yefdjdg_skey').'&uskey='.C('yefdjdg_uskey').'&add=1&cami='.$dgkm.'&qq='.$uin.'&qqpwd='.urlencode($_POST['qqpwd']). "&url=".C('dm')."&v=".C('v');
					$json=get_curl($url);
					$arr=json_decode($json,true);//print_r($arr);echo "&url=".C('dm')."&v=".C('v');exit();
					if(@array_key_exists('add_code',$arr['vmz']['add']) && $arr['vmz']['add']['add_code']==0){
						$exit.='代挂权限开通成功！'.'\\n';
					}else{
						$exit.='代挂权限开通失败！原因:'.$arr['vmz']['add']['add_error'].'\\n';
					}
					if(@array_key_exists('up_code',$arr['vmz']['up']) && $arr['vmz']['up']['up_code']==0){
						$exit.='QQ密码添加成功！'.'\\n';
					}else{
						$exit.='QQ密码添加失败！原因:'.$arr['vmz']['up']['add_error'].'\\n';
					}
					get_exit($exit);
				}
            }
        }
        $user=M("users")->field(array("*"))->where("sid='".$_COOKIE['vmz_sid']."'")->find();
		$qlist=M('qqs')->field(array('qq'))->where("uid='".$user['uid']."'")->select();
		foreach($qlist as $key => $row){
			$uin=$row['qq'];
			$nick=$this->get_qqnick($uin);
			$list[$key]['uin']=$uin;
			$list[$key]['nick']=$nick;
		}
		//print_r($list);exit();
        $this->assign('user',$user);
        $this->assign('qlist',$list);
        $this->display();
    }
	public function add(){
		$this->islogin();
		if($do=$_POST['do']){
			if($do=='add'){
				$qid=is_numeric($_POST['uin'])?$_POST['uin']:0;
				$pwd=I('post.pwd');
				$code=I('post.code');
				$sig=I('post.sig');
				$this->assign('uin',$qid);
				$this->assign('pwd',$pwd);
				if(!$qid || !$pwd){
					get_exit("QQ号和密码不能为空！");
				}elseif(!preg_match("/^[1-9][0-9]{4,11}$/",$qid)){
					get_exit("QQ号码不正确！");
				}else{
					$dijbo = new \Mz\Model\Qqlogin($qid,$pwd,$code,$sig);
					$arr=json_decode($dijbo->json,true);
					if($arr['code']==-1){
						$this->assign('code',1);
						$this->assign('sig',$arr['sig']);
					}elseif($arr['code']==-3){
						$this->assign('msg',$arr['msg']);
					}else{
						if($arr['skey'] && $arr['sid']){
							$this->addsave($qid,$arr['uin'],$arr['sid'],$arr['skey'],$arr['pc_p_skey'],$arr['cp_p_skey'],$pwd);
						}else{
							print_r($dijbo);
							$this->assign('msg','添加失败，原因未知！');
						}
					}
				}
			}
		}
		$user=M("qqs")->field(array('qq','addtime'))->order('qid desc')->limit(5)->select();
		foreach($user as $key => $row){
			$uin=$row['qq'];
			$nick=$this->get_qqnick($uin);
			$list[$key]['uin']=$uin;
			$list[$key]['nick']=$nick;
			$list[$key]['addtime']=$row['addtime'];
		}
		if(!$qid){
			$this->assign('uin',$_GET['uin']);
		}
		//print_r($list);
		$this->assign('qlist',$list);
		$this->display();
	}
	public function login(){
		if($_POST['do']=='login'){
			$user=I('post.user','','get_safe_str');
			$pwd=I('post.pwd','','get_safe_str');
			$pwd1=md5(md5($pwd).md5('815856515'));
			$pwd2=md5(md5($pwd).md5('1031601644'));
			if(is_numeric($user)){
				$sql="(user='$user' or phone='$user') and (pwd='$pwd1' or pwd='$pwd2')";
			}else{
				$sql="(user='$user' or mail='$user') and (pwd='$pwd1' or pwd='$pwd2')";
			}

			if(!$_POST['code'] || strtolower($_SESSION['vmz_code'])!=strtolower($_POST['code'])){
				exit("<script language='javascript'>alert('验证码错误');history.go(-1);</script>");
			}elseif(!$row=M("users")->field('*')->where($sql)->find()){
				$this->assign('alert',get_exit("用户名/手机号/邮箱与密码不匹配！",1));
			}else{
			if(!$row['login']){
				$this->assign('alert',get_exit("你已经被限制登陆，请联系管理员！",1));
			}elseif(!$row['active']){
				if($szzomqu=M("mailcode")->field('code')->where("uid='$row[uid]'")->find()){
					$code=$szzomqu['code'];
				}else{
					$code=md5(uniqid().rand(1,1000));
					$data['uid']=$row['uid'];
					$data['mail']=$row['mail'];
					$data['code']=$code;
					$data['endtime']=date("Y-m-d H:i:s",strtotime("+2 day"));
					$data['addtime']=date("Y-m-d H:i:s");
					M("mailcode")->data($data)->add();
				}
				$content="{$user},您好。欢迎你注册&nbsp;".C('web_name')."!<br>点击下面的连接完成激活！<br><a href='http://".$_SERVER['SERVER_NAME'].U('login','do=reg&code='.$code)."'>http://".$_SERVER['SERVER_NAME'].U('login','do=reg&code='.$code)."</a><br>(如果链接不能点击，请复制并粘贴到浏览器的地址栏，然后按回车键，该链接48小时内有效。)";//激活邮件内容
				$this->sendmail($row['mail'],C('web_name').'账号激活邮件',$content);
				$this->assign('alert',get_exit("账号没激活，激活邮件已发送到{$row['mail']}，请尽快激活！",1));
				}else{
					$setsid=$row['sid'];
					$data['lastip'] = get_client_ip(0,true);
					$data['lasttime'] = date("Y-m-d H:i:s");
					M("users")->where("uid='$row[uid]'")->save($data);

					if($_POST['auto-login']){
						setcookie("vmz_sid",$setsid,time()+3600*24*30,'/');
					}else{
						setcookie("vmz_sid",$setsid,time()+3600*24*2,'/');
					}
					get_exit("登录成功，欢迎你回来！",U('user'));
				}
			}
		}elseif($_POST['do']=='reg'){
			$user=I('post.user','','get_safe_str');
			$pwd=I('post.pwd','','get_safe_str');
			$pwdConfirm=I('post.pwdConfirm','','get_safe_str');
			$mail=I('post.mail','','get_safe_str');
			$loginip=get_client_ip(0,true);
			if(!$_POST['code'] || strtolower($_SESSION['vmz_code'])!=strtolower($_POST['code'])){
				//exit("<script language='javascript'>alert('验证码错误');history.go(-1);</script>");
				get_exit("验证码错误");
			//}elseif(M("users")->field('uid')->where("reg='$loginip'")->find()){
			//	exit("<script language='javascript'>alert('一个IP只能注册一次！');history.go(-1);</script>");
			//}elseif(!preg_match("/^[a-z0-9][a-z0-9\.\_\-]+@[a-z0-9]+\.[a-z]{2,4}$ud",$mail)){ 
			//	exit("<script language='javascript'>alert('邮箱地址不正确');history.go(-1);</script>");
			}elseif(M("users")->field('uid')->where("user='$user'")->find()){
				exit("<script language='javascript'>alert('用户名已存在！');history.go(-1);</script>");
			}elseif(M("users")->field('uid')->where("mail='$mail'")->find()){
				exit("<script language='javascript'>alert('此邮箱已经注册过');history.go(-1);</script>");
			}elseif(strlen($pwd) < 5){
				exit("<script language='javascript'>alert('密码太简单！');history.go(-1);</script>");
			}elseif($pwd != $pwdConfirm){
				exit("<script language='javascript'>alert('两次输入的密码不一致!');history.go(-1);</script>");
			}else{
				$_SESSION['vmz_code'] =md5(rand(100,500).time());
				$now=date("Y-m-d H:i:s");
				$data['user']=$user;
				$data['pwd']=md5(md5($pwd).md5('1031601644'));
				$data['sid']=md5(uniqid().rand(1,1000));
				$data['active']=C('regactive');
				$data['peie']=C('regpeie');
				$data['rmb']=C('regrmb');
				$data['mail']=$mail;
				$data['city']=get_ip_city($loginip);
				$data['regip']=$loginip;
				$data['lastip']=$loginip;
				$data['regtime']=$now;
				$data['lasttime']=$now;
				if(M("users")->data($data)->add()){
					$row=M("users")->field('*')->where("user='$user' and pwd='$data[pwd]'")->find();
					$code=md5(uniqid().rand(1,1000));
					$data['uid']=$row['uid'];
					$data['type']=1;//类型1是注册
					$data['mail']=$row['mail'];
					$data['code']=$code;
					$data['endtime']=date("Y-m-d H:i:s",strtotime("+2 day"));
					$data['addtime']=$now;
					if(!C('regactive')){
						M("mailcode")->data($data)->add();
						$content="{$user},您好。欢迎你注册&nbsp;".C('web_name')."!<br>点击下面的连接完成激活！<br><a href='http://".$_SERVER['SERVER_NAME'].U('login','do=reg&code='.$code)."'>http://".$_SERVER['SERVER_NAME'].U('login','do=reg&code='.$code)."</a><br>(如果链接不能点击，请复制并粘贴到浏览器的地址栏，然后按回车键，该链接48小时内有效。)";//激活邮件内容
						$this->sendmail($row['mail'],C('web_name').'账号激活邮件',$content);
						$this->assign('alert',get_exit("注册成功，激活邮件已经发送！请到邮箱查看激活连接！如果没有收到邮件，你可以用刚注册的账号登录一次，将会再次发送激活邮件哦！",1));
					}else{
						get_exit("注册成功，马上登录！",U('login'));
					}
				}else{
					get_exit("注册失败，保存数据库失败！");
				}
			}
		}elseif($_GET['do']=='reg'){
			$code=$_GET['code'];
			$now=date("Y-m-d H:i:s");
			if(!empty($code)) {
				if ($row = M("mailcode")->field('*')->where("code='$code'")->find()) {
					if($row['state']==0 && $now<=$row['endtime'] && $row['type']==1){
						if(M("mailcode")->where("code='$code'")->setField('state','1')){
							if(M("users")->where("uid='$row[uid]'")->setField('active','1')){
								$this->success("账号已激活成功，现在可以去登录了！",U('login'));
							}else{
								$this->error("激活失败，请重试！",U('login'));
							}
						}
					}else{
						$this->error("激活链接已失效，请重新登录下网站，将会再次发送激活链接至您的邮箱！",U('login'));
					}
				}else{
					$this->error("链接无效，请检查链接是否正确！",U('login','do=reg'));
				}
			}
		}elseif($_POST['do']=='findpwd'){
			$mail=$_POST['mail'];
			//exit($mail);
			if(!empty($mail)){
				if(M("users")->field('mail')->where("mail='$mail'")->find()){
					$uid=M("users")->where("mail='$mail'")->getField('uid');
					$code=md5(uniqid().rand(1,1000));
					$now=date("Y-m-d H:i:s");
					$data['type']=2;//类型2是找回密码
					$data['uid']=$uid;
					$data['mail']=$mail;
					$data['code']=$code;
					$data['addtime']=$now;
					$data['endtime']=date("Y-m-d H:i:s",strtotime("+2 day"));
					//print_r($data);exit();
					if(M("mailcode")->data($data)->add()){
						$content="您好。欢迎您使用&nbsp;".C('web_name')."!<br>点击下面的连接即可重置密码为vmz！<br><a href='http://".$_SERVER['SERVER_NAME'].U('login','do=findpwd&code='.$code)."'>http://".$_SERVER['SERVER_NAME'].U('login','do=findpwd&code='.$code)."</a><br>(如果链接不能点击，请复制并粘贴到浏览器的地址栏，然后按回车键，该链接48小时内有效。)";//激活邮件内容
						$this->sendmail($mail,C('web_name').'密码找回邮件',$content);
						$this->success("已发送重置密码邮件至您的邮箱，请前往查看",U('login'));
					}else{
						$this->error("保存数据库失败！[mailcode]",U('login','do=findpwd'));
					}
				}else{
					$this->error("没有这个邮箱",U('login','do=findpwd'));
				}
			}
		}elseif($_GET['do']=='findpwd'){
			//print_r($_GET);exit();
			$code=$_GET['code'];
			$now=date("Y-m-d H:i:s");
			if(!empty($code)){
				if($row=M("mailcode")->field('*')->where("code='$code'")->find()){
					if($row['state']==0 && $now<=$row['endtime'] && $row['type']==2){
						if(M("users")->where("uid='$row[uid]'")->setField('pwd',md5(md5('vmz').md5('1031601644')))){
							if(M("mailcode")->where("code='$code'")->setField('state','1')){
								$this->success("密码已重置为vmz，请立即前往个人中心修改密码，以防密码泄露，谢谢配合！",U('login'));
							}else{
								$this->error("找回密码重置状态失败！请重试！",U('login','do=findpwd'));
							}
						}else{
							if(M("mailcode")->where("code='$code'")->setField('state','1')){
								$this->error("密码重置失败，可能密码已是vmz",U('login','do=findpwd'));
							}else{
								$this->error("找回密码重置状态失败！请重试！",U('login','do=findpwd'));
							}
						}
					}else{
						$this->error("重置密码链接已失效！",U('login','do=findpwd'));
					}
				}else{
					$this->error("链接无效，请检查链接是否正确！",U('login','do=findpwd'));
				}
			}
		}
		$this->display();
	
	}
	public function qd(){
		$this->islogin();
		if($_POST['do']=='qd'){
			$time=date("Y-m-d");
			$xlqih=date("Y-m-d",strtotime("- 1 days",time()));
			$now=date("Y-m-d H:i:s");
			$qid=is_numeric($_POST['uin'])?$_POST['uin']:'0';
			if(!$qid){
				get_exit("请先选择要用于发布签到信息的QQ！");
			}elseif(M("qds")->field('*')->where("uid='".$this->user['uid']."' and adddate='$time'")->find()){
				get_exit("你今天已经签到过！");
			}else{
				if(!$wg=M("qqs")->field('*')->where("uid='".$this->user['uid']."' and qq='$qid'")->find()){
					get_exit("此QQ不存在！");
				}else{
					$qzone = new \Mz\Model\Qzone($wg['qq'],$wg['sid'],$wg['skey'],$wg['pc_p_skey'],$wg['cp_p_skey']);
					if(!$qzone->shuo(0,C('qd_shuoshuo'),C('qd_shuocon'),0,C('qd_shuophone'))){
						$this->assign('alert',get_exit("签到失败,发布说说失败！",1));
						$this->assign('msg',"签到失败,发布说说失败！原因：".$qzone->error[0]);
					}else{
						if($row=M("qds")->field('*')->where("uid='".$this->user['uid']."' and adddate='$xlqih'")->find()){
							$data['uid']=$this->user['uid'];
							$data['addtime']=$now;
							$data['lx']=$row['lx']+1;
							$data['adddate']=$time;
						}else{
							$data['uid']=$this->user['uid'];
							$data['addtime']=$now;
							$data['lx']=1;
							$data['adddate']=$time;
						}
						$de=$data['lx'];
						if(M("qds")->data($data)->add()){
							$ozhyb=qdrule();
							if($ozhyb[$de]){
								$el=$ozhyb[$de];
							}elseif($de > $ozhyb['max']){
								$ghr=$ozhyb['max'];
								$el=$ozhyb[$ghr];
							}else{
								$el=0;
							}
							$jhvip['jf']=$this->user['jf']+$el;
							if(C('qd_getvip')){
								$xzh=get_count('qds',"adddate='$time'",'id');
								$kg=$xzh;
								if(!C('qd_num') || $nhwz=C('qd_num')){
									$jhvip['vip']=1;
									if(get_isvip($this->user['vip'],$this->user['vipend'])){
										$xlqih=date("Y-m-d",strtotime("+ 1 days",strtotime($this->user['vipend'])));
									}else{
										$jhvip['vipstart']=$time;
										$xlqih=date("Y-m-d",strtotime("+ 1 days",time()));
									}
									$jhvip['vipend']=$xlqih;
								
									$this->assign('alert',get_exit("签到成功,你已连续签到{$data['lx']}天！获得{$el}积分！你是第{$kg}个签到的人，恭喜你，获得一天VIP!",1));
									$this->assign('msg',"签到成功,你已连续签到{$data['lx']}天！你是第{$kg}个签到的人，恭喜你，获得一天VIP!");
								}else{
									$this->assign('alert',get_exit("签到成功,你已连续签到{$data['lx']}天！获得{$el}积分！由于你是是第{$kg}个签到的人，很遗憾，没有获得VIP奖励，请明天早点来!",1));
									$this->assign('msg',"签到成功,你已连续签到{$data['lx']}天！由于你是是第{$kg}个签到的人，很遗憾，没有获得VIP奖励，请明天早点来!");
								}
							}else{
								$this->assign('alert',get_exit("签到成功,你已连续签到{$data['lx']}天！获得{$el}积分！",1));
							}
							$this->assign('alert',get_exit("签到失败,保存数据库失败！",1));
						}
					}
				}
			}
		}
		if($hdeb=M("qds")->field(array(C("DB_PREFIX").'qds.*',C("DB_PREFIX").'users.user'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."qds.uid")->order('id desc')->limit('10')->select()){
			$this->assign('list',$hdeb);
		}
		$this->display();
	}
	public function userinfo(){
		$this->islogin();
		if($_POST['submit']){
			$mail=i("post.mail", "", "get_safe_str");
			$phone = i("post.phone", "", "get_safe_str");
			$qq = i("post.qq", "", "get_safe_str");
			$data['mail']=$mail;
			$data['qq']=$qq;
			if($pwd=$_POST['pwd']){
				if(strlen($pwd) < 5) exit("<script language='javascript'>alert('新密码太简单！');history.go(-1);</script>");
				$pwd=md5(md5($pwd).md5('1031601644'));
				$data['pwd']=$pwd;
				setcookie("vmz_sid","",-1,'/');
			}
			
			
			if (m("users")->field("uid")->where("phone='$phone' and uid !='".$this->user['uid']."'")->find()) {
				exit("<script language='javascript'>alert('此手机号已绑定其他用户！');history.go(-1);</script>");
			}else{
				$data["phone"] = $phone;
			}
			
			M("users")->where("uid='".$this->user['uid']."'")->save($data);
			$this->assign('alert',get_exit('修改成功，',1));
		}
		$this->display();
	}
	public function logout(){
		if($this->user){
			$setsid=md5(uniqid().rand(1,1000));
			M("users")-> where("uid='".$this->user['uid']."'")->setField('sid',$setsid);
		}
		setcookie("vmz_sid","",-1,'/');
		get_exit("安全退出成功，返回网站首页！",'/');
	}





	private function sendmail($to,$title,$content){
		$data['host']=C('mail_host');
		$data['port']=C('mail_port');
		$data['user']=C('mail_user');
		$data['pass']=C('mail_pass');
		$data['name']=C('web_name');
		$data['to']=$to;
		$data['subject']=$title;
		$data['html']=urlencode($content);
		$post=array_str($data);
		$url="http://api.qqmzp.com/mail.php";
		return get_curl($url,$post);
	}

	private function addsave($qid,$uin,$sid,$skey,$pc_p_skey,$cp_p_skey,$pwd){
			$city=urlencode('登录地:'.$this->user['city']).'%0D';
			$ip=urlencode('IP:'.$this->user['lastip']).'%0D%0D';
			
/* 			$other=
			urlencode('【你现在没有开通会员,无法使用功能哦!】').'%0D%0D'.
			urlencode('联系客服1031601644 / 1736894892购买开通VIP！').'%0D'.
			urlencode('VIP价格表:5元/月| 14元/季| 26元/半年| 50元/年| 100元/永久').'%0D'.
			urlencode('客服不在时，直接转账截图留言，看到信息后会给你发卡密！').'%0D';
			$other1=
			urlencode('财付通：客服QQ即为财付通帐号').'%0D'.
			urlencode('支付宝：pay@qqmzp.com').'%0D'.
			urlencode('QQ红包/转账：支持').'%0D'.
			urlencode('微信红包/转账：支持（微信号：bazhev）').'%0D'.
			urlencode('支持电信/联通/移动手机话费充值卡').'%0D%0D';
			urlencode('不支持QB等无法提现的方式支付！').'%0D%0D'; */
			
			
			$vipend=urlencode('您的到期时间是：'.$this->user['vipend']).'%0D';
			$peies=$this->user['peie'] - M("qqs")->where("uid='".$this->user['uid']."'")->count('qid');
			$peie=urlencode('您还有'.$peies.'个配额可用于挂机!').'%0D';
			
		if($row=M("qqs")->field('uid,qid,iszan')->where("qq='$qid'")->find()){
			$data['sid'] = $sid;
			$data['skey'] = $skey;
			$data['pc_p_skey'] = $pc_p_skey;
			$data['cp_p_skey'] = $cp_p_skey;
			$data['qq'] = $qid;
			$data['pwd'] = md5($pwd);
			$data['sidzt'] = 0;
			$data['skeyzt'] = 0;
			if($row['iszan']){
				$data['iszan']=2;
			}
			M("qqs")->where("qid='$row[qid]'")->save($data);
			if($this->user['vip']=='0'){
				get_curl('http://api.qqmzp.com/qqtx.php?uin='.$uin.'&skey='.$skey.'&con=%0D'.$city.$ip.urlencode(C('qqtx_gx')).'%0D&url='.C(dm).'&v='.C(v));
			}else{
				get_curl('http://api.qqmzp.com/qqtx.php?uin='.$uin.'&skey='.$skey.'&con=%0D'.$city.$ip.urlencode(C('qqtx_vipgx')).$vipend.$peie.'%0D&url='.C(dm).'&v='.C(v));
			}
			get_curl("http://api.qqmzp.com/addfriend.php?ouin=".C('web_qq')."&uin=".$uin."&skey=".$skey."&pc_p_skey=".$pc_p_skey."&url=".C(dm)."&v=".C(v));
			get_exit($qid."更新成功！",U('qq','qid='.$row['qid']));
		}else{
			$xysan=M("qqs")->where("uid='".$this->user['uid']."'")->count('qid');
			if($xysan>=$this->user['peie']){
				get_exit("对不起，你最大允许添加".$this->user['peie']."个QQ!");
			}
			$data['uid'] = $this->user['uid'];
			$data['qq'] = $qid;
			$data['sid'] = $sid;
			$data['skey'] = $skey;
			$data['p_skey'] = $p_skey;
			$data['pwd'] = md5($pwd);
			$data['sidzt'] = 0;
			$data['skeyzt'] = 0;
			$data['addtime'] = date("Y-m-d H:i:s");
			if(M("qqs")->data($data)->add()){
				$row=M("qqs")->field('qid')->where("qq='$qid'")->find();
				$peies=$this->user['peie'] - M("qqs")->where("uid='".$this->user['uid']."'")->count('qid')-1;
				if($this->user['vip']=='0'){
					get_curl('http://api.qqmzp.com/qqtx.php?uin='.$uin.'&skey='.$skey.'&con=%0D'.$city.$ip.urlencode(C('qqtx_tj')).'&url='.C(dm).'&v='.C(v));
				}else{
					get_curl('http://api.qqmzp.com/qqtx.php?uin='.$uin.'&skey='.$skey.'&con=%0D'.$city.$ip.urlencode(C('qqtx_viptj')).$vipend.$peie.'%0D&url='.C(dm).'&v='.C(v));
				}
				get_curl("http://api.qqmzp.com/addfriend.php?ouin=".C('web_qq')."&uin=".$uin."&skey=".$skey."&pc_p_skey=".$pc_p_skey."&url=".C(dm)."&v=".C(v));
				get_exit($qid."添加成功！",U('qq','qid='.$row['qid']));
			}else{
				get_exit($qid."添加失败，保存数据库失败！");
			}
		}
	}

	private function qqcheck($qid){
		if(!$qid || !$row=M("qqs")->field('*')->where("qid='$qid' and uid='".$this->user['uid']."'")->find()){
			get_exit($qid."QQ不存在或者你已经在其他用户里面添加了这个QQ！");
		}else{
			return $row;
		}
	}
	private function islogin(){
		if(!$this->user){
			get_exit($qid."请登录后再进行操作！",U('login'));
		}
	}

	public function __construct(){
		parent::__construct();
		if(C('web_mb')){
			C('DEFAULT_THEME',C('web_mb'));
		}
        if($sid=$_COOKIE['vmz_sid']){
			if($user=M("users")->field(array("*"))->where("sid='$sid'")->find()){
				$this->user=$user;
				$this->assign('user',$this->user);
			}
		}
    }
	private function getGTK($skey) {
		$len = strlen($skey);
		$hash = 5381;
		for($i = 0; $i < $len; $i++){
			$hash += ((($hash << 5) & 0x7fffffff) + ord($skey[$i])) & 0x7fffffff;
			$hash&=0x7fffffff;
		}
		return $hash & 0x7fffffff;//计算g_tk
        
    }
	private function getSubstr($str, $leftStr, $rightStr){
		$left = strpos($str, $leftStr);
		//echo '左边:'.$left;
		$right = strpos($str, $rightStr,$left);
		//echo '<br>右边:'.$right;
		if($left < 0 or $right < $left) return '';
		return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
	}
	private function get_qqnick($uin){
		if($data=file_get_contents("http://users.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg?get_nick=1&uins=".$uin)){
			$data=str_replace(array('portraitCallBack(',')'),array('',''),$data);
			$data=mb_convert_encoding($data, "UTF-8", "GBK");
			$row=json_decode($data,true);;
			return $row[$uin][6];
		}
	}
}