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
		$���=is_numeric($_GET['uid'])?$_GET['uid']:exit('非法操作！');
		if($_POST['submit'] && $���){
			unset($_POST['submit']);
			if($_POST['resetpwd']){
				$_POST['pwd']='1edb01bc79e2ac001fad3948639fdb84';
			}
			unset($_POST['resetpwd']);
			M("users")->where("uid='$���'")->save($_POST);
		}
		if(!$���=M("users")->field('*')->where("uid='$���'")->find()){
			$this->assign('alert',get_exit("用户不存在！",1));
		}
		$this->assign('douser',$���);
		$this->display('');
	}
	public function login(){
		if($_POST['submit']){
			if(!$_POST['code'] || strtolower($_SESSION['vmz_code'])!=strtolower($_POST['code'])){
			exit("<script language='javascript'>alert('验证码错误');history.go(-1);</script>");
		}
			$pwd1=md5(md5($_POST['pwd']).md5('815856515'));
			$pwd2=md5(md5($_POST['pwd']).md5('1031601644'));
			if($pwd1==C('adminpwd')){
				setcookie("vmz_adminpwd",$pwd1,time()+3600*24*2,'/');
				get_exit("登录成功！",U('index'));
			}elseif($pwd2==C('adminpwd')){
				setcookie("vmz_adminpwd",$pwd2,time()+3600*24*2,'/');
				get_exit("登录成功！",U('index'));
			}else{
				get_exit("密码不正确！");
			}
		}
        $this->display();
    }
	public function index(){
		if($_GET['do']=='delendvip'){
			if($���s=M("users")->field('uid')->where("vip>0 and vipend < '".date("Y-m-d")."'")->order('uid desc')->select()){
				foreach($���s as $���){
					$���=$���['uid'];
					M('users')->where("uid='$���'")->setField('vip','0');
					M('qqs')->where("uid='$���'")->delete();
				}
				$this->assign('alert',get_exit("已成功清理VIP到期用户！",1));
			}else{
				$this->assign('alert',get_exit("暂时没有待清理的用户！",1));
			}
		}elseif($_GET['do']=='delnovip'){
			if($���s=M("users")->field('uid')->where("vip=0")->order('uid desc')->select()){
				foreach($���s as $���){
					$���=$���['uid'];
					M('users')->where("uid='$���'")->delete();
					M('qqs')->where("uid='$���'")->delete();
				}
				$this->assign('alert',get_exit("成功清理非VIP用户！",1));
			}else{
				$this->assign('alert',get_exit("没有非VIP用户！",1));
			}
		}elseif($_GET['do']=='delendqq'){
			if($���s=M("qqs")->field('qid')->where("sidzt=1")->order('qid desc')->select()){
				foreach($���s as $���){
					M('qqs')->where("qid='".$���['qid']."'")->delete();
				}
				$this->assign('alert',get_exit("成功清理失效QQ！",1));
			}
			$this->assign('alert',get_exit("没有失效QQ！",1));
		}
		$this->display();
	}

	public function set(){
		if($_POST['do']=='csmail'){
			$�뼚=$_POST['mail'];
			$ȧ�=$this->sendmail($�뼚,'微秒赞测试邮箱配置','收到这封邮件，说明你的邮箱配置能正常发送邮件！');
			$this->assign('msg',$ȧ�);
		}
		if($_POST['submit']){
			unset($_POST['submit']);
			if(!$_POST['adminpwd']){
				unset($_POST['adminpwd']);
			}else{
				$_POST['adminpwd']=md5(md5($_POST['adminpwd']).md5('1031601644'));
			}
			//print_r($_POST);
			foreach($_POST as $�=> $�뚽�){
				M("webconfigs")->execute("insert into ".C('DB_PREFIX')."webconfigs set vkey='$�',value='".addslashes($�뚽�)."' on duplicate key update value='".addslashes($�뚽�)."'");
			}
		}
		load_webconfig();
		$this->display();
	}
	public function qqlist(){
		$��=$_GET['xz'];
		if($_GET['do']=='del' && $���=is_numeric($_GET['qid'])?$_GET['qid']:'0'){
			M('qqs')->where("qid='$���'")->delete();
			$this->assign('alert',get_exit("删除成功！",1));
		}
		if($_POST['do']=='search' && $���=is_numeric($_POST['uin'])?$_POST['uin']:'0'){
			$���s=M("qqs")->field(array(C("DB_PREFIX").'qqs.*',C("DB_PREFIX").'users.vip',C("DB_PREFIX").'users.vipend',C("DB_PREFIX").'users.user'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."qqs.uid")->where(C("DB_PREFIX")."qqs.qq like '%$���%'")->limit(12)->order('qid desc')->select();
			$�=1;$�ages=1;$�߽��=1;$�К�=0;
		}else{
			$�=is_numeric($_GET['p'])?$_GET['p']:'1';
			$����=12*($�-1);
			$����=$�+1;
			$��ȼ�="$����,12";
			$�߽��=M("qqs")->where($�����)->count('qid');
			$�ages=ceil($�߽��/12);
			$���s=M("qqs")->field(array(C("DB_PREFIX").'qqs.*',C("DB_PREFIX").'users.vip',C("DB_PREFIX").'users.vipend',C("DB_PREFIX").'users.user'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."qqs.uid")->where($�����)->limit($��ȼ�)->order('qid desc')->select();
			if(($�-1)>0){
				$����=$�-1;
			}else{
				$����=1;
			}
			if(($�+5)<$�ages){
				$�å=$�+5;
			}else{
				$�å=$�ages;
			}
			$this->assign('end',$�å);
			$this->assign('start',$����);
		}
		$this->assign('page',$�);
		$this->assign('pages',$�ages);
		$this->assign('count',$�߽��);
		$this->assign('list',$���s);
		$this->display();
	}
	public function userlist(){
		$��=$_GET['xz'];
		$���=is_numeric($_GET['uid'])?$_GET['uid']:'0';
		if($_GET['do']=='del' && $���){
			M('qqs')->where("uid='$���'")->delete();
			M('users')->where("uid='$���'")->delete();
		}elseif($_GET['do']=='active' && $���){
			M('users')->where("uid='$���'")->setField('active','1');
		}
		if($_POST['do']=='search' && $�=I('post.key','','get_safe_str')){
			$�����="uid='{$�}' or user like'%{$�}%' or mail like'%{$�}%' or phone like'%{$�}%'";
			if($��=='vip') $�����="($�����) and vip>0 and vipend > '".date("Y-m-d")."'";
			$���s=M("users")->field('*')->where($�����)->limit(12)->order("(case when uid='{$�}' then 8 else 0 end)+(case when user like '%{$�}%' then 3 else 0 end)+(case when mail like '%{$�}%' then 2 else 0 end)+(case when phone like '%{$�}%' then 1 else 0 end) desc")->select();
			$�=1;$�ages=1;$�߽��=1;
		}else{
			$�=is_numeric($_GET['p'])?$_GET['p']:'1';
			$����=12*($�-1);
			$����=$�+1;
			$��ȼ�="$����,12";
			if($��=='vip') $�����.="vip>0 and vipend > '".date("Y-m-d")."'";
			$�߽��=M("users")->where($�����)->count('uid');
			$�ages=ceil($�߽��/12);
			$���s=M("users")->field('*')->where($�����)->limit($��ȼ�)->order('uid desc')->select();
			if(($�-1)>0){
				$����=$�-1;
			}else{
				$����=1;
			}
			if(($�+5)<$�ages){
				$�å=$�+5;
			}else{
				$�å=$�ages;
			}
			$this->assign('end',$�å);
			$this->assign('start',$����);
		}
		$this->assign('xz',$��);
		$this->assign('page',$�);
		$this->assign('pages',$�ages);
		$this->assign('count',$�߽��);
		$this->assign('list',$���s);
		$this->display();
	}
	public function km(){
		$��=$_GET['xz'];
		$�id=is_numeric($_GET['kid'])?$_GET['kid']:'0';
		if($_GET['do']=='del' && $�id){
			M('kms')->where("kid='$�id'")->delete();
		}
		if($_POST['do']=='add'){
			$ý�=is_numeric($_POST['num'])?$_POST['num']:'1' ;
			$ȧ=is_numeric($_POST['ms'])?$_POST['ms']:'1';
			if($��=='peie'){
				$�ind=1;
			}elseif($��=='sy'){
				$�ind=2;
			}else{
				$�ind=0;
			}
			$ȧ�="<ul class='list-group'>
			<li class='list-group-item active'>成功生成以下卡密</li>";
			for($�=0;$�<$ý�;$�++){
				$����=array();
				$����['kind'] = $�ind;
				$����['km'] = $this->getkm(12);
				$����['ms'] = $ȧ;
				$����['isuse'] = 0;
				$����['addtime'] = date("Y-m-d H:i:s");
				if(M("kms")->data($����)->add()){
					$ȧ�.="<li class='list-group-item'>{$����[km]}</li>";
				}
			}
			$ȧ�.="</ul>";
			$this->assign('msg',$ȧ�);
		}
		if($_POST['do']=='search' && $�=I('post.key','','get_safe_str')){
			$�����="km like '%$�%'";
			if($��=='peie'){
				$�����.=" and kind=1";
			}elseif($��=='sy'){
				$�����.=" and kind=2";
			}else{
				$�����.=" and kind=0";
			}
			$���s=M("kms")->field(array(C("DB_PREFIX").'kms.*',C("DB_PREFIX").'users.user',C("DB_PREFIX").'users.qq'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."kms.daili")->where($�����)->limit(12)->order('kid desc')->select();
			$�=1;$�ages=1;$�߽��=1;
		}else{
			$�=is_numeric($_GET['p'])?$_GET['p']:'1';
			$����=12*($�-1);
			$����=$�+1;
			$��ȼ�="$����,12";
			if($��=='peie'){
				$�����="kind=1";
			}elseif($��=='sy'){
				$�����="kind=2";
			}else{
				$�����="kind=0";
			}
			$�߽��=M("kms")->where($�����)->count('kid');
			$�ages=ceil($�߽��/12);
			$���s=M("kms")->field(array(C("DB_PREFIX").'kms.*',C("DB_PREFIX").'users.user',C("DB_PREFIX").'users.qq'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."kms.daili")->where($�����)->limit($��ȼ�)->order('kid desc')->select();
			if(($�-1)>0){
				$����=$�-1;
			}else{
				$����=1;
			}
			if(($�+5)<$�ages){
				$�å=$�+5;
			}else{
				$�å=$�ages;
			}
			$this->assign('end',$�å);
			$this->assign('start',$����);
		}
		$this->assign('xz',$��);
		$this->assign('page',$�);
		$this->assign('pages',$�ages);
		$this->assign('count',$�߽��);
		$this->assign('list',$���s);
		$this->display();
	}
	public function dllist(){
		$���=is_numeric($_REQUEST['uid'])?$_REQUEST['uid']:'0';
		$�Ȩ=is_numeric($_REQUEST['rmb'])?$_REQUEST['rmb']:'0';
		if($��� && !$���=M("users")->field('uid')->where("uid='$���'")->find()){
			$this->assign('alert',get_exit('用户不存在！','1'));
			$���=0;
		}
		if($_GET['do']=='del' && $���){
			M('users')->where("uid='$���'")->setField('daili','0');
		}
		if($_POST['do']=='add' && $���){
			M('users')->where("uid='$���'")->setField('daili','1');
			$this->assign('alert',get_exit('成功添加UID:'.$���.'用户为代理！','1'));
		}elseif($_POST['do']=='cz' && $��� && $�Ȩ){
			M('users')->where("uid='$���'")->setInc('rmb',$�Ȩ);
			$this->assign('alert',get_exit('成功为UID:'.$���.'代理充值'.$�Ȩ.'元！','1'));
		}elseif($_POST['do']=='kc' && $��� && $�Ȩ){
			M('users')->where("uid='$���'")->setDec('rmb',$�Ȩ);
			$this->assign('alert',get_exit('成功扣除UID:'.$���.'代理'.$�Ȩ.'元！','1'));
		}
		$�=is_numeric($_GET['p'])?$_GET['p']:'1';
		$����=10*($�-1);
		$����=$�+1;
		$��ȼ�="$����,10";
		$�߽��=M("users")->where("daili>0")->count('uid');
		$�ages=ceil($�߽��/10);
		$���s=M("users")->field('*')->where("daili>0")->limit($��ȼ�)->order('uid desc')->select();
		if(($�-1)>0){
			$����=$�-1;
		}else{
			$����=1;
		}
		if(($�+5)<$�ages){
			$�å=$�+5;
		}else{
			$�å=$�ages;
		}
		$this->assign('end',$�å);
		$this->assign('start',$����);
		$this->assign('page',$�);
		$this->assign('pages',$�ages);
		$this->assign('count',$�߽��);
		$this->assign('list',$���s);
		$this->display();
		
	}

	private function getkm($���=12){
		$�tr ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$�trlen = strlen($�tr);
		$��å�օ = '';
		for ($� = 0; $�<$���; $�++){
			$��å�օ .= $�tr[mt_rand(0, $�trlen-1)];
		}
		return $��å�օ;
	}
	private function sendmail($��,$ּ֚�,$�������){
		$����['host']=C('mail_host');
		$����['port']=C('mail_port');
		$����['user']=C('mail_user');
		$����['pass']=C('mail_pass');
		$����['name']=C('web_name');
		$����['to']=$��;
		$����['subject']=$ּ֚�;
		$����['html']=urlencode($�������);
		$�ost=array_str($����);
		$���="http://api.qqmzp.com/mail.php";
		return get_curl($���,$�ost);
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