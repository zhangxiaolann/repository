<?php


namespace Mz\Controller;
use Think\Controller;
class DailiController extends Controller {
	public function index(){
		
		$this->display();
	}
	public function km(){
		$±¦=$_GET['xz'];
		$Σς=is_numeric($_GET['kid'])?$_GET['kid']:'0';
		if($_GET['do']=='del' && $Σς){
			if(!$ΣΎΞ=M("kms")->field('*')->where("kid='$Σς' and daili='".$this->user['uid']."'")->find()){
				$this->assign('alert',get_exit("θ¦ε ι€ηε‘ε―δΈε­ε¨οΌ",1));
				$Σς=0;
			}else{
				if(!$ΣΎΞ['isuse']){
					$=$ΣΎΞ['ms'];
					if($ΣΎΞ['kind']==1){
						if($==10){
							$=C('price_daili_10peie');
						}elseif($==5){
							$=C('price_daili_5peie');
						}elseif($==3){
							$=C('price_daili_3peie');
						}else{
							$=1;
							$=C('price_daili_1peie');
						}
					}elseif($ΣΎΞ['kind']==0){
						if($==127){
							$=C('price_daili_0vip');
						}elseif($==12){
							$=C('price_daili_12vip');
						}elseif($==6){
							$=C('price_daili_6vip');
						}elseif($==3){
							$=C('price_daili_3vip');
						}else{
							$=1;
							$=C('price_daili_1vip');
						}
					}
					if(M('kms')->where("kid='$Σς'")->delete()){
						M('users')->where("uid='".$this->user['uid']."'")->setInc('rmb',$);
						$this->assign('user',M("users")->field('*')->where("uid='".$this->user['uid']."'")->find());
						$this->assign('alert',get_exit('ε ι€ε‘ε―ζεοΌη±δΊζ­€ε‘ε―ζ²‘ζδ½Ώη¨οΌζειθΏ'.$.'εε°δ½ θ΄¦ζ·οΌ',1));
					}else{
						$this->assign('alert',get_exit("ε‘ε―ε ι€ε€±θ΄₯οΌ",1));
					}
				}else{
					if(M('kms')->where("kid='$Σς'")->delete()){
						$this->assign('alert',get_exit('ε ι€ε‘ε―ζεοΌ',1));
					}else{
						$this->assign('alert',get_exit("ε‘ε―ε ι€ε€±θ΄₯οΌ",1));
					}
				}
			}
		}
		if($_POST['do']=='add'){
			$υ=is_numeric($_POST['num'])?$_POST['num']:'1' ;
			$=is_numeric($_POST['ms'])?$_POST['ms']:'1';
			if($±¦=='peie'){
				$Σς=1;
			}elseif($±¦=='sy'){
				$Σς=2;
			}else{
				$Σς=0;
			}
			if($Σς==1){
				if($==10){
					$=C('price_daili_10peie');
				}elseif($==5){
					$=C('price_daili_5peie');
				}elseif($==3){
					$=C('price_daili_3peie');
				}else{
					$=1;
					$=C('price_daili_1peie');
				}
			}else{
				if($==127){
					$=C('price_daili_0vip');
				}elseif($==12){
					$=C('price_daili_12vip');
				}elseif($==6){
					$=C('price_daili_6vip');
				}elseif($==3){
					$=C('price_daili_3vip');
				}else{
					$=1;
					$=C('price_daili_1vip');
				}
			}
			if($this->user['rmb']>=$){
				$g="<ul class='list-group'>
				<li class='list-group-item active'>ζεηζδ»₯δΈε‘ε―</li>";
				for($ς=0;$ς<$υ;$ς++){
					$ϊ=array();
					$ϊ['kind'] = $Σς;
					$ϊ['daili']=$this->user['uid'];
					$ϊ['km'] = $this->getkm(12);
					$ϊ['ms'] = $;
					$ϊ['isuse'] = 0;
					$ϊ['addtime'] = date("Y-m-d H:i:s");
					if($this->user['rmb']>=$){
						if(M("kms")->data($ϊ)->add()){
							M("users")->where("uid='".$this->user['uid']."'")->setDec('rmb',$); 
							$this->assign('user',M("users")->field('*')->where("uid='".$this->user['uid']."'")->find());
							$g.="<li class='list-group-item'>{$ϊ[km]}</li>";
						}else{
							$g.="<li class='list-group-item list-group-item-danger' style='color:red;'>ε‘ε―ηζε€±θ΄₯οΌ</li>";
						}
					}else{
						$g.="<li class='list-group-item list-group-item-danger' style='color:red;'>θ΄¦ζ·δ½ι’δΈθΆ³οΌ</li>";
					}
				}
				$g.="</ul>";
			}else{
				$g="<ul class='list-group'>
				<li class='list-group-item list-group-item-danger'>θ΄¦ζ·δ½ι’δΈθΆ³οΌθ―·εεεΌοΌ</li></ul>";
			}
			$this->assign('msg',$g);
		}
		if($_POST['do']=='search' && $=I('post.key','','get_safe_str')){
			$Ξεάά="km like '%$%' and ".C("DB_PREFIX")."kms.daili='".$this->user['uid']."'";
			if($±¦=='peie'){
				$Ξεάά.=" and kind=1";
			}elseif($±¦=='sy'){
				$Ξεάά.=" and kind=2";
			}else{
				$Ξεάά.=" and kind=0";
			}
			$ΎΞ=M("kms")->field(array(C("DB_PREFIX").'kms.*',C("DB_PREFIX").'users.user',C("DB_PREFIX").'users.qq'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."kms.daili")->where($Ξεάά)->limit(12)->order('kid desc')->select();
			$χ=1;$χages=1;$οΎυϊ=1;
		}else{
			$χ=is_numeric($_GET['p'])?$_GET['p']:'1';
			$tart=12*($χ-1);
			$ά±ϊ=$χ+1;
			$ςςϊ="$tart,12";
			if($±¦=='peie'){
				$Ξεάά="kind=1";
			}elseif($±¦=='sy'){
				$Ξεάά="kind=2";
			}else{
				$Ξεάά="kind=0";
			}
			$Ξεάά.=" and ".C("DB_PREFIX")."kms.daili='".$this->user['uid']."'";
			$οΎυϊ=M("kms")->where($Ξεάά)->count('kid');
			$χages=ceil($οΎυϊ/12);
			$ΎΞ=M("kms")->field(array(C("DB_PREFIX").'kms.*',C("DB_PREFIX").'users.user',C("DB_PREFIX").'users.qq'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."kms.daili")->where($Ξεάά)->limit($ςςϊ)->order('kid desc')->select();
			if(($χ-1)>0){
				$tart=$χ-1;
			}else{
				$tart=1;
			}
			if(($χ+5)<$χages){
				$ά=$χ+5;
			}else{
				$ά=$χages;
			}
			$this->assign('end',$ά);
			$this->assign('start',$tart);
		}
		$this->assign('xz',$±¦);
		$this->assign('page',$χ);
		$this->assign('pages',$χages);
		$this->assign('count',$οΎυϊ);
		$this->assign('list',$ΎΞ);
		$this->display();
	}

	private function getkm($ά=12){
		$tr ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$trlen = strlen($tr);
		$ϊ = '';
		for ($ς = 0; $ς<$ά; $ς++){
			$ϊ .= $tr[mt_rand(0, $trlen-1)];
		}
		return $ϊ;
	}
	public function __construct(){
		parent::__construct();
		if($id=get_safe_str($_COOKIE['vmz_sid'])){
			if($υά=M("users")->field(array("*"))->where("sid='$id'")->find()){
				if(!$υά['daili']){
					get_exit($υς."δ½ δΈζ―δ»£ηοΌ",U('index/user'));
				}else{
					$this->user=$υά;
					$this->assign('user',$this->user);
				}
			}else{
				get_exit($υς."θ―·η»ε½εεθΏθ‘ζδ½οΌ",U('index/login'));
			}
		}else{
			get_exit($υς."θ―·η»ε½εεθΏθ‘ζδ½οΌ",U('index/login'));
		}
    }
}