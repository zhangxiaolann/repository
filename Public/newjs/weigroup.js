function updateFigureInfo(a) {
	$("#figure_group").html(docBuffer.figure_group[a]),
	$("#figure_desc").html(docBuffer.figure_desc[a]),
	$("#figure_name").html(docBuffer.figure_name[a]),
	$("#figure_img").attr("src", docBuffer.figure_img[a]),
	$("#figure_popup_shell").animate({
		opacity: 1
	},
	"slow"),
	docBuffer.figure_mark = a
}
function isScrollUp() {
	var a = window.scrollYMark;
	return document.documentElement.scrollTop > a || window.scrollY > a ? !1 : !0
}
window.scrollYMark = 0,
$(document).scroll(function() {
	var a = [0, 2500, 4500];
	if (!skrollr.get().isAnimatingTo()) {
		for (var b = window.scrollY || document.documentElement.scrollTop,
		c = 0; c < a.length; c++) if (b > a[c] && b < a[c + 1]) {
			isScrollUp() ? (skrollr.get().animateTo(a[c], {
				duration: 2e3,
				easing: "linear"
			}), 1 == c && $("#back_1_container").animate({
				opacity: 1
			},
			{
				duration: 2e3,
				queue: !1
			})) : (skrollr.get().animateTo(a[c + 1], {
				duration: 2e3,
				easing: "linear"
			}), 1 == c && $("#back_1_container").animate({
				opacity: .2
			},
			{
				duration: 2e3,
				queue: !1
			}));
			break
		}
		window.scrollYMark = window.scrollY || document.documentElement.scrollTop
	}
});
var docBuffer = {
	figure_mark: 1,
	figure_desc: ["通过微群组，我找到了同样喜欢小动物的一群好朋友<br>生活变得越来越有趣了~", "一直有个梦想，成为一名DJ主持节目分享喜欢的东西给朋友，<br>加入微群组成为主播收获的不仅仅是圆梦，还有那么多人喜欢我并给我的鼓励！", "美好的周末，同事朋友却住在城市的不同地方很难聚齐，<br>微群组帮我找到附近的篮球小分队，加入，约球，畅汗淋漓，SO EASY！"],
	figure_name: ["喵小姐", "阿卡", "石头"],
	figure_group: ["小动物关爱协会", "布二电台", "无篮球不兄弟"],
	figure_img: ["/Public/newimg/intro/upic1.png", "/Public/newimg/intro/upic2.png", "/Public/newimg/intro/upic3.png"]
};
$("#introSwitchLeft").click(function() {
	$("#figure_popup_shell").animate({
		opacity: 0
	},
	"fast");
	var a = (docBuffer.figure_mark + 2) % 3;
	updateFigureInfo(a)
}),
$("#introSwitchRight").click(function() {
	$("#figure_popup_shell").animate({
		opacity: 0
	},
	"fast");
	var a = (docBuffer.figure_mark + 1) % 3;
	updateFigureInfo(a)
}),
$.easing.smoothmove = function(a, b, c, d, e) {
	return - d * (b /= e) * (b - 2) + c
},
-1 == navigator.userAgent.indexOf("MSIE") && $("#back_1_container").bind("mousemove",
function(a) {
	this.bound = this.getBoundingClientRect(),
	this.top = this.bound.top,
	this.bottom = this.bound.bottom,
	this.left = this.bound.left,
	this.right = this.bound.right,
	this.width = this.bound.width || this.right - this.left,
	this.height = this.bound.height || this.bottom - this.top,
	this.centerX = this.width / 2,
	this.centerY = this.height / 2,
	this.offsetX = this.centerX - a.clientX,
	this.offsetY = this.centerY - a.clientY,
	this.offsetX = Math.min(this.offsetX, 0),
	this.offsetY = Math.min(this.offsetY, 0),
	this.offsetX = Math.max(this.offsetX, -10),
	this.offsetY = Math.max(this.offsetY, -10),
	$("#back_1").animate({
		"background-position-x": Math.min(50 - 1e4 * this.offsetX / this.width, 100) + "%",
		"background-position-y": Math.min(0 - 1e4 * this.offsetY / this.height, 100) + "%"
	},
	{
		queue: !1,
		duration: 200,
		easing: "smoothmove"
	})
}),
$(".slider").bind("mouseover",
function() {
	var a = $(this).attr("data-slide") || "img img-1";
	$("#slide_pic").attr("class", a)
}),
$(".download").click(function() {
	$("#download_popup").show()
}),
$("#close_popup").click(function() {
	$("#download_popup").hide()
}),
$("#gotop").click(function() {
	$("#back_1_container").animate({
		opacity: 1
	},
	{
		duration: 0,
		queue: !1
	}),
	skrollr.get().setScrollTop(0)
});
/*  |xGv00|92e48956d081aea56ddd687a571a3b76 */
