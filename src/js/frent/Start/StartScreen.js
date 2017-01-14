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
