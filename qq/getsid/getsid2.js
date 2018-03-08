var xiha={
	postData: function(url, parameter, callback, dataType, ajaxType) {
		if(!dataType) dataType='json';
		$.ajax({
			type: "POST",
			url: url,
			async: true,
			dataType: dataType,
			json: "callback",
			data: parameter,
			success: function(data) {
				if (callback == null) {
					return;
				} 
				callback(data);
			},
			error: function(error) {
				alert('创建连接失败');
			}
		});
	}
}
function login(uin,vcode,pt_verifysession){
	$('#load').html('正在登录，请稍等...');
	var loginurl="index.php?mod=qqlogin";
	xiha.postData(loginurl,"uin="+uin+"&vcode="+vcode+"&pt_verifysession="+pt_verifysession+"&r="+Math.random(1), function(d) {
		if(d.saveOK ==0){
			$('#load').html('SID获取成功，请稍等...');
			var backurl = "index.php?mod=dama";
			alert("QQ "+uin+" 协助打码成功！");
			window.location.href=backurl;
		}else if(d.saveOK ==4){
			$('#load').html('验证码错误，请重新输入。');
			$('#submit').attr('do','submit');
			$('.code').hide();
			$('#code').val("");
		}else if(d.saveOK ==3){
			$('#load').html('该QQ密码不正确，此次协助打码失败！');
			$('#submit').attr('do','submit');
			$('.code').hide();
			var backurl = "index.php?mod=dama";
			alert("该QQ密码不正确，此次协助打码失败！");
			window.location.href=backurl;
		}else{
			var backurl = "index.php?mod=dama";
			alert("您打码的QQ "+d.msg);
			$('#submit').attr('do','submit');
			window.location.href=backurl;
		}
	});
	
}

function getvc(uin,sig,sess){
	$('#load').html('获取验证码，请稍等...');
	sess = sess||0;
	var getvcurl="qq/getsid/login.php?do=getvc";
	xiha.postData(getvcurl,'uin='+uin+'&sig='+sig+'&sess='+sess+'&r='+Math.random(1), function(d) {
		if(d.saveOK ==0){
			$('#load').html('请输入验证码');
			$('#codeimg').attr('vc',d.vc);
			$('#codeimg').attr('sess',d.sess);
			$('#codeimg').attr('cdata',d.cdata);
			if(typeof d.websig != "undefined"){
				$('#codeimg').attr('collectname',d.collectname);
				$('#codeimg').attr('websig',d.websig);
			}
			$('#codeimg').html('<img onclick="getvc(\''+uin+'\',\''+d.vc+'\',\''+d.sess+'\')" src="qq/getsid/login.php?do=getvcpic&uin='+uin+'&cap_cd='+sig+'&sig='+d.vc+'&sess='+d.sess+'&r='+Math.random(1)+'" title="点击刷新">');
			$('#submit').attr('do','code');
			$('#code').val('');
			$('.code').show();
		}else if(d.saveOK ==2){
			$('#codeimg').attr('vc',d.vc);
			$('#codeimg').attr('sess',d.sess);
			$('#codeimg').attr('cdata',d.cdata);
			if(typeof d.websig != "undefined"){
				$('#codeimg').attr('collectname',d.collectname);
				$('#codeimg').attr('websig',d.websig);
			}
			dovc(uin,d.ans,d.vc);
		}else{
			alert(d.msg);
		}
	});

}
function dovc(uin,code,vc){
	$('#load').html('验证验证码，请稍等...');
	var cap_cd=$('#uin').attr('cap_cd');
	var sess=$('#codeimg').attr('sess');
	var cdata=$('#codeimg').attr('cdata');
	var websig=$('#codeimg').attr('websig');
	var collect=jisuan();
	var collectname=$('#codeimg').attr('collectname');
	var getvcurl="qq/getsid/login.php?do=dovc";
	xiha.postData(getvcurl,'uin='+uin+'&ans='+code+'&sig='+vc+'&cap_cd='+cap_cd+'&sess='+sess+'&collectname='+collectname+'&websig='+websig+'&cdata='+cdata+'&collect='+collect+'&r='+Math.random(1), function(d) {
		if(d.rcode ==0){
			login(uin,d.randstr.toUpperCase(),d.sig);
		}else if(d.rcode == 50){
			$('#load').html('验证码错误，重新生成验证码，请稍等...');
			getvc(uin,cap_cd,sess);
		}else if(d.rcode == 12){
			$('#load').html('验证失败，请重试。');
		}else{
			$('#load').html('验证失败，请重试。');
			getvc(uin,cap_cd,sess);
		}
	});

}
function checkvc(){
	var uin=$('#uin').val();
	$('#load').html('登录中，请稍候...');
	var checkvcurl="qq/getsid/login.php?do=checkvc";
	xiha.postData(checkvcurl,'uin='+uin+'&r='+Math.random(1), function(d) {
		if(d.saveOK ==0){
			login(d.uin,d.vcode,d.pt_verifysession);
		}else if(d.saveOK ==1){
			$('#uin').attr('cap_cd',d.sig);
			getvc(uin,d.sig,0);
		}else{
			alert(d.msg);
			$('#load').html('');
		}
	});
}
$(document).ready(function(){
	$('#submit').click(function(){
		var self=$(this);
		var uin=$('#uin').val();
		$('#load').show();
		if(self.attr('do') == 'code'){
			var vcode=$('#code').val(),
				vc=$('#codeimg').attr('vc');
			dovc(uin,vcode,vc);
		}else{
		if (self.attr("data-lock") === "true") return;
			else self.attr("data-lock", "true");
			checkvc();
			self.attr("data-lock", "false");
		}
	});
});