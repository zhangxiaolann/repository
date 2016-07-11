<?php

function qdrule(){
	if($rows=explode('|',C('qd_rule'))){
		$array=array();
		foreach($rows as $row){
			if($arr=explode(',',$row)){
				$key=trim($arr[0]);
				$array["$key"]=trim($arr[1]);
				$max=$key;
			}
		}
		if($array){
			$narr=array();
			$jf=0;
			for($i=1;$i<=$max;$i++){
				if($num=$array[$i]){
					$narr[$i]=$num;
					$jf=$num;
				}else{
					$narr[$i]=$jf;
				}
			}
			$narr['max']=$max;
			return $narr;
		}else{
			return array();
		}
	}else{
		return array();
	}
}
