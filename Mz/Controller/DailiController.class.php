<?php
// 此程序由微秒赞（洛绝尘）深度定制修改 <1031601644@qq.com>
// 底包为快乐是福1.81 <815856515@qq.com>
// 人不要脸，天下无敌。 儿子你要改版权爸爸也不拦你，尤其是龙魂儿子

namespace Mz\Controller;
use Think\Controller;
class DailiController extends Controller {
	public function index(){
		
		$this->display();
	}
	public function km(){
		$��=$_GET['xz'];
		$��=is_numeric($_GET['kid'])?$_GET['kid']:'0';
		if($_GET['do']=='del' && $��){
			if(!$Ә���=M("kms")->field('*')->where("kid='$��' and daili='".$this->user['uid']."'")->find()){
				$this->assign('alert',get_exit("要删除的卡密不存在！",1));
				$��=0;
			}else{
				if(!$Ә���['isuse']){
					$��=$Ә���['ms'];
					if($Ә���['kind']==1){
						if($��==10){
							$���=C('price_daili_10peie');
						}elseif($��==5){
							$���=C('price_daili_5peie');
						}elseif($��==3){
							$���=C('price_daili_3peie');
						}else{
							$��=1;
							$���=C('price_daili_1peie');
						}
					}elseif($Ә���['kind']==0){
						if($��==127){
							$���=C('price_daili_0vip');
						}elseif($��==12){
							$���=C('price_daili_12vip');
						}elseif($��==6){
							$���=C('price_daili_6vip');
						}elseif($��==3){
							$���=C('price_daili_3vip');
						}else{
							$��=1;
							$���=C('price_daili_1vip');
						}
					}
					if(M('kms')->where("kid='$��'")->delete()){
						M('users')->where("uid='".$this->user['uid']."'")->setInc('rmb',$���);
						$this->assign('user',M("users")->field('*')->where("uid='".$this->user['uid']."'")->find());
						$this->assign('alert',get_exit('删除卡密成功，由于此卡密没有使用，成功退还'.$���.'元到你账户！',1));
					}else{
						$this->assign('alert',get_exit("卡密删除失败！",1));
					}
				}else{
					if(M('kms')->where("kid='$��'")->delete()){
						$this->assign('alert',get_exit('删除卡密成功！',1));
					}else{
						$this->assign('alert',get_exit("卡密删除失败！",1));
					}
				}
			}
		}
		if($_POST['do']=='add'){
			$���=is_numeric($_POST['num'])?$_POST['num']:'1' ;
			$��=is_numeric($_POST['ms'])?$_POST['ms']:'1';
			if($��=='peie'){
				$��=1;
			}elseif($��=='sy'){
				$��=2;
			}else{
				$��=0;
			}
			if($��==1){
				if($��==10){
					$���=C('price_daili_10peie');
				}elseif($��==5){
					$���=C('price_daili_5peie');
				}elseif($��==3){
					$���=C('price_daili_3peie');
				}else{
					$��=1;
					$���=C('price_daili_1peie');
				}
			}else{
				if($��==127){
					$���=C('price_daili_0vip');
				}elseif($��==12){
					$���=C('price_daili_12vip');
				}elseif($��==6){
					$���=C('price_daili_6vip');
				}elseif($��==3){
					$���=C('price_daili_3vip');
				}else{
					$��=1;
					$���=C('price_daili_1vip');
				}
			}
			if($this->user['rmb']>=$���){
				$��g="<ul class='list-group'>
				<li class='list-group-item active'>成功生成以下卡密</li>";
				for($�=0;$�<$���;$�++){
					$����=array();
					$����['kind'] = $��;
					$����['daili']=$this->user['uid'];
					$����['km'] = $this->getkm(12);
					$����['ms'] = $��;
					$����['isuse'] = 0;
					$����['addtime'] = date("Y-m-d H:i:s");
					if($this->user['rmb']>=$���){
						if(M("kms")->data($����)->add()){
							M("users")->where("uid='".$this->user['uid']."'")->setDec('rmb',$���); 
							$this->assign('user',M("users")->field('*')->where("uid='".$this->user['uid']."'")->find());
							$��g.="<li class='list-group-item'>{$����[km]}</li>";
						}else{
							$��g.="<li class='list-group-item list-group-item-danger' style='color:red;'>卡密生成失败！</li>";
						}
					}else{
						$��g.="<li class='list-group-item list-group-item-danger' style='color:red;'>账户余额不足！</li>";
					}
				}
				$��g.="</ul>";
			}else{
				$��g="<ul class='list-group'>
				<li class='list-group-item list-group-item-danger'>账户余额不足，请先充值！</li></ul>";
			}
			$this->assign('msg',$��g);
		}
		if($_POST['do']=='search' && $�=I('post.key','','get_safe_str')){
			$��܌�="km like '%$�%' and ".C("DB_PREFIX")."kms.daili='".$this->user['uid']."'";
			if($��=='peie'){
				$��܌�.=" and kind=1";
			}elseif($��=='sy'){
				$��܌�.=" and kind=2";
			}else{
				$��܌�.=" and kind=0";
			}
			$��Έ=M("kms")->field(array(C("DB_PREFIX").'kms.*',C("DB_PREFIX").'users.user',C("DB_PREFIX").'users.qq'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."kms.daili")->where($��܌�)->limit(12)->order('kid desc')->select();
			$�=1;$�ages=1;$����=1;
		}else{
			$�=is_numeric($_GET['p'])?$_GET['p']:'1';
			$�tart=12*($�-1);
			$�ܱ�=$�+1;
			$����="$�tart,12";
			if($��=='peie'){
				$��܌�="kind=1";
			}elseif($��=='sy'){
				$��܌�="kind=2";
			}else{
				$��܌�="kind=0";
			}
			$��܌�.=" and ".C("DB_PREFIX")."kms.daili='".$this->user['uid']."'";
			$����=M("kms")->where($��܌�)->count('kid');
			$�ages=ceil($����/12);
			$��Έ=M("kms")->field(array(C("DB_PREFIX").'kms.*',C("DB_PREFIX").'users.user',C("DB_PREFIX").'users.qq'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."kms.daili")->where($��܌�)->limit($����)->order('kid desc')->select();
			if(($�-1)>0){
				$�tart=$�-1;
			}else{
				$�tart=1;
			}
			if(($�+5)<$�ages){
				$܋�=$�+5;
			}else{
				$܋�=$�ages;
			}
			$this->assign('end',$܋�);
			$this->assign('start',$�tart);
		}
		$this->assign('xz',$��);
		$this->assign('page',$�);
		$this->assign('pages',$�ages);
		$this->assign('count',$����);
		$this->assign('list',$��Έ);
		$this->display();
	}

	private function getkm($�܋=12){
		$�tr ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$�trlen = strlen($�tr);
		$������� = '';
		for ($� = 0; $�<$�܋; $�++){
			$������� .= $�tr[mt_rand(0, $�trlen-1)];
		}
		return $�������;
	}
	public function __construct(){
		parent::__construct();
		if($�id=get_safe_str($_COOKIE['vmz_sid'])){
			if($��܌=M("users")->field(array("*"))->where("sid='$�id'")->find()){
				if(!$��܌['daili']){
					get_exit($��."你不是代理！",U('index/user'));
				}else{
					$this->user=$��܌;
					$this->assign('user',$this->user);
				}
			}else{
				get_exit($��."请登录后再进行操作！",U('index/login'));
			}
		}else{
			get_exit($��."请登录后再进行操作！",U('index/login'));
		}
    }
}