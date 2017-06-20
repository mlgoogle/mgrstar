/**
 * Tab
 */
(function ($) {
	$.fn.tabs = function (options) {
		// 设置默认参数
		var settings = $.extend({
			'btn': '.tab_btn',
			'page': '.tab_content',
			'current': 'active'
		},options);
		// 维护链式调用
		return this.each(function () {
			var that = $(this);
			that.find(settings.btn).on('click',function () {
                var _this = $(this);
				var index =  _this.index();
                if(_this.hasClass(settings.current)) return false;
                _this.addClass(settings.current).siblings().removeClass(settings.current);
				that.find(settings.page).eq(index).show().siblings().hide();
			})
		})
	};
})(jQuery);
