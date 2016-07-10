<?php
$mysql=require("../Common/Conf/db.php");
try{
	$db=new PDO("mysql:host=".$mysql['DB_HOST'].";dbname=".$mysql['DB_NAME'].";port=".$mysql['DB_PORT'],$mysql['DB_USER'],$mysql['DB_PWD']);
}catch(Exception $e){
	exit('链接数据库失败:'.$e->getMessage());
}
$db->exec("set names utf8");
$code1 = "123456";
$code2 = "654321";
$orderid = isset($_POST['orderid'])?$_POST['orderid']:exit("订单号不能为空！");
$money = isset($_POST['money'])?$_POST['money']:exit("金额不能为空！");
$note = isset($_POST['note'])?$_POST['note']:exit("备注不能为空！");
$ordertime = isset($_POST['ordertime'])?$_POST['ordertime']:exit("订单创建日期不能为空！");
$sign = isset($_POST['sign'])?$_POST['sign']:exit("签名不能为空！");
if (strtoupper(md5($code1.$code2.$orderid)) == strtoupper($sign)) {
    $query = $db->query("SELECT `orderid` FROM `" . $mysql['DB_PREFIX'] . "pays` WHERE orderid='" . $orderid . "' limit 1");
    $sql = $query->fetch();
    if ($sql['orderid'] == $orderid) {
        $msg = "Succ";
    } else {
		$query = $db->exec("INSERT INTO `" . $mysql['DB_PREFIX'] . "pays` (orderid,money,note,star,ordertime,addtime) VALUES ('{$orderid}','{$money}','{$note}','1','{$ordertime}','" . date("Y-m-d-H:i:s") . "')");
        if ($query) {
            $msg = "Succ";
        } else {
            $msg = "数据库写入失败，请重试";
        }
    }
} else {
    $msg = "签名错误";
}
$db = NULL;
//exit($msg);
echo $msg;
?>