$(document).ready(function(){
	$(window).scroll(function(){
		var h=document.documentElement.scrollTop||document.body.scrollTop,
			$left=$('.left_container');
		var top;
		if(h<104){
			top=104-h;
			$left.css('top',top+'px');
		}
	});
});

var vmz={
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