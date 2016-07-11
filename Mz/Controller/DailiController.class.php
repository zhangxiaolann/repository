<?php
// æ­¤ç¨‹åºç”±å¾®ç§’èµï¼ˆæ´›ç»å°˜ï¼‰æ·±åº¦å®šåˆ¶ä¿®æ”¹ <1031601644@qq.com>
// åº•åŒ…ä¸ºå¿«ä¹æ˜¯ç¦1.81 <815856515@qq.com>
// äººä¸è¦è„¸ï¼Œå¤©ä¸‹æ— æ•Œã€‚ å„¿å­ä½ è¦æ”¹ç‰ˆæƒçˆ¸çˆ¸ä¹Ÿä¸æ‹¦ä½ ï¼Œå°¤å…¶æ˜¯é¾™é­‚å„¿å­

namespace Mz\Controller;
use Think\Controller;
class DailiController extends Controller {
	public function index(){
		
		$this->display();
	}
	public function km(){
		$±¦=$_GET['xz'];
		$Óò•=is_numeric($_GET['kid'])?$_GET['kid']:'0';
		if($_GET['do']=='del' && $Óò•){
			if(!$Ó˜Œ¾Î=M("kms")->field('*')->where("kid='$Óò•' and daili='".$this->user['uid']."'")->find()){
				$this->assign('alert',get_exit("è¦åˆ é™¤çš„å¡å¯†ä¸å­˜åœ¨ï¼",1));
				$Óò•=0;
			}else{
				if(!$Ó˜Œ¾Î['isuse']){
					$˜ˆ=$Ó˜Œ¾Î['ms'];
					if($Ó˜Œ¾Î['kind']==1){
						if($˜ˆ==10){
							$Œ˜=C('price_daili_10peie');
						}elseif($˜ˆ==5){
							$Œ˜=C('price_daili_5peie');
						}elseif($˜ˆ==3){
							$Œ˜=C('price_daili_3peie');
						}else{
							$˜ˆ=1;
							$Œ˜=C('price_daili_1peie');
						}
					}elseif($Ó˜Œ¾Î['kind']==0){
						if($˜ˆ==127){
							$Œ˜=C('price_daili_0vip');
						}elseif($˜ˆ==12){
							$Œ˜=C('price_daili_12vip');
						}elseif($˜ˆ==6){
							$Œ˜=C('price_daili_6vip');
						}elseif($˜ˆ==3){
							$Œ˜=C('price_daili_3vip');
						}else{
							$˜ˆ=1;
							$Œ˜=C('price_daili_1vip');
						}
					}
					if(M('kms')->where("kid='$Óò•'")->delete()){
						M('users')->where("uid='".$this->user['uid']."'")->setInc('rmb',$Œ˜);
						$this->assign('user',M("users")->field('*')->where("uid='".$this->user['uid']."'")->find());
						$this->assign('alert',get_exit('åˆ é™¤å¡å¯†æˆåŠŸï¼Œç”±äºæ­¤å¡å¯†æ²¡æœ‰ä½¿ç”¨ï¼ŒæˆåŠŸé€€è¿˜'.$Œ˜.'å…ƒåˆ°ä½ è´¦æˆ·ï¼',1));
					}else{
						$this->assign('alert',get_exit("å¡å¯†åˆ é™¤å¤±è´¥ï¼",1));
					}
				}else{
					if(M('kms')->where("kid='$Óò•'")->delete()){
						$this->assign('alert',get_exit('åˆ é™¤å¡å¯†æˆåŠŸï¼',1));
					}else{
						$this->assign('alert',get_exit("å¡å¯†åˆ é™¤å¤±è´¥ï¼",1));
					}
				}
			}
		}
		if($_POST['do']=='add'){
			$‹õ˜=is_numeric($_POST['num'])?$_POST['num']:'1' ;
			$˜ˆ=is_numeric($_POST['ms'])?$_POST['ms']:'1';
			if($±¦=='peie'){
				$Óò‹•=1;
			}elseif($±¦=='sy'){
				$Óò‹•=2;
			}else{
				$Óò‹•=0;
			}
			if($Óò‹•==1){
				if($˜ˆ==10){
					$Œ˜=C('price_daili_10peie');
				}elseif($˜ˆ==5){
					$Œ˜=C('price_daili_5peie');
				}elseif($˜ˆ==3){
					$Œ˜=C('price_daili_3peie');
				}else{
					$˜ˆ=1;
					$Œ˜=C('price_daili_1peie');
				}
			}else{
				if($˜ˆ==127){
					$Œ˜=C('price_daili_0vip');
				}elseif($˜ˆ==12){
					$Œ˜=C('price_daili_12vip');
				}elseif($˜ˆ==6){
					$Œ˜=C('price_daili_6vip');
				}elseif($˜ˆ==3){
					$Œ˜=C('price_daili_3vip');
				}else{
					$˜ˆ=1;
					$Œ˜=C('price_daili_1vip');
				}
			}
			if($this->user['rmb']>=$Œ˜){
				$˜ˆg="<ul class='list-group'>
				<li class='list-group-item active'>æˆåŠŸç”Ÿæˆä»¥ä¸‹å¡å¯†</li>";
				for($ò=0;$ò<$‹õ˜;$ò++){
					$•”ú”=array();
					$•”ú”['kind'] = $Óò‹•;
					$•”ú”['daili']=$this->user['uid'];
					$•”ú”['km'] = $this->getkm(12);
					$•”ú”['ms'] = $˜ˆ;
					$•”ú”['isuse'] = 0;
					$•”ú”['addtime'] = date("Y-m-d H:i:s");
					if($this->user['rmb']>=$Œ˜){
						if(M("kms")->data($•”ú”)->add()){
							M("users")->where("uid='".$this->user['uid']."'")->setDec('rmb',$Œ˜); 
							$this->assign('user',M("users")->field('*')->where("uid='".$this->user['uid']."'")->find());
							$˜ˆg.="<li class='list-group-item'>{$•”ú”[km]}</li>";
						}else{
							$˜ˆg.="<li class='list-group-item list-group-item-danger' style='color:red;'>å¡å¯†ç”Ÿæˆå¤±è´¥ï¼</li>";
						}
					}else{
						$˜ˆg.="<li class='list-group-item list-group-item-danger' style='color:red;'>è´¦æˆ·ä½™é¢ä¸è¶³ï¼</li>";
					}
				}
				$˜ˆg.="</ul>";
			}else{
				$˜ˆg="<ul class='list-group'>
				<li class='list-group-item list-group-item-danger'>è´¦æˆ·ä½™é¢ä¸è¶³ï¼Œè¯·å…ˆå……å€¼ï¼</li></ul>";
			}
			$this->assign('msg',$˜ˆg);
		}
		if($_POST['do']=='search' && $ˆ=I('post.key','','get_safe_str')){
			$ÎåÜŒÜ="km like '%$ˆ%' and ".C("DB_PREFIX")."kms.daili='".$this->user['uid']."'";
			if($±¦=='peie'){
				$ÎåÜŒÜ.=" and kind=1";
			}elseif($±¦=='sy'){
				$ÎåÜŒÜ.=" and kind=2";
			}else{
				$ÎåÜŒÜ.=" and kind=0";
			}
			$Œ¾Îˆ=M("kms")->field(array(C("DB_PREFIX").'kms.*',C("DB_PREFIX").'users.user',C("DB_PREFIX").'users.qq'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."kms.daili")->where($ÎåÜŒÜ)->limit(12)->order('kid desc')->select();
			$÷=1;$÷ages=1;$ï¾õ‹ú=1;
		}else{
			$÷=is_numeric($_GET['p'])?$_GET['p']:'1';
			$ˆtart=12*($÷-1);
			$‹Ü±ú=$÷+1;
			$—ò˜òú="$ˆtart,12";
			if($±¦=='peie'){
				$ÎåÜŒÜ="kind=1";
			}elseif($±¦=='sy'){
				$ÎåÜŒÜ="kind=2";
			}else{
				$ÎåÜŒÜ="kind=0";
			}
			$ÎåÜŒÜ.=" and ".C("DB_PREFIX")."kms.daili='".$this->user['uid']."'";
			$ï¾õ‹ú=M("kms")->where($ÎåÜŒÜ)->count('kid');
			$÷ages=ceil($ï¾õ‹ú/12);
			$Œ¾Îˆ=M("kms")->field(array(C("DB_PREFIX").'kms.*',C("DB_PREFIX").'users.user',C("DB_PREFIX").'users.qq'))->join("left join ".C("DB_PREFIX")."users on ".C("DB_PREFIX")."users.uid=".C("DB_PREFIX")."kms.daili")->where($ÎåÜŒÜ)->limit($—ò˜òú)->order('kid desc')->select();
			if(($÷-1)>0){
				$ˆtart=$÷-1;
			}else{
				$ˆtart=1;
			}
			if(($÷+5)<$÷ages){
				$Ü‹•=$÷+5;
			}else{
				$Ü‹•=$÷ages;
			}
			$this->assign('end',$Ü‹•);
			$this->assign('start',$ˆtart);
		}
		$this->assign('xz',$±¦);
		$this->assign('page',$÷);
		$this->assign('pages',$÷ages);
		$this->assign('count',$ï¾õ‹ú);
		$this->assign('list',$Œ¾Îˆ);
		$this->display();
	}

	private function getkm($—Ü‹=12){
		$ˆtr ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$ˆtrlen = strlen($ˆtr);
		$Œ”‹•ˆúŒ = '';
		for ($ò = 0; $ò<$—Ü‹; $ò++){
			$Œ”‹•ˆúŒ .= $ˆtr[mt_rand(0, $ˆtrlen-1)];
		}
		return $Œ”‹•ˆúŒ;
	}
	public function __construct(){
		parent::__construct();
		if($ˆid=get_safe_str($_COOKIE['vmz_sid'])){
			if($õˆÜŒ=M("users")->field(array("*"))->where("sid='$ˆid'")->find()){
				if(!$õˆÜŒ['daili']){
					get_exit($õò‹."ä½ ä¸æ˜¯ä»£ç†ï¼",U('index/user'));
				}else{
					$this->user=$õˆÜŒ;
					$this->assign('user',$this->user);
				}
			}else{
				get_exit($õò‹."è¯·ç™»å½•åå†è¿›è¡Œæ“ä½œï¼",U('index/login'));
			}
		}else{
			get_exit($õò‹."è¯·ç™»å½•åå†è¿›è¡Œæ“ä½œï¼",U('index/login'));
		}
    }
}