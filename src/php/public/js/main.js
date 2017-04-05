(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
var App = function($el, routes) {
	
	var _this = this;
	
	(function() {
		_bind();
	})();
	
	this.open = function(url) {
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
	
	this.go = function(url) {
		window.history.pushState({}, '', url);
		this.open(url);
	};
	
	function _route(url, routes) {
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
	
	function _render($content) {
		$el.empty().append($content);
	};
	
	function _bind() {
		// обрабатываем переходы по всем всем ссылкам
		$el.on('click', 'a', function() {
			var href = $(this).attr('href');
			_this.go(href);
			
			return false;
		});
		// обрабатываем переходы по истории
		$(window).on('popstate', function() {
			_this.open(window.location.href);
		});
	};
};

module.exports = App;


},{}],2:[function(require,module,exports){
var tpl = {
    form: require('./formTpl.html')
};

var Screen = function(app) {
	
	var _this = this;
	
	var _formTpl;
	
	this.$el;
	
	this.error = function(message) {
		alert(message);
	};
	
	var _submit = function() {
		// validation
		var formData = _this.$el.find('form').serialize();
		$.ajax({
			method: 'post',
			url: '/login',
			data: formData,
			dataType: 'json',
			beforeSend: function() {},
			complete: function() {},
			success: function(data) {
				if (data.sessionId) {
					app.go('/');
				} else {
					_.each(data.errors, function(error) {
						_this.error(error);
					});
				}
			},
			error: function() {
				_this.error(':-(');
			}
		});
		
		return false;
	};
	
	var _bind = function() {
		_this.$el.on('submit', 'form', _submit);
	};
	
	var _render = function($content) {
		_this.$el.empty().append($content);
	};
	
	// INIT
	(function() {
		_this.$el = $('<div />');
		_formTpl = _.template(tpl.form);
		_render(_formTpl());
		_bind();
	})();
	
};

module.exports = Screen;

},{"./formTpl.html":3}],3:[function(require,module,exports){
module.exports = "<form action=\"/login\" method=\"post\">\n\t<input name=\"login\" type=\"text\">\n\t<input name=\"password\" type=\"password\">\n\t<button type=\"submit\">Login</button>\n</form>";

},{}],4:[function(require,module,exports){
var tpl = {
	form: require('./formTpl.html')
};

var Screen = function() {
	
	var _this = this;
	
	var _formTpl;
	
	this.$el;
	
	this.error = function(message) {
		alert(message);
	};
	
	var _submit = function() {
		// validation
		var $form = $(this);
		var url = $form.attr('action');
		var formData = $form.serialize();
		$.ajax({
			method: 'post',
			url: url,
			data: formData,
			dataType: 'json',
			beforeSend: function() {},
			complete: function() {},
			success: function(data) {
				console.log(data);
				if (data.sessionId) {
					app.go('/');
				} else {
					_.each(data.errors, function(error) {
						_this.error(error);
					});
				}
			},
			error: function() {
				_this.error(':-(');
			}
		});
		
		return false;
	};
	
	var _bind = function() {
		_this.$el.on('submit', 'form', _submit);
	};
	
	var _render = function($content) {
		_this.$el.empty().append($content);
	};
	
	// INIT
	(function() {
		_this.$el = $('<div />');
		_formTpl = _.template(tpl.form);
		_render(_formTpl());
		_bind();
	})();
	
};

module.exports = Screen;
},{"./formTpl.html":5}],5:[function(require,module,exports){
module.exports = "<form action= \"/signup\" method=\"post\">\n\t<input name=\"login\" type=\"text\">\n\t<input name=\"password\" type=\"password\">\n\t<button type=\"submit\">Зарегистрироваться</button>\n</form>";

},{}],6:[function(require,module,exports){
var startTpl = require('./startTpl.html');

var Screen = function(app) {
	
	var _this = this;
	
	var _formTpl;
	
	this.$el;
	
	var _render = function($content) {
		_this.$el.empty().append($content);
	};
	
	// INIT
	(function() {
		_this.$el = $('<div />');
		_formTpl = _.template(startTpl);
		_render(_formTpl());
	})();
	
};

module.exports = Screen;

},{"./startTpl.html":7}],7:[function(require,module,exports){
module.exports = "добро пожаловать, друзья\n<div>\n\t<a href=\"/login\">вход</a>\n\t<a href=\"/signup\">регистрация</a>\n</div>";

},{}],8:[function(require,module,exports){
var App = require('./frent/App.js');

var StartScreen = require('./frent/Start/StartScreen.js');
var LoginScreen = require('./frent/Login/LoginScreen.js');
var SignupScreen = require('./frent/Signup/SignupScreen.js');

$(document).ready(function() {
	var routes = {
		'^/$': StartScreen,
		'^/login$': LoginScreen,
		'^/signup$': SignupScreen
	};
	var app = new App($('#frent'), routes);
	app.go(window.location.href);
	
});
},{"./frent/App.js":1,"./frent/Login/LoginScreen.js":2,"./frent/Signup/SignupScreen.js":4,"./frent/Start/StartScreen.js":6}]},{},[8]);
