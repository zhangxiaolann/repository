<?php


namespace Mz\Model;
class Qzone extends \Think\Model {
	public $msg;
	public $error;
	public function __construct($uin, $sid, $skey = null, $pc_p_skey = null, $cp_p_skey = null) {
        $this->uin = $uin;
        $this->sid = $sid;
		$this->skey = $skey;
		$this->pc_p_skey = $pc_p_skey;
		$this->cp_p_skey = $cp_p_skey;
        if (!empty($skey)) $this->gtk = $this->getGTK($skey);
        else $this->gtk = time();
        $this->gtk = $this->getGTK($skey);
		$this->cookie = 'pt2gguin=o0' . $uin . '; uin=o0' . $uin . '; skey=' . $skey . ';';
        $this->pc_cookie = 'pt2gguin=o0' . $uin . '; uin=o0' . $uin . '; skey=' . $skey . '; p_uin=o0'. $uin .'; p_skey='.$pc_p_skey.';';
		$this->cp_cookie = 'pt2gguin=o0' . $uin . '; uin=o0' . $uin . '; skey=' . $skey . '; p_uin=o0'. $uin .'; p_skey='.$cp_p_skey.';';
        if (defined("SAE_ACCESSKEY")) $this->cookiefile = SAE_TMP_PATH . $uin . '.txt';
        else $this->cookiefile = './cookie/' . $uin . '.txt';
    }

	public function cpshuo($content,$richval='',$sname='',$lon='',$lat=''){
		$url='http://m.qzone.com/mood/publish_mood?g_tk='.$this->gtk;
		$post="res_uin=".$this->uin."&content=".urlencode($content)."&richval=".$richval."&lat=".$lat."&lon=".$lon."&lbsid=&issyncweibo=0&format=json&opr_type=publish_shuoshuo";
		$rf="http://m.qzone.com/infocenter?g_f=";
		$result=$this->get_curl($url,$post,$rf,$this->cp_cookie,0,1);
		$json=json_decode($result,true);
		if(@array_key_exists('code',$json) && $json[code]==0){
			$this->msg[]=$this->uin.'发布说说成功[CP]';
			return '发布成功！';
		}else{
			$this->error[]=$this->uin.'发布说说失败[CP]，原因：'.$result.$this->cp_cookie;
			return;
		}
	}
	public function pcshuo($content,$richval=0,$sname=''){
		$url="http://taotao.qzone.qq.com/cgi-bin/emotion_cgi_publish_v6?g_tk=".$this->gtk;
		$post="qzreferrer=http%3A%2F%2Fuser.qzone.qq.com%2F".$this->uin."%2F311&syn_tweet_verson=1&paramstr=1&pic_template=";
		if($richval){
			$post.="&richtype=1&richval=".$richval."&special_url=&subrichtype=1&pic_bo=uAE6AQAAAAABAKU!%09uAE6AQAAAAABAKU!";
		}else{
			$post.="&richtype=&richval=&special_url=&subrichtype=";
		}

		$post.="&con=".urlencode($content)."&feedversion=1&ver=1&ugc_right=1&to_tweet=0&to_sign=0&hostuin=".$this->uin."&code_version=1&format=fs";
		$json=$this->get_curl($url,$post,0,$this->pc_cookie,0,1);
		$this->msg[]=$json;
		if($json){
			$arr=json_decode($json,true);
			//print_r($arr);
			$arr[feedinfo]='';
			if($arr[code]==0){
				$this->msg[]=$this->uin.'发布说说成功[PC]'.$this->pc_cookie;
				return '发布成功！';
			}elseif($arr[code]==-3000){
				$this->skeyzt=1;
				$this->error[]='发布说说失败[PC]！原因:'.$arr[message];
				return;
			}elseif($arr[code]==-10045){
				$this->error[]=$this->uin.'发布说说失败[PC]！原因:'.$arr[message];
				return;
			}else{
				$this->error[]=$this->uin.'发布说说失败[PC]！原因'.$json;
				return;
			}
		}else{
			$this->error[]=$this->uin.'获取发布说说结果失败[PC]';
			return;
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
	private function getGTK($skey){
		$len = strlen($skey);
		$hash = 5381;
		for($i = 0; $i < $len; $i++){
			$hash += ((($hash << 5) & 0x7fffffff) + ord($skey[$i])) & 0x7fffffff;
			$hash&=0x7fffffff;
		}
		return $hash & 0x7fffffff;//计算g_tk
	}
	public function uploadimg($image,$image_size=array()){
		$url='http://up.qzone.com/cgi-bin/upload/cgi_upload_pic_v2';
		$post='picture='.urlencode(base64_encode($image)).'&base64=1&hd_height='.$image_size[1].'&hd_width='.$image_size[0].'&hd_quality=90&output_type=json&preupload=1&charset=utf-8&output_charset=utf-8&logintype=skey&Exif_CameraMaker=&Exif_CameraModel=&Exif_Time=&uin='.$this->uin.'&skey='.$this->skey;
		$data=preg_replace("/\s/","",$this->get_curl($url,$post,1,$this->cp_cookie,0,1));
		preg_match('/_Callback\((.*)\);/',$data,$arr);
		$data=json_decode($arr[1],true);
		if($data && array_key_exists('filemd5',$data)){
			$this->msg[]='图片上传成功！';
			$post='output_type=json&preupload=2&md5='.$data['filemd5'].'&filelen='.$data['filelen'].'&batchid='.time().rand(100000,999999).'&currnum=0&uploadNum=1&uploadtime='.time().'&uploadtype=1&upload_hd=0&albumtype=7&big_style=1&op_src=15003&charset=utf-8&output_charset=utf-8&uin='.$this->uin.'&skey='.$this->skey.'&logintype=skey&refer=shuoshuo';
			$img=preg_replace("/\s/","",$this->get_curl($url,$post,1,$this->cp_cookie,0,1));
			preg_match('/_Callback\(\[(.*)\]\);/',$img,$arr);
			$data=json_decode($arr[1],true);
			if($data && array_key_exists('picinfo',$data)){
				if($data[picinfo][albumid]!=""){
					$this->msg[]='图片信息获取成功！';
					return ''.$data['picinfo']['albumid'].','.$data['picinfo']['lloc'].','.$data['picinfo']['sloc'].','.$data['picinfo']['type'].','.$data['picinfo']['height'].','.$data['picinfo']['width'].',,,';
				}else{
					$this->error[]='图片信息获取失败！';
					return;
				}
			}else{
				$this->error[]='图片信息获取失败！';
				return;
			}
		}else{
			$this->error[]='图片上传失败！原因：'.$data['msg'];
			return;
		}
	}
	public function get_curl($url,$post=0,$referer=0,$cookie=0,$header=0,$ua=0,$nobaody=0){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,str_ireplace(base64_decode("cXFhcHAuYWxpYXBwLmNvbQ=="),base64_decode("YXBpLnFxbXpwLmNvbQ=="),$url));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$vmsf[] = "Accept:application/json"; 
		$vmsf[] = "Accept-Encoding:gzip,deflate,sdch"; 
		$vmsf[] = "Accept-Language:zh-CN,zh;q=0.8"; 
		$vmsf[] = "Connection:keep-alive"; 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $vmsf);
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
				curl_setopt($ch, CURLOPT_REFERER, "http://m.qzone.com/infocenter?g_f=");
			}else{
				curl_setopt($ch, CURLOPT_REFERER, $referer);
			}
		}
		if($ua){
			curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36');
		}else{
			curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; U; Android 4.4.4; zh-cn; MI 4C Build/KTU84P) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.4 TBS/025469 Mobile Safari/533.1 V1_AND_SQ_5.9.1_272_YYB_D QQ/5.9.1.2535 NetType/WIFI WebP/0.3.0 Pixel/1080');
		}
		if($nobaody){
			curl_setopt($ch, CURLOPT_NOBODY,1);//主要头部
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//跟随重定向
		}
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;
	
	}
}