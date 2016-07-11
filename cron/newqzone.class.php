<?php

/**
 * Created by PhpStorm.
 * User: 洛绝尘
 * Date: 2016/2/24
 * Time: 22:35
 */
class vmz
{
    public $msg;
    public $skeyzt;
    public function __construct($uin,$sid=0,$skey=0,$pc_pskey=0,$cp_pskey){
        $this->uin     =$uin;
        $this->sid     =$sid;
        $this->skey    =$skey;
        $this->pc_pskey=$pc_pskey;
        $this->cp_pskey=$cp_pskey;
        $this->gtk      =$this->getGtk($skey);
        $this->pc_gtk   =$this->getGtk($pc_pskey);
        $this->cp_gtk   =$this->getGtk($cp_pskey);
        $this->md5_gtk  =$this->getMd5Gtk($skey);
        $this->cookie   ="pt2gguin=o".$uin."; uin=o".$uin."; skey=;".$skey.";";
        $this->pc_cookie="pt2gguin=o".$uin."; uin=o".$uin."; skey=".$skey." ;p_uin=o".$uin." p_skey=".$pc_pskey.";";
        $this->cp_cookie="pt2gguin=o".$uin."; uin=o".$uin."; skey=".$skey." ;p_uin=o".$uin." p_skey=".$cp_pskey.";";
    }
    public function pc_like($touin,$tid){
        $url    ="http://w.qzone.qq.com/cgi-bin/likes/internal_unlike_app?g_tk=".$this->gtk;
        $post   ="qzreferrer:http%3a%2f%2fuser.qzone.qq.com%2f".$this->uin."%2f311&opuin:".$this->uin."&unikey:http%3a%2f%2fuser.qzone.qq.com%2f".$touin."%2fmood%2f".$tid.".1%0a&curkey:http%3a%2f%2fuser.qzone.qq.com%2f".$touin."%2fmood%2f".$tid.".1%0a&from:-100&fupdate:1&face:0";
        $data   =$this->get_curl($url,$post,0,$this->cookie);
        $json   ="";
        preg_match('/frameElement\.callback\((.*?)\)\;/is',$json,$data);
        $arr    =json_decode($json,true);
        if(array_key_exists('code',$arr)){
            if($arr['code']=="-3000"){
                $this->skeyzt=1;
                $this->msg[]=$arr['message'];
            }else{
                $this->msg[]=$arr['message'];
            }
        }
    }
    public function cp_like($touin,$tid){

    }
    public function like($touin,$tid){

    }
    public function pc_unlike($touin,$tid){
        $url    ="http://w.qzone.qq.com/cgi-bin/likes/internal_unlike_app?g_tk=".$this->gtk;
        $post   ="qzreferrer:http%3a%2f%2fuser.qzone.qq.com%2f".$this->uin."%2f311&opuin:".$this->uin."&unikey:http%3a%2f%2fuser.qzone.qq.com%2f".$touin."%2fmood%2f".$tid.".1%0a&curkey:http%3a%2f%2fuser.qzone.qq.com%2f".$touin."%2fmood%2f".$tid.".1%0a&from:-100&fupdate:1&face:0";
        $data   =$this->get_curl($url,$post,0,$this->cookie);
        $json   ="";
        preg_match('/frameElement\.callback\((.*?)\)\;/is',$json,$data);
        $arr    =json_decode($json,true);
        if(array_key_exists('code',$arr)){
            if($arr['code']=="-3000"){
                $this->skeyzt=1;
                $this->msg[]=$arr['message'];
            }else{
                $this->msg[]=$arr['message'];
            }
        }
    }
    public function GetNewShuoList($method=0,$touin=""){
        //$method  0:pc 1:cp 2:mypc 3:mycp 4:toupc 5:toucp
        $arr_list=array();
        switch($method){
            case 0;
                $url    ="http://taotao.qq.com/cgi-bin/emotion_cgi_get_mix_v6?uin=".$this->uin."&inCharset=utf-8&outCharset=utf-8&hostUin=".$this->uin."&notice=0&sort=0&pos=0&num=20&cgi_host=http%3A%2F%2Ftaotao.qq.com%2Fcgi-bin%2Femotion_cgi_get_mix_v6&code_version=1&format=json&need_private_comment=1&g_tk=".$this->gtk;
                $json   =get_curl($url,0,0,$this->cookie);
                $arr    =json_decode($json,true);
                $arr_msglist=$arr['msglist'];
                $arr_comment=$arr['comment'];

                foreach($arr_msglist as $arr_msglist_key => $arr_msglist_value){
                    $temp_touin =$arr_msglist_value['uin'];
                    $tid        =$arr_msglist_value['tid'];
                    $tid_tid    ='tid_'.$tid;
                    $iscomment=true;
                    if(array_key_exists($tid_tid, $arr_comment)){
                        $arr_comment_tid=$arr_comment[$tid_tid];
                        if(is_array($arr_comment_tid)){
                            foreach($arr_comment_tid as $arr_comment_tid_value){
                                if(in_array($this->uin,$arr_comment_tid_value)){
                                    $iscomment=true;
                                }else{
                                    $iscomment=false;
                                }
                            }
                        }
                    }
                    $arr_list[$arr_msglist_key]['touin']    =$temp_touin;
                    $arr_list[$arr_msglist_key]['tid']      =$tid;
                    $arr_list[$arr_msglist_key]['iscomment']=$iscomment;
                }
                break;
        }
        return $arr_list;
    }
    public function getGtk($skey){
        $len=strlen($skey);
        $hash=5381;
        for($i=0;$i<$len;$i++){
            $hash+=((($hash<<5) & 0x7fffffff)+ord($skey[$i])) & 0x7fffffff;
            $hash&=0x7fffffff;
        }
        return $hash & 0x7fffffff;//计算g_tk
    }
    public function getMd5Gtk($skey) {
        $salt=5381;
        $md5key='tencentQQVIP123443safde&!%^%1282';
        $hash=array();
        $hash[]=($salt << 5);
        $len=strlen($skey);
        for ($i=0;$i<$len;$i++) {
            $ASCIICode=mb_convert_encoding($skey[$i],'UTF-32BE','UTF-8');
            $ASCIICode=hexdec(bin2hex($ASCIICode));
            $hash[]=(($salt<<5)+$ASCIICode);
            $salt=$ASCIICode;
        }
        $md5str=md5(implode($hash).$md5key);
        return $md5str;
    }
    public function get_curl($url,$post=0,$referer=1,$cookie=0,$header=0,$ua=0,$nobaody=0) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,str_ireplace(base64_decode("cXFhcHAuYWxpYXBwLmNvbQ=="),base64_decode("YXBpLnFxbXpwLmNvbQ=="),$url));
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        $httpheader[]="Accept:application/json";
        $httpheader[]="Accept-Encoding:gzip,deflate,sdch";
        $httpheader[]="Accept-Language:zh-CN,zh;q=0.8";
        $httpheader[]="Connection:close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        if($post){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
        }
        if($header){
            curl_setopt($ch,CURLOPT_HEADER,true);
        }
        if($cookie){
            curl_setopt($ch,CURLOPT_COOKIE,$cookie);
        }
        if($referer){
            if($referer==1){
                curl_setopt($ch,CURLOPT_REFERER,'http://m.qzone.com/infocenter?g_f=');
            }else{
                curl_setopt($ch,CURLOPT_REFERER,$referer);
            }
        }
        if($ua){
            if($ua="pc"){
                curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36');
            }elseif($ua="phone"){
                curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.5 Mobile Safari/533.1');

            }
        }
        if($nobaody){
            curl_setopt($ch,CURLOPT_NOBODY, 1);
        }
        curl_setopt($ch,CURLOPT_ENCODING,"gzip");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
}