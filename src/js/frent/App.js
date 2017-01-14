var App = function($el, routes) {
	
	var _this = this;
	
	var _route = function(url, routes) {
		var route = false;
		for (var pattern in routes) {
			var regex = new RegExp(pattern);
			var params = regex.exec(url);
			if (params) {
				route = {
					screen: routes[pattern],
					params: params
				};
				break;
			}
		}
		
		return route;
	};
	
	var _bind = function() {
		// обрабатываем переходы по всем всем ссылкам
		$el.on('click', 'a', function() {
			var href = $(this).attr('href');
			_this.go(href);
			
			return false;
		});
		// обрабатываем переходы по истории
		$(window).on('popstate', function() {
			_this.go(window.location.href);
		});
	};
	
	var _render = function($content) {
		$el.empty().append($content);
	};
	
	
	this.go = function(url) {
		window.history.pushState({}, '', url);
		var a = document.createElement('a');
		a.href = url;
		var route = _route(a.pathname, routes);
		if (route) {
			var screen = new route.screen(_this, route.params);
			_render(screen.$el);
		} else {
			alert('404');
		}
	};
	
	// INIT
	(function() {
		_bind();
	})();
	
};

module.exports = App;

