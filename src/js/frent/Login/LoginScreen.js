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
