<?php

if ($qzone->sidzt || $qzone->skeyzt) {
    if ($row['isauto']) {
		$uin=$row['qq'];
		$pwd=$row['pwd'];
		@$db->exec("UPDATE ".DB_PREFIX."qqs SET lastauto='$now' WHERE qid='{$qid}'");
		include_once 'AutoSkey.class.php';
		$AutoSkey = new AutoSkey($uin,$pwd,0,0);
		$arr=json_decode($AutoSkey->json,true);
		if($arr['code']==0){
			$db->exec("UPDATE ".DB_PREFIX."qqs SET sid='".$arr['sid']."',skey='".$arr['skey']."',pc_p_skey='".$arr['pc_p_skey']."',cp_p_skey='".$arr['cp_p_skey']."',sidzt='0',skeyzt='0' WHERE qid='{$qid}'");
			echo "更新成功";
		}elseif($arr['code']==-2 || $arr['code']==-1){
			$mail = 'skey';
			$db->exec("UPDATE " . DB_PREFIX . "qqs SET sidzt='1',skeyzt='1' WHERE qid='{$qid}'");
			echo "需要验证码";
		}else{
			$db->exec("UPDATE " . DB_PREFIX . "qqs SET sidzt='1',skeyzt='1' WHERE qid='{$qid}'");
			echo $arr['msg'];
		}
    }
}
if ($_GET['get']) {
    print_r($qzone->msg);
    print_r($qzone->error);
}
if ($mail) {
    $urs = $db->query("SELECT mail FROM " . DB_PREFIX . "users where uid='{$row['uid']}' limit 1");
    if ($user = $urs->fetch()) {
        sendmail($user['mail'], $uin, $mail, $config);
    }
}
$db = NULL;